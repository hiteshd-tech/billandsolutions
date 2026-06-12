<?php
/**
 * The7 elements product data tabs widget for Elementor.
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widgets\Woocommerce;

use Elementor\Controls_Manager;
use Elementor\Plugin;
use Elementor\Widget_Base;

use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;

use The7\Mods\Compatibility\Elementor\The7_Elementor_Less_Vars_Decorator_Interface;
use Elementor\Group_Control_Typography;

defined( 'ABSPATH' ) || exit;

class Product_Tabs extends The7_Elementor_Widget_Base {

	public function get_name() {
		return 'the7-woocommerce-product-data-tabs';
	}

	protected function the7_title() {
		return esc_html__( 'Product Data Tabs', 'the7mk2' );
	}

	protected function the7_icon() {
		return 'eicon-product-tabs';
	}

	protected function the7_keywords() {
		return [ 'woocommerce', 'shop', 'store', 'data', 'product', 'tabs' ];
	}

	public function get_categories() {
		return [ 'woocommerce-elements-single' ];
	}

	public function get_script_depends() {
		if ( Plugin::$instance->preview->is_preview_mode() ) {
			return [ 'the7-single-product-tab-preview' ];
		}

		return [];
	}

	public function render_plain_content() {
	}

	protected function register_controls() {
		$this->start_controls_section( 'section_product_tabs_style', [
			'label' => esc_html__( 'Content', 'the7mk2' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
		] );
		$this->add_basic_responsive_control(
			'type',
			[
				'label' => esc_html__( 'Layout', 'the7mk2' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'horizontal',
				'mobile_default' => 'accordion',
				'options' => [
					'horizontal' => esc_html__( 'Top', 'the7mk2' ),
					'vertical' => esc_html__( 'Side', 'the7mk2' ),
					'accordion' => esc_html__( 'Accordion', 'the7mk2' ),
				],
				'device_args' => [
					'tablet' => [
						'options' => [
							'default'  => esc_html__( 'No change', 'the7mk2' ),
							'horizontal' => esc_html__( 'Top', 'the7mk2' ),
							'vertical' => esc_html__( 'Side', 'the7mk2' ),
							'accordion' => esc_html__( 'Accordion', 'the7mk2' ),
						],
					],
					'mobile' => [
						'options' => [
							'default'  => esc_html__( 'No change', 'the7mk2' ),
							'horizontal' => esc_html__( 'Top', 'the7mk2' ),
							'vertical' => esc_html__( 'Side', 'the7mk2' ),
							'accordion' => esc_html__( 'Accordion', 'the7mk2' ),
						],
					],
				],
			]
		);
		$this->add_control(
			'show_description',
			[
				'label' => esc_html__( 'Description', 'the7mk2' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'the7mk2' ),
				'label_off' => esc_html__( 'Hide', 'the7mk2' ),
				'return_value' => 'yes',
				'default' => 'yes',

			]
		);
		$this->add_control(
			'show_additional',
			[
				'label' => esc_html__( 'Additional information', 'the7mk2' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'the7mk2' ),
				'label_off' => esc_html__( 'Hide', 'the7mk2' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);
		$this->add_control(
			'show_reviews',
			[
				'label' => esc_html__( 'Reviews', 'the7mk2' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'the7mk2' ),
				'label_off' => esc_html__( 'Hide', 'the7mk2' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->end_controls_section();
		$this->start_controls_section( 'section_product_tabs_top', [
			'label' => esc_html__( 'Top tabs', 'the7mk2' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
		] );
		$this->add_control(
			'align',
			[
				'label' => esc_html__( 'Alignment', 'the7mk2' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left'    => [
						'title' => esc_html__( 'Left', 'the7mk2' ),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'the7mk2' ),
						'icon' => 'eicon-h-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'the7mk2' ),
						'icon' => 'eicon-h-align-right',
					],
					'justify' => [
						'title' => esc_html__( 'Justified', 'the7mk2' ),
						'icon' => 'eicon-h-align-stretch',
					],
				],
				'prefix_class' => 'tabs-top-align-',
				'default' => 'left',
				'toggle' => false,
			]
		);
		$this->end_controls_section();
		$this->start_controls_section( 'section_product_tabs_side', [
			'label' => esc_html__( 'Side tabs', 'the7mk2' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
		] );
		$this->add_control(
			'position',
			[
				'label' => esc_html__( 'Position', 'the7mk2' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left'    => [
						'title' => esc_html__( 'Left', 'the7mk2' ),
						'icon' => 'eicon-h-align-left',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'the7mk2' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'prefix_class' => 'elementor-position-',
				'default' => 'left',
				'toggle' => false,

			]
		);
		$this->add_basic_responsive_control(
			'navigation_width',
			[
				'label' => esc_html__( 'Navigation Width', 'the7mk2' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
					'size' => '25',
				],
				'range' => [
					'%' => [
						'min' => 10,
						'max' => 50,
					],
					'px' => [
						'min' => 0,
						'max' => 250,
					],
				],
				'size_units' => [ 'px', '%' ],
				// 'selectors' => [
				// 	'{{WRAPPER}} .woocommerce-tabs'=> 'display: flex',
				// 	'{{WRAPPER}} .wc-tabs' => 'width: {{SIZE}}{{UNIT}}; flex-shrink: 0;',
				// 	'{{WRAPPER}} .wc-tabs li' => 'margin: 0;',
				// 	'{{WRAPPER}} .woocommerce-Tabs-panel' => 'flex-grow: 1; width: calc(100% - {{SIZE}}{{UNIT}})',
				// ],
			]
		);
		$this->end_controls_section();
		$this->start_controls_section( 'section_product_tabs_accordion', [
			'label' => esc_html__( 'Accordion tabs', 'the7mk2' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
		] );

		$this->add_control(
			'align_accord',
			[
				'label' => esc_html__( 'Alignment', 'the7mk2' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left'    => [
						'title' => esc_html__( 'Left', 'the7mk2' ),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'the7mk2' ),
						'icon' => 'eicon-h-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'the7mk2' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'prefix_class' => 'tabs-accordion-align-',
				'default' => 'left',
				'toggle' => false,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'skin_section',
			[
				'label'     => esc_html__( 'General', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'tabs_panel_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'the7mk2' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'.woocommerce {{WRAPPER}} .woocommerce-tabs ul.wc-tabs li.active, {{WRAPPER}} .woocommerce-tabs .woocommerce-Tabs-panel, {{WRAPPER}} .dt-tab-accordion-title, #the7-body.woocommerce {{WRAPPER}} .wc-tabs li.active:before, #the7-body.woocommerce {{WRAPPER}} .wc-tabs li.active:after' => 'background-color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'tabs_panel_border_width',
			[
				'label' => esc_html__( 'Border Width', 'the7mk2' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => '1',
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 20,
					],
				],
			]
		);

		$this->add_control(
			'tabs_panel_border_color',
			[
				'label' => esc_html__( 'Border Color', 'the7mk2' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'.woocommerce {{WRAPPER}} .dt-tab-accordion-title'  => 'border-color: {{VALUE}}',
					'.woocommerce {{WRAPPER}} .woocommerce-tabs ul.wc-tabs li.active' => 'border-color: {{VALUE}}',
					'#the7-body.woocommerce {{WRAPPER}} .woocommerce-tabs .woocommerce-Tabs-panel,
					#the7-body.woocommerce {{WRAPPER}} .wc-tabs-wrapper' => 'border-color: {{VALUE}}',
					'#the7-body.woocommerce {{WRAPPER}} .wc-tabs li.active:before, #the7-body.woocommerce {{WRAPPER}} .wc-tabs li.active:after' => 'border-color: {{VALUE}}',
					// '.woocommerce {{WRAPPER}} .dt-tab-accordion-title'  => 'box-shadow-color: {{VALUE}}'
				],
			]
		);
		$this->add_control(
			'tab_content_header_color',
			[
				'label' => esc_html__( 'Headers font color', 'the7mk2' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-Tabs-panel h1, {{WRAPPER}} .woocommerce-Tabs-panel h2, {{WRAPPER}} .woocommerce-Tabs-panel h3, {{WRAPPER}} .woocommerce-Tabs-panel h4, {{WRAPPER}} .woocommerce-Tabs-panel h5, {{WRAPPER}} .woocommerce-Tabs-panel h6, {{WRAPPER}} #reply-title, {{WRAPPER}} .woocommerce-Reviews label, {{WRAPPER}} .woocommerce-Reviews label .required' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'tab_content_text_color',
			[
				'label' => esc_html__( 'Text font color', 'the7mk2' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-Tabs-panel' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'attribute_value_link_heading',
			[
				'label'     => esc_html__( 'Link', 'the7mk2' ),
				'type'      => Controls_Manager::RAW_HTML,
			]
		);

		$this->start_controls_tabs( 'tabs_style_link' );

		$this->start_controls_tab( 'normal_tabs_link_style',
			[
				'label' => esc_html__( 'Normal', 'the7mk2' ),
			]
		);

			$this->add_control(
				'tab_link_color',
				[
					'label' => esc_html__( 'Color', 'the7mk2' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .woocommerce-Tabs-panel a' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'tab_text_decoration',
				[
					'label' => esc_html__( 'Decoration', 'the7mk2' ),
					'type' => Controls_Manager::SELECT,
					'default' => '',
					'options' => [
						'' => esc_html__( 'Default', 'the7mk2' ),
						'underline' => _x( 'Underline', 'Typography Control', 'the7mk2' ),
						'overline' => _x( 'Overline', 'Typography Control', 'the7mk2' ),
						'line-through' => _x( 'Line Through', 'Typography Control', 'the7mk2' ),
						'none' => _x( 'None', 'Typography Control', 'the7mk2' ),
					],
					'selectors' => [
						'{{WRAPPER}} .woocommerce-Tabs-panel a' => 'text-decoration: {{VALUE}}',
					],
				]
			);

		$this->end_controls_tab();

		$this->start_controls_tab( 'active_tabs_link_style',
			[
				'label' => esc_html__( 'Hover', 'the7mk2' ),
			]
		);

			$this->add_control(
				'active_tab_link_color',
				[
					'label' => esc_html__( 'Color', 'the7mk2' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						' {{WRAPPER}} .woocommerce-Tabs-panel a:hover' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'active_tab_text_decoration',
				[
					'label' => esc_html__( 'Decoration', 'the7mk2' ),
					'type' => Controls_Manager::SELECT,
					'default' => '',
					'options' => [
						'' => esc_html__( 'Default', 'the7mk2' ),
						'underline' => _x( 'Underline', 'Typography Control', 'the7mk2' ),
						'overline' => _x( 'Overline', 'Typography Control', 'the7mk2' ),
						'line-through' => _x( 'Line Through', 'Typography Control', 'the7mk2' ),
						'none' => _x( 'None', 'Typography Control', 'the7mk2' ),
					],
					'selectors' => [
						'{{WRAPPER}} .woocommerce-Tabs-panel a:hover' => 'text-decoration: {{VALUE}}',
					],
				]
			);

		$this->end_controls_tab();

		$this->end_controls_tabs();
		$this->end_controls_section();

		$this->start_controls_section(
			'section_tabs_style',
			[
				'label' => esc_html__( 'Tabs', 'the7mk2' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' => 'tabs_typography',
					'label' => esc_html__( 'Tabs font', 'the7mk2' ),
					'selector' => '.woocommerce {{WRAPPER}} .woocommerce-tabs ul.wc-tabs li a, {{WRAPPER}} .dt-tab-accordion-title',
				]
			);


			$this->start_controls_tabs( 'tabs_style' );



			$this->start_controls_tab( 'normal_tabs_style',
				[
					'label' => esc_html__( 'Normal', 'the7mk2' ),
				]
			);

			$this->add_control(
				'tab_text_color',
				[
					'label' => esc_html__( 'Text Color', 'the7mk2' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'.woocommerce {{WRAPPER}} .woocommerce-tabs ul.wc-tabs li a, {{WRAPPER}} .dt-tab-accordion-title' => 'color: {{VALUE}}',
					],
				]
			);

		$this->end_controls_tab();

		$this->start_controls_tab( 'hover_tabs_style',
				[
					'label' => esc_html__( 'Hover', 'the7mk2' ),
				]
			);

			$this->add_control(
				'tab_hover_text_color',
				[
					'label' => esc_html__( 'Text Color', 'the7mk2' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'.woocommerce {{WRAPPER}} .woocommerce-tabs ul.wc-tabs li:not(.active) a:hover, {{WRAPPER}} .dt-tab-accordion-title:hover' => 'color: {{VALUE}}',
					],
				]
			);

		$this->end_controls_tab();

		$this->start_controls_tab( 'active_tabs_style',
			[
				'label' => esc_html__( 'Active', 'the7mk2' ),
			]
		);

			$this->add_control(
				'active_tab_text_color',
				[
					'label' => esc_html__( 'Text Color', 'the7mk2' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'.woocommerce {{WRAPPER}} .woocommerce-tabs ul.wc-tabs li.active a, {{WRAPPER}} .dt-tab-accordion-title.active' => 'color: {{VALUE}}',
					],
				]
			);



		$this->end_controls_tab();

		$this->end_controls_tabs();


		$this->add_basic_responsive_control(
			'tabs_padding',
			[
				'label'      => esc_html__( 'Paddings, px', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'default'    => [
					'top'      => '20',
					'right'     => '20',
					'bottom'   => '20',
					'left'     => '20',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .woocommerce-tabs  .tabs li > a, {{WRAPPER}} .dt-tab-accordion-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_product_panel_style',
			[
				'label' => esc_html__( 'Description', 'the7mk2' ),
				'tab' => Controls_Manager::TAB_STYLE,

				'condition'   => [
					'show_description' => 'yes',
				],
			]
		);

		$this->add_basic_responsive_control(
			'tabs_content_padding',
			[
				'label'      => esc_html__( 'Description Padding', 'the7mk2' ),
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
					'top'      => '20',
					'right'     => '20',
					'bottom'   => '20',
					'left'     => '20',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .woocommerce-Tabs-panel--description' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',

				],
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_product_info_style',
			[
				'label' => esc_html__( 'Additional information', 'the7mk2' ),
				'tab' => Controls_Manager::TAB_STYLE,

				'condition'   => [
					'show_additional' => 'yes',
				],
			]
		);

		$this->add_basic_responsive_control(
			'tabs_info_padding',
			[
				'label'      => esc_html__( 'Additional Information Padding', 'the7mk2' ),
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
					'top'      => '20',
					'right'     => '20',
					'bottom'   => '20',
					'left'     => '20',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .woocommerce-Tabs-panel--additional_information' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',

				],
			]
		);
		$this->add_basic_responsive_control(
			'align_items',
			[
				'label'                => esc_html__( 'Alignment', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => [
					'left'   => [
						'title' => esc_html__( 'Left', 'the7mk2' ),
						'icon'  => 'eicon-text-align-left',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'the7mk2' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'	=> 'left',
				'selectors'  => [
					'{{WRAPPER}} .woocommerce-product-attributes .woocommerce-product-attributes-item__value' => 'text-align: {{VALUE}};',
				],
			]
		);
		$this->add_basic_responsive_control(
			'space_between',
			[
				'label' => esc_html__( 'Space Between', 'the7mk2' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 10,
				],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 200,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .shop_attributes tr:first-child td, {{WRAPPER}} .shop_attributes tr:first-child th' => 'padding: 5{{UNIT}} 10{{UNIT}} {{SIZE}}{{UNIT}} 5{{UNIT}}',
					'{{WRAPPER}} .shop_attributes tr td, {{WRAPPER}} .shop_attributes tr th' => 'padding: {{SIZE}}{{UNIT}} 10{{UNIT}} {{SIZE}}{{UNIT}} 5{{UNIT}}',
					'{{WRAPPER}} .shop_attributes tr:last-child td, {{WRAPPER}} .shop_attributes tr:last-child th' => 'padding: {{SIZE}}{{UNIT}} 10{{UNIT}} 5{{UNIT}} 5{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'divider',
			[
				'label' => esc_html__( 'Divider', 'the7mk2' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'Off', 'elementor' ),
				'label_on' => esc_html__( 'On', 'elementor' ),
				'default' => 'yes',
				'prefix_class' => 'wc-product-info-',
			]
		);

		$this->add_control(
			'divider_style',
			[
				'label' => esc_html__( 'Style', 'the7mk2' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'solid' => esc_html__( 'Solid', 'the7mk2' ),
					'double' => esc_html__( 'Double', 'the7mk2' ),
					'dotted' => esc_html__( 'Dotted', 'the7mk2' ),
					'dashed' => esc_html__( 'Dashed', 'the7mk2' ),
				],
				'default' => 'solid',
				'condition' => [
					'divider' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .shop_attributes tr:not(:first-child) td, {{WRAPPER}} .shop_attributes tr:not(:first-child) th' => 'border-top-style: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'divider_weight',
			[
				'label' => esc_html__( 'Weight', 'the7mk2' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 1,
				],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 20,
					],
				],
				'condition' => [
					'divider' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .shop_attributes tr:not(:first-child) td, {{WRAPPER}} .shop_attributes tr:not(:first-child) th' => 'border-top-width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'divider_color',
			[
				'label'     => esc_html__( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				//'default'	=> of_get_option( 'dividers-color', '#cccccc' ),
				'condition' => [
					'divider' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .shop_attributes tr:not(:first-child) td, {{WRAPPER}} .shop_attributes tr:not(:first-child) th' => 'border-color: {{VALUE}}',
				],
			]
		);
		$this->add_basic_responsive_control(
			'tabs_info_width',
			[
				'label' => esc_html__( 'Width', 'the7mk2' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
					'size' => '100',
				],
				'range' => [
					'%' => [
						'min' => 10,
						'max' => 100,
					],
					'px' => [
						'min' => 0,
						'max' => 1250,
					],
				],
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .woocommerce-Tabs-panel--additional_information .woocommerce-product-attributes' => 'max-width: 100%; width: {{SIZE}}{{UNIT}}; margin: 0 auto;',
				],
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_product_reviews_style',
			[
				'label' => esc_html__( 'Reviews', 'the7mk2' ),
				'tab' => Controls_Manager::TAB_STYLE,

				'condition'   => [
					'show_reviews' => 'yes',
				],
			]
		);

		$this->add_basic_responsive_control(
			'tabs_reviews_padding',
			[
				'label'      => esc_html__( 'Reviews Padding', 'the7mk2' ),
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
					'top'      => '20',
					'right'     => '20',
					'bottom'   => '20',
					'left'     => '20',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .woocommerce-Tabs-panel--reviews' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',

				],
			]
		);
		$this->add_basic_responsive_control(
			'tabs_reviews_width',
			[
				'label' => esc_html__( 'Width', 'the7mk2' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
					'size' => '100',
				],
				'range' => [
					'%' => [
						'min' => 10,
						'max' => 100,
					],
					'px' => [
						'min' => 0,
						'max' => 1250,
					],
				],
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .woocommerce-Tabs-panel--reviews .woocommerce-Reviews' => 'max-width: 100%; width: {{SIZE}}{{UNIT}}; margin: 0 auto;',
				],
			]
		);

		$text_columns = range( 1, 2 );
		$text_columns = array_combine( $text_columns, $text_columns );
		$text_columns[''] = esc_html__( 'Default', 'the7mk2' );

		$this->add_basic_responsive_control(
			'text_columns',
			[
				'label' => esc_html__( 'Columns', 'the7mk2' ),
				'type' => Controls_Manager::SELECT,
				'options' => $text_columns,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-Reviews' => 'columns: {{VALUE}};',
					'{{WRAPPER}} #reviews ol.commentlist li, {{WRAPPER}} .woocommerce-Reviews > *' => 'break-inside: avoid;'
				],
			]
		);

		$this->add_basic_responsive_control(
			'column_gap',
			[
				'label' => esc_html__( 'Columns Gap', 'the7mk2' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'vw' ],
				'range' => [
					'px' => [
						'max' => 100,
					],
					'%' => [
						'max' => 10,
						'step' => 0.1,
					],
					'vw' => [
						'max' => 10,
						'step' => 0.1,
					],
					'em' => [
						'max' => 10,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .woocommerce-Reviews' => 'column-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();


	}

	protected function render() {
		global $product;

		$product = wc_get_product();
		$settings = $this->get_active_settings();
		$this->print_inline_css();

		if ( empty( $product ) ) {
			return;
		}
		$show_description = '';
		$show_additional = '';
		$show_eviews = '';
		if($settings['show_description'] != 'yes'){
			$show_description = 'hide-tab-description';

		}
		if($settings['show_additional'] != 'yes'){
			$show_additional = 'hide-tab-additional';
		}
		if($settings['show_reviews'] != 'yes'){
			$show_eviews = 'hide-tab-eviews';
		}

		$this->add_render_attribute(
			'the7-elementor-widget',
			'class',
			[
				'the7-elementor-product-' . esc_attr( wc_get_product()->get_type() ),
				'the7-elementor-widget',
				'elementor-widget-tabs',
				'elementor-tabs',
				'dt-tabs-view-' . $settings['type'],
				'dt-tabs-view-tablet-' . $settings['type_tablet'],
				'dt-tabs-view-mobile-' . $settings['type_mobile'],
				$show_description,
				$show_additional,
				$show_eviews,
				$this->get_unique_class(),
			]
		);

		echo '<div ' . $this->get_render_attribute_string( 'the7-elementor-widget' ) . ' >';

		setup_postdata( $product->get_id() );

		//wc_get_template( 'single-product/tabs/tabs.php' );
		$product_tabs = apply_filters( 'woocommerce_product_tabs', array() );


		if ( ! empty( $product_tabs ) ) :?>
		<div class="woocommerce-tabs wc-tabs-wrapper">
			<ul class="tabs wc-tabs" role="tablist">
				<?php foreach ( $product_tabs as $key => $product_tab ) : ?>
					<li class="<?php echo esc_attr( $key ); ?>_tab" id="tab-title-<?php echo esc_attr( $key ); ?>" role="tab" aria-controls="tab-<?php echo esc_attr( $key ); ?>">
						<a href="#tab-<?php echo esc_attr( $key ); ?>">
							<?php echo wp_kses_post( apply_filters( 'woocommerce_product_' . $key . '_tab_title', $product_tab['title'], $key ) ); ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
			<?php
				$tab_count = 0;
				foreach ( $product_tabs as $key => $product_tab) :
					$tab_count++;

					$tab_title_mobile_setting_key = $this->get_repeater_setting_key( 'tab_title_mobile', 'tabs', $tab_count );

					$tab_title_class = [ 'elementor-tab-title', 'dt-tab-accordion-title' ];

					if ( $tab_count === 1 ) {
						$tab_title_class[] = 'active';
					}

					$this->add_render_attribute(
						$tab_title_mobile_setting_key,
						[
							'class'         => $tab_title_class,
							'aria-controls' => 'tab-' . $key,
							'role'          => 'tab',
							'id'            => 'tab-title-' . $key,
						]
					);
			?>
				<div <?php echo $this->get_render_attribute_string( $tab_title_mobile_setting_key ); ?>><?php echo wp_kses_post( apply_filters( 'woocommerce_product_' . $key . '_tab_title', $product_tab['title'], $key ) ); ?></div>

				<div class="woocommerce-Tabs-panel woocommerce-Tabs-panel--<?php echo esc_attr( $key ); ?> panel entry-content wc-tab" id="tab-<?php echo esc_attr( $key ); ?>" role="tabpanel" aria-labelledby="tab-title-<?php echo esc_attr( $key ); ?>">
					<?php
					if ( isset( $product_tab['callback'] ) ) {
						call_user_func( $product_tab['callback'], $key, $product_tab );
					}
					?>
				</div>
			<?php endforeach; ?>

			<?php do_action( 'woocommerce_product_after_tabs' ); ?>
		</div>
		<?php endif; ?>

    </div>
	<?php
	}
	/**
	 * Return shortcode less file absolute path to output inline.
	 *
	 * @return string
	 */
	protected function get_less_file_name() {
		return PRESSCORE_THEME_DIR . '/css/dynamic-less/elementor/the7-product-tabs-widget.less';
	}

	/**
	 * Specify a vars to be inserted in to a less file.
	 */
	protected function less_vars( The7_Elementor_Less_Vars_Decorator_Interface $less_vars ) {
		// For project icon style, see `selectors` in settings declaration.

		$settings = $this->get_settings_for_display();

		$less_vars->add_keyword(
			'unique-shortcode-class-name',
			$this->get_unique_class(),
			'~"%s"'
		);

		$tab_side = array_merge( [ 'size' => 0 ], array_filter( $settings['navigation_width'] ) );
		$tab_side_tablet = array_merge(
			$tab_side,
			$this->unset_empty_value( $settings['navigation_width_tablet'] )
		);
		$tab_side_mobile = array_merge(
			$tab_side_tablet,
			$this->unset_empty_value( $settings['navigation_width_mobile'] )
		);

		$less_vars->add_rgba_color( 'tabs-bg', $settings['tabs_panel_bg_color'] );
		$less_vars->add_pixel_or_percent_number( 'side-tab-width',  $tab_side );
		$less_vars->add_pixel_or_percent_number( 'side-tab-width-tablet',  $tab_side_tablet );
		$less_vars->add_pixel_or_percent_number( 'side-tab-width-mobile',  $tab_side_mobile );

		$less_vars->add_pixel_number( 'tabs-border-width', $settings['tabs_panel_border_width']);
	}

}
