<?php
/**
 * Main plugin class.
 *
 * @package BetterBlockEditor
 */

namespace BetterBlockEditor;

use BetterBlockEditor\Core\ModulesManager;
use BetterBlockEditor\Core\BundledAssetsManager;
use BetterBlockEditor\Core\Settings;

defined( 'ABSPATH' ) || exit;
class Plugin {

	/**
	 * @var Plugin
	 */
	private static $_instance;

	/**
	 * @var ModulesManager
	 */
	public $modules_manager;

	/**
	 * @var BundledAssetsManager
	 */
	public $bundled_assets_manager;

	/**
	 * Plugin constructor.
	 */
	private function __construct() {
		add_action( 'init', array( $this, 'on_init' ), 0 );

		// settings menu item and page.
		add_action( 'admin_init', array( Settings::class, 'settings_init' ) );
		add_action( 'rest_api_init', array( Settings::class, 'rest_settings_init' ) );
		add_action( 'admin_menu', array( Settings::class, 'settings_page' ) );

		// add link to settings page in plugins list.
		add_filter(
			'plugin_action_links_' . WPBBE_BASE,
			function ( $links ) {
				$url = admin_url( 'options-general.php?page=' . Settings::MENU_PAGE_SLUG );
				array_push( $links, '<a href="' . $url . '">' . esc_html( __( 'Settings', 'better-block-editor' ) ) . '</a>' );

				return $links;
			}
		);

		// add custom links to plugin row meta
		add_filter( 'plugin_row_meta', function( $links, $file ) {
			if ( $file === 'better-block-editor/better-block-editor.php' ) {
				$links[] = sprintf(
					'<a href="%s" target="_blank">%s</a>',
					esc_url( 'https://docs.wpbbe.io' ),
					esc_html__( 'User guide', 'better-block-editor' )
				);

				$links[] = sprintf(
					'<a href="%s" target="_blank">%s</a>',
					esc_url( 'https://wpbbe.io/contact/' ),
					esc_html__( 'Report a problem', 'better-block-editor' )
				);
			}

			return $links;
		}, 10, 2 );
	}

	/**
	 * Singleton implementation
	 *
	 * @return self
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();

			do_action( 'wpbbe/loaded' );
		}

		return self::$_instance;
	}

	/**
	 * Uninstall hook.
	 *
	 * @return void
	 */
	public static function on_uninstall() {
		// This is a placeholder for any future uninstall logic.
	}

	public function on_init() {
		$this->modules_manager = new ModulesManager();
		$this->modules_manager->setup_hooks();

		// in case there is BBE Pro Kit we have to add BBE Pro Kit bundles as dependencies for our bundles
		// so they are loaded before our bundles (based on defer loading strategy).
		$dependencies =array();  

		if ( defined('BBE_PRO_KIT_PLUGIN_ID')) {
			$supported_bundles = array(
				BundledAssetsManager::EDITOR_BUNDLE,
				BundledAssetsManager::EDITOR_CONTENT_BUNDLE,
				BundledAssetsManager::VIEW_BUNDLE,
			);

			foreach ( $supported_bundles as $bundle ) {
				$dependencies[ $bundle ] = array(
					BundledAssetsManager::build_handle( BBE_PRO_KIT_PLUGIN_ID, $bundle, 'script' )
				);
			}
		}
		
		$this->bundled_assets_manager = new BundledAssetsManager( 
			WPBBE_PLUGIN_ID, 
			WPBBE_DIST, 
			WPBBE_URL_DIST, 
			$dependencies
		);

		if ( $this->is_asset_bundle_mode() ) {
			if ( is_admin() ) {
				$this->bundled_assets_manager->process_editor_assets();
				$this->bundled_assets_manager->process_editor_content_assets();
			} else {
				$this->bundled_assets_manager->process_view_assets();
			}
		}

		do_action( 'wpbbe/init' );
	}


	/**
	 * Check if the plugin is in asset bundle mode.
	 * Asset bundle mode means that all modules are enabled and their assets
	 * can be loaded as a single bundle.
	 * If any module is disabled, we need to load each module assets separately.
	 *
	 * @return bool
	 */
	public function is_asset_bundle_mode() {
		$disabled_modules = array_filter(
			$this->modules_manager->get_managable_modules_data(),
			function ( $module_data ) {
				return $module_data['is_freemium'] && ! $module_data['enabled'];
			}
		);
		return empty( $disabled_modules );
	}

	/**
	 * Check if a feature (module) is active
	 *
	 * @param string $feature Feature identifier.
	 *
	 * @return bool
	 */
	public function is_feature_active( $feature ) {
		return (bool) $this->modules_manager->get_modules( $feature );
	}


	/**
	 * Get all features (modules)
	 *
	 * @return array
	 */
	public function get_active_features_keys() {
		$data = array();

		$modules = $this->modules_manager->get_modules();

		foreach ( $modules as $module ) {
			if ( $module::is_core_module() ) {
				continue;
			}
			$data[] = $module::get_identifier();
		}

		return $data;
	}


	/**
	 * Clone.
	 * Disable class cloning and throw an error on object clone.
	 * The whole idea of the singleton design pattern is that there is a single
	 * object. Therefore, we don't want the object to be cloned.
	 *
	 * @since  1.7.0
	 * @access public
	 */
	public function __clone() {
		_doing_it_wrong(
			__FUNCTION__,
			sprintf( 'Cloning instances of the singleton "%s" class is forbidden.', get_class( $this ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			'1.0.0'
		);
	}

	/**
	 * Wakeup.
	 * Disable unserializing of the class.
	 *
	 * @since  1.7.0
	 * @access public
	 */
	public function __wakeup() {
		_doing_it_wrong(
			__FUNCTION__,
			sprintf( 'Unserializing instances of the singleton "%s" class is forbidden.', get_class( $this ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			'1.0.0'
		);
	}
}
