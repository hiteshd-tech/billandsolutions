<?php
/**
 * Admin module.
 *
 * @package BbeProKit
 */

namespace BbeProKit\Modules\Admin;

use BbeProKit\Base\ModuleBasePro;

defined( 'ABSPATH' ) || exit;

class Module extends ModuleBasePro {
	const MODULE_IDENTIFIER = 'admin';
	const IS_CORE_MODULE = true;

	public function setup_hooks() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	function enqueue_scripts() {
		$module_id = self::get_identifier();
		$this->register_assets( $module_id );
		$this->enqueue_assets( $module_id );
		wp_localize_script( $this->build_script_handle( $module_id ), 'admin', apply_filters('bbe_pro_kit_admin_filter', array()) );
	}

}
