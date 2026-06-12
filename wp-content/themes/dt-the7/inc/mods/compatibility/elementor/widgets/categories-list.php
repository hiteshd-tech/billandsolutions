<?php
/**
 * The7 Categories List widget for Elementor.
 *
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;
use The7\Mods\Compatibility\Elementor\Walkers\Custom_Taxonomy_List as Custom_Taxonomy_List_Walker;

defined( 'ABSPATH' ) || exit;

/**
 * Categories_List class.
 */
class Categories_List extends The7_Elementor_Widget_Base {


	/**
	 * Get element name.
	 */
	public function get_name() {
		return 'the7-categories-list';
	}

	/**
	 * Get element title.
	 */
	protected function the7_title() {
		return esc_html__( 'Categories List', 'the7mk2' );
	}

	/**
	 * Get element icon.
	 */
	protected function the7_icon() {
		return 'eicon-post-list';
	}

	/**
	 * Get script dependencies.
	 * Retrieve the list of script dependencies the element requires.
	 *
	 * @return array Element scripts dependencies.
	 */
	public function get_script_depends() {
		return [ 'the7-categories-handler' ];
	}

	/**
	 * @return string[]
	 */
	public function get_style_depends() {
		return [ $this->get_name(), 'the7-vertical-list-common' ];
	}

	/**
	 * Register assets.
	 */
	protected function register_assets() {
		the7_register_style( $this->get_name(), THE7_ELEMENTOR_CSS_URI . '/the7-categories-list.css' );
	}

	/**
	 * Get element keywords.
	 *
	 * @return string[] Element keywords.
	 */
	protected function the7_keywords() {
		return [ 'categories', 'post' ];
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
			'widget_title_text',
			[
				'label'   => esc_html__( 'Title', 'the7mk2' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Widget title',

			]
		);

		$this->add_control(
			'toggle',
			[
				'label'        => esc_html__( 'Widget Toggle', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'On', 'the7mk2' ),
				'label_off'    => esc_html__( 'Off', 'the7mk2' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
				'condition'    => [
					'widget_title_text!' => '',
				],
			]
		);

		$this->add_control(
			'toggle_closed_by_default',
			[
				'label'        => esc_html__( 'Closed By Default', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'the7mk2' ),
				'label_off'    => esc_html__( 'No', 'the7mk2' ),
				'return_value' => 'closed',
				'default'      => '',
				'condition'    => [
					'toggle!'            => '',
					'widget_title_text!' => '',
				],
				'render_type'  => 'template',
			]
		);

		$this->add_control(
			'toggle_icon',
			[
				'label'            => esc_html__( 'Icon', 'the7mk2' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default'          => [
					'value'   => 'fas fa-chevron-down',
					'library' => 'fa-solid',
				],
				'recommended'      => [
					'fa-solid'   => [
						'chevron-down',
						'angle-down',
						'angle-double-down',
						'caret-down',
						'caret-square-down',
					],
					'fa-regular' => [
						'caret-square-down',
					],
				],
				'label_block'      => false,
				'skin'             => 'inline',
				'condition'        => [
					'toggle!'            => '',
					'widget_title_text!' => '',
				],
			]
		);

		$this->add_control(
			'toggle_active_icon',
			[
				'label'            => esc_html__( 'Active Icon', 'the7mk2' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon_active',
				'default'          => [
					'value'   => 'fas fa-chevron-up',
					'library' => 'fa-solid',
				],
				'recommended'      => [
					'fa-solid'   => [
						'chevron-up',
						'angle-up',
						'angle-double-up',
						'caret-up',
						'caret-square-up',
					],
					'fa-regular' => [
						'caret-square-up',
					],
				],
				'skin'             => 'inline',
				'label_block'      => false,
				'condition'        => [
					'toggle!'             => '',
					'toggle_icon[value]!' => '',
					'widget_title_text!'  => '',
				],
				'separator'        => 'after',
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
			'submenu_display',
			[
				'label'              => esc_html__( 'Display the subcategories', 'the7mk2' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => 'on_click',
				'options'            => [
					'always'         => esc_html__( 'Standard', 'the7mk2' ),
					'all_categories' => esc_html__( 'All categories at once', 'the7mk2' ),
					'only_children'  => esc_html__( 'Only children of the category', 'the7mk2' ),
					'on_click'       => esc_html__( 'Drop down', 'the7mk2' ),
				],
				'frontend_available' => true,
				'render_type'        => 'template',
			]
		);

		$this->add_control(
			'show_hierarchical',
			[
				'label'        => esc_html__( 'Show hierarchy', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the7mk2' ),
				'label_off'    => esc_html__( 'Hide', 'the7mk2' ),
				'condition'    => [
					'submenu_display' => 'all_categories',
				],
				'return_value' => 'y',
				'default'      => 'y',
				'render_type'  => 'template',
			]
		);

		$this->add_control(
			'count',
			[
				'label'        => esc_html__( 'Category counts', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the7mk2' ),
				'label_off'    => esc_html__( 'Hide', 'the7mk2' ),
				'default'      => 'y',
				'return_value' => 'y',
			]
		);

		$this->add_control(
			'hide_empty',
			[
				'label'        => esc_html__( 'Empty categories', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the7mk2' ),
				'label_off'    => esc_html__( 'Hide', 'the7mk2' ),
				'default'      => 'y',
				'return_value' => 'y',
			]
		);

		$this->add_control(
			'max_depth',
			[
				'label'      => esc_html__( 'Maximum depth', 'the7mk2' ),
				'type'       => Controls_Manager::TEXT,
				'default'    => '',
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'terms' => [
								[
									'name'     => 'submenu_display',
									'operator' => '=',
									'value'    => 'all_categories',
								],
								[
									'name'     => 'show_hierarchical',
									'operator' => '=',
									'value'    => 'y',
								],
							],
						],
						[
							'terms' => [
								[
									'name'     => 'submenu_display',
									'operator' => '!=',
									'value'    => 'all_categories',
								],
							],
						],
					],
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_layout_icon',
			[
				'label'     => esc_html__( 'Subcategory Indicator Icons', 'the7mk2' ),
				'condition' => [
					'submenu_display' => 'on_click',
				],
			]
		);

		$this->add_control(
			'selected_icon',
			[
				'label'       => esc_html__( 'Icon', 'the7mk2' ),
				'type'        => Controls_Manager::ICONS,
				'default'     => [
					'value'   => 'fas fa-caret-right',
					'library' => 'fa-solid',
				],
				'skin'        => 'inline',
				'label_block' => false,
			]
		);

		$this->add_control(
			'selected_active_icon',
			[
				'label'       => esc_html__( 'Active icon', 'the7mk2' ),
				'type'        => Controls_Manager::ICONS,
				'default'     => [
					'value'   => 'fas fa-caret-down',
					'library' => 'fa-solid',
				],
				'skin'        => 'inline',
				'label_block' => false,
				'condition'   => [
					'selected_icon[value]!' => '',
				],
			]
		);

		$this->end_controls_section();

		// Style.
		$this->add_widget_title_style_controls();
		$this->add_box_attributes_styles();
		$this->start_controls_section(
			'section_style_main-menu',
			[
				'label' => esc_html__( 'Main categories', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,

			]
		);

		$this->add_control(
			'list_heading',
			[
				'label' => esc_html__( 'List', 'the7mk2' ),
				'type'  => \Elementor\Controls_Manager::HEADING,
			]
		);

		$this->add_responsive_control(
			'rows_gap',
			[
				'label'      => esc_html__( 'Rows Gap', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [
					'size' => '0',
				],
				'range'      => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .dt-categories-list > li:not(:last-child)' => 'padding-bottom: calc({{SIZE}}{{UNIT}}); margin-bottom: 0;',
					'{{WRAPPER}}.widget-divider-yes .dt-categories-list > li:first-child' => 'padding-top: calc({{SIZE}}{{UNIT}}/2);',

					'{{WRAPPER}}.widget-divider-yes .dt-categories-list > li:last-child' => 'padding-bottom: calc({{SIZE}}{{UNIT}}/2);',
					'{{WRAPPER}} .dt-categories-list' => ' --grid-row-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'divider',
			[
				'label'        => esc_html__( 'Dividers', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_off'    => esc_html__( 'Off', 'elementor' ),
				'label_on'     => esc_html__( 'On', 'elementor' ),
				'prefix_class' => 'widget-divider-',
			]
		);

		$this->add_control(
			'divider_style',
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
					'divider' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}.widget-divider-yes .dt-categories-list > li:after'                                  => 'border-bottom-style: {{VALUE}}',
					'{{WRAPPER}}.widget-divider-yes .dt-categories-list > li:first-child:before'                     => 'border-top-style: {{VALUE}};',
					'{{WRAPPER}} .first-item-border-hide.dt-categories-list > li:first-child:before'                 => ' border-top-style: none;',
					'{{WRAPPER}}.widget-divider-yes .first-item-border-hide.dt-categories-list > li:first-child'     => 'padding-top: 0;',
					'{{WRAPPER}}.widget-divider-yes .last-item-border-hide.dt-categories-list > li:last-child:after' => 'border-bottom-style: none;',
					'{{WRAPPER}}.widget-divider-yes .last-item-border-hide.dt-categories-list > li:last-child'       => 'padding-bottom: 0;',
				],
			]
		);

		$this->add_control(
			'divider_weight',
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
					'divider' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}.widget-divider-yes' => '--divider-width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'divider_color',
			[
				'label'     => esc_html__( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'divider' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}.widget-divider-yes .dt-categories-list > li:after, {{WRAPPER}}.widget-divider-yes .dt-categories-list > li:before' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'show_first_border',
			[
				'label'        => esc_html__( 'First Divider', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'y',
				'default'      => 'y',
				'label_on'     => esc_html__( 'Show', 'the7mk2' ),
				'label_off'    => esc_html__( 'Hide', 'the7mk2' ),
				'condition'    => [
					'divider' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_last_border',
			[
				'label'        => esc_html__( 'Last Divider', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'y',
				'default'      => 'y',
				'condition'    => [
					'divider' => 'yes',
				],
			]
		);

		$this->add_control(
			'items_heading',
			[
				'label'     => esc_html__( 'Item', 'the7mk2' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'align_items',
			[
				'label'                => esc_html__( 'Text alignment', 'the7mk2' ),
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
				'default'              => is_rtl() ? 'right' : 'left',
				'prefix_class'         => 'dt-categories-list_align-',
				'selectors_dictionary' => [
					'left'   => 'justify-content: flex-start; align-items: center; text-align: left; --justify-count: flex-start',
					'center' => 'justify-content: center; align-items: center; text-align: center; --justify-count: center;',
					'right'  => 'justify-content: flex-end;  align-items: flex-end; text-align: right; --justify-count: flex-end;',
				],
				'selectors'            => [
					'{{WRAPPER}} .dt-categories-list > li > a'                                                         => ' --justify-count: {{VALUE}};',
					'{{WRAPPER}} .dt-categories-list > li > a, {{WRAPPER}} .dt-categories-list > li > a .item-content' => ' {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'menu_typography',
				'separator' => 'before',
				'selector'  => ' {{WRAPPER}} .dt-categories-list > li > a > .item-content',
			]
		);

		$this->add_control(
			'icon_alignment',
			[
				'label'     => esc_html__( 'Indicator Align', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'with_text' => esc_html__( 'With text', 'the7mk2' ),
					'side'      => esc_html__( 'Side', 'the7mk2' ),
				],
				'default'   => 'with_text',
				'condition' => [
					'selected_icon[value]!' => '',
					'submenu_display'       => 'on_click',
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label'      => esc_html__( 'Indicator size', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'vw' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .dt-categories-list' => '--icon-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .dt-categories-list > li > a .next-level-button i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .dt-categories-list > li > a .next-level-button, {{WRAPPER}} .dt-categories-list > li > a .next-level-button svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'selected_icon[value]!' => '',
					'submenu_display'       => 'on_click',
				],
			]
		);

		$this->add_responsive_control(
			'icon_space',
			[
				'label'     => esc_html__( 'Indicator Spacing', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .dt-categories-list' => '--icon-spacing: {{SIZE}}{{UNIT}}',

					'{{WRAPPER}} .dt-categories-list > li > a  .next-level-button' => 'margin-left: {{SIZE}}{{UNIT}};',

					'{{WRAPPER}} .dt-icon-align-side .dt-categories-list > li > a .item-content ' => 'margin-right: {{SIZE}}{{UNIT}};',
					'(desktop) {{WRAPPER}}.dt-categories-list_align-center .dt-icon-align-side .dt-categories-list > li > a  .item-content ' => 'margin: 0 {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'selected_icon[value]!' => '',
					'submenu_display'       => 'on_click',
				],
			]
		);
		/* This control is required to handle with complicated conditions */
		$this->add_control(
			'hr',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$this->add_responsive_control(
			'border_menu_item_width',
			[
				'label'      => esc_html__( 'Border width', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default'    => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .dt-categories-list > li > a' => 'border-top-width: {{TOP}}{{UNIT}};
					border-right-width: {{RIGHT}}{{UNIT}}; border-bottom-width: {{BOTTOM}}{{UNIT}}; border-left-width:{{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'padding_menu_item',
			[
				'label'      => esc_html__( 'Item paddings', 'the7mk2' ),
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
					'{{WRAPPER}} .dt-categories-list > li > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'menu_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .dt-categories-list > li > a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'category_hr',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$this->start_controls_tabs( 'tabs_menu_item_style' );

		$this->start_controls_tab(
			'tab_menu_item_normal',
			[
				'label' => esc_html__( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_control(
			'color_menu_item',
			[
				'label'     => esc_html__( 'Text Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .dt-categories-list > li > a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'normal_item_count_color',
			[
				'label'     => esc_html__( 'Count Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .dt-categories-list > li > a .count' => 'color: {{VALUE}};',
				],
				'condition' => [
					'count' => 'y',
				],
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label'     => esc_html__( 'Indicator Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dt-categories-list > li > a .next-level-button' => 'color: {{VALUE}};',
					'{{WRAPPER}} .dt-categories-list > li > a svg'                => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
				'condition' => [
					'selected_icon[value]!' => '',
					'submenu_display'       => 'on_click',
				],
			]
		);

		$this->add_control(
			'bg_menu_item',
			[
				'label'     => esc_html__( 'Background color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .dt-categories-list > li > a' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'border_menu_item',
			[
				'label'     => esc_html__( 'Border color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .dt-categories-list > li > a' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_menu_item_hover',
			[
				'label' => esc_html__( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			'color_menu_item_hover',
			[
				'label'     => esc_html__( 'Text Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dt-categories-list > li > a:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'hover_item_count_color',
			[
				'label'     => esc_html__( 'Count Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .dt-categories-list > li > a:hover .count' => 'color: {{VALUE}};',
				],
				'condition' => [
					'count' => 'y',
				],
			]
		);
		$this->add_control(
			'icon_hover_color',
			[
				'label'     => esc_html__( 'Indicator Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dt-categories-list > li > a .next-level-button:hover '    => 'color: {{VALUE}};',
					'{{WRAPPER}} .dt-categories-list > li > a .next-level-button:hover svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
				'condition' => [
					'selected_icon[value]!' => '',
					'submenu_display'       => 'on_click',
				],
			]
		);

		$this->add_control(
			'bg_menu_item_hover',
			[
				'label'     => esc_html__( 'Background color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .dt-categories-list > li > a:hover' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'border_menu_item_hover',
			[
				'label'     => esc_html__( 'Border color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .dt-categories-list > li > a:hover' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_menu_item_active',
			[
				'label' => esc_html__( 'Active', 'the7mk2' ),
			]
		);

		$this->add_control(
			'color_menu_item_active',
			[
				'label'     => esc_html__( 'Text Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .dt-categories-list > li.current-cat > a' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'active_item_count_color',
			[
				'label'     => esc_html__( 'Count Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .dt-categories-list > li.current-cat > a .count' => 'color: {{VALUE}};',
				],
				'condition' => [
					'count' => 'y',
				],
			]
		);

		$this->add_control(
			'icon_active_color',
			[
				'label'     => esc_html__( 'Indicator Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dt-categories-list > li.current-cat > a .next-level-button' => 'color: {{VALUE}};',
					'{{WRAPPER}} .dt-categories-list > li.current-cat > a svg'                => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
				'condition' => [
					'selected_icon[value]!' => '',
					'submenu_display'       => 'on_click',
				],
			]
		);

		$this->add_control(
			'bg_menu_item_active',
			[
				'label'     => esc_html__( 'Background color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .dt-categories-list > li.current-cat > a' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'border_menu_item_active',
			[
				'label'     => esc_html__( 'Border color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .dt-categories-list > li.current-cat > a' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_sub-menu',
			[
				'label'      => esc_html__( 'Sub Categories', 'the7mk2' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'terms' => [
								[
									'name'     => 'submenu_display',
									'operator' => '=',
									'value'    => 'all_categories',
								],
								[
									'name'     => 'show_hierarchical',
									'operator' => '=',
									'value'    => 'y',
								],
							],
						],
						[
							'terms' => [
								[
									'name'     => 'submenu_display',
									'operator' => '!=',
									'value'    => 'all_categories',
								],
							],
						],
					],
				],
			]
		);

		$this->add_control(
			'sub_list_heading',
			[
				'label' => esc_html__( 'List', 'the7mk2' ),
				'type'  => \Elementor\Controls_Manager::HEADING,
			]
		);

		$this->add_responsive_control(
			'padding_sub_menu',
			[
				'label'      => esc_html__( '2 menu level Paddings', 'the7mk2' ),
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
					'{{WRAPPER}} .dt-categories-list > li > .children' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'padding_sub_sub_menu',
			[
				'label'      => esc_html__( '3+ menu level Paddings', 'the7mk2' ),
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
					'{{WRAPPER}} .children .children' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'sub_rows_gap',
			[
				'label'      => esc_html__( 'Rows Gap', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [
					'size' => '0',
				],
				'range'      => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .children > li:not(:last-child)'                   => 'padding-bottom: calc({{SIZE}}{{UNIT}}); margin-bottom: 0; --sub-grid-row-gap: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.sub-widget-divider-yes .children > li:first-child' => 'padding-top: calc({{SIZE}}{{UNIT}}/2);',
					'{{WRAPPER}} .children .children > li:first-child'              => 'margin-top: calc({{SIZE}}{{UNIT}}/2); padding-top: calc({{SIZE}}{{UNIT}}/2);',

					'{{WRAPPER}} .first-sub-item-border-hide.dt-categories-list > li > .children > li:first-child' => 'padding-top: 0;',

					'{{WRAPPER}}.sub-widget-divider-yes .children > li:last-child'                                                      => 'padding-bottom: calc({{SIZE}}{{UNIT}}/2);',
					'{{WRAPPER}} .children .children > li:last-child'                                                                   => 'margin-bottom: calc({{SIZE}}{{UNIT}}/2); padding-bottom: calc({{SIZE}}{{UNIT}}/2);',
					'{{WRAPPER}}.sub-widget-divider-yes .last-sub-item-border-hide.dt-categories-list > li > .children > li:last-child' => 'padding-bottom: 0;',
					'{{WRAPPER}} .dt-categories-list > li > .children .children'                                                        => 'margin-bottom: calc(-{{SIZE}}{{UNIT}});',
				],
			]
		);

		$this->add_control(
			'sub_divider',
			[
				'label'        => esc_html__( 'Dividers', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_off'    => esc_html__( 'Off', 'elementor' ),
				'label_on'     => esc_html__( 'On', 'elementor' ),
				'prefix_class' => 'sub-widget-divider-',
			]
		);

		$this->add_control(
			'sub_divider_style',
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
					'sub_divider' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}.sub-widget-divider-yes .children > li:after'              => 'border-bottom-style: {{VALUE}}',
					'{{WRAPPER}}.sub-widget-divider-yes .children > li:first-child:before' => 'border-top-style: {{VALUE}};',

					'{{WRAPPER}} .first-sub-item-border-hide .children > li:first-child:before' => ' border-top-style: none;',

					'{{WRAPPER}} .last-sub-item-border-hide .children > li:last-child:after, {{WRAPPER}} .last-sub-item-border-hide .children .children > li:last-child:after' => ' border-bottom-style: none;',
				],
			]
		);

		$this->add_control(
			'sub_divider_weight',
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
					'sub_divider' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}.sub-widget-divider-yes' => '--divider-sub-width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'sub_divider_color',
			[
				'label'     => esc_html__( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'sub_divider' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}.sub-widget-divider-yes .children > li:after, {{WRAPPER}}.sub-widget-divider-yes .children > li:before' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'show_sub_first_border',
			[
				'label'        => esc_html__( 'First Divider', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'y',
				'default'      => 'y',
				'label_on'     => esc_html__( 'Show', 'the7mk2' ),
				'label_off'    => esc_html__( 'Hide', 'the7mk2' ),
				'condition'    => [
					'sub_divider' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_sub_last_border',
			[
				'label'        => esc_html__( 'Last Divider', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'y',
				'default'      => 'y',
				'condition'    => [
					'sub_divider' => 'yes',
				],
			]
		);

		$this->add_control(
			'sub_item_heading',
			[
				'label'     => esc_html__( 'Item', 'the7mk2' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'align_sub_items',
			[
				'label'                => esc_html__( 'Text alignment', 'the7mk2' ),
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
				'default'              => is_rtl() ? 'right' : 'left',
				'prefix_class'         => 'dt-sub-category_align-',
				'selectors_dictionary' => [
					'left'   => 'justify-content: flex-start; align-items: center; text-align: left; --justify-count: flex-start',
					'center' => 'justify-content: center; align-items: center; text-align: center; --justify-count: center;',
					'right'  => 'justify-content: flex-end;  align-items: flex-end; text-align: right; --justify-count: flex-end;',
				],
				'selectors'            => [
					'{{WRAPPER}} .children > li' => ' --justify-count: {{VALUE}};',
					'{{WRAPPER}} .children > li > a .item-content' => ' {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'sub_menu_typography',
				'selector'  => '{{WRAPPER}} .children > li, {{WRAPPER}} .children > li a',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'sub_icon_alignment',
			[
				'label'     => esc_html__( 'Indicator Align', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'with_text' => esc_html__( 'With text', 'the7mk2' ),
					'side'      => esc_html__( 'Side', 'the7mk2' ),
				],
				'default'   => 'with_text',
				'condition' => [
					'selected_icon[value]!' => '',
					'submenu_display'       => 'on_click',
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'sub_icon_size',
			[
				'label'      => esc_html__( 'Indicator size', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'vw' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .children' => '--sub-icon-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .children > li > a .next-level-button i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .children > li > a .next-level-button, {{WRAPPER}} .children > li > a .next-level-button svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'selected_icon[value]!' => '',
					'submenu_display'       => 'on_click',
				],
			]
		);

		$this->add_responsive_control(
			'sub_icon_space',
			[
				'label'     => esc_html__( 'Indicator Spacing', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .children' => '--sub-icon-spacing: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .children > li > a  .next-level-button' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .dt-sub-icon-align-side .dt-categories-list > li > a .item-content ' => 'margin-right: {{SIZE}}{{UNIT}};',
					'(desktop) {{WRAPPER}}.dt-sub-category_align-center .dt-sub-icon-align-side .children > li > a  .item-content ' => 'margin: 0 {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'selected_icon[value]!' => '',
					'submenu_display'       => 'on_click',
				],
			]
		);
		/* This control is required to handle with complicated conditions */
		$this->add_control(
			'sub_hr',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$this->add_responsive_control(
			'border_sub_menu_item_width',
			[
				'label'      => esc_html__( 'Border width', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default'    => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .children li a' => 'border-top-width: {{TOP}}{{UNIT}};
					border-right-width: {{RIGHT}}{{UNIT}}; border-bottom-width: {{BOTTOM}}{{UNIT}}; border-left-width:{{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'padding_sub_menu_item',
			[
				'label'      => esc_html__( 'Item paddings', 'the7mk2' ),
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
					'{{WRAPPER}} .children li a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'menu_sub_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .children li a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'subcategory_hr',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$this->start_controls_tabs( 'tabs_sub_menu_item_style' );

		$this->start_controls_tab(
			'tab_sub_menu_item_normal',
			[
				'label' => esc_html__( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_control(
			'color_sub_menu_item',
			[
				'label'     => esc_html__( 'Text Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .children li a' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'normal_sub_item_count_color',
			[
				'label'     => esc_html__( 'Count Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .children .cat-item a .count' => 'color: {{VALUE}};',
				],
				'condition' => [
					'count' => 'y',
				],
			]
		);

		$this->add_control(
			'sub_menu_icon_color',
			[
				'label'     => esc_html__( 'Indicator Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .children > li > a .next-level-button' => 'color: {{VALUE}};',
					'{{WRAPPER}} .children > li > a svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
				'condition' => [
					'selected_icon[value]!' => '',
					'submenu_display'       => 'on_click',
				],
			]
		);

		$this->add_control(
			'bg_sub_menu_item',
			[
				'label'     => esc_html__( 'Background color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .children a' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'border_sub_menu_item',
			[
				'label'     => esc_html__( 'Border color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .children a' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_sub_menu_item_hover',
			[
				'label' => esc_html__( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			'color_sub_menu_item_hover',
			[
				'label'     => esc_html__( 'Text Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .children li a:hover' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'hover_sub_item_count_color',
			[
				'label'     => esc_html__( 'Count Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .children .cat-item a:hover .count' => 'color: {{VALUE}};',
				],
				'condition' => [
					'count' => 'y',
				],
			]
		);

		$this->add_control(
			'sub_menu_icon_hover_color',
			[
				'label'     => esc_html__( 'Indicator Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dt-sub-menu-display-on_click .children > li > a .next-level-button:hover, {{WRAPPER}} .dt-sub-menu-display-on_item_click .children > li > a:hover .next-level-button'         => 'color: {{VALUE}};',
					'{{WRAPPER}} .dt-sub-menu-display-on_click .children > li > a .next-level-button:hover svg, {{WRAPPER}} .dt-sub-menu-display-on_item_click .children > li > a:hover .next-level-button svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
				'condition' => [
					'selected_icon[value]!' => '',
					'submenu_display'       => 'on_click',
				],
			]
		);

		$this->add_control(
			'bg_sub_menu_item_hover',
			[
				'label'     => esc_html__( 'Background color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .children li a:hover' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'border_sub_menu_item_hover',
			[
				'label'     => esc_html__( 'Border color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .children li a:hover' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_sub_menu_item_active',
			[
				'label' => esc_html__( 'Active', 'the7mk2' ),
			]
		);

		$this->add_control(
			'color_sub_menu_item_active',
			[
				'label'     => esc_html__( 'Text Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .children li.current-cat > a' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'active_sub_item_count_color',
			[
				'label'     => esc_html__( 'Count  Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .children .cat-item.current-cat a .count' => 'color: {{VALUE}};',
				],
				'condition' => [
					'count' => 'y',
				],
			]
		);

		$this->add_control(
			'sub_menu_icon_active_color',
			[
				'label'     => esc_html__( 'Indicator Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .children li.current-cat > a .next-level-button' => 'color: {{VALUE}};',
					'{{WRAPPER}} .children li.current-cat > a svg'                => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
				'condition' => [
					'selected_icon[value]!' => '',
					'submenu_display'       => 'on_click',
				],
			]
		);

		$this->add_control(
			'bg_sub_menu_item_active',
			[
				'label'     => esc_html__( 'Background color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .children li.current-cat > a' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'border_sub_menu_item_active',
			[
				'label'     => esc_html__( 'Border color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .children li.current-cat > a' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function add_widget_title_style_controls() {
		$this->start_controls_section(
			'widget_style_section',
			[
				'label'     => esc_html__( 'Widget Title Area', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'widget_title_text!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'widget_title_typography',
				'selector' => '{{WRAPPER}} .filter-title',
			]
		);

		$this->add_responsive_control(
			'title_arrow_size',
			[
				'label'     => esc_html__( 'Toggle Icon Size', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'   => [
					'size' => 16,
				],
				'selectors' => [
					'{{WRAPPER}} .filter-toggle-icon .elementor-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'toggle!' => '',
				],
			]
		);

		$selector = '{{WRAPPER}} .filter-header';

		$this->add_responsive_control(
			'title_padding',
			[
				'label'      => esc_html__( 'Padding', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					$selector => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'title_margin',
			[
				'label'      => esc_html__( 'Margins', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					$selector => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'title_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					$selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'title_border',
				'label'    => esc_html__( 'Border', 'the7mk2' ),
				'selector' => $selector,
				'exclude'  => [ 'color' ],
			]
		);

		$this->start_controls_tabs(
			'title_arrow_tabs_style'
		);

		$this->start_controls_tab(
			'normal_title_arrow_style',
			[
				'label' => esc_html__( 'Closed', 'the7mk2' ),
			]
		);
		$this->add_control(
			'widget_title_color',
			[
				'label'     => esc_html__( 'Title Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .filter-header .filter-title' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'title_arrow_color',
			[
				'label'     => esc_html__( 'Icon Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .filter-header .filter-toggle-icon .filter-toggle-closed i'   => 'color: {{VALUE}};',
					'{{WRAPPER}} .filter-header .filter-toggle-icon .filter-toggle-closed svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
				'condition' => [
					'toggle!'             => '',
					'toggle_icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'title_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .filter-header' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .filter-header' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'hover_title_arrow_style',
			[
				'label' => esc_html__( 'Hover', 'the7mk2' ),
			]
		);
		$this->add_control(
			'hover_title_color',
			[
				'label'     => esc_html__( 'Title Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}}:not(.fix) .filter-header:hover .filter-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'hover_title_arrow_color',
			[
				'label'     => esc_html__( 'Icon Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .filter-header:hover .filter-toggle-icon .elementor-icon i'   => 'color: {{VALUE}};',
					'{{WRAPPER}} .filter-header:hover .filter-toggle-icon .elementor-icon svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
				'condition' => [
					'toggle!'             => '',
					'toggle_icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'hover_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}:not(.fix) .filter-header:hover' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'hover_title_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}}:not(.fix) .filter-header:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'active_title_arrow_style',
			[
				'label' => esc_html__( 'Active', 'the7mk2' ),
			]
		);

		$this->add_control(
			'active_title_color',
			[
				'label'     => esc_html__( 'Title Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}}:not(.closed) .filter-header .filter-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'active_title_arrow_color',
			[
				'label'     => esc_html__( 'Icon Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .filter-header .filter-toggle-icon .filter-toggle-active i'   => 'color: {{VALUE}};',
					'{{WRAPPER}} .filter-header .filter-toggle-icon .filter-toggle-active svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
				'condition' => [
					'toggle!'             => '',
					'toggle_icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'active_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}:not(.closed) .filter-header' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'active_title_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}}:not(.closed) .filter-header' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function add_box_attributes_styles() {
		$this->start_controls_section(
			'container_section',
			[
				'label' => esc_html__( 'Widget Content Area', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$selector = '#the7-body .elementor-element.elementor-element-{{ID}} .dt-categories-list';

		$this->add_responsive_control(
			'container_padding',
			[
				'label'      => esc_html__( 'Padding', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'    => [
					'top'      => '0',
					'right'    => '0',
					'bottom'   => '0',
					'left'     => '0',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					$selector => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'container_margin',
			[
				'label'      => esc_html__( 'Margins', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'    => [
					'top'      => '15',
					'right'    => '0',
					'bottom'   => '0',
					'left'     => '0',
					'unit'     => 'px',
					'isLinked' => false,
				],
				'selectors'  => [
					$selector => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'container_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					$selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'container_border',
				'label'    => esc_html__( 'Border', 'the7mk2' ),
				'selector' => $selector,
			]
		);

		$this->add_control(
			'container_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$selector => 'background: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render element.
	 * Generates the final HTML on the frontend.
	 */
	protected function render() {
		global $wp_query;

		$settings           = $this->get_settings_for_display();
		$hierarchical       = ( $settings['show_hierarchical'] === '' && $settings['submenu_display'] === 'all_categories' ) || $settings['submenu_display'] === 'only_children' ? false : true;
		$show_children_only = $settings['submenu_display'] === 'only_children';
		$taxonomy           = $settings['target_taxonomy'];
		if ( ! $taxonomy ) {
			echo esc_html__( 'No taxonomy chosen.', 'the7mk2' );

			return;
		}

		$this->add_render_attribute( 'categories-list', 'class', 'the7-categories-list-wrap' );

		if ( $settings['toggle'] === 'yes' ) {
			$this->add_render_attribute( 'categories-list', 'class', 'collapsible' );
			$this->add_render_attribute( '_wrapper', 'class', $settings['toggle_closed_by_default'] );
			if ( $settings['toggle_closed_by_default'] ) {
				$this->add_render_attribute( 'dt-categories-list', 'style', 'display:none' );
			}
		}

		$max_depth     = isset( $settings['max_depth'] ) ? absint( $settings['max_depth'] ) : 0;
		$cat_ancestors = [];
		$current_cat   = $wp_query->queried_object;
		if ( isset( $current_cat->term_id ) ) {
			$cat_ancestors = get_ancestors( $current_cat->term_id, $taxonomy );
		}

		$list_args = [
			'show_count'                 => isset( $settings['count'] ) ? $settings['count'] : '',
			'hierarchical'               => $hierarchical,
			'taxonomy'                   => $taxonomy,
			// Inverted logic here. On purpose.
			'hide_empty'                 => $settings['hide_empty'] !== 'y',
			'menu_order'                 => false,
			'depth'                      => $max_depth,
			'max_depth'                  => $max_depth,
			'echo'                       => false,
			'title_li'                   => '',
			'pad_counts'                 => 1,
			'show_option_none'           => '',
			'current_category'           => $current_cat && isset( $current_cat->term_id ) ? $current_cat->term_id : '',
			'current_category_ancestors' => $cat_ancestors,
			'walker'                     => new Custom_Taxonomy_List_Walker( $this, $taxonomy ),
		];

		if ( $show_children_only && isset( $current_cat->term_id ) ) {
			if ( $hierarchical ) {
				$include = array_merge(
					$cat_ancestors,
					[ $current_cat->term_id ],
					get_terms(
						$taxonomy,
						[
							'fields'       => 'ids',
							'parent'       => 0,
							'hierarchical' => true,
							'hide_empty'   => false,
						]
					),
					get_terms(
						$taxonomy,
						[
							'fields'       => 'ids',
							'parent'       => $current_cat->term_id,
							'hierarchical' => true,
							'hide_empty'   => false,
						]
					)
				);
				// Gather siblings of ancestors.
				if ( $cat_ancestors ) {
					foreach ( $cat_ancestors as $ancestor ) {
						$include = array_merge(
							$include,
							get_terms(
								$taxonomy,
								[
									'fields'       => 'ids',
									'parent'       => $ancestor,
									'hierarchical' => false,
									'hide_empty'   => false,
								]
							)
						);
					}
				}
			} else {
				// Direct children.
				$include = get_terms(
					$taxonomy,
					[
						'fields'       => 'ids',
						'parent'       => $current_cat->term_id,
						'hierarchical' => true,
						'hide_empty'   => false,
					]
				);
			}

			$list_args['include'] = implode( ',', $include );

			if ( empty( $include ) ) {
				return;
			}
		} elseif ( $show_children_only ) {
			$list_args['depth']        = 1;
			$list_args['child_of']     = 0;
			$list_args['hierarchical'] = true;
		}

		$categories_html = wp_list_categories( $list_args );
		if ( ! $categories_html ) {
			return;
		}

		echo '<div ' . $this->get_render_attribute_string( 'categories-list' ) . '>';

		// Widget title.
		if ( $settings['widget_title_text'] || ! empty( $settings['toggle_icon']['value'] ) ) {
			$this->add_render_attribute( 'filter-title', 'class', 'filter-title' );
			if ( empty( $settings['widget_title_text'] ) ) {
				$this->add_render_attribute( 'filter-title', 'class', 'empty' );
			}

			echo '<div class="filter-header widget-title">';
			echo '<div ' . $this->get_render_attribute_string( 'filter-title' ) . '>';
			echo esc_html( $settings['widget_title_text'] );
			echo '</div>';
			if ( ! empty( $settings['toggle_icon']['value'] ) ) {
				echo '<div class="filter-toggle-icon">';
				echo '<span class="elementor-icon filter-toggle-closed">';
				Icons_Manager::render_icon( $settings['toggle_icon'] );
				echo '</span>';
				if ( ! empty( $settings['toggle_active_icon']['value'] ) ) {
					echo '<span class="elementor-icon filter-toggle-active">';
					Icons_Manager::render_icon( $settings['toggle_active_icon'] );
					echo '</span>';
				}
				echo '</div>';
			}
			echo '</div>';
		}

		$class     = [
			'the7-vertical-list',
			'dt-categories-list',
			'dt-sub-menu-display-' . $settings['submenu_display'],
			'dt-icon-align-' . $settings['icon_alignment'],
			'dt-sub-icon-align-' . $settings['sub_icon_alignment'],
		];
		$switchers = [
			'show_first_border'     => 'first-item-border-hide',
			'show_last_border'      => 'last-item-border-hide',
			'show_sub_first_border' => 'first-sub-item-border-hide',
			'show_sub_last_border'  => 'last-sub-item-border-hide',
		];
		foreach ( $switchers as $control => $class_to_add ) {
			if ( isset( $settings[ $control ] ) && $settings[ $control ] !== 'y' ) {
				$class[] = $class_to_add;
			}
		}
		$this->add_render_attribute( 'dt-categories-list', 'class', $class );

		echo '<ul ' . $this->get_render_attribute_string( 'dt-categories-list' ) . '>';
		echo $categories_html;
		echo '</ul>';
		echo '</div>';
	}
}
