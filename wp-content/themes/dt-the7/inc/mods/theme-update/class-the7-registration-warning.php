<?php
/**
 * @package The7
 */

/**
 * Class The7_Registration_Warning
 */
class The7_Registration_Warning {

    const OPTION_WARNING = 'the7_registered_domains_count';

	public static function add_admin_notices() {
		// Notice is now registered via the7_add_admin_notices() in inc/admin/admin-notices.php
	}


	public static function dismiss_admin_notices() {
		delete_site_option( self::OPTION_WARNING );
		the7_admin_notices()->reset( 'the7_registration_soft_warning' );
	}

	public static function get_domains_count() {
		return (int) get_site_option( self::OPTION_WARNING );
	}

	public static function setup_registration_warning( $response ) {
		$data = isset( $response['data'] ) ? $response['data'] : array();
		if ( isset( $data['domains_count'] ) ) {
			update_site_option( self::OPTION_WARNING, (int) $data['domains_count'] );
		} else {
		    self::dismiss_admin_notices();
		}
	}
}
