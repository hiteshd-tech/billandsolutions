<?php
/**
 * AJAX handler to install and activate Better Block Theme.
 *
 * @package BetterBlockEditor
 */

namespace BetterBlockEditor\Modules\DemoContent\AjaxHandlers;

use BetterBlockEditor\Modules\DemoContent\RemoteAPI\Theme as RemoteApiTheme;

defined( 'ABSPATH' ) || exit;

/**
 * Handles theme installation requests triggered from the demo importer.
 */
class ThemeInstallAjaxHandler extends AjaxHandlerBase {

	/**
	 * Registers the theme install AJAX hook.
	 *
	 * @return void
	 */
	public static function register() {
		add_action( 'wp_ajax_wpbbe_install_bb_theme', array( self::class, 'install_theme' ) );
	}

	/**
	 * Installs or activates the Better Block Theme via AJAX.
	 *
	 * @return void
	 */
	public static function install_theme() {
		$slug = RemoteApiTheme::DEFAULT_SLUG;

		$theme = wp_get_theme( $slug );

		// If theme is already installed.
		if ( $theme && $theme->exists() ) {
			// If it's not the active theme, activate it.
			if ( get_template() !== $slug ) {
				switch_theme( $slug );
				wp_send_json_success( array( 'message' => __( 'Theme activated.', 'better-block-editor' ) ) );
			} else {
				wp_send_json_success( array( 'message' => __( 'Theme already installed and active.', 'better-block-editor' ) ) );
			}
			return;
		}

		if ( ! function_exists( 'wp_ajax_install_theme' ) ) {
			wp_send_json_error( array( 'message' => __( 'Theme installation function not found.', 'better-block-editor' ) ) );
			return;
		}

		// Not installed — proceed with installation.
		$_POST['slug'] = $slug;
		add_filter( 'themes_api', array( self::class, 'override_theme_information_api' ), 10, 3 );
		wp_ajax_install_theme();
		switch_theme( $slug );
	}

	/**
	 * Overrides the theme information API response for bundled themes.
	 *
	 * @param mixed  $res    Original response.
	 * @param string $action Current action.
	 * @param object $args   Request arguments.
	 * @return mixed Modified response.
	 */
	public static function override_theme_information_api( $res, $action, $args ) {
		if ( $res ) {
			return $res;
		}

		if ( ! isset( $args->slug ) || $args->slug !== RemoteApiTheme::DEFAULT_SLUG ) {
			return $res;
		}

		$theme_data = RemoteApiTheme::get_theme_info( $args->slug );

		if ( ! empty( $theme_data ) ) {
			$res = (object) $theme_data;
		}

		return $res;
	}
}
