<?php
/**
 * Migrate image aspect ratio settings in product widgets.
 *
 * @package The7
 */

namespace The7\Mods\Theme_Update\Migrations\v11_5_0;

use The7\Mods\Compatibility\Elementor\Upgrade\Widget_Migration;

defined( 'ABSPATH' ) || exit;

/**
 * Product_Widgets_Image_Aspect_Ratio_Migration class.
 */
class Product_Widgets_Image_Aspect_Ratio_Migration extends Widget_Migration {

	/**
	 * Apply migration.
	 *
	 * @return void
	 */
	public function do_apply() {
		// Widget has item_preserve_ratio == 'y' by default.
		if ( in_array( $this->get( 'item_preserve_ratio' ), [ 'y', null ], true ) ) {
			foreach ( self::get_responsive_devices() as $device ) {
				$this->remove( 'item_ratio' . $device );
			}
			$this->set(
				'item_ratio',
				[
					'size'  => '',
					'unit'  => 'px',
					'sizes' => [],
				]
			);
		}

		if ( $this->get_subkey( 'item_ratio', 'size' ) === null ) {
			// It was 0.66 by default (h/w). Need to migrate it to w/h form.
			$this->add(
				'item_ratio',
				[
					'size'  => $this->migrate_aspect_ratio( 0.66 ),
					'unit'  => 'px',
					'sizes' => [],
				]
			);
		} else {
			foreach ( self::get_responsive_devices() as $device ) {
				$key      = 'item_ratio' . $device;
				$size_val = $this->get_subkey( $key, 'size' );
				if ( $size_val ) {
					$this->set_subkey( $key, 'size', $this->migrate_aspect_ratio( $size_val ) );
				}
			}
		}

		// Cleanup.
		$this->remove( 'item_preserve_ratio' );
	}

	/**
	 * @param float $val
	 *
	 * @return float
	 */
	protected function migrate_aspect_ratio( $val ) {
		return round( 1 / $val, 2 );
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
			[ 'the7-woocommerce-product-navigation' ],
		];
	}
}
