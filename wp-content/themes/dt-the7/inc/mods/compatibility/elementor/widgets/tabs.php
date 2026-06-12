<?php
/**
 * The7 tabs widget  for Elementor.
 *
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Core\Breakpoints\Manager as Breakpoints_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Plugin;
use Elementor\Repeater;
use The7\Mods\Compatibility\Elementor\Pro\Modules\Dynamic_Tags\The7\Module as TagsModule;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Tabs widget class.
 */
class Tabs extends The7_Elementor_Widget_Base {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'the7-tabs';
	}

	/**
	 * @return string[]
	 */
	public function get_style_depends() {
		return $this->getDepends();
	}

	/**
	 * @return string[]
	 */
	public function get_script_depends() {
		return $this->getDepends();
	}

	/**
	 * @return string[]
	 */
	private function getDepends() {
		// css and js use the same names.
		return [ 'the7-tabs-widget' ];
	}

	/**
	 * Register widget assets.
	 */
	protected function register_assets() {
		the7_register_script_in_footer(
			'the7-tabs-widget',
			THE7_ELEMENTOR_JS_URI . '/the7-tabs-widget.js',
			[ 'the7-elementor-frontend-common' ]
		);
	}

	/**
	 * @return string
	 */
	protected function the7_title() {
		return esc_html__( 'Tabs & Accordion', 'the7mk2' );
	}

	/**
	 * @return string
	 */
	protected function the7_icon() {
		return 'eicon-tabs';
	}

	/**
	 * @return string[]
	 */
	protected function the7_keywords() {
		return [ 'tabs', 'accordion', 'toggle' ];
	}

	/**
	 * @return void
	 */
	protected function register_controls() {
		// Content Tab.
		$this->add_repeater_controls();

		// Styles Tab.
		$this->add_title_styles();
		$this->add_icon_styles();
		$this->add_tab_styles();
		$this->add_accordion_styles();
		$this->add_tab_content_styles();
	}

	/**
	 * @return void
	 */
	protected function add_repeater_controls() {
		$this->start_controls_section(
			'section_tabs',
			[
				'label' => esc_html__( 'Tabs', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'tab_title',
			[
				'label'       => esc_html__( 'Title & Description', 'the7mk2' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Tab Title', 'the7mk2' ),
				'placeholder' => esc_html__( 'Tab Title', 'the7mk2' ),
				'label_block' => true,
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'tab_content',
			[
				'label'       => esc_html__( 'Content', 'the7mk2' ),
				'default'     => esc_html__( 'Tab Content', 'the7mk2' ),
				'placeholder' => esc_html__( 'Tab Content', 'the7mk2' ),
				'type'        => Controls_Manager::WYSIWYG,
				'dynamic'     => [
					'categories' => [
						TagsModule::TEXT_CATEGORY_WITH_TEMPLATE,
						TagsModule::TEXT_CATEGORY,
					],
				],
				'show_label'  => false,
			]
		);

		$repeater->add_control(
			'tab_icon',
			[
				'label'       => esc_html__( 'Icon', 'the7mk2' ),
				'type'        => Controls_Manager::ICONS,
				'separator'   => 'before',
				'default'     => [
					'value'   => '',
					'library' => '',
				],
				'skin'        => 'inline',
				'label_block' => false,
			]
		);

		$this->add_control(
			'tabs',
			[
				'label'       => esc_html__( 'Tabs Items', 'the7mk2' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[
						'tab_title'   => esc_html__( 'Tab #1', 'the7mk2' ),
						'tab_content' => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'the7mk2' ),
					],
					[
						'tab_title'   => esc_html__( 'Tab #2', 'the7mk2' ),
						'tab_content' => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'the7mk2' ),
					],
					[
						'tab_title'   => esc_html__( 'Tab #3', 'the7mk2' ),
						'tab_content' => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'the7mk2' ),
					],
				],
				'title_field' => '{{{ tab_title }}}',
			]
		);

		$this->add_control(
			'view_type',
			[
				'label'        => esc_html__( 'Tabs Position', 'the7mk2' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'horizontal',
				'options'      => [
					'horizontal' => esc_html__( 'Horizontal', 'the7mk2' ),
					'vertical'   => esc_html__( 'Vertical', 'the7mk2' ),
				],
				'prefix_class' => 'the7-e-tabs-view-',
				'separator'    => 'before',
			]
		);

		$devices       = Plugin::$instance->breakpoints->get_active_devices_list();
		$desktop_width = 1300;
		$options       = [];
		foreach ( $devices as $device ) {
			if ( $device === Breakpoints_Manager::BREAKPOINT_KEY_DESKTOP ) {
				$options[ Breakpoints_Manager::BREAKPOINT_KEY_DESKTOP ] = sprintf( 'Desktop (> %dpx)', $desktop_width );
				continue;
			}

			$breakpoint = Plugin::$instance->breakpoints->get_active_breakpoints( $device );
			if ( $breakpoint ) {
				$breakpoint_value = $breakpoint->get_value();
				if ( $device === Breakpoints_Manager::BREAKPOINT_KEY_WIDESCREEN ) {
					$options[ $device ] = sprintf( '%s (> %dpx)', $breakpoint->get_label(), $breakpoint_value );
				} else {
					$options[ $device ] = sprintf( '%s (< %dpx)', $breakpoint->get_label(), $breakpoint_value );
					$desktop_width      = $breakpoint_value;
				}
			}
		}
		$options['none'] = esc_html__( 'None', 'the7mk2' );

		$this->add_control(
			'accordion_breakpoint',
			[
				'label'              => esc_html__( 'Accordion Breakpoint', 'the7mk2' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => 'none',
				'options'            => $options,
				'frontend_available' => true,
				'render_type'        => 'template',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function add_title_styles() {
		$this->start_controls_section(
			'section_title_style',
			[
				'label' => esc_html__( 'Title', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$selector_title      = '{{WRAPPER}} .the7-e-tab-title';
		$selector_title_text = '{{WRAPPER}} .the7-e-tab-title > .the7-e-tab-title-text';

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'tab_header_typography',
				'selector' => $selector_title_text,
				'global'   => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
			]
		);

		$this->add_responsive_control(
			'title_min_height',
			[
				'label'     => esc_html__( 'Min Height', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 300,
					],
				],
				'selectors' => [
					$selector_title => 'min-height: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'title_padding',
			[
				'label'      => esc_html__( 'Paddings', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					$selector_title => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'title_border_width',
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
					$selector_title => 'border-top-width: {{TOP}}{{UNIT}};border-right-width: {{RIGHT}}{{UNIT}};border-bottom-width: {{BOTTOM}}{{UNIT}};border-left-width:{{LEFT}}{{UNIT}};',
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
					$selector_title => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'title_alignment',
			[
				'label'                => esc_html__( 'Alignment', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'description'          => esc_html__( 'Works for horizontal tabs and accordion layout only.', 'the7mk2' ),
				'default'              => 'left',
				'options'              => [
					'left'   => [
						'title' => esc_html__( 'Left', 'the7mk2' ),
						'icon'  => 'eicon-h-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'the7mk2' ),
						'icon'  => 'eicon-h-align-center',
					],
				],
				'selectors_dictionary' => [
					'left'   => 'justify-content: left;',
					'center' => 'justify-content: center; text-align: center;',
				],
				'selectors'            => [
					$selector_title => '{{VALUE}}',
					'{{WRAPPER}}.the7-e-tabs-view-vertical .item-divider' => '{{VALUE}}',
					'{{WRAPPER}}.the7-e-accordion .item-divider' => '{{VALUE}}',
				],
			]
		);

		$this->start_controls_tabs( 'title_tabs_style' );
		$this->add_tab_header_states_controls( 'normal_', esc_html__( 'Normal', 'the7mk2' ) );
		$this->add_tab_header_states_controls( 'hover_', esc_html__( 'Hover', 'the7mk2' ) );
		$this->add_tab_header_states_controls( 'active_', esc_html__( 'Active', 'the7mk2' ) );
		$this->end_controls_tabs();

		$this->add_divider_title_styles();

		$this->end_controls_section();
	}

	/**
	 * @param string $prefix_name Prefix.
	 * @param string $box_name Box.
	 *
	 * @return void
	 */
	protected function add_tab_header_states_controls( $prefix_name, $box_name ) {
		$extra_class = '';
		if ( strpos( $prefix_name, 'active_' ) === 0 ) {
			$extra_class .= '.active';
		}
		$is_hover = '';
		if ( strpos( $prefix_name, 'hover_' ) === 0 ) {
			$is_hover = ':hover';
		}

		$selector = '{{WRAPPER}} .the7-e-tab-title' . $extra_class . $is_hover;

		$this->start_controls_tab(
			$prefix_name . 'header_tab_style',
			[
				'label' => $box_name,
			]
		);

		$this->add_control(
			$prefix_name . 'tab_header_color',
			[
				'label'     => esc_html__( 'Text Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					$selector . ' > .the7-e-tab-title-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			$prefix_name . 'tab_header_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$selector => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			$prefix_name . 'tab_header_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$selector => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => $prefix_name . 'tab_header_shadow',
				'label'    => esc_html__( 'Box Shadow', 'the7mk2' ),
				'selector' => $selector,
			]
		);

		$this->end_controls_tab();
	}

	/**
	 * @return void
	 */
	protected function add_divider_title_styles() {
		$selector = '{{WRAPPER}} .item-divider';

		$this->add_control(
			'divider',
			[
				'label'        => esc_html__( 'Dividers', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_off'    => esc_html__( 'Off', 'the7mk2' ),
				'label_on'     => esc_html__( 'On', 'the7mk2' ),
				'return_value' => 'yes',
				'empty_value'  => 'no',
				'selectors'    => [
					$selector . ':not(:first-child):not(:last-child)' => 'display:flex;',
				],
				'prefix_class' => 'widget-divider-',
				'separator'    => 'before',
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
					'{{WRAPPER}}.the7-e-tabs-view-horizontal .the7-e-tabs-nav .item-divider:after' => 'border-left-style: {{VALUE}}',
					'{{WRAPPER}}.the7-e-tabs-view-vertical .the7-e-tabs-nav .item-divider:after' => 'border-top-style: {{VALUE}}',
					'{{WRAPPER}}.the7-e-accordion .the7-e-tabs-content .item-divider:after' => 'border-top-style: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'divider_thickness',
			[
				'label'     => esc_html__( 'Thickness', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 1,
				],
				'range'     => [
					'px' => [
						'min' => 1,
						'max' => 200,
					],
				],
				'condition' => [
					'divider' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}.the7-e-tabs-view-horizontal .the7-e-tabs-nav .item-divider:after' => 'border-left-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.the7-e-tabs-view-vertical .the7-e-tabs-nav .item-divider:after' => 'border-top-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.the7-e-accordion .the7-e-tabs-content .item-divider:after' => 'border-top-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'divider_length',
			[
				'label'      => esc_html__( 'Length', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min' => 1,
						'max' => 2000,
					],
				],
				'condition'  => [
					'divider' => 'yes',
				],
				'selectors'  => [
					'{{WRAPPER}}.the7-e-tabs-view-horizontal .the7-e-tabs-nav .item-divider:after' => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.the7-e-tabs-view-vertical .the7-e-tabs-nav .item-divider:after' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.the7-e-accordion .the7-e-tabs-content .item-divider:after' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'display_first_divider',
			[
				'label'        => esc_html__( 'First Divider', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the7mk2' ),
				'label_off'    => esc_html__( 'Hide', 'the7mk2' ),
				'condition'    => [
					'divider' => 'yes',
				],
				'return_value' => 'yes',
				'empty_value'  => 'no',
				'prefix_class' => 'widget-divider-first-',
				'selectors'    => [
					$selector . ':first-child' => 'display:flex',
				],
			]
		);

		$this->add_control(
			'display_last_divider',
			[
				'label'        => esc_html__( 'Last Divider', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the7mk2' ),
				'label_off'    => esc_html__( 'Hide', 'the7mk2' ),
				'condition'    => [
					'divider' => 'yes',
				],
				'return_value' => 'yes',
				'empty_value'  => 'no',
				'prefix_class' => 'widget-divider-last-',
				'selectors'    => [
					$selector . ':last-child' => 'display:flex',
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
					$selector . ':after' => 'border-color: {{VALUE}}',
				],
			]
		);
	}

	/**
	 * @return void
	 */
	protected function add_icon_styles() {
		$this->start_controls_section(
			'section_icon_title_style',
			[
				'label' => esc_html__( 'Title Icon', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'icon_title_align',
			[
				'label'                => esc_html__( 'Alignment', 'the7mk2' ),
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
				'selectors_dictionary' => [
					'top'   => 'flex-flow: column wrap; --icon-title-margin: 0 0 var(--icon-title-spacing) 0;',
					'left'  => 'flex-flow: row nowrap;  --icon-title-margin: 0 var(--icon-title-spacing) 0 0;',
					'right' => 'flex-flow: row-reverse nowrap; --icon-title-margin: 0 0 0 var(--icon-title-spacing);',
				],
				'selectors'            => [
					'{{WRAPPER}} .the7-e-tab-title' => '{{VALUE}}',
				],
				'default'              => 'left',
				'toggle'               => false,
			]
		);

		$selector = '{{WRAPPER}} .the7-e-tab-title > .elementor-icon';

		$this->add_responsive_control(
			'icon_title_size',
			[
				'label'     => esc_html__( 'Icon Size', 'the7mk2' ),
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
					$selector => 'font-size: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'icon_title_padding',
			[
				'label'      => esc_html__( 'Padding', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'    => [
					'size' => '',
				],
				'selectors'  => [
					$selector => 'padding: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_title_border_width',
			[
				'label'      => esc_html__( 'Border Width', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					$selector => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'icon_title_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					$selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'icon_title_tabs_style' );
		$this->add_icon_title_states_controls( 'normal_', esc_html__( 'Normal', 'the7mk2' ) );
		$this->add_icon_title_states_controls( 'hover_', esc_html__( 'Hover', 'the7mk2' ) );
		$this->add_icon_title_states_controls( 'active_', esc_html__( 'Active', 'the7mk2' ) );
		$this->end_controls_tabs();

		$this->add_responsive_control(
			'icon_title_space',
			[
				'label'     => esc_html__( 'Spacing', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .the7-e-tab-title' => '--icon-title-spacing: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @param string $prefix_name Prefix.
	 * @param string $box_name Box.
	 *
	 * @return void
	 */
	protected function add_icon_title_states_controls( $prefix_name, $box_name ) {
		$extra_class = '';
		if ( $prefix_name === 'active_' ) {
			$extra_class .= '.active';
		}
		$is_hover = '';
		if ( $prefix_name === 'hover_' ) {
			$is_hover = ':hover';
		}
		$selector = '{{WRAPPER}} .the7-e-tab-title' . $extra_class . $is_hover . ' > .elementor-icon';
		$this->start_controls_tab(
			$prefix_name . 'icon_title_tab_style',
			[
				'label' => $box_name,
			]
		);

		$this->add_control(
			$prefix_name . 'icon_title_color',
			[
				'label'     => esc_html__( 'Icon Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'alpha'     => true,
				'selectors' => [
					$selector          => 'color: {{VALUE}};',
					$selector . ' svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			$prefix_name . 'icon_title_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$selector => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			$prefix_name . 'icon_title_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					$selector => 'background: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
	}

	/**
	 * @return void
	 */
	protected function add_tab_styles() {
		$this->start_controls_section(
			'section_tabs_style',
			[
				'label' => esc_html__( 'Tabs', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$selector = '{{WRAPPER}} .the7-e-tabs-nav .the7-e-tab-title';

		$this->add_responsive_control(
			'tab_header_min_width',
			[
				'label'     => esc_html__( 'Min Width', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'unit' => 'px',
				],
				'range'     => [
					'px' => [
						'max' => 1000,
					],
				],
				'selectors' => [
					$selector => 'min-width: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'view_type' => 'horizontal',
				],
			]
		);

		$this->add_control(
			'tab_header_vertical_width',
			[
				'label'      => esc_html__( 'Width', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'max' => 1000,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .the7-e-tabs-nav-wrapper' => 'width: {{SIZE}}{{UNIT}}',
				],
				'condition'  => [
					'view_type' => 'vertical',
				],
			]
		);

		$this->add_responsive_control(
			'tab_header_gap',
			[
				'label'      => esc_html__( 'Gap Between Tabs', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'max' => 200,
					],
				],
				'selectors'  => [
					'{{WRAPPER}}.the7-e-tabs-view-horizontal .the7-e-tabs-nav .the7-e-tab-title:not(:first-child):not(:last-child)' =>
					'margin-left: calc({{SIZE}}{{UNIT}}/2); margin-right: calc({{SIZE}}{{UNIT}}/2);',
					'{{WRAPPER}}.the7-e-tabs-view-vertical .the7-e-tabs-nav .the7-e-tab-title:not(:first-child):not(:last-child)' =>
					'margin-top: calc({{SIZE}}{{UNIT}}/2); margin-bottom: calc({{SIZE}}{{UNIT}}/2);',
				],
			]
		);

		$this->add_control(
			'tab_header_items_position',
			[
				'label'        => esc_html__( 'Items position', 'the7mk2' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'flex-start',
				'options'      => [
					''              => esc_html__( 'Default', 'the7mk2' ),
					'flex-start'    => esc_html__( 'Start', 'the7mk2' ),
					'center'        => esc_html__( 'Center', 'the7mk2' ),
					'flex-end'      => esc_html__( 'End', 'the7mk2' ),
					'space-between' => esc_html__( 'Space Between', 'the7mk2' ),
					'space-around'  => esc_html__( 'Space Around', 'the7mk2' ),
					'space-evenly'  => esc_html__( 'Space Evenly', 'the7mk2' ),
					'fullwidth'     => esc_html__( 'Equal Width', 'the7mk2' ),
				],
				'prefix_class' => 'the7-e-tabs-nav-justify-',
				'selectors'    => [
					'{{WRAPPER}} .the7-e-tabs-nav' => 'justify-content: {{VALUE}}',
				],
				'condition'    => [
					'view_type' => 'horizontal',
				],
			]
		);

		$this->add_responsive_control(
			'tab_content_gap',
			[
				'label'      => esc_html__( 'Content Spacing', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'max' => 500,
					],
				],
				'selectors'  => [
					'{{WRAPPER}}.the7-e-tabs-view-horizontal:not(.the7-e-accordion) .the7-e-tabs-content .the7-e-tab-content' => 'margin-top: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.the7-e-tabs-view-vertical:not(.the7-e-accordion) .the7-e-tabs-wrapper' => 'column-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'tab_nowrap',
			[
				'label'     => esc_html__( 'NoWrap', 'the7mk2' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'Yes', 'the7mk2' ),
				'label_on'  => esc_html__( 'No', 'the7mk2' ),
				'selectors' => [
					$selector => 'white-space: nowrap;',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function add_accordion_styles() {
		$this->start_controls_section(
			'section_accordion_style',
			[
				'label'     => esc_html__( 'Accordion', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'accordion_breakpoint!' => 'none',
				],
			]
		);

		$this->add_responsive_control(
			'tab_accordion_gap',
			[
				'label'      => esc_html__( 'Gap Between Accordions', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'max' => 200,
					],
				],
				'selectors'  => [
					'{{WRAPPER}}.the7-e-accordion .the7-e-tabs-content .the7-e-tab-item-wrapper:not(:first-child):not(:last-child)' => 'margin-top: calc({{SIZE}}{{UNIT}}/2); margin-bottom: calc({{SIZE}}{{UNIT}}/2);',
				],
			]
		);

		$this->add_responsive_control(
			'tab_accordion_content_gap_top',
			[
				'label'      => esc_html__( 'Content Top spacing', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'max' => 200,
					],
				],
				'selectors'  => [
					'{{WRAPPER}}.the7-e-accordion .the7-e-tabs-content .the7-e-tab-content' => 'margin-top: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'tab_accordion_content_gap_bottom',
			[
				'label'      => esc_html__( 'Content Bottom Spacing', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'max' => 200,
					],
				],
				'selectors'  => [
					'{{WRAPPER}}.the7-e-accordion .the7-e-tabs-content .the7-e-tab-content' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function add_tab_content_styles() {
		$this->start_controls_section(
			'section_tabs_content_style',
			[
				'label' => esc_html__( 'Content', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$selector = '{{WRAPPER}} .the7-e-tabs-content .the7-e-tab-content';

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'tab_content_typography',
				'selector' => $selector,
				'global'   => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
			]
		);

		$this->add_responsive_control(
			'tab_content_padding',
			[
				'label'      => esc_html__( 'Paddings', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					$selector => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'tab_content_border_width',
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
					$selector => 'border-top-width: {{TOP}}{{UNIT}};border-right-width: {{RIGHT}}{{UNIT}};border-bottom-width: {{BOTTOM}}{{UNIT}};border-left-width:{{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'tab_content_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					$selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'tab_content_color',
			[
				'label'     => esc_html__( 'Text Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					$selector => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tab_content_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$selector => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tab_content_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$selector => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'tab_content_shadow',
				'label'    => esc_html__( 'Box Shadow', 'the7mk2' ),
				'selector' => $selector,
			]
		);

		$this->add_responsive_control(
			'tab_content_template_margin',
			[
				'label'      => esc_html__( 'The7 Template Margins', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .the7-e-tabs-content .the7-e-tab-content > .elementor-template' => 'margin-right: {{RIGHT}}{{UNIT}}; margin-left: {{LEFT}}{{UNIT}}; margin-bottom: {{BOTTOM}}{{UNIT}};  margin-top: {{TOP}}{{UNIT}};',
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

		$id_int = substr( $this->get_id_int(), 0, 3 );

		$this->add_render_attribute(
			'tabs-wrapper',
			[
				'class' => 'the7-e-tabs-wrapper',
			]
		);

		$this->add_render_attribute(
			'tabs-nav',
			[
				'class' => [ 'the7-e-tabs-nav' ],
				'role'  => 'tablist',
			]
		);

		$this->add_render_attribute(
			'tabs-content',
			[
				'class'            => [ 'the7-e-tabs-content' ],
				'aria-orientation' => 'vertical',
			]
		)
		?>
	<div <?php $this->print_render_attribute_string( 'tabs-wrapper' ); ?>>
		<div class="the7-e-tabs-nav-wrapper">
			<div class="the7-e-tabs-nav-scroll-wrapper">
				<div <?php $this->print_render_attribute_string( 'tabs-nav' ); ?>>
					<?php
					$tabs = $settings['tabs'];
					?>
					<span class="item-divider" aria-hidden="true"></span>
						<?php
						// Add first divider.
						foreach ( $tabs as $index => $item ) :
							$tab_count             = $index + 1;
							$tab_title_setting_key = $this->get_repeater_setting_key( 'tab_title', 'tabs', $index );

							$this->add_render_attribute(
								$tab_title_setting_key,
								[
									'id'            => 'the7-e-tab-title-' . $id_int . $tab_count,
									'class'         => [ 'the7-e-tab-title' ],
									'aria-selected' => 1 === $tab_count ? 'true' : 'false',
									'data-tab'      => $tab_count,
									'role'          => 'tab',
									'tabindex'      => 1 === $tab_count ? '0' : '-1',
									'aria-controls' => 'the7-e-tab-content-' . $id_int . $tab_count,
									'aria-expanded' => 'false',
								]
							);
							?>
						<div <?php $this->print_render_attribute_string( $tab_title_setting_key ); ?>>
							<?php $this->print_tab_icon( $item['tab_icon'] ); ?>
							<h5 class="the7-e-tab-title-text">
							<?php $this->print_unescaped_setting( 'tab_title', 'tabs', $index ); ?>
							</h5>
						</div>
						<span class="item-divider" aria-hidden="true"></span>
					<?php endforeach; ?>
				</div>
			</div>
			<div class="the7-e-tab-nav-button left-button" role="button" tabindex="0" aria-label="Prev tab"></div>
			<div class="the7-e-tab-nav-button right-button" role="button" tabindex="0" aria-label="Next tab"></div>
		</div>
		<div <?php $this->print_render_attribute_string( 'tabs-content' ); ?>>
			<span class="item-divider" aria-hidden="true"></span>
			<?php
			foreach ( $tabs as $index => $item ) :
				$tab_count               = $index + 1;
				$hidden                  = 1 === $tab_count ? 'false' : 'hidden';
				$tab_content_setting_key = $this->get_repeater_setting_key( 'tab_content', 'tabs', $index );

				$tab_title_mobile_setting_key = $this->get_repeater_setting_key( 'tab_title_mobile', 'tabs', $tab_count );

				$this->add_render_attribute(
					$tab_title_mobile_setting_key,
					[
						'class'         => [ 'the7-e-tab-title' ],
						'aria-selected' => 1 === $tab_count ? 'true' : 'false',
						'data-tab'      => $tab_count,
						'role'          => 'tab',
						'tabindex'      => 1 === $tab_count ? '0' : '-1',
						'aria-controls' => 'elementor-tab-content-' . $id_int . $tab_count,
						'aria-expanded' => 'false',
					]
				);

				$this->add_render_attribute(
					$tab_content_setting_key,
					[
						'id'              => 'the7-e-tab-content-' . $id_int . $tab_count,
						'class'           => [ 'the7-e-tab-content' ],
						'data-tab'        => $tab_count,
						'role'            => 'tabpanel',
						'aria-labelledby' => 'the7-e-tab-title-' . $id_int . $tab_count,
						'tabindex'        => '0',
						'hidden'          => $hidden,
					]
				);

				$this->add_render_attribute(
					'tab-item-wrapper',
					[
						'class'    => [ 'the7-e-tab-item-wrapper' ],
						'data-tab' => $tab_count,
					],
					null,
					true
				);

				$this->add_inline_editing_attributes( $tab_content_setting_key, 'advanced' );

				if ( ! isset( $item['__dynamic__'] ) ) {
					$this->add_render_attribute( $tab_content_setting_key, 'class', 'the7-e-tab-text-content' );
				}
				?>
				<div <?php $this->print_render_attribute_string( 'tab-item-wrapper' ); ?>>
					<div <?php $this->print_render_attribute_string( $tab_title_mobile_setting_key ); ?>>
						<?php $this->print_tab_icon( $item['tab_icon'] ); ?>
						<h5 class="the7-e-tab-title-text">
							<?php $this->print_unescaped_setting( 'tab_title', 'tabs', $index ); ?>
						</h5>
					</div>
					<div <?php $this->print_render_attribute_string( $tab_content_setting_key ); ?>><?php $this->print_text_editor( $item['tab_content'] ); ?></div>
				</div>
				<span class="item-divider" aria-hidden="true"></span>
			<?php endforeach; ?>
		</div>
	</div>
		<?php
	}

	/**
	 * @param array $icon_element Icon element.
	 *
	 * @return void
	 */
	protected function print_tab_icon( $icon_element ) {
		$has_tab_icon = empty( $icon_element['value'] ) ? '' : $icon_element['value'];
		if ( $has_tab_icon ) {
			?>
			<span class="the7-e-tab-icon elementor-icon" aria-hidden="true"><?php Icons_Manager::render_icon( $icon_element ); ?></span>
			<?php
		}
	}
}
