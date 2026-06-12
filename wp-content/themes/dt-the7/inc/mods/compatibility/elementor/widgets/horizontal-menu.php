<?php
/**
 * The7 'Horizontal Menu' widget for Elementor.
 *
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Plugin;
use Elementor\TemplateLibrary\Source_Local;
use ElementorPro\Modules\Popup\Module as PopupModule;
use stdClass;
use The7\Mods\Compatibility\Elementor\Modules\Mega_Menu\Mega_Menu;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;
use WP_Post;

defined( 'ABSPATH' ) || exit;

/**
 * Horizontal_Menu class.
 */
class Horizontal_Menu extends The7_Elementor_Widget_Base {

	const STICKY_WRAPPER = '.the7-e-sticky-effects .elementor-element.elementor-element-{{ID}}';

	/**
	 * Get element name.
	 */
	public function get_name() {
		return 'the7_horizontal-menu';
	}

	/**
	 * Get element title.
	 */
	protected function the7_title() {
		return esc_html__( 'Horizontal Menu', 'the7mk2' );
	}

	/**
	 * Get element icon.
	 */
	protected function the7_icon() {
		return 'eicon-nav-menu';
	}

	/**
	 * Get style dependencies.
	 *
	 * Retrieve the list of style dependencies the element requires.
	 *
	 * @return array Element styles dependencies.
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
		the7_register_script_in_footer(
			$this->get_name(),
			THE7_ELEMENTOR_JS_URI . '/the7-horizontal-menu.js',
			[ 'the7-elementor-frontend-common' ]
		);
		the7_register_style(
			$this->get_name(),
			THE7_ELEMENTOR_CSS_URI . '/the7-horizontal-menu-widget.css'
		);
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
		$this->add_layout_section_controls();
		$this->add_main_menu_styles();
		$this->add_menu_decoration_styles();
		$this->add_icon_styles( 0, esc_html__( 'Main Menu Icons', 'the7mk2' ) );
		$this->add_menu_indicator_styles();
		$this->add_sub_menu_styles();
		$this->add_icon_styles( 1, esc_html__( 'Drop Down Icons', 'the7mk2' ) );
		$this->add_sub__menu_indicator_styles();
		$this->add_toogle_styles();
	}

	/**
	 * @return void
	 */
	protected function add_layout_section_controls() {
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
					'label'        => esc_html__( 'Menu', 'the7mk2' ),
					'type'         => Controls_Manager::SELECT,
					'options'      => $menus,
					'default'      => array_keys( $menus )[0],
					'save_default' => true,
					'description'  => sprintf(
					/* translators: 1: Link open tag, 2: Link closing tag. */
						esc_html__( 'Go to the %1$sMenus screen%2$s to manage your menus.', 'the7mk2' ),
						sprintf( '<a href="%s" target="_blank">', admin_url( 'nav-menus.php' ) ),
						'</a>'
					),
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
		$icon_visible_options = [
			'enable'  => esc_html__( 'Show', 'the7mk2' ),
			'disable' => esc_html__( 'Hide', 'the7mk2' ),
		];
		$this->add_responsive_control(
			'icons_visible',
			[
				'label'                => esc_html__( 'Menu icons', 'the7mk2' ),
				'type'                 => Controls_Manager::SELECT,
				'options'              => $icon_visible_options,
				'device_args'          => $this->generate_device_args(
					[
						'default' => '',
						'options' => [ '' => esc_html__( 'Default', 'the7mk2' ) ] + $icon_visible_options,
					]
				),
				'default'              => 'disable',
				'selectors_dictionary' => [
					'enable'  => $this->combine_to_css_vars_definition_string(
						[
							'icon-display'      => 'inline-flex',
							'icon-column-gap'   => 'var(--icon-column-spacing)',
							'icon-column-width' => 'var(--icon-column-size)',
						]
					),
					'disable' => $this->combine_to_css_vars_definition_string(
						[
							'icon-display'      => 'none',
							'icon-column-gap'   => '0px',
							'icon-column-width' => '0px',
						]
					),
				],
				'selectors'            => [
					'{{WRAPPER}} .dt-nav-menu-horizontal li.depth-0 > a, {{WRAPPER}} .dt-nav-menu-horizontal--main .horizontal-sub-nav' => '{{VALUE}}',
				],
			]
		);

		$this->add_control(
			'parent_is_clickable',
			[
				'label'              => esc_html__( 'Parent menu items clickable', 'the7mk2' ),
				'type'               => Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'Yes', 'the7mk2' ),
				'label_off'          => esc_html__( 'No', 'the7mk2' ),
				'return_value'       => 'yes',
				'default'            => 'yes',
				'prefix_class'       => 'parent-item-clickable-',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'submenu_display',
			[
				'label'        => esc_html__( 'Show submenu on', 'the7mk2' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'hover',
				'options'      => [
					'hover' => esc_html__( 'Hover', 'the7mk2' ),
					'click' => esc_html__( 'Click', 'the7mk2' ),
				],
				'prefix_class' => 'show-sub-menu-on-',
				'condition'    => [
					'parent_is_clickable' => '',
				],
				'render_type'  => 'template',
			]
		);

		$this->add_control(
			'heading_mobile_dropdown',
			[
				'label'     => esc_html__( 'Mobile Dropdown', 'the7mk2' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'dropdown',
			[
				'label'              => esc_html__( 'Breakpoint', 'the7mk2' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => 'tablet',
				'options'            => $this->get_mobile_breakpoint_options(),
				'frontend_available' => true,
				'render_type'        => 'template',
			]
		);

		if ( the7_elementor_pro_is_active() ) {
			$this->add_control(
				'dropdown_type',
				[
					'label'              => esc_html__( 'Display mobile menu as', 'the7mk2' ),
					'type'               => Controls_Manager::SELECT,
					'default'            => 'dropdown',
					'options'            => [
						'dropdown' => esc_html__( 'Dropdown', 'the7mk2' ),
						'popup'    => esc_html__( 'Popup', 'the7mk2' ),
					],
					'frontend_available' => true,
					'render_type'        => 'template',
					'prefix_class'       => 'mob-menu-',
					'condition'          => [
						'dropdown!' => 'none',
					],
				]
			);

			$this->add_control(
				'popup_link',
				[
					'label'      => esc_html__( 'Popup', 'the7mk2' ),
					'type'       => Controls_Manager::SELECT2,
					'options'    => $this->get_popups_list(),
					'conditions' => [
						'relation' => 'and',
						'terms'    => [
							[
								'name'     => 'dropdown_type',
								'operator' => '=',
								'value'    => 'popup',
							],
							[
								'name'     => 'dropdown',
								'operator' => '!=',
								'value'    => 'none',
							],
						],
					],
				]
			);
		} else {
			// Show only dropdown in case we do not have PRO Elements.
			$this->add_control(
				'dropdown_type',
				[
					'type'         => Controls_Manager::HIDDEN,
					'default'      => 'dropdown',
					'prefix_class' => 'mob-menu-',
					'render_type'  => 'none',
					'condition'    => [
						'dropdown!' => 'none',
					],
				]
			);
		}

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function add_main_menu_styles() {
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

		$this->add_control(
			'items_position',
			[
				'label'       => esc_html__( 'Items position', 'the7mk2' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'start',
				'options'     => [
					'start'     => esc_html__( 'Start', 'the7mk2' ),
					'center'    => esc_html__( 'Center', 'the7mk2' ),
					'end'       => esc_html__( 'End', 'the7mk2' ),
					'around'    => esc_html__( 'Space Around', 'the7mk2' ),
					'between'   => esc_html__( 'Space Between', 'the7mk2' ),
					'evenly'    => esc_html__( 'Space Evenly', 'the7mk2' ),
					'justified' => esc_html__( 'Stretch', 'the7mk2' ),
					'fullwidth' => esc_html__( 'Equal Width', 'the7mk2' ),
				],
				'render_type' => 'template',
			]
		);

		$this->add_responsive_control(
			'rows_gap',
			[
				'label'      => esc_html__( 'Gap between items', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}}' => ' --grid-row-gap: {{SIZE}}{{UNIT}};',

					'{{WRAPPER}} .dt-nav-menu-horizontal > li:not(.item-divider):not(:first-child):not(:last-child) ' => '  padding-left: calc({{SIZE}}{{UNIT}}/2); padding-right: calc({{SIZE}}{{UNIT}}/2);',

					'{{WRAPPER}}.widget-divider-yes .first-item-border-hide .dt-nav-menu-horizontal > li:nth-child(2)' => 'padding-left: 0',

					'{{WRAPPER}}.widget-divider-yes .last-item-border-hide .dt-nav-menu-horizontal > li:nth-last-child(2)' => 'padding-right: 0',
				],
			]
		);

		$this->add_control(
			'height',
			[
				'label'      => esc_html__( 'Height', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 225,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .dt-nav-menu-horizontal' => 'min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'height_sticky',
			[
				'label'       => esc_html__( 'Change height', 'the7mk2' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => [ 'px' ],
				'range'       => [
					'px' => [
						'min' => 0,
						'max' => 225,
					],
				],
				'selectors'   => [
					self::STICKY_WRAPPER . ' .dt-nav-menu-horizontal' => 'min-height: {{SIZE}}{{UNIT}};',
				],
				'description' => esc_html__( 'When “Sticky” and “Change Styles when Sticky” are ON for the parent section.', 'the7mk2' ),
			]
		);

		$this->add_control(
			'divider',
			[
				'label'        => esc_html__( 'Dividers', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_off'    => esc_html__( 'Off', 'the7mk2' ),
				'label_on'     => esc_html__( 'On', 'the7mk2' ),
				'return_value' => 'yes',
				'empty_value'  => 'no',
				'render_type'  => 'template',
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
					'{{WRAPPER}}.widget-divider-yes .dt-nav-menu-horizontal > .item-divider' => 'border-left-style: {{VALUE}}',
					'{{WRAPPER}} .first-item-border-hide .dt-nav-menu-horizontal > .item-divider:first-child' => 'display: none;',
					'{{WRAPPER}}.widget-divider-yes .last-item-border-hide .dt-nav-menu-horizontal > .item-divider:last-child' => 'display: none;',
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
			'divider_height',
			[
				'label'     => esc_html__( 'Height', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 1,
						'max' => 50,
					],
				],
				'condition' => [
					'divider' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .dt-nav-menu-horizontal' => '--divider-height: {{SIZE}}{{UNIT}}',
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
				'label_on'     => esc_html__( 'Show', 'the7mk2' ),
				'label_off'    => esc_html__( 'Hide', 'the7mk2' ),
				'condition'    => [
					'divider' => 'yes',
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
					'{{WRAPPER}}.widget-divider-yes .dt-nav-menu-horizontal > .item-divider' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'sticky_divider_color',
			[
				'label'       => esc_html__( 'Change color', 'the7mk2' ),
				'type'        => Controls_Manager::COLOR,
				'condition'   => [
					'divider' => 'yes',
				],
				'selectors'   => [
					self::STICKY_WRAPPER . '.widget-divider-yes .dt-nav-menu-horizontal > .item-divider' => 'border-color: {{VALUE}}',
				],
				'description' => esc_html__( 'When “Sticky” and “Change Styles when Sticky” are ON for the parent section.', 'the7mk2' ),
			]
		);

		$this->add_control(
			'items_heading',
			[
				'label'     => esc_html__( 'Items', 'the7mk2' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'menu_typography',
				'separator' => 'before',
				'selector'  => ' {{WRAPPER}} .dt-nav-menu-horizontal > li > a .menu-item-text',
			]
		);

		$this->add_responsive_control(
			'padding_menu_item',
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
					'{{WRAPPER}} .dt-nav-menu-horizontal > li > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
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
					'{{WRAPPER}} .dt-nav-menu-horizontal > li > a' => 'border-top-width: {{TOP}}{{UNIT}};
					border-right-width: {{RIGHT}}{{UNIT}}; border-bottom-width: {{BOTTOM}}{{UNIT}}; border-left-width:{{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .dt-nav-menu-horizontal > li > a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'item_colors',
			[
				'label'     => esc_html__( 'Colors', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->start_controls_tabs( 'tabs_menu_item_style' );

		// Normal colors.
		$this->start_controls_tab( 'tab_menu_item_normal', [ 'label' => esc_html__( 'Normal', 'the7mk2' ) ] );
		$this->add_item_color_controls( 'normal' );
		$this->end_controls_tab();

		// Hover colors.
		$this->start_controls_tab( 'tab_menu_item_hover', [ 'label' => esc_html__( 'Hover', 'the7mk2' ) ] );
		$this->add_item_color_controls( 'hover' );
		$this->end_controls_tab();

		// Active colors.
		$this->start_controls_tab( 'tab_menu_item_active', [ 'label' => esc_html__( 'Active', 'the7mk2' ) ] );
		$this->add_item_color_controls( 'active' );
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'item_color_sticky',
			[
				'label'       => esc_html__( 'Change Colors', 'the7mk2' ),
				'type'        => Controls_Manager::SWITCHER,
				'separator'   => 'before',
				'description' => esc_html__( 'When “Sticky” and “Change Styles when Sticky” are ON for the parent section.', 'the7mk2' ),
			]
		);

		$this->start_controls_tabs(
			'tabs_menu_item_sticky_style',
			[
				'condition' => [
					'item_color_sticky!' => '',
				],
			]
		);

		// Normal colors.
		$this->start_controls_tab( 'tab_menu_item_sticky', [ 'label' => esc_html__( 'Normal', 'the7mk2' ) ] );
		$this->add_item_color_controls( 'normal', 'sticky', self::STICKY_WRAPPER );
		$this->end_controls_tab();

		// Hover colors.
		$this->start_controls_tab( 'tab_menu_item_sticky_hover', [ 'label' => esc_html__( 'Hover', 'the7mk2' ) ] );
		$this->add_item_color_controls( 'hover', 'sticky', self::STICKY_WRAPPER );
		$this->end_controls_tab();

		// Active colors.
		$this->start_controls_tab( 'tab_menu_item_sticky_active', [ 'label' => esc_html__( 'Active', 'the7mk2' ) ] );
		$this->add_item_color_controls( 'active', 'sticky', self::STICKY_WRAPPER );
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function add_menu_decoration_styles() {
		$this->start_controls_section(
			'section_style_decoration-menu',
			[
				'label' => esc_html__( 'Main menu Decoration', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'decoration',
			[
				'label'        => esc_html__( 'Decoration', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_off'    => esc_html__( 'Off', 'the7mk2' ),
				'label_on'     => esc_html__( 'On', 'the7mk2' ),
				'prefix_class' => 'items-decoration-',
			]
		);

		$this->add_control(
			'decoration_height',
			[
				'label'      => esc_html__( 'Height', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 25,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .dt-nav-menu-horizontal' => '--decoration-height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .dt-nav-menu-horizontal > li > a:after' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'decoration!' => '',
				],
			]
		);

		$this->add_control(
			'decoration_width',
			[
				'label'      => esc_html__( 'Width', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 225,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .dt-nav-menu-horizontal' => '--decoration-wiidth: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}:not(.decoration-left-to-right) .dt-nav-menu-horizontal > li > a:after' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'decoration!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'decoration_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .dt-nav-menu-horizontal > li > a:after' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'decoration_align',
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
				'prefix_class'         => 'decoration-align%s-',
				'default'              => 'left',
				'condition'            => [
					'decoration_width[size]!' => '',
				],
				'selectors_dictionary' => [
					'left'   => 'left: 0; right: auto;',
					'center' => 'left: auto; right: auto;',
					'right'  => 'right: 0; left: auto;',
				],
				'selectors'            => [
					'{{WRAPPER}}.items-decoration-yes .dt-nav-menu-horizontal > li > a:after' => ' {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'decoration_position',
			[
				'label'        => esc_html__( 'Position', 'the7mk2' ),
				'type'         => Controls_Manager::CHOOSE,
				'options'      => [
					'top'    => [
						'title' => esc_html__( 'Top', 'the7mk2' ),
						'icon'  => 'eicon-v-align-top',
					],
					'bottom' => [
						'title' => esc_html__( 'Bottom', 'the7mk2' ),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'prefix_class' => 'decoration-position-',
				'default'      => 'bottom',
				'toggle'       => false,
				'condition'    => [
					'decoration!' => '',
				],
			]
		);

		$this->add_control(
			'decoration_align_with',
			[
				'label'        => esc_html__( 'Align with', 'the7mk2' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'height',
				'options'      => [
					'height' => esc_html__( 'Menu Height', 'the7mk2' ),
					'text'   => esc_html__( 'Item', 'the7mk2' ),
				],
				'prefix_class' => 'decoration-align-',
				'render_type'  => 'template',
				'condition'    => [
					'decoration!' => '',
				],
			]
		);

		$this->add_control(
			'decoration_direction',
			[
				'label'        => esc_html__( 'Direction', 'the7mk2' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'left-to-right',
				'options'      => [
					'left-to-right' => esc_html__( 'Expand', 'the7mk2' ),
					'upwards'       => esc_html__( 'Upwards', 'the7mk2' ),
					'downwards'     => esc_html__( 'Downwards', 'the7mk2' ),
					'fade'          => esc_html__( 'Fade', 'the7mk2' ),
				],
				'prefix_class' => 'decoration-',
				'render_type'  => 'template',
				'condition'    => [
					'decoration!' => '',
				],
			]
		);

		$this->start_controls_tabs(
			'tabs_menu_decoration_style',
			[
				'condition' => [
					'decoration!' => '',
				],
			]
		);

		// Hover colors.
		$this->start_controls_tab( 'tab_menu_decoration_hover', [ 'label' => esc_html__( 'Hover', 'the7mk2' ) ] );
		$this->add_control(
			'decoration_menu_item_hover',
			[
				'label'     => esc_html__( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .dt-nav-menu-horizontal > li:not(.act) > a:hover:after' => 'background: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();

		// Active colors.
		$this->start_controls_tab( 'tab_menu_decoration_active', [ 'label' => esc_html__( 'Active', 'the7mk2' ) ] );
		$this->add_control(
			'decoration_menu_item_active',
			[
				'label'     => esc_html__( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .dt-nav-menu-horizontal > li.act > a:after' => 'background: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'decoration_color_sticky',
			[
				'label'       => esc_html__( 'Change Colors', 'the7mk2' ),
				'type'        => Controls_Manager::SWITCHER,
				'separator'   => 'before',
				'description' => esc_html__( 'When “Sticky” and “Change Styles when Sticky” are ON for the parent section.', 'the7mk2' ),
				'condition'   => [
					'decoration!' => '',
				],
			]
		);

		$this->start_controls_tabs(
			'tabs_menu_decoration_sticky_style',
			[
				'condition' => [
					'decoration_color_sticky!' => '',
					'decoration!'              => '',
				],
			]
		);

		// Hover colors.
		$this->start_controls_tab( 'tab_menu_decoration_sticky_hover', [ 'label' => esc_html__( 'Hover', 'the7mk2' ) ] );
		$this->add_control(
			'decoration_menu_item_sticky_hover',
			[
				'label'     => esc_html__( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => $this->give_me_megaselectors(
					[ self::STICKY_WRAPPER . ' .dt-nav-menu-horizontal > li:not(.act) > a:hover' ],
					[ ':after' => 'background: {{VALUE}}' ]
				),
				'condition' => [
					'decoration!' => '',
				],
			]
		);
		$this->end_controls_tab();

		// Active colors.
		$this->start_controls_tab( 'tab_menu_decoration_sticky_active', [ 'label' => esc_html__( 'Active', 'the7mk2' ) ] );
		$this->add_control(
			'decoration_menu_item_sticky_active',
			[
				'label'     => esc_html__( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => $this->give_me_megaselectors(
					[ self::STICKY_WRAPPER . ' .dt-nav-menu-horizontal > li.act > a' ],
					[ ':after' => 'background: {{VALUE}}' ]
				),
				'condition' => [
					'decoration!' => '',
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
	protected function add_menu_indicator_styles() {
		$this->start_controls_section(
			'section_style_indicator-menu',
			[
				'label' => esc_html__( 'Main menu Indicators', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'selected_icon',
			[
				'label'       => esc_html__( 'Icon', 'the7mk2' ),
				'type'        => Controls_Manager::ICONS,
				'default'     => [
					'value'   => 'fas fa-caret-down',
					'library' => 'fa-solid',
				],
				'skin'        => 'inline',
				'label_block' => false,
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
					'{{WRAPPER}} .dt-nav-menu-horizontal' => '--icon-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .dt-nav-menu-horizontal > li > a .submenu-indicator i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .dt-nav-menu-horizontal > li > a .submenu-indicator svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'selected_icon[value]!' => '',
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
					'{{WRAPPER}} .dt-nav-menu-horizontal' => '--icon-spacing: {{SIZE}}{{UNIT}}',

					'{{WRAPPER}} .dt-nav-menu-horizontal > li > a  .submenu-indicator' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'selected_icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'tab_menu_indicator_heading',
			[
				'label'     => esc_html__( 'Colors', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'selected_icon[value]!' => '',
				],
			]
		);

		$this->start_controls_tabs(
			'tabs_menu_indicator_style',
			[
				'condition' => [
					'selected_icon[value]!' => '',
				],
			]
		);

		// Normal colors.
		$this->start_controls_tab( 'tab_menu_indicator', [ 'label' => esc_html__( 'Normal', 'the7mk2' ) ] );
		$this->add_control(
			'icon_color',
			[
				'label'     => esc_html__( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => $this->give_me_megaselectors(
					[ '{{WRAPPER}} .dt-nav-menu-horizontal > li > a' ],
					[
						' .submenu-indicator' => 'color: {{VALUE}}',
						' svg'                => 'fill: {{VALUE}}; color: {{VALUE}};',
					]
				),
				'condition' => [
					'selected_icon[value]!' => '',
				],
			]
		);
		$this->end_controls_tab();

		// Hover colors.
		$this->start_controls_tab( 'tab_menu_indicator_hover', [ 'label' => esc_html__( 'Hover', 'the7mk2' ) ] );
		$this->add_control(
			'icon_color_hover',
			[
				'label'     => esc_html__( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => $this->give_me_megaselectors(
					[ '{{WRAPPER}} .dt-nav-menu-horizontal > li:not(.act) > a:hover' ],
					[
						' .submenu-indicator' => 'color: {{VALUE}}',
						' svg'                => 'fill: {{VALUE}}; color: {{VALUE}};',
					]
				),
				'condition' => [
					'selected_icon[value]!' => '',
				],
			]
		);
		$this->end_controls_tab();

		// Active colors.
		$this->start_controls_tab( 'tab_menu_indicator_active', [ 'label' => esc_html__( 'Active', 'the7mk2' ) ] );
		$this->add_control(
			'icon_color_active',
			[
				'label'     => esc_html__( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => $this->give_me_megaselectors(
					[ '{{WRAPPER}} .dt-nav-menu-horizontal > li.act > a' ],
					[
						' .submenu-indicator' => 'color: {{VALUE}}',
						' svg'                => 'fill: {{VALUE}}; color: {{VALUE}};',
					]
				),
				'condition' => [
					'selected_icon[value]!' => '',
				],
			]
		);
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'indicator_color_sticky',
			[
				'label'       => esc_html__( 'Change Colors', 'the7mk2' ),
				'type'        => Controls_Manager::SWITCHER,
				'separator'   => 'before',
				'description' => esc_html__( 'When “Sticky” and “Change Styles when Sticky” are ON for the parent section.', 'the7mk2' ),
				'condition'   => [
					'selected_icon[value]!' => '',
				],
			]
		);

		$this->start_controls_tabs(
			'tabs_menu_indicator_sticky_style',
			[
				'condition' => [
					'indicator_color_sticky!' => '',
					'selected_icon[value]!'   => '',
				],
			]
		);

		// Normal colors.
		$this->start_controls_tab( 'tab_menu_indicator_sticky', [ 'label' => esc_html__( 'Normal', 'the7mk2' ) ] );
		$this->add_control(
			'icon_color_sticky',
			[
				'label'     => esc_html__( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => $this->give_me_megaselectors(
					[ self::STICKY_WRAPPER . ' .dt-nav-menu-horizontal > li > a' ],
					[
						' .submenu-indicator' => 'color: {{VALUE}}',
						' svg'                => 'fill: {{VALUE}}; color: {{VALUE}};',
					]
				),
			]
		);
		$this->end_controls_tab();

		// Hover colors.
		$this->start_controls_tab( 'tab_menu_indicator_sticky_hover', [ 'label' => esc_html__( 'Hover', 'the7mk2' ) ] );
		$this->add_control(
			'icon_color_sticky_hover',
			[
				'label'     => esc_html__( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => $this->give_me_megaselectors(
					[ self::STICKY_WRAPPER . ' .dt-nav-menu-horizontal > li:not(.act) > a:hover' ],
					[
						' .submenu-indicator' => 'color: {{VALUE}}',
						' svg'                => 'fill: {{VALUE}}; color: {{VALUE}};',
					]
				),
			]
		);
		$this->end_controls_tab();

		// Active colors.
		$this->start_controls_tab( 'tab_menu_indicator_sticky_active', [ 'label' => esc_html__( 'Active', 'the7mk2' ) ] );
		$this->add_control(
			'icon_color_sticky_active',
			[
				'label'     => esc_html__( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => $this->give_me_megaselectors(
					[ self::STICKY_WRAPPER . ' .dt-nav-menu-horizontal > li.act > a' ],
					[
						' .submenu-indicator' => 'color: {{VALUE}}',
						' svg'                => 'fill: {{VALUE}}; color: {{VALUE}};',
					]
				),
			]
		);
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function add_sub_menu_styles() {
		$this->start_controls_section(
			'section_style_sub-menu',
			[
				'label' => esc_html__( 'Drop down', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'sub_menu_gap',
			[
				'label'      => esc_html__( 'Submenu Margins', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}}' => '--sub-menu-gap: {{TOP}}{{UNIT}}; --sub-menu-right-gap: {{RIGHT}}{{UNIT}}; --sub-menu-left-gap: {{LEFT}}{{UNIT}}; --sub-menu-bottom-gap: {{BOTTOM}}{{UNIT}};',
					'{{WRAPPER}} .horizontal-menu-dropdown .dt-nav-menu-horizontal--main' => 'top: calc(100% + {{TOP}}{{UNIT}});',
				],
			]
		);

		$this->add_responsive_control(
			'sub_menu_position',
			[
				'label'                => esc_html__( 'Submenu Position', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'default'              => 'left',
				'toggle'               => false,
				'device_args'          => $this->generate_device_args(
					[
						'toggle' => true,
					]
				),
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
					'left'    => $this->combine_to_css_vars_definition_string(
						[
							'position'          => 'relative',
							'width'             => 'var(--sub-menu-width)',
							'sub-width'         => '100%',
							'sub-left'          => '0px',
							'sub-paddings'      => 'calc(var(--sub-menu-gap, 0px) + var(--submenu-padding-top, 0px)) var(--submenu-padding-right, 20px) var(--submenu-padding-bottom, 20px) var(--submenu-padding-left, 20px)',
							'sub-margins'       => '0 var(--sub-menu-right-gap, 0px) 0 var(--sub-menu-left-gap, 0px)',
							'left'              => 'calc(var(--first-level-submenu-offset))',
							'right'             => 'auto',
							'first-item-offset' => '0px',
							'last-item-offset'  => 'auto',
							'submenu-max-width' => 'var(--default-submenu-max-width)',
						]
					),
					'right'   => $this->combine_to_css_vars_definition_string(
						[
							'position'          => 'relative',
							'width'             => 'var(--sub-menu-width)',
							'sub-width'         => '100%',
							'sub-left'          => '0px',
							'sub-paddings'      => 'calc(var(--sub-menu-gap, 0px) + var(--submenu-padding-top, 0px)) var(--submenu-padding-right, 20px) var(--submenu-padding-bottom, 20px) var(--submenu-padding-left, 20px)',
							'sub-margins'       => '0 var(--sub-menu-right-gap, 0px) 0 var(--sub-menu-left-gap, 0px)',
							'left'              => 'auto',
							'right'             => 'calc(var(--first-level-submenu-offset))',
							'first-item-offset' => 'auto',
							'last-item-offset'  => '0px',
							'submenu-max-width' => 'var(--default-submenu-max-width)',
						]
					),
					'center'  => $this->combine_to_css_vars_definition_string(
						[
							'position'          => 'relative',
							'width'             => 'var(--sub-menu-width)',
							'sub-width'         => '100%',
							'sub-left'          => '0px',
							'sub-paddings'      => 'calc(var(--sub-menu-gap, 0px) + var(--submenu-padding-top, 0px)) var(--submenu-padding-right, 20px) var(--submenu-padding-bottom, 20px) var(--submenu-padding-left, 20px)',
							'sub-margins'       => '0 var(--sub-menu-right-gap, 0px) 0 var(--sub-menu-left-gap, 0px)',
							'left'              => 'auto',
							'right'             => 'auto',
							'first-item-offset' => 'auto',
							'last-item-offset'  => 'auto',
							'submenu-max-width' => 'var(--default-submenu-max-width)',
						]
					),
					'justify' => $this->combine_to_css_vars_definition_string(
						[
							'position'                   => 'static',
							'width'                      => 'calc(100vw - var(--sub-menu-right-gap, 0px) - var(--sub-menu-left-gap, 0px))',
							'sub-width'                  => 'calc(100% - var(--sub-menu-right-gap, 0px) - var(--sub-menu-left-gap, 0px))',
							'sub-left'                   => 'var(--sub-menu-left-gap, 0px)',
							'sub-paddings'               => 'calc(var(--sub-menu-gap, 0px) + var(--submenu-padding-top, 20px)) calc(var(--sub-menu-right-gap, 0px) + var(--submenu-padding-right, 20px)) var(--submenu-padding-bottom, 20px) calc(var(--sub-menu-left-gap, 0px) + var(--submenu-padding-left, 20px))',
							'sub-margins'                => '0',
							'left'                       => 'calc(var(--dynamic-justified-submenu-left-offset) + var(--sub-menu-left-gap, 0px))',
							'right'                      => 'auto',
							'first-item-offset'          => 'calc(var(--dynamic-justified-submenu-left-offset) + var(--sub-menu-left-gap, 0px))',
							'first-level-submenu-offset' => 'calc(var(--dynamic-justified-submenu-left-offset) + var(--sub-menu-left-gap, 0px))',
							'last-item-offset'           => 'auto',
							'submenu-max-width'          => 'calc(100vw - var(--scrollbar-width, 0px))',
						]
					),
				],
				'selectors'            => [
					'{{WRAPPER}} .horizontal-menu-wrap' => '{{VALUE}}',
					'{{WRAPPER}} .dt-nav-menu-horizontal .depth-0 > .horizontal-sub-nav' => '{{VALUE}}',
					'{{WRAPPER}} .dt-nav-menu-horizontal .depth-0 > .the7-e-mega-menu-sub-nav' => '{{VALUE}}',
				],
				'prefix_class'         => 'sub-menu-position%s-',
			]
		);

		$this->add_control(
			'submenu_heading',
			[
				'label'     => esc_html__( 'Box', 'the7mk2' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'sub_menu_width',
			[
				'label'      => esc_html__( 'Background width', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'vw' ],
				'range'      => [
					'px' => [
						'max' => 1000,
					],
					'vw' => [
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .dt-nav-menu-horizontal > li > .horizontal-sub-nav, {{WRAPPER}} .dt-nav-menu-horizontal > li > .horizontal-sub-nav .horizontal-sub-nav' => 'min-width: calc({{SIZE}}{{UNIT}}); --sub-menu-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .horizontal-menu-dropdown' => '--sub-menu-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'padding_sub_menu',
			[
				'label'      => esc_html__( 'Background Paddings', 'the7mk2' ),
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
					'{{WRAPPER}}' => '--submenu-padding-top: {{TOP}}{{UNIT}}; --submenu-padding-right: {{RIGHT}}{{UNIT}}; --submenu-padding-bottom: {{BOTTOM}}{{UNIT}}; --submenu-padding-left: {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .horizontal-menu-dropdown .dt-nav-menu-horizontal--main' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'bg_sub_menu',
			[
				'label'     => esc_html__( 'Background color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .dt-nav-menu-horizontal > li > .horizontal-sub-nav:before, {{WRAPPER}} .dt-nav-menu-horizontal > li > .horizontal-sub-nav .horizontal-sub-nav, {{WRAPPER}} .horizontal-menu-dropdown .dt-nav-menu-horizontal--main' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'border_sub_menu_width',
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
					'{{WRAPPER}} .dt-nav-menu-horizontal > li > .horizontal-sub-nav:before, {{WRAPPER}} .dt-nav-menu-horizontal > li > .horizontal-sub-nav .horizontal-sub-nav, {{WRAPPER}} .horizontal-menu-dropdown .dt-nav-menu-horizontal--main' => '--submenu-border-right: {{RIGHT}}{{UNIT}}; border-style: solid; border-top-width: {{TOP}}{{UNIT}}; border-right-width: {{RIGHT}}{{UNIT}}; border-bottom-width: {{BOTTOM}}{{UNIT}}; border-left-width:{{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'border_sub_menu_color',
			[
				'label'     => esc_html__( 'Border color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .dt-nav-menu-horizontal > li > .horizontal-sub-nav:before, {{WRAPPER}} .dt-nav-menu-horizontal > li > .horizontal-sub-nav .horizontal-sub-nav, {{WRAPPER}} .horizontal-menu-dropdown .dt-nav-menu-horizontal--main' => 'border-color: {{VALUE}}',
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
					'{{WRAPPER}} .dt-nav-menu-horizontal > li > .horizontal-sub-nav:before, {{WRAPPER}} .dt-nav-menu-horizontal > li > .horizontal-sub-nav .horizontal-sub-nav, {{WRAPPER}} .horizontal-menu-dropdown .dt-nav-menu-horizontal--main' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'submenu_shadow',
				'label'    => esc_html__( 'Box Shadow', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .dt-nav-menu-horizontal > li > .horizontal-sub-nav:before, {{WRAPPER}} .dt-nav-menu-horizontal > li > .horizontal-sub-nav .horizontal-sub-nav, {{WRAPPER}} .horizontal-menu-dropdown .dt-nav-menu-horizontal--main',
			]
		);

		$this->add_control(
			'sub_list_heading',
			[
				'label'     => esc_html__( 'List', 'the7mk2' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'sub_rows_gap',
			[
				'label'      => esc_html__( 'Rows Gap', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .horizontal-sub-nav > li:not(:last-child)' => 'padding-bottom: {{SIZE}}{{UNIT}}; --sub-grid-row-gap: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .horizontal-menu-dropdown .horizontal-sub-nav .horizontal-sub-nav' => 'padding-top: {{SIZE}}{{UNIT}}; --sub-grid-row-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'sub_divider',
			[
				'label'        => esc_html__( 'Dividers', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_off'    => esc_html__( 'Off', 'the7mk2' ),
				'label_on'     => esc_html__( 'On', 'the7mk2' ),
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
					'{{WRAPPER}}.sub-widget-divider-yes .horizontal-sub-nav li:after' => 'border-bottom-style: {{VALUE}}',
					'{{WRAPPER}} .horizontal-menu-dropdown > ul .horizontal-sub-nav:before' => 'border-bottom-style: {{VALUE}}',
					'{{WRAPPER}} .horizontal-sub-nav li:last-child:after' => ' border-bottom-style: none;',
				],
			]
		);

		$this->add_control(
			'sub_divider_weight',
			[
				'label'     => esc_html__( 'Height', 'the7mk2' ),
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
					'{{WRAPPER}}.sub-widget-divider-yes .horizontal-sub-nav' => '--divider-sub-width: {{SIZE}}{{UNIT}}',
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
					'{{WRAPPER}}.sub-widget-divider-yes .horizontal-sub-nav > li:after, {{WRAPPER}} .horizontal-menu-dropdown > ul .horizontal-sub-nav:before' => 'border-color: {{VALUE}} !important',
				],
			]
		);

		$this->add_control(
			'sub_item_heading',
			[
				'label'     => esc_html__( 'Items', 'the7mk2' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'align_sub_items',
			[
				'label'                => esc_html__( 'Text alignment', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'default'              => is_rtl() ? 'right' : 'left',
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
				'prefix_class'         => 'dt-sub-menu_align%s-',
				'selectors_dictionary' => [
					'left'   => '--h-menu-sub-nav-justify-content: flex-start; --h-menu-sub-nav-align-items: flex-start; --h-menu-sub-nav-text-align: left; --submenu-side-gap: 20px;',
					'center' => '--h-menu-sub-nav-justify-content: center; --h-menu-sub-nav-align-items: center; --h-menu-sub-nav-text-align: center; --submenu-side-gap: 0px;',
					'right'  => '--h-menu-sub-nav-justify-content: flex-end;  --h-menu-sub-nav-align-items: flex-end; --h-menu-sub-nav-text-align: right; --submenu-side-gap: 20px;',
				],
				'selectors'            => [
					'{{WRAPPER}} .horizontal-sub-nav' => '{{VALUE}};',

					// TODO: It looks like there is room for improvement here.
					'(tablet) {{WRAPPER}}.dt-sub-menu_align-tablet-left.sub-icon_position-left.sub-icon_align-side .horizontal-sub-nav > li .menu-item-text' => 'margin: 0 0 0 var(--sub-icon-spacing); padding: 0 0 0 var(--sub-icon-size)',
					'(tablet) {{WRAPPER}}.dt-sub-menu_align-tablet-right.sub-icon_position-left.sub-icon_align-side .horizontal-sub-nav > li .menu-item-text' => 'margin: 0 0 0 var(--sub-icon-spacing); padding: 0 0 0 var(--sub-icon-size)',

					'(tablet) {{WRAPPER}}.dt-sub-menu_align-tablet-left.sub-icon_position-right.sub-icon_align-side .horizontal-sub-nav > li .menu-item-text' => 'margin: 0 var(--sub-icon-spacing) 0 0; padding: 0 var(--sub-icon-size) 0 0',
					'(tablet) {{WRAPPER}}.dt-sub-menu_align-tablet-right.sub-icon_position-right.sub-icon_align-side .horizontal-sub-nav > li .menu-item-text' => 'margin: 0 var(--sub-icon-spacing) 0 0; padding: 0 var(--sub-icon-size) 0 0',

					'(tablet) {{WRAPPER}}.dt-sub-menu_align-tablet-center.sub-icon_align-side .horizontal-sub-nav > li .menu-item-text ' => 'margin: 0 var(--icon-spacing); padding: 0 var(--sub-icon-size)',

					'(mobile) {{WRAPPER}}.dt-sub-menu_align-mobile-left.sub-icon_position-left.sub-icon_align-side .horizontal-sub-nav > li .menu-item-text' => 'margin: 0 0 0 var(--sub-icon-spacing); padding: 0 0 0 var(--sub-icon-size)',
					'(mobile) {{WRAPPER}}.dt-sub-menu_align-mobile-right.sub-icon_position-left.sub-icon_align-side .horizontal-sub-nav > li .menu-item-text' => 'margin: 0 0 0 var(--sub-icon-spacing); padding: 0 0 0 var(--sub-icon-size)',

					'(mobile) {{WRAPPER}}.dt-sub-menu_align-mobile-left.sub-icon_position-right.sub-icon_align-side .horizontal-sub-nav > li .menu-item-text' => 'margin: 0 var(--sub-icon-spacing) 0 0; padding: 0 var(--sub-icon-size) 0 0',
					'(mobile) {{WRAPPER}}.dt-sub-menu_align-mobile-right.sub-icon_position-right.sub-icon_align-side .horizontal-sub-nav > li .menu-item-text' => 'margin: 0 var(--sub-icon-spacing) 0 0; padding: 0 var(--sub-icon-size) 0 0',

					'(mobile) {{WRAPPER}}.dt-sub-menu_align-tablet-right.sub-icon_position-right.sub-icon_align-side .horizontal-sub-nav > li .menu-item-text' => 'margin: 0 var(--sub-icon-spacing) 0 0; padding: 0 var(--sub-icon-size) 0 0',
					'(mobile) {{WRAPPER}}.dt-sub-menu_align-right.sub-icon_position-right.sub-icon_align-side:not(.dt-sub-menu_align-tablet-center) .horizontal-sub-nav > li .menu-item-text' => 'margin: 0 var(--sub-icon-spacing) 0 0; padding: 0 var(--sub-icon-size) 0 0',

					'(mobile) {{WRAPPER}}.dt-sub-menu_align-mobile-center.sub-icon_align-side .horizontal-sub-nav > li .menu-item-text ' => 'margin: 0 var(--icon-spacing) !important; padding: 0 var(--sub-icon-size) !important',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'sub_menu_typography',
				'selector'  => '{{WRAPPER}} .horizontal-sub-nav > li a .menu-item-text',
				'separator' => 'before',
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
					'{{WRAPPER}} .horizontal-sub-nav > li > a' => 'border-top-width: {{TOP}}{{UNIT}};
					border-right-width: {{RIGHT}}{{UNIT}}; border-bottom-width: {{BOTTOM}}{{UNIT}}; border-left-width:{{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'padding_sub_menu_item',
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
					'{{WRAPPER}} .horizontal-sub-nav' => '--submenu-item-padding-right: {{RIGHT}}{{UNIT}}; --submenu-item-padding-left: {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .horizontal-sub-nav > li > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'sub_menu_item_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .horizontal-sub-nav > li > a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'sub_colors_heading',
			[
				'label'     => esc_html__( 'Colors', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->start_controls_tabs( 'tabs_sub_menu_item_style' );

		// Normal colors.
		$this->start_controls_tab( 'tab_sub_menu_item_normal', [ 'label' => esc_html__( 'Normal', 'the7mk2' ) ] );
		$this->add_sub_menu_item_color_controls( '' );
		$this->end_controls_tab();

		// Hover colors.
		$this->start_controls_tab( 'tab_sub_menu_item_hover', [ 'label' => esc_html__( 'Hover', 'the7mk2' ) ] );
		$this->add_sub_menu_item_color_controls( '_hover' );
		$this->end_controls_tab();

		// Active colors.
		$this->start_controls_tab( 'tab_sub_menu_item_active', [ 'label' => esc_html__( 'Active', 'the7mk2' ) ] );
		$this->add_sub_menu_item_color_controls( '_active' );
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function add_sub__menu_indicator_styles() {
		$this->start_controls_section(
			'section_style_indicator-sub_menu',
			[
				'label' => esc_html__( 'Drop Down Indicators', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'sub_icon',
			[
				'label'       => esc_html__( 'Desktop Icon', 'the7mk2' ),
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
			'dropdown_icon',
			[
				'label'       => esc_html__( 'Mobile Icon', 'the7mk2' ),
				'type'        => Controls_Manager::ICONS,
				'default'     => [
					'value'   => 'fas fa-caret-down',
					'library' => 'fa-solid',
				],
				'skin'        => 'inline',
				'label_block' => false,
				'condition'   => [
					'dropdown_type' => 'dropdown',
				],
			]
		);

		$this->add_control(
			'dropdown_icon_act',
			[
				'label'       => esc_html__( 'Mobile Active Icon', 'the7mk2' ),
				'type'        => Controls_Manager::ICONS,
				'default'     => [
					'value'   => 'fas fa-caret-up',
					'library' => 'fa-solid',
				],
				'skin'        => 'inline',
				'label_block' => false,
				'condition'   => [
					'dropdown_icon[value]!' => '',
					'dropdown_type'         => 'dropdown',
				],
			]
		);

		$this->add_control(
			'sub_icon_align',
			[
				'label'                => esc_html__( 'Indicator Position', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'default'              => is_rtl() ? 'left' : 'right',
				'options'              => [
					'left'  => [
						'title' => esc_html__( 'Start', 'the7mk2' ),
						'icon'  => 'eicon-h-align-left',
					],
					'right' => [
						'title' => esc_html__( 'End', 'the7mk2' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'toggle'               => false,
				'selectors_dictionary' => [
					'right' => 'order: 2; margin-left: var(--sub-icon-spacing);',
					'left'  => 'order: 0; margin-right: var(--sub-icon-spacing);',
				],
				'selectors'            => [
					'{{WRAPPER}} .horizontal-sub-nav > li a .submenu-indicator, {{WRAPPER}} .horizontal-menu-dropdown > ul > li a .submenu-indicator' => ' {{VALUE}};',
				],
				'prefix_class'         => 'sub-icon_position-',
				'conditions'           => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'sub_icon[value]',
							'operator' => '!=',
							'value'    => '',
						],
						[
							'name'     => 'dropdown_icon[value]',
							'operator' => '!=',
							'value'    => '',
						],
					],
				],
			]
		);

		$this->add_control(
			'sub_icon_alignment',
			[
				'label'                => esc_html__( 'Indicator Align', 'the7mk2' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => 'with_text',
				'options'              => [
					'with_text' => esc_html__( 'With text', 'the7mk2' ),
					'side'      => esc_html__( 'Side', 'the7mk2' ),
				],
				'selectors_dictionary' => [
					'with_text' => '',
					'side'      => '',
				],
				'selectors'            => [
					'{{WRAPPER}} .horizontal-sub-nav > li a .item-content, {{WRAPPER}} .horizontal-sub-nav > li a .menu-item-text, {{WRAPPER}} .horizontal-menu-dropdown > ul > li .item-content' => ' {{VALUE}};',
				],
				'prefix_class'         => 'sub-icon_align-',
				'conditions'           => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'sub_icon[value]',
							'operator' => '!=',
							'value'    => '',
						],
						[
							'name'     => 'dropdown_icon[value]',
							'operator' => '!=',
							'value'    => '',
						],

					],
				],
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
					'{{WRAPPER}} .horizontal-sub-nav' => '--sub-icon-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .horizontal-sub-nav .submenu-indicator i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .horizontal-sub-nav .submenu-indicator, {{WRAPPER}} .horizontal-sub-nav .submenu-indicator svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'sub_icon[value]',
							'operator' => '!=',
							'value'    => '',
						],
						[
							'name'     => 'dropdown_icon[value]',
							'operator' => '!=',
							'value'    => '',
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'sub_icon_space',
			[
				'label'      => esc_html__( 'Indicator Spacing', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'    => [
					'size' => '5',
				],
				'selectors'  => [
					'{{WRAPPER}} .horizontal-sub-nav' => '--sub-icon-spacing: {{SIZE}}{{UNIT}}',
				],
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'sub_icon[value]',
							'operator' => '!=',
							'value'    => '',
						],
						[
							'name'     => 'dropdown_icon[value]',
							'operator' => '!=',
							'value'    => '',
						],
					],
				],
			]
		);

		$this->add_control(
			'tab_sub_menu_indicator_heading',
			[
				'label'      => esc_html__( 'Colors', 'the7mk2' ),
				'type'       => Controls_Manager::HEADING,
				'separator'  => 'before',
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'sub_icon[value]',
							'operator' => '!=',
							'value'    => '',
						],
						[
							'name'     => 'dropdown_icon[value]',
							'operator' => '!=',
							'value'    => '',
						],
					],
				],
			]
		);

		$this->start_controls_tabs(
			'tabs_sub_menu_indicator_style',
			[
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'sub_icon[value]',
							'operator' => '!=',
							'value'    => '',
						],
						[
							'name'     => 'dropdown_icon[value]',
							'operator' => '!=',
							'value'    => '',
						],
					],
				],
			]
		);

		// Normsl colors.
		$this->start_controls_tab( 'tab_sub_menu_indicator', [ 'label' => esc_html__( 'Normal', 'the7mk2' ) ] );
		$this->add_control(
			'sub_menu_icon_color',
			[
				'label'     => esc_html__( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .horizontal-sub-nav li > a .submenu-indicator' => 'color: {{VALUE}};',
					'{{WRAPPER}} .horizontal-sub-nav li > a .submenu-indicator svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();

		// Hover colors.
		$this->start_controls_tab( 'tab_sub_menu_indicator_hover', [ 'label' => esc_html__( 'Hover', 'the7mk2' ) ] );
		$this->add_control(
			'sub_menu_icon_color_hover',
			[
				'label'     => esc_html__( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .horizontal-sub-nav li:not(.act) > a:hover .submenu-indicator' => 'color: {{VALUE}};',
					'{{WRAPPER}} .horizontal-sub-nav li:not(.act) > a:hover .submenu-indicator svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();

		// Active colors.
		$this->start_controls_tab( 'tab_sub_menu_indicator_active', [ 'label' => esc_html__( 'Active', 'the7mk2' ) ] );
		$this->add_control(
			'sub_menu_icon_color_active',
			[
				'label'     => esc_html__( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .horizontal-sub-nav li.act > a .submenu-indicator' => 'color: {{VALUE}};',
					'{{WRAPPER}} .horizontal-sub-nav li.act > a .submenu-indicator svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
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
	protected function add_toogle_styles() {
		$this->start_controls_section(
			'style_toggle',
			[
				'label'     => esc_html__( 'Mob. Menu Button', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'dropdown!' => 'none',
				],
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
					'{{WRAPPER}} .horizontal-menu-wrap'   => '{{VALUE}}',
					'{{WRAPPER}} .horizontal-menu-toggle' => 'align-self: var(--justify, center)',
				],
				'prefix_class'         => 'toggle-align%s-',
				'condition'            => [
					'dropdown!' => 'none',
				],
			]
		);

		$this->add_control(
			'toggle_align_divider',
			[
				'type'      => Controls_Manager::DIVIDER,
				'condition' => [
					'dropdown!' => 'none',
				],
			]
		);

		$this->add_control(
			'toggle_text',
			[
				'label'       => esc_html__( 'Toggle Text', 'the7mk2' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Menu', 'the7mk2' ),
				'placeholder' => esc_html__( 'Menu', 'the7mk2' ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'toggle_typography',
				'selector'  => ' {{WRAPPER}} .toggle-text',
				'condition' => [
					'toggle_text!' => '',
				],
			]
		);

		$selector = '{{WRAPPER}} .horizontal-menu-toggle';

		$this->add_responsive_control(
			'toggle_icon_space',
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
					$selector => '--toggle-icon-spacing: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'toggle_icon[value]!' => '',
					'toggle_text!'        => '',
				],
			]
		);

		$this->add_control(
			'toggle_icon_align',
			[
				'label'                => esc_html__( 'Position', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => [
					'left'  => [
						'title' => esc_html__( 'Start', 'the7mk2' ),
						'icon'  => 'eicon-h-align-left',
					],
					'right' => [
						'title' => esc_html__( 'End', 'the7mk2' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'condition'            => [
					'toggle_icon[value]!' => '',
					'toggle_text!'        => '',
				],
				'selectors_dictionary' => [
					'left'  => 'order: 0; margin-right: var(--toggle-icon-spacing);',
					'right' => 'order: 2; margin-left: var(--toggle-icon-spacing);',
				],
				'selectors'            => [
					'{{WRAPPER}} .toggle-text' => ' {{VALUE}};',
				],
				'prefix_class'         => 'toggle-icon_position-',
				'default'              => is_rtl() ? 'left' : 'right',
				'toggle'               => false,
			]
		);

		$this->add_control(
			'toggle_typography_divider',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$this->add_control(
			'toggle_icon_heading',
			[
				'label' => esc_html__( 'Icon', 'the7mk2' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'toggle_icon',
			[
				'label'       => esc_html__( 'Icon to open menu', 'the7mk2' ),
				'type'        => Controls_Manager::ICONS,
				'default'     => [
					'value'   => 'fas fa-bars',
					'library' => 'fa-solid',
				],
				'skin'        => 'inline',
				'label_block' => false,
			]
		);

		$this->add_control(
			'toggle_open_icon',
			[
				'label'       => esc_html__( 'Icon to close menu', 'the7mk2' ),
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
					'{{WRAPPER}} .menu-toggle-icons'     => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .menu-toggle-icons svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
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
			'padding_toggle',
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
					$selector => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'toggle_min_width',
			[
				'label'     => esc_html__( 'Min Width', 'the7mk2' ),
				'type'      => Controls_Manager::NUMBER,
				'selectors' => [
					$selector => 'min-width: {{SIZE}}px;',
				],
			]
		);

		$this->add_responsive_control(
			'toggle_min_height',
			[
				'label'     => esc_html__( 'Min Height', 'the7mk2' ),
				'type'      => Controls_Manager::NUMBER,
				'selectors' => [
					$selector => 'min-height: {{SIZE}}px;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'toggle_border',
				'selector' => $selector,
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
					$selector => 'border-radius: {{SIZE}}{{UNIT}}',
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

		$this->start_controls_tabs( 'tabs_toggle_style' );

		// Normal colors.
		$this->start_controls_tab( 'tab_toggle_style_normal', [ 'label' => esc_html__( 'Normal', 'the7mk2' ) ] );
		$this->add_toggle_button_color_controls( '' );
		$this->end_controls_tab();

		// Hover colors.
		$this->start_controls_tab( 'tab_toggle_style_hover', [ 'label' => esc_html__( 'Hover', 'the7mk2' ) ] );
		$this->add_toggle_button_color_controls( '_hover' );
		$this->end_controls_tab();

		// Active colors.
		$this->start_controls_tab( 'tab_toggle_style_active', [ 'label' => esc_html__( 'Active', 'the7mk2' ) ] );
		$this->add_toggle_button_color_controls( '_active' );
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'tabs_toggle_sticky',
			[
				'label'       => esc_html__( 'Change Colors', 'the7mk2' ),
				'type'        => Controls_Manager::SWITCHER,
				'separator'   => 'before',
				'description' => esc_html__( 'When “Sticky” and “Change Styles when Sticky” are ON for the parent section.', 'the7mk2' ),
			]
		);

		$this->start_controls_tabs(
			'tabs_toggle_sticky_style',
			[
				'condition' => [
					'tabs_toggle_sticky!' => '',
				],
			]
		);

		// Normal colors.
		$this->start_controls_tab( 'tab_toggle_style_sticky_normal', [ 'label' => esc_html__( 'Normal', 'the7mk2' ) ] );
		$this->add_toggle_button_color_controls( '', true );
		$this->end_controls_tab();

		// Hover colors.
		$this->start_controls_tab( 'tab_toggle_style_sticky_hover', [ 'label' => esc_html__( 'Hover', 'the7mk2' ) ] );
		$this->add_toggle_button_color_controls( '_hover', true );
		$this->end_controls_tab();

		// Active colors.
		$this->start_controls_tab( 'tab_toggle_style_sticky_active', [ 'label' => esc_html__( 'Active', 'the7mk2' ) ] );
		$this->add_toggle_button_color_controls( '_active', true );
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
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
			$selector_menu = '{{WRAPPER}} .dt-nav-menu-horizontal li.depth-0 > a';
			$selector_icon = '{{WRAPPER}} .dt-nav-menu-horizontal li.menu-item.depth-0 > a .menu-item-text > i';
			$selector_img  = '{{WRAPPER}} .dt-nav-menu-horizontal li.menu-item.depth-0 > a .menu-item-text > img, {{WRAPPER}} .dt-nav-menu-horizontal li.menu-item.depth-0 > a .menu-item-text > svg';
		} else {
			$selector_menu = '{{WRAPPER}} .dt-nav-menu-horizontal--main .horizontal-sub-nav ';
			$selector_icon = '{{WRAPPER}} .horizontal-sub-nav li.menu-item > a .menu-item-text > i';
			$selector_img  = '{{WRAPPER}} .horizontal-sub-nav li.menu-item > a .menu-item-text > img, {{WRAPPER}} .horizontal-sub-nav li.menu-item > a .menu-item-text > svg';
		}

		$this->start_controls_section(
			$prefix . 'section_icon_style',
			[
				'label'      => $section_label,
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => $this->generate_conditions( 'icons_visible', '==', 'enable' ),
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
							'icon-margin'        => '0 0 var(--icon-column-gap) 0',
							'icon-grid-template' => '" icon " " header " " subtitle "',
							'icon-grid-columns'  => '1fr',
							'column-gap'         => '0px',
							'row-gap'            => 'var(--icon-column-gap)',
						]
					),
					'right' => $this->combine_to_css_vars_definition_string(
						[
							'icon-margin'        => '0 0 0 var(--icon-column-gap)',
							'icon-grid-template' => '" before icon " " header icon " " subtitle icon " " empty icon " ',
							'icon-grid-columns'  => 'max-content max(var(--icon-column-width, 1em), max-content)',
							'column-gap'         => 'var(--icon-column-gap)',
							'row-gap'            => '0px',
						]
					),
					'left'  => $this->combine_to_css_vars_definition_string(
						[
							'icon-margin'        => '0 var(--icon-column-gap) 0 0',
							'icon-grid-template' => '" icon before" " icon header " " icon subtitle " " icon empty"',
							'icon-grid-columns'  => ' max(var(--icon-column-width, 1em), max-content) max-content',
							'column-gap'         => 'var(--icon-column-gap)',
							'row-gap'            => '0px',
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

		$this->start_controls_tabs( $prefix . 'icon_tabs_style' );
		$this->add_icon_title_states_controls( 'normal_', esc_html__( 'Normal', 'the7mk2' ), false, $menu_deph );
		$this->add_icon_title_states_controls( 'hover_', esc_html__( 'Hover', 'the7mk2' ), false, $menu_deph );
		$this->add_icon_title_states_controls( 'active_', esc_html__( 'Active', 'the7mk2' ), false, $menu_deph );
		$this->end_controls_tabs();

		// Sticky styles.
		if ( $menu_deph === 0 ) {
			$this->add_control(
				$prefix . 'icon_color_sticky_switch',
				[
					'label'       => esc_html__( 'Change Colors', 'the7mk2' ),
					'type'        => Controls_Manager::SWITCHER,
					'separator'   => 'before',
					'description' => esc_html__( 'When “Sticky” and “Change Styles when Sticky” are ON for the parent section.', 'the7mk2' ),
				]
			);

			$this->start_controls_tabs(
				$prefix . 'tabs_menu_icons_sticky_style',
				[
					'condition' => [
						$prefix . 'icon_color_sticky_switch!' => '',
					],
				]
			);
			$this->add_icon_title_states_controls( 'normal_', esc_html__( 'Normal', 'the7mk2' ), true, $menu_deph );
			$this->add_icon_title_states_controls( 'hover_', esc_html__( 'Hover', 'the7mk2' ), true, $menu_deph );
			$this->add_icon_title_states_controls( 'active_', esc_html__( 'Active', 'the7mk2' ), true, $menu_deph );
			$this->end_controls_tabs();
		}

		$this->end_controls_section();
	}

	/**
	 * @param string $prefix_name Prefix.
	 * @param string $box_name    Lable.
	 * @param bool   $sticky      Is sticky.
	 * @param int    $menu_deph   Menu depth.
	 *
	 * @return void
	 */
	protected function add_icon_title_states_controls( $prefix_name, $box_name, $sticky = false, $menu_deph = 0 ) {
		$hover_sel = '';
		if ( $prefix_name === 'hover_' ) {
			$hover_sel = ':hover';
		}

		$act_sel = '';
		if ( $prefix_name === 'active_' ) {
			$act_sel = '.act';
		}

		$wrapper     = '{{WRAPPER}}';
		$prefix_name = 'deph_' . $menu_deph . '_' . $prefix_name;
		if ( $sticky ) {
			$prefix_name .= 'sticky_';
			$wrapper      = self::STICKY_WRAPPER;
		}

		if ( $menu_deph === 0 ) {
			$icon_selector = $wrapper . ' .dt-nav-menu-horizontal li.menu-item.depth-0' . $act_sel . ' > a' . $hover_sel . ' .item-content > .menu-item-icon';
		} else {
			$icon_selector = $wrapper . ' .dt-nav-menu-horizontal--main .horizontal-sub-nav li.menu-item' . $act_sel . ' > a' . $hover_sel . ' .item-content >  .menu-item-icon';
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
	 * @param string $type    Controls group type.
	 * @param string $prefix  Controls prefix.
	 * @param string $wrapper Selectors wrapper.
	 *
	 * @return void
	 */
	private function add_item_color_controls( $type = '', $prefix = '', $wrapper = '{{WRAPPER}}' ) {
		if ( $type === 'hover' ) {
			$selectors = [
				"$wrapper .dt-nav-menu-horizontal > li:not(.act) > a:hover",
				"$wrapper .dt-nav-menu-horizontal > li.parent-clicked > a",
			];
		} elseif ( $type === 'active' ) {
			$selectors = [
				"$wrapper .dt-nav-menu-horizontal > li.act > a",
			];
		} else {
			$selectors = [
				"$wrapper .dt-nav-menu-horizontal > li > a",
			];

			$type = '';
		}

		$item_prefix = '';
		if ( $prefix ) {
			$item_prefix .= "_{$prefix}";
		}
		if ( $type ) {
			$item_prefix .= "_{$type}";
		}

		$this->add_control(
			'color_menu_item' . $item_prefix,
			[
				'label'     => esc_html__( 'Text Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => $this->give_me_megaselectors(
					$selectors,
					[
						''     => 'color: {{VALUE}}',
						' svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
					]
				),
			]
		);

		$this->add_control(
			'bg_menu_item' . $item_prefix,
			[
				'label'     => esc_html__( 'Background color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => $this->give_me_megaselectors( $selectors, 'background-color: {{VALUE}}' ),
			]
		);

		$this->add_control(
			'border_menu_item' . $item_prefix,
			[
				'label'     => esc_html__( 'Border color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => $this->give_me_megaselectors( $selectors, 'border-color: {{VALUE}}' ),
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'menu_item_shadow' . $item_prefix,
				'label'    => esc_html__( 'Shadow', 'the7mk2' ),
				'selector' => implode( ', ', $selectors ),
			]
		);
	}

	/**
	 * @param string $prefix Control name prefix.
	 * @param bool   $is_sticky Set true for sricky control set.
	 */
	private function add_sub_menu_item_color_controls( $prefix, $is_sticky = false ) {
		$wrapper = '{{WRAPPER}}';

		$sticky_prefix = '';
		if ( $is_sticky ) {
			$wrapper       = self::STICKY_WRAPPER;
			$sticky_prefix = 'sticky_';
		}

		$css_prefix   = 'li';
		$hover_prefix = '';
		switch ( $prefix ) {
			case '_hover':
				$css_prefix   = '> li:not(.act)';
				$hover_prefix = ':hover';
				break;
			case '_active':
				$css_prefix = '> li.act';
				break;
		}

		$selectors[ $wrapper . ' .horizontal-sub-nav ' . $css_prefix . ' > a' . $hover_prefix ] = 'color: {{VALUE}}';
		if ( empty( $prefix ) ) {
			$selectors[ $wrapper ] = '--submenu-item-color: {{VALUE}}';
		}

		$this->add_control(
			$sticky_prefix . 'color_sub_menu_item' . $prefix,
			[
				'label'     => esc_html__( 'Text Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => $selectors,
			]
		);

		$this->add_control(
			$sticky_prefix . 'bg_sub_menu_item' . $prefix,
			[
				'label'     => esc_html__( 'Background color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'selectors' => [
					$wrapper . ' .horizontal-sub-nav ' . $css_prefix . ' > a' . $hover_prefix => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			$sticky_prefix . 'border_sub_menu_item' . $prefix,
			[
				'label'     => esc_html__( 'Border color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					$wrapper . ' .horizontal-sub-nav ' . $css_prefix . ' > a' . $hover_prefix  => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => $sticky_prefix . 'sub_menu_item_shadow' . $prefix,
				'label'    => esc_html__( 'Shadow', 'the7mk2' ),
				'selector' => $wrapper . ' .horizontal-sub-nav ' . $css_prefix . ' > a' . $hover_prefix,
			]
		);
	}

	/**
	 * @param string $prefix Control name prefix.
	 * @param bool   $sticky Is sticky control.
	 */
	private function add_toggle_button_color_controls( $prefix, $sticky = false ) {
		$css_prefix       = '';
		$css_hover_prefix = '';
		switch ( $prefix ) {
			case '_hover':
				$css_prefix       = ':hover';
				$css_hover_prefix = '.no-touchevents ';
				break;
			case '_active':
				$css_prefix = '.elementor-active';
				break;
		}

		if ( $sticky ) {
			$selector = $css_hover_prefix . self::STICKY_WRAPPER . ' .horizontal-menu-toggle' . $css_prefix;
		} else {
			$selector = $css_hover_prefix . '{{WRAPPER}} .horizontal-menu-toggle' . $css_prefix;
		}

		$sticky_prefix = '';
		if ( $sticky ) {
			$sticky_prefix = '_sticky_';
		}

		$this->add_control(
			'toggle_color' . $sticky_prefix . $prefix,
			[
				'label'     => esc_html__( 'Text & icon color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$selector          => 'color: {{VALUE}}',
					$selector . ' svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
			]
		);

		$fields_options = [
			'background' => [
				'label' => esc_html__( 'Background', 'the7mk2' ),
			],
		];

		if ( ! empty( $prefix ) ) {
			$fields_options['color'] = [
				'selectors' => [
					'{{SELECTOR}}' => 'background: {{VALUE}}',
				],
			];
		}

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'           => 'toggle_background_color' . $sticky_prefix . $prefix,
				'types'          => [ 'classic', 'gradient' ],
				'exclude'        => [ 'image' ],
				'fields_options' => $fields_options,
				'selector'       => $selector,
			]
		);

		$this->add_control(
			'toggle_border_color' . $sticky_prefix . $prefix,
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$selector => 'border-color: {{VALUE}}',
				],
				'condition' => [
					'toggle_border_border!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'toggle_shadow' . $sticky_prefix . $prefix,
				'selector' => $selector,
			]
		);
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

		$settings = $this->get_settings_for_display();

		$class     = [
			'dt-nav-menu-horizontal--main',
			'dt-nav-menu-horizontal__container',
			'justify-content-' . esc_attr( $settings['items_position'] ),
			'widget-divider-' . esc_attr( $settings['divider'] ),
		];
		$switchers = [
			'show_first_border' => 'first-item-border-hide',
			'show_last_border'  => 'last-item-border-hide',
		];
		foreach ( $switchers as $control => $class_to_add ) {
			if ( isset( $settings[ $control ] ) && $settings[ $control ] !== 'y' ) {
				$class[] = $class_to_add;
			}
		}
		$this->add_render_attribute(
			'main-menu',
			[
				'class' => $class,
			]
		);

		if ( $settings['selected_icon'] && $settings['selected_icon']['value'] === '' ) {
			$this->add_render_attribute( 'main-menu', 'class', 'indicator-off' );
		}

		echo '<div class="horizontal-menu-wrap">';

		if ( isset( $settings['dropdown'] ) && $settings['dropdown'] !== 'none' ) {
			$breakpoint_obj = Plugin::$instance->breakpoints->get_active_breakpoints( $settings['dropdown'] );
			$breakpoint     = 1024;
			if ( is_object( $breakpoint_obj ) ) {
				$breakpoint = (int) $breakpoint_obj->get_value();
			}
			if ( $settings['dropdown'] === 'desktop' ) {
				?>
				<style>
					@media screen {
						.elementor-widget-the7_horizontal-menu.elementor-widget {
							--menu-display: none;
							--mobile-display: inline-flex;
						}
					}
				</style>
				<?php
			} else {
				?>
				<style>
					@media screen and (max-width: <?php echo esc_html( $breakpoint ); ?>px) {
						.elementor-widget-the7_horizontal-menu.elementor-widget {
							--menu-display: none;
							--mobile-display: inline-flex;
						}
					}
				</style>
				<?php
			}
		}

		if ( $settings['dropdown'] !== 'none' ) {
			$this->add_render_attribute(
				'menu-toggle',
				[
					'class'         => 'horizontal-menu-toggle hidden-on-load',
					'role'          => 'button',
					'tabindex'      => '0',
					'aria-label'    => esc_html__( 'Menu Toggle', 'the7mk2' ),
					'aria-expanded' => 'false',
				]
			);

			$tag = 'div';
			if ( $settings['dropdown_type'] === 'popup' && the7_elementor_pro_is_active() ) {
				$this->add_render_attribute( 'menu-toggle', 'href', $this->get_popup_url( $settings['popup_link'] ) );
				$tag = 'a';
			}

			echo "<{$tag} " . $this->get_render_attribute_string( 'menu-toggle' ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			$this->add_render_attribute(
				'menu-toggle-icon',
				[
					'class'       => $settings['sub_icon_align'] ? $settings['sub_icon_align'] . ' menu-toggle-icons' : '',
					'aria-hidden' => 'true',
					'role'        => 'presentation',
				]
			);

			echo '<span ' . $this->get_render_attribute_string( 'menu-toggle-icon' ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			Icons_Manager::render_icon(
				$settings['toggle_icon'],
				[
					'class'       => 'open-button',
					'aria-hidden' => 'true',
				]
			);
			Icons_Manager::render_icon(
				$settings['toggle_open_icon'],
				[
					'class'       => 'icon-active',
					'aria-hidden' => 'true',
				]
			);
			echo '</span>';

			if ( $settings['toggle_text'] !== '' ) {
				echo '<span class="toggle-text">' . esc_html( $settings['toggle_text'] ) . '</span>';
			}

			echo "</{$tag}>"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		echo '<nav ' . $this->get_render_attribute_string( 'main-menu' ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<ul class="dt-nav-menu-horizontal d-flex flex-row justify-content-' . esc_attr( $settings['items_position'] ) . '">';

		// Fix "menu in menu" case by removing local hooks before template rendering.
		add_action( 'presscore_mega_menu_before_template', [ $this, 'before_template_action' ], 10, 1 );
		add_action( 'presscore_mega_menu_after_template', [ $this, 'after_template_action' ], 10, 1 );

		$mega_menu_handler = new Mega_Menu();
		$mega_menu_handler->add_hooks();
		$this->add_hooks();

		// Add the first divider if dividers are enabled.
		$items_wrap = $this->add_divider_elements_for_the_top_menu_level_filter( '', null, (object) [ 'menu_id' => $this->get_id() ], 0 );

		presscore_nav_menu(
			[
				'menu'                => $settings['menu'],
				// Prevent caching by placing unique value.
				'menu_id'             => $this->get_id(),
				'theme_location'      => 'the7_nav-menu',
				'items_wrap'          => $items_wrap . '%3$s',
				'submenu_class'       => 'horizontal-sub-nav',
				'link_before'         => '<span class="item-content">',
				'link_after'          => '</span>',
				'parent_is_clickable' => $settings['parent_is_clickable'],
			]
		);

		$this->remove_hooks();
		$mega_menu_handler->remove_hooks();

		remove_action( 'presscore_mega_menu_before_template', [ $this, 'before_template_action' ] );
		remove_action( 'presscore_mega_menu_after_template', [ $this, 'after_template_action' ] );

		echo '</ul></nav></div>';
	}

	/**
	 * @param stdClass $args Menu arguments.
	 *
	 * @return void
	 */
	public function before_template_action( $args ) {
		if ( $this->is_local_menu_handler( $args ) ) {
			$this->remove_hooks();
		}
	}

	/**
	 * @param stdClass $args Menu arguments.
	 *
	 * @return void
	 */
	public function after_template_action( $args ) {
		if ( $this->is_local_menu_handler( $args ) ) {
			$this->add_hooks();
		}
	}

	/**
	 * @return void
	 */
	public function add_hooks() {
		add_filter( 'presscore_nav_menu_item_elementor', [ $this, 'add_submenu_icons_filter' ], 20, 7 );
		add_filter( 'presscore_nav_menu_el_after', [ $this, 'add_divider_elements_for_the_top_menu_level_filter' ], 20, 4 );
		add_filter( 'presscore_nav_menu_link_before', [ $this, 'menu_link_before_filter' ], 10, 3 );
	}

	/**
	 * @return void
	 */
	public function remove_hooks() {
		remove_filter( 'presscore_nav_menu_link_before', [ $this, 'menu_link_before_filter' ] );
		remove_filter( 'presscore_nav_menu_item_elementor', [ $this, 'add_submenu_icons_filter' ], 20 );
		remove_filter( 'presscore_nav_menu_el_after', [ $this, 'add_divider_elements_for_the_top_menu_level_filter' ], 20 );
	}

	/**
	 * @param string   $menu_item   Menu item code.
	 * @param string   $title       Menu item title.
	 * @param string   $description Menu item description.
	 * @param WP_Post  $item        Menu item data object.
	 * @param int      $depth       Menu item depth.
	 * @param stdClass $args        An object of wp_nav_menu() arguments.
	 * @param string   $icon        Menu item icon.
	 *
	 * @return string
	 */
	public function add_submenu_icons_filter( $menu_item, $title, $description, $item, $depth, $args, $icon ) {
		if ( ! $this->is_local_menu_handler( $args ) ) {
			return $menu_item;
		}

		$settings = $this->get_settings_for_display();

		$sub_menu_icon_html = $this->get_elementor_icon_html(
			( $depth === 0 ? $settings['selected_icon'] : $settings['sub_icon'] ),
			'i',
			[
				'class' => 'desktop-menu-icon',
			]
		);

		$dropdown_icon_html = '';
		if ( $settings['dropdown'] !== 'none' ) {
			$dropdown_icon_html .= $this->get_elementor_icon_html(
				$settings['dropdown_icon'],
				'i',
				[
					'class' => 'mobile-menu-icon',
				]
			);

			$dropdown_icon_html .= $this->get_elementor_icon_html(
				$settings['dropdown_icon_act'],
				'i',
				[
					'class' => 'mobile-act-icon',
				]
			);
		}
		$icon_class = '';
		if ( ! empty( $icon ) ) {
			$icon_class = 'menu-item-icon';
		}
		$submenu_html = '<span class="submenu-indicator" >' . $sub_menu_icon_html . '<span class="submenu-mob-indicator" >' . $dropdown_icon_html . '</span></span>';

		return '<span class="menu-item-text ' . $icon_class . ' ">' . $icon . '<span class="menu-text">' . $title . '</span>' . $description . '</span>' . $submenu_html;
	}

	/**
	 * @param  string       $after_menu_item  A code after an item.
	 * @param  WP_Post|null $item  Page data object. Not used.
	 * @param  stdClass     $args  An object of wp_nav_menu() arguments.
	 * @param  int          $depth  Depth of page. Not Used.
	 *
	 * @return string
	 */
	public function add_divider_elements_for_the_top_menu_level_filter( $after_menu_item, $item, $args, $depth ) {
		if ( ! $this->is_local_menu_handler( $args ) ) {
			return $after_menu_item;
		}

		if ( $depth === 0 && $this->get_settings_for_display( 'divider' ) === 'yes' ) {
			$after_menu_item .= '<li class="item-divider" aria-hidden="true"></li>';
		}

		return $after_menu_item;
	}

	/**
	 * @param  string   $link_before  The part before link.
	 * @param  WP_Post  $item         Menu item.
	 * @param  stdClass $args         Menu args.
	 *
	 * @return string
	 */
	public function menu_link_before_filter( $link_before, $item, $args ) {
		if ( ! $this->is_local_menu_handler( $args ) ) {
			return $link_before;
		}

		if ( $item->description ) {
			$link_before = str_replace( 'item-content', 'item-content with-description', $link_before );
		}

		return $link_before;
	}

	/**
	 * @param stdClass $args Menu args.
	 *
	 * @return bool
	 */
	protected function is_local_menu_handler( $args ) {
		return isset( $args->menu_id ) && $args->menu_id === $this->get_id();
	}

	/**
	 * Render widget plain content.
	 *
	 * No plain content here.
	 */
	public function render_plain_content() {
	}

	/**
	 * @return array
	 */
	protected function get_popups_list() {
		$popups_query = new \WP_Query(
			[
				'post_type'      => Source_Local::CPT,
				'post_status'    => 'publish',
				'posts_per_page' => - 1,
				'meta_key'       => '_elementor_template_type',
				'meta_value'     => PopupModule::DOCUMENT_TYPE,
			]
		);

		return wp_list_pluck( $popups_query->posts, 'post_title', 'ID' );
	}

	/**
	 * Returns mobile breakpoint options.
	 *
	 * @return array
	 */
	protected function get_mobile_breakpoint_options(): array {
		$active_breakpoints = Plugin::$instance->breakpoints->get_active_breakpoints();
		if ( $active_breakpoints ) {
			unset( $active_breakpoints['widescreen'] );
			$desktop_breakpoint        = 0;
			$mobile_breakpoint_options = [];
			foreach ( $active_breakpoints as $slug => $breakpoint ) {
				$mobile_breakpoint_options[ $slug ] = esc_html( sprintf( '%1$s (< %2$dpx)', $breakpoint->get_label(), $breakpoint->get_value() ) );
				$desktop_breakpoint                 = $breakpoint->get_value();
			}

			$mobile_breakpoint_options['desktop'] = esc_html( 'Desktop (> ' . $desktop_breakpoint . 'px)' );
			$mobile_breakpoint_options['none']    = esc_html__( 'None', 'the7mk2' );
		} else {
			// Fallback. Potentially useless, but for safety.
			$breakpoints               = [
				'md' => 768,
				'lg' => 1024,
			];
			$mobile_breakpoint_options = [
				/* translators: %d: Breakpoint number. */
				'mobile'  => sprintf( esc_html__( 'Mobile (< %dpx)', 'the7mk2' ), $breakpoints['md'] ),
				/* translators: %d: Breakpoint number. */
				'tablet'  => sprintf( esc_html__( 'Tablet (< %dpx)', 'the7mk2' ), $breakpoints['lg'] ),
				/* translators: %d: Breakpoint number. */
				'desktop' => sprintf( esc_html__( 'Desktop (> %dpx)', 'the7mk2' ), $breakpoints['lg'] ),
				'none'    => esc_html__( 'None', 'the7mk2' ),
			];
		}
		return $mobile_breakpoint_options;
	}
}
