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

class Product_Price extends The7_Elementor_Widget_Base {

	public function get_name() {
		return 'the7-woocommerce-product-price';
	}

	protected function the7_title() {
		return esc_html__( 'Product Price', 'the7mk2' );
	}

	protected function the7_icon() {
		return 'eicon-product-price';
	}

	protected function the7_keywords() {
		return [ 'woocommerce', 'shop', 'store', 'price', 'product' ];
	}

	public function get_categories() {
		return [ 'woocommerce-elements-single' ];
	}

	public function get_style_depends() {
		return [ 'the7-woocommerce-product-price-widget' ];
	}

	protected function register_controls() {

		// Price Style
		$this->start_controls_section(
			'price_style',
			[
				'label' => esc_html__( 'Price', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'text_align',
			[
				'label'                => esc_html__( 'Alignment', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => [
					'left'   => [
						'title' => esc_html__( 'Left', 'the7mk2' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'the7mk2' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'the7mk2' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors_dictionary' => [
					'left'   => 'justify-content: flex-start; text-align: left;',
					'center' => 'justify-content: center; text-align: center;',
					'right'  => 'justify-content: flex-end; text-align: right;',
				],
				'selectors'            => [
					'{{WRAPPER}} .price' => ' {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'normal_price_heading',
			[
				'type'  => \Elementor\Controls_Manager::HEADING,
				'label' => esc_html__( 'Normal price', 'the7mk2' ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'price_typography',
				'label'    => esc_html__( 'Normal Price Typography', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .price, {{WRAPPER}} .price > span.woocommerce-Price-amount.amount, {{WRAPPER}} .price > span.woocommerce-Price-amount span',
			]
		);

		$this->add_control(
			'normal_price_text_color',
			[
				'label'     => esc_html__( 'Normal Price Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .price, {{WRAPPER}} .price > span.woocommerce-Price-amount.amount, {{WRAPPER}} .price > span.woocommerce-Price-amount span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'sale_price_heading',
			[
				'type'      => \Elementor\Controls_Manager::HEADING,
				'label'     => esc_html__( 'Sale Price', 'the7mk2' ),
				'separator' => 'before',
			]
		);
		$this->add_control(
			'sale_price_position',
			[
				'label'                => esc_html__( 'Old Price Position', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'default'              => is_rtl() ? 'right' : 'left',
				'options'              => [
					'left'  => [
						'title' => esc_html__( 'Start', 'the7mk2' ),
						'icon'  => 'eicon-h-align-left',
					],
					'right' => [
						'title' => esc_html__( 'End', 'the7mk2' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'toggle'               => false,
				'selectors_dictionary' => [
					'right' => 'order: 2;',
					'left'  => 'order: 0;',
				],
				'selectors'            => [
					'{{WRAPPER}} .price del' => ' {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'sale_price_typography',
				'label'    => esc_html__( 'Old Price Typography', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .price del, {{WRAPPER}} .price del span',
			]
		);

		$this->add_control(
			'sale_price_text_color',
			[
				'label'     => esc_html__( 'Old Price Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .price del span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'old_price_line_color',
			[
				'label'     => esc_html__( 'Old Price Line Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .price del' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'sale_new_price_typography',
				'label'    => esc_html__( 'New Price Typography', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .price ins span',
			]
		);

		$this->add_control(
			'sale_new_price_text_color',
			[
				'label'     => esc_html__( 'New Price Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .price ins span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'price_block',
			[
				'label'        => esc_html__( 'Stacked', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'prefix_class' => 'elementor-product-price-block-',
			]
		);

		$this->add_responsive_control(
			'sale_price_spacing',
			[
				'label'      => esc_html__( 'Spacing', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range'      => [
					'em' => [
						'min'  => 0,
						'max'  => 5,
						'step' => 0.1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}}' => '--sale-price-spacing: {{SIZE}}{{UNIT}}',
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

		wc_get_template( '/single-product/price.php' );
	}
}
