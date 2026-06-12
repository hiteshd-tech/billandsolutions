<?php
/**
 * Migrate Add to Cart border
 *
 * @package The7
 */

namespace The7\Mods\Theme_Update\Migrations\v12_3_0;

use The7\Mods\Compatibility\Elementor\Upgrade\Widget_Migration;

defined( 'ABSPATH' ) || exit;

/**
 * Add_To_Cart_Border_Migration class.
 */
class Add_To_Cart_Border_Migration extends Widget_Migration {

	/**
	 * Apply migration.
	 *
	 * @see \The7\Mods\Compatibility\Elementor\Widgets\Woocommerce\Filter_Attribute
	 */
	public function do_apply() {
		$slider_to_dimensions = [
			'variations_border_width',
			'swatch_border_width',
		];
		foreach ( $slider_to_dimensions as $control_id ) {
			$border_width = $this->get_subkey( $control_id, 'size' );
			if ( $border_width !== null ) {
				$border_width = (string) $border_width;
				$this->set(
					$control_id,
					[
						'top'      => $border_width,
						'right'    => $border_width,
						'bottom'   => $border_width,
						'left'     => $border_width,
						'unit'     => 'px',
						'isLinked' => true,
					]
				);
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
			[ 'the7-woocommerce-product-add-to-cart-v2' ],
		];
	}
}
