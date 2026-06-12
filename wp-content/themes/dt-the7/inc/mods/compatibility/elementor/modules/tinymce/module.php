<?php
/**
 * Elementor tinymce extension.
 *
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Modules\Tinymce;

use The7\Mods\Compatibility\Elementor\The7_Elementor_Module_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Elementor tinymce extension module.
 */
class Module extends The7_Elementor_Module_Base {

	/**
	 * @var string
	 */
	protected static $plugin_id = 'the7_elementor_elements';

	/**
	 * @var string
	 */
	protected static $button_id = 'the7_elements';

	/**
	 * Init module.
	 */
	public function __construct() {
		/**
		 * The elementor clears filters 'mce_buttons' and 'mce_external_plugins' after the elementor/editor/init so we need to add our buttons later.
		 */
		add_action( 'elementor/editor/init', [ __CLASS__, 'add_buttons_after_elementor_init' ] );
	}

	/**
	 * @return void
	 */
	public static function add_buttons_after_elementor_init() {
		add_action( 'print_default_editor_scripts', [ __CLASS__, 'add_buttons' ] );
	}

	/**
	 * @return void
	 */
	public static function add_buttons() {
		// Don't bother doing this stuff if the current user lacks permissions.
		if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
			return;
		}

		// Add only in Rich Editor mode.
		if ( get_user_option( 'rich_editing' ) === 'true' ) {
			add_filter( 'mce_external_plugins', [ __CLASS__, 'add_plugins' ] );
			add_filter( 'mce_buttons', [ __CLASS__, 'register_buttons' ] );
		}
	}

	/**
	 * @param array $buttons Buttons array.
	 *
	 * @return array
	 */
	public static function register_buttons( $buttons ) {
		$buttons[] = self::$button_id;

		return $buttons;
	}

	/**
	 * @param array $plugins_array Plugins array.
	 *
	 * @return array
	 */
	public static function add_plugins( $plugins_array ) {
		$plugins_array[ self::$plugin_id ] = THE7_ELEMENTOR_ADMIN_JS_URI . '/tiny-mce-plugin.js';

		return $plugins_array;
	}

	/**
	 * Get module name.
	 * Retrieve the module name.
	 *
	 * @access public
	 * @return string Module name.
	 */
	public function get_name() {
		return 'tinymce';
	}
}
