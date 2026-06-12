<?php
/**
 * Main plugin class.
 *
 * @package BetterBlockEditor
 */

namespace BbeProKit;

use BetterBlockEditor\Base\ModuleInterface;
use BetterBlockEditor\Core\BundledAssetsManager;
use BetterBlockEditor\Plugin as CorePlugin;

defined( 'ABSPATH' ) || exit;

final class Plugin {

	/**
	 * @var Plugin
	 */
	private static $_instance;


	/**
	 * @var BundledAssetsManager
	 */
	public $bundled_assets_manager;


	/**
	 * Plugin constructor.
	 */
	private function __construct() {
		$this->setup_hooks();
	}

	private function setup_hooks(): void {
		register_activation_hook( BBE_PRO_KIT_FILE, array( __CLASS__, 'activation' ) );
		register_deactivation_hook( BBE_PRO_KIT_FILE, array( __CLASS__, 'deactivation' ) );
		register_uninstall_hook( BBE_PRO_KIT_FILE, array( __CLASS__, 'uninstall' ) );

		// add pro modules to the list of modules in core version
		add_filter( 'wpbbe_modules_classnames', array( $this, 'register_pro_modules' ) );

		add_action( 'wpbbe/init', array( $this, 'init' ) );
	}

	public function init(): void {
		$this->bundled_assets_manager = new BundledAssetsManager(
			BBE_PRO_KIT_PLUGIN_ID,
			BBE_PRO_KIT_DIST,
			BBE_PRO_KIT_URL_DIST
		);

		// maybe load bundled assets
		if ( $this->is_asset_bundle_mode() ) {

			if ( is_admin() ) {
				$this->bundled_assets_manager->process_editor_assets();
				$this->bundled_assets_manager->process_editor_content_assets();
			} else {
				$this->bundled_assets_manager->process_view_assets();
			}
		}

		do_action( 'bbe-pro-kit/init' );
	}

	/**
	 * Check if asset bundle mode is enabled (proxy call to the core plugin instance)
	 *
	 * @return bool True if asset bundle mode is enabled, false otherwise.
	 */
	public function is_asset_bundle_mode(): bool {
		return $this->get_core_plugin()->is_asset_bundle_mode();
	}

	/**
	 * Check if a feature is active (proxy call to the core plugin instance)
	 *
	 * @param string $feature Feature name.
	 * @return bool True if the feature is active, false otherwise.
	 */
	public function is_feature_active( string $feature ): bool {
		return $this->get_core_plugin()->is_feature_active( $feature );
	}

	/**
	 * Get the core plugin instance
	 *
	 * @return CorePlugin
	 */
	private function get_core_plugin(): CorePlugin {
		return CorePlugin::instance();
	}

	/**
	 * Add pro module files to the list of module classnames
	 *
	 * @param array $module_classnames List of module classnames.
	 * @return array Updated list of module classnames.
	 */
	public function register_pro_modules( $module_classnames ) {
		$pro_files = glob( BBE_PRO_KIT_DIR . 'Modules/*/Module.php' );

		foreach ( $pro_files as $file ) {
			$dirname          = pathinfo( $file, PATHINFO_DIRNAME );
			$module_name      = substr( $dirname, strrpos( $dirname, '/' ) + 1 );
			$module_classname = 'BbeProKit\Modules\\' . $module_name . '\\Module';

			if ( is_a( $module_classname, ModuleInterface::class, true ) ) {
				$module_classnames[] = $module_classname;
			}
		}

		return $module_classnames;
	}

	/**
	 * Singleton implementation
	 *
	 * @return self
	 */
	public static function instance(): self {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Activation hook.
	 *
	 * @return void
	 */
	public static function activation() {
	}

	/**
	 * Deactivation hook.
	 *
	 * @return void
	 */
	public static function deactivation() {
	}

	/**
	 * Uninstall hook.
	 *
	 * @return void
	 */
	public static function uninstall() {
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

Plugin::instance();
