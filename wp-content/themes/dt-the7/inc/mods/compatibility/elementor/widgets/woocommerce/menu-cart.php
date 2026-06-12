<?php
/**
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widgets\Woocommerce;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;
use Elementor\TemplateLibrary\Source_Local;
use ElementorPro\Modules\Popup\Module as PopupModule;

defined( 'ABSPATH' ) || exit;

/**
 * Menu cart widget class.
 */
class Menu_Cart extends The7_Elementor_Widget_Base {

	const STICKY_WRAPPER = '.the7-e-sticky-effects .elementor-element.elementor-element-{{ID}}';

	/**
	 * @return string
	 */
	public function get_name() {
		return 'the7-woocommerce-menu-cart';
	}

	/**
	 * @return string
	 */
	public function the7_title() {
		return esc_html__( 'Menu Cart', 'the7mk2' );
	}

	/**
	 * @return string
	 */
	public function the7_icon() {
		return 'eicon-cart';
	}

	/**
	 * @return string[]
	 */
	protected function the7_keywords() {
		return [ 'woocommerce', 'cart', 'menu', 'shop' ];
	}

	/**
	 * @return string[]
	 */
	public function the7_categories() {
		return [ 'theme-elements', 'woocommerce-elements' ];
	}

	/**
	 * @return string[]
	 */
	public function get_style_depends() {
		return [ $this->get_name() ];
	}
	/**
	 * @return string[]
	 */
	public function get_script_depends() {
		return [ $this->get_name() ];
	}

	/**
	 * @return void
	 */
	protected function register_assets() {
		the7_register_style(
			$this->get_name(),
			THE7_ELEMENTOR_CSS_URI . '/the7-woocommerce-menu-cart.css'
		);

		the7_register_script_in_footer(
			$this->get_name(),
			THE7_ELEMENTOR_JS_URI . '/the7-woocommerce-menu-cart.js',
			[ 'the7-elementor-frontend-common' ]
		);
	}

	/**
	 * @return void
	 */
	protected function register_controls() {
		$this->add_cart_content_controls();
		$this->add_popup_content_controls();

		$this->add_box_style_conrols();
		$this->add_title_style_controls();
		$this->add_icon_style_controls();
		$this->add_indicator_style_controls();
	}

	/**
	 * @param  string $prefix Control name prefix.
	 *
	 * @return void
	 */
	private function add_title_color_sticky_controls( $prefix = '' ) {
		switch ( $prefix ) {
			case '_hover':
				$selectors           = [
					self::STICKY_WRAPPER . ' .dt-menu-cart__toggle_button:hover',
				];
				$subtotal_selectors  = [
					self::STICKY_WRAPPER . ' .dt-menu-cart__toggle_button:hover .dt-cart-subtotal',
				];
				$indicator_selectors = [
					self::STICKY_WRAPPER . ' .dt-menu-cart__toggle_button:hover .dt-cart-indicator',
				];
				break;
			default:
				$selectors           = [
					self::STICKY_WRAPPER . ' .dt-menu-cart__toggle_button',
				];
				$subtotal_selectors  = [
					self::STICKY_WRAPPER . ' .dt-menu-cart__toggle_button .dt-cart-subtotal',
				];
				$indicator_selectors = [
					self::STICKY_WRAPPER . ' .dt-menu-cart__toggle_button .dt-cart-indicator',
				];
		}

		$item_prefix = '_sticky' . $prefix;

		$this->add_control(
			'toggle_title_text_color' . $item_prefix,
			[
				'label'     => esc_html__( 'Text Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => $this->give_me_megaselectors(
					$selectors,
					[
						'' => 'color: {{VALUE}}',
					]
				),
			]
		);

		$this->add_control(
			'toggle_subtotal_text_color' . $item_prefix,
			[
				'label'     => esc_html__( 'Subtotal Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => $this->give_me_megaselectors(
					$subtotal_selectors,
					[
						'' => 'color: {{VALUE}}',
					]
				),
			]
		);

		$this->add_control(
			'indicator_text_color' . $item_prefix,
			[
				'label'     => esc_html__( 'Indicator color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => $this->give_me_megaselectors(
					$indicator_selectors,
					[
						'' => 'color: {{VALUE}}',
					]
				),
				'condition' => [
					'items_indicator' => 'plain',
				],
			]
		);
	}

	/**
	 * @param string $prefix Control name prefix.
	 * @param bool   $sticky Is sticky control.
	 */
	private function add_toggle_button_color_controls( $prefix, $sticky = false ) {
		$css_prefix       = '';
		$css_hover_prefix = '';
		$sticky_prefix    = '';
		if ( $sticky ) {
			$sticky_prefix = '_sticky_';
		}
		switch ( $prefix ) {
			case '_hover':
				$css_prefix       = ':hover';
				$css_hover_prefix = '.no-touchevents ';
				break;
			case '_active':
				$css_prefix = '.elementor-active';
				break;
		}

		if ( $sticky ) {
			$selector = $css_hover_prefix . self::STICKY_WRAPPER . ' .dt-menu-cart__toggle_button' . $css_prefix;
		} else {
			$selector = $css_hover_prefix . '{{WRAPPER}} .dt-menu-cart__toggle_button' . $css_prefix;
		}

		$fields_options = [
			'background' => [
				'label' => esc_html__( 'Background', 'the7mk2' ),
			],
		];

		if ( ! empty( $prefix ) ) {
			$fields_options['color'] = [
				'selectors' => [
					'{{SELECTOR}}' => 'background: {{VALUE}}',
				],
			];
		}

		$this->add_control(
			'toggle_button_background_color' . $sticky_prefix . $prefix,
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$selector => 'background: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'toggle_button_border_color' . $sticky_prefix . $prefix,
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$selector => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'toggle_button_normal_box_shadow' . $sticky_prefix . $prefix,
				'label'    => esc_html__( 'Box Shadow', 'the7mk2' ),
				'selector' => $selector,
			]
		);
	}

	/**
	 * @param sring $prefix Prefix.
	 *
	 * @return void
	 */
	private function add_icon_sticky_controls( $prefix = '' ) {
		switch ( $prefix ) {
			case '_hover':
				$selectors = [
					self::STICKY_WRAPPER . ' .dt-menu-cart__toggle:hover .dt-button-icon .elementor-icon',
				];
				break;
			case '_full':
				$selectors = [
					self::STICKY_WRAPPER . ':not(.dt-empty-cart) .dt-menu-cart__toggle .elementor-icon',
				];
				break;
			default:
				$selectors = [
					self::STICKY_WRAPPER . ' .dt-menu-cart__toggle .elementor-icon',
				];
		}

		$item_prefix = '_sticky' . $prefix;

		$this->add_control(
			'toggle_icon_color' . $item_prefix,
			[
				'label'     => esc_html__( 'Icon Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => $this->give_me_megaselectors(
					$selectors,
					[
						''     => 'color: {{VALUE}}',
						' svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
					]
				),
			]
		);

		$this->add_control(
			'icon_bg_color' . $item_prefix,
			[
				'label'     => esc_html__( 'Background', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => $this->give_me_megaselectors(
					$selectors,
					[
						'' => 'background: {{VALUE}}',
					]
				),
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'icon_normal_box_shadow' . $item_prefix,
				'label'    => esc_html__( 'Box Shadow', 'the7mk2' ),
				'selector' => implode( ', ', $selectors ),
			]
		);
	}

	/**
	 * @param string $prefix Prefix.
	 *
	 * @return void
	 */
	private function add_indicator_sticky_controls( $prefix = '' ) {
		switch ( $prefix ) {
			case '_hover':
				$selectors = [
					self::STICKY_WRAPPER . '.dt-menu-cart--items-indicator-bubble .dt-menu-cart__toggle:hover',
				];
				break;
			case '_full':
				$selectors = [
					self::STICKY_WRAPPER . '.dt-menu-cart--items-indicator-bubble:not(.dt-empty-cart) .dt-menu-cart__toggle',
				];
				break;
			default:
				$selectors = [
					self::STICKY_WRAPPER . ' ',
				];
		}
		$item_prefix = '_sticky' . $prefix;

		$this->add_control(
			'toggle_empty_indicator_color' . $item_prefix,
			[
				'label'     => esc_html__( 'Text Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => $this->give_me_megaselectors(
					$selectors,
					[
						' [data-counter]:before' => 'color: {{VALUE}}',
					]
				),
			]
		);

		$this->add_control(
			'empty_indicator_bg_color' . $item_prefix,
			[
				'label'     => esc_html__( 'Background color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => $this->give_me_megaselectors(
					$selectors,
					[
						' [data-counter]:before' => 'background: {{VALUE}}',
					]
				),
			]
		);
	}

	/**
	 * @return void
	 */
	protected function render() {
		if ( null === WC()->cart || apply_filters( 'woocommerce_widget_cart_is_hidden', false ) ) {
			return;
		}

		$settings      = $this->get_settings_for_display();
		$product_count = (int) WC()->cart->get_cart_contents_count();

		$this->add_render_attribute( 'wrapper', 'class', 'dt-menu-cart__toggle' );
		if ( ! $product_count ) {
			$this->add_render_attribute( '_wrapper', 'class', 'dt-empty-cart' );
		}
		if ( $settings['popup_id'] ) {
			$this->add_render_attribute( 'wrapper', 'class', 'has-popup' );
			$cart_link = $this->get_popup_url( $settings['popup_id'] );
		} else {
			$cart_link = wc_get_cart_url();
		}

		$this->add_render_attribute(
			'button',
			[
				'class'         => 'dt-menu-cart__toggle_button',
				'href'          => $cart_link,
				'aria-expanded' => 'false',
				'aria-label'    => esc_html__( 'Open cart', 'the7mk2' ),
			]
		);
		$this->add_render_attribute( 'button-icon', 'class', 'dt-button-icon' );

		$counter_parent_element = 'button';
		if ( $settings['indicator_alignment'] === 'icon' ) {
			$counter_parent_element = 'button-icon';
		}
		$this->add_render_attribute( $counter_parent_element, 'data-counter', $product_count );
		?>

		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<a <?php $this->print_render_attribute_string( 'button' ); ?>>
				<span <?php $this->print_render_attribute_string( 'button-icon' ); ?>>
					<?php
					if ( ! empty( $settings['cart_icon']['value'] ) ) :
						?>
						<span class="elementor-icon">
							<?php Icons_Manager::render_icon( $settings['cart_icon'] ); ?>
						</span>
					<?php endif; ?>
				</span>
				<span class="dt-cart-content">
					<?php if ( ! empty( $settings['title_text'] ) ) : ?>
					<span class="dt-cart-title"><?php echo esc_html( $settings['title_text'] ); ?> </span>
					<?php endif; ?>
					<?php
					self::render_subtotal();
					if ( $settings['items_indicator'] === 'plain' ) :
						?>
						<span class="dt-cart-indicator">(<?php echo (int) $product_count; ?>)</span>
					<?php endif; ?>
				</span>
			</a>
		</div>

		<?php
    }

	/**
	 * @return void
	 */
	public static function render_subtotal() {
		if ( null === WC()->cart || apply_filters( 'woocommerce_widget_cart_is_hidden', false ) ) {
			return;
		}

		$product_count = (int) WC()->cart->get_cart_contents_count();
		$sub_total     = WC()->cart->get_cart_subtotal();

		echo '<span class="dt-cart-subtotal" data-product-count="' . ( (int) $product_count ) . '">';
		echo $sub_total; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</span>';
	}

	/**
	 * @return void
	 */
	protected function add_popup_content_controls() {
		if ( the7_elementor_pro_is_active() ) {
			$this->start_controls_section(
				'section_popup_content',
				[
					'label' => esc_html__( 'Popup', 'the7mk2' ),
				]
			);
			$this->add_control(
				'popup_id',
				[
					'label'       => esc_html__( 'Popup', 'the7mk2' ),
					'type'        => Controls_Manager::SELECT2,
					'options'     => $this->get_popups_list(),
					'description' => esc_html__( 'Will open the selected popup by action specified below', 'the7mk2' ),
				]
			);

			$this->add_control(
				'popup_action_adding_product',
				[
					'label'              => esc_html__( 'Open cart after adding product', 'the7mk2' ),
					'type'               => Controls_Manager::SWITCHER,
					'render_type'        => 'none',
					'frontend_available' => true,
				]
			);

			$this->end_controls_section();
		}
	}

	/**
	 * @return void
	 */
	protected function add_cart_content_controls() {
		$this->start_controls_section(
			'section_menu_icon_content',
			[
				'label' => esc_html__( 'Cart', 'the7mk2' ),
			]
		);

		$this->add_responsive_control(
			'alignment',
			[
				'label'     => esc_html__( 'Alignment', 'the7mk2' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
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
				'selectors' => [
					'{{WRAPPER}}' => '--main-alignment: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'hide_empty_cart',
			[
				'label'        => esc_html__( 'Hide cart when empty', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'the7mk2' ),
				'label_off'    => esc_html__( 'No', 'the7mk2' ),
				'return_value' => 'yes',
				'selectors'    => [
					'{{WRAPPER}}.dt-empty-cart' => 'display: none;',
				],
			]
		);

		$this->add_control(
			'title_heading',
			[
				'type'      => Controls_Manager::HEADING,
				'label'     => esc_html__( 'Title', 'the7mk2' ),
				'separator' => 'before',
			]
		);

		$visibility_options            = [
			'show' => esc_html__( 'Show', 'the7mk2' ),
			'hide' => esc_html__( 'Hide', 'the7mk2' ),
		];
		$visibility_options_on_devices = [
			'' => esc_html__( 'Default', 'the7mk2' ),
		] + $visibility_options;

		$this->add_responsive_control(
			'hide_title',
			[
				'label'                => esc_html__( 'Title', 'the7mk2' ),
				'type'                 => Controls_Manager::SELECT,
				'options'              => $visibility_options,
				'device_args'          => [
					'tablet' => [
						'default' => '',
						'options' => $visibility_options_on_devices,
					],
					'mobile' => [
						'default' => '',
						'options' => $visibility_options_on_devices,
					],
				],
				'default'              => 'show',
				'selectors_dictionary' => [
					'show' => $this->combine_to_css_vars_definition_string(
						[
							'title-display'    => 'inline-flex',
							'title-visibility' => '1',
						]
					),
					'hide' => $this->combine_to_css_vars_definition_string(
						[
							'title-display'    => 'none',
							'title-visibility' => '0',
						]
					),
				],
				'selectors'            => [
					'{{WRAPPER}}' => '{{VALUE}}',
				],
			]
		);

		$this->add_control(
			'title_text',
			[
				'label'   => esc_html__( 'Title', 'the7mk2' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Cart', 'the7mk2' ),
			]
		);

		$this->add_control(
			'hide_empty_text',
			[
				'label'        => esc_html__( 'Hide Empty', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'the7mk2' ),
				'label_off'    => esc_html__( 'No', 'the7mk2' ),
				'return_value' => 'yes',
				'selectors'    => [
					'{{WRAPPER}}.dt-empty-cart' => $this->combine_to_css_vars_definition_string(
						[
							'title-display'    => 'none',
							'title-visibility' => '0',
						]
					),
				],
			]
		);

		$this->add_control(
			'subtotal_heading',
			[
				'type'      => Controls_Manager::HEADING,
				'label'     => esc_html__( 'Subtotal', 'the7mk2' ),
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'hide_subtotal',
			[
				'label'                => esc_html__( 'Subtotal', 'the7mk2' ),
				'type'                 => Controls_Manager::SELECT,
				'options'              => $visibility_options,
				'device_args'          => [
					'tablet' => [
						'default' => '',
						'options' => $visibility_options_on_devices,
					],
					'mobile' => [
						'default' => '',
						'options' => $visibility_options_on_devices,
					],
				],
				'default'              => 'show',
				'selectors_dictionary' => [
					'show' => $this->combine_to_css_vars_definition_string(
						[
							'subtotal-display' => 'inline-flex',
							'price-visibility' => '1',
						]
					),
					'hide' => $this->combine_to_css_vars_definition_string(
						[
							'subtotal-display' => 'none',
							'price-visibility' => '0',
						]
					),
				],
				'selectors'            => [
					'{{WRAPPER}}' => '{{VALUE}}',
				],
			]
		);

		$this->add_control(
			'show_subtotal',
			[
				'label'        => esc_html__( 'Hide Empty', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'the7mk2' ),
				'label_off'    => esc_html__( 'No', 'the7mk2' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'selectors'    => [
					'{{WRAPPER}}.dt-empty-cart' => $this->combine_to_css_vars_definition_string(
						[
							'subtotal-display' => 'none',
							'price-visibility' => '0',
						]
					),
				],
			]
		);

		$this->add_control(
			'icon_heading',
			[
				'type'      => Controls_Manager::HEADING,
				'label'     => esc_html__( 'Icon', 'the7mk2' ),
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'hide_icon',
			[
				'label'                => esc_html__( 'Icon', 'the7mk2' ),
				'type'                 => Controls_Manager::SELECT,
				'options'              => $visibility_options,
				'device_args'          => [
					'tablet' => [
						'default' => '',
						'options' => $visibility_options_on_devices,
					],
					'mobile' => [
						'default' => '',
						'options' => $visibility_options_on_devices,
					],
				],
				'default'              => 'show',
				'selectors_dictionary' => [
					'show' => 'display: inline-flex;',
					'hide' => 'display: none;',
				],
				'selectors'            => [
					'{{WRAPPER}} .dt-button-icon' => '{{VALUE}}',
				],
			]
		);

		$this->add_control(
			'cart_icon',
			[
				'label'            => esc_html__( 'Icon', 'the7mk2' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default'          => [
					'value'   => 'fas fa-shopping-cart',
					'library' => 'fa-solid',
				],
				'label_block'      => false,
				'skin'             => 'inline',
				'render_type'      => 'template',
			]
		);

		$this->add_control(
			'show_icon',
			[
				'label'        => esc_html__( 'Hide Empty', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'the7mk2' ),
				'label_off'    => esc_html__( 'No', 'the7mk2' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'selectors'    => [
					'{{WRAPPER}}.dt-empty-cart .dt-button-icon' => 'display: none;',
				],
				'condition'    => [
					'items_indicator!' => 'none',
				],
			]
		);

		$this->add_control(
			'indicator_heading',
			[
				'type'      => Controls_Manager::HEADING,
				'label'     => esc_html__( 'Indicator', 'the7mk2' ),
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'hide_indicator',
			[
				'label'                => esc_html__( 'Indicator', 'the7mk2' ),
				'type'                 => Controls_Manager::SELECT,
				'options'              => $visibility_options,
				'device_args'          => [
					'tablet' => [
						'default' => '',
						'options' => $visibility_options_on_devices,
					],
					'mobile' => [
						'default' => '',
						'options' => $visibility_options_on_devices,
					],
				],
				'default'              => 'show',
				'selectors_dictionary' => [
					'show' => $this->combine_to_css_vars_definition_string(
						[
							'indicator-display'    => 'inline-flex',
							'indicator-visibility' => '1',
						]
					),
					'hide' => $this->combine_to_css_vars_definition_string(
						[
							'indicator-display'    => 'none',
							'indicator-visibility' => '0',
						]
					),
				],
				'selectors'            => [
					'{{WRAPPER}}' => '{{VALUE}}',
				],
			]
		);

		$this->add_control(
			'items_indicator',
			[
				'label'                => esc_html__( 'Items Indicator', 'the7mk2' ),
				'type'                 => Controls_Manager::SELECT,
				'options'              => [
					'bubble' => esc_html__( 'Bubble', 'the7mk2' ),
					'plain'  => esc_html__( 'Text', 'the7mk2' ),
				],
				'render_type'          => 'template',
				'selectors_dictionary' => [
					'plain'  => $this->combine_to_css_vars_definition_string(
						[
							'indicator-visibility' => '1',
						]
					),
					'bubble' => $this->combine_to_css_vars_definition_string(
						[
							'indicator-visibility' => '0',
						]
					),
				],
				'selectors'            => [
					'{{WRAPPER}}.dt-menu-cart--items-indicator-bubble .dt-menu-cart__toggle' => '{{VALUE}}',
				],
				'prefix_class'         => 'dt-menu-cart--items-indicator-',
				'default'              => 'bubble',
			]
		);

		$this->add_control(
			'hide_empty_indicator',
			[
				'label'        => esc_html__( 'Hide Empty', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'the7mk2' ),
				'label_off'    => esc_html__( 'No', 'the7mk2' ),
				'return_value' => 'yes',
				'selectors'    => [
					'{{WRAPPER}}.dt-empty-cart' => $this->combine_to_css_vars_definition_string(
						[
							'indicator-display'    => 'none',
							'indicator-visibility' => '0',
						]
					),
				],
				'condition'    => [
					'items_indicator!' => 'none',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return array
	 */
	protected function get_popups_list() {
		$popups_query = new \WP_Query(
			[
				'post_type'      => Source_Local::CPT,
				'post_status'    => 'publish',
				'posts_per_page' => - 1,
				'meta_key'       => '_elementor_template_type',
				'meta_value'     => PopupModule::DOCUMENT_TYPE,
			]
		);

		return wp_list_pluck( $popups_query->posts, 'post_title', 'ID' );
	}

	/**
	 * @return void
	 */
	protected function add_box_style_conrols() {
		$this->start_controls_section(
			'section_toggle_style',
			[
				'label' => esc_html__( 'Box', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'toggle_button_border_width',
			[
				'label'     => esc_html__( 'Border Width', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 20,
					],
				],
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}}' => '--toggle-button-border-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'toggle_button_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}}' => '--toggle-button-border-radius: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'toggle_button_padding',
			[
				'label'      => esc_html__( 'Padding', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}}' => '--toggle-icon-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'toggle_button_colors' );
		// Normal colors.
		$this->start_controls_tab( 'tab_toggle_style_normal', [ 'label' => esc_html__( 'Normal', 'the7mk2' ) ] );
		$this->add_toggle_button_color_controls( '' );
		$this->end_controls_tab();

		// Hover colors.
		$this->start_controls_tab( 'tab_toggle_style_hover', [ 'label' => esc_html__( 'Hover', 'the7mk2' ) ] );
		$this->add_toggle_button_color_controls( '_hover' );
		$this->end_controls_tab();

		$this->end_controls_tabs();
		$this->add_control(
			'toggle_button_color_sticky',
			[
				'label'       => esc_html__( 'Change Colors', 'the7mk2' ),
				'type'        => Controls_Manager::SWITCHER,
				'separator'   => 'before',
				'description' => esc_html__( 'When “Sticky” and “Change Styles when Sticky” are ON for the parent section.', 'the7mk2' ),
			]
		);
		$this->start_controls_tabs(
			'tabs_toggle_sticky_style',
			[
				'condition' => [
					'toggle_button_color_sticky!' => '',
				],
			]
		);

		// Normal colors.
		$this->start_controls_tab( 'tab_toggle_style_sticky_normal', [ 'label' => esc_html__( 'Normal', 'the7mk2' ) ] );
		$this->add_toggle_button_color_controls( '', true );
		$this->end_controls_tab();

		// Hover colors.
		$this->start_controls_tab( 'tab_toggle_style_sticky_hover', [ 'label' => esc_html__( 'Hover', 'the7mk2' ) ] );
		$this->add_toggle_button_color_controls( '_hover', true );
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function add_title_style_controls() {
		$this->start_controls_section(
			'section_title_style',
			[
				'label' => esc_html__( 'Title', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'title_typography',
				'selector'  => '{{WRAPPER}} .dt-cart-title, {{WRAPPER}} .dt-cart-subtotal, {{WRAPPER}} .dt-cart-indicator',
				'condition' => [
					'title_text!' => '',
				],
			]
		);

		$this->start_controls_tabs( 'toggle_title_colors' );

		$this->start_controls_tab( 'toggle_title_normal_colors', [ 'label' => esc_html__( 'Normal', 'the7mk2' ) ] );

		$this->add_control(
			'toggle_title_text_color',
			[
				'label'     => esc_html__( 'Title Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dt-menu-cart__toggle_button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'toggle_subtotal_text_color',
			[
				'label'     => esc_html__( 'Subtotal Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dt-cart-subtotal' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'indicator_text_color',
			[
				'label'     => esc_html__( 'Indicator Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dt-cart-indicator' => 'color: {{VALUE}};',
				],
				'condition' => [
					'items_indicator' => 'plain',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'toggle_title_hover_colors', [ 'label' => esc_html__( 'Hover', 'the7mk2' ) ] );

		$this->add_control(
			'toggle_title_hover_text_color',
			[
				'label'     => esc_html__( 'Text Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dt-menu-cart__toggle_button:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'toggle_subtotal_hover_text_color',
			[
				'label'     => esc_html__( 'Subtotal Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dt-menu-cart__toggle_button:hover .dt-cart-subtotal' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'indicator_hover_text_color',
			[
				'label'     => esc_html__( 'Indicator Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dt-menu-cart__toggle_button:hover .dt-cart-indicator' => 'color: {{VALUE}};',
				],
				'condition' => [
					'items_indicator' => 'plain',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();
		$this->add_control(
			'title_color_sticky',
			[
				'label'       => esc_html__( 'Change Colors', 'the7mk2' ),
				'type'        => Controls_Manager::SWITCHER,
				'separator'   => 'before',
				'description' => esc_html__( 'When “Sticky” and “Change Styles when Sticky” are ON for the parent section.', 'the7mk2' ),
			]
		);
		$this->start_controls_tabs(
			'tabs_title_sticky_style',
			[
				'condition' => [
					'title_color_sticky!' => '',
				],
			]
		);

		// Normal colors.
		$this->start_controls_tab( 'tab_title_sticky', [ 'label' => esc_html__( 'Normal', 'the7mk2' ) ] );
		$this->add_title_color_sticky_controls( '' );
		$this->end_controls_tab();

		// Hover colors.
		$this->start_controls_tab( 'tab_title_sticky_hover', [ 'label' => esc_html__( 'Hover', 'the7mk2' ) ] );
		$this->add_title_color_sticky_controls( '_hover' );
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function add_icon_style_controls() {
		$this->start_controls_section(
			'section_icon_style',
			[
				'label'     => esc_html__( 'Cart Icon', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'cart_icon[value]!' => '',
				],
			]
		);

		$icon_gap_definition = 'calc(max(var(--title-visibility,1), var(--price-visibility,1), var(--indicator-visibility,1)) * var(--cart-icon-spacing-value, 10px));';

		$this->add_responsive_control(
			'icon_position',
			[
				'label'                => esc_html__( 'Position', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => [
					'left'  => [
						'title' => esc_html__( 'Left', 'the7mk2' ),
						'icon'  => 'eicon-h-align-left',
					],
					'top'   => [
						'title' => esc_html__( 'Top', 'the7mk2' ),
						'icon'  => 'eicon-v-align-top',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'the7mk2' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'selectors_dictionary' => [
					'top'   => 'flex-flow: column wrap; --icon-order: 0; column-gap: 0; row-gap: ' . $icon_gap_definition,
					'left'  => 'flex-flow: row nowrap; --icon-order: 0; row-gap: 0; column-gap: ' . $icon_gap_definition,
					'right' => 'flex-flow: row nowrap; --icon-order: 4; row-gap: 0; column-gap: ' . $icon_gap_definition,
				],
				'selectors'            => [
					'{{WRAPPER}} .dt-menu-cart__toggle_button' => '{{VALUE}}',
				],
				'default'              => 'left',
			]
		);

		$this->add_responsive_control(
			'toggle_icon_spacing',
			[
				'label'      => esc_html__( 'Spacing', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'size_units' => [ 'em', 'px' ],
				'selectors'  => [
					'{{WRAPPER}}' => '--cart-icon-spacing-value: {{SIZE}}{{UNIT}};',
				],
				'separator'  => 'before',
			]
		);

		$this->add_responsive_control(
			'toggle_icon_size',
			[
				'label'      => esc_html__( 'Size', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'size_units' => [ '%', 'px', 'em' ],
				'selectors'  => [
					'{{WRAPPER}}' => '--toggle-icon-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_padding',
			[
				'label'      => esc_html__( 'Padding', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'selectors'  => [
					'{{WRAPPER}} .elementor-icon' => 'padding: {{SIZE}}{{UNIT}};',
				],
				'size_units' => [ 'px', 'em' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
					'em' => [
						'min'  => 0.1,
						'max'  => 5,
						'step' => 0.01,
					],
				],
			]
		);

		$this->add_control(
			'border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .elementor-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'toggle_icon_colors' );

		$this->start_controls_tab( 'toggle_icon_empty_colors', [ 'label' => esc_html__( 'Empty cart', 'the7mk2' ) ] );

		$this->add_control(
			'toggle_empty_icon_color',
			[
				'label'     => esc_html__( 'Icon Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-icon'     => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementor-icon svg' => 'fill: {{VALUE}};  color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_bg_color',
			[
				'label'     => esc_html__( 'Background', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-icon' => 'background: {{VALUE}}',
				],

			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'icon_normal_box_shadow',
				'label'    => esc_html__( 'Box Shadow', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .elementor-icon',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'toggle_icon_full_colors', [ 'label' => esc_html__( 'Full cart', 'the7mk2' ) ] );

		$this->add_control(
			'toggle_full_icon_color',
			[
				'label'     => esc_html__( 'Icon Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}:not(.dt-empty-cart) .dt-menu-cart__toggle .elementor-icon'     => 'color: {{VALUE}};',
					'{{WRAPPER}}:not(.dt-empty-cart) .dt-menu-cart__toggle .elementor-icon svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_bg_full_color',
			[
				'label'     => esc_html__( 'Background', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}:not(.dt-empty-cart) .dt-menu-cart__toggle .elementor-icon' => 'background: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'icon_full_box_shadow',
				'label'    => esc_html__( 'Box Shadow', 'the7mk2' ),
				'selector' => '{{WRAPPER}}:not(.dt-empty-cart) .dt-menu-cart__toggle .elementor-icon',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'toggle_icon_hover_colors', [ 'label' => esc_html__( 'Hover', 'elementor-pro' ) ] );

		$this->add_control(
			'toggle_button_hover_icon_color',
			[
				'label'     => esc_html__( 'Icon Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dt-menu-cart__toggle:hover .dt-button-icon .elementor-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .dt-menu-cart__toggle:hover .elementor-icon svg'             => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_bg_hover_color',
			[
				'label'     => esc_html__( 'Background', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dt-menu-cart__toggle:hover .elementor-icon' => 'background: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'icon_hover_box_shadow',
				'label'    => esc_html__( 'Box Shadow', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .dt-menu-cart__toggle:hover .elementor-icon',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'icon_color_sticky',
			[
				'label'       => esc_html__( 'Change Colors', 'the7mk2' ),
				'type'        => Controls_Manager::SWITCHER,
				'separator'   => 'before',
				'description' => esc_html__( 'When “Sticky” and “Change Styles when Sticky” are ON for the parent section.', 'the7mk2' ),
			]
		);

		$this->start_controls_tabs(
			'tabs_icon_sticky_style',
			[
				'condition' => [
					'icon_color_sticky!' => '',
				],
			]
		);

		// Normal colors.
		$this->start_controls_tab( 'tab_icon_sticky', [ 'label' => esc_html__( 'Empty cart', 'the7mk2' ) ] );
		$this->add_icon_sticky_controls( '' );
		$this->end_controls_tab();

		// Hover colors.
		$this->start_controls_tab( 'tab_icon_sticky_full', [ 'label' => esc_html__( 'Full cart', 'the7mk2' ) ] );
		$this->add_icon_sticky_controls( '_full' );
		$this->end_controls_tab();

		// Active colors.
		$this->start_controls_tab( 'tab_icon_sticky_hover', [ 'label' => esc_html__( 'Hover', 'the7mk2' ) ] );
		$this->add_icon_sticky_controls( '_hover' );
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function add_indicator_style_controls() {
		$this->start_controls_section(
			'section_indicator_style',
			[
				'label'     => esc_html__( 'Indicator', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'items_indicator' => 'bubble',
				],
			]
		);

		$this->add_control(
			'indicator_alignment',
			[
				'label'   => esc_html__( 'Align with', 'the7mk2' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'icon' => esc_html__( 'Icon', 'the7mk2' ),
					'box'  => esc_html__( 'Box', 'the7mk2' ),
				],
				'default' => 'box',
			]
		);

		$this->add_responsive_control(
			'indicator_h_position',
			[
				'label'                => esc_html__( 'Horizontal Position', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'label_block'          => false,
				'options'              => [
					'left'   => [
						'title' => esc_html__( 'Left', 'the7mk2' ),
						'icon'  => 'eicon-h-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'the7mk2' ),
						'icon'  => 'eicon-h-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'the7mk2' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'selectors_dictionary' => [
					'left'   => 'left: var(--indicator-h-distance, 0px); right: auto; transform: translate3d(0,0,0);',
					'center' => 'right: auto; left: 50%; transform: translate3d(calc(-50% + var(--indicator-h-distance, 0px)),0,0);',
					'right'  => 'right: var(--indicator-h-distance, 0px); left: auto; transform: translate3d(0,0,0);',
				],
				'selectors'            => [
					'{{WRAPPER}} [data-counter]:before, {{WRAPPER}} .dt-cart-indicator' => ' {{VALUE}};',
				],
				'default'              => 'right',
			]
		);

		$this->add_responsive_control(
			'indicator_h_offset',
			[
				'label'      => esc_html__( 'Offset', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 0,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => - 100,
						'max'  => 100,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}}' => ' --indicator-h-distance: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'indicator_v_position',
			[
				'label'                => esc_html__( 'Vertical Position', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'label_block'          => false,
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
				'selectors_dictionary' => [
					'top'    => 'top: var(--indicator-v-distance, 0px); bottom: auto; margin-top: 0;',
					'center' => 'bottom: auto; top: 50%; margin-top: calc(-1*var(--indicator-bg-size, 1.3em)/2 + var(--indicator-v-distance, 0px));',
					'bottom' => 'bottom: var(--indicator-v-distance, 0px); top: auto; margin-top: 0;',
				],
				'selectors'            => [
					'{{WRAPPER}} [data-counter]:before, {{WRAPPER}} .dt-cart-indicator' => ' {{VALUE}};',
				],
				'default'              => 'top',
			]
		);

		$this->add_responsive_control(
			'indicator_v_offset',
			[
				'label'      => esc_html__( 'Offset', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 0,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => - 100,
						'max'  => 100,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}}' => ' --indicator-v-distance: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'indicator_divider',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'indicator_typography',
				'selector' => '{{WRAPPER}} [data-counter]:before, {{WRAPPER}} .dt-cart-indicator',
			]
		);

		$this->add_responsive_control(
			'indicator_bg_size',
			[
				'label'      => esc_html__( 'Background Size', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'size_units' => [ '%', 'px', 'em' ],
				'selectors'  => [
					'{{WRAPPER}}' => '--indicator-bg-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.dt-menu-cart--items-indicator-bubble [data-counter]:before' => 'min-width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'toggle_indicator_colors' );

		$this->start_controls_tab( 'toggle_indicator_empty_colors', [ 'label' => esc_html__( 'Empty cart', 'the7mk2' ) ] );

		$this->add_control(
			'toggle_empty_indicator_color',
			[
				'label'     => esc_html__( 'Text Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} [data-counter]:before' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'empty_indicator_bg_color',
			[
				'label'     => esc_html__( 'Background color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} [data-counter]:before' => 'background: {{VALUE}}',
				],

			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'toggle_indicator_normal_colors', [ 'label' => esc_html__( 'Full cart', 'the7mk2' ) ] );

		$this->add_control(
			'items_indicator_text_color',
			[
				'label'     => esc_html__( 'Text Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}:not(.dt-empty-cart) .dt-menu-cart__toggle [data-counter]:before' => 'color: {{VALUE}};',
				],

			]
		);

		$this->add_control(
			'items_indicator_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}.dt-menu-cart--items-indicator-bubble:not(.dt-empty-cart) .dt-menu-cart__toggle [data-counter]:before' => 'background: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'toggle_indicator_hover_colors', [ 'label' => esc_html__( 'Hover', 'the7mk2' ) ] );

		$this->add_control(
			'items_indicator_text_hover_color',
			[
				'label'     => esc_html__( 'Text Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dt-menu-cart__toggle:hover [data-counter]:before' => 'color: {{VALUE}};',
				],

			]
		);

		$this->add_control(
			'items_indicator_background_hover_color',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}.dt-menu-cart--items-indicator-bubble .dt-menu-cart__toggle:hover [data-counter]:before' => 'background: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();
		$this->add_control(
			'tabs_indicator_sticky',
			[
				'label'       => esc_html__( 'Change Colors', 'the7mk2' ),
				'type'        => Controls_Manager::SWITCHER,
				'separator'   => 'before',
				'description' => esc_html__( 'When “Sticky” and “Change Styles when Sticky” are ON for the parent section.', 'the7mk2' ),
			]
		);

		$this->start_controls_tabs(
			'tabs_indicator_sticky_style',
			[
				'condition' => [
					'tabs_indicator_sticky!' => '',
				],
			]
		);
		// Normal colors.
		$this->start_controls_tab( 'tab_indicator_style_sticky_normal', [ 'label' => esc_html__( 'Empty cart', 'the7mk2' ) ] );
		$this->add_indicator_sticky_controls( '', true );
		$this->end_controls_tab();

		// Hover colors.
		$this->start_controls_tab( 'tab_indicator_style_sticky_full', [ 'label' => esc_html__( 'Full cart', 'the7mk2' ) ] );
		$this->add_indicator_sticky_controls( '_full', true );
		$this->end_controls_tab();

		// Active colors.
		$this->start_controls_tab( 'tab_indicator_style_sticky_hover', [ 'label' => esc_html__( 'Hover', 'the7mk2' ) ] );
		$this->add_indicator_sticky_controls( '_hover', true );
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}
}


