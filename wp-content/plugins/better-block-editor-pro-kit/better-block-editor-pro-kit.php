<?php
/**
 * Plugin Name:       BBE Plus
 * Description:       This plugin adds pro features to Better Block Editor.
 * Requires at least: 6.5
 * Requires PHP:      7.4
 * Requires Plugins:  better-block-editor
 * Version:           1.2.0
 * Author:            Dream Theme
 * License:           Proprietary
 * Text Domain:       better-block-editor
 *
 * @package           BbeProKit
 */

defined( 'ABSPATH' ) || exit;

define( 'BBE_PRO_KIT_VERSION', '1.2.0' );
/**
 * All versions should be without patch, in order to compare them properly.
 * For example, 1.0.0, 1.0.1, 1.1.0, 2.0.0, 2.1.0, 3.0.0
 */

define( 'BBE_PRO_KIT_REQUIRED_CORE_VERSION', '1.3.0' );
define( 'BBE_PRO_KIT_RECOMMENDED_CORE_VERSION', '1.3.0' );

define( 'BBE_PRO_KIT_FILE', __FILE__ );
define( 'BBE_PRO_KIT_DIR', plugin_dir_path( BBE_PRO_KIT_FILE ) );
define( 'BBE_PRO_KIT_BASE', plugin_basename( BBE_PRO_KIT_FILE ) );
define( 'BBE_PRO_KIT_URL', plugins_url( '/', BBE_PRO_KIT_FILE ) );
define( 'BBE_PRO_KIT_URL_DIST', BBE_PRO_KIT_URL . 'dist/' );
define( 'BBE_PRO_KIT_DIST', BBE_PRO_KIT_DIR . 'dist/' );
define( 'BBE_PRO_KIT_ULR_LIB_ASSETS', BBE_PRO_KIT_URL . 'lib/' );
define( 'BBE_PRO_KIT_BLOCKS_DIR', BBE_PRO_KIT_DIST . 'blocks/' );
define( 'BBE_PRO_KIT_TEMPLATES_DIR', BBE_PRO_KIT_DIR . 'templates/' );
define( 'BBE_PRO_KIT_PLUGIN_ID', 'bbe-pro-kit' );
define( 'BBE_PRO_KIT_PLUGIN_NAME', 'BBE Plus' );
define( 'BBE_PRO_KIT_REST_BASE', 'wpbbe-pro/v1' );

define( 'BBE_PRO_KIT_CORE_PLUGIN_NAME', 'Better Block Editor' );
define( 'BBE_PRO_KIT_CORE_PATH', 'better-block-editor/better-block-editor.php' );

// Load Composer autoload.
if ( file_exists( BBE_PRO_KIT_DIR . 'vendor/autoload.php' ) ) {
	require_once BBE_PRO_KIT_DIR . 'vendor/autoload.php';
}

function bbe_pro_kit_load_plugin() {
	if ( ! did_action( 'wpbbe/loaded' ) && ! did_action( 'wpbbe/init' ) ) {
		add_action( 'admin_notices', 'bbe_pro_kit_load_error' );

		return;
	}

	if ( ! defined( 'WPBBE_VERSION' ) || ! bbe_pro_kit_compare_version( WPBBE_VERSION, BBE_PRO_KIT_REQUIRED_CORE_VERSION, '>=' ) ) {
		add_action( 'admin_notices', 'bbe_pro_kit_load_error_out_of_date' );

		return;
	}

	if ( ! bbe_pro_kit_compare_version( WPBBE_VERSION, BBE_PRO_KIT_RECOMMENDED_CORE_VERSION, '>=' ) ) {
		add_action( 'admin_notices', 'bbe_pro_kit_load_error_upgrade_recommended' );
	}

	require_once BBE_PRO_KIT_DIR . 'plugin.php';
}

function bbe_pro_kit_compare_version( $version1, $version2, $operator ) {
	// Remove any suffix (e.g., '-a9', '-rc1') using regex
	$pattern        = '/-.*$/';
	$version1_clean = preg_replace( $pattern, '', $version1 );
	$version2_clean = preg_replace( $pattern, '', $version2 );

	return version_compare( $version1_clean, $version2_clean, $operator );
}

add_action( 'plugins_loaded', 'bbe_pro_kit_load_plugin' );

if ( ! function_exists( 'bbe_pro_kit_load_error' ) ) {
	function bbe_pro_kit_load_error() {
		$screen = get_current_screen();
		if ( isset( $screen->parent_file ) && 'plugins.php' === $screen->parent_file && 'update' === $screen->id ) {
			return;
		}

		if ( is_wpbbe_core_installed() ) {
			if ( ! current_user_can( 'activate_plugins' ) ) {
				return;
			}

			$activation_url = wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . BBE_PRO_KIT_CORE_PATH . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . BBE_PRO_KIT_CORE_PATH );

			$message  = '<h3>' . sprintf( /* translators: 1: Plugin name.*/ esc_html__( 'You\'re not using %1$s yet!', 'bbe-pro-kit' ), BBE_PRO_KIT_CORE_PLUGIN_NAME ) . '</h3>';
			$message .= '<p>' . sprintf( /* translators: 1: Plugin name. 2: Core plugin name.*/ esc_html__( 'Activate the %2$s plugin to start using all of %1$s plugin’s features.', 'bbe-pro-kit' ), BBE_PRO_KIT_PLUGIN_NAME, BBE_PRO_KIT_CORE_PLUGIN_NAME ) . '</p>';
			$message .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', $activation_url, esc_html__( 'Activate Now', 'bbe-pro-kit' ) ) . '</p>';
		} else {
			if ( ! current_user_can( 'install_plugins' ) ) {
				return;
			}

			$install_url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=better-block-editor' ), 'install-plugin_better-block-editor' );

			$message  = '<h3>' . sprintf( /* translators: 1: Plugin name. 2: Core plugin name.*/ esc_html__( '%1$s plugin requires installing the %2$s plugin', 'bbe-pro-kit' ), BBE_PRO_KIT_PLUGIN_NAME, BBE_PRO_KIT_CORE_PLUGIN_NAME ) . '</h3>';
			$message .= '<p>' . sprintf( /* translators: 1: Plugin name.*/ esc_html__( 'Install and activate the %1$s plugin to access all the Pro features.', 'bbe-pro-kit' ), BBE_PRO_KIT_CORE_PLUGIN_NAME ) . '</p>';
			$message .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', $install_url, esc_html__( 'Install Now', 'bbe-pro-kit' ) ) . '</p>';
		}

		bbe_pro_kit_print_error( $message );
	}
}

if ( ! function_exists( 'bbe_pro_kit_load_error_out_of_date' ) ) {
	function bbe_pro_kit_load_error_out_of_date() {
		if ( ! current_user_can( 'update_plugins' ) ) {
			return;
		}

		$upgrade_link = wp_nonce_url( self_admin_url( 'update.php?action=upgrade-plugin&plugin=' ) . BBE_PRO_KIT_CORE_PATH, 'upgrade-plugin_' . BBE_PRO_KIT_CORE_PATH );

		$message = sprintf(
			'<h3>%1$s</h3><p>%2$s <a href="%3$s" class="button-primary">%4$s</a></p>',
			sprintf(
			/* translators: 1: Plugin name, 2: Core plugin name */
				esc_html__( '%1$s requires a newer version of the %2$s plugin.', 'bbe-pro-kit' ),
				BBE_PRO_KIT_PLUGIN_NAME,
				BBE_PRO_KIT_CORE_PLUGIN_NAME
			),
			sprintf(
			/* translators: 1: Core plugin name, 2: Plugin name */
				esc_html__( 'Update the %1$s plugin to reactivate the %2$s plugin.', 'bbe-pro-kit' ),
				BBE_PRO_KIT_CORE_PLUGIN_NAME,
				BBE_PRO_KIT_PLUGIN_NAME
			),
			esc_url( $upgrade_link ),
			esc_html__( 'Update Now', 'bbe-pro-kit' )
		);
		bbe_pro_kit_print_error( $message );
	}
}

if ( ! function_exists( 'bbe_pro_kit_load_error_upgrade_recommended' ) ) {
	function bbe_pro_kit_load_error_upgrade_recommended() {
		if ( ! current_user_can( 'update_plugins' ) ) {
			return;
		}

		$upgrade_link = wp_nonce_url( self_admin_url( 'update.php?action=upgrade-plugin&plugin=' ) . BBE_PRO_KIT_CORE_PATH, 'upgrade-plugin_' . BBE_PRO_KIT_CORE_PATH );

		$message = sprintf( '<h3>%1$s</h3><p>%2$s <a href="%3$s" class="button-primary">%4$s</a></p>', esc_html__( 'Don’t miss out on the new version of The7 Block Editor', 'bbe-pro-kit' ), esc_html__( 'Update to the latest version of The7 Block Editor to enjoy new features, better performance and compatibility.', 'bbe-pro-kit' ), $upgrade_link, esc_html__( 'Update Now', 'bbe-pro-kit' ) );
		bbe_pro_kit_print_error( $message );
	}
}

if ( ! function_exists( 'bbe_pro_kit_print_error' ) ) {
	function bbe_pro_kit_print_error( $message ) {
		if ( ! $message ) {
			return;
		}
		// PHPCS - $message should not be escaped
		echo '<div class="error">' . $message . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
if ( ! function_exists( 'is_wpbbe_core_installed' ) ) {
	function is_wpbbe_core_installed() {

		$installed_plugins = get_plugins();

		return isset( $installed_plugins[ BBE_PRO_KIT_CORE_PATH ] );
	}
}
