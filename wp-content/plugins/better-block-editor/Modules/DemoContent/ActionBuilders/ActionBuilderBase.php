<?php
/**
 * Base action builder for demo import actions.
 *
 * @package BetterBlockEditor
 */

namespace BetterBlockEditor\Modules\DemoContent\ActionBuilders;

use BetterBlockEditor\Modules\DemoContent\Demo\Demo;

defined( 'ABSPATH' ) || exit;

/**
 * Provides shared behaviour for building demo import actions.
 */
abstract class ActionBuilderBase {

	/**
	 * Introductory text shown before actions run.
	 *
	 * @var string
	 */
	protected $starting_text = '';

	/**
	 * Error message generated during setup.
	 *
	 * @var string|null
	 */
	protected $error;

	/**
	 * External data passed to the builder.
	 *
	 * @var array
	 */
	protected $external_data;

	/**
	 * Demo instance currently being processed.
	 *
	 * @var Demo
	 */
	protected $demo;

	/**
	 * Prepares the localized data structure consumed by JS.
	 *
	 * @return void
	 */
	abstract protected function setup_data();

	/**
	 * Base constructor storing demo and context.
	 *
	 * @param Demo  $demo          Demo instance.
	 * @param array $external_data Additional data.
	 */
	public function __construct( $demo, $external_data = array() ) {
		$this->demo          = $demo;
		$this->external_data = $external_data;

		$this->init();
	}

	/**
	 * Localises action data to JavaScript when no errors are present.
	 *
	 * @return void
	 */
	public function localize_data_to_js() {
		if ( ! empty( $this->error ) ) {
			return;
		}

		$this->setup_data();
	}

	/**
	 * Retrieves the starting text for the action list.
	 *
	 * @return string Starting text.
	 */
	public function get_starting_text() {
		return $this->starting_text;
	}

	/**
	 * Retrieves the current error message, if any.
	 *
	 * @return string|null Error message or null when none exists.
	 */
	public function get_error() {
		return $this->error;
	}

	/**
	 * Hook for subclasses to run custom initialisation.
	 *
	 * @return void
	 */
	protected function init() {
		// Do nothing by default.
	}

	/**
	 * Sets the initial status text.
	 *
	 * @param string $text Status text.
	 * @return void
	 */
	protected function setup_starting_text( $text ) {
		$this->starting_text = $text;
	}

	/**
	 * Stores an error message preventing further localisation.
	 *
	 * @param string $error_text Error description.
	 * @return void
	 */
	protected function add_error( $error_text ) {
		$this->error = $error_text;
	}

	/**
	 * Provides access to the demo instance.
	 *
	 * @return Demo Demo object.
	 */
	protected function demo() {
		return $this->demo;
	}

	/**
	 * Localises import data for consumption by the admin script.
	 *
	 * @param array $data Data array to localise.
	 * @return void
	 */
	protected function localize_import_data( $data = array() ) {
		wp_localize_script( WPBBE_PLUGIN_ID . '__demo-content__admin-script', 'wpbbeImportData', $data );
	}
}
