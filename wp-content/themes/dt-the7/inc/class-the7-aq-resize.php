<?php
/**
 * The7 image resize utility.
 *
 * @package The7
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class The7_Aq_Resize
 */
class The7_Aq_Resize
{
	/**
	 * The singleton instance
	 */
	static private $instance = null;

	/**
	 * No initialization allowed
	 */
	private function __construct() {}

	/**
	 * No cloning allowed
	 */
	private function __clone() {}

	/**
	 * Cache uploads dir.
	 */
	private $upload_info = array();

	/**
	 * For your custom default usage you may want to initialize an Aq_Resize object by yourself and then have own defaults
	 *
	 * @return The7_Aq_Resize
	 */
	static public function getInstance() {
		if(self::$instance == null) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Run, forest.
	 */
	public function process( $url, $img_width, $img_height, $width = null, $height = null, $crop = null, $single = true, $upscale = false ) {
		// Validate inputs.
		if ( ! $url || ( ! $width && ! $height ) ) return false;

		// Caipt'n, ready to hook.
		if ( true === $upscale ) add_filter( 'image_resize_dimensions', array($this, 'aq_upscale'), 10, 6 );

		// Define upload path & dir.
		if ( !empty( $this->upload_info ) ) {
			$upload_info = $this->upload_info;
		} else {
			$upload_info = $this->upload_info = wp_upload_dir();
		}

		$upload_dir = $upload_info['basedir'];
		$upload_url = $upload_info['baseurl'];

		$http_prefix = "http://";
		$https_prefix = "https://";

		/* if the $url scheme differs from $upload_url scheme, make them match
		   if the schemes differe, images don't show up. */
		if(!strncmp($url,$https_prefix,strlen($https_prefix))){ //if url begins with https:// make $upload_url begin with https:// as well
			$upload_url = str_replace($http_prefix,$https_prefix,$upload_url);
		}
		elseif(!strncmp($url,$http_prefix,strlen($http_prefix))){ //if url begins with https:// make $upload_url begin with https:// as well
			$upload_url = str_replace($https_prefix,$http_prefix,$upload_url);
		}


		// Check if $img_url is local.
		if ( false === strpos( $url, $upload_url ) ) return false;

		// Define path of image.
		$rel_path = str_replace( $upload_url, '', $url );
		$img_path = $upload_dir . $rel_path;

		// Check if img path exists, and is an image indeed.
		if ( ! file_exists( $img_path ) ) return false;

		if ( $img_width && $img_height ) {
			$img_dim = array( $img_width, $img_height );
		} else {
			$img_dim = getimagesize( $img_path );
		}

		if ( ! is_array( $img_dim ) ) return false;

		// Get image info.
		$info = pathinfo( $img_path );
		$ext = $info['extension'];
		list( $orig_w, $orig_h ) = $img_dim;

		// Get image size after cropping.
		$dims = image_resize_dimensions( $orig_w, $orig_h, $width, $height, $crop );
		$dst_w = isset( $dims[4] ) ? $dims[4] : $orig_w;
		$dst_h = isset( $dims[5] ) ? $dims[5] : $orig_h;

		// Return the original image only if it exactly fits the needed measures.
		if ( ! $dims && ( ( ( null === $height && $orig_w == $width ) xor ( null === $width && $orig_h == $height ) ) xor ( $height == $orig_h && $width == $orig_w ) ) ) {
			$img_url = $url;
			$dst_w = $orig_w;
			$dst_h = $orig_h;
		} else {
			// Use this to check if cropped image already exists, so we can return that instead.
			$suffix = "{$dst_w}x{$dst_h}";
			$dst_rel_path = THE7_RESIZED_IMAGES_DIR . str_replace( '.' . $ext, '', $rel_path );
			$destfilename = "{$upload_dir}{$dst_rel_path}-{$suffix}.{$ext}";

			if ( ! $dims || ( true == $crop && false == $upscale && ( $dst_w < $width || $dst_h < $height ) ) ) {
				// Can't resize, so return false saying that the action to do could not be processed as planned.
				return false;
			}
			// Else check if cache exists.
			elseif ( file_exists( $destfilename ) ) {
				$img_url = "{$upload_url}{$dst_rel_path}-{$suffix}.{$ext}";
			}
			// Else, we resize the image and return the new resized image url.
			else {

				$editor = wp_get_image_editor( $img_path );

				if ( is_wp_error( $editor ) || is_wp_error( $editor->resize( $width, $height, $crop ) ) )
					return false;

				$resized_file = $editor->save( $destfilename );

				if ( ! is_wp_error( $resized_file ) ) {
					$resized_rel_path = str_replace( $upload_dir, '', $resized_file['path'] );
					$img_url = $upload_url . $resized_rel_path;
					$dst_w = $resized_file['width'];
					$dst_h = $resized_file['height'];
				} else {
					return false;
				}

			}
		}

		// Okay, leave the ship.
		if ( true === $upscale ) remove_filter( 'image_resize_dimensions', array( $this, 'aq_upscale' ) );

		// Return the output.
		if ( $single ) {
			// str return.
			$image = $img_url;
		} else {
			// array return.
			$image = array (
				0 => $img_url,
				1 => $dst_w,
				2 => $dst_h
			);
		}

		return $image;
	}

	/**
	 * Callback to overwrite WP computing of thumbnail measures
	 */
	function aq_upscale( $default, $orig_w, $orig_h, $dest_w, $dest_h, $crop ) {
		if ( ! $crop ) return null; // Let the wordpress default function handle this.

		// Here is the point we allow to use larger image size than the original one.
		$aspect_ratio = $orig_w / $orig_h;
		$new_w = $dest_w;
		$new_h = $dest_h;

		if ( ! $new_w ) {
			$new_w = intval( $new_h * $aspect_ratio );
		}

		if ( ! $new_h ) {
			$new_h = intval( $new_w / $aspect_ratio );
		}

		$size_ratio = max( $new_w / $orig_w, $new_h / $orig_h );

		$crop_w = round( $new_w / $size_ratio );
		$crop_h = round( $new_h / $size_ratio );

		$s_x = floor( ( $orig_w - $crop_w ) / 2 );
		$s_y = floor( ( $orig_h - $crop_h ) / 2 );

		return array( 0, 0, (int) $s_x, (int) $s_y, (int) $new_w, (int) $new_h, (int) $crop_w, (int) $crop_h );
	}

	/**
	 * @return void
	 */
	public static function setup_resizes_deleteion() {
		add_filter( 'pre_delete_attachment', [ __CLASS__, 'add_resizes_to_image_meta_sizes_filter' ], 9999, 2 );
	}

	/**
	 * @return void
	 */
	public static function off_resizes_deleteion() {
		remove_filter( 'pre_delete_attachment', [ __CLASS__, 'add_resizes_to_image_meta_sizes_filter' ], 9999 );
	}

	/**
	 * @param bool $check Whether to allow the attachment to be deleted. Default true.
	 * @param WP_Post $post Attachment post.
	 *
	 * @return bool|null
	 */
	public static function add_resizes_to_image_meta_sizes_filter( $check, $post ) {
		if ( ! wp_attachment_is_image( $post ) ) {
			return $check;
		}

		$attachment_id = $post->ID;
		$file          = get_attached_file( $attachment_id );
		$file_type     = wp_check_filetype( $file );
		$file_ext      = $file_type['ext'];
		if ( ! $file_ext ) {
			return $check;
		}

		$wp_filesystem = the7_get_filesystem();
		if ( is_wp_error( $wp_filesystem ) ) {
			return $check;
		}

		// Get all files in the same directory as the file.
		$files = $wp_filesystem->dirlist( dirname( $file ), false, false );
		if ( ! $files ) {
			return $check;
		}

		$file_basename = basename( $file );
		$ext_offset    = -1 * ( strlen( $file_ext ) + 1 );
		$file_base     = substr( $file_basename, 0, $ext_offset ) . '-';
		// Filter out files that don't match the base name.
		$file_names = array_filter(
			wp_list_pluck( $files, 'name' ),
			function ( $f ) use ( $file_base ) {
				return strpos( $f, $file_base ) === 0;
			}
		);

		if ( ! $file_names ) {
			return $check;
		}

		$meta          = wp_get_attachment_metadata( $attachment_id );
		$exclude_files = [];
		if ( is_array( $meta ) ) {
			$exclude_files = wp_list_pluck( (array) $meta['sizes'], 'file' );
		}
		$exclude_files[] = $file_basename;

		// Filter out files that are already in the meta data.
		$potential_resizes = array_values( array_diff( $file_names, $exclude_files ) );

		// Filter out files that don't match the pattern of {file_base}-{number}x{number}.{ext}.
		$potential_resizes = array_filter(
			$potential_resizes,
			function ( $f ) use ( $file_base, $file_ext ) {
				return preg_match( '/^' . preg_quote( $file_base, '/' ) . '\d+x\d+\.(' . preg_quote( $file_ext, '/' ) . ')$/', $f );
			}
		);

		// Add potential resizes to the meta data and update.
		add_filter(
			'wp_get_attachment_metadata',
			function ( $data, $id ) use ( $potential_resizes, $attachment_id, $file_base, $file_ext ) {
				// Only add resizes to the meta data for the attachment being deleted.
				if ( $id === $attachment_id ) {
					$additional_sizes = [];
					foreach ( $potential_resizes as $file ) {
						$key                      = sanitize_key( str_replace( [ $file_base, $file_ext ], '', $file ) );
						$key_parts                = (array) explode( 'x', $key );
						$additional_sizes[ $key ] = [
							'file'      => $file,
							'width'     => ( isset( $key_parts[0] ) ? (int) $key_parts[0] : 0 ),
							'height'    => ( isset( $key_parts[1] ) ? (int) $key_parts[1] : 0 ),
							'mime-type' => 'image/jpeg',
						];
					}
					$data['sizes'] = array_merge(
						array_filter(
							isset( $data['sizes'] ) && is_array( $data['sizes'] ) ? $data['sizes'] : []
						),
						$additional_sizes
					);
				}

				return $data;
			},
			20,
			2
		);

		return $check;
	}
}
