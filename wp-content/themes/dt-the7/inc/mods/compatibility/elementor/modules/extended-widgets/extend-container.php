<?php

namespace The7\Mods\Compatibility\Elementor\Modules\Extended_Widgets;

use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use Elementor\Core\Breakpoints\Manager as Breakpoints_Manager;
use Elementor\Element_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Extend_Container {



	public function __construct() {
		// inject controls
		add_action( 'elementor/element/container/the7_section_sticky_row/after_section_end', [ $this, 'addScrollSettings' ] );

		add_action( 'elementor/element/before_section_end', [ $this, 'update_controls' ], 20, 3 );
	}

	/**
	 * Before container end.
	 * Fires before Elementor container ends in the editor panel.
	 *
	 * @param Controls_Stack $widget     The control.
	 * @param string         $section_id Section ID.
	 * @param array          $args       Section arguments.
	 *
	 * @since 1.4.0
	 */
	public function update_controls( $widget, $section_id, $args ) {
		$widgets = [
			'container' => [
				'section_name' => [ 'section_layout_container', 'section_layout_additional_options' ],
			],
		];

		if ( ! array_key_exists( $widget->get_name(), $widgets ) ) {
			return;
		}

		$curr_section = $widgets[ $widget->get_name() ]['section_name'];
		if ( ! in_array( $section_id, $curr_section ) ) {
			return;
		}
		if ( $section_id == 'section_layout_container' ) {

			$widget->update_control(
				'content_width',
				[
					'options' => [
						'boxed' => esc_html__( 'Boxed', 'the7mk2' ),
						'full'  => esc_html__( 'Full Width', 'the7mk2' ),
						'fit'   => esc_html__( 'Fit Content', 'the7mk2' ),
					],
				]
			);

			$widget->update_responsive_control(
				'width',
				[
					'description' => __( 'Select  <i class="eicon-edit"></i>  and enter "fit-content" to make container width fit its content', 'the7mk2' ),
				]
			);

			$widget->start_injection(
				[
					'of' => 'content_width',
					'at' => 'after',
				]
			);

			$widget->add_control(
				'the7_width_fit',
				[
					'label'        => esc_html__( 'Fit Content', 'the7mk2' ),
					'type'         => Controls_Manager::HIDDEN,
					'selectors'    => [
						'{{WRAPPER}}' => '--width: fit-content;',
					],
					'condition'    => [
						'content_width' => 'fit',
					],
					'default'      => 'y',
					'return_value' => 'y',
				]
			);
			$widget->end_injection();
		}
		if ( $section_id == 'section_layout_additional_options' ) {
			$widget->update_control(
				'overflow',
				[
					'description' => esc_html__( 'This setting can be overridden in Advanced > Container Scroll Settings.', 'the7mk2' ),
				]
			);

		}
	}


	public function addScrollSettings( Element_Base $element ) {

		$element->start_controls_section(
			'the7_section_scroll',
			[
				'label'   => __( 'Container Scroll Settings<i></i>', 'the7mk2' ),
				'tab'     => Controls_Manager::TAB_ADVANCED,
				'classes' => 'the7-control',
			]
		);

		$element->add_control(
			'the7_section_scroll_vert',
			[
				'label'                => esc_html__( 'Vertical Scrolling', 'the7mk2' ),
				'type'                 => Controls_Manager::SELECT,
				'options'              => [
					''       => esc_html__( 'Default', 'the7mk2' ),
					'hidden' => esc_html__( 'Always Off', 'the7mk2' ),
					'auto'   => esc_html__( 'Auto', 'the7mk2' ),
					'scroll' => esc_html__( 'Always On', 'the7mk2' ),
				],
				'selectors'            => [
					'{{WRAPPER}}' => '{{VALUE}}',
				],
				'selectors_dictionary' => [
					'hidden' => 'overflow-y: hidden;',
					'auto'   => 'overflow-y: auto;',
					'scroll' => 'overflow-y: scroll;',
				],
				'default'              => '',
			]
		);
		$element->add_control(
			'the7_section_scroll_hor',
			[
				'label'                => esc_html__( 'Horizontal Scrolling', 'the7mk2' ),
				'type'                 => Controls_Manager::SELECT,
				'options'              => [
					''       => esc_html__( 'Default', 'the7mk2' ),
					'hidden' => esc_html__( 'Always Off', 'the7mk2' ),
					'auto'   => esc_html__( 'Auto', 'the7mk2' ),
					'scroll' => esc_html__( 'Always On', 'the7mk2' ),
				],
				'selectors'            => [
					'{{WRAPPER}}' => '{{VALUE}}',
				],
				'selectors_dictionary' => [
					'hidden' => 'overflow-x: hidden;',
					'auto'   => 'overflow-x: auto;',
					'scroll' => 'overflow-x: scroll;',
				],
				'default'              => '',
			]
		);

        $element->add_control(
            'the7_overscroll_behavior',
            [
                'label' => esc_html__( 'Overscroll Behavior', 'the7mk2' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    '' => esc_html__( 'Default', 'the7mk2' ),
                    'none' => esc_html__( 'None', 'the7mk2' ),
                    'auto' => esc_html__( 'Auto', 'the7mk2' ),
                    'contain' => esc_html__( 'Contain', 'the7mk2' ),
                ],
                'separator' => 'after',
                'selectors' => [
                    '{{WRAPPER}}' => 'overscroll-behavior: {{VALUE}};',
                ],
            ]
        );

        $element->add_responsive_control(
            'the7_section_height',
            [
                'label'       => esc_html__( 'Container height', 'the7mk2' ),
                'type'        => Controls_Manager::SLIDER,
                'size_units'  => [ 'px', '%', 'em', 'rem', 'vh', 'custom' ],
                'range'       => [
                    'px' => [
                        'min' => 200,
                        'max' => 1600,
                    ],
                ],
                'default'     => [
                    'unit' => '%',
                ],
                'selectors'   => [
                    '{{WRAPPER}}' => 'height: {{SIZE}}{{UNIT}};',
                ]
            ]
        );

		$element->add_control(
			'the7_section_custom_scrollbar',
			[
				'label'              => esc_html__( 'Enable Custom Scrollbar', 'the7mk2' ),
				'type'               => Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'On', 'the7mk2' ),
				'label_off'          => esc_html__( 'Off', 'the7mk2' ),
				'default'            => '',
				'prefix_class'       => 'the7-custom-scroll the7-custom-scroll-',
				'frontend_available' => true,
			]
		);



		$element->start_controls_tabs(
			'the7_section_scroll_tabs_style',
			[ 'condition' => [ 'the7_section_custom_scrollbar' => 'yes' ] ]
		);

		$element->start_controls_tab(
			'the7_section_scroll_tabs_style_normal',
			[
				'label' => esc_html__( 'Normal', 'the7mk2' ),
			]
		);

		$element->add_control(
			'the7_section_scroll_color',
			[
				'label'     => esc_html__( 'Thumb Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--scrollbar-thumb-color: {{VALUE}};',
				],
			]
		);

		$element->add_control(
			'the7_section_scroll_track_color',
			[
				'label'     => esc_html__( 'Track Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--scrollbar-track-color: {{VALUE}};',
				],
			]
		);

		$element->end_controls_tab();

		$element->start_controls_tab(
			'the7_section_scroll_tabs_style_hover',
			[
				'label' => esc_html__( 'Hover', 'the7mk2' ),
			]
		);
		$element->add_control(
			'the7_section_scroll_color_hover',
			[
				'label'     => esc_html__( 'Thumb Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--scrollbar-thumb-hover-color: {{VALUE}};',
				],
			]
		);

		$element->add_control(
			'the7_section_scroll_track_color_hover',
			[
				'label'     => esc_html__( 'Track Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--scrollbar-track-hover-color: {{VALUE}};',
				],
			]
		);

		$element->end_controls_tab();
		$element->end_controls_tabs();

		$element->end_controls_section();
	}
}
