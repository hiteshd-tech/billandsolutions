<?php
/**
 * The7 Elementor widget: Product Out Of Stock Label.
 *
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widgets\Woocommerce;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Class Product_Sale_flash.
 */
class Product_Out_Of_Stock_label extends The7_Elementor_Widget_Base {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'the7-woocommerce-product-out-of-stock-label';
	}

	/**
	 * @return string|null
	 */
	protected function the7_title() {
		return esc_html__( 'Product Out Of Stock Label', 'the7mk2' );
	}

	/**
	 * @return string
	 */
	protected function the7_icon() {
		return 'eicon-woocommerce';
	}

	/**
	 * @return string[]
	 */
	protected function the7_keywords() {
		return [ 'woocommerce', 'shop', 'store', 'out of stock', 'product' ];
	}

	/**
	 * @return string[]
	 */
	public function get_categories() {
		return [ 'woocommerce-elements-single' ];
	}

	/**
	 * @return void
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_out_of_stock',
			[
				'label' => esc_html__( 'Out Of Stock Content', 'the7mk2' ),
			]
		);

		$this->add_control(
			'out_of_stock_text',
			[
				'label'       => esc_html__( 'Text', 'the7mk2' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Out Of Stock', 'the7mk2' ),
				'placeholder' => esc_html__( 'Out Of Stock', 'the7mk2' ),
			]
		);

		$this->add_responsive_control(
			'out_of_stock_alignment',
			[
				'label'                => esc_html__( 'Alignment', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'default'              => 'left',
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
				'toggle'               => false,
				'device_args'          => $this->generate_device_args(
					[
						'toggle' => true,
					]
				),
				'selectors_dictionary' => [
					'left'   => 'display: flex; text-align: left; justify-content: flex-start; --content-align: flex-start;',
					'center' => 'display: flex; text-align: center; justify-content: center; --content-align: center;',
					'right'  => 'display: flex; text-align: right; justify-content: flex-end; --content-align: flex-end;',
				],
				'selectors'            => [
					'{{WRAPPER}} .elementor-widget-container' => '{{VALUE}}',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'out_of_stock_style',
			[
				'label' => esc_html__( 'Out Of Stock Label', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'out_of_stock_background_size_heading',
			[
				'label'     => esc_html__( 'Background Size', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'out_of_stock_height',
			[
				'label'      => esc_html__( 'Height', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .the7-out-of-stock' => 'min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'out_of_stock_width',
			[
				'label'      => esc_html__( 'Width', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .the7-out-of-stock' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'out_of_stock_style_heading',
			[
				'label'     => esc_html__( 'Style', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'out_of_stock_typography',
				'selector' => '{{WRAPPER}} .the7-out-of-stock',
				'exclude'  => [ 'line_height' ],
			]
		);

		$this->add_control(
			'out_of_stock_text_color',
			[
				'label'     => esc_html__( 'Text Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .the7-out-of-stock' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'out_of_stock_text_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .the7-out-of-stock' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'out_of_stock_border',
				'label'    => esc_html__( 'Border', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .the7-out-of-stock',
			]
		);

		$this->add_control(
			'out_of_stock_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default'    => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .the7-out-of-stock' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'out_of_stock_padding',
			[
				'label'      => esc_html__( 'Padding', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default'    => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .the7-out-of-stock' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'out_of_stock_shadow',
				'selector' => '{{WRAPPER}} .the7-out-of-stock',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function render() {
		$product = wc_get_product();

		if ( empty( $product ) ) {
			return;
		}

		if ( ! $product->is_in_stock() || $this->is_edit_mode() ) {
			echo '<div class="the7-out-of-stock">';

			$out_of_stock      = $this->get_settings_for_display( 'out_of_stock_text' );
			$out_of_stock_text = $out_of_stock ? $out_of_stock : __( 'Sale!', 'the7mk2' );

			echo '<span class="out-stock-label">' . esc_html( $out_of_stock_text ) . '</span>';

			echo '</div>';
		}
	}
}
