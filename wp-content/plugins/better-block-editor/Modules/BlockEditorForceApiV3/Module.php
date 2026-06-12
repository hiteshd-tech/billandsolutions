<?php
/**
 * Force iFrame in Block Editor for pages and posts
 *
 * @package BetterBlockEditor
 */

namespace BetterBlockEditor\Modules\BlockEditorForceApiV3;

use BetterBlockEditor\Base\ManagableModuleInterface;
use BetterBlockEditor\Base\ModuleBase;

defined( 'ABSPATH' ) || exit;

class Module extends ModuleBase implements ManagableModuleInterface {

	const MODULE_IDENTIFIER = 'block-editor-force-api-v3';
	const ASSETS_BUILD_PATH = 'editor/blocks/__all__/block-editor-force-api-v3/';

	const SETTINGS_ORDER = 1500;

	public function setup_hooks() {
		// expose current screen data from WP_Screen object to JS
		add_filter('wpbbe_script_data', array($this, 'expose_data_to_js' ));
	}


	public function expose_data_to_js($data) {
		$screen = get_current_screen();
		// cleanup and convert to camelCase
		$data['currentScreen'] = array();
		foreach ( $screen as $key => $value ) {
			if ( ! is_null( $value ) && $value !== '' ) {
				$camel_cased                 = \lcfirst( \str_replace( '_', '', \ucwords( $key, '_' ) ) );
				$data['currentScreen'][ $camel_cased ] = $value;
			}
		}
		
		// add information about is it custom post type (not available in JS by default)
		$post_type = get_post_type_object($screen->post_type);		

		$data['currentScreen']['isCustomPostType'] = $post_type && !$post_type->_builtin;

		return $data;	
	}

	public static function get_title() {
		return __( 'Force API v3', 'better-block-editor' );
	}

	public static function get_label() {
		return __( 'Force the Editor into iframe mode for the best performance and compatibility with BBE. This may conflict with older plugins, but it will become the native WordPress behavior starting with WP v.7.', 'better-block-editor' );
	}
}