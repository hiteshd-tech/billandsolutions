<?php
/**
 * Module for adding hover colors to navigation
 *
 * @package BetterBlockEditor
 */

namespace BetterBlockEditor\Modules\NavigationHoverColors;

use BetterBlockEditor\Base\ManagableModuleInterface;
use BetterBlockEditor\Base\ModuleBase;
use BetterBlockEditor\Core\BlockUtils;
use BetterBlockEditor\Core\ColorUtils;

defined( 'ABSPATH' ) || exit;

class Module extends ModuleBase implements ManagableModuleInterface {

	const MODULE_IDENTIFIER = 'navigation-hover';
	const ASSETS_BUILD_PATH = 'editor/blocks/navigation/hover-colors/';

	const SETTINGS_ORDER = 600;

	const ATTRIBUTE_MENU_COLOR    = 'wpbbeMenuHoverColor';
	const ATTRIBUTE_SUBMENU_COLOR = 'wpbbeSubmenuHoverColor';

	const BlOCK_NAME = 'core/navigation';

	public function setup_hooks() {
		add_filter( 'render_block', array( $this, 'render' ), 20, 3 );
	}

	public function render( $block_content, $block ) {
		if ( ( $block['blockName'] ?? null ) !== self::BlOCK_NAME || $block_content === '' ) {
			return $block_content;
		}

		$main_color    = $block['attrs'][ self::ATTRIBUTE_MENU_COLOR ] ?? null;
		$submenu_color = $block['attrs'][ self::ATTRIBUTE_SUBMENU_COLOR ] ?? null;
		if ( $main_color === null && $submenu_color === null ) {
			return $block_content;
		}

		if ( $main_color !== null ) {
			$block_content = BlockUtils::append_classes( $block_content, array( 'has-hover' ) );
			$block_content = BlockUtils::append_inline_styles(
				$block_content,
				array( '--wp-navigation-hover' => ColorUtils::color_attribute_to_css( $main_color ) )
			);
		}

		if ( $submenu_color !== null ) {
			$block_content = BlockUtils::append_classes( $block_content, array( 'has-submenu-hover' ) );
			$block_content = BlockUtils::append_inline_styles(
				$block_content,
				array( '--wp-navigation-submenu-hover' => ColorUtils::color_attribute_to_css( $submenu_color ) )
			);
		}

		return $block_content;
	}

	public static function get_title() {
		return __( 'Navigation Hover Color', 'better-block-editor' );
	}

	public static function get_label() {
		return __( 'Add Hover Color settings to Navigation block.', 'better-block-editor' );
	}
}
