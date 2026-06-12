<?php
/**
 * The7 Expanding Search widget for Elementor.
 *
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Expanding Search widget class.
 */
class Search_Form_Expand extends The7_Elementor_Widget_Base {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'the7-search-expand-widget';
	}

	/**
	 * @return string
	 */
	public function the7_title() {
		return esc_html__( 'Expanding Search', 'the7mk2' );
	}

	/**
	 * @return string
	 */
	public function the7_icon() {
		return 'eicon-site-search';
	}

	/**
	 * @return string[]
	 */
	protected function the7_keywords() {
		return [ 'search', 'form' ];
	}

	/**
	 * @return string[]
	 */
	public function get_style_depends() {
		return [ $this->get_name() ];
	}

	/**
	 * @return string[]
	 */
	public function get_script_depends() {
		return [ $this->get_name() ];
	}

	/**
	 * Register widget assets.
	 */
	protected function register_assets() {
		the7_register_style(
			$this->get_name(),
			THE7_ELEMENTOR_CSS_URI . '/the7-search-expand-widget.css'
		);
		the7_register_script_in_footer(
			$this->get_name(),
			THE7_ELEMENTOR_JS_URI . '/the7-search-expand-widget.js'
		);
	}

	/**
	 * @return void
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'search_content',
			[
				'label' => esc_html__( 'Search Form', 'the7mk2' ),
			]
		);

		$this->add_control(
			'placeholder',
			[
				'label'     => esc_html__( 'Placeholder', 'the7mk2' ),
				'type'      => Controls_Manager::TEXT,
				'separator' => 'before',
				'default'   => esc_html__( 'Search', 'the7mk2' ) . '...',
			]
		);

		$searchable_post_types  = [ '' => esc_html__( 'Default', 'the7mk2' ) ];
		$searchable_post_types += the7_get_public_post_types(
			[
				'exclude_from_search' => false,
				'_builtin'            => false,
			]
		);

		$this->add_control(
			'search_by',
			[
				'label'   => esc_html__( 'Search By', 'the7mk2' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => $searchable_post_types,
			]
		);

		$this->add_control(
			'search_icon',
			[
				'label'            => esc_html__( 'Search Icon', 'the7mk2' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default'          => [
					'value'   => 'fas fa-arrow-right',
					'library' => 'fa-solid',
				],
				'skin'             => 'inline',
				'label_block'      => false,
			]
		);

		$this->add_control(
			'toggle_icon',
			[
				'label'       => esc_html__( 'Open Icon', 'the7mk2' ),
				'type'        => Controls_Manager::ICONS,
				'default'     => [
					'value'   => 'fas fa-search',
					'library' => 'fa-solid',
				],
				'skin'        => 'inline',
				'label_block' => false,
			]
		);

		$this->add_control(
			'toggle_open_icon',
			[
				'label'       => esc_html__( 'Close Icon', 'the7mk2' ),
				'type'        => Controls_Manager::ICONS,
				'default'     => [
					'value'   => 'fas fa-times',
					'library' => 'fa-solid',
				],
				'skin'        => 'inline',
				'label_block' => false,
				'condition'   => [
					'toggle_icon[value]!' => '',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_input_style',
			[
				'label' => esc_html__( 'Input', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'input_typography',
				'selector' => '{{WRAPPER}} input[type="search"].the7-search-form__input',
				'global'   => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
			]
		);

		$this->add_responsive_control(
			'toggle_space',
			[
				'label'     => esc_html__( 'Spacing', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--toggle-icon-spacing: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'position',
			[
				'label'                => esc_html__( 'Expand to', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => [
					'left'  => [
						'title' => esc_html__( 'Left', 'the7mk2' ),
						'icon'  => 'eicon-h-align-left',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'the7mk2' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'default'              => 'right',
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
					'left'  => '--flex-direction: row-reverse; --right-input-padding: min(var(--toggle-width, 40px) + var(--box-right-padding,0px), 100% - var(--box-right-padding,0px)); --left-input-padding: min(var(--box-left-padding,0px), 100% - var(--box-left-padding, 0px)); --left-toggle: auto; --left-expand: auto; --right-expand: 0; --bottom-expand: 0;  --right-toggle: var(--box-right-padding,0px); --input-margin: 0 var(--toggle-icon-spacing, 0px) 0 0; --search-icon-margin: 0 var(--toggle-icon-spacing, 0px) 0 0; --expand-float: right; --button-order: 0;',
					'right' => '--flex-direction: row; --left-input-padding: min(var(--toggle-width, 40px) + var(--box-left-padding,0px), 100% - var(--box-right-padding, 0px)); --right-input-padding: var(--box-right-padding, 0px); --right-toggle: auto; --left-toggle: var(--box-left-padding,0px); --input-margin: 0 0 0 var(--toggle-icon-spacing, 0px); --search-icon-margin: 0 0 0 var(--toggle-icon-spacing, 0px); --right-expand: auto; --bottom-expand: 0;  --left-expand: 0; --expand-float: left; --button-order: 2;',
				],
				'selectors'            => [
					'{{WRAPPER}}' => '{{VALUE}}',
				],
				'separator'            => 'before',
				'prefix_class'         => 'expand%s-position-',
				'frontend_available' => true,
			]
		);

		$this->add_responsive_control(
			'box_expand_width',
			[
				'label'      => esc_html__( 'Expand Width', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => '',
				],
				'size_units' => [ 'px', 'vw' ],
				'range'      => [
					'px' => [
						'min' => 1,
						'max' => 500,
					],
					'vw' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}}' => '--expand-width-focus: {{SIZE}}{{UNIT}};',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'box_expand_transition',
			[
				'label'     => esc_html__( 'Transition Speed (ms)', 'the7mk2' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => '300',
				'min'       => 100,
				'max'       => 1500,
				'selectors' => [
					'{{WRAPPER}}' => '--expand-timing: {{SIZE}}ms;',
				],
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
				'separator'  => 'before',
				'selectors'  => [
					'{{WRAPPER}}'                          => '--input-height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .the7-search-expand-wrap' => 'min-height: max({{SIZE}}{{UNIT}}, var(--submit-height, 30px));',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'           => 'box_border',
				'label'          => esc_html__( 'Border', 'the7mk2' ),
				'fields_options' => [
					'width'  => [
						'selectors' => [
							'{{SELECTOR}}' => '--the7-top-input-border-width: {{TOP}}{{UNIT}}; --the7-right-input-border-width: {{RIGHT}}{{UNIT}}; --the7-bottom-input-border-width: {{BOTTOM}}{{UNIT}}; --the7-left-input-border-width: {{LEFT}}{{UNIT}};',
						],
					],
					'border' => [
						'selectors' => [
							'{{SELECTOR}}' => '--the7-box-border-style: {{VALUE}};',
						],
					],
				],
				'selector'       => '{{WRAPPER}}',
				'exclude'        => [
					'color',
				],
			]
		);

		$this->add_responsive_control(
			'box_padding',
			[
				'label'              => esc_html__( 'Padding', 'the7mk2' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => [ 'px', '%' ],
				'allowed_dimensions' => 'horizontal',
				'range'              => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'          => [
					'{{WRAPPER}}'                          => '--box-right-padding: {{RIGHT}}{{UNIT}}; --box-left-padding: {{LEFT}}{{UNIT}}',
					'{{WRAPPER}} .the7-search-expand-wrap' => 'padding: {{TOP}}{{UNIT}} var(--right-input-padding, {{RIGHT}}{{UNIT}}) {{BOTTOM}}{{UNIT}} var(--left-input-padding, {{LEFT}}{{UNIT}})',
				],
			]
		);

		$this->add_responsive_control(
			'border_radius',
			[
				'label'     => esc_html__( 'Border Radius', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .the7-search-expand-wrap' => 'border-radius: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_input_colors' );

		$this->start_controls_tab(
			'tab_input_normal',
			[
				'label' => esc_html__( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_control(
			'input_placeholder_color',
			[
				'label'     => esc_html__( 'Placeholder Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => [
					'default' => Global_Colors::COLOR_TEXT,
				],
				'selectors' => [
					'{{WRAPPER}}' => '--placeholder-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'input_text_color',
			[
				'label'     => esc_html__( 'Text Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => [
					'default' => Global_Colors::COLOR_TEXT,
				],
				'selectors' => [
					'{{WRAPPER}} .the7-search-form__input' => 'color: {{VALUE}}; fill: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'input_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .the7-search-expand-wrap' => 'background-color: {{VALUE}}',

				],
			]
		);

		$this->add_control(
			'input_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .the7-search-expand-wrap' => 'border-color: {{VALUE}}',
				],
				'condition' => [
					'box_border_border!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'           => 'input_box_shadow',
				'selector'       => '{{WRAPPER}} .the7-search-expand-wrap',
				'fields_options' => [
					'box_shadow_type' => [
						'separator' => 'default',
					],
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_input_focus',
			[
				'label' => esc_html__( 'Focus', 'the7mk2' ),
			]
		);

		$this->add_control(
			'input_placeholder_color_focus',
			[
				'label'     => esc_html__( 'Placeholder Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => [
					'default' => Global_Colors::COLOR_TEXT,
				],
				'selectors' => [
					'{{WRAPPER}}' => '--placeholder-color-focus: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'input_text_color_focus',
			[
				'label'     => esc_html__( 'Text Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => [
					'default' => Global_Colors::COLOR_TEXT,
				],
				'selectors' => [
					'{{WRAPPER}} .the7-search-form__input:focus' => 'color: {{VALUE}}; fill: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'input_background_color_focus',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}  .the7-search-form.show-input .the7-search-expand-wrap' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'input_border_color_focus',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .the7-search-form.show-input .the7-search-expand-wrap' => 'border-color: {{VALUE}}',
				],
				'condition' => [
					'box_border_border!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'           => 'input_box_shadow_focus',
				'selector'       => '{{WRAPPER}} .the7-search-form.show-input .the7-search-expand-wrap',
				'fields_options' => [
					'box_shadow_type' => [
						'separator' => 'default',
					],
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_button_style',
			[
				'label' => esc_html__( 'Search Icon', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label'     => esc_html__( 'Icon Size', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--e-search-form-submit-icon-size: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'search_icon[value]!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'button_width',
			[
				'label'     => esc_html__( 'Width', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--icon-width: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .the7-search-form__submit' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_height',
			[
				'label'     => esc_html__( 'Height', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--submit-height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .the7-search-form__submit' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'           => 'btn_border',
				'label'          => esc_html__( 'Border', 'the7mk2' ),
				'fields_options' => [
					'width'  => [
						'selectors' => [
							'{{SELECTOR}}' => '--the7-top-btn-border-width: {{TOP}}{{UNIT}}; --the7-right-btn-border-width: {{RIGHT}}{{UNIT}}; --the7-bottom-btn-border-width: {{BOTTOM}}{{UNIT}}; --the7-left-btn-border-width: {{LEFT}}{{UNIT}};',
						],
					],
					'border' => [
						'selectors' => [
							'{{SELECTOR}}' => '--the7-btn-border-style: {{VALUE}};',
						],
					],
				],
				'selector'       => '{{WRAPPER}}',
				'exclude'        => [
					'color',
				],
			]
		);

		$this->add_responsive_control(
			'btn_border_radius',
			[
				'label'     => esc_html__( 'Border Radius', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .the7-search-form__submit' => 'border-radius: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_button_colors' );

		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label' => esc_html__( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_control(
			'button_text_color',
			[
				'label'     => esc_html__( 'Icon Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .the7-search-form__submit' => '--e-search-form-submit-text-color: {{VALUE}}',
				],
				'condition' => [
					'search_icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'button_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => [
					'default' => Global_Colors::COLOR_SECONDARY,
				],
				'selectors' => [
					'{{WRAPPER}} .the7-search-form__submit' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'btn_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .the7-search-form__submit' => 'border-color: {{VALUE}}',
				],
				'condition' => [
					'btn_border_border!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'           => 'btn_box_shadow',
				'selector'       => '{{WRAPPER}} .the7-search-form__submit',
				'fields_options' => [
					'box_shadow_type' => [
						'separator' => 'default',
					],
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label' => esc_html__( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			'button_text_color_hover',
			[
				'label'     => esc_html__( 'Icon Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .the7-search-form__submit:hover' => '--e-search-form-submit-text-hover-color: {{VALUE}}',
				],
				'condition' => [
					'search_icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'button_background_color_hover',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .the7-search-form__submit:hover' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'btn_border_hover_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .the7-search-form__submit:hover' => 'border-color: {{VALUE}}',
				],
				'condition' => [
					'btn_border_border!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'           => 'btn_box_shadow_hover',
				'selector'       => '{{WRAPPER}} .the7-search-form__submit:hover',
				'fields_options' => [
					'box_shadow_type' => [
						'separator' => 'default',
					],
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'style_toggle',
			[
				'label' => esc_html__( 'Open/Close Icon', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'toggle_align',
			[
				'label'                => esc_html__( 'Alignment', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'default'              => 'center',
				'options'              => [
					'left'    => [
						'title' => esc_html__( 'Left', 'the7mk2' ),
						'icon'  => 'eicon-h-align-left',
					],
					'center'  => [
						'title' => esc_html__( 'Center', 'the7mk2' ),
						'icon'  => 'eicon-h-align-center',
					],
					'right'   => [
						'title' => esc_html__( 'Right', 'the7mk2' ),
						'icon'  => 'eicon-h-align-right',
					],
					'justify' => [
						'title' => esc_html__( 'Justified', 'the7mk2' ),
						'icon'  => 'eicon-text-align-justify',
					],
				],
				'selectors_dictionary' => [
					'left'    => '--justify: flex-start;',
					'center'  => '--justify: center;',
					'right'   => '--justify: flex-end;',
					'justify' => '--justify: stretch;',
				],
				'selectors'            => [
					'{{WRAPPER}} .horizontal-menu-wrap' => '{{VALUE}}',
					'{{WRAPPER}}.horizontal-menu--dropdown-desktop .horizontal-menu-wrap, {{WRAPPER}} .horizontal-menu-toggle' => 'align-self: var(--justify, center)',
					'(tablet) {{WRAPPER}}.horizontal-menu--dropdown-tablet .horizontal-menu-wrap' => 'align-self: var(--justify, center)',
					'(mobile) {{WRAPPER}}.horizontal-menu--dropdown-mobile .horizontal-menu-wrap' => 'align-self: var(--justify, center)',
				],
				'prefix_class'         => 'toggle-align%s-',
				'condition'            => [
					'dropdown!' => 'none',
				],
			]
		);

		$this->add_control(
			'toggle_icon_heading',
			[
				'label' => esc_html__( 'Icon', 'the7mk2' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_responsive_control(
			'toggle_size',
			[
				'label'     => esc_html__( 'Icon Size', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 15,
					],
				],
				'selectors' => [
					'{{WRAPPER}}'                          => '--toggle-icon-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .the7-search-form-toggle' => 'font-size: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'toggle_icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'toggle_box_heading',
			[
				'label'     => esc_html__( 'Box', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'toggle_min_width',
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
					'{{WRAPPER}}' => '--toggle-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'toggle_min_height',
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
					'{{WRAPPER}}' => '--toggle-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'     => 'toggle_border',
				'selector' => '{{WRAPPER}} .the7-search-form-toggle',
				'exclude'  => [ 'color' ],
			]
		);

		$this->add_responsive_control(
			'toggle_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .the7-search-form-toggle' => 'border-radius: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'toggle_colors_heading',
			[
				'label'     => esc_html__( 'Colors', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->start_controls_tabs( 'tabs_toggle_colors' );

		$this->start_controls_tab(
			'tab_toggle_normal',
			[
				'label' => esc_html__( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_control(
			'toggle_text_color',
			[
				'label'     => esc_html__( 'Icon color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .the7-search-form-toggle' => 'color: {{VALUE}}',
					'{{WRAPPER}} .the7-search-form-toggle svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
				'condition' => [
					'search_icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'toggle_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => [
					'default' => Global_Colors::COLOR_SECONDARY,
				],
				'selectors' => [
					'{{WRAPPER}} .the7-search-form-toggle' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'toggle_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .the7-search-form-toggle' => 'border-color: {{VALUE}}',
				],
				'condition' => [
					'toggle_border_border!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'           => 'toggle_box_shadow',
				'selector'       => '{{WRAPPER}} .the7-search-form-toggle',
				'fields_options' => [
					'box_shadow_type' => [
						'separator' => 'default',
					],
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_toggle_hover',
			[
				'label' => esc_html__( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			'toggle_text_color_hover',
			[
				'label'     => esc_html__( 'Icon color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .the7-search-form-toggle:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .the7-search-form-toggle:hover svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
				'condition' => [
					'search_icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'toggle_background_color_hover',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .the7-search-form-toggle:hover' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'toggle_border_hover_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .the7-search-form-toggle:hover' => 'border-color: {{VALUE}}',
				],
				'condition' => [
					'toggle_border_border!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'           => 'toggle_box_shadow_hover',
				'selector'       => '{{WRAPPER}} .the7-search-form-toggle:hover',
				'fields_options' => [
					'box_shadow_type' => [
						'separator' => 'default',
					],
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'toggle_toggle_focus',
			[
				'label' => esc_html__( 'Focus', 'the7mk2' ),
			]
		);

		$this->add_control(
			'toggle_text_color_focus',
			[
				'label'     => esc_html__( 'Icon color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .the7-search-form.show-input .the7-search-form-toggle:not(:hover)' => 'color: {{VALUE}}',
					'{{WRAPPER}} .the7-search-form.show-input .the7-search-form-toggle:not(:hover) svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
				'condition' => [
					'search_icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'toggle_background_color_focus',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .the7-search-form.show-input .the7-search-form-toggle:not(:hover)' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'toggle_border_focus_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .the7-search-form.show-input .the7-search-form-toggle:not(:hover)' => 'border-color: {{VALUE}}',
				],
				'condition' => [
					'toggle_border_border!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'           => 'toggle_box_shadow_focus',
				'selector'       => '{{WRAPPER}} .the7-search-form.show-input .the7-search-form-toggle:not(:hover)',
				'fields_options' => [
					'box_shadow_type' => [
						'separator' => 'default',
					],
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
		$this->add_render_attribute(
			'input',
			[
				'placeholder' => $settings['placeholder'],
				'class'       => 'the7-search-form__input',
				'type'        => 'search',
				'name'        => 's',
				'title'       => esc_html__( 'Search', 'the7mk2' ),
				'value'       => get_search_query(),
			]
		);
		?>
		<form class="the7-search-form" role="search" action="<?php echo esc_url( home_url() ); ?>" method="get">
			<div class="the7-search-expand-wrap">
				<div class="the7-search-expand__container">

					<input <?php $this->print_render_attribute_string( 'input' ); ?>>
					<?php if ( $settings['search_by'] ) : ?>
					<input type="hidden" name="post_type" value="<?php echo esc_attr( $settings['search_by'] ); ?>">
					<?php endif; ?>

					<button class="the7-search-form__submit" type="submit" title="<?php esc_attr_e( 'Search', 'the7mk2' ); ?>" aria-label="<?php esc_attr_e( 'Search', 'the7mk2' ); ?>">

							<?php Icons_Manager::render_icon( $settings['search_icon'], [ 'aria-hidden' => 'true' ] ); ?>
							<span class="elementor-screen-only"><?php esc_html_e( 'Search', 'the7mk2' ); ?></span>

					</button>

				</div>
				<a class="the7-search-form-toggle" href="#">
				<?php
					Icons_Manager::render_icon( $settings['toggle_icon'], [ 'aria-hidden' => 'true' ] );
					Icons_Manager::render_icon( $settings['toggle_open_icon'], [ 'aria-hidden' => 'true' ] );
				?>
				</a>
			</div>
		</form>
		<?php
	}
}
