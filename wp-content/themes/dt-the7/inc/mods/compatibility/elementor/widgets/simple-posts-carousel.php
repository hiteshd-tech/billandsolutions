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
use The7\Mods\Compatibility\Elementor\Style\Posts_Masonry_Style;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Less_Vars_Decorator_Interface;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Arrows;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Bullets;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Button;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Image_Aspect_Ratio;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Image_Size;
use The7_Categorization_Request;
use The7_Query_Builder;
use The7_Related_Query_Builder;

defined( 'ABSPATH' ) || exit;

/**
 *  Simple_Posts_Carousel class.
 */
class Simple_Posts_Carousel extends Simple_Posts {

	/**
	 * Get element name.
	 * Retrieve the element name.
	 *
	 * @return string The name.
	 */
	public function get_name() {
		return 'the7-elements-simple-posts-carousel';
	}

	/**
	 * @return string
	 */
	protected function the7_title() {
		return esc_html__( 'Simple Posts Carousel', 'the7mk2' );
	}

	/**
	 * @return string
	 */
	protected function the7_icon() {
		return 'eicon-posts-carousel';
	}

	/**
	 * @return string
	 */
	protected function get_less_file_name() {
		return PRESSCORE_THEME_DIR . '/css/dynamic-less/elementor/the7-simple-posts-carousel.less';
	}

	/**
	 * Register widget assets.
	 *
	 * @see The7_Elementor_Widget_Base::__construct()
	 */
	protected function register_assets() {
		the7_register_style(
			$this->get_name(),
			PRESSCORE_THEME_URI . '/css/compatibility/elementor/the7-simple-posts-carousel.css',
			[ 'the7-simple-common' ]
		);

		the7_register_script_in_footer(
			$this->get_name(),
			PRESSCORE_THEME_URI . '/js/compatibility/elementor/the7-simple-posts-carousel.js',
			[ 'dt-main' ]
		);
	}

	/**
	 * @return string[]
	 */
	public function get_style_depends() {
		return [ $this->get_name(), 'the7-carousel-navigation' ];
	}

	/**
	 * @return string[]
	 */
	public function get_script_depends() {
		$scripts = [
			$this->get_name(),
		];

		if ( $this->is_preview_mode() ) {
			$scripts[] = $this->get_name() . '-preview';
		}

		return $scripts;
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
				'show_post_image' => 'y',
			]
		);

		$this->add_content_area_style_controls();
		$this->add_title_style_controls();
		$this->add_meta_style_controls();
		$this->add_excerpt_style_controls();
		$this->template( Button::class )->add_style_controls(
			Button::ICON_MANAGER,
			[
				'show_read_more_button' => 'y',
				'article_links'         => 'y',
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
		/**
		 * Must have section_id = query_section to work properly.
		 *
		 * @see elements-widget-settings.js:onEditSettings()
		 */
		$this->start_controls_section(
			'query_section',
			[
				'label' => esc_html__( 'Query', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'post_type',
			[
				'label'   => esc_html__( 'Source', 'the7mk2' ),
				'type'    => Controls_Manager::SELECT2,
				'default' => 'post',
				'options' => the7_elementor_elements_widget_post_types() + [ 'related' => esc_html__( 'Related', 'the7mk2' ) ],
				'classes' => 'select2-medium-width',
			]
		);

		$this->add_control(
			'taxonomy',
			[
				'label'     => esc_html__( 'Select Taxonomy', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'category',
				'options'   => [],
				'classes'   => 'select2-medium-width',
				'condition' => [
					'post_type!' => [ '', 'current_query' ],
				],
			]
		);

		$this->add_control(
			'terms',
			[
				'label'     => esc_html__( 'Select Terms', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT2,
				'default'   => '',
				'multiple'  => true,
				'options'   => [],
				'classes'   => 'select2-medium-width',
				'condition' => [
					'taxonomy!'  => '',
					'post_type!' => [ 'current_query', 'related' ],
				],
			]
		);

		$this->add_control(
			'order',
			[
				'label'     => esc_html__( 'Order', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'desc',
				'options'   => [
					'asc'  => esc_html__( 'Ascending', 'the7mk2' ),
					'desc' => esc_html__( 'Descending', 'the7mk2' ),
				],
				'condition' => [
					'post_type!' => 'current_query',
				],
			]
		);

		$this->add_control(
			'orderby',
			[
				'label'     => esc_html__( 'Order By', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'date',
				'options'   => [
					'date'          => esc_html__( 'Date', 'the7mk2' ),
					'title'         => esc_html__( 'Name', 'the7mk2' ),
					'ID'            => esc_html__( 'ID', 'the7mk2' ),
					'modified'      => esc_html__( 'Modified', 'the7mk2' ),
					'comment_count' => esc_html__( 'Comment count', 'the7mk2' ),
					'menu_order'    => esc_html__( 'Menu order', 'the7mk2' ),
					'rand'          => esc_html__( 'Rand', 'the7mk2' ),
				],
				'condition' => [
					'post_type!' => 'current_query',
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
					'post_type!' => 'current_query',
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
					'post_type!' => 'current_query',
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
	protected function add_content_controls() {

		$this->start_controls_section(
			'post_content_section',
			[
				'label' => esc_html__( 'Content', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'article_links',
			[
				'label'        => esc_html__( 'Links To A Single Post', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'the7mk2' ),
				'label_off'    => esc_html__( 'No', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
			]
		);

		$this->add_control(
			'article_links_goes_to',
			[
				'label'     => esc_html__( 'Links Lead To', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'posts',
				'options'   => [
					'posts'                => esc_html__( 'Posts', 'the7mk2' ),
					'external_or_posts'    => esc_html__( 'External links or posts', 'the7mk2' ),
					'external_or_disabled' => esc_html__( 'External links or disabled', 'the7mk2' ),
				],
				'condition' => [
					'article_links' => 'y',
				],
			]
		);

		$this->add_control(
			'article_link_meta_field',
			[
				'label'       => esc_html__( 'Link Meta Field', 'the7mk2' ),
				'description' => esc_html__( 'Post meta field name, f.e. site_link, with url.', 'the7mk2' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => 'meta_field_name',
				'condition'   => [
					'post_type!'             => 'dt_portfolio',
					'article_links_goes_to!' => 'posts',
					'article_links'          => 'y',
				],
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
			'show_post_image',
			[
				'label'        => esc_html__( 'Image', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the7mk2' ),
				'label_off'    => esc_html__( 'Hide', 'the7mk2' ),
				'return_value' => 'y',
				'separator'    => 'before',
				'default'      => 'y',
			]
		);

		$this->add_control(
			'show_post_title',
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
					'show_post_title' => 'y',
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
					'show_post_title' => 'y',
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
					'show_post_title' => 'y',
					'title_width'     => 'normal',
				],
			]
		);

		$this->add_control(
			'post_content',
			[
				'label'        => esc_html__( 'Excerpt', 'the7mk2' ),
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
				'label'     => esc_html__( 'Width', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'normal'      => esc_html__( 'Normal', 'the7mk2' ),
					'crp-to-line' => esc_html__( 'Crop to one line', 'the7mk2' ),
				],
				'default'   => 'normal',
				'condition' => [
					'post_content' => 'show_excerpt',
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
					'post_content'      => 'show_excerpt',
					'description_width' => 'normal',
				],
			]
		);

		$this->add_control(
			'post_terms',
			[
				'label'        => esc_html__( 'Category', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the7mk2' ),
				'label_off'    => esc_html__( 'Hide', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'post_terms_link',
			[
				'label'        => esc_html__( 'Link', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'the7mk2' ),
				'label_off'    => esc_html__( 'No', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
				'condition'    => [
					'post_terms' => 'y',
					'link_click' => 'button',
				],
			]
		);

		$this->add_control(
			'post_author',
			[
				'label'        => esc_html__( 'Author', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the7mk2' ),
				'label_off'    => esc_html__( 'Hide', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'post_author_link',
			[
				'label'        => esc_html__( 'Link', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'the7mk2' ),
				'label_off'    => esc_html__( 'No', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
				'condition'    => [
					'post_author' => 'y',
					'link_click'  => 'button',
				],
			]
		);

		$this->add_control(
			'post_date',
			[
				'label'        => esc_html__( 'Date', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the7mk2' ),
				'label_off'    => esc_html__( 'Hide', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'post_date_link',
			[
				'label'        => esc_html__( 'Link', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'the7mk2' ),
				'label_off'    => esc_html__( 'No', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
				'condition'    => [
					'post_date'  => 'y',
					'link_click' => 'button',
					'post_type'  => [ 'post', 'current_query', 'related' ],
				],
			]
		);

		$this->add_control(
			'post_comments',
			[
				'label'        => esc_html__( 'Comments Count', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the7mk2' ),
				'label_off'    => esc_html__( 'Hide', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'post_comments_link',
			[
				'label'        => esc_html__( 'Link', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'the7mk2' ),
				'label_off'    => esc_html__( 'No', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
				'condition'    => [
					'post_comments' => 'y',
					'link_click'    => 'button',
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
				'condition'    => [
					'article_links' => 'y',
				],
			]
		);

		$this->add_control(
			'read_more_button_text',
			[
				'label'     => esc_html__( 'Button Text', 'the7mk2' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Read more', 'the7mk2' ),
				'condition' => [
					'show_read_more_button' => 'y',
					'article_links'         => 'y',
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
					'show_post_title' => 'y',
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
					'{{WRAPPER}} .post-title' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .post-title:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} a.post.wrapper:hover .post-title' => 'color: {{VALUE}};',
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
				'label'      => esc_html__( 'Meta Information', 'the7mk2' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'post_date',
							'operator' => '!==',
							'value'    => '',
						],
						[
							'name'     => 'post_terms',
							'operator' => '!==',
							'value'    => '',
						],
						[
							'name'     => 'post_author',
							'operator' => '!==',
							'value'    => '',
						],
						[
							'name'     => 'post_comments',
							'operator' => '!==',
							'value'    => '',
						],
					],
				],
			]
		);

		$this->add_control(
			'post_meta_separator',
			[
				'label'       => esc_html__( 'Separator Between', 'the7mk2' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '•',
				'placeholder' => '',
				'selectors'   => [
					'{{WRAPPER}} .entry-meta .meta-item:not(:first-child):before' => 'content: "{{VALUE}}";',
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
					'{{WRAPPER}}' => '--meta-color: {{VALUE}}',
					'{{WRAPPER}} .entry-meta .meta-item, {{WRAPPER}} .entry-meta .meta-item *' => 'color: {{VALUE}}',
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
					'{{WRAPPER}} .entry-meta .meta-item:hover, {{WRAPPER}} .entry-meta .meta-item:hover *' => 'color: {{VALUE}}',
					'{{WRAPPER}} a.post.wrapper:hover .entry-meta .meta-item, {{WRAPPER}} a.post.wrapper:hover .entry-meta .meta-item *' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'post_meta_bottom_margin',
			[
				'label'      => esc_html__( 'Meta Spacing Above', 'the7mk2' ),
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
	protected function add_excerpt_style_controls() {
		$this->start_controls_section(
			'short_description',
			[
				'label'     => esc_html__( 'Excerpt', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'post_content' => 'show_excerpt',
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
				'label'      => esc_html__( 'Excerpt Spacing Above', 'the7mk2' ),
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
	 * @param sring $element Element name.
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
	 * @return mixed|\WP_Query
	 */
	protected function get_query() {
		$settings  = $this->get_settings_for_display();
		$post_type = $settings['post_type'];

		if ( $post_type === 'current_query' ) {
			return $GLOBALS['wp_query'];
		}

		$taxonomy = $settings['taxonomy'];
		$terms    = $settings['terms'];

		// Loop query.
		$query_args = [
			'posts_offset'   => $settings['posts_offset'],
			'post_type'      => $post_type,
			'order'          => $settings['order'],
			'orderby'        => $settings['orderby'],
			'posts_per_page' => $settings['dis_posts_total'],
		];

		if ( $post_type === 'related' ) {
			$query_builder = new The7_Related_Query_Builder( $query_args );
		} else {
			$query_builder = new The7_Query_Builder( $query_args );
		}

		$query_builder->from_terms( $taxonomy, $terms );

		$request = new The7_Categorization_Request();
		if ( $request->taxonomy && $request->not_empty() ) {
			$query_builder->with_categorizaition( $request );
		}

		return $query_builder->query();
	}

	/**
	 * @return void
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( ! in_array( $settings['post_type'], [ 'current_query', 'related' ], true ) && ! post_type_exists( $settings['post_type'] ) ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo the7_elementor_get_message_about_disabled_post_type();

			return;
		}

		$query = $this->get_query();
		if ( ! $query->have_posts() ) {
			if ( $settings['post_type'] === 'current_query' ) {
				$this->render_nothing_found_message();
			}
			return;
		}

		$this->print_inline_css();

		if ( $settings['show_widget_title'] === 'y' && $settings['widget_title_text'] ) {
			echo $this->display_widget_title( $settings['widget_title_text'], $settings['title_tag'] );
		}

		$this->template( Arrows::class )->add_container_render_attributes( 'wrapper' );
		$this->add_container_class_render_attribute( 'wrapper' );
		$this->add_container_data_render_attributes( 'wrapper' );

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<div ' . $this->get_render_attribute_string( 'wrapper' ) . '>';

		while ( $query->have_posts() ) {
			$query->the_post();

			$post_class_array = [
				'post',
				'visible',
				'wrapper',
				'dt-owl-item-wrap',
			];

			if ( ! has_post_thumbnail() ) {
				$post_class_array[] = 'no-img';
			}

			$this->remove_render_attribute( 'inner-wrapper' );
			$this->add_render_attribute( 'inner-wrapper', 'class', get_post_class( $post_class_array ) );

			$link_attributes = $this->get_link_attributes( $settings );
			$wrapper_tag     = 'article';
			if ( 'button' !== $settings['link_click'] && $link_attributes['href'] ) {
				$this->add_render_attribute( 'inner-wrapper', 'class', 'box-hover' );
				$this->add_render_attribute( 'inner-wrapper', $link_attributes );
				$wrapper_tag = 'a';
			}

			$post_media = $this->get_post_image( $settings );

			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo '<' . $wrapper_tag . ' ' . $this->get_render_attribute_string( 'inner-wrapper' ) . '>';
			?>

				<div class="post-content-wrapper">
					<?php if ( $settings['show_post_image'] && $post_media ) { ?>
						<div class="the7-simple-post-thumb">
							<?php echo $post_media; ?>
						</div>
					<?php } ?>

					<div class="post-entry-content">
						<?php
						if ( $settings['show_post_title'] ) {
							echo $this->get_post_title( $settings, $settings['post_title_tag'] );
						}

						echo $this->get_post_meta_html_based_on_settings( $settings );

						if ( $settings['post_content'] === 'show_excerpt' ) {
							echo $this->get_post_excerpt();
						}

						if ( $settings['show_read_more_button'] ) {
							echo '<div class="details-wrap">' . $this->get_details_btn( $settings ) . '</div>';
						}
						?>
					</div>
				</div>

			<?php
			echo '</' . $wrapper_tag . '>';
		}

		wp_reset_postdata();

		echo '</div>';

		$this->template( Arrows::class )->render();
	}

	/**
	 * @param array $settings Settings array.
	 *
	 * @return false|string
	 */
	protected function get_details_btn( $settings ) {
		// Cleanup button render attributes.
		$this->remove_render_attribute( 'box-button' );

		$link_attributes = $this->get_link_attributes( $settings );
		$btn_element     = 'button';
		if ( $link_attributes['href'] ) {
			$link_attributes['aria-label'] = the7_get_read_more_aria_label();
			$this->add_render_attribute( 'box-button', $link_attributes );

			if ( 'button' === $settings['link_click'] ) {
				$btn_element = 'a';
			}
		}

		ob_start();
		$this->template( Button::class )->render_button( 'box-button', esc_html( $settings['read_more_button_text'] ), $btn_element );

		return ob_get_clean();
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
	 * @param string $text Title.
	 * @param string $tag HTML tag.
	 *
	 * @return string
	 */
	protected function display_widget_title( $text, $tag = 'h3' ) {
		$tag = Utils::validate_html_tag( $tag );

		$output = '<' . $tag . ' class="rp-heading">' . esc_html( $text ) . '</' . $tag . '>';

		return $output;
	}

	/**
	 * @param array  $settings Settings array.
	 * @param string $tag HTML tag.
	 *
	 * @return false|string
	 */
	protected function get_post_title( $settings, $tag = 'h3' ) {
		$title = get_the_title();

		if ( ! $title ) {
			return '';
		}

		$link_attributes = [];

		$link_tag = 'span';
		if ( 'button' === $settings['link_click'] ) {
			$link_attributes = $this->get_link_attributes( $settings );

			if ( $link_attributes['href'] ) {
				$link_tag               = 'a';
				$link_attributes['rel'] = 'bookmark';
			}
		}

		$link_attributes['title'] = the_title_attribute( 'echo=0' );
		$link_attributes['class'] = 'post-title';

		if ( $settings['title_words_limit'] && $settings['title_width'] === 'normal' ) {
			$title = wp_trim_words( $title, $settings['title_words_limit'] );
		}

        $tag = Utils::validate_html_tag( $tag );

		$output  = '';
		$output .= '<' . $tag . ' class="heading">';
		$output .= '<' . $link_tag . ' ' . the7_get_html_attributes_string( $link_attributes ) . '>';
		$output .= esc_html( $title );
		$output .= '</' . $link_tag . '>';
		$output .= '</' . $tag . '>';

		return $output;
	}

	/**
	 * @return false|string
	 */
	protected function get_post_excerpt() {
		$settings = $this->get_settings_for_display();

		$excerpt = get_the_excerpt();
		if ( ! $excerpt ) {
			return false;
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
			'the7-simple-widget-posts-carousel',
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

		if ( ! $settings['show_post_image'] ) {
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
			$this->get_unique_class() . '.the7-elementor-widget',
			'~"%s"'
		);
	}
}
