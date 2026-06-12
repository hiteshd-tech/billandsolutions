<?php

namespace The7_Post_Types\Bundled;

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
					'name'               => _x( 'Team', 'backend team', 'dt-the7-post-types' ),
					'singular_name'      => _x( 'Teammate', 'backend team', 'dt-the7-post-types' ),
					'add_new'            => _x( 'Add New', 'backend team', 'dt-the7-post-types' ),
					'add_new_item'       => _x( 'Add New Teammate', 'backend team', 'dt-the7-post-types' ),
					'edit_item'          => _x( 'Edit Teammate', 'backend team', 'dt-the7-post-types' ),
					'new_item'           => _x( 'New Teammate', 'backend team', 'dt-the7-post-types' ),
					'view_item'          => _x( 'View Teammate', 'backend team', 'dt-the7-post-types' ),
					'search_items'       => _x( 'Search Teammates', 'backend team', 'dt-the7-post-types' ),
					'not_found'          => _x( 'No teammates found', 'backend team', 'dt-the7-post-types' ),
					'not_found_in_trash' => _x( 'No Teammates found in Trash', 'backend team', 'dt-the7-post-types' ),
					'parent_item_colon'  => '',
					'menu_name'          => _x( 'Team', 'backend team', 'dt-the7-post-types' ),
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
