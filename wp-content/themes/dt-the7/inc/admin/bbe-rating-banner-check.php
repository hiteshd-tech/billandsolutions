<?php
/**
 * The7 BBE Rating Banner Logic.
 *
 * @package The7
 */

namespace The7\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Handles the logic for displaying the BBE rating banner.
 *
 * This class manages when and how the Better Block Editor rating notice appears,
 * including scheduling, visibility checks, and user interactions.
 */
class BBE_Rating_Banner_Check {

	/**
	 * Cron event hook name.
	 *
	 * @var string
	 */
	const CRON_EVENT        = 'the7_bbe_rating_cron_event';

	/**
	 * Option name for banner visibility status.
	 *
	 * @var string
	 */
	const OPTION_VISIBILITY = 'the7_bbe_rating_notice_visibility';

	/**
	 * Option name for dismissed status.
	 *
	 * @var string
	 */
	const OPTION_DISMISSED  = 'the7_bbe_rating_notice_dismissed';

	/**
	 * BBE plugin file path.
	 *
	 * @var string
	 */
	const BBE_PLUGIN        = 'better-block-editor/better-block-editor.php';

	/**
	 * AJAX action name.
	 *
	 * @var string
	 */
	const AJAX_ACTION       = 'the7_bbe_rating_action';

	/**
	 * Initialize hooks for BBE rating banner.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'admin_init', [ __CLASS__, 'check_conditions' ] );

		// Ensure conditions are also checked in WP-CLI context where admin_init does not fire.
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			add_action( 'init', [ __CLASS__, 'check_conditions' ] );
		}
		add_action( self::CRON_EVENT, [ __CLASS__, 'cron_callback' ] );
		add_action( 'wp_ajax_' . self::AJAX_ACTION, [ __CLASS__, 'ajax_callback' ] );
	}

	/**
	 * Check conditions and schedule banner display if requirements are met.
	 *
	 * Runs on admin_init. Schedules a cron event to show the banner after 1 month
	 * if FSE mode is active and BBE plugin is installed.
	 *
	 * @return void
	 */
	public static function check_conditions() {
		// If dismissed, stop.
		if ( get_option( self::OPTION_DISMISSED ) ) {
			return;
		}

		// If already visible, stop.
		if ( get_option( self::OPTION_VISIBILITY ) ) {
			return;
		}

		// If cron scheduled, stop.
		if ( wp_next_scheduled( self::CRON_EVENT ) ) {
			return;
		}

		// Check requirements: FSE + BBE.
		if ( self::meet_requirements() ) {
			// Schedule check in 1 month.
			wp_schedule_single_event( time() + MONTH_IN_SECONDS, self::CRON_EVENT );
		}
	}

	/**
	 * Cron event callback to check if banner should be shown.
	 *
	 * Re-checks requirements and shows the banner if conditions are met.
	 *
	 * @return void
	 */
	public static function cron_callback() {
		// Re-check requirements.
		self::maybe_show_banner();
		// If conditions not met, we do nothing. The cron is gone.
		// Logic in check_conditions will restart the cycle (schedule another month)
		// when the user visits admin and meets requirements later.
	}

	/**
	 * Handle AJAX request for user actions on the banner.
	 *
	 * Processes 'rate_forever' (dismisses permanently) or 'maybe_later' (reschedules).
	 *
	 * @return void
	 */
	public static function ajax_callback() {
		check_ajax_referer( self::AJAX_ACTION, 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'You are not allowed to perform this action.', 'the7mk2' ) ) );
		}
		$action = isset( $_POST['button_action'] ) ? sanitize_text_field( wp_unslash( $_POST['button_action'] ) ) : '';

		if ( $action === 'rate_forever' ) {
			update_option( self::OPTION_DISMISSED, 1 );
			delete_option( self::OPTION_VISIBILITY );
			// Unschedule if exists (just in case).
			wp_clear_scheduled_hook( self::CRON_EVENT );
		} elseif ( $action === 'maybe_later' ) {
			delete_option( self::OPTION_VISIBILITY );
			// Ensure only one cron event is scheduled.
			wp_clear_scheduled_hook( self::CRON_EVENT );
			// Schedule in 1 month.
			wp_schedule_single_event( time() + MONTH_IN_SECONDS, self::CRON_EVENT );
		}

		wp_send_json_success();
	}

	/**
	 * Check if requirements are met to show the banner.
	 *
	 * Requirements: FSE (Gutenberg) theme mode is active and BBE plugin is active.
	 *
	 * @return bool True if requirements are met, false otherwise.
	 */
	private static function meet_requirements() {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		return the7_is_gutenberg_theme_mode_active() && is_plugin_active( self::BBE_PLUGIN );
	}

	/**
	 * Show the banner if requirements are met.
	 *
	 * Sets the visibility option to true if FSE mode and BBE plugin are active.
	 *
	 * @return void
	 */
	public static function maybe_show_banner() {
		if ( self::meet_requirements() ) {
			update_option( self::OPTION_VISIBILITY, 1 );
		}
	}
}
