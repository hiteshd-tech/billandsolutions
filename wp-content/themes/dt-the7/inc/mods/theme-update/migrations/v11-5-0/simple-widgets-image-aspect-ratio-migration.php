<?php
/**
 * Migrate image aspect ratio settings in simple widgets.
 *
 * @package The7
 */

namespace The7\Mods\Theme_Update\Migrations\v11_5_0;

use The7\Mods\Compatibility\Elementor\Upgrade\Widget_Migration;

defined( 'ABSPATH' ) || exit;

/**
 * Simple_Widgets_Image_Aspect_Ratio_Migration class.
 */
class Simple_Widgets_Image_Aspect_Ratio_Migration extends Widget_Migration {

	/**
	 * Apply migration.
	 *
	 * @return void
	 */
	public function do_apply() {
		// Simple widget has item_preserve_ratio == '' by default.
		if ( $this->get( 'item_preserve_ratio' ) === 'y' ) {
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
			// It was 1 by default.
			$this->add(
				'item_ratio',
				[
					'size'  => 1,
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
			[ 'the7-elements-simple-posts' ],
			[ 'the7-elements-simple-posts-carousel' ],
			[ 'the7-elements-woo-simple-products' ],
			[ 'the7-elements-woo-simple-products-carousel' ],
			[ 'the7-elements-simple-product-categories' ],
			[ 'the7-simple-product-categories-carousel' ],
			[ 'the7-woocommerce-cart-preview' ],
		];
	}
}
