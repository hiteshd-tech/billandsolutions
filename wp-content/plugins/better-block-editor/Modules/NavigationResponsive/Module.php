<?php
/**
 * Adds responsiveness to navigation
 *
 * @package BetterBlockEditor
 */

namespace BetterBlockEditor\Modules\NavigationResponsive;

use BetterBlockEditor\Base\ManagableModuleInterface;
use BetterBlockEditor\Base\ModuleBase;
use BetterBlockEditor\Core\BlockUtils;
use BetterBlockEditor\Core\CssMediaBreakpoints;

defined( 'ABSPATH' ) || exit;

class Module extends ModuleBase implements ManagableModuleInterface {

	const MODULE_IDENTIFIER = 'navigation-responsive';
	const ASSETS_BUILD_PATH = 'editor/blocks/navigation/responsiveness/';

	const ATTRIBUTES                        = 'wpbbeOverlayMenu';
	const ATTRIBUTE_BREAKPOINT              = 'breakpoint';
	const ATTRIBUTE_BREAKPOINT_CUSTOM_VALUE = 'breakpointCustomValue';

	const SETTINGS_ORDER = 300;

	const BlOCK_NAME = 'core/navigation';

	public function setup_hooks() {
		add_filter( 'render_block', array( $this, 'render' ), 20, 3 );
	}

	public function render( $block_content, $block ) {
		if ( ( $block['blockName'] ?? null ) !== self::BlOCK_NAME || $block_content === '' ) {
			return $block_content;
		}

		$attributes = isset( $block['attrs'] ) ? $block['attrs'] : null;

		if ( ! ( $attributes[ self::ATTRIBUTES ][ self::ATTRIBUTE_BREAKPOINT ] ?? false ) ) {
			return $block_content;
		}

		$class_id      = BlockUtils::get_unique_class_id( $block_content );
		$block_content = BlockUtils::append_classes( $block_content, array( $class_id, 'wpbbe-responsive-navigation' ) );
		$this->add_styles( $attributes, $class_id );

		return $block_content;
	}

	private function add_styles( $attributes, $class_id ) {
		$switch_width = CssMediaBreakpoints::getSwitchWidth(
			$attributes[ self::ATTRIBUTES ][ self::ATTRIBUTE_BREAKPOINT ],
			$attributes[ self::ATTRIBUTES ][ self::ATTRIBUTE_BREAKPOINT_CUSTOM_VALUE ] ?? null
		);

		// if we can not determine switch width we always show menu expanded (no overlay icon)
		$switch_width = $switch_width ?: '0px';

		// to override default styles for responsiveness (mobile) we increase specificity of our selectors
		// and then we use THE SAME selectors "last step" to set up responsiveness
		// CSS approach to show/hide button and menu content was copied from original navigation block
		$navSelector        = ".wp-block-navigation.{$class_id}";
		$navOpenerSelector  = "$navSelector .wp-block-navigation__responsive-container-open:not(.always-shown)";
		$navContentSelector = "$navSelector .wp-block-navigation__responsive-container:not(.hidden-by-default):not(.is-menu-open)";

		// add the same rules as they were for "mobile" overlayMenu to our own media query
		$css_rules = array(
			array(
				'selector'     => "@media screen and (width > {$switch_width})",
				'declarations' => array(
					array(
						'selector'     => $navOpenerSelector,
						'declarations' => array( 'display' => 'none' ),
					),

					array(
						'selector'     => $navContentSelector,
						'declarations' => array(
							'display'  => 'block',
							'position' => 'relative',
							'width'    => '100%',
							'z-index'  => 'auto',
						),
					),

					array(
						'selector'     => $navContentSelector . ' .wp-block-navigation__responsive-container-close',
						'declarations' => array(
							'display' => 'none',
						),
					),

					array(
						'selector'     => $navSelector . ' .wp-block-navigation__responsive-container.is-menu-open .wp-block-navigation__submenu-container.wp-block-navigation__submenu-container.wp-block-navigation__submenu-container.wp-block-navigation__submenu-container',
						'declarations' => array(
							'left' => '0',
						),
					),
				),
			),
		);

		BlockUtils::add_styles_from_css_rules( $css_rules );
	}

	public static function get_title() {
		return __( 'Responsive Navigation', 'better-block-editor' );
	}

	public static function get_label() {
		return __( 'Add Responsive Settings to Navigation block.', 'better-block-editor' );
	}
}
