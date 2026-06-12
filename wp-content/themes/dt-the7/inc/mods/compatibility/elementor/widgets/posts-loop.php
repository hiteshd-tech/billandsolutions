<?php
/**
 * The7 Posts Masonry & Grid widget for Elementor.
 *
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Core\Base\Document;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Plugin as Elementor;
use ElementorPro\Modules\LoopBuilder\Documents\Loop as LoopDocument;
use ElementorPro\Modules\LoopBuilder\Files\Css\Loop_Dynamic_CSS;
use ElementorPro\Modules\QueryControl\Controls\Template_Query;
use ElementorPro\Modules\QueryControl\Module as QueryControlModule;
use The7\Mods\Compatibility\Elementor\Modules\Loop\Alternate_Template_Trait;
use The7\Mods\Compatibility\Elementor\Shortcode_Adapters\Query_Adapters\Products_Query;
use The7\Mods\Compatibility\Elementor\Style\Posts_Masonry_Style;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Button as Button_Template;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Pagination_Loop as Pagination;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Woocommerce\Products_Query as Query;
use The7_Categorization_Request;
use The7_Elementor_Compatibility;
use The7_Query_Builder;
use The7_Related_Query_Builder;


defined( 'ABSPATH' ) || exit;

/**
 * Posts class.
 */
class Posts_Loop extends The7_Elementor_Widget_Base {


	const WIDGET_NAME = 'the7-post-loop';

	use Posts_Masonry_Style;
	use Alternate_Template_Trait;

	protected $_has_template_content = false;

	/**
	 * @var \WP_Query
	 */
	protected $query = null;
    private $terms;

    /**
	 * @return array
	 */
	public function get_script_depends() {
		return [ $this->get_name() ];
	}

	/**
	 * Get element name.
	 * Retrieve the element name.
	 *
	 * @return string The name.
	 */
	public function get_name() {
		return self::WIDGET_NAME;
	}

	/**
	 * @return string[]
	 */
	public function get_style_depends() {
		return [ $this->get_name() ];
	}

	/**
	 * @param array    $attributes The attributes array.
	 * @param Document $document The document object.
	 *
	 * @return string
	 */
	public function add_class_to_loop_item( $attributes, $document ) {
		if ( LoopDocument::DOCUMENT_TYPE === $document::get_type() ) {
			$attributes           = array_merge( $attributes, presscore_tpl_masonry_item_wrap_data_attr_arr() );
			$attributes['class'] .= ' ' . implode( ' ', $this->get_render_attributes( 'article_wrapper', 'class' ) );
		}

		return $attributes;
	}

	/**
	 * @return string|null
	 */
	protected function the7_title() {
		return esc_html__( 'Masonry & Grid Loop', 'the7mk2' );
	}

	/**
	 * @return string[]
	 */
	protected function the7_keywords() {
		return [ 'masonry', 'grid', 'post', 'image', 'loop', 'custom post type' ];
	}

	/**
	 * @return string
	 */
	protected function the7_icon() {
		return 'eicon-loop-builder';
	}

	protected function get_initial_config() {
		$config = parent::get_initial_config();

		$config['is_loop']              = true;
		$config['edit_handle_selector'] = '.elementor-widget-container';

		return $config;
	}

	/**
	 * Register widget assets.
	 *
	 * @see The7_Elementor_Widget_Base::__construct()
	 */
	protected function register_assets() {
		the7_register_style(
			$this->get_name(),
			THE7_ELEMENTOR_CSS_URI . '/the7-post-loop.css',
			[
				'the7-simple-grid',
				'the7-filter-decorations-base',
			]
		);
		the7_register_script_in_footer(
			$this->get_name(),
			THE7_ELEMENTOR_JS_URI . '/the7-post-loop.js',
			[
				'the7-simple-grid',
				'the7-elementor-frontend-common',
			]
		);
	}

	/**
	 * Register widget controls.
	 */
	protected function register_controls() {
		// Content Tab.
		$this->add_content_controls();
		$this->add_posts_query_content_controls();
		$this->add_products_query_content_controls();
		$this->add_layout_content_controls();
		$this->add_filter_bar_content_controls();
		$this->add_pagination_content_controls();

		$this->start_injection(
			[
				'type' => 'control',
				'at'   => 'before',
				'of'   => 'loading_mode',
			]
		);

		$this->add_control(
			'standard_pagination_mode_description',
			[
				'raw'             => esc_html__( 'Filter and pagination with page reloading.', 'the7mk2' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => [
					'loading_mode' => 'standard',
				],
				'conditions'      => [
					'relation' => 'and',
					'terms'    => [
						[
							'name'     => 'loading_mode',
							'operator' => 'in',
							'value'    => [ 'standard' ],
						],
						[
							'relation' => 'or',
							'terms'    => $this->template( Pagination::class )->get_post_type_term_conditions( 'post_type', [ 'current_query' ] ),
						],
					],
				],

			]
		);

		$this->end_injection();

		// Style Tab.
		$this->add_box_style_controls();
		$this->add_filter_bar_style_controls();
		$this->template( Pagination::class )->add_style_controls( 'post_type' );

		$this->update_control(
			'gap_before_pagination',
			[
				'default' => [
					'unit' => 'px',
					'size' => '30',
				],
			]
		);

		$condition = [
			'loading_mode' => 'js_more_button',
			'post_type!'   => 'current_query',
		];

		$this->template( Button_Template::class )->add_style_controls(
			Button_Template::ICON_MANAGER,
			$condition,
			[
				'load_more_button_size'      => [
					'default' => 'lg',
				],
				'load_more_gap_above_button' => [
					'label'     => esc_html__( 'Margin', 'the7mk2' ),
					'type'      => Controls_Manager::DIMENSIONS,
					'default'   => [
						'top'      => '30',
						'right'    => '',
						'bottom'   => '',
						'left'     => '',
						'unit'     => 'px',
						'isLinked' => false,
					],
					'selectors' => [
						'{{WRAPPER}} .the7-post-loop.paginator-more-button .button-load-more' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				],
			],
			'load_more_',
			'.the7-post-loop.paginator-more-button .button-load-more',
			esc_html__( 'Load more button', 'the7mk2' )
		);

		$this->start_injection(
			[
				'type' => 'control',
				'at'   => 'before',
				'of'   => 'load_more_button_size',
			]
		);

		$this->add_responsive_control(
			'load_more_button_align',
			[
				'label'                => esc_html__( 'Alignment', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => [
					'left'    => [
						'title' => esc_html__( 'Left', 'the7mk2' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center'  => [
						'title' => esc_html__( 'Center', 'the7mk2' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'   => [
						'title' => esc_html__( 'Right', 'the7mk2' ),
						'icon'  => 'eicon-text-align-right',
					],
					'justify' => [
						'title' => esc_html__( 'Justified', 'the7mk2' ),
						'icon'  => 'eicon-text-align-justify',
					],
				],
				'condition'            => $condition,
				'default'              => '',
				'selectors_dictionary' => [
					'left'    => 'justify-content: flex-start;',
					'center'  => 'justify-content: center;',
					'right'   => 'justify-content: flex-end;',
					'justify' => '--load-more-button-width: 100%;',
				],
				'selectors'            => [
					'{{WRAPPER}} .paginator-more-button' => '{{VALUE}}',
				],
			]
		);

		$this->end_injection();

		/**
		 * Inject archive posts per page control.
		 */
		$this->start_injection(
			[
				'type' => 'control',
				'at'   => 'before',
				'of'   => 'loading_mode',
			]
		);

		$this->add_control(
			'archive_loading_mode',
			[
				'label'      => esc_html__( 'Pagination mode', 'the7mk2' ),
				'type'       => Controls_Manager::SELECT,
				'default'    => 'standard',
				'options'    => [
					'standard'        => esc_html__( 'Standard', 'the7mk2' ),
					'ajax_pagination' => esc_html__( 'AJAX pages', 'the7mk2' ),
				],
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'relation' => 'and',
							'terms'    => [
								[
									'name'     => 'template_type',
									'operator' => '==',
									'value'    => 'posts',
								],
								[
									'name'     => 'post_type',
									'operator' => '==',
									'value'    => 'current_query',
								],
							],
						],
						[
							'relation' => 'and',
							'terms'    => [
								[
									'name'     => 'template_type',
									'operator' => '==',
									'value'    => 'products',
								],
								[
									'name'     => 'query_post_type',
									'operator' => '==',
									'value'    => 'current_query',
								],
							],
						],
					],
				],
			]
		);

		/**
		 * Add archive posts_per_page setting.
		 *
		 * @see Custom_Pagination_Query_Handler::handle_archive_and_search_posts_per_page()
		 */
		$this->add_control(
			'archive_posts_per_page',
			[
				'label'       => esc_html__( 'Number Of Posts On One Page', 'the7mk2' ),
				'description' => esc_html__( 'Leave empty to display default archive posts amount.', 'the7mk2' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => '',
				'conditions'  => [
					'relation' => 'or',
					'terms'    => [
						[
							'relation' => 'and',
							'terms'    => [
								[
									'name'     => 'template_type',
									'operator' => '==',
									'value'    => 'posts',
								],
								[
									'name'     => 'post_type',
									'operator' => '==',
									'value'    => 'current_query',
								],
							],
						],
						[
							'relation' => 'and',
							'terms'    => [
								[
									'name'     => 'template_type',
									'operator' => '==',
									'value'    => 'products',
								],
								[
									'name'     => 'query_post_type',
									'operator' => '==',
									'value'    => 'current_query',
								],
							],
						],
					],
				],
			]
		);

		$this->end_injection();
	}

	/**
	 * Add content controls.
	 */
	protected function add_content_controls() {
		// 'section_layout' name is important for createTemplate js function.
		$this->start_controls_section(
			'section_layout',
			[
				'label' => esc_html__( 'Loop Template', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);
		// We need to have class div[class*=elementor-widget-loop] to have editor a handlers.
		$this->add_control(
			'wrap_helper',
			[
				'type'         => Controls_Manager::HIDDEN,
				'default'      => 'elementor-widget-loop-the7-post-loop',
				'prefix_class' => '',
			]
		);

		// we should use _skin contol in order to use inline editing (in this case we use only '_skin' controll to emulate skin usage).
		// The skin name should be 'post'
		$this->add_control(
			'_skin',
			[
				'label'   => esc_html__( 'Skin', 'elementor' ),
				'type'    => Controls_Manager::HIDDEN,
				'default' => 'post',
			]
		);

		$this->add_control(
			'wc_is_active',
			[
				'type'    => Controls_Manager::HIDDEN,
				'default' => the7_is_woocommerce_enabled() ? 'y' : '',
			]
		);

		if ( the7_is_woocommerce_enabled() ) {
			$this->add_control(
				'template_type',
				[
					'label'   => esc_html__( 'Choose Template Type', 'the7mk2' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'posts',
					'options' => [
						'posts'    => esc_html__( 'Posts', 'the7mk2' ),
						'products' => esc_html__( 'Products', 'the7mk2' ),
					],
				]
			);
		} else {
			$this->add_control(
				'template_type',
				[
					'type'    => Controls_Manager::HIDDEN,
					'default' => 'posts',
				]
			);
		}

		$this->add_control(
			'template_id',
			[
				'label'              => esc_html__( 'Choose Loop Template', 'the7mk2' ),
				'type'               => Template_Query::CONTROL_ID,
				'label_block'        => true,
				'autocomplete'       => [
					'object' => QueryControlModule::QUERY_OBJECT_LIBRARY_TEMPLATE,
					'query'  => [
						'post_status' => Document::STATUS_PUBLISH,
						'meta_query'  => [
							[
								'key'     => Document::TYPE_META_KEY,
								'value'   => LoopDocument::get_type(),
								'compare' => 'IN',
							],
						],
					],
				],
				'actions'            => [
					'new'  => [
						'visible'         => true,
						'document_config' => [
							'type' => LoopDocument::get_type(),
						],
					],
					'edit' => [
						'visible' => true,
					],
				],
				'frontend_available' => true,
			]
		);

		$this->add_alternate_templates_controls();
		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function add_posts_query_content_controls() {
		/**
		 * Must have section_id = query_section to work properly.
		 *
		 * @see elements-widget-settings.js:onEditSettings()
		 */
		$this->start_controls_section(
			'query_section',
			[
				'label'      => esc_html__( 'Query', 'the7mk2' ),
				'tab'        => Controls_Manager::TAB_CONTENT,
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'template_type',
							'operator' => '=',
							'value'    => 'posts',
						],
						[
							'name'     => 'wc_is_active',
							'operator' => '!=',
							'value'    => 'y',
						],
					],
				],
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

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function add_products_query_content_controls() {
		$this->start_controls_section(
			'product_query_section',
			[
				'label'     => esc_html__( 'Query', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'template_type' => [ 'products' ],
					'wc_is_active'  => 'y',
				],
			]
		);

		$this->template( Query::class )->add_query_group_control();

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function add_layout_content_controls() {
		$this->start_controls_section(
			'layout_section',
			[
				'label' => esc_html__( 'Layout', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'layout',
			[
				'label'              => esc_html__( 'Masonry', 'the7mk2' ),
				'type'               => Controls_Manager::SWITCHER,
				'frontend_available' => true,
				'render_type'        => 'ui',
			]
		);

		$this->add_control(
			'loading_effect',
			[
				'label'   => esc_html__( 'Loading Effect', 'the7mk2' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'none'             => esc_html__( 'None', 'the7mk2' ),
					'fade_in'          => esc_html__( 'Fade in', 'the7mk2' ),
					'move_up'          => esc_html__( 'Move up', 'the7mk2' ),
					'scale_up'         => esc_html__( 'Scale up', 'the7mk2' ),
					'fall_perspective' => esc_html__( 'Fall perspective', 'the7mk2' ),
					'fly'              => esc_html__( 'Fly', 'the7mk2' ),
					'flip'             => esc_html__( 'Flip', 'the7mk2' ),
					'helix'            => esc_html__( 'Helix', 'the7mk2' ),
					'scale'            => esc_html__( 'Scale', 'the7mk2' ),
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

		$this->add_responsive_control(
			'columns_gap',
			[
				'label'              => esc_html__( 'Gap Between Columns', 'the7mk2' ),
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
				'selectors'          => [
					$selector => '--grid-column-gap: {{SIZE}}{{UNIT}};',
				],
				'frontend_available' => true,
				'separator'          => 'before',
			]
		);

		$this->add_responsive_control(
			'rows_gap',
			[
				'label'              => esc_html__( 'Rows Gap', 'the7mk2' ),
				'type'               => Controls_Manager::SLIDER,
				'size_units'         => [ 'px' ],
				'default'            => [
					'size' => '30',
				],
				'range'              => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors'          => [
					$selector => '--grid-row-gap: {{SIZE}}{{UNIT}}',
				],
				'frontend_available' => true,
			]
		);

		$this->add_responsive_control(
			'row_align',
			[
				'label'     => esc_html__( 'Align Items', 'the7mk2' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'flex-start' => [
						'title' => esc_html__( 'Start', 'the7mk2' ),
						'icon'  => 'eicon-flex eicon-align-start-v',
					],
					'center'     => [
						'title' => esc_html__( 'Center', 'the7mk2' ),
						'icon'  => 'eicon-flex eicon-align-center-v',
					],
					'flex-end'   => [
						'title' => esc_html__( 'End', 'the7mk2' ),
						'icon'  => 'eicon-flex eicon-align-end-v',
					],
				],
				'selectors' => [
					$selector => 'align-items:{{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function add_filter_bar_content_controls() {
		$this->start_controls_section(
			'categorization_section',
			[
				'label'     => esc_html__( 'Filter Bar', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'template_type' => [ 'posts' ],
					'post_type!'    => [ 'current_query', 'related' ],
				],
			]
		);

		$layouts = [
			'show' => esc_html__( 'Show', 'the7mk2' ),
			'hide' => esc_html__( 'Hide', 'the7mk2' ),
		];
		$this->add_responsive_control(
			'show_categories_filter',
			[
				'label'                => esc_html__( 'Taxonomy Filter', 'the7mk2' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => 'hide',
				'options'              => $layouts,
				'device_args'          => $this->generate_device_args(
					[
						'options' => [ '' => esc_html__( 'No change', 'the7mk2' ) ] + $layouts,
					]
				),
				'selectors'            => [
					'{{WRAPPER}} .filter' => '{{VALUE}}',
				],
				'selectors_dictionary' => [
					'show' => '--filter-display: flex;',
					'hide' => '--filter-display: none;',
				],
				'render_type'          => 'template',
                'frontend_available'   => true,
			]
		);

		$this->add_control(
			'filter_show_all',
			[
				'label'        => esc_html__( '"All" Filter', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'the7mk2' ),
				'label_off'    => esc_html__( 'No', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
				'conditions'   => $this->generate_conditions( 'show_categories_filter', '==', 'show' ),
			]
		);

		$this->add_control(
			'filter_all_text',
			[
				'label'       => esc_html__( '"All" Filter Label', 'the7mk2' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'View all', 'the7mk2' ),
				'placeholder' => '',
				'conditions'  => [
					'relation' => 'and',
					'terms'    => [
						[
							'name'     => 'filter_show_all',
							'operator' => '==',
							'value'    => 'y',
						],
						$this->generate_conditions( 'show_categories_filter', '==', 'show' ),
					],
				],
			]
		);

		$this->add_control(
			'allow_filter_navigation_by_url',
			[
				'label'        => esc_html__( 'Allow Navigation By Url', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'the7mk2' ),
				'label_off'    => esc_html__( 'No', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => '',
				'separator'    => 'before',
				'conditions'   => [
					'relation' => 'and',
					'terms'    => [
						[
							'name'     => 'loading_mode',
							'operator' => '!=',
							'value'    => 'standard',
						],
						$this->generate_conditions( 'show_categories_filter', '==', 'show' ),
					],
				],
				'render_type'  => 'template',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function add_pagination_content_controls() {
		$this->template( Pagination::class )->add_content_controls( 'post_type' );
	}

	/**
	 * @return void
	 */
	protected function add_box_style_controls() {
		$this->start_controls_section(
			'box_section',
			[
				'label' => esc_html__( 'Box', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$selector       = '{{WRAPPER}} .sGrid-container > .wf-cell';
		$selector_hover = $selector . ':hover';

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
					$selector => 'border-style: solid; border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
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
					$selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
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
					$selector => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->start_controls_tabs( 'box_style_tabs' );

		$this->start_controls_tab(
			'box_style',
			[
				'label' => esc_html__( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'box_shadow',
				'selector' => $selector,
			]
		);

		$this->add_control(
			'box_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$selector => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'box_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$selector => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'box_style_hover',
			[
				'label' => esc_html__( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'box_shadow_hover',
				'selector' => $selector_hover,
			]
		);

		$this->add_control(
			'box_background_color_hover',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$selector_hover => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'box_border_color_hover',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$selector_hover => 'border-color: {{VALUE}}',
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
	protected function add_filter_bar_style_controls() {
		$this->start_controls_section(
			'filter_bar_style_section',
			[
				'label'      => esc_html__( 'Filter Bar', 'the7mk2' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation' => 'and',
					'terms'    => [
						[
							'name'     => 'template_type',
							'operator' => '==',
							'value'    => 'posts',
						],
						$this->generate_conditions( 'show_categories_filter', '==', 'show' ),
					],
				],
			]
		);

		$selector      = '{{WRAPPER}} .filter';
		$cat_selector  = '{{WRAPPER}} .filter .filter-categories';
		$item_selector = '{{WRAPPER}} .filter .filter-item';

		$this->add_responsive_control(
			'filter_position',
			[
				'label'                => esc_html__( 'Align', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'toggle'               => false,
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
				'selectors_dictionary' => [
					'left'   => 'flex-start',
					'center' => 'center',
					'right'  => 'flex-end',
				],
				'selectors'            => [
					'{{WRAPPER}} .filter'                => 'justify-content: {{VALUE}};',
					'{{WRAPPER}} .filter .filter-categories' => 'justify-content: {{VALUE}};',
					'{{WRAPPER}} .filter .filter-extras' => 'justify-content: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'filter_min_width',
			[
				'label'      => esc_html__( 'Min Width', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors'  => [
					$item_selector => 'min-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'filter_min_height',
			[
				'label'      => esc_html__( 'Min Height', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors'  => [
					$item_selector => 'min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'filter_typography',
				'label'    => esc_html__( 'Typography', 'the7mk2' ),
				'selector' => $item_selector,
			]
		);

		$this->add_responsive_control(
			'filter_border_width',
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
					$item_selector => 'border-style: solid; border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'filter_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					$item_selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'filter_element_padding',
			[
				'label'      => esc_html__( 'Padding', 'the7mk2' ),
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
					$item_selector => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->start_controls_tabs( 'filter_style' );

		$this->add_filter_category_states_controls( 'normal_', esc_html__( 'Normal', 'the7mk2' ) );
		$this->add_filter_category_states_controls( 'hover_', esc_html__( 'Hover', 'the7mk2' ) );
		$this->add_filter_category_states_controls( 'active_', esc_html__( 'Active', 'the7mk2' ) );

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'filter_column_gap',
			[
				'label'      => esc_html__( 'Columns Gap', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [
					'size' => '30',
				],
				'range'      => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors'  => [
					$cat_selector => '--filter-column-gap: {{SIZE}}{{UNIT}};',
				],
				'separator'  => 'before',
			]
		);

		$this->add_responsive_control(
			'filter_rows_gap',
			[
				'label'      => esc_html__( 'Rows Gap', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [
					'size' => '30',
				],
				'range'      => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors'  => [
					$cat_selector => '--filter-row-gap: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'filter_margin',
			[
				'label'     => esc_html__( 'Margin', 'the7mk2' ),
				'type'      => Controls_Manager::DIMENSIONS,
				'default'   => [
					'top'      => '0',
					'right'    => '0',
					'bottom'   => '30',
					'left'     => '0',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors' => [
					$selector => '--filter-top-gap: {{TOP}}{{UNIT}}; --filter-right-gap: {{RIGHT}}{{UNIT}};  --filter-bottom-gap: {{BOTTOM}}{{UNIT}}; --filter-left-gap: {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'post_type!' => 'current_query',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @param string $prefix_name Prefix.
	 * @param string $box_name    Box.
	 *
	 * @return void
	 */
	protected function add_filter_category_states_controls( $prefix_name, $box_name ) {
		$var_prefix = '';
		$sel_prefix = '';
		if ( strpos( $prefix_name, 'active_' ) === 0 ) {
			$var_prefix = '-active';
			$sel_prefix = '.act';
		}
		if ( strpos( $prefix_name, 'hover_' ) === 0 ) {
			$var_prefix = '-hover';
			$sel_prefix = ':hover';
		}

		$selector = '{{WRAPPER}} .the7-elementor-widget > .filter .filter-item';

		$this->start_controls_tab(
			$prefix_name . 'filter_category_tab_style',
			[
				'label' => $box_name,
			]
		);

		$this->add_control(
			$prefix_name . 'filter_category_color',
			[
				'label'     => esc_html__( 'Text Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					$selector => "--filter-color{$var_prefix}: {{VALUE}};",
				],
			]
		);

		$this->add_control(
			$prefix_name . 'filter_category_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$selector => "--filter-border-color{$var_prefix}: {{VALUE}};",
				],
			]
		);

		$this->add_control(
			$prefix_name . 'filter_category_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$selector => "--filter-background-color{$var_prefix}: {{VALUE}};",
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => $prefix_name . 'filter_category_box_shadow',
				'selector' => $selector . $sel_prefix,
			]
		);

		$this->end_controls_tab();
	}


	/**
	 * Render widget.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		// Force disable pagination for related posts.
		if ( $settings['post_type'] === 'related' ) {
			$this->template( Pagination::class )->set_loading_mode( 'disabled' );
		}

        $this->terms = [];
        if ( $this->filter_is_visible() ) {
			$this->terms = $this->get_posts_filter_terms( $settings['taxonomy'], $settings['terms'] );
		}

		$this->alternate_template_before_render();
		$query = $this->query_posts();
		if ( ! $query->have_posts() ) {
			if ( $settings['post_type'] === 'current_query' ) {
				$this->render_nothing_found_message();
			}
			$this->alternate_template_after_render();
			return;
		}

		$this->add_container_attributes( 'wrapper' );
		$this->template( Pagination::class )->add_containter_attributes( 'wrapper' );
		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<?php
			if ( empty( $settings['template_id'] ) ) {
				$this->render_empty_view();
				$this->alternate_template_after_render();
				return;
			}

			// Posts filter.
			$this->display_filter($this->terms);
			$this->add_render_attribute( 'grid-wrapper', 'class', [ 'sGrid-container', 'elementor-loop-container' ] );
			?>
			<div <?php $this->print_render_attribute_string( 'grid-wrapper' ); ?>>
				<?php
				$data_post_limit     = $this->template( Pagination::class )->get_post_limit();
				$is_product_template = $this->is_product_template();

				while ( $query->have_posts() ) {
					$query->the_post();

					if ( $is_product_template ) {
						// Start loop.
						global $product;
						$product = wc_get_product( get_the_ID() );
					}

					// Post is visible on the first page.
					$visibility = 'visible';
					if ( $data_post_limit >= 0 && $query->current_post >= $data_post_limit ) {
						$visibility = 'hidden';
					}
					$this->remove_render_attribute( 'article_wrapper' );
					$this->add_render_attribute(
						'article_wrapper',
						'class',
						$this->masonry_item_wrap_class(
							[
								$visibility,
								'wf-cell',
							]
						)
					);
					$this->render_post();
				}
				wp_reset_postdata();
				?>
			</div>
			<?php
			$this->add_render_attribute(
				'paginator-wrapper',
				'class',
				[
					self::WIDGET_NAME,
				]
			);
			$this->template( Pagination::class )->render( $query->max_num_pages );
			?>
		</div>
		<?php
		$this->alternate_template_after_render();
	}

	/**
	 * @return bool
	 */
	protected function filter_is_visible() {
		if ( $this->is_product_template() || $this->get_settings_for_display( 'post_type' ) === 'current_query' ) {
			return false;
		}

		if ( Elementor::$instance->editor->is_edit_mode() ) {
			return true;
		}

		return $this->any_responsive_setting_equals( 'show_categories_filter', 'show' );
	}

	/**
	 * @return bool
	 */
	protected function is_product_template() {
		return the7_is_woocommerce_enabled() && $this->get_settings_for_display( 'template_type' ) === 'products';
	}

	/**
	 * @param string $taxonomy Taxonomy.
	 * @param array  $terms    Terms array.
	 *
	 * @return int[]|string|string[]|\WP_Error|\WP_Term[]
	 */
	protected function get_posts_filter_terms( $taxonomy, $terms = [] ) {
		$get_terms_args = [
			'taxonomy'   => $taxonomy,
			'hide_empty' => true,
			'orderby'    => 'slug',
		];

		if ( $terms ) {
			$get_terms_args['include'] = $terms;
		}

		return get_terms( $get_terms_args );
	}

	/**
	 * @return bool
	 */
	protected function use_filter_request() {
		$settings = $this->get_settings_for_display();

		return $settings['loading_mode'] === 'standard' || $settings['loading_mode'] === 'ajax_pagination' || ! empty( $settings['allow_filter_navigation_by_url'] || $this->has_alternate_templates() );
	}

	/**
	 * @return mixed
	 */
	protected function filter_show_all() {
		return $this->get_settings_for_display( 'filter_show_all' );
	}

	/**
	 * @return false|\WP_Query
	 */
	public function query_posts() {
		$query = $this->query_posts_for_alternate_templates();

		if ( ! $query ) {
			// Get normal query.
			$query = $this->query();
		}

		$this->query = $query;

		return $query;
	}

	/**
	 * @return The7_Categorization_Request|null
	 */
	protected function get_categorization_request() {
		$settings = $this->get_settings_for_display();
		$request  = new The7_Categorization_Request();

		// Do local filtering if there are no global (no taxonomy in request).
		if ( ! $request->taxonomy ) {
			// Turn off query filtering if current_query.
			// Turn off query filtering if not use_filter_request().
			if ( $settings['post_type'] === 'current_query' || ! $this->use_filter_request() ) {
				$request = null;
			} elseif ( isset( $this->terms[0] ) && ! $this->filter_show_all() && ! $request->get_first_term() ) {
				// Force filter by terms[0] if there are no requested term or show all button.
				$request->filter_by_term( $this->terms[0]->term_id );
			}
		}

		return $request;
	}

	/**
	 * @param array $query_args query params.
	 *
	 * @return \WP_Query
	 */
	protected function query( $query_args = [] ) {
		$settings           = $this->get_settings_for_display();
		$post_type_settings = 'post_type';
		if ( $this->is_product_template() ) {
			$post_type_settings = 'query_' . $post_type_settings;
		}
		$post_type = $settings[ $post_type_settings ];

		if ( $post_type === 'current_query' ) {
			return static::get_current_query( $settings );
		}

		$this->posts_per_page = $this->template( Pagination::class )->get_posts_per_page();
		$this->paged          = $this->template( Pagination::class )->get_paged();

		if ( $this->is_product_template() ) {
			// Loop query.
			$query_builder = new Products_Query( $settings, 'query_' );

			$query_builder->add_pre_query_hooks();
			$args                   = $query_builder->parse_query_args();
			$args['paged']          = $this->paged;
			$args['posts_per_page'] = $this->posts_per_page;

			$query_args = array_merge( $args, $query_args );

			$query = new \WP_Query( $query_args );

			$query_builder->remove_pre_query_hooks();
			return $query;
		}

		$taxonomy = $settings['taxonomy'];
		$terms    = $settings['terms'];

		// Loop query.
		$args = [
			'posts_offset'   => $settings['posts_offset'],
			'post_type'      => $post_type,
			'order'          => $settings['order'],
			'orderby'        => $settings['orderby'],
			'paged'          => $this->paged,
			'posts_per_page' => $this->posts_per_page,
		];

		$query_args = array_merge( $args, $query_args );

		if ( $post_type === 'related' ) {
			$query_builder = new The7_Related_Query_Builder( $query_args );
		} else {
			$query_builder = new The7_Query_Builder( $query_args );
		}

		$query_builder->from_terms( $taxonomy, $terms );

		$request = $this->get_categorization_request();
		if ( $request ) {
			$loading_mode = $this->template( Pagination::class )->get_loading_mode();
			if ( ! empty( $request->taxonomy ) || $loading_mode === 'standard' || $loading_mode === 'ajax_pagination' || $this->has_alternate_templates() ) {
				$query_builder->with_categorizaition( $request );
			}
		}

		return $query_builder->query();
	}

	/**
	 * Add container class attribute.
	 *
	 * @param string $element Elementor element.
	 */
	protected function add_container_attributes( $element ) {
		$class   = [];
		$class[] = 'the7-elementor-widget';

		$settings = $this->get_settings_for_display();

		$class[] = presscore_tpl_get_load_effect_class( $settings['loading_effect'] );

		$loading_mode = $settings['loading_mode'];

		if ( 'js_lazy_loading' === $loading_mode ) {
			$class[] = 'loading-effect-none';
		}
		if ( $this->is_product_template() ) {
			$class[] = 'woocommerce';
		}

		if ( $this->has_alternate_templates() ) {
			$class[] = 'the7-alternate-templates';
		}

		$this->add_render_attribute( $element, 'class', $class );
	}

	/**
	 * Render Empty View.
	 * Renders the widget's view if there is no posts to display.
	 */
	protected function render_empty_view() {
		if ( Elementor::$instance->editor->is_edit_mode() ) {
			?>
			<div class="e-loop-empty-view__wrapper"><!-- Will be filled with JS --></div>
			<?php
		}
	}

	/**
	 * @param array $terms   Terms array.
	 *
	 * @return void
	 */
	protected function display_filter( $terms ) {
		$request      = $this->get_categorization_request();
		$settings     = $this->get_settings_for_display();
		$loading_mode = $this->template( Pagination::class )->get_loading_mode();

		// This allows to reload page on filter items click.
		if ( $loading_mode === 'standard' || $loading_mode === 'ajax_pagination' || $this->has_alternate_templates() ) {
			$filter_class[] = 'without-isotope';
		}

		$show_order     = false;
		$show_orderby   = false;
		$filter_class[] = 'filter';

		$current_term = 'all';

		$sorting_args = [
			'show_order'      => $show_order,
			'show_orderby'    => $show_orderby,
			'order'           => $settings['order'],
			'orderby'         => $settings['orderby'],
			'default_order'   => $settings['order'],
			'default_orderby' => $settings['orderby'],
			'select'          => 'all',
			'term_id'         => 'none',
		];

		if ( is_object( $request ) && $request->not_empty() && $this->use_filter_request() ) {
			if ( $request->order ) {
				$sorting_args['order'] = $request->order;
			}

			if ( $request->orderby ) {
				$sorting_args['orderby'] = $request->orderby;
			}

			$sorting_args['select']  = 'only';
			$sorting_args['term_id'] = $request->get_first_term();
			$current_term            = $request->get_first_term();
		}

		$args_filter_priority = has_filter( 'presscore_get_category_list-args', 'presscore_filter_categorizer_current_arg' );
		remove_filter( 'presscore_get_category_list-args', 'presscore_filter_categorizer_current_arg', $args_filter_priority );

		presscore_get_category_list(
			[
				'data'       => [
					'terms'       => $terms,
					'all_count'   => false,
					'other_count' => false,
				],
				'hash'       => [ 'term' => '%TERM_SLUG%' ],
				'class'      => implode( ' ', $filter_class ),
				'item_class' => 'filter-item',
				'all_class'  => 'show-all filter-item',
				'sorting'    => $sorting_args,
				'all_btn'    => $this->filter_show_all(),
				'all_text'   => $settings['filter_all_text'],
				'current'    => $current_term,
			]
		);

		$args_filter_priority !== false && add_filter( 'presscore_get_category_list-args', 'presscore_filter_categorizer_current_arg', $args_filter_priority );
	}

	/**
	 * @param array $class Class array.
	 *
	 * @return string
	 */
	protected function masonry_item_wrap_class( $class = [] ) {
		global $post;

		if ( ! is_array( $class ) ) {
			$class = explode( ' ', $class );
		}

		$settings = $this->get_settings_for_display();

		if ( $this->filter_is_visible() ) {
			$terms = get_the_terms( $post->ID, $settings['taxonomy'] );
			if ( is_array( $terms ) ) {
				foreach ( $terms as $term ) {
					$class[] = sanitize_html_class( 'category-' . $term->term_id );
				}
			} else {
				$class[] = 'category-0';
			}
		}

		return $class;
	}

	/**
	 * Render Post
	 * Uses the chosen custom template to render Loop posts.
	 */
	protected function render_post() {
		if ( $this->has_alternate_templates() ) {
			$this->render_post_alternate_templates();
		} else {
			$this->render_post_content( $this->get_settings_for_display( 'template_id' ) );
		}
	}

	/**
	 * @param string $template_id Template ID.
	 *
	 * @return void
	 */
	private function render_post_content( $template_id ) {
		$post_id = get_the_ID();

		/** @var LoopDocument $document */
		$document = Elementor::$instance->documents->get( $template_id );

		// Bail if document is not an instance of LoopDocument.
		if ( ! $document instanceof LoopDocument ) {
			return;
		}

		$this->print_dynamic_css( $post_id, $template_id );
		$this->before_skin_render();
		The7_Elementor_Compatibility::instance()->print_loop_document( $document );
		$this->after_skin_render();
	}

	/**
	 * @param int $post_id Post ID.
	 * @param int $post_id_for_data Post ID for data.
	 *
	 * @return void
	 */
	protected function print_dynamic_css( $post_id, $post_id_for_data ) {
		$document = Elementor::instance()->documents->get_doc_for_frontend( $post_id_for_data );

		if ( ! $document ) {
			return;
		}

		Elementor::instance()->documents->switch_to_document( $document );

		$css_file = Loop_Dynamic_CSS::create( $post_id, $post_id_for_data );
		$post_css = $css_file->get_content();

		if ( ! empty( $post_css ) ) {
			$css = str_replace( '.elementor-' . $post_id, '.e-loop-item-' . $post_id, $post_css );
			$css = sprintf( '<style id="%s">%s</style>', 'loop-dynamic-' . $post_id_for_data, $css );

			echo $css; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		Elementor::instance()->documents->restore_document();
	}

	/**
	 * @return void
	 */
	public function before_skin_render() {
		add_filter( 'elementor/document/wrapper_attributes', [ $this, 'add_class_to_loop_item' ], 10, 2 );
	}

	/**
	 * @return void
	 */
	public function after_skin_render() {
		remove_filter( 'elementor/document/wrapper_attributes', [ $this, 'add_class_to_loop_item' ] );
	}
}
