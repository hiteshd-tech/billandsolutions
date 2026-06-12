<?php
/**
 * The7 "Image Box" widget for Elementor.
 *
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Icons_Manager;
use Elementor\Utils;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Button;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Image_Aspect_Ratio;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Image_Size;

defined( 'ABSPATH' ) || exit;

/**
 * Image_Box class.
 */
class Image_Box extends The7_Elementor_Widget_Base {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'the7_image_box_widget';
	}

	/**
	 * @return string
	 */
	protected function the7_title() {
		return esc_html__( 'Image Box', 'the7mk2' );
	}

	/**
	 * @return string
	 */
	protected function the7_icon() {
		return 'eicon-icon-box';
	}

	/**
	 * @return string[]
	 */
	protected function the7_keywords() {
		return [ 'image', 'box' ];
	}

	/**
	 * @return string[]
	 */
	public function get_style_depends() {
		return [ 'the7-image-box-widget' ];
	}

	/**
	 * @return string[]
	 */
	public function get_script_depends() {
		return [ 'the7-image-box-widget' ];
	}

	/**
	 * @return void
	 */
	protected function register_controls() {
		// Content.
		$this->add_content_controls();

		// Style.
		$this->add_box_content_style_controls();
		$this->add_icon_style_controls();
		$this->add_title_style_controls();
		$this->add_description_style_controls();
		$this->template( Button::class )->add_style_controls();
	}

	/**
	 * @return void
	 */
	protected function add_content_controls() {

		$this->start_controls_section(
			'section_icon',
			[
				'label' => esc_html__( 'Content', 'the7mk2' ),
			]
		);

		$this->add_control(
			'image',
			[
				'label'   => esc_html__( 'Image', 'the7mk2' ),
				'type'    => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
			]
		);

		$this->template( Image_Size::class )->add_style_controls();

		$this->add_control(
			'title_heading',
			[
				'label'     => esc_html__( 'Title & Description', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'text_align',
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
				'prefix_class'         => 'content-align%s-',
				'default'              => 'left',
				'selectors_dictionary' => [
					'left'    => 'align-items: flex-start; text-align: left;',
					'center'  => 'align-items: center; text-align: center;',
					'right'   => 'align-items: flex-end; text-align: right;',
					'justify' => 'align-items: stretch; text-align: justify;',
				],
				'selectors'            => [
					'{{WRAPPER}} .box-content' => ' {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_html_tag',
			[
				'label'   => esc_html__( 'Title HTML Tag', 'the7mk2' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'h1'  => 'H1',
					'h2'  => 'H2',
					'h3'  => 'H3',
					'h4'  => 'H4',
					'h5'  => 'H5',
					'h6'  => 'H6',
					'div' => 'div',
				],
				'default' => 'h4',
			]
		);

		$this->add_control(
			'title_text',
			[
				'label'       => esc_html__( 'Title & Description', 'the7mk2' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [
					'active' => true,
				],
				'default'     => esc_html__( 'This is the heading', 'the7mk2' ),
				'placeholder' => esc_html__( 'Enter your title', 'the7mk2' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'description_text',
			[
				'label'       => '',
				'type'        => Controls_Manager::TEXTAREA,
				'dynamic'     => [
					'active' => true,
				],
				'default'     => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'the7mk2' ),
				'placeholder' => esc_html__( 'Enter your description', 'the7mk2' ),
				'rows'        => 10,
				'separator'   => 'none',
				'show_label'  => false,
			]
		);

		$this->add_control(
			'button_heading',
			[
				'label'     => esc_html__( 'Button', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'button_text',
			[
				'label'       => esc_html__( 'Button Text', 'the7mk2' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => '',
			]
		);

		$this->add_control(
			'link_heading',
			[
				'label'     => esc_html__( 'Link & Hover', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'link',
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
			'link_click',
			[
				'label'   => esc_html__( 'Apply Link & Hover', 'the7mk2' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'button',
				'options' => [
					'box'    => esc_html__( 'Whole box', 'the7mk2' ),
					'button' => esc_html__( "Separate element's", 'the7mk2' ),
				],
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
				'label'      => esc_html__( 'Min Height', 'the7mk2' ),
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
					'{{WRAPPER}} .the7-box-wrapper' => 'min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'box_fixed_height',
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
					'{{WRAPPER}} .the7-box-wrapper' => 'height: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .the7-box-wrapper' => '{{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'     => 'box_border',
				'label'    => esc_html__( 'Border', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .the7-box-wrapper',
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
					'{{WRAPPER}} .the7-box-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .the7-box-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
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
					'{{WRAPPER}} .the7-box-wrapper' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'box_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .the7-box-wrapper' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'box_shadow',
				'label'    => esc_html__( 'Box Shadow', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .the7-box-wrapper',
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
					'{{WRAPPER}} .the7-box-wrapper:hover' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'box_hover_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .the7-box-wrapper:hover' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'box_hover_shadow',
				'label'    => esc_html__( 'Box Shadow', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .the7-box-wrapper:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function add_icon_style_controls() {
		$this->start_controls_section(
			'section_style_image',
			[
				'label' => esc_html__( 'Image', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
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
						'max' => 1030,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}}' => '--image-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->template( Image_Aspect_Ratio::class )->add_style_controls();

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
					'top'   => $this->combine_to_css_vars_definition_string(
						[
							'flex-flow'     => 'column wrap',
							'img-space'     => '0 0 var(--icon-spacing, 15px) 0',
							'img-order'     => '0',
							'img-width'     => '100%',
							'content-width' => ' width: 100%',
						]
					),
					'left'  => $this->combine_to_css_vars_definition_string(
						[
							'flex-flow'     => 'row nowrap',
							'img-space'     => '0 var(--icon-spacing, 15px) 0 0',
							'img-order'     => '0',
							'img-width'     => '30%',
							'content-width' => ' width: calc(100% - var(--image-size) - var(--icon-spacing, 15px))',
						]
					),
					'right' => $this->combine_to_css_vars_definition_string(
						[
							'flex-flow'     => 'row nowrap',
							'img-space'     => '0 0 0 var(--icon-spacing, 15px)',
							'img-order'     => '2',
							'img-width'     => '30%',
							'content-width' => ' width: calc(100% - var(--image-size) - var(--icon-spacing, 15px))',
						]
					),
				],
				'selectors'            => [
					'{{WRAPPER}}' => '{{VALUE}}',
				],
			]
		);

		$icon_position_options = [
			'start'  => esc_html__( 'Start', 'the7mk2' ),
			'center' => esc_html__( 'Center', 'the7mk2' ),
			'end'    => esc_html__( 'End', 'the7mk2' ),
		];
		$this->add_responsive_control(
			'icon_position',
			[
				'label'                => esc_html__( 'Align', 'the7mk2' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => 'start',
				'options'              => $icon_position_options,
				'device_args'          => $this->generate_device_args(
					[
						'default' => '',
						'options' => [ '' => esc_html__( 'Default', 'the7mk2' ) ] + $icon_position_options,
					]
				),
				'prefix_class'         => 'icon-vertical-align%s-',
				'selectors_dictionary' => [
					'start'  => 'align-self: flex-start;',
					'center' => 'align-self: center;',
					'end'    => 'align-self: flex-end;',
				],
				'selectors'            => [
					'{{WRAPPER}} .elementor-image-div' => '{{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'icon_padding',
			[
				'label'      => esc_html__( 'Padding', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'selectors'  => [
					'{{WRAPPER}} .elementor-image-div img' => 'padding: {{SIZE}}{{UNIT}};',
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
			'icon_title',
			[
				'label'     => esc_html__( 'Hover icon', 'the7mk2' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
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
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'border_width',
			[
				'label'      => esc_html__( 'Border Width', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .elementor-image-div' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; border-style:solid;',
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
					'{{WRAPPER}} .elementor-image-div' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
			'border_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-image-div' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-image-div' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'img_shadow',
				'selector' => '{{WRAPPER}} .elementor-image-div',
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
				'default'    => [
					'unit' => '%',
					'size' => '100',
				],
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
			'icon_colors_hover',
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
			'hover_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-image-div:hover, {{WRAPPER}} a.the7-box-wrapper:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_hover_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-image-div:hover, {{WRAPPER}} a.the7-box-wrapper:hover .elementor-image-div' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'img_hover_shadow',
				'selector' => '
					{{WRAPPER}} a:hover .elementor-image-div,
					{{WRAPPER}} .elementor-image-div:hover',
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'     => 'thumbnail_hover_filters',
				'selector' => '{{WRAPPER}} a:hover .elementor-image-div img,
					{{WRAPPER}} .post-thumbnail-rollover:hover img
				',
			]
		);

		$this->add_control(
			'thumbnail_hover_opacity',
			[
				'label'      => esc_html__( 'Image opacity (%)', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => '%',
					'size' => '100',
				],
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
					{{WRAPPER}} .elementor-image-div img { transition: opacity 0.3s ease; }
					{{WRAPPER}} a:hover .the7-simple-post-thumb img,
					{{WRAPPER}} .post-thumbnail-rollover:hover img ' => 'opacity: calc({{SIZE}}/100)',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'icon_spacing',
			[
				'label'      => esc_html__( 'Spacing', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 15,
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
					'{{WRAPPER}}' => '--icon-spacing: {{SIZE}}{{UNIT}}',
				],
				'separator'  => 'before',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function add_title_style_controls() {
		// Title Style.
		$this->start_controls_section(
			'title_style',
			[
				'label' => esc_html__( 'Title', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .the7-box-wrapper .box-heading, {{WRAPPER}} .the7-box-wrapper .box-heading a',
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
					'{{WRAPPER}} .the7-box-wrapper .box-heading, {{WRAPPER}} .the7-box-wrapper .box-heading a' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .the7-box-wrapper .box-heading:hover, {{WRAPPER}} .the7-box-wrapper .box-heading:hover a' => 'color: {{VALUE}};',
					'{{WRAPPER}} a.the7-box-wrapper:hover .box-heading, {{WRAPPER}} a.the7-box-wrapper:hover .box-heading a' => 'color: {{VALUE}};',
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
	protected function add_description_style_controls() {
		$this->start_controls_section(
			'section_style_desc',
			[
				'label' => esc_html__( 'Description', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'description_typography',
				'selector' => '{{WRAPPER}} .box-description',
			]
		);

		$this->start_controls_tabs( 'tabs_description_style' );

		$this->start_controls_tab(
			'tab_desc_color_normal',
			[
				'label' => esc_html__( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_control(
			'short_desc_color',
			[
				'label'     => esc_html__( 'Font Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .box-description' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_desc_color_hover',
			[
				'label' => esc_html__( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			'short_desc_color_hover',
			[
				'label'     => esc_html__( 'Font Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .box-description:hover, {{WRAPPER}} a.the7-box-wrapper:hover .box-description' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'gap_above_description',
			[
				'label'      => esc_html__( 'Description Spacing Above', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 10,
				],
				'selectors'  => [
					'{{WRAPPER}} .box-description' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'description_text', 'class', 'box-description' );

		$this->add_inline_editing_attributes( 'title_text', 'none' );
		$this->add_inline_editing_attributes( 'description_text' );

		$parent_wrapper       = '<div class="the7-box-wrapper the7-elementor-widget ' . $this->get_unique_class() . '">';
		$parent_wrapper_close = '</div>';
		$title_link           = '';
		$title_link_close     = '';

		$this->add_link_attributes( 'link', $settings['link'] );
		$btn_attributes    = $this->get_render_attribute_string( 'link' );
		$img_wrapper_class = implode(
			' ',
			array_filter(
				[
					'post-thumbnail-rollover',
					$this->template( Image_Size::class )->get_wrapper_class(),
					$this->template( Image_Aspect_Ratio::class )->get_wrapper_class(),
				]
			)
		);

		if ( 'button' === $settings['link_click'] ) {
			$title_link         = '<a ' . $btn_attributes . '>';
			$title_link_close   = '</a>';
			$icon_wrapper       = '<a class="' . $img_wrapper_class . '" ' . $btn_attributes . '>';
			$icon_wrapper_close = '</a>';
		} else {
			$parent_wrapper       = '<a class="the7-box-wrapper the7-elementor-widget box-hover ' . $this->get_unique_class() . '" ' . $btn_attributes . '>';
			$parent_wrapper_close = '</a>';
			$icon_wrapper         = '<div class="' . $img_wrapper_class . '">';
			$icon_wrapper_close   = '</div>';
		}
		?>

		<?php echo $parent_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<div class="box-content-wrapper">
				<?php if ( ! empty( $settings['image']['id'] ) ) { ?>
				<div class="elementor-image-div ">
					<?php
					echo $icon_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $this->template( Image_Size::class )->get_image( $settings['image']['id'] );

					echo '<span class="the7-hover-icon">';
					Icons_Manager::render_icon(
						$this->get_settings_for_display( 'hover_icon' ),
						[ 'aria-hidden' => 'true' ],
						'i'
					);
					echo '</span>';

					echo $icon_wrapper_close; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					?>
				</div>
				<?php } ?>

				<div class="box-content">
					<?php
					if ( $settings['title_text'] ) {
						$title_html_tag = Utils::validate_html_tag( $settings['title_html_tag'] );

						echo '<' . $title_html_tag . ' class="box-heading">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						echo $title_link; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						echo wp_kses_post( $settings['title_text'] );
						echo $title_link_close; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						echo '</' . $title_html_tag . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}

					if ( ! Utils::is_empty( $settings['description_text'] ) ) {
						echo '<div ' . $this->get_render_attribute_string( 'description_text' ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						echo wp_kses_post( $settings['description_text'] );
						echo '</div>';
					}

					if ( $settings['button_text'] || $this->template( Button::class )->is_icon_visible() ) {
						// Cleanup button render attributes.
						$this->remove_render_attribute( 'box-button' );

						$tag = 'div';
						if ( $settings['link_click'] === 'button' ) {
							$tag = 'a';
							$this->add_render_attribute( 'box-button', $this->get_render_attributes( 'link' ) ?: [] );
						}

						$this->template( Button::class )->render_button( 'box-button', esc_html( $settings['button_text'] ), $tag );
					}
					?>
				</div>
			</div>
		<?php
		echo $parent_wrapper_close; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
