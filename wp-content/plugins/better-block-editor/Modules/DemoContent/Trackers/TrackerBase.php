<?php
/**
 * Abstract base for demo content trackers.
 *
 * Provides helper utilities for managing imported demo history.
 *
 * @package BetterBlockEditor
 */

namespace BetterBlockEditor\Modules\DemoContent\Trackers;

defined( 'ABSPATH' ) || exit;

/**
 * Base class describing the interface for demo content trackers.
 */
abstract class TrackerBase {

	/**
	 * Option ID used to store overall demo import history.
	 */
	const HISTORY_OPTION_ID = 'wpbbe_demo_history';

	/**
	 * Currently active demo type identifier.
	 *
	 * @var string
	 */
	protected $demo_type;

	/**
	 * Cached demo history array.
	 *
	 * @var array
	 */
	protected $demo_history;

	/**
	 * Sets up a tracker instance for a specific demo type.
	 *
	 * @param string $demo_type Demo type identifier.
	 */
	public function __construct( $demo_type ) {
		$this->demo_type    = $demo_type;
		$this->demo_history = get_option( static::HISTORY_OPTION_ID, array() );
	}

	/**
	 * Retrieves stored history for a particular demo.
	 *
	 * @param string $demo_id Demo identifier.
	 * @return array Stored history or empty array when missing.
	 */
	public static function get_demo_history( $demo_id ) {
		$demo_history = get_option( static::HISTORY_OPTION_ID, array() );

		if ( ! array_key_exists( $demo_id, $demo_history ) ) {
			return array();
		}

		return $demo_history[ $demo_id ];
	}

	/**
	 * Retrieves the entire demo history dataset.
	 *
	 * @return array Demo history indexed by demo identifier.
	 */
	public static function get_all_demo_history() {
		return get_option( static::HISTORY_OPTION_ID, array() );
	}

	/**
	 * Gets a value from the tracker by key.
	 *
	 * @param string $key History key.
	 * @return mixed
	 */
	abstract public function get( $key );

	/**
	 * Sets a value in the tracker.
	 *
	 * @param string $key   History key.
	 * @param mixed  $value Value to store.
	 * @return void
	 */
	abstract public function set( $key, $value );

	/**
	 * Adds a value to the tracker if not present.
	 *
	 * @param string $key   History key.
	 * @param mixed  $value Value to store.
	 * @return void
	 */
	abstract public function add( $key, $value );

	/**
	 * Removes a value from the tracker.
	 *
	 * @param string $key History key.
	 * @return void
	 */
	abstract public function remove( $key );

	/**
	 * Gets the demo type identifier represented by the tracker.
	 *
	 * @return string
	 */
	abstract public function get_demo_type();

	/**
	 * Hooks required to track imported items.
	 *
	 * @return void
	 */
	abstract public function track_imported_items();

	/**
	 * Keeps imported content while updating history state.
	 *
	 * @return void
	 */
	abstract public function keep_demo_content();

	/**
	 * Removes demo history and related metadata.
	 *
	 * @return void
	 */
	abstract public function remove_demo();
}
