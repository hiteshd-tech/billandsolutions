<?php
/**
 * The7 breadcrumb widget for Elementor.
 *
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widgets;

use Elementor\Icons_Manager;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;


defined( 'ABSPATH' ) || exit;

class Breadcrumbs extends The7_Elementor_Widget_Base {

	public function get_name() {
		return 'the7-breadcrumb';
	}

	protected function the7_title() {
		return esc_html__( 'Breadcrumbs', 'the7mk2' );
	}

	protected function the7_icon() {
		return 'eicon-navigation-horizontal';
	}

	protected function the7_keywords() {
		return [ 'breadcrumbs' ];
	}

	public function get_style_depends() {
		the7_register_style( 'the7-widget', PRESSCORE_THEME_URI . '/css/compatibility/elementor/the7-widget' );

		return [ 'the7-widget' ];
	}
	public function get_script_depends() {
		return [ $this->get_name() ];
	}
	/**
	 * Register widget assets.
	 */
	protected function register_assets() {
		the7_register_script_in_footer(
			$this->get_name(),
			THE7_ELEMENTOR_JS_URI . '/the7-breadcrumbs.js',
			[ 'jquery' ]
		);
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_breadcrumb_content',
			[
				'label' => esc_html__( 'Content', 'the7mk2' ),
			]
		);
		$this->add_control(
			'separator',
			[
				'label'   => esc_html__( 'Separator Between', 'the7mk2' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'text' => [
						'title' => esc_html__( 'Text', 'the7mk2' ),
						'icon'  => 'eicon-font',
					],
					'icon' => [
						'title' => esc_html__( 'Icon', 'the7mk2' ),
						'icon'  => 'eicon-star',
					],
				],
				'default' => 'text',
			]
		);
		$this->add_control(
			'meta_separator',
			[
				'label'     => esc_html__( 'Text', 'the7mk2' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '/',
				'selectors' => [
					'{{WRAPPER}} .breadcrumbs li:not(.first):before' => 'content: "{{VALUE}}"',
				],
				'condition' => [
					'separator' => 'text',
				],
			]
		);
		$this->add_control(
			'icon_separator',
			[
				'label'            => esc_html__( 'Icon', 'the7mk2' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default'          => [
					'value'   => 'fas fa-star',
					'library' => 'fa-solid',
				],
				'selectors'        => [
					'{{WRAPPER}} .breadcrumbs li:not(:first-child):before' => 'display: none',
				],
				'condition'        => [
					'separator' => 'icon',
				],
				'render_type'      => 'template',
			]
		);

		$this->add_control(
			'show_act_item',
			[
				'label'        => esc_html__( 'Current item', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the7mk2' ),
				'label_off'    => esc_html__( 'Hide', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
				'selectors'    => [
					'{{WRAPPER}} .breadcrumbs li.current:last-child' => 'display: inline-flex',
				],
				// 'render_type'          => 'template',
			]
		);
		$this->add_control(
			'split_items',
			[
				'label'        => esc_html__( 'Split into lines', 'the7mk2' ),
				'description'  => esc_html__( 'If there’s not enough space.', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'the7mk2' ),
				'label_off'    => esc_html__( 'No', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
				'empty_value'  => 'n',
				'prefix_class' => 'split-breadcrumbs-',
				'selectors'    => [
					'{{WRAPPER}} .breadcrumbs' => 'flex-flow: wrap',
				],
				'render_type'  => 'template',
			]
		);
		$this->add_control(
			'title_words_limit',
			[
				'label'       => esc_html__( 'Max number of letters in page title', 'the7mk2' ),
				'description' => esc_html__( 'Leave empty to show the entire title.', 'the7mk2' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => '',
				'min'         => 1,
				'max'         => 100,
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_breadcrumb_style',
			[
				'label' => esc_html__( 'Style', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
			'alignment',
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
				'selectors_dictionary' => [
					'left'   => 'flex-start',
					'center' => 'center',
					'right'  => 'flex-end',
				],
				'selectors'            => [
					'{{WRAPPER}} .breadcrumbs' => 'justify-content: {{VALUE}}',
				],
			]
		);
		$this->add_responsive_control(
			'min_height',
			[
				'label'     => esc_html__( 'Min Height', 'the7mk2' ),
				'type'      => Controls_Manager::NUMBER,
				'selectors' => [
					'{{WRAPPER}} .breadcrumbs' => 'min-height: {{SIZE}}px;',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'text_typography',
				'selector' => '{{WRAPPER}} .breadcrumbs',
			]
		);
		$this->add_control(
			'text_color',
			[
				'label'     => esc_html__( 'Text Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .breadcrumbs' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'the7-link-heading',
			[
				'type'      => Controls_Manager::HEADING,
				'label'     => esc_html__( 'Links', 'the7mk2' ),
				'separator' => 'before',
			]
		);

		$this->start_controls_tabs( 'tabs_link_style' );

		$this->start_controls_tab(
			'the7_tab_link_normal',
			[
				'label' => esc_html__( 'Normal', 'the7mk2' ),
			]
		);
		$this->add_control(
			'link_color',
			[
				'label'     => esc_html__( 'Link Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .breadcrumbs li > a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'the7_link_decorator',
			[
				'label'     => esc_html__( 'Underlined Links', 'the7mk2' ),
				'type'      => Controls_Manager::SWITCHER,
				'selectors' => [
					'{{WRAPPER}} .breadcrumbs li > a' => 'text-decoration: underline;',
					'{{WRAPPER}} .breadcrumbs li > a:hover' => 'text-decoration: none;',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'the7_tab_link_hover',
			[
				'label' => esc_html__( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			'link_color_hover',
			[
				'label'     => esc_html__( 'Link Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .breadcrumbs li > a:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'the7_link_hover_decorator',
			[
				'label'     => esc_html__( 'Underlined Links', 'the7mk2' ),
				'type'      => Controls_Manager::SWITCHER,
				'dynamic'   => [],
				'selectors' => [
					'{{WRAPPER}} .breadcrumbs li > a:hover' => 'text-decoration: underline;',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'the7-separator-heading',
			[
				'type'      => Controls_Manager::HEADING,
				'label'     => esc_html__( 'Separator', 'the7mk2' ),
				'separator' => 'before',
			]
		);

		$this->add_control(
			'divider_color',
			[
				'label'     => esc_html__( 'Separator Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .breadcrumbs li:not(:first-child):before, {{WRAPPER}} .breadcrumbs li:not(:first-child) i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .breadcrumbs li:not(:first-child) svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
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
					'{{WRAPPER}} .children' => '--sub-icon-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .breadcrumbs li:not(:first-child):before, {{WRAPPER}} .breadcrumbs li:not(:first-child) i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .breadcrumbs li:not(:first-child) svg' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'divider_spacing',
			[
				'label'      => esc_html__( 'Separator Spacing', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 5,
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
					'{{WRAPPER}} .breadcrumbs li:not(:first-child):before, {{WRAPPER}} .breadcrumbs li:not(:first-child) i, {{WRAPPER}} .breadcrumbs li:not(:first-child) svg' => 'margin: 0 {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings       = $this->get_settings_for_display();
		$icon_separator = '';
		if ( $settings['icon_separator'] !== '' && $settings['separator'] == 'icon' ) {
			$icon_separator = $this->get_elementor_icon_html( $settings['icon_separator'] );
		}

		$default_args = [
			'text'              => [
				'home'     => esc_html__( 'Home', 'the7mk2' ),
				'category' => esc_html__( 'Category "%s"', 'the7mk2' ),
				'search'   => esc_html__( 'Results for "%s"', 'the7mk2' ),
				'tag'      => esc_html__( 'Entries tagged with "%s"', 'the7mk2' ),
				'author'   => esc_html__( 'Article author %s', 'the7mk2' ),
				'404'      => esc_html__( 'Error 404', 'the7mk2' ),
			],
			'showCurrent'       => true,
			'showOnHome'        => true,
			'delimiter'         => '',
			'before'            => '<li class="current" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">' . $icon_separator,
			'after'             => '</li>',
			'linkBefore'        => '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">' . $icon_separator,
			'linkAfter'         => '</li>',
			'linkAttr'          => ' itemprop="item"',
			'beforeBreadcrumbs' => '',
			'afterBreadcrumbs'  => '',
			'listAttr'          => ' class="breadcrumbs text-small rcrumbs"',
			// It's actually max character count, not words.
			'itemMaxChrCount'   => $settings['title_words_limit'],
		];

		add_filter( 'presscore_get_breadcrumbs-current_words_num', [ $this, 'set_default_max_words_limit' ], 9 );
		$breadcrumbs = presscore_get_breadcrumbs( $default_args );
		remove_filter( 'presscore_get_breadcrumbs-current_words_num', [ $this, 'set_default_max_words_limit' ], 9 );

		if ( $breadcrumbs ) {
			echo $breadcrumbs;
		}
	}

	/**
	 * @return int
	 */
	public function set_default_max_words_limit() {
		return 55;
	}
}
