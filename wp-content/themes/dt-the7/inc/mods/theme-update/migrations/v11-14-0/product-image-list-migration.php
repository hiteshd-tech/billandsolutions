<?php
/**
 * Migrate video autoplay and columns gap controls.
 *
 * @package The7
 */

namespace The7\Mods\Theme_Update\Migrations\v11_14_0;

use The7\Mods\Compatibility\Elementor\Upgrade\Widget_Migration;

defined( 'ABSPATH' ) || exit;

/**
 * Product_Image_List_Migration class.
 */
class Product_Image_List_Migration extends Widget_Migration {

	/**
	 * Apply migration.
	 */
	public function do_apply() {
		$this->rename( 'autoplay', 'video_autoplay' );
		foreach ( static::get_responsive_devices() as $device ) {
			$this->rename( "columns_gap{$device}", "slides_gap{$device}" );
		}
	}

	/**
	 * List of widgets to apply migration.
	 *
	 * @return \string[][]
	 */
	public static function get_callback_args_array() {
		return [
			[ 'the7-woocommerce-product-images-list' ],
		];
	}
}
