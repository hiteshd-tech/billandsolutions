<?php

function dt_the7_child_enqueue_styles() {
    $parent_style = 'dt-the7-style';

    // Parent theme style.
    wp_enqueue_style(
        $parent_style,
        get_template_directory_uri() . '/style.css',
        array(),
        wp_get_theme( 'dt-the7' )->get( 'Version' )
    );

    // Child theme style.
    wp_enqueue_style(
        'dt-the7-child',
        get_stylesheet_directory_uri() . '/style.css',
        array( $parent_style ),
        wp_get_theme()->get( 'Version' )
    );
}
add_action( 'wp_enqueue_scripts', 'dt_the7_child_enqueue_styles', 20 );

function create_services_post_type() {

    $labels = array(
        'name'               => 'Services',
        'singular_name'      => 'Service',
        'menu_name'          => 'Services',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New Service',
        'edit_item'          => 'Edit Service',
        'new_item'           => 'New Service',
        'view_item'          => 'View Service',
        'all_items'          => 'All Services',
        'search_items'       => 'Search Services',
        'not_found'          => 'No services found',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'menu_icon'          => 'dashicons-admin-tools',
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt'),
        'has_archive'        => true,
        'rewrite'            => array('slug' => 'services'),
        'show_in_rest'       => true
    );

    register_post_type('services', $args);
}

add_action('init', 'create_services_post_type');

/*function remove_service_slug() {
    add_rewrite_rule(
        '^([^/]+)/?$',
        'index.php?services=$matches[1]',
        'top'
    );
}
add_action('init', 'remove_service_slug');


function service_custom_permalink($post_link, $post) {
    if ($post->post_type == 'services' && $post->post_status == 'publish') {
        return home_url('/' . $post->post_name . '/');
    }
    return $post_link;
}
add_filter('post_type_link', 'service_custom_permalink', 10, 2);*/

function remove_service_slug_fix() {
    add_rewrite_rule(
        '^service/([^/]+)/?$',
        'index.php?services=$matches[1]',
        'top'
    );
}
add_action('init', 'remove_service_slug_fix');

function custom_service_query($query) {
    if (!is_admin() && $query->is_main_query() && isset($query->query['name']) && !isset($query->query['post_type'])) {

        $slug = $query->query['name'];

        // Check if it's a service
        $post = get_page_by_path($slug, OBJECT, 'services');

        if ($post) {
            $query->set('post_type', 'services');
            $query->set('name', $slug);
        }
    }
}
add_action('pre_get_posts', 'custom_service_query');


function service_custom_permalink($post_link, $post) {
    if ($post->post_type == 'services') {
        return home_url('/' . $post->post_name . '/');
    }
    return $post_link;
}
add_filter('post_type_link', 'service_custom_permalink', 10, 2);