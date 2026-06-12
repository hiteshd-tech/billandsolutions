<?php

namespace The7_Core\Mods\Post_Type_Builder\Models;

use The7_Core\Mods\Post_Type_Builder\Bundled\Albums_Post_Type;
use The7_Core\Mods\Post_Type_Builder\Bundled\Portfolio_Post_Type;
use The7_Core\Mods\Post_Type_Builder\Bundled\Slideshow_Post_Type;
use The7_Core\Mods\Post_Type_Builder\Bundled\Team_Post_Type;
use The7_Core\Mods\Post_Type_Builder\Bundled\Testimonials_Post_Type;
use The7_Core\Mods\Post_Type_Builder\Models\Items\Post_Type;
use The7_Core\Mods\Post_Type_Builder\Models\Items\Bundled_Post_Type;

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

	public static function get_bundle_definition() {
		return apply_filters(
			'the7_core_bundled_post_types_list',
			[
				'dt_portfolio'    => Portfolio_Post_Type::class,
				'dt_testimonials' => Testimonials_Post_Type::class,
				'dt_team'         => Team_Post_Type::class,
				'dt_slideshow'    => Slideshow_Post_Type::class,
				'dt_gallery'      => Albums_Post_Type::class,
			]
		);
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
