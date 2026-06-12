<?php

namespace The7_Core\Mods\Post_Type_Builder\Models\Items;

defined( 'ABSPATH' ) || exit;

abstract class Item_Model {

	/**
	 * @var array
	 */
	protected $data;

	public function __construct( $data ) {
		$this->data = $this->sanitize( (array) $data );
	}

	public function get_raw() {
		return $this->data;
	}

	public function get_for_save() {
		$data     = $this->data;
		$defaults = $this->sanitize( [] );

		// Remove default values.
		foreach ( $defaults as $key => $value ) {
			if ( ! array_key_exists( $key, $data ) ) {
				continue;
			}

			if ( $data[ $key ] === null || $data[ $key ] == $value) {
				unset( $data[ $key ] );
			}
		}

		// The 'name' should always be there.
		if ( $data ) {
			$data['name'] = $this->data['name'];
		}

		return $data;
	}

	public function detach( $item ) {
		$relations = $this->get_relations();
		if ( $relations ) {
			$this->update_relations( array_diff( $relations, [ $item ] ) );
		}
	}

	public function attach( $item ) {
		$relations = $this->get_relations();
		$relations[] = $item;
		$this->update_relations( array_unique( $relations ) );
	}

	public function get_prop( $key ) {
		return isset( $this->data[ $key ] ) ? $this->data[ $key ] : null;
	}

	abstract public function get_relations();

	abstract protected function update_relations( $data );

	abstract protected function sanitize( $data );
}
