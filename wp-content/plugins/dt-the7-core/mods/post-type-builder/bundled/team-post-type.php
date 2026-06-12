<?php

namespace The7_Core\Mods\Post_Type_Builder\Bundled;

defined( 'ABSPATH' ) || exit;

class Team_Post_Type extends Bundled_Item {

	/**
	 * @return string
	 */
	public static function get_name() {
		return 'dt_team';
	}

	/**
	 * @return array
	 */
	public static function get_args() {
		return apply_filters(
			'presscore_post_type_' . ( static::get_name() ) . '_args',
			[
				'labels'             => [
					'name'               => _x( 'Team', 'backend team', 'dt-the7-core' ),
					'singular_name'      => _x( 'Teammate', 'backend team', 'dt-the7-core' ),
					'add_new'            => _x( 'Add New', 'backend team', 'dt-the7-core' ),
					'add_new_item'       => _x( 'Add New Teammate', 'backend team', 'dt-the7-core' ),
					'edit_item'          => _x( 'Edit Teammate', 'backend team', 'dt-the7-core' ),
					'new_item'           => _x( 'New Teammate', 'backend team', 'dt-the7-core' ),
					'view_item'          => _x( 'View Teammate', 'backend team', 'dt-the7-core' ),
					'search_items'       => _x( 'Search Teammates', 'backend team', 'dt-the7-core' ),
					'not_found'          => _x( 'No teammates found', 'backend team', 'dt-the7-core' ),
					'not_found_in_trash' => _x( 'No Teammates found in Trash', 'backend team', 'dt-the7-core' ),
					'parent_item_colon'  => '',
					'menu_name'          => _x( 'Team', 'backend team', 'dt-the7-core' ),
				],
				'public'             => '1',
				'publicly_queryable' => '1',
				'show_ui'            => '1',
				'show_in_menu'       => '1',
				'query_var'          => '1',
				'rewrite'            => [ 'slug' => 'dt_team' ],
				'capability_type'    => 'post',
				'has_archive'        => '1',
				'hierarchical'       => '0',
				'menu_position'      => 37,
				'supports'           => [ 'title', 'editor', 'comments', 'excerpt', 'thumbnail' ],
				'show_in_rest'       => '1',
				'taxonomies'	     => [
					'dt_team_category',
				],
			]
		);
	}

	/**
	 * @return string
	 */
	public static function get_module_name() {
		return 'team';
	}

}
