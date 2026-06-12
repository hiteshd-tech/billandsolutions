<?php
/**
 * Albums admin part.
 */

// File Security Check
if ( ! defined( 'ABSPATH' ) ) { exit; }

class Presscore_Mod_Albums_Admin {

	public function add_meta_boxes( $metaboxes ) {
		$metaboxes[] = plugin_dir_path( __FILE__ ) . 'metaboxes/metaboxes-albums.php';
		return $metaboxes;
	}

	public function add_basic_meta_boxes_support( $pages ) {
		$pages[] = \The7_Core\Mods\Post_Type_Builder\Bundled\Albums_Post_Type::get_name();
		return $pages;
	}

	public function add_options( $options ) {
		if ( array_key_exists( 'of-blog-and-portfolio-menu', $options ) ) {
			$options['of-albums-mod-injected-options'] = plugin_dir_path( __FILE__ ) . 'options/options-albums.php';
			$options['of-albums-mod-injected-slug-options'] = plugin_dir_path( __FILE__ ) . 'options/options-slug-albums.php';
		}
		if ( function_exists( 'presscore_module_archive_get_menu_slug' ) && array_key_exists( presscore_module_archive_get_menu_slug(), $options ) ) {
			$options['of-albums-mod-injected-archive-options'] = plugin_dir_path( __FILE__ ) . 'options/options-archive-albums.php';
		}
		return $options;
	}

	public function js_composer_default_editor_post_types_filter( $post_types ) {
		$post_types[] = \The7_Core\Mods\Post_Type_Builder\Bundled\Albums_Post_Type::get_name();
		return $post_types;
	}

	public function filter_admin_post_thumbnail( $thumbnail, $post_type, $post_id ) {
		if ( ! $thumbnail && \The7_Core\Mods\Post_Type_Builder\Bundled\Albums_Post_Type::get_name() === $post_type ) {
			$media_gallery = get_post_meta( $post_id, '_dt_album_media_items', true );
			if ( $media_gallery && is_array( $media_gallery ) ) {
				$thumbnail = wp_get_attachment_image_src( current( $media_gallery ), 'thumbnail' );
			}
		}
		return $thumbnail;
	}
}
