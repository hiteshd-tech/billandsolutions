<?php
/**
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widget_Templates;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Utils;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Class Button
 *
 * @package The7\Mods\Compatibility\Elementor\Widget_Templates
 */
class Button extends Abstract_Template {

	const ICON_MANAGER  = 'icon_manager';
	const ICON_SWITCHER = 'icon_switcher';

	/**
	 * Add button style controls section.
	 *
	 * @param string $icon_controls Icon controls type. Can be Button::ICON_MANAGER or Button::ICON_SWITCHER.
	 * @param array  $condition     Section conditions.
	 * @param array  $override      Controls override.
	 */
	public function add_style_controls( $icon_controls = self::ICON_MANAGER, $condition = [], $override = [], $prefix = '', $selector_prefix = '', $section_label = ''  ) {
		if (empty($section_label)){
			$section_label = esc_html__( 'Button', 'the7mk2' );
		}

		$this->widget->start_controls_section(
			$prefix . 'button_style_section',
			[
				'label'     => $section_label,
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => $condition,
			]
		);

		if ( $icon_controls === self::ICON_MANAGER ) {
			$button_icon     = [
				'label'          => esc_html__( 'Icon', 'the7mk2' ),
				'type'           => Controls_Manager::ICONS,
				'default'        => [
					'value'   => '',
					'library' => '',
				],
				'skin'           => 'inline',
				'label_block'    => false,
				'style_transfer' => false,
			];
			$icon_conditions = [
				$prefix . 'button_icon[value]!' => '',
			];
		} else {
			$button_icon     = [
				'label'          => esc_html__( 'Icon', 'the7mk2' ),
				'type'           => Controls_Manager::SWITCHER,
				'label_on'       => esc_html__( 'Show', 'the7mk2' ),
				'label_off'      => esc_html__( 'Hide', 'the7mk2' ),
				'return_value'   => 'y',
				'default'        => 'y',
				'style_transfer' => false,
			];
			$icon_conditions = [
				$prefix . 'button_icon' => 'y',
			];
		}

		$fields = [
			$prefix . 'button_size'          => [
				'label'          => esc_html__( 'Size', 'the7mk2' ),
				'type'           => Controls_Manager::SELECT,
				'default'        => 'xs',
				'options'        => The7_Elementor_Widget_Base::get_button_sizes(),
				'style_transfer' => true,
			],
			$prefix . 'button_icon'          => $button_icon,
			$prefix . 'button_icon_size'     => [
				'label'      => esc_html__( 'Icon Size', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => '',
				],
				'size_units'   => [ 'px' ],
				'range'        => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} '  => '--btn-icon-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} ' . $selector_prefix . '.box-button.elementor-button i'   => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} ' . $selector_prefix . '.box-button.elementor-button svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
				],
				'condition'    => $icon_conditions,
				'control_type' => self::CONTROL_TYPE_RESPONSIVE,
			],
			$prefix . 'button_icon_position' => [
				'label'                => esc_html__( 'Icon Position', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'toggle'               => false,
				'default'              => 'after',
				'options'              => [
					'before' => [
						'title' => esc_html__( 'Before', 'the7mk2' ),
						'icon'  => 'eicon-h-align-left',
					],
					'after'  => [
						'title' => esc_html__( 'After', 'the7mk2' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'selectors_dictionary' => [
					'before' => 'order: -1; margin: 0 var(--btn-icon-spacing) 0 0;',
					'after'  => 'order: 1; margin: 0 0 0 var(--btn-icon-spacing);',
				],
				//Exclude product icon text
				'selectors'            => [
					'{{WRAPPER}} ' . $selector_prefix . '.box-button > span:not(.filter-popup)' => 'display: flex; align-items: center; justify-content: center; flex-flow: row nowrap;',
					'{{WRAPPER}} ' . $selector_prefix . '.box-button i'      => '{{VALUE}}',
					'{{WRAPPER}} ' . $selector_prefix . '.box-button svg'    => '{{VALUE}}',
					'{{WRAPPER}} ' . $selector_prefix . '.box-button .popup-icon'    => '{{VALUE}}',
				],
				'condition'            => $icon_conditions,
			],
			$prefix . 'button_icon_spacing'  => [
				'label'        => esc_html__( 'Icon Spacing', 'the7mk2' ),
				'type'         => Controls_Manager::SLIDER,
				'default'      => [
					'unit' => 'px',
					'size' => '',
				],
				'size_units'   => [ 'px' ],
				'range'        => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
				],
				'selectors'    => [
					'{{WRAPPER}} ' . $selector_prefix . '.box-button' => '--btn-icon-spacing: {{SIZE}}{{UNIT}};',
				],
				'condition'    => $icon_conditions,
				'control_type' => self::CONTROL_TYPE_RESPONSIVE,
			],
			$prefix . 'button_style_divider'  => [
				'type' => Controls_Manager::DIVIDER,
			],

			$prefix . 'style_btn_title'  => [
				'label' => esc_html__( 'Style', 'the7mk2' ),
				'type'  => Controls_Manager::HEADING,
			],
			$prefix . 'button_typography'    => [
				'type'           => Group_Control_Typography::get_type(),
				'name'           => $prefix . 'button_typography',
				'selector'       => '{{WRAPPER}} ' . $selector_prefix . '.box-button',
				'fields_options' => [
					'font_size' => [
						'selectors' => [
							'{{SELECTOR}}'     => 'font-size: {{SIZE}}{{UNIT}}',
						],
					],
				],
				'control_type'   => self::CONTROL_TYPE_GROUP,
			],

			$prefix . 'button_min_width'     => [
				'label'        => esc_html__( 'Min Width', 'the7mk2' ),
				'type'         => Controls_Manager::NUMBER,
				'selectors'    => [
					'{{WRAPPER}}' => '--box-button-width: {{SIZE}}px;',
					'{{WRAPPER}} ' . $selector_prefix . '.box-button' => 'min-width: {{SIZE}}px;',
				],
				'control_type' => self::CONTROL_TYPE_RESPONSIVE,
			],
			$prefix . 'button_min_height'    => [
				'label'        => esc_html__( 'Min Height', 'the7mk2' ),
				'type'         => Controls_Manager::NUMBER,
				'selectors'    => [
					'{{WRAPPER}} ' => '--box-button-min-height: {{SIZE}}px;',
					'{{WRAPPER}} ' . $selector_prefix . '.box-button' => 'min-height: {{SIZE}}px;',
				],
				'control_type' => self::CONTROL_TYPE_RESPONSIVE,
			],
			$prefix . 'button_text_padding'  => [
				'label'        => esc_html__( 'Text Padding', 'the7mk2' ),
				'type'         => Controls_Manager::DIMENSIONS,
				'size_units'   => [ 'px', 'em', '%' ],
				'selectors'    => [
					'{{WRAPPER}} ' . $selector_prefix => '--box-button-padding-top: {{TOP}}{{UNIT}}; --box-button-padding-right: {{RIGHT}}{{UNIT}}; --box-button-padding-bottom: {{BOTTOM}}{{UNIT}}; --box-button-padding-left: {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} ' . $selector_prefix . '.box-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'control_type' => self::CONTROL_TYPE_RESPONSIVE,
			],
			$prefix . 'button_border'        => [
				'type'         => Group_Control_Border::get_type(),
				'name'         => $prefix . 'button_border',
				'selector'     => '{{WRAPPER}} ' . $selector_prefix . '.box-button',
				'exclude'      => [ 'color' ],
				'control_type' => self::CONTROL_TYPE_GROUP,
			],
			$prefix . 'button_border_radius' => [
				'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} ' . $selector_prefix . '.box-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			],
			$prefix . 'tabs_button_style'    => [
				'control_type' => self::CONTROL_TYPE_TABS,
				'fields'       => [
					$prefix . 'tab_button_normal' => [
						'label'  => esc_html__( 'Normal', 'the7mk2' ),
						'fields' => [
							$prefix . 'button_text_color'   => [
								'label'     => esc_html__( 'Text Color', 'the7mk2' ),
								'type'      => Controls_Manager::COLOR,
								'default'   => '',
								'selectors' => [
									'{{WRAPPER}} ' . $selector_prefix . '.box-button, {{WRAPPER}} ' . $selector_prefix . '.box-button *'       => 'color: {{VALUE}};',
									'{{WRAPPER}} ' . $selector_prefix . '.box-button svg' => 'fill: {{VALUE}};  color: {{VALUE}};',
								],
							],
							$prefix . 'button_icon_color'   => [
								'label'     => esc_html__( 'Icon Color', 'the7mk2' ),
								'type'      => Controls_Manager::COLOR,
								'default'   => '',
								'selectors' => [
									'{{WRAPPER}} ' . $selector_prefix    => '--box-button-icon-color: {{VALUE}};',
									'{{WRAPPER}} ' . $selector_prefix . '.box-button i, {{WRAPPER}} ' . $selector_prefix . '.box-button.elementor-button:hover i, {{WRAPPER}} ' . $selector_prefix . '.box-button.elementor-button:focus i, {{WRAPPER}} .box-hover:hover ' . $selector_prefix . '.box-button.elementor-button i'       => 'color: {{VALUE}};',
									'{{WRAPPER}} ' . $selector_prefix . '.box-button svg, {{WRAPPER}} ' . $selector_prefix . '.box-button.elementor-button:hover svg, {{WRAPPER}} ' . $selector_prefix . '.box-button.elementor-button:focus svg, {{WRAPPER}} .box-hover:hover ' . $selector_prefix . '.box-button.elementor-button svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
								],
							],
							$prefix . 'button_background'   => [
								'type'           => Group_Control_Background::get_type(),
								'name'           => $prefix . 'button_background',
								'label'          => esc_html__( 'Background', 'the7mk2' ),
								'types'          => [ 'classic', 'gradient' ],
								'exclude'        => [ 'image' ],
								// Be careful, magic transition selector here.
								'selector'       => ' {{WRAPPER}} ' . $selector_prefix . '.box-button, {{WRAPPER}} ' . $selector_prefix . '.box-button .popup-icon,  {{WRAPPER}} ' . $selector_prefix . '.box-button:hover,  {{WRAPPER}} ' . $selector_prefix . '.box-button:focus, {{WRAPPER}} .box-hover:hover ' . $selector_prefix . '.box-button',
								'fields_options' => [
									'background' => [
										'default' => 'classic',
									],
									'color'      => [
										'selectors' => [
											'{{SELECTOR}}' => 'background: {{VALUE}}',
										],
									],
								],
								'control_type'   => self::CONTROL_TYPE_GROUP,
							],
							$prefix . 'button_border_color' => [
								'label'     => esc_html__( 'Border Color', 'the7mk2' ),
								'type'      => Controls_Manager::COLOR,
								'selectors' => [
									'{{WRAPPER}} ' . $selector_prefix . '.box-button,  {{WRAPPER}} ' . $selector_prefix . '.box-button:hover,  {{WRAPPER}} ' . $selector_prefix . '.box-button:focus, {{WRAPPER}} .box-hover:hover ' . $selector_prefix . '.box-button' => 'border-color: {{VALUE}};',
								],
								'condition' => [
									$prefix . 'button_border_border!' => '',
								],
							],
							$prefix . 'button_shadow'       => [
								'type'         => Group_Control_Box_Shadow::get_type(),
								'name'         => $prefix . 'button_shadow',
								'selector'     => '{{WRAPPER}} ' . $selector_prefix . '.box-button,  {{WRAPPER}} ' . $selector_prefix . '.box-button:hover, {{WRAPPER}} ' . $selector_prefix . '.box-button:focus, {{WRAPPER}} .box-hover:hover ' . $selector_prefix . '.box-button',
								'control_type' => self::CONTROL_TYPE_GROUP,
							],
						],
					],
					$prefix . 'tab_button_hover'  => [
						'label'  => esc_html__( 'Hover', 'the7mk2' ),
						'fields' => [
							$prefix . 'button_hover_color'        => [
								'label'     => esc_html__( 'Text Color', 'the7mk2' ),
								'type'      => Controls_Manager::COLOR,
								'selectors' => [
									'{{WRAPPER}} ' . $selector_prefix . '.box-button.elementor-button:hover, {{WRAPPER}} ' . $selector_prefix . '.box-button.elementor-button:focus, {{WRAPPER}} ' . $selector_prefix . '.box-button.elementor-button:hover *, {{WRAPPER}} ' . $selector_prefix . '.box-button.elementor-button:focus *, {{WRAPPER}} .box-hover:hover ' . $selector_prefix . '.box-button.elementor-button, {{WRAPPER}} .box-hover:hover ' . $selector_prefix . '.box-button.elementor-button *'             => 'color: {{VALUE}};',
									'{{WRAPPER}} ' . $selector_prefix . '.box-button.elementor-button:hover svg, {{WRAPPER}} ' . $selector_prefix . '.box-button.elementor-button:focus svg, {{WRAPPER}} .box-hover:hover ' . $selector_prefix . '.box-button.elementor-button svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
								],
							],
							$prefix . 'button_hover_icon_color'        => [
								'label'     => esc_html__( 'Icon Color', 'the7mk2' ),
								'type'      => Controls_Manager::COLOR,
								'selectors' => [
									'{{WRAPPER}} ' . $selector_prefix . '.box-button.elementor-button:hover i, {{WRAPPER}} ' . $selector_prefix . '.box-button.elementor-button:focus i, {{WRAPPER}} .box-hover:hover ' . $selector_prefix . '.box-button.elementor-button i'             => 'color: {{VALUE}};',
									'{{WRAPPER}} ' . $selector_prefix . '.box-button.elementor-button:hover svg, {{WRAPPER}} ' . $selector_prefix . '.box-button.elementor-button:focus svg, {{WRAPPER}} .box-hover:hover ' . $selector_prefix . '.box-button.elementor-button svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
								],
							],
							$prefix . 'button_background_hover'   => [
								'type'           => Group_Control_Background::get_type(),
								'name'           => $prefix . 'button_background_hover',
								'label'          => esc_html__( 'Background', 'the7mk2' ),
								'types'          => [ 'classic', 'gradient' ],
								'exclude'        => [ 'image' ],
								'selector'       => '{{WRAPPER}} ' . $selector_prefix . '.box-button.elementor-button:hover, {{WRAPPER}} ' . $selector_prefix . '.box-button.elementor-button:focus, {{WRAPPER}} ' . $selector_prefix . '.box-button.elementor-button:hover .popup-icon, {{WRAPPER}} ' . $selector_prefix . '.box-button.elementor-button:focus .popup-icon,  {{WRAPPER}} .box-hover:hover ' . $selector_prefix . '.box-button.elementor-button, {{WRAPPER}} .box-hover:hover ' . $selector_prefix . '.box-button.elementor-button .popup-icon',
								'fields_options' => [
									'background' => [
										'default' => 'classic',
									],
									'color'      => [
										'selectors' => [
											'{{SELECTOR}}' => 'background: {{VALUE}}',
										],
									],
								],
								'control_type'   => self::CONTROL_TYPE_GROUP,
							],
							$prefix . 'button_hover_border_color' => [
								'label'     => esc_html__( 'Border Color', 'the7mk2' ),
								'type'      => Controls_Manager::COLOR,
								'selectors' => [
									'{{WRAPPER}} ' . $selector_prefix . '.box-button.elementor-button:hover, {{WRAPPER}} ' . $selector_prefix . '.box-button.elementor-button:focus, {{WRAPPER}} .box-hover:hover ' . $selector_prefix . '.box-button.elementor-button' => 'border-color: {{VALUE}};',
								],
								'condition' => [
									$prefix . 'button_border_border!' => '',
								],
							],
							$prefix . 'button_hover_shadow'       => [
								'type'         => Group_Control_Box_Shadow::get_type(),
								'name'         => $prefix . 'button_hover_shadow',
								'selector'     => '{{WRAPPER}} ' . $selector_prefix . '.box-button:hover, {{WRAPPER}} ' . $selector_prefix . '.box-button:focus, {{WRAPPER}} .box-hover:hover ' . $selector_prefix . '.box-button',
								'control_type' => self::CONTROL_TYPE_GROUP,
							],
						],
					],
				],
			],
			$prefix . 'gap_above_button'     => [
				'label'      => esc_html__( 'Spacing Above Button', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'control_type' => self::CONTROL_TYPE_RESPONSIVE,
				'selectors'  => [
					'{{WRAPPER}} ' . $selector_prefix . '.box-button' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
				'separator'    => 'before',
			],
		];

		$this->setup_controls( $fields, $override );

		$this->widget->end_controls_section();
	}

	/**
	 * Add button base render attributes.
	 *
	 * @param string $element Element name.
	 */
	public function add_base_render_attributes( $element, $prefix = '') {
		$settings = $this->get_settings();
		$button_size = $settings[ $prefix . 'button_size' ];
		if ( ! empty( $button_size ) ) {
			$this->widget->add_render_attribute( $element, 'class', 'box-button elementor-button elementor-size-' . $button_size );
		}
	}

	/**
	 * Add render attributes for the case when there is no text.
	 *
	 * @param string $element Element name.
	 */
	public function add_icon_only_render_attributes( $element ) {
		$this->widget->add_render_attribute( $element, 'class', 'no-text' );
	}

	/**
	 * Determine if button icon is visible.
	 *
	 * @return bool
	 */
	public function is_icon_visible( $prefix = '') {
		$button_icon = $this->get_settings( $prefix .'button_icon' );

		if ( is_array( $button_icon ) ) {
			return ! empty( $button_icon['value'] );
		}

		return (bool) $button_icon;
	}

	/**
	 * Output button HTML.
	 *
	 * @param string $element Element name.
	 * @param string $text    Button text. Should be escaped beforehand.
	 * @param string $tag     Button HTML tag, 'a' by default.
	 */
	public function render_button( $element, $text = '', $tag = 'a', $prefix = '' ) {
		$settings = $this->get_settings();
        $tag = Utils::validate_html_tag( $tag );

		$this->add_base_render_attributes( $element, $prefix );

		if ( ! $text ) {
			$this->add_icon_only_render_attributes( $element );
		}

		// Output native icon only if it's Button::ICON_MANAGER.
		if ( isset( $settings[ $prefix . 'button_icon' ] ) && is_array( $settings[ $prefix . 'button_icon' ] ) ) {
			$text .= $this->widget->get_elementor_icon_html( $settings[ $prefix . 'button_icon' ], 'i', [
					'class' => 'elementor-button-icon',
				] );
		}

		echo '<' . $tag . ' ' . $this->widget->get_render_attribute_string( $element ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Escaped above
		echo $text; // Should be escaped beforehand.
		echo '</' . $tag . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Escaped above
	}
}
