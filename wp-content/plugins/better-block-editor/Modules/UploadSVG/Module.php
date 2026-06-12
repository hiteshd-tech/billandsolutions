<?php
/**
 * Adds SVG upload support.
 *
 * @package BetterBlockEditor
 */

namespace BetterBlockEditor\Modules\UploadSVG;

use BetterBlockEditor\Base\ModuleBase;
use BetterBlockEditor\Base\ManagableModuleInterface;
use BetterBlockEditor\Core\Settings;
use enshrined\svgSanitize\Sanitizer;

defined( 'ABSPATH' ) || exit;

class Module extends ModuleBase implements ManagableModuleInterface {

	const SETTINGS_ORDER = 1100;

	const IMAGE_SVG_XML = 'image/svg+xml';
	protected $sanitizer;
	private $svg_cache = array();

	public static function get_default_state() {
		return true;
	}

	const MODULE_IDENTIFIER = 'upload-svg';

	public function init() {
		$this->sanitizer = new Sanitizer();
		$this->sanitizer->minify( true );

		add_action( 'load-upload.php', array( $this, 'allow_svg_from_upload' ) );
		add_action( 'load-post-new.php', array( $this, 'allow_svg_from_upload' ) );
		add_action( 'load-post.php', array( $this, 'allow_svg_from_upload' ) );
		add_action( 'load-site-editor.php', array( $this, 'allow_svg_from_upload' ) );

		add_filter( 'wp_handle_sideload_prefilter', array( $this, 'handle_svg_file' ) );
		add_filter( 'wp_handle_upload_prefilter', array( $this, 'handle_svg_file' ) );
		add_filter( 'wp_prepare_attachment_for_js', array( $this, 'adjust_svg_admin_preview' ), 10, 3 );
		add_filter( 'wp_get_attachment_image_src', array( $this, 'svg_size_fix' ), 10, 4 );
		add_filter( 'admin_post_thumbnail_html', array( $this, 'featured_image_fix' ), 10, 3 );
		add_action( 'get_image_tag', array( $this, 'get_image_tag_override' ), 10, 6 );
		add_filter( 'wp_generate_attachment_metadata', array( $this, 'skip_svg_regeneration' ), 10, 2 );
		add_filter( 'upload_mimes', array( $this, 'allow_svg_uploads' ) );
		add_filter( 'wp_get_attachment_metadata', array( $this, 'fix_attachment_metadata_error' ), 10, 2 );
		add_filter( 'wp_calculate_image_srcset_meta', array( $this, 'disable_srcset' ), 10, 4 );
	}

	/**
	 * Handle SVG files
	 *
	 * @param array $file The file being uploaded.
	 *
	 * @return array
	 */
	public function handle_svg_file( $file ) {
		if ( ! isset( $file['tmp_name'] ) ) {
			return $file;
		}
		$this->allow_svg_from_upload();

		$file_name   = isset( $file['name'] ) ? sanitize_file_name( $file['name'] ) : '';
		$wp_filetype = wp_check_filetype_and_ext( $file['tmp_name'], $file_name );

		// Ensure SVG MIME type is temporarily allowed during the full upload process
		add_filter( 'pre_move_uploaded_file', array( $this, 'pre_move_uploaded_file' ) );

		$type = ! empty( $wp_filetype['type'] ) ? $wp_filetype['type'] : '';

		if ( self::IMAGE_SVG_XML !== $type ) {
			return $file;
		}

		if ( ! $this->user_can_upload_svg() ) {
			$file['error'] = __( 'Sorry, you are not allowed to upload SVG files.', 'better-block-editor' );

			return $file;
		}

		if ( ! $this->sanitize( $file['tmp_name'] ) ) {
			$file['error'] = __( "Sorry, this file couldn't be sanitized so for security reasons wasn't uploaded", 'better-block-editor' );
		}

		return $file;
	}

	/**
	 * Remove the filters after the file has been processed.
	 * We need to utilize the pre_move_uploaded_file filter to ensure we can remove the filters after the file has been full-processed.
	 * This is because wp_check_filetype_and_ext() is called multiple times during the upload process.
	 *
	 * @param string $move_new_file The new file path. We don't touch this, just return it.
	 *
	 * @return string
	 */
	public function pre_move_uploaded_file( $move_new_file ) {
		remove_filter( 'wp_check_filetype_and_ext', array( $this, 'fix_mime_type_svg' ), 10 );
		remove_filter( 'upload_mimes', array( $this, 'allow_svg_uploads' ) );

		return $move_new_file;
	}

	/**
	 * Adjust the SVG preview in the media library
	 *
	 * @param array    $response   Array of data for the image.
	 * @param \WP_Post $attachment Attachment object.
	 * @param array    $meta       Array of meta data for the image.
	 *
	 * @return array
	 */
	public function adjust_svg_admin_preview( $response, $attachment, $meta ) {

		if ( self::IMAGE_SVG_XML === $response['mime'] ) {
			$dimensions = $this->svg_dimensions( $attachment->ID );

			if ( $dimensions ) {
				$response = array_merge( $response, $dimensions );
			}

			$response['sizes']['full'] = array(
				'height'      => $dimensions['height'] ?? 2000,
				'width'       => $dimensions['width'] ?? 2000,
				'url'         => $response['url'],
				'orientation' => ( isset( $dimensions['width'] ) && isset( $dimensions['height'] ) && $dimensions['width'] > $dimensions['height'] ) ? 'landscape' : 'portrait',
			);

			$response['icon'] = $response['url']; // Use the URL as the icon
		}

		return $response;
	}

	public function svg_size_fix( $image, $attachment_id, $size, $icon ) {
		if ( $this->is_svg_mime_type( $attachment_id ) ) {
			$dimensions    = $this->svg_dimensions( $attachment_id, $size );
			$fallback_size = 80;
			$image[1]      = $dimensions['width'] ?? $fallback_size;
			$image[2]      = $dimensions['height'] ?? $fallback_size;
		}

		return $image;
	}

	/**
	 * If the featured image is an SVG we wrap it in an SVG class so we can apply our CSS fix.
	 *
	 * @param string   $content      Admin post thumbnail HTML markup.
	 * @param int      $post_id      Post ID.
	 * @param int|null $thumbnail_id Thumbnail attachment ID, or null if there isn't one.
	 *
	 * @return string
	 */
	public function featured_image_fix( $content, $post_id, $thumbnail_id = null ) {
		if ( $this->is_svg_mime_type( $thumbnail_id ) ) {
			$content = sprintf( '<span class="svg">%s</span>', $content );
		}

		return $content;
	}

	protected function sanitize( $file ) {
		$dirty = file_get_contents( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

		$is_zipped = $this->is_gzipped( $dirty );
		if ( $is_zipped ) {
			$dirty = gzdecode( $dirty );

			// If decoding fails, bail as we're not secure
			if ( false === $dirty ) {
				return false;
			}
		}

		// Allow large SVGs if the setting is on.
		if ( apply_filters( 'wpbbe-svg_large_svg', false ) ) {
			$this->sanitizer->setAllowHugeFiles( true );
		}

		/**
		 * Load extra filters to allow devs to access the safe tags and attrs by themselves.
		 */
		$this->sanitizer->setAllowedTags( new SvgTags() );
		$this->sanitizer->setAllowedAttrs( new SvgAttributes() );

		$clean = $this->sanitizer->sanitize( $dirty );

		if ( false === $clean ) {
			return false;
		}

		if ( $is_zipped ) {
			$clean = gzencode( $clean );
		}

		file_put_contents( $file, $clean ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_file_put_contents

		return true;
	}


	/**
	 * Check if the contents are gzipped
	 *
	 * @see http://www.gzip.org/zlib/rfc-gzip.html#member-format
	 *
	 * @param string $contents Content to check.
	 *
	 * @return bool
	 */
	protected function is_gzipped( $contents ) {
		// phpcs:disable Generic.Strings.UnnecessaryStringConcat.Found
		if ( function_exists( 'mb_strpos' ) ) {
			return 0 === mb_strpos( $contents, "\x1f" . "\x8b" . "\x08" );
		} else {
			return 0 === strpos( $contents, "\x1f" . "\x8b" . "\x08" );
		}
		// phpcs:enable
	}


	/**
	 * Allow SVGs to be uploaded
	 */
	public function allow_svg_from_upload() {
		add_filter( 'upload_mimes', array( $this, 'allow_svg_uploads' ) );
		add_filter( 'wp_check_filetype_and_ext', array( $this, 'fix_mime_type_svg' ), 10, 4 );
	}

	/**
	 * Override the default height and width string on an SVG
	 *
	 * @param string       $html  HTML content for the image.
	 * @param int          $id    Attachment ID.
	 * @param string       $alt   Alternate text.
	 * @param string       $title Attachment title.
	 * @param string       $align Part of the class name for aligning the image.
	 * @param string|array $size  Size of image. Image size or array of width and height values (in that order).
	 *                            Default 'medium'.
	 *
	 * @return mixed
	 */
	public function get_image_tag_override( $html, $id, $alt, $title, $align, $size ) {
		if ( $this->is_svg_mime_type( $id ) ) {
			if ( is_array( $size ) ) {
				$width  = $size[0];
				$height = $size[1];
			} elseif ( $dimensions = $this->svg_dimensions( $id ) ) { // phpcs:ignore WordPress.CodeAnalysis.AssignmentInCondition.Found, Squiz.PHP.DisallowMultipleAssignments.FoundInControlStructure
				$width  = $dimensions['width'];
				$height = $dimensions['height'];
			} else {
				$width  = 0;
				$height = 0;
			}
			if ( $height && $width ) {
				$html = str_replace( 'width="1" ', sprintf( 'width="%s" ', $width ), $html );
				$html = str_replace( 'height="1" ', sprintf( 'height="%s" ', $height ), $html );
			} else {
				$html = str_replace( 'width="1" ', '', $html );
				$html = str_replace( 'height="1" ', '', $html );
			}
			$html = str_replace( '/>', ' role="img" />', $html );
		}

		return $html;
	}

	/**
	 * Fixes the issue in WordPress 4.7.1 being unable to correctly identify SVGs
	 *
	 * @thanks @lewiscowles
	 *
	 * @param array    $data     Values for the extension, mime type, and corrected filename.
	 * @param string   $file     Full path to the file.
	 * @param string   $filename The name of the file.
	 * @param string[] $mimes    Array of mime types keyed by their file extension regex.
	 *
	 * @return null
	 */
	public function fix_mime_type_svg( $data = null, $file = null, $filename = null, $mimes = null ) {
		$ext = isset( $data['ext'] ) ? $data['ext'] : '';
		if ( strlen( $ext ) < 1 ) {
			$exploded = explode( '.', $filename );
			$ext      = strtolower( end( $exploded ) );
		}
		if ( 'svg' === $ext ) {
			$data['type'] = self::IMAGE_SVG_XML;
			$data['ext']  = 'svg';
		} elseif ( 'svgz' === $ext ) {
			$data['type'] = self::IMAGE_SVG_XML;
			$data['ext']  = 'svgz';
		}

		return $data;
	}

	private function is_svg_mime_type( $attachment_id ) {
		return self::IMAGE_SVG_XML === get_post_mime_type( $attachment_id );
	}

	/**
	 * Disable srcset for SVGs
	 *
	 * @param array  $image_meta    The image meta data.
	 * @param array  $size_array    The image size.
	 * @param string $image_src     The image source.
	 * @param int    $attachment_id The attachment ID.
	 *
	 * @return array
	 */
	public function disable_srcset( $image_meta, $size_array, $image_src, $attachment_id ) {
		if ( $attachment_id && $this->is_svg_mime_type( $attachment_id ) && is_array( $image_meta ) ) {
			$image_meta['sizes'] = array();
		}

		return $image_meta;
	}

	public function allow_svg_uploads( $mimes ) {
		if ( ! class_exists( 'DOMDocument' ) ) {
			return $mimes;
		}

		if ( $this->user_can_upload_svg() ) {
			$mimes['svg']  = self::IMAGE_SVG_XML;
			$mimes['svgz'] = self::IMAGE_SVG_XML;
		}

		return $mimes;
	}

	/**
	 * Get SVG size from the width/height or viewport.
	 *
	 * @param integer $attachment_id The attachment ID of the SVG being processed.
	 *
	 * @return array|bool
	 */
	protected function svg_dimensions( $attachment_id ) {
		if ( isset( $this->svg_cache[ $attachment_id ] ) ) {
			return $this->svg_cache[ $attachment_id ];
		}
		$svg_dimensions = apply_filters( 'wpbbe-svg_dimensions', false, $attachment_id );

		if ( $svg_dimensions !== false ) {
			$this->svg_cache[ $attachment_id ] = $svg_dimensions;

			return $this->svg_cache[ $attachment_id ];
		}

		$width    = 0;
		$height   = 0;
		$svg_file = get_attached_file( $attachment_id );
		$metadata = wp_get_attachment_metadata( $attachment_id );

		if ( $svg_file && ! empty( $metadata['width'] ) && ! empty( $metadata['height'] ) ) {
			$width  = (float) $metadata['width'];
			$height = (float) $metadata['height'];
		} elseif ( $svg_file ) {
			if ( ! function_exists( 'simplexml_load_file' ) ) {
				return false;
			}
			$svg_file = @simplexml_load_file( $svg_file ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged

			if ( ! $svg_file ) {
				return false;
			}

			$attrs = $svg_file->attributes();
			// Extract viewBox dimensions if present
			if ( isset( $attrs->viewBox ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$sizes = explode( ' ', $attrs->viewBox ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				if ( isset( $sizes[2], $sizes[3] ) ) {
					$viewbox_width  = (float) $sizes[2];
					$viewbox_height = (float) $sizes[3];
				}
			}

			if ( isset( $attrs->width, $attrs->height ) && is_numeric( (float) $attrs->width ) && is_numeric( (float) $attrs->height ) && ! $this->str_ends_with( (string) $attrs->width, '%' ) && ! $this->str_ends_with( (string) $attrs->height, '%' ) ) {
				$attr_width  = (float) $attrs->width;
				$attr_height = (float) $attrs->height;
			}

			$use_attr = (bool) apply_filters( 'wpbbe-svg_use_width_height_attributes', false, $svg_file );

			if ( $use_attr && isset( $attr_width, $attr_height ) ) {
				$width  = $attr_width;
				$height = $attr_height;
			} elseif ( isset( $viewbox_width, $viewbox_height ) ) {
				$width  = $viewbox_width;
				$height = $viewbox_height;
			} elseif ( isset( $attr_width, $attr_height ) ) {
				// Fallback to width/height if viewBox missing
				$width  = $attr_width;
				$height = $attr_height;
			}

			if ( ! $width && ! $height ) {
				return false;
			}
		}

		$dimensions = array(
			'width'       => $width,
			'height'      => $height,
			'orientation' => ( $width > $height ) ? 'landscape' : 'portrait',
		);

		$this->svg_cache[ $attachment_id ] = apply_filters( 'wpbbe-svg_dimensions', $dimensions, $svg_file );

		return $this->svg_cache[ $attachment_id ];
	}

	/**
	 * Skip regenerating SVGs
	 *
	 * @param array $metadata      An array of attachment meta data.
	 * @param int   $attachment_id Attachment Id to process.
	 *
	 * @return mixed Metadata for attachment.
	 */
	public function skip_svg_regeneration( $metadata, $attachment_id ) {
		if ( $this->is_svg_mime_type( $attachment_id ) ) {
			$dimensions = $this->svg_dimensions( $attachment_id );

			if ( ! $dimensions ) {
				return $metadata;
			}

			$upload_dir    = wp_upload_dir();
			$svg_path      = get_attached_file( $attachment_id );
			$relative_path = str_replace( trailingslashit( $upload_dir['basedir'] ), '', $svg_path );
			$filename      = basename( $svg_path );

			$metadata = array(
				'width'  => intval( $dimensions['width'] ),
				'height' => intval( $dimensions['height'] ),
				'file'   => $relative_path,
				'sizes'  => array(),
			);

			$additional_image_sizes = wp_get_additional_image_sizes();
			foreach ( get_intermediate_image_sizes() as $size ) {
				$metadata['sizes'][ $size ] = array(
					'width'     => $additional_image_sizes[ $size ]['width'] ?? get_option( "{$size}_size_w", 0 ),
					'height'    => $additional_image_sizes[ $size ]['height'] ?? get_option( "{$size}_size_h", 0 ),
					'crop'      => $additional_image_sizes[ $size ]['crop'] ?? get_option( "{$size}_crop", false ),
					'file'      => $filename,
					'mime-type' => self::IMAGE_SVG_XML,
				);
			}
		}

		return $metadata;
	}

	/**
	 * Filters the attachment meta data to prevent errors.
	 *
	 * @param array|bool $data    Array of meta data for the given attachment, or false
	 *                            if the object does not exist.
	 * @param int        $post_id Attachment ID.
	 */
	public function fix_attachment_metadata_error( $data, $post_id ) {
		if ( ! is_wp_error( $data ) ) {
			return $data;
		}
		if ( $this->is_svg_mime_type( $post_id ) ) {
			$data = wp_generate_attachment_metadata( $post_id, get_attached_file( $post_id ) );
			wp_update_attachment_metadata( $post_id, $data );
		}

		return $data;
	}

	protected function str_ends_with( $haystack, $needle ) {
		if ( '' === $haystack && '' !== $needle ) {
			return false;
		}

		$len = strlen( $needle );

		return 0 === substr_compare( $haystack, $needle, - $len, $len );
	}

	public function user_can_upload_svg() {
		$can_upload = current_user_can( 'upload_files' );

		return (bool) apply_filters( 'wpbbe-svg_user_can_upload', $can_upload );
	}

	public static function get_title() {
		return __( 'BBE SVG Icon', 'better-block-editor' );
	}

	public static function get_tab() {
		return Settings::TAB_BLOCKS;
	}

	public static function get_label() {
		return __( 'Allow uploading SVG images, and enable the BBE SVG Icon block.', 'better-block-editor' );
	}
}
