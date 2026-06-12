<?php
/**
 * The7 login widget for Elementor.
 *
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * The7 login widget for Elementor.
 */
class Login extends The7_Elementor_Widget_Base {
	const STICKY_WRAPPER = '.the7-e-sticky-effects .elementor-element.elementor-element-{{ID}}';

	/**
	 * Get element name.
	 *
	 * Retrieve the element name.
	 *
	 * @return string The name.
	 */
	public function get_name() {
		return 'the7-login-widget';
	}

	/**
	 * @return string|null
	 */
	protected function the7_title() {
		return esc_html__( 'Login Link', 'the7mk2' );
	}

	/**
	 * @return string
	 */
	protected function the7_icon() {
		return 'eicon-lock-user';
	}

	/**
	 * @return string[]
	 */
	protected function the7_keywords() {
		return [ 'login', 'account', 'user' ];
	}

	/**
	 * @return string[]
	 */
	public function get_style_depends() {
		return [ 'the7-login-widget' ];
	}

	/**
	 * Register assets.
	 */
	protected function register_assets() {
		the7_register_style( 'the7-login-widget', THE7_ELEMENTOR_CSS_URI . '/the7-login-widget.css' );
	}

	/**
	 * @return void
	 */
	protected function register_controls() {
		// Content.
		$this->add_logged_out_controls();

		// Style.
		$this->add_box_content_style_controls();
		$this->add_icon_style_controls();
		$this->add_text_style_controls();
	}

	/**
	 * @return void
	 */
	protected function add_logged_out_controls() {
		$this->start_controls_section(
			'logged_out_section',
			[
				'label' => esc_html__( 'Content', 'the7mk2' ),
			]
		);

		$this->add_control(
			'logged_out_link_heading',
			[
				'label' => esc_html__( 'Logged out', 'the7mk2' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'logged_out_icon',
			[
				'label'       => esc_html__( 'Icon', 'the7mk2' ),
				'type'        => Controls_Manager::ICONS,
				'default'     => [
					'value'   => 'fas fa-user-circle',
					'library' => 'fa-solid',
				],
			]
		);

		$this->add_control(
			'logged_out_text',
			[
				'label'       => esc_html__( 'Text', 'the7mk2' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [
					'active' => true,
				],
				'default'     => esc_html__( 'Account', 'the7mk2' ),
				'placeholder' => esc_html__( 'Enter your text', 'the7mk2' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'logged_out_link',
			[
				'label'       => esc_html__( 'Link', 'the7mk2' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => [
					'active' => true,
				],
				'placeholder' => esc_html__( 'https://your-link.com', 'the7mk2' ),
			]
		);

		$this->add_control(
			'logged_in_link_heading',
			[
				'label'     => esc_html__( 'Logged in', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'logged_in_icon',
			[
				'label'       => esc_html__( 'Icon', 'the7mk2' ),
				'type'        => Controls_Manager::ICONS,
				'default'     => [
					'value'   => 'fas fa-user-circle',
					'library' => 'fa-solid',
				],
			]
		);

		$this->add_control(
			'logged_in_text',
			[
				'label'       => esc_html__( 'Text', 'the7mk2' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [
					'active' => true,
				],
				'default'     => esc_html__( 'Log in', 'the7mk2' ),
				'placeholder' => esc_html__( 'Enter your text', 'the7mk2' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'account_link',
			[
				'label'       => esc_html__( 'Link', 'the7mk2' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => [
					'active' => true,
				],
				'placeholder' => esc_html__( 'https://your-link.com', 'the7mk2' ),
			]
		);

		$this->add_responsive_control(
			'logged_out_text_align',
			[
				'label'                => esc_html__( 'Alignment', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => [
					'left'    => [
						'title' => esc_html__( 'Left', 'the7mk2' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center'  => [
						'title' => esc_html__( 'Center', 'the7mk2' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'   => [
						'title' => esc_html__( 'Right', 'the7mk2' ),
						'icon'  => 'eicon-text-align-right',
					],
					'justify' => [
						'title' => esc_html__( 'Justified', 'the7mk2' ),
						'icon'  => 'eicon-text-align-justify',
					],
				],
				'default'              => 'left',
				'selectors_dictionary' => [
					'left'    => '--align-content: flex-start; --text-align: left;',
					'center'  => '--align-content: center; --text-align: center;',
					'right'   => '--align-content: flex-end; --text-align: right;',
					'justify' => '--align-content: stretch; --text-align: justify;',
				],
				'selectors'            => [
					'{{WRAPPER}}' => ' {{VALUE}};',
				],
				'separator'            => 'before',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function add_box_content_style_controls() {
		$this->start_controls_section(
			'section_design_box',
			[
				'label' => esc_html__( 'Box', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'box_height',
			[
				'label'      => esc_html__( 'Height', 'the7mk2' ),
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
					'{{WRAPPER}} .the7-login-wrapper' => 'min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_position',
			[
				'label'                => esc_html__( 'Content Position', 'the7mk2' ),
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
					'top'    => $this->combine_to_css_vars_definition_string(
						[
							'content-position' => 'flex-start',
						]
					),
					'center' => $this->combine_to_css_vars_definition_string(
						[
							'content-position' => 'center',
						]
					),
					'bottom' => $this->combine_to_css_vars_definition_string(
						[
							'content-position' => 'flex-end',
						]
					),
				],
				'selectors'            => [
					'{{WRAPPER}}' => '{{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'     => 'box_border',
				'label'    => esc_html__( 'Border', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .the7-login-wrapper',
				'exclude'  => [
					'color',
				],
			]
		);

		$this->add_responsive_control(
			'box_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .the7-login-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'box_padding',
			[
				'label'      => esc_html__( 'Padding', 'the7mk2' ),
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
				'selectors'  => [
					'{{WRAPPER}} .the7-login-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_icon_box_style' );

		$this->start_controls_tab(
			'tab_color_normal',
			[
				'label' => esc_html__( 'Normal', 'the7mk2' ),
			]
		);
		$this->add_box_color_controls( 'normal' );

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_color_hover',
			[
				'label' => esc_html__( 'Hover', 'the7mk2' ),
			]
		);
		$this->add_box_color_controls( 'hover' );

		$this->end_controls_tab();

		$this->end_controls_tabs();
		$this->add_control(
			'tabs_box_style_sticky',
			[
				'label'       => esc_html__( 'Change colors', 'the7mk2' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => esc_html__( 'When “Sticky” and “Change Styles when Sticky” are ON for the parent section.', 'the7mk2' ),
				'separator'   => 'before',
			]
		);

		$this->start_controls_tabs(
			'tabs_box_style_sticky_colors',
			[
				'condition' => [
					'tabs_box_style_sticky!' => '',
				],
			]
		);

		$this->start_controls_tab(
			'box_style_sticky_colors_normal',
			[
				'label' => esc_html__( 'Normal', 'the7mk2' ),
			]
		);
		$this->add_box_color_controls( 'normal', 'sticky', self::STICKY_WRAPPER );
		$this->end_controls_tab();

		$this->start_controls_tab(
			'box_style_sticky_colors_hover',
			[
				'label' => esc_html__( 'Hover', 'the7mk2' ),
			]
		);
		$this->add_box_color_controls( 'hover', 'sticky', self::STICKY_WRAPPER );
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}
	/**
	 * @param string $type    Controls group type.
	 * @param string $prefix  Controls prefix.
	 * @param string $wrapper Selectors wrapper.
	 *
	 * @return void
	 */
	protected function add_box_color_controls( $type = '', $prefix = '', $wrapper = '{{WRAPPER}}' ) {
		if ( $type === 'hover' ) {
			$selectors = [
				"$wrapper .the7-login-wrapper:hover",
			];
		} else {
			$type      = '';
			$selectors = [
				"$wrapper .the7-login-wrapper",
			];
		}

		$box_prefix = '';
		if ( $prefix ) {
			$box_prefix .= "_{$prefix}";
		}
		if ( $type ) {
			$box_prefix .= "_{$type}";
		}

		$this->add_control(
			'box_bg_color' . $box_prefix,
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => $this->give_me_megaselectors(
					$selectors,
					[
						'' => 'background: {{VALUE}};',
					]
				),
			]
		);


		$this->add_control(
			'box_border_color' . $box_prefix,
			[
				'label'     => esc_html__( 'Border color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => $this->give_me_megaselectors( $selectors, 'border-color: {{VALUE}}' ),
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'box_shadow' . $box_prefix,
				'label'    => esc_html__( 'Shadow', 'the7mk2' ),
				'selector' => implode( ', ', $selectors ),
			]
		);
	}

	/**
	 * @return void
	 */
	protected function add_icon_style_controls() {
		$this->start_controls_section(
			'section_style_icon',
			[
				'label' => esc_html__( 'Icon', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'position',
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
				'default'              => 'left',
				'toggle'               => false,
				'device_args'          => $this->generate_device_args(
					[
						'toggle' => true,
					]
				),
				'selectors_dictionary' => [
					'top'   => '--flex-flow-content: column wrap; align-items: var(--content-position); justify-content: var(--align-content); --icon-order: 0; --icon-gap: 0 0 var(--icon-spacing) 0;',
					'left'  => '--flex-flow-content: row nowrap; justify-content: var(--align-content); align-items: var(--content-position); --icon-order: 0; --icon-gap: 0 var(--icon-spacing) 0 0;',
					'right' => '--flex-flow-content: row- nowrap; justify-content: var(--align-content); align-items: var(--content-position); --icon-order: 2; --icon-gap: 0 0 0 var(--icon-spacing);',
				],
				'selectors'            => [
					'{{WRAPPER}} .the7-login-wrapper' => '{{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'size',
			[
				'label'     => esc_html__( 'Size', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 6,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementor-icon svg' => 'width: {{SIZE}}{{UNIT}};',
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

		$this->add_responsive_control(
			'border_width',
			[
				'label'      => esc_html__( 'Border Width', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .elementor-icon' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; border-style:solid;',
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

		$this->start_controls_tabs( 'icon_colors' );

		$this->start_controls_tab(
			'icon_colors_normal',
			[
				'label' => esc_html__( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_icon_color_controls( 'normal' );

		$this->end_controls_tab();

		$this->start_controls_tab(
			'icon_colors_hover',
			[
				'label' => esc_html__( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_icon_color_controls( 'hover' );

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'icon_colors_sticky',
			[
				'label'       => esc_html__( 'Change colors', 'the7mk2' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => esc_html__( 'When “Sticky” and “Change Styles when Sticky” are ON for the parent section.', 'the7mk2' ),
				'separator'   => 'before',
			]
		);

		$this->start_controls_tabs(
			'sticky_icon_colors',
			[
				'condition' => [
					'icon_colors_sticky!' => '',
				],
			]
		);

		$this->start_controls_tab(
			'sticky_icon_colors_normal',
			[
				'label' => esc_html__( 'Normal', 'the7mk2' ),
			]
		);
		$this->add_icon_color_controls( 'normal', 'sticky', self::STICKY_WRAPPER );
		$this->end_controls_tab();

		$this->start_controls_tab(
			'sticky_icon_colors_hover',
			[
				'label' => esc_html__( 'Hover', 'the7mk2' ),
			]
		);
		$this->add_icon_color_controls( 'hover', 'sticky', self::STICKY_WRAPPER );
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'icon_spacing',
			[
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
				'selectors'  => [
					'{{WRAPPER}}' => '--icon-spacing: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();
	}
	protected function add_icon_color_controls( $type = '', $prefix = '', $wrapper = '{{WRAPPER}}' ) {
		if ( $type === 'hover' ) {
			$selectors = [
				"$wrapper .the7-login-wrapper:hover .elementor-icon",
			];
		} else {
			$type      = '';
			$selectors = [
				"$wrapper .elementor-icon",
			];
		}

		$icon_prefix = '';
		if ( $prefix ) {
			$icon_prefix .= "_{$prefix}";
		}
		if ( $type ) {
			$icon_prefix .= "_{$type}";
		}


		$this->add_control(
			'primary_color' . $icon_prefix,
			[
				'label'     => esc_html__( 'Icon Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => $this->give_me_megaselectors(
					$selectors,
					[
						' i'   => 'color: {{VALUE}};',
						' svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
					]
				),
			]
		);


		$this->add_control(
			'border_color' . $icon_prefix,
			[
				'label'     => esc_html__( 'Border color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => $this->give_me_megaselectors( $selectors, 'border-color: {{VALUE}}' ),
			]
		);

		$this->add_control(
			'icon_bg_color' . $icon_prefix,
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => $this->give_me_megaselectors( $selectors, 'background: {{VALUE}}' ),
			]
		);
	}

	/**
	 * @return void
	 */
	protected function add_text_style_controls() {
		$this->start_controls_section(
			'title_style',
			[
				'label' => esc_html__( 'Text', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .the7-login-wrapper .login-text',
			]
		);

		$this->start_controls_tabs( 'tabs_title_style' );

		$this->start_controls_tab(
			'tab_title_color_normal',
			[
				'label' => esc_html__( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_control(
			'tab_title_text_color',
			[
				'label'     => esc_html__( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .the7-login-wrapper .login-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_title_color_hover',
			[
				'label' => esc_html__( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			'tab_title_hover_color',
			[
				'label'     => esc_html__( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .the7-login-wrapper:hover .login-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();
		$this->add_control(
			'tabs_title_style_sticky',
			[
				'label'       => esc_html__( 'Change colors', 'the7mk2' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => esc_html__( 'When “Sticky” and “Change Styles when Sticky” are ON for the parent section.', 'the7mk2' ),
				'separator'   => 'before',
			]
		);

		$this->start_controls_tabs(
			'tab_title_color_normal_sticky',
			[
				'condition' => [
					'tabs_title_style_sticky!' => '',
				],
			]
		);

		$this->start_controls_tab(
			'box_style_colors_normal_sticky',
			[
				'label' => esc_html__( 'Normal', 'the7mk2' ),
			]
		);
		$this->add_control(
			'tab_title_text_color_sticky',
			[
				'label'     => esc_html__( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					self::STICKY_WRAPPER . ' .the7-login-wrapper .login-text' => 'color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_title_color_hover_sticky',
			[
				'label' => esc_html__( 'Hover', 'the7mk2' ),
			]
		);
		$this->add_control(
			'tab_title_hover_color_sticky',
			[
				'label'     => esc_html__( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					self::STICKY_WRAPPER . ' .the7-login-wrapper:hover .login-text' => 'color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( is_user_logged_in() ) {

			$this->add_link_attributes( 'link', $settings['account_link'] );
			$btn_attributes = $this->get_render_attribute_string( 'link' );
			$icon           = $settings['logged_in_icon'];
			$text           = $settings['logged_in_text'];
		} else {
			$this->add_link_attributes( 'link', $settings['logged_out_link'] );
			$btn_attributes = $this->get_render_attribute_string( 'link' );
			$icon           = $settings['logged_out_icon'];
			$text           = $settings['logged_out_text'];
		}
		$empty_text = "";
		if ( !$text ){
			$empty_text = " no-text";
		}

		$parent_wrapper       = '<div class="the7-login-wrapper the7-elementor-widget'. $empty_text . '">';
		$parent_wrapper_close = '</div>';
		if ( $btn_attributes ) {
			$parent_wrapper       = '<a class="the7-login-wrapper the7-elementor-widget" ' . $btn_attributes . '>';
			$parent_wrapper_close = '</a>';
		}

		echo $parent_wrapper;
		echo '<span class="the7-login-content-wrapper">';

		if ( ! empty( $icon['value'] ) ) {
			echo '<span class="elementor-icon">';
			Icons_Manager::render_icon( $icon, [ 'aria-hidden' => 'true' ] );
			echo '</span>';
		}

		if ( $text ) {
			echo '<span class="login-text">';
			echo wp_kses_post( $text );
			echo '</span>';
		}

		echo '</span>';
		echo $parent_wrapper_close;
	}
}
