<?php
/**
 * Module for adding hover colors to navigation
 *
 * @package BetterBlockEditor
 */

namespace BetterBlockEditor\Modules\ButtonHoverColors;

use BetterBlockEditor\Base\ManagableModuleInterface;
use BetterBlockEditor\Base\ModuleBase;
use BetterBlockEditor\Core\BlockUtils;
use BetterBlockEditor\Core\ColorUtils;

defined( 'ABSPATH' ) || exit;

class Module extends ModuleBase implements ManagableModuleInterface {

	const MODULE_IDENTIFIER = 'button-hover';
	const ASSETS_BUILD_PATH = 'editor/blocks/button/hover-colors/';

	const SETTINGS_ORDER = 700;

	const ATTRIBUTE_GROUP = 'wpbbeHoverColor';
	const ATTRIBUTE_NAMES = array( 'text', 'background', 'border' );

	const BlOCK_NAME = 'core/button';

	public function setup_hooks() {
		add_filter( 'render_block', array( $this, 'render' ), 20, 3 );
	}

	public function render( $block_content, $block ) {
		if ( ( $block['blockName'] ?? null ) !== self::BlOCK_NAME || $block_content === '' ) {
			return $block_content;
		}

		$settings = $block['attrs'][ self::ATTRIBUTE_GROUP ] ?? array();

		foreach ( self::ATTRIBUTE_NAMES as $name ) {
			if ( $settings[ $name ] ?? '' !== '' ) {
				$block_content = BlockUtils::append_classes( $block_content, array( 'has-hover-' . $name ) );
				$block_content = BlockUtils::append_inline_styles(
					$block_content,
					array( '--wp-block-button--hover-' . $name => ColorUtils::color_attribute_to_css( $settings[ $name ] ) )
				);
			}
		}

		return $block_content;
	}

	public static function get_title() {
		return __( 'Button Hover Color', 'better-block-editor' );
	}

	public static function get_label() {
		return __( 'Add Hover Color settings to Button block.', 'better-block-editor' );
	}
}
