<?php
/**
 * Content tracker for demo imports.
 *
 * Stores information about imported items and demo history.
 *
 * @package BetterBlockEditor
 */

namespace BetterBlockEditor\Modules\DemoContent\Trackers;

defined( 'ABSPATH' ) || exit;

/**
 * Tracks imported demo content and settings.
 *
 * Utilizes WordPress options and metadata to keep a record of imported items,
 * allowing for easy rollback or management of demo content.
 */
class ContentTracker extends TrackerBase {

	/**
	 * Option meta key stored on imported objects for tracking.
	 */
	const META_KEY = '_wpbbe_imported_item';

	/**
	 * Retrieves a tracked value by key.
	 *
	 * @param string $key History key.
	 * @return mixed False if missing, otherwise stored value.
	 */
	public function get( $key ) {
		if ( empty( $this->demo_history[ $this->demo_type ][ $key ] ) ) {
			return false;
		}

		return $this->demo_history[ $this->demo_type ][ $key ];
	}

	/**
	 * Stores a value for the current demo type.
	 *
	 * @param string $key   History key.
	 * @param mixed  $value Value to store.
	 * @return void
	 */
	public function set( $key, $value ) {
		$this->demo_history[ $this->demo_type ][ $key ] = $value;
		$this->save_demo_history();
	}

	/**
	 * Adds a value if it doesn't already exist.
	 *
	 * @param string $key   History key.
	 * @param mixed  $value Value to store.
	 * @return void
	 */
	public function add( $key, $value ) {
		if ( ! isset( $this->demo_history[ $this->demo_type ][ $key ] ) ) {
			$this->demo_history[ $this->demo_type ][ $key ] = $value;
			$this->save_demo_history();
		}
	}

	/**
	 * Removes a tracked value for the current demo type.
	 *
	 * @param string $key History key to delete.
	 * @return void
	 */
	public function remove( $key ) {
		unset( $this->demo_history[ $this->demo_type ][ $key ] );
		$this->save_demo_history();
	}

	/**
	 * Gets the currently selected demo type.
	 *
	 * @return string Demo type identifier.
	 */
	public function get_demo_type() {
		return $this->demo_type;
	}

	/**
	 * Adds filters to tag imported objects with demo metadata.
	 *
	 * @return void
	 */
	public function track_imported_items() {
		add_filter( 'wp_import_post_meta', array( $this, 'add_wpbbe_imported_item_meta_filter' ), 10, 3 );
		add_filter( 'wp_import_term_meta', array( $this, 'add_wpbbe_imported_item_meta_filter' ), 10, 3 );
	}

	/**
	 * Keeps imported content but removes history for the demo.
	 *
	 * @return void
	 */
	public function keep_demo_content() {
		delete_metadata( 'post', 0, self::META_KEY, $this->demo_type, true );
		delete_metadata( 'term', 0, self::META_KEY, $this->demo_type, true );

		$this->remove_demo();
	}

	/**
	 * Appends tracking metadata during WP importer execution.
	 *
	 * @param array $meta    Existing meta entries.
	 * @param int   $post_id Post being imported.
	 * @param array $post    Original post data.
	 * @return array Modified metadata list.
	 */
	public function add_wpbbe_imported_item_meta_filter( $meta, $post_id = 0, $post = array() ) {
		$meta[] = array(
			'key'   => self::META_KEY,
			'value' => $this->demo_type,
		);

		return $meta;
	}

	/**
	 * Removes all tracked data for the current demo type.
	 *
	 * @return void
	 */
	public function remove_demo() {
		if ( isset( $this->demo_history[ $this->demo_type ] ) ) {
			unset( $this->demo_history[ $this->demo_type ] );
		}
		$this->save_demo_history();
	}

	/**
	 * Persists demo history to the database.
	 *
	 * @return void
	 */
	protected function save_demo_history() {
		update_option( static::HISTORY_OPTION_ID, $this->demo_history, false );
	}
}
