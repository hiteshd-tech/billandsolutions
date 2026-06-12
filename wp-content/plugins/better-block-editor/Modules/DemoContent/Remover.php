<?php
/**
 * Demo content remover.
 *
 * Removes demo content and reverts settings imported by demo packages.
 *
 * @package BetterBlockEditor
 */

namespace BetterBlockEditor\Modules\DemoContent;

use BetterBlockEditor\Modules\DemoContent\Trackers\ContentTracker;

defined( 'ABSPATH' ) || exit;

/**
 * Removes imported demo content and restores original settings.
 */
class Remover {

	/**
	 * Demo type identifier to clean up.
	 *
	 * @var string
	 */
	private $demo_type;

	/**
	 * Tracks stored values for rollback.
	 *
	 * @var ContentTracker
	 */
	private $content_tracker;

	/**
	 * Remover constructor.
	 *
	 * @param ContentTracker $content_tracker Content tracker instance.
	 */
	public function __construct( $content_tracker ) {
		$this->content_tracker = $content_tracker;
		$this->demo_type       = $content_tracker->get_demo_type();
	}

	/**
	 * Removes all imported content types.
	 *
	 * @return void
	 */
	public function remove_content() {
		$this->remove_posts();
		$this->remove_terms();
		$this->revert_wp_global_styles();
	}

	/**
	 * Restores site-level settings changed by imports.
	 *
	 * @return void
	 */
	public function revert_site_settings() {
		$this->revert_wp_settings();
		$this->revert_menus();
		$this->revert_widgets();
		$this->revert_site_identity();
	}

	/**
	 * Revert WP Global Styles.
	 *
	 * @return void
	 */
	protected function revert_wp_global_styles() {
		$latest_globl_styles = $this->content_tracker->get( 'wp_global_styles' );
		if ( ! $latest_globl_styles ) {
			return;
		}

		$existing_post_id = \WP_Theme_JSON_Resolver::get_user_global_styles_post_id();
		if ( ! $existing_post_id ) {
			return;
		}

		wp_update_post(
			array(
				'ID'           => $existing_post_id,
				'post_content' => wp_slash( $latest_globl_styles ),
			)
		);
	}

	/**
	 * Deletes posts that were created during the import.
	 *
	 * @return void
	 */
	protected function remove_posts() {
		global $wpdb;

		$post_ids = $wpdb->get_col( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- no need to cache this since this is a one-time operation.
			$wpdb->prepare(
				"SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value = %s",
				'_wpbbe_imported_item',
				$this->demo_type
			)
		);
		foreach ( $post_ids as $post_id ) {
			wp_delete_post( $post_id, true );
		}
	}

	/**
	 * Deletes imported terms tracked in the database.
	 *
	 * @return void
	 */
	protected function remove_terms() {
		global $wpdb;

		$term_ids = $wpdb->get_col( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- no need to cache this since this is a one-time operation.
			$wpdb->prepare(
				"SELECT term_id FROM {$wpdb->termmeta} WHERE meta_key = %s AND meta_value = %s",
				'_wpbbe_imported_item',
				$this->demo_type
			)
		);
		foreach ( $term_ids as $term_id ) {
			$term = get_term( $term_id );
			if ( $term && ! is_wp_error( $term ) ) {
				wp_delete_term( $term_id, $term->taxonomy );
			}
		}
	}

	/**
	 * Restores WordPress options to their pre-import values.
	 *
	 * @return void
	 */
	protected function revert_wp_settings() {
		$wp_settings = $this->content_tracker->get( 'wp_settings' );
		if ( ! $wp_settings ) {
			return;
		}

		foreach ( $wp_settings as $key => $value ) {
			update_option( $key, $value );
		}

		$this->content_tracker->remove( 'wp_settings' );
	}

	/**
	 * Restores menu location assignments.
	 *
	 * @return void
	 */
	protected function revert_menus() {
		$menu_locations = $this->content_tracker->get( 'menu_locations' );
		if ( ! $menu_locations ) {
			return;
		}

		$locations = get_theme_mod( 'nav_menu_locations' );

		foreach ( $menu_locations as $location => $menu_id ) {
			$locations[ $location ] = $menu_id;
		}

		set_theme_mod( 'nav_menu_locations', $locations );

		$this->content_tracker->remove( 'menu_locations' );
	}

	/**
	 * Restores widget settings saved before the import.
	 *
	 * @return void
	 */
	protected function revert_widgets() {
		$widgets_settings = $this->content_tracker->get( 'widgets_settings' );
		if ( ! $widgets_settings ) {
			return;
		}

		foreach ( $widgets_settings as $setting => $value ) {
			update_option( $setting, $value );
		}

		$this->content_tracker->remove( 'widgets_settings' );
	}

	/**
	 * Restores site identity settings such as logo and icon.
	 *
	 * @return void
	 */
	protected function revert_site_identity() {
		$custom_logo = $this->content_tracker->get( 'custom_logo' );
		if ( $custom_logo ) {
			set_theme_mod( 'custom_logo', $custom_logo );
		}

		$site_icon = $this->content_tracker->get( 'site_icon' );
		if ( $site_icon ) {
			update_option( 'site_icon', $site_icon );
		}

		$this->content_tracker->remove( 'custom_logo' );
		$this->content_tracker->remove( 'site_icon' );
	}
}
