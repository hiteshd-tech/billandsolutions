<?php
/**
 * System status admin page.
 *
 * @package The7\Admin\Pages
 */

namespace The7\Admin\Pages;

defined( 'ABSPATH' ) || exit;

class Status extends Admin_Page {

	public function __construct() {
		$this->slug        = 'the7-status';
		$this->title       = __( 'System Status', 'the7mk2' );
		$this->capability  = 'switch_themes';
	}

	/**
	 * Render page content.
	 */
	public function render() {
		presscore_get_template_part( 'the7_admin', 'status' );
	}
}
