<?php

namespace The7\Mods\Compatibility\Elementor\Modules\AJAX_Pagination;

use Elementor\Plugin as Elementor;
use Elementor\Utils;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Module_Base;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Module extends The7_Elementor_Module_Base
{
    const WIDGET_CONTENT_PARAM = 'the7-widget-content';
    const WIDGET_POST_ID_PARAM = 'the7-widget-post-id';
    const WIDGET_PAGE_ID_PARAM = 'the7-page-';
    protected $widget_id = '';

    public function __construct()
    {
        add_filter('template_include', [$this, 'template_include'], 30);
        add_action('presscore_render_e_widget', [$this, 'render_widget_template']);
    }

    public function template_include($template)
    {
        if ($this->get_val($_GET, self::WIDGET_CONTENT_PARAM)) {
            $template = locate_template('inc/mods/compatibility/elementor/page-templates/elementor-widget.php', false, false);
        }

        return $template;
    }

    function get_val($arr, $key)
    {
        if ( ! isset($arr[ $key ])) {
            return null;
        }

        return wp_kses_post_deep(wp_unslash($arr[ $key ]));
    }

    public function render_widget_template()
    {
        $widget_id = $this->get_val($_GET, self::WIDGET_CONTENT_PARAM);
        if ($widget_id) {
            $post_id = $this->get_val($_GET, self::WIDGET_POST_ID_PARAM);
            $post_id = is_numeric($post_id) ? $post_id: get_the_ID();

            if (get_post_status($post_id) === 'private' && ! current_user_can('read_private_pages')) {
                return;
            }
            $paged = $this->get_val($_GET, self::WIDGET_PAGE_ID_PARAM . $widget_id);
            $paged = is_numeric($paged) ? $paged: 0;
            if ($paged) {
                set_query_var('paged', $paged);
            }

            $_SERVER['REQUEST_URI'] = remove_query_arg(self::WIDGET_CONTENT_PARAM);
            $_SERVER['REQUEST_URI'] = remove_query_arg(self::WIDGET_POST_ID_PARAM);

            // Make the post as global post for dynamic values.
            Elementor::instance()->db->switch_to_post($post_id);
            $this->widget_id = $widget_id;
            echo $this->render_widget($post_id);
        }
    }

    public function render_widget($post_id)
    {
        add_filter('elementor/frontend/builder_content_data', [$this, 'replace_widget_data'], 10, 2);

        $content = Elementor::instance()->frontend->get_builder_content($post_id, false);

        remove_filter('elementor/frontend/builder_content_data', [$this, 'replace_widget_data']);

        return $content;
    }

    public function replace_widget_data($data, $post_id)
    {
        $posts_widget = Utils::find_element_recursive($data, $this->widget_id);

        if ( ! empty($posts_widget)) {
            $this->widget_id = '';
            $data = [];
            $data[] = $posts_widget;
            remove_filter('elementor/frontend/builder_content_data', [$this, 'replace_widget_data']);
        }

        return $data;
    }

    /**
     * Get module name.
     * Retrieve the module name.
     * @access public
     * @return string Module name.
     */
    public function get_name()
    {
        return 'ajax_pagination';
    }
}
