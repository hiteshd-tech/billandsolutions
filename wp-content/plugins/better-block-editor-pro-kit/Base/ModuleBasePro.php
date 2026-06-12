<?php
/**
 * Base class for all pro modules.
 *
 * @package BbeProKit
 */

namespace BbeProKit\Base;

use BetterBlockEditor\Base\ModuleBase;
use BetterBlockEditor\Base\ModuleInterface;

defined( 'ABSPATH' ) || exit;

/**
 * Base class for all Pro modules.
 * Provides singleton implementation.
 */
abstract class ModuleBasePro extends ModuleBase implements ModuleInterface {

	/**
	 * Full path to module assets in file system
	 */
	protected function get_assets_full_path() {
		return BBE_PRO_KIT_DIST . $this::ASSETS_BUILD_PATH;
	}

	protected function get_asset_url( $key ) {
		return BBE_PRO_KIT_URL_DIST . $this::ASSETS_BUILD_PATH . $key;
	}

	/**
	 * Unique string that used to identify style by WP
	 */
	protected function build_style_handle( $key ) {
		return BBE_PRO_KIT_PLUGIN_ID . '__' . $this->get_identifier() . '__' . $key . '-style';
	}

	/**
	 * Unique string that used to identify script by WP
	 * For library modules that provide dependencies
	 * script handle has to be the same as it's provided
	 * in webpack DependencyExtractionWebpackPlugin requestToHandle return
	 */
	protected function build_script_handle( $key ) {
		return BBE_PRO_KIT_PLUGIN_ID . '__' . $this->get_identifier() . '__' . $key . '-script';
	}
}
