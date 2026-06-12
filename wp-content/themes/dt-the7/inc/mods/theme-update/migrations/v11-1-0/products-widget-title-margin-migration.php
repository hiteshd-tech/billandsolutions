<?php
/**
 * Widget title margin migration. Applicable for prouct fiters and caegory widgets.
 *
 * @package The7
 */

namespace The7\Mods\Theme_Update\Migrations\v11_1_0;

use The7\Mods\Compatibility\Elementor\Upgrade\Widget_Migration;

defined( 'ABSPATH' ) || exit;

/**
 * Products_Widget_Title_Margin_Migration class.
 */
class Products_Widget_Title_Margin_Migration extends Widget_Migration {

	/**
	 * Apply migration.
	 */
	public function do_apply() {
		if ( $this->get_current_widget_name() === 'the7_product-categories' ) {
			$this->migrate_to_container_top_margin( 'widget_title_bottom_margin', 20 );
		} else {
			foreach ( self::get_responsive_devices() as $device ) {
				$this->migrate_to_container_top_margin( 'title_space', null, $device );
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
			[ 'the7-woocommerce-filter-active' ],
			[ 'the7-woocommerce-filter-attribute' ],
			[ 'the7-woocommerce-filter-price' ],
			[ 'the7_product-categories' ],
		];
	}

	/**
	 * @param string          $setting_name Setting name.
	 * @param string|int|null $default $default value.
	 * @param string          $device       Device.
	 *
	 * @return void
	 */
	protected function migrate_to_container_top_margin( $setting_name, $default = null, $device = '' ) {
		$title_space = $setting_name . $device;
		if ( $this->exists( $title_space ) ) {
			$value = (string) $this->get_subkey( $title_space, 'size' );
			$this->add_container_top_margin( $value, $device );
			$this->remove( $title_space );
		} elseif ( isset( $default ) ) {
			$this->add_container_top_margin( $default, $device );
		}
	}

	/**
	 * @param string $top_margin Value.
	 * @param string $device     Device.
	 *
	 * @return void
	 */
	protected function add_container_top_margin( $top_margin, $device ) {
		$this->add(
			'container_margin' . $device,
			[
				'top'      => (string) $top_margin,
				'right'    => '0',
				'bottom'   => '0',
				'left'     => '0',
				'unit'     => 'px',
				'isLinked' => false,
			]
		);
	}
}
