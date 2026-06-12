<?php
/**
 * Global Callback Module
 *
 * @package BetterBlockEditor
 */

namespace BetterBlockEditor\Modules\GlobalCallback;

use BetterBlockEditor\Base\ModuleBase;

defined( 'ABSPATH' ) || exit;

class Module extends ModuleBase {

	const MODULE_IDENTIFIER = 'core-global-callback';
	const ASSETS_BUILD_PATH = 'editor/global-callback/';

	const IS_CORE_MODULE = true;

	/**
	 * !!! IMPORTANT !!!
	 * rewrite to MATCH HANDLE in webpack settings
	 *
	 * @see parent::build_script_handle() comment
	 * @param string $key
	 * @return string
	 */
	protected function build_script_handle( $key ): string {
		return 'wpbbe-global-callback';
	}

	/**
	 * It's a core module which is included in dependency lists of other modules
	 */
	protected function process_assets(): void {

		$asset_file = require $this->get_assets_full_path() . 'index.asset.php';
		// we need the store to be created earlier to be used by other modules
		// so load it in header and in sync mode (defaults)
		wp_register_script(
			$this->build_script_handle( 'index' ),
			$this->get_asset_url('index.js'),
			$asset_file['dependencies'],
			$asset_file['version'],
			array(
				'strategy'  => 'defer',
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
