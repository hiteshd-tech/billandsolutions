<?php
/**
 * @package The7
 */

namespace The7\Mods\Bundled_Content;

defined( 'ABSPATH' ) || exit;

/**
 * Abstract_Bundled_Plugin class.
 */
abstract class Abstract_Bundled_Plugin {

	/**
	 * @param string $plugin_slug Plugins slug.
	 *
	 * @return bool
	 */
	public function is_bundled_plugin( $plugin_slug ) {
		global $the7_tgmpa;

		if ( ! $the7_tgmpa && class_exists( '\Presscore_Modules_TGMPAModule' ) ) {
			\Presscore_Modules_TGMPAModule::init_the7_tgmpa();
			\Presscore_Modules_TGMPAModule::register_plugins_action();
		}

		if ( empty( $the7_tgmpa->plugins ) ) {
			\Presscore_Modules_TGMPAModule::register_plugins_action();
		}

		return $the7_tgmpa->is_the7_plugin( $plugin_slug );
	}

	/**
	 * @return void
	 */
	abstract public function activate_plugin();

	/**
	 * @return void
	 */
	abstract public function deactivate_plugin();

	/**
	 * @return bool
	 */
	abstract protected function is_activated_plugin();

	/**
	 * @return string|false
	 */
	abstract protected function get_bundled_plugin_code();

	/**
	 * @return bool
	 */
	public function is_active() {
		return false;
	}

	/**
	 * @return bool
	 */
	public function is_activated_by_theme() {
		$bundled_plugin_code = $this->get_bundled_plugin_code();
		if ( empty( $bundled_plugin_code ) ) {
			return false;
		}

		$theme_code = get_site_option( 'the7_purchase_code', '' );
		if ( empty( $theme_code ) ) {
			return true;
		}

		return ( $theme_code === $bundled_plugin_code );
	}
}
