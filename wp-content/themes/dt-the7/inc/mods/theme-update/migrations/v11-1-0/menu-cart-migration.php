<?php
/**
 * Menu cart widget settings migration.
 *
 * @package The7
 */

namespace The7\Mods\Theme_Update\Migrations\v11_1_0;

use The7\Mods\Compatibility\Elementor\Upgrade\Widget_Migration;

defined( 'ABSPATH' ) || exit;

/**
 * Menu_Cart_Migration class.
 */
class Menu_Cart_Migration extends Widget_Migration {

	/**
	 * @return string
	 */
	public static function get_widget_name() {
		return 'the7-woocommerce-menu-cart';
	}

	/**
	 * Apply migration.
	 */
	public function do_apply() {
		$this->copy( 'indicator_size', 'indicator_typography_font_size' );
		$this->remove( 'indicator_size' );
		$this->remove( 'indicator_size_tablet' );
		$this->remove( 'indicator_size_mobile' );
	}
}
