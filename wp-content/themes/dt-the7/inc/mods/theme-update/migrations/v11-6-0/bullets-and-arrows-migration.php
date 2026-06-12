<?php
/**
 * Migrate bullets and arrows settings in carousel widgets.
 *
 * @package The7
 */

namespace The7\Mods\Theme_Update\Migrations\v11_6_0;

use The7\Mods\Compatibility\Elementor\Upgrade\Widget_Migration;

defined( 'ABSPATH' ) || exit;

/**
 * Bullets_And_Arrows_Migration class.
 */
class Bullets_And_Arrows_Migration extends Widget_Migration {

	/**
	 * Apply migration.
	 */
	public function do_apply() {
		$this->rename( 'arrow_icon_color', 'normal_arrow_icon_color' );
		$this->rename( 'arrow_border_color', 'normal_arrow_border_color' );
		$this->rename( 'arrow_bg_color', 'normal_arrow_bg_color' );
		$this->rename( 'arrow_icon_color_hover', 'hover_arrow_icon_color' );
		$this->rename( 'arrow_border_color_hover', 'hover_arrow_border_color' );
		$this->rename( 'arrow_bg_color_hover', 'hover_arrow_bg_color' );

		$prefixes = [
			'l_' => 'prev_',
			'r_' => 'next_',
		];
		foreach ( $prefixes as $old_prefix => $new_prefix ) {
			foreach ( self::get_responsive_devices() as $device ) {
				$this->rename( $old_prefix . 'arrow_v_position' . $device, $new_prefix . 'arrow_v_position' . $device );
				$this->rename( $old_prefix . 'arrow_h_position' . $device, $new_prefix . 'arrow_h_position' . $device );
				$this->rename( $old_prefix . 'arrow_v_offset' . $device, $new_prefix . 'arrow_v_offset' . $device );
				$this->rename( $old_prefix . 'arrow_h_offset' . $device, $new_prefix . 'arrow_h_offset' . $device );
			}
		}

		$arrows_offsets = [
			'prev_arrow_h_offset',
			'prev_arrow_v_offset',
			'next_arrow_h_offset',
			'next_arrow_v_offset',
		];
		foreach ( $arrows_offsets as $offset ) {
			if ( $this->get_subkey( $offset, 'size' ) === '' ) {
				$this->set_subkey( $offset, 'size', 0 );
			}
		}

		foreach ( self::get_responsive_devices() as $device ) {
			$show_bullets = $this->get( 'show_bullets' . $device );
			if ( $show_bullets ) {
				$this->set( 'show_bullets' . $device, $show_bullets === 'n' ? 'hide' : 'show' );
			}
		}

		$changed_defaults = [
			'arrow_icon_size'     => [
				'size'  => 16,
				'unit'  => 'px',
				'sizes' => [],
			],
			'arrow_bg_width'      => [
				'size'  => 30,
				'unit'  => 'px',
				'sizes' => [],
			],
			'arrow_bg_height'     => [
				'size'  => 30,
				'unit'  => 'px',
				'sizes' => [],
			],
			'arrow_border_radius' => [
				'size'  => 500,
				'unit'  => 'px',
				'sizes' => [],
			],
			'arrow_border_width'  => [
				'size'  => 2,
				'unit'  => 'px',
				'sizes' => [],
			],
			'prev_arrow_h_offset' => [
				'size'  => -15,
				'unit'  => 'px',
				'sizes' => [],
			],
			'next_arrow_h_offset' => [
				'size'  => -15,
				'unit'  => 'px',
				'sizes' => [],
			],
		];
		foreach ( $changed_defaults as $setting => $default ) {
			if ( ! $this->exists( $setting ) ) {
				$this->set( $setting, $default );
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
			[ 'the7-elements-simple-posts-carousel' ],
			[ 'the7-elements-woo-simple-products-carousel' ],
			[ 'the7-simple-product-categories-carousel' ],
			[ 'the7-wc-products-carousel' ],
			[ 'the7_content_carousel' ],
			[ 'the7_elements_carousel' ],
			[ 'the7_testimonials_carousel' ],
		];
	}
}
