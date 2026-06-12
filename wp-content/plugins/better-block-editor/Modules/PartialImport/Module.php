<?php
/**
 * Provides partial import capabilities triggered from the block editor paste events.
 *
 * @package BetterBlockEditor
 */

namespace BetterBlockEditor\Modules\PartialImport;

use BetterBlockEditor\Base\ModuleBase;
use BetterBlockEditor\Modules\UploadSVG\Module as UploadSVGModule;
use BetterBlockEditor\Plugin;

defined( 'ABSPATH' ) || exit;

class Module extends ModuleBase {

	private const NONCE_ACTION = 'custom-paste-image-download';
	private const CACHE_KEY    = 'the7_fse_downloaded_attachments';
	private const CACHE_TTL    = 3 * MONTH_IN_SECONDS;
	private const BATCH_SIZE   = 3;
	private const UPLOAD_SVG_FEATURE = 'upload-svg';

	const IS_CORE_MODULE = true;

	const MODULE_IDENTIFIER = 'partial-import';
	const ASSETS_BUILD_PATH = 'editor/plugins/partial-import/';


	/**
	 * Cached value of the downloaded attachments transient to minimise lookups.
	 *
	 * @var array|null
	 */
	private $download_cache;

	public function setup_hooks() {
		add_filter( 'wpbbe_script_data', array( $this, 'add_script_data' ) );

		add_action( 'wp_ajax_custom_paste_download_image_batch', array( $this, 'handle_batch_image_request' ) );
	}

	public function add_script_data( $data ) {
		$data['wpbbePasteConfig'] = array(
			'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
			'ajaxNonce' => wp_create_nonce( self::NONCE_ACTION ),
			'batchSize' => self::BATCH_SIZE,
			'debug'     => ( defined( 'WP_DEBUG' ) && WP_DEBUG ),
			'siteUrl'   => get_site_url(),
		);

		return $data;
	}


	/**
	 * AJAX endpoint for downloading multiple images in one request.
	 */
	public function handle_batch_image_request() {
		check_ajax_referer( self::NONCE_ACTION, 'nonce' );

		if ( ! current_user_can( 'upload_files' ) ) {
			wp_send_json_error(
				array( 'message' => __( 'You do not have permission to upload files', 'better-block-editor' ) ),
				403
			);
		}

		$image_urls_json = isset( $_POST['image_urls'] ) ? sanitize_text_field( wp_unslash( $_POST['image_urls'] ) ) : '';

		if ( empty( $image_urls_json ) ) {
			$image_urls = array();
		} else {
			$image_urls = json_decode( $image_urls_json, true );
		}

		if ( ! is_array( $image_urls ) ) {
			wp_send_json_error(
				array( 'message' => __( 'Invalid image URLs format', 'better-block-editor' ) ),
				400
			);
		}

		if ( function_exists( 'ini_get' ) && function_exists( 'set_time_limit' ) ) {
			$max_execution_time = ini_get( 'max_execution_time' );
			$min_execution_time = 5 * MINUTE_IN_SECONDS;
			if ( $max_execution_time > 0 && $max_execution_time < $min_execution_time ) {
				set_time_limit( $min_execution_time );
			}
		}
		if (Plugin::instance()->is_feature_active( self::UPLOAD_SVG_FEATURE )) {
			$svg_module = Plugin::instance()->modules_manager->get_modules( self::UPLOAD_SVG_FEATURE );
		} else{
			$svg_module = new UploadSVGModule();
			$svg_module->init();
		}

		$results = array();

		foreach ( $image_urls as $raw_url ) {
			if ( ! is_string( $raw_url ) ) {
				$results[ (string) $raw_url ] = array(
					'error' => __( 'Invalid URL', 'better-block-editor' ),
				);
				continue;
			}

			$raw_url   = trim( $raw_url );
			$image_url = esc_url_raw( $raw_url );

			if ( empty( $image_url ) ) {
				$results[ $raw_url ] = array(
					'error' => __( 'Invalid URL', 'better-block-editor' ),
				);
				continue;
			}

			$svg_module->allow_svg_from_upload();
			$result = $this->process_image_url( $image_url );

			if ( is_wp_error( $result ) ) {
				if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					$error_data            = $result->get_error_data();
					$results[ $image_url ] = array(
						'error' => $result->get_error_message() . ( isset( $error_data['details'] ) ? ': ' . $error_data['details'] : '' ),
					);
				} else {
					$results[ $image_url ] = array(
						'error' => __( 'Failed to process image', 'better-block-editor' ),
					);
				}

				continue;
			}

			$results[ $image_url ] = $result;
		}

		wp_send_json_success(
			array(
				'data' => $results,
				'meta' => array(
					'total' => count( $image_urls ),
				),
			)
		);
	}

	/**
	 * Downloads the image identified by the URL and stores it in the media library.
	 *
	 * @param string $image_url Image URL.
	 * @param array  $mimes Allowed mime types.
	 *
	 * @return array|\WP_Error
	 */
	private function process_image_url( $image_url ) {
		$file_name = basename( (string) wp_parse_url( $image_url, PHP_URL_PATH ) );
		if ( empty( $file_name ) ) {
			return new \WP_Error(
				'invalid_file_name',
				__( 'Could not determine file name from URL', 'better-block-editor' ),
				array(
					'status' => 400,
				)
			);
		}

		$cache      = $this->get_download_cache();
		$attachment = 0;
		$from_cache = false;

		if ( isset( $cache[ $image_url ] ) ) {
			$cached_attachment = get_post( $cache[ $image_url ] );

			if ( $cached_attachment && 'attachment' === $cached_attachment->post_type ) {
				$attachment = (int) $cache[ $image_url ];
				$from_cache = true;
			}
		}

		if ( ! $attachment ) {

			$temp_file = download_url( $image_url );

			if ( is_wp_error( $temp_file ) ) {

				return new \WP_Error(
					'custom_paste_download_error',
					__( 'Failed to download image', 'better-block-editor' ),
					array(
						'status'  => 500,
						'details' => $temp_file->get_error_message(),
					)
				);
			}

			$file_array = array(
				'name'     => $file_name,
				'tmp_name' => $temp_file,
			);

			$file_type = wp_check_filetype_and_ext( $temp_file, $file_name );

			if ( empty( $file_type['type'] ) || strpos( $file_type['type'], 'image/' ) !== 0 ) {
				wp_delete_file( $temp_file );

				return new \WP_Error(
					'invalid_file_type',
					__( 'File is not an image or SVG', 'better-block-editor' ),
					array(
						'status' => 400,
					)
				);
			}

			$attachment = media_handle_sideload( $file_array, 0 );

			if ( is_wp_error( $attachment ) ) {
				wp_delete_file( $temp_file );

				return new \WP_Error(
					'custom_paste_upload_error',
					__( 'Failed to upload image to media library', 'better-block-editor' ),
					array(
						'status'  => 500,
						'details' => $attachment->get_error_message(),
					)
				);
			}

			$cache[ $image_url ] = $attachment;
			$this->update_download_cache( $cache );
		}

		$attachment_url  = wp_get_attachment_url( $attachment );
		$attachment_post = get_post( $attachment );
		$alt_text        = get_post_meta( $attachment, '_wp_attachment_image_alt', true );

		return array(
			'id'         => $attachment,
			'url'        => $attachment_url,
			'alt'        => $alt_text,
			'caption'    => $attachment_post ? $attachment_post->post_excerpt : '',
			'from_cache' => $from_cache,
		);
	}

	/**
	 * Returns cached map of already imported attachments.
	 *
	 * @return array Map of image URL => attachment ID.
	 */
	private function get_download_cache() {
		if ( null === $this->download_cache ) {
			$cache                = get_transient( self::CACHE_KEY );
			$this->download_cache = is_array( $cache ) ? $cache : array();
		}

		return $this->download_cache;
	}

	/**
	 * Persists cache updates both in memory and as a transient.
	 *
	 * @param array $cache Updated cache.
	 */
	private function update_download_cache( array $cache ) {
		$this->download_cache = $cache;
		set_transient( self::CACHE_KEY, $cache, self::CACHE_TTL );
	}
}
