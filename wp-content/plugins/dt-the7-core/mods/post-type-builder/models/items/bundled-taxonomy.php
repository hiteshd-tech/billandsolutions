<?php

namespace The7_Core\Mods\Post_Type_Builder\Models\Items;

defined( 'ABSPATH' ) || exit;

class Bundled_Taxonomy extends Bundled_Item_Model {

	public function get_relations() {
		return $this->data['object_types'];
	}

	protected function update_relations( $data ) {
		return true;
	}

	protected function transform_canonical( $taxonomy, $data ) {
		$args = $data['args'];
		$post_types = $data['post_types'];

		$local_data = [
			'name'                  => $taxonomy,
			'label'                 => $args['labels']['name'],
			'singular_label'        => $args['labels']['singular_name'],
			'description'           => '',
			'public'                => $args['public'],
			'publicly_queryable'    => '1',
			'hierarchical'          => $args['hierarchical'],
			'show_ui'               => $args['show_ui'],
			'show_in_menu'          => '1',
			'show_in_nav_menus'     => '1',
			'query_var'             => '1',
			'query_var_slug'        => '',
			'rewrite'               => empty( $args['rewrite'] ) ? '0' : '1',
			'rewrite_slug'          => isset( $args['rewrite']['slug'] ) ? $args['rewrite']['slug'] : '',
			'rewrite_withfront'     => '0',
			'rewrite_hierarchical'  => '1',
			'show_admin_column'     => $args['show_admin_column'],
			'show_in_rest'          => $args['show_in_rest'],
			'show_tagcloud'         => '1',
			'show_in_quick_edit'    => '1',
			'rest_base'             => '',
			'rest_controller_class' => '',
			'labels'                => $args['labels'],
			'meta_box_cb'           => '',
			'default_term'          => '',
			'object_types'          => $post_types,
		];

		return $local_data;
	}

	protected function sanitize( $data ) {
		$whitelist = [
			'name',
			'label',
			'singular_label',
			'description',
			'rewrite',
			'rewrite_slug',
			'rewrite_withfront',
			'menu_position',
			'menu_icon',
			'rewrite_hierarchical',
			'labels',
			'default_term',
		];

		$data = array_intersect_key( $data, array_fill_keys( $whitelist, null ) );

		if ( $this->bundled_data_class ) {
			$converted_data = $this->transform_canonical( $this->bundled_data_class::get_name(), $this->bundled_data_class::get_args() );
			$data = array_merge( $converted_data, $data );
		}

		return parent::sanitize( $data );
	}

}
