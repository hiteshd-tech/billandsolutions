<?php
/**
 * Core class for Design System module.
 *
 * @package BetterBlockEditor
 */

namespace BetterBlockEditor\Modules\DesignSystemCore;

use BetterBlockEditor\Base\ModuleBase;
use BetterBlockEditor\Plugin;

defined( 'ABSPATH' ) || exit;

class Module extends ModuleBase {

	const MODULE_IDENTIFIER = 'design-system-core';
	const PLUGIN_ASSETS_BUILD_PATH = 'editor/plugins/design-system/';
	const IS_CORE_MODULE = true;

	const PARTS_ACTIVATED_FLAG = 'parts_activated_once_flag';

	const RESET_DESIGN_SYSTEM_FLAG_QUERY_PARAM = 'wpbbe_reset_ds_flag';

	public static function get_title() {
		return __( 'Design System Core', 'better-block-editor' );
	}

	public static function get_label() {
		return '';
	}

	protected function __construct() {
		add_filter( 'wpbbe_is_manageable_module_enabled', array( $this, 'disable_parts_module' ), 10, 2 );
	}

	public function setup_hooks() {
		add_filter( 'wpbbe_script_data', array( $this, 'add_script_data' ) );

		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
		//for testing purposes only, remove later
		add_action( 'admin_init', function () {
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			if ( isset( $_GET[ self::RESET_DESIGN_SYSTEM_FLAG_QUERY_PARAM ] ) && $_GET[ self::RESET_DESIGN_SYSTEM_FLAG_QUERY_PARAM ] === '1' ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				if ( $this->set_part_activated_flag( false ) ) {
					add_action( 'admin_notices', function () {
						echo '<div class="notice notice-success"><p>DesignSystemActivatedOnce flag has been reset.</p></div>';
					} );
				}
			}
		} );
	}

	public function register_rest_routes() {
		register_rest_route( WPBBE_REST_BASE, '/design-system-set-activated-once-flag', array(
				'methods'             => 'POST',
				'callback'            => function ( \WP_REST_Request $request ) {
					$flag = (bool) $request->get_param( 'activated' );
					$this->set_part_activated_flag( $flag );

					return array( 'success' => true, 'activated' => $flag );
				},
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			) );
	}


	public function disable_parts_module( $enabled, $module_classname ) {
		if ( ! $this->is_bbe_template() ) {
			return $enabled;
		}
		if ( $module_classname === 'BetterBlockEditor\Modules\DesignSystemParts\Module' ) {
			return false;
		}

		return $enabled;
	}

	/**
	 * Add design system data to the global script data.
	 *
	 * @param array $data The existing script data.
	 *
	 * @return array The modified script data with design system information.
	 */
	public function add_script_data( $data ) {
		$data['designSystem'] = array(
			'isBBETemplate'          => $this->is_bbe_template(),
			'partsActivatedOnceFlag' => $this->is_parts_activated_flag(),
		);

		return $data;
	}

	private function is_bbe_template() {
		$template = get_template();

		return in_array( $template, array( 'dt-the7', 'better-block-theme', 'bbe' ), true );
	}

	public function process_assets() {
		parent::process_assets();

		// in asset bundle mode plugin assets are already registered
		if ( Plugin::instance()->is_asset_bundle_mode() ) {
			return;
		}

		$asset_file = require WPBBE_DIST . $this::PLUGIN_ASSETS_BUILD_PATH . 'editor.asset.php';
		wp_register_script( $this->build_script_handle( 'editor-plugin' ), WPBBE_URL_DIST . $this::PLUGIN_ASSETS_BUILD_PATH . $this::EDITOR_ASSET_KEY . '.js', $asset_file['dependencies'], $asset_file['version'], array(
				'strategy'  => 'defer',
				'in_footer' => true,
			) );

		if ( file_exists( WPBBE_DIST . $this::PLUGIN_ASSETS_BUILD_PATH . $this::EDITOR_ASSET_KEY . '.css' ) ) {
			wp_register_style( $this->build_style_handle( 'editor-plugin' ), WPBBE_URL_DIST . $this::PLUGIN_ASSETS_BUILD_PATH . $this::EDITOR_ASSET_KEY . '.css', array(), $asset_file['version'] );
		}

		add_action( 'enqueue_block_editor_assets', function () {
			$this->enqueue_assets( 'editor-plugin' );
		} );
	}

	/**
	 * Check if the parts activated flag is set.
	 * @return bool True if the flag is set, false otherwise.
	 */
	public function is_parts_activated_flag(): bool {
		return (bool) $this->get_option( self::PARTS_ACTIVATED_FLAG, false );
	}

	/**
	 * Set the parts activated flag.
	 *
	 * @param bool $value The value to set the flag to. Default is true.
	 *
	 * @return bool True if the flag was changed, false if it was already set to the given value.
	 */
	public function set_part_activated_flag( bool $value = true ) {
		$current = $this->is_parts_activated_flag();
		if ( $current === $value ) {
			return false;
		}
		$this->set_option( self::PARTS_ACTIVATED_FLAG, $value );

		return true;
	}
}
