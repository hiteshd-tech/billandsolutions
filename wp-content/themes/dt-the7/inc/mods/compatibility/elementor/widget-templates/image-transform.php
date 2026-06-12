<?php
/**
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widget_Templates;

use Elementor\Controls_Manager;

defined( 'ABSPATH' ) || exit;

/**
 * Class Arrows.
 */
class Image_Transform extends Abstract_Template {
	/**
	 * Add transform style controls.
	 *
	 * @return void
	 */
	public function add_style_controls($condition = null) {
		$this->widget->start_controls_section(
			'section_transform',
			[
				'label' => esc_html__( 'Animation on hover', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->widget->add_control(
			'image_overlay_transition_function',
			[
				'label'     => esc_html__( 'Transition timing function', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'ease-out',
				'options'   => [
					'ease'        => esc_html__( 'Ease', 'the7mk2' ),
					'ease-in'     => esc_html__( 'Ease in', 'the7mk2' ),
					'ease-out'    => esc_html__( 'Ease out', 'the7mk2' ),
					'ease-in-out' => esc_html__( 'Ease in out', 'the7mk2' ),
					'linear'      => esc_html__( 'Linear', 'the7mk2' ),
				],
				'selectors' => [
					'{{WRAPPER}}' => '--transition-overlay-timing: {{VALUE}}',
				],
			]
		);
		$this->widget->add_control(
			'image_overlay_transition_title',
			[
				'label'     => esc_html__( 'Overlay fade', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->widget->add_control(
			'image_overlay_transition_duration',
			[
				'label'      => esc_html__( 'Fade in', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'ms',
					'size' => '300',
				],
				'size_units' => [ 'ms' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 1000,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}}' => '--transition-overlay-duration: {{SIZE}}{{UNIT}}',
				],
			]
		);
		$this->widget->add_control(
			'image_overlay_transition_duration_out',
			[
				'label'      => esc_html__( 'Fade out', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'ms',
					'size' => '300',
				],
				'size_units' => [ 'ms' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 1000,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}}' => '--transition-overlay-duration-out: {{SIZE}}{{UNIT}}',
				],
			]
		);
		$this->widget->add_control(
			'image_overlay_description',
			[
				'raw'             => esc_html__( 'Duration of fade-in/out effects when changing image style, displaying an alternative image or a template on hover', 'the7mk2' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-descriptor',
			]
		);
		$this->widget->add_control(
			'image_transition_title',
			[
				'label'     => esc_html__( 'Image transform', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		
		$this->widget->add_control(
			'image_forward_transition_duration',
			[
				'label'      => esc_html__( 'Forward transition', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'ms',
					'size' => '300',
				],
				'size_units' => [ 'ms' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 1000,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}}' => '--transition-img-forward-duration: {{SIZE}}{{UNIT}}',
				],
			]
		);
		$this->widget->add_control(
			'image_back_transition_duration',
			[
				'label'      => esc_html__( 'Back transition', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'ms',
					'size' => '300',
				],
				'size_units' => [ 'ms' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 1000,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}}' => '--transition-img-back-duration: {{SIZE}}{{UNIT}}',
				],
			]
		);
		$this->widget->add_control(
			'image_transform_description',
			[
				'raw'             => esc_html__( 'Duration of rotate, scale, offset etc. effects', 'the7mk2' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-descriptor',
			]
		);

		$this->widget->add_control(
			'transform_overlay',
			[
				'label'        => esc_html__( 'Affect overlay template', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'the7mk2' ),
				'label_off'    => esc_html__( 'No', 'the7mk2' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'prefix_class' => 'transform-overlay-',
				'description' => esc_html__( 'Choose if transforms affect only image or both image and overlay template', 'the7mk2' ),
			]
		);
		$this->widget->add_control(
			'image_overflow',
			[
				'label'        => esc_html__( 'Exceed Image Frame', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'the7mk2' ),
				'label_off'    => esc_html__( 'No', 'the7mk2' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'selectors'    => [
					'{{WRAPPER}} .post-thumbnail-rollover, {{WRAPPER}} .the7-transform-container' => 'overflow: visible;',
				],
				'prefix_class' => 'exceeding-frame-',
				'description' => esc_html__( 'Configure image visibility outside the initial image box when applying rotate, scale, shadow, etc. effect', 'the7mk2' ),
				'condition' =>$condition
			]
		);

		$this->widget->start_controls_tabs( 'image_colors' );

		foreach ( [ '', '_hover' ] as $tab ) {
			$state = $tab === '_hover' ? ':hover' : '';

			$this->widget->start_controls_tab(
				"image_tab_positioning{$tab}",
				[
					'label' => $tab === '' ? esc_html__( 'Normal', 'the7mk2' ) : esc_html__( 'Hover', 'the7mk2' ),
				]
			);

			$this->widget->add_control(
				"image_transform_rotate_popover{$tab}",
				[
					'label' => esc_html__( 'Rotate', 'the7mk2' ),
					'type'  => Controls_Manager::POPOVER_TOGGLE,
				]
			);

			$this->widget->start_popover();

			$this->widget->add_responsive_control(
				"image_transform_rotateZ_effect{$tab}",
				[
					'label'              => esc_html__( 'Rotate', 'the7mk2' ),
					'type'               => Controls_Manager::SLIDER,
					'range'              => [
						'px' => [
							'min' => -360,
							'max' => 360,
						],
					],
					'selectors'          => [
						"{{WRAPPER}} .the7-transform-container{$state}" => '--the7-transform-rotateZ: {{SIZE}}deg',
					],
					'condition'          => [
						"image_transform_rotate_popover{$tab}!" => '',
					],
				]
			);

			$this->widget->add_control(
				"image_transform_rotate_3d{$tab}",
				[
					'label'     => esc_html__( '3D Rotate', 'the7mk2' ),
					'type'      => Controls_Manager::SWITCHER,
					'label_on'  => esc_html__( 'On', 'the7mk2' ),
					'label_off' => esc_html__( 'Off', 'the7mk2' ),
					'selectors' => [
						"{{WRAPPER}} .the7-transform-container{$state}" => '--the7-transform-rotateX: 1{{UNIT}};  --the7-transform-perspective: 20px;',
					],
					'condition' => [
						"image_transform_rotate_popover{$tab}!" => '',
					],
				]
			);

			$this->widget->add_responsive_control(
				"image_transform_rotateX_effect{$tab}",
				[
					'label'              => esc_html__( 'Rotate X', 'the7mk2' ),
					'type'               => Controls_Manager::SLIDER,
					'range'              => [
						'px' => [
							'min' => -360,
							'max' => 360,
						],
					],
					'condition'          => [
						"image_transform_rotate_3d{$tab}!" => '',
						"image_transform_rotate_popover{$tab}!" => '',
					],
					'selectors'          => [
						"{{WRAPPER}} .the7-transform-container{$state}" => '--the7-transform-rotateX: {{SIZE}}deg;',
					],
				]
			);

			$this->widget->add_responsive_control(
				"image_transform_rotateY_effect{$tab}",
				[
					'label'              => esc_html__( 'Rotate Y', 'the7mk2' ),
					'type'               => Controls_Manager::SLIDER,
					'range'              => [
						'px' => [
							'min' => -360,
							'max' => 360,
						],
					],
					'condition'          => [
						"image_transform_rotate_3d{$tab}!" => '',
						"image_transform_rotate_popover{$tab}!" => '',
					],
					'selectors'          => [
						"{{WRAPPER}} .the7-transform-container{$state}" => '--the7-transform-rotateY: {{SIZE}}deg;',
					],
				]
			);

			$this->widget->add_responsive_control(
				"image_transform_perspective_effect{$tab}",
				[
					'label'              => esc_html__( 'Perspective', 'the7mk2' ),
					'type'               => Controls_Manager::SLIDER,
					'range'              => [
						'px' => [
							'min' => 0,
							'max' => 1000,
						],
					],
					'condition'          => [
						"image_transform_rotate_popover{$tab}!" => '',
						"image_transform_rotate_3d{$tab}!" => '',
					],
					'selectors'          => [
						"{{WRAPPER}} .the7-transform-container{$state}" => '--the7-transform-perspective: {{SIZE}}px',
					],
				]
			);

			$this->widget->end_popover();

			$this->widget->add_control(
				"image_transform_translate_popover{$tab}",
				[
					'label' => esc_html__( 'Offset', 'the7mk2' ),
					'type'  => Controls_Manager::POPOVER_TOGGLE,
				]
			);

			$this->widget->start_popover();

			$this->widget->add_responsive_control(
				"image_transform_translateX_effect{$tab}",
				[
					'label'              => esc_html__( 'Offset X', 'the7mk2' ),
					'type'               => Controls_Manager::SLIDER,
					'size_units'         => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
					'range'              => [
						'%'  => [
							'min' => -100,
							'max' => 100,
						],
						'px' => [
							'min' => -1000,
							'max' => 1000,
						],
					],
					'condition'          => [
						"image_transform_translate_popover{$tab}!" => '',
					],
					'selectors'          => [
						"{{WRAPPER}} .the7-transform-container{$state}" => '--the7-transform-translateX: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->widget->add_responsive_control(
				"image_transform_translateY_effect{$tab}",
				[
					'label'              => esc_html__( 'Offset Y', 'the7mk2' ),
					'type'               => Controls_Manager::SLIDER,
					'size_units'         => [ 'px', '%', 'em', 'rem', 'vh', 'custom' ],
					'range'              => [
						'%'  => [
							'min' => -100,
							'max' => 100,
						],
						'px' => [
							'min' => -1000,
							'max' => 1000,
						],
					],
					'condition'          => [
						"image_transform_translate_popover{$tab}!" => '',
					],
					'selectors'          => [
						"{{WRAPPER}} .the7-transform-container{$state}" => '--the7-transform-translateY: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->widget->end_popover();

			$this->widget->add_control(
				"image_transform_scale_popover{$tab}",
				[
					'label' => esc_html__( 'Scale', 'the7mk2' ),
					'type'  => Controls_Manager::POPOVER_TOGGLE,
				]
			);

			$this->widget->start_popover();

			$this->widget->add_control(
				"image_transform_keep_proportions{$tab}",
				[
					'label'     => esc_html__( 'Keep Proportions', 'the7mk2' ),
					'type'      => Controls_Manager::SWITCHER,
					'label_on'  => esc_html__( 'On', 'the7mk2' ),
					'label_off' => esc_html__( 'Off', 'the7mk2' ),
					'default'   => 'yes',
				]
			);

			$this->widget->add_responsive_control(
				"image_transform_scale_effect{$tab}",
				[
					'label'              => esc_html__( 'Scale', 'the7mk2' ),
					'type'               => Controls_Manager::SLIDER,
					'range'              => [
						'px' => [
							'min'  => 0,
							'max'  => 2,
							'step' => 0.1,
						],
					],
					'condition'          => [
						"image_transform_scale_popover{$tab}!" => '',
						"image_transform_keep_proportions{$tab}!" => '',
					],
					'selectors'          => [
						"{{WRAPPER}} .the7-transform-container{$state}" => '--the7-transform-scale: {{SIZE}};',
					],
				]
			);

			$this->widget->add_responsive_control(
				"image_transform_scaleX_effect{$tab}",
				[
					'label'              => esc_html__( 'Scale X', 'the7mk2' ),
					'type'               => Controls_Manager::SLIDER,
					'range'              => [
						'px' => [
							'min'  => 0,
							'max'  => 2,
							'step' => 0.1,
						],
					],
					'condition'          => [
						"image_transform_scale_popover{$tab}!" => '',
						"image_transform_keep_proportions{$tab}" => '',
					],
					'selectors'          => [
						"{{WRAPPER}} .the7-transform-container{$state}" => '--the7-transform-scaleX: {{SIZE}};',
					],
				]
			);

			$this->widget->add_responsive_control(
				"image_transform_scaleY_effect{$tab}",
				[
					'label'              => esc_html__( 'Scale Y', 'the7mk2' ),
					'type'               => Controls_Manager::SLIDER,
					'range'              => [
						'px' => [
							'min'  => 0,
							'max'  => 2,
							'step' => 0.1,
						],
					],
					'condition'          => [
						"image_transform_scale_popover{$tab}!" => '',
						"image_transform_keep_proportions{$tab}" => '',
					],
					'selectors'          => [
						"{{WRAPPER}} .the7-transform-container{$state}" => '--the7-transform-scaleY: {{SIZE}};',
					],
				]
			);

			$this->widget->end_popover();

			$this->widget->add_control(
				"image_transform_skew_popover{$tab}",
				[
					'label' => esc_html__( 'Skew', 'the7mk2' ),
					'type'  => Controls_Manager::POPOVER_TOGGLE,
				]
			);

			$this->widget->start_popover();

			$this->widget->add_responsive_control(
				"image_transform_skewX_effect{$tab}",
				[
					'label'              => esc_html__( 'Skew X', 'the7mk2' ),
					'type'               => Controls_Manager::SLIDER,
					'range'              => [
						'px' => [
							'min' => -360,
							'max' => 360,
						],
					],
					'condition'          => [
						"image_transform_skew_popover{$tab}!" => '',
					],
					'selectors'          => [
						"{{WRAPPER}} .the7-transform-container{$state}" => '--the7-transform-skewX: {{SIZE}}deg;',
					],
				]
			);

			$this->widget->add_responsive_control(
				"image_transform_skewY_effect{$tab}",
				[
					'label'              => esc_html__( 'Skew Y', 'the7mk2' ),
					'type'               => Controls_Manager::SLIDER,
					'range'              => [
						'px' => [
							'min' => -360,
							'max' => 360,
						],
					],
					'condition'          => [
						"image_transform_skew_popover{$tab}!" => '',
					],
					'selectors'          => [
						"{{WRAPPER}} .the7-transform-container{$state}" => '--the7-transform-skewY: {{SIZE}}deg;',
					],
				]
			);

			$this->widget->end_popover();

			$this->widget->add_control(
				"image_transform_flipX_effect{$tab}",
				[
					'label'              => esc_html__( 'Flip Horizontal', 'the7mk2' ),
					'type'               => Controls_Manager::CHOOSE,
					'options'            => [
						'transform' => [
							'title' => esc_html__( 'Flip Horizontal', 'the7mk2' ),
							'icon'  => 'eicon-flip eicon-tilted',
						],
					],
					'selectors'          => [
						"{{WRAPPER}} .the7-transform-container{$state}" => '--the7-transform-flipX: -1',
					],
				]
			);

			$this->widget->add_control(
				"image_transform_flipY_effect{$tab}",
				[
					'label'              => esc_html__( 'Flip Vertical', 'the7mk2' ),
					'type'               => Controls_Manager::CHOOSE,
					'options'            => [
						'transform' => [
							'title' => esc_html__( 'Flip Vertical', 'the7mk2' ),
							'icon'  => 'eicon-flip',
						],
					],
					'selectors'          => [
						"{{WRAPPER}} .the7-transform-container{$state}" => '--the7-transform-flipY: -1',
					],
				]
			);

			$this->widget->end_controls_tab();
		}

		$this->widget->end_controls_tabs();

		$this->widget->end_controls_section();
	}
	/**
	 * Return wrapper HTML class.
	 *
	 * @return string
	 */
	public function get_wrapper_class() {
		return 'the7-transform-container';
	}

}
