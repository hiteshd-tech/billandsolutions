<?php
/**
 * The7 Brand Logos widget for Elementor.
 *
 * @package The7
 */

 namespace The7\Mods\Compatibility\Elementor\Widgets\Woocommerce;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Typography;
use Elementor\Utils;
use stdClass;
use The7\Mods\Compatibility\Elementor\Style\Posts_Masonry_Style;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Less_Vars_Decorator_Interface;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Image_Aspect_Ratio;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Image_Size;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Pagination;
use The7\Mods\Compatibility\Elementor\With_Post_Excerpt;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;

defined( 'ABSPATH' ) || exit;

class Brand_Logos extends The7_Elementor_Widget_Base {

	use With_Post_Excerpt;
	use Posts_Masonry_Style;

	/**
	 * Get element name.
	 * Retrieve the element name.
	 *
	 * @return string The name.
	 */
	public function get_name() {
		return 'the7-woocommerce-brand-logos';
	}

	protected function the7_title() {
		return esc_html__( 'Brand Logos', 'the7mk2' );
	}

	protected function the7_icon() {
		return 'eicon-posts-grid';
	}

	/**
	 * Register widget assets.
	 *
	 * @see The7_Elementor_Widget_Base::__construct()
	 */
	protected function register_assets() {
		the7_register_style(
			$this->get_name(),
			PRESSCORE_THEME_URI . '/css/compatibility/elementor/the7-brand-logos.css',
			[ 'the7-filter-decorations-base', 'the7-simple-grid' ]
		);

		the7_register_script_in_footer(
			$this->get_name(),
			PRESSCORE_THEME_URI . '/js/compatibility/elementor/the7-brand-logos.js',
			[ 'dt-main' ]
		);

	}

	public function get_style_depends() {
		return [ $this->get_name() ];
	}

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

		$this->template( Pagination::class )->add_content_controls( 'source' );

		// Style.
		$this->add_widget_title_style_controls();

		$this->add_image_style_controls(
			[
				'show_post_image' => 'y',
			]
		);

		$this->add_title_style_controls();
		$this->add_content_area_style_controls();
		$this->add_meta_style_controls();
		$this->add_description_style_controls();

		$this->template( Pagination::class )->add_style_controls( 'source' );
	}

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
					'current_product'      => __( 'Current Product Brands', 'the7mk2' )
				],
				'label_block' => true,
			]
		);

		$brands = get_terms( [
			'taxonomy'   => 'product_brand',
			'hide_empty' => false,
		] );

		$options = [];

		if ( ! is_wp_error( $brands ) && ! empty( $brands ) ) {
			foreach ( $brands as $brand ) {
				$options[ $brand->term_id ] = $brand->name;
			}
		}


		$this->add_control(
			'brands',
			[
				'label'       => esc_html__( 'Brands', 'the7mk2' ),
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

		$this->end_controls_section();
	}

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

		$selector = '{{WRAPPER}} .sGrid-container';

		$this->add_responsive_control(
			'columns',
			[
				'label'              => esc_html__( 'Columns', 'the7mk2' ),
				'type'               => Controls_Manager::NUMBER,
				'default'            => 3,
				'tablet_default'     => 2,
				'mobile_default'     => 1,
				'min'                => 1,
				'max'                => 12,
				'selectors'          => [
					$selector => '--grid-columns: {{SIZE}};',
				],
				'frontend_available' => true,
			]
		);

		$this->template( Image_Size::class )->add_style_controls();

		$this->add_responsive_control(
			'gap_between_posts',
			[
				'label'      => esc_html__( 'Columns Gap', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [
					'size' => '40',
				],
				'range'      => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .sGrid-container' => 'grid-column-gap: {{SIZE}}{{UNIT}};',
				],
				'separator'  => 'before',
			]
		);

		$this->add_responsive_control(
			'rows_gap',
			[
				'label'      => esc_html__( 'Rows Gap', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [
					'size' => '20',
				],
				'range'      => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .sGrid-container' => 'grid-row-gap: {{SIZE}}{{UNIT}}; --grid-row-gap: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();
	}

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
				'label'     => esc_html__( 'Apply Link & Hover', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'button',
				'options'   => [
					'box'  => esc_html__( 'Whole box', 'the7mk2' ),
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
				'default'      => 'y',
				'separator'    => 'before',
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
				// translators: %s: number of products in a category.
				'placeholder' => esc_html( _n( '%s Product', '%s Products', 2, 'the7mk2' ) ),
				'condition'   => [
					'products_custom_format' => 'yes',
					'products_count'         => 'y',
				],
			]
		);

		$this->end_controls_section();
	}

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
				]
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
				'label'     => esc_html__( 'Color', 'the7mk2' ),
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

	protected function add_content_area_style_controls() {
		// Title Style.
		$this->start_controls_section(
			'content_area_style',
			[
				'label'     => esc_html__( 'Content Area', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'content_alignment',
			[
				'label'        => esc_html__( 'Alignment', 'the7mk2' ),
				'type'         => Controls_Manager::CHOOSE,
				'label_block'  => false,
				'options'      => [
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
				'prefix_class' => 'slide-h-position%s-',
				'default'      => 'left',
				'selectors_dictionary' => [
					'left'   => 'align-items: flex-start; text-align: left;',
					'center' => 'align-items: center; text-align: center;',
					'right'  => 'align-items: flex-end; text-align: right;',
				],
				'selectors'    => [
					'{{WRAPPER}} .the7-brand-content' => '{{VALUE}}',
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
					'{{WRAPPER}} .the7-brand-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();
	}

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
					'{{WRAPPER}} a.wf-cell:hover .product-name' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

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
                'selector'       => '{{WRAPPER}} .the7-brand-logos .entry-meta',
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
				'label'     => esc_html__( 'Color', 'the7mk2' ),
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
				'label'     => esc_html__( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .entry-meta:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} a.wf-cell:hover .entry-meta' => 'color: {{VALUE}}',
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

	protected function add_description_style_controls() {

		$this->start_controls_section(
			'short_description',
			[
				'label'     => esc_html__( 'Description', 'the7mk2' ),
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
				'label'     => esc_html__( 'Color', 'the7mk2' ),
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
				'label'     => esc_html__( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .short-description:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} a.wf-cell:hover .short-description' => 'color: {{VALUE}}',
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
	 * Add image style controls.
	 */
	protected function add_image_style_controls() {
		$this->start_controls_section(
			'section_design_image',
			[
				'label' => esc_html__( 'Image', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->template( Image_Aspect_Ratio::class )->add_style_controls();

		$this->add_control(
			'image_style_title',
			[
				'label'     => esc_html__( 'Style', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'image_padding',
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
					'{{WRAPPER}} .the7-image-wrapper img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'image_border',
				'selector' => '{{WRAPPER}} .the7-image-wrapper img',
				'exclude'  => [ 'color' ],
			]
		);

		$this->add_control(
			'image_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .the7-image-wrapper, {{WRAPPER}} .the7-image-wrapper img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'image_effects_tabs' );

		$this->start_controls_tab(
			'normal',
			[
				'label' => esc_html__( 'Normal', 'the7mk2' ),
			]
		);


		$this->add_control(
			'image_border_color',
			[
				'label'     => esc_html__( 'Border', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} img' => 'border-color: {{VALUE}}',
				],
				'condition' => [
					'image_border_border!' => [ '', 'none' ],
				],
			]
		);

		$this->add_control(
			'image_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .the7-image-wrapper img' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'image_shadow',
				'selector' => '{{WRAPPER}} img',
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'     => 'image_filters',
				'selector' => '{{WRAPPER}} img',
			]
		);

		$this->add_control(
			'image_opacity',
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
					'{{WRAPPER}} img:not(.show-on-hover)' => 'opacity: calc({{SIZE}}/100)',
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


		$this->add_control(
			'image_hover_border_color',
			[
				'label'     => esc_html__( 'Border', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .post-thumbnail-rollover:hover img' => 'border-color: {{VALUE}}',
				],
				'condition' => [
					'image_border_border!' => [ '', 'none' ],
				],
			]
		);

		$this->add_control(
			'image_hover_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .post-thumbnail-rollover:hover img' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'image_hover_shadow',
				'selector' => '{{WRAPPER}} .post-thumbnail-rollover:hover img',
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'     => 'image_hover_filters',
				'selector' => '{{WRAPPER}} .post-thumbnail-rollover:hover img',
			]
		);

		$this->add_control(
			'image_hover_opacity',
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
					'{{WRAPPER}}:hover img:not(.show-on-hover)' => 'opacity: calc({{SIZE}}/100)',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$product_brands = $this->product_brands();
		$terms = $product_brands->terms;

		$this->add_container_class_render_attribute( 'wrapper' );

		$this->template( Pagination::class )->add_containter_attributes( 'wrapper' );

		echo '<div ' . $this->get_render_attribute_string( 'wrapper' ) . '>';

		if ( $settings['show_widget_title'] === 'y' && $settings['widget_title_text'] ) {
			echo $this->display_widget_title( $settings['widget_title_text'], $settings['widget_title_tag'] );
		}
		$link_class = '';
		if ( 'button' !== $settings['link_click'] ) {
			$link_class = 'box-hover';
		}

		echo '<div class="sGrid-container dt-css-grid custom-pagination-handler" >';

		if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {

			foreach ( $terms as $term ) {
				$image_id = get_term_meta( $term->term_id, 'thumbnail_id', true );

				if ( ! $image_id ) {
					continue; // Skip terms without thumbnails.
				}

				$name = $term->name;

				$this->remove_render_attribute( 'product_wrapper' );
				$this->add_render_attribute(
					'product_wrapper',
					'class',
					[
						'wf-cell',
						'visible',
						$link_class
					],
					true
				);

				$wrapper_tag = 'div';
				if ( 'button' !== $settings['link_click'] ) {
					$this->add_link_attributes( 'product_wrapper', $this->get_custom_link_attributes(  $term ) );
					$wrapper_tag = 'a';
				}

				echo '<' . $wrapper_tag . ' ' . $this->get_render_attribute_string( 'product_wrapper' ) . '>';

				echo '<div class="the7-brand-container">';
				if ( $settings['show_post_image'] ) {
					echo '<div class="the7-image-wrapper">';
					echo $this->get_brand_image( $term );
					echo '</div>'; // .the7-image-wrapper
				}

				// Brand name and description
				echo '<div class="the7-brand-content">';
				if ( $settings['show_post_title'] ) {
					echo $this->get_brand_title( $settings, $settings['post_title_tag'], $term );
				}
				if ( $settings['products_count'] ) {
					echo $this->get_brand_count( $settings, $term );
				}
				if ( $settings['post_content'] === 'show_excerpt' ) {
					echo $this->get_brand_description( $term );
				}
				echo '</div>';

				echo '</div>'; // .the7-brand-container
				echo '</' . $wrapper_tag . '>'; // .wf-cell
			}

		}

		echo '</div>';

		$this->template( Pagination::class )->render( $product_brands->max_num_pages );

		echo '</div>';
	}

	protected function get_custom_link_attributes( $brand ) {
		$term_link = get_term_link( $brand, 'product_brand' );

		if ( is_wp_error( $term_link ) ) {
			$term_link = '';
		}

		return [
			'url' => $term_link,
		];
	}

	protected function product_brands() {
		$settings = $this->get_settings_for_display();

		$posts_per_page = $this->template( Pagination::class )->get_posts_per_page();
		$number         = $posts_per_page;
		if ( $this->template( Pagination::class )->get_loading_mode() === 'standard' ) {
			$number = 9999;
		}

		$attributes = [
			'taxonomy'   => 'product_brand',
			'number'     => $number,
			'hide_empty' => 'yes' === $settings['hide_empty'],
			'orderby'    => $settings['orderby'],
			'order'      => $settings['order'],
			'offset'     => $settings['posts_offset'],
			'include'    => [],
			'parent'     => '',
		];

		if ( 'by_id' === $settings['source'] ) {
			$attributes['include'] = array_filter( (array) $settings['brands'] );
		} elseif ( 'by_parent' === $settings['source'] ) {
			$attributes['parent'] = $settings['parent'];
		} elseif ( 'current_subcategories' === $settings['source'] ) {
			$attributes['child_of'] = get_queried_object_id();
		} elseif ( 'current_product' === $settings['source'] && is_singular('product') ) {
			global $product;
			if ( ! $product || ! is_a( $product, 'WC_Product' ) ) {
				$product = wc_get_product( get_the_ID() );
			}
			if ( $product ) {
				$terms = wp_get_post_terms( $product->get_id(), 'product_brand' );
			} else {
				$terms = [];
			}

			// Return as terms_query object
			$terms_query                = new \stdClass();
			$terms_query->terms         = $terms;
			$terms_query->max_num_pages = 1;
			return $terms_query;
		}

		// Default get_terms for other cases
		$terms = get_terms( 'product_brand', $attributes );

		$max_num_pages = 1;
		if ( $settings['loading_mode'] === 'standard' ) {
			$terms_chunk   = array_chunk( $terms, $posts_per_page );
			$max_num_pages = count( $terms_chunk );
			$current_page  = the7_get_paged_var();
			if ( isset( $terms_chunk[ $current_page - 1 ] ) ) {
				$terms = $terms_chunk[ $current_page - 1 ];
			}
		}

		$terms_query                = new \stdClass();
		$terms_query->terms         = $terms;
		$terms_query->max_num_pages = $max_num_pages;

		return $terms_query;
	}


	protected function display_widget_title( $text, $tag = 'h3' ) {
		$tag = Utils::validate_html_tag( $tag );

		$output = '<' . $tag . ' class="rp-heading">';
		$output .= esc_html( $text );
		$output .= '</' . $tag . '>';

		return $output;
	}

	/**
	 * @param  \WP_Term $brand   WP term object.
	 *
	 * @return string
	 */
	protected function get_brand_image( $category ) {
		$thumbnail_id = get_term_meta( $category->term_id, 'thumbnail_id', true );

		if ( ! $thumbnail_id ) {
			return '';
		}

		$settings          = $this->get_settings_for_display();
		$img_wrapper_class = implode( ' ', array_filter( [
			'post-thumbnail-rollover',
			$this->template( Image_Size::class )->get_wrapper_class(),
			$this->template( Image_Aspect_Ratio::class )->get_wrapper_class(),
		] ) );
		$wrap_attributes   = [
			'class'      => $img_wrapper_class,
			'aria-label' => esc_html__( 'Category image', 'the7mk2' ),
		];
		$wrap_tag        = 'div';
		if ( $settings['link_click'] !== 'box' ) {
			$wrap_tag = 'a';
			$link     = get_term_link( $category, 'product_brand' );
			if ( $link ) {
				$wrap_attributes['href'] = $link;
			} else {
				$wrap_attributes['href']  = '#';
				$wrap_attributes['class'] .= 'not-clickable-item';
			}
		}

		$image      = $this->template( Image_Size::class )->get_image( $thumbnail_id );

		return '<' . $wrap_tag . ' ' . the7_get_html_attributes_string( $wrap_attributes ) . '>' . $image  . '</' . $wrap_tag . '>';
	}

	protected function get_brand_title( $settings, $tag, $category ) {
        $tag = Utils::validate_html_tag( $tag );
		$title_link = [
			'href'  => get_term_link( $category, 'product_brand' ),
			'class' => 'product-name',
		];
		$title      = $category->name;
		if ( $settings['title_words_limit'] && $settings['title_width'] === 'normal' ) {
			$title = wp_trim_words( $title, $settings['title_words_limit'] );
		}

		if ( 'button' === $settings['link_click'] ) {
			$title_link_wrapper     	= '<a ' . the7_get_html_attributes_string( $title_link ) . '>';
			$title_link_wrapper_close 	= '</a>';
		} else {
			$title_link['href'] 		= '';
			$title_link_wrapper    		= '<span ' . the7_get_html_attributes_string( $title_link ) . '>';
			$title_link_wrapper_close 	= '</span>';
		}

		$output = '<' . $tag . ' class="heading">';
		$output .=  sprintf( '%s%s%s', $title_link_wrapper, $title, $title_link_wrapper_close );
		$output .= '</' . $tag . '>';

		return $output;
	}

	protected function get_brand_count( $settings, $brand ) {
		$default_strings = [
			'string_no_products' => esc_html__( 'No Products', 'the7mk2' ),
			'string_one_product' => esc_html__( 'One Product', 'the7mk2' ),
			'string_products'    => esc_html( _n( '%s Product', '%s Products', 2, 'the7mk2' ) ),
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

		// Use get_posts + count for accurate result
		$num_products = count( get_posts( [
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'tax_query'      => [
				[
					'taxonomy' => 'product_brand',
					'field'    => 'term_id',
					'terms'    => $brand->term_id,
				],
			],
			'posts_per_page' => -1,
			'fields'         => 'ids',
		] ) );

		if ( 0 === $num_products ) {
			$string = $default_strings['string_no_products'];
		} elseif ( 1 === $num_products ) {
			$string = $default_strings['string_one_product'];
		} else {
			$string = sprintf( $default_strings['string_products'], $num_products );
		}

		$output  = '<div class="entry-meta">';
		$output .= wp_kses_post( $string );
		$output .= '</div>';

		return $output;
	}

	protected function get_brand_description( $category ) {
		$settings = $this->get_settings_for_display();

		$excerpt = $category->description;
		if ( ! $excerpt ) {
			return;
		}

		if ( $settings['excerpt_words_limit'] && $settings['description_width'] === 'normal' ) {
			$excerpt = wp_trim_words( $excerpt, $settings['excerpt_words_limit'] );
		}

		$output = '<p class="short-description">';
		$output .= wp_kses_post( $excerpt );
		$output .= '</p>';

		return $output;
	}

	protected function add_container_class_render_attribute( $element ) {

		$class = [
			'the7-brand-logos',
			'the7-elementor-widget',
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
			$class[] = 'hide-post-image';
		}

		$this->add_render_attribute( $element, 'class', $class );
	}

}
