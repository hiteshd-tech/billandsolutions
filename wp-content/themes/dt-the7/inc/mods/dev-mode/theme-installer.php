<?php
/**
 * @package The7
 */

namespace The7\Mods\Dev_Mode;

defined( 'ABSPATH' ) || exit;

/**
 * Theme installer class.
 */
class Theme_Installer {

	/**
	 * Bootstrap.
	 */
	public static function init() {
		add_filter( 'site_transient_update_themes', [ __CLASS__, 'force_theme_re_install_filter' ] );
	}

	/**
	 * Fix update_theme transient so theme could be re installed.
	 *
	 * @param null|Object $transient Transient object.
	 *
	 * @return mixed
	 */
	public static function force_theme_re_install_filter( $transient ) {
		if ( ! isset( $_GET['the7-force-update'] ) || ! check_ajax_referer( 'upgrade-theme_dt-the7', false, false ) || ! presscore_theme_is_activated() ) {
			return $transient;
		}

		if ( ! is_object( $transient ) ) {
			$transient           = new \stdClass();
			$transient->response = [];
		}

		$new_version      = THE7_VERSION;
		$download_version = ''; // Latest.
		$theme_template   = get_template();
		$the7_remote_api  = new \The7_Remote_API( presscore_get_purchase_code() );
		if ( ! empty( $_POST['version'] ) ) {
			$versions       = $the7_remote_api->get_available_theme_versions();
			$posted_version = sanitize_text_field( wp_unslash( $_POST['version'] ) );
			if ( $versions && in_array( $posted_version, $versions, true ) ) {
				$new_version      = $posted_version;
				$download_version = $posted_version;
			}
		}

		$transient->response[ $theme_template ] = [
			'theme'       => $theme_template,
			'new_version' => $new_version,
			'url'         => presscore_theme_update_get_changelog_url(),
			'package'     => $the7_remote_api->get_theme_download_url( $download_version ),
		];

		return $transient;
	}

}
