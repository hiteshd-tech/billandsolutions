<?php
/**
 * Search widget for Elementor.
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
 * Search_Form class.
 */
class Search_Form extends The7_Elementor_Widget_Base {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'the7-search-form-widget';
	}

	/**
	 * @return string
	 */
	public function the7_title() {
		return esc_html__( 'Search Form', 'the7mk2' );
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
			THE7_ELEMENTOR_CSS_URI . '/the7-search-form-widget'
		);
		the7_register_script_in_footer(
			$this->get_name(),
			THE7_ELEMENTOR_JS_URI . '/the7-search-form-widget.js'
		);
	}

	/**
	 * @return void
	 */
	protected function register_controls() {
		$selectors = '{{WRAPPER}}.the7-search-form-icon-position-inside .the7-search-form, {{WRAPPER}}:not(.the7-search-form-icon-position-inside) .the7-search-input__container';

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
				'label'   => esc_html__( 'Seach By', 'the7mk2' ),
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
					'value'   => 'fas fa-search',
					'library' => 'fa-solid',
				],
				'skin'             => 'inline',
				'label_block'      => false,
			]
		);
		$this->add_control(
			'clear_icon',
			[
				'label'            => esc_html__( 'Clear Icon', 'the7mk2' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default'          => [
					'value'   => 'fas fa-times',
					'library' => 'fa-solid',
				],
				'skin'             => 'inline',
				'label_block'      => false,
			]
		);
		$this->add_responsive_control(
			'search_position',
			[
				'label'                => esc_html__( 'Alignment', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => [
					'left'   => [
						'title' => esc_html__( 'Left', 'the7mk2' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'the7mk2' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'the7mk2' ),
						'icon'  => 'eicon-text-align-right',
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
					'left'   => 'justify-content: flex-start;',
					'center' => 'justify-content: center;',
					'right'  => 'justify-content: flex-end;',
				],
				'selectors'            => [
					'{{WRAPPER}} .elementor-widget-container {display: flex;} {{WRAPPER}} .the7-search-form, {{WRAPPER}} .elementor-widget-container' => '{{VALUE}}',
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
			'box_height',
			[
				'label'      => esc_html__( 'Height', 'the7mk2' ),
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
					$selectors => 'min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'           => 'box_border',
				'label'          => esc_html__( 'Border', 'the7mk2' ),
				'fields_options' => [
					'width' => [
						'selectors' => [
							'{{SELECTOR}}' => '--the7-top-input-border-width: {{TOP}}{{UNIT}}; --the7-right-input-border-width: {{RIGHT}}{{UNIT}}; --the7-bottom-input-border-width: {{BOTTOM}}{{UNIT}}; --the7-left-input-border-width: {{LEFT}}{{UNIT}}; border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					],
				],
				'selector'       => $selectors,
				'exclude'        => [
					'color',
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
					'{{WRAPPER}}' => '--box-top-padding: {{TOP}}{{UNIT}}; --box-right-padding: {{RIGHT}}{{UNIT}}; --box-bottom-padding: {{BOTTOM}}{{UNIT}}; --box-left-padding: {{LEFT}}{{UNIT}}',
					$selectors    => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'border_radius',
			[
				'label'     => esc_html__( 'Border Radius', 'the7mk2' ),
				'type'      => Controls_Manager::DIMENSIONS,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' => [
					$selectors => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
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
					'{{WRAPPER}}' => '--input-color: {{VALUE}};',
					'{{WRAPPER}} .the7-search-form__input,
					{{WRAPPER}} .the7-search-form__icon' => 'color: {{VALUE}}; fill: {{VALUE}};',
				],
			]
		);


		$this->add_control(
			'input_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$selectors => 'background-color: {{VALUE}}',

				],
			]
		);

		$this->add_control(
			'input_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$selectors => 'border-color: {{VALUE}}',

				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'           => 'input_box_shadow',
				'selector'       => $selectors,
				'fields_options' => [
					'box_shadow_type' => [
						'separator' => 'default',
					],
				],
			]
		);

		$this->add_responsive_control(
			'box_width',
			[
				'label'      => esc_html__( 'Width', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => '',
				],
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min' => 1,
						'max' => 1000,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}}' => '--expand-width: {{SIZE}}{{UNIT}};',
					' {{WRAPPER}}.the7-search-form-icon-position-outside .the7-search-input__container' => 'width: min({{SIZE}}{{UNIT}}, 100% - var(--icon-width,30px) - var(--btn-space, 10px));',
					'{{WRAPPER}}.the7-search-form-icon-position-inside .the7-search-form' => 'width: {{SIZE}}{{UNIT}};',
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
				'selectors' => [
					'{{WRAPPER}}' => '--input-color-focus: {{VALUE}};',
					'{{WRAPPER}}.the7-search-form-icon-position-inside .the7-search-form:focus-within .the7-search-form__input,
					{{WRAPPER}}.the7-search-form-icon-position-inside .the7-search-form:focus-within .the7-search-form__icon, {{WRAPPER}}:not(.the7-search-form-icon-position-inside) .the7-search-form__input:focus
					' => 'color: {{VALUE}}; fill: {{VALUE}};',
				],
			]
		);


		$this->add_control(
			'input_background_color_focus',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}.the7-search-form-icon-position-inside .the7-search-form:focus-within, {{WRAPPER}}:not(.the7-search-form-icon-position-inside) .the7-search-input__container:focus-within' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'input_border_color_focus',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}.the7-search-form-icon-position-inside .the7-search-form:focus-within, {{WRAPPER}}:not(.the7-search-form-icon-position-inside) .the7-search-input__container:focus-within' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'           => 'input_box_shadow_focus',
				'selector'       => '{{WRAPPER}}.the7-search-form-icon-position-inside .the7-search-form:focus-within, {{WRAPPER}}:not(.the7-search-form-icon-position-inside) .the7-search-input__container:focus-within',
				'fields_options' => [
					'box_shadow_type' => [
						'separator' => 'default',
					],
				],
			]
		);

		$this->add_responsive_control(
			'box_width_focus',
			[
				'label'      => esc_html__( 'Width', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => '',
				],
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min' => 1,
						'max' => 1000,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}}.the7-search-form-icon-position-outside .the7-search-input__container:focus-within' => 'width: min({{SIZE}}{{UNIT}}, 100% - var(--icon-width,30px) - var(--btn-space, 10px));',
					'{{WRAPPER}}.the7-search-form-icon-position-inside .the7-search-form:focus-within' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
		$this->start_controls_section(
			'section_clear_style',
			[
				'label'     => esc_html__( 'Clear Icon', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'search_icon[value]!' => '',
				],
			]
		);
		$this->add_responsive_control(
			'clear_size',
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
					'{{WRAPPER}} .the7-clear-search' => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .the7-clear-search svg' => 'width: {{SIZE}}{{UNIT}}; height: auto;',
				],
				'separator' => 'before',
				'condition' => [
					'clear_icon[value]!' => '',
				],
			]
		);
		$this->add_responsive_control(
			'clear_space',
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
					'{{WRAPPER}} .the7-clear-search' => 'margin-left: {{SIZE}}{{UNIT}}',
				],
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'           => 'clear_border',
				'label'          => esc_html__( 'Border', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .the7-clear-search',
				'exclude'        => [
					'color',
				],
			]
		);

		$this->add_responsive_control(
			'clear_width',
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
					'{{WRAPPER}}' => '--clear-width: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .the7-clear-search' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'clear_height',
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
					'{{WRAPPER}} .the7-clear-search' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'clear_border_radius',
			[
				'label'     => esc_html__( 'Border Radius', 'the7mk2' ),
				'type'      => Controls_Manager::DIMENSIONS,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .the7-clear-search' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_clear_colors' );

		$this->start_controls_tab(
			'tab_clear_normal',
			[
				'label' => esc_html__( 'Normal', 'the7mk2' ),
			]
		);
		$this->add_control(
			'clear_color',
			[
				'label'     => esc_html__( 'Icon Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .the7-clear-search' => '--clear-color: {{VALUE}}',
				],
				'condition' => [
					'clear_icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'clear_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => [
					'default' => Global_Colors::COLOR_SECONDARY,
				],
				'selectors' => [
					'{{WRAPPER}} .the7-clear-search' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'clear_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .the7-clear-search' => 'border-color: {{VALUE}}',

				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'           => 'clear_box_shadow',
				'selector'       => '{{WRAPPER}} .the7-clear-search',
				'fields_options' => [
					'box_shadow_type' => [
						'separator' => 'default',
					],
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_clear_hover',
			[
				'label' => esc_html__( 'Hover', 'the7mk2' ),
			]
		);
		$this->add_control(
			'clear_color_hover',
			[
				'label'     => esc_html__( 'Icon Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .the7-search-form .the7-clear-search:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .the7-search-form .the7-clear-search:hover svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
				'condition' => [
					'clear_icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'clear_background_color_hover',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .the7-clear-search:hover' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'clear_border_hover_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .the7-clear-search:hover' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'           => 'clear_box_shadow_hover',
				'selector'       => '{{WRAPPER}} .the7-clear-search:hover',
				'fields_options' => [
					'box_shadow_type' => [
						'separator' => 'default',
					],
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_clear_focus',
			[
				'label' => esc_html__( 'Focus', 'the7mk2' ),
			]
		);
		$this->add_control(
			'clear_color_focus',
			[
				'label'     => esc_html__( 'Icon Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}.the7-search-form-icon-position-inside .the7-search-form:focus-within .the7-clear-search:not(:hover), {{WRAPPER}}:not(.the7-search-form-icon-position-inside) .the7-search-input__container:focus-within .the7-clear-search:not(:hover)' => 'color: {{VALUE}}',
					'{{WRAPPER}}.the7-search-form-icon-position-inside .the7-search-form:focus-within .the7-clear-search:not(:hover) svg, {{WRAPPER}}:not(.the7-search-form-icon-position-inside) .the7-search-input__container:focus-within .the7-clear-search:not(:hover) svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
				'condition' => [
					'clear_icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'clear_background_color_focus',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .the7-search-form:focus-within .the7-clear-search:not(:hover)' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'clear_border_focus_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .the7-search-form:focus-within .the7-clear-search:not(:hover)' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'           => 'clear_box_shadow_focus',
				'selector'       => '{{WRAPPER}} .the7-search-form:focus-within .the7-clear-search:not(:hover)',
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
				'label'     => esc_html__( 'Search Icon', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'search_icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'skin',
			[
				'label'        => esc_html__( 'Position', 'the7mk2' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'inside',
				'options'      => [
					'inside'  => esc_html__( 'Inside input', 'the7mk2' ),
					'outside' => esc_html__( 'Outside input', 'the7mk2' ),
				],
				'prefix_class' => 'the7-search-form-icon-position-',
				'render_type'  => 'template',
			]
		);

		$this->add_responsive_control(
			'icon_position',
			[
				'label'                => esc_html__( 'Alignment', 'the7mk2' ),
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
				'device_args'          => $this->generate_device_args(
					[
						'toggle' => true,
					]
				),
				'selectors_dictionary' => [
					'left'  => 'order: 2; margin-left: var(--btn-space, 10px); margin-right: 0;',
					'right' => 'order: 0; margin-right: var(--btn-space, 10px); margin-left: 0;',
				],
				'selectors'            => [
					'{{WRAPPER}} .the7-search-input__container' => '{{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'btn_space',
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
					'{{WRAPPER}}' => '--btn-space: {{SIZE}}{{UNIT}}',
				],
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
					'{{WRAPPER}} .the7-search-form__submit' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'btn_border_radius',
			[
				'label'     => esc_html__( 'Border Radius', 'the7mk2' ),
				'type'      => Controls_Manager::DIMENSIONS,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .the7-search-form__submit' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
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

		$this->start_controls_tab(
			'tab_btn_focus',
			[
				'label' => esc_html__( 'Focus', 'the7mk2' ),
			]
		);
		$this->add_control(
			'button_text_color_focus',
			[
				'label'     => esc_html__( 'Icon Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .the7-search-form:focus-within .the7-search-form__submit:not(:hover)' => '--e-search-form-submit-text-color: {{VALUE}}',
				],
				'condition' => [
					'search_icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'button_background_color_focus',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .the7-search-form:focus-within .the7-search-form__submit:not(:hover)' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'btn_border_focus_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .the7-search-form:focus-within .the7-search-form__submit:not(:hover)' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'           => 'btn_box_shadow_focus',
				'selector'       => '{{WRAPPER}} .the7-search-form:focus-within .the7-search-form__submit:not(:hover)',
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
		$settings = $this->get_settings();
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
			<div class="the7-search-input__container">
				<input <?php $this->print_render_attribute_string( 'input' ); ?>>

				<?php if ( $settings['search_by'] ) : ?>
				<input type="hidden" name="post_type" value="<?php echo esc_attr( $settings['search_by'] ); ?>">
				<?php endif; ?>
				<?php if ( $settings['clear_icon']['value'] !== '' ) : ?>
					<button class="the7-clear-search" type="reset" title="<?php esc_attr_e( 'Search', 'the7mk2' ); ?>" aria-label="<?php esc_attr_e( 'Clear', 'the7mk2' ); ?>">

							<?php Icons_Manager::render_icon( $settings['clear_icon'], [ 'aria-hidden' => 'true' ] ); ?>
							<span class="elementor-screen-only"><?php esc_html_e( 'Clear', 'the7mk2' ); ?></span>

					</button>
				<?php endif; ?>
			</div>
				<?php if ( $settings['search_icon']['value'] !== '' ) : ?>
					<button class="the7-search-form__submit" type="submit" title="<?php esc_attr_e( 'Search', 'the7mk2' ); ?>" aria-label="<?php esc_attr_e( 'Search', 'the7mk2' ); ?>">

							<?php Icons_Manager::render_icon( $settings['search_icon'], [ 'aria-hidden' => 'true' ] ); ?>
							<span class="elementor-screen-only"><?php esc_html_e( 'Search', 'the7mk2' ); ?></span>

					</button>
				<?php endif; ?>

		</form>
		<?php
	}

}
