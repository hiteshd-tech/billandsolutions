<?php
/**
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Core\Breakpoints\Manager as Breakpoints;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Plugin;
use Elementor\Utils;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Arrows;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Bullets;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Button;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Image_Aspect_Ratio;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Image_Size;

defined( 'ABSPATH' ) || exit;

/**
 * Testimonials_Carousel class.
 */
class Testimonials_Carousel extends The7_Elementor_Widget_Base {

	/**
	 * Get element name.
	 *
	 * Retrieve the element name.
	 *
	 * @return string The name.
	 */
	public function get_name() {
		return 'the7_testimonials_carousel';
	}

	/**
	 * @return string[]
	 */
	protected function the7_keywords() {
		return [ 'carousel', 'testimonials' ];
	}

	/**
	 * @return string
	 */
	protected function the7_title() {
		return esc_html__( 'Testimonials Carousel', 'the7mk2' );
	}

	/**
	 * @return string
	 */
	protected function the7_icon() {
		return 'eicon-posts-carousel';
	}

	/**
	 * @return array|string[]
	 */
	public function get_script_depends() {
		if ( $this->is_preview_mode() ) {
			return [ 'the7-elements-carousel-widget-preview' ];
		}

		return [];
	}

	/**
	 * @return string[]
	 */
	public function get_style_depends() {
		return [ 'the7-carousel-text-and-icon-widget', 'the7-carousel-navigation' ];
	}

	/**
	 * Register widget controls.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Slides', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'list_title',
			[
				'label'   => esc_html__( 'Title', 'the7mk2' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Title', 'the7mk2' ),
			]
		);

		$repeater->add_control(
			'list_subtitle',
			[
				'label'   => esc_html__( 'Subtitle', 'the7mk2' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
			]
		);

		$repeater->add_control(
			'list_content',
			[
				'label' => esc_html__( 'Text', 'the7mk2' ),
				'type'  => Controls_Manager::TEXTAREA,
			]
		);

		$repeater->add_control(
			'graphic_type',
			[
				'label'       => esc_html__( 'Graphic Element', 'the7mk2' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => [
					'icon'  => [
						'title' => esc_html__( 'Icon', 'the7mk2' ),
						'icon'  => 'eicon-favorite',
					],
					'image' => [
						'title' => esc_html__( 'Image', 'the7mk2' ),
						'icon'  => 'eicon-image',
					],
					'none'  => [
						'title' => esc_html__( 'None', 'the7mk2' ),
						'icon'  => 'eicon-ban',
					],
				],
				'toggle'      => false,
				'default'     => 'icon',
			]
		);

		$repeater->add_control(
			'list_icon',
			[
				'label'     => esc_html__( 'Icon', 'the7mk2' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => [
					'value'   => 'fas fa-quote-right',
					'library' => 'fa-solid',
				],
				'condition' => [
					'graphic_type' => 'icon',
				],
			]
		);

		$repeater->add_control(
			'list_image',
			[
				'name'        => 'image',
				'label'       => esc_html__( 'Choose Image', 'the7mk2' ),
				'type'        => Controls_Manager::MEDIA,
				'default'     => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'label_block' => true,
				'condition'   => [
					'graphic_type' => 'image',
				],
			]
		);

		$repeater->add_control(
			'button',
			[
				'label'   => esc_html__( 'Button text', 'the7mk2' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Button text', 'the7mk2' ),
			]
		);

		$repeater->add_control(
			'link',
			[
				'label'       => esc_html__( 'Link', 'the7mk2' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => [
					'active' => true,
				],
				'placeholder' => 'https://your-link.com',
			]
		);

		$defaults = [];
		for ( $i = 1; $i <= 4; $i++ ) {
			$defaults[] = [
				'list_title'    => esc_html__( 'Item title', 'the7mk2' ) . " #{$i} ",
				'list_subtitle' => esc_html__( 'Item subtitle ', 'the7mk2' ),
				'list_content'  => esc_html__( 'Item content. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'the7mk2' ),
				'list_icon'     => 'fas fa-quote-right',
				'button'        => esc_html__( 'Click Here', 'the7mk2' ),
				'link'          => esc_html__( 'https://your-link.com', 'the7mk2' ),
			];
		}

		$this->add_control(
			'list',
			[
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => $defaults,
				'title_field' => '{{{ list_title }}}',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'layout_section',
			[
				'label' => esc_html__( 'Layout', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		if ( ! Plugin::$instance->breakpoints->get_active_breakpoints( Breakpoints::BREAKPOINT_KEY_WIDESCREEN ) ) {
			$this->add_control(
				'wide_desk_columns',
				[
					'label'              => esc_html__( 'Columns On A Wide Desktop', 'the7mk2' ),
					'type'               => Controls_Manager::NUMBER,
					'default'            => '',
					'min'                => 1,
					'max'                => 12,
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'widget_columns_wide_desktop_breakpoint',
				[
					'label'              => esc_html__( 'Wide Desktop Breakpoint (px)', 'the7mk2' ),
					'description'        => the7_elementor_get_wide_columns_control_description(),
					'type'               => Controls_Manager::NUMBER,
					'default'            => '',
					'min'                => 0,
					'frontend_available' => true,
				]
			);
		}

		$this->add_responsive_control(
			'widget_columns',
			[
				'label'              => esc_html__( 'Columns', 'the7mk2' ),
				'type'               => Controls_Manager::NUMBER,
				'default'            => 3,
				'tablet_default'     => 2,
				'mobile_default'     => 1,
				'frontend_available' => true,
			]
		);

		$this->template( Image_Size::class )->add_style_controls();

		$this->add_responsive_control(
			'gap_between_posts',
			[
				'label'              => esc_html__( 'Gap Between Columns (px)', 'the7mk2' ),
				'type'               => Controls_Manager::SLIDER,
				'default'            => [
					'unit' => 'px',
					'size' => 30,
				],
				'size_units'         => [ 'px' ],
				'range'              => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'separator'          => 'before',
				'frontend_available' => true,
			]
		);

		$this->add_responsive_control(
			'carousel_margin',
			[
				'label'       => esc_html__( 'outer gaps', 'the7mk2' ),
				'type'        => Controls_Manager::DIMENSIONS,
				'size_units'  => [ 'px' ],
				'range'       => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors'   => [
					'{{WRAPPER}} .owl-stage-outer' => ' --stage-right-gap:{{RIGHT}}{{UNIT}};  --stage-left-gap:{{LEFT}}{{UNIT}}; padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'adaptive_height',
			[
				'label'        => esc_html__( 'Adaptive Height', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'y',
				'default'      => '',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'scrolling_section',
			[
				'label' => esc_html__( 'Scrolling', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'slide_to_scroll',
			[
				'label'   => esc_html__( 'Scroll Mode', 'the7mk2' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'single',
				'options' => [
					'single' => 'One slide at a time',
					'all'    => 'All slides',
				],
			]
		);

		$this->add_control(
			'speed',
			[
				'label'   => esc_html__( 'Transition Speed (ms)', 'the7mk2' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => '600',
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label'        => esc_html__( 'Autoplay Slides', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'y',
				'default'      => '',
			]
		);

		$this->add_control(
			'autoplay_speed',
			[
				'label'     => esc_html__( 'Autoplay Speed (ms)', 'the7mk2' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 6000,
				'min'       => 100,
				'max'       => 10000,
				'step'      => 10,
				'condition' => [
					'autoplay' => 'y',
				],
			]
		);

		$this->end_controls_section();

		$this->template( Arrows::class )->add_content_controls();
		$this->template( Bullets::class )->add_content_controls();

		$this->start_controls_section(
			'skin_section',
			[
				'label' => esc_html__( 'Skin', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$layouts = [
			'layout_1' => esc_html__( 'Stacked, above content', 'the7mk2' ),
			'layout_5' => esc_html__( 'Stacked, below content', 'the7mk2' ),
			'layout_2' => esc_html__( 'Inline, above content', 'the7mk2' ),
			'layout_9' => esc_html__( 'Stacked, title after content', 'the7mk2' ),
			'layout_6' => esc_html__( 'Inline, below content', 'the7mk2' ),
			'layout_3' => esc_html__( 'Left, title before content', 'the7mk2' ),
			'layout_7' => esc_html__( 'Left, title after content', 'the7mk2' ),
			'layout_4' => esc_html__( 'Right, title before content', 'the7mk2' ),
			'layout_8' => esc_html__( 'Right, title after content', 'the7mk2' ),
		];

		$this->add_responsive_control(
			'layout',
			[
				'label'                => esc_html__( 'Choose Skin', 'the7mk2' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => 'layout_1',
				'options'              => $layouts,
				'device_args'          => $this->generate_device_args(
					[
						'options' => [ '' => esc_html__( 'No change', 'the7mk2' ) ] + $layouts,
					]
				),
				'selectors_dictionary' => [
					'layout_1' => $this->combine_to_css_vars_definition_string(
						[
							'the7-slider-layout-columns' => 'minmax(0, 100%)',
							'the7-slider-layout-columns-noicon' => 'minmax(0, 100%)',
							'the7-slider-template-areas' => '" icon" " header " " subtitle " " desc" " button "',
							'the7-slider-template-areas-noicon' => '" header " " subtitle " " desc" " button "',
							'the7-slider-template-rows'  => 'none',
							'img-width'                  => 'var(--icon-size, 40px)',
							'img-height'                 => 'var(--icon-size, 40px)',
							'icon-width'                 => 'var(--icon-size, 40px)',
							'icon-top-padding'           => 'var(--icon-size, 40px)',
							'the7-slider-layout-gap'     => 'normal',
							'the7-slider-layout-margin'  => 'var(--icon-top-gap, 0px) var(--icon-right-gap, 0px) var(--icon-bottom-gap, 0px) var(--icon-left-gap, 0px)',
							'the7-title-alignment'       => 'var(--content-text-align)',
							'the7-title-justify'         => 'var(--content-justify-self)',
						]
					),
					'layout_5' => $this->combine_to_css_vars_definition_string(
						[
							'the7-slider-layout-columns' => 'minmax(0, 100%)',
							'the7-slider-layout-columns-noicon' => 'minmax(0, 100%)',
							'the7-slider-template-areas' => '" desc" " icon" " header " " subtitle " " button "',
							'the7-slider-template-areas-noicon' => '" desc" " header " " subtitle " " button "',
							'the7-slider-template-rows'  => 'none',
							'img-width'                  => 'var(--icon-size, 40px)',
							'img-height'                 => 'var(--icon-size, 40px)',
							'icon-width'                 => 'var(--icon-size, 40px)',
							'icon-top-padding'           => 'var(--icon-size, 40px)',
							'the7-slider-layout-gap'     => 'normal',
							'the7-slider-layout-margin'  => 'var(--icon-top-gap, 0px) var(--icon-right-gap, 0px) var(--icon-bottom-gap, 0px) var(--icon-left-gap, 0px)',
							'the7-title-alignment'       => 'var(--content-text-align)',
							'the7-title-justify'         => 'var(--content-justify-self)',
						]
					),
					'layout_2' => $this->combine_to_css_vars_definition_string(
						[
							'the7-slider-layout-columns' => 'var(--the7-slider-layout-2-columns)',
							'the7-slider-layout-columns-noicon' => 'minmax(0, 100%)',
							'the7-slider-template-areas' => 'var(--the7-slider-template-2-areas)',
							'the7-slider-template-areas-noicon' => '" header header " " subtitle subtitle " " desc desc " " button button "',
							'the7-slider-template-rows'  => 'none',
							'img-width'                  => 'var(--icon-size, 40px)',
							'img-height'                 => 'var(--icon-size, 40px)',
							'icon-width'                 => 'var(--icon-size, 40px)',
							'icon-top-padding'           => 'var(--icon-size, 40px)',
							'the7-slider-layout-gap'     => 'var(--icon-right-gap, 0px)',
							'the7-slider-layout-margin'  => 'var(--the7-slider-layout-2-margin)',
							'the7-title-alignment'       => 'var(--the7-layout-2-title-alignment)',
							'the7-title-justify'         => 'var(--the7-layout-2-title-justify)',
						]
					),
					'layout_9' => $this->combine_to_css_vars_definition_string(
						[
							'the7-slider-layout-columns' => 'minmax(0, 100%)',
							'the7-slider-layout-columns-noicon' => 'minmax(0, 100%)',
							'the7-slider-template-areas' => '" icon" " desc" " header " " subtitle " " button "',
							'the7-slider-template-areas-noicon' => '"desc" " header " " subtitle " " button "',
							'the7-slider-template-rows'  => 'none',
							'img-width'                  => 'var(--icon-size, 40px)',
							'img-height'                 => 'var(--icon-size, 40px)',
							'icon-width'                 => 'var(--icon-size, 40px)',
							'icon-top-padding'           => 'var(--icon-size, 40px)',
							'the7-slider-layout-gap'     => 'normal',
							'the7-slider-layout-margin'  => 'var(--icon-top-gap, 0px) var(--icon-right-gap, 0px) var(--icon-bottom-gap, 0px) var(--icon-left-gap, 0px)',
							'the7-title-alignment'       => 'var(--content-text-align)',
							'the7-title-justify'         => 'var(--content-justify-self)',
						]
					),
					'layout_3' => $this->combine_to_css_vars_definition_string(
						[
							'the7-slider-layout-columns' => 'calc(var(--icon-size, 40px) + var(--icon-left-gap, 0px)) minmax(30px,1fr)',
							'the7-slider-layout-columns-noicon' => 'minmax(0, 100%)',
							'the7-slider-template-areas' => '"icon header" "icon subtitle" "icon desc" "icon button"',
							'the7-slider-template-areas-noicon' => '" header" " subtitle" " desc" " button"',
							'the7-slider-template-rows'  => 'repeat(3, auto) 1fr',
							'the7-slider-layout-gap'     => 'var(--icon-right-gap, 0px)',
							'img-height'                 => 'var(--icon-size, 40px)',
							'icon-width'                 => 'var(--icon-size, 40px)',
							'img-width'                  => 'var(--icon-size, 40px)',
							'icon-top-padding'           => 'var(--icon-size, 40px)',
							'the7-slider-layout-margin'  => 'var(--icon-top-gap, 0px) 0 var(--icon-bottom-gap, 0px) var(--icon-left-gap, 0px)',
							'the7-title-alignment'       => 'var(--content-text-align)',
							'the7-title-justify'         => 'var(--content-justify-self)',
						]
					),
					'layout_6' => $this->combine_to_css_vars_definition_string(
						[
							'the7-slider-layout-columns' => 'var(--the7-slider-layout-6-columns)',
							'the7-slider-layout-columns-noicon' => 'minmax(0, 100%)',
							'the7-slider-template-areas' => 'var(--the7-slider-template-6-areas)',
							'the7-slider-template-areas-noicon' => '" desc desc "  " header header " " subtitle subtitle "  " button button "',
							'the7-slider-template-rows'  => 'none',
							'img-width'                  => 'var(--icon-size, 40px)',
							'img-height'                 => 'var(--icon-size, 40px)',
							'icon-width'                 => 'var(--icon-size, 40px)',
							'icon-top-padding'           => 'var(--icon-size, 40px)',
							'the7-slider-layout-gap'     => 'var(--icon-right-gap, 0px)',
							'the7-slider-layout-margin'  => 'var(--the7-slider-layout-2-margin)',
							'the7-title-alignment'       => 'var(--the7-layout-2-title-alignment)',
							'the7-title-justify'         => 'var(--the7-layout-2-title-justify)',
						]
					),
					'layout_4' => $this->combine_to_css_vars_definition_string(
						[
							'the7-slider-layout-columns' => 'minmax(0,1fr) calc(var(--icon-size, 40px) + var(--icon-right-gap, 0px))',
							'the7-slider-layout-columns-noicon' => 'minmax(0, 100%)',
							'the7-slider-template-areas' => '" header icon " " subtitle icon " " desc icon " " button icon "',
							'the7-slider-template-areas-noicon' => '" header  " " subtitle  " " desc  " " button "',
							'the7-slider-template-rows'  => 'repeat(3, auto) 1fr',
							'img-width'                  => 'var(--icon-size, 40px)',
							'img-height'                 => 'var(--icon-size, 40px)',
							'icon-width'                 => 'var(--icon-size, 40px)',
							'icon-top-padding'           => 'var(--icon-size, 40px)',
							'the7-slider-layout-gap'     => 'var(--icon-left-gap, 0px)',
							'the7-slider-layout-margin'  => 'var(--icon-top-gap, 0px) var(--icon-right-gap, 0px) var(--icon-bottom-gap, 0px) 0',
							'the7-title-alignment'       => 'var(--content-text-align)',
							'the7-title-justify'         => 'var(--content-justify-self)',
						]
					),
					'layout_7' => $this->combine_to_css_vars_definition_string(
						[
							'the7-slider-layout-columns' => 'calc(var(--icon-size, 40px) + var(--icon-left-gap, 0px)) minmax(30px,1fr)',
							'the7-slider-layout-columns-noicon' => 'minmax(0, 100%)',
							'the7-slider-template-areas' => '"icon desc" "icon header" "icon subtitle" "icon button"',
							'the7-slider-template-areas-noicon' => '" desc" " header" " subtitle" " button"',
							'the7-slider-template-rows'  => 'repeat(3, auto) 1fr',
							'img-height'                 => 'var(--icon-size, 40px)',
							'icon-width'                 => 'var(--icon-size, 40px)',
							'img-width'                  => 'var(--icon-size, 40px)',
							'icon-top-padding'           => 'var(--icon-size, 40px)',
							'the7-slider-layout-gap'     => ' var(--icon-right-gap, 0px)',
							'the7-slider-layout-margin'  => 'var(--icon-top-gap, 0px) 0 var(--icon-bottom-gap, 0px) var(--icon-left-gap, 0px)',
							'the7-title-alignment'       => 'var(--content-text-align)',
							'the7-title-justify'         => 'var(--content-justify-self)',
						]
					),
					'layout_8' => $this->combine_to_css_vars_definition_string(
						[
							'the7-slider-layout-columns' => 'minmax(0,1fr) calc(var(--icon-size, 40px) + var(--icon-right-gap, 0px))',
							'the7-slider-layout-columns-noicon' => 'minmax(0, 100%)',
							'the7-slider-template-areas' => '" desc icon " " header icon " " subtitle icon " " button icon "',
							'the7-slider-template-areas-noicon' => '" desc " " header " " subtitle " " button "',
							'the7-slider-template-rows'  => 'repeat(3, auto) 1fr',
							'img-width'                  => 'var(--icon-size, 40px)',
							'img-height'                 => 'var(--icon-size, 40px)',
							'icon-width'                 => 'var(--icon-size, 40px)',
							'icon-top-padding'           => 'var(--icon-size, 40px)',
							'the7-slider-layout-gap'     => 'var(--icon-left-gap, 0px)',
							'the7-slider-layout-margin'  => 'var(--icon-top-gap, 0px) var(--icon-right-gap, 0px) var(--icon-bottom-gap, 0px) 0',
							'the7-title-alignment'       => 'var(--content-text-align)',
							'the7-title-justify'         => 'var(--content-justify-self)',
						]
					),
				],
				'selectors'            => [
					'{{WRAPPER}}' => '{{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'content_alignment',
			[
				'label'                => esc_html__( 'Alignment', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'default'              => 'center',
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
				'toggle'               => false,
				'device_args'          => $this->generate_device_args(
					[
						'toggle' => true,
					]
				),
				'label_block'          => false,
				'selectors_dictionary' => [
					'left'   => $this->combine_to_css_vars_definition_string(
						[
							'content-text-align'           => 'left',
							'content-justify-self'         => 'flex-start',
							'the7-layout-2-title-justify'  => 'flex-start',
							'the7-layout-2-title-alignment' => 'left',
							'the7-slider-layout-2-columns' => 'calc(var(--icon-size, 40px) + var(--icon-left-gap, 0px)) minmax(0,1fr)',
							'the7-slider-layout-2-columns-noicon' => 'minmax(0, 100%)',
							'the7-slider-template-2-areas' => '" icon before" " icon header " " icon subtitle " " icon empty" " desc desc " " button button "',
							'the7-slider-template-6-areas' => '" desc desc " " icon empty "  " icon header " " icon subtitle "  " icon button " " icon empty1"',
							'the7-slider-layout-6-columns' => 'calc(var(--icon-size, 40px) + var(--icon-left-gap, 0px)) minmax(0,1fr)',
							'the7-slider-layout-2-margin'  => 'var(--icon-top-gap, 0px) 0 var(--icon-bottom-gap, 0px) var(--icon-left-gap, 0px)',
						]
					),
					'center' => $this->combine_to_css_vars_definition_string(
						[
							'content-text-align'           => 'center',
							'content-justify-self'         => 'center',
							'the7-layout-2-title-justify'  => 'flex-start',
							'the7-layout-2-title-alignment' => 'left',
							'the7-slider-layout-2-columns' => '1fr calc(var(--icon-size, 40px)  + var(--icon-left-gap, 0px)) minmax(auto,  max-content) 1fr',
							'the7-slider-template-2-areas' => '"empty1 icon before empty2" "empty1 icon header empty2" "empty1 icon subtitle empty2" "empty1 icon empty empty2" "desc desc desc desc" "button button button button"',
							'the7-slider-template-6-areas' => '"desc desc desc desc" "empty1 icon before empty2" "empty1 icon header empty2" "empty1 icon subtitle empty2" "empty1 icon button empty2" "empty1 icon empty empty2"',
							'the7-slider-layout-6-columns' => '1fr calc(var(--icon-size, 40px) + var(--icon-left-gap, 0px)) minmax(auto,  max-content) 1fr',

							'the7-slider-layout-2-margin'  => 'var(--icon-top-gap, 0px) 0 var(--icon-bottom-gap, 0px) var(--icon-left-gap, 0px)',
						]
					),
					'right'  => $this->combine_to_css_vars_definition_string(
						[
							'content-text-align'           => 'right',
							'content-justify-self'         => 'flex-end',
							'the7-layout-2-title-justify'  => 'flex-end',
							'the7-layout-2-title-alignment' => 'right',
							'the7-slider-layout-2-columns' => ' minmax(0,1fr) calc(var(--icon-size, 40px) + var(--icon-left-gap, 0px))',
							'the7-slider-template-2-areas' => '" before icon " " header icon " " subtitle icon " " empty icon " " desc desc " " button button "',
							'the7-slider-template-6-areas' => '" desc desc " " empty icon "  " header icon " " subtitle  icon "  " button icon " " empty1 icon "',
							'the7-slider-layout-6-columns' => 'minmax(0,1fr) calc(var(--icon-size, 40px) + var(--icon-left-gap, 0px))',

							'the7-slider-layout-2-margin'  => 'var(--icon-top-gap, 0px) var(--icon-right-gap, 0px) var(--icon-bottom-gap, 0px) 0',
						]
					),
				],
				'selectors'            => [
					'{{WRAPPER}}' => '{{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'icon_below_gap',
			[
				'label'      => esc_html__( 'Graphic Element Margin', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}}' => '--icon-top-gap: {{TOP}}{{UNIT}}; --icon-right-gap: {{RIGHT}}{{UNIT}}; --icon-left-gap: {{LEFT}}{{UNIT}}; --icon-bottom-gap: {{BOTTOM}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_bg_size',
			[
				'label'      => esc_html__( 'Graphic Element Width', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 40,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 1,
						'max' => 1000,
					],
				],
				'selectors'  => [
					'{{WRAPPER}}' => '--icon-size: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'link_click',
			[
				'label'   => esc_html__( 'Apply Link & Hover On', 'the7mk2' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'button',
				'options' => [
					'slide'  => esc_html__( 'Whole box', 'the7mk2' ),
					'button' => esc_html__( "Separate slide's elements", 'the7mk2' ),
				],
			]
		);

		$this->add_control(
			'link_hover',
			[
				'label'        => esc_html__( 'Apply Hover To Slides With No Links', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'y',
				'default'      => 'y',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'box_section',
			[
				'label' => esc_html__( 'Box', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'box_border_width',
			[
				'label'      => esc_html__( 'Border Width', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .dt-owl-item-wrap' => 'border-style: solid; box-sizing: border-box; border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'box_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .dt-owl-item-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
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
					'{{WRAPPER}} .owl-carousel'     => '--box-padding-top: {{TOP}}{{UNIT}}; --box-padding-bottom: {{BOTTOM}}{{UNIT}};',
					'{{WRAPPER}} .dt-owl-item-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'box_style_tabs' );

		$this->start_controls_tab(
			'classic_style_normal',
			[
				'label' => esc_html__( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'           => 'box_shadow',
				'selector'       => '{{WRAPPER}} .dt-owl-item-wrap',
				'fields_options' => [
					'box_shadow' => [
						'selectors' => [
							'{{SELECTOR}}'                 => 'box-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{SPREAD}}px {{COLOR}} {{box_shadow_position.VALUE}};',
							'{{WRAPPER}} .owl-stage-outer' => '--shadow-horizontal: {{HORIZONTAL}}px; --shadow-vertical: {{VERTICAL}}px; --shadow-blur: {{BLUR}}px; --shadow-spread: {{SPREAD}}px',
						],
					],
				],
			]
		);

		$this->add_control(
			'box_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dt-owl-item-wrap' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'box_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dt-owl-item-wrap' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'classic_style_hover',
			[
				'label' => esc_html__( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'           => 'box_shadow_hover',
				'selector'       => '{{WRAPPER}} .dt-owl-item-wrap { transition: all 0.3s ease; } {{WRAPPER}} .dt-owl-item-wrap.box-hover:hover, {{WRAPPER}} .dt-owl-item-wrap.elements-hover:hover',
				'fields_options' => [
					'box_shadow' => [
						'selectors' => [
							'{{SELECTOR}}' => 'box-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{SPREAD}}px {{COLOR}} {{box_shadow_position.VALUE}};',

						],

					],
				],
			]
		);

		$this->add_control(
			'box_background_color_hover',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dt-owl-item-wrap.box-hover:hover, {{WRAPPER}} .dt-owl-item-wrap.elements-hover:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'box_border_color_hover',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dt-owl-item-wrap.box-hover:hover, {{WRAPPER}} .dt-owl-item-wrap.elements-hover:hover' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_main-menu',
			[
				'label' => esc_html__( 'Title', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,

			]
		);

		$this->add_control(
			'title_tag',
			[
				'label'   => esc_html__( 'HTML Tag', 'the7mk2' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'h1'   => 'H1',
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'h5'   => 'H5',
					'h6'   => 'H6',
					'div'  => 'div',
					'span' => 'span',
					'p'    => 'p',
				],
				'default' => 'h4',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'post_title',
				'label'          => esc_html__( 'Typography', 'the7mk2' ),
				'selector'       => '{{WRAPPER}} .dt-owl-item-heading',
				'fields_options' => [
					'font_family' => [
						'default' => '',
					],
					'font_size'   => [
						'default' => [
							'unit' => 'px',
							'size' => '',
						],
					],
					'font_weight' => [
						'default' => '',
					],
					'line_height' => [
						'default' => [
							'unit' => 'px',
							'size' => '',
						],
					],
				],
			]
		);
		$this->start_controls_tabs( 'post_title_style_tabs' );

		$this->start_controls_tab(
			'post_title_normal_style',
			[
				'label' => esc_html__( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_control(
			'custom_title_color',
			[
				'label'     => esc_html__( 'Font Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .dt-owl-item-heading' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'post_title_hover_style',
			[
				'label' => esc_html__( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			'post_title_color_hover',
			[
				'label'     => esc_html__( 'Font Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .box-hover:hover .dt-owl-item-heading, {{WRAPPER}} .elements-hover .dt-owl-item-heading:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'post_title_bottom_margin',
			[
				'label'      => esc_html__( 'Gap Below Title', 'the7mk2' ),
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
					'{{WRAPPER}} .dt-owl-item-heading' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_subtitle',
			[
				'label' => esc_html__( 'Subtitle', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,

			]
		);

		$this->add_control(
			'subtitle_tag',
			[
				'label'   => esc_html__( 'HTML Tag', 'the7mk2' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'h1'   => 'H1',
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'h5'   => 'H5',
					'h6'   => 'H6',
					'div'  => 'div',
					'span' => 'span',
					'p'    => 'p',
				],
				'default' => 'h6',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'post_subtitle',
				'label'          => esc_html__( 'Typography', 'the7mk2' ),
				'selector'       => '{{WRAPPER}} .dt-owl-item-subtitle',
				'fields_options' => [
					'font_family' => [
						'default' => '',
					],
					'font_size'   => [
						'default' => [
							'unit' => 'px',
							'size' => '',
						],
					],
					'font_weight' => [
						'default' => '',
					],
					'line_height' => [
						'default' => [
							'unit' => 'px',
							'size' => '',
						],
					],
				],
			]
		);

		$this->start_controls_tabs( 'post_subtitle_style_tabs' );

		$this->start_controls_tab(
			'post_subtitle_normal_style',
			[
				'label' => esc_html__( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_control(
			'custom_subtitle_color',
			[
				'label'     => esc_html__( 'Font Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .dt-owl-item-subtitle' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'post_subtitle_hover_style',
			[
				'label' => esc_html__( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			'post_subtitle_color_hover',
			[
				'label'     => esc_html__( 'Font Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .box-hover:hover .dt-owl-item-subtitle, {{WRAPPER}} .elements-hover .dt-owl-item-subtitle:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'post_subtitle_bottom_margin',
			[
				'label'      => esc_html__( 'Gap Below Subtitle', 'the7mk2' ),
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
					'{{WRAPPER}} .dt-owl-item-subtitle' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Text section.
		 */
		$this->start_controls_section(
			'text_section',
			[
				'label' => esc_html__( 'Text', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'post_content',
				'label'          => esc_html__( 'Typography', 'the7mk2' ),
				'fields_options' => [
					'font_family' => [
						'default' => '',
					],
					'font_size'   => [
						'default' => [
							'unit' => 'px',
							'size' => '',
						],
					],
					'font_weight' => [
						'default' => '',
					],
					'line_height' => [
						'default' => [
							'unit' => 'px',
							'size' => '',
						],
					],
				],
				'selector'       => '{{WRAPPER}} .dt-owl-item-description',
			]
		);

		$this->start_controls_tabs( 'post_content_style_tabs' );

		$this->start_controls_tab(
			'post_content_normal_style',
			[
				'label' => esc_html__( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_control(
			'post_content_color',
			[
				'label'     => esc_html__( 'Font Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .dt-owl-item-description' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'post_content_hover_style',
			[
				'label' => esc_html__( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			'post_content_color_hover',
			[
				'label'     => esc_html__( 'Font Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .box-hover:hover .dt-owl-item-description,
					{{WRAPPER}} .elements-hover .dt-owl-item-description:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'post_content_bottom_margin',
			[
				'label'      => esc_html__( 'Gap Below Text', 'the7mk2' ),
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
					'{{WRAPPER}} .dt-owl-item-description' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();
		/**
		 * Icon section.
		 */
		$this->start_controls_section(
			'icon_section',
			[
				'label' => esc_html__( 'Icon', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label'      => esc_html__( 'Icon Size', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 16,
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
					'{{WRAPPER}}' => '--icon-font-size: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'icon_border_width',
			[
				'label'      => esc_html__( 'Border Width', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'default'    => [
					'unit' => 'px',
					'size' => 2,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 25,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .dt-owl-item-icon:before' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
					'{{WRAPPER}} .dt-owl-item-icon:after'  => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'icon_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'default'    => [
					'unit' => 'px',
					'size' => 100,
				],
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .dt-owl-item-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
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
			'icon_color',
			[
				'label'     => esc_html__( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .dt-owl-item-icon i'   => 'color: {{VALUE}}',
					'{{WRAPPER}} .dt-owl-item-icon svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .dt-owl-item-icon:before' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .dt-owl-item-icon:after'  => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .dt-owl-item-icon:before' => 'background: {{VALUE}};',
					'{{WRAPPER}} .dt-owl-item-icon:after'  => 'background: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_icon_hover',
			[
				'label' => esc_html__( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			'icon_color_hover',
			[
				'label'     => esc_html__( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .box-hover:hover .dt-owl-item-icon > i,  {{WRAPPER}} .elements-hover .dt-owl-item-icon:hover > i'     => 'color: {{VALUE}}',
					'{{WRAPPER}} .box-hover:hover .dt-owl-item-icon > svg,  {{WRAPPER}} .elements-hover .dt-owl-item-icon:hover > svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_border_color_hover',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'
					{{WRAPPER}} .dt-owl-item-icon:before,
					{{WRAPPER}} .dt-owl-item-icon:after { transition: opacity 0.3s ease; }
					{{WRAPPER}} .dt-owl-item-icon:after' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_bg_color_hover',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'
					{{WRAPPER}} .dt-owl-item-icon:before,
					{{WRAPPER}} .dt-owl-item-icon:after { transition: opacity 0.3s ease; }
					{{WRAPPER}} .dt-owl-item-icon:after' => 'background: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_design_image',
			[
				'label' => esc_html__( 'Image', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->template( Image_Aspect_Ratio::class )->add_style_controls();

		$this->add_control(
			'img_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .dt-owl-item-image, {{WRAPPER}} .dt-owl-item-image:before, {{WRAPPER}} .dt-owl-item-image:after' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .dt-owl-item-image > a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .dt-owl-item-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'image_scale_animation_on_hover',
			[
				'label'   => esc_html__( 'Scale Animation On Hover', 'the7mk2' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'quick_scale',
				'options' => [
					'disabled'    => esc_html__( 'Disabled', 'the7mk2' ),
					'quick_scale' => esc_html__( 'Quick scale', 'the7mk2' ),
					'slow_scale'  => esc_html__( 'Slow scale', 'the7mk2' ),
				],
			]
		);

		$this->start_controls_tabs( 'thumbnail_effects_tabs' );

		$this->start_controls_tab(
			'normal',
			[
				'label' => esc_html__( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'           => 'overlay_background',
				'types'          => [ 'classic', 'gradient' ],
				'exclude'        => [ 'image' ],
				'fields_options' => [
					'background' => [
						'label' => esc_html__( 'Overlay', 'the7mk2' ),
					],
				],
				'selector'       => '
				{{WRAPPER}} .dt-owl-item-image:before,
				{{WRAPPER}} .dt-owl-item-image:after
				',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'img_shadow',
				'selector' => '
				{{WRAPPER}} .dt-owl-item-image
				',
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'     => 'thumbnail_filters',
				'selector' => '
				{{WRAPPER}} .dt-owl-item-image img
				',
			]
		);

		$this->add_control(
			'thumbnail_opacity',
			[
				'label'      => esc_html__( 'Opacity', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .dt-owl-item-image' => 'opacity: calc({{SIZE}}/100)',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'hover',
			[
				'label' => esc_html__( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'           => 'overlay_hover_background',
				'types'          => [ 'classic', 'gradient' ],
				'exclude'        => [ 'image' ],
				'fields_options' => [
					'background' => [
						'label' => esc_html__( 'Overlay', 'the7mk2' ),
					],
					'color'      => [
						'selectors' => [
							'
							{{SELECTOR}},
							{{WRAPPER}} .dt-owl-item-image:before { transition: opacity 0.3s ease; }
							{{SELECTOR}}' => 'background: {{VALUE}};',
						],
					],

				],
				'selector'       => '{{WRAPPER}} .dt-owl-item-image:after',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'img_hover_shadow',

				'selector' => '{{WRAPPER}} .box-hover:hover .dt-owl-item-image, {{WRAPPER}} .elements-hover .dt-owl-item-image:hover',
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'     => 'thumbnail_hover_filters',

				'selector' => '{{WRAPPER}} .box-hover:hover .dt-owl-item-image img, {{WRAPPER}} .elements-hover .dt-owl-item-image:hover img',
			]
		);

		$this->add_control(
			'thumbnail_hover_opacity',
			[
				'label'      => esc_html__( 'Opacity', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'selectors'  => [
					'
					{{WRAPPER}} .dt-owl-item-image { transition: opacity 0.3s ease; }
					{{WRAPPER}} .box-hover:hover .dt-owl-item-image,
					{{WRAPPER}} .elements-hover .dt-owl-item-image:hover' => 'opacity: calc({{SIZE}}/100)',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->template( Button::class )->add_style_controls(
			Button::ICON_MANAGER,
			[],
			[
				'gap_above_button' => null,
			]
		);
		$this->template( Arrows::class )->add_style_controls();
		$this->template( Bullets::class )->add_style_controls();
	}

	/**
	 * Render widget.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( empty( $settings['list'] ) ) {
			return;
		}

		$this->remove_image_hooks();

		$this->template( Arrows::class )->add_container_render_attributes( 'wrapper' );
		$this->add_container_class_render_attribute( 'wrapper' );
		$this->add_container_data_render_attributes( 'wrapper' );

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<div ' . $this->get_render_attribute_string( 'wrapper' ) . '>';

		$this->add_render_attribute( 'button', 'class', [ 'dt-btn-s dt-btn', 'dt-slide-button' ] );

		$title_element     = Utils::validate_html_tag( $settings['title_tag'] );
		$subtitle_element  = Utils::validate_html_tag( $settings['subtitle_tag'] );
		$slide_count       = 0;
		$img_wrapper_class = implode(
			' ',
			array_filter(
				[
					'dt-owl-item-image',
					$this->template( Image_Size::class )->get_wrapper_class(),
					$this->template( Image_Aspect_Ratio::class )->get_wrapper_class(),
				]
			)
		);

		foreach ( $settings['list'] as $slide ) {
			$btn_attributes       = '';
			$btn_attributes_array = [];
			$slide_attributes     = '';
			$slide_element        = 'div';
			$btn_element          = 'div';
			$icon_element         = 'div';
			$wrap_class           = '';
			$title_link           = '';
			$title_link_close     = '';

			if ( $slide['graphic_type'] === 'none' ) {
				$wrap_class .= ' hide-icon';
			}
			if ( $slide['button'] === '' ) {
				$wrap_class .= ' hide-btn';
			}
			if ( 'y' === $settings['link_hover'] && 'button' === $settings['link_click'] ) {
				$wrap_class .= ' elements-hover';
			} elseif ( 'y' === $settings['link_hover'] ) {
				$wrap_class .= ' box-hover';
			}

			if ( ! empty( $slide['link']['url'] ) ) {
				$this->add_link_attributes( 'slide_link' . $slide_count, $slide['link'] );

				if ( 'button' === $settings['link_click'] ) {
					$wrap_class          .= ' elements-hover';
					$btn_element          = 'a';
					$icon_element         = 'a';
					$btn_attributes       = $this->get_render_attribute_string( 'slide_link' . $slide_count );
					$btn_attributes_array = $this->get_render_attributes( 'slide_link' . $slide_count );

					$title_link       = '<a ' . $btn_attributes . '>';
					$title_link_close = '</a>';
				} else {
					$wrap_class      .= ' box-hover';
					$slide_element    = 'a';
					$slide_attributes = $this->get_render_attribute_string( 'slide_link' . $slide_count );
				}
			}

			echo '<' . $slide_element . '  class="dt-owl-item-wrap' . $wrap_class . '"  ' . $slide_attributes . '>';
			echo '<div class="dt-owl-item-inner ">';

			if ( $slide['list_icon'] ) {
				echo '<' . $icon_element . ' ' . $btn_attributes . '  class="dt-owl-item-icon">';
				Icons_Manager::render_icon(
					$slide['list_icon'],
					[
						'aria-hidden' => 'true',
						'class'       => 'open-button',
					],
					'i'
				);
				echo '</' . $icon_element . '>';
			} elseif ( 'image' === $slide['graphic_type'] && ! empty( $slide['list_image']['id'] ) ) {
				echo '<' . $icon_element . ' ' . $btn_attributes . ' class="' . $img_wrapper_class . '"> ';
				echo $this->template( Image_Size::class )->get_image( $slide['list_image']['id'] );
				echo '</' . $icon_element . '>';
			}

			if ( $slide['list_title'] ) {
				echo '<' . $title_element . '  class="dt-owl-item-heading">' . $title_link . wp_kses_post( $slide['list_title'] ) . $title_link_close . '</' . $title_element . '>';
			}
			if ( $slide['list_subtitle'] ) {
				echo '<' . $subtitle_element . '  class="dt-owl-item-subtitle">' . wp_kses_post( $slide['list_subtitle'] ) . '</' . $subtitle_element . '>';
			}
			if ( $slide['list_content'] ) {
				echo '<div class="dt-owl-item-description">' . wp_kses_post( $slide['list_content'] ) . '</div>';
			}

			if ( $slide['button'] || $this->template( Button::class )->is_icon_visible() ) {
				// Cleanup button render attributes.
				$this->remove_render_attribute( 'box-button' );

				$this->add_render_attribute( 'box-button', $btn_attributes_array ?: [] );
				$this->add_render_attribute( 'box-button', 'class', 'dt-slide-button' );

				$this->template( Button::class )->render_button( 'box-button', esc_html( $slide['button'] ), $btn_element );
			}

			echo '</div>';
			echo '</' . $slide_element . '>';

			++$slide_count;
		}

		echo '</div>';

		$this->template( Arrows::class )->render();

		$this->add_image_hooks();
	}

	/**
	 * @param string $element Element name.
	 *
	 * @return void
	 */
	protected function add_container_class_render_attribute( $element ) {
		$class = [ 'owl-carousel', 'testimonials-carousel', 'elementor-owl-carousel-call', 'the7-elementor-widget' ];

		// Unique class.
		$class[] = $this->get_unique_class();

		$settings = $this->get_settings_for_display();

		if ( $settings['image_scale_animation_on_hover'] === 'quick_scale' ) {
			$class[] = 'quick-scale-img';
		} elseif ( $settings['image_scale_animation_on_hover'] === 'slow_scale' ) {
			$class[] = 'scale-img';
		}

		$this->add_render_attribute( $element, 'class', $class );
	}

	/**
	 * @param string $element Element name.
	 *
	 * @return void
	 */
	protected function add_container_data_render_attributes( $element ) {
		$settings = $this->get_settings_for_display();

		$data_atts = [
			'data-scroll-mode'    => $settings['slide_to_scroll'] === 'all' ? 'page' : '1',
			'data-auto-height'    => $settings['adaptive_height'] ? 'true' : 'false',
			'data-speed'          => $settings['speed'],
			'data-autoplay'       => $settings['autoplay'] ? 'true' : 'false',
			'data-autoplay_speed' => $settings['autoplay_speed'],
		];

		$this->add_render_attribute( $element, $data_atts );
	}
}
