<?php
/**
 * Migrate Product Image Slider position.
 *
 * @package The7
 */

namespace The7\Mods\Theme_Update\Migrations\v12_4_0;

use The7\Mods\Compatibility\Elementor\Upgrade\Widget_Migration;

defined( 'ABSPATH' ) || exit;

/**
 * Product_Image_Slider_Position_Migration class.
 */
class Product_Image_Slider_Position_Migration extends Widget_Migration {

	/**
	 * Apply migration.
	 *
	 * @see \The7\Mods\Compatibility\Elementor\Widgets\Woocommerce\Filter_Attribute
	 */
	public function do_apply() {
		foreach ( static::get_responsive_devices() as $device ) {
			$this->migrate_position( $device );
			$this->migrate_margins( $device );
		}
	}

	/**
	 * List of widgets to apply migration.
	 *
	 * @return \string[][]
	 */
	public static function get_callback_args_array() {
		return [
			[ 'the7-woocommerce-product-images-slider' ],
		];
	}

	/**
	 * @param string $device Device name.
	 *
	 * @return void
	 */
	protected function migrate_margins( string $device ) {
		$v_key    = "thumbs_v_offset{$device}";
		$h_key    = "thumbs_h_offset{$device}";
		$v_offset = $this->get_subkey( $v_key, 'size' );
		$h_offset = $this->get_subkey( $h_key, 'size' );

		if ( $v_offset === null && $h_offset === null ) {
			return;
		}

		$v_offset = (int) $v_offset;
		$h_offset = (int) $h_offset;

		$thumbs_direction = $this->get( "thumbs_direction{$device}" ) ?: 'vertical';

		if ( $thumbs_direction === 'horizontal' ) {
			$h_alignment         = $this->get( "thumbs_h_alignment{$device}" ) ?: 'left';
			$left_margin         = $h_alignment === 'right' ? -1 * $h_offset : $h_offset;
			$thumbs_margin_value = [
				'top'    => (string) $v_offset,
				'right'  => '0',
				'bottom' => (string) $v_offset,
				'left'   => (string) $left_margin,
			];
		} else {
			$thumbs_margin_value = [
				'top'    => (string) $v_offset,
				'right'  => (string) $h_offset,
				'bottom' => (string) $v_offset,
				'left'   => (string) $h_offset,
			];
		}

		$thumbs_margin_value['unit']     = 'px';
		$thumbs_margin_value['isLinked'] = false;

		$this->add(
			"thumbs_margin{$device}",
			$thumbs_margin_value
		);
		$this->remove( $v_key );
		$this->remove( $h_key );
	}

	/**
	 * @param string $device Device name.
	 *
	 * @return void
	 */
	protected function migrate_position( string $device ) {
			$v_alignment_key = "thumbs_v_alignment{$device}";
			$h_alignment_key = "thumbs_h_alignment{$device}";

			// Skip migration if alignment settings already exist
			if ( $this->get( $v_alignment_key ) !== null || $this->get( $h_alignment_key ) !== null ) {
				return;
			}

			$v_pos_key  = "thumbs_v_position{$device}";
			$h_pos_key  = "thumbs_h_position{$device}";
			$v_position = $this->get( $v_pos_key );
			$h_position = $this->get( $h_pos_key );

			if ( $v_position === null && $h_position === null ) {
				return;
			}

			$v_position = $v_position ?? 'bottom';
			$h_position = $h_position ?? 'left';

			$this->set( $h_pos_key, $v_position === 'center' ? 'bottom' : $v_position );
			$this->set( $v_pos_key, $h_position === 'left' ? 'top' : 'bottom' );

			if ( $v_position === 'top' ) {
				$this->add( $v_alignment_key, 'left' );
			} elseif ( $v_position === 'bottom' ) {
				$this->add( $v_alignment_key, 'right' );
			} elseif ( $v_position === 'center' ) {
				$this->add( $v_alignment_key, 'center' );
			}
			$this->add( $h_alignment_key, $h_position );
	}
}
