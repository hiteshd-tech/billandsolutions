<?php
/**
 * Migrate FSE version from site transient to local option.
 *
 * @package The7
 */

namespace The7\Mods\Theme_Update\Migrations\v12_5_0;

use The7\Mods\Compatibility\Gutenberg\Block_Theme\The7_Block_Theme_Compatibility;

defined( 'ABSPATH' ) || exit;

/**
 * Fse_Version_Migration class.
 */
class Fse_Version_Migration {

	/**
	 * @return void
	 */
	public static function migrate() {
		if ( ! the7_is_gutenberg_theme_mode_active() ) {
			return;
		}

		$version = get_site_transient( The7_Block_Theme_Compatibility::FSE_VERSION_OPTION );

		if ( $version ) {
			The7_Block_Theme_Compatibility::instance()->set_fse_version( $version );
		}
	}
}
