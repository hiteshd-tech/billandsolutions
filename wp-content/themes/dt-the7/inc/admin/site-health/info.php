<?php
/**
 * This file contains additional system information blocks.
 *
 * @package The7
 */

defined( 'ABSPATH' ) || exit;

/**
 * Theme debug information.
 */
add_filter(
	'debug_information',
	static function ( $info ) {
		$fields = [];

		// DB version.
		$current_db_version   = The7_Install::get_db_version();
		$fields['db_version'] = [
			'label' => esc_html__( 'DB Version', 'the7mk2' ),
			'value' => esc_html( $current_db_version ),
			'debug' => esc_html( $current_db_version ),
		];
		if ( version_compare( $current_db_version, PRESSCORE_DB_VERSION, '<' ) ) {
			/* translators: 1: current db version, 2: max db version, */
			$fields['db_version']['value'] = esc_html( sprintf( __( '%1$s, can be upgraded to %2$s', 'the7mk2' ), $current_db_version, PRESSCORE_DB_VERSION ) );
		}

		// Server availability.
		$fields['server_availability'] = [
			'label' => esc_html__( 'The7 Remote Server', 'the7mk2' ),
		];

		$the7_server_code = wp_remote_retrieve_response_code( wp_safe_remote_get( 'https://repo.the7.io/theme/info.json', [ 'decompress' => false ] ) );
		if ( $the7_server_code >= 200 && $the7_server_code < 300 ) {
			$fields['server_availability']['value'] = esc_html__( 'Accessible', 'the7mk2' );
		} else {
			$fields['server_availability']['value'] = esc_html(
				sprintf(
				// translators: %s - remote server url.
					__( 'Service is temporary unavailable. Please check back later. If the issue persists, contact your hosting provider and make sure that %s is not blocked.', 'the7mk2' ),
					'https://repo.the7.io/'
				)
			);
			$fields['server_availability']['debug'] = 'Inaccessible';
		}

		// Ajax calls.
		$fields['ajax_calls'] = [
			'label' => esc_html__( 'Ajax calls with wp_remote_post', 'the7mk2' ),
		];

		$ajax_url         = esc_url_raw( admin_url( 'admin-ajax.php' ) );
		$the7_server_code = wp_remote_retrieve_response_code( wp_remote_post( $ajax_url, [ 'decompress' => false ] ) );
		if ( $the7_server_code === 400 ) {
			$fields['ajax_calls']['value'] = esc_html__( 'Accessible', 'the7mk2' );
		} else {
			$fields['ajax_calls']['value'] = esc_html(
				sprintf(
					// translators: %1$s - response code, %2$s - url.
					__(
						'Seems that your server is blocking connections to your own site (responded with %1$s code). It may break theme db update process and lead to style corruption. Please, make sure that remote requests to %2$s are not blocked.',
						'the7mk2'
					),
					$the7_server_code,
					$ajax_url
				)
			);
			$fields['ajax_calls']['debug'] = esc_html( "Inaccessible, response code: {$the7_server_code}" );
		}

		// Final info section.
		$info = [
			'dt-the7-section' => [
				'label'  => esc_html__( 'The7 Theme', 'the7mk2' ),
				'fields' => $fields,
			],
		] + $info;

		return $info;
	},
	20
);

/**
 * The7 Elements debug information.
 */
add_filter(
	'debug_information',
	static function ( $info ) {
		if ( ! dt_the7_core_is_enabled() ) {
			return $info;
		}

		$fields = [];

		// DB version.
		$fields['db_version'] = [
			'label' => esc_html__( 'DB Version', 'the7mk2' ),
			'value' => esc_html__( 'Unknown', 'the7mk2' ),
		];

		if ( class_exists( 'The7PT_Install' ) && class_exists( 'The7PT_Core' ) ) {
			if ( The7PT_Install::db_update_is_needed() ) {
				/* translators: 1: current the7 core db version, 2: max the7 core db version, */
				$fields['db_version']['value'] = esc_html( sprintf( __( '%1$s, can be upgraded to %2$s', 'the7mk2' ), The7PT_Install::get_db_version(), The7PT_Core::PLUGIN_DB_VERSION ) );
				$fields['db_version']['debug'] = esc_html( The7PT_Install::get_db_version() );
			} else {
				$fields['db_version']['value'] = esc_html( The7PT_Install::get_db_version() );
			}
		}

		$info = [
			'dt-the7-core-section' => [
				'label'  => esc_html__( 'The7 Elements', 'the7mk2' ),
				'fields' => $fields,
			],
		] + $info;

		return $info;
	}
);
