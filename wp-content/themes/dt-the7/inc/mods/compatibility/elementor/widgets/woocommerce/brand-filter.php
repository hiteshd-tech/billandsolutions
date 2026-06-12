<?php
/*
 * The7 elements product add to cart widget for Elementor.
 *
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widgets\Woocommerce;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Plugin;
use The7\Mods\Compatibility\Elementor\Pro\Modules\Woocommerce\WC_Widget_Nav;
use The7\Mods\Compatibility\Elementor\Pro\Modules\Woocommerce\Woocommerce_Support;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;
use WC_Query;

defined( 'ABSPATH' ) || exit;

class Brand_Filter extends The7_Elementor_Widget_Base {

	public function get_name() {
		return 'the7-woocommerce-brand-filter';
	}

	public function get_categories() {
		return [ 'woocommerce-elements-single', 'woocommerce-elements-archive' ];
	}

	public function get_style_depends() {
		return $this->getDepends();
	}

	private function getDepends() {
		// css and js use the same names
		$ret = [ 'the7-woocommerce-filter-brand', 'the7-custom-scrollbar' ];
		if ( ! Plugin::$instance->preview->is_preview_mode() ) {
			$settings = $this->get_settings_for_display();
			if ( $settings['navigation'] !== 'scroll' ) {
				unset( $ret['the7-custom-scrollbar'] );
			}
		}

		return $ret;
	}

	public function get_script_depends() {
		return $this->getDepends();
	}

	protected function the7_title() {
		return esc_html__( 'Filter by Brand', 'the7mk2' );
	}

	protected function the7_icon() {
		return 'eicon-table-of-contents';
	}

	protected function the7_keywords() {
		return [ 'woocommerce', 'shop', 'store', 'cart', 'product', 'filter', 'brand' ];
	}

	protected function register_controls() {
		// Content Tab.
		$this->add_title_area_content_controls();
		$this->add_attributes_content_controls();

		// styles tab
		$this->add_title_styles();
		$this->add_box_attributes_styles();
		$this->add_box_styles_controls();
		$this->add_filter_indicator_styles_controls();
		$this->add_more_button_styles_controls();
	}

	protected function add_title_area_content_controls() {
		$this->start_controls_section(
			'title_area_section',
			[
				'label' => esc_html__( 'Title Area', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'title_text',
			[
				'label'   => esc_html__( 'Widget Title', 'the7mk2' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Widget Title', 'the7mk2' ),
			]
		);
		$this->add_control(
			'selected_attrs',
			[
				'label'        => esc_html__( 'Selected brands number', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'On', 'the7mk2' ),
				'label_off'    => esc_html__( 'Off', 'the7mk2' ),
				'return_value' => 'yes',
				'default'      => '',
				'condition' => [
					'title_text!' => '',
				],
			]
		);

		$this->add_control(
			'toggle',
			[
				'label'        => esc_html__( 'Widget Toggle', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'On', 'the7mk2' ),
				'label_off'    => esc_html__( 'Off', 'the7mk2' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition' => [
					'title_text!' => '',
				],
			]
		);

		$this->add_control(
			'toggle_closed_by_default',
			[
				'label'        => esc_html__( 'Closed By Default', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'the7mk2' ),
				'label_off'    => esc_html__( 'No', 'the7mk2' ),
				'return_value' => 'closed',
				'default'      => '',
				'condition'    => [
					'toggle!' => '',
					'title_text!' => '',
				],
			]
		);

		$this->add_control(
			'toggle_icon',
			[
				'label'            => esc_html__( 'Icon', 'the7mk2' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default'          => [
					'value'   => 'fas fa-chevron-down',
					'library' => 'fa-solid',
				],
				'recommended'      => [
					'fa-solid'   => [
						'chevron-down',
						'angle-down',
						'angle-double-down',
						'caret-down',
						'caret-square-down',
					],
					'fa-regular' => [
						'caret-square-down',
					],
				],
				'label_block'      => false,
				'skin'             => 'inline',
				'condition'        => [
					'toggle!' => '',
					'title_text!' => '',
				],
			]
		);

		$this->add_control(
			'toggle_active_icon',
			[
				'label'            => esc_html__( 'Active Icon', 'the7mk2' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon_active',
				'default'          => [
					'value'   => 'fas fa-chevron-up',
					'library' => 'fa-solid',
				],
				'recommended'      => [
					'fa-solid'   => [
						'chevron-up',
						'angle-up',
						'angle-double-up',
						'caret-up',
						'caret-square-up',
					],
					'fa-regular' => [
						'caret-square-up',
					],
				],
				'skin'             => 'inline',
				'label_block'      => false,
				'condition'        => [
					'toggle!'             => '',
					'toggle_icon[value]!' => '',
					'title_text!' => '',
				],
			]
		);

		$this->end_controls_section();
	}


	protected function add_attributes_content_controls() {
		$this->start_controls_section(
			'attributes_section',
			[
				'label' => esc_html__( 'Items', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'attr_query_type',
			[
				'label'   => esc_html__( 'Query Type', 'the7mk2' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'and' => esc_html__( 'AND', 'the7mk2' ),
					'or'  => esc_html__( 'OR', 'the7mk2' ),
				],
				'default' => 'and',
			]
		);

		$this->add_control(
			'items_count',
			[
				'label'        => esc_html__( 'Products Count', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'the7mk2' ),
				'label_off'    => esc_html__( 'No', 'the7mk2' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);
		$this->add_control(
			'items_name',
			[
				'label'        => esc_html__( 'Brand name', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'the7mk2' ),
				'label_off'    => esc_html__( 'No', 'the7mk2' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition' => [
					'filter_type' => 'swatch',
				],
			]
		);

		$this->add_control(
			'active_filter_indicator_icon_show',
			[
				'label'        => esc_html__( 'Filter icon', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'the7mk2' ),
				'label_off'    => esc_html__( 'No', 'the7mk2' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);
		$this->add_control(
			'active_filter_indicator_icon',
			[
				'label'       => esc_html__( 'Icon', 'the7mk2' ),
				'type'        => Controls_Manager::ICONS,
				'label_block' => false,
				'skin'        => 'inline',
				'default' => [
					'value'   => 'fas fa-check',
					'library' => 'fa-solid',
				],
				'exclude_inline_options' => [ 'none' ],
				'condition'      => [
					'active_filter_indicator_icon_show' => 'yes',
				],
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'attributes_layout',
			[
				'label' => esc_html__( 'Layout', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'layout',
			[
				'label'                => esc_html__( 'Layout', 'the7mk2' ),
				'type'                 => Controls_Manager::SELECT,
				'options'              => [
					'grid'   => esc_html__( 'Grid', 'the7mk2' ),
					'inline' => esc_html__( 'Inline', 'the7mk2' ),
				],
				'separator'            => 'before',
				'default'              => 'grid',
				'prefix_class'         => 'filter-layout-',
				'selectors'            => [
					'{{WRAPPER}} .filter-nav' => '{{VALUE}}',
				],
				'selectors_dictionary' => [
					'grid'   => 'display: grid',
					'inline' => 'display: flex; flex-wrap: wrap;',
				],
			]
		);

		$this->add_responsive_control(
			'grid_columns',
			[
				'label'          => esc_html__( 'Number Of Columns', 'the7mk2' ),
				'type'           => Controls_Manager::NUMBER,
				'default'        => 1,
				'mobile_default' => 1,
				'min'            => 1,
				'max'            => 6,
				'condition'      => [
					'layout' => 'grid',
				],
				'selectors'      => [
					'{{WRAPPER}} .filter-nav' => 'grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr));',
				],
			]
		);

		$this->add_responsive_control(
			'box_row_space',
			[
				'label'     => esc_html__( 'Row Gap', 'the7mk2' ),
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
					'{{WRAPPER}}  .filter-nav' => 'grid-row-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'box_column_space',
			[
				'label'     => esc_html__( 'Column Gap', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}}  .filter-nav' => 'grid-column-gap: {{SIZE}}{{UNIT}};',
				],
				'default'   => [
					'size' => 10,
				],
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'attributes_navigation',
			[
				'label' => esc_html__( 'Navigation', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'navigation',
			[
				'label'     => esc_html__( 'Widget Navigation', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'disabled'    => esc_html__( 'Disabled', 'the7mk2' ),
					'scroll'      => esc_html__( 'Scroll', 'the7mk2' ),
					'more_button' => esc_html__( 'Show more items', 'the7mk2' ),
				],
				'separator' => 'before',
				'default'   => 'disabled',
			]
		);

		$this->add_responsive_control(
			'navigation_max_height',
			[
				'label'     => esc_html__( 'Maximum Height', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'condition' => [
					'navigation' => 'scroll',
				],
				'default'   => [
					'size' => 50,
				],
				'selectors' => [
					'{{WRAPPER}} .filter-container' => 'max-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'navigation_items',
			[
				'label'     => esc_html__( 'Visible Number Of Brands', 'the7mk2' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 5,
				'min'       => 1,
				'max'       => 50,
				'condition' => [
					'navigation' => 'more_button',
				],
			]
		);

		$this->add_control(
			'navigation_items_more_button_text',
			[
				'label'     => esc_html__( 'Show More Items Text', 'the7mk2' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( '+%s more', 'the7mk2' ),
				'condition' => [
					'navigation' => 'more_button',
				],
			]
		);

		$this->add_control(
			'navigation_items_more_button_text_description',
			[
				'raw'             => esc_html__( 'Use "%s" to display the number of items. Example: +%s more', 'the7mk2' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-descriptor',
				'condition'       => [
					'navigation' => 'more_button',
				],
			]
		);
		$this->end_controls_section();
	}

	public function get_attributes() {
		$attribute_array      = array();
		$attribute_taxonomies = wc_get_attribute_taxonomies();

		if ( ! empty( $attribute_taxonomies ) ) {
			foreach ( $attribute_taxonomies as $tax ) {
				if ( taxonomy_exists( wc_attribute_taxonomy_name( $tax->attribute_name ) ) ) {
					$attribute_array[ $tax->attribute_name ] = $tax->attribute_name;
				}
			}
		}

		return $attribute_array;
	}

	protected function add_title_styles() {
		$this->start_controls_section(
			'title_section',
			[
				'label'     => esc_html__( 'Title Area', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'title_text!' => '',
				],
			]
		);

		$selector = '{{WRAPPER}} .filter-title';

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => $selector,
			]
		);


		$this->add_responsive_control(
			'title_arrow_size',
			[
				'label'     => esc_html__( 'Toggle Icon Size', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'   => [
					'size' => 16,
				],
				'condition' => [
					'toggle!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .filter-toggle-icon .elementor-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'title_min_height',
			[
				'label'          => esc_html__( 'Min background height', 'the7mk2' ),
				'type'           => Controls_Manager::NUMBER,
				'min'            => 1,
				'max'            => 600,
				'selectors'      => [
					'{{WRAPPER}} .filter-header' => 'min-height: {{SIZE}}px;',
				],
			]
		);

		$selector = '{{WRAPPER}} .filter-header';

		$this->add_responsive_control( 'title_padding', [
			'label'      => esc_html__( 'Padding', 'the7mk2' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'range'      => [
				'px' => [
					'min' => 0,
					'max' => 200,
				],
				'%'  => [
					'min' => 0,
					'max' => 100,
				],
			],
			'selectors'  => [
				$selector => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
			],
		] );

		$this->add_responsive_control( 'title_margin', [
			'label'      => esc_html__( 'Margins', 'the7mk2' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'range'      => [
				'px' => [
					'min' => 0,
					'max' => 200,
				],
				'%'  => [
					'min' => 0,
					'max' => 100,
				],
			],
			'selectors'  => [
				$selector => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
			],
		] );

		$this->add_responsive_control( 'title_border_radius', [
			'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors'  => [
				$selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		] );

		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => 'title_border',
			'label'    => esc_html__( 'Border', 'the7mk2' ),
			'selector' => $selector,
			'exclude'  => [ 'color' ],
		] );

		$this->start_controls_tabs(
			'title_arrow_tabs_style'
		);

		$this->start_controls_tab(
			'normal_title_arrow_style',
			[
				'label' => esc_html__( 'Closed', 'the7mk2' ),
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__( 'Title Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .filter-header .filter-title' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'title_attr_number_color',
			[
				'label'     => esc_html__( 'Brands number color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .filter-header .selected-attr-number' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_arrow_color',
			[
				'label'     => esc_html__( 'Toggle Icon Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .filter-header .filter-toggle-icon .filter-toggle-closed i'   => 'color: {{VALUE}};',
					'{{WRAPPER}} .filter-header .filter-toggle-icon .filter-toggle-closed svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
				'condition' => [
					'toggle!'             => '',
					'toggle_icon[value]!' => '',
				],
			]
		);

		$this->add_control( 'title_bg_color', [
			'label'     => esc_html__( 'Background Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .filter-header'     => 'background: {{VALUE}};',
			],
		] );

		$this->add_control(
			'title_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .filter-header' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'title_border_border!' => [ '', 'none' ],
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'hover_title_arrow_style',
			[
				'label' => esc_html__( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			'hover_title_color',
			[
				'label'     => esc_html__( 'Title Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .the7-product-filter:not(.fix) .filter-header:hover .filter-title' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'hover_title_attr_number_color',
			[
				'label'     => esc_html__( 'Brands number color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .the7-product-filter:not(.fix) .filter-header:hover .selected-attr-number' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'hover_title_arrow_color',
			[
				'label'     => esc_html__( 'Toggle Icon Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .filter-header:hover .filter-toggle-icon .elementor-icon i'   => 'color: {{VALUE}};',
					'{{WRAPPER}} .filter-header:hover .filter-toggle-icon .elementor-icon svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
				'condition' => [
					'toggle!'             => '',
					'toggle_icon[value]!' => '',
				],
			]
		);

		$this->add_control( 'hover_bg_color', [
			'label'     => esc_html__( 'Background Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .the7-product-filter:not(.fix) .filter-header:hover'  => 'background: {{VALUE}};',
			],
		] );

		$this->add_control(
			'hover_title_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .the7-product-filter:not(.fix) .filter-header:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'title_border_border!' => [ '', 'none' ],
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'active_title_arrow_style',
			[
				'label' => esc_html__( 'Open', 'the7mk2' ),
			]
		);

		$this->add_control(
			'active_title_color',
			[
				'label'     => esc_html__( 'Title Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .the7-product-filter:not(.closed) .filter-header .filter-title' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'active_title_attr_number_color',
			[
				'label'     => esc_html__( 'Brands number color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .the7-product-filter:not(.closed) .filter-header .selected-attr-number' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'active_title_arrow_color',
			[
				'label'     => esc_html__( 'Toggle Icon Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .filter-header .filter-toggle-icon .filter-toggle-active i'   => 'color: {{VALUE}};',
					'{{WRAPPER}} .filter-header .filter-toggle-icon .filter-toggle-active svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
				'condition' => [
					'toggle!'             => '',
					'toggle_icon[value]!' => '',
				],
			]
		);

		$this->add_control( 'active_bg_color', [
			'label'     => esc_html__( 'Background Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .the7-product-filter:not(.closed) .filter-header'     => 'background: {{VALUE}};',
			],
		] );

		$this->add_control(
			'active_title_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .the7-product-filter:not(.closed) .filter-header' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'title_border_border!' => [ '', 'none' ],
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();


		$this->end_controls_section();
	}

	protected function add_filter_indicator_styles_controls() {
		$this->start_controls_section(
			'filter_indicator_section',
			[
				'label'      => esc_html__( 'Filter Icon', 'the7mk2' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'condition' => [
					'active_filter_indicator_icon_show' => 'yes',
				],
			]
		);

		$icon_selector = '{{WRAPPER}} .filter-nav-item-container .indicator';

		$this->add_responsive_control(
			'filter_indicator_space',
			[
				'label'     => esc_html__( 'Spacing', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}}  .filter-nav-item-container .indicator' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'filter_indicator_icon_size',
			[
				'label'     => esc_html__( 'Icon Size', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}}' => '--indicator-icon-size: {{SIZE}}{{UNIT}};',
					$icon_selector . ' .elementor-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'condition'      => [
					'active_filter_indicator_icon_show' => 'yes',
					'active_filter_indicator_icon_show[value]!' => '',
				],
			]
		);
		$this->add_responsive_control(
			'filter_indicator_min_height',
			[
				'label'          => esc_html__( 'Min background height', 'the7mk2' ),
				'type'           => Controls_Manager::NUMBER,
				'min'            => 1,
				'max'            => 600,
				'selectors'      => [
					'{{WRAPPER}}' => '--indicator-height-size: max({{SIZE}}px, var(--indicator-icon-size, 1em));',
					$icon_selector => 'height: max({{SIZE}}px, var(--indicator-icon-size, 10px));',
				],
			]
		);
		$this->add_responsive_control(
			'filter_indicator_min_width',
			[
				'label'          => esc_html__( 'Min background width', 'the7mk2' ),
				'type'           => Controls_Manager::NUMBER,
				'min'            => 1,
				'max'            => 600,
				'selectors'      => [
					'{{WRAPPER}}' => '--indicator-size: max({{SIZE}}px, var(--indicator-icon-size, 1em));',
					$icon_selector => 'width: max({{SIZE}}px, var(--indicator-icon-size, 10px));',
				],
			]
		);

		$this->add_control(
			'filter_indicator_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					$icon_selector => 'border-radius: {{SIZE}}{{UNIT}}',
				],
			]
		);
		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => 'filter_indicator_border',
			'label'    => esc_html__( 'Border Type', 'the7mk2' ),
			'selector' => $icon_selector,
			'exclude'        => [ 'color' ],
		] );

		$this->add_filter_indicator_tabs_controls( 'normal_' );

		$this->end_controls_section();
	}

	protected function add_filter_indicator_tabs_controls( $prefix ) {
		$active_class = ':not(.active)';

		$selector = '{{WRAPPER}} .filter-nav-item .filter-nav-item-container .indicator';

		$this->start_controls_tabs(
			$prefix . 'indicator_tabs',
			[
			]
		);

		$this->start_controls_tab(
			$prefix . 'filter_indicator_tab',
			[
				'label' => esc_html__( 'Normal', 'the7mk2' ),
			]
		);
		$this->add_control(
			$prefix . 'filter_indicator_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$selector => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			$prefix . 'filter_indicator_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$selector => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'filter_indicator_border_border!' => [ '', 'none' ],
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			$prefix . 'filter_indicator_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'the7mk2' ),
			]
		);

		$helper_indicator_class = '.the7-product-brand-filter.anim-disp-normal-indicator';
		if ( $prefix === 'active_' ) {
			$helper_indicator_class = '.the7-product-brand-filter.anim-disp-active-indicator';
		}

		$hov_selector = '{{WRAPPER}} .filter-nav-item .filter-nav-item-container:hover .indicator';
		$this->add_control(
			$prefix . 'filter_indicator_hover_icon_color',
			[
				'label'     => esc_html__( 'Icon Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					// $selector . ' .elementor-icon.indicator-hover' => 'color: {{VALUE}};',
					// $selector . ' .elementor-icon.indicator-hover svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
					$hov_selector . ' .elementor-icon'     => 'color: {{VALUE}};',
					$hov_selector . ' .elementor-icon svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
			]
		);

		$selector = '{{WRAPPER}} .filter-nav-item .filter-nav-item-container:hover .indicator';

		$this->add_control(
			$prefix . 'filter_indicator_hover_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$selector => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			$prefix . 'filter_indicator_hover_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$selector => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'filter_indicator_border_border!' => [ '', 'none' ],
				],
			]
		);

		$this->end_controls_tab();
		$this->start_controls_tab(
			$prefix . 'filter_indicator_active_tab',
			[
				'label' => esc_html__( 'Active', 'the7mk2' ),
			]
		);

		$helper_indicator_class = '.the7-product-brand-filter.anim-disp-normal-indicator';

		$active_selector = '{{WRAPPER}} .filter-nav-item.active .filter-nav-item-container:not(:hover) .indicator';
		$this->add_control(
			$prefix . 'filter_indicator_active_icon_color',
			[
				'label'     => esc_html__( 'Icon Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$active_selector . ' .elementor-icon'     => 'color: {{VALUE}};',
					$active_selector . ' .elementor-icon svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
			]
		);

		$selector = '{{WRAPPER}} .filter-nav-item' . $active_class . ' .filter-nav-item-container:hover .indicator';

		$this->add_control(
			$prefix . 'filter_indicator_active_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$active_selector => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			$prefix . 'filter_indicator_active_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$active_selector => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'filter_indicator_border_border!' => [ '', 'none' ],
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();
	}

	protected function add_box_attributes_styles() {
		$this->start_controls_section(
			'container_section',
			[
				'label'     => esc_html__( 'Content Area', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$selector = '{{WRAPPER}} .filter-container';

		$this->add_responsive_control( 'container_padding', [
			'label'      => esc_html__( 'Padding', 'the7mk2' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'range'      => [
				'px' => [
					'min' => 0,
					'max' => 200,
				],
				'%'  => [
					'min' => 0,
					'max' => 100,
				],
			],
			'selectors'  => [
				$selector => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
			],
		] );

		$this->add_responsive_control( 'container_margin', [
			'label'      => esc_html__( 'Margins', 'the7mk2' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'range'      => [
				'px' => [
					'min' => 0,
					'max' => 200,
				],
				'%'  => [
					'min' => 0,
					'max' => 100,
				],
			],
			'default'    => [
				'top'      => '15',
				'right'    => '0',
				'bottom'   => '0',
				'left'     => '0',
				'unit'     => 'px',
				'isLinked' => false,
			],
			'selectors'  => [
				$selector => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
			],
		] );

		$this->add_responsive_control( 'container_border_radius', [
			'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors'  => [
				$selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		] );

		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => 'container_border',
			'label'    => esc_html__( 'Border', 'the7mk2' ),
			'selector' => $selector,
		] );

		$this->add_control( 'container_bg_color', [
			'label'     => esc_html__( 'Background Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				$selector => 'background: {{VALUE}};',
			],
		] );

		$this->end_controls_section();
	}

	protected function add_box_styles_controls() {
		$this->start_controls_section(
			'box_section',
			[
				'label' => esc_html__( 'Items', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$selector = '{{WRAPPER}}  .filter-nav-item-container';
		$this->add_responsive_control(
			'attr_text_alignment',
			[
				'label'                => esc_html__( 'Alignment', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'label_block'          => false,
				'options'              => [
					'left'   => [
						'title' => esc_html__( 'Left', 'the7mk2' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'the7mk2' ),
						'icon'  => 'eicon-text-align-center',
					],
				],
				'default'              => 'left',
				'selectors'            => [
					'{{WRAPPER}}' => '--attribute-align-content: {{VALUE}};',
					$selector => 'justify-content: {{VALUE}};',
				],
				'selectors_dictionary' => [
					'left'   => 'flex-start',
					'center' => 'center',
				],
			]
		);


		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'box_text_typography',
				'selector' => $selector . ' .name,' . $selector . ' .count',
			]
		);
		$this->add_responsive_control(
			'attr_min_height',
			[
				'label'          => esc_html__( 'Min background height', 'the7mk2' ),
				'type'           => Controls_Manager::NUMBER,
				'min'            => 1,
				'max'            => 600,
				'selectors'      => [
					$selector => 'min-height: {{SIZE}}px;',
				],
			]
		);
		$this->add_responsive_control(
			'attr_min_width',
			[
				'label'          => esc_html__( 'Min background width', 'the7mk2' ),
				'type'           => Controls_Manager::NUMBER,
				'min'            => 1,
				'max'            => 600,
				'selectors'      => [
					'{{WRAPPER}}  .filter-nav-item' => 'min-width: min({{SIZE}}px, 100%);',
				],
				'condition'      => [
					'layout!' => 'grid',
				],
			]
		);


		$this->add_responsive_control(
			'box_padding',
			[
				'label'      => esc_html__( 'Paddings', 'the7mk2' ),
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
				'default'    => [
					'top'      => '0',
					'right'    => '0',
					'bottom'   => '0',
					'left'     => '0',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					$selector => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'box_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					$selector => 'border-radius: {{SIZE}}{{UNIT}}',
				],
			]
		);
		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => 'box_border',
			'label'    => esc_html__( 'Border Type', 'the7mk2' ),
			'selector' => $selector,
			'exclude'        => [ 'color' ],
		] );

		$this->start_controls_tabs( 'box_tabs_style' );
		$this->add_box_tab_controls( 'normal_', esc_html__( 'Normal', 'the7mk2' ) );
		$this->add_box_tab_controls( 'hover_', esc_html__( 'Hover', 'the7mk2' ) );
		$this->add_box_tab_controls( 'active_', esc_html__( 'Active', 'the7mk2' ) );
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function add_box_tab_controls( $prefix_name, $box_name ) {
		$extra_class = '';

		$isHover = '';
		if ( $prefix_name === 'hover_' ) {
			$extra_class .= ':not(.fix)';
			$isHover      = ':hover';
		} elseif ( $prefix_name === 'active_' ) {
			$extra_class .= '.active';
		}
		$selector = '{{WRAPPER}} .filter-nav-item' . $extra_class . ' .filter-nav-item-container' . $isHover;

		$this->start_controls_tab(
			$prefix_name . 'box_style',
			[
				'label' => $box_name,
			]
		);
		$this->add_control(
			$prefix_name . 'box_text_color',
			[
				'label'     => esc_html__( 'Text Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$selector . ' .name' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			$prefix_name . 'item_count_color',
			[
				'label'     => esc_html__( 'Count  Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					$selector . ' .count' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			$prefix_name . 'box_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$selector => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			$prefix_name . 'box_border_color',
			[
				'label'     => esc_html__( 'Border color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$selector => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'box_border_border!' => [ '', 'none' ],
				],
			]
		);

		$this->end_controls_tab();
	}

	protected function add_more_button_styles_controls() {
		$this->start_controls_section(
			'more_button_section',
			[
				'label'     => esc_html__( 'Show More Items', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'navigation' => 'more_button',
				],
			]
		);

		$selector = '{{WRAPPER}} .filter-container .filter-show-more';

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'more_button_typography',
				'selector' => $selector,
			]
		);

		$this->start_controls_tabs( 'more_button_tabs_style' );

		$this->start_controls_tab(
			'normal_more_button_style',
			[
				'label' => esc_html__( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_control(
			'more_button_text_color',
			[
				'label'     => esc_html__( 'Text Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					$selector . ' span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'hover_more_button_style',
			[
				'label' => esc_html__( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			'hover_more_button_text_color',
			[
				'label'     => esc_html__( 'Text Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					$selector . ':hover span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_responsive_control(
			'more_button_space',
			[
				'label'     => esc_html__( 'Gap', 'the7mk2' ),
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
					$selector => 'margin-top: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		if ( ! $this->isPreview() && ! is_shop() && ! is_product_taxonomy() ) {
			return;
		}

		$settings = $this->get_settings_for_display();
		$taxonomy = 'product_brand';

		if ( ! taxonomy_exists( $taxonomy ) ) {
			return;
		}

		$terms = get_terms( $taxonomy, [ 'hide_empty' => true ] );

		if ( empty( $terms ) || is_wp_error( $terms ) ) {
			return;
		}

		$active_count = $this->get_active_brand_filters_count( );


		$this->add_render_attribute( 'product-brand-filter', 'class', 'the7-product-brand-filter the7-product-filter' );
		$this->add_render_attribute( 'product-brand-filter', 'class', 'filter-navigation-' . esc_attr( $settings['navigation'] ) );

		if ( $settings['toggle'] === 'yes' ) {
			$this->add_render_attribute( 'product-brand-filter', 'class', 'collapsible' );
			$this->add_render_attribute( 'product-brand-filter', 'class', $settings['toggle_closed_by_default'] );
			if ( $settings['toggle_closed_by_default'] ) {
				$this->add_render_attribute( 'filter-container', 'style', 'display:none' );
			}
		}

		if ( $settings['navigation'] === 'scroll' ) {
			$this->add_render_attribute( 'product-brand-filter', 'class', 'the7-scrollbar-style' );
		}

		$this->add_render_attribute( 'filter-title', 'class', 'filter-title' );
		if ( empty( $settings['title_text'] ) ) {
			$this->add_render_attribute( 'filter-title', 'class', 'empty' );
		}

		$this->add_render_attribute( 'filter-container', 'class', 'filter-container' );
		?>
		<div <?php echo $this->get_render_attribute_string( 'product-brand-filter' ); ?>>
			<div class="filter-header widget-title">
				<div <?php echo $this->get_render_attribute_string( 'filter-title' ); ?>>
					<?php echo esc_html( $settings['title_text'] ); ?>
					<?php if ( ! empty( $settings['selected_attrs'] ) && $active_count > 0 ) : ?>
						<span class="selected-attr-number">(<?php echo esc_html( $active_count ); ?>)</span>
					<?php endif; ?>
				</div>
				<?php if ( ! empty( $settings['toggle_icon']['value'] ) ) : ?>
					<div class="filter-toggle-icon">
						<span class="elementor-icon filter-toggle-closed">
							<?php Icons_Manager::render_icon( $settings['toggle_icon'] ); ?>
						</span>
						<?php if ( ! empty( $settings['toggle_active_icon']['value'] ) ) : ?>
							<span class="elementor-icon filter-toggle-active">
								<?php Icons_Manager::render_icon( $settings['toggle_active_icon'] ); ?>
							</span>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
			<div <?php echo $this->get_render_attribute_string( 'filter-container' ); ?>>
				<?php $this->display_items( $terms, $taxonomy, $settings ); ?>
			</div>
		</div>
		<?php
	}

	protected function add_indicator_anim_attribute( $settings, $prefix ) {
		if ( $settings[ $prefix . '_filter_indicator_icon_show' ] === 'yes' ) {
			$normal_icon   = $settings[ $prefix . '_filter_indicator_icon' ] ['value'];
			$hover_icon    = $settings[ $prefix . '_filter_indicator_hover_icon' ] ['value'];
			$add_animate   = false;
			$has_animation = false;
			if ( ! empty( $normal_icon ) && ! empty( $hover_icon ) && $normal_icon == $hover_icon ) {
				$this->add_render_attribute( 'product-brand-filter', 'class', 'anim-disp-' . $prefix . '-indicator' );
				$has_animation = true;
			} elseif ( empty( $normal_icon ) && ! empty( $hover_icon ) ) {
				$add_animate = true;
			} elseif ( ! empty( $normal_icon ) && empty( $hover_icon ) ) {
				$add_animate = true;
			}
			if ( $add_animate ) {
				$has_animation = true;
				$this->add_render_attribute( 'product-brand-filter', 'class', 'anim-trans-' . $prefix . '-indicator' );
			}
			if ( ! $has_animation ) {
				$this->add_render_attribute( 'product-brand-filter', 'class', 'anim-off-' . $prefix . '-indicator' );
			}
		}
	}
	protected function get_active_brand_filters_count( $taxonomy = 'product_brand' ) {
		$active_count = 0;

		if ( isset( $_GET[ $taxonomy ] ) ) {
			$filter = wc_clean( wp_unslash( $_GET[ $taxonomy ] ) );

			if ( is_array( $filter ) ) {
				$active_count = count( array_filter( $filter ) );
			} elseif ( ! empty( $filter ) ) {
				$active_count = count( explode( ',', $filter ) );
			}
		}

		return $active_count;
	}

	protected function display_items( $terms, $taxonomy, $settings ) {
		$query_type  = $settings['attr_query_type'];
		$filter_name    = 'filter_' . $taxonomy;
		$current_filter = isset( $_GET[ $filter_name ] ) ? explode( ',', wc_clean( wp_unslash( $_GET[ $filter_name ] ) ) ) : []; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$current_filter = array_map( 'intval', $current_filter );
		$base_link      = $this->get_page_base_url( $taxonomy );

		$nav_items_limit = isset( $settings['navigation_items'] ) ? (int) $settings['navigation_items'] : 0;
		$is_nav_limited  = $nav_items_limit > 0;

		// Get current product category
		$current_category_id = 0;
		if ( is_product_category() ) {
			$current_category   = get_queried_object();
			$current_category_id = $current_category->term_id;
		}
	
		// Count products for each term (filtering by current category if present)
		$term_counts = [];
		foreach ( $terms as $term ) {
			$tax_query = [
				'relation' => 'AND',
				[
					'taxonomy' => $taxonomy,
					'field'    => 'term_id',
					'terms'    => $term->term_id,
				],
			];
	
			// If we are in a product category, filter by it
			if ( $current_category_id ) {
				$tax_query[] = [
					'taxonomy' => 'product_cat',
					'field'    => 'term_id',
					'terms'    => $current_category_id,
				];
			}
	
			$query = new \WP_Query( [
				'post_type' => 'product',
				'tax_query' => $tax_query,
				'fields'    => 'ids',
				'nopaging'  => true,
			] );
	
			$term_counts[ $term->term_id ] = $query->found_posts;
		}
	
		// Filter out terms (brands) that have no products in current category
		$terms = array_filter( $terms, function( $term ) use ( $term_counts ) {
			return ! empty( $term_counts[ $term->term_id ] );
		} );
		?>
	
		<ul class="filter-nav brand-nav">
			<?php
			$term_items = 0;
			foreach ( $terms as $term ) :
				$is_active = in_array( $term->term_id, $current_filter, true );
	
				$new_values = $current_filter;
				if ( ! in_array( $term->term_id, $new_values, true ) ) {
					$new_values[] = $term->term_id;
				}
	
				// Remove self or current term
				foreach ( $new_values as $key => $value ) {
					if ( $value === $this->get_current_term_id() || ( $is_active && $value === $term->term_id ) ) {
						unset( $new_values[ $key ] );
					}
				}
	
				$link = $base_link;
				if ( ! empty( $new_values ) ) {
					$link = add_query_arg(
						[
							'filtering'  => '1',
							$filter_name => implode( ',', $new_values ),
						],
						$link
					);
				}
	
				$term_items++;
				$link = remove_query_arg( 'paged', $link ); // Reset pagination

				$render_item_key = 'filter-nav-item' . $term_items;
				$class = 'filter-nav-item';
				$class .= $is_active ? ' active' : '';
				if ( $is_nav_limited && $term_items > $nav_items_limit ) {
					// Hide extra items.
					$this->add_render_attribute( $render_item_key, 'style', 'display: none;' );
				} else {
					$class .= ' show';
				}
				$this->add_render_attribute( $render_item_key, 'class', esc_attr( $class ) );
				$count = isset( $term_counts[ $term->term_id ] ) ? $term_counts[ $term->term_id ] : 0;
				?>

				<li <?php echo $this->get_render_attribute_string( $render_item_key ); ?>>
					<a href="<?php echo esc_url( $link ); ?>" class="filter-nav-item-container">
						<?php $this->displayFilterIndicator( $settings ); ?>
						<span class="name">
							<?php echo esc_html( $term->name ); ?>
							<?php if ( $settings['items_count'] === 'yes' ) : ?>
								<span class="count">(<?php echo esc_html( $count ); ?>)</span>
							<?php endif; ?>
						</span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php
		if ( $is_nav_limited && count( $terms ) > $nav_items_limit && ! empty( $settings['navigation_items_more_button_text'] ) ) {
			$hidden_items_count = count( $terms ) - $nav_items_limit;
			$button_text        = str_replace( '%s', $hidden_items_count, $settings['navigation_items_more_button_text'] );
			echo '<a class="filter-show-more" href="#"><span>' . esc_html( $button_text ) . '</span></a>';
		}
	}
	

	protected function displayFilterIndicator( $settings ) {

		if ( $settings['active_filter_indicator_icon_show' ] === 'yes' ) {
			?>
			<div class="indicator">
				<span class="elementor-icon">
					<?php
					if ( empty( $settings[ 'active_filter_indicator_icon' ] ['value'] ) ) {
						?>
						 <i class="empty-icon"></i>
						<?php
					} else {
						Icons_Manager::render_icon( $settings[ 'active_filter_indicator_icon' ] );
					}
					?>
				</span>
			</div>
			<?php
		}
	}
	protected function displayFilterSwatchIndicator( $settings ) {

		if ( $settings[ 'active_filter_indicator_icon_show' ] === 'yes' ) {
			?>
				<span class="elementor-icon">
					<?php
					if ( empty( $settings[ 'active_filter_indicator_icon' ] ['value'] ) ) {
						?>
						 <i class="empty-icon"></i>
						<?php
					} else {
						Icons_Manager::render_icon( $settings['active_filter_indicator_icon' ] );
					}
					?>
				</span>
			<?php
		}
	}

	private function isPreview() {
		return $this->is_preview_mode() || Plugin::$instance->editor->is_edit_mode();
	}

	/**
	 * Get current page URL for layered nav items.
	 *
	 * @param  string $taxonomy Taxonomy.
	 * @return string
	 */
	protected function get_page_base_url( $taxonomy ) {
		if ( defined( 'SHOP_IS_ON_FRONT' ) ) {
			$link = home_url();
		} elseif ( is_post_type_archive( 'product' ) || is_page( wc_get_page_id( 'shop' ) ) ) {
			$link = get_post_type_archive_link( 'product' );
		} elseif ( is_product_category() ) {
			$link = get_term_link( get_query_var( 'product_cat' ), 'product_cat' );
		} elseif ( is_product_tag() ) {
			$link = get_term_link( get_query_var( 'product_tag' ), 'product_tag' );
		} elseif ( is_tax() ) {
			// Handle any taxonomy archive, including attributes
			$queried_object = get_queried_object();
			if ( is_null( $queried_object ) ) {
				$link = get_post_type_archive_link( 'product' );
			} else {
				$link = get_term_link( $queried_object->term_id, $queried_object->taxonomy );
			}
		} else {
			$link = get_post_type_archive_link( 'product' );
		}
		// phpcs:disable WordPress.Security.NonceVerification.Recommended

		// Min/Max.
		if ( isset( $_GET['min_price'] ) ) {
			$link = add_query_arg( 'min_price', wc_clean( wp_unslash( $_GET['min_price'] ) ), $link );
		}

		if ( isset( $_GET['max_price'] ) ) {
			$link = add_query_arg( 'max_price', wc_clean( wp_unslash( $_GET['max_price'] ) ), $link );
		}

		// Orderby.
		if ( isset( $_GET['orderby'] ) ) {
			$link = add_query_arg( 'orderby', wc_clean( wp_unslash( $_GET['orderby'] ) ), $link );
		}

		/**
		 * Search Arg.
		 * To support quote characters, first they are decoded from &quot; entities, then URL encoded.
		 */
		if ( get_search_query() ) {
			$link = add_query_arg( 's', rawurlencode( htmlspecialchars_decode( get_search_query() ) ), $link );
		}

		// Post Type Arg.
		if ( isset( $_GET['post_type'] ) ) {
			$link = add_query_arg( 'post_type', wc_clean( wp_unslash( $_GET['post_type'] ) ), $link );
		}

		// Min Rating Arg.
		if ( isset( $_GET['min_rating'] ) ) {
			$link = add_query_arg( 'min_rating', wc_clean( wp_unslash( $_GET['min_rating'] ) ), $link );
		}

		// All current filters.
		$_chosen_attributes = WC_Query::get_layered_nav_chosen_attributes();
		if ( $_chosen_attributes ) {
			foreach ( $_chosen_attributes as $name => $data ) {
				if ( $name === $taxonomy ) {
					continue;
				}
				$filter_name = sanitize_title( str_replace( 'pa_', '', $name ) );
				if ( ! empty( $data['terms'] ) ) {
					$link = add_query_arg( 'filter_' . $filter_name, implode( ',', $data['terms'] ), $link );
				}
				if ( 'or' === $data['query_type'] ) {
					$link = add_query_arg( 'query_type_' . $filter_name, 'or', $link );
				}
			}
		}

		return $link;
	}

	/**
	 * Return the currently viewed term ID.
	 *
	 * @return int
	 */
	protected function get_current_term_id() {
		return absint( is_tax() ? get_queried_object()->term_id : 0 );
	}
}
