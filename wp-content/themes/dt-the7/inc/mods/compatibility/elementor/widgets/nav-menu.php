<?php
/**
 * The7 'Vertical Menu' widget for Elementor.
 *
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Utils;
use The7\Mods\Compatibility\Elementor\Modules\Mega_Menu\Mega_Menu;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Nav_Menu class.
 */
class Nav_Menu extends The7_Elementor_Widget_Base {

	/**
	 * Get element name.
	 */
	public function get_name() {
		return 'the7_nav-menu';
	}

	/**
	 * Get element title.
	 */
	protected function the7_title() {
		return esc_html__( 'Vertical Menu', 'the7mk2' );
	}

	/**
	 * Get element icon.
	 */
	protected function the7_icon() {
		return 'eicon-nav-menu';
	}

	/**
	 * Get script dependencies.
	 *
	 * Retrieve the list of script dependencies the element requires.
	 *
	 * @return array Element scripts dependencies.
	 */
	public function get_script_depends() {
		return [ $this->get_name() ];
	}

	/**
	 * Register widget assets.
	 */
	protected function register_assets() {
		the7_register_script_in_footer(
			$this->get_name(),
			THE7_ELEMENTOR_JS_URI . '/the7-vertical-menu.js',
			[ 'jquery' ]
		);
	}

	/**
	 * Get style dependencies.
	 *
	 * Retrieve the list of style dependencies the element requires.
	 *
	 * @return array Element styles dependencies.
	 */
	public function get_style_depends() {
		return [ 'the7-vertical-menu-widget' ];
	}

	/**
	 * Get element keywords.
	 *
	 * @return string[] Element keywords.
	 */
	protected function the7_keywords() {
		return [ 'nav', 'menu' ];
	}

	/**
	 * Define what element data to export.
	 *
	 * @param array $element Element data.
	 *
	 * @return array Element data.
	 */
	public function on_export( $element ) {
		unset( $element['settings']['menu'] );

		return $element;
	}

	/**
	 * Get available menus list.
	 *
	 * @return array List of menus.
	 */
	private function get_available_menus() {
		$menus = wp_get_nav_menus();

		$options = [];

		foreach ( $menus as $menu ) {
			$options[ $menu->slug ] = $menu->name;
		}

		return $options;
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

		$menus = $this->get_available_menus();

		if ( ! empty( $menus ) ) {
			$this->add_control(
				'menu',
				[
					'label'          => esc_html__( 'Menu', 'the7mk2' ),
					'type'           => Controls_Manager::SELECT,
					'options'        => $menus,
					'default'        => array_keys( $menus )[0],
					'save_default'   => true,
					'desctiption'    => sprintf(
					/* translators: 1: Link open tag, 2: Link closing tag. */
						esc_html__( 'Go to the %1$sMenus screen%2$s to manage your menus.', 'the7mk2' ),
						sprintf( '<a href="%s" target="_blank">', admin_url( 'nav-menus.php' ) ),
						'</a>'
					),
					'style_transfer' => false,
				]
			);
		} else {
			$this->add_control(
				'menu',
				[
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => '<strong>' . esc_html__( 'There are no menus in your site.', 'the7mk2' ) . '</strong><br>' .
	                     sprintf(
							 /* translators: 1: Link open tag, 2: Link closing tag. */
		                     esc_html__( 'Go to the %1$sMenus screen%2$s to create one.', 'the7mk2' ),
		                     sprintf( '<a href="%s" target="_blank">', admin_url( 'nav-menus.php?action=edit&menu=0' ) ),
		                     '</a>'
	                     ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				]
			);
		}
		$icon_visible_options            = [
			'enable'  => esc_html__( 'Show', 'the7mk2' ),
			'disable' => esc_html__( 'Hide', 'the7mk2' ),
		];
		$icon_visible_options_on_devices = [ '' => esc_html__( 'Default', 'the7mk2' ) ] + $icon_visible_options;

		$this->add_responsive_control(
			'icons_visible',
			[
				'label'                => esc_html__( 'Menu icons', 'the7mk2' ),
				'type'                 => Controls_Manager::SELECT,
				'options'              => $icon_visible_options,
				'device_args'          => [
					'tablet' => [
						'default' => '',
						'options' => $icon_visible_options_on_devices,
					],
					'mobile' => [
						'default' => '',
						'options' => $icon_visible_options_on_devices,
					],
				],
				'selectors_dictionary' => [
					'enable'  => 'flex',
					'disable' => 'none',
				],
				'default'              => 'enable',
				'selectors'            => [
					'{{WRAPPER}} li > a .item-content > i, {{WRAPPER}} li > a .item-content > img, {{WRAPPER}} li > a .item-content > svg' => 'display:{{VALUE}};',
				],
			]
		);

		$this->add_control(
			'submenu_display',
			[
				'label'              => esc_html__( 'Display the submenu', 'the7mk2' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => 'always',
				'options'            => [
					'always'        => esc_html__( 'Always', 'the7mk2' ),
					'on_click'      => esc_html__( 'On icon click (parent clickable)', 'the7mk2' ),
					'on_item_click' => esc_html__( 'On item click (parent unclickable)', 'the7mk2' ),
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'show_widget_title',
			[
				'label'        => esc_html__( 'Widget Title', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the7mk2' ),
				'label_off'    => esc_html__( 'Hide', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => '',
			]
		);

		$this->add_control(
			'widget_title_text',
			[
				'label'     => esc_html__( 'Title', 'the7mk2' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => 'Widget title',
				'condition' => [
					'show_widget_title' => 'y',
				],
			]
		);

		$this->add_control(
			'widget_title_tag',
			[
				'label'     => esc_html__( 'Title HTML Tag', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
				],
				'default'   => 'h3',
				'condition' => [
					'show_widget_title' => 'y',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_layout_icon',
			[
				'label'     => esc_html__( 'Submenu Indicator Icons', 'the7mk2' ),
				'condition' => [
					'submenu_display!' => 'always',
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

		$this->start_controls_section(
			'section_style_main-menu',
			[
				'label' => esc_html__( 'Main Menu', 'the7mk2' ),
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

		$this->add_basic_responsive_control(
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
					'{{WRAPPER}} .dt-nav-menu > li:not(:last-child)' => 'padding-bottom: calc({{SIZE}}{{UNIT}}); margin-bottom: 0;',
					'{{WRAPPER}}.widget-divider-yes .dt-nav-menu > li:first-child' => 'padding-top: calc({{SIZE}}{{UNIT}}/2);',

					'{{WRAPPER}}.widget-divider-yes .dt-nav-menu > li:last-child' => 'padding-bottom: calc({{SIZE}}{{UNIT}}/2);',
					'{{WRAPPER}} .dt-nav-menu' => ' --grid-row-gap: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}}.widget-divider-yes .dt-nav-menu > li:after' => 'border-bottom-style: {{VALUE}}',
					'{{WRAPPER}}.widget-divider-yes .dt-nav-menu > li:first-child:before' => 'border-top-style: {{VALUE}};',
					'{{WRAPPER}} .first-item-border-hide .dt-nav-menu > li:first-child:before' => ' border-top-style: none;',
					'{{WRAPPER}}.widget-divider-yes .first-item-border-hide .dt-nav-menu > li:first-child' => 'padding-top: 0;',
					'{{WRAPPER}}.widget-divider-yes .last-item-border-hide .dt-nav-menu > li:last-child:after' => 'border-bottom-style: none;',
					'{{WRAPPER}}.widget-divider-yes .last-item-border-hide .dt-nav-menu > li:last-child' => 'padding-bottom: 0;',
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
					'{{WRAPPER}}.widget-divider-yes .dt-nav-menu > li:after, {{WRAPPER}}.widget-divider-yes .dt-nav-menu > li:before' => 'border-color: {{VALUE}}',
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

		$this->add_basic_responsive_control(
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
				'prefix_class'         => 'dt-nav-menu_align%s-',
				'selectors_dictionary' => [
					'left'   => 'justify-content: flex-start; align-items: flex-start; text-align: left; --menu-position: flex-start;',
					'center' => 'justify-content: center; align-items: center; text-align: center; --menu-position: center;',
					'right'  => 'justify-content: flex-end;  align-items: flex-end; text-align: right; --menu-position: flex-end;',
				],
				'selectors'            => [
					'{{WRAPPER}} .dt-nav-menu > li > a' => ' {{VALUE}};',
					'{{WRAPPER}}.dt-nav-menu_align-center .dt-icon-align-side .dt-nav-menu > li > a .item-content' => 'padding: 0 var(--icon-size);',
					'(desktop) {{WRAPPER}}.dt-nav-menu_align-left .dt-icon-position-left.dt-icon-align-side .dt-nav-menu > li > a .item-content' => 'margin: 0 0 0 var(--icon-spacing); padding: 0 0 0 var(--icon-size)',
					'(desktop) {{WRAPPER}}.dt-nav-menu_align-right .dt-icon-position-left.dt-icon-align-side .dt-nav-menu > li > a .item-content' => 'margin: 0 0 0 var(--icon-spacing); padding: 0 0 0 var(--icon-size)',

					'(desktop) {{WRAPPER}}.dt-nav-menu_align-left .dt-icon-position-right.dt-icon-align-side .dt-nav-menu > li > a .item-content' => 'margin: 0 var(--icon-spacing) 0 0; padding: 0 var(--icon-size) 0 0',
					'(desktop) {{WRAPPER}}.dt-nav-menu_align-right .dt-icon-position-right.dt-icon-align-side .dt-nav-menu > li > a .item-content' => 'margin: 0 var(--icon-spacing) 0 0; padding: 0 var(--icon-size) 0 0',

					'(tablet) {{WRAPPER}}.dt-nav-menu_align-tablet-left .dt-icon-position-left.dt-icon-align-side .dt-nav-menu > li > a .item-content' => 'margin: 0 0 0 var(--icon-spacing); padding: 0 0 0 var(--icon-size)',
					'(tablet) {{WRAPPER}}.dt-nav-menu_align-tablet-right .dt-icon-position-left.dt-icon-align-side .dt-nav-menu > li > a .item-content' => 'margin: 0 0 0 var(--icon-spacing); padding: 0 0 0 var(--icon-size)',

					'(tablet) {{WRAPPER}}.dt-nav-menu_align-tablet-left .dt-icon-position-right.dt-icon-align-side .dt-nav-menu > li > a .item-content' => 'margin: 0 var(--icon-spacing) 0 0; padding: 0 var(--icon-size) 0 0',
					'(tablet) {{WRAPPER}}.dt-nav-menu_align-tablet-right .dt-icon-position-right.dt-icon-align-side .dt-nav-menu > li > a .item-content' => 'margin: 0 var(--icon-spacing) 0 0; padding: 0 var(--icon-size) 0 0',

					'(tablet) {{WRAPPER}}.dt-nav-menu_align-tablet-center .dt-icon-align-side .dt-nav-menu > li > a .item-content ' => 'margin: 0 var(--icon-spacing); padding: 0 var(--icon-size)',

					'(mobile) {{WRAPPER}}.dt-nav-menu_align-mobile-left .dt-icon-position-left.dt-icon-align-side .dt-nav-menu > li > a .item-content' => 'margin: 0 0 0 var(--icon-spacing); padding: 0 0 0 var(--icon-size)',
					'(mobile) {{WRAPPER}}.dt-nav-menu_align-mobile-right .dt-icon-position-left.dt-icon-align-side .dt-nav-menu > li > a .item-content' => 'margin: 0 0 0 var(--icon-spacing); padding: 0 0 0 var(--icon-size)',

					'(mobile) {{WRAPPER}}.dt-nav-menu_align-mobile-left .dt-icon-position-right.dt-icon-align-side .dt-nav-menu > li > a .item-content' => 'margin: 0 var(--icon-spacing) 0 0; padding: 0 var(--icon-size) 0 0',
					'(mobile) {{WRAPPER}}.dt-nav-menu_align-mobile-right .dt-icon-position-right.dt-icon-align-side .dt-nav-menu > li > a .item-content' => 'margin: 0 var(--icon-spacing) 0 0; padding: 0 var(--icon-size) 0 0',

					'(mobile) {{WRAPPER}}.dt-nav-menu_align-mobile-center .dt-icon-align-side.dt-icon-position-right .dt-nav-menu > li > a .item-content ' => 'margin: 0 var(--icon-spacing); padding: 0 var(--icon-size)',
					'(mobile) {{WRAPPER}}.dt-nav-menu_align-mobile-center .dt-icon-align-side.dt-icon-position-left .dt-nav-menu > li > a .item-content ' => 'margin: 0 var(--icon-spacing); padding: 0 var(--icon-size)',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'menu_typography',
				'separator' => 'before',
				'selector'  => ' {{WRAPPER}} .dt-nav-menu > li > a',
			]
		);

		$this->add_basic_responsive_control(
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
					'{{WRAPPER}} .dt-nav-menu > li > a' => 'border-top-width: {{TOP}}{{UNIT}};
					border-right-width: {{RIGHT}}{{UNIT}}; border-bottom-width: {{BOTTOM}}{{UNIT}}; border-left-width:{{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_basic_responsive_control(
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
					'{{WRAPPER}} .dt-nav-menu > li > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .dt-icon-position-left.dt-icon-align-side .dt-nav-menu > li > a .next-level-button ' => 'left: {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .dt-icon-position-right.dt-icon-align-side .dt-nav-menu > li > a .next-level-button ' => 'right: {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_basic_responsive_control(
			'menu_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .dt-nav-menu > li > a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],

			]
		);

		$this->add_control(
			'main_menu_colors_heading',
			[
				'label'     => esc_html__( 'Colors', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
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
					'{{WRAPPER}} .dt-nav-menu > li > a' => 'color: {{VALUE}}',
					'{{WRAPPER}} .dt-nav-menu > li > a .item-content svg' => 'fill: {{VALUE}}; color: {{VALUE}}',
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
					'{{WRAPPER}} .dt-nav-menu > li > a' => 'background-color: {{VALUE}}',
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
					'{{WRAPPER}} .dt-nav-menu > li > a' => 'border-color: {{VALUE}}',
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
					'{{WRAPPER}} .dt-nav-menu > li > a:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .dt-nav-menu > li > a:hover .item-content svg' => 'fill: {{VALUE}}; color: {{VALUE}}',
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
					'{{WRAPPER}} .dt-nav-menu > li > a:hover' => 'background-color: {{VALUE}}',
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
					'{{WRAPPER}} .dt-nav-menu > li > a:hover' => 'border-color: {{VALUE}}',
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
					'{{WRAPPER}} .dt-nav-menu > li > a.active, {{WRAPPER}} .dt-nav-menu > li > a.active-item' => 'color: {{VALUE}}',
					'{{WRAPPER}} .dt-nav-menu > li > a.active .item-content svg, {{WRAPPER}} .dt-nav-menu > li > a.active-item .item-content svg' => 'fill: {{VALUE}}; color: {{VALUE}}',
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
					'{{WRAPPER}} .dt-nav-menu > li > a.active, {{WRAPPER}} .dt-nav-menu > li > a.active-item' => 'background-color: {{VALUE}}',
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
					'{{WRAPPER}} .dt-nav-menu > li > a.active, {{WRAPPER}} .dt-nav-menu > li > a.active-item' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->add_icon_styles( 0, esc_html__( 'Main Menu Icons', 'the7mk2' ) );
		$this->start_controls_section(
			'section_style_indicator-menu',
			[
				'label'     => esc_html__( 'Main menu Indicators', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'submenu_display!' => 'always',
					'selected_icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'icon_align',
			[
				'label'     => esc_html__( 'Indicator Position', 'the7mk2' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'  => [
						'title' => esc_html__( 'Start', 'the7mk2' ),
						'icon'  => 'eicon-h-align-left',
					],
					'right' => [
						'title' => esc_html__( 'End', 'the7mk2' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'default'   => is_rtl() ? 'left' : 'right',
				'toggle'    => false,
			]
		);

		$this->add_control(
			'icon_alignment',
			[
				'label'                => esc_html__( 'Indicator Align', 'the7mk2' ),
				'type'                 => Controls_Manager::SELECT,
				'options'              => [
					'with_text' => esc_html__( 'With text', 'the7mk2' ),
					'side'      => esc_html__( 'Side', 'the7mk2' ),
				],
				'default'              => 'with_text',
				'selectors_dictionary' => [
					'with_text' => '',
					'side'      => 'justify-content: space-between;',
				],
			]
		);

		$this->add_basic_responsive_control(
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
					'{{WRAPPER}} .dt-nav-menu' => '--icon-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .dt-nav-menu > li > a .next-level-button i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .dt-nav-menu > li > a .next-level-button, {{WRAPPER}} .dt-nav-menu > li > a .next-level-button svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_basic_responsive_control(
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
					'{{WRAPPER}} .dt-nav-menu' => '--icon-spacing: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .dt-icon-position-left .dt-nav-menu > li > a .next-level-button' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .dt-icon-position-right .dt-nav-menu > li > a  .next-level-button' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .dt-icon-position-left.dt-icon-align-side .dt-nav-menu > li > a .item-content ' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .dt-icon-position-right.dt-icon-align-side .dt-nav-menu > li > a .item-content ' => 'margin-right: {{SIZE}}{{UNIT}};',
					'(desktop) {{WRAPPER}}.dt-nav-menu_align-center .dt-icon-align-side .dt-nav-menu > li > a  .item-content ' => 'margin: 0 {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'indicator_colors_heading',
			[
				'label'     => esc_html__( 'Colors', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->start_controls_tabs( 'tabs_indicator_style' );

		$this->start_controls_tab(
			'tab_indicator_normal',
			[
				'label' => esc_html__( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label'     => esc_html__( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dt-nav-menu > li > a .next-level-button' => 'color: {{VALUE}};',
					'{{WRAPPER}} .dt-nav-menu > li > a .next-level-button svg'                => 'fill: {{VALUE}}; color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_indicator_hover',
			[
				'label' => esc_html__( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			'icon_hover_color',
			[
				'label'     => esc_html__( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dt-sub-menu-display-on_click .dt-nav-menu > li > a .next-level-button:hover ' => 'color: {{VALUE}};',
					'
					{{WRAPPER}} .dt-sub-menu-display-on_item_click .dt-nav-menu > li > a:hover .next-level-button' => 'color: {{VALUE}};',
					'{{WRAPPER}} .dt-sub-menu-display-on_click .dt-nav-menu > li > a .next-level-button:hover svg, {{WRAPPER}} .dt-sub-menu-display-on_item_click .dt-nav-menu > li > a:hover .next-level-button svg' => 'fill: {{VALUE}}; color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_indicator_active',
			[
				'label' => esc_html__( 'Active', 'the7mk2' ),
			]
		);

		$this->add_control(
			'icon_active_color',
			[
				'label'     => esc_html__( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}  .dt-nav-menu > li > .active .next-level-button, {{WRAPPER}}  .dt-nav-menu > li > .active-item .next-level-button' => 'color: {{VALUE}};',
					'{{WRAPPER}} .dt-nav-menu > li > .active .next-level-button svg, {{WRAPPER}} .dt-nav-menu > li > .active-item .next-level-button svg' => 'fill: {{VALUE}}; color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_sub-menu',
			[
				'label' => esc_html__( 'Sub Menu', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'sub_list_heading',
			[
				'label' => esc_html__( 'List', 'the7mk2' ),
				'type'  => \Elementor\Controls_Manager::HEADING,
			]
		);

		$this->add_basic_responsive_control(
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
					'{{WRAPPER}} .dt-nav-menu > li > .vertical-sub-nav' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_basic_responsive_control(
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
					'{{WRAPPER}} .vertical-sub-nav .vertical-sub-nav' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_basic_responsive_control(
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
					'{{WRAPPER}} .vertical-sub-nav > li:not(:last-child)' => 'padding-bottom: calc({{SIZE}}{{UNIT}}); margin-bottom: 0; --sub-grid-row-gap: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.sub-widget-divider-yes .vertical-sub-nav > li:first-child' => 'padding-top: calc({{SIZE}}{{UNIT}}/2);',
					'{{WRAPPER}} .vertical-sub-nav .vertical-sub-nav > li:first-child' => 'margin-top: calc({{SIZE}}{{UNIT}}/2); padding-top: calc({{SIZE}}{{UNIT}}/2);',

					'{{WRAPPER}} .first-sub-item-border-hide .dt-nav-menu > li > .vertical-sub-nav > li:first-child' => 'padding-top: 0;',

					'{{WRAPPER}}.sub-widget-divider-yes .vertical-sub-nav > li:last-child' => 'padding-bottom: calc({{SIZE}}{{UNIT}}/2);',
					'{{WRAPPER}} .vertical-sub-nav .vertical-sub-nav > li:last-child' => 'margin-bottom: calc({{SIZE}}{{UNIT}}/2); padding-bottom: calc({{SIZE}}{{UNIT}}/2);',
					'{{WRAPPER}}.sub-widget-divider-yes .last-sub-item-border-hide .dt-nav-menu > li > .vertical-sub-nav > li:last-child' => 'padding-bottom: 0;',
					'{{WRAPPER}} .dt-nav-menu > li > .vertical-sub-nav .vertical-sub-nav' => 'margin-bottom: calc(-{{SIZE}}{{UNIT}});',
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
					'{{WRAPPER}}.sub-widget-divider-yes .vertical-sub-nav > li:after' => 'border-bottom-style: {{VALUE}}',
					'{{WRAPPER}}.sub-widget-divider-yes .vertical-sub-nav > li:first-child:before' => 'border-top-style: {{VALUE}};',

					'{{WRAPPER}} .first-sub-item-border-hide .dt-nav-menu > li > .vertical-sub-nav > li:first-child:before' => ' border-top-style: none;',

					'{{WRAPPER}} .last-sub-item-border-hide .vertical-sub-nav > li:last-child:after, {{WRAPPER}} .vertical-sub-nav .vertical-sub-nav > li:last-child:after' => ' border-bottom-style: none;',
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
					'{{WRAPPER}}.sub-widget-divider-yes .vertical-sub-nav > li:after, {{WRAPPER}}.sub-widget-divider-yes .vertical-sub-nav > li:before' => 'border-color: {{VALUE}}',
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

		$this->add_basic_responsive_control(
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
				'prefix_class'         => 'dt-sub-menu_align%s-',
				'selectors_dictionary' => [
					'left'   => '--sub-justify-content: flex-start; --sub-align-items: flex-start; --sub-text-align: left; --sub-menu-position: flex-start;',
					'center' => '--sub-justify-content: center; --sub-align-items: center; --sub-text-align: center; --sub-menu-position: center;',
					'right'  => '--sub-justify-content: flex-end;  --sub-align-items: flex-end; --sub-text-align: right; --sub-menu-position: flex-end;',
				],
				'selectors'            => [
					'{{WRAPPER}} .vertical-sub-nav' => ' {{VALUE}};',

					'(tablet) {{WRAPPER}}.dt-sub-menu_align-tablet-left .dt-sub-icon-position-left.dt-sub-icon-align-side .vertical-sub-nav > li .item-content' => 'margin: 0 0 0 var(--sub-icon-spacing); padding: 0 0 0 var(--sub-icon-size)',
					'(tablet) {{WRAPPER}}.dt-sub-menu_align-tablet-right .dt-sub-icon-position-left.dt-sub-icon-align-side .vertical-sub-nav > li .item-content' => 'margin: 0 0 0 var(--sub-icon-spacing); padding: 0 0 0 var(--sub-icon-size)',

					'(tablet) {{WRAPPER}}.dt-sub-menu_align-tablet-left .dt-sub-icon-position-right.dt-sub-icon-align-side .vertical-sub-nav > li .item-content' => 'margin: 0 var(--sub-icon-spacing) 0 0; padding: 0 var(--sub-icon-size) 0 0',
					'(tablet) {{WRAPPER}}.dt-sub-menu_align-tablet-right .dt-sub-icon-position-right.dt-sub-icon-align-side .vertical-sub-nav > li .item-content' => 'margin: 0 var(--sub-icon-spacing) 0 0; padding: 0 var(--sub-icon-size) 0 0',

					'(tablet) {{WRAPPER}}.dt-sub-menu_align-tablet-center .dt-sub-icon-align-side .vertical-sub-nav > li .item-content ' => 'margin: 0 var(--icon-spacing); padding: 0 var(--sub-icon-size)',

					'(mobile) {{WRAPPER}}.dt-sub-menu_align-mobile-left .dt-sub-icon-position-left.dt-sub-icon-align-side .vertical-sub-nav > li .item-content' => 'margin: 0 0 0 var(--sub-icon-spacing); padding: 0 0 0 var(--sub-icon-size)',
					'(mobile) {{WRAPPER}}.dt-sub-menu_align-mobile-right .dt-sub-icon-position-left.dt-sub-icon-align-side .vertical-sub-nav > li .item-content' => 'margin: 0 0 0 var(--sub-icon-spacing); padding: 0 0 0 var(--sub-icon-size)',

					'(mobile) {{WRAPPER}}.dt-sub-menu_align-mobile-left .dt-sub-icon-position-right.dt-sub-icon-align-side .vertical-sub-nav > li .item-content' => 'margin: 0 var(--sub-icon-spacing) 0 0; padding: 0 var(--sub-icon-size) 0 0',
					'(mobile) {{WRAPPER}}.dt-sub-menu_align-mobile-right .dt-sub-icon-position-right.dt-sub-icon-align-side .vertical-sub-nav > li .item-content' => 'margin: 0 var(--sub-icon-spacing) 0 0; padding: 0 var(--sub-icon-size) 0 0',

					'(mobile) {{WRAPPER}}.dt-sub-menu_align-mobile-center .dt-sub-icon-align-side.dt-sub-icon-position-right .vertical-sub-nav > li .item-content ' => 'margin: 0 var(--sub-icon-spacing); padding: 0 var(--sub-icon-size)',
					'(mobile) {{WRAPPER}}.dt-sub-menu_align-mobile-center .dt-sub-icon-align-side.dt-sub-icon-position-left .vertical-sub-nav > li .item-content ' => 'margin: 0 var(--sub-icon-spacing); padding: 0 var(--sub-icon-size)',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'sub_menu_typography',
				'selector'  => '{{WRAPPER}} .vertical-sub-nav > li, {{WRAPPER}} .vertical-sub-nav > li a',
				'separator' => 'before',
			]
		);

		/* This control is required to handle with complicated conditions */
		$this->add_control(
			'sub_hr',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$this->add_basic_responsive_control(
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
					'{{WRAPPER}} .vertical-sub-nav li a' => 'border-top-width: {{TOP}}{{UNIT}};
					border-right-width: {{RIGHT}}{{UNIT}}; border-bottom-width: {{BOTTOM}}{{UNIT}}; border-left-width:{{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_basic_responsive_control(
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
					'{{WRAPPER}} .vertical-sub-nav li a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .dt-sub-icon-position-left.dt-sub-icon-align-side .vertical-sub-nav li a .next-level-button ' => 'left: {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .dt-sub-icon-position-right.dt-sub-icon-align-side .vertical-sub-nav li a .next-level-button ' => 'right: {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_basic_responsive_control(
			'menu_sub_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .vertical-sub-nav li a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'sub_menu_colors_heading',
			[
				'label'     => esc_html__( 'Colors', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
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
					'{{WRAPPER}} .vertical-sub-nav li a' => 'color: {{VALUE}}',
					'{{WRAPPER}} .vertical-sub-nav li a .item-content svg' => 'fill: {{VALUE}}; color: {{VALUE}}',
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
					'{{WRAPPER}} .vertical-sub-nav a' => 'background-color: {{VALUE}}',
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
					'{{WRAPPER}} .vertical-sub-nav a' => 'border-color: {{VALUE}}',
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
					'{{WRAPPER}} .vertical-sub-nav li a:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .vertical-sub-nav li a:hover .item-content svg' => 'fill: {{VALUE}}; color: {{VALUE}}',
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
					'{{WRAPPER}} .vertical-sub-nav li a:hover' => 'background-color: {{VALUE}}',
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
					'{{WRAPPER}} .vertical-sub-nav li a:hover' => 'border-color: {{VALUE}}',
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
					'{{WRAPPER}} .vertical-sub-nav li > a.active-item' => 'color: {{VALUE}}',
					'{{WRAPPER}} .vertical-sub-nav li a.active-item .item-content svg' => 'fill: {{VALUE}}; color: {{VALUE}}',
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
					'{{WRAPPER}} .vertical-sub-nav li > a.active-item' => 'background-color: {{VALUE}}',
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
					'{{WRAPPER}} .vertical-sub-nav li > a.active-item' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->add_icon_styles( 1, esc_html__( 'Sub Menu Icons', 'the7mk2' ) );

		$this->start_controls_section(
			'section_style_indicator-sub_menu',
			[
				'label'     => esc_html__( 'Sub Menu Indicators', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'submenu_display!' => 'always',
					'selected_icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'sub_icon_align',
			[
				'label'     => esc_html__( 'Indicator Position', 'the7mk2' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'  => [
						'title' => esc_html__( 'Start', 'the7mk2' ),
						'icon'  => 'eicon-h-align-left',
					],
					'right' => [
						'title' => esc_html__( 'End', 'the7mk2' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'default'   => is_rtl() ? 'left' : 'right',
				'toggle'    => false,
			]
		);

		$this->add_control(
			'sub_icon_alignment',
			[
				'label'                => esc_html__( 'Indicator Align', 'the7mk2' ),
				'type'                 => Controls_Manager::SELECT,
				'options'              => [
					'with_text' => esc_html__( 'With text', 'the7mk2' ),
					'side'      => esc_html__( 'Side', 'the7mk2' ),
				],
				'default'              => 'with_text',
				'selectors_dictionary' => [
					'with_text' => '',
					'side'      => 'justify-content: space-between;',
				],
			]
		);

		$this->add_basic_responsive_control(
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
					'{{WRAPPER}} .vertical-sub-nav' => '--sub-icon-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .vertical-sub-nav > li > a .next-level-button i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .vertical-sub-nav > li > a .next-level-button, {{WRAPPER}} .vertical-sub-nav > li > a .next-level-button svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_basic_responsive_control(
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
					'{{WRAPPER}} .vertical-sub-nav' => '--sub-icon-spacing: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .dt-sub-icon-position-left .vertical-sub-nav > li > a .next-level-button' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .dt-sub-icon-position-right .vertical-sub-nav > li > a  .next-level-button' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .dt-sub-icon-position-left.dt-sub-icon-align-side .vertical-sub-nav > li > a .item-content ' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .dt-sub-icon-position-right.dt-sub-icon-align-side .dt-nav-menu > li > a .item-content ' => 'margin-right: {{SIZE}}{{UNIT}};',
					'(desktop) {{WRAPPER}}.dt-sub-menu_align-center .dt-sub-icon-align-side .vertical-sub-nav > li > a  .item-content ' => 'margin: 0 {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'sub_indicator_colors_heading',
			[
				'label'     => esc_html__( 'Colors', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->start_controls_tabs( 'tabs_sub_menu_indicator_style' );

		$this->start_controls_tab(
			'tab_sub_menu_indicator_normal',
			[
				'label' => esc_html__( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_control(
			'sub_menu_icon_color',
			[
				'label'     => esc_html__( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .vertical-sub-nav > li > a .next-level-button' => 'color: {{VALUE}};',
					'{{WRAPPER}} .vertical-sub-nav > li > a .next-level-button svg'                => 'fill: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_sub_menu_indicator_hover',
			[
				'label' => esc_html__( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			'sub_menu_icon_hover_color',
			[
				'label'     => esc_html__( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dt-sub-menu-display-on_click .vertical-sub-nav > li > a .next-level-button:hover, {{WRAPPER}} .dt-sub-menu-display-on_item_click .vertical-sub-nav > li > a:hover .next-level-button' => 'color: {{VALUE}};',
					'{{WRAPPER}} .dt-sub-menu-display-on_click .vertical-sub-nav > li > a .next-level-button:hover svg,  {{WRAPPER}} .dt-sub-menu-display-on_item_click .vertical-sub-nav > li > a:hover .next-level-button svg'                                    => 'fill: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_sub_menu_indicator_active',
			[
				'label' => esc_html__( 'Active', 'the7mk2' ),
			]
		);

		$this->add_control(
			'sub_menu_icon_active_color',
			[
				'label'     => esc_html__( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .vertical-sub-nav a.active-item .next-level-button, {{WRAPPER}} .vertical-sub-nav a.active .next-level-button' => 'color: {{VALUE}};',
					'{{WRAPPER}} .vertical-sub-nav a.active-item .next-level-button svg, {{WRAPPER}} .vertical-sub-nav a.active .next-level-button svg'                => 'fill: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Render element.
	 *
	 * Generates the final HTML on the frontend.
	 */
	protected function render() {
		if ( ! $this->get_available_menus() ) {
			return;
		}

		$settings = $this->get_active_settings();

		$class     = [
			'dt-nav-menu--main',
			'dt-nav-menu__container',
			'dt-sub-menu-display-' . $settings['submenu_display'],
			'dt-icon-align-' . $settings['icon_alignment'],
			'dt-icon-position-' . $settings['icon_align'],
			'dt-sub-icon-position-' . $settings['sub_icon_align'],
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
		$this->add_render_attribute( 'main-menu', 'class', $class );

		$sub_menu_act_icon = '';
		if ( $settings['selected_active_icon'] ) {
			$sub_menu_act_icon = $this->get_elementor_icon_html(
				$settings['selected_active_icon'],
				'i',
				[
					'class' => 'icon-active',
				]
			);
		}

		$sub_menu_icon = '';
		if ( $settings['selected_icon'] ) {
			$sub_menu_icon = $this->get_elementor_icon_html(
				$settings['selected_icon'],
				'i',
				[
					'class' => 'open-button',
				]
			);
		}

		if ( $settings['selected_icon'] && $settings['selected_icon']['value'] === '' ) {
			$this->add_render_attribute( 'main-menu', 'class', 'indicator-off' );
		}

		$link_after = sprintf(
			'</span><span class="%s" data-icon = "%s">%s %s</span>',
			esc_attr( $settings['icon_align'] ? $settings['icon_align'] . ' next-level-button' : '' ),
			esc_attr( ! empty( $settings['selected_active_icon']['value'] ) && is_string( $settings['selected_active_icon']['value'] ) ? $settings['selected_active_icon']['value'] : '' ),
			$sub_menu_icon,
			$sub_menu_act_icon
		);
		if ( $settings['show_widget_title'] === 'y' && $settings['widget_title_text'] ) {
			echo $this->display_widget_title( $settings['widget_title_text'], $settings['widget_title_tag'] );
		}

		$mega_menu_handler = new Mega_Menu();
		$mega_menu_handler->add_hooks();

		presscore_nav_menu(
			[
				'menu'                => $settings['menu'],
				// Prevent caching by placing unique value.
				'menu_id'             => $this->get_id(),
				'theme_location'      => 'the7_nav-menu',
				'items_wrap'          => '<nav ' . $this->get_render_attribute_string( 'main-menu' ) . '><ul class="dt-nav-menu">%3$s</ul></nav>',
				'submenu_class'       => implode( ' ', presscore_get_primary_submenu_class( 'vertical-sub-nav' ) ),
				'link_before'         => '<span class="item-content">',
				'link_after'          => $link_after,
				'parent_is_clickable' => $settings['submenu_display'] !== 'on_item_click',
				'force_icons_only'    => true,
			]
		);

		$mega_menu_handler->remove_hooks();
	}

	/**
	 * @param int    $menu_deph Menu depth.
	 * @param string $section_label Label.
	 *
	 * @return void
	 */
	protected function add_icon_styles( $menu_deph, $section_label ) {
		$prefix = 'deph_' . $menu_deph . '_';

		if ( $menu_deph === 0 ) {
			$selector_menu = '{{WRAPPER}} .dt-nav-menu li.depth-0 > a';
			$selector_icon = '{{WRAPPER}} .dt-nav-menu li.menu-item.depth-0 > a .item-content > i';
			$selector_img  = '{{WRAPPER}} .dt-nav-menu li.menu-item.depth-0 > a .item-content > img, {{WRAPPER}} .dt-nav-menu li.menu-item.depth-0 > a .item-content > svg';
		} else {
			$selector_menu = '{{WRAPPER}} .dt-nav-menu .vertical-sub-nav ';
			$selector_icon = '{{WRAPPER}} .vertical-sub-nav li.menu-item > a .item-content > i';
			$selector_img  = '{{WRAPPER}} .vertical-sub-nav li.menu-item > a .item-content > img, {{WRAPPER}} .vertical-sub-nav li.menu-item > a .item-content > svg';
		}

		$this->start_controls_section(
			$prefix . 'section_icon_style',
			[
				'label' => $section_label,
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			$prefix . 'icon_align',
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
					'top'   => $this->combine_to_css_vars_definition_string(
						[
							'icon-margin'    => '0 0 var(--icon-column-spacing) 0',
							'item-direction' => 'column',
							'item-align'     => 'var(--menu-position)',
							'sub-item-align' => 'var(--sub-menu-position)',
							'item-justify'   => 'center',
							'icon-order'     => '0',
						]
					),
					'right' => $this->combine_to_css_vars_definition_string(
						[
							'icon-margin'    => '0 0 0 var(--icon-column-spacing)',
							'item-direction' => 'row',
							'item-align'     => 'center',
							'sub-item-align' => 'center',
							'item-justify'   => 'inherit',
							'icon-order'     => '2',
						]
					),
					'left'  => $this->combine_to_css_vars_definition_string(
						[
							'icon-margin'    => '0 var(--icon-column-spacing) 0 0',
							'item-direction' => 'row',
							'item-align'     => 'center',
							'sub-item-align' => 'center',
							'item-justify'   => 'inherit',
							'icon-order'     => '0',
						]
					),
				],
				'selectors'            => [
					$selector_menu => '{{VALUE}}',
				],
				'default'              => 'left',
				'toggle'               => false,
			]
		);

		$this->add_responsive_control(
			$prefix . 'icon_space',
			[
				'label'     => esc_html__( 'Spacing', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'default'   => [
					'size' => '5',
				],
				'selectors' => [
					$selector_menu => '--icon-column-spacing: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			$prefix . 'icon_size',
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
					$selector_menu => '--icon-column-size: {{SIZE}}{{UNIT}};',
					$selector_icon => 'font-size: {{SIZE}}{{UNIT}};',
					$selector_img  => 'width: {{SIZE}}{{UNIT}} !important;height: {{SIZE}}{{UNIT}}!important;',
				],
			]
		);

		$this->add_responsive_control(
			$prefix . 'icon_padding',
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
					$selector_menu => '--icon-paddings: {{SIZE}}{{UNIT}};',
					$selector_icon => 'padding: {{SIZE}}{{UNIT}};',
					$selector_img  => 'padding: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => $prefix . 'icon_border_width',
				'label'    => esc_html__( 'Border', 'the7mk2' ),
				'selector' => $selector_icon . ', ' . $selector_img,
				'exclude'  => [
					'color',
				],
			]
		);

		$this->add_control(
			$prefix . 'icon_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					$selector_icon => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
					$selector_img  => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
			]
		);
		$this->add_control(
			$prefix . 'icon_colors_heading',
			[
				'label'     => esc_html__( 'Colors', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->start_controls_tabs( $prefix . 'icon_tabs_style' );

		$this->add_icon_title_states_controls( 'normal_', esc_html__( 'Normal', 'the7mk2' ), $menu_deph );
		$this->add_icon_title_states_controls( 'hover_', esc_html__( 'Hover', 'the7mk2' ), $menu_deph );
		$this->add_icon_title_states_controls( 'active_', esc_html__( 'Active', 'the7mk2' ), $menu_deph );

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function add_widget_title_style_controls() {
		$this->start_controls_section(
			'widget_style_section',
			[
				'label'     => esc_html__( 'Widget Title', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_widget_title' => 'y',
				],
			]
		);

		$this->add_basic_responsive_control(
			'widget_title_align',
			[
				'label'     => esc_html__( 'Alignment', 'the7mk2' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
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
				'selectors' => [
					'{{WRAPPER}} .rp-heading' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'widget_title_typography',
				'selector' => '{{WRAPPER}} .rp-heading',
			]
		);

		$this->add_control(
			'widget_title_color',
			[
				'label'     => esc_html__( 'Font Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .rp-heading' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'widget_title_bottom_margin',
			[
				'label'      => esc_html__( 'Spacing Below Title', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 20,
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
					'{{WRAPPER}} .rp-heading' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @param string $prefix_name Prefix.
	 * @param string $box_name    Lable.
	 * @param int    $menu_deph   Menu depth.
	 *
	 * @return void
	 */
	protected function add_icon_title_states_controls( $prefix_name, $box_name, $menu_deph = 0 ) {
		$hover_sel = '';
		if ( $prefix_name === 'hover_' ) {
			$hover_sel = ':hover';
		}

		$act_sel = '';
		$active_sel = '';
		if ( $prefix_name === 'active_' ) {
			$act_sel = '.act';
			$active_sel = '.active';
		}

		$wrapper     = '{{WRAPPER}}';
		$prefix_name = 'deph_' . $menu_deph . '_' . $prefix_name;

		if ( $menu_deph === 0 ) {
			$icon_selector = $wrapper . ' .dt-nav-menu li.menu-item.depth-0' . $act_sel . ' > a' . $hover_sel . ' .item-content';
		} else {
			$icon_selector = $wrapper . ' .dt-nav-menu .vertical-sub-nav li.menu-item' . $act_sel . ' > a' . $hover_sel . ' .item-content';
		}

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
					$icon_selector . ' i'   => 'color: {{VALUE}};',
					$icon_selector . ' svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			$prefix_name . 'icon_title_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$icon_selector . ' i'   => 'border-color: {{VALUE}};',
					$icon_selector . ' svg' => 'border-color: {{VALUE}};',
					$icon_selector . ' img' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'deph_' . $menu_deph . '_icon_border_width_border!' => '',
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
					$icon_selector . ' i'   => 'background: {{VALUE}};',
					$icon_selector . ' svg' => 'background: {{VALUE}};',
					$icon_selector . ' img' => 'background: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
	}

	/**
	 * @param string $text Title text.
	 * @param string $tag Tag.
	 *
	 * @return string
	 */
	protected function display_widget_title( $text, $tag = 'h3' ) {
		$tag = Utils::validate_html_tag( $tag );

		$output  = '<' . $tag . ' class="rp-heading">';
		$output .= esc_html( $text );
		$output .= '</' . $tag . '>';

		return $output;
	}

	/**
	 * Render widget plain content.
	 *
	 * No plain content here.
	 */
	public function render_plain_content() {
	}
}
