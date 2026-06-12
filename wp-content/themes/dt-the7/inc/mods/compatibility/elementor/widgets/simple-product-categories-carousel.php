<?php
/**
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Core\Breakpoints\Manager as Breakpoints;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Plugin;
use Elementor\Utils;
use stdClass;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Less_Vars_Decorator_Interface;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Arrows;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Bullets;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Button;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Image_Aspect_Ratio;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Image_Size;

defined( 'ABSPATH' ) || exit;

/**
 * Simple_Product_Categories_Carousel class.
 */
class Simple_Product_Categories_Carousel extends Simple_Product_Categories {

	/**
	 * Get element name.
	 * Retrieve the element name.
	 *
	 * @return string The name.
	 */
	public function get_name() {
		return 'the7-simple-product-categories-carousel';
	}

	/**
	 * @return string
	 */
	protected function the7_title() {
		return esc_html__( 'Product Categories Carousel', 'the7mk2' );
	}

	/**
	 * @return string
	 */
	protected function the7_icon() {
		return 'eicon-products';
	}

	/**
	 * @return string
	 */
	protected function get_less_file_name() {
		return PRESSCORE_THEME_DIR . '/css/dynamic-less/elementor/the7-simple-product-categories-carousel.less';
	}

	/**
	 * @return string[]
	 */
	public function get_style_depends() {
		return [ 'the7-simple-product-categories-carousel', 'the7-simple-common', 'the7-carousel-navigation' ];
	}

	/**
	 * @return string[]
	 */
	public function get_script_depends() {
		$scripts = [
			'the7-simple-product-categories-carousel',
		];

		if ( $this->is_preview_mode() ) {
			$scripts[] = 'the7-elements-carousel-widget-preview';
		}

		return $scripts;
	}

	/**
	 * Register widget assets.
	 *
	 * @see The7_Elementor_Widget_Base::__construct()
	 */
	protected function register_assets() {
		the7_register_style(
			'the7-simple-product-categories-carousel',
			PRESSCORE_THEME_URI . '/css/compatibility/elementor/the7-simple-product-categories-carousel.css'
		);

		the7_register_script_in_footer(
			'the7-simple-product-categories-carousel',
			PRESSCORE_THEME_URI . '/js/compatibility/elementor/the7-simple-product-categories-carousel.js',
			[ 'dt-main' ]
		);

		the7_register_script_in_footer(
			'the7-simple-product-categories-carousel-preview',
			PRESSCORE_ADMIN_URI . '/assets/js/elementor/the7-simple-product-categories-carousel-preview.js'
		);
	}

	/**
	 * Register widget controls.
	 */
	protected function register_controls() {

		// Content.
		$this->add_query_controls();
		$this->add_layout_content_controls();
		$this->add_content_controls();
		$this->add_scrolling_controls();
		$this->template( Arrows::class )->add_content_controls();
		$this->template( Bullets::class )->add_content_controls();

		// Style.
		$this->add_widget_title_style_controls();

		/**
		 * Common simple box style settings.
		 *
		 * @see Simple_Widget_Base::add_box_content_style_controls()
		 */
		$this->add_box_content_style_controls();

		/**
		 * Common simple image style settings.
		 *
		 * @see Simple_Widget_Base::add_image_style_controls()
		 */
		$this->add_image_style_controls(
			[
				'show_product_image' => 'y',
			]
		);

		$this->add_content_area_style_controls();
		$this->add_title_style_controls();
		$this->add_meta_style_controls();
		$this->add_description_style_controls();
		$this->template( Button::class )->add_style_controls(
			Button::ICON_MANAGER,
			[
				'show_read_more_button' => 'y',
			],
			[
				'button_icon' => [
					'default' => [
						'value'   => 'dt-icon-the7-arrow-552',
						'library' => 'the7-icons',
					],
				],
			]
		);
		$this->template( Arrows::class )->add_style_controls();
		$this->template( Bullets::class )->add_style_controls();
	}

	/**
	 * @return void
	 */
	protected function add_query_controls() {

		$this->start_controls_section(
			'query_section',
			[
				'label' => esc_html__( 'Query', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'source',
			[
				'label'       => esc_html__( 'Source', 'the7mk2' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => [
					''                      => esc_html__( 'Show All', 'the7mk2' ),
					'by_id'                 => esc_html__( 'Manual Selection', 'the7mk2' ),
					'by_parent'             => esc_html__( 'By Parent', 'the7mk2' ),
					'current_subcategories' => esc_html__( 'Current Subcategories', 'the7mk2' ),
				],
				'label_block' => true,
			]
		);

		$categories = get_terms( 'product_cat' );

		$options = [];
		foreach ( $categories as $category ) {
			$options[ $category->term_id ] = $category->name;
		}

		$this->add_control(
			'categories',
			[
				'label'       => esc_html__( 'Categories', 'the7mk2' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => $options,
				'default'     => [],
				'label_block' => true,
				'multiple'    => true,
				'condition'   => [
					'source' => 'by_id',
				],
			]
		);

		$parent_options = [ '0' => esc_html__( 'Only Top Level', 'the7mk2' ) ] + $options;
		$this->add_control(
			'parent',
			[
				'label'     => esc_html__( 'Parent', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '0',
				'options'   => $parent_options,
				'condition' => [
					'source' => 'by_parent',
				],
			]
		);

		$this->add_control(
			'hide_empty',
			[
				'label'     => esc_html__( 'Hide Empty', 'the7mk2' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => '',
				'label_on'  => 'Hide',
				'label_off' => 'Show',
			]
		);

		$this->add_control(
			'orderby',
			[
				'label'   => esc_html__( 'Order By', 'the7mk2' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'name',
				'options' => [
					'name'        => esc_html__( 'Name', 'the7mk2' ),
					'slug'        => esc_html__( 'Slug', 'the7mk2' ),
					'description' => esc_html__( 'Description', 'the7mk2' ),
					'count'       => esc_html__( 'Count', 'the7mk2' ),
				],
			]
		);

		$this->add_control(
			'order',
			[
				'label'   => esc_html__( 'Order', 'the7mk2' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'desc',
				'options' => [
					'asc'  => esc_html__( 'ASC', 'the7mk2' ),
					'desc' => esc_html__( 'DESC', 'the7mk2' ),
				],
			]
		);

		$this->add_control(
			'dis_posts_total',
			[
				'label'       => esc_html__( 'Total Number Of Posts', 'the7mk2' ),
				'description' => esc_html__( 'Leave empty to display all posts.', 'the7mk2' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => '12',
				'condition'   => [
					'source!' => 'current_subcategories',
				],
			]
		);

		$this->add_control(
			'posts_offset',
			[
				'label'       => esc_html__( 'Posts Offset', 'the7mk2' ),
				'description' => esc_html__(
					'Offset for posts query (i.e. 2 means, posts will be displayed starting from the third post).',
					'the7mk2'
				),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 0,
				'min'         => 0,
				'condition'   => [
					'source!' => 'current_subcategories',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function add_scrolling_controls() {

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
	}

	/**
	 * @return void
	 */
	protected function add_layout_content_controls() {

		$this->start_controls_section(
			'layout_content_section',
			[
				'label' => esc_html__( 'Layout', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
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
			'title_tag',
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

		if ( ! Plugin::$instance->breakpoints->get_active_breakpoints( Breakpoints::BREAKPOINT_KEY_WIDESCREEN ) ) {
			$this->add_control(
				'widget_columns_wide_desktop',
				[
					'label'              => esc_html__( 'Columns On A Wide Desktop', 'the7mk2' ),
					'type'               => Controls_Manager::NUMBER,
					'default'            => '',
					'min'                => 1,
					'max'                => 12,
					'separator'          => 'before',
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
				'default'            => 1,
				'tablet_default'     => 1,
				'mobile_default'     => 1,
				'min'                => 1,
				'max'                => 12,
				'frontend_available' => true,
			]
		);

		$this->template( Image_Size::class )->add_style_controls();

		$this->add_responsive_control(
			'gap_between_posts',
			[
				'label'              => esc_html__( 'Columns Gap (px)', 'the7mk2' ),
				'type'               => Controls_Manager::SLIDER,
				'size_units'         => [ 'px' ],
				'default'            => [
					'size' => 40,
				],
				'range'              => [
					'px' => [
						'max' => 100,
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
					'{{WRAPPER}} .owl-stage, {{WRAPPER}} .owl-carousel' => '--stage-top-gap:{{TOP}}{{UNIT}}; --stage-right-gap:{{RIGHT}}{{UNIT}};  --stage-left-gap:{{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .owl-stage-outer' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
				'render_type' => 'template',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function add_image_controls() {

		$this->start_controls_section(
			'featured_image',
			[
				'label' => esc_html__( 'Featured Image', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function add_content_controls() {

		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Content', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'link_click',
			[
				'label'   => esc_html__( 'Apply Link & Hover', 'the7mk2' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'button',
				'options' => [
					'box'    => esc_html__( 'Whole box', 'the7mk2' ),
					'button' => esc_html__( "Separate element's", 'the7mk2' ),
				],
			]
		);

		$this->add_control(
			'show_product_image',
			[
				'label'        => esc_html__( 'Image', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the7mk2' ),
				'label_off'    => esc_html__( 'Hide', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'show_product_title',
			[
				'label'        => esc_html__( 'Title', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the7mk2' ),
				'label_off'    => esc_html__( 'Hide', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'post_title_tag',
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
				'default'   => 'h5',
				'condition' => [
					'show_product_title' => 'y',
				],
			]
		);

		$this->add_control(
			'title_width',
			[
				'label'     => esc_html__( 'Title Width', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'normal'      => esc_html__( 'Normal', 'the7mk2' ),
					'crp-to-line' => esc_html__( 'Crop to one line', 'the7mk2' ),
				],
				'default'   => 'normal',
				'condition' => [
					'show_product_title' => 'y',
				],
			]
		);

		$this->add_control(
			'title_words_limit',
			[
				'label'       => esc_html__( 'Maximum Number Of Words', 'the7mk2' ),
				'description' => esc_html__( 'Leave empty to show the entire title.', 'the7mk2' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => '',
				'min'         => 1,
				'max'         => 20,
				'condition'   => [
					'show_product_title' => 'y',
					'title_width'        => 'normal',
				],
			]
		);

		$this->add_control(
			'show_term_description',
			[
				'label'        => esc_html__( 'Description', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the7mk2' ),
				'label_off'    => esc_html__( 'Hide', 'the7mk2' ),
				'return_value' => 'show_excerpt',
				'default'      => 'show_excerpt',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'description_width',
			[
				'label'     => esc_html__( 'Description Width', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'normal'      => esc_html__( 'Normal', 'the7mk2' ),
					'crp-to-line' => esc_html__( 'Crop to one line', 'the7mk2' ),
				],
				'default'   => 'normal',
				'condition' => [
					'show_term_description' => 'show_excerpt',
				],
			]
		);

		$this->add_control(
			'excerpt_words_limit',
			[
				'label'       => esc_html__( 'Maximum Number Of Words', 'the7mk2' ),
				'description' => esc_html__( 'Leave empty to show the entire excerpt.', 'the7mk2' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => '',
				'condition'   => [
					'show_term_description' => 'show_excerpt',
					'description_width'     => 'normal',
				],
			]
		);

		$this->add_control(
			'products_count',
			[
				'label'        => esc_html__( 'Products Count', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the7mk2' ),
				'label_off'    => esc_html__( 'Hide', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'products_custom_format',
			[
				'label'        => esc_html__( 'Custom Format', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => false,
				'return_value' => 'yes',
				'condition'    => [
					'products_count' => 'y',
				],
			]
		);

		$this->add_control(
			'string_no_products',
			[
				'label'       => esc_html__( 'No Products', 'the7mk2' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'No Products', 'the7mk2' ),
				'condition'   => [
					'products_custom_format' => 'yes',
					'products_count'         => 'y',
				],
			]
		);

		$this->add_control(
			'string_one_product',
			[
				'label'       => esc_html__( 'One Product', 'the7mk2' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'One Product', 'the7mk2' ),
				'condition'   => [
					'products_custom_format' => 'yes',
					'products_count'         => 'y',
				],
			]
		);

		$this->add_control(
			'string_products',
			[
				'label'       => esc_html__( 'Products', 'the7mk2' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__( '%s Products', 'the7mk2' ),
				'condition'   => [
					'products_custom_format' => 'yes',
					'products_count'         => 'y',
				],
			]
		);

		$this->add_control(
			'show_read_more_button',
			[
				'label'        => esc_html__( 'Button', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the7mk2' ),
				'label_off'    => esc_html__( 'Hide', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'read_more_button_text',
			[
				'label'     => esc_html__( 'Button Text', 'the7mk2' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'View Category', 'the7mk2' ),
				'condition' => [
					'show_read_more_button' => 'y',
				],
			]
		);

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

		$this->add_responsive_control(
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

		$this->add_responsive_control(
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
	 * @return void
	 */
	protected function add_content_area_style_controls() {
		// Title Style.
		$this->start_controls_section(
			'content_area_style',
			[
				'label' => esc_html__( 'Content Area', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'content_alignment',
			[
				'label'                => esc_html__( 'Alignment', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'label_block'          => false,
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
				'prefix_class'         => 'slide-h-position%s-',
				'default'              => 'left',
				'selectors_dictionary' => [
					'left'   => 'align-items: flex-start; text-align: left;',
					'center' => 'align-items: center; text-align: center;',
					'right'  => 'align-items: flex-end; text-align: right;',
				],
				'selectors'            => [
					'{{WRAPPER}} .post-entry-content' => '{{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'content_area_padding',
			[
				'label'      => esc_html__( 'Content Area Padding', 'the7mk2' ),
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
					'{{WRAPPER}} .post-entry-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function add_title_style_controls() {
		// Title Style.
		$this->start_controls_section(
			'title_style',
			[
				'label'     => esc_html__( 'Title', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_product_title' => 'y',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .heading',
			]
		);

		$this->start_controls_tabs( 'tabs_post_navigation_style' );

		$this->start_controls_tab(
			'tab_title_color_normal',
			[
				'label' => esc_html__( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_control(
			'text_color',
			[
				'label'     => esc_html__( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .product-name' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_title_color_hover',
			[
				'label' => esc_html__( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			'hover_color',
			[
				'label'     => esc_html__( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .product-name:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} a.post.wrapper:hover .product-name' => 'color: {{VALUE}};',
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
	protected function add_meta_style_controls() {
		$this->start_controls_section(
			'post_meta_style_section',
			[
				'label'     => esc_html__( 'Products Count', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'products_count' => 'y',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'post_meta',
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
				'selector'       => '{{WRAPPER}} .entry-meta',
			]
		);

		$this->start_controls_tabs( 'tabs_post_meta_style' );

		$this->start_controls_tab(
			'tab_post_meta_color_normal',
			[
				'label' => esc_html__( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_control(
			'tab_post_meta_color',
			[
				'label'     => esc_html__( 'Font Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .entry-meta' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_post_meta_color_hover',
			[
				'label' => esc_html__( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			'field_post_meta_color_hover',
			[
				'label'     => esc_html__( 'Font Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .entry-meta:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} a.post.wrapper:hover .entry-meta' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'post_meta_bottom_margin',
			[
				'label'      => esc_html__( 'Product Count Spacing Above', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .entry-meta' => 'margin-top: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function add_description_style_controls() {

		$this->start_controls_section(
			'short_description',
			[
				'label'     => esc_html__( 'Description', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_term_description' => 'show_excerpt',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'description_typography',
				'selector' => '{{WRAPPER}} .short-description',
			]
		);

		$this->start_controls_tabs( 'tabs_description_style' );

		$this->start_controls_tab(
			'tab_desc_color_normal',
			[
				'label' => esc_html__( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_control(
			'short_desc_color',
			[
				'label'     => esc_html__( 'Font Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .short-description' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_desc_color_hover',
			[
				'label' => esc_html__( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			'short_desc_color_hover',
			[
				'label'     => esc_html__( 'Font Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .short-description:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} a.post.wrapper:hover .short-description' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'gap_above_description',
			[
				'label'      => esc_html__( 'Description Spacing Above', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .short-description' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
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

	/**
	 * @return false|void
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$product_categories = $this->product_categories();
		if ( empty( $product_categories->terms ) ) {
			return false;
		}

		$this->print_inline_css();

		if ( $settings['show_widget_title'] === 'y' && $settings['widget_title_text'] ) {
			echo $this->display_widget_title( $settings['widget_title_text'], $settings['title_tag'] );
		}

		$this->template( Arrows::class )->add_container_render_attributes( 'inner-wrapper' );
		$this->add_container_class_render_attribute( 'inner-wrapper' );
		$this->add_container_data_render_attributes( 'inner-wrapper' );

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<div ' . $this->get_render_attribute_string( 'inner-wrapper' ) . '>';

		$index = 0;
		foreach ( $product_categories->terms as $category ) {
			$index++;

			$repeater_setting_key = $this->get_repeater_setting_key( 'text', 'link_wrapper', $index );

			$post_class_array = [
				'post',
				'visible',
				'wrapper',
			];

			if ( ! get_term_meta( $category->term_id, 'thumbnail_id', true ) ) {
				$post_class_array[] = 'no-img';
			}

			$link_key        = 'link_' . $index;
			$link_attridutes = $this->get_custom_link_attributes( $category );
			$this->add_link_attributes( $link_key, $link_attridutes, true );
			$btn_attributes = $this->get_render_attribute_string( $link_key );

			if ( 'button' === $settings['link_click'] ) {
				$parent_wrapper       = '<article class="dt-owl-item-wrap ' . esc_attr( implode( ' ', get_post_class( $post_class_array ) ) ) . '">';
				$parent_wrapper_close = '</article>';
			} else {
				$parent_wrapper       = '<a ' . $btn_attributes . ' class="dt-owl-item-wrap box-hover ' . esc_attr( implode( ' ', get_post_class( $post_class_array ) ) ) . '">';
				$parent_wrapper_close = '</a>';
			}

			echo $parent_wrapper;
			echo '<div class="post-content-wrapper">';
			if ( $settings['show_product_image'] ) {
				$post_media = $this->get_category_image( $category );
				if ( $post_media ) {
					echo '<div class="the7-simple-post-thumb">' . $post_media . '</div>';
				}
			}

			echo '<div class="post-entry-content">';
			if ( $settings['show_product_title'] ) {
				echo $this->get_category_title( $settings, $settings['post_title_tag'], $category );
			}

			if ( $settings['products_count'] ) {
				echo $this->get_category_count( $settings, $category );
			}

			if ( $settings['show_term_description'] === 'show_excerpt' ) {
				echo $this->get_category_description( $category );
			}

			if ( $settings['show_read_more_button'] ) {
				$this->render_details_btn( $category );
			}
			echo '</div>';
			echo '</div>';
			echo $parent_wrapper_close;
		}

		echo '</div>';

		$this->template( Arrows::class )->render();
	}

	/**
	 * @return stdClass
	 */
	protected function product_categories() {
		$settings = $this->get_settings_for_display();

		$attributes = [
			'number'     => $settings['dis_posts_total'] ? : 9999,
			'hide_empty' => 'yes' === $settings['hide_empty'],
			'orderby'    => $settings['orderby'],
			'order'      => $settings['order'],
			'parent'     => '',
			'include'    => [],
			'offset'     => $settings['posts_offset'],
		];

		if ( 'by_id' === $settings['source'] ) {
			$attributes['include'] = array_filter( (array) $settings['categories'] );
		} elseif ( 'by_parent' === $settings['source'] ) {
			$attributes['parent'] = $settings['parent'];
		} elseif ( 'current_subcategories' === $settings['source'] ) {
			$attributes['child_of'] = get_queried_object_id();
		}

		$terms = get_terms( 'product_cat', $attributes );

		$terms_query        = new stdClass();
		$terms_query->terms = $terms;

		return $terms_query;
	}

	/**
	 * @param array $settings Settings array.
	 *
	 * @return string
	 */
	protected function get_hover_icons_html_template( $settings ) {
		$a_atts = [
			'class' => 'the7-hover-icon',
		];

		return sprintf(
			'<span %s>%s</span>',
			the7_get_html_attributes_string( $a_atts ),
			$this->get_elementor_icon_html( $settings['hover_icon'], 'i' )
		);
	}

	/**
	 * @param string $text Text.
	 * @param string $tag HTML tag.
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
	 * @param array    $settings Settings array.
	 * @param string   $tag HTML tag.
	 * @param \WP_Term $category Term object.
	 *
	 * @return string
	 */
	protected function get_category_title( $settings, $tag, $category ) {
        $tag = Utils::validate_html_tag( $tag );
		$title_link = [
			'href'  => get_term_link( $category, 'product_cat' ),
			'class' => 'product-name',
		];

		if ( 'button' === $settings['link_click'] ) {
			$title_link_wrapper       = '<a ' . the7_get_html_attributes_string( $title_link ) . '>';
			$title_link_wrapper_close = '</a>';
		} else {
			$title_link['href']       = '';
			$title_link_wrapper       = '<span ' . the7_get_html_attributes_string( $title_link ) . '>';
			$title_link_wrapper_close = '</span>';
		}

		$title = $category->name;
		if ( $settings['title_words_limit'] && $settings['title_width'] === 'normal' ) {
			$title = wp_trim_words( $title, $settings['title_words_limit'] );
		}

		$output  = '<' . $tag . ' class="heading">';
		$output .= sprintf( '%s%s%s', $title_link_wrapper, $title, $title_link_wrapper_close );
		$output .= '</' . $tag . '>';

		return $output;
	}

	/**
	 * @param array    $settings Settings array.
	 * @param \WP_Term $category Term object.
	 *
	 * @return string
	 */
	protected function get_category_count( $settings, $category ) {
		$default_strings = [
			'string_no_products' => esc_html__( 'No Products', 'the7mk2' ),
			'string_one_product' => esc_html__( 'One Product', 'the7mk2' ),
			'string_products'    => esc_html__( '%s Products', 'the7mk2' ),
		];

		if ( 'yes' === $settings['products_custom_format'] ) {
			if ( ! empty( $settings['string_no_products'] ) ) {
				$default_strings['string_no_products'] = $settings['string_no_products'];
			}

			if ( ! empty( $settings['string_one_product'] ) ) {
				$default_strings['string_one_product'] = $settings['string_one_product'];
			}

			if ( ! empty( $settings['string_products'] ) ) {
				$default_strings['string_products'] = $settings['string_products'];
			}
		}

		$num_products = (int) $category->count;

		if ( 0 === $num_products ) {
			$string = $default_strings['string_no_products'];
		} elseif ( 1 === $num_products ) {
			$string = $default_strings['string_one_product'];
		} else {
			$string = sprintf( $default_strings['string_products'], $num_products );
		}

		return '<div class="entry-meta">' . wp_kses_post( $string ) . '</div>';
	}

	/**
	 * @param \WP_Term $category Term object.
	 *
	 * @return string|void
	 */
	protected function get_category_description( $category ) {
		$settings = $this->get_settings_for_display();

		$excerpt = $category->description;
		if ( ! $excerpt ) {
			return;
		}

		if ( $settings['excerpt_words_limit'] && $settings['description_width'] === 'normal' ) {
			$excerpt = wp_trim_words( $excerpt, $settings['excerpt_words_limit'] );
		}

		$output  = '<p class="short-description">';
		$output .= wp_kses_post( $excerpt );
		$output .= '</p>';

		return $output;
	}

	/**
	 * @param string $element Element name.
	 *
	 * @return void
	 */
	protected function add_container_class_render_attribute( $element ) {

		$class = [
			'owl-carousel',
			'the7-elementor-widget',
			'the7-simple-widget-product-categories-carousel',
			'elementor-owl-carousel-call',
			'loading-effect-none',
			'classic-layout-list',
		];

		// Unique class.
		$class[] = $this->get_unique_class();

		$settings = $this->get_settings_for_display();

		if ( $settings['title_width'] === 'crp-to-line' ) {
			$class[] = 'title-to-line';
		}

		if ( $settings['description_width'] === 'crp-to-line' ) {
			$class[] = 'desc-to-line';
		}

		if ( ! $settings['show_product_image'] ) {
			$class[] = 'hide-product-image';
		}

		$this->add_render_attribute( $element, 'class', $class );
	}

	/**
	 * @param  The7_Elementor_Less_Vars_Decorator_Interface $less_vars Less vars manager object.
	 *
	 * @return void
	 */
	protected function less_vars( The7_Elementor_Less_Vars_Decorator_Interface $less_vars ) {
		$settings = $this->get_settings_for_display();

		$less_vars->add_keyword(
			'unique-shortcode-class-name',
			$this->get_unique_class(),
			'~"%s"'
		);
	}
}
