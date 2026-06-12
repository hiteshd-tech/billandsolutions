<?php

namespace The7_Core\Mods\Post_Type_Builder\Bundled;

defined( 'ABSPATH' ) || exit;

abstract class Bundled_Item {

	/**
	 * @return string
	 */
	abstract public static function get_name();

	/**
	 * @return array
	 */
	abstract public static function get_args();

	/**
	 * @return string
	 */
	public static function get_module_name() {
		return '';
	}

	/**
	 * @return bool
	 */
	public static function is_active() {
		$module = static::get_module_name();
		if ( ! $module ) {
			return true;
		}

		$supported_modules = get_theme_support( 'presscore-modules' );

		if ( empty( $supported_modules[0] ) ) {
			return false;
		}

		return in_array( $module, (array) $supported_modules[0], true );
	}
}
