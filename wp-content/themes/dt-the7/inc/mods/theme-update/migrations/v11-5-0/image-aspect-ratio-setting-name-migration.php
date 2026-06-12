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
 * Image_Aspect_Ratio_Setting_Name_Migration class.
 */
class Image_Aspect_Ratio_Setting_Name_Migration extends Widget_Migration {

	/**
	 * Apply migration.
	 */
	public function do_apply() {
		$this->copy( 'image_ratio', 'item_ratio' );
		foreach ( self::get_responsive_devices() as $device ) {
			$this->remove( 'image_ratio' . $device );
		}
	}

	/**
	 * List of widgets to apply migration.
	 *
	 * @return \string[][]
	 */
	public static function get_callback_args_array() {
		return [
			[ 'the7_image_box_grid_widget' ],
			[ 'the7_image_box_widget' ],
			[ 'the7_content_carousel' ],
			[ 'the7_testimonials_carousel' ],
		];
	}
}
