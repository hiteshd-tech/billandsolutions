<?php
/**
 * The7 elements scroller widget for Elementor.
 *
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Utils;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Less_Vars_Decorator_Interface;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Button;

defined( 'ABSPATH' ) || exit;

class Icon_Box extends The7_Elementor_Widget_Base {

	/**
	 * Get element name.
	 *
	 * Retrieve the element name.
	 *
	 * @return string The name.
	 */
	public function get_name() {
		return 'the7_icon_box_widget';
	}

	protected function the7_title() {
		return esc_html__( 'Icon Box', 'the7mk2' );
	}

	protected function the7_icon() {
		return 'eicon-icon-box';
	}

	protected function the7_keywords() {
		return [ 'icon', 'box' ];
	}

	protected function get_less_file_name() {
		return PRESSCORE_THEME_DIR . '/css/dynamic-less/elementor/the7-icon-box-widget.less';
	}

	public function get_style_depends() {
		return [ 'the7-icon-box-widget' ];
	}

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

	protected function add_content_controls() {

		$this->start_controls_section(
			'section_icon',
			[
				'label' => esc_html__( 'Content', 'the7mk2' ),
			]
		);

		$this->add_control(
			'selected_icon',
			[
				'label' => esc_html__( 'Icon', 'the7mk2' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => [
					'value' => 'fas fa-star',
					'library' => 'fa-solid',
				],
			]
		);

		$this->add_control(
			'title_heading',
			[
				'label'     => esc_html__( 'Title & Description', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_basic_responsive_control(
			'text_align',
			[
				'label' => esc_html__( 'Alignment', 'the7mk2' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'the7mk2' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'the7mk2' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'the7mk2' ),
						'icon' => 'eicon-text-align-right',
					],
					'justify' => [
						'title' => esc_html__( 'Justified', 'the7mk2' ),
						'icon' => 'eicon-text-align-justify',
					],
				],
				'prefix_class'         => 'content-align%s-',
				'default' 			=> 'left',
				'selectors_dictionary' => [
					'left'   => 'align-items: flex-start; text-align: left;',
					'center' => 'align-items: center; text-align: center;',
					'right'  => 'align-items: flex-end; text-align: right;',
					'justify' => 'align-items: stretch; text-align: justify;',
				],
				'selectors'    => [
					'{{WRAPPER}} .box-content' => ' {{VALUE}};',
				]
			]
		);

		$this->add_control(
			'title_html_tag',
			[
				'label' => esc_html__( 'Title HTML Tag', 'the7mk2' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
				],
				'default' => 'h4',
			]
		);

		$this->add_control(
			'title_text',
			[
				'label' => esc_html__( 'Title & Description', 'the7mk2' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => esc_html__( 'This is the heading', 'the7mk2' ),
				'placeholder' => esc_html__( 'Enter your title', 'the7mk2' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'description_text',
			[
				'label' => '',
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'default' => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'the7mk2' ),
				'placeholder' => esc_html__( 'Enter your description', 'the7mk2' ),
				'rows' => 10,
				'separator' => 'none',
				'show_label' => false,
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
				'label' => esc_html__( 'Link', 'the7mk2' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( 'https://your-link.com', 'the7mk2' ),
			]
		);

		$this->add_control(
			'link_click',
			[
				'label'     => esc_html__( 'Apply Link & Hover', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'button',
				'options'   => [
					'box'  => esc_html__( 'Whole box', 'the7mk2' ),
					'button' => esc_html__( "Separate element's", 'the7mk2' ),
				],
			]
		);

		$this->end_controls_section();
	}

	protected function add_box_content_style_controls() {
		$this->start_controls_section(
			'section_design_box',
			[
				'label' => esc_html__( 'Box', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_basic_responsive_control(
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
		$this->add_basic_responsive_control(
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

		$this->add_basic_responsive_control(
			'content_position',
			[
				'label'                => esc_html__( 'Content Position', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options' => [
					'top' => [
						'title' => esc_html__( 'Top', 'the7mk2' ),
						'icon' => 'eicon-v-align-top',
					],
					'center' => [
						'title' => esc_html__( 'Middle', 'the7mk2' ),
						'icon' => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => esc_html__( 'Bottom', 'the7mk2' ),
						'icon' => 'eicon-v-align-bottom',
					],
				],
				'default'              => 'top',
				'prefix_class'         => 'icon-box-vertical-align%s-',
				'selectors_dictionary' => [
					'top'   => 'align-items: flex-start;align-content: flex-start;',
					'center' => 'align-items: center;align-content: center;',
					'bottom'  => 'align-items: flex-end;align-content: flex-end;',
				],
				'selectors'    => [
					'{{WRAPPER}} .the7-box-wrapper' => '{{VALUE}}',
				]
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'box_border',
				'label' => esc_html__( 'Border', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .the7-box-wrapper',
				'exclude'	=> [
					'color'
				]
			]
		);

	    $this->add_basic_responsive_control(
	     	'box_border_radius',
	     	[
	     		'label' => esc_html__('Border Radius', 'the7mk2'),
	     		'type' => Controls_Manager::DIMENSIONS,
	     		'size_units' => ['px', '%'],
	     		'selectors' =>  [
	     			'{{WRAPPER}} .the7-box-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
	     		]
	     	]
	    );

		$this->add_basic_responsive_control(
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
                ]
            ]
        );

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'box_shadow',
				'label' => esc_html__( 'Box Shadow', 'the7mk2' ),
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
                ]
            ]
        );

        $this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'box_hover_shadow',
				'label' => esc_html__( 'Box Shadow', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .the7-box-wrapper:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function add_icon_style_controls() {
		$this->start_controls_section(
			'section_style_icon',
			[
				'label' => esc_html__( 'Icon', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_basic_responsive_control(
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
				'device_args'          => [
					'tablet' => [
						'toggle' => true,
					],
					'mobile' => [
						'toggle' => true,
					],
				],
				'selectors_dictionary' => [
					'top'   => 'flex-flow: column wrap;',
					'left'  => 'flex-flow: row nowrap;',
					'right' => 'flex-flow: row nowrap;',
				],
				'selectors'            => [
					'{{WRAPPER}} .box-content-wrapper' => '{{VALUE}}',
				],
				'prefix_class'         => 'icon-position%s-',
			]
		);

		$icon_position_options            = [
			'start'  => esc_html__( 'Start', 'the7mk2' ),
			'center' => esc_html__( 'Center', 'the7mk2' ),
			'end'    => esc_html__( 'End', 'the7mk2' ),
		];
		$icon_position_options_on_devices = [ '' => esc_html__( 'Default', 'the7mk2' ) ] + $icon_position_options;

		$this->add_basic_responsive_control(
			'icon_position',
			[
				'label'                => esc_html__( 'Align', 'the7mk2' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => 'start',
				'options'              => $icon_position_options,
				'device_args'          => [
					'tablet' => [
						'default' => '',
						'options' => $icon_position_options_on_devices,
					],
					'mobile' => [
						'default' => '',
						'options' => $icon_position_options_on_devices,
					],
				],
				'prefix_class'         => 'icon-vertical-align%s-',
				'selectors_dictionary' => [
					'start'  => 'align-self: flex-start;',
					'center' => 'align-self: center;',
					'end'    => 'align-self: flex-end;',
				],
				'selectors'            => [
					'{{WRAPPER}} .elementor-icon-div' => '{{VALUE}}',
				],
			]
		);

		$this->add_basic_responsive_control(
			'size',
			[
				'label' => esc_html__( 'Size', 'the7mk2' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 6,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-icon' => 'font-size: {{SIZE}}{{UNIT}};',
					//'{{WRAPPER}} .elementor-icon-div' => 'font-size: {{SIZE}}{{UNIT}};',
				]
			]
		);
		$this->add_responsive_control(
			'icon_min_width',
			[
				'label'      => esc_html__( 'Min Width', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => '',
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 1,
						'max' => 500,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .elementor-icon-div .elementor-icon' => 'min-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_min_height',
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
					'{{WRAPPER}} .elementor-icon-div .elementor-icon' => 'min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_basic_responsive_control(
			'icon_padding',
			[
				'label'      => esc_html__( 'Padding', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'selectors'  => [
					'{{WRAPPER}} .elementor-icon-div .elementor-icon' => 'padding: {{SIZE}}{{UNIT}};',
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

		$this->add_basic_responsive_control(
			'border_width',
			[
				'label'      => esc_html__( 'Border Width', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .elementor-icon-div .elementor-icon' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; border-style:solid;',
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
					'{{WRAPPER}} .elementor-icon-div .elementor-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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

		$this->add_control(
			'primary_color',
			[
				'label'     => esc_html__( 'Icon Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-div i'   => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementor-icon-div svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'border_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-div .elementor-icon' => 'border-color: {{VALUE}};',
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
					'{{WRAPPER}} .elementor-icon-div .elementor-icon' => 'background: {{VALUE}};',
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

		$this->add_control(
			'hover_primary_color',
			[
				'label'     => esc_html__( 'Icon Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-div:hover i, {{WRAPPER}} a.the7-box-wrapper:hover .elementor-icon-div i'     => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementor-icon-div:hover svg, {{WRAPPER}} a.the7-box-wrapper:hover .elementor-icon-div svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'hover_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-div:hover .elementor-icon, {{WRAPPER}} a.the7-box-wrapper:hover .elementor-icon' => 'border-color: {{VALUE}};',
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
					'{{WRAPPER}} .elementor-icon-div:hover .elementor-icon, {{WRAPPER}} a.the7-box-wrapper:hover .elementor-icon-div .elementor-icon' => 'background: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_basic_responsive_control(
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
					'{{WRAPPER}}'                                                         => '--icon-spacing: {{SIZE}}{{UNIT}}',
					'(tablet) {{WRAPPER}}.icon-position-tablet-left .elementor-icon-div'  => 'margin: 0 var(--icon-spacing) 0 0',
					'(tablet) {{WRAPPER}}.icon-position-tablet-right .elementor-icon-div' => 'margin: 0 0 0 var(--icon-spacing)',
					'(mobile) {{WRAPPER}}.icon-position-mobile-left .elementor-icon-div'  => ' margin: 0 var(--icon-spacing) 0 0',
					'(mobile) {{WRAPPER}}.icon-position-mobile-right .elementor-icon-div' => 'margin: 0 0 0 var(--icon-spacing)',
					'(tablet) {{WRAPPER}}.icon-position-tablet-top .elementor-icon-div'   => 'margin: 0 0 var(--icon-spacing) 0',
					'(mobile) {{WRAPPER}}.icon-position-mobile-top .elementor-icon-div'   => 'margin: 0 0 var(--icon-spacing) 0',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function add_title_style_controls() {
		// Title Style.
		$this->start_controls_section(
			'title_style',
			[
				'label'     => esc_html__( 'Title', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .the7-box-wrapper .box-heading, {{WRAPPER}} .the7-box-wrapper .box-heading a',
			]
		);
		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'title_text_shadow',
				'selector' => '{{WRAPPER}} .the7-box-wrapper .box-heading, {{WRAPPER}} .the7-box-wrapper .box-heading a',
				'fields_options' => [
					'text_shadow_type' => [
						'label' => esc_html__( 'Text Shadow', 'the7mk2' ),
					],
				],
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

	protected function add_description_style_controls() {
		$this->start_controls_section(
			'section_style_desc',
			[
				'label' => esc_html__( 'Description', 'the7mk2' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'description_typography',
				'selector' => '{{WRAPPER}} .box-description',
			]
		);
		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'description_text_shadow',
				'selector' => '{{WRAPPER}} .box-description',
				'fields_options' => [
					'text_shadow_type' => [
						'label' => esc_html__( 'Text Shadow', 'the7mk2' ),
					],
				],
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

	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->print_inline_css();

		$this->add_render_attribute( 'description_text', 'class', 'box-description' );

		$this->add_inline_editing_attributes( 'title_text', 'none' );
		$this->add_inline_editing_attributes( 'description_text' );

		$parent_wrapper       = '<div class="the7-box-wrapper the7-elementor-widget '. $this->get_unique_class() .'">';
		$parent_wrapper_close = '</div>';
		$title_link       = '';
		$title_link_close = '';

		$this->add_link_attributes( 'link', $settings['link'] );
		$btn_attributes = $this->get_render_attribute_string( 'link' );

		if ( 'button' === $settings['link_click'] ) {
			$title_link       		= '<a ' . $btn_attributes . '>';
			$title_link_close 		= '</a>';
			$icon_wrapper       	= '<a class="elementor-icon-div" '. $btn_attributes .'>';
			$icon_wrapper_close 	= '</a>';
		} else {
			$parent_wrapper       	= '<a class="the7-box-wrapper the7-elementor-widget box-hover '. $this->get_unique_class() .'" '. $btn_attributes .'>';
			$parent_wrapper_close 	= '</a>';
			$icon_wrapper       	= '<div class="elementor-icon-div">';
			$icon_wrapper_close 	= '</div>';
		}

		?>

		<?php echo $parent_wrapper; ?>
			<div class="box-content-wrapper">
				<?php if ( $settings['selected_icon']["value"] !== '' ) : ?>
					<?php echo $icon_wrapper; ?>
						<div class="elementor-icon">
							<?php Icons_Manager::render_icon( $settings['selected_icon'], [ 'aria-hidden' => 'true' ] );?>
						</div>
					<?php echo $icon_wrapper_close; ?>
				<?php endif; ?>
				<div class="box-content">
					<?php if ( $settings['title_text'] ) : ?>
						<?php $title_html_tag = Utils::validate_html_tag( $settings['title_html_tag'] ); ?>
						<<?php echo $title_html_tag; ?> class="box-heading">
							<?php echo $title_link; ?>
								<?php echo wp_kses_post( $settings['title_text'] ); ?>
							<?php echo $title_link_close;?>
						</<?php echo $title_html_tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
					<?php endif; ?>
					<?php if ( ! Utils::is_empty( $settings['description_text'] ) ) : ?>
						<div <?php echo $this->get_render_attribute_string( 'description_text' ); ?>><?php echo $settings['description_text']; ?></div>
					<?php endif; ?>

					<?php $this->render_button(); ?>

				</div>
			</div>
		<?php echo $parent_wrapper_close; ?>
	<?php
	}

	protected function render_button() {
		$settings = $this->get_settings_for_display();

		if ( $settings['button_text'] || $this->template( Button::class )->is_icon_visible() ) {
			// Cleanup button render attributes.
			$this->remove_render_attribute( 'box-button' );

			$tag = 'div';
			if ( 'button' === $settings['link_click'] ) {
				$tag = 'a';
				$this->add_render_attribute( 'box-button', $this->get_render_attributes( 'link' ) ?: [] );
			}

			$this->template( Button::class )->render_button( 'box-button', esc_html( $settings['button_text'] ), $tag );
		}
	}

	protected function less_vars( The7_Elementor_Less_Vars_Decorator_Interface $less_vars ) {
		$less_vars->add_keyword(
			'unique-shortcode-class-name',
			$this->get_unique_class() . '.the7-box-wrapper',
			'~"%s"'
		);
	}
}
