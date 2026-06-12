<?php
/**
 * Horizontal menu decoration migration.
 *
 * @package The7
 */

namespace The7\Mods\Theme_Update\Migrations\v10_12_0;

use The7\Mods\Compatibility\Elementor\Upgrade\Widget_Migration;

defined( 'ABSPATH' ) || exit;

/**
 * Horizontal_Menu_Decoration_Migration class.
 */
class Horizontal_Menu_Decoration_Migration extends Widget_Migration {

	/**
	 * @return string
	 */
	public static function get_widget_name() {
		return 'the7_horizontal-menu';
	}

	/**
	 * Apply migration.
	 */
	public function do_apply() {
		$direction = $this->get( 'decoration_direction' );

		if ( ! $direction || $direction === 'center' ) {
			$this->remove( 'decoration_direction' );
			$this->add( 'decoration_align', 'center' );
		}
	}
}
