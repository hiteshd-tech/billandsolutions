<?php
/**
 * @package The7
 */

defined( 'ABSPATH' ) || exit;

function the7_elementor_elements_widget_post_types( $exclude = null ) {
	if ( ! $exclude ) {
		$exclude = [
			'page',
			'product',
		];
	}

	$post_types = array_diff_key( get_post_types( [ 'show_in_nav_menus' => true ], 'object' ), array_fill_keys( $exclude, '' ) );

	$supported_post_types = [];
	foreach ( $post_types as $post_type ) {
		$supported_post_types[ $post_type->name ] = $post_type->label;
	}

	$supported_post_types['current_query'] = __( 'Archive (current query)', 'the7mk2' );

	return $supported_post_types;
}

function the7_get_public_post_types( $args = [] ) {
	$post_type_args = [
		// Default is the value $public.
		'show_in_nav_menus' => true,
		'public'            => true,
	];

	// Keep for backwards compatibility
	if ( ! empty( $args['post_type'] ) ) {
		$post_type_args['name'] = $args['post_type'];
		unset( $args['post_type'] );
	}

	$post_type_args = wp_parse_args( $post_type_args, $args );

	$_post_types = get_post_types( $post_type_args, 'objects' );

	$post_types = [];

	foreach ( $_post_types as $post_type => $object ) {
		$post_types[ $post_type ] = $object->label;
	}

	// Exclude Elementor `Landing page` post type.
	unset( $post_types['e-landing-page'] );

	/**
	 * Public Post types
	 *
	 * Allow 3rd party plugins to filters the public post types the7 widgets should work on
	 *
	 * @param array $post_types The7 widgets supported public post types.
	 */
	return apply_filters( 'the7_get_public_post_types', $post_types );
}

function the7_get_taxonomies( $args = [], $output = 'names', $operator = 'and' ) {
	global $wp_taxonomies;

	$field = ( 'names' === $output ) ? 'name' : false;

	// Handle 'object_type' separately.
	if ( isset( $args['object_type'] ) ) {
		$object_type = (array) $args['object_type'];
		unset( $args['object_type'] );
	}

	$taxonomies = wp_filter_object_list( $wp_taxonomies, $args, $operator );

	if ( isset( $object_type ) ) {
		foreach ( $taxonomies as $tax => $tax_data ) {
			if ( ! array_intersect( $object_type, $tax_data->object_type ) ) {
				unset( $taxonomies[ $tax ] );
			}
		}
	}

	if ( $field ) {
		$taxonomies = wp_list_pluck( $taxonomies, $field );
	}

	return $taxonomies;
}

/**
 * @return string
 */
function the7_elementor_get_message_about_disabled_post_type() {
	return '<p>' . esc_html__( 'The corresponding post type is disabled. Please make sure to 1) install The7 Elements plugin under The7 > Plugins and 2) enable desired post types under The7 > My The7, in the Settings section.', 'the7mk2' ) . '</p>';
}

/**
 * Return Elementor content width as a string.
 *
 * @return string
 */
function the7_elementor_get_content_width_string() {
	$content_width = \The7_Elementor_Compatibility::get_elementor_settings( 'container_width' );
	if ( isset( $content_width['size'], $content_width['unit'] ) ) {
		$size = $content_width['size'] ?: 1140;
		$unit = $content_width['unit'] ?: 'px';

		return $size . $unit;
	}

	return (string) ( $content_width ?: '1140px' );
}

/**
 * Return description string for the wide columns control in widgets.
 *
 * @since 9.15.0
 *
 * @return string
 */
function the7_elementor_get_wide_columns_control_description() {
	// translators: %s: elementor content width.
	$description = esc_html__( 'Leave empty to use %s (value of "Content Width" from Elementor setting).', 'the7mk2' );

	return sprintf( $description, the7_elementor_get_content_width_string() );
}

/**
 * @since 9.4.0
 *
 * @return bool
 */
function the7_is_elementor_schemes_disabled() {
	$custom_colors_disabled      = get_option( 'elementor_disable_color_schemes' );
	$typography_schemes_disabled = get_option( 'elementor_disable_typography_schemes' );

	return the7_is_elementor3() || ( $custom_colors_disabled && $typography_schemes_disabled );
}

/**
 * @param  array $widget_names Widget names.
 *
 * @return array
 */
function the7_find_posts_with_elementor_widgets( array $widget_names ) {
	global $wpdb;

	$query_meta_value_equasion = [];

	foreach ( $widget_names as $widget_name ) {
		$query_meta_value_equasion[] = '`meta_value` LIKE \'%"widgetType":"' . $widget_name . '"%\'';
	}

	$query = 'SELECT `post_id` 
		FROM `' . $wpdb->postmeta . '` 
		WHERE `meta_key` = "_elementor_data" 
		AND (' . implode( ' OR ', $query_meta_value_equasion ) . ');';

	return $wpdb->get_col( $query );
}

/**
 * @return array
 */
function the7_get_deprecated_elementor_breakpoints() {
	$default_breakpoints       = [
		'xs'  => 0,
		'sm'  => 480,
		'md'  => 768,
		'lg'  => 1024,
		'xl'  => 1440,
		'xxl' => 1600,
	];
	$editable_breakpoints_keys = [
		'md',
		'lg',
	];

	return array_reduce(
		array_keys( $default_breakpoints ),
		function ( $new_array, $breakpoint_key ) use ( $default_breakpoints, $editable_breakpoints_keys ) {
			if ( ! in_array( $breakpoint_key, $editable_breakpoints_keys, true ) ) {
				$new_array[ $breakpoint_key ] = $default_breakpoints[ $breakpoint_key ];
			} else {
				$saved_option = \Elementor\Plugin::$instance->kits_manager->get_current_settings( 'viewport_' . $breakpoint_key );

				$new_array[ $breakpoint_key ] = $saved_option ? (int) $saved_option : $default_breakpoints[ $breakpoint_key ];
			}

			return $new_array;
		},
		[]
	);
}
