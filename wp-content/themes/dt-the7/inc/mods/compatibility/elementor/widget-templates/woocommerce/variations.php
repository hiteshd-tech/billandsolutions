<?php
/**
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widget_Templates\Woocommerce;

use The7\Mods\Compatibility\Elementor\Widget_Templates\Abstract_Template;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use WC_Product;
use stdClass;

defined( 'ABSPATH' ) || exit;

/**
 * Class Variations
 *
 * @package The7\Mods\Compatibility\Elementor\Widget_Templates
 */
class Variations extends Abstract_Template {

	/**
	 * Add variations style controls.
	 *
	 * @param array|null $conditions Conditions.
	 */
	public function add_style_controls( array $conditions = null ) {
		$this->widget->start_controls_section(
			'show_variations_style',
			[
				'label'      => esc_html__( 'Variations box', 'the7mk2' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => $conditions,
			]
		);

		$this->widget->add_responsive_control(
			'variations_row_space',
			[
				'label'     => esc_html__( 'Attributes gap', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'default'   => [
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}}  .products-variations-wrap' => 'grid-row-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->widget->add_control(
			'variations_box',
			[
				'label'     => esc_html__( 'Box', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->widget->add_responsive_control(
			'margin_variations',
			[
				'label'      => esc_html__( 'Margins', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'    => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}}' => '--variations-margin-top: {{TOP}}{{UNIT}}; --variations-margin-right: {{RIGHT}}{{UNIT}}; --variations-margin-bottom: {{BOTTOM}}{{UNIT}}; --variations-margin-left: {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .products-variations-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->widget->add_responsive_control(
			'padding_variations',
			[
				'label'      => esc_html__( 'Paddings', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'    => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .products-variations-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->widget->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'border_variations_width',
				'selector' => '{{WRAPPER}} .products-variations-wrap',
				'exclude'  => [ 'color' ],
			]
		);

		$this->widget->add_responsive_control(
			'menu_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .products-variations-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->widget->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'           => 'variations_bg_color',
				'types'          => [ 'classic', 'gradient' ],
				'exclude'        => [ 'image' ],
				'fields_options' => [
					'background' => [
						'label'     => esc_html__( 'Background', 'the7mk2' ),
						'selectors' => [
							'{{SELECTOR}}' => 'content: ""',
						],
					],
				],
				'selector'       => '{{WRAPPER}} .products-variations-wrap',
			]
		);

		$this->widget->add_control(
			'variations_border_color',
			[
				'label'     => esc_html__( 'Box Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .products-variations-wrap' => 'border-color: {{VALUE}}',
				],
				'condition' => [
					'border_variations_width_border!' => '',
				],
			]
		);

		$this->widget->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'variations_shadow',
				'label'    => esc_html__( 'Box Shadow', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .products-variations-wrap',
			]
		);

		$this->widget->end_controls_section();
		$this->widget->start_controls_section(
			'show_variations_label_style',
			[
				'label' => esc_html__( 'Variations label', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,

				'condition'            => [
					'variations_label' => 'y',
				],
			]
		);

		$this->widget->add_control(
			'variations_label_style',
			[
				'label'     => esc_html__( 'Label', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->widget->add_control(
			'variations_label_position',
			[
				'label'                => esc_html__( 'Label Position', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'toggle'               => false,
				'default'              => 'left',
				'options'              => [
					'left' => [
						'title' => esc_html__( 'Left', 'the7mk2' ),
						'icon'  => 'eicon-h-align-left',
					],
					'top'  => [
						'title' => esc_html__( 'Top', 'the7mk2' ),
						'icon'  => 'eicon-v-align-top',
					],
				],
				'prefix_class'         => 'label-position-',
				'selectors_dictionary' => [
					'left' => $this->widget->combine_to_css_vars_definition_string(
						[
							'variations-direction' => 'row',
							'variations-align'     => 'center',
							'variations-justify'   => 'var(--align-variation-items)',
							'label-justify'        => 'flex-start',
							'label-margin'         => 'var(--label-margin-top, 0px) var(--label-margin-right, 10px) var(--label-margin-bottom, 0px) var(--label-margin-left, 0px);',
						]
					),
					'top'  => $this->widget->combine_to_css_vars_definition_string(
						[
							'variations-direction' => 'column',
							'variations-align'     => 'var(--align-variation-items)',
							'variations-justify'   => 'center',
							'label-justify'        => 'var(--align-variation-items)',
							'label-margin'         => 'var(--label-margin-top, 0px) var(--label-margin-right, 0px) var(--label-margin-bottom, 10px) var(--label-margin-left, 0px);',
						]
					),
				],
				'selectors'            => [
					'{{WRAPPER}}' => '{{VALUE}}',
				],
			]
		);

		$this->widget->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'variations_label_typography',
				'selector'  => ' {{WRAPPER}} .product-variation-row > span',
			]
		);

		$this->widget->add_control(
			'variations_label_bg_color',
			[
				'label'     => esc_html__( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .product-variation-row > span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->widget->add_responsive_control(
			'variations_label_min_width',
			[
				'label'      => esc_html__( 'Min Width', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => '',
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 1,
						'max' => 500,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .product-variation-row > span' => 'min-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->widget->add_responsive_control(
			'variations_label_gap',
			[
				'label'      => esc_html__( 'Margins', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'    => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}}' => '--label-margin-top: {{TOP}}{{UNIT}}; --label-margin-right: {{RIGHT}}{{UNIT}}; --label-margin-bottom: {{BOTTOM}}{{UNIT}}; --label-margin-left: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->widget->end_controls_section();
		$this->widget->start_controls_section(
			'show_variations_default',
			[
				'label' => esc_html__( 'Default variation type', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->widget->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'variations_typography',
				'selector' => ' {{WRAPPER}} .products-variations li a',
			]
		);

		$this->widget->add_responsive_control(
			'variation_width',
			[
				'label'      => esc_html__( 'Min Width', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => '',
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 1,
						'max' => 500,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .products-variations li a' => 'min-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->widget->add_responsive_control(
			'variation_height',
			[
				'label'      => esc_html__( 'Min Height', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => '',
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 1,
						'max' => 500,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .products-variations li a' => 'min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->widget->add_responsive_control(
			'variations_column_space',
			[
				'label'     => esc_html__( 'Values gap', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .products-variations:not(.swatch-type-list)' => 'grid-column-gap: {{SIZE}}{{UNIT}}; grid-row-gap: {{SIZE}}{{UNIT}};',
				],
				'default'   => [
					'size' => 10,
				],
			]
		);

		$this->widget->add_responsive_control(
			'padding_variable_item',
			[
				'label'      => esc_html__( 'Paddings', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'    => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .products-variations li a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->widget->add_responsive_control(
			'border_variation_width',
			[
				'label'      => esc_html__( 'Border width', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default'    => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .products-variations-wrap li a' => 'border-style: solid; border-top-width: {{TOP}}{{UNIT}};
					border-right-width: {{RIGHT}}{{UNIT}}; border-bottom-width: {{BOTTOM}}{{UNIT}}; border-left-width:{{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->widget->add_responsive_control(
			'variation_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .products-variations-wrap li a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->widget->start_controls_tabs( 'tabs_variations_style' );
			$this->add_variations_tab_controls( 'normal_', esc_html__( 'Normal', 'the7mk2' ) );
			$this->add_variations_tab_controls( 'hover_', esc_html__( 'Hover', 'the7mk2' ) );
			$this->add_variations_tab_controls( 'active_', esc_html__( 'Selected', 'the7mk2' ) );
			$this->add_variations_tab_controls( 'of_stock_', esc_html__( 'Out', 'the7mk2' ) );
		$this->widget->end_controls_tabs();
		$this->widget->add_control(
			'out_of_stock_line_color',
			[
				'label'     => esc_html__( '"Out of stock" line', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--out-of-stock-line-color: {{VALUE}};',
				],
			]
		);

		$this->widget->end_controls_section();
	}

	/**
	 * @return void
	 */
	public function add_variation_type_controls() {
		$this->widget->add_control(
			'variation_type',
			[
				'label'        => esc_html__( 'Type', 'the7mk2' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'default',
				'options'      => [
					'default' => esc_html__( 'Default', 'the7mk2' ),
					'swatch'  => esc_html__( 'Swatch', 'the7mk2' ),
				],
				'description'  => esc_html__( 'Visible if variations are enabled', 'the7mk2' ),
				'render_type'  => 'template',
				'prefix_class' => 'variations-type-',
			]
		);
		$this->widget->add_control(
			'swatches_description',
			[
				'raw'             => sprintf(
					// translators: %s: link to attributes page.
					esc_html__( 'Enable %s styling for an attribute under Edit Attribute → Type → The7 swatches', 'the7mk2' ),
					'<a href="' . esc_url( admin_url( 'edit.php?post_type=product&page=product_attributes' ) ) . '" target="_blank">swatch</a>'
				),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			]
		);
	}

	/**
	 * Add swatch style controls.
	 */
	public function add_variation_swatch_styles_controls() {
		$this->widget->start_controls_section(
			'swatch_section',
			[
				'label'     => esc_html__( 'Swatch variation type', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'variation_type' => 'swatch',
				],
			]
		);

		$selector = '{{WRAPPER}} .products-variations a.isset-swatch';

		$this->widget->add_responsive_control(
			'swatch_width',
			[
				'label'      => esc_html__( 'Width', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors'  => [
					$selector => 'min-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->widget->add_responsive_control(
			'swatch_height',
			[
				'label'      => esc_html__( 'Height', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors'  => [
					$selector => ' min-height: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->widget->add_responsive_control(
			'swatch_column_space',
			[
				'label'     => esc_html__( 'Values gap', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .swatch-type-list' => 'grid-column-gap: {{SIZE}}{{UNIT}}; grid-row-gap: {{SIZE}}{{UNIT}};',
				],
				'default'   => [
					'size' => 10,
				],
			]
		);

		$this->widget->add_responsive_control(
			'swatch_padding',
			[
				'label'      => esc_html__( 'Padding', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					$selector => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);
		$this->widget->add_responsive_control(
			'swatch_border_width',
			[
				'label'      => esc_html__( 'Border Width', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'    => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					$selector  => 'border-style: solid; border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->widget->add_responsive_control(
			'swatch_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					$selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->widget->start_controls_tabs( 'swatch_tabs_style' );
		$this->add_swatches_tab_controls( 'normal_', esc_html__( 'Normal', 'the7mk2' ) );
		$this->add_swatches_tab_controls( 'hover_', esc_html__( 'Hover', 'the7mk2' ) );
		$this->add_swatches_tab_controls( 'active_', esc_html__( 'Selected', 'the7mk2' ) );
		$this->add_swatches_tab_controls( 'of_stock_', esc_html__( 'Out', 'the7mk2' ) );
		$this->widget->end_controls_tabs();
		$this->widget->add_control(
			'out_of_stock_swatch_line_color',
			[
				'label'     => esc_html__( '"Out of stock" line', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--out-of-stock-swatch-line-color: {{VALUE}};',
				],
			]
		);

		$this->widget->end_controls_section();
	}

	protected function add_swatches_tab_controls( $prefix_name, $box_name ) {
		$extra_class = '';
		if ( $prefix_name === 'active_' ) {
			$extra_class .= '.active';
		}

		$extra_link_class = '.isset-swatch';
		if ( $prefix_name === 'of_stock_' ) {
			$extra_link_class .= '.isset-swatch.out-of-stock';
		}

		$is_hover = '';
		if ( $prefix_name === 'hover_' ) {
			$is_hover = ':not(.out-of-stock):hover';
		}
		$selector = '{{WRAPPER}} .products-variations li' . $extra_class . ' a' . $extra_link_class . $is_hover;
		$pseudo_selector = '{{WRAPPER}} .products-variations.swatch-type-list li' . $extra_class . ' a' . $extra_link_class . $is_hover . ':after';

		$this->widget->start_controls_tab(
			$prefix_name . 'item_swatch_style',
			[
				'label' => $box_name,
			]
		);

		$this->widget->add_control(
			$prefix_name . 'swatch_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$selector => 'background-color: {{VALUE}};',
				],
			]
		);

		$item_count_border_color_selectors = [
			$selector => 'border-color: {{VALUE}};',
		];

		if ( ! in_array( $prefix_name, [ 'hover_', 'active_' ], true ) ) {
			$item_count_border_color_selectors['{{WRAPPER}} .products-variations'] = '--swatch-variations-border-color: {{VALUE}};';
		}

		$this->widget->add_control(
			$prefix_name . 'swatch_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => $item_count_border_color_selectors,
			]
		);
		$this->widget->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => $prefix_name . 'swatch_shadow',
				'label'    => esc_html__( 'Box Shadow', 'the7mk2' ),
				'selector' => $pseudo_selector,
			]
		);


		$this->widget->end_controls_tab();
	}

	/**
	 * @param string $prefix_name Prefix.
	 * @param string $box_name Tab label.
	 *
	 * @return void
	 */
	protected function add_variations_tab_controls( $prefix_name, $box_name ) {

		$css_prefix = 'li a';
		switch ( $prefix_name ) {
			case 'hover_':
				$css_prefix = 'li:not(.active) a:not(.out-of-stock):hover';
				break;
			case 'active_':
				$css_prefix = 'li.active a:not(.out-of-stock)';
				break;
			case 'of_stock_':
				$css_prefix = 'li a.out-of-stock';
				break;
		}
		$extra_class = '';
		if ( $prefix_name === 'active_' ) {
			$extra_class .= '.active';
		}

		$extra_link_class = '.isset-swatch';
		if ( $prefix_name === 'of_stock_' ) {
			$extra_link_class .= '.isset-swatch.out-of-stock';
		}

		$is_hover = '';
		if ( $prefix_name === 'hover_' ) {
			$is_hover = ':not(.out-of-stock):hover';
		}

		$selector         = '{{WRAPPER}} .products-variations ' . $css_prefix;
		$shadow_selector  = '{{WRAPPER}} .products-variations:not(.swatch-type-list) ' . $css_prefix;
		$shadow_selector .= ', {{WRAPPER}} .products-variations.swatch-type-list li' . $extra_class . ' a' . $extra_link_class . $is_hover . ':after';

		$this->widget->start_controls_tab(
			$prefix_name . 'item_count_style',
			[
				'label' => $box_name,
			]
		);

		$this->widget->add_control(
			$prefix_name . 'item_count_color',
			[
				'label'     => esc_html__( 'Text  Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					$selector => 'color: {{VALUE}};',
				],
			]
		);

		$this->widget->add_control(
			$prefix_name . 'item_count_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$selector => 'background-color: {{VALUE}};',
				],
			]
		);

		$item_count_border_color_selectors = [
			$selector => 'border-color: {{VALUE}};',
		];

		if ( ! in_array( $prefix_name, [ 'hover_', 'active_' ], true ) ) {
			$item_count_border_color_selectors['{{WRAPPER}} .products-variations'] = '--variations-border-color: {{VALUE}};';
		}

		$this->widget->add_control(
			$prefix_name . 'item_count_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => $item_count_border_color_selectors,
			]
		);

		$this->widget->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => $prefix_name . 'item_count_shadow',
				'selector' => $shadow_selector,
			]
		);

		$this->widget->end_controls_tab();
	}

	/**
	 * @param WC_Product $product WC product object.
	 */
	public function render( $product ) {
		if ( $product->get_type() !== 'variable' ) {
			return;
		}

		wp_enqueue_script( 'the7-woocommerce-product-variations' );

		// This setting can be missed in some cases.
		$is_variation_type_swatch = $this->get_settings( 'variation_type' ) === 'swatch';
		$product_attributes       = $product->get_attributes();

		/**
		 * @var \WC_Product_Variation[] $available_variations
		 */
		$available_variations     = $product->get_available_variations( 'objects' );
		$variations_per_attribute = [];
		foreach ( $available_variations as $variation ) {
			$variation_attributes = $variation->get_variation_attributes( false );
			foreach ( $variation_attributes as $attribute_slug => $variation_slug ) {
				$variations_per_attribute[ $attribute_slug ][ $variation_slug ] = $variation;
			}
		}
		$wrap_class = '';
		if ( $this->is_wc_ajax_add_to_cart_enabled() ) {
			$wrap_class = 'ajax-variation-enabled';
		}
		echo '<div class="products-variations-wrap ' . esc_attr( $wrap_class ) . ' ">';
		foreach ( $product_attributes as $attribute_slug => $attribute ) {

			/**
			 * @var \WC_Product_Attribute $attribute
			 */
			if ( ! isset( $variations_per_attribute[ $attribute_slug ] ) ) {
				continue;
			}

			$attribute_taxonomy = $attribute->get_taxonomy_object();
			if ( $attribute_taxonomy ) {
				// Get terms if this is a taxonomy - ordered. We need the names too.
				$terms_args  = [
					'fields' => 'all',
				];

				$attribute_variations = array_filter( array_keys( (array) $variations_per_attribute[ $attribute_slug ] ) );
				if ( $attribute_variations ) {
					$terms_args['slug'] = $attribute_variations;
				}

				$terms = wc_get_product_terms( $product->get_id(), $attribute->get_taxonomy(), $terms_args );

				if ( ! $terms ) {
					continue;
				}

				$attribute_label = $attribute_taxonomy->attribute_label;
			} else {
				$options = array_filter( array_keys( (array) $variations_per_attribute[ $attribute_slug ] ) );
				$terms   = [];
				foreach ( $options as $option ) {
					$term       = new stdClass();
					$term->name = ucfirst( $option );
					$term->slug = $option;
					$terms[]    = $term;
				}

				$attribute_label = $attribute->get_name();
			}

			$prefixed_attribute_slug = 'attribute_' . $attribute_slug;

			$swatch_wrap_class = '';
			$show_attribute_swatches = false;
			if ( $is_variation_type_swatch ) {
				$attribute_taxonomy = $attribute->get_taxonomy_object();
				if ( $attribute_taxonomy ) {
					$show_attribute_swatches = $attribute_taxonomy->attribute_type === 'the7_echanced';
				}
			}
			if ( $show_attribute_swatches ){
				$swatch_wrap_class = 'swatch-type-list';
			}

			echo '<div class="product-variation-row">';
			echo '<span>' . esc_html( $attribute_label ) . '</span>';
			echo '<ul class="products-variations ' . $swatch_wrap_class . '" data-atr="' . esc_attr( $prefixed_attribute_slug ) . '">';

			foreach ( $terms as $term ) {
				$class       = str_replace( ' ', '-', $prefixed_attribute_slug . '_' . $term->slug );
				$swatch_html = '';

				// Add swatch.
				if ( $show_attribute_swatches && isset( $term->term_id ) ) {
					$swatch_bg      = '';
					$the7_attr_type = get_term_meta( $term->term_id, 'the7_attribute_type', true ) ?: 'color';
					if ( $the7_attr_type === 'color' ) {
						$color = get_term_meta( $term->term_id, 'the7_attribute_type_color', true );
						if ( empty( $color ) ) {
							$class .= ' empty-swatch';
						} else {
							$swatch_bg = 'background-color:' . $color;
						}
					} elseif ( $the7_attr_type === 'image' ) {
						$image = get_term_meta( $term->term_id, 'the7_attribute_type_image', true );
						if ( isset( $image['id'], $image['url'] ) ) {
							$swatch_bg = 'background-image:url(' . $image['url'] . ')';
						} else {
							$class .= ' empty-swatch';
						}
					}
					// Update link class.
					$class .= ' isset-swatch';

					// Generate swatch html.
					$swatch_html  = '<span class="the7-variable-span the7-variable-span-color"' . ( $swatch_bg ? ' style="' . esc_attr( $swatch_bg ) . '"' : '' ) . '></span>';
					$swatch_html .= '<span class="filter-popup">' . esc_html( $term->name ) . '</span>';
				}

				$href = add_query_arg( [ $prefixed_attribute_slug => $term->slug ], $product->get_permalink() );

				echo '<li>';
				echo '<a href="' . esc_url( $href ) . '" data-id="' . esc_attr( $term->slug ) . '" class="' . esc_attr( $class ) . '">';
				echo esc_html( $term->name );
				echo $swatch_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo '</a>';
				echo '</li>';
			}

			echo '</ul>';
			echo '</div>';
		}

		echo '</div>';
	}

	/**
	 * @param  WC_Product $product
	 * @param string     $element
	 *
	 * @return void
	 */
	public function add_data_attributes_to_element( WC_Product $product, $element ) {
		$available_variations = $product->get_available_variations();


		$available_variations = array_map(
			function ( $variation ) {
				$fields_white_list = [
					'attributes',
					'variation_id',
					'image',
					'is_in_stock',
					'sku',
					'price_html',
				];

				return array_intersect_key( $variation, array_flip( $fields_white_list ) );
			},
			$available_variations
		);

		$this->widget->add_render_attribute( $element, 'data-product_variations', wp_json_encode( $available_variations ), true );

		$default_attributes = $product->get_default_attributes();
		if ( $default_attributes ) {
			$prefixed_default_attributes = [];
			foreach ( $default_attributes as $attr => $val ) {
				$prefixed_default_attributes[ 'attribute_' . $attr ] = $val;
			}
			$this->widget->add_render_attribute( $element, 'data-default_attributes', wp_json_encode( $prefixed_default_attributes ), true );
		}
	}

	/**
	 * @param string $element
	 *
	 * @return void
	 */
	public function remove_data_attributes_from_element( $element ) {
		$this->widget->remove_render_attribute( $element, 'data-product_variations' );
		$this->widget->remove_render_attribute( $element, 'data-default_attributes' );
	}

	/**
	 * @return bool
	 */
	protected function is_wc_ajax_add_to_cart_enabled() {
		return get_option( 'woocommerce_enable_ajax_add_to_cart' ) === 'yes';
	}
}
