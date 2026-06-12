<?php
/**
 * @package The7
 */

defined( 'ABSPATH' ) || exit;

/**
 * Imports The7 Post Type Builder data.
 */
class The7_Post_Types_Builder_Data_Importer {

	const TRACKER_POST_TYPES_KEY = 'pt_builder_post_types';
	const TRACKER_TAXONOMIES_KEY = 'pt_builder_taxonomies';
	const DATA_POST_TYPES_KEY    = 'post_types';
	const DATA_TAXONOMIES_KEY    = 'taxonomies';

	/**
	 * @var The7_Demo_Content_Tracker
	 */
	private $content_tracker;

	/**
	 * @param The7_Demo_Content_Tracker $content_tracker Content tracker class.
	 */
	public function __construct( $content_tracker ) {
		$this->content_tracker = $content_tracker;
	}

	/**
	 * @param array $data Builder data array.
	 *
	 * @return void
	 */
	public function import( array $data ) {
		if ( ! empty( $data[ self::DATA_POST_TYPES_KEY ] ) && is_array( $data[ self::DATA_POST_TYPES_KEY ] ) ) {
			$this->import_post_types( $data[ self::DATA_POST_TYPES_KEY ] );
		}

		if ( ! empty( $data[ self::DATA_TAXONOMIES_KEY ] ) && is_array( $data[ self::DATA_TAXONOMIES_KEY ] ) ) {
			$this->import_taxonomies( $data[ self::DATA_TAXONOMIES_KEY ] );
		}
	}

	/**
	 * @param array $data Post types data array.
	 *
	 * @return boolean
	 */
	protected function import_post_types( array $data ) {
		if ( ! class_exists( '\The7_Core\Mods\Post_Type_Builder\Models\Post_Types' ) ) {
			return false;
		}

		$post_types = (array) \The7_Core\Mods\Post_Type_Builder\Models\Post_Types::get();

		$this->backup_data( $data, $post_types, self::TRACKER_POST_TYPES_KEY );

		return \The7_Core\Mods\Post_Type_Builder\Models\Post_Types::save( array_merge( $post_types, $data ) );
	}

	/**
	 * @param array $data Taxonopmies data array.
	 *
	 * @return boolean
	 */
	protected function import_taxonomies( array $data ) {
		if ( ! class_exists( '\The7_Core\Mods\Post_Type_Builder\Models\Taxonomies' ) ) {
			return false;
		}

		$taxonomies = (array) \The7_Core\Mods\Post_Type_Builder\Models\Taxonomies::get();

		$this->backup_data( $data, $taxonomies, self::TRACKER_TAXONOMIES_KEY );

		return \The7_Core\Mods\Post_Type_Builder\Models\Taxonomies::save( array_merge( $taxonomies, $data ) );
	}

	/**
	 * @param  array  $new_data New data.
	 * @param  array  $origin_data Origin data.
	 * @param string $backup_key Bakup key.
	 */
	protected function backup_data( array $new_data, array $origin_data, $backup_key ) {
		$backup_data = [];
		foreach ( $new_data as $key => $value ) {
			$backup_data[ $key ] = isset( $origin_data[ $key ] ) ? $origin_data[ $key ] : [];
		}
		$this->content_tracker->add( $backup_key, $backup_data );
	}

}
