<?php
/**
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widget_Templates\Woocommerce;

use Elementor\Controls_Manager;
use The7\Mods\Compatibility\Elementor\Pro\Modules\Query_Control\The7_Group_Control_Query;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Abstract_Template;

defined( 'ABSPATH' ) || exit;

/**
 * Class Products_Query
 */
class Products_Query extends Abstract_Template {

	/**
	 * @return void
	 */
	public function add_query_controls() {
		$this->widget->start_controls_section(
			'section_query',
			[
				'label' => __( 'Query', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_query_group_control();

		$this->widget->end_controls_section();
	}

	/**
	 * @return void
	 */
	public function add_carousel_query_controls() {
		$this->widget->start_controls_section(
			'section_query',
			[
				'label' => __( 'Query', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_query_group_control();

		$this->widget->add_control(
			'dis_posts_total',
			[
				'label'       => __( 'Total Number Of Posts', 'the7mk2' ),
				'description' => __( 'Leave empty to display all posts.', 'the7mk2' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => '12',
				'condition'   => [
					'query_post_type!' => 'current_query',
				],
			]
		);

		$this->widget->add_control(
			'posts_offset',
			[
				'label'       => __( 'Posts Offset', 'the7mk2' ),
				'description' => __(
					'Offset for posts query (i.e. 2 means, posts will be displayed starting from the third post).',
					'the7mk2'
				),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 0,
				'min'         => 0,
				'condition'   => [
					'query_post_type!' => 'current_query',
				],
			]
		);

		$this->widget->end_controls_section();
	}

	/**
	 * @return void
	 */
	public function add_query_group_control() {
		$this->widget->add_control(
			'current_query_info',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __(
					'Note that the amount of posts per page is the product of "Products per row" and "Rows per page" settings from "Appearance"->"Customize"->"WooCommerce"->"Products Catalog".',
					'the7mk2'
				),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => [
					'query_post_type' => 'current_query',
				],
			]
		);

		$this->widget->add_group_control(
			The7_Group_Control_Query::get_type(),
			[
				'name'            => 'query',
				'query_post_type' => 'product',
				'presets'         => [ 'include', 'exclude', 'order' ],
				'fields_options'  => [
					'post_type' => [
						'default' => 'product',
						'options' => [
							'current_query'   => __( 'Current Query', 'the7mk2' ),
							'product'         => __( 'Latest Products', 'the7mk2' ),
							'sale'            => __( 'Sale', 'the7mk2' ),
							'top'             => __( 'Top rated products', 'the7mk2' ),
							'best_selling'    => __( 'Best selling', 'the7mk2' ),
							'featured'        => __( 'Featured', 'the7mk2' ),
							'up_sales'        => esc_html__( 'Up-sales', 'the7mk2' ),
							'cross_sales'     => esc_html__( 'Cross-sales', 'the7mk2' ),
							'by_id'           => _x( 'Manual Selection', 'Posts Query Control', 'the7mk2' ),
							'related'         => __( 'Related Products', 'the7mk2' ),
							'recently_viewed' => __( 'Recently Viewed', 'the7mk2' ),
						],
					],
					'orderby'   => [
						'default' => 'date',
						'options' => [
							'date'       => __( 'Date', 'the7mk2' ),
							'title'      => __( 'Title', 'the7mk2' ),
							'price'      => __( 'Price', 'the7mk2' ),
							'popularity' => __( 'Popularity', 'the7mk2' ),
							'rating'     => __( 'Rating', 'the7mk2' ),
							'rand'       => __( 'Random', 'the7mk2' ),
							'menu_order' => __( 'Menu Order', 'the7mk2' ),
						],
					],
					'exclude'   => [
						'options' => [
							'current_post'     => __( 'Current Post', 'the7mk2' ),
							'manual_selection' => __( 'Manual Selection', 'the7mk2' ),
							'terms'            => __( 'Term', 'the7mk2' ),
						],
					],
					'include'   => [
						'options' => [
							'terms' => __( 'Term', 'the7mk2' ),
						],
					],
				],
				'exclude'         => [
					'posts_per_page',
					'exclude_authors',
					'authors',
					'offset',
					'related_fallback',
					'related_ids',
					'query_id',
					'avoid_duplicates',
					'ignore_sticky_posts',
				],
			]
		);

		$this->widget->start_injection(
			[
				'type' => Controls_Manager::TABS,
				'at'   => 'after',
				'of'   => 'query_query_args',
			]
		);

		$this->widget->add_control(
			'query_related_products_by',
			[
				'label'        => esc_html__( 'Relate products by', 'the7mk2' ),
				'type'         => Controls_Manager::SELECT,
				'options'      => [
					''         => esc_html__( 'Taxonomy and Tag (default)', 'the7mk2' ),
					'taxonomy' => esc_html__( 'Only Taxonomy', 'the7mk2' ),
				],
				'condition'    => [
					'query_post_type' => 'related',
				],
				'tabs_wrapper' => 'query_query_args',
			]
		);

		$this->widget->end_injection();
	}
}
