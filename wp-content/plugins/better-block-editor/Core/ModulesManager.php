<?php
/**
 * Manages all available modules.
 * Set up hooks and enable modules based on settings.
 * Module detection is based on the module class names found in the `Modules` directory.
 *
 * @package BetterBlockEditor
 */

namespace BetterBlockEditor\Core;

use BetterBlockEditor\Base\ManagableModuleInterface;
use BetterBlockEditor\Base\ModuleInterface;

defined( 'ABSPATH' ) || exit;

final class ModulesManager {

	/** @var ModuleInterface[] */
	private $modules = array();
	/** @var string[] */
	private $manageable_module_classnames = array();
	/**
	 * Relative path to the modules directory.
	 *
	 * @var string
	 */
	const MODULE_NAMESPACE_BASE = 'BetterBlockEditor\Modules\\';

	/**
	 * Retrieve the class names of all available modules.
	 * File pat, naming convention and interface implementation are checked.
	 *
	 * @return string[] The array of module class names that instantiate the ModuleInterface.
	 */
	private function prepare_module_classnames() {
		foreach ( glob( WPBBE_DIR . 'Modules/*/Module.php' ) as $file ) {
			$dirname     = pathinfo( $file, PATHINFO_DIRNAME );
			$module_name = substr( $dirname, strrpos( $dirname, '/' ) + 1 );

			$module_classname = self::MODULE_NAMESPACE_BASE . $module_name . '\\Module';

			if ( is_a( $module_classname, ModuleInterface::class, true ) ) {
				$module_classnames[] = $module_classname;
			}
		}
		$module_classnames = apply_filters( 'wpbbe_modules_classnames', $module_classnames );

		$module_classnames = array_filter(
			$module_classnames,
			function ( $class ) {
				return is_a( $class, ModuleInterface::class, true );
			}
		);

		// Sort core modules first
		usort(
			$module_classnames,
			function ( $a, $b ) {
				$a_is_core = $a::is_core_module() ? 1 : 0;
				$b_is_core = $b::is_core_module() ? 1 : 0;
				return $b_is_core <=> $a_is_core;
			}
		);

		return array_values( $module_classnames );
	}

	/**
	 * Initializes the module classnames, core modules, and enabled modules.
	 * Module instances are created only for core and enabled modules.
	 *
	 * @return void
	 */
	public function __construct() {
		foreach ( $this->prepare_module_classnames() as $module_classname ) {
			if ( !$module_classname::is_active()){
				continue;
			}
			if ( is_a( $module_classname, ManagableModuleInterface::class, true ) ) {
				if ( ! self::is_manageable_module_enabled( $module_classname ) ) {
					continue;
				}
				$this->manageable_module_classnames[] = $module_classname;
				if ( self::is_module_enabled( $module_classname ) ) {
					$this->modules[ $module_classname::get_identifier() ] = $module_classname::instance();
				}
			} else {
				$this->modules[ $module_classname::get_identifier() ] = $module_classname::instance();
			}
		}
		add_action( 'wpbbe/init', array( $this, 'init' ) );
	}

	/**
	 * Initializes the modules by calling the `init` method on each module.
	 *
	 * @return void
	 */
	public function init() {
		// init core modules first
		foreach ( $this->modules as $module ) {
			$module->init();
		}
	}

	/**
	 * Sets up the hooks for the modules.
	 *
	 * @return void
	 */
	public function setup_hooks() {
		// for core modules first
		foreach ( $this->modules as $module ) {
			$module->setup_hooks();
		}
	}

	/**
	 * Retrieves data of manageable modules to be used later in the plugin settings (admin panel).
	 *
	 * @return array
	 */
	public function get_managable_modules_data() {
		$data = array();

		foreach ( $this->manageable_module_classnames as $classname ) {
			$data[] = array(
				'identifier'     => $classname::get_identifier(),
				'title'          => $classname::get_title(),
				'label'          => $classname::get_label(),
				'description'    => $classname::get_description(),
				'tab'            => $classname::get_tab(),
				'settings_order' => $classname::get_settings_order(),
				'enabled'        => self::is_module_enabled( $classname ),
				'is_freemium'    => !is_a( $classname, 'BbeProKit\Base\ModuleBasePro', true ),
				'classname'      => $classname,
				'active'         => isset( $this->modules[ $classname::get_identifier() ] ),
			);
		}

		return $data;
	}

	/**
	 * Checks if a module is enabled: tries to read the value from the options,
	 * if not set the default value of the module is used.
	 *
	 * @param string $module_classname the fully qualified module class name
	 * @return bool
	 */
	private static function is_module_enabled( $module_classname ) {
		$is_enabled = $module_classname::is_active();
		if ( $is_enabled ) {
			$is_enabled = Settings::is_module_enabled( $module_classname::get_identifier(), $module_classname::get_default_state() );
		}
		return apply_filters( 'wpbbe_is_module_enabled', $is_enabled, $module_classname );
	}

	/**
	 * Checks if a manageable module is enabled via filter.
	 * @param string $module_classname the fully qualified module class name
	 * @return bool
	 */
	private static function is_manageable_module_enabled( $module_classname ) {
		return apply_filters( 'wpbbe_is_manageable_module_enabled', true, $module_classname );
	}

	/**
	 * Retrieves the module instances.
	 *
	 * @param string|null $module_name The name of the module to retrieve.
	 *
	 * @return ModuleInterface[]|ModuleInterface|null
	 */
	public function get_modules( $module_name = null ) {
		if ( $module_name ) {
			if ( isset( $this->modules[ $module_name ] ) ) {
				return $this->modules[ $module_name ];
			}

			return null;
		}

		return $this->modules;
	}
}
