<?php
/**
 * Post Meta module.
 *
 * @package BbeProKit
 */

namespace BbeProKit\Modules\PostMeta;

use BbeProKit\Base\ModuleBasePro;
use BbeProKit\Modules\PostMeta\Rest\GetPostMetaKeysRoute;
use BbeProKit\Modules\PostMeta\Rest\GetPostMetaPreviewRoute;
use WP_Block;

defined( 'ABSPATH' ) || exit;

class Module extends ModuleBasePro {

	const MODULE_IDENTIFIER = 'post-meta';

	public function init() {
		parent::init();

		register_block_type(
			BBE_PRO_KIT_BLOCKS_DIR . 'post-meta',
			array(
				'render_callback' => array( $this, 'render_post_meta' ),
			)
		);
	}

	public function setup_hooks() {
		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
	}

	public function register_rest_routes() {
		$routes = array(
			new GetPostMetaKeysRoute(),
			new GetPostMetaPreviewRoute(),
		);

		foreach ( $routes as $route ) {
			$route->register();
		}
	}

	public function render_post_meta( array $attributes, string $content, WP_Block $block ): string {
		$meta_key        = isset( $attributes['metaKey'] ) ? sanitize_text_field( $attributes['metaKey'] ) : '';
		$post_id         = isset( $block->context['postId'] ) ? absint( $block->context['postId'] ) : 0;
		$mode            = isset( $attributes['mode'] ) && is_string( $attributes['mode'] ) ? $attributes['mode'] : 'text';
		$mode            = 'link' === $mode ? 'link' : 'text';
		$prefix          = isset( $attributes['prefix'] ) && is_string( $attributes['prefix'] ) ? $attributes['prefix'] : '';
		$suffix          = isset( $attributes['suffix'] ) && is_string( $attributes['suffix'] ) ? $attributes['suffix'] : '';
		$link_text       = isset( $attributes['linkText'] ) && is_string( $attributes['linkText'] ) ? $attributes['linkText'] : '';
		$open_in_new_tab = ! empty( $attributes['openInNewTab'] );

		if ( ! $this->is_allowed_meta_key( $post_id, $meta_key ) ) {
			return '';
		}

		$meta_value = get_post_meta( $post_id, $meta_key, true );

		// We intentionally output only plain strings.
		if ( ! is_string( $meta_value ) || '' === $meta_value ) {
			return '';
		}

		$prefix_markup  = $prefix ? sprintf( '<span class="wpbbe-post-meta-block__prefix">%s</span>', esc_html( $prefix ) ) : '';
		$suffix_markup  = $suffix ? sprintf( '<span class="wpbbe-post-meta-block__suffix">%s</span>', esc_html( $suffix ) ) : '';
		$content_markup = esc_html( $meta_value );

		if ( 'link' === $mode ) {
			$sanitized_url = esc_url( $meta_value );

			// Don't render invalid or empty URLs on the frontend.
			if ( '' === $sanitized_url ) {
				return '';
			}

			// Use custom link text if provided, otherwise use the meta value.
			$display_text = '' !== $link_text ? $link_text : $meta_value;

			$link_attrs = array(
				sprintf( 'href="%s"', esc_url( $sanitized_url ) ),
			);

			if ( $open_in_new_tab ) {
				$link_attrs[] = 'target="_blank"';
				$link_attrs[] = 'rel="noopener noreferrer"';
			}

			$content_markup = sprintf(
				'<a %1$s>%2$s</a>',
				implode( ' ', $link_attrs ),
				esc_html( $display_text )
			);
		}

		return sprintf(
			'<div %1$s>%2$s</div>',
			get_block_wrapper_attributes(),
			$prefix_markup . $content_markup . $suffix_markup
		);
	}

	private function is_allowed_meta_key( int $post_id, string $meta_key ): bool {
		if ( ! $meta_key || ! $post_id || is_protected_meta( $meta_key, 'post' ) ) {
			return false;
		}

		return current_user_can( 'read_post', $post_id );
	}
}
