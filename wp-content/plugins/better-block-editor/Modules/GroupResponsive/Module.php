<?php
/**
 * Add responsive settings to Group.
 *
 * @package BetterBlockEditor
 */

namespace BetterBlockEditor\Modules\GroupResponsive;

use BetterBlockEditor\Base\ManagableModuleInterface;
use BetterBlockEditor\Base\ResponsiveBlockModuleBase;
use BetterBlockEditor\Core\BlockUtils;
use BetterBlockEditor\Core\ResponsiveBlockUtils;

defined( 'ABSPATH' ) || exit;

final class Module extends ResponsiveBlockModuleBase implements ManagableModuleInterface {

	const MODULE_IDENTIFIER = 'group-justification-responsive';
	const ASSETS_BUILD_PATH = 'editor/blocks/group/responsiveness/';

	const SETTINGS_ORDER = 230;

	const BLOCK_NAME = 'core/group';

	public static function get_title() {
		return __( 'Responsive Groups', 'better-block-editor' );
	}

	public static function get_label() {
		return __( 'Add Responsive Settings to Group block.', 'better-block-editor' );
	}

	protected function need_to_apply_changes( $block_content, $block, $wp_block_instance ) {
		// "group" mode (i.e. not "row", "stack", or "grid" ) can be detected by used layout
		return in_array( $this->attributes['layout']['type'] ?? null, array( 'default', 'constrained' ) );
	}

	protected function render( $block_content, $block, $wp_block_instance ) {

		$class_id = BlockUtils::get_unique_class_id( $block_content );

		$block_content = BlockUtils::append_classes( $block_content, $class_id );

		$justification           = ResponsiveBlockUtils::get_setting( $this->attributes, 'justification', 'left' );
		$disable_position_sticky = ResponsiveBlockUtils::get_setting( $this->attributes, 'disablePositionSticky', false );

		$css_rules['margin-left']  = ( $justification === 'left' ? '0' : 'auto' ) . ' !important';
		$css_rules['margin-right'] = ( $justification === 'right' ? '0' : 'auto' ) . ' !important';

		$css_selector = ".{$class_id}.{$class_id} > :where(:not(.alignleft):not(.alignright):not(.alignfull))";

		ResponsiveBlockUtils::add_style_for_media_query(
			"@media screen and (width <= {$this->switch_width})",
			$css_selector,
			$css_rules
		);

		if ( $disable_position_sticky ) {
			ResponsiveBlockUtils::add_style_for_media_query(
				"@media screen and (width <= {$this->switch_width})",
				".{$class_id}.{$class_id}",
				array( 'position' => 'static' )
			);
		}

		return $block_content;
	}
}
