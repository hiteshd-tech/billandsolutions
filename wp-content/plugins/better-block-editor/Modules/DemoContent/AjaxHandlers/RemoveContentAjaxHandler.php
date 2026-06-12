<?php
/**
 * AJAX handler for removing imported demo content.
 *
 * @package BetterBlockEditor
 */

namespace BetterBlockEditor\Modules\DemoContent\AjaxHandlers;

use BetterBlockEditor\Modules\DemoContent\Demo\Factory as DemoFactory;
use BetterBlockEditor\Modules\DemoContent\Module;
use BetterBlockEditor\Modules\DemoContent\Trackers\ContentTracker;
use BetterBlockEditor\Modules\DemoContent\Remover;

defined( 'ABSPATH' ) || exit;

/**
 * Handles AJAX requests that remove imported demo content and settings.
 */
class RemoveContentAjaxHandler extends AjaxHandlerBase {

	/**
	 * Registers AJAX hook for demo removal.
	 *
	 * @return void
	 */
	public static function register() {
		add_action( 'wp_ajax_wpbbe_remove_content', array( self::class, 'remove_content' ) );
	}

	/**
	 * Processes the removal of a demo via AJAX.
	 *
	 * @return void
	 */
	public static function remove_content() {
		if ( ! check_ajax_referer( 'wpbbe_remove_demo', false, false ) || ! current_user_can( Module::CAPABILITY ) ) {
			wp_send_json_error( array( 'error_msg' => '<p>' . esc_html__( 'Insufficient user rights.', 'better-block-editor' ) . '</p>' ) );
		}

		$demo_slug = isset( $_POST['demo'] ) ? sanitize_key( wp_unslash( $_POST['demo'] ) ) : '';
		if ( ! $demo_slug ) {
			wp_send_json_error( array( 'error_msg' => '<p>' . esc_html__( 'Invalid request.', 'better-block-editor' ) . '</p>' ) );
		}

		$demo = DemoFactory::create( $demo_slug );

		if ( ! $demo ) {
			wp_send_json_error();
		}

		$demo_to_remove         = $demo->id;
		$rollback_site_settings = true;
		$history                = get_option( ContentTracker::HISTORY_OPTION_ID, array() );
		if ( count( $history ) > 1 ) {
			$history = array_reverse( $history );
			reset( $history );
			$latest_installed_demo_id = key( $history );

			if ( $latest_installed_demo_id !== $demo_to_remove ) {
				$rollback_site_settings = false;
			}
		}

		$content_tracker = new ContentTracker( $demo_to_remove );
		$demo_remover    = new Remover( $content_tracker );

		if ( $content_tracker->get( 'post_types' ) || $content_tracker->get( 'attachments' ) ) {
			$demo_remover->remove_content();
		}

		if ( $rollback_site_settings ) {
			$demo_remover->revert_site_settings();
		}

		$content_tracker->remove_demo();

		// Since we changed content tracker history.
		$demo->refresh_import_status();

		// Set a one-time success notice for the current user to be shown on page load.
		set_transient( Module::MENU_PAGE_SLUG . '_remove_content_notice', 1, MINUTE_IN_SECONDS * 2 );

		wp_send_json_success(
			array(
				'status' => $demo->get_import_status_text(),
			)
		);
	}
}
