<?php

namespace The7_Core\Mods\Post_Type_Builder\Models\Items;

defined( 'ABSPATH' ) || exit;

class Bundled_Post_Type extends Bundled_Item_Model {

	public function get_relations() {
		return (array) $this->data['taxonomies'];
	}

	protected function update_relations( $data ) {
		$this->data['taxonomies'] = (array) $data;
	}

	protected function transform_canonical( $post_type, $data ) {
		$local_data = [
			'name'                  => $post_type,
			'label'                 => $data['labels']['name'],
			'singular_label'        => $data['labels']['singular_name'],
			'description'           => '',
			'public'                => $data['public'],
			'publicly_queryable'    => $data['publicly_queryable'],
			'show_ui'               => $data['show_ui'],
			'show_in_nav_menus'     => $data['show_in_menu'],
			'delete_with_user'      => '0',
			'show_in_rest'          => isset( $data['show_in_rest'] ) ? $data['show_in_rest'] : '1',
			'rest_base'             => '',
			'rest_controller_class' => '',
			'has_archive'           => $data['has_archive'],
			'has_archive_string'    => '',
			'exclude_from_search'   => '0',
			'capability_type'       => $data['capability_type'],
			'hierarchical'          => $data['hierarchical'],
			'rewrite'               => empty( $data['rewrite'] ) ? '0' : '1',
			'rewrite_slug'          => isset( $data['rewrite']['slug'] ) ? $data['rewrite']['slug'] : '',
			'rewrite_withfront'     => '0',
			'query_var'             => $data['query_var'],
			'query_var_slug'        => '',
			'menu_position'         => $data['menu_position'],
			'show_in_menu'          => $data['show_in_menu'],
			'show_in_menu_string'   => '',
			'menu_icon'             => '',
			'supports'              => $data['supports'],
			'taxonomies'            => isset( $data['taxonomies'] ) ? $data['taxonomies'] : [],
			'bundled_taxonomies'    => isset( $data['taxonomies'] ) ? $data['taxonomies'] : [],
			'labels'                => $data['labels'],
			'custom_supports'       => '',
		];

		return $local_data;
	}

	protected function sanitize( $data ) {
		$whitelist = [
			'name',
			'label',
			'singular_label',
			'description',
			'delete_with_user',
			'has_archive',
			'has_archive_string',
			'exclude_from_search',
			'rewrite',
			'rewrite_slug',
			'rewrite_withfront',
			'menu_position',
			'menu_icon',
			'taxonomies',
			'labels',
			'show_thumbnail_admin_column',
		];

		$data = array_intersect_key( $data, array_fill_keys( $whitelist, null ) );

		if ( ! isset( $data['taxonomies'] ) ) {
			$data['taxonomies'] = [];
		}

		if ( $this->bundled_data_class ) {
			$converted_data     = $this->transform_canonical( $this->bundled_data_class::get_name(), $this->bundled_data_class::get_args() );
			$data['taxonomies'] = array_unique( array_merge( $converted_data['taxonomies'], $data['taxonomies'] ) );
			$data               = array_merge( $converted_data, $data );
		}

		return parent::sanitize( $data );
	}

}
