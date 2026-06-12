<?php
/**
 * @package The7
 */

namespace The7\Mods\Dev_Mode;

defined( 'ABSPATH' ) || exit;

/**
 * Admin page class.
 */
class Admin_Page {

	/**
	 * Bootstrap page.
	 */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'add_menu_page' ) );
		add_action(
			'admin_head',
			static function () {
				// Hide dev admin page.
				remove_menu_page( 'the7-dev' );
			}
		);
	}

	/**
	 * Add admin menu.
	 */
	public static function add_menu_page() {
		global $menu;

		$page_slug = add_menu_page(
			__( 'The7 Dev', 'the7mk2' ),
			__( 'The7 Dev', 'the7mk2' ),
			'switch_themes',
			'the7-dev',
			array( __CLASS__, 'display_page' )
		);

		add_action( "admin_print_styles-{$page_slug}", array( __CLASS__, 'enqueue_styles' ) );
	}

	/**
	 * Display admin page.
	 */
	public static function display_page() {
		include __DIR__ . '/views/the7-dev-page.php';
	}

	/**
	 * Enqueue page assets.
	 */
	public static function enqueue_styles() {
		wp_enqueue_style( 'the7-dashboard' );
	}
}
