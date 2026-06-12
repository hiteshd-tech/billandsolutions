<?php
/**
 * Migrate bullets vertical offset in carousel widgets.
 *
 * @package The7
 */

namespace The7\Mods\Theme_Update\Migrations\v11_6_0_1;

use The7\Mods\Compatibility\Elementor\Upgrade\Widget_Migration;

defined( 'ABSPATH' ) || exit;

/**
 * Bullets_Vertical_Offset_Migration class.
 */
class Bullets_Vertical_Offset_Migration extends Widget_Migration {

	/**
	 * Apply migration.
	 */
	public function do_apply() {
		$default_base_font_size   = [
			'size' => 16,
			'unit' => 'px',
		];
		$default_base_line_height = [
			'size' => 1.5,
			'unit' => 'em',
		];
		$base_font_size           = null;
		$base_line_height         = null;

		$active_kit          = \Elementor\Plugin::$instance->kits_manager->get_active_kit();
		$base_typography_key = 'the7-base-typography';
		$kit_globals         = $active_kit->get_settings( '__globals__' );
		if ( is_array( $kit_globals ) && array_key_exists( $base_typography_key, $kit_globals ) ) {
			$base_typography  = $this->get_global_value( $kit_globals[ $base_typography_key ] );
			$base_line_height = isset( $base_typography['typography_line_height'] ) ? $base_typography['typography_line_height'] : [];
			$base_font_size   = isset( $base_typography['typography_font_size'] ) ? $base_typography['typography_font_size'] : [];
		} elseif ( $active_kit->get_settings_for_display( $base_typography_key . '_typography' ) === 'custom' ) {
			$base_line_height = $active_kit->get_settings_for_display( 'the7-base-typography_line_height' );
			$base_font_size   = $active_kit->get_settings_for_display( 'the7-base-typography_font_size' );
		}

		if ( empty( $base_line_height['size'] ) ) {
			$base_line_height = $default_base_line_height;
		}
		if ( empty( $base_font_size['size'] ) || ! isset( $base_font_size['unit'] ) || $base_font_size['unit'] !== 'px' ) {
			$base_font_size = $default_base_font_size;
		}

		$base_line_height_size = $base_line_height['size'];
		if ( $base_line_height['unit'] === 'em' ) {
			$base_line_height_size = $this->em_to_px( $base_line_height_size, $base_font_size['size'] );
		}
		$vertical_offset = $this->get_subkey( 'bullets_v_offset', 'size' );
		if ( in_array( $vertical_offset, [ null, '' ], true ) ) {
			$vertical_offset = 0;
		}

		$bullet_size = $this->get_subkey( 'bullet_size', 'size' );
		if ( in_array( $bullet_size, [ null, '' ], true ) ) {
			$bullet_size = 10;
		}

		if ( $base_line_height_size > $bullet_size ) {
			$vertical_offset += round( ( $base_line_height_size - $bullet_size ) / 2 );
			$this->set(
				'bullets_v_offset',
				[
					'size'  => $vertical_offset,
					'unit'  => 'px',
					'sizes' => [],
				]
			);
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

	/**
	 * @param float|int $em em value.
	 * @param int       $px px value.
	 *
	 * @return int
	 */
	protected function em_to_px( $em, $px ) {
		return round( $em * $px );
	}
}
