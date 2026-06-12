<?php
/**
 * Search border radius migration.
 *
 * @package The7
 */

namespace The7\Mods\Theme_Update\Migrations\v10_13_1;

use The7\Mods\Compatibility\Elementor\Upgrade\Widget_Migration;

defined( 'ABSPATH' ) || exit;

/**
 * Search_Border_Radius_Migration class.
 */
class Search_Border_Radius_Migration extends Widget_Migration {

	/**
	 * @return string
	 */
	public static function get_widget_name() {
		return 'the7-search-form-widget';
	}

	/**
	 * Apply migration.
	 */
	public function do_apply() {
		foreach ( static::get_responsive_devices() as $device ) {
			$setting_key = 'border_radius' . $device;

			if ( ! $this->exists( $setting_key ) ) {
				continue;
			}

			$border_radius_size = $this->get_subkey( $setting_key, 'size' );
			if ( $border_radius_size !== null ) {
				$this->set(
					$setting_key,
					[
						'unit'     => $this->get_subkey( $setting_key, 'unit' ) ?: 'px',
						'top'      => (string) $border_radius_size,
						'bottom'   => (string) $border_radius_size,
						'left'     => (string) $border_radius_size,
						'right'    => (string) $border_radius_size,
						'isLinked' => true,
					]
				);
			}
		}
	}
}
