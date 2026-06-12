<?php
/**
 * @package The7
 */

namespace The7\Mods\Bundled_Content;

defined( 'ABSPATH' ) || exit;

/**
 * WPB class.
 */
class WPB extends Abstract_Bundled_Plugin {

	/**
	 * @var string
	 */
	protected static $field_prefix = 'wpb_js_';

	/**
	 * @return void
	 */
	public function activate_plugin() {
		if ( $this->is_activated_plugin() ) {
			return;
		}

		if ( ! defined( 'JS_COMPOSER_THE7' ) && ! $this->is_bundled_plugin( 'js_composer' ) ) {
			$this->deactivate_plugin();
			return;
		}

		$this->disable_composer_notification();

		if ( $this->is_activated_by_theme() ) {
			add_filter( 'vc_page-welcome-slugs-list', [ $this, 'the7_vc_page_welcome_slugs_list' ], 30 );
			return;
		}

		update_site_option( self::$field_prefix . 'the7_js_composer_purchase_code', presscore_get_purchase_code() );
	}

	/**
	 * @return void
	 */
	public function deactivate_plugin() {
		$this->disable_composer_notification();

		if ( $this->is_activated_by_theme() ) {
			update_site_option( self::$field_prefix . 'the7_js_composer_purchase_code', '' );
		}
	}

	/**
	 * @return bool
	 */
	public function is_activated_plugin() {
		return (bool) get_site_option( self::$field_prefix . 'js_composer_purchase_code' );
	}

	/**
	 * @return bool
	 */
	public function is_active() {
		return the7_wpb_is_active();
	}

	/**
	 * @return string|false
	 */
	public function get_bundled_plugin_code() {
		return get_site_option( self::$field_prefix . 'the7_js_composer_purchase_code', '' );
	}

	/**
	 * @return void
	 */
	private function disable_composer_notification() {
		if ( ! function_exists( 'vc_manager' ) ) {
			return;
		}

		if ( version_compare( WPB_VC_VERSION, '5.5.4', '>' ) ) {
			return;
		}

		if ( ! $this->is_activated_plugin() && $this->is_activated_by_theme() ) {
			// Disable updater.
			vc_manager()->disableUpdater();
		}
	}

	/**
	 * @param array $dashboard_array Dashboard elements.
	 *
	 * @return array
	 */
	public function the7_vc_page_welcome_slugs_list( $dashboard_array ) {
		foreach ( [ 'vc-resources' ] as $element ) {
			unset( $dashboard_array[ $element ] );
		}

		return $dashboard_array;
	}

}
