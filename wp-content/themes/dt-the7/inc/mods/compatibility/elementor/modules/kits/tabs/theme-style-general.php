<?php

namespace The7\Mods\Compatibility\Elementor\Modules\Kits\Tabs;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Theme_Style_General extends The7_Tab_Base {



	function the7_title() {
		return __( 'General Appearance', 'the7mk2' );
	}

	function the7_id() {
		return 'general';
	}

	public function get_icon() {
		return 'eicon-theme-style';
	}

	protected function register_tab_controls() {
		$this->add_general_appearance_section();
		$this->add_beautiful_loading_section();
		$this->add_scroll_to_top_button_section();
		$this->add_anchor_scroll_offset();
	}

	private function add_general_appearance_section() {
		$wrapper = $this->get_wrapper();
		$this->start_controls_section(
			'the7_section_appearance',
			[
				'label' => __( 'General Appearance', 'the7mk2' ),
				'tab'   => $this->get_id(),
			]
		);

		$this->add_control(
			'the7-accent-color',
			[
				'label'     => __( 'Accent Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'dynamic'   => [],
				'selectors' => [
					$wrapper => '--the7-accent-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'the7-divider-color',
			[
				'label'     => __( 'Dividers Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'dynamic'   => [],
				'selectors' => [
					$wrapper => '--the7-divider-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'the7-content-boxes',
			[
				'label'     => __( 'Content Boxes Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'dynamic'   => [],
				'selectors' => [
					$wrapper => '--the7-content-boxes-bg: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'the7_general_accessibility_outline_links',
			[
				'label'     => esc_html__( 'Accessibility: Links Outline', 'the7mk2' ),
				'type'      => Controls_Manager::SWITCHER,
				'selectors' => [
					'#the7-body a:focus' => 'outline: auto;',
				],
				'export'    => false,
			]
		);

        $this->add_control(
            'the7_general_accessibility_user_scalable',
            [
                'label'     => esc_html__( 'Accessibility: User Scalable', 'the7mk2' ),
                'type'      => Controls_Manager::SWITCHER,
                'default'          => of_get_option( 'general-user_scalable' ),
                'return_value'     => '1',
                'empty_value'      => '0',
                'the7_save'        => true,
                'the7_option_name' => 'general-user_scalable',
                'export'           => false,
            ]
        );

		$this->end_controls_section();
	}

	private function add_beautiful_loading_section() {
		$this->start_controls_section(
			'the7_section_general_beautiful_loading',
			[
				'label' => __( 'Beautiful Loading', 'the7mk2' ),
				'tab'   => $this->get_id(),
			]
		);
		$this->add_control(
			'the7_general_beautiful_loading',
			[
				'label'            => esc_html__( 'Beautiful loading', 'the7mk2' ),
				'type'             => Controls_Manager::SWITCHER,
				'default'          => of_get_option( 'general-beautiful_loading' ),
				'return_value'     => 'enabled',
				'empty_value'      => 'disabled',
				'the7_save'        => true,
				'the7_option_name' => 'general-beautiful_loading',
				'export'           => false,
			]
		);
		$selector = $this->get_wrapper();

		$this->add_control(
			'the7_general_beautiful_loading_color',
			[
				'label'     => __( 'Spinner color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'dynamic'   => [],
				'selectors' => [
					$selector => '--the7-beautiful-spinner-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'           => 'the7_general_beautiful_loading_background',
				'types'          => [ 'classic', 'gradient' ],
				'exclude'        => [ 'image' ],
				'selector'       => $selector,
				'condition'      => [
					'the7_general_beautiful_loading' => 'enabled',
				],
				'fields_options' => [
					'background'        => [
						'default' => 'classic',
					],
					'color'             => [
						'dynamic'   => [],
						'selectors' => [
							'{{SELECTOR}}' => '--the7-elementor-beautiful-loading-bg: {{VALUE}};',
						],
					],
					'gradient_angle'    => [
						'selectors' => [
							'{{SELECTOR}}' => '--the7-elementor-beautiful-loading-bg: transparent linear-gradient({{SIZE}}{{UNIT}}, {{color.VALUE}} {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}});',
						],
					],
					'gradient_position' => [
						'selectors' => [
							'{{SELECTOR}}' => '--the7-elementor-beautiful-loading-bg: transparent radial-gradient(at {{VALUE}}, {{color.VALUE}} {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}});',
						],
					],
					'color_b'           => [
						'dynamic' => [],
					],
				],
			]
		);

		$this->add_control(
			'the7_general_beautiful_loading_style',
			[
				'label'            => esc_html__( 'Loader style', 'the7mk2' ),
				'type'             => Controls_Manager::SELECT,
				'default'          => of_get_option( 'general-loader_style' ),
				'separator'        => 'before',
				'label_block'      => false,
				'options'          => [
					'double_circles'    => esc_html__( 'Spinner', 'the7mk2' ),
					'square_jelly_box'  => esc_html__( 'Ring', 'the7mk2' ),
					'ball_elastic_dots' => esc_html__( 'Bars', 'the7mk2' ),
					'custom'            => esc_html__( 'Custom', 'the7mk2' ),
				],
				'condition'        => [
					'the7_general_beautiful_loading' => 'enabled',
				],
				'the7_save'        => true,
				'the7_option_name' => 'general-loader_style',
				'export'           => false,
			]
		);

		$this->add_control(
			'the7_general_beautiful_loading_custom_style_title',
			[
				'raw'             => __( 'Paste HTML code of your custom pre-loader image in the field below.', 'the7mk2' ),
				'type'            => Controls_Manager::RAW_HTML,
				'condition'       => [
					'the7_general_beautiful_loading_style' => 'custom',
				],
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			]
		);

		$this->add_control(
			'the7_general_beautiful_loading_custom_style_code',
			[
				'type'             => Controls_Manager::CODE,
				'label'            => __( 'Custom Loader Code', 'the7mk2' ),
				'language'         => 'html',
				'default'          => (string) of_get_option( 'general-custom_loader', '' ),
				'render_type'      => 'ui',
				'show_label'       => false,
				'separator'        => 'none',
				'the7_save'        => true,
				'the7_option_name' => 'general-custom_loader',
				'condition'        => [
					'the7_general_beautiful_loading_style' => 'custom',
				],
			]
		);

		$this->end_controls_section();
	}

	private function add_scroll_to_top_button_section() {
		$name = 'the7_scroll_to_top_button';
		$this->start_controls_section(
			"{$name}",
			[
				'label' => __( 'Scroll To Top Button', 'the7mk2' ),
				'tab'   => $this->get_id(),
			]
		);

		$selector = '#the7-body a.scroll-top';

		$this->add_control(
			'the7_scroll_to_top_button_enable',
			[
				'label'                => esc_html__( 'Show button', 'the7mk2' ),
				'type'                 => Controls_Manager::SWITCHER,
				'label_on'             => __( 'Show', 'the7mk2' ),
				'label_off'            => __( 'Hide', 'the7mk2' ),
				'default'              => 'enabled',
				'return_value'         => 'enabled',
				'empty_value'          => 'disabled',
				'selectors'            => [
					$selector => '{{VALUE}}',
				],
				'selectors_dictionary' => [
					''        => 'display: none;',
					'enabled' => '',
				],
                'render_type' => 'template',
			]
		);

		$this->add_control(
			"{$name}_icon",
			[
				'label'                 => esc_html__( 'Icon', 'the7mk2' ),
				'type'                  => Controls_Manager::ICONS,
				'separator'             => 'after',
				'condition'             => [
					'the7_scroll_to_top_button_enable' => 'enabled',
				],
                'render_type' => 'template',
			]
		);

		$this->add_responsive_control(
			"{$name}_icon_size",
			[
				'label'      => __( 'Icon Size', 'the7mk2' ),
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
					'.scroll-top-elementor-icon i,.scroll-top:before' => 'font-size: {{SIZE}}{{UNIT}};',
					'a.scroll-top-elementor-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'the7_scroll_to_top_button_enable' => 'enabled',
				],
			]
		);

		$this->add_responsive_control(
			"{$name}_size",
			[
				'label'      => __( 'Button Paddings', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
				],
				'selectors'  => [
					$selector                      => 'padding: {{SIZE}}{{UNIT}}; width: auto; height: auto;',
					'.scroll-top-elementor-icon i' => 'width: 1em; height: 1em; text-align: center;',
				],
				'condition'  => [
					'the7_scroll_to_top_button_enable' => 'enabled',
				],
			]
		);

		$this->add_control(
			"{$name}_border_width",
			[
				'label'      => __( 'Border Width', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors'  => [
					$selector => 'border-style: solid; border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
				'condition'  => [
					'the7_scroll_to_top_button_enable' => 'enabled',
				],
			]
		);

		$this->add_control(
			"{$name}_border_radius",
			[
				'label'      => __( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors'  => [
					$selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
				'condition'  => [
					'the7_scroll_to_top_button_enable' => 'enabled',
				],
			]
		);

		$this->start_controls_tabs( "{$name}_style_tabs" );

		// Normal colors.
		$this->start_controls_tab(
			"{$name}_style_tab",
			[
				'label'     => __( 'Normal', 'the7mk2' ),
				'condition' => [
					'the7_scroll_to_top_button_enable' => 'enabled',
				],
			]
		);
		$this->add_scroll_to_top_button_controls( '' );
		$this->end_controls_tab();

		// Hover colors.
		$this->start_controls_tab(
			"{$name}_style_tab_hover",
			[
				'label'     => __( 'Hover', 'the7mk2' ),
				'condition' => [
					'the7_scroll_to_top_button_enable' => 'enabled',
				],
			]
		);
		$this->add_scroll_to_top_button_controls( '_hover' );
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			"{$name}_position",
			[
				'separator'            => 'before',
				'label'                => __( 'Horizontal Position', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => [
					'left'   => [
						'title' => __( 'Left', 'the7mk2' ),
						'icon'  => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'the7mk2' ),
						'icon'  => 'eicon-h-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'the7mk2' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'selectors'            => [
					$selector => '{{VALUE}}',
				],
				'selectors_dictionary' => [
					'left'   => 'right: auto; left: var(--the7-scroll-h-offset,0 ); transform: translate3d(0,0,0);',
					'center' => 'left: auto; right: 50%; transform: translate3d(calc(50% + var(--the7-scroll-h-offset,0 )),0, 0px );',
					'right'  => 'left: auto; right: var(--the7-scroll-h-offset,0 ); transform: translate3d(0,0,0);',
				],
				'toggle'               => false,
				'condition'            => [
					'the7_scroll_to_top_button_enable' => 'enabled',
				],
			]
		);

		$this->add_responsive_control(
			"{$name}_h_offset",
			[
				'label'      => __( 'Horizontal Offset', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					$selector => '--the7-scroll-h-offset: {{SIZE}}{{UNIT}}',
				],
				'range'      => [
					'px' => [
						'min'  => -1000,
						'max'  => 1000,
						'step' => 1,
					],
					'em' => [
						'min'  => -20,
						'max'  => 20,
						'step' => 1,
					],
					'%'  => [
						'min'  => -100,
						'max'  => 100,
						'step' => 1,
					],
				],
				'condition'  => [
					'the7_scroll_to_top_button_enable' => 'enabled',
				],
			]
		);

		$this->add_responsive_control(
			"{$name}_v_offset",
			[
				'label'      => __( 'Vertical Offset', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					$selector => 'bottom: {{SIZE}}{{UNIT}}',
				],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 1000,
						'step' => 1,
					],
				],
				'condition'  => [
					'the7_scroll_to_top_button_enable' => 'enabled',
				],
			]
		);

		$this->end_controls_section();
	}

	private function add_anchor_scroll_offset() {
		$this->start_controls_section(
			'the7_anchor_scroll_settings',
			[
				'label' => __( 'Anchor Scroll', 'the7mk2' ),
				'tab'   => $this->get_id(),
			]
		);

		$selector = 'html';

		$this->add_responsive_control(
			'the7_anchor_scroll_offset',
			[
				'label'      => __( 'Offset for anchor navigation', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'selectors'  => [
					$selector => '--the7-anchor-scroll-offset: {{SIZE}}; scroll-padding-top: {{SIZE}}{{UNIT}}',
				],
				'range'      => [
					'px' => [
						'min'  => -1000,
						'max'  => 1000,
						'step' => 1,
					],
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @param string $prefix Control name prefix.
	 */
	private function add_scroll_to_top_button_controls( $prefix = '' ) {
		switch ( $prefix ) {
			case '_hover':
				$selectors     = 'body .scroll-top-elementor-icon:hover i, body .scroll-top:hover:before';
				$svg_selectors = 'body .scroll-top-elementor-icon:hover svg';
				break;
			default:
				$selectors     = 'body .scroll-top-elementor-icon i,body .scroll-top:before';
				$svg_selectors = 'body .scroll-top-elementor-icon svg';
		}

		$this->add_control(
			'the7_scroll_to_top_button_icon_color' . $prefix,
			[
				'label'     => __( 'Icon Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$selectors     => 'color: {{VALUE}};',
					$svg_selectors => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
				'condition' => [
					'the7_scroll_to_top_button_enable' => 'enabled',
				],
			]
		);

		switch ( $prefix ) {
			case '_hover':
				$selectors = 'body .scroll-top:hover';
				break;
			default:
				$selectors = 'body .scroll-top';
		}

		$this->add_control(
			'the7_scroll_to_top_button_bg_color' . $prefix,
			[
				'label'     => __( 'Background color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'selectors' => [
					$selectors => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'the7_scroll_to_top_button_enable' => 'enabled',
				],
			]
		);

		$this->add_control(
			'the7_scroll_to_top_button_border_color' . $prefix,
			[
				'label'     => __( 'Border color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'selectors' => [
					$selectors => 'border-color: {{VALUE}}',
				],
				'condition' => [
					'the7_scroll_to_top_button_enable' => 'enabled',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => 'the7_scroll_to_top_button_shadow' . $prefix,
				'selector'  => $selectors,
				'condition' => [
					'the7_scroll_to_top_button_enable' => 'enabled',
				],
			]
		);
	}
}
