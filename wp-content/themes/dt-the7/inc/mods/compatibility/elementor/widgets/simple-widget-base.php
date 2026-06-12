<?php
/**
 * Simple widget base class.
 *
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Image_Aspect_Ratio;

defined( 'ABSPATH' ) || exit;

/**
 * Simple widget base class.
 *
 * Contains common to all simple widgets.
 */
abstract class Simple_Widget_Base extends The7_Elementor_Widget_Base {

	/**
	 * Common simple image style settings.
	 *
	 * @param array $condition Section condition.
	 */
	protected function add_image_style_controls( array $condition ) {
		$this->start_controls_section(
			'fetatured_image_style',
			[
				'label'     => esc_html__( 'Featured Image', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => $condition,
			]
		);

		$this->add_control(
			'position_title',
			[
				'label' => esc_html__( 'Position', 'the7mk2' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_responsive_control(
			'align_image',
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
				'prefix_class'         => 'img-align%s-',
				'selectors_dictionary' => [
					'top'   => $this->combine_to_css_vars_definition_string(
						[
							'content-wrapper-flex-flow' => 'column wrap',
							'thumb-margin'              => '0 0 var(--image-spacing) 0',
							'thumb-order'               => '0',
							'content-width'             => '100%',
						]
					),
					'left'  => $this->combine_to_css_vars_definition_string(
						[
							'content-wrapper-flex-flow' => 'row nowrap',
							'thumb-margin'              => '0 var(--image-spacing) 0 0',
							'thumb-order'               => '0',
							'content-width'             => 'calc(100% - var(--image-size) - var(--image-spacing))',
						]
					),
					'right' => $this->combine_to_css_vars_definition_string(
						[
							'content-wrapper-flex-flow' => 'row nowrap',
							'thumb-margin'              => '0 0 0 var(--image-spacing)',
							'thumb-order'               => '2',
							'content-width'             => 'calc(100% - var(--image-size) - var(--image-spacing))',
						]
					),
				],
				'selectors'            => [
					'{{WRAPPER}} [class*="the7-simple-widget-"]' => '{{VALUE}}',
				],
			]
		);

		$img_position_options = [
			'start'  => esc_html__( 'Start', 'the7mk2' ),
			'center' => esc_html__( 'Center', 'the7mk2' ),
			'end'    => esc_html__( 'End', 'the7mk2' ),
		];

		$this->add_responsive_control(
			'image_position',
			[
				'label'                => esc_html__( 'Align', 'the7mk2' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => 'start',
				'options'              => $img_position_options,
				'device_args'          => $this->generate_device_args(
					[
						'default' => '',
						'options' => [ '' => esc_html__( 'Default', 'the7mk2' ) ] + $img_position_options,
					]
				),
				'prefix_class'         => 'image-vertical-align%s-',
				'selectors_dictionary' => [
					'start'  => 'align-self: flex-start;',
					'center' => 'align-self: center;',
					'end'    => 'align-self: flex-end;',
				],
				'selectors'            => [
					'{{WRAPPER}} .the7-simple-post-thumb, {{WRAPPER}} .post-entry-content' => '{{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'image_space',
			[
				'label'     => esc_html__( 'Image Spacing', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => '',
				],
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} [class*="the7-simple-widget-"]'                                                                             => '--image-spacing: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'size_title',
			[
				'label'     => esc_html__( 'Size', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'image_size',
			[
				'label'      => esc_html__( 'Max Width', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min' => 5,
						'max' => 130,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} [class*="the7-simple-widget-"]' => '--image-size: {{SIZE}}{{UNIT}}; --image-ratio: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .the7-simple-post-thumb' => 'width: var(--image-size);',
				],
			]
		);

		$this->template( Image_Aspect_Ratio::class )->add_style_controls();

		$this->add_control(
			'icon_title',
			[
				'label'     => esc_html__( 'Hover icon', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'hover_icon',
			[
				'label' => esc_html__( 'Icon', 'the7mk2' ),
				'type'  => Controls_Manager::ICONS,
				'skin'  => 'inline',
			]
		);

		$this->add_responsive_control(
			'hover_icon_size',
			[
				'label'      => esc_html__( 'Icon Size', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => '24',
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
					'{{WRAPPER}} .the7-hover-icon'     => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .the7-hover-icon svg' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'hover_icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'hover_icon_color',
			[
				'label'     => esc_html__( 'Icon Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '#FFFFFF',
				'selectors' => [
					'{{WRAPPER}} .the7-hover-icon'     => 'color: {{VALUE}};',
					'{{WRAPPER}} .the7-hover-icon svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
				'condition' => [
					'hover_icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'style_title',
			[
				'label'     => esc_html__( 'Style', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'image_border',
				'label'    => esc_html__( 'Border', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .the7-simple-post-thumb',
				'exclude'  => [
					'color',
				],
			]
		);

		$this->add_responsive_control(
			'image_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .the7-simple-post-thumb, {{WRAPPER}} .post-thumbnail-rollover, {{WRAPPER}} .the7-simple-post-thumb img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .the7-simple-post-thumb .layzr-bg'                                 => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'thumbnail_effects_tabs' );

		$this->start_controls_tab(
			'normal',
			[
				'label' => esc_html__( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'           => 'overlay_background',
				'types'          => [ 'classic', 'gradient' ],
				'exclude'        => [ 'image' ],
				'fields_options' => [
					'background' => [
						'label' => esc_html__( 'Overlay', 'the7mk2' ),
					],
				],
				'selector'       => '{{WRAPPER}} .post-thumbnail-rollover:before, {{WRAPPER}} .post-thumbnail-rollover:after { transition: none; }
				{{WRAPPER}} .post-thumbnail-rollover:before,
				{{WRAPPER}} .post-thumbnail-rollover:after
				',
			]
		);

		$this->add_control(
			'image_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .the7-simple-post-thumb' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'img_shadow',
				'selector' => '
				{{WRAPPER}} .the7-simple-post-thumb
				',
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'     => 'thumbnail_filters',
				'selector' => '
				{{WRAPPER}} .post-thumbnail-rollover img
				',
			]
		);

		$this->add_control(
			'thumbnail_opacity',
			[
				'label'      => esc_html__( 'Image opacity (%)', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .post-thumbnail-rollover img' => 'opacity: calc({{SIZE}}/100)',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'hover',
			[
				'label' => esc_html__( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'           => 'overlay_hover_background',
				'types'          => [ 'classic', 'gradient' ],
				'exclude'        => [ 'image' ],
				'fields_options' => [
					'background' => [
						'label' => esc_html__( 'Overlay', 'the7mk2' ),
					],
					'color'      => [
						'selectors' => [
							'
							{{SELECTOR}},
							{{WRAPPER}} .post-thumbnail-rollover:before, {{WRAPPER}} .post-thumbnail-rollover:after { transition: opacity 0.3s ease; } {{SELECTOR}}' => 'background: {{VALUE}};',
						],
					],

				],
				'selector'       => '{{WRAPPER}} .post-thumbnail-rollover:after',
			]
		);

		$this->add_control(
			'image_hover_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} a.post:hover .the7-simple-post-thumb,
					{{WRAPPER}} .the7-simple-post-thumb:hover' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'img_hover_shadow',
				'selector' => '
					{{WRAPPER}} a:hover .the7-simple-post-thumb,
					{{WRAPPER}} .the7-simple-post-thumb:hover',
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'     => 'thumbnail_hover_filters',
				'selector' => '{{WRAPPER}} a:hover .the7-simple-post-thumb img,
					{{WRAPPER}} .post-thumbnail-rollover:hover img
				',
			]
		);

		$this->add_control(
			'thumbnail_hover_opacity',
			[
				'label'      => esc_html__( 'Image opacity (%)', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'selectors'  => [
					'
					{{WRAPPER}} .the7-simple-post-thumb img { transition: opacity 0.3s ease; }
					{{WRAPPER}} a:hover .the7-simple-post-thumb img,
					{{WRAPPER}} .post-thumbnail-rollover:hover img ' => 'opacity: calc({{SIZE}}/100)',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Common simple box style settings.
	 */
	protected function add_box_content_style_controls() {
		$this->start_controls_section(
			'section_design_box',
			[
				'label' => esc_html__( 'Box', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'adaptive_height',
			[
				'label'        => esc_html__( 'Adaptive Height', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'y',
				'default'      => '',
				'prefix_class' => 'auto-height-',
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
					'{{WRAPPER}} .post.wrapper' => 'min-height: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'adaptive_height!' => 'y',
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
				'prefix_class'         => 'icon-box-vertical-align%s-',
				'selectors_dictionary' => [
					'top'    => 'align-items: flex-start;align-content: flex-start;',
					'center' => 'align-items: center;align-content: center;',
					'bottom' => 'align-items: flex-end;align-content: flex-end;',
				],
				'selectors'            => [
					'{{WRAPPER}} .post.wrapper' => '{{VALUE}}',
				],
				'condition'            => [
					'adaptive_height!' => 'y',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'box_border',
				'label'    => esc_html__( 'Border', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .post.wrapper',
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
					'{{WRAPPER}} .post.wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .owl-carousel' => '--box-padding-top: {{TOP}}{{UNIT}};',
					'{{WRAPPER}} .post.wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
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

		$this->add_control(
			'box_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .post.wrapper' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'box_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .post.wrapper' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'box_shadow',
				'label'    => esc_html__( 'Box Shadow', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .post.wrapper',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_color_hover',
			[
				'label' => esc_html__( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			'bg_hover_color',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .post.wrapper:hover' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'box_hover_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .post.wrapper:hover' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'box_hover_shadow',
				'label'    => esc_html__( 'Box Shadow', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .post.wrapper:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}
}
