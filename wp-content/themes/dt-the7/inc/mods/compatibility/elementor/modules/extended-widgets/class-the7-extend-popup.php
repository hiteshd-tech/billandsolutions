<?php

namespace The7\Mods\Compatibility\Elementor\Modules\Extended_Widgets;

use Elementor\Controls_Manager;
use ElementorPro\Modules\Popup\Document as Element_Document;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widgets;
use Elementor\Plugin as Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class The7_Extend_Popup {

	public function __construct() {
		//inject controls
		add_action( 'elementor/element/before_section_end', [ $this, 'update_controls' ], 20, 3 );
		add_action( 'elementor/element/popup/section_close_button/after_section_end', [ $this, 'register_controls' ] );
	}

	public function update_controls( $widget, $section_id, $args ) {
		$widgets = [
			'popup' => [
				'section_name' => [ 'popup_layout', ],
			],
		];

		if ( ! array_key_exists( $widget->get_name(), $widgets ) ) {
			return;
		}

		$curr_section = $widgets[ $widget->get_name() ]['section_name'];
		if ( ! in_array( $section_id, $curr_section ) ) {
			return;
		}

		if ( $section_id == 'popup_layout' ) {
			$control_data = [
				'selectors' => [
					'{{WRAPPER}} .dialog-message'        => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .dialog-widget-content' => 'width: {{SIZE}}{{UNIT}};',
				],
			];

			The7_Elementor_Widgets::update_responsive_control_fields( $widget, 'width', $control_data );


			$widget->start_injection( [
				'of' => 'height_type',
				'at' => 'after',
			] );
//
//			$control_params = [
//				'label'        => esc_html__( 'sidebar helper', 'the7mk2' ),
//				'type'         => Controls_Manager::HIDDEN,
//				'condition'    => [
//					'height_type' => 'fit_to_screen',
//				],
//				'default'      => 'y',
//				'return_value' => 'y',
//				'selectors'    => [
//					'body:not(.admin-bar) {{WRAPPER}}'   => 'top:0;',
//					'body.admin-bar {{WRAPPER}}'         => 'position: fixed;',
//					'{{WRAPPER}} .dialog-widget-content' => 'position: absolute;height: 100%;',
//					'{{WRAPPER}} .dialog-message '       => 'position: absolute;height: 100%;width: 100%;',
//				],
//			];
//
//			$widget->add_control( 'sidebar_helper', $control_params );

			$control_params = [
				'label'      => esc_html__( 'Max Height', 'the7mk2' ),
				'description' => esc_html__( 'Leave this field empty to fit screen height', 'the7mk2' ),
				'condition'  => [
					'height_type' => 'auto',
				],
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [
						'min' => 100,
						'max' => 1000,
					],
					'vh' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'size_units' => [ 'px', 'vh' ],
				'selectors'  => [
					'{{WRAPPER}} .dialog-message' => 'max-height: {{SIZE}}{{UNIT}};',
				],
				'classes'              => 'the7-control',
			];
			$widget->add_control( 'fit_content_max_height', $control_params );


			$control_params = [
				'label'      => esc_html__( 'Max Height Override', 'the7mk2' ),
				'condition'  => [
					'height_type' => 'auto',
					'fit_content_max_height[size]' => '',
				],
				'default'      => 'y',
				'return_value' => 'y',
				'type'       => Controls_Manager::HIDDEN,
				'selectors'  => [
					'{{WRAPPER}} .dialog-message' => 'max-height: var(--the7-fit-height, 100vh)',
				],
			];
			$widget->add_control( 'fit_content_max_height_empty', $control_params );

			$control_params = [
				'label'      => esc_html__( 'Min Height Override', 'the7mk2' ),
				'condition'  => [
					'height_type' => 'fit_to_screen',
				],
				'default'      => 'y',
				'return_value' => 'y',
				'type'       => Controls_Manager::HIDDEN,
				'selectors'  => [
					'{{WRAPPER}} .dialog-message' => 'height: var(--the7-fit-height, 100vh); max-height: initial;',
				],
			];
			$widget->add_control( 'fit_screen_min_height_helper', $control_params );

			$widget->end_injection();

		}
	}

	public function register_controls( Element_Document $element ) {
		$element->start_controls_section(
			'section_the7_scrollbar',
			[
				'label' => __( 'Scrollbar<i></i>', 'the7mk2' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'classes'   => 'the7-control',
			]
		);
		$element->add_control( 'the7_scrollbar', [
			'label'              => esc_html__( 'Enable Custom Scrollbar', 'the7mk2' ),
			'type'               => Controls_Manager::SWITCHER,
			'label_on'           => esc_html__( 'On', 'the7mk2' ),
			'label_off'          => esc_html__( 'Off', 'the7mk2' ),
			'default'            => '',
			'frontend_available' => true,
		] );

		$condition = [ 'the7_scrollbar' => 'yes'];
		$selector = '{{WRAPPER}} .dialog-message';
		if ( Elementor::$instance->editor->is_edit_mode() ) {
			$element->add_control( 'the7_scrollbar_style_helper', [
				'label'        => '',
				'type'         => Controls_Manager::HIDDEN,
				'condition'  => $condition,
				'default'      => 'y',
				'return_value' => 'y',
				'selectors'  => [
					$selector => '  --scrollbar-thumb-color: rgb(0 0 0 / 8%);
									--scrollbar-thumb-hover-color: rgb(0 0 0 / 15%);
									scrollbar-width: thin;
								    scrollbar-color: var(--scrollbar-thumb-color) transparent;',
					$selector . ':hover' => 'scrollbar-color: var(--scrollbar-thumb-hover-color) transparent;',
					$selector . '::-webkit-scrollbar' => 'width: 7px; height: 7px;',
					$selector . '::-webkit-scrollbar-track' => 'background: transparent;',
					$selector . '::-webkit-scrollbar-thumb' => 'border-radius: 4px; background: var(--scrollbar-thumb-color);',
					$selector . ':hover::-webkit-scrollbar-thumb' => 'background: var(--scrollbar-thumb-hover-color);',
				],
			] );
		}

		$element->start_controls_tabs(
			'the7_scrollbar_tabs_style',
			['condition' => $condition]

		);

		$element->start_controls_tab(
			'the7_normal_scrollbar_style',
			[
				'label' => esc_html__( 'Normal', 'the7mk2' ),
			]
		);

		$element->add_control( 'the7_scrollbar_color', [
			'label'      => esc_html__( 'Thumb Color', 'the7mk2' ),
			'type'       => Controls_Manager::COLOR,
			'selectors'  => [
				$selector  => '--scrollbar-thumb-color: {{VALUE}};',
			],
		] );

		$element->end_controls_tab();

		$element->start_controls_tab(
			'the7_hover_scrollbar_style',
			[
				'label' => esc_html__( 'Hover', 'the7mk2' ),
			]
		);
		$element->add_control( 'the7_hover_scrollbar_color', [
			'label'      => esc_html__( 'Thumb Color', 'the7mk2' ),
			'type'       => Controls_Manager::COLOR,
			'selectors'  => [
				$selector  => '--scrollbar-thumb-hover-color: {{VALUE}};',
			],
		] );

		$element->end_controls_tab();
		$element->end_controls_tabs();

		$element->end_controls_section();
	}
}