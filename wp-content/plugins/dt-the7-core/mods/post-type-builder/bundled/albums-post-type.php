<?php

namespace The7_Core\Mods\Post_Type_Builder\Bundled;

defined( 'ABSPATH' ) || exit;

class Albums_Post_Type extends Bundled_Item {

	/**
	 * @return string
	 */
	public static function get_name() {
		return 'dt_gallery';
	}

	/**
	 * @return array
	 */
	public static function get_args() {
		return apply_filters(
			'presscore_post_type_' . ( static::get_name() ) . '_args',
			[
				'labels'                => [
					'name'                  => _x('Photo Albums',             'backend albums', 'dt-the7-core'),
					'singular_name'         => _x('Photo Album',              'backend albums', 'dt-the7-core'),
					'add_new'               => _x('Add New Album',            'backend albums', 'dt-the7-core'),
					'add_new_item'          => _x('Add New Album',            'backend albums', 'dt-the7-core'),
					'edit_item'             => _x('Edit Album',               'backend albums', 'dt-the7-core'),
					'new_item'              => _x('New Album',                'backend albums', 'dt-the7-core'),
					'view_item'             => _x('View Album',               'backend albums', 'dt-the7-core'),
					'search_items'          => _x('Search for Albums',        'backend albums', 'dt-the7-core'),
					'not_found'             => _x('No Albums Found',          'backend albums', 'dt-the7-core'),
					'not_found_in_trash'    => _x('No Albums Found in Trash', 'backend albums', 'dt-the7-core'),
					'parent_item_colon'     => '',
					'menu_name'             => _x('Photo Albums',             'backend albums', 'dt-the7-core')
				],
				'public'                => '1',
				'publicly_queryable'    => '1',
				'show_ui'               => '1',
				'show_in_menu'          => '1',
				'query_var'             => '1',
				'rewrite'               => [ 'slug' => static::get_name() ],
				'capability_type'       => 'post',
				'has_archive'           => '1',
				'hierarchical'          => '0',
				'menu_position'         => 40,
				'supports'              => [ 'author', 'title', 'thumbnail', 'excerpt', 'editor', 'comments', 'revisions' ],
				'show_in_rest'          => '1',
				'taxonomies'			=> [
					'dt_gallery_category',
				],
			]
		);
	}

	/**
	 * @return string
	 */
	public static function get_module_name() {
		return 'albums';
	}

}
