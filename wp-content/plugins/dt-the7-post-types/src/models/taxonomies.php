<?php

namespace The7_Post_Types\Models;

use The7_Post_Types\Models\Items\Bundled_Taxonomy;
use The7_Post_Types\Models\Items\Taxonomy;

defined( 'ABSPATH' ) || exit;

class Taxonomies extends Composition_Model {

	/**
	 * @return string
	 */
	protected static function get_data_key() {
		return 'the7_core_taxonomies';
	}

	public static function update( $data ) {
		$result = parent::update( $data );

		if ( $result && ! empty( $data['name'] ) ) {
			delete_option( "default_term_{$data['name']}" );
		}

		return $result;
	}

	public static function delete( $slug ) {
		$result = parent::delete( $slug );

		if ( $result ) {
			delete_option( "default_term_{$slug}" );
		}

		return $result;
	}

	public static function convert( $original_slug, $new_slug ) {
		global $wpdb;

		$args = [
			'taxonomy'   => $original_slug,
			'hide_empty' => false,
			'fields'     => 'ids',
		];

		$term_ids = get_terms( $args );

		if ( is_int( $term_ids ) ) {
			$term_ids = (array) $term_ids;
		}

		if ( is_array( $term_ids ) && ! empty( $term_ids ) ) {
			$term_ids = implode( ',', $term_ids );

			$query = "UPDATE `{$wpdb->term_taxonomy}` SET `taxonomy` = %s WHERE `taxonomy` = %s AND `term_id` IN ( {$term_ids} )";

			$wpdb->query(
				$wpdb->prepare( $query, $new_slug, $original_slug )
			);
		}
	}

	/**
	 * @param $data
	 *
	 * @return Taxonomy
	 */
	protected static function create_custom_item( $data ) {
		return new Taxonomy( $data );
	}

	/**
	 * @param $data
	 * @param $class
	 *
	 * @return Bundled_Taxonomy
	 */
	protected static function create_bundled_item( $data, $class = null ) {
		return new Bundled_Taxonomy( $data, $class );
	}
}
