<?php

namespace The7_Core\Mods\Post_Type_Builder\Bundled;

defined( 'ABSPATH' ) || exit;

class Testimonials_Post_Type extends Bundled_Item {

	/**
	 * @return string
	 */
	public static function get_name() {
		return 'dt_testimonials';
	}

	/**
	 * @return array
	 */
	public static function get_args() {
		return apply_filters(
			'presscore_post_type_' . ( static::get_name() ) . '_args',
			[
				'labels'             => [
					'name'               => _x( 'Testimonials', 'backend testimonials', 'dt-the7-core' ),
					'singular_name'      => _x( 'Testimonials', 'backend testimonials', 'dt-the7-core' ),
					'add_new'            => _x( 'Add New Testimonial', 'backend testimonials', 'dt-the7-core' ),
					'add_new_item'       => _x( 'Add New Testimonial', 'backend testimonials', 'dt-the7-core' ),
					'edit_item'          => _x( 'Edit Testimonial', 'backend testimonials', 'dt-the7-core' ),
					'new_item'           => _x( 'New Testimonial', 'backend testimonials', 'dt-the7-core' ),
					'view_item'          => _x( 'View Testimonial', 'backend testimonials', 'dt-the7-core' ),
					'search_items'       => _x( 'Search Testimonials', 'backend testimonials', 'dt-the7-core' ),
					'not_found'          => _x( 'No Testimonials found', 'backend testimonials', 'dt-the7-core' ),
					'not_found_in_trash' => _x( 'No Testimonials found in Trash', 'backend testimonials', 'dt-the7-core' ),
					'parent_item_colon'  => '',
					'menu_name'          => _x( 'Testimonials', 'backend testimonials', 'dt-the7-core' ),
				],
				'public'             => '1',
				'publicly_queryable' => '1',
				'show_ui'            => '1',
				'show_in_menu'       => '1',
				'query_var'          => '1',
				'rewrite'            => '1',
				'capability_type'    => 'post',
				'has_archive'        => '1',
				'hierarchical'       => '0',
				'menu_position'      => 36,
				'supports'           => [ 'title', 'editor', 'excerpt', 'thumbnail' ],
				'show_in_rest'       => '1',
				'taxonomies'	     => [
					'dt_testimonials_category',
				],
			]
		);
	}

	/**
	 * @return string
	 */
	public static function get_module_name() {
		return 'testimonials';
	}

}
