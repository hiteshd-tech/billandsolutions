<?php

namespace The7_Core\Mods\Post_Type_Builder\Models;

use The7_Core\Mods\Post_Type_Builder\Models\Items\Item_Model;

defined( 'ABSPATH' ) || exit;

abstract class Composition_Model {

	/**
	 * @param string $item
	 *
	 * @return array|null
	 */
	public static function get( $item = null ) {
		$items = get_option( static::get_data_key(), [] );

		// Fix data in case of trouble.
		if ( ! is_array( $items ) ) {
			$items = [];
		}

		if ( $item ) {
			return isset( $items[ $item ] ) ? $items[ $item ] : null;
		}

		return $items;
	}

	/**
	 * @param array $data
	 *
	 * @return bool
	 */
	public static function save( $data ) {
		$data = array_map(
			function ( $item ) {
				if ( $item instanceof Item_Model ) {
					return $item->get_for_save();
				}

				return $item;
			},
			$data
		);

		return update_option( static::get_data_key(), array_filter( $data ) );
	}

	/**
	 * @param array $data
	 *
	 * @return bool
	 */
	public static function update( $data ) {
		$post_types = static::get();

		$post_types[ $data['name'] ] = static::create_item( $data );

		return static::save( $post_types );
	}

	/**
	 * @param string $slug
	 *
	 * @return bool
	 */
	public static function delete( $slug ) {
		if ( ! $slug ) {
			return false;
		}

		$slug  = strtolower( $slug );
		$items = static::get();

		if ( array_key_exists( $slug, $items ) ) {
			unset( $items[ $slug ] );

			return static::save( $items );
		}

		return false;
	}

	/**
	 * @param string $taxonomy_slug
	 *
	 * @return Item_Model[]|null
	 */
	public static function get_for_display_objects( $taxonomy_slug = null ) {
		$taxonomies = array_map( function ( $data ) {
			return static::create_item( $data );
		}, static::get() );

		$predefined = static::get_bundle_definition();

		foreach ( $predefined as $name => $class ) {
			if ( ! isset( $taxonomies[ $name ] ) ) {
				$taxonomies[ $name ] = static::create_item( [ 'name' => $name ] );
			}
		}

		if ( $taxonomy_slug ) {
			return isset( $taxonomies[ $taxonomy_slug ] ) ? $taxonomies[ $taxonomy_slug ] : null;
		}

		return array_filter( $taxonomies );
	}

	/**
	 * @param string $taxonomy_slug
	 *
	 * @return array|null
	 */
	public static function get_for_display( $taxonomy_slug = null ) {
		$taxonomies = static::get_for_display_objects( $taxonomy_slug );
		if ( is_array( $taxonomies ) ) {
			return array_map( function ( $item ) {
				return $item->get_raw();
			}, $taxonomies );
		} elseif ( $taxonomies ) {
			return $taxonomies->get_raw();
		}

		return null;
	}

	/**
	 * @param string $post_type
	 *
	 * @return void
	 */
	public static function mass_detach( $post_type ) {
		// Empty taxonomies list works as detach.
		static::mass_attach( $post_type, [] );
	}

	/**
	 * @param string $post_type
	 * @param string[]|null $tax_list
	 *
	 * @return bool
	 */
	public static function mass_attach( $post_type, $tax_list = null ) {
		if ( ! $post_type || $tax_list === null ) {
			return false;
		}

		$taxonomies_for_display = static::get_for_display_objects();

		foreach ( $taxonomies_for_display as $name => $taxonomy ) {
			$taxonomy->detach( $post_type );

			if ( $tax_list && in_array( $name, (array) $tax_list, true ) ) {
				$taxonomy->attach( $post_type );
			}
		}

		return static::save( $taxonomies_for_display );
	}

	/**
	 * @param array|Item_Model $data
	 *
	 * @return Item_Model
	 */
	public static function create_item( $data ) {
		if ( $data instanceof Item_Model) {
			return $data;
		}

		if ( ! is_array( $data ) ) {
			$data = [];
		}

		if ( isset( $data['name'] ) ) {
			$slug   = $data['name'];
			$bundle = static::get_bundle_definition();

			if ( isset( $bundle[ $slug ] ) ) {
				return static::create_bundled_item( $data, $bundle[ $slug ] );
			}
		}

		return static::create_custom_item( $data );
	}

	/**
	 * @return array
	 */
	public static function get_bundle_definition() {
		return [];
	}

	/**
	 * @return string
	 */
	abstract protected static function get_data_key();

	/**
	 * @param array $data
	 *
	 * @return Item_Model
	 */
	abstract protected static function create_custom_item( $data );

	/**
	 * @param array $data
	 * @param string $class
	 *
	 * @return Item_Model
	 */
	abstract protected static function create_bundled_item( $data, $class = null );

}
