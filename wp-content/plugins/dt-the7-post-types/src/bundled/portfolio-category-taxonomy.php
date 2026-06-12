<?php

namespace The7_Post_Types\Bundled;

defined( 'ABSPATH' ) || exit;

class Portfolio_Category_Taxonomy extends Bundled_Item {

	/**
	 * @return string
	 */
	public static function get_name() {
		return 'dt_portfolio_category';
	}

	/**
	 * @return array
	 */
	public static function get_args() {
		return [
			'post_types' => [ Portfolio_Post_Type::get_name() ],
			'args' => apply_filters(
				'presscore_taxonomy_' . ( static::get_name() ) . '_args',
				[
					'labels'                => [
						'name'              => _x( 'Portfolio Categories', 'backend portfolio', 'dt-the7-post-types' ),
						'singular_name'     => _x( 'Portfolio Category', 'backend portfolio', 'dt-the7-post-types' ),
						'search_items'      => _x( 'Search in Category', 'backend portfolio', 'dt-the7-post-types' ),
						'all_items'         => _x( 'Portfolio Categories', 'backend portfolio', 'dt-the7-post-types' ),
						'parent_item'       => _x( 'Parent Portfolio Category', 'backend portfolio', 'dt-the7-post-types' ),
						'parent_item_colon' => _x( 'Parent Portfolio Category:', 'backend portfolio', 'dt-the7-post-types' ),
						'edit_item'         => _x( 'Edit Category', 'backend portfolio', 'dt-the7-post-types' ),
						'update_item'       => _x( 'Update Category', 'backend portfolio', 'dt-the7-post-types' ),
						'add_new_item'      => _x( 'Add New Portfolio Category', 'backend portfolio', 'dt-the7-post-types' ),
						'new_item_name'     => _x( 'New Category Name', 'backend portfolio', 'dt-the7-post-types' ),
						'menu_name'         => _x( 'Portfolio Categories', 'backend portfolio', 'dt-the7-post-types' )
					],
					'hierarchical'          => '1',
					'public'                => '1',
					'show_ui'               => '1',
					'rewrite'               => [ 'slug' => 'project-category' ],
					'show_admin_column'		=> '1',
					'show_in_rest'          => '1',
				]
			)
		];
	}

	/**
	 * @return string
	 */
	public static function get_module_name() {
		return 'portfolio';
	}
}
