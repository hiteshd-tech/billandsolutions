<?php
/**
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widgets\Woocommerce;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Button;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Woocommerce\Add_To_Cart_Button;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Button as Button_Template;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Woocommerce\Variations;
use WC_Product;

defined( 'ABSPATH' ) || exit;

/**
 * Loop_Add_To_Cart class.
 */
class Loop_Add_To_Cart extends The7_Elementor_Widget_Base {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'the7-woocommerce-loop-add-to-cart';
	}

	/**
	 * @return string
	 */
	public function the7_title() {
		return esc_html__( 'Loop Add To Cart', 'the7mk2' );
	}

	/**
	 * @return string
	 */
	public function the7_icon() {
		return 'eicon-product-add-to-cart';
	}

	/**
	 * @return string[]
	 */
	public function the7_keywords() {
		return [ 'woocommerce', 'shop', 'store', 'cart', 'product', 'button', 'add to cart' ];
	}

	/**
	 * @return string[]
	 */
	public function get_style_depends() {
		return [ 'the7-wc-loop-add-to-cart-css' ];
	}

	/**
	 * Get script dependencies.
	 *
	 * Retrieve the list of script dependencies the element requires.
	 *
	 * @return array Element scripts dependencies.
	 */
	public function get_script_depends() {
		$scripts = parent::get_script_depends();
		// Always load variations script to handle quantity input.
		$scripts[] = 'the7-woocommerce-product-variations';

		return $scripts;
	}

	/**
	 * Register widget assets.
	 */
	protected function register_assets() {
		the7_register_style(
			'the7-wc-loop-add-to-cart-css',
			THE7_ELEMENTOR_CSS_URI . '/the7-wc-loop-add-to-cart.css'
		);
	}

	/**
	 * @return void
	 */
	protected function render() {
		global $product;

		$product = wc_get_product();

		if ( empty( $product ) ) {
			return;
		}

		$this->add_render_attribute(
			'container',
			'class',
			[
				'the7-add-to-cart',
				'the7-product-' . esc_attr( $product->get_type() ),
			]
		);

		if ( $this->is_show_product_variations( $product ) ) {
			$this->template( Variations::class )->add_data_attributes_to_element( $product, 'container' );
		}

		echo '<div ' . $this->get_render_attribute_string( 'container' ) . '>';

		if ( $this->is_show_product_variations( $product ) ) {
			$this->template( Variations::class )->render( $product );
		}

			echo '<div class="woocommerce-variation-add-to-cart variations_button">';
				woocommerce_quantity_input(
					[
						'min_value'   => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
						'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
						'input_value' => $product->get_min_purchase_quantity(),
					]
				);
				$this->render_submit_button();
			echo '</div>';
		echo '</div>';
	}

	/**
	 * @return void
	 */
	public function render_submit_button() {
		global $product;
		if ( ! $product ) {
			return;
		}

		if ( $product->is_in_stock() && $this->is_show_product_variations( $product ) ) {
			// Add ajax handler if it is enabled.
			if ( $this->is_wc_ajax_add_to_cart_enabled() ) {
				$this->add_render_attribute(
					'box-button',
					[
						'class' => 'ajax_add_to_cart variation-btn-disabled',
					]
				);
			}
			$button_text = '<span>' . esc_html__( 'Add to cart', 'the7mk2' ) . '</span>';
		} else {
			$button_text = '<span>' . esc_html( $product->add_to_cart_text() ) . '</span>';
		}
		$button_text .= $this->get_product_add_to_cart_icon_html( $product, 'elementor-button-icon' );

		$this->template( Add_To_Cart_Button::class )->render_button( 'box-button', $button_text, 'a', $product );
	}

	/**
	 * @param WC_Product $product WC_Product object.
	 *
	 * @return bool
	 */
	protected function is_show_product_variations( WC_Product $product ) {
		return $product->get_type() === 'variable' && $this->is_show_variations();
	}

	/**
	 * @return bool
	 */
	protected function is_show_variations() {
		return $this->any_responsive_setting_equals( 'show_variations', 'y' );
	}

	/**
	 * @param WC_Product $product Product.
	 * @param string     $class Icon class attribute value.
	 *
	 * @return array|null
	 */
	protected function get_product_add_to_cart_icon_html( $product, $class ) {
		if ( in_array( $product->get_type(), [ 'variable', 'grouped' ], true ) && ! $this->is_show_variations() ) {
			$icon_setting = $this->get_add_to_cart_icon_setting( 'options_icon' );
		} else {
			$icon_setting = $this->get_add_to_cart_icon_setting();
		}

		return $this->get_button_icon_html( $icon_setting, $class );
	}

	/**
	 * Used as a general entry point to the button icons. Return 'add_to_cart_icon' by default.
	 *
	 * @param string $custom_icon_setting_key Setting key.
	 *
	 * @return array|null
	 */
	protected function get_add_to_cart_icon_setting( $custom_icon_setting_key = null ) {
		if ( $custom_icon_setting_key ) {
			$custom_icon = $this->get_settings_for_display( $custom_icon_setting_key );
			if ( ! empty( $custom_icon['value'] ) ) {
				return $custom_icon;
			}
		}

		return $this->get_settings_for_display( 'add_to_cart_icon' );
	}

	/**
	 * @return bool
	 */
	protected function is_wc_ajax_add_to_cart_enabled() {
		return get_option( 'woocommerce_enable_ajax_add_to_cart' ) === 'yes';
	}

	/**
	 * @param  array|null $icon  Icon setting value.
	 * @param  string     $class  CSS class of the icon.
	 *
	 * @return mixed|string
	 */
	protected function get_button_icon_html( $icon, $class = '' ) {
		$icon_html = $this->get_elementor_icon_html( $icon, 'i', [ 'class' => $class ] );

		/**
		 * "Icon on image" skin only.
		 *
		 * Add additional wrapper for svg and empty "add to cart" icon (we have to show something).
		 */
		if ( ( empty( $icon['library'] ) || $icon['library'] === 'svg' ) ) {
			$icon_html = $icon_html;
		}

		return $icon_html;
	}

	/**
	 * @return void
	 */
	protected function register_controls() {

		$this->add_layout_content_controls();
		$this->template( Variations::class )->add_style_controls();
		$this->template( Variations::class )->add_variation_swatch_styles_controls();
		$this->add_quantity_style_controls();

		/**
		 * Override default button icon conditions to respect custom icons set.
		 */
		$button_icon_conditions = [
			'condition'  => [],
			'conditions' => [
				'relation' => 'or',
				'terms'    => [
					[
						'name'     => 'add_to_cart_icon[value]',
						'operator' => '!==',
						'value'    => '',
					],
					[
						'name'     => 'options_icon[value]',
						'operator' => '!==',
						'value'    => '',
					],
				],

			],
		];
		$button_icon_gap_conditions = [
			'condition'  => [],
			'conditions' => [
				'relation' => 'or',
				'terms'    => [
					[
						'terms' => [
							[
								'name'     => 'add_to_cart_icon[value]',
								'operator' => '!==',
								'value'    => '',
							],
							[
								'name'     => 'show_btn_text',
								'operator' => '=',
								'value'    => 'yes',
							],
						],
					],
					[
						'terms' => [
							[
								'name'     => 'options_icon[value]',
								'operator' => '!==',
								'value'    => '',
							],
							[
								'name'     => 'show_btn_text',
								'operator' => '=',
								'value'    => 'yes',
							],
						]
					],
				],

			],

		];

		$this->template( Add_To_Cart_Button::class )->add_style_controls(
			Button_Template::ICON_MANAGER,
			[],
			[
				'button_icon'         => null,
				'gap_above_button'    => null,
				'button_text_padding'   => null,
				'button_icon_size'    => $button_icon_conditions,
				'button_icon_position'  => $button_icon_gap_conditions,
				'button_icon_spacing' => $button_icon_gap_conditions,
				'button_size'      => [
					'default' => 'sm',
				],
			]
		);

		// Button horizontal position.
		$this->start_injection(
			[
				'type' => 'control',
				'at'   => 'before',
				'of'   => 'button_size',
			]
		);


		// $this->add_control(
		// 	'add_to_cart_divider',
		// 	[
		// 		'type' => Controls_Manager::DIVIDER,
		// 	]
		// );

		$this->end_injection();

		// Add button icon heading.
		$this->start_injection(
			[
				'type' => 'control',
				'at'   => 'before',
				'of'   => 'button_icon_size',
			]
		);
		$button_width_options            = [
			'inline'  => esc_html__( 'Default', 'the7mk2' ),
			'stretch' => esc_html__( 'Stretch', 'the7mk2' ),
		];
		$button_width_options_on_devices = [ '' => esc_html__( 'No change', 'the7mk2' ) ] + $button_width_options;

		$this->add_responsive_control(
			'button_width',
			[
				'label'                => esc_html__( 'Width', 'the7mk2' ),
				'type'                 => Controls_Manager::SELECT,
				'options'              => $button_width_options,
				'device_args'          => [
					'tablet' => [
						'default' => '',
						'options' => $button_width_options_on_devices,
					],
					'mobile' => [
						'default' => '',
						'options' => $button_width_options_on_devices,
					],
				],
				'default'              => 'inline',
				'selectors_dictionary' => [
					'inline'  => ' width: auto;',
					'stretch' => ' width: 100%;',
				],
				'selectors'            => [
					'{{WRAPPER}} .box-button' => ' {{VALUE}};',
				],
				'condition'            => [
					'show_button' => 'yes',
				],
			]
		);
		$this->add_control(
			'show_btn_text',
			[
				'label'                => esc_html__( 'Text on button', 'the7mk2' ),
				'type'                 => Controls_Manager::SWITCHER,
				'label_on'             => esc_html__( 'Show', 'the7mk2' ),
				'label_off'            => esc_html__( 'Hide', 'the7mk2' ),
				'return_value'         => 'yes',
				'default'              => 'yes',
				'selectors_dictionary' => [
					'yes' => '--btn-text-display: inline-flex; --show-btn-icon-spacing: var(--btn-icon-spacing, 5px)',
					''    => '--btn-text-display: none; --show-btn-icon-spacing: 0px',
				],
				'selectors'            => [
					'{{WRAPPER}}' => '{{VALUE}}',
				],
			]
		);

		$this->add_control(
			'icon_title',
			[
				'label' => esc_html__( 'Icon', 'the7mk2' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'add_to_cart_icon',
			[
				'label'       => esc_html__( '"Add To Cart" Icon', 'the7mk2' ),
				'type'        => Controls_Manager::ICONS,
				'default'     => [
					'value'   => 'fas fa-shopping-cart',
					'library' => 'fa-solid',
				],
			]
		);

		$this->add_control(
			'options_icon',
			[
				'label'       => esc_html__( '"Options" Icon ', 'the7mk2' ),
				'type'        => Controls_Manager::ICONS,
				'description' => esc_html__( 'Shows up if variations are hidden. If none is selected, inherits from "Add to Cart"', 'the7mk2' ),
				'default'     => [
					'value'   => '',
					'library' => '',
				],
				'render_type' => 'template',
			]
		);

		$this->end_injection();
		$this->start_injection(
			[
				'type' => 'control',
				'at'   => 'after',
				'of'   => 'button_min_height',
			]
		);
		$this->add_responsive_control(
			'add_to_cart_margin',
			[
				'label'      => esc_html__( 'Margins', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .box-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);
		$this->add_responsive_control(
			'add_to_cart_padding',
			[
				'label'      => esc_html__( 'Padding', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .box-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);
		$this->end_injection();
	}

	/**
	 * @return void
	 */
	protected function add_layout_content_controls() {
		$this->start_controls_section(
			'section_layout',
			[
				'label' => esc_html__( 'Add to cart', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'show_button',
			[
				'label'                => esc_html__( 'Visibility', 'the7mk2' ),
				'type'                 => Controls_Manager::SWITCHER,
				'label_on'             => esc_html__( 'Show', 'the7mk2' ),
				'label_off'            => esc_html__( 'Hide', 'the7mk2' ),
				'return_value'         => 'yes',
				'default'              => 'yes',
				'selectors_dictionary' => [
					'yes' => 'display: flex;',
					''    => 'display: none !important;',
				],
				'selectors'            => [
					'{{WRAPPER}} .woocommerce-variation-add-to-cart' => '{{VALUE}}',
				],
				'prefix_class'         => 'show-add-to-cart-button-',
			]
		);
		$this->add_responsive_control(
			'add_to_cart_align',
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
				'default'              => 'left',
				'selectors_dictionary' => [
					'left'   => 'text-align: left; justify-content: flex-start; --content-align: flex-start;',
					'center' => 'text-align: center; justify-content: center; --content-align: center;',
					'right'  => 'text-align: right; justify-content: flex-end; --content-align: flex-end;',
				],
				'selectors'            => [
					'{{WRAPPER}} .woocommerce-variation-add-to-cart' => '{{VALUE}}',
				],
			]
		);


		$this->add_control(
			'show_quantity',
			[
				'label'                => esc_html__( 'Quantity', 'the7mk2' ),
				'type'                 => Controls_Manager::SWITCHER,
				'label_on'             => esc_html__( 'Show', 'the7mk2' ),
				'label_off'            => esc_html__( 'Hide', 'the7mk2' ),
				'return_value'         => 'yes',
				'default'              => 'yes',
				'selectors_dictionary' => [
					'yes' => '--quantity-display: inline-flex;',
					''    => '--quantity-display: none !important;',
				],
				'selectors'            => [
					'{{WRAPPER}}' => '{{VALUE}}',
				],
			]
		);

		$this->end_controls_section();
		$this->start_controls_section(
			'variation_section_layout',
			[
				'label' => esc_html__( 'Variations', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
			]
		);

		$variations_show_options = [
			'y' => esc_html__( 'Show', 'the7mk2' ),
			'n' => esc_html__( 'Hide', 'the7mk2' ),
		];
		$this->add_responsive_control(
			'show_variations',
			[
				'label'                => esc_html__( 'Visibility', 'the7mk2' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => 'n',
				'options'              => $variations_show_options,
				'device_args'          => $this->generate_device_args(
					[
						'options' => [ '' => esc_html__( 'No change', 'the7mk2' ) ] + $variations_show_options,
					]
				),
				'selectors_dictionary' => [
					'y' => $this->combine_to_css_vars_definition_string(
						[
							'variations-display' => 'flex',
							'added-btn-display'  => 'none',
							'variative-quantity-display' => 'var(--quantity-display)'
						]
					),
					'n' => $this->combine_to_css_vars_definition_string(
						[
							'added-btn-display'  => 'flex',
							'variations-display' => 'none',
							'variative-quantity-display' => 'var(--variations-display)'
						]
					),
				],
				'selectors'            => [
					'{{WRAPPER}}' => '{{VALUE}}',
				],
				'prefix_class'         => 'variations-visible%s-',
				'render_type'          => 'template',
			]
		);
		$this->add_responsive_control(
			'variations_align',
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
				'default'              => 'left',
				'selectors_dictionary' => [
					'left'   => '--align-variation-items: flex-start;',
					'center' => '--align-variation-items: center;',
					'right'  => '--align-variation-items: flex-end;',
				],
				'selectors'            => [
					'{{WRAPPER}}' => '{{VALUE}}',
				],
			]
		);
		$this->add_control(
			'variations_label',
			[
				'label'        => esc_html__( 'Label', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the7mk2' ),
				'label_off'    => esc_html__( 'Hide', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
				'selectors'    => [
					'{{WRAPPER}} .product-variation-row > span' => 'display: flex;',
				],
			]
		);


		$this->template( Variations::class )->add_variation_type_controls();

		$this->end_controls_section();
	}

	/**
	 * Add quantity style controls.
	 */
	public function add_quantity_style_controls() {
		$this->start_controls_section(
			'section_atc_quantity_style',
			[
				'label'     => esc_html__( 'Quantity', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_quantity' => 'yes', // Located in Variations::add_variation_type_controls().
				],
			]
		);

		$this->add_responsive_control(
			'quantity_position',
			[
				'label'                => esc_html__( 'Position', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => [
					'inline'  => [
						'title' => esc_html__( 'Inline', 'the7mk2' ),
						'icon'  => 'eicon-h-align-left',
					],
					'stacked' => [
						'title' => esc_html__( 'Stacked', 'the7mk2' ),
						'icon'  => 'eicon-v-align-top',
					],
				],
				'default'              => 'stacked',
				'selectors_dictionary' => [
					'stacked' => 'flex-flow: column; --quantity-gap: var(--the7-top-quantity-margin, 0) var(--the7-right-quantity-margin, 0)var(--the7-bottom-quantity-margin, 20px) var(--the7-left-quantity-margin, 0); align-items: var(--content-align, center);',
					'inline'  => 'flex-flow: row nowrap; align-items: center; --quantity-gap: var(--the7-top-quantity-margin, 0) var(--the7-right-quantity-margin, 20px)var(--the7-bottom-quantity-margin, 0) var(--the7-left-quantity-margin, 0); justify-content: var(--content-align);',
				],
				'selectors'            => [
					'{{WRAPPER}} .woocommerce-variation-add-to-cart' => '{{VALUE}}',
				],
				'condition'            => [
					'show_quantity' => 'yes',
				],
			]
		);

		$quantity_width_options            = [
			'inline'  => esc_html__( 'Default', 'the7mk2' ),
			'stretch' => esc_html__( 'Stretch', 'the7mk2' ),
		];
		$quantity_width_options_on_devices = [ '' => esc_html__( 'No change', 'the7mk2' ) ] + $quantity_width_options;

		$this->add_responsive_control(
			'quantity_width',
			[
				'label'                => esc_html__( 'Width', 'the7mk2' ),
				'type'                 => Controls_Manager::SELECT,
				'options'              => $quantity_width_options,
				'device_args'          => [
					'tablet' => [
						'default' => '',
						'options' => $quantity_width_options_on_devices,
					],
					'mobile' => [
						'default' => '',
						'options' => $quantity_width_options_on_devices,
					],
				],
				'default'              => 'inline',
				'selectors_dictionary' => [
					'inline'  => ' width: auto;',
					'stretch' => ' width: 100%;',
				],
				'selectors'            => [
					'{{WRAPPER}} .woocommerce-variation-add-to-cart, {{WRAPPER}} .woocommerce-variation-add-to-cart .quantity' => ' {{VALUE}};',
				],
				'condition'            => [
					'quantity_position' => 'stacked',
					'show_quantity'     => 'yes',
				],
			]
		);

		$this->add_control(
			'quantity_heading',
			[
				'type'  => Controls_Manager::HEADING,
				'label' => esc_html__( 'Box & Number', 'the7mk2' ),
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'quantity_typography',
				'selector' => '{{WRAPPER}} .quantity .qty',
			]
		);

		$this->add_responsive_control(
			'quantity_min_width',
			[
				'label'      => esc_html__( 'Min Width', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .quantity' => '--quantity-width: {{SIZE}}{{UNIT}}; min-width: {{SIZE}}{{UNIT}};',
				],
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'  => 'quantity_width',
							'value' => 'inline',
						],
						[
							'name'  => 'quantity_width_tablet',
							'value' => 'inline',
						],
						[
							'name'  => 'quantity_width_mobile',
							'value' => 'inline',
						],
						[
							'name'  => 'quantity_position',
							'value' => 'inline',
						],
						[
							'name'  => 'quantity_position_tablet',
							'value' => 'inline',
						],
						[
							'name'  => 'quantity_position_mobile',
							'value' => 'inline',
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'quantity_min_height',
			[
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
					'{{WRAPPER}} .quantity' => 'min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'quantity_margins',
			[
				'label'     => esc_html__( 'Margin', 'the7mk2' ),
				'type'      => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}}' => '--the7-top-quantity-margin: {{TOP}}{{UNIT}}; --the7-right-quantity-margin: {{RIGHT}}{{UNIT}}; --the7-bottom-quantity-margin: {{BOTTOM}}{{UNIT}}; --the7-left-quantity-margin: {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .quantity' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'quantity_paddings',
			[
				'label'     => esc_html__( 'Padding', 'the7mk2' ),
				'type'      => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .quantity' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'           => 'quantity_border',
				'fields_options' => [
					'width' => [
						'selectors' => [
							'{{SELECTOR}}' => '--the7-top-input-border-width: {{TOP}}{{UNIT}}; --the7-right-input-border-width: {{RIGHT}}{{UNIT}}; --the7-bottom-input-border-width: {{BOTTOM}}{{UNIT}}; --the7-left-input-border-width: {{LEFT}}{{UNIT}}; border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					],
				],
				'selector'       => '{{WRAPPER}} .quantity',
				'exclude'        => [ 'color' ],
			]
		);

		$this->add_responsive_control(
			'quantity_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .quantity' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'quantity_text_color',
			[
				'label'     => esc_html__( 'Number Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .quantity .qty' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'quantity_bg_color',
			[
				'label'     => esc_html__( 'Background', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .quantity' => 'background: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'quantity_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .quantity' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'quantity_box_shadow',
				'label'    => esc_html__( 'Box Shadow', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .quantity',
			]
		);

		$this->add_control(
			'quantity_button_heading',
			[
				'type'      => Controls_Manager::HEADING,
				'label'     => esc_html__( '+/- Settings', 'the7mk2' ),
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'quantity_button_icon_width',
			[
				'label'      => esc_html__( 'Icon Size', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .quantity button.is-form svg ' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'quantity_button_width',
			[
				'label'      => esc_html__( 'Background Width', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors'  => [
					'{{WRAPPER}}' => '--quantity-btn-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .quantity button.is-form' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'quantity_button_height',
			[
				'label'      => esc_html__( 'Background Height', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors'  => [
					'{{WRAPPER}}' => '--quantity-btn-height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .quantity button.is-form' => 'height: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_responsive_control(
			'quantity_button_border',
			[
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
					'{{WRAPPER}}' => '--quantity-btn-border-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'quantity_button_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .quantity button.is-form' => 'border-radius: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->start_controls_tabs( 'quantity_style_tabs' );

		$this->start_controls_tab(
			'quantity_style_normal',
			[
				'label' => esc_html__( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_control(
			'quantity_btn_color',
			[
				'label'     => esc_html__( '+/- Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .quantity button.is-form' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'quantity_bg_btn',
			[
				'label'     => esc_html__( '+/- Background', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'#the7-body {{WRAPPER}} .woocommerce-variation-add-to-cart .quantity button.is-form, #the7-body {{WRAPPER}} .the7-add-to-cart .quantity button.is-form' => 'background: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'quantity_btn_border_color',
			[
				'label'     => esc_html__( '+/- Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--quantity-btn-border-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'           => 'quantity_btn_box_shadow',
				'label'          => esc_html__( '+/- Box Shadow', 'the7mk2' ),
				'selector'       => '{{WRAPPER}} .quantity button.is-form',
				'fields_options' => [
					'box_shadow' => [
						'selectors' => [
							'{{SELECTOR}}' => 'box-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{SPREAD}}px {{COLOR}} {{box_shadow_position.VALUE}} !important;',
						],
					],
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'quantity_style_hover',
			[
				'label' => esc_html__( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			'quantity_btn_color_focus',
			[
				'label'     => esc_html__( '+/- Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .quantity button.is-form:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'quantity_bg_btn_focus',
			[
				'label'     => esc_html__( '+/- Background', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'#the7-body {{WRAPPER}} .woocommerce-variation-add-to-cart .quantity button.is-form:hover, #the7-body {{WRAPPER}} .the7-add-to-cart .quantity button.is-form:hover' => 'background: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'quantity_border_color_focus',
			[
				'label'     => esc_html__( '+/- Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--quantity-btn-border-hover-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'           => 'quantity_box_shadow_focus',
				'label'          => esc_html__( '+/- Box Shadow', 'the7mk2' ),
				'selector'       => '{{WRAPPER}} .quantity button.is-form:hover',
				'fields_options' => [
					'box_shadow' => [
						'selectors' => [
							'{{SELECTOR}}' => 'box-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{SPREAD}}px {{COLOR}} {{box_shadow_position.VALUE}} !important;',
						],
					],
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}
}
