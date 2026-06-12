<?php

namespace The7\Mods\Compatibility\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Core\Breakpoints\Manager as Breakpoints;
use Elementor\Icons_Manager;
use Elementor\Plugin;
use Elementor\Plugin as Elementor;
use Elementor\Repeater;
use Elementor\TemplateLibrary\Source_Local;
use The7\Mods\Compatibility\Elementor\Modules\Slider\Module as Slider_Module;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;
use The7\Mods\Compatibility\Elementor\Widgets\Skins\Slider\Skin_Normal;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Bullets;
use Elementor\Group_Control_Box_Shadow;
use The7_Elementor_Compatibility;

/**
 * Slider widget class.
 */
class Slider extends The7_Elementor_Widget_Base {

    const WIDGET_NAME = 'the7-slider';
	const AUTOPLAY_DEFAULT = 'yes';
	const SLIDES_PER_VIEW_DEFAULT = '1';

	/**
	 * @return string[]
	 */
	public function get_style_depends() {
		return [ 'the7-slider-widget' ];
	}

	/**
	 * @return string[]
	 */
	public function get_script_depends() {
		return [ 'the7-slider' ];
	}

	/**
	 * @return string|void
	 */
	protected function the7_title() {
		return esc_html__( 'Slider', 'the7mk2' );
	}

	/**
	 * @return string
	 */
	protected function the7_icon() {
		return 'eicon-carousel-loop';
	}

	/**
	 * @return string[]
	 */
	protected function the7_keywords() {
		return [ 'slides', 'carousel', 'image', 'slider', 'carousel' ];
	}


	/**
	 * @return string
	 */
	public function get_name() {
		return self::WIDGET_NAME;
	}

	protected function register_controls() {
		// Content Tab.
		$this->add_content_controls();
		$this->add_query_content_controls();
		$this->add_scrolling_content_controls();
		$this->add_layout_content_controls();

		$this->add_arrows_content_controls();
		$this->add_bullets_content_controls();

		// styles tab
		$this->add_arrows_style_controls();
		$this->template( Bullets::class )->add_style_controls( null, null );

		$this->update_control(
			'bullets_v_position',
			[
				'selectors_dictionary' => [
					'top'    => 'top: var(--bullet-v-offset); bottom: auto; --bullet-translate-y:0;',
					'center' => 'top: calc(50% + var(--bullet-v-offset)); bottom: auto; --bullet-translate-y:-50%;',
					'bottom' => 'top: calc(100% + var(--bullet-v-offset)); bottom: auto; --bullet-translate-y:-100%;',
				],
				'selectors'            => [
					"{{WRAPPER}} .elementor-slides-wrapper > .swiper-pagination" => '{{VALUE}}',
				],
			]
		);
		$this->update_control(
			'bullets_h_position',
			[
				'selectors_dictionary' => [
					'left'   => 'left: var(--bullet-h-offset); --bullet-translate-x:0;',
					'center' => 'left: calc(50% + var(--bullet-h-offset)); --bullet-translate-x:-50%;',
					'right'  => 'left: calc(100% - var(--bullet-h-offset)); --bullet-translate-x:-100%;',
				],
				'selectors'            => [
					"{{WRAPPER}} .elementor-slides-wrapper > .swiper-pagination" => '{{VALUE}}',
				],
			]
		);
	}

	protected function add_content_controls() {
		//'section_layout' name is important for createTemplate js function
		$this->start_controls_section( 'section_layout', [
			'label' => esc_html__( 'Content', 'the7mk2' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
		] );
		$this->add_control( 'slider_wrap_helper', [
			'type'         => Controls_Manager::HIDDEN,
			'default'      => 'elementor-widget-the7-slider-common owl-carousel elementor-widget-loop-the7-slider',
			'prefix_class' => '',
		] );

		$this->add_slide_content_controls();
		$this->add_slider_height_controls();

		$this->end_controls_section();
	}

	protected function add_slide_content_controls() {
		$slider_module = The7_Elementor_Compatibility::instance()->modules->get_modules( 'slider' );
		$library_ids = $slider_module->get_posts();
		$repeater = new Repeater();

		$repeater->add_control( 'slide_name', [
			'label'   => esc_html__( 'Name', 'the7mk2' ),
			'type'    => Controls_Manager::TEXT,
			'default' => esc_html__( 'Slide name', 'the7mk2' ),
		] );

		$repeater->add_control( 'slide_id', [
			'label'       => esc_html__( 'Select Slide', 'the7mk2' ),
			'type'        => Controls_Manager::SELECT,
			'options'     => $library_ids,
			'label_block' => false,
		] );

		$this->add_control( 'slides_heading', [
			'label' => esc_html__( 'Slides', 'the7mk2' ),
			'type'  => Controls_Manager::HEADING,
		] );

		$this->add_control( 'slides_heading_description', [
			'type'            => Controls_Manager::RAW_HTML,
			'raw'             => sprintf( __( 'Add/Edit individual slides <a href="%s" target="_blank">here</a>', 'the7mk2' ), admin_url( Source_Local::ADMIN_MENU_SLUG . '&elementor_library_type=' . Slider_Module::DOCUMENT_TYPE ) ),
			'separator'       => 'none',
			'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
		] );

		$this->add_control( 'slides', [
			'type'         => Controls_Manager::REPEATER,
			'show_label'   => false,
			'fields'       => $repeater->get_controls(),
			'title_field'  => '{{{ slide_name }}}',
			'item_actions' => [
				'add'       => true,
				'duplicate' => true,
				'remove'    => true,
			],
		] );
	}

	protected function add_slider_height_controls() {
		$this->add_control( 'slides_height_heading', [
			'label'     => esc_html__( 'Height', 'the7mk2' ),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		] );

		$this->add_control( 'slides_height_heading_description', [
			'type'            => Controls_Manager::RAW_HTML,
			'raw'             => esc_html__( 'Leave both fields empty for automatic height', 'the7mk2' ),
			'separator'       => 'none',
			'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
		] );

		$this->add_responsive_control( 'slides_min_height', [
			'label'      => esc_html__( 'Min Height', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'range'      => [
				'px' => [
					'min' => 100,
					'max' => 1000,
				],
				'vh' => [
					'min' => 10,
					'max' => 100,
				],
			],
			'size_units' => [ 'px', 'vh', 'em',  'custom' ],
			'selectors'  => [
				'{{WRAPPER}} .the7-swiper-slide .the7-slide-content > .elementor-section-wrap' => 'min-height: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->add_responsive_control( 'slides_height', [
			'label'      => esc_html__( 'Height', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'range'      => [
				'px' => [
					'min' => 100,
					'max' => 1000,
				],
				'vh' => [
					'min' => 10,
					'max' => 100,
				],
			],
			'size_units' => [ 'px', 'vh', 'em',  'custom' ],
			'selectors'  => [
				'{{WRAPPER}}' => '--slide-height: {{SIZE}}{{UNIT}};',
			],
		] );
	}

	protected function add_query_content_controls() {
	}

	protected function add_scrolling_content_controls() {
		$this->start_controls_section( 'scrolling_section', [
			'label' => esc_html__( 'Scrolling', 'the7mk2' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
		] );

		$this->add_control( 'infinite', [
			'label'              => esc_html__( 'Infinite Loop', 'the7mk2' ),
			'type'               => Controls_Manager::SWITCHER,
			'default'            => 'yes',
			'frontend_available' => true,
		] );

		$this->add_control( 'transition', [
			'label'              => esc_html__( 'Transition', 'the7mk2' ),
			'type'               => Controls_Manager::SELECT,
			'default'            => 'slide',
			'options'            => [
				'slide' => esc_html__( 'Slide', 'the7mk2' ),
				'fade'  => esc_html__( 'Fade', 'the7mk2' ),
			],
			'frontend_available' => true,
		] );

		$this->add_control( 'slides_to_scroll', [
			'label'              => esc_html__( 'Scroll Mode', 'the7mk2' ),
			'type'               => Controls_Manager::SELECT,
			'default'            => 'single',
			'options'            => [
				'single' => esc_html__( 'One slide at a time', 'the7mk2' ),
				'all'    => esc_html__( 'All slides', 'the7mk2' ),
			],
			'frontend_available' => true,
			'render_type'        => 'none',
			'condition'          => [
				'transition' => 'slide',
			],
		] );

		$this->add_control( 'transition_speed', [
			'label'              => esc_html__( 'Transition Speed', 'the7mk2' ) . '(ms)',
			'type'               => Controls_Manager::NUMBER,
			'default'            => 500,
			'frontend_available' => true,
			'selectors'          => [
				'{{WRAPPER}}' => '--slide-transition-speed:{{VALUE}}ms',
			],
		] );

		$this->add_control( 'autoplay', [
			'label'              => esc_html__( 'Autoplay', 'the7mk2' ),
			'type'               => Controls_Manager::SWITCHER,
			'default'            => static::AUTOPLAY_DEFAULT,
			'render_type'        => 'none',
			'frontend_available' => true,
		] );

		$this->add_control( 'pause_on_hover', [
			'label'              => esc_html__( 'Pause on Hover', 'the7mk2' ),
			'type'               => Controls_Manager::SWITCHER,
			'default'            => 'yes',
			'render_type'        => 'none',
			'frontend_available' => true,
			'condition'          => [
				'autoplay' => 'yes',
			],
		] );

		$this->add_control( 'autoplay_speed', [
			'label'              => esc_html__( 'Autoplay Speed (ms)', 'the7mk2' ),
			'type'               => Controls_Manager::NUMBER,
			'default'            => 5000,
			'condition'          => [
				'autoplay' => 'yes',
			],
			'render_type'        => 'none',
			'selectors'          => [
				'{{WRAPPER}} .the7-swiper-slide' => 'transition-duration: calc({{VALUE}}ms*1.2)',
			],
			'frontend_available' => true,
		] );
		$this->end_controls_section();
	}

	protected function add_layout_content_controls() {
		$this->start_controls_section( 'layout_section', [
			'label'     => esc_html__( 'Columns Layout', 'the7mk2' ),
			'tab'       => Controls_Manager::TAB_CONTENT,
			'condition' => [
				'transition' => 'slide',
			],
		] );

		$slides_per_view = range( 1, 12 );
		$slides_per_view = array_combine( $slides_per_view, $slides_per_view );

		if ( ! Plugin::$instance->breakpoints->get_active_breakpoints( Breakpoints::BREAKPOINT_KEY_WIDESCREEN ) ) {
			$this->add_control( 'wide_desk_columns', [
				'label'              => esc_html__( 'Columns On A Wide Desktop', 'the7mk2' ),
				'type'               => Controls_Manager::SELECT,
				'options'            => [ '' => esc_html__( 'Default', 'the7mk2' ) ] + $slides_per_view,
				'default'            => '',
				'frontend_available' => true,
			] );

			$this->add_control( 'widget_columns_wide_desktop_breakpoint', [
				'label'              => esc_html__( 'Wide Desktop Breakpoint (px)', 'the7mk2' ),
				'description'        => the7_elementor_get_wide_columns_control_description(),
				'type'               => Controls_Manager::NUMBER,
				'default'            => '',
				'min'                => 0,
				'frontend_available' => true,
			] );
		}

		$this->add_responsive_control( 'slides_per_view', [
			'type'                 => Controls_Manager::SELECT,
			'label'                => esc_html__( 'Columns', 'the7mk2' ),
			'options'              => [ '' => esc_html__( 'Default', 'the7mk2' ) ] + $slides_per_view,
			'default'              => static::SLIDES_PER_VIEW_DEFAULT,
			'inherit_placeholders' => false,
			'frontend_available'   => true,
			'render_type'          => 'none',
		] );

		$this->add_responsive_control( 'slides_gap', [
			'label'              => esc_html__( 'Gap Between Columns (px)', 'the7mk2' ),
			'type'               => Controls_Manager::SLIDER,
			'size_units'         => [ 'px' ],
			'range'              => [
				'px' => [
					'min'  => 0,
					'max'  => 100,
					'step' => 1,
				],
			],
			'default'            => [
				'unit' => 'px',
				'size' => 0,
			],
			'frontend_available' => true,
			'render_type'        => 'none',
			'selectors'          => [
				'{{WRAPPER}}' => '--slides-gap: {{SIZE}}{{UNIT}}',
			],
			'separator'          => 'before',
		] );

		$this->end_controls_section();
	}

	protected function add_arrows_content_controls() {
		$this->start_controls_section( 'arrows_section', [
			'label' => esc_html__( 'Arrows', 'the7mk2' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
		] );

		$arrow_options = [
			'never'  => esc_html__( 'Never', 'the7mk2' ),
			'always' => esc_html__( 'Always', 'the7mk2' ),
			'hover'  => esc_html__( 'On Hover', 'the7mk2' ),
		];
		$this->add_responsive_control( 'arrows', [
			'label'                => esc_html__( 'Show Arrows', 'the7mk2' ),
			'type'                 => Controls_Manager::SELECT,
			'options'              => $arrow_options,
			'device_args'          => $this->generate_device_args(
                [
                    'default' => '',
                    'options' => [ '' => esc_html__( 'Default', 'the7mk2' ) ] + $arrow_options,
                ]
            ),
			'default'              => 'always',
			'frontend_available'   => true,
			'selectors'            => [
				'{{WRAPPER}}' => '{{VALUE}}',
			],
			'selectors_dictionary' => [
				'never'  => '--arrow-display: none;',
				'always' => '--arrow-display: inline-flex;--arrow-opacity:1;',
				'hover'  => '--arrow-display: inline-flex;--arrow-opacity:0;',
			],
		] );

		$this->end_controls_section();
	}

	protected function get_swiper_container_class(){
		return Elementor::$instance->experiments->is_feature_active( 'e_swiper_latest' ) ? 'swiper' : 'swiper-container';
    }

	protected function add_bullets_content_controls() {
		$this->start_controls_section( 'bullets_section', [
			'label' => esc_html__( 'Bullets', 'the7mk2' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
		] );

		$layouts = [
			'show' => esc_html__( 'Show', 'the7mk2' ),
			'hide' => esc_html__( 'Hide', 'the7mk2' ),
		];
		$this->add_responsive_control( 'bullets', [
			'label'                => esc_html__( 'Show Bullets', 'the7mk2' ),
			'type'                 => Controls_Manager::SELECT,
            'device_args'          => $this->generate_device_args(
                [
                    'options' => [ '' => esc_html__( 'No change', 'the7mk2' ) ] + $layouts,
                ]
            ),
			'options'              => $layouts,
			'default'              => 'show',
			'frontend_available'   => true,
			'selectors'            => [
				'{{WRAPPER}}' => '{{VALUE}}',
			],
			'selectors_dictionary' => [
				'show' => '--bullet-display: inline-flex;',
				'hide' => '--bullet-display: none',
			],
		] );

		$this->end_controls_section();
	}

	protected function add_arrows_style_controls() {
		$this->start_controls_section( 'arrow_style_section', [
			'label'      => esc_html__( 'Arrows', 'the7mk2' ),
			'tab'        => Controls_Manager::TAB_STYLE,
			'conditions' => $this->generate_conditions( 'arrows', '!=', 'never' ),
		] );

		$this->add_control( 'arrow_icon_heading', [
			'label'     => esc_html__( 'Arrow Icon', 'the7mk2' ),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		] );

		$this->add_control( 'arrow_next', [
			'label'   => esc_html__( 'Next Arrow', 'the7mk2' ),
			'type'    => Controls_Manager::ICONS,
			'default' => [
				'value'   => 'fas fa-chevron-right',
				'library' => 'fa-solid',
			],
		] );

		$this->add_control( 'arrow_prev', [
			'label'   => esc_html__( 'Previous Arrow', 'the7mk2' ),
			'type'    => Controls_Manager::ICONS,
			'default' => [
				'value'   => 'fas fa-chevron-left',
				'library' => 'fa-solid',
			],
		] );

		$this->add_responsive_control( 'arrow_icon_size', [
			'label'      => esc_html__( 'Arrow Icon Size', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'default'    => [
				'unit' => 'px',
				'size' => 24,
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
				'{{WRAPPER}}' => '--arrow-icon-size: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->add_control( 'arrow_style_heading', [
			'label'     => esc_html__( 'Arrow style', 'the7mk2' ),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		] );

		$arrow_selector = '{{WRAPPER}} .' . $this->get_swiper_container_class() . ' > .the7-swiper-button';

		$this->add_responsive_control( 'arrow_bg_width', [
			'label'      => esc_html__( 'Background Width', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'default'    => [
				'unit' => 'px',
				'size' => 40,
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
				$arrow_selector => 'width: max({{SIZE}}{{UNIT}}, var(--arrow-icon-size, 1em))',
			],
		] );

		$this->add_responsive_control( 'arrow_bg_height', [
			'label'      => esc_html__( 'Background Height', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'default'    => [
				'unit' => 'px',
				'size' => 40,
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
				$arrow_selector => 'height: max({{SIZE}}{{UNIT}}, var(--arrow-icon-size, 1em))',
			],
		] );

		$this->add_control( 'arrow_border_radius', [
			'label'      => esc_html__( 'Arrow Border Radius', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'default'    => [
				'unit' => 'px',
				'size' => 0,
			],
			'size_units' => [ 'px' ],
			'range'      => [
				'px' => [
					'min'  => 0,
					'max'  => 500,
					'step' => 1,
				],
			],
			'selectors'  => [
				$arrow_selector => 'border-radius: {{SIZE}}{{UNIT}}',
			],
		] );

		$this->add_control( 'arrow_border_width', [
			'label'      => esc_html__( 'Arrow Border Width', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'default'    => [
				'unit' => 'px',
				'size' => 0,
			],
			'size_units' => [ 'px' ],
			'range'      => [
				'px' => [
					'min'  => 0,
					'max'  => 25,
					'step' => 1,
				],
			],
			'selectors'  => [
				$arrow_selector => 'border-width: {{SIZE}}{{UNIT}}; border-style: solid',
			],
		] );

		$this->start_controls_tabs( 'arrows_style_tabs' );

		$this->add_arrow_style_states_controls( 'normal_', esc_html__( 'Normal', 'the7mk2' ) );
		$this->add_arrow_style_states_controls( 'hover_', esc_html__( 'Hover', 'the7mk2' ) );

        $this->end_controls_tabs();

		$this->add_arrow_position_styles( 'prev_', esc_html__( 'Prev Arrow Position', 'the7mk2' ) );
		$this->add_arrow_position_styles( 'next_', esc_html__( 'Next Arrow Position', 'the7mk2' ) );


		$this->end_controls_section();
	}

	/**
	 * @param string $prefix_name Prefix.
	 * @param string $box_name    Box.
	 *
	 * @return void
	 */
	protected function add_arrow_style_states_controls( $prefix_name, $box_name ) {
		$is_hover = '';
		if ( strpos( $prefix_name, 'hover_' ) === 0 ) {
			$is_hover = ':hover';
		}

		$selector = '{{WRAPPER}} .' . $this->get_swiper_container_class() . ' > .the7-swiper-button' . $is_hover;

		$this->start_controls_tab( $prefix_name . 'arrow_colors_tab_style', [
			'label' => $box_name,
		] );

		$this->add_control( $prefix_name . 'arrow_icon_color', [
			'label'     => esc_html__( 'Icon Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'alpha'     => true,
			'default'   => '',
			'selectors' => [
				$selector . '> i'   => 'color: {{VALUE}};',
				$selector . '> svg' => 'fill: {{VALUE}};color: {{VALUE}};',
			],
		] );

		$this->add_control( $prefix_name . 'arrow_border_color', [
			'label'     => esc_html__( 'Border Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'alpha'     => true,
			'default'   => '',
			'selectors' => [
				$selector => 'border-color: {{VALUE}};',
			],
		] );

		$this->add_control( $prefix_name . 'arrow_bg_color', [
			'label'     => esc_html__( 'Background Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'alpha'     => true,
			'default'   => '',
			'selectors' => [
				$selector => 'background: {{VALUE}};',
			],
		] );
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => $prefix_name . 'arrow_bg_shadow',
				'selector' => $selector,
			]
		);

		$this->end_controls_tab();
	}

	protected function add_arrow_position_styles( $prefix, $heading_name ) {
		$button_class = '';
		$default_h_pos = 'left';
		if ( $prefix === 'next_' ) {
			$button_class = '.the7-swiper-button-next';
			$default_h_pos = 'right';
		} elseif ( $prefix === 'prev_' ) {
			$button_class = '.the7-swiper-button-prev';
		}

		$selector = '{{WRAPPER}} .' . $this->get_swiper_container_class() . ' > .the7-swiper-button' . $button_class;

		$this->add_control( $prefix . 'arrow_position_heading', [
			'label'     => $heading_name,
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		] );

		$this->add_responsive_control( $prefix . 'arrow_v_position', [
			'label'                => esc_html__( 'Vertical Position', 'the7mk2' ),
			'type'                 => Controls_Manager::CHOOSE,
			'label_block'          => false,
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
			'default'              => 'center',
			'selectors_dictionary' => [
				'top'    => 'top: var(--arrow-v-offset); --arrow-translate-y:0;',
				'center' => 'top: calc(50% + var(--arrow-v-offset)); --arrow-translate-y:-50%;',
				'bottom' => 'top: calc(100% + var(--arrow-v-offset)); --arrow-translate-y:-100%;',
			],
			'selectors'            => [
				$selector => '{{VALUE}};',
			],
		] );

		$this->add_responsive_control( $prefix . 'arrow_h_position', [
			'label'                => esc_html__( 'Horizontal Position', 'the7mk2' ),
			'type'                 => Controls_Manager::CHOOSE,
			'label_block'          => false,
			'options'              => [
				'left'   => [
					'title' => esc_html__( 'Left', 'the7mk2' ),
					'icon'  => 'eicon-h-align-left',
				],
				'center' => [
					'title' => esc_html__( 'Center', 'the7mk2' ),
					'icon'  => 'eicon-h-align-center',
				],
				'right'  => [
					'title' => esc_html__( 'Right', 'the7mk2' ),
					'icon'  => 'eicon-h-align-right',
				],
			],
			'default'              => $default_h_pos,
			'selectors_dictionary' => [
				'left'   => 'left: var(--arrow-h-offset); --arrow-translate-x:0;',
				'center' => 'left: calc(50% + var(--arrow-h-offset)); --arrow-translate-x:-50%;',
				'right'  => 'left: calc(100% - var(--arrow-h-offset)); --arrow-translate-x:-100%;',
			],
			'selectors'            => [
				$selector => '{{VALUE}};',
			],
		] );

		$this->add_responsive_control( $prefix . 'arrow_v_offset', [
			'label'      => esc_html__( 'Vertical Offset', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'default'    => [
				'unit' => 'px',
				'size' => 0,
			],
			'size_units' => [ 'px' ],
			'range'      => [
				'px' => [
					'min'  => - 1000,
					'max'  => 1000,
					'step' => 1,
				],
			],
			'selectors'  => [
				$selector => '--arrow-v-offset: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->add_responsive_control( $prefix . 'arrow_h_offset', [
			'label'      => esc_html__( 'Horizontal Offset', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'default'    => [
				'unit' => 'px',
				'size' => 0,
			],
			'size_units' => [ 'px' ],
			'range'      => [
				'px' => [
					'min'  => - 1000,
					'max'  => 1000,
					'step' => 1,
				],
			],
			'selectors'  => [
				$selector => '--arrow-h-offset: {{SIZE}}{{UNIT}};',
			],
		] );
	}


	/**
	 * Slides content to display
	 */
	protected function render() {
		$slides_count = $this->get_slides_count();
		if ( ! $slides_count ) {
			$this->render_empty_view();
			return;
		}

		$settings = $this->get_settings();

		$show_dots = true;
		$this->add_render_attribute( 'elementor_swiper_wrapper', 'class', 'elementor-swiper' );

		$this->add_render_attribute( 'elementor_swiper_container', 'class', [
			'elementor-slides-wrapper',
			'elementor-main-swiper',
			'swiper',
			$this->get_swiper_container_class(),
		] );
		?>
        <div <?php echo $this->get_render_attribute_string( 'elementor_swiper_wrapper' ); ?>>
            <div <?php echo $this->get_render_attribute_string( 'elementor_swiper_container' ); ?>>
                <div class="swiper-wrapper the7-elementor-slides">
					<?php $this->render_slides() ?>
                </div>
					<?php if ( $show_dots ) {
	                        echo '<div class="swiper-pagination owl-dots ' . $this->add_bullets_class() . '"></div>';
	                    }
                     	$this->render_arrows( $settings );
                     ?>
            </div>
        </div>
		<?php
	}

	protected function get_slides_count() {
		$settings = $this->get_settings();

		if ( empty( $settings['slides'] ) ) {
			return 0;
		}

		return count( $settings['slides'] );
	}
	protected function add_bullets_class( $class = '') {
		$settings = $this->get_settings();
		$style_classes = [
			'small-dot-stroke'  => 'bullets-small-dot-stroke',
			'scale-up'    => 'bullets-scale-up',
			'stroke'  => 'bullets-stroke',
			'fill-in'  => 'bullets-fill-in',
			'ubax' => 'bullets-ubax',
			'etefu' => 'bullets-etefu',
		];

		$layout = $settings['bullets_style'];
		if ( array_key_exists( $layout, $style_classes ) ) {
			$class = $style_classes[ $layout ];
		}
		return $class;

	}

	/**
	 * Render Empty View
	 * Renders the widget's view if there is no posts to display
	 */
	protected function render_empty_view() {
		if ( Elementor::$instance->editor->is_edit_mode() ) {
			if ( the7_elementor_pro_is_active() && version_compare( ELEMENTOR_PRO_VERSION, '3.11.0', '>' ) ) {
				//Will be filled with JS
				?>
                <div class="e-loop-empty-view__wrapper"></div>
				<?php
			} else {
				?>
                <div class="e-loop-empty-view__wrapper_old the7-slider-error-template">
					<?php echo esc_html__( 'Either choose an existing template or create a new one and use it as the template in the slide.', 'the7mk2' ) ?>
                </div>
				<?php
			}
		}
	}

	protected function render_slides() {
		$settings = $this->get_settings();
		$this->add_render_attribute( 'swiper_slide_inner_wrapper', 'class', 'the7-swiper-slide-inner' );

		foreach ( $settings['slides'] as $slide ) {
			$this->remove_render_attribute( 'swiper_slide_wrapper' );
			$this->add_render_attribute( 'swiper_slide_wrapper', 'class', [
				'elementor-repeater-item-' . $slide['_id'],
				'the7-swiper-slide',
			] );
			?>
            <div <?php echo $this->get_render_attribute_string( 'swiper_slide_wrapper' ); ?>>
                <div <?php echo $this->get_render_attribute_string( 'swiper_slide_inner_wrapper' ); ?>>
					<?php echo $this->render_template( $slide ); ?>
                </div>
            </div>
			<?php
		}
	}

	private function render_template( $slide ) {
		if ( ! $slide['slide_id'] ) {
			return '<div class="the7-slider-error-template">' . esc_html__( 'Slide template not selected in ', 'the7mk2' ) . '"' . $slide['slide_name'] . '"' . '</div>';
		}
		$slide_id = $slide['slide_id'];

		if ( 'publish' !== get_post_status( $slide_id ) ) {
			return '<div class="the7-slider-error-template">' . esc_html__( 'Slide template not exist in', 'the7mk2' ) . '"' . $slide['slide_name'] . '"' . '</div>';
		}

		return The7_Elementor_Compatibility::get_builder_content_for_display( $slide_id, false );
	}

	protected function render_arrows( $settings ) {
		?>
        <div class="the7-swiper-button the7-swiper-button-prev elementor-icon">
			<?php
			Icons_Manager::render_icon( $settings['arrow_prev'], [ 'aria-hidden' => 'true' ] ); ?>
        </div>
        <div class="the7-swiper-button the7-swiper-button-next elementor-icon">
			<?php
			Icons_Manager::render_icon( $settings['arrow_next'], [ 'aria-hidden' => 'true' ] ); ?>
        </div>
		<?php
	}
}
