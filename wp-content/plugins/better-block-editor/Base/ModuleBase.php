<?php
/**
 * Base class for all modules in BetterBlockEditor plugin.
 *
 * @package BetterBlockEditor
 */

namespace BetterBlockEditor\Base;

use BetterBlockEditor\Core\Settings;
use BetterBlockEditor\Plugin;

defined( 'ABSPATH' ) || exit;

/**
 * Base class for all modules.
 * Provides singleton implementation.
 */
abstract class ModuleBase implements ModuleInterface {
	/**
	 * Singleton instances
	 *
	 * @var ModuleBase[]
	 */
	protected static $_instances = array();

	/**
	 * Define if module is core
	 *
	 * @var bool
	 */
	const IS_CORE_MODULE = false;

	/**
	 * Order to sort on settings page
	 *
	 * @var int
	 */
	const SETTINGS_ORDER = 1024;

	/**
	 * To be set in implementations
	 *
	 * @var string|null unique value that identifies module
	 */
	const MODULE_IDENTIFIER = null;

	// apply only to editor interface
	const EDITOR_ASSET_KEY = 'editor';

	// apply only to editor content edit area (aka editor content or editor iframe)
	const EDITOR_CONTENT_ASSET_KEY = 'editor-content';

	// apply only to what user see in non admin (public) area (aka frontend)
	// when he interacts with site
	const VIEW_ASSET_KEY = 'view';

	// apply to both editor content and frontend
	const COMMON_ASSET_KEY = 'common';

	// path to module assets in build folder (dist)
	const ASSETS_BUILD_PATH = null;


	/**
	 * Singleton implementation.
	 */
	public static function instance() {
		$class_name = static::class;

		if ( empty( static::$_instances[ $class_name ] ) ) {
			static::$_instances[ $class_name ] = new static();
		}

		return static::$_instances[ $class_name ];
	}

	protected function __construct() {
	}

	/**
	 * core modules provides core functionality used by other modules
	 * they can not be disabled
	 */
	public static function is_core_module() {
		return static::IS_CORE_MODULE;
	}

	public static function get_default_state() {
		return true;
	}

	public static function is_active() {
		return true;
	}

	/**
	 * {inheritdocs}
	 *
	 * @return string
	 */
	public static function get_identifier() {
		if ( empty( static::MODULE_IDENTIFIER ) ) {
			throw new \Exception( 'Module ' . static::class . ' identifier not set.' );
		}

		return static::MODULE_IDENTIFIER;
	}

	/**
	 * {inheritdocs}
	 */
	public static function get_settings_order() {
		return static::SETTINGS_ORDER;
	}

	/**
	 * {inheritdocs}
	 */
	public static function get_tab() {
		return Settings::TAB_FEATURES;
	}

	/**
	 * {inheritdocs}
	 */
	public static function get_title() {
		return self::class;
	}

	/**
	 * {inheritdocs}
	 */
	public static function get_label() {
		return '';
	}

	/**
	 * {inheritdocs}
	 */
	public static function get_description() {
		return '';
	}

	/**
	 * {inheritdocs}
	 */
	public function init() {
		$this->process_assets();
	}

	/**
	 * {inheritdocs}
	 *
	 * This method should be overridden by child classes if required.
	 *
	 * @return void
	 */
	public function setup_hooks() {
		// some modules don't use hooks at all
	}

	/**
	 * Checks if the module has assets and registers and enqueues them for the editor and view (aka frontend).
	 * It also handles common assets that are used in both editor and view interfaces.
	 *
	 * @see https://developer.wordpress.org/block-editor/how-to-guides/enqueueing-assets-in-the-editor/
	 *
	 * @return void
	 */
	protected function process_assets() {
		// process assets only if we are not in asset bundle mode
		if ( Plugin::instance()->is_asset_bundle_mode() ) {
			return;
		}

		// some modules don't have assets at all
		if ( empty( $this::ASSETS_BUILD_PATH ) ) {
			return;
		}

		// editor interface assets
		if ( file_exists( $this->get_assets_full_path() . $this::EDITOR_ASSET_KEY . '.js' ) ) {
			$this->register_assets( $this::EDITOR_ASSET_KEY );
			add_action(
				'enqueue_block_editor_assets',
				function () {
					$this->enqueue_assets( $this::EDITOR_ASSET_KEY );
				}
			);
		}

		// editor content edit area assets (add check for admin panel, otherwise it will be added to FE as well)
		if ( is_admin() && file_exists( $this->get_assets_full_path() . $this::EDITOR_CONTENT_ASSET_KEY . '.js' ) ) {
			$this->register_assets( $this::EDITOR_CONTENT_ASSET_KEY );
			add_action(
				'enqueue_block_assets',
				function () {
					$this->enqueue_assets( $this::EDITOR_CONTENT_ASSET_KEY );
				}
			);
		}

		// view assets
		if ( file_exists( $this->get_assets_full_path() . $this::VIEW_ASSET_KEY . '.js' ) ) {
			$this->register_assets( $this::VIEW_ASSET_KEY );
			add_action(
				'wp_enqueue_scripts',
				function () {
					$this->enqueue_assets( $this::VIEW_ASSET_KEY );
				}
			);
		}

		// common assets (used in both editor and view)
		if ( file_exists( $this->get_assets_full_path() . $this::COMMON_ASSET_KEY . '.js' ) ) {
			$this->register_assets( $this::COMMON_ASSET_KEY );

			add_action(
				is_admin() ? 'enqueue_block_assets' : 'wp_enqueue_scripts',
				function () {
					$this->enqueue_assets( $this::COMMON_ASSET_KEY );
				}
			);
		}
	}

	protected function register_assets( $key ) {
		// asset file must exist after webpack build
		$asset_file = require $this->get_assets_full_path() . $key . '.asset.php';

		wp_register_script(
			$this->build_script_handle( $key ),
			$this->get_asset_url( $key ) . '.js',
			$asset_file['dependencies'],
			$asset_file['version'],
			array(
				'strategy'  => 'defer',
				'in_footer' => true,
			)
		);

		if ( file_exists( $this->get_assets_full_path() . $key . '.css' ) ) {
			wp_register_style(
				$this->build_style_handle( $key ),
				$this->get_asset_url( $key ) . '.css',
				array(), // no dependencies for styles by default
				$asset_file['version']
			);
		}
	}

	protected function enqueue_assets( $key ) {
		wp_enqueue_script( $this->build_script_handle( $key ) );
		wp_enqueue_style( $this->build_style_handle( $key ) );
	}

	/**
	 * Full path to module assets in file system
	 */
	protected function get_assets_full_path() {
		return WPBBE_DIST . $this::ASSETS_BUILD_PATH;
	}

	/**
	 * Unique string that used to identify style by WP
	 */
	protected function build_style_handle( $key ) {
		return WPBBE_PLUGIN_ID . '__' . $this->get_identifier() . '__' . $key . '-style';
	}

	/**
	 * Unique string that used to identify script by WP
	 * For library modules that provide dependencies
	 * script handle has to be the same as it's provided
	 * in webpack DependencyExtractionWebpackPlugin requestToHandle return
	 */
	protected function build_script_handle( $key ) {
		return WPBBE_PLUGIN_ID . '__' . $this->get_identifier() . '__' . $key . '-script';
	}

	/**
	 * URL to module assets
	 */
	protected function get_asset_url( $key ) {
		return WPBBE_URL_DIST . $this::ASSETS_BUILD_PATH . $key;
	}


	/**
	 * Get stored options for this module.
	 *
	 * @param string|null $key     Optional suboption key.
	 * @param mixed       $default Default if option/suboption does not exist.
	 *
	 * @return mixed
	 */
	protected function get_option( string $key = null, $default = null ) {
		$option_name = Settings::build_module_settings_name( $this->get_identifier() );
		return Settings::get_setting($option_name, $key, $default );
	}

	protected function set_option( string $key, $value ) {
		$option_name = Settings::build_module_settings_name( $this->get_identifier() );
		$options = get_option( $option_name, array() );
		$options[ $key ] = $value;
		$sanitized = sanitize_option( $option_name, $options );
		update_option( $option_name, $sanitized );
		return $sanitized;
	}

	/**
	 * Disable class cloning and throw an error on object clone (singleton implementation).
	 */
	public function __clone() {
		_doing_it_wrong(
			__FUNCTION__,
			sprintf( 'Cloning instances of the singleton "%s" class is forbidden.', get_class( $this ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			'1.0.0'
		);
	}


	/**
	 * Disable unserializing of the class (singleton implementation).
	 */
	public function __wakeup() {
		_doing_it_wrong(
			__FUNCTION__,
			sprintf( 'Unserializing instances of the singleton "%s" class is forbidden.', get_class( $this ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			'1.0.0'
		);
	}
}
