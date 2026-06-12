<?php
/**
 * Remote API helper for theme content.
 *
 * Provides Better Block Theme metadata from the remote repository.
 *
 * @package BetterBlockEditor
 */

namespace BetterBlockEditor\Modules\DemoContent\RemoteAPI;

defined( 'ABSPATH' ) || exit;

/**
 * Provides access to remote Better Block Theme metadata.
 */
class Theme {

	const THEME_LIST_URL   = 'https://repo.wpbbe.io/themes/list.json';
	const CACHE_KEY_THEMES = 'wpbbe_theme_list';
	const DEFAULT_SLUG     = 'better-block-theme';

	/**
	 * Returns Better Block Theme metadata fetched from remote JSON definition.
	 *
	 * @param string $slug Theme slug to retrieve.
	 * @return array Theme information or empty array when unavailable.
	 */
	public static function get_theme_info( $slug = self::DEFAULT_SLUG ) {
		$themes = self::get_theme_list();

		if ( isset( $themes[ $slug ] ) && is_array( $themes[ $slug ] ) ) {
			return $themes[ $slug ];
		}

		return array();
	}

	/**
	 * Removes cached remote data.
	 *
	 * @return void
	 */
	public static function clear_cache() {
		delete_transient( self::CACHE_KEY_THEMES );
	}

	/**
	 * Fetches a list of themes from remote API with a short-lived cache.
	 *
	 * @return array Associative array of theme metadata.
	 */
	protected static function get_theme_list() {
		$cached = get_transient( self::CACHE_KEY_THEMES );
		if ( is_array( $cached ) ) {
			return $cached;
		}

		$response = wp_safe_remote_get(
			self::THEME_LIST_URL,
			array(
				'timeout'    => 15,
				'user-agent' => 'WordPress/' . get_bloginfo( 'version' ) . '; ' . network_site_url(),
			)
		);

		if ( is_wp_error( $response ) ) {
			return array();
		}

		$code = (int) wp_remote_retrieve_response_code( $response );
		$body = wp_remote_retrieve_body( $response );

		if ( 200 !== $code || ! $body ) {
			return array();
		}

		$data = json_decode( $body, true );
		if ( is_array( $data ) && ! empty( $data ) ) {
			set_transient( self::CACHE_KEY_THEMES, $data, 5 * MINUTE_IN_SECONDS );
			return $data;
		}

		return array();
	}
}
