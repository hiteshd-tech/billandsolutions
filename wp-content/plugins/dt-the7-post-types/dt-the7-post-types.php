<?php
/**
 * Plugin Name: 	  The7 Post Types
 * Description: 	  Custom Post Types for The7
 * Version:			  1.0.0
 * Requires PHP:      8.0.0
 * Requires at least: 6.8.0
 * Author:            Dream-Theme
 * Author URI:        https://dream-theme.com/
 * Text Domain:       dt-the7-post-types
 * Domain Path:       /languages
 */

defined( 'ABSPATH' ) || exit;

class The7_Post_Type_Builder_Plugin {
	public static function assets_url( $path = '' ) {
		return plugin_dir_url( __FILE__ ) . 'assets/' . ltrim( $path, '/' );
	}

	public static function version() {
		return '1.0.0';
	}
}

add_action(
	'plugins_loaded',
	static function () {
		$should_bail = false;
		if ( class_exists( \The7PT_Core::class ) && method_exists( \The7PT_Core::class, 'instance' ) ) {
			$the7_core = \The7PT_Core::instance();
			if ( is_object( $the7_core ) && method_exists( $the7_core, 'version' ) ) {
				$should_bail = version_compare( $the7_core->version(), '2.8.0', '<' );
			}
		}

		if ( $should_bail ) {
			return;
		}

		require_once plugin_dir_path( __FILE__ ) . 'src/post-type-builder.php';
	}
);
