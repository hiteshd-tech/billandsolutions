<?php
/**
 * Remote API helper for demo content.
 *
 * Handles remote requests to fetch demo lists and packages.
 *
 * @package BetterBlockEditor
 */

namespace BetterBlockEditor\Modules\DemoContent\RemoteAPI;

defined( 'ABSPATH' ) || exit;

/**
 * Provides helper methods for retrieving remote demo packages.
 */
class Demo {

	const API_DEMO_CONTENT_DOWNLOAD_URL = 'https://repo.wpbbe.io/templates/';

	/**
	 * Retrieves the list of available demos from the remote API.
	 *
	 * @return array Associative array of remote demos.
	 */
	public static function get_demos() {
		// Use transient cache to avoid frequent remote requests.
		$transient_key = 'wpbbe_demo_list';

		$cached = get_transient( $transient_key );
		if ( is_array( $cached ) ) {
			return $cached;
		}

		$remote_url = trailingslashit( self::API_DEMO_CONTENT_DOWNLOAD_URL ) . 'list.json';

		$response = wp_safe_remote_get(
			$remote_url,
			array(
				'timeout'    => 15,
				'user-agent' => 'WordPress/' . get_bloginfo( 'version' ) . '; ' . network_site_url(),
			)
		);

		if ( ! is_wp_error( $response ) ) {
			$code = (int) wp_remote_retrieve_response_code( $response );
			$body = wp_remote_retrieve_body( $response );

			if ( 200 === $code && $body ) {
				$data = json_decode( $body, true );
				if ( is_array( $data ) && ! empty( $data ) ) {
					// Cache for 5 minutes.
					set_transient( $transient_key, $data, 5 * MINUTE_IN_SECONDS );
					return $data;
				}
			}
		}

		// Return empty array on failure.
		return array();
	}

	/**
	 * Clear cached demo list.
	 *
	 * Can be called on admin hooks that signal a user-triggered update check.
	 */
	public static function clear_demos_cache() {
		delete_transient( 'wpbbe_demo_list' );
	}

	/**
	 * Downloads demo content by identifier into the specified directory.
	 *
	 * Creates the target directory when needed and extracts the archive.
	 *
	 * @param string $id         Demo identifier.
	 * @param string $target_dir Destination directory path.
	 * @return string|\WP_Error Path where dummy content files are located on success or WP_Error on failure.
	 */
	public static function download_demo( $id, $target_dir ) {
		/**
		 * WordPress filesystem abstraction used for file operations.
		 *
		 * @var \WP_Filesystem_Base $wp_filesystem
		 */
		global $wp_filesystem;

		$strings = array(
			'fs_unavailable'  => __( 'File system is unavailable.', 'better-block-editor' ),
			/* translators: Placeholder is the folder path that was expected to exist. */
			'fs_no_folder'    => __( 'No folder found at %s.', 'better-block-editor' ),
			/* translators: Placeholder is the HTTP response code returned by the remote server. */
			'download_failed' => __( 'Failed to download template content. HTTP response code: %d', 'better-block-editor' ),
		);

		if ( ! $wp_filesystem && ! WP_Filesystem() ) {
			return new \WP_Error( 'fs_unavailable', $strings['fs_unavailable'] );
		}

		if ( is_wp_error( $wp_filesystem->errors ) && $wp_filesystem->errors->get_error_code() ) {
			return $wp_filesystem->errors;
		}

		$request_url     = self::API_DEMO_CONTENT_DOWNLOAD_URL . '/' . sanitize_file_name( $id . '.zip' );
		$remote_response = wp_safe_remote_get(
			$request_url,
			array(
				'timeout'    => 300,
				'user-agent' => 'WordPress/' . get_bloginfo( 'version' ) . '; ' . network_site_url(),
			)
		);

		if ( is_wp_error( $remote_response ) ) {
			return $remote_response;
		}

		$response_code = (int) wp_remote_retrieve_response_code( $remote_response );

		if ( ! is_array( $remote_response ) || 200 !== $response_code ) {
			return new \WP_Error(
				'download_failed',
				sprintf( $strings['download_failed'], $response_code )
			);
		}

		wp_mkdir_p( $target_dir );

		$file_content  = wp_remote_retrieve_body( $remote_response );
		$zip_file_name = trailingslashit( $target_dir ) . "{$id}.zip";
		$wp_filesystem->put_contents( $zip_file_name, $file_content );

		$unzip_result = unzip_file( $zip_file_name, $target_dir );
		if ( is_wp_error( $unzip_result ) ) {
			return $unzip_result;
		}

		$dummy_dir = trailingslashit( $target_dir ) . $id;

		if ( ! is_dir( $dummy_dir ) ) {
			return new \WP_Error( 'fs_no_folder', sprintf( $strings['fs_no_folder'], $dummy_dir ) );
		}

		return $dummy_dir;
	}
}
