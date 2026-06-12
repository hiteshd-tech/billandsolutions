<?php

namespace The7_Core\Mods\Post_Type_Builder\Bundled;

defined( 'ABSPATH' ) || exit;

class Testimonials_Category_Taxonomy extends Bundled_Item {

	/**
	 * @return string
	 */
	public static function get_name() {
		return 'dt_testimonials_category';
	}

	/**
	 * @return array
	 */
	public static function get_args() {
		return [
			'post_types' => [ Testimonials_Post_Type::get_name() ],
			'args' => apply_filters(
				'presscore_taxonomy_' . ( static::get_name() ) . '_args',
				[
					'labels'            => [
						'name'              => _x( 'Testimonial Categories', 'backend testimonials', 'dt-the7-core' ),
						'singular_name'     => _x( 'Testimonial Category', 'backend testimonials', 'dt-the7-core' ),
						'search_items'      => _x( 'Search in Category', 'backend testimonials', 'dt-the7-core' ),
						'all_items'         => _x( 'Categories', 'backend testimonials', 'dt-the7-core' ),
						'parent_item'       => _x( 'Parent Category', 'backend testimonials', 'dt-the7-core' ),
						'parent_item_colon' => _x( 'Parent Category:', 'backend testimonials', 'dt-the7-core' ),
						'edit_item'         => _x( 'Edit Category', 'backend testimonials', 'dt-the7-core' ),
						'update_item'       => _x( 'Update Category', 'backend testimonials', 'dt-the7-core' ),
						'add_new_item'      => _x( 'Add New Testimonial Category', 'backend testimonials', 'dt-the7-core' ),
						'new_item_name'     => _x( 'New Category Name', 'backend testimonials', 'dt-the7-core' ),
						'menu_name'         => _x( 'Testimonial Categories', 'backend testimonials', 'dt-the7-core' ),
					],
					'hierarchical'      => '1',
					'public'            => '1',
					'show_ui'           => '1',
					'rewrite'           => '1',
					'show_admin_column' => '1',
					'show_in_rest'      => '1',
				]
			)
		];
	}

	/**
	 * @return string
	 */
	public static function get_module_name() {
		return 'testimonials';
	}
}
