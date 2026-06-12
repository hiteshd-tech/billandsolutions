<?php
/*
 * The7 elements product info widget for Elementor.
 *
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widgets\Woocommerce;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;

defined( 'ABSPATH' ) || exit;

class Product_Additional_Information extends The7_Elementor_Widget_Base {

	public function get_name() {
		return 'the7-woocommerce-product-additional-information';
	}

	protected function the7_title() {
		return esc_html__( 'Additional Information', 'the7mk2' );
	}

	protected function the7_icon() {
		return 'eicon-product-info';
	}

	protected function the7_keywords() {
		return [ 'woocommerce', 'shop', 'store', 'info', 'data', 'product'];
	}

	public function get_categories() {
		return [ 'woocommerce-elements-single' ];
	}

	public function get_style_depends() {
		return [ 'the7-woocommerce-product-additional-information-widget' ];
	}

	protected function register_controls() {
		$this->start_controls_section( 'section_product_attribute_title_style', [
			'label' => esc_html__( 'Attribute title', 'the7mk2' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );


		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .woocommerce-product-attributes .woocommerce-product-attributes-item__label' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .woocommerce-product-attributes .woocommerce-product-attributes-item__label',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section( 'section_product_attribute_value_style', [
			'label' => esc_html__( 'Attribute value', 'the7mk2' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_control(
			'attribute_color',
			[
				'label'     => esc_html__( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .woocommerce-product-attributes .woocommerce-product-attributes-item__value' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'attribute_typography',
				'selector' => '{{WRAPPER}} .woocommerce-product-attributes .woocommerce-product-attributes-item__value',
			]
		);

		$this->add_responsive_control(
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

		$this->add_control(
			'attribute_value_link_heading',
			[
				'type'      	=> \Elementor\Controls_Manager::RAW_HTML,
				'label'     	=> esc_html__( 'Link', 'the7mk2' ),
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
				'label' => esc_html__( 'Color', 'the7mk2' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-product-attributes .woocommerce-product-attributes-item__value a' => 'color: {{VALUE}}',
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
					'{{WRAPPER}} .woocommerce-product-attributes .woocommerce-product-attributes-item__value a' => 'text-decoration: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'active_tabs_style',
			[
				'label' => esc_html__( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			'active_tab_text_color',
			[
				'label' => esc_html__( 'Color', 'the7mk2' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-product-attributes .woocommerce-product-attributes-item__value a:hover' => 'color: {{VALUE}}',
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
					'{{WRAPPER}} .woocommerce-product-attributes .woocommerce-product-attributes-item__value a:hover' => 'text-decoration: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section( 'section_product_attribute_list_style', [
			'label' => esc_html__( 'List', 'the7mk2' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_responsive_control(
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
					'{{WRAPPER}}.wc-product-info-top-border-yes tr:first-child th, {{WRAPPER}}.wc-product-info-top-border-yes tr:first-child td' => 'padding-top: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}}.wc-product-info-bottom-border-yes tr:last-child th, {{WRAPPER}}.wc-product-info-bottom-border-yes tr:last-child td' => 'padding-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'divider',
			[
				'label' => esc_html__( 'Dividers', 'the7mk2' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'Off', 'the7mk2' ),
				'label_on' => esc_html__( 'On', 'the7mk2' ),
				'default' => 'yes',
				'separator' => 'before',
				'prefix_class' => 'wc-product-info-',
			]
		);

		$this->add_control(
			'top_divider',
			[
				'label' => esc_html__( 'Top Divider', 'the7mk2' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'Off', 'the7mk2' ),
				'label_on' => esc_html__( 'On', 'the7mk2' ),
				'default' => 'no',
				'prefix_class' => 'wc-product-info-top-border-',
				'condition' => [
					'divider' => 'yes',
				],
			]
		);

		$this->add_control(
			'bottm_divider',
			[
				'label' => esc_html__( 'Bottom Divider', 'the7mk2' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'Off', 'the7mk2' ),
				'label_on' => esc_html__( 'On', 'the7mk2' ),
				'default' => 'no',
				'prefix_class' => 'wc-product-info-bottom-border-',
				'condition' => [
					'divider' => 'yes',
				],
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
					'{{WRAPPER}}.wc-product-info-top-border-yes .shop_attributes tr:first-child th, {{WRAPPER}}.wc-product-info-top-border-yes .shop_attributes tr:first-child td' => 'border-top-style: {{VALUE}}',
					'{{WRAPPER}}.wc-product-info-bottom-border-yes .shop_attributes tr:last-child th, {{WRAPPER}}.wc-product-info-bottom-border-yes .shop_attributes tr:last-child td' => 'border-bottom-style: {{VALUE}}',
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
					'{{WRAPPER}}.wc-product-info-top-border-yes tr:first-child th, {{WRAPPER}}.wc-product-info-top-border-yes tr:first-child td' => 'border-top-width: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}}.wc-product-info-bottom-border-yes tr:last-child th, {{WRAPPER}}.wc-product-info-bottom-border-yes tr:last-child td' => 'border-bottom-width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'divider_color',
			[
				'label'     => esc_html__( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'	=> '',
				'condition' => [
					'divider' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .shop_attributes tr:not(:first-child) td, {{WRAPPER}} .shop_attributes tr:not(:first-child) th' => 'border-color: {{VALUE}}',
					'{{WRAPPER}}.wc-product-info-top-border-yes tr:first-child th, {{WRAPPER}}.wc-product-info-top-border-yes tr:first-child td' => 'border-color: {{VALUE}}',
					'{{WRAPPER}}.wc-product-info-bottom-border-yes tr:last-child th, {{WRAPPER}}.wc-product-info-bottom-border-yes tr:last-child td' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		global $product;

		$product = wc_get_product();

		if ( empty( $product ) ) {
			return;
		}

		$this->print_inline_css();

		wc_display_product_attributes( $product );
	}
}
