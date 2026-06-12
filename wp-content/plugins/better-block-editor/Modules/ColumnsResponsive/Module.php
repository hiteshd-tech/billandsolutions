<?php
/**
 * Adds responsive settings to Columns block.
 *
 * @package BetterBlockEditor
 */

namespace BetterBlockEditor\Modules\ColumnsResponsive;

use BetterBlockEditor\Base\ModuleBase;
use BetterBlockEditor\Base\ManagableModuleInterface;
use BetterBlockEditor\Core\BlockUtils;
use BetterBlockEditor\Core\CssMediaBreakpoints;

defined( 'ABSPATH' ) || exit;

class Module extends ModuleBase implements ManagableModuleInterface {

	const MODULE_IDENTIFIER = 'columns-responsive';
	const ASSETS_BUILD_PATH = 'editor/blocks/columns/responsiveness/';

	const SETTINGS_ORDER = 400;

	const ATTRIBUTES                        = 'wpbbeResponsive';
	const ATTRIBUTE_BREAKPOINT              = 'breakpoint';
	const ATTRIBUTE_BREAKPOINT_CUSTOM_VALUE = 'breakpointCustomValue';
	const ATTRIBUTE_REVERSE_ORDER           = 'reverseOrder';

	const COLUMNS_BlOCK_NAME = 'core/columns';
	const COLUMN_BlOCK_NAME  = 'core/column';

	public function setup_hooks() {
		add_filter( 'render_block', array( $this, 'render_columns' ), 20, 3 );
		add_filter( 'render_block', array( $this, 'render_column' ), 20, 3 );
	}

	/**
	 * @param string $block_content The block frontend output.
	 * @param array  $block         The block info and attributes.
	 *
	 * @return mixed                Return $block_content
	 */
	function render_column( $block_content, $block ) {
		if ( ( $block['blockName'] ?? null ) !== self::COLUMN_BlOCK_NAME || $block_content === '' ) {
			return $block_content;
		}

		$attributes = $block['attrs'] ?? array();

		if ( ! isset( $attributes['width'] ) ) {
			return $block_content;
		}

		$class_id      = BlockUtils::get_unique_class_id( $block_content );
		$block_content = BlockUtils::append_classes( $block_content, array( $class_id ) );

		$column_selector = ".wp-block-columns:not(.is-not-stacked-on-mobile) > .wp-block-column.{$class_id}[style*=flex-basis]";

		BlockUtils::add_styles_from_css_rules(
			array(
				array(
					'selector'     => $column_selector,
					'declarations' => array(
						'flex-basis' => $attributes['width'] . ' !important',
					),
				),
			)
		);

		return $block_content;
	}

	/**
	 * @param string $block_content The block frontend output.
	 * @param array  $block         The block info and attributes.
	 *
	 * @return mixed                Return $block_content
	 */
	function render_columns( $block_content, $block ) {
		if ( ( $block['blockName'] ?? null ) !== self::COLUMNS_BlOCK_NAME || $block_content === '' ) {
			return $block_content;
		}

		$attributes = $block['attrs'] ?? array();

		$class_id      = BlockUtils::get_unique_class_id( $block_content );
		$block_content = BlockUtils::append_classes( $block_content, array( $class_id ) );

		// native attribute isStackedOnMobile is changed together with our the custom attribute
		if ( ! ( $attributes['isStackedOnMobile'] ?? true ) ) {
			// add is-not-stacked-on-mobile class as stacking is turned off
			$block_content = BlockUtils::append_classes( $block_content, array( 'is-not-stacked-on-mobile' ) );
		} else {
			$this->add_styles_to_columns( $attributes, $class_id );
		}

		return $block_content;
	}

	/**
	 * in case we just turned module on and there is no settings in self::ATTRIBUTES
	 * we use value based on isStackedOnMobile attribute
	 *
	 * @param array $attributes
	 */
	private function get_responsive_configuration( $attributes ) {
		if ( isset( $attributes[ self::ATTRIBUTES ] ) ) {
			return $attributes[ self::ATTRIBUTES ];
		}

		// if isStackedOnMobile not set it equals true (see core/columns block.json default value)
		return array(
			self::ATTRIBUTE_BREAKPOINT              => ( $attributes['isStackedOnMobile'] ?? true )
				? CssMediaBreakpoints::BREAKPOINT_NAME_MOBILE
				: CssMediaBreakpoints::BREAKPOINT_NAME_OFF,

			self::ATTRIBUTE_BREAKPOINT_CUSTOM_VALUE => null,

			'settings'                              => array(
				self::ATTRIBUTE_REVERSE_ORDER => false,
			),
		);
	}

	/**
	 * see logic explanations in corresponding editor.js
	 */
	function add_styles_to_columns( $attributes, $class_id ) {
		$responsive_config = $this->get_responsive_configuration( $attributes );

		$switch_width = CssMediaBreakpoints::getSwitchWidth(
			$responsive_config[ self::ATTRIBUTE_BREAKPOINT ],
			$responsive_config[ self::ATTRIBUTE_BREAKPOINT_CUSTOM_VALUE ] ?? null
		);

		// prevent the columns from being stacked when custom breakpoint is empty
		// such stacking is caused by core/columns css rules
		$switch_width = $switch_width ?: '0px';

		$reverse_order = $responsive_config['settings'][ self::ATTRIBUTE_REVERSE_ORDER ] ?? false;

		$colums_block_selector         = ".wp-block-columns.{$class_id}.{$class_id}";
		$colums_block_stacked_selector = "$colums_block_selector:not(.is-not-stacked-on-mobile)";

		BlockUtils::add_styles_from_css_rules(
			array(
				array(
					'selector'     => $colums_block_selector,
					'declarations' => array( 'flex-wrap' => 'nowrap !important;' ),
				),

				array(
					'selector'     => "@media screen and (width <= {$switch_width})",
					'declarations' => array(
						array(
							'selector'     => "$colums_block_stacked_selector",
							'declarations' => array(

								'flex-direction' => $reverse_order ? 'column-reverse' : 'column',
								'align-items'    => 'stretch !important',
							),
						),

						array(
							'selector'     => "$colums_block_stacked_selector > .wp-block-column.wp-block-column",
							'declarations' => array(
								'flex-basis' => 'auto !important',
								'width'      => 'auto',
								'flex-grow'  => '1',
								'align-self' => 'auto !important',
							),
						),

					),
				),
				array(
					'selector'     => "@media screen and (width > {$switch_width})",
					'declarations' => array(
						array(
							'selector'     => "$colums_block_stacked_selector > .wp-block-column:not([style*=flex-basis])",
							'declarations' => array(
								'flex-basis' => '0 !important',
								'flex-grow'  => '1',
							),
						),
						array(
							'selector'     => "$colums_block_stacked_selector > .wp-block-column[style*=flex-basis]",
							'declarations' => array(
								'flex-grow' => '0',
							),
						),
					),
				),
			)
		);
	}

	public static function get_title() {
		return __( 'Responsive Columns', 'better-block-editor' );
	}

	public static function get_label() {
		return __( 'Add Responsive Settings to Columns block.', 'better-block-editor' );
	}
}
