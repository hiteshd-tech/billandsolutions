<?php

namespace The7_Post_Types\Models;

use The7_Post_Types\Models\Items\Post_Type;
use The7_Post_Types\Models\Items\Bundled_Post_Type;

defined( 'ABSPATH' ) || exit;

class Post_Types extends Composition_Model {

	public static function convert_posts( $original_slug, $new_slug ) {
		$convert = new \WP_Query( [
			'posts_per_page' => -1,
			'post_type'      => $original_slug,
		] );
		foreach ( $convert->posts as $post ) {
			set_post_type( $post->ID, $new_slug );
		}
	}

	/**
	 * @return string
	 */
	protected static function get_data_key() {
		return 'the7_core_post_types';
	}

	protected static function create_custom_item( $data ) {
		return new Post_Type( $data );
	}

	protected static function create_bundled_item( $data, $class = null ) {
		return new Bundled_Post_Type( $data, $class );
	}
}
