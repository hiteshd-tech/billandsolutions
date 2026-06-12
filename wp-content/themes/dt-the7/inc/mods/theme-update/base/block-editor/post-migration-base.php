<?php
/**
 * Base class for post-based better block editor migrations within The7.
 */

namespace The7\Mods\Theme_Update\Base\Block_Editor;

use The7\Mods\Theme_Update\Base\Migration_Base;

defined( 'ABSPATH' ) || exit;

abstract class Post_Migration_Base extends Migration_Base {

	public static function migrate(): bool {
		$post_ids = static::get_post_ids_to_migrate();

		foreach ( $post_ids as $post_id ) {
			static::migrate_post( get_post( $post_id ) );
		}

		return false;
	}

	public static function migrate_post( $post ): void {
		$updated_post = static::process_post( clone $post );

		static::update_post( $post, $updated_post );
	}

	/**
	 * @return array<\WP_Post|object>
	 */
	protected static function get_post_ids_to_migrate(): array {
		global $wpdb;

		return $wpdb->get_col( "SELECT ID FROM {$wpdb->posts} WHERE post_type != 'attachment' AND post_content != ''" );
	}

	abstract protected static function process_post( $post );

	protected static function update_post( $original_post, $migrated_post ): void {
		$changes = [];

		foreach ( get_object_vars( $original_post ) as $key => $value ) {
			if ( $migrated_post->$key !== $value ) {
				$changes[ $key ] = $migrated_post->$key;
			}
		}

		if ( empty( $changes ) ) {
			return;
		}

		// Prevent revision creation.
		add_filter( 'wp_revisions_to_keep', '__return_zero', 999 );

		wp_update_post(
			array_merge(
				$changes,
				[
					'ID'           => $original_post->ID,
					'post_content' => wp_slash( $changes['post_content'] ?? $migrated_post->post_content ),
				]
			)
		);

		remove_filter( 'wp_revisions_to_keep', '__return_zero', 999 );
	}
}
