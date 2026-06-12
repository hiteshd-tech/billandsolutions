<?php
/**
 * Removes default value for `arrows_tablet` and `arrows_mobile`.
 *
 * @package The7
 */

namespace The7\Mods\Theme_Update\Migrations\v10_8_0;

use The7\Mods\Compatibility\Elementor\Upgrade\Widget_Migration;
use The7\Mods\Theme_Update\Migrations\v10_7_0\Arrows_Visibility_Migration as Arrows_Visibility_Migration_10_7_0;

defined( 'ABSPATH' ) || exit;

/**
 * Arrows_Visibility_Migration class.
 */
class Arrows_Visibility_Migration extends Arrows_Visibility_Migration_10_7_0 {

	/**
	 * Default widget migration logic here.
	 *
	 * @see Widget_Migration::migrate()
	 */
	public function do_apply() {
		$remove_default = [
			'arrows_tablet',
			'arrows_mobile',
		];
		foreach ( $remove_default as $key ) {
			if ( $this->get( $key ) === 'default' ) {
				$this->remove( $key );
			}
		}
	}

	/**
	 * List of widgets to apply migration.
	 *
	 * @return \string[][]
	 */
	public static function get_callback_args_array() {
		return [
			[ 'the7_content_carousel' ],
			[ 'the7_testimonials_carousel' ],
		];
	}

}
