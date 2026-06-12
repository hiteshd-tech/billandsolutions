<?php
/**
 * Add responsive settings to Row / Stack
 *
 * @package BetterBlockEditor
 */

namespace BetterBlockEditor\Modules\RowResponsive;

use BetterBlockEditor\Base\ManagableModuleInterface;
use BetterBlockEditor\Base\ResponsiveBlockModuleBase;
use BetterBlockEditor\Core\BlockUtils;
use BetterBlockEditor\Core\ResponsiveBlockUtils;

defined( 'ABSPATH' ) || exit;

final class Module extends ResponsiveBlockModuleBase implements ManagableModuleInterface {

	const MODULE_IDENTIFIER = 'row-responsive';
	const ASSETS_BUILD_PATH = 'editor/blocks/row/responsiveness/';

	const SETTINGS_ORDER = 200;

	const BLOCK_NAME = 'core/group';

	public static function get_title() {
		return __( 'Responsive Rows', 'better-block-editor' );
	}

	public static function get_label() {
		return __( 'Add Responsive Settings to Row block.', 'better-block-editor' );
	}

	protected function need_to_apply_changes( $block_content, $block, $wp_block_instance ) {
		// "row" mode can be detected by used layout
		return ( $this->attributes['layout']['type'] ?? null ) === 'flex';
	}

	protected function render( $block_content, $block, $wp_block_instance ) {
		$class_id      = BlockUtils::get_unique_class_id( $block_content );
		$block_content = BlockUtils::append_classes( $block_content, $class_id );
		$this->add_styles( $class_id );

		return $block_content;
	}

	private function add_styles( $class_id ) {
		$justification           = ResponsiveBlockUtils::get_setting( $this->attributes, 'justification', 'left' );
		$orientation             = ResponsiveBlockUtils::get_setting( $this->attributes, 'orientation', 'row' );
		$vertical_alignment      = ResponsiveBlockUtils::get_setting( $this->attributes, 'verticalAlignment', 'top' );
		$gap                     = ResponsiveBlockUtils::get_setting( $this->attributes, 'gap', null );
		$disable_position_sticky = ResponsiveBlockUtils::get_setting( $this->attributes, 'disablePositionSticky', false );

		$vertical_alignment_map = array(
			'top'           => 'flex-start',
			'bottom'        => 'flex-end',
			'center'        => 'center',
			'stretch'       => 'stretch',
			'space-between' => 'space-between',
		);

		$vertical_alignment_reverse_map = array_merge(
			$vertical_alignment_map,
			array(
				'top'    => 'flex-end',
				'bottom' => 'flex-start',
			)
		);

		$declarations = array();

		if ( $orientation === 'row' || $orientation === 'row-reverse' ) {
			// horizontal orientation
			$horizontal_alignment_property = 'justify-content';
			$vertical_alignment_property   = 'align-items';
		} else {
			// vertical orientation
			$horizontal_alignment_property = 'align-items';
			$vertical_alignment_property   = 'justify-content';
		}

		$horizontal_alignment_value = BlockUtils::get_horizontal_alignment_by_attribute(
			$justification,
			$orientation === 'row-reverse'
		);

		if ( $orientation === 'column-reverse' ) {
			$vertical_alignment_map = $vertical_alignment_reverse_map;
		}

		$declarations[ $horizontal_alignment_property ] = $horizontal_alignment_value . ' !important';
		$declarations[ $vertical_alignment_property ]   = $vertical_alignment_map[ $vertical_alignment ] . ' !important';

		$declarations['flex-direction'] = "{$orientation}";

		// disabling position sticky
		// on FE we use static as default (as it's done in WP core)
		if ( $disable_position_sticky ) {
			$declarations['position'] = 'static';
		}

		// add gap if provided
		if ( $gap !== null ) {
			$declarations['gap'] = $gap . ' !important';
		}

		$css_rules = array(
			array(
				'selector'     => "@media screen and (width <= {$this->switch_width})",
				'declarations' => array(
					array(
						'selector'     => "body .{$class_id}.{$class_id}",
						'declarations' => $declarations,
					),

				),
			),
		);

		// when we switch orientation direction in responsive mode
		// remove provided flex-basis value from direct children (FSE-3)
		// by default group flex orientation is horizontal (even if it's not set in attributes)
		$layout_orientation     = $attributes['layout']['orientation'] ?? 'horizontal';
		$responsive_orientation = in_array( $orientation, array( 'row', 'row-reverse' ) ) ? 'horizontal' : 'vertical';
		if ( $layout_orientation !== $responsive_orientation ) {
			$remove_flex_basis_css = array(
				'selector'     => "body .{$class_id}.{$class_id} > *",
				'declarations' => array(
					'flex-basis' => 'auto !important',
				),
			);

			array_push( $css_rules[0]['declarations'], $remove_flex_basis_css );
		}

		BlockUtils::add_styles_from_css_rules( $css_rules );
	}
}
