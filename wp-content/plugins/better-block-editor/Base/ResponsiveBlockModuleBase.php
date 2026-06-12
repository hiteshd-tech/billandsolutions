<?php
/**
 * Base class for module which renders block with responsive settings.
 *
 * @package BetterBlockEditor
 */

namespace BetterBlockEditor\Base;

use BetterBlockEditor\Core\ResponsiveBlockUtils;

defined( 'ABSPATH' ) || exit;

abstract class ResponsiveBlockModuleBase extends ModuleBase {
	/**
	 * Attributes of the block being processed.
	 *
	 * @var array
	 */
	protected $attributes = array();

	/**
	 * Width at which the block should switch to responsive mode.
	 *
	 * @var int|null
	 */
	protected $switch_width = null;

	protected const BLOCK_NAME = null;

	public function setup_hooks() {
		add_filter( 'render_block', array( $this, 'render_with_early_return' ), 20, 3 );
	}

	/**
	 * Render block with responsive settings.
	 *
	 * @param string   $block_content Block content.
	 * @param array    $block         Block data (including name and attributes).
	 * @param WP_Block $wp_block_instance The WP_Block class instance.
	 *
	 * @return string  Block content with applied responsive settings.
	 */
	public function render_with_early_return( $block_content, $block, $wp_block_instance ) {
		// if there is no content
		if ( $block_content === '' ) {
			return $block_content;
		}

		// if there are no responsive settings
		if ( ! ResponsiveBlockUtils::is_responsive( $block ) ) {
			return $block_content;
		}

		// check block type if it's provided
		$blockName = $block['blockName'] ?? null;
		if ( static::BLOCK_NAME !== null && $blockName !== null && $blockName !== static::BLOCK_NAME ) {
			return $block_content;
		}

		// Store some data for later use
		$this->attributes = $block['attrs'] ?? array();

		$this->switch_width = ResponsiveBlockUtils::get_switch_width( $this->attributes );

		// if we can not get switch width
		if ( null === $this->switch_width ) {
			return $block_content;
		}

		if ( ! $this->need_to_apply_changes( $block_content, $block, $wp_block_instance ) ) {
			return $block_content;
		}

		return $this->render( $block_content, $block, $wp_block_instance );
	}

	protected function get_responsive_setting( $name, $default = null ) {
		return ResponsiveBlockUtils::get_setting( $this->attributes, $name, $default );
	}

	/**
	 * Check if we need to apply changes to the block content.
	 *
	 * @param string   $block_content Block content.
	 * @param array    $block         Block data (including name and attributes).
	 * @param WP_Block $instance      The block instance.
	 *
	 * @return bool True if changes are needed, false otherwise.
	 */
	abstract protected function need_to_apply_changes( $block_content, $block, $wp_block_instance );

	/**
	 * Render the block content with responsive settings.
	 *
	 * @param string   $block_content Block content.
	 * @param array    $block         Block data (including name and attributes).
	 * @param WP_Block $instance      The block instance.
	 *
	 * @return string  Rendered block content.
	 */
	abstract protected function render( $block_content, $block, $wp_block_instance );
}
