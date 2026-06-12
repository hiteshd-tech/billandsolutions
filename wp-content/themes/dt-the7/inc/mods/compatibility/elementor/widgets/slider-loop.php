<?php

namespace The7\Mods\Compatibility\Elementor\Widgets;

use Elementor\Controls_Manager;
use ElementorPro\Modules\LoopBuilder\Documents\Loop as LoopDocument;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Loop_Query;
 use The7_Elementor_Compatibility;

class Slider_Loop extends Slider
{

    use Loop_Base_Trait;

    const WIDGET_NAME = 'the7-slider-loop';

    const AUTOPLAY_DEFAULT = 'no';
    const SLIDES_PER_VIEW_DEFAULT = '3';

    protected $_has_template_content = false;


    /**
     * @return string
     */
    public function get_name()
    {
        return self::WIDGET_NAME;
    }

    public function render()
    {
        $settings = $this->get_settings_for_display();
        if ( ! empty($settings['template_id'])) {

            if ($this->is_product_template()) {
                $query_args['posts_per_page'] = $settings['product_dis_posts_total'];
            } else {
                $query_args['posts_per_page'] = $settings['dis_posts_total'];
            }

            $this->query($query_args);
            $this->add_render_attribute('elementor_swiper_container', 'class', [
                'elementor-loop-container',
                'elementor-grid',
            ]);
        }
        parent::render();
    }


    protected function add_content_controls()
    {
        //'section_layout' name is important for createTemplate js function
        $this->start_controls_section('section_layout', [
            'label' => esc_html__('Loop Template', 'the7mk2'),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ]);
        $this->add_control('slider_wrap_helper', [
            'type'         => Controls_Manager::HIDDEN,
            'default'      => 'elementor-widget-the7-slider-common owl-carousel elementor-widget-loop-the7-slider',
            'prefix_class' => '',
        ]);

        $this->add_loop_content_controls();
        $this->add_slider_height_controls();

        $this->end_controls_section();
    }

    protected function add_slider_height_controls()
    {
        $this->add_control('equal_height', [
            'label'     => esc_html__('Equal height', 'the7mk2'),
            'type'      => Controls_Manager::SWITCHER,
            'label_off' => esc_html__('Off', 'the7mk2'),
            'label_on'  => esc_html__('On', 'the7mk2'),
            'default'   => 'yes',
            'condition' => [
                'dis_posts_total!' => 1,
                'template_id!'     => '',
            ],
            'selectors' => [
                '{{WRAPPER}} .e-loop-item > .elementor-section,
					 {{WRAPPER}} .e-loop-item > .elementor-section > .elementor-container,
					 {{WRAPPER}} .e-loop-item > .e-con,
					 {{WRAPPER}} .e-loop-item .elementor-section-wrap  > .e-con, 
                     {{WRAPPER}} .the7-swiper-slide' => 'height: 100%',
            ],
        ]);
    }


    /**
     * @return string|void
     */
    protected function the7_title()
    {
        return esc_html__('Loop Slider', 'the7mk2');
    }

    /**
     * @return string[]
     */
    protected function the7_keywords()
    {
        return ['slides', 'carousel', 'image', 'slider', 'loop', 'custom post type', 'carousel'];
    }


    protected function render_slides()
    {
        $this->add_render_attribute('swiper_slide_inner_wrapper', 'class', 'the7-swiper-slide-inner');
        $this->render_posts();
    }


    protected function render_post_content($template_id)
    {
        $loop_item_id = get_the_ID();


        /** @var LoopDocument $document */
        $document = \Elementor\Plugin::$instance->documents->get($template_id);

        // Bail if document is not an instance of LoopDocument.
        if ( ! $document instanceof LoopDocument) {
            return;
        }

        $this->remove_render_attribute('swiper_slide_wrapper');
        $this->add_render_attribute('swiper_slide_wrapper', 'class', [
            'post-id-' . $loop_item_id,
            'the7-swiper-slide',
        ]);

        ?>
        <div <?php echo $this->get_render_attribute_string('swiper_slide_wrapper'); ?>>
            <div <?php echo $this->get_render_attribute_string('swiper_slide_inner_wrapper'); ?>>
                <?php
                $this->print_dynamic_css($loop_item_id, $template_id);

                $this->before_skin_render();

                The7_Elementor_Compatibility::instance()->print_loop_document($document);
                $this->after_skin_render();
                ?>
            </div>
        </div>
        <?php
    }

    public function skin_render_callback($attributes, $document)
    {
        if (LoopDocument::DOCUMENT_TYPE === $document::get_type()) {
            $attributes['class'] .= ' the7-slide-content';
        }

        return $attributes;
    }

    protected function get_slides_count()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings['template_id'])) {
            return 0;
        }

        return $this->get_query()->post_count;
    }
}
