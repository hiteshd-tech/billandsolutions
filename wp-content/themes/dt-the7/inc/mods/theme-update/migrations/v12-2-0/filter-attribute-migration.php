<?php
/**
 * Migrate Filter Attribute widget.
 *
 * @package The7
 */

namespace The7\Mods\Theme_Update\Migrations\v12_2_0;

use The7\Mods\Compatibility\Elementor\Upgrade\Widget_Migration;

defined( 'ABSPATH' ) || exit;

/**
 * Filter_Attribute_Migration class.
 */
class Filter_Attribute_Migration extends Widget_Migration {

	/**
	 * Apply migration.
	 *
	 * @see \The7\Mods\Compatibility\Elementor\Widgets\Woocommerce\Filter_Attribute
	 */
	public function do_apply() {
		if ( $this->get( 'layout' ) === 'inline' ) {
			$this->set( 'active_filter_indicator_icon_show', '' );

			// Run this for each device.
			foreach ( self::get_responsive_devices() as $device ) {
				$this->migrate_box_margin( $device );
			}
		}

		$box_border_width = $this->get( 'box_border_width' );
		if ( is_array( $box_border_width ) && isset( $box_border_width['top'], $box_border_width['right'], $box_border_width['bottom'], $box_border_width['left'] ) ) {
			$border_is_not_epty = $box_border_width['top'] || $box_border_width['right'] || $box_border_width['bottom'] || $box_border_width['left'];
			if ( $border_is_not_epty ) {
				$this->add( 'box_border_border', 'solid' );
			}
		}

		$this->add( 'filter_indicator_border_border', 'solid' );

		$filter_indicator_border_width = $this->get_subkey( 'filter_indicator_border_width', 'size' );

		if ( $filter_indicator_border_width !== null && $this->get_subkey( 'filter_indicator_border_width', 'top' ) === null ) {
			$filter_indicator_border_width = (string) $filter_indicator_border_width;
			$this->set(
				'filter_indicator_border_width',
				[
					'top'      => $filter_indicator_border_width,
					'right'    => $filter_indicator_border_width,
					'bottom'   => $filter_indicator_border_width,
					'left'     => $filter_indicator_border_width,
					'unit'     => 'px',
					'isLinked' => true,
				]
			);
		}

		// Active colors.
		$this->copy( 'active_filter_indicator_background_color', 'normal_filter_indicator_active_background_color' );
		$this->copy( 'active_filter_indicator_icon_color', 'normal_filter_indicator_active_icon_color' );
		$this->copy( 'active_filter_indicator_border_color', 'normal_filter_indicator_active_border_color' );

		$normal_icon_color = (string) $this->get( 'normal_filter_indicator_hover_icon_color' );
		if ( strlen( $normal_icon_color ) === 9 && substr( $normal_icon_color, -2 ) === '00' ) {
			$this->force_copy( 'active_filter_indicator_icon_color', 'normal_filter_indicator_hover_icon_color' );
			$this->force_copy( 'active_filter_indicator_background_color', 'normal_filter_indicator_hover_background_color' );
			$this->force_copy( 'active_filter_indicator_border_color', 'normal_filter_indicator_hover_border_color' );
		}
	}

	/**
	 * List of widgets to apply migration.
	 *
	 * @return \string[][]
	 */
	public static function get_callback_args_array() {
		return [
			[ 'the7-woocommerce-filter-attribute' ],
		];
	}

	/**
	 * Migrate box margin.
	 *
	 * @param string $device Device.
	 * @return void
	 */
	protected function migrate_box_margin( $device ) {
		$box_margin = $this->get( 'box_margin' . $device );

		// Skip if box_margin is not set or is in percentage.
		if ( ! is_array( $box_margin ) || ( isset( $box_margin['unit'] ) && $box_margin['unit'] === '%' ) ) {
			return;
		}

		if ( isset( $box_margin['top'], $box_margin['bottom'] ) ) {
			$this->add(
				'box_row_space' . $device,
				[
					'size'  => ( (int) $box_margin['top'] + (int) $box_margin['bottom'] ),
					'unit'  => 'px',
					'sizes' => [],
				]
			);
		}

		if ( isset( $box_margin['right'], $box_margin['left'] ) ) {
			$this->add(
				'box_column_space' . $device,
				[
					'size'  => ( (int) $box_margin['right'] + (int) $box_margin['left'] ),
					'unit'  => 'px',
					'sizes' => [],
				]
			);
		}
	}
}
