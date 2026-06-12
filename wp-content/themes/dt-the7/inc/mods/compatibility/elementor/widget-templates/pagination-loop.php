<?php
/**
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widget_Templates;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use The7\Mods\Compatibility\Elementor\Modules\AJAX_Pagination\Module as AJAX_Pagination;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Button as Button_Template;

defined('ABSPATH') || exit;

/**
 * Pagination template class.
 */
class Pagination_Loop extends Pagination
{

    /**
     * Register pagination content controls.
     *
     * @param string $query_control_name Query control name.
     */
    public function add_content_controls($query_control_name)
    {
        $this->set_query_control_name($query_control_name);

        $this->widget->start_controls_section('pagination', [
            'label' => esc_html__('Pagination', 'the7mk2'),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ]);

        $this->widget->add_control('loading_mode', [
            'label'      => esc_html__('Pagination mode', 'the7mk2'),
            'type'       => Controls_Manager::SELECT,
            'default'    => 'disabled',
            'options'    => [
                'disabled'        => esc_html__('Disabled', 'the7mk2'),
                'standard'        => esc_html__('Standard', 'the7mk2'),
                'ajax_pagination' => esc_html__('AJAX pages', 'the7mk2'),
                'js_pagination'   => esc_html__('JavaScript pages', 'the7mk2'),
                'js_more_button'  => esc_html__('"Load more" button', 'the7mk2'),
                'js_lazy_loading' => esc_html__('Infinite scroll', 'the7mk2'),
            ],
            'conditions' => [
                'relation' => 'or',
                'terms'    => $this->get_post_type_term_conditions($query_control_name, ['current_query', 'related']),
            ],
        ]);

        $this->widget->add_control('pagination_load_more_text', [
            'label'       => esc_html__('Button Text', 'the7mk2'),
            'type'        => Controls_Manager::TEXT,
            'default'     => esc_html__('Load more', 'the7mk2'),
            'placeholder' => '',
            'conditions'  => [
                'relation' => 'and',
                'terms'    => [
                    [
                        'name'     => 'loading_mode',
                        'operator' => 'in',
                        'value'    => ['js_more_button'],
                    ],
                    [
                        'relation' => 'or',
                        'terms'    => $this->get_post_type_term_conditions($query_control_name, ['current_query']),
                    ],
                ],
            ],
        ]);

        $this->widget->add_control('posts_per_page', [
            'label'      => esc_html__('Posts Per Page', 'the7mk2'),
            'type'       => Controls_Manager::NUMBER,
            'default'    => '',
            'conditions' => [
                'relation' => 'and',
                'terms'    => [
                    [
                        'name'     => 'loading_mode',
                        'operator' => '!in',
                        'value'    => ['disabled'],
                    ],
                    [
                        'relation' => 'or',
                        'terms'    => $this->get_post_type_term_conditions($query_control_name, ['current_query', 'related']),
                    ],
                ],
            ],
        ]);

        $this->widget->add_control('posts_per_page_standart_description', [
            'type'            => Controls_Manager::RAW_HTML,
            'raw'             => esc_html__('Leave empty to use value from the WP Reading settings. Set "-1" to show all posts', 'the7mk2'),
            'separator'       => 'none',
            'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
            'conditions'      => [
                'relation' => 'and',
                'terms'    => [
                    [
                        'name'     => 'loading_mode',
                        'operator' => 'in',
                        'value'    => ['standard'],
                    ],
                    [
                        'relation' => 'or',
                        'terms'    => $this->get_post_type_term_conditions($query_control_name, ['current_query', 'related']),
                    ],
                ],
            ],
        ]);

        $this->widget->add_control('posts_per_page_nonstandart_description', [
            'type'            => Controls_Manager::RAW_HTML,
            'raw'             => esc_html__('Leave empty to use value from the WP Reading settings.', 'the7mk2'),
            'separator'       => 'none',
            'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',

            'conditions' => [
                'relation' => 'and',
                'terms'    => [
                    [
                        'name'     => 'loading_mode',
                        'operator' => '!in',
                        'value'    => ['standard'],
                    ],
                    [
                        'relation' => 'or',
                        'terms'    => $this->get_post_type_term_conditions($query_control_name, ['current_query', 'related']),
                    ],
                ],
            ],
        ]);

        // JS infinite scroll.
        $this->widget->add_control('posts_total', [
            'label'       => esc_html__('Total Number Of Posts', 'the7mk2'),
            'description' => esc_html__('Leave empty to display all posts.', 'the7mk2'),
            'type'        => Controls_Manager::NUMBER,
            'default'     => '',
            'conditions'  => [
                'relation' => 'or',
                'terms'    => [
                    ['relation' => 'and',
                     'terms'    => [
                         [
                             'name'     => 'loading_mode',
                             'operator' => '!in',
                             'value'    => ['standard', 'ajax_pagination'],
                         ],
                         [
                             'relation' => 'or',
                             'terms'    => $this->get_post_type_term_conditions($query_control_name, ['current_query']),
                         ],
                     ],
                    ],
                    [
                        'relation' => 'or',
                        'terms'    => $this->get_post_type_term_conditions($query_control_name, ['related'], '=='),
                    ],
                ],
            ],
        ]);

        $this->widget->add_control('pagination_scroll', [
            'label'              => esc_html__('Scroll to Top', 'the7mk2'),
            'type'               => Controls_Manager::SWITCHER,
            'description'        => esc_html__('When enabled, scrolls page to top of widget.', 'the7mk2'),
            'return_value'       => 'y',
            'default'            => 'y',
            'condition'          => [
                'loading_mode' => ['js_pagination', 'ajax_pagination'],
            ],
            'render_type'        => 'none',
            'frontend_available' => true,
        ]);

        $this->widget->add_control('pagination_scroll_offset', [
            'label'              => esc_html__('Scroll offset (px)', 'the7mk2'),
            'type'               => Controls_Manager::SLIDER,
            'description'        => esc_html__('Negative value will scroll page above top of widget; positive - below it.', 'the7mk2'),
            'default'            => [
                'unit' => 'px',
                'size' => '0',
            ],
            'range'              => [
                'px' => [
                    'max'  => 500,
                    'min'  => -500,
                    'step' => 1,
                ],
            ],
            'condition'          => [
                'pagination_scroll' => 'y',
                'loading_mode'      => ['js_pagination', 'ajax_pagination'],
            ],
            'render_type'        => 'none',
            'frontend_available' => true,
        ]);


        // Posts offset.
        $this->widget->add_control('posts_offset', [
            'label'       => esc_html__('Posts Offset', 'the7mk2'),
            'description' => esc_html__('Offset for posts query (i.e. 2 means, posts will be displayed starting from the third post).', 'the7mk2'),
            'type'        => Controls_Manager::NUMBER,
            'default'     => 0,
            'min'         => 0,
            'conditions'  => [
                'relation' => 'or',
                'terms'    => $this->get_post_type_term_conditions($query_control_name, ['current_query', 'related']),
            ],
        ]);

        $this->widget->add_control('show_all_pages', [
            'label'        => esc_html__('Show All Pages In Paginator', 'the7mk2'),
            'type'         => Controls_Manager::SWITCHER,
            'return_value' => 'y',
            'default'      => '',
            'conditions'   => [
                'relation' => 'or',
                'terms'    => [
                    [
                        'name'     => 'loading_mode',
                        'operator' => 'in',
                        'value'    => ['standard', 'js_pagination', 'ajax_pagination'],
                    ],
                    [
                        'relation' => 'or',
                        'terms'    => $this->get_post_type_term_conditions($query_control_name, ['current_query'], 'in'),
                    ],
                ],
            ],
        ]);

        $this->widget->end_controls_section();
    }

    public function get_post_type_term_conditions($query_control_name, $post_types, $operator = '!in')
    {
        return [
            [
                'relation' => 'and',
                'terms'    => [
                    [
                        'name'     => 'template_type',
                        'operator' => '==',
                        'value'    => 'posts',
                    ],
                    [
                        'name'     => $query_control_name,
                        'operator' => $operator,
                        'value'    => $post_types,
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
                        'name'     => 'query_' . $query_control_name,
                        'operator' => $operator,
                        'value'    => $post_types,
                    ],
                ],
            ],
        ];
    }

    /**
     * Add container attributes.
     *
     * @param string $element Element.
     *
     * @return void
     */
    public function add_containter_attributes($element)
    {
        $loading_mode = $this->get_loading_mode();

        $data_pagination_mode = 'none';
        if (in_array($loading_mode, ['js_more_button', 'js_lazy_loading'], true)) {
            $data_pagination_mode = 'load-more';
        } elseif ($loading_mode === 'js_pagination') {
            $data_pagination_mode = 'pages';
        } elseif ($loading_mode === 'standard') {
            $data_pagination_mode = 'standard';
        } elseif ($loading_mode === 'ajax_pagination') {
            $data_pagination_mode = $loading_mode;
        }

        $attributes = [
            'data-post-limit'      => (int)($this->get_post_limit()),
            'data-pagination-mode' => $data_pagination_mode,
            //'data-scroll-offset'   => $this->get_settings( 'pagination_scroll_offset' ),
            'class'                => [],
        ];

//		if ( $this->get_settings( 'pagination_scroll' ) === 'y' ) {
//			$attributes['class'][] = 'enable-pagination-scroll';
//		}

        if ('standard' !== $loading_mode && 'ajax_pagination' !== $loading_mode) {
            $attributes['class'][] = 'jquery-filter';
        }

        if ('js_lazy_loading' === $loading_mode) {
            $attributes['class'][] = 'lazy-loading-mode';
        }

        if ($this->get_settings('show_all_pages')) {
            $attributes['class'][] = 'show-all-pages';
        }

        $attributes['data-paged'] = $this->get_paged();

		if ( ! in_array( $loading_mode, [ 'standard', 'none' ], true ) ) {
			$attributes['aria-live'] = 'assertive';
		}

        $this->widget->add_render_attribute($element, $attributes);
    }

    public function get_loading_mode()
    {
        if ($this->is_current_query()) {
            return $this->get_settings('archive_loading_mode');
        }

        return $this->get_settings( 'loading_mode' );
    }

    protected function is_current_query()
    {
        return ((($this->get_settings('template_type') === 'products')
                && ($this->get_settings('query_post_type' )) === 'current_query')
            || (($this->get_settings('template_type') === 'posts') && $this->get_settings('post_type') === 'current_query'));
    }

    /**
     * Returns post limit based on loading mode.
     * @return string|int
     */
    public function get_post_limit()
    {
        $post_limit = '-1';
        $loading_mode = $this->get_loading_mode();
        if ($loading_mode !== 'none' && $loading_mode !== 'disabled') {
            if ($this->is_current_query()) {
                $post_limit = $this->get_settings('archive_posts_per_page') ?: get_option('posts_per_page');
            } else {
                $post_limit = $this->get_settings('posts_per_page') ?: get_option('posts_per_page');
            }
        }

        return $post_limit;
    }

    /**
     * @return int
     */
    public function get_paged()
    {
        $loading_mode = $this->get_loading_mode();
        if (in_array($loading_mode, ['standard', 'ajax_pagination'], true)) {
            return max(the7_get_paged_var(), (int) $this->get_val($_GET, AJAX_Pagination::WIDGET_PAGE_ID_PARAM . $this->widget->get_id()));
        }

        return 1;
    }

    function get_val($arr, $key)
    {
        if ( ! isset($arr[ $key ])) {
            return null;
        }

        return wp_kses_post_deep(wp_unslash($arr[ $key ]));
    }

    /**
     * @return int
     */
    public function get_posts_per_page()
    {
        $settings = wp_parse_args(
            $this->get_settings(),
            [
                'posts_total' => -1,
            ]
        );
        $loading_mode = $this->get_loading_mode();
        if (in_array($loading_mode, ['standard', 'ajax_pagination'], true)) {
            $posts_per_page = $settings['posts_per_page'] ?: get_option('posts_per_page');
        } else {
            $posts_per_page = $settings['posts_total'] === "" ? -1 : $settings['posts_total'];
        }

        $max_posts_per_page = 99999;
        $posts_per_page = (int)$posts_per_page;
        if ($posts_per_page === -1) {
            $posts_per_page = $max_posts_per_page;
        }

        return $posts_per_page;
    }

    /**
     * Render pagination.
     *
     * @param int $max_num_pages Max num pages.
     */
    public function render($max_num_pages)
    {
        global $wp_query;

        $loading_mode = $this->get_loading_mode();


        if (in_array($loading_mode, ['js_more_button'], true)) {
            $this->widget->add_render_attribute('paginator-wrapper', 'class', [
                'paginator-more-button',
            ]);
            ?>
            <div <?php echo $this->widget->get_render_attribute_string('paginator-wrapper'); ?>>
                <?php

                $this->widget->add_render_attribute('box-button', 'href', ['#']);
                $this->widget->add_render_attribute('box-button', 'class', ['button-load-more', 'filter-item']);
                $this->widget->template(Button_Template::class)->render_button('box-button', esc_html($this->get_settings('pagination_load_more_text')), 'a', 'load_more_');
                ?>
            </div>
            <?php
        } elseif (in_array($loading_mode, ['ajax_pagination'], true)) {
            $is_current_query = $this->is_current_query();
            if ( ! $is_current_query) {
                add_filter('dt_paginator_args', [$this, 'ajax_paginator_filter']);
            }
            $this->render_standard_pagination($max_num_pages, $this->get_pagination_wrap_class());
            if ( ! $is_current_query) {
                remove_filter('dt_paginator_args', [$this, 'ajax_paginator_filter']);
            }
        } else {
            parent::render($max_num_pages);
        }
    }

    /**
     * Return pagination wrapper common classes.
     *
     * @param string $class Custom class.
     *
     * @return string
     */
    public function get_pagination_wrap_class($class = '')
    {
        $wrap_class = ['paginator', $class];

        return implode(' ', array_filter($wrap_class));
    }

    function ajax_paginator_filter($opts)
    {
        global $wp_rewrite;

        if (is_singular() && ! is_front_page()) {
            if ($wp_rewrite->using_permalinks()) {
                $opts['base'] = trailingslashit(get_permalink()) . '%_%';
            }
        }

        $query_connector = ! empty($opts['base']) && strpos($opts['base'], '?') ? '&' : '?';
        $opts['format'] = $query_connector . AJAX_Pagination::WIDGET_PAGE_ID_PARAM . $this->widget->get_id() . '=%#%';

        $opts['paged'] = $this->get_paged();

        return $opts;
    }

    /**
     * Register pagination style controls.
     *
     * @param string $query_control_name Query control name to participate in cinsitions.
     */
    public function add_style_controls($query_control_name)
    {
        $this->widget->start_controls_section(
            'pagination_style_tab',
            [
                'label'      => esc_html__('Pagination', 'the7mk2'),
                'tab'        => Controls_Manager::TAB_STYLE,
                'conditions' => [
                    'relation' => 'or',
                    'terms'    => [
                        [
                            'name'     => 'loading_mode',
                            'operator' => 'in',
                            'value'    => ['standard', 'js_pagination', 'js_more', 'ajax_pagination'],
                        ],
                        [
                            'relation' => 'or',
                            'terms'    => $this->get_post_type_term_conditions($query_control_name, ['current_query'], 'in'),
                        ],
                    ],
                ],
            ]
        );

        $selector = '{{WRAPPER}} .the7-elementor-widget > .paginator';
        $item_selector = $selector . ' a';

        $this->widget->add_control(
            'pagination_position',
            [
                'label'                => esc_html__('Align', 'the7mk2'),
                'type'                 => Controls_Manager::CHOOSE,
                'toggle'               => false,
                'default'              => 'center',
                'options'              => [
                    'left'   => [
                        'title' => esc_html__('Left', 'the7mk2'),
                        'icon'  => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'the7mk2'),
                        'icon'  => 'eicon-text-align-center',
                    ],
                    'right'  => [
                        'title' => esc_html__('Right', 'the7mk2'),
                        'icon'  => 'eicon-text-align-right',
                    ],
                ],
                'selectors_dictionary' => [
                    'left'   => 'flex-start',
                    'center' => 'center',
                    'right'  => 'flex-end',
                ],
                'selectors'            => [
                    $selector => 'justify-content: {{VALUE}};',
                ],
            ]
        );

        $this->widget->add_responsive_control('pagination_min_width', [
            'label'      => esc_html__('Min Width', 'the7mk2'),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range'      => [
                'px' => [
                    'min' => 0,
                    'max' => 200,
                ],
            ],
            'selectors'  => [
                $item_selector => 'min-width: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->widget->add_responsive_control('pagination_min_height', [
            'label'      => esc_html__('Min Height', 'the7mk2'),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range'      => [
                'px' => [
                    'min' => 0,
                    'max' => 200,
                ],
            ],
            'selectors'  => [
                $item_selector => 'min-height: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->widget->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'pagination_typography',
                'label'    => esc_html__('Typography', 'the7mk2'),
                'selector' => $selector . ' a,' . $selector . ' .button-load-more',
                'exclude'  => [
                    'text_decoration',
                ],
            ]
        );

        $this->widget->add_responsive_control('pagination_border_width', [
            'label'      => esc_html__('Border width', 'the7mk2'),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => ['px'],
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
        ]);

        $this->widget->add_responsive_control('pagination_border_radius', [
            'label'      => esc_html__('Border Radius', 'the7mk2'),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors'  => [
                $item_selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->widget->add_responsive_control('pagination_element_padding', [
            'label'      => esc_html__('Padding', 'the7mk2'),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => ['px'],
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
        ]);

        $this->widget->start_controls_tabs('pagination_style');

        $this->add_pagination_states_controls('normal_', esc_html__('Normal', 'the7mk2'));
        $this->add_pagination_states_controls('hover_', esc_html__('Hover', 'the7mk2'));
        $this->add_pagination_states_controls('active_', esc_html__('Active', 'the7mk2'));

        $this->widget->end_controls_tabs();

        $this->widget->add_responsive_control('pagination_column_gap', [
            'label'      => esc_html__('Columns Gap', 'the7mk2'),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'default'    => [
                'size' => '30',
            ],
            'range'      => [
                'px' => [
                    'max' => 100,
                ],
            ],
            'selectors'  => [
                $selector => '--paginator-column-gap: {{SIZE}}{{UNIT}};',
            ],
            'separator'  => 'before',
        ]);

        $this->widget->add_responsive_control('pagination_rows_gap', [
            'label'      => esc_html__('Rows Gap', 'the7mk2'),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'default'    => [
                'size' => '30',
            ],
            'range'      => [
                'px' => [
                    'max' => 100,
                ],
            ],
            'selectors'  => [
                $selector => '--paginator-row-gap: {{SIZE}}{{UNIT}}',
            ],
        ]);

        $this->widget->add_control(
            'pagination_margin',
            [
                'label'      => esc_html__('Margin', 'the7mk2'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px'],
                'default'    => [
                    'top'      => '',
                    'right'    => '',
                    'bottom'   => '',
                    'left'     => '',
                    'unit'     => 'px',
                    'isLinked' => true,
                ],
                'selectors'  => [
                    $selector => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ],
            ]
        );

        $this->widget->end_controls_section();
    }

    /**
     * @param string $prefix_name Prefix.
     * @param string $box_name    Box.
     *
     * @return void
     */
    protected function add_pagination_states_controls($prefix_name, $box_name)
    {
        $sel_prefix = '';
        if (strpos($prefix_name, 'active_') === 0) {
            $sel_prefix = '.act';
        }
        if (strpos($prefix_name, 'hover_') === 0) {
            $sel_prefix = ':hover';
        }

        $selector = '{{WRAPPER}} .the7-elementor-widget > .paginator a' . $sel_prefix;

        $this->widget->start_controls_tab($prefix_name . 'paginator_tab_style', [
            'label' => $box_name,
        ]);

        $this->widget->add_control($prefix_name . 'paginator_color', [
            'label'     => esc_html__('Text Color', 'the7mk2'),
            'type'      => Controls_Manager::COLOR,
            'alpha'     => true,
            'default'   => '',
            'selectors' => [
                $selector => "color: {{VALUE}};",
            ],
        ]);

        $this->widget->add_control($prefix_name . 'paginator_border_color', [
            'label'     => esc_html__('Border Color', 'the7mk2'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
                $selector => "border-color: {{VALUE}};",
            ],
        ]);

        $this->widget->add_control($prefix_name . 'paginator_background_color', [
            'label'     => esc_html__('Background Color', 'the7mk2'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
                $selector => "background-color: {{VALUE}};",
            ],
        ]);

        $this->widget->add_group_control(Group_Control_Box_Shadow::get_type(), [
            'name'     => $prefix_name . 'paginator_box_shadow',
            'selector' => $selector,
        ]);

        $this->widget->end_controls_tab();
    }

}


