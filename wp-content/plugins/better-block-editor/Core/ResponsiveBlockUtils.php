<?php
/**
 * Utility class for handling block responsiveness settings and functionality.
 *
 * @package BetterBlockEditor
 */

namespace BetterBlockEditor\Core;

use BetterBlockEditor\Core\BlockUtils;
use BetterBlockEditor\Core\CssMediaBreakpoints;

defined( 'ABSPATH' ) || exit;

class ResponsiveBlockUtils {

	/**
	 * The name of the attributes group used for responsive settings.
	 * Use it only for block specific settings to avoid name conflicts with other modules.
	 *
	 * @var string
	 */
	const ATTRIBUTES_GROUP_NAME = 'wpbbeResponsive';

	/**
	 * The key representing the breakpoint in responsive settings.
	 *
	 * @var string
	 */
	const BREAKPOINT_KEY = 'breakpoint';

	/**
	 * The key for specifying a custom value for the breakpoint.
	 *
	 * @var string
	 */

	const BREAKPOINT_CUSTOM_VALUE_KEY = 'breakpointCustomValue';

	/**
	 * The key used to access settings within the responsive block utilities.
	 *
	 * @var string
	 */
	const SETTINGS_KEY = 'settings';


	/**
	 * Determines if the given block has attributes for responsiveness.
	 *
	 * @param mixed $block The block to check for responsiveness.
	 * @return bool
	 */
	public static function is_responsive( $block ) {
		return is_array( $block['attrs'][ self::ATTRIBUTES_GROUP_NAME ] ?? null );
	}

	/**
	 * Retrieves a responsive setting from the block attributes.
	 *
	 * @param array  $attributes The block attributes.
	 * @param string $setting_name The name of the responsive setting to retrieve.
	 * @param mixed  $default      The default value to return if the setting is not set.
	 *
	 * @return mixed|null The value of the responsive setting, or null if not set.
	 */
	public static function get_setting( $attributes, $responsive_setting_name, $default = null ) {
		return $attributes[ self::ATTRIBUTES_GROUP_NAME ][ self::SETTINGS_KEY ][ $responsive_setting_name ] ?? $default;
	}

	public static function get_switch_width( $attributes ) {
		return CssMediaBreakpoints::getSwitchWidth(
			$attributes[ self::ATTRIBUTES_GROUP_NAME ][ self::BREAKPOINT_KEY ] ?? null,
			$attributes[ self::ATTRIBUTES_GROUP_NAME ][ self::BREAKPOINT_CUSTOM_VALUE_KEY ] ?? null
		);
	}

	/**
	 * Adds CSS style declarations for a specific media query and selector.
	 * Just a useful wrapper for self::add_styles_from_css_rules().
	 *
	 * @param string $media_query   The media query condition (e.g., '@media screen and (width <= 500px)').
	 * @param string $selector      The CSS selector to which the declarations will apply.
	 * @param array  $css_rules     An associative array of CSS properties and their values.
	 * @return void
	 */
	public static function add_style_for_media_query( $media_query, $selector, $css_rules ) {
		return BlockUtils::add_styles_from_css_rules(
			array(
				array(
					'selector'     => $media_query,
					'declarations' => array(
						array(
							'selector'     => $selector,
							'declarations' => $css_rules,
						),
					),
				),
			)
		);
	}
}
