<?php

namespace The7_Core\Mods\Post_Type_Builder\Models\Items;

defined( 'ABSPATH' ) || exit;

abstract class Bundled_Item_Model extends Item_Model {

	protected $bundled_data_class;

	public function __construct( $data, $bundled_data_class = null ) {
		$this->bundled_data_class = $bundled_data_class;

		parent::__construct( $data );
	}

	/**
	 * @param array $data
	 *
	 * @return mixed
	 */
	protected function sanitize( $data ) {
		foreach ( $data as $key => $value ) {
			if ( is_array( $value ) ) {
				array_map( 'sanitize_text_field', $data[ $key ] );
			} else {
				$data[ $key ] = sanitize_text_field( $value );
			}
		}

		if ( $this->bundled_data_class ) {
			$data['disabled'] = ! $this->bundled_data_class::is_active();
		}

		if ( ! isset( $data['show_thumbnail_admin_column'] ) ) {
			$data['show_thumbnail_admin_column'] = true;
		}

		$data['predefined'] = true;

		return $data;
	}

}
