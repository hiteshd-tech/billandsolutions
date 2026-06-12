<?php
/**
 * Responsive settings for some text blocks
 *
 * @package BetterBlockEditor
 */

namespace BetterBlockEditor\Modules\TextResponsive;

use BetterBlockEditor\Base\ManagableModuleInterface;
use BetterBlockEditor\Base\ResponsiveBlockModuleBase;
use BetterBlockEditor\Core\BlockUtils;

defined( 'ABSPATH' ) || exit;

class Module extends ResponsiveBlockModuleBase implements ManagableModuleInterface {

	const MODULE_IDENTIFIER = 'text-responsive';
	const ASSETS_BUILD_PATH = 'editor/blocks/__all__/text-responsive/';

	const SETTINGS_ORDER = 920;

	const BLOCK_NAMES = array(
		'core/post-title',
		'core/post-excerpt',
		'core/heading',
		'core/paragraph',
	);

	public static function get_title() {
		return __( 'Responsive Text Alignment', 'better-block-editor' );
	}

	public static function get_label() {
		return __( 'Add Responsive text alignment settings to Header, Paragraph, Post Title and Post Excerpt blocks.', 'better-block-editor' );
	}

	protected function need_to_apply_changes( $block_content, $block, $wp_block_instance ) {
		return in_array( $block['blockName'] ?? null, self::BLOCK_NAMES );
	}

	protected function render( $block_content, $block, $wp_block_instance ) {
		$alignment = $this->get_responsive_setting( 'alignment', null );

		if ( ! $alignment ) {
			return $block_content;
		}

		$class_id      = BlockUtils::get_unique_class_id( $block_content );
		$block_content = BlockUtils::append_classes( $block_content, array( $class_id ) );

		BlockUtils::add_style_for_media_query(
			"@media screen and (width <= {$this->switch_width})",
			'body .' . $class_id,
			array( 'text-align' => $alignment )
		);

		return $block_content;
	}
}
