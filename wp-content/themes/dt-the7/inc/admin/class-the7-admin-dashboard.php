<?php
/**
 * The7 admin dashboard class.
 *
 * @package The7\Admin
 */

use The7\Admin\Pages\Admin_Page;
use The7\Admin\Pages\Dashboard;
use The7\Admin\Pages\Demo_Content;
use The7\Admin\Pages\Plugins;
use The7\Admin\Pages\Settings;
use The7\Admin\Pages\Status;

defined( 'ABSPATH' ) || exit;

/**
 * Class The7_Admin_Dashboard
 */
class The7_Admin_Dashboard {

	const UPDATE_DASHBOARD_SETTINGS_NONCE_ACTION = 'the7-update-dashboard_settings';

	/**
	 * Init admin dashboard. Add hooks and all the needed to dashboard works.
	 */
	public function init() {
		add_action( 'admin_menu', array( $this, 'add_menu_page' ) );
		add_action( 'after_switch_theme', array( $this, 'on_after_theme_switch' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'register_scripts' ) );
	}

	public function register_scripts() {
		the7_register_style( 'the7-admin-icon', PRESSCORE_ADMIN_URI . '/assets/css/the7-admin-icon.css' );
		the7_register_style( 'the7-dashboard', PRESSCORE_ADMIN_URI . '/assets/css/the7-dashboard.css' );
		the7_register_script( 'the7-dashboard', PRESSCORE_ADMIN_URI . '/assets/js/the7-dashboard.js', [], false, true );

		wp_enqueue_style( 'the7-admin-icon' );
		wp_enqueue_style( 'the7-dashboard' );
		wp_enqueue_script( 'the7-dashboard' );
	}

	/**
	 * Add admin pages.
	 */
	public function add_menu_page() {
		$dashboard         = new Dashboard();
		$the7_settings     = new Settings();
		$the7_plugins      = new Plugins();
		$the7_status       = new Status();

		$the7_page = $dashboard->add_menu_page( __( 'The7', 'the7mk2' ), '', 3 );

		add_action( 'load-' . $the7_page, array( $this, 'update_dashboard_settings_by_url' ) );

		$dashboard->add_submenu_page( $the7_settings );
		$dashboard->add_submenu_page( $the7_plugins );
		$dashboard->add_submenu_page( $the7_status );

		$dashboard_slug = $dashboard->get_slug();
		global $submenu;
		if ( isset( $submenu[ $dashboard_slug ] ) ) {
			$submenu[ $dashboard_slug ][0][0] = $dashboard->get_title();
		}
	}

	/**
	 * Enqueue common styles.
	 */
	public function enqueue_styles() {
		wp_enqueue_style( 'the7-dashboard' );
	}

	/**
	 * Enqueue common scripts.
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'the7-dashboard' );
	}

	/**
	 * Redirect to theme dashboard and disable splash-screen right away.
	 */
	public function on_after_theme_switch() {
		the7_admin_notices()->reset_notices();
		// Hide DB update notice by default...
		the7_admin_notices()->dismiss_notice( 'the7_updated' );

		$main_page_slug = 'the7-dashboard';
		wp_safe_redirect( admin_url( "admin.php?page=$main_page_slug" ) );
	}

	/**
	 * Update dashboard settings by url.
	 */
	public function update_dashboard_settings_by_url() {
		$settings_id = 'the7_dashboard_settings';

		if ( ! array_key_exists( $settings_id, $_GET ) || ! is_array( $_GET[ $settings_id ] ) ) {
			return;
		}

		if ( ! isset( $_GET[ '_wpnonce' ] ) || ! wp_verify_nonce( $_GET[ '_wpnonce' ], self::UPDATE_DASHBOARD_SETTINGS_NONCE_ACTION ) ) {
			return;
		}

		if ( ! current_user_can( 'edit_theme_options' ) ) {
			return;
		}

		$new_settings = wp_unslash( $_GET[ $settings_id ] );
		$settings_definition = The7_Admin_Dashboard_Settings::get_settings_definition();

		foreach ( $new_settings as $id => $value ) {
			if ( ! array_key_exists( $id, $settings_definition ) ) {
				continue;
			}

			$type = $settings_definition[ $id ]['type'];
			The7_Admin_Dashboard_Settings::set( $id, The7_Admin_Dashboard_Settings::sanitize_setting( $value, $type ) );
		}

		$this->on_after_theme_switch();
		exit;
	}
}
