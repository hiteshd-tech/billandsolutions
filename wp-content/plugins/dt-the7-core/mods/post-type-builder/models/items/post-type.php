<?php

namespace The7_Core\Mods\Post_Type_Builder\Models\Items;

defined( 'ABSPATH' ) || exit;

class Post_Type extends Item_Model {

	public function get_relations() {
		return (array) $this->data['taxonomies'];
	}

	protected function update_relations( $data ) {
		$this->data['taxonomies'] = (array) $data;
	}

	protected function sanitize( $data ) {
		$data = wp_parse_args(
			$data,
			[
				'name'                        => '',
				'label'                       => '',
				'singular_label'              => '',
				'description'                 => '',
				'public'                      => '1',
				'publicly_queryable'          => '1',
				'show_ui'                     => '1',
				'show_in_nav_menus'           => '1',
				'delete_with_user'            => '0',
				'show_in_rest'                => '1',
				'rest_base'                   => '',
				'rest_controller_class'       => '',
				'has_archive'                 => '0',
				'has_archive_string'          => '',
				'exclude_from_search'         => '0',
				'capability_type'             => 'post',
				'hierarchical'                => '0',
				'rewrite'                     => '1',
				'rewrite_slug'                => '',
				'rewrite_withfront'           => '1',
				'query_var'                   => '1',
				'query_var_slug'              => '',
				'menu_position'               => '10',
				'show_in_menu'                => '1',
				'show_in_menu_string'         => '',
				'show_thumbnail_admin_column' => '0',
				'menu_icon'                   => '',
				'supports'                    => [
					'title',
					'editor',
					'thumbnail',
				],
				'taxonomies'                  => [],
				'bundled_taxonomies'          => [],
				'labels'                      => [],
				'custom_supports'             => '',
			]
		);

		return $data;
	}

}
