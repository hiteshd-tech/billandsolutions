<?php
/**
 * AJAX handler for keeping imported demo content without cleanup.
 *
 * @package BetterBlockEditor
 */

namespace BetterBlockEditor\Modules\DemoContent\AjaxHandlers;

use BetterBlockEditor\Modules\DemoContent\Module;
use BetterBlockEditor\Modules\DemoContent\Demo\Factory as DemoFactory;
use BetterBlockEditor\Modules\DemoContent\Trackers\ContentTracker;

defined( 'ABSPATH' ) || exit;

/**
 * Handles AJAX requests marking demo content as kept.
 */
class KeepContentAjaxHandler extends AjaxHandlerBase {

	/**
	 * Registers the keep-content AJAX hook.
	 *
	 * @return void
	 */
	public static function register() {
		add_action( 'wp_ajax_wpbbe_keep_content', array( self::class, 'ajax_keep_content' ) );
	}

	/**
	 * Responds to keep-content AJAX requests.
	 *
	 * @return void
	 */
	public static function ajax_keep_content() {
		if ( ! check_ajax_referer( 'wpbbe_keep_demo_content', false, false ) || ! current_user_can( Module::CAPABILITY ) ) {
			wp_send_json_error( array( 'error_msg' => '<p>' . esc_html__( 'Insufficient user rights.', 'better-block-editor' ) . '</p>' ) );
		}

		$demo_id = isset( $_POST['demo_id'] ) ? sanitize_key( $_POST['demo_id'] ) : '';
		$demo    = DemoFactory::create( $demo_id );
		if ( ! $demo ) {
			wp_send_json_error( array( 'error_msg' => '<p>' . esc_html__( 'Unable to recognise demo.', 'better-block-editor' ) . '</p>' ) );
		}

		$content_tracker = new ContentTracker( $demo_id );
		$content_tracker->keep_demo_content();

		// Since we changed content tracker history.
		$demo->refresh_import_status();

		// Set a one-time success notice for the current user to be shown on page load.
		set_transient( Module::MENU_PAGE_SLUG . '_keep_content_notice', 1, MINUTE_IN_SECONDS * 2 );

		wp_send_json_success(
			array(
				'status' => $demo->get_import_status_text(),
			)
		);
	}
}
