<?php
/**
 * Update changes in variations responsiveness.
 *
 * @package The7
 */

namespace The7\Mods\Theme_Update\Migrations\v11_5_0;

use The7\Mods\Compatibility\Elementor\Upgrade\Widget_Migration;

defined( 'ABSPATH' ) || exit;

/**
 * Products_Variation_Responsiveness_Migration class.
 */
class Products_Variation_Responsiveness_Migration extends Widget_Migration {

	/**
	 * Apply migration.
	 */
	public function do_apply() {
		if ( $this->get( 'show_variations' ) === '' ) {
			$this->remove( 'show_variations' );
		}

		if ( $this->get( 'show_add_to_cart' ) === '' ) {
			$this->set( 'show_add_to_cart', 'n' );
		}

		if ( $this->get( 'variations_position' ) === 'on_image_hover' ) {
			$this->set( 'variations_position', 'on_image' );
			$this->add( 'show_variations_on_hover', 'on-hover' );
		}

		if ( $this->get( 'button_position' ) === 'on_image_hover' ) {
			$this->set( 'button_position', 'on_image' );
			$this->add( 'show_btn_on_hover', 'on-hover' );
		}
	}

	/**
	 * List of widgets to apply migration.
	 *
	 * @return \string[][]
	 */
	public static function get_callback_args_array() {
		return [
			[ 'the7-wc-products' ],
			[ 'the7-wc-products-carousel' ],
		];
	}
}
