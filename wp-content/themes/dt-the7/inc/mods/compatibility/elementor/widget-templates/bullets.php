<?php
/**
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widget_Templates;

use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

use Elementor\Controls_Manager;

defined( 'ABSPATH' ) || exit;

/**
 * Class Bullets.
 */
class Bullets extends Abstract_Template {

	/**
	 * @return void
	 */
	public function add_content_controls( $condition = null ) {
		$this->widget->start_controls_section(
			'bullets_section',
			[
				'label'     => esc_html__( 'Bullets', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => $condition,
			]
		);

		$layouts = [
			'show'  => esc_html__( 'Always', 'the7mk2' ),
			'hide'  => esc_html__( 'Never', 'the7mk2' ),
			'hover' => esc_html__( 'On Hover', 'the7mk2' ),
		];
		$this->widget->add_responsive_control(
			'show_bullets',
			[
				'label'                => esc_html__( 'Show Bullets', 'the7mk2' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => 'show',
				'options'              => $layouts,
				'device_args'          => $this->widget->generate_device_args(
					[
						'options' => [ '' => esc_html__( 'Default', 'the7mk2' ) ] + $layouts,
					]
				),
				'selectors'            => [
					'{{WRAPPER}}' => '{{VALUE}};',
				],
				'selectors_dictionary' => [
					'show'  => '--bullet-display: inline-flex; --bullet-opacity:1;',
					'hide'  => '--bullet-display: none',
					'hover' => '--bullet-display: inline-flex;--bullet-opacity:0;',
				],
			]
		);

		$this->widget->end_controls_section();
	}

	/**
	 * @return void
	 */
	public function add_style_controls( $condition = null, $conditions = false ) {
		if ( $conditions === false ) {
			$conditions = $this->widget->generate_conditions( 'show_bullets', '!=', 'hide' );
		}

		$this->widget->start_controls_section(
			'bullets_style_section',
			[
				'label'      => esc_html__( 'Bullets', 'the7mk2' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => $conditions,
				'condition'  => $condition,
			]
		);

		$this->widget->add_control(
			'bullets_style_heading',
			[
				'label'     => esc_html__( 'Bullets Style', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->widget->add_control(
			'bullets_style',
			[
				'label'        => esc_html__( 'Choose Bullets Style', 'the7mk2' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'small-dot-stroke',
				'options'      => [
					'small-dot-stroke' => 'Small dot stroke',
					'scale-up'         => 'Scale up',
					'stroke'           => 'Stroke',
					'fill-in'          => 'Fill in',
					'ubax'             => 'Square',
					'etefu'            => 'Rectangular',
					'custom'           => 'Custom',
				],
				'prefix_class' => 'bullets-',
				'render_type'  => 'template',
			]
		);
		$this->widget->add_control(
			'stretch_bullets',
			[
				'label'                => esc_html__( 'Stretch bullets', 'the7mk2' ),
				'type'                 => Controls_Manager::SWITCHER,
				'label_off'            => esc_html__( 'No', 'the7mk2' ),
				'label_on'             => esc_html__( 'Yes', 'the7mk2' ),
				'return_value'         => 'y',
				'selectors_dictionary' => [
					'y' => 'display: flex; justify-content: center; --bullet-flex-grow: 1;',
				],
				'prefix_class'         => 'bullets-stretch-',
				'selectors'            => [
					'{{WRAPPER}} .owl-dots' => '{{VALUE}};',
				],
				'condition'            => [
					'bullets_style' => 'custom',
				],
			]
		);

		$selector        = '{{WRAPPER}} .owl-dots';
		$custom_selector = '{{WRAPPER}} .owl-dots .owl-dot';

		$this->widget->add_responsive_control(
			'bullet_size',
			[
				'label'      => esc_html__( 'Bullets Size', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 10,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
				],
				'condition'  => [
					'bullets_style!' => 'custom',
				],
				'selectors'  => [
					$selector => '--bullet-size: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->widget->add_responsive_control(
			'bullet_border_size',
			[
				'label'      => esc_html__( 'Border Size', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 1,
						'max'  => 100,
						'step' => 1,
					],
				],
				'condition'  => [
					'bullets_style!' => [ 'scale-up',  'custom' ],
				],
				'selectors'  => [
					$selector => '--bullet-border-width: {{SIZE}}{{UNIT}}',
				],
			]
		);
		$this->widget->add_responsive_control(
			'bullet_width',
			[
				'label'      => esc_html__( 'Width', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					$custom_selector => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'bullets_style' => 'custom',
				],
			]
		);

		$this->widget->add_responsive_control(
			'bullet_height',
			[
				'label'      => esc_html__( 'Height', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					$custom_selector => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'bullets_style' => 'custom',
				],
			]
		);
		$this->widget->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'bullet_border',
				'selector'  => $custom_selector,
				'exclude'   => [ 'color' ],
				'condition' => [
					'bullets_style' => 'custom',
				],
			]
		);
		$this->widget->add_control(
			'bullet_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					$custom_selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'bullets_style' => 'custom',
				],
			]
		);

		$this->widget->add_responsive_control(
			'bullet_gap',
			[
				'label'      => esc_html__( 'Gap Between Bullets', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 16,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 1,
						'max'  => 200,
						'step' => 1,
					],
				],
				'selectors'  => [
					$selector => '--bullet-gap: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->widget->start_controls_tabs( 'bullet_style_tabs' );

		$this->widget->start_controls_tab(
			'bullet_colors',
			[
				'label' => esc_html__( 'Normal', 'the7mk2' ),
			]
		);

		$this->widget->add_control(
			'bullet_color',
			[
				'label'     => esc_html__( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					$selector => '--bullet-color: {{VALUE}};',
				],
				'condition' => [
					'bullets_style!' => 'custom',
				],
			]
		);
		$this->widget->add_control(
			'bullet_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					$custom_selector => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'bullets_style' => 'custom',
				],
			]
		);

		$this->widget->add_control(
			'bullet_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					$custom_selector => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'bullet_border_border!' => [ '', 'none' ],
					'bullets_style'         => 'custom',
				],
			]
		);
		$this->widget->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'bullet_shadow',
				'selector' => $custom_selector,
			]
		);

		$this->widget->end_controls_tab();

		$this->widget->start_controls_tab(
			'bullet_hover_colors',
			[
				'label' => esc_html__( 'Hover', 'the7mk2' ),
			]
		);

		$this->widget->add_control(
			'bullet_color_hover',
			[
				'label'     => esc_html__( 'Hover Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					$selector => '--bullet-hover-color: {{VALUE}};',
				],
				'condition' => [
					'bullets_style!' => 'custom',
				],
			]
		);
		$this->widget->add_control(
			'bullet_bg_color_hover',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					$custom_selector . ':hover' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'bullets_style' => 'custom',
				],
			]
		);
		$this->widget->add_control(
			'bullet_border_color_hover',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					$custom_selector . ':hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'bullet_border_border!' => [ '', 'none' ],
					'bullets_style'         => 'custom',
				],
			]
		);
		$this->widget->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'bullet_shadow_hover',
				'selector' => $custom_selector . ':hover',
			]
		);

		$this->widget->end_controls_tab();

		$this->widget->start_controls_tab(
			'bullet_active_colors',
			[
				'label' => esc_html__( 'Active', 'the7mk2' ),
			]
		);

		$this->widget->add_control(
			'bullet_color_active',
			[
				'label'     => esc_html__( 'Active Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					$selector => '--bullet-active-color: {{VALUE}};',
				],
				'condition' => [
					'bullets_style!' => 'custom',
				],
			]
		);
		$this->widget->add_control(
			'bullet_bg_color_active',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					$custom_selector . '.active' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'bullets_style' => 'custom',
				],
			]
		);
		$this->widget->add_control(
			'bullet_border_color_active',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					$custom_selector . '.active' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'bullet_border_border!' => [ '', 'none' ],
					'bullets_style'         => 'custom',
				],
			]
		);
		$this->widget->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'bullet_shadow_active',
				'selector' => $custom_selector . '.active',
			]
		);

		$this->widget->end_controls_tab();

		$this->widget->end_controls_tabs();

		$this->widget->add_control(
			'bullets_position_heading',
			[
				'label'     => esc_html__( 'Bullets Position', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->widget->add_responsive_control(
			'bullets_v_position',
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
					'top'    => 'top: var(--bullet-v-offset, 10px); bottom: auto; --bullet-translate-y:0;',
					'center' => 'top: calc(50% + var(--bullet-v-offset, 10px)); bottom: auto; --bullet-translate-y:-50%;',
					'bottom' => 'top: calc(100% + var(--bullet-v-offset, 10px)); bottom: auto; --bullet-translate-y:0;',
				],
				'toggle'               => false,
				'selectors'            => [
					$selector => '{{VALUE}}',
				],
				'default'              => 'bottom',
			]
		);

		$this->widget->add_responsive_control(
			'bullets_h_position',
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
				'toggle'               => false,
				'default'              => 'center',
				'selectors_dictionary' => [
					'left'   => 'left: var(--bullet-h-offset, 0px); right: auto; --bullet-translate-x:0; --bullet-position-left: var(--bullet-h-offset, 0px);',
					'center' => 'left: calc(50% + var(--bullet-h-offset, 0px)); right: auto; --bullet-translate-x:-50%; --bullet-position-left: calc(50% + var(--bullet-h-offset, 0px));',
					'right'  => 'left: auto; right: var(--bullet-h-offset, 0px); --bullet-translate-x:0; --bullet-position-left: 0;',
				],
				'selectors'            => [
					$selector => '{{VALUE}}',
				],
				'conditions'           => [
					'relation' => 'or',
					'terms'    => [
						[
							'relation' => 'and',
							'terms'    => [
								[
									'name'     => 'stretch_bullets',
									'operator' => '===',
									'value'    => 'y',
								],
								[
									'name'     => 'bullets_direction',
									'operator' => '==',
									'value'    => 'vertical',
								],
								[
									'name'     => 'bullets_style',
									'operator' => '===',
									'value'    => 'custom',
								],
							],
						],
						[
							'relation' => 'and',
							'terms'    => [
								[
									'name'     => 'stretch_bullets',
									'operator' => '!==',
									'value'    => 'y',
								],
							],
						],
						[
							'relation' => 'and',
							'terms'    => [
								[
									'name'     => 'bullets_style',
									'operator' => '!==',
									'value'    => 'custom',
								],
							],
						],
					],
				],
			]
		);

		$this->widget->add_responsive_control(
			'bullets_v_offset',
			[
				'label'      => esc_html__( 'Vertical Offset', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 10,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => - 1000,
						'max'  => 1000,
						'step' => 1,
					],
				],
				'selectors'  => [
					$selector => '--bullet-v-offset: {{SIZE}}{{UNIT}};',
				],

			]
		);

		$this->widget->add_responsive_control(
			'bullets_h_offset',
			[
				'label'      => esc_html__( 'Horizontal Offset', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => - 1000,
						'max'  => 1000,
						'step' => 1,
					],
				],
				'selectors'  => [
					$selector => '--bullet-h-offset: {{SIZE}}{{UNIT}};',
				],
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'relation' => 'and',
							'terms'    => [
								[
									'name'     => 'stretch_bullets',
									'operator' => '===',
									'value'    => 'y',
								],
								[
									'name'     => 'bullets_direction',
									'operator' => '==',
									'value'    => 'vertical',
								],
								[
									'name'     => 'bullets_style',
									'operator' => '===',
									'value'    => 'custom',
								],
							],
						],
						[
							'relation' => 'and',
							'terms'    => [
								[
									'name'     => 'stretch_bullets',
									'operator' => '!==',
									'value'    => 'y',
								],
							],
						],
						[
							'relation' => 'and',
							'terms'    => [
								[
									'name'     => 'bullets_style',
									'operator' => '!==',
									'value'    => 'custom',
								],
							],
						],
					],
				],
			]
		);

		$this->widget->end_controls_section();
	}
}