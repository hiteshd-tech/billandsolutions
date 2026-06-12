<?php

namespace The7\Mods\Compatibility\Elementor\Widgets\Woocommerce;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Plugin as Elementor;
use The7\Mods\Compatibility\Elementor\Modules\Woocommerce_Cart\Module as WoocommerceCartModule;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Abstract_Template;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Button;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Image_Aspect_Ratio;

/**
 * Menu cart widget class.
 */
class Cart_Preview extends The7_Elementor_Widget_Base {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'the7-woocommerce-cart-preview';
	}

	public function get_categories() {
		return [ 'theme-elements', 'woocommerce-elements' ];
	}

	public function get_script_depends() {
		return [ 'the7-woocommerce-e-cart', 'wc-cart-fragments' ];
	}

	public function get_style_depends() {
		return [ 'the7-woocommerce-e-cart' ];
	}

	/**
	 * @return string|void
	 */
	protected function the7_title() {
		return esc_html__( 'Mini Cart', 'the7mk2' );
	}

	/**
	 * @return string
	 */
	protected function the7_icon() {
		return 'eicon-cart';
	}

	/**
	 * @return string[]
	 */
	protected function the7_keywords() {
		return [ 'woocommerce', 'shop', 'store', 'cart', 'product', 'filter', 'price' ];
	}

	/**
	 * @return void
	 */
	protected function register_assets() {
		the7_register_style( 'the7-woocommerce-e-cart', THE7_ELEMENTOR_CSS_URI . '/the7-woocommerce-cart.css' );
		the7_register_script_in_footer( 'the7-woocommerce-e-cart', THE7_ELEMENTOR_JS_URI . '/the7-woocommerce-cart.js', [ 'the7-elementor-frontend-common' ] );
	}

	protected function register_controls() {
		// Content Tab.
		$this->add_general_controls();
		$this->add_widget_title_controls();
		$this->add_filled_cart_controls();
		$this->add_empty_cart_controls();

		// styles tab
		$this->add_widget_title_styles();
		$this->add_product_box_styles();
		$this->add_thumbnail_styles();
		$this->add_product_content_styles();
		$this->add_quantity_change_style_controls();

		$this->add_remove_icon_styles();
		$this->add_divider_styles();
		$this->add_subtotal_styles();

		$this->add_buttons_general_styles();

		$this->add_button_styles( 'shop_button_', '.the7-e-button-shop ', esc_html__( 'Continue Shopping Button', 'the7mk2' ), [ 'shop_button' => 'yes' ] );
		$this->add_button_styles( 'view_cart_button_', '.the7-e-button-view-cart ', esc_html__( 'View Cart Button', 'the7mk2' ), [ 'view_cart_button' => 'yes' ] );
		$this->add_button_styles( 'checkout_button_', '.the7-e-button-checkout ', esc_html__( 'Checkout Button', 'the7mk2' ), [ 'checkout_button' => 'yes' ] );

		$this->add_empty_cart_box_styles();
		$this->add_empty_cart_icon_styles();
		$this->add_empty_cart_text_styles();
		$this->add_empty_cart_button_styles();
		$this->add_scroll_styles();
	}

	protected function add_general_controls() {
		$this->start_controls_section( 'widget_general_section', [
			'label' => esc_html__( 'General', 'the7mk2' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
		] );

		//add simplified wrapper identificator to the widget main class. Mostly used for general css
		$this->add_control( 'wrap_helper', [
			'type'         => Controls_Manager::HIDDEN,
			'default'      => 'the7-e-woo-cart-wrapper',
			'prefix_class' => '',
		] );

		$this->add_responsive_control( 'widget_min_height', [
			'label'       => esc_html__( 'Min Height', 'the7mk2' ),
			'description' => esc_html__( 'Leave this field empty to fit screen height', 'the7mk2' ),
			'type'        => Controls_Manager::SLIDER,
			'size_units'  => [ 'px', 'vh' ],
			'selectors'   => [
				'{{WRAPPER}}' => '--min-height: {{SIZE}}{{UNIT}}',
			]
		] );

		$this->add_responsive_control( 'widget_max_height', [
			'label'       => esc_html__( 'Max Height', 'the7mk2' ),
			'description' => esc_html__( 'Leave this field empty to fit screen height', 'the7mk2' ),
			'type'        => Controls_Manager::SLIDER,
			'size_units'  => [ 'px', 'vh' ],
			'selectors'   => [
				'{{WRAPPER}}' => '--max-height: {{SIZE}}{{UNIT}}',
			]
		] );

		$this->end_controls_section();
	}

	protected function add_widget_title_controls() {
		$this->start_controls_section( 'widget_title_section', [
			'label' => esc_html__( 'Title', 'the7mk2' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
		] );

		$this->add_responsive_control( 'widget_title_visibility', [
			'label'                => esc_html__( 'Visibility', 'the7mk2' ),
			'type'                 => Controls_Manager::SELECT,
			'default'              => 'always',
			'options'              => [
				''       => esc_html__( 'Disabled', 'the7mk2' ),
				'always' => esc_html__( 'Always', 'the7mk2' ),
				'filled' => esc_html__( 'Filled Cart', 'the7mk2' ),
				'empty'  => esc_html__( 'Empty Cart', 'the7mk2' ),
			],
			'selectors'            => [
				'{{WRAPPER}}' => '{{VALUE}}',
			],
			'selectors_dictionary' => [
				'always' => '--title-visibility-filled: flex; --title-visibility-empty: flex;',
				'filled' => '--title-visibility-filled: flex;',
				'empty'  => '--title-visibility-empty: flex;',
			],
            'render_type' => 'template',
		] );

		$this->add_control( 'widget_title_text', [
			'label'     => esc_html__( 'Widget Title', 'the7mk2' ),
			'type'      => Controls_Manager::TEXT,
			'default'   => esc_html__( 'Shopping cart', 'the7mk2' ),
			'condition' => [
				'widget_title_visibility!' => '',
			],
		] );

		$this->end_controls_section();
	}

	protected function add_filled_cart_controls() {
		$this->start_controls_section( 'filled_cart_section', [
			'label' => esc_html__( 'Filled Cart', 'the7mk2' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
		] );
		$this->add_control( 'product_image', [
			'label'        => esc_html__( 'Product Image', 'the7mk2' ),
			'type'         => Controls_Manager::SWITCHER,
			'label_on'     => esc_html__( 'On', 'the7mk2' ),
			'label_off'    => esc_html__( 'Off', 'the7mk2' ),
			'return_value' => 'yes',
			'default'      => 'yes',
			'selectors'    => [
				'{{WRAPPER}} .the7-e-mini-cart-product > .product-thumbnail' => 'display: flex;',
			],
		] );

		$this->add_control( 'remove_icon', [
			'label'            => esc_html__( 'Remove Product Icon', 'the7mk2' ),
			'type'             => Controls_Manager::ICONS,
			'fa4compatibility' => 'icon',
			'default'          => [
				'value'   => 'fas fa-times',
				'library' => 'fa-solid',
			],
			'label_block'      => false,
			'skin'             => 'inline',
		] );

		$this->add_control( 'product_remove_helper', [
			'label'        => '',
			'type'         => Controls_Manager::HIDDEN,
			'default'      => 'y',
			'return_value' => 'y',
			'selectors'    => [
				'{{WRAPPER}} .the7-e-mini-cart-product  > .product-remove' => 'display: block;',
			],
			'condition'    => [
				'remove_icon[value]!' => '',
			],
		] );

		$this->add_control( 'product_quantity_change', [
			'label'        => esc_html__( 'Product Quantity Change', 'the7mk2' ),
			'type'         => Controls_Manager::SWITCHER,
			'label_on'     => esc_html__( 'On', 'the7mk2' ),
			'label_off'    => esc_html__( 'Off', 'the7mk2' ),
			'return_value' => 'yes',
			'default'      => 'yes',
			'separator'    => 'before',
			'selectors'    => [
				'{{WRAPPER}} .the7-e-mini-cart-product .product-quantity' => 'display: flex;',
			],
		] );

		$this->add_control( 'product_quantity_change_position', [
			'label'                => esc_html__( 'Position', 'the7mk2' ),
			'type'                 => Controls_Manager::SELECT,
			'default'              => 'after',
			'options'              => [
				'before'  => esc_html__( 'Before Quantity & Price', 'the7mk2' ),
				'after'   => esc_html__( 'After Quantity & Price', 'the7mk2' ),
				'instead' => esc_html__( 'Instead Of Quantity', 'the7mk2' ),
			],
			'condition'            => [
				'product_quantity_change' => 'yes',
			],
			'selectors'            => [
				'{{WRAPPER}} .the7-e-mini-cart-product .product-price' => '{{VALUE}}',
			],
			'selectors_dictionary' => [
				'before'  => 'flex-direction: column; --product-quantity-change-margin: var(--quantity-change-spacing) 0 0 0; --product-price-quantity-margin: var(--product-price-quantity-spacing) 0 0 0;',
				'after'   => 'flex-direction: column-reverse; --product-quantity-change-margin: var(--quantity-change-spacing) 0 0 0; --product-price-quantity-margin: var(--product-price-quantity-spacing) 0 0 0;',
				'instead' => 'align-items: center; flex-direction: row; --product-item-quantity-display: none; --product-quantity-change-margin: 0 var(--quantity-change-spacing) 0 0; --product-price-margin: var(--product-price-quantity-spacing) 0 0 0; --product-price-quantity-margin:0;',
			],
		] );


		$this->add_control( 'subtotal', [
			'label'        => esc_html__( 'Subtotal', 'the7mk2' ),
			'type'         => Controls_Manager::SWITCHER,
			'label_on'     => esc_html__( 'On', 'the7mk2' ),
			'label_off'    => esc_html__( 'Off', 'the7mk2' ),
			'return_value' => 'yes',
			'default'      => 'yes',
			'render_type'  => 'template',
			'separator'    => 'before',
			'selectors'    => [
				'{{WRAPPER}} .the7-e-mini-cart-footer .total' => 'display: flex;',
			],
		] );

		$this->add_control( 'subtotal_text', [
			'label'     => esc_html__( 'Subtotal Title', 'woocommerce' ),
			'type'      => Controls_Manager::TEXT,
			'default'   => esc_html__( 'Subtotal', 'woocommerce' ),
			'condition' => [
				'subtotal' => 'yes',
			],
		] );

		$this->add_control( 'shop_button', [
			'label'        => esc_html__( 'Continue Shopping Button', 'the7mk2' ),
			'type'         => Controls_Manager::SWITCHER,
			'label_on'     => esc_html__( 'On', 'the7mk2' ),
			'label_off'    => esc_html__( 'Off', 'the7mk2' ),
			'return_value' => 'yes',
			'default'      => 'yes',
			'render_type'  => 'template',
			'separator'    => 'before',
			'selectors'    => [
				'{{WRAPPER}} .the7-e-mini-cart-footer .the7-e-button-shop' => 'display: flex;',
			],
		] );

		$this->add_control( 'shop_button_text', [
			'label'     => esc_html__( 'Button Name', 'the7mk2' ),
			'type'      => Controls_Manager::TEXT,
			'default'   => esc_html__( 'Continue shopping', 'woocommerce' ),
			'condition' => [
				'shop_button' => 'yes',
			],
		] );

		$this->add_control( 'shop_button_link', [
			'label'       => esc_html__( 'Link', 'the7mk2' ),
			'type'        => Controls_Manager::URL,
			'dynamic'     => [
				'active' => true,
			],
			'condition'   => [
				'shop_button' => 'yes',
			],
			'placeholder' => esc_html__( 'https://your-link.com', 'the7mk2' ),
		] );

		$this->add_control( 'view_cart_button', [
			'label'        => esc_html__( 'View Cart Button', 'the7mk2' ),
			'type'         => Controls_Manager::SWITCHER,
			'label_on'     => esc_html__( 'On', 'the7mk2' ),
			'label_off'    => esc_html__( 'Off', 'the7mk2' ),
			'return_value' => 'yes',
			'default'      => 'yes',
			'render_type'  => 'template',
			'separator'    => 'before',
			'selectors'    => [
				'{{WRAPPER}} .the7-e-mini-cart-footer .the7-e-button-view-cart' => 'display: flex;',
			],
		] );

		$this->add_control( 'view_cart_button_text', [
			'label'     => esc_html__( 'Button Name', 'the7mk2' ),
			'type'      => Controls_Manager::TEXT,
			'default'   => esc_html__( 'View cart', 'woocommerce' ),
			'condition' => [
				'view_cart_button' => 'yes',
			],
		] );

		$this->add_control( 'checkout_button', [
			'label'        => esc_html__( 'Checkout Button', 'the7mk2' ),
			'type'         => Controls_Manager::SWITCHER,
			'label_on'     => esc_html__( 'On', 'the7mk2' ),
			'label_off'    => esc_html__( 'Off', 'the7mk2' ),
			'return_value' => 'yes',
			'default'      => 'yes',
			'render_type'  => 'template',
			'separator'    => 'before',
			'selectors'    => [
				'{{WRAPPER}} .the7-e-mini-cart-footer .the7-e-button-checkout' => 'display: flex;',
			],
		] );

		$this->add_control( 'checkout_button_text', [
			'label'     => esc_html__( 'Checkout Text', 'the7mk2' ),
			'type'      => Controls_Manager::TEXT,
			'default'   => esc_html__( 'Checkout', 'woocommerce' ),
			'condition' => [
				'checkout_button' => 'yes',
			],
		] );

		$this->add_control( 'divider', [
			'label'        => esc_html__( 'Dividers', 'the7mk2' ),
			'type'         => Controls_Manager::SWITCHER,
			'label_off'    => esc_html__( 'Off', 'the7mk2' ),
			'label_on'     => esc_html__( 'On', 'the7mk2' ),
			'return_value' => 'yes',
			'empty_value'  => 'no',
			'default'      => 'yes',
			'selectors'    => [
				'{{WRAPPER}} .item-divider:not(:first-child):not(:last-child)' => 'display:flex;',
			],
			'prefix_class' => 'widget-divider-',
			'separator'    => 'before',
		] );

		$this->add_responsive_control( 'products_gap', [
			'label'      => esc_html__( 'Space Between Products', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [
				'px' => [
					'max' => 200,
				],
			],
			'default'    => [
				'unit' => 'px',
				'size' => '40',
			],
			'separator'  => 'before',
			'selectors'  => [
				'{{WRAPPER}}.widget-divider-yes .the7-e-woo-cart-content > .the7-e-mini-cart-product:not(:first-child):not(:last-child)' => 'margin-top: calc({{SIZE}}{{UNIT}}/2); margin-bottom: calc({{SIZE}}{{UNIT}}/2);',
				'{{WRAPPER}}:not(.widget-divider-yes) .the7-e-woo-cart-content > .the7-e-mini-cart-product:not(:first-child)'            => 'margin-top: {{SIZE}}{{UNIT}};',
				'{{WRAPPER}}:not(.widget-divider-yes) .the7-e-woo-cart-content > div:last-of-type'                                       => 'margin-bottom: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->end_controls_section();
	}

	protected function add_empty_cart_controls() {
		$this->start_controls_section( 'empty_cart_section', [
			'label' => esc_html__( 'Empty Cart', 'the7mk2' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
		] );

		$this->add_control( 'empty_cart_icon', [
			'label'            => esc_html__( 'Empty Cart Icon', 'the7mk2' ),
			'type'             => Controls_Manager::ICONS,
			'fa4compatibility' => 'icon',
			'default'          => [
				'value'   => 'fas fa-shopping-cart',
				'library' => 'fa-solid',
			],
			'label_block'      => false,
			'skin'             => 'inline',
		] );
		$this->add_control( 'empty_cart_text', [
			'label'   => esc_html__( 'Empty Cart Text', 'the7mk2' ),
			'type'    => Controls_Manager::TEXT,
			'default' => esc_html__( 'No products in the cart.', 'the7mk2' ),
		] );


		$this->add_control( 'empty_cart_button', [
			'label'        => esc_html__( 'Return To Shop Button', 'the7mk2' ),
			'type'         => Controls_Manager::SWITCHER,
			'label_on'     => esc_html__( 'On', 'the7mk2' ),
			'label_off'    => esc_html__( 'Off', 'the7mk2' ),
			'return_value' => 'yes',
			'default'      => 'yes',
			'render_type'  => 'template',
			'selectors'    => [
				'{{WRAPPER}} .the7-e-empty-cart-button-shop' => 'display: flex;',
			],
		] );

		$this->add_control( 'empty_cart_button_shop_text', [
			'label'     => esc_html__( 'Button Text', 'the7mk2' ),
			'type'      => Controls_Manager::TEXT,
			'default'   => esc_html__( 'Return to shop', 'the7mk2' ),
			'condition' => [
				'empty_cart_button' => 'yes',
			],
		] );


		$this->add_control( 'empty_cart_button_link', [
			'label'       => esc_html__( 'Link', 'the7mk2' ),
			'type'        => Controls_Manager::URL,
			'dynamic'     => [
				'active' => true,
			],
			'condition'   => [
				'empty_cart_button' => 'yes',
			],
			'placeholder' => esc_html( 'https://your-link.com' ),
		] );


		$this->end_controls_section();
	}

	protected function add_widget_title_styles() {
		$this->start_controls_section( 'section_widget_title_style', [
			'label'     => esc_html__( 'Title', 'the7mk2' ),
			'tab'       => Controls_Manager::TAB_STYLE,
			'condition' => [
				'widget_title_visibility!' => '',
			],
		] );
		$selector = '{{WRAPPER}} .title-text';
		$this->add_responsive_control( 'widget_title_align', [
			'label'                => esc_html__( 'Horizontal Alignment', 'the7mk2' ),
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
				'left'  => 'flex-start;',
				'right' => 'flex-end;',
			],
			'selectors'            => [
				$selector => 'justify-content: {{VALUE}};',
			],
		] );


		$this->add_responsive_control( 'widget_title_vertical_align', [
			'label'                => esc_html__( 'Vertical Alignment', 'the7mk2' ),
			'type'                 => Controls_Manager::CHOOSE,
			'options'              => [
				'top'    => [
					'title' => esc_html__( 'Top', 'the7mk2' ),
					'icon'  => 'eicon-v-align-top',
				],
				'center' => [
					'title' => esc_html__( 'Middle', 'the7mk2' ),
					'icon'  => 'eicon-v-align-middle',
				],
				'bottom' => [
					'title' => esc_html__( 'Bottom', 'the7mk2' ),
					'icon'  => 'eicon-v-align-bottom',
				],
			],
			'default'              => 'top',
			'selectors_dictionary' => [
				'top'    => 'flex-start',
				'bottom' => 'flex-end',
			],
			'selectors'            => [
				$selector => 'align-items: {{VALUE}};',
			],
		] );

		$this->add_responsive_control( 'widget_title_height', [
			'label'      => esc_html__( 'Min Height', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'default'    => [
				'unit' => 'px',
				'size' => '',
			],
			'size_units' => [ 'px', 'vh' ],
			'range'      => [
				'px' => [
					'min' => 1,
					'max' => 500,
				],
				'vh' => [
					'min' => 1,
					'max' => 100,
				],
			],
			'selectors'  => [
				$selector => 'min-height: {{SIZE}}{{UNIT}};',
			],
		] );


		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'widget_title_typography',
			'selector' => $selector,
		] );

		$this->add_control( 'widget_title_color', [
			'label'     => esc_html__( 'Font Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'alpha'     => true,
			'default'   => '',
			'selectors' => [
				$selector => 'color: {{VALUE}}',
			],
		] );

		$this->add_responsive_control( 'widget_title_padding', [
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
		] );

		$this->add_responsive_control( 'widget_title_margin', [
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
				'top'      => '0',
				'right'    => '0',
				'bottom'   => '15',
				'left'     => '0',
				'unit'     => 'px',
				'isLinked' => false,
			],
			'selectors'  => [
				$selector => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
			],
		] );

		$this->add_responsive_control( 'widget_title_border_radius', [
			'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors'  => [
				$selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		] );

		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => 'widget_title_border',
			'label'    => esc_html__( 'Border', 'the7mk2' ),
			'selector' => $selector,
		] );

		$this->add_control( 'widget_title_bg_color', [
			'label'     => esc_html__( 'Background Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				$selector => 'background: {{VALUE}};',
			],
		] );

		$this->end_controls_section();
	}

	protected function add_product_box_styles() {
		$this->start_controls_section( 'section_product_box_style', [
			'label' => esc_html__( 'Product box', 'the7mk2' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$selector = '{{WRAPPER}} .the7-e-mini-cart-product';
		$this->add_responsive_control( 'product_box_padding', [
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

		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => 'product_box_border',
			'label'    => esc_html__( 'Border', 'the7mk2' ),
			'selector' => $selector,
			'exclude'  => [
				'color',
			],
		] );

		$this->add_responsive_control( 'product_box_border_radius', [
			'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors'  => [
				$selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		] );

		$this->start_controls_tabs( 'product_box_style' );

		$this->start_controls_tab( 'product_box_normal', [
			'label' => esc_html__( 'Normal', 'the7mk2' ),
		] );

		$this->add_control( 'product_box_bg_color', [
			'label'     => esc_html__( 'Background Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				$selector => 'background: {{VALUE}};',
			],
		] );

		$this->add_control( 'product_box_border_color', [
			'label'     => esc_html__( 'Border Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				$selector => 'border-color: {{VALUE}}',
			],
		] );

		$this->end_controls_tab();

		$this->start_controls_tab( 'product_box_hover', [
			'label' => esc_html__( 'Hover', 'the7mk2' ),
		] );

		$this->add_control( 'product_box_bg_hover_color', [
			'label'     => esc_html__( 'Background Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				$selector . ':hover' => 'background: {{VALUE}};',
			],
		] );

		$this->add_control( 'product_box_hover_border_color', [
			'label'     => esc_html__( 'Border Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				$selector . ':hover' => 'border-color: {{VALUE}}',
			],
		] );

		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
	}

	protected function add_thumbnail_styles() {
		$this->start_controls_section( 'section_thumbnail_style', [
			'label'     => esc_html__( 'Image', 'the7mk2' ),
			'tab'       => Controls_Manager::TAB_STYLE,
			'condition' => [
				'product_image' => 'yes',
			],
		] );

		$selector = '{{WRAPPER}} .the7-e-mini-cart-product > .product-thumbnail';

		$this->add_control( 'thumbnail_position', [
			'label'                => esc_html__( 'Position', 'the7mk2' ),
			'type'                 => Controls_Manager::CHOOSE,
			'options'              => [
				'left'  => [
					'title' => esc_html__( 'Left', 'the7mk2' ),
					'icon'  => 'eicon-h-align-left',
				],
				'right' => [
					'title' => esc_html__( 'Right', 'the7mk2' ),
					'icon'  => 'eicon-h-align-right',
				],
			],
			'default'              => 'left',
			'toggle'               => false,
			'selectors'            => [
				$selector => '{{VALUE}}',
			],
			'selectors_dictionary' => [
				'right' => 'order: 1; --thumbnail-margin:0 0 0 var(--thumbnail-spacing, 15px);',
				'left'  => 'order: 0; --thumbnail-margin:0 var(--thumbnail-spacing, 15px) 0 0;',
			],
		] );

		/**
		 * @see \The7\Mods\Compatibility\Elementor\Widget_Templates\Image_Aspect_Ratio::get_wrapper_class()
		 */
		$image_selector = $selector . ' > .img-css-resize-wrapper';

		$this->add_responsive_control( 'image_size', [
			'label'      => esc_html__( 'Size', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px', '%' ],
			'default'    => [
				'unit' => 'px',
				'size' => 80,
			],
			'range'      => [
				'px' => [
					'min' => 5,
					'max' => 1030,
				],
				'%'  => [
					'min' => 0,
					'max' => 100,
				],
			],
			'selectors'  => [
				$selector => '--image-size: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->template( Image_Aspect_Ratio::class )->add_style_controls();

		$this->add_responsive_control( 'thumbnail_border_width', [
			'label'      => esc_html__( 'Border Width', 'the7mk2' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%', 'em' ],
			'selectors'  => [
				$image_selector => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; border-style:solid;',
			],
		] );

		$this->add_control( 'thumbnail_border_radius', [
			'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors'  => [
				$image_selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		] );

		$this->add_control( 'thumbnail_border_color', [
			'label'     => esc_html__( 'Border Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [
				$image_selector => 'border-color: {{VALUE}};',
			],
		] );

		$this->add_responsive_control( 'thumbnail_spacing', [
			'label'      => esc_html__( 'Spacing', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'default'    => [
				'unit' => 'px',
				'size' => 20,
			],
			'size_units' => [ 'px' ],
			'range'      => [
				'px' => [
					'min'  => 0,
					'max'  => 200,
					'step' => 1,
				],
			],

			'selectors' => [
				$selector => '--thumbnail-spacing: {{SIZE}}{{UNIT}}',
			],
		] );

		$this->end_controls_section();
	}

	protected function add_product_content_styles() {
		$this->start_controls_section( 'section_roduct_content_style', [
			'label' => esc_html__( 'Product Content', 'the7mk2' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_title_styles();
		$this->add_price_styles();
		$this->add_variation_styles();

		$this->end_controls_section();
	}

	protected function add_title_styles() {

		$this->add_control( 'title_heading', [
			'type'  => Controls_Manager::HEADING,
			'label' => esc_html__( 'Product Title', 'the7mk2' ),
		] );

		$selector = '{{WRAPPER}} .the7-e-mini-cart-product .cart-info .product-name';

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'product_title_typography',
			'selector' => $selector,
			'label'    => esc_html__( 'Title Typography', 'the7mk2' ),
		] );

		$this->start_controls_tabs( 'product_title_colors' );

		$this->start_controls_tab( 'product_title_normal_colors', [ 'label' => esc_html__( 'Normal', 'the7mk2' ) ] );

		$this->add_control( 'product_title_text_color', [
			'label'     => esc_html__( 'Text Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				$selector . ' a' => 'color: {{VALUE}};',
			],
		] );

		$this->end_controls_tab();

		$this->start_controls_tab( 'product_title_hover_colors', [ 'label' => esc_html__( 'Hover', 'the7mk2' ) ] );

		$this->add_control( 'product_title_hover_text_color', [
			'label'     => esc_html__( 'Text Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				$selector . ':hover a' => 'color: {{VALUE}};',
			],
		] );
		$this->end_controls_tab();
		$this->end_controls_tabs();
	}

	protected function add_price_styles() {
		$this->add_control( 'price_heading', [
			'type'      => Controls_Manager::HEADING,
			'label'     => esc_html__( 'Product Price & Quantity', 'the7mk2' ),
			'separator' => 'before',
		] );


		$this->add_product_price_sub_styles();
		$this->add_product_quantity_sub_styles();

		$this->add_responsive_control( 'product_price_gap', [
			'label'      => esc_html__( 'Spacing', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [
				'px' => [
					'min'  => 0,
					'max'  => 200,
					'step' => 1,
				],
			],
			'default'    => [
				'unit' => 'px',
				'size' => '10',
			],
			'selectors'  => [
				'{{WRAPPER}} .the7-e-mini-cart-product .product-price' => '--product-price-quantity-spacing: {{SIZE}}{{UNIT}}',
			],
		] );
	}

	protected function add_product_price_sub_styles() {

		$selector = '{{WRAPPER}} .the7-e-mini-cart-product .cart-info .product-price > .quantity .amount';

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'product_price_typography',
			'selector' => $selector,
			'label'    => esc_html__( 'Price Typography', 'the7mk2' ),
		] );

		$this->add_control( 'product_price_text_color', [
			'label'     => esc_html__( 'Price Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				$selector => 'color: {{VALUE}};',
			],
		] );
	}

	protected function add_product_quantity_sub_styles() {

		$selector = '{{WRAPPER}} .the7-e-mini-cart-product .cart-info .product-price > .quantity .product-item-quantity,
		 {{WRAPPER}}  .the7-e-mini-cart-product .cart-info .product-price > .quantity .quantity-separator';

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'product_quantity_typography',
			'selector' => $selector,
			'label'    => esc_html__( 'Quantity Typography', 'the7mk2' ),
		] );

		$this->add_control( 'product_quantity_text_color', [
			'label'     => esc_html__( 'Quantity Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				$selector => 'color: {{VALUE}};',
			],
		] );
	}

	protected function add_variation_styles() {
		$this->add_control( 'variation_heading', [
			'type'      => Controls_Manager::HEADING,
			'label'     => esc_html__( 'Variations', 'the7mk2' ),
			'separator' => 'before',
		] );

		$selector = '{{WRAPPER}} .the7-e-mini-cart-product .variation';

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'product_variation_typography',
			'selector' => $selector,
		] );

		$this->add_control( 'product_variation_text_color', [
			'label'     => esc_html__( 'Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				$selector => 'color: {{VALUE}};',
			],
		] );

		$this->add_responsive_control( 'product_variation_gap', [
			'label'      => esc_html__( 'Spacing', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'default'    => [
				'unit' => 'px',
				'size' => '10',
			],
			'range'      => [
				'px' => [
					'min'  => 0,
					'max'  => 200,
					'step' => 1,
				],
			],
			'selectors'  => [
				$selector => 'margin-top: {{SIZE}}{{UNIT}};',
			],
		] );
	}

	protected function add_quantity_change_style_controls() {
		$this->start_controls_section( 'section_quantity_change_style', [
			'label'     => esc_html__( 'Product Quantity Change', 'the7mk2' ),
			'tab'       => Controls_Manager::TAB_STYLE,
			'condition' => [
				'product_quantity_change' => 'yes',
			],
		] );

		$selector = '{{WRAPPER}} .the7-e-mini-cart-product .product-quantity .quantity';

		$this->add_responsive_control( 'quantity_change_spacing', [
			'label'      => esc_html__( 'Spacing', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px', 'em' ],
			'default'    => [
				'unit' => 'px',
				'size' => '10',
			],
			'selectors'  => [
				'{{WRAPPER}} .the7-e-mini-cart-product .product-price' => '--quantity-change-spacing: {{SIZE}}{{UNIT}}',
			],
			'separator'  => 'after',
		] );

		$this->add_control( 'quantity_change_heading', [
			'type'  => Controls_Manager::HEADING,
			'label' => esc_html__( 'Box & Number', 'the7mk2' ),
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'quantity_change_typography',
			'selector' => $selector . ' .qty',
		] );

		$this->add_responsive_control( 'quantity_change_min_width', [
			'label'      => esc_html__( 'Min Width', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [
				'px' => [
					'min' => 0,
					'max' => 200,
				],
			],
			'default'    => [
				'unit' => 'px',
				'size' => '60',
			],
			'selectors'  => [
				$selector => 'min-width: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->add_responsive_control( 'quantity_change_min_height', [
			'label'      => esc_html__( 'Min Height', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [
				'px' => [
					'min' => 0,
					'max' => 200,
				],
			],
			'selectors'  => [
				$selector => 'min-height: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->add_responsive_control( 'quantity_change_paddings', [
			'label'     => esc_html__( 'Padding', 'the7mk2' ),
			'type'      => Controls_Manager::DIMENSIONS,
			'selectors' => [
				$selector => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		] );

		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'           => 'quantity_change_border',
			'fields_options' => [
				'width' => [
					'selectors' => [
						'{{SELECTOR}}' => '--the7-top-input-border-width: {{TOP}}{{UNIT}}; --the7-right-input-border-width: {{RIGHT}}{{UNIT}}; --the7-bottom-input-border-width: {{BOTTOM}}{{UNIT}}; --the7-left-input-border-width: {{LEFT}}{{UNIT}}; border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				],
			],
			'selector'       => $selector,
			'exclude'        => [ 'color' ],
		] );

		$this->add_responsive_control( 'quantity_change_border_radius', [
			'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors'  => [
				$selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		] );

		$this->add_control( 'quantity_change_text_color', [
			'label'     => esc_html__( 'Number Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				$selector . ' .qty' => 'color: {{VALUE}}',
			],
		] );

		$this->add_control( 'quantity_change_bg_color', [
			'label'     => esc_html__( 'Background', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				$selector => 'background: {{VALUE}}',
			],
		] );

		$this->add_control( 'quantity_change_border_color', [
			'label'     => esc_html__( 'Border Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				$selector => 'border-color: {{VALUE}}',
			],
		] );

		$this->add_group_control( Group_Control_Box_Shadow::get_type(), [
			'name'     => 'quantity_change_box_shadow',
			'label'    => esc_html__( 'Box Shadow', 'the7mk2' ),
			'selector' => $selector,
		] );

		$this->add_control( 'quantity_change_button_heading', [
			'type'      => Controls_Manager::HEADING,
			'label'     => esc_html__( '+/- Settings', 'the7mk2' ),
			'separator' => 'before',
		] );

		$this->add_responsive_control( 'quantity_change_button_icon_width', [
			'label'      => esc_html__( 'Icon Size', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [
				'px' => [
					'min' => 0,
					'max' => 200,
				],
			],
			'default'    => [
				'unit' => 'px',
				'size' => '14',
			],
			'selectors'  => [
				$selector . ' button' => 'font-size: {{SIZE}}{{UNIT}};',
				$selector . ' button svg' => 'width: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->add_responsive_control( 'quantity_change_button_width', [
			'label'      => esc_html__( 'Background Width', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'default'    => [
				'unit' => 'px',
				'size' => '20',
			],
			'range'      => [
				'px' => [
					'min' => 0,
					'max' => 200,
				],
			],
			'selectors'  => [
				$selector              => '--quantity-btn-width: {{SIZE}}{{UNIT}};',
				$selector . ' button' => 'width: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->add_responsive_control( 'quantity_change_button_height', [
			'label'      => esc_html__( 'Background Height', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'default'    => [
				'unit' => 'px',
				'size' => '20',
			],
			'range'      => [
				'px' => [
					'min' => 0,
					'max' => 200,
				],
			],
			'selectors'  => [
				$selector              => '--quantity-btn-height: {{SIZE}}{{UNIT}};',
				$selector . ' button' => 'height: {{SIZE}}{{UNIT}} !important;',
			],
		] );

		$this->add_responsive_control( 'quantity_change_button_border', [
			'label'      => esc_html__( 'Border Width', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [
				'px' => [
					'min' => 0,
					'max' => 200,
				],
			],
			'selectors'  => [
				$selector => '--quantity-btn-border-width: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->add_responsive_control( 'quantity_change_button_border_radius', [
			'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors'  => [
				$selector . ' button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
			],
		] );

		$this->start_controls_tabs( 'quantity_change_style_tabs' );

		$this->start_controls_tab( 'quantity_change_style_normal', [
			'label' => esc_html__( 'Normal', 'the7mk2' ),
		] );

		$this->add_control( 'quantity_change_btn_color', [
			'label'     => esc_html__( '+/- Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				$selector . ' button' => 'color: {{VALUE}}',
				$selector . ' button svg' => 'fill: {{VALUE}}; color: {{VALUE}}',
			],
		] );

		$this->add_control( 'quantity_change_bg_btn', [
			'label'     => esc_html__( '+/- Background', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'#the7-body ' . $selector . ' button.is-form' => 'background: {{VALUE}}',
			],
		] );

		$this->add_control( 'quantity_change_btn_border_color', [
			'label'     => esc_html__( '+/- Border Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				$selector => '--quantity-btn-border-color: {{VALUE}}',
			],
		] );

		$this->add_group_control( Group_Control_Box_Shadow::get_type(), [
			'name'           => 'quantity_change_btn_box_shadow',
			'label'          => esc_html__( '+/- Box Shadow', 'the7mk2' ),
			'selector'       => $selector . ' button',
			'fields_options' => [
				'box_shadow' => [
					'selectors' => [
						'{{SELECTOR}}' => 'box-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{SPREAD}}px {{COLOR}} {{box_shadow_position.VALUE}} !important;',
					],
				],
			],
		] );

		$this->end_controls_tab();

		$this->start_controls_tab( 'quantity_change_style_hover', [
			'label' => esc_html__( 'Hover', 'the7mk2' ),
		] );

		$this->add_control( 'quantity_change_btn_color_focus', [
			'label'     => esc_html__( '+/- Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				$selector . ' button:hover' => 'color: {{VALUE}}',
				$selector . ' button:hover svg' => 'fill: {{VALUE}}; color: {{VALUE}}',
			],
		] );

		$this->add_control( 'quantity_change_bg_btn_focus', [
			'label'     => esc_html__( '+/- Background', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'#the7-body ' . $selector . ' button.is-form:hover' => 'background: {{VALUE}}',
			],
		] );

		$this->add_control( 'quantity_change_border_color_focus', [
			'label'     => esc_html__( '+/- Border Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				$selector => '--quantity-btn-border-hover-color: {{VALUE}}',
			],
		] );

		$this->add_group_control( Group_Control_Box_Shadow::get_type(), [
			'name'           => 'quantity_change_box_shadow_focus',
			'label'          => esc_html__( '+/- Box Shadow', 'the7mk2' ),
			'selector'       => $selector . ' button:hover',
			'fields_options' => [
				'box_shadow' => [
					'selectors' => [
						'{{SELECTOR}}' => 'box-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{SPREAD}}px {{COLOR}} {{box_shadow_position.VALUE}} !important;',
					],
				],
			],
		] );
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
	}

	protected function add_remove_icon_styles() {
		$this->start_controls_section( 'section_remove_icon_style', [
			'label'     => esc_html__( 'Remove Product Icon', 'the7mk2' ),
			'tab'       => Controls_Manager::TAB_STYLE,
			'condition' => [
				'remove_icon[value]!' => '',
			],
		] );

		$selector = '{{WRAPPER}} .the7-e-mini-cart-product .product-remove > .remove_from_cart_button';

		$this->add_control( 'remove_icon_horizontal_position', [
			'label'                => esc_html__( 'Horizontal Position', 'the7mk2' ),
			'type'                 => Controls_Manager::CHOOSE,
			'options'              => [
				'left'  => [
					'title' => esc_html__( 'Left', 'the7mk2' ),
					'icon'  => 'eicon-h-align-left',
				],
				'right' => [
					'title' => esc_html__( 'Right', 'the7mk2' ),
					'icon'  => 'eicon-h-align-right',
				],
			],
			'selectors'            => [
				$selector => '{{VALUE}}',
			],
			'selectors_dictionary' => [
				'left'  => 'right: auto; left: 0; margin-left: var(--zoom-h-offset,0);',
				'right' => 'left: auto; right: 0; margin-right: var(--zoom-h-offset,0);',
			],
			'default'              => 'left',
			'toggle'               => false,
		] );

		$this->add_responsive_control( 'remove_icon_horizontal_distance', [
			'label'      => esc_html__( 'Offset', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px', 'em' ],
			'range'      => [
				'px' => [
					'min' => - 200,
					'max' => 200,
				],
				'em' => [
					'min' => - 5,
					'max' => 5,
				],
			],
			'default'    => [
				'unit' => 'px',
				'size' => '-5',
			],
			'selectors'  => [
				$selector => '--zoom-h-offset: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->add_control( 'remove_icon_vertical_position', [
			'label'                => esc_html__( 'Vertical Position', 'the7mk2' ),
			'type'                 => Controls_Manager::CHOOSE,
			'options'              => [
				'top'    => [
					'title' => esc_html__( 'Top', 'the7mk2' ),
					'icon'  => 'eicon-v-align-top',
				],
				'bottom' => [
					'title' => esc_html__( 'Bottom', 'the7mk2' ),
					'icon'  => 'eicon-v-align-bottom',
				],
			],
			'selectors'            => [
				$selector => '{{VALUE}}',
			],
			'selectors_dictionary' => [
				'top'    => 'bottom: auto; top: 0; margin-top: var(--zoom-v-offset,0);',
				'bottom' => 'top: auto; bottom: 0; margin-bottom: var(--zoom-v-offset,0);',
			],
			'default'              => 'top',
			'toggle'               => false,
		] );

		$this->add_responsive_control( 'remove_icon_vertical_distance', [
			'label'      => esc_html__( 'Offset', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px', 'em' ],
			'default'    => [
				'unit' => 'px',
				'size' => '-5',
			],
			'range'      => [
				'px' => [
					'min' => - 200,
					'max' => 200,
				],
				'em' => [
					'min' => - 5,
					'max' => 5,
				],
			],
			'selectors'  => [
				$selector => '--zoom-v-offset: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->add_responsive_control( 'remove_icon_icon_size', [
			'label'      => esc_html__( 'Icon Size', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'default'    => [
				'unit' => 'px',
				'size' => '12',
			],
			'size_units' => [ 'px' ],
			'range'      => [
				'px' => [
					'min'  => 0,
					'max'  => 200,
					'step' => 1,
				],
			],
			'selectors'  => [
				$selector . ' .the7_template_icon_remove'     => 'font-size: {{SIZE}}{{UNIT}}; line-height: 1; width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				$selector . ' .the7_template_icon_remove svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
			],
		] );


		$this->add_control( 'remove_icon_padding', [
			'label'      => esc_html__( 'Icon Padding', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px', 'em' ],
			'range'      => [
				'px' => [
					'min' => 0,
					'max' => 50,
				],
			],
			'default'    => [
				'unit' => 'em',
				'size' => '0.3',
			],
			'selectors'  => [
				$selector => 'padding: {{SIZE}}{{UNIT}}',
			],
		] );

		$this->add_responsive_control( 'remove_icon_border_radius', [
			'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'default'    => [
				'top'      => '100',
				'right'    => '100',
				'bottom'   => '100',
				'left'     => '100',
				'unit'     => '%',
				'isLinked' => true,
			],
			'selectors'  => [
				$selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		] );

		$this->add_responsive_control( 'remove_icon_border_width', [
			'label'      => esc_html__( 'Border Width', 'the7mk2' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors'  => [
				$selector => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; border-style:solid;',
			],
		] );

		$this->start_controls_tabs( 'remove_icon_style' );

		$this->start_controls_tab( 'remove_icon_normal', [
			'label' => esc_html__( 'Normal', 'the7mk2' ),
		] );

		$this->add_control( 'remove_icon_icon_color', [
			'label'     => esc_html__( 'Icon Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				$selector . ' i'   => 'color: {{VALUE}};',
				$selector . ' svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
			],
		] );

		$this->add_control( 'remove_icon_bg_color', [
			'label'     => esc_html__( 'Background Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				$selector => 'background: {{VALUE}};',
			],
		] );

		$this->add_control( 'remove_icon_border_color', [
			'label'     => esc_html__( 'Border Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				$selector => 'border-color: {{VALUE}}',
			],
		] );

		$this->end_controls_tab();

		$this->start_controls_tab( 'remove_icon_hover', [
			'label' => esc_html__( 'Hover', 'the7mk2' ),
		] );

		$this->add_control( 'remove_icon_hover_icon_color', [
			'label'     => esc_html__( 'Icon Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				$selector . ':hover i'   => 'color: {{VALUE}};',
				$selector . ':hover svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
			],
		] );

		$this->add_control( 'remove_icon_hover_bg_color', [
			'label'     => esc_html__( 'Background Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				$selector . ':hover' => 'background: {{VALUE}};',
			],
		] );

		$this->add_control( 'remove_icon_hover_border_color', [
			'label'     => esc_html__( 'Border Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				$selector . ':hover' => 'border-color: {{VALUE}}',
			],
		] );

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function add_divider_styles() {
		$this->start_controls_section( 'section_divider_style', [
			'label'     => esc_html__( 'Divider', 'the7mk2' ),
			'tab'       => Controls_Manager::TAB_STYLE,
			'condition' => [
				'divider' => 'yes',
			],
		] );

		$selector = '{{WRAPPER}} .item-divider';

		$this->add_control( 'divider_style', [
			'label'     => esc_html__( 'Style', 'the7mk2' ),
			'type'      => Controls_Manager::SELECT,
			'options'   => [
				'solid'  => esc_html__( 'Solid', 'the7mk2' ),
				'double' => esc_html__( 'Double', 'the7mk2' ),
				'dotted' => esc_html__( 'Dotted', 'the7mk2' ),
				'dashed' => esc_html__( 'Dashed', 'the7mk2' ),
			],
			'default'   => 'solid',
			'condition' => [
				'divider' => 'yes',
			],
			'selectors' => [
				'{{WRAPPER}} .the7-e-woo-cart-content > .item-divider:after' => 'border-top-style: {{VALUE}}',
			],
		] );

		$this->add_responsive_control( 'divider_thickness', [
			'label'     => esc_html__( 'Thickness', 'the7mk2' ),
			'type'      => Controls_Manager::SLIDER,
			'default'   => [
				'size' => 1,
			],
			'range'     => [
				'px' => [
					'min' => 1,
					'max' => 200,
				],
			],
			'condition' => [
				'divider' => 'yes',
			],
			'selectors' => [
				'{{WRAPPER}} .the7-e-woo-cart-content > .item-divider:after' => 'border-top-width: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->add_responsive_control( 'divider_length', [
			'label'      => esc_html__( 'Length', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px', '%' ],
			'range'      => [
				'px' => [
					'min' => 1,
					'max' => 2000,
				],
			],
			'condition'  => [
				'divider' => 'yes',
			],
			'selectors'  => [
				'{{WRAPPER}} .the7-e-woo-cart-content > .item-divider:after' => 'width: {{SIZE}}{{UNIT}};',
			],
		] );

		/*$this->add_control(
			'display_first_divider',
			[
				'label'        => esc_html__( 'First Divider', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the7mk2' ),
				'label_off'    => esc_html__( 'Hide', 'the7mk2' ),
				'condition'    => [
					'divider' => 'yes',
				],
				'return_value' => 'yes',
				'empty_value'  => 'no',
				'prefix_class' => 'widget-divider-first-',
				'selectors'    => [
					$selector . ':first-child' => 'display:flex',
				],
			]
		);

		$this->add_control(
			'display_last_divider',
			[
				'label'        => esc_html__( 'Last Divider', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the7mk2' ),
				'label_off'    => esc_html__( 'Hide', 'the7mk2' ),
				'condition'    => [
					'divider' => 'yes',
				],
				'return_value' => 'yes',
				'empty_value'  => 'no',
				'prefix_class' => 'widget-divider-last-',
				'selectors'    => [
					$selector . ':last-child' => 'display:flex',
				],
			]
		);*/

		$this->add_control( 'divider_color', [
			'label'     => esc_html__( 'Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'condition' => [
				'divider' => 'yes',
			],
			'selectors' => [
				$selector . ':after' => 'border-color: {{VALUE}}',
			],
		] );

		$this->end_controls_section();
	}

	protected function add_subtotal_styles() {
		$this->start_controls_section( 'section_subtotal_style', [
			'label'     => esc_html__( 'Subtotal', 'the7mk2' ),
			'tab'       => Controls_Manager::TAB_STYLE,
			'condition' => [
				'subtotal' => 'yes',
			],
		] );

		$selector = '{{WRAPPER}} .the7-e-mini-cart-footer .total';

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'subtotal_typography',
			'selector' => $selector,
			'label'    => esc_html__( 'Title Typography', 'the7mk2' ),
		] );

		$this->add_control( 'subtotal_text_color', [
			'label'     => esc_html__( 'Title Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				$selector => 'color: {{VALUE}};',
			],
		] );

		$num_selector = '{{WRAPPER}} .the7-e-mini-cart-footer .total .amount';

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'subtotal_number_typography',
			'selector' => $num_selector,
			'label'    => esc_html__( 'Number Typography', 'the7mk2' ),
		] );

		$this->add_control( 'subtotal_number_text_color', [
			'label'     => esc_html__( 'Number Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				$num_selector => 'color: {{VALUE}};',
			],
		] );

		$this->add_responsive_control( 'subtotal_number_align', [
			'label'                => esc_html__( 'Alignment', 'the7mk2' ),
			'type'                 => Controls_Manager::CHOOSE,
			'default'              => 'stretch',
			'options'              => [
				'left'    => [
					'title' => esc_html__( 'Left', 'the7mk2' ),
					'icon'  => 'eicon-h-align-left',
				],
				'center'  => [
					'title' => esc_html__( 'Center', 'the7mk2' ),
					'icon'  => 'eicon-h-align-center',
				],
				'right'   => [
					'title' => esc_html__( 'Right', 'the7mk2' ),
					'icon'  => 'eicon-h-align-right',
				],
				'stretch' => [
					'title' => esc_html__( 'Space Between', 'the7mk2' ),
					'icon'  => 'eicon-h-align-stretch',
				],
			],
			'selectors_dictionary' => [
				'left'    => 'flex-start',
				'right'   => 'flex-end',
				'stretch' => 'space-between',
			],
			'selectors'            => [
				$selector => 'justify-content:{{VALUE}};',
			],
		] );


		$this->add_responsive_control( 'subtotal_padding', [
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
			'default'    => [
				'top'      => '20',
				'right'    => '20',
				'bottom'   => '20',
				'left'     => '20',
				'unit'     => 'px',
				'isLinked' => true,
			],
			'selectors'  => [
				$selector => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
			],
		] );

		$this->add_responsive_control( 'subtotal_margin', [
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
				'top'      => '20',
				'right'    => '0',
				'bottom'   => '20',
				'left'     => '0',
				'unit'     => 'px',
				'isLinked' => false,
			],
			'selectors'  => [
				$selector => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
			],
		] );

		$this->add_responsive_control( 'subtotal_box_border_radius', [
			'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors'  => [
				$selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		] );

		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => 'subtotal_box_border',
			'label'    => esc_html__( 'Border', 'the7mk2' ),
			'selector' => $selector,
		] );

		$this->add_control( 'subtotal_bg_color', [
			'label'     => esc_html__( 'Background Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				$selector => 'background: {{VALUE}};',
			],
		] );

		$this->end_controls_section();
	}

	protected function add_buttons_general_styles() {
		$this->start_controls_section( 'section_button_general_style', [
			'label'      => esc_html__( 'Buttons', 'the7mk2' ),
			'tab'        => Controls_Manager::TAB_STYLE,
			'conditions' => [
				'relation' => 'or',
				'terms'    => [
					[
						'name'     => 'shop_button',
						'operator' => '==',
						'value'    => 'yes',
					],
					[
						'name'     => 'view_cart_button',
						'operator' => '==',
						'value'    => 'yes',
					],
					[
						'name'     => 'checkout_button',
						'operator' => '==',
						'value'    => 'yes',
					],
				],
			],

		] );

		$selector = '{{WRAPPER}} .woocommerce-mini-cart__buttons';
		$selector_wrap = '{{WRAPPER}} .woocommerce-mini-cart__buttons_wrapper';

		$this->add_responsive_control( 'buttons_column_gap', [
			'label'     => esc_html__( 'Columns Gap', 'the7mk2' ),
			'type'      => Controls_Manager::SLIDER,
			'default'   => [
				'size' => 20,
			],
			'range'     => [
				'px' => [
					'min' => 0,
					'max' => 60,
				],
			],
			'selectors' => [
				$selector . ' .the7-e-wc-button-wrap' => 'padding-right: calc( {{SIZE}}{{UNIT}}/2 ); padding-left: calc( {{SIZE}}{{UNIT}}/2 );',
				$selector                             => 'margin-left: calc( -{{SIZE}}{{UNIT}}/2 ); margin-right: calc( -{{SIZE}}{{UNIT}}/2 );',
			],
		] );

		$this->add_responsive_control( 'buttons_row_gap', [
			'label'     => esc_html__( 'Rows Gap', 'the7mk2' ),
			'type'      => Controls_Manager::SLIDER,
			'default'   => [
				'size' => 20,
			],
			'range'     => [
				'px' => [
					'min' => 0,
					'max' => 60,
				],
			],
			'selectors' => [
				$selector . ' .the7-e-wc-button-wrap' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				$selector                             => 'margin-bottom: -{{SIZE}}{{UNIT}};',
			],
		] );

		$this->add_responsive_control( 'buttons_align', [
			'label'                => esc_html__( 'Alignment', 'the7mk2' ),
			'type'                 => Controls_Manager::CHOOSE,
			'options'              => [
				'left'    => [
					'title' => esc_html__( 'Left', 'the7mk2' ),
					'icon'  => 'eicon-h-align-left',
				],
				'center'  => [
					'title' => esc_html__( 'Center', 'the7mk2' ),
					'icon'  => 'eicon-h-align-center',
				],
				'right'   => [
					'title' => esc_html__( 'Right', 'the7mk2' ),
					'icon'  => 'eicon-h-align-right',
				],
				'stretch' => [
					'title' => esc_html__( 'Space Between', 'the7mk2' ),
					'icon'  => 'eicon-h-align-stretch',
				],
			],
			'default'              => 'stretch',
			'selectors_dictionary' => [
				'left'    => 'flex-start',
				'right'   => 'flex-end',
				'stretch' => 'space-between',
			],
			'selectors'            => [
				$selector => 'justify-content: {{VALUE}};',
			],
		] );


		$this->add_responsive_control( 'buttons_padding', [
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
				$selector_wrap => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
			],
		] );

		$this->add_responsive_control( 'buttons_margin', [
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
				$selector_wrap => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
			],
		] );

		$this->add_responsive_control( 'buttons_border_radius', [
			'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors'  => [
				$selector_wrap => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		] );

		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => 'buttons_title_border',
			'label'    => esc_html__( 'Border', 'the7mk2' ),
			'selector' => $selector_wrap,
		] );

		$this->add_control( 'buttons_bg_color', [
			'label'     => esc_html__( 'Background Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				$selector_wrap => 'background: {{VALUE}};',
			],
		] );

		$this->end_controls_section();
	}

	protected function add_button_styles( $prefix, $selector_prefix, $label, $condition = [] ) {
		$this->template( Button::class )->add_style_controls( Button::ICON_MANAGER, $condition, [
			$prefix . 'gap_above_button'  => null,
			$prefix . 'button_size'       => [
				'label'          => esc_html__( 'Size', 'the7mk2' ),
				'type'           => Controls_Manager::SELECT,
				'default'        => 'md',
				'options'        => The7_Elementor_Widget_Base::get_button_sizes(),
				'style_transfer' => true,
			],
			$prefix . 'button_min_width'  => [
				'label'        => esc_html__( 'Min Width', 'the7mk2' ),
				'type'         => Controls_Manager::SLIDER,
				'size_units'   => [ 'px' ],
				'selectors'    => [
					'{{WRAPPER}} ' . $selector_prefix . '.box-button' => 'min-width: {{SIZE}}{{UNIT}};',
				],
				'separator'    => 'before',
				'control_type' => Abstract_Template::CONTROL_TYPE_RESPONSIVE,
			],
			$prefix . 'button_min_height' => [
				'label'        => esc_html__( 'Min Height', 'the7mk2' ),
				'type'         => Controls_Manager::SLIDER,
				'selectors'    => [
					'{{WRAPPER}} ' . $selector_prefix . '.box-button' => 'min-height: {{SIZE}}{{UNIT}};',
				],
				'control_type' => Abstract_Template::CONTROL_TYPE_RESPONSIVE,
			],
		], $prefix, $selector_prefix, $label );

		$selector = '{{WRAPPER}} ' . $selector_prefix;

		$this->start_injection( [
			'of' => $prefix . 'button_size',
			'at' => 'before',
		] );

		$default_col_width = '100';
		if ( $prefix == 'shop_button_' || $prefix == 'view_cart_button_' ) {
			$default_col_width = '50';
		}

		$this->add_responsive_control( $prefix . 'button_column_width', [
			'label'     => esc_html__( 'Column Width', 'the7mk2' ),
			'type'      => Controls_Manager::SELECT,
			'options'   => [
				''    => esc_html__( 'Default', 'the7mk2' ),
				'100' => '100%',
				'80'  => '80%',
				'75'  => '75%',
				'70'  => '70%',
				'66'  => '66%',
				'60'  => '60%',
				'50'  => '50%',
				'40'  => '40%',
				'33'  => '33%',
				'30'  => '30%',
				'25'  => '25%',
				'20'  => '20%',
			],
			'default'   => $default_col_width,
			'selectors' => [
				$selector => 'width: {{VALUE}}%',
			],
		] );

		$this->add_responsive_control( $prefix . 'button_align', [
			'label'                => esc_html__( 'Alignment', 'the7mk2' ),
			'type'                 => Controls_Manager::CHOOSE,
			'options'              => [
				'left'    => [
					'title' => esc_html__( 'Left', 'the7mk2' ),
					'icon'  => 'eicon-h-align-left',
				],
				'center'  => [
					'title' => esc_html__( 'Center', 'the7mk2' ),
					'icon'  => 'eicon-h-align-center',
				],
				'right'   => [
					'title' => esc_html__( 'Right', 'the7mk2' ),
					'icon'  => 'eicon-h-align-right',
				],
				'stretch' => [
					'title' => esc_html__( 'Stretch', 'the7mk2' ),
					'icon'  => 'eicon-h-align-stretch',
				],
			],
			'selectors_dictionary' => [
				'left'  => 'flex-start',
				'right' => 'flex-end',
			],
			'selectors'            => [
				$selector . '.box-button' => 'align-self: {{VALUE}};',
			],
			'condition'            => [
				$prefix . 'button_column_width[value]!' => '',
			],
		] );

		$this->end_injection();
	}

	protected function add_empty_cart_box_styles() {
		$this->start_controls_section( 'section_empty_cart_box_style', [
			'label'     => esc_html__( 'Empty Cart Box', 'the7mk2' ),
			'tab'       => Controls_Manager::TAB_STYLE,
			'condition' => [
				'empty_cart_icon[value]!' => '',
			],
		] );

		$selector = '{{WRAPPER}} .the7-e-woo-cart-empty-cart';

		$this->add_responsive_control( 'empty_cart_box_margin', [
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

		$this->end_controls_section();
	}

	protected function add_empty_cart_icon_styles() {
		$this->start_controls_section( 'section_empty_cart_icon_style', [
			'label'     => esc_html__( 'Empty Cart Icon', 'the7mk2' ),
			'tab'       => Controls_Manager::TAB_STYLE,
			'condition' => [
				'empty_cart_icon[value]!' => '',
			],
		] );

		$selector = '{{WRAPPER}} .the7-e-woo-cart-empty-cart .the7-e-woo-cart-empty-cart-icon';

		$this->add_responsive_control( 'empty_cart_icon_size', [
			'label'      => esc_html__( 'Icon Size', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'default'    => [
				'unit' => 'px',
				'size' => '48',
			],
			'size_units' => [ 'px' ],
			'range'      => [
				'px' => [
					'min'  => 0,
					'max'  => 200,
					'step' => 1,
				],
			],
			'selectors'  => [
				$selector          => 'font-size: {{SIZE}}{{UNIT}}',
				$selector . ' svg' => 'width: {{SIZE}}{{UNIT}};',
			],
			'condition'  => [
				'empty_cart_icon[value]!' => '',
			],
		] );

		$this->add_control( 'empty_cart_icon_color', [
			'label'     => esc_html__( 'Icon Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'alpha'     => true,
			'selectors' => [
				$selector          => 'color: {{VALUE}};',
				$selector . ' svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
			],
			'condition' => [
				'empty_cart_icon[value]!' => '',
			],
		] );

		$this->add_responsive_control( 'empty_cart_icon_gap_above', [
			'label'        => esc_html__( 'Spacing', 'the7mk2' ),
			'type'         => Controls_Manager::SLIDER,
			'size_units'   => [ 'px' ],
			'range'        => [
				'px' => [
					'min'  => 0,
					'max'  => 100,
					'step' => 1,
				],
			],
			'default'      => [
				'unit' => 'px',
				'size' => '20',
			],
			'control_type' => Abstract_Template::CONTROL_TYPE_RESPONSIVE,
			'selectors'    => [
				$selector => 'margin-top: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->end_controls_section();
	}

	protected function add_empty_cart_text_styles() {
		$this->start_controls_section( 'section_empty_cart_text_style', [
			'label' => esc_html__( 'Empty Cart Text', 'the7mk2' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$selector = '{{WRAPPER}} .the7-e-woo-cart-empty-cart .the7-e-woo-cart-empty-cart-text';

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'empty_cart_text_typography',
			'selector' => $selector,
		] );

		$this->add_control( 'empty_cart_text_color', [
			'label'     => esc_html__( 'Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				$selector => 'color: {{VALUE}};',
			],
		] );

		$this->add_responsive_control( 'empty_cart_text_gap', [
			'label'      => esc_html__( 'Spacing', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [
				'px' => [
					'min'  => 0,
					'max'  => 200,
					'step' => 1,
				],
			],
			'default'    => [
				'unit' => 'px',
				'size' => '20',
			],
			'selectors'  => [
				$selector => 'margin-top: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->end_controls_section();
	}


	protected function add_scroll_styles() {
		$this->start_controls_section(
			'section_scrollbar',
			[
				'label' => esc_html__( 'Scrollbar', 'the7mk2' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'classes'   => 'the7-control',
			]
		);
		$this->add_control( 'scrollbar', [
			'label'              => esc_html__( 'Enable Custom Scrollbar', 'the7mk2' ),
			'type'               => Controls_Manager::SWITCHER,
			'label_on'           => esc_html__( 'On', 'the7mk2' ),
			'label_off'          => esc_html__( 'Off', 'the7mk2' ),
			'default'            => '',
			'prefix_class' => 'the7-custom-scroll-',
		] );

		$condition = [ 'scrollbar' => 'yes'];
		$selector = '{{WRAPPER}}';


		$this->start_controls_tabs(
			'scrollbar_tabs_style',
			['condition' => $condition]

		);

		$this->start_controls_tab(
			'normal_scrollbar_style',
			[
				'label' => esc_html__( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_control( 'scrollbar_color', [
			'label'      => esc_html__( 'Thumb Color', 'the7mk2' ),
			'type'       => Controls_Manager::COLOR,
			'selectors'  => [
				$selector  => '--scrollbar-thumb-color: {{VALUE}};',
			],
		] );

		$this->end_controls_tab();

		$this->start_controls_tab(
			'hover_scrollbar_style',
			[
				'label' => esc_html__( 'Hover', 'the7mk2' ),
			]
		);
		$this->add_control( 'hover_scrollbar_color', [
			'label'      => esc_html__( 'Thumb Color', 'the7mk2' ),
			'type'       => Controls_Manager::COLOR,
			'selectors'  => [
				$selector  => '--scrollbar-thumb-hover-color: {{VALUE}};',
			],
		] );

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
    }

	protected function add_empty_cart_button_styles() {

		$prefix = 'empty_cart_button_shop_';
		$selector_prefix = '.the7-e-empty-cart-button-shop';

		$this->template( Button::class )->add_style_controls( Button::ICON_MANAGER, [ 'empty_cart_button' => 'yes' ], [
			$prefix . 'button_size'      => [
				'label'          => esc_html__( 'Size', 'the7mk2' ),
				'type'           => Controls_Manager::SELECT,
				'default'        => 'md',
				'options'        => The7_Elementor_Widget_Base::get_button_sizes(),
				'style_transfer' => true,
			],
			$prefix . 'gap_above_button' => [
				'label'        => esc_html__( 'Spacing Above Button', 'the7mk2' ),
				'type'         => Controls_Manager::SLIDER,
				'size_units'   => [ 'px' ],
				'range'        => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'default'      => [
					'unit' => 'px',
					'size' => '20',
				],
				'control_type' => Abstract_Template::CONTROL_TYPE_RESPONSIVE,
				'selectors'    => [
					'{{WRAPPER}} ' . $selector_prefix . '.box-button' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
				'separator'    => 'before',
			],
			$prefix . 'button_min_width' => [
				'label'        => esc_html__( 'Min Width', 'the7mk2' ),
				'type'         => Controls_Manager::SLIDER,
				'selectors'    => [
					'{{WRAPPER}} ' . $selector_prefix . '.box-button' => 'min-width: {{SIZE}}{{UNIT}}',
				],
				'separator'    => 'before',
				'control_type' => Abstract_Template::CONTROL_TYPE_RESPONSIVE,
			],

			$prefix . 'button_min_height' => [
				'label'        => esc_html__( 'Min Height', 'the7mk2' ),
				'type'         => Controls_Manager::SLIDER,
				'selectors'    => [
					'{{WRAPPER}} ' . $selector_prefix . '.box-button' => 'min-height: {{SIZE}}{{UNIT}}',
				],
				'control_type' => Abstract_Template::CONTROL_TYPE_RESPONSIVE,
			],
		], $prefix, $selector_prefix, esc_html__( 'Empty Cart Button', 'the7mk2' ) );

		$selector = '{{WRAPPER}} .the7-e-empty-cart-button-shop.box-button';

		$this->start_injection( [
			'of' => $prefix . 'button_size',
			'at' => 'before',
		] );

		$this->add_responsive_control( 'empty_cart_button_shop_align', [
			'label'                => esc_html__( 'Alignment', 'the7mk2' ),
			'type'                 => Controls_Manager::CHOOSE,
			'options'              => [
				'left'    => [
					'title' => esc_html__( 'Left', 'the7mk2' ),
					'icon'  => 'eicon-h-align-left',
				],
				'center'  => [
					'title' => esc_html__( 'Center', 'the7mk2' ),
					'icon'  => 'eicon-h-align-center',
				],
				'right'   => [
					'title' => esc_html__( 'Right', 'the7mk2' ),
					'icon'  => 'eicon-h-align-right',
				],
				'stretch' => [
					'title' => esc_html__( 'Stretch', 'the7mk2' ),
					'icon'  => 'eicon-h-align-stretch',
				],
			],
			'default'              => 'center',
			'selectors_dictionary' => [
				'left'  => 'flex-start',
				'right' => 'flex-end',
			],
			'selectors'            => [
				$selector => 'align-self: {{VALUE}};',
			],
		] );

		$this->end_injection();
	}

	protected function render() {
		$edit = Elementor::$instance->editor->is_edit_mode();
		$settings = $this->get_settings_for_display();
		if ( null === WC()->cart ) {
			return '';
		}
		$cart_status = '';
		if ( empty( WC()->cart->get_cart() ) ) {
			$cart_status = 'the7-e-woo-cart-status-cart-empty';
		}
		$this->add_render_attribute( '_wrapper', 'class', [ $cart_status ] );
		?>
        <h3 class="title-text">
			<?php
			if ( $settings['widget_title_text'] ) {
				echo esc_html( $settings['widget_title_text'] );
			} ?>
        </h3>
        <div class="the7-e-woo-cart-empty-cart">
			<?php if ( ! empty( $settings['empty_cart_icon']['value'] ) ) : ?>
                <div class="the7-e-woo-cart-empty-cart-icon elementor-icon">
					<?php Icons_Manager::render_icon( $settings['empty_cart_icon'] ); ?>
                </div>
			<?php endif; ?>
            <div class="the7-e-woo-cart-empty-cart-text">
				<?php
				if ( $settings['empty_cart_text'] ) {
					echo esc_html( $settings['empty_cart_text'] );
				}
				?>
            </div>
			<?php
			if ( $settings['empty_cart_button_shop_text'] || $this->template( Button::class )->is_icon_visible( 'empty_cart_button_shop_' ) ) {
				$this->remove_render_attribute( 'empty_cart_button_shop' );
				$this->add_render_attribute( 'empty_cart_button_shop', 'class', 'the7-e-empty-cart-button-shop' );
				if ( $settings['empty_cart_button_link'] && ! empty( $settings['empty_cart_button_link']['url'] ) ) {
					$this->add_link_attributes( 'empty_cart_button_shop', $settings['empty_cart_button_link'] );
				} else {
					$this->add_render_attribute( 'empty_cart_button_shop', 'href', esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ) );
				}
				$this->template( Button::class )->render_button( 'empty_cart_button_shop', esc_html( $settings['empty_cart_button_shop_text'] ), 'a', 'empty_cart_button_shop_' );
			}
			?>
        </div>
        <div class="the7-e-woo-cart-not-empty-cart">
			<?php echo WoocommerceCartModule::get_cart_content(); ?>
            <div class="the7-e-mini-cart-footer"><?php
				$this->display_cart_subtotal( $settings );
				$this->display_cart_buttons( $settings );
				?>
            </div>
        </div>
        <div class="the7_templates">
			<?php if ( ! empty( $settings['remove_icon']['value'] ) ) : ?>
                <div class="the7_template_icon_remove">
					<?php Icons_Manager::render_icon( $settings['remove_icon'] ); ?>
                </div>
			<?php endif; ?>
        </div>
		<?php
	}

	protected function display_cart_subtotal( $settings ) {
		?>
        <div class="woocommerce-mini-cart__total total">
            <div class="subtotal-text">
				<?php
				if ( $settings['subtotal_text'] ) {
					echo esc_html( $settings['subtotal_text'] );
				} ?>
            </div>
			<?php
			echo WoocommerceCartModule::get_cart_subtotal();
			?>
        </div>
		<?php
	}

	protected function display_cart_buttons( $settings ) {
		?>
        <div class="woocommerce-mini-cart__buttons_wrapper">
            <div class="woocommerce-mini-cart__buttons buttons">
				<?php
				$this->render_button( $settings, get_permalink( wc_get_page_id( 'shop' ) ), 'shop_button', 'shop_button_', 'the7-e-button-shop' );
				$this->render_button( $settings, wc_get_cart_url(), 'view_cart_button', 'view_cart_button_', 'the7-e-button-view-cart', true );
				$this->render_button( $settings, wc_get_checkout_url(), 'checkout_button', 'checkout_button_', 'the7-e-button-checkout', true );
				?>
            </div>
        </div>
		<?php
	}

	protected function render_button( $settings, $url, $element, $prefix, $selector_prefix, $force_link = false ) {
		$this->remove_render_attribute( 'shop_button_wrapper' );
		$this->add_render_attribute( 'shop_button_wrapper', 'class', [ 'the7-e-wc-button-wrap', $selector_prefix ] );
		?>
        <div <?php echo $this->get_render_attribute_string( 'shop_button_wrapper' ); ?>><?php
		if ( $settings[ $prefix . 'text' ] || $this->template( Button::class )->is_icon_visible( $prefix ) ) {
			$this->remove_render_attribute( $element );
			if ( $force_link || ! empty( $settings[ $element . '_link' ] ) ) {
				if ( $force_link || empty( $settings[ $element . '_link' ]['url'] ) ) {
					$this->add_render_attribute( $element, 'href', esc_url( $url ) );
				} else {
					$this->add_link_attributes( $element, $settings[ $element . '_link' ] );
				}
			}
			$this->template( Button::class )->render_button( $element, esc_html( $settings[ $prefix . 'text' ] ), 'a', $prefix );
		}
		?></div><?php
	}
}
