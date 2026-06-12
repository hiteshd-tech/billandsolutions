<?php
/**
 * The7 Elementor widget: Product Sale Flash.
 *
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widgets\Woocommerce;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;
use WC_Product;

defined( 'ABSPATH' ) || exit;

/**
 * Class Product_Sale_flash.
 */
class Product_Sale_flash extends The7_Elementor_Widget_Base {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'the7-woocommerce-product-sale-flash';
	}

	/**
	 * @return string|null
	 */
	protected function the7_title() {
		return esc_html__( 'Product Sale Label', 'the7mk2' );
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
		return [ 'woocommerce', 'shop', 'store', 'price', 'product' ];
	}

	/**
	 * @return string[]
	 */
	public function get_categories() {
		return [ 'woocommerce-elements-single' ];
	}

	/**
	 * Register widget assets.
	 *
	 * @see The7_Elementor_Widget_Base::__construct()
	 */
	protected function register_assets() {
		the7_register_style(
			'the7-woocommerce-product-sale-flash',
			THE7_ELEMENTOR_CSS_URI . '/the7-woocommerce-product-sale-flash.css'
		);
	}

	/**
	 * @return string[]
	 */
	public function get_style_depends() {
		return [ 'the7-woocommerce-product-sale-flash' ];
	}

	/**
	 * @return void
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_onsale',
			[
				'label' => esc_html__( 'Sale Content', 'the7mk2' ),
			]
		);

		$this->add_control(
			'onsale_text',
			[
				'label'       => esc_html__( 'Text', 'the7mk2' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Sale!', 'the7mk2' ),
				'placeholder' => esc_html__( 'Sale!', 'the7mk2' ),
				'description' => esc_html__( 'Use %s to display sale discount', 'the7mk2' ),
			]
		);

		$this->add_responsive_control(
			'onsale_alignment',
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
				'device_args'          => [
					'tablet' => [
						'toggle' => true,
					],
					'mobile' => [
						'toggle' => true,
					],
				],
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
			'onsale_style',
			[
				'label' => esc_html__( 'Sale Flash', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'onsale_background_size_heading',
			[
				'label'     => esc_html__( 'Background Size', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'onsale_height',
			[
				'label'      => esc_html__( 'Height', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .the7-onsale' => 'min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'onsale_width',
			[
				'label'      => esc_html__( 'Width', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .the7-onsale' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'onsale_style_heading',
			[
				'label'     => esc_html__( 'Style', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'onsale_typography',
				'selector' => '{{WRAPPER}} .the7-onsale',
				'exclude'  => [ 'line_height' ],
			]
		);

		$this->add_control(
			'onsale_text_color',
			[
				'label'     => esc_html__( 'Text Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .the7-onsale' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'onsale_text_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .the7-onsale' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'     => 'onsale_border',
				'label'    => esc_html__( 'Border', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .the7-onsale',
			]
		);

		$this->add_control(
			'onsale_border_radius',
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
					'{{WRAPPER}} .the7-onsale' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'onsale_padding',
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
					'{{WRAPPER}} .the7-onsale' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'onsale_shadow',
				'selector' => '{{WRAPPER}} .the7-onsale',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function render() {
		global $post;

		$product = wc_get_product();

		if ( empty( $product ) ) {
			return;
		}

		if ( $product->is_on_sale() || $this->is_edit_mode() ) {
			echo '<div class="the7-onsale">';

			$onsale      = $this->get_settings_for_display( 'onsale_text' );
			$onsale_text = $onsale ? $onsale : esc_html__( 'Sale!', 'the7mk2' );
			if ( strpos( $onsale_text, '%s' ) !== false ) {
				$onsale_text = sprintf( $onsale_text, $this->add_percentage_to_sale_badge( $product ) );
			}

			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo apply_filters( 'woocommerce_sale_flash', '<span class="onsale-text">' . esc_html( $onsale_text ) . '</span>', $post, $product );

			echo '</div>';
		}
	}

	/**
	 * @param WC_Product $product Product object.
	 *
	 * @return string
	 */
	protected function add_percentage_to_sale_badge( $product ) {
		if ( $product->is_type( 'variable' ) ) {
			$percentages = [];
			$percentage  = '';

			// Get all variation prices.
			$prices = $product->get_variation_prices();

			// Loop through variation prices.
			foreach ( $prices['price'] as $key => $price ) {
				// Only on sale variations.
				if ( $prices['regular_price'][ $key ] !== $price ) {
					// Calculate and set in the array the percentage for each variation on sale.
					$percentages[] = round( 100 - ( $prices['sale_price'][ $key ] / $prices['regular_price'][ $key ] * 100 ) );
				}
			}
			if ( $percentages ) {
				$percentage = max( $percentages ) . '%';
			}
		} else {
			$regular_price = (float) $product->get_regular_price();
			$sale_price    = (float) $product->get_sale_price();

			$percentage = round( 100 - ( $sale_price / $regular_price * 100 ) ) . '%';
		}

		return $percentage;
	}
}