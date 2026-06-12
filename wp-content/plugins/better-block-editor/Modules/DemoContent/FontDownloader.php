<?php
/**
 * Font downloader utility.
 *
 * Handles downloading fonts required by demo content packages.
 *
 * @package BetterBlockEditor
 */

namespace BetterBlockEditor\Modules\DemoContent;

use WP_Error;
use WP_Font_Utils;

defined( 'ABSPATH' ) || exit;

/**
 * Font downloader utility class.
 */
class FontDownloader {

	const FONTS_OPTION_NAME = 'wpbbe_fse_fonts_to_download';

	/**
	 * Shared singleton instance.
	 *
	 * @var FontDownloader
	 */
	public static $instance;

	/**
	 * Get singleton instance.
	 *
	 * @return FontDownloader Font downloader instance.
	 */
	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Extracts font-face entries from a theme.json payload.
	 *
	 * @param string $json Raw JSON string containing typography settings.
	 * @return array List of font-face definitions discovered in the JSON.
	 */
	public function get_fonts_from_json( $json ) {
		$this->reset_fonts_to_download();
		$fonts = array();
		$data  = json_decode( $json, true );

		if ( ! $data || ! isset( $data['settings']['typography']['fontFamilies']['custom'] ) ) {
			return array();
		}

		$font_families = $data['settings']['typography']['fontFamilies']['custom'];
		foreach ( $font_families as $font_family ) {
			if ( empty( $font_family['fontFace'] ) || ! is_array( $font_family['fontFace'] ) ) {
				continue;
			}
			$fonts = array_merge( $fonts, $font_family['fontFace'] );
		}

		$this->set_fonts_to_download( $fonts );

		return $fonts;
	}

	/**
	 * Reads existing font files from the font folder.
	 *
	 * @return array List of existing font files.
	 */
	public function fetch_existing_fonts() {
		$font_dir   = untrailingslashit( wp_get_font_dir()['basedir'] );
		$font_files = array_merge(
			(array) glob( $font_dir . '/*.woff' ),
			(array) glob( $font_dir . '/*.woff2' ),
			(array) glob( $font_dir . '/*.ttf' ),
			(array) glob( $font_dir . '/*.otf' )
		);

		return array_map( 'basename', $font_files );
	}

	/**
	 * Handles the upload of a font file using wp_handle_upload().
	 *
	 * @param array $file Single file item from $_FILES.
	 *
	 * @return array|WP_Error Array containing uploaded file attributes on success, or WP_Error object on failure.
	 */
	public function handle_font_file_upload( $file ) {
		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		$mimes_hook_name = 'upload_' . 'mimes'; // phpcs:ignore Generic.Strings.UnnecessaryStringConcat
		add_filter( $mimes_hook_name, array( 'WP_Font_Utils', 'get_allowed_font_mime_types' ) );
		// Filter the upload directory to return the fonts directory.
		add_filter( 'upload_dir', '_wp_filter_font_directory' );

		$overrides = array(
			'upload_error_handler' => array( $this, 'handle_font_file_upload_error' ),
			// Not testing a form submission.
			'test_form'            => false,
			// Only allow uploading font files for this request.
			'mimes'                => WP_Font_Utils::get_allowed_font_mime_types(),
		);

		$uploaded_file = wp_handle_upload( $file, $overrides );

		remove_filter( 'upload_dir', '_wp_filter_font_directory' );
		remove_filter( $mimes_hook_name, array( 'WP_Font_Utils', 'get_allowed_font_mime_types' ) );

		return $uploaded_file;
	}

	/**
	 * Handles file upload error.
	 *
	 * @param array  $file    File upload data.
	 * @param string $message Error message from wp_handle_upload().
	 *
	 * @return WP_Error WP_Error object.
	 */
	public function handle_font_file_upload_error( $file, $message ) {
		$status = 500;
		$code   = 'rest_font_upload_unknown_error';

		if ( __( 'Sorry, you are not allowed to upload this file type.', 'better-block-editor' ) === $message ) {
			$status = 400;
			$code   = 'rest_font_upload_invalid_file_type';
		}

		return new WP_Error( $code, $message, array( 'status' => $status ) );
	}

	/**
	 * Get the list of fonts to download.
	 *
	 * @return array List of fonts to download (full font-face structures).
	 */
	public function get_fonts_to_download() {
		$fonts_to_download = get_transient( self::FONTS_OPTION_NAME );

		return $fonts_to_download ? (array) $fonts_to_download : array();
	}

	/**
	 * Reset the list of fonts to download.
	 *
	 * @return void
	 */
	public function reset_fonts_to_download() {
		delete_transient( self::FONTS_OPTION_NAME );
	}

	/**
	 * Set the list of fonts to download.
	 *
	 * @param array $fonts_list List of fonts to download.
	 * @return void
	 */
	public function set_fonts_to_download( $fonts_list ) {
		set_transient( self::FONTS_OPTION_NAME, $fonts_list, HOUR_IN_SECONDS * 12 );
	}

	/**
	 * Remove the font from the list of fonts to download.
	 *
	 * @param string $font Font file name.
	 * @return void
	 */
	public function font_was_uploaded( $font ) {
		$fonts_to_download = $this->get_fonts_to_download();

		// Filter out any font-face entries whose src basename matches the uploaded filename.
		$fonts_to_download = array_values(
			array_filter(
				$fonts_to_download,
				static function ( $font_face ) use ( $font ) {
					if ( ! is_array( $font_face ) ) {
						// If for some reason the cached value is not an array, keep it unchanged.
						return true;
					}

					$src = $font_face['src'] ?? '';
					if ( is_array( $src ) ) {
						$src = array_values( $src )[0] ?? '';
					}
					$src      = is_string( $src ) ? $src : '';
					$basename = $src !== '' ? basename( $src ) : '';

					return $basename !== $font;
				}
			)
		);

		$this->set_fonts_to_download( $fonts_to_download );
	}
}
