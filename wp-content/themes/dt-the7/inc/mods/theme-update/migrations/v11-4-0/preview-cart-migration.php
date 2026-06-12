<?php
/**
 * Preview cart height migration.
 *
 * @package The7
 */

namespace The7\Mods\Theme_Update\Migrations\v11_4_0;

use The7\Mods\Compatibility\Elementor\Upgrade\Widget_Migration;

defined( 'ABSPATH' ) || exit;

/**
 * Preview_Cart_Migration class.
 */
class Preview_Cart_Migration extends Widget_Migration {

	/**
	 * @return string
	 */
	public static function get_widget_name() {
		return 'the7-woocommerce-cart-preview';
	}

	/**
	 * Apply migration.
	 */
	public function do_apply() {
		$position   = $this->get( 'footer_position' );
		$height_sel = $this->get( 'widget_height_select' );
		if ( $height_sel === 'custom' && in_array( $position, [ null, 'bottom', 'stick_bottom' ], true ) ) {
			$this->copy( 'stick_to_bottom_widget_height', 'widget_min_height' );
			$this->copy( 'stick_to_bottom_widget_height', 'widget_max_height' );
		}

		foreach ( static::get_responsive_devices() as $device ) {
			$this->remove( 'stick_to_bottom_widget_height' . $device );
		}
		$this->remove( 'footer_position' );
		$this->remove( 'widget_height_select' );
	}
}
