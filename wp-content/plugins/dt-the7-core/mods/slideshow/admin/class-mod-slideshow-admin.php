<?php
/**
 * Slideshow admin part.
 */

// File Security Check
if ( ! defined( 'ABSPATH' ) ) { exit; }

class Presscore_Mod_Slideshow_Admin {

	public function add_meta_boxes( $metaboxes ) {
		$metaboxes[] = plugin_dir_path( __FILE__ ) . 'metaboxes/metaboxes-slideshow.php';
		return $metaboxes;
	}

	public function filter_admin_post_thumbnail( $thumbnail, $post_type, $post_id ) {
		if ( ! $thumbnail && \The7_Core\Mods\Post_Type_Builder\Bundled\Slideshow_Post_Type::get_name() === $post_type ) {
			$media_gallery = get_post_meta( $post_id, '_dt_slider_media_items', true );
			if ( $media_gallery && is_array( $media_gallery ) ) {
				$thumbnail = wp_get_attachment_image_src( current( $media_gallery ), 'thumbnail' );
			}
		}
		return $thumbnail;
	}
}
