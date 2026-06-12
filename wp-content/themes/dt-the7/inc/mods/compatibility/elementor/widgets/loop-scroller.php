<?php

namespace The7\Mods\Compatibility\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Core\Base\Document;
use Elementor\Group_Control_Border;
use Elementor\Icons_Manager;
use Elementor\Plugin as Elementor;
use Elementor\Repeater;
use ElementorPro\Modules\LoopBuilder\Documents\Loop as LoopDocument;
use ElementorPro\Modules\QueryControl\Controls\Template_Query;
use ElementorPro\Modules\QueryControl\Module as QueryControlModule;
use The7\Mods\Compatibility\Elementor\Modules\Loop\Alternate_Template_Trait;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;
use The7\Mods\Compatibility\Elementor\Widgets\Skins\Slider\Skin_Normal;

/**
 * Slider widget class.
 */
class Loop_Scroller extends The7_Elementor_Widget_Base
{
    use Alternate_Template_Trait;
    use Loop_Base_Trait;

    const SLIDES_PER_VIEW_DEFAULT = '3';

    const WIDGET_NAME = 'the7-multipurpose-scroller';
    const ASSET_NAME = self::WIDGET_NAME . '-widget';

    /**
     * @return string[]
     */
    public function get_style_depends()
    {
        return [self::ASSET_NAME];
    }

    /**
     * @return string[]
     */
    public function get_script_depends()
    {
        return [self::ASSET_NAME];
    }

    /**
     * @return string|void
     */
    protected function the7_title()
    {
        return esc_html__('Loop Scroller', 'the7mk2');
    }

    /**
     * @return string
     */
    protected function the7_icon()
    {
        return 'eicon-posts-carousel';
    }

    /**
     * @return string[]
     */
    protected function the7_keywords()
    {
        return ['slides', 'carousel', 'image', 'slider', 'loop', 'custom post type', 'carousel'];
    }

    /**
     * Register widget assets.
     * @see The7_Elementor_Widget_Base::__construct()
     */
    protected function register_assets()
    {
        the7_register_style(
            self::ASSET_NAME,
            THE7_ELEMENTOR_CSS_URI . '/the7-multipurpose-scroller',
            ['the7-multipurpose-scroller']
        );
        the7_register_script_in_footer(
            self::ASSET_NAME,
            THE7_ELEMENTOR_JS_URI . '/the7-multipurpose-scroller',
            ['the7-multipurpose-scroller']
        );
    }


    /**
     * @return string
     */
    public function get_name()
    {
        return self::WIDGET_NAME;
    }

    protected function register_controls()
    {
        $this->start_controls_section('section_layout', [
            'label' => esc_html__('Loop Template', 'the7mk2'),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ]);
        $this->add_control('slider_wrap_helper', [
            'type'         => Controls_Manager::HIDDEN,
            'default'      => 'elementor-widget-loop-' . $this->get_name(),
            'prefix_class' => '',
        ]);

        $this->add_loop_content_controls();
        $this->add_alternate_templates_controls();

        $this->end_controls_section();

        $this->add_layout_content_controls();
        $this->add_query_content_controls();
        $this->add_arrows_content_controls();
        $this->add_progress_content_controls();

        $this->add_arrows_style_controls();
        $this->add_progress_style_controls();
    }

    protected function add_alternate_templates_controls()
    {
        $this->add_control(
            'alternate_template',
            [
                'label'              => esc_html__('Apply an alternate template', 'the7mk2'),
                'type'               => Controls_Manager::SWITCHER,
                'label_off'          => esc_html__('Off', 'the7mk2'),
                'label_on'           => esc_html__('On', 'the7mk2'),
                'condition'          => [
                    'template_id!' => '',
                ],
                'render_type'        => 'template',
                'frontend_available' => true,
                'separator'          => 'before',
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'template_id',
            [
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
                        'after_action'    => false,
                    ],
                    'edit' => [
                        'visible'      => true,
                        'after_action' => false,
                    ],
                ],
                'frontend_available' => true,
            ]
        );

        $repeater->add_control(
            'repeat_template',
            [
                'label'     => esc_html__('Position in slider', 'the7mk2'),
                'type'      => Controls_Manager::NUMBER,
                'condition' => [
                    'template_id!' => '',
                ],
            ]
        );

        $repeater->add_control(
            'repeat_template_note',
            [
                'type'            => Controls_Manager::RAW_HTML,
                'raw'             => esc_html__('Note: Repeat the alternate template once every chosen number of items.', 'the7mk2'),
                'content_classes' => 'elementor-descriptor',
                'condition'       => [
                    'template_id!' => '',
                ],
            ]
        );

        $repeater->add_control(
            'show_once',
            [
                'label'       => esc_html__('Apply Once', 'the7mk2'),
                'type'        => Controls_Manager::SWITCHER,
                'default'     => 'yes',
                'label_off'   => esc_html__('No', 'the7mk2'),
                'label_on'    => esc_html__('Yes', 'the7mk2'),
                'condition'   => [
                    'template_id!' => '',
                ],
                'render_type' => 'template',
            ]
        );

        $repeater->add_control(
            'static_position',
            [
                'label'       => esc_html__('Static item position', 'the7mk2'),
                'type'        => Controls_Manager::SWITCHER,
                'label_off'   => esc_html__('Off', 'the7mk2'),
                'label_on'    => esc_html__('On', 'the7mk2'),
                'condition'   => [
                    'template_id!' => '',
                ],
                'render_type' => 'template',
            ]
        );

        $repeater->add_control(
            'static_position_note',
            [
                'type'            => Controls_Manager::RAW_HTML,
                'raw'             => esc_html__('Note: Can be used for featured posts, promo banners etc. Static Items remain in place when new items are added to grid. Other items appear according to query settings.', 'the7mk2'),
                'content_classes' => 'elementor-descriptor',
                'condition'       => [
                    'static_position!' => '',
                    'template_id!'     => '',
                ],
            ]
        );

        $this->add_control(
            'alternate_templates',
            [
                'label'       => esc_html__('Templates', 'the7mk2'),
                'type'        => Controls_Manager::REPEATER,
                'fields'      => $repeater->get_controls(),
                'title_field' => 'Alternate Template',
                'condition'   => [
                    'alternate_template' => 'yes',
                    'template_id!'       => '',
                ],
                'default'     => [
                    [
                        'template_id' => null,
                    ],
                ],
            ]
        );
    }

    protected function add_layout_content_controls()
    {
        $this->start_controls_section('layout_section', [
            'label' => esc_html__('Columns Layout', 'the7mk2'),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ]);

        $slides_per_view = range(1, 12);
        $slides_per_view = array_combine($slides_per_view, $slides_per_view);


        $selector = '{{WRAPPER}} .nativeScroll';

        $this->add_responsive_control('slides_per_view', [
            'type'      => Controls_Manager::SELECT,
            'label'     => esc_html__('Columns', 'the7mk2'),
            'options'   => ['' => esc_html__('Default', 'the7mk2')] + $slides_per_view,
            'default'   => static::SLIDES_PER_VIEW_DEFAULT,
            'selectors' => [
                $selector => '--nsItems: {{VALUE}}',
            ],
        ]);

        $this->add_responsive_control('slides_tail', [
            'type'       => Controls_Manager::SLIDER,
            'label'      => esc_html__(' Show Next Item (%)', 'the7mk2'),
            'options'    => ['' => esc_html__('Default', 'the7mk2')] + $slides_per_view,
            'size_units' => ['px'],
            'range'      => [
                'px' => [
                    'min'  => 0,
                    'max'  => 100,
                    'step' => 1,
                ],
            ],
            'default'    => [
                'unit' => 'px',
                'size' => 25,
            ],
            'selectors'  => [
                $selector => '--nsItemTail: calc(({{SIZE}}/100))',
            ],
        ]);

        $this->add_responsive_control('slides_gap', [
            'label'      => esc_html__('Gap Between Columns (px)', 'the7mk2'),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range'      => [
                'px' => [
                    'min'  => 0,
                    'max'  => 100,
                    'step' => 1,
                ],
            ],
            'default'    => [
                'unit' => 'px',
                'size' => 40,
            ],
            'selectors'  => [
                $selector => '--nsItemGap: {{SIZE}}{{UNIT}}',
            ],
        ]);

        $this->add_responsive_control('slides_min_height', [
            'label'      => esc_html__('Column Min Height (px)', 'the7mk2'),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range'      => [
                'px' => [
                    'min'  => 0,
                    'max'  => 500,
                    'step' => 1,
                ],
            ],
            'default'    => [
                'unit' => 'px',
                'size' => 0,
            ],
            'selectors'  => [
                $selector => '--nsItemMinHeight: {{SIZE}}{{UNIT}}',
            ],
        ]);

        $this->add_responsive_control('slides_min_width', [
            'label'      => esc_html__('Column Min Width (px)', 'the7mk2'),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range'      => [
                'px' => [
                    'min'  => 0,
                    'max'  => 500,
                    'step' => 1,
                ],
            ],
            'default'    => [
                'unit' => 'px',
                'size' => 0,
            ],
            'selectors'  => [
                $selector => '--nsItemMinWidth: {{SIZE}}{{UNIT}}',
            ],
        ]);

        $this->add_responsive_control('slides_inline_padding', [
            'label'      => esc_html__('Inline Scroll Padding (px)', 'the7mk2'),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range'      => [
                'px' => [
                    'min'  => 0,
                    'max'  => 500,
                    'step' => 1,
                ],
            ],
            'default'    => [
                'unit' => 'px',
                'size' => 0,
            ],
            'selectors'  => [
                $selector => '--nsScrollPadding: {{SIZE}}{{UNIT}}',
            ],
        ]);

        $this->add_responsive_control('slides_block_padding', [
            'label'      => esc_html__('Block Padding (px)', 'the7mk2'),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range'      => [
                'px' => [
                    'min'  => 0,
                    'max'  => 500,
                    'step' => 1,
                ],
            ],
            'default'    => [
                'unit' => 'px',
                'size' => 0,
            ],
            'selectors'  => [
                $selector => '--nsScrollBlockPadding: {{SIZE}}{{UNIT}}',
            ],
        ]);


        $this->add_responsive_control(
            'slides_scroll_snap',
            [
                'label'     => esc_html__('Scroll Snap', 'the7mk2'),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'start',
                'options'   => [
                    'start'  => 'Left',
                    'center' => 'Center',
                    'end'    => 'Right',
                    'none'   => 'None',
                ],
                'selectors' => [
                    $selector => '--nsScrollSnapMode: {{VALUE}}',
                ],
            ]
        );


        $this->add_responsive_control(
            'text_align',
            [
                'label'     => esc_html__('Align Items', 'the7mk2'),
                'type'      => Controls_Manager::CHOOSE,
                'options'   => [
                    'flex-start' => [
                        'title' => esc_html__('Start', 'the7mk2'),
                        'icon'  => 'eicon-flex eicon-align-start-v',
                    ],
                    'center'     => [
                        'title' => esc_html__('Center', 'the7mk2'),
                        'icon'  => 'eicon-flex eicon-align-center-v',
                    ],
                    'flex-end'   => [
                        'title' => esc_html__('End', 'the7mk2'),
                        'icon'  => 'eicon-flex eicon-align-end-v',
                    ],
                    'stretch'    => [
                        'title' => esc_html__('Stretch', 'the7mk2'),
                        'icon'  => 'eicon-flex eicon-align-stretch-v',
                    ],
                ],
                'selectors' => [
                    $selector . '> .nsContent' => 'align-items:{{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'slides_justify_content',
            [
                'label'                => esc_html__('No Scroll Content Alignment', 'the7mk2'),
                'type'                 => Controls_Manager::CHOOSE,
                'toggle'               => false,
                'default'              => 'left',
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
                    $selector . '.nsNoScroll > .nsContent' => 'justify-content: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
    }


    protected function add_arrows_content_controls()
    {
        $this->start_controls_section('arrows_section', [
            'label' => esc_html__('Arrows', 'the7mk2'),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ]);
        $selector = '{{WRAPPER}} .nativeScroll';
        $arrow_options = [
            'never'  => esc_html__('Never', 'the7mk2'),
            'always' => esc_html__('Always', 'the7mk2'),
            'hover'  => esc_html__('On Hover', 'the7mk2'),
        ];
        $this->add_responsive_control('arrows', [
            'label'                => esc_html__('Show Arrows', 'the7mk2'),
            'type'                 => Controls_Manager::SELECT,
            'options'              => $arrow_options,
            'device_args'          => $this->generate_device_args(
                [
                    'default' => '',
                    'options' => ['' => esc_html__('Default', 'the7mk2')] + $arrow_options,
                ]
            ),
            'default'              => 'always',
            'frontend_available'   => true,
            'selectors'            => [
                $selector => '{{VALUE}}',
            ],
            'selectors_dictionary' => [
                'never'  => '--arrow-display: none;',
                'always' => '--arrow-display: inline-flex;--arrow-opacity:1;',
                'hover'  => '--arrow-display: inline-flex;--arrow-opacity:0;',
            ],
        ]);

        $this->end_controls_section();
    }

    protected function add_progress_style_controls()
    {
        $this->start_controls_section('progress_style_section', [
            'label' => esc_html__('Scroll Indicator', 'the7mk2'),
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('progress_track_heading', [
            'label' => esc_html__('Track Style', 'the7mk2'),
            'type'  => Controls_Manager::HEADING,
        ]);

        $selector = '{{WRAPPER}} .nativeScroll';

        $selector_track = '{{WRAPPER}} .nativeScroll > .nsProgressTrack';

        $this->add_responsive_control('progress_track_width', [
            'label'      => esc_html__('Track Width', 'the7mk2'),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => ['px', '%'],
            'range'      => [
                'px' => [
                    'min'  => 0,
                    'max'  => 500,
                    'step' => 1,
                ],
            ],
            'selectors'  => [
                $selector => '--nsProgressWidth: {{SIZE}}{{UNIT}}',
            ],
        ]);

        $this->add_responsive_control('progress_track_height', [
            'label'      => esc_html__('Track Height', 'the7mk2'),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range'      => [
                'px' => [
                    'min'  => 0,
                    'max'  => 500,
                    'step' => 1,
                ],
            ],
            'selectors'  => [
                $selector => '--nsProgressHeight: {{SIZE}}{{UNIT}}',
            ],
        ]);

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'progress_track_border',
                'label'    => esc_html__('Border', 'the7mk2'),
                'selector' => $selector_track,
                'exclude'  => ['color'],
            ]
        );

        $this->add_responsive_control('progress_track_radius', [
            'label'      => esc_html__('Radius', 'the7mk2'),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors'  => [
                $selector_track => '--nsProgressRadius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);


        $this->add_control('progress_track_color', [
            'label'     => esc_html__('Track Color', 'the7mk2'),
            'type'      => Controls_Manager::COLOR,
            'alpha'     => true,
            'default'   => '',
            'selectors' => [
                $selector_track => 'background: {{VALUE}};',
            ],
        ]);

        $this->add_control(
            'progress_track_border_color',
            [
                'label'     => esc_html__('Border Color', 'the7mk2'),
                'type'      => Controls_Manager::COLOR,
                'alpha'     => true,
                'default'   => '',
                'selectors' => [
                    $selector_track => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control('progress_bar_heading', [
            'label'     => esc_html__('Bar Style', 'the7mk2'),
            'type'      => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $selector_bar = $selector_track . '  > .nsProgressIndicator';

        $this->add_responsive_control('progress_bar_height', [
            'label'      => esc_html__('Track Height', 'the7mk2'),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range'      => [
                'px' => [
                    'min'  => 0,
                    'max'  => 500,
                    'step' => 1,
                ],
            ],
            'selectors'  => [
                $selector_bar => 'height: {{SIZE}}{{UNIT}}',
            ],
        ]);

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'progress_bar_border',
                'label'    => esc_html__('Border', 'the7mk2'),
                'selector' => $selector_bar,
                'exclude'  => ['color'],
            ]
        );

        $this->add_responsive_control('progress_bar_radius', [
            'label'      => esc_html__('Radius', 'the7mk2'),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors'  => [
                $selector_bar => '--nsProgressRadius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);


        $this->add_control('progress_bar_color', [
            'label'     => esc_html__('Bar Color', 'the7mk2'),
            'type'      => Controls_Manager::COLOR,
            'alpha'     => true,
            'default'   => '',
            'selectors' => [
                $selector_bar => 'background: {{VALUE}};',
            ],
        ]);

        $this->add_control(
            'progress_bar_border_color',
            [
                'label'     => esc_html__('Border Color', 'the7mk2'),
                'type'      => Controls_Manager::COLOR,
                'alpha'     => true,
                'default'   => '',
                'selectors' => [
                    $selector_bar => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control('progress_ind_pos_heading', [
            'label'     => esc_html__('Scroll Indicator Position', 'the7mk2'),
            'type'      => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_responsive_control('progress_ind_v_position', [
            'label'                => esc_html__('Vertical Position', 'the7mk2'),
            'type'                 => Controls_Manager::CHOOSE,
            'label_block'          => false,
            'options'              => [
                'top'    => [
                    'title' => esc_html__('Top', 'the7mk2'),
                    'icon'  => 'eicon-v-align-top',
                ],
                'center' => [
                    'title' => esc_html__('Middle', 'the7mk2'),
                    'icon'  => 'eicon-v-align-middle',
                ],
                'bottom' => [
                    'title' => esc_html__('Bottom', 'the7mk2'),
                    'icon'  => 'eicon-v-align-bottom',
                ],
            ],
            'default'              => 'bottom',
            'selectors_dictionary' => [
                'top'    => '--nsProgressTopPosition: var(--nsProgressVOffset); --nsProgressTranslateY:0;',
                'center' => '--nsProgressTopPosition: calc(50% + var(--nsProgressVOffset)); --nsProgressTranslateY:-50%;',
                'bottom' => '--nsProgressTopPosition: calc(100% + var(--nsProgressVOffset)); --nsProgressTranslateY:-100%;',
            ],
            'selectors'            => [
                $selector => '{{VALUE}};',
            ],
        ]);

        $this->add_responsive_control('progress_ind_h_position', [
            'label'                => esc_html__('Horizontal Position', 'the7mk2'),
            'type'                 => Controls_Manager::CHOOSE,
            'label_block'          => false,
            'options'              => [
                'left'   => [
                    'title' => esc_html__('Left', 'the7mk2'),
                    'icon'  => 'eicon-h-align-left',
                ],
                'center' => [
                    'title' => esc_html__('Center', 'the7mk2'),
                    'icon'  => 'eicon-h-align-center',
                ],
                'right'  => [
                    'title' => esc_html__('Right', 'the7mk2'),
                    'icon'  => 'eicon-h-align-right',
                ],
            ],
            'default'              => 'center',
            'selectors_dictionary' => [
                'left'   => '--nsProgressLeftPosition: var(--nsProgressHOffset);--nsProgressTranslateX:0%;',
                'center' => '--nsProgressLeftPosition: calc(50% + var(--nsProgressHOffset));--nsProgressTranslateX:-50%;',
                'right'  => '--nsProgressLeftPosition: calc(100% - var(--nsProgressHOffset));--nsProgressTranslateX:-100%;',
            ],
            'selectors'            => [
                $selector => '{{VALUE}};',
            ],
        ]);

        $this->add_responsive_control('progress_ind_v_offset', [
            'label'      => esc_html__('Vertical Offset', 'the7mk2'),
            'type'       => Controls_Manager::SLIDER,
            'default'    => [
                'unit' => 'px',
                'size' => 0,
            ],
            'size_units' => ['px'],
            'range'      => [
                'px' => [
                    'min'  => -500,
                    'max'  => 500,
                    'step' => 1,
                ],
            ],
            'selectors'  => [
                $selector => '--nsProgressVOffset: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control('progress_ind_h_offset', [
            'label'      => esc_html__('Horizontal Offset', 'the7mk2'),
            'type'       => Controls_Manager::SLIDER,
            'default'    => [
                'unit' => 'px',
                'size' => 0,
            ],
            'size_units' => ['px'],
            'range'      => [
                'px' => [
                    'min'  => -500,
                    'max'  => 500,
                    'step' => 1,
                ],
            ],
            'selectors'  => [
                $selector => '--nsProgressHOffset: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_control('progress_bar_pos_heading', [
            'label'     => esc_html__('Bar Position Relative to Track', 'the7mk2'),
            'type'      => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_responsive_control('progress_bar_v_offset', [
            'label'      => esc_html__('Vertical Offset', 'the7mk2'),
            'type'       => Controls_Manager::SLIDER,
            'default'    => [
                'unit' => 'px',
                'size' => 0,
            ],
            'size_units' => ['px'],
            'range'      => [
                'px' => [
                    'min'  => -500,
                    'max'  => 500,
                    'step' => 1,
                ],
            ],
            'selectors'  => [
                $selector => '--nsProgressBarVOffset: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_section();
    }

    protected function add_arrows_style_controls()
    {
        $this->start_controls_section('arrow_style_section', [
            'label' => esc_html__('Arrows', 'the7mk2'),
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('arrow_icon_heading', [
            'label' => esc_html__('Arrow Icon', 'the7mk2'),
            'type'  => Controls_Manager::HEADING,
        ]);

        $this->add_control('arrow_next', [
            'label'   => esc_html__('Next Arrow', 'the7mk2'),
            'type'    => Controls_Manager::ICONS,
            'default' => [
                'value'   => 'fas fa-chevron-right',
                'library' => 'fa-solid',
            ],
        ]);

        $this->add_control('arrow_prev', [
            'label'   => esc_html__('Previous Arrow', 'the7mk2'),
            'type'    => Controls_Manager::ICONS,
            'default' => [
                'value'   => 'fas fa-chevron-left',
                'library' => 'fa-solid',
            ],
        ]);

        $selector = '{{WRAPPER}} .nativeScroll';

        $this->add_responsive_control('arrow_icon_size', [
            'label'      => esc_html__('Arrow Icon Size', 'the7mk2'),
            'type'       => Controls_Manager::SLIDER,
            'default'    => [
                'unit' => 'px',
                'size' => 24,
            ],
            'size_units' => ['px'],
            'range'      => [
                'px' => [
                    'min'  => 0,
                    'max'  => 200,
                    'step' => 1,
                ],
            ],
            'selectors'  => [
                $selector => '--arrow-icon-size: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_control('arrow_style_heading', [
            'label'     => esc_html__('Arrow style', 'the7mk2'),
            'type'      => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $arrow_selector = '{{WRAPPER}} .nativeScroll > .nsArrow';

        $this->add_responsive_control('arrow_bg_width', [
            'label'      => esc_html__('Background Width', 'the7mk2'),
            'type'       => Controls_Manager::SLIDER,
            'default'    => [
                'unit' => 'px',
                'size' => 40,
            ],
            'size_units' => ['px'],
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
        ]);

        $this->add_responsive_control('arrow_bg_height', [
            'label'      => esc_html__('Background Height', 'the7mk2'),
            'type'       => Controls_Manager::SLIDER,
            'default'    => [
                'unit' => 'px',
                'size' => 40,
            ],
            'size_units' => ['px'],
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
        ]);

        $this->add_control('arrow_border_radius', [
            'label'      => esc_html__('Arrow Border Radius', 'the7mk2'),
            'type'       => Controls_Manager::SLIDER,
            'default'    => [
                'unit' => 'px',
                'size' => 0,
            ],
            'size_units' => ['px'],
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
        ]);

        $this->add_control('arrow_border_width', [
            'label'      => esc_html__('Arrow Border Width', 'the7mk2'),
            'type'       => Controls_Manager::SLIDER,
            'default'    => [
                'unit' => 'px',
                'size' => 0,
            ],
            'size_units' => ['px'],
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
        ]);

        $this->start_controls_tabs('arrows_style_tabs');

        $this->add_arrow_style_states_controls('normal_', esc_html__('Normal', 'the7mk2'));
        $this->add_arrow_style_states_controls('hover_', esc_html__('Hover', 'the7mk2'));
        $this->add_arrow_style_states_controls('inactive_', esc_html__('Inactive', 'the7mk2'));

        $this->end_controls_tabs();

        $this->add_arrow_position_styles('prev_', esc_html__('Prev Arrow Position', 'the7mk2'));
        $this->add_arrow_position_styles('next_', esc_html__('Next Arrow Position', 'the7mk2'));

        $this->end_controls_section();
    }

    /**
     * @param string $prefix_name Prefix.
     * @param string $box_name    Box.
     *
     * @return void
     */
    protected function add_arrow_style_states_controls($prefix_name, $box_name)
    {
        $extra_selector = '';
        if (strpos($prefix_name, 'hover_') === 0) {
            $extra_selector = ':hover';
        }
        if (strpos($prefix_name, 'inactive_') === 0) {
            $extra_selector = '.nsDisabled';
        }

        $selector = '{{WRAPPER}} .nativeScroll > .nsArrow' . $extra_selector;

        $this->start_controls_tab($prefix_name . 'arrow_colors_tab_style', [
            'label' => $box_name,
        ]);

        $this->add_control($prefix_name . 'arrow_icon_color', [
            'label'     => esc_html__('Icon Color', 'the7mk2'),
            'type'      => Controls_Manager::COLOR,
            'alpha'     => true,
            'default'   => '',
            'selectors' => [
                $selector . '> i'   => 'color: {{VALUE}};',
                $selector . '> svg' => 'fill: {{VALUE}};color: {{VALUE}};',
            ],
        ]);

        $this->add_control($prefix_name . 'arrow_border_color', [
            'label'     => esc_html__('Border Color', 'the7mk2'),
            'type'      => Controls_Manager::COLOR,
            'alpha'     => true,
            'default'   => '',
            'selectors' => [
                $selector => 'border-color: {{VALUE}}; outline-color: {{VALUE}};',
            ],
        ]);

        $this->add_control($prefix_name . 'arrow_bg_color', [
            'label'     => esc_html__('Background Color', 'the7mk2'),
            'type'      => Controls_Manager::COLOR,
            'alpha'     => true,
            'default'   => '',
            'selectors' => [
                $selector => 'background: {{VALUE}};',
            ],
        ]);

        $this->end_controls_tab();
    }

    protected function add_arrow_position_styles($prefix, $heading_name)
    {
        $button_class = '';
        $default_h_pos = 'left';
        if ($prefix === 'next_') {
            $button_class = '.nsRightArrow';
            $default_h_pos = 'right';
        } elseif ($prefix === 'prev_') {
            $button_class = '.nsLeftArrow';
        }
        $selector = '{{WRAPPER}} .nativeScroll > .nsArrow' . $button_class;

        $this->add_control($prefix . 'arrow_position_heading', [
            'label'     => $heading_name,
            'type'      => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_responsive_control($prefix . 'arrow_v_position', [
            'label'                => esc_html__('Vertical Position', 'the7mk2'),
            'type'                 => Controls_Manager::CHOOSE,
            'label_block'          => false,
            'options'              => [
                'top'    => [
                    'title' => esc_html__('Top', 'the7mk2'),
                    'icon'  => 'eicon-v-align-top',
                ],
                'center' => [
                    'title' => esc_html__('Middle', 'the7mk2'),
                    'icon'  => 'eicon-v-align-middle',
                ],
                'bottom' => [
                    'title' => esc_html__('Bottom', 'the7mk2'),
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
        ]);

        $this->add_responsive_control($prefix . 'arrow_h_position', [
            'label'                => esc_html__('Horizontal Position', 'the7mk2'),
            'type'                 => Controls_Manager::CHOOSE,
            'label_block'          => false,
            'options'              => [
                'left'   => [
                    'title' => esc_html__('Left', 'the7mk2'),
                    'icon'  => 'eicon-h-align-left',
                ],
                'center' => [
                    'title' => esc_html__('Center', 'the7mk2'),
                    'icon'  => 'eicon-h-align-center',
                ],
                'right'  => [
                    'title' => esc_html__('Right', 'the7mk2'),
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
        ]);

        $this->add_responsive_control($prefix . 'arrow_v_offset', [
            'label'      => esc_html__('Vertical Offset', 'the7mk2'),
            'type'       => Controls_Manager::SLIDER,
            'default'    => [
                'unit' => 'px',
                'size' => 0,
            ],
            'size_units' => ['px'],
            'range'      => [
                'px' => [
                    'min'  => -500,
                    'max'  => 500,
                    'step' => 1,
                ],
            ],
            'selectors'  => [
                $selector => '--arrow-v-offset: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control($prefix . 'arrow_h_offset', [
            'label'      => esc_html__('Horizontal Offset', 'the7mk2'),
            'type'       => Controls_Manager::SLIDER,
            'default'    => [
                'unit' => 'px',
                'size' => 0,
            ],
            'size_units' => ['px'],
            'range'      => [
                'px' => [
                    'min'  => -500,
                    'max'  => 500,
                    'step' => 1,
                ],
            ],
            'selectors'  => [
                $selector => '--arrow-h-offset: {{SIZE}}{{UNIT}};',
            ],
        ]);
    }


    protected function add_progress_content_controls()
    {
        $this->start_controls_section('progress_section', [
            'label' => esc_html__('Scroll Indicator', 'the7mk2'),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ]);
        $selector = '{{WRAPPER}} .nativeScroll';
        $arrow_options = [
            'never'  => esc_html__('Never', 'the7mk2'),
            'always' => esc_html__('Always', 'the7mk2'),
            'hover'  => esc_html__('On Hover', 'the7mk2'),
        ];
        $this->add_responsive_control('progress', [
            'label'                => esc_html__('Show Scroll Indicator:', 'the7mk2'),
            'type'                 => Controls_Manager::SELECT,
            'options'              => $arrow_options,
            'device_args'          => $this->generate_device_args(
                [
                    'default' => '',
                    'options' => ['' => esc_html__('Default', 'the7mk2')] + $arrow_options,
                ]
            ),
            'default'              => 'always',
            'frontend_available'   => true,
            'selectors'            => [
                $selector => '{{VALUE}}',
            ],
            'selectors_dictionary' => [
                'never'  => '--progress-display: none;',
                'always' => '--progress-display: inline-flex;--progress-opacity:1;',
                'hover'  => '--progress-display: inline-flex;--progress-opacity:0;',
            ],
        ]);

        $this->end_controls_section();
    }

    public function query_posts()
    {
        $query = $this->query_posts_for_alternate_templates();

        if ( ! $query) {
            //get normal query
            $query = $this->make_query();
        }

        $this->query = $query;

        return $query;
    }

    public function make_query($query_args = [])
    {
        $settings = $this->get_settings_for_display();
        if ($this->is_product_template()) {
            $args['posts_per_page'] = $settings['product_dis_posts_total'];
        } else {
            $args['posts_per_page'] = $settings['dis_posts_total'];
        }

        $this->posts_per_page = $args['posts_per_page'];

        $query_args = array_merge($args, $query_args);

        return $this->query($query_args);
    }

    /**
     * render content to display
     */
    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $this->alternate_template_before_render();
        $this->query_posts();
        $slides_count = $this->get_slides_count();
        if ( ! $slides_count) {
            $this->render_empty_view();
            $this->alternate_template_after_render();

            return;
        }

        ?>
        <div class="nativeScroll">
            <div tabindex="0" class="nsContent">
                <?php $this->render_posts(); ?>
            </div>
            <?php $this->render_arrows($settings); ?>
            <div class="nsProgressTrack">
                <div class="nsProgressIndicator"></div>
            </div>
        </div>
        <?php
        $this->alternate_template_after_render();
    }

    protected function render_post()
    {
        if ($this->has_alternate_templates()) {
            $this->render_post_alternate_templates();
        } else {
            $this->render_post_content($this->get_settings_for_display('template_id'));
        }
    }


    protected function render_arrows($settings)
    {
        ?>
        <div class="nsArrow nsLeftArrow elementor-icon" role="button" tabindex="0" aria-label="Prev slide">
            <?php Icons_Manager::render_icon($settings['arrow_prev'], ['aria-hidden' => 'true']); ?>
        </div>
        <div class="nsArrow nsRightArrow elementor-icon"  role="button" tabindex="0" aria-label="Next slide">
            <?php Icons_Manager::render_icon($settings['arrow_next'], ['aria-hidden' => 'true']); ?>
        </div>
        <?php
    }

    protected function get_slides_count()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings['template_id'])) {
            return 0;
        }

        return $this->get_query()->post_count;
    }

    /**
     * Render Empty View
     * Renders the widget's view if there is no posts to display
     */
    protected function render_empty_view()
    {
        if (Elementor::$instance->editor->is_edit_mode()) {
            if (the7_elementor_pro_is_active() && version_compare(ELEMENTOR_PRO_VERSION, '3.11.0', '>')) {
                //Will be filled with JS
                ?>
                <div class="e-loop-empty-view__wrapper"></div>
                <?php
            } else {
                ?>
                <div class="e-loop-empty-view__wrapper_old the7-slider-error-template">
                    <?php echo esc_html__('Either choose an existing template or create a new one and use it as the template in the slide.', 'the7mk2') ?>
                </div>
                <?php
            }
        }
    }

    public function skin_render_callback($attributes, $document)
    {
        if (LoopDocument::DOCUMENT_TYPE === $document::get_type()) {
            $attributes['class'] .= ' nsItem';
        }

        return $attributes;
    }
}
