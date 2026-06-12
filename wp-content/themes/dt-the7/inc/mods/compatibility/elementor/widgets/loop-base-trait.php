<?php

namespace The7\Mods\Compatibility\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Core\Base\Document;
use Elementor\Plugin as Elementor;
use ElementorPro\Modules\LoopBuilder\Documents\Loop as LoopDocument;
use ElementorPro\Modules\LoopBuilder\Files\Css\Loop_Dynamic_CSS;
use ElementorPro\Modules\QueryControl\Controls\Template_Query;
use ElementorPro\Modules\QueryControl\Module as QueryControlModule;
use The7\Mods\Compatibility\Elementor\Shortcode_Adapters\Query_Adapters\Products_Query;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Woocommerce\Products_Query as Query;
use The7_Categorization_Request;
use The7_Elementor_Compatibility;
use The7_Query_Builder;
use The7_Related_Query_Builder;

trait Loop_Base_Trait{
    /**
     * @var \WP_Query
     */
    private $_query = null;


    public function print_dynamic_css($post_id, $post_id_for_data)
    {
        $document = Elementor::instance()->documents->get_doc_for_frontend($post_id_for_data);

        if ( ! $document) {
            return;
        }

        Elementor::instance()->documents->switch_to_document($document);

        $css_file = Loop_Dynamic_CSS::create($post_id, $post_id_for_data);
        $post_css = $css_file->get_content();

        if ( ! empty($post_css)) {
            $css = str_replace('.elementor-' . $post_id, '.e-loop-item-' . $post_id, $post_css);
            $css = sprintf('<style id="%s">%s</style>', 'loop-dynamic-' . $post_id_for_data, $css);

            echo $css; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }
        Elementor::instance()->documents->restore_document();
    }




    /**
     * @param array $query_args query settings
     *
     * @return \WP_Query
     */
    public function query($query_args = [])
    {
        $settings = $this->get_settings_for_display();
        $post_type_settings = 'post_type';
        if ($this->is_product_template()) {
            $post_type_settings = 'query_' . $post_type_settings;
        }
        $post_type = $settings[ $post_type_settings ];

        if ($post_type === 'current_query') {
            $this->_query = The7_Elementor_Widget_Base::get_current_query($settings);
            return  $this->_query;
        }

        if ($this->is_product_template()) {
            $settings['posts_offset'] = $settings['product_posts_offset'];
            // Loop query.
            $query_builder = new Products_Query($settings, 'query_');

            $query_builder->add_pre_query_hooks();
            $args = $query_builder->parse_query_args();

            $query_args = array_merge($args, $query_args);

            $this->_query = new \WP_Query($query_args);

            $query_builder->remove_pre_query_hooks();

            return  $this->_query;
        }

        $taxonomy = $settings['taxonomy'];
        $terms = $settings['terms'];

        // Loop query.
        $args = [
            'posts_offset' => $settings['posts_offset'],
            'post_type'    => $post_type,
            'order'        => $settings['order'],
            'orderby'      => $settings['orderby'],
        ];

        $query_args = array_merge($args, $query_args);

        if ($post_type === 'related') {
            $query_builder = new The7_Related_Query_Builder($query_args);
        } else {
            $query_builder = new The7_Query_Builder($query_args);
        }

        $query_builder->from_terms($taxonomy, $terms);

        $request = $this->get_categorization_request();
        if ($request) {
            $query_builder->with_categorizaition($request);
        }

        $this->_query = $query_builder->query();

        return $this->_query;
    }

    protected function get_categorization_request()
    {
        $request = new The7_Categorization_Request();
        if ($request->taxonomy && $request->not_empty()) {
            return $request;
        }

        return null;
    }

    /**
     * @return bool
     */
    public function is_product_template()
    {
        return the7_is_woocommerce_enabled() && $this->get_settings_for_display('template_type') === 'products';
    }

    public function render_posts()
    {
        /** @var \WP_Query $query */
        $query = $this->_query;

        if ( ! $query->found_posts) {
            return;
        }

        // It's the global `wp_query` it self. and the loop was started from the theme.
        if ($query->in_the_loop) {
            $this->render_post();
        } else {
            $is_product_template = $this->is_product_template();

            while ($query->have_posts()) {
                $query->the_post();

                if ($is_product_template) {
                    // Start loop.
                    global $product;
                    $product = wc_get_product(get_the_ID());
                }

                $this->render_post();
            }
        }

        wp_reset_postdata();
    }

    protected function get_initial_config()
    {
        $config = parent::get_initial_config();

        $config['is_loop'] = true;
        $config['edit_handle_selector'] = '.elementor-widget-container';

        return $config;
    }

    /*
     * @return  \WP_Query $query
    */
    public function get_query()
    {
        return $this->_query;
    }

    protected function render_post()
    {
        $this->render_post_content($this->get_settings('template_id'));
    }


    private function render_post_content($template_id)
    {
        $post_id = get_the_ID();

        /** @var LoopDocument $document */
        $document = Elementor::$instance->documents->get($template_id);

        // Bail if document is not an instance of LoopDocument.
        if ( ! $document instanceof LoopDocument) {
            return;
        }

        $this->print_dynamic_css($post_id, $template_id);
        $this->before_skin_render();
        The7_Elementor_Compatibility::instance()->print_loop_document($document);
        $this->after_skin_render();
    }

    public function before_skin_render()
    {
        add_filter('elementor/document/wrapper_attributes', [$this, 'skin_render_callback'], 10, 2);
    }

    public function after_skin_render()
    {
        remove_filter('elementor/document/wrapper_attributes', [$this, 'skin_render_callback']);
    }

    public function skin_render_callback($attributes, $document)
    {
        return $attributes;
    }

    protected function add_loop_content_controls()
    {
        //we should use _skin contoll in order to use inline editing (in this case we use only '_skin' controll to emulate skin usage).
        //The skin name should be 'post'
        $this->add_control('_skin', [
            'label'   => esc_html__('Skin', 'elementor'),
            'type'    => Controls_Manager::HIDDEN,
            'default' => 'post',
        ]);

        $this->add_control('wc_is_active', [
            'type'    => Controls_Manager::HIDDEN,
            'default' => the7_is_woocommerce_enabled() ? 'y' : '',
        ]);

        if (the7_is_woocommerce_enabled()) {
            $this->add_control('template_type', [
                'label'   => esc_html__('Choose Template Type', 'the7mk2'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'posts',
                'options' => [
                    'posts'    => esc_html__('Posts', 'the7mk2'),
                    'products' => esc_html__('Products', 'the7mk2'),
                ],
            ]);
        } else {
            $this->add_control('template_type', [
                'type'    => Controls_Manager::HIDDEN,
                'default' => 'posts',
            ]);
        }

        $this->add_control('template_id', [
            'label'              => esc_html__('Choose Loop Template', 'the7mk2'),
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
        ]);
    }

    /**
     * this section only for loop skin
     * @return void
     */
    public function add_query_content_controls()
    {
        $this->add_posts_query_content_controls();
        $this->add_products_query_content_controls();
    }

    /**
     * @return void
     */
    protected function add_posts_query_content_controls()
    {
        /**
         * Must have section_id = query_section to work properly.
         * @see elements-widget-settings.js:onEditSettings()
         */
        $this->start_controls_section('query_section', [
            'label'      => esc_html__('Query', 'the7mk2'),
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
        ]);

        $this->add_control('post_type', [
            'label'   => esc_html__('Source', 'the7mk2'),
            'type'    => Controls_Manager::SELECT2,
            'default' => 'post',
            'options' => the7_elementor_elements_widget_post_types() + ['related' => esc_html__('Related', 'the7mk2')],
            'classes' => 'select2-medium-width',
        ]);

        $this->add_control('taxonomy', [
            'label'     => esc_html__('Select Taxonomy', 'the7mk2'),
            'type'      => Controls_Manager::SELECT,
            'default'   => 'category',
            'options'   => [],
            'classes'   => 'select2-medium-width',
            'condition' => [
                'post_type!' => ['', 'current_query'],
            ],
        ]);

        $this->add_control('terms', [
            'label'     => esc_html__('Select Terms', 'the7mk2'),
            'type'      => Controls_Manager::SELECT2,
            'default'   => '',
            'multiple'  => true,
            'options'   => [],
            'classes'   => 'select2-medium-width',
            'condition' => [
                'taxonomy!'  => '',
                'post_type!' => ['current_query', 'related'],
            ],
        ]);

        $this->add_control('order', [
            'label'     => esc_html__('Order', 'the7mk2'),
            'type'      => Controls_Manager::SELECT,
            'default'   => 'desc',
            'options'   => [
                'asc'  => esc_html__('Ascending', 'the7mk2'),
                'desc' => esc_html__('Descending', 'the7mk2'),
            ],
            'condition' => [
                'post_type!' => 'current_query',
            ],
        ]);

        $this->add_control('orderby', [
            'label'     => esc_html__('Order By', 'the7mk2'),
            'type'      => Controls_Manager::SELECT,
            'default'   => 'date',
            'options'   => [
                'date'          => esc_html__('Date', 'the7mk2'),
                'title'         => esc_html__('Name', 'the7mk2'),
                'ID'            => esc_html__('ID', 'the7mk2'),
                'modified'      => esc_html__('Modified', 'the7mk2'),
                'comment_count' => esc_html__('Comment count', 'the7mk2'),
                'menu_order'    => esc_html__('Menu order', 'the7mk2'),
                'rand'          => esc_html__('Rand', 'the7mk2'),
            ],
            'condition' => [
                'post_type!' => 'current_query',
            ],
        ]);


        $this->add_control('dis_posts_total', [
            'label'       => esc_html__('Total Number Of Posts', 'the7mk2'),
            'description' => esc_html__('Leave empty to display all posts.', 'the7mk2'),
            'type'        => Controls_Manager::NUMBER,
            'default'     => '12',
            'condition'   => [
                'post_type!' => 'current_query',
            ],
        ]);

        $this->add_control('posts_offset', [
            'label'       => esc_html__('Posts Offset', 'the7mk2'),
            'description' => esc_html__('Offset for posts query (i.e. 2 means, posts will be displayed starting from the third post).', 'the7mk2'),
            'type'        => Controls_Manager::NUMBER,
            'default'     => 0,
            'min'         => 0,
            'condition'   => [
                'post_type!' => 'current_query',
            ],
        ]);

        $this->end_controls_section();
    }

    /**
     * @return void
     */
    protected function add_products_query_content_controls()
    {
        $this->start_controls_section('product_query_section', [
            'label'     => esc_html__('Query', 'the7mk2'),
            'tab'       => Controls_Manager::TAB_CONTENT,
            'condition' => [
                'template_type' => ['products'],
                'wc_is_active'  => 'y',
            ],
        ]);

        $this->template(Query::class)->add_query_group_control();


        $this->add_control('product_dis_posts_total', [
            'label'       => esc_html__('Total Number Of Posts', 'the7mk2'),
            'description' => esc_html__('Leave empty to display all posts.', 'the7mk2'),
            'type'        => Controls_Manager::NUMBER,
            'default'     => '12',
            'condition'   => [
                'query_post_type!' => 'current_query',
            ],
        ]);

        $this->add_control('product_posts_offset', [
            'label'       => esc_html__('Posts Offset', 'the7mk2'),
            'description' => esc_html__('Offset for posts query (i.e. 2 means, posts will be displayed starting from the third post).', 'the7mk2'),
            'type'        => Controls_Manager::NUMBER,
            'default'     => 0,
            'min'         => 0,
            'condition'   => [
                'query_post_type!' => 'current_query',
            ],
        ]);

        $this->end_controls_section();
    }
}