<?php
/**
 * Post meta keys REST route.
 *
 * @package BbeProKit
 */

namespace BbeProKit\Modules\PostMeta\Rest;

use WP_Error;
use WP_REST_Request;
use WP_REST_Server;

defined( 'ABSPATH' ) || exit;

class GetPostMetaKeysRoute {

	const REST_ROUTE = '/post-meta-keys';

	public function register() {
		register_rest_route(
			BBE_PRO_KIT_REST_BASE,
			self::REST_ROUTE,
			array(
				'methods'             => WP_REST_Server::READABLE,
				'permission_callback' => array( $this, 'permission_callback' ),
				'callback'            => array( $this, 'handle_request' ),
				'args'                => array(
					'postType' => array(
						'required'          => true,
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_key',
					),
					'search'   => array(
						'required'          => false,
						'type'              => 'string',
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
					),
					'limit'    => array(
						'required'          => false,
						'type'              => 'integer',
						'default'           => 20,
						'sanitize_callback' => 'absint',
					),
				),
			)
		);
	}

	public function permission_callback( WP_REST_Request $request ) {
		$post_type = $request->get_param( 'postType' );

		if ( ! $post_type || ! post_type_exists( $post_type ) ) {
			return new WP_Error(
				'wpbbe_pro_invalid_post_type',
				__( 'Invalid post type.', 'better-block-editor' ),
				array( 'status' => 400 )
			);
		}

		$post_type_object = get_post_type_object( $post_type );
		if (
			! isset( $post_type_object->cap->edit_posts )
			|| ! current_user_can( $post_type_object->cap->edit_posts )
		) {
			// Mask it for unauthorized users, as the existence of the post type itself can be considered sensitive information.
			return new WP_Error(
				'wpbbe_pro_invalid_post_type',
				__( 'Invalid post type.', 'better-block-editor' ),
				array( 'status' => 400 )
			);
		}

		return true;
	}

	public function handle_request( WP_REST_Request $request ) {
		$post_type = $request->get_param( 'postType' );
		$search    = $request->get_param( 'search' );
		$limit     = $request->get_param( 'limit' );

		if ( $limit < 1 ) {
			$limit = 20;
		}
		if ( $limit > 50 ) {
			$limit = 50;
		}

		$meta_keys = $this->query_meta_keys_for_post_type( $post_type, $search, $limit );

		return rest_ensure_response(
			array(
				'keys' => $meta_keys,
			)
		);
	}

	private function query_meta_keys_for_post_type( string $post_type, string $search, int $limit ): array {
		global $wpdb;

		$search = trim( $search );

		$cache_key = '';
		if ( ! $search ) {
			$cache_key = sprintf(
				'post_meta_keys:%s:%d',
				$post_type,
				$limit
			);
			$cached_keys = wp_cache_get( $cache_key, 'wpbbe-pro-kit' );
			if ( is_array( $cached_keys ) ) {
				return $cached_keys;
			}
		}

		$where  = array(
			'p.post_type = %s',
			"p.post_status NOT IN ('auto-draft', 'trash')",
			'pm.meta_key NOT LIKE %s',
		);
		$params = array( $post_type, '\\_%' );

		if ( $search ) {
			$where[]  = 'pm.meta_key LIKE %s';
			$params[] = '%' . $wpdb->esc_like( $search ) . '%';
		}

		$params[] = $limit;

		$sql = "
			SELECT DISTINCT pm.meta_key
			FROM {$wpdb->postmeta} AS pm
			INNER JOIN {$wpdb->posts} AS p ON p.ID = pm.post_id
			WHERE " . implode( ' AND ', $where ) . '
			ORDER BY pm.meta_key ASC
			LIMIT %d
		';

		$prepared_sql = $wpdb->prepare( $sql, $params );
		$keys         = $wpdb->get_col( $prepared_sql );

		if ( ! is_array( $keys ) ) {
			return array();
		}

		$keys = array_values( array_filter( $keys ) );

		if ( $cache_key ) {
			// Cache empty search results because they are common and expensive to compute.
			wp_cache_set( $cache_key, $keys, 'wpbbe-pro-kit', 5 * MINUTE_IN_SECONDS );
		}

		return $keys;
	}
}
