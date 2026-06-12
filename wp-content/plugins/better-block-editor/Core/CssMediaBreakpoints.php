<?php
/**
 * Utility class for logic related to CSS media breakpoints.
 *
 * @package BetterBlockEditor
 */

namespace BetterBlockEditor\Core;

use BetterBlockEditor\Core\Settings;

defined( 'ABSPATH' ) || exit;

class CssMediaBreakpoints {
	// these names must be synchronized with names in Settings
	const BREAKPOINT_NAME_TABLET = 'tablet';
	const BREAKPOINT_NAME_MOBILE = 'mobile';
	const BREAKPOINT_NAME_CUSTOM = 'custom';
	const BREAKPOINT_NAME_OFF    = '';

	public static function getSwitchWidth( $breakpoint, $breakpoint_value = null ) {
		$user_defined_breakpoints = Settings::get_user_defined_breakpoints();

		if ( $breakpoint === self::BREAKPOINT_NAME_CUSTOM ) {
			return $breakpoint_value;
		}

		if ( isset( $user_defined_breakpoints[ $breakpoint ] ) ) {
			return $user_defined_breakpoints[ $breakpoint ]['value'] . $user_defined_breakpoints[ $breakpoint ]['unit'];
		}

		return null;
	}
}
