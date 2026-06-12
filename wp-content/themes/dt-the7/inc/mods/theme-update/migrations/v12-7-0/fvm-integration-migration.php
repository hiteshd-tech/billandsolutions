<?php
/**
 * Turn off FVM integration if it's not explicitly on.
 *
 * @package The7
 */

namespace The7\Mods\Theme_Update\Migrations\v12_7_0;

defined( 'ABSPATH' ) || exit;

class Fvm_Integration_Migration extends \The7_DB_Patch {

	/**
	 * Main method. Apply all migrations.
	 */
	protected function do_apply() {
		// Backward compatibility with 0 default.
		if ( ! $this->option_exists( 'advanced-fvm_enable_integration' ) ) {
			$this->set_option( 'advanced-fvm_enable_integration', 0 );
		}
	}
}
