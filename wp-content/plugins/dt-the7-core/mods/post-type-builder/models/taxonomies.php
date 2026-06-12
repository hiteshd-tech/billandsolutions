<?php

namespace The7_Core\Mods\Post_Type_Builder\Models;

use The7_Core\Mods\Post_Type_Builder\Bundled\Albums_Category_Taxonomy;
use The7_Core\Mods\Post_Type_Builder\Bundled\Portfolio_Category_Taxonomy;
use The7_Core\Mods\Post_Type_Builder\Bundled\Portfolio_Tags_Taxonomy;
use The7_Core\Mods\Post_Type_Builder\Bundled\Team_Category_Taxonomy;
use The7_Core\Mods\Post_Type_Builder\Bundled\Testimonials_Category_Taxonomy;
use The7_Core\Mods\Post_Type_Builder\Models\Items\Bundled_Taxonomy;
use The7_Core\Mods\Post_Type_Builder\Models\Items\Taxonomy;

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
	 * @return string[]
	 */
	public static function get_bundle_definition() {
		return apply_filters(
			'the7_core_bundled_taxonomies_list',
			[
				'dt_portfolio_category'    => Portfolio_Category_Taxonomy::class,
				'dt_portfolio_tags'        => Portfolio_Tags_Taxonomy::class,
				'dt_testimonials_category' => Testimonials_Category_Taxonomy::class,
				'dt_team_category'         => Team_Category_Taxonomy::class,
				'dt_gallery_category'      => Albums_Category_Taxonomy::class,
			]
		);
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
