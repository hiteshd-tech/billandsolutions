<?php
/**
 * Settings admin page.
 *
 * @package The7\Admin\Pages
 */

namespace The7\Admin\Pages;

defined( 'ABSPATH' ) || exit;

class Settings extends Admin_Page {

	public function __construct() {
		$this->slug        = 'the7-settings';
		$this->title       = __( 'Settings', 'the7mk2' );
		$this->capability  = 'install_plugins';
	}

	/**
	 * Render page content.
	 */
	public function render() {
		presscore_get_template_part( 'the7_admin', 'settings' );
	}
}
