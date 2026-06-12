<?php
/**
 * Font downloader REST API integration.
 *
 * Registers REST endpoints used by the font downloader UI.
 *
 * @package BetterBlockEditor
 */

namespace BetterBlockEditor\Modules\DemoContent;

use WP_Error;
use WP_REST_Request;

defined( 'ABSPATH' ) || exit;

/**
 * Handles REST API for Font Downloader.
 */
class FontDownloaderRestAPI {

	/**
	 * Handles font file processing tasks.
	 *
	 * @var FontDownloader
	 */
	private $font_downloader;

	/**
	 * Constructor.
	 *
	 * @param FontDownloader $font_downloader Font downloader service.
	 */
	public function __construct( FontDownloader $font_downloader ) {
		$this->font_downloader = $font_downloader;
	}

	/**
	 * Initialize all necessary hooks.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'rest_api_init', array( $this, 'register_rest_route' ) );
	}

	/**
	 * Create REST API endpoint to download fonts.
	 *
	 * @return void
	 */
	public function register_rest_route() {
		register_rest_route(
			'wpbbe/v1',
			'/fse-font',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'rest_upload_font' ),
				'permission_callback' => array( $this, 'check_permission' ),
			)
		);

		register_rest_route(
			'wpbbe/v1',
			'/fse-fonts',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'rest_get_fonts' ),
				'permission_callback' => array( $this, 'check_permission' ),
			)
		);
	}

	/**
	 * REST callback to retrieve the list of fonts to download.
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return array List of font filenames to download.
	 */
	public function rest_get_fonts( WP_REST_Request $request ) {
		unset( $request );

		return $this->font_downloader->get_fonts_to_download();
	}

	/**
	 * Check if the user has permission to download fonts.
	 *
	 * @return bool
	 */
	public function check_permission() {
		return current_user_can( 'switch_themes' );
	}

	/**
	 * Upload font.
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_Error|array Array of upload details on success or error object on failure.
	 */
	public function rest_upload_font( WP_REST_Request $request ) {
		$file_params = $request->get_file_params();

		if ( empty( $file_params['font'] ) ) {
			return new WP_Error( 'no_font', 'No font to download', array( 'status' => 400 ) );
		}

		$font     = $file_params['font'];
		$filename = $font['name'];
		$font_dir = untrailingslashit( wp_font_dir()['basedir'] );

		if ( file_exists( $font_dir . '/' . $filename ) ) {
			$this->font_downloader->font_was_uploaded( $filename );

			return array(
				'info' => 'Font already exists',
			);
		}

		$downloaded_font = $this->font_downloader->handle_font_file_upload( $font );
		if ( ! is_wp_error( $downloaded_font ) ) {
			$this->font_downloader->font_was_uploaded( $filename );
		}

		return $downloaded_font;
	}
}
