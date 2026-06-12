<?php
/**
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widgets\Woocommerce;

use Elementor\Controls_Manager;
use Elementor\Core\Breakpoints\Manager as Breakpoints;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Plugin;
use Elementor\Utils;
use The7\Inc\Mods\Compatibility\WooCommerce\Front\Recently_Viewed_Products;
use The7\Mods\Compatibility\Elementor\Pro\Modules\Query_Control\The7_Group_Control_Query;
use The7\Mods\Compatibility\Elementor\Shortcode_Adapters\Query_Adapters\Products_Query;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Arrows;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Bullets;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Button;
use The7\Mods\Compatibility\Elementor\Widget_Templates\General;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Image_Size;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Woocommerce\Products_Query as Query;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Woocommerce\Sale_Flash;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Woocommerce\Variations;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Woocommerce\Price;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Less_Vars_Decorator_Interface;
use WC_Product;
use WP_Query;

defined( 'ABSPATH' ) || exit;

/**
 * Products_Carousel class.
 */
class Products_Carousel extends Products {

	/**
	 * Get element name.
	 * Retrieve the element name.
	 *
	 * @return string The name.
	 */
	public function get_name() {
		return 'the7-wc-products-carousel';
	}

	/**
	 * Get widget title.
	 *
	 * @return string
	 */
	protected function the7_title() {
		return esc_html__( 'Products Carousel', 'the7mk2' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string
	 */
	protected function the7_icon() {
		return 'eicon-posts-carousel';
	}

	/**
	 * @return string
	 */
	protected function get_less_file_name() {
		return PRESSCORE_THEME_DIR . '/css/dynamic-less/elementor/the7-woocommerce-products-carousel.less';
	}

	/**
	 * Get script dependencies.
	 *
	 * Retrieve the list of script dependencies the element requires.
	 *
	 * @return array Element scripts dependencies.
	 */
	public function get_script_depends() {
		$scripts = [ 'the7-elementor-masonry' ];

		if ( $this->is_preview_mode() ) {
			$scripts[] = 'the7-elements-carousel-widget-preview';
		}

		return $scripts;
	}

	/**
	 * @return array
	 */
	public function get_style_depends() {
		$styles   = parent::get_style_depends();
		$styles[] = 'the7-carousel-navigation';

		return $styles;
	}

	/**
	 * @param strig $element Element name.
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
	 * Render element.
	 *
	 * Generates the final HTML on the frontend.
	 */
	protected function render() {
		$this->print_inline_css();

		$settings = $this->get_settings_for_display();

		if ( $settings['query_post_type'] === 'recently_viewed' && ! $this->is_preview_mode() ) {
			Recently_Viewed_Products::track_via_js();
		}

		// Loop query.
		$query_builder = new Products_Query( $settings, 'query_' );
		$query         = $query_builder->create();

		if ( ! $query->have_posts() ) {
			if ( $settings['query_post_type'] === 'current_query' ) {
				$this->render_nothing_found_message();
			}
			$this->remove_hooks();
			return;
		}
		$this->setup_wrapper_class();

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<div ' . $this->get_render_attribute_string( 'wrapper' ) . '>';

		if ( $settings['show_widget_title'] === 'y' && $settings['widget_title_text'] ) {
			echo $this->display_widget_title( $settings['widget_title_text'], $settings['title_tag'] );
		}

		$this->template( Arrows::class )->add_container_render_attributes( 'inner-wrapper' );
		$this->add_container_class_render_attribute( 'inner-wrapper' );
		$this->add_container_data_render_attributes( 'inner-wrapper' );

		echo '<div ' . $this->get_render_attribute_string( 'inner-wrapper' ) . '>';

		// Related to print_render_attribute_string( 'woo_buttons_on_img' ); .
		$this->setup_woo_buttons_on_image_attributes();

		// Start loop.
		global $product;

		while ( $query->have_posts() ) {
			$query->the_post();

			$product = wc_get_product( get_the_ID() );

			if ( ! $product ) {
				continue;
			}

			$this->set_article_render_attributes( 'article', $product );

			/**
			 * Elements with render attributes:
			 *  - article
			 *  - woo_buttons_on_img
			 */
			$this->render_product_article( $product );
		}

		wc_reset_loop();
		wp_reset_postdata();

		echo '</div>';

		$this->template( Arrows::class )->render();

		echo '</div>';

		$this->render_added_to_cart_icon_template();

		$this->remove_hooks();
	}

	/**
	 * Setup article wrapper attribute.
	 */
	protected function setup_article_wrapper_attributes() {
		global $post;

		$settings = $this->get_settings_for_display();

		if ( $settings['image_hover_trigger'] === 'box' ) {
			$class[] = 'trigger-img-hover';
		}
	}

	/**
	 * Register widget controls.
	 */
	protected function register_controls() {
		// Content.
		$this->template( Query::class )->add_carousel_query_controls();
		$this->add_layout_content_controls();
		$this->add_content_controls();
		$this->add_variations_controls();
		$this->add_scrolling_controls();
		$this->template( Arrows::class )->add_content_controls();
		$this->template( Bullets::class )->add_content_controls();

		// Style.
		$this->add_widget_title_style_controls();
		$this->template( General::class )->add_box_style_controls();
		$this->add_image_style_controls();
		$this->add_content_style_controls();
		$this->template( Sale_Flash::class )->add_style_controls();
		$this->add_title_style_controls();
		$this->template( Price::class )->add_style_controls();
		$this->add_rating_style_controls();
		$this->add_short_description_style_controls();
		$this->add_variations_style_controls();
		$this->template( Variations::class )->add_variation_swatch_styles_controls();
		$this->add_button_style_controls();
		$this->template( Arrows::class )->add_style_controls();
		$this->template( Bullets::class )->add_style_controls();
	}

	/**
	 * Add scrolling controls.
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
	 * Register layout controls.
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
				'default'            => 3,
				'tablet_default'     => 2,
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
					'{{WRAPPER}} .owl-stage-outer' => ' padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
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
	}

	/**
	 * Setup wrapper class attribute.
	 */
	protected function setup_wrapper_class() {
		$class = [
			'products-shortcode',
			'the7-elementor-widget',
			'loading-effect-none',
		];

		$settings = $this->get_settings_for_display();

		// Unique class.
		$class[] = $this->get_unique_class();


		$class[] = the7_array_match(
			$settings['button_position'],
			[
				'below_image'    => 'cart-btn-below-img',
				'on_image'       => 'cart-btn-on-img',
				'on_image_hover' => 'cart-btn-on-hover',
			]
		);

		$class[] = the7_array_match(
			$settings['image_hover_style'],
			[
				'quick_scale' => 'quick-scale-img',
				'slow_scale'  => 'scale-img',
				'hover_image' => 'wc-img-hover',
			]
		);

		$this->add_render_attribute( 'wrapper', 'class', $class );
	}

	/**
	 * @param string $element Render element.
	 */
	protected function add_container_class_render_attribute( $element ) {

		$class = [
			'owl-carousel',
			'the7-elementor-widget',
			'the7-products-carousel',
			'elementor-owl-carousel-call',
			'loading-effect-none',
			'classic-layout-list',
		];

		// Unique class.
		$class[] = $this->get_unique_class();

		$settings = $this->get_settings_for_display();

		$class[] = the7_array_match(
			$settings['image_hover_style'],
			[
				'quick_scale' => 'quick-scale-img',
				'slow_scale'  => 'scale-img',
				'hover_image' => 'wc-img-hover',
			]
		);

		if ( $settings['product_title_width'] === 'crp-to-line' ) {
			$class[] = 'title-to-line';
		}

		if ( $settings['description_width'] === 'crp-to-line' ) {
			$class[] = 'desc-to-line';
		}

		$this->add_render_attribute( $element, 'class', $class );
	}

	/**
	 * @param  The7_Elementor_Less_Vars_Decorator_Interface $less_vars Less vars manager.
	 */
	protected function less_vars( The7_Elementor_Less_Vars_Decorator_Interface $less_vars ) {
		$settings = $this->get_settings_for_display();

		$less_vars->add_keyword(
			'unique-shortcode-class-name',
			$this->get_unique_class() . '.the7-products-carousel',
			'~"%s"'
		);
	}
}
