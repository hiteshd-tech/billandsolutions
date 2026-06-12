<?php
/**
 * @package The7
 */

namespace The7\Mods\Theme_Update\Migrations\v09_15_1;

use The7\Mods\Compatibility\Elementor\Upgrade\Widget_Migration;

defined( 'ABSPATH' ) || exit;

/**
 * Class Carousel_Widget_Width_Migration
 *
 * @package The7\Mods\Theme_Update\Migrations\v09_15_1
 */
class Carousel_Widget_Width_Migration extends Widget_Migration {

	/**
	 * Apply migration.
	 */
	public function do_apply() {
		foreach ( self::DEVICES as $device ) {
			$width = $this->get( 'carousel_width' . $device );
			$this->remove( 'carousel_width' . $device );

			if ( ! isset( $width['size'], $width['unit'] ) ) {
				continue;
			}

			if ( (int) $width['size'] === 100 && $width['unit'] === '%' ) {
				continue;
			}

			$this->add( '_element_width' . $device, 'initial' );
			$this->add( '_element_custom_width' . $device, $width );
		}
	}
}
