<?php
/**
 * Plugins admin page.
 *
 * @package The7\Admin\Pages
 */

namespace The7\Admin\Pages;

defined( 'ABSPATH' ) || exit;

class Plugins extends Admin_Page {

	public function __construct() {
		$this->slug        = 'the7-plugins';
		$this->title       = __( 'Plugins', 'the7mk2' );
		$this->capability  = 'install_plugins';
	}

	/**
	 * Render page content.
	 */
	public function render() {
		presscore_get_template_part( 'the7_admin', 'plugins' );
	}

	protected function on_after_add_menu_page( $hook_suffix ) {
		// Plugins.
		if ( class_exists( \Presscore_Modules_TGMPAModule::class ) ) {
			\Presscore_Modules_TGMPAModule::setup_hooks( $hook_suffix );
		}

		parent::on_after_add_menu_page( $hook_suffix );
	}
}
