<?php
/**
 * The7 Elements site health tests.
 *
 * @package The7Elements\Admin
 */

defined( 'ABSPATH' ) || exit;

/**
 * Add the7 Elements tests.
 *
 * @param array $tests Tests array.
 *
 * @return array
 */
function the7pt_add_site_health_tests( $tests ) {
	$tests['direct']['the7_unused_elements'] = array(
		'label' => __( 'The7 Elements unused post types', 'dt-the7-core' ),
		'test'  => 'the7pt_site_health_unused_post_types_test',
	);

	$tests['direct']['the7_orphaned_posts'] = array(
		'label' => __( 'The7 Elements orphaned posts', 'dt-the7-core' ),
		'test'  => 'the7pt_site_health_orphaned_posts',
	);

	return $tests;
}

add_filter( 'site_status_tests', 'the7pt_add_site_health_tests' );

/**
 * Tests for unused post types.
 *
 * @return array
 */
function the7pt_site_health_unused_post_types_test() {
	global $wpdb;

	$result = array(
		'label'       => __( 'All active The7 Elements post types are in use', 'dt-the7-core' ),
		'status'      => 'good',
		'badge'       => array(
			'label' => __( 'Performance', 'dt-the7-core' ),
			'color' => 'blue',
		),
		'description' => sprintf(
			'<p>%s</p>',
			__( 'Disabling unused post types increase overall installation performance.', 'dt-the7-core' )
		),
		'actions'     => '',
		'test'        => 'the7pt_site_health_unused_post_types_test',
	);

	if ( ! class_exists( 'The7_Admin_Dashboard_Settings' ) ) {
		return $result;
	}

	$unused_post_types = array();
	$query             = "SELECT post_type, COUNT(*) AS num_posts FROM {$wpdb->posts} GROUP BY post_type";
	$posts_count       = (array) $wpdb->get_results( $query, ARRAY_A );
	$posts_count       = wp_list_pluck( $posts_count, 'num_posts', 'post_type' );

	$modules_to_post_types = array(
		'albums'       => 'dt_gallery',
		'portfolio'    => 'dt_portfolio',
		'benefits'     => 'dt_benefits',
		'logos'        => 'dt_logos',
		'team'         => 'dt_team',
		'testimonials' => 'dt_testimonials',
		'slideshow'    => 'dt_slideshow',
	);
	foreach ( $modules_to_post_types as $module => $post_type ) {
		if ( empty( $posts_count[ $post_type ] ) && The7_Admin_Dashboard_Settings::get( $module ) ) {
			$post_type_object = get_post_type_object( $post_type );
			if ( $post_type_object ) {
				$unused_post_types[] = '<li>' . $post_type_object->labels->name . '</li>';
			}
		}
	}

	if ( $unused_post_types ) {
		$unused_post_types = '<br><ol>' . implode( '', $unused_post_types ) . '</ol><br>';

		$result['status']         = 'recommended';
		$result['label']          = __( 'Some of the The7 Elements post types can be disabled', 'dt-the7-core' );
		$result['badge']['color'] = 'blue';
		$result['actions']        = sprintf(
			'<a href="%s">%s</a>',
			esc_url( admin_url( 'admin.php?page=the7-dashboard' ) ),
			__( 'Manage The7 Post Types and Elements', 'dt-the7-core' )
		);
		$result['description']    = sprintf(
			'<p>%s</p>',
			sprintf(
				// translators: $s - remote server url.
				__(
					'Following post types are not used:%sIt is recommended to disable unused post types to increase overall installation performance.',
					'dt-the7-core'
				),
				$unused_post_types
			)
		);
	}

	return $result;
}

/**
 * @return array
 */
function the7pt_site_health_orphaned_posts() {
	global $wpdb;

	$result = [
		'label'       => __( 'You have no posts from disabled post types', 'dt-the7-core' ),
		'status'      => 'good',
		'badge'       => [
			'label' => __( 'Performance', 'dt-the7-core' ),
			'color' => 'blue',
		],
		'description' => '<p>' . __( 'Posts from disabled post types could be the result of a configuration error.', 'dt-the7-core' ) . '</p>',
		'actions'     => '',
	];

	$query       = "SELECT post_type, COUNT(*) AS num_posts FROM {$wpdb->posts} GROUP BY post_type";
	$posts_in_db = (array) $wpdb->get_results( $query, ARRAY_A );
	$posts_in_db = wp_list_pluck( $posts_in_db, 'num_posts', 'post_type' );
	$post_types  = array_merge( get_post_types( [ 'public' => true ] ), get_post_types( [ 'public' => false ] ) );

	$orphaned_posts = array_diff_key( $posts_in_db, $post_types );

	if ( $orphaned_posts ) {
		$bundled_post_types       = \The7_Core\Mods\Post_Type_Builder\Models\Post_Types::get_bundle_definition();
		$orphaned_posts_list_html = '';

		foreach ( $orphaned_posts as $post_type => $count ) {
			$action = \The7_Core\Mods\Post_Type_Builder\Admin_Page::ACTION_QUICK_ADD;
			if ( array_key_exists( $post_type, $bundled_post_types ) ) {
				$action = \The7_Core\Mods\Post_Type_Builder\Admin_Page::ACTION_ACTIVATE;
			}

			$link = \The7_Core\Mods\Post_Type_Builder\Handlers\Post_Types_Handler::nonce_url(
				\The7_Core\Mods\Post_Type_Builder\Admin_Page::get_post_type_link( $action, $post_type )
			);

			$orphaned_posts_list_html .= '<tr>';
			$orphaned_posts_list_html .= '<td>' . $post_type . '</td>';
			$orphaned_posts_list_html .= '<td>' . $count . '</td>';
			$orphaned_posts_list_html .= '<td>' . ' <a href="' . esc_url( $link ) . '" target="_blank">Restore</a>' . '</td>';
			$orphaned_posts_list_html .= '</tr>';
		}

		$head = '<thead><tr><th>Post Type</th><th>Posts Count</th><th>Action</th></tr></thead>';
		$orphaned_posts_list_html = '<table class="wp-list-table widefat fixed striped table-view-list">' . $head . '<tbody>' . $orphaned_posts_list_html . '</tbody></table><br>';

		$result = [
			'label'       => __( 'You have some posts from disabled post types', 'dt-the7-core' ),
			'status'      => 'recommended',
			'badge'       => [
				'label' => __( 'Performance', 'dt-the7-core' ),
				'color' => 'blue',
			],
			'description' => '<p>' . __( 'You may want to review and delete posts of this post types', 'dt-the7-core' ) . ':</p>' . $orphaned_posts_list_html,
			'actions'     => '',
		];
	}

	$result['test'] = 'the7pt_site_health_orphaned_posts_and_terms';

	return $result;
}
