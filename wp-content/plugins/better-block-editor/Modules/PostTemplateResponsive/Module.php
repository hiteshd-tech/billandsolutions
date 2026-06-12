<?php
/**
 * Adds responsiveness settings to Post Template block.
 *
 * @package BetterBlockEditor
 */

namespace BetterBlockEditor\Modules\PostTemplateResponsive;

use BetterBlockEditor\Base\ManagableModuleInterface;
use BetterBlockEditor\Base\ResponsiveBlockModuleBase;
use BetterBlockEditor\Core\BlockUtils;

defined( 'ABSPATH' ) || exit;

class Module extends ResponsiveBlockModuleBase implements ManagableModuleInterface {

	const MODULE_IDENTIFIER = 'post-template-stack-on-responsive';
	const ASSETS_BUILD_PATH = 'editor/blocks/post-template/responsiveness/';

	const SETTINGS_ORDER = 900;

	const BLOCK_NAME = 'core/post-template';

	public static function get_title() {
		return __( 'Responsive Post Template', 'better-block-editor' );
	}

	public static function get_label() {
		return __( 'Add Responsive Settings to Post Template block when used in Grid view.', 'better-block-editor' );
	}

	protected function need_to_apply_changes( $block_content, $block, $wp_block_instance ) {
		// handle only "grid" mode
		if ( ( $this->attributes['layout']['type'] ?? null ) !== 'grid' ) {
			return false;
		}

		return true;
	}

	protected function render( $block_content, $block, $wp_block_instance ) {
		$class_id      = BlockUtils::get_unique_class_id( $block_content );
		$block_content = BlockUtils::append_classes( $block_content, array( $class_id ) );

		// stack on responsive
		$css_rules = array( 'grid-template-columns' => 'repeat(1, 1fr) !important' );

		$gap = $this->get_responsive_setting( 'gap' );

		// need strict comparison here as gap may be 0
		if ( null !== $gap ) {
			$css_rules['gap'] = $gap . ' !important';
		}

		BlockUtils::add_style_for_media_query(
			"@media screen and (width <= {$this->switch_width})",
			'body .' . $class_id,
			$css_rules
		);

		return $block_content;
	}
}
