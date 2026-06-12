<?php
/**
 * The7 site health tests.
 *
 * @since 7.6.1
 *
 * @package The7\Admin
 */

defined( 'ABSPATH' ) || exit;

/**
 * Filter that add The7 site health tests.
 *
 * @since 7.6.1
 *
 * @param array $tests Tests.
 *
 * @return array
 */
function the7_add_site_health_tests( $tests ) {
	$async = &$tests['async'];

	$async['the7_server'] = [
		'label' => esc_html__( 'The7 remote content server availability', 'the7mk2' ),
		'test'  => 'the7_site_health_server_availability_test',
	];

	if ( the7_is_gutenberg_theme_mode_active() ) {
		$async['the7_local_server_supported_request_methods'] = [
			'label' => esc_html__('Local server supported request methods', 'the7mk2'),
			'test' => 'the7_site_health_local_server_supported_request_methods_test',
		];
	}

	return $tests;
}

add_filter( 'site_status_tests', 'the7_add_site_health_tests' );

/**
 * Ajax handler for The7 remote server test.
 *
 * @since 7.6.1
 */
function the7_site_health_server_availability_test() {
	check_ajax_referer( 'health-check-site-status' );

	if ( ! current_user_can( 'view_site_health_checks' ) ) {
		wp_send_json_error();
	}

	$result = array(
		'label'       => __( 'The7 remote content server is available', 'the7mk2' ),
		'status'      => 'good',
		'badge'       => array(
			'label' => __( 'The7', 'the7mk2' ),
			'color' => 'blue',
		),
		'description' => sprintf(
			'<p>%s</p>',
			__( 'Access to The7 remote server allow to auto update theme, bundled plugins and install demo content.', 'the7mk2' )
		),
		'actions'     => '',
		'test'        => 'the7_site_health_server_availability_test',
	);

	$the7_server_code = wp_remote_retrieve_response_code( wp_safe_remote_get( 'https://repo.the7.io/theme/info.json', array( 'decompress' => false ) ) );
	if ( $the7_server_code < 200 || $the7_server_code >= 300 ) {
		$result['status']         = 'recommended';
		$result['label']          = __( 'The7 remote content server is not available', 'the7mk2' );
		$result['badge']['color'] = 'blue';
		$result['description']    = sprintf(
			'<p>%s</p>',
			sprintf(
				// translators: $s - remote server url.
				__(
					'Service is temporary unavailable. Theme update, installation and update of bundled plugins and demo content are not available. Please check back later.
If the issue persists, contact your hosting provider and make sure that %s is not blocked.',
					'the7mk2'
				),
				'https://repo.the7.io/'
			)
		);
	}

	wp_send_json_success( $result );
}

add_action( 'wp_ajax_health-check-the7-site_health_server_availability_test', 'the7_site_health_server_availability_test' );
add_action( 'wp_ajax_health-check-the7_site_health_server_availability_test', 'the7_site_health_server_availability_test' );
add_action( 'wp_ajax_the7_site_health_server_availability_test', 'the7_site_health_server_availability_test' );

/**
 * Test if local server supports OPTIONS request method. Important for Full Site Editing.
 *
 * @return void
 */
function the7_site_health_local_server_supported_request_methods_test() {
	check_ajax_referer( 'health-check-site-status' );

	if ( ! current_user_can( 'view_site_health_checks' ) ) {
		wp_send_json_error();
	}

	$result = [
		'label'       => __( 'Site server supports OPTIONS request methods', 'the7mk2' ),
		'status'      => 'good',
		'badge'       => [
			'label' => __( 'Full Site Editing' ),
			'color' => 'blue',
		],
		'description' => sprintf(
			'<p>%s</p>',
			__( 'In order Full Site Editing to work properly, the site server should support OPTIONS request method.', 'the7mk2' )
		),
		'actions'     => '',
		'test'        => 'the7_site_health_local_server_supported_request_methods_test',
	];

	$local_server_response_code = wp_remote_retrieve_response_code(
		wp_safe_remote_request(
			rest_url( 'wp/v2/settings' ),
			[
				'method' => 'OPTIONS',
				'timeout' => 5,
				'decompress' => false,
			]
		)
	);

	if ( $local_server_response_code !== 200 ) {
		$result['status']         = 'critical';
		$result['label']          = __( 'Site server does not support OPTIONS request method', 'the7mk2' );
		$result['description']    = sprintf(
			'<p>%s</p>',
			__(
				'Full Site Editing may be disrupted due to lack of OPTIONS request method support by the site server. Please contact your hosting provider to enable this method if you experience any issues.',
				'the7mk2'
			)
		);
	}

	wp_send_json_success( $result );
}

if ( the7_is_gutenberg_theme_mode_active() ) {
	add_action(
		'wp_ajax_health-check-the7-site_health_local_server_supported_request_methods_test',
		'the7_site_health_local_server_supported_request_methods_test'
	);
	add_action(
		'wp_ajax_health-check-the7_site_health_local_server_supported_request_methods_test',
		'the7_site_health_local_server_supported_request_methods_test'
	);
	add_action(
		'wp_ajax_the7_site_health_local_server_supported_request_methods_test',
		'the7_site_health_local_server_supported_request_methods_test'
	);
}
