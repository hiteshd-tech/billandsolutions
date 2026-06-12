<?php
/**
 * Rename legacy dt-cr options to wpbbe equivalents.
 *
 * @package The7
 */

namespace The7\Mods\Theme_Update\Migrations\v12_10_0;

use The7\Mods\Theme_Update\Base\Migration_Base;

defined( 'ABSPATH' ) || exit;

final class Options_Migration extends Migration_Base {

	public static function migrate(): bool {
		foreach ( wp_load_alloptions() as $name => $value ) {
			if ( strpos( $name, 'dt-cr__' ) !== 0 ) {
				continue;
			}

			$new_name = 'better-block-editor__' . substr( $name, 7 );
			update_option( $new_name, maybe_unserialize( $value ) );
			delete_option( $name );
		}

		return false;
	}
}
