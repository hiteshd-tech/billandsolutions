<?php

namespace The7_Core\Mods\Post_Type_Builder\Models\Items;

defined( 'ABSPATH' ) || exit;

class Taxonomy extends Item_Model {

	public function get_relations() {
		return (array) $this->data['object_types'];
	}

	protected function update_relations( $data ) {
		$this->data['object_types'] = (array) $data;
	}

	protected function sanitize( $data ) {
		$data = wp_parse_args( $data, [
			'name'                  => '',
			'label'                 => '',
			'singular_label'        => '',
			'description'           => '',
			'public'                => '1',
			'publicly_queryable'    => '1',
			'hierarchical'          => '0',
			'show_ui'               => '1',
			'show_in_menu'          => '1',
			'show_in_nav_menus'     => '1',
			'query_var'             => '1',
			'query_var_slug'        => '',
			'rewrite'               => '1',
			'rewrite_slug'          => '',
			'rewrite_withfront'     => '1',
			'rewrite_hierarchical'  => '0',
			'show_admin_column'     => '0',
			'show_in_rest'          => '1',
			'show_tagcloud'         => '0',
			'show_in_quick_edit'    => '0',
			'rest_base'             => '',
			'rest_controller_class' => '',
			'labels'                => [],
			'meta_box_cb'           => '',
			'default_term'          => '',
			'object_types'          => [],
		] );

		return $data;
	}

}
