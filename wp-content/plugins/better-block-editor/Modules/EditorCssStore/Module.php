<?php
/**
 * Editor CSS Store
 *
 * @package BetterBlockEditor
 */

namespace BetterBlockEditor\Modules\EditorCssStore;

use BetterBlockEditor\Base\ModuleBase;

defined( 'ABSPATH' ) || exit;

class Module extends ModuleBase {

	const MODULE_IDENTIFIER = 'core-editor-css-store';
	const ASSETS_BUILD_PATH = 'editor/editor-css-store/';

	const IS_CORE_MODULE = true;

	/**
	 * !!! IMPORTANT !!!
	 * rewrite to MATCH HANDLE in webpack settings
	 *
	 * @see parent::build_script_handle() comment
	 */
	protected function build_script_handle( $key ) {
		return 'wpbbe-editor-css-store';
	}

	/**
	 * It's a core module which is included in dependency lists of other modules
	 *
	 * also register here style to fix margins in block editor (details in readme)
	 */
	protected function process_assets() {

		if ( ! is_admin() ) {
			return;
		}

		$asset_file = require $this->get_assets_full_path() . 'index.asset.php';
		// we need the store to be created earlier to be used by other modules
		// so load it in header and in sync mode (defaults)
		wp_register_script(
			$this->build_script_handle( 'index' ),
			WPBBE_URL_DIST . $this::ASSETS_BUILD_PATH . 'index.js',
			$asset_file['dependencies'],
			$asset_file['version'],
			array(
				'strategy'  => 'async',
				'in_footer' => false,
			)
		);

		add_action(
			'enqueue_block_assets',
			function () {
				$this->enqueue_assets( 'index' );
			}
		);
	}
}
