<?php
/**
 * Adopt new variations presentation with product and carousel widgets.
 *
 * @package The7
 */

namespace The7\Mods\Theme_Update\Migrations\v11_4_0;

use The7\Mods\Compatibility\Elementor\Upgrade\Widget_Migration;

defined( 'ABSPATH' ) || exit;

/**
 * Products_Variation_Migration class.
 */
class Products_Variation_Migration extends Widget_Migration {

	/**
	 * Apply migration.
	 */
	public function do_apply() {
		if ( $this->get( 'layout' ) === 'btn_on_img' ) {
			foreach ( self::get_responsive_devices() as $device ) {
				$this->force_rename( 'icon_on_image_icon_size' . $device, 'button_icon_size' . $device );

				$icon_on_image_background_size = $this->get_subkey( 'icon_on_image_background_size' . $device, 'size' );
				if ( $icon_on_image_background_size ) {
					$this->set( 'button_min_width' . $device, $icon_on_image_background_size );
					$this->set( 'button_min_height' . $device, $icon_on_image_background_size );
				}

				$product_icon_h_offset = $this->get_subkey( 'product_icon_h_offset' . $device, 'size' );
				$product_icon_v_offset = $this->get_subkey( 'product_icon_v_offset' . $device, 'size' );

				if ( $product_icon_h_offset === null ) {
					$product_icon_h_offset = 10;
				}

				if ( $product_icon_v_offset === null ) {
					$product_icon_v_offset = 10;
				}

				if ( $product_icon_v_offset || $product_icon_h_offset ) {
					$this->set(
						'add_to_cart_margin' . $device,
						[
							'top'      => (string) $product_icon_v_offset,
							'right'    => (string) $product_icon_h_offset,
							'bottom'   => (string) $product_icon_v_offset,
							'left'     => (string) $product_icon_h_offset,
							'unit'     => 'px',
							'isLinked' => ( $product_icon_v_offset === $product_icon_h_offset ),
						]
					);
				}
			}

			$icon_on_image_border_radius = $this->get_subkey( 'icon_on_image_border_radius', 'size' );
			if ( in_array( $icon_on_image_border_radius, [ null, '' ], true ) ) {
				$icon_on_image_border_radius = 100;
			}

			$this->set(
				'button_border_radius',
				[
					'top'      => (string) $icon_on_image_border_radius,
					'right'    => (string) $icon_on_image_border_radius,
					'bottom'   => (string) $icon_on_image_border_radius,
					'left'     => (string) $icon_on_image_border_radius,
					'unit'     => 'px',
					'isLinked' => true,
				]
			);

			$this->force_rename( 'icon_on_image_icon_color', 'button_icon_color' );
			$this->force_rename( 'icon_on_image_icon_hover_color', 'button_hover_icon_color' );
			$this->force_rename( 'icon_on_image_text_color', 'button_text_color' );
			$this->force_copy( 'button_text_color', 'button_hover_color' );

			if ( $this->force_rename( 'icon_on_image_background_color', 'button_background_color' ) ) {
				$this->set( 'button_background_background', 'classic' );
			}

			if ( $this->force_rename( 'icon_on_image_background_hover_color', 'button_background_hover_color' ) ) {
				$this->set( 'button_background_hover_background', 'classic' );
			}

			$this->remove_typography( 'button_typography' );
			$this->rename_typography( 'icon_on_image_text_typography', 'button_typography' );

			// Icon on image was always turned on. By default it is 'y', so just remove it.
			$this->remove( 'show_add_to_cart' );
		} else {
			foreach ( self::get_responsive_devices() as $device ) {
				$gap_above_button = $this->get_subkey( 'gap_above_button' . $device, 'size' );
				if ( $gap_above_button ) {
					$this->set(
						'add_to_cart_margin' . $device,
						[
							'top'      => (string) $gap_above_button,
							'right'    => '0',
							'bottom'   => '0',
							'left'     => '0',
							'unit'     => 'px',
							'isLinked' => false,
						]
					);
				}
			}

			if ( ! $this->exists( 'button_size' ) ) {
				$this->add( 'button_size', 'xs' );
			}

			$this->copy( 'post_content_alignment', 'add_to_cart_align' );
		}

		$layout = $this->get( 'layout' );
		if ( $layout === 'btn_on_img' ) {
			if ( $this->get( 'product_icon_visibility' ) === 'on-hover' ) {
				$button_position = 'on_image_hover';
			} else {
				$button_position = 'on_image';
			}

			if ( ! $this->exists( 'expand_product_icon_on_hover' ) || $this->get( 'expand_product_icon_on_hover' ) === 'y' ) {
				$this->set( 'layout', 'icon_with_text' );
			}
		} else {
			$button_position = 'below_image';
		}

		$this->add( 'button_position', $button_position );

		foreach ( self::get_responsive_devices() as $device ) {
			$this->remove( 'icon_on_image_icon_size' . $device );
			$this->remove( 'icon_on_image_background_size' . $device );
			$this->remove( 'product_icon_visibility' . $device );
			$this->remove( 'product_icon_h_offset' . $device );
			$this->remove( 'product_icon_v_offset' . $device );
			$this->remove( 'gap_above_button' . $device );
		}

		$this->remove( 'icon_on_image_border_radius' );
		$this->remove( 'icon_on_image_text_color' );
		$this->remove( 'icon_on_image_icon_color' );
		$this->remove( 'icon_on_image_icon_hover_color' );
		$this->remove( 'icon_on_image_background_color' );
		$this->remove( 'icon_on_image_background_hover_color' );
		$this->remove( 'expand_product_icon_on_hover' );
		$this->remove_typography( 'icon_on_image_text_typography' );
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
		];
	}
}
