<?php
/**
 * The7 Taxonomies List widget for Elementor.
 *
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;
use The7\Mods\Compatibility\Elementor\Walkers\Custom_Taxonomy_List as Custom_Taxonomy_List_Walker;

defined( 'ABSPATH' ) || exit;

/**
 * Taxonomies class.
 */
class Taxonomies extends The7_Elementor_Widget_Base {

	/**
	 * Get element name.
	 */
	public function get_name() {
		return 'the7-taxonomies';
	}

	/**
	 * Get element title.
	 */
	protected function the7_title() {
		return esc_html__( 'Post Taxonomies', 'the7mk2' );
	}

	/**
	 * Get element icon.
	 */
	protected function the7_icon() {
		return 'eicon-navigation-horizontal';
	}

	/**
	 * Get element keywords.
	 *
	 * @return string[] Element keywords.
	 */
	protected function the7_keywords() {
		return [ 'categories', 'post', 'taxonomies' ];
	}

	/**
	 * @return string[]
	 */
	public function get_style_depends() {
		return [ $this->get_name() ];
	}

	/**
	 * Register assets.
	 */
	protected function register_assets() {
		the7_register_style( $this->get_name(), THE7_ELEMENTOR_CSS_URI . '/the7-taxonomies.css' );
	}

	/**
	 * Register controls.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_layout',
			[
				'label' => esc_html__( 'Layout', 'the7mk2' ),
			]
		);

		$this->add_control(
			'target_taxonomy',
			[
				'label'   => esc_html__( 'Taxonomy', 'the7mk2' ),
				'type'    => Controls_Manager::SELECT2,
				'default' => 'post',
				'options' => array_diff_key( get_taxonomies( [ 'public' => true ] ), array_flip( [ 'post_format' ] ) ),
				'classes' => 'select2-medium-width',
			]
		);

		$this->add_control(
			'use_queried_post',
			[
				'label'        => esc_html__( "Display Only Current Post's Terms", 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'the7mk2' ),
				'label_off'    => esc_html__( 'No', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
			]
		);
		$this->add_control(
			'taxonomy_links',
			[
				'label'        => esc_html__( 'Links', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'the7mk2' ),
				'label_off'    => esc_html__( 'No', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
				'render_type'  => 'template',
			]
		);

		$this->add_control(
			'taxonomy_label',
			[
				'label'        => esc_html__( 'Label', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the7mk2' ),
				'label_off'    => esc_html__( 'Hide', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
				'render_type'  => 'template',
				'selectors'    => [
					'{{WRAPPER}} .the7-taxonomies-row > span' => 'display: flex;',
				],
			]
		);

		$this->add_control(
			'taxonomy_label_text',
			[
				'label'       => esc_html__( 'Title', 'the7mk2' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [
					'active' => true,
				],
				'placeholder' => esc_html__( 'Enter your title', 'the7mk2' ),
				'label_block' => true,
				'condition'   => [
					'taxonomy_label' => 'y',
				],
			]
		);

		$this->end_controls_section();

		// Style.
		$this->add_taxonomy_style_controls();
	}

	/**
	 * Add taxonomy style controls.
	 */
	protected function add_taxonomy_style_controls() {
		$this->start_controls_section(
			'taxonomy_label_style',
			[
				'label'     => esc_html__( 'Label', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'taxonomy_label' => 'y',
				],
			]
		);

		$this->add_responsive_control(
			'taxonomy_label_position',
			[
				'label'                => esc_html__( 'Label Position', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'toggle'               => false,
				'default'              => 'left',
				'options'              => [
					'left' => [
						'title' => esc_html__( 'Left', 'the7mk2' ),
						'icon'  => 'eicon-h-align-left',
					],
					'top'  => [
						'title' => esc_html__( 'Top', 'the7mk2' ),
						'icon'  => 'eicon-v-align-top',
					],
				],
				'prefix_class'         => 'label-position-',
				'selectors_dictionary' => [
					'left' => $this->combine_to_css_vars_definition_string(
						[
							'variations-direction' => 'row',
							'variations-align'     => 'center',
							'variations-justify'   => 'var(--align-taxonomy-items)',
							'label-justify'        => 'flex-start',
							'label-margin'         => '0 var(--label-spacing, 10px) 0 0;',
						]
					),
					'top'  => $this->combine_to_css_vars_definition_string(
						[
							'variations-direction' => 'column',
							'variations-align'     => 'var(--align-taxonomy-items)',
							'variations-justify'   => 'center',
							'label-justify'        => 'var(--align-taxonomy-items)',
							'label-margin'         => '0 0 var(--label-spacing, 10px) 0;',
						]
					),
				],
				'selectors'            => [
					'{{WRAPPER}}' => '{{VALUE}}',
				],
				'condition'            => [
					'taxonomy_label' => 'y',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'taxonomy_label_typography',
				'selector'  => ' {{WRAPPER}} .the7-taxonomies-row > span',
				'condition' => [
					'taxonomy_label' => 'y',
				],
			]
		);

		$this->add_control(
			'taxonomy_label_bg_color',
			[
				'label'     => esc_html__( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .the7-taxonomies-row > span' => 'color: {{VALUE}};',
				],
				'condition' => [
					'taxonomy_label' => 'y',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'      => 'label_text_shadow',
				'selector'  => '{{WRAPPER}} .the7-taxonomies-row > span',
				'condition' => [
					'taxonomy_label' => 'y',
				],
			]
		);

		$this->add_responsive_control(
			'taxonomy_label_min_width',
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
					'{{WRAPPER}} .the7-taxonomies-row > span' => 'min-width: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'taxonomy_label' => 'y',
				],
			]
		);

		$this->add_responsive_control(
			'taxonomy_label_gap',
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
					'{{WRAPPER}}' => '--label-spacing: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'taxonomy_label' => 'y',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'taxonomy_style',
			[
				'label' => esc_html__( 'Taxonomies', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'taxonomy_align',
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
				'selectors_dictionary' => [
					'left'   => '--align-taxonomy-items: flex-start;',
					'center' => '--align-taxonomy-items: center;',
					'right'  => '--align-taxonomy-items: flex-end;',
				],
				'selectors'            => [
					'{{WRAPPER}}' => '{{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'taxonomy_column_space',
			[
				'label'     => esc_html__( 'Values gap', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}}'                  => '--grid-row-gap: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .the7-taxonomies' => 'grid-row-gap: {{SIZE}}{{UNIT}};',
				],
				'default'   => [
					'size' => 10,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'taxonomy_typography',
				'selector' => ' {{WRAPPER}} .the7-taxonomies li .cat-item-wrap',
				'fields_options' => [
					'font_size'   => [
						'selectors' => [
							'{{WRAPPER}}' => '--taxonomy_font_size: {{SIZE}}{{UNIT}}',
							'{{WRAPPER}} .the7-taxonomies li .cat-item-wrap' => 'font-size: {{SIZE}}{{UNIT}}',
						],
					],
					'line_height' => [
						'selectors' => [
							'{{SELECTOR}}' => '--taxonomy_line_height: {{SIZE}}{{UNIT}}',
							'{{WRAPPER}} .the7-taxonomies li .cat-item-wrap' => 'line-height: {{SIZE}}{{UNIT}}',
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'taxonomy_min_width',
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
					'{{WRAPPER}} .the7-taxonomies li .cat-item-wrap' => 'min-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'taxonomy_min_height',
			[
				'label'      => esc_html__( 'Min Height', 'the7mk2' ),
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
					'{{WRAPPER}} .the7-taxonomies li .cat-item-wrap' => 'min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'taxonomy_padding',
			[
				'label'      => esc_html__( 'Paddings', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'    => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .the7-taxonomies li .cat-item-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'taxonomy_border_width',
				'selector' => '{{WRAPPER}} .the7-taxonomies li .cat-item-wrap',
				'exclude'  => [ 'color' ],
			]
		);

		$this->add_responsive_control(
			'taxonomy_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .the7-taxonomies li .cat-item-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_taxonomy_style' );
		$this->add_taxonomy_tab_controls( 'normal_', esc_html__( 'Normal', 'the7mk2' ) );
		$this->add_taxonomy_tab_controls( 'hover_', esc_html__( 'Hover', 'the7mk2' ) );
		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_dividers',
			[
				'label' => esc_html__( 'Dividers', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'taxonomy_divider',
			[
				'label'        => esc_html__( 'Dividers', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_off'    => esc_html__( 'Off', 'the7mk2' ),
				'label_on'     => esc_html__( 'On', 'the7mk2' ),
				'return_value' => 'yes',
				'empty_value'  => 'no',
				'render_type'  => 'template',
				'prefix_class' => 'widget-divider-',
			]
		);

		$this->add_control(
			'taxonomy_divider_type',
			[
				'label'       => esc_html__( 'Separator Between', 'the7mk2' ),
				'type'        => Controls_Manager::CHOOSE,
				'options'     => [
					'divider' => [
						'title' => esc_html__( 'Divider', 'the7mk2' ),
						'icon'  => 'eicon-ellipsis-v',
					],
					'text'    => [
						'title' => esc_html__( 'Text', 'the7mk2' ),
						'icon'  => 'eicon-font',
					],
					'icon'    => [
						'title' => esc_html__( 'Icon', 'the7mk2' ),
						'icon'  => 'eicon-star',
					],
				],
				'default'     => 'divider',
				'render_type' => 'template',
				'condition'   => [
					'taxonomy_divider' => 'yes',
				],
			]
		);
		$this->add_control(
			'taxonomy_text_separator',
			[
				'label'     => esc_html__( 'Text', 'the7mk2' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '/',
				'selectors' => [
					'{{WRAPPER}} .the7-taxonomies li:before, {{WRAPPER}} .the7-taxonomies li:last-child:after' => 'content: "{{VALUE}}"',
				],
				'condition' => [
					'taxonomy_divider_type' => 'text',
					'taxonomy_divider'      => 'yes',
				],
			]
		);

		$this->add_control(
			'taxonomy_icon_separator',
			[
				'label'            => esc_html__( 'Icon', 'the7mk2' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default'          => [
					'value'   => 'fas fa-star',
					'library' => 'fa-solid',
				],
				'selectors'        => [
					'{{WRAPPER}} .the7-taxonomies' => '--first-divider-display: none; --last-divider-display: none;',
					'{{WRAPPER}} .the7-taxonomies li:before, {{WRAPPER}} .the7-taxonomies li:after' => 'display: none',
				],
				'condition'        => [
					'taxonomy_divider_type' => 'icon',
					'taxonomy_divider'      => 'yes',
				],
				'render_type'      => 'template',
			]
		);

		$this->add_control(
			'taxonomy_divider_style',
			[
				'label'     => esc_html__( 'Style', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'solid'  => esc_html__( 'Solid', 'the7mk2' ),
					'double' => esc_html__( 'Double', 'the7mk2' ),
					'dotted' => esc_html__( 'Dotted', 'the7mk2' ),
					'dashed' => esc_html__( 'Dashed', 'the7mk2' ),
				],
				'default'   => 'solid',
				'condition' => [
					'taxonomy_divider'      => 'yes',
					'taxonomy_divider_type' => 'divider',
				],
				'selectors' => [
					'{{WRAPPER}}.widget-divider-yes .the7-taxonomies li:before, {{WRAPPER}}.widget-divider-yes .the7-taxonomies li:after' => 'border-left-style: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'taxonomy_divider_weight',
			[
				'label'     => esc_html__( 'Width', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 1,
				],
				'range'     => [
					'px' => [
						'min' => 1,
						'max' => 20,
					],
				],
				'condition' => [
					'taxonomy_divider'      => 'yes',
					'taxonomy_divider_type' => 'divider',
				],
				'selectors' => [
					'{{WRAPPER}}.widget-divider-yes' => '--divider-width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'taxonomy_divider_height',
			[
				'label'     => esc_html__( 'Height', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 10,
				],
				'range'     => [
					'px' => [
						'min' => 1,
						'max' => 50,
					],
				],
				'condition' => [
					'taxonomy_divider'      => 'yes',
					'taxonomy_divider_type' => 'divider',
				],
				'selectors' => [
					'{{WRAPPER}} .the7-taxonomies' => '--divider-height: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'divider_size',
			[
				'label'      => esc_html__( 'Separator size', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'vw' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}}'                      => '--taxonomies-separator-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .the7-taxonomies li:before, {{WRAPPER}} .the7-taxonomies li:after, {{WRAPPER}} .the7-taxonomies li i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .the7-taxonomies svg' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'taxonomy_divider'       => 'yes',
					'taxonomy_divider_type!' => 'divider',
				],
			]
		);

		$this->add_control(
			'taxonomy_show_first_border',
			[
				'label'                => esc_html__( 'First Divider', 'the7mk2' ),
				'type'                 => Controls_Manager::SWITCHER,
				'return_value'         => 'y',
				'default'              => 'y',
				'label_on'             => esc_html__( 'Show', 'the7mk2' ),
				'label_off'            => esc_html__( 'Hide', 'the7mk2' ),
				'prefix_class'         => 'first-item-divider-',
				'render_type'          => 'template',
				'selectors_dictionary' => [
					'y' => '--first-divider-display: block;',
					''  => '--first-divider-display: none;',
				],
				'selectors'            => [
					'{{WRAPPER}}' => '{{VALUE}}',
				],
				'condition'            => [
					'taxonomy_divider' => 'yes',
				],
			]
		);

		$this->add_control(
			'taxonomy_show_last_border',
			[
				'label'                => esc_html__( 'Last Divider', 'the7mk2' ),
				'type'                 => Controls_Manager::SWITCHER,
				'return_value'         => 'y',
				'default'              => 'y',
				'label_on'             => esc_html__( 'Show', 'the7mk2' ),
				'label_off'            => esc_html__( 'Hide', 'the7mk2' ),
				'prefix_class'         => 'last-item-divider-',
				'render_type'          => 'template',
				'selectors_dictionary' => [
					'y' => '--last-divider-display: block;',
					''  => '--last-divider-display: none;',
				],
				'selectors'            => [
					'{{WRAPPER}}' => '{{VALUE}}',
				],
				'condition'            => [
					'taxonomy_divider' => 'yes',
				],
			]
		);

		$this->add_control(
			'taxonomy_divider_color',
			[
				'label'     => esc_html__( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'taxonomy_divider' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}.widget-divider-yes .the7-taxonomies li:before, {{WRAPPER}}.widget-divider-yes .the7-taxonomies li:after' => 'border-color: {{VALUE}}; color: {{VALUE}}',
					'{{WRAPPER}}.widget-divider-yes .the7-taxonomies li i, {{WRAPPER}}.widget-divider-yes .the7-taxonomies li svg' => 'color: {{VALUE}}; fill: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @param string $prefix_name Prefix name.
	 * @param string $box_name Box name.
	 *
	 * @return void
	 */
	protected function add_taxonomy_tab_controls( $prefix_name, $box_name ) {
		$css_prefix = 'li .cat-item-wrap';
		if ( $prefix_name === 'hover_' ) {
			$css_prefix = 'li .cat-item-wrap:hover';
		}

		$selector = '{{WRAPPER}} .the7-taxonomies ' . $css_prefix;

		$this->start_controls_tab(
			$prefix_name . 'taxonomy_count_style',
			[
				'label' => $box_name,
			]
		);

		$this->add_control(
			$prefix_name . 'taxonomy_color',
			[
				'label'     => esc_html__( 'Text  Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					$selector => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			$prefix_name . 'taxonomy_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$selector => 'background-color: {{VALUE}};',
				],
			]
		);

		$item_count_border_color_selectors = [
			$selector => 'border-color: {{VALUE}};',
		];

		if ( $prefix_name !== 'hover_' ) {
			$item_count_border_color_selectors['{{WRAPPER}} .the7-taxonomies'] = '--variations-border-color: {{VALUE}};';
		}

		$this->add_control(
			$prefix_name . 'taxonomy_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => $item_count_border_color_selectors,
				'condition' => [
					'taxonomy_border_width_border!' => [ '', 'none' ],

				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'     => $prefix_name . 'taxonomy_text_shadow',
				'selector' => $selector,
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => $prefix_name . 'taxonomy_shadow',
				'label'    => esc_html__( 'Box Shadow', 'the7mk2' ),
				'selector' => $selector,
			]
		);

		$this->end_controls_tab();
	}

	/**
	 * Render element.
	 *
	 * Generates the final HTML on the frontend.
	 */
	protected function render() {
		global $wp_query;

		$settings = $this->get_settings_for_display();
		$taxonomy = $settings['target_taxonomy'];
		if ( ! $taxonomy ) {
			echo esc_html__( 'No taxonomy chosen.', 'the7mk2' );
			return;
		}

		$cat_ancestors = [];
		$current_cat   = $wp_query->queried_object;
		if ( isset( $current_cat->term_id ) ) {
			$cat_ancestors = get_ancestors( $current_cat->term_id, $taxonomy );
		}

		$list_args = [
			'hierarchical'               => false,
			'taxonomy'                   => $taxonomy,
			// Inverted logic here. On purpose.
			'menu_order'                 => false,
			'echo'                       => false,
			'title_li'                   => '',
			'pad_counts'                 => 1,
			'show_option_none'           => '',
			'current_category'           => $current_cat && isset( $current_cat->term_id ) ? $current_cat->term_id : '',
			'current_category_ancestors' => $cat_ancestors,
			'walker'                     => new Custom_Taxonomy_List_Walker( $this, $taxonomy ),
		];

		if ( $settings['use_queried_post'] === 'y' ) {
			$list_args['object_ids'] = get_the_ID();
		}

		$taxonomies_html = wp_list_categories( $list_args );
		if ( ! $taxonomies_html ) {
			return;
		}

		echo '<div class="the7-taxonomies-row">';
		if ( $settings['taxonomy_label'] ) {
			echo '<span>' . esc_html( $this->get_tax_label( $taxonomy ) ) . '</span>';
		}
		echo '<ul class="the7-taxonomies">';
		echo $taxonomies_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</ul>';
		echo '</div>';
	}

	/**
	 * @param string $taxonomy Taxonomy.
	 *
	 * @return string
	 */
	protected function get_tax_label( $taxonomy ) {
		$taxonomy_label_text = $this->get_settings_for_display( 'taxonomy_label_text' ) ?: '';
		if ( ! $taxonomy_label_text ) {
			$tax_obj = get_taxonomy( $taxonomy );
			if ( $tax_obj ) {
				$tax_labels          = get_taxonomy_labels( $tax_obj );
				$taxonomy_label_text = $tax_labels->name;
			}
		}

		return $taxonomy_label_text;
	}
}
