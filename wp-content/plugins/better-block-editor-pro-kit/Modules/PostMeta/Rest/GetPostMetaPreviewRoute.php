<?php
/**
 * Post meta preview REST route.
 *
 * @package BbeProKit
 */

namespace BbeProKit\Modules\PostMeta\Rest;

use WP_Error;
use WP_REST_Request;
use WP_REST_Server;

defined( 'ABSPATH' ) || exit;

class GetPostMetaPreviewRoute {

	const REST_ROUTE = '/post-meta-preview';

	public function register() {
		register_rest_route(
			BBE_PRO_KIT_REST_BASE,
			self::REST_ROUTE,
			array(
				'methods'             => WP_REST_Server::READABLE,
				'permission_callback' => array( $this, 'permission_callback' ),
				'callback'            => array( $this, 'handle_request' ),
				'args'                => array(
					'postId'  => array(
						'required'          => true,
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
					),
					'metaKey' => array(
						'required'          => true,
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
			)
		);
	}

	public function permission_callback( WP_REST_Request $request ) {
		$meta_key = $request->get_param( 'metaKey' );
		if ( ! $meta_key || is_protected_meta( $meta_key, 'post' ) ) {
			return new WP_Error(
				'wpbbe_pro_invalid_meta_key',
				__( 'Invalid meta key.', 'better-block-editor' ),
				array( 'status' => 400 )
			);
		}

		$post_id = $request->get_param( 'postId' );
		if ( $post_id < 1 ) {
			return new WP_Error(
				'wpbbe_pro_invalid_post_id',
				__( 'Invalid post ID.', 'better-block-editor' ),
				array( 'status' => 400 )
			);
		}

		if (
			! current_user_can( 'edit_post', $post_id )
			|| in_array( get_post_status( $post_id ), array( 'auto-draft', 'trash' ), true )
		) {
			return new WP_Error(
				'wpbbe_pro_forbidden_post_meta_preview',
				__( 'You are not allowed to preview post meta for this post.', 'better-block-editor' ),
				array( 'status' => 403 )
			);
		}

		return true;
	}

	public function handle_request( WP_REST_Request $request ) {
		$post_id  = $request->get_param( 'postId' );
		$meta_key = $request->get_param( 'metaKey' );

		$meta_value = get_post_meta( $post_id, $meta_key, true );
		$meta_value = is_string( $meta_value ) ? $meta_value : '';

		return rest_ensure_response(
			array(
				'preview_value' => esc_html( $meta_value ),
			)
		);
	}
}
