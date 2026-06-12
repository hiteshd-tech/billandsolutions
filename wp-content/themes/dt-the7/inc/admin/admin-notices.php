<?php
/**
 * Admin notices hooks.
 */

use The7\Admin\Notices\Cannot_Write_CSS;
use The7\Admin\Notices\Dev_Tool_Error;
use The7\Admin\Notices\Elementor_Unreliable_Experiments;
use The7\Admin\Notices\Hosting_Issue;
use The7\Admin\Notices\Import_Failed;
use The7\Admin\Notices\Import_Succeed;
use The7\Admin\Notices\Outdated_Core_Version;
use The7\Admin\Notices\Outdated_Elementor_Version;
use The7\Admin\Notices\Outdated_Wp_Version;
use The7\Admin\Notices\Registration_Deregistration_Success;
use The7\Admin\Notices\Registration;
use The7\Admin\Notices\Registration_Error;
use The7\Admin\Notices\Registration_Soft_Warning;
use The7\Admin\Notices\Registration_Invalid_Purchase_Code;
use The7\Admin\Notices\TGMPA_Notice;
use The7\Admin\Notices\Theme_Auto_Deactivation;
use The7\Admin\Notices\Theme_Update;
use The7\Admin\Notices\Theme_Updated;
use The7\Admin\Notices\Theme_Updating;
use The7\Admin\Notices\The7_Block_Editor_Obsolete;
use The7\Admin\Notices\Turn_On_Critical_Alerts;
use The7\Admin\Notices\Rate_BBE;
use The7\Admin\BBE_Rating_Banner_Check;

defined( 'ABSPATH' ) || exit;

BBE_Rating_Banner_Check::init();

/**
 * Add admin notices.
 *
 * @return void
 */
function the7_add_admin_notices() {
	the7_admin_notices()->add_object( new Rate_BBE() );
	the7_admin_notices()->add_object( new Registration() );
	the7_admin_notices()->add_object( new Registration_Soft_Warning() );
	the7_admin_notices()->add_object( new Theme_Auto_Deactivation() );
	the7_admin_notices()->add_object( new Elementor_Unreliable_Experiments() );
	the7_admin_notices()->add_object( new Hosting_Issue() );
	the7_admin_notices()->add_object( new Turn_On_Critical_Alerts() );
	the7_admin_notices()->add_object( new Theme_Update() );
	the7_admin_notices()->add_object( new Theme_Updating() );
	the7_admin_notices()->add_object( new Theme_Updated() );
	the7_admin_notices()->add_object( new The7_Block_Editor_Obsolete() );
	the7_admin_notices()->add_object( new Outdated_Wp_Version() );
	the7_admin_notices()->add_object( new Outdated_Core_Version() );
	the7_admin_notices()->add_object( new Outdated_Elementor_Version() );
	the7_admin_notices()->add_object( new Registration_Invalid_Purchase_Code() );
	the7_admin_notices()->add_object( new Registration_Error() );
	the7_admin_notices()->add_object( new Registration_Deregistration_Success() );
	the7_admin_notices()->add_object( new Cannot_Write_CSS() );
	the7_admin_notices()->add_object( new Dev_Tool_Error() );
	the7_admin_notices()->add_object( new Import_Succeed() );
	the7_admin_notices()->add_object( new Import_Failed() );
	the7_admin_notices()->add_object( new TGMPA_Notice() );
}

add_action( 'admin_notices', 'the7_add_admin_notices' );

/**
 * Enqueue admin notices scripts.
 */
function the7_admin_notices_scripts() {
	the7_register_script( 'the7-admin-notices', PRESSCORE_ADMIN_URI . '/assets/js/admin-notices', array( 'jquery' ), false, true );

	wp_enqueue_script( 'the7-admin-notices' );
	wp_localize_script( 'the7-admin-notices', 'the7Notices', array( '_ajax_nonce' => the7_admin_notices()->get_nonce() ) );
}

/**
 * Main function to handle custom admin notices. Adds action handlers.
 */
function the7_admin_notices_bootstrap() {
	$notices = the7_admin_notices();

	add_action( 'admin_enqueue_scripts', 'the7_admin_notices_scripts', 9999 );
	add_action( 'wp_ajax_the7-dismiss-admin-notice', array( $notices, 'dismiss_notices' ) );
	add_action( 'admin_notices', array( $notices, 'print_admin_notices' ), 40 );
}
add_action( 'admin_init', 'the7_admin_notices_bootstrap' );
