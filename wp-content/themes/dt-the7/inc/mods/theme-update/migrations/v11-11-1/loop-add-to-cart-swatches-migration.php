<?php
/**
 * Migrate swatches column space controls.
 *
 * @package The7
 */

namespace The7\Mods\Theme_Update\Migrations\v11_11_1;

use The7\Mods\Compatibility\Elementor\Upgrade\Widget_Migration;

defined( 'ABSPATH' ) || exit;

/**
 * Loop_Add_To_Cart_Swatches_Migration class.
 */
class Loop_Add_To_Cart_Swatches_Migration extends Widget_Migration {

	/**
	 * Apply migration.
	 */
	public function do_apply() {
		$this->copy( 'variations_column_space', 'swatch_column_space' );
	}

	/**
	 * List of widgets to apply migration.
	 *
	 * @return \string[][]
	 */
	public static function get_callback_args_array() {
		return [
			[ 'the7-woocommerce-loop-add-to-cart' ],
			[ 'the7-wc-products' ],
			[ 'the7-wc-products-carousel' ],
		];
	}
}
