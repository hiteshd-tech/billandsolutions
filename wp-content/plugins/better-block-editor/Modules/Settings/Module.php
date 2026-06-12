<?php
/**
 * Expose some settings to JS in admin panel (JS level).
 *
 * @package BetterBlockEditor
 */

namespace BetterBlockEditor\Modules\Settings;

use BetterBlockEditor\Base\ModuleBase;
use BetterBlockEditor\Core\Settings;
use BetterBlockEditor\Plugin;

defined( 'ABSPATH' ) || exit;

class Module extends ModuleBase {


	const MODULE_IDENTIFIER = 'core-settings';
	const ASSETS_BUILD_PATH = 'editor/plugins/settings/';

	const IS_CORE_MODULE = true;

	/**
	 * rewrite processing assets as we need to increase priority in enqueue_block_editor_assets
	 * default priority is 10 and it's too late as when all blocks are loaded in editor already
	 * but we need user defined breakpoints to be used in our blocks
	 * 9 is fine (empirically)
	 */
	protected function process_assets() {
		// editor interface assets
		if ( ! file_exists( $this->get_assets_full_path() . $this::EDITOR_ASSET_KEY . '.js' ) ) {
			return;
		}

		$this->register_assets( $this::EDITOR_ASSET_KEY );

		add_action(
			'enqueue_block_editor_assets',
			function () {
				$this->enqueue_assets( $this::EDITOR_ASSET_KEY );
				// inject some additional data to JS
				// it's important to do as late as possible so all modules can add their data 
				// via wpbbe_script_data filter (sometimes we need there core data to be 
				// formed or functions initialized which happens on late stages of code execution)
				$this->enqueue_block_editor_inline_data();
			},
			9
		);
	}

	protected function enqueue_assets( $key ) {
		$script_handle = $this->build_script_handle( $key );
		wp_enqueue_script( $script_handle );
	}


	/**
	 * Inject inline modules script data to the frontend.
	 *
	 * This method attaches a global JS object (`window.WPBBE_DATA`).
	 * We use wp_add_inline_script() with the 'wp-block-editor' handle because:
	 * - 'wp-block-editor' is a core WordPress script always loaded in the block editor context.
	 * - Attaching inline data here ensures our global JS data (window.WPBBE_DATA) is available
	 *   before any of our custom block scripts run since they depend on 'wp-block-editor',
	 *   which is automatically defined as a dependency during the build process.
	 */
	protected function enqueue_block_editor_inline_data() {
		$data = array(
			'features'    => Plugin::instance()->get_active_features_keys(),
			'breakpoints' => $this->get_user_defined_breakpoints_data(),
		);

		$data = apply_filters( 'wpbbe_script_data', $data );

		wp_add_inline_script(
			'wp-block-editor',
			'window.WPBBE_DATA = ' . wp_json_encode( $data ) . ';'
		);
	}


	/**
	 * Get all user defined breakpoints data
	 *
	 * @return array
	 */
	protected function get_user_defined_breakpoints_data() {
		$data = array();
		foreach ( Settings::get_user_defined_breakpoints() as $key => $breakpoint ) {
			$js_data           = array();
			$js_data['key']    = $key;
			$js_data['name']   = $breakpoint['name'];
			$js_data['value']  = $breakpoint['value'] . $breakpoint['unit'];
			$js_data['active'] = $breakpoint['active'] ? true : false;

			$data[] = $js_data;
		}
		return $data;
	}
}
