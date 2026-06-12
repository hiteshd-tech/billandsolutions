<?php

namespace The7_Core\Mods\Post_Type_Builder\Bundled;

defined( 'ABSPATH' ) || exit;

class Portfolio_Post_Type extends Bundled_Item {

	/**
	 * @return string
	 */
	public static function get_name() {
		return 'dt_portfolio';
	}

	/**
	 * @return array
	 */
	public static function get_args() {
		return apply_filters(
			'presscore_post_type_' . ( static::get_name() ) . '_args',
			[
				'labels'                => [
					'name'               => _x( 'Portfolio', 'backend portfolio', 'dt-the7-core' ),
					'singular_name'      => _x( 'Project', 'backend portfolio', 'dt-the7-core' ),
					'add_new'            => _x( 'Add New', 'backend portfolio', 'dt-the7-core' ),
					'add_new_item'       => _x( 'Add New Item', 'backend portfolio', 'dt-the7-core' ),
					'edit_item'          => _x( 'Edit Item', 'backend portfolio', 'dt-the7-core' ),
					'new_item'           => _x( 'New Item', 'backend portfolio', 'dt-the7-core' ),
					'view_item'          => _x( 'View Item', 'backend portfolio', 'dt-the7-core' ),
					'search_items'       => _x( 'Search Items', 'backend portfolio', 'dt-the7-core' ),
					'not_found'          => _x( 'No items found', 'backend portfolio', 'dt-the7-core' ),
					'not_found_in_trash' => _x( 'No items found in Trash', 'backend portfolio', 'dt-the7-core' ),
					'parent_item_colon'     => '',
					'menu_name'             => _x( 'Portfolio', 'backend portfolio', 'dt-the7-core' )
				],
				'public'                => '1',
				'publicly_queryable'    => '1',
				'show_ui'               => '1',
				'show_in_menu'          => '1',
				'query_var'             => '1',
				'rewrite'               => [ 'slug' => 'project' ],
				'capability_type'       => 'post',
				'has_archive'           => '1',
				'hierarchical'          => '0',
				'menu_position'         => 35,
				'supports'              => [ 'author', 'title', 'editor', 'thumbnail', 'comments', 'excerpt', 'revisions' ],
				'show_in_rest'          => '1',
				'taxonomies'			=> [
					'dt_portfolio_category',
					'dt_portfolio_tags'
				],
			]
		);
	}

	/**
	 * @return string
	 */
	public static function get_module_name() {
		return 'portfolio';
	}
}
