<?php
/**
 * The7 BBE Rating Banner notice.
 *
 * @package The7
 */

namespace The7\Admin\Notices;

use The7\Admin\BBE_Rating_Banner_Check;

defined( 'ABSPATH' ) || exit;

/**
 * Notice for rating Better Block Editor (BBE) plugin.
 *
 * Displays a notice asking users to rate the BBE plugin after using FSE mode for a while.
 */
class Rate_BBE extends Abstract_Notice {

	/**
	 * Get the notice code identifier.
	 *
	 * @return string The notice code.
	 */
	public function get_code() {
		return 'the7_rate_bbe';
	}

	/**
	 * Check if the notice should be visible.
	 *
	 * @return bool True if the notice should be shown, false otherwise.
	 */
	public function is_visible() {
		return (bool) get_option( BBE_Rating_Banner_Check::OPTION_VISIBILITY );
	}

	/**
	 * Render the notice content.
	 *
	 * Outputs the HTML and JavaScript for the BBE rating notice.
	 *
	 * @return void
	 */
	public function render() {
		$rate_url = 'https://wordpress.org/support/plugin/better-block-editor/reviews/#new-post';
		?>
		<div class="the7-rate-bbe-content">
			<p>
				<?php esc_html_e( 'Hi,', 'the7mk2' ); ?><br>
				<?php esc_html_e( "You've been using The7 in native Full Site Editing (FSE) mode for a while now. It's powered by our free Better Block Editor (BBE) plugin, which adds responsiveness and extra features to WordPress core blocks.", 'the7mk2' ); ?>
			</p>
			<p>
				<?php esc_html_e( "If you have a moment, we'd really appreciate a quick rating on WordPress.org — it helps us keep improving BBE and keeping it free for everyone.", 'the7mk2' ); ?>
			</p>
			<p>
				<button type="button" class="button the7-rate-bbe-later"><?php esc_html_e( 'Maybe later', 'the7mk2' ); ?></button>
				<a href="<?php echo esc_url( $rate_url ); ?>" class="button button-primary the7-rate-bbe-now" target="_blank" rel="noopener noreferrer" style="margin-left: .5rem;"><?php esc_html_e( 'Rate BBE', 'the7mk2' ); ?></a>
			</p>
		</div>
		<script type="text/javascript">
			jQuery(document).ready(function($) {
				var $notice = $('.the7-rate-bbe-content').closest('.the7-notice');

				function handle_rate_bbe_action(action) {
					$.post(ajaxurl, {
						action: '<?php echo esc_js( BBE_Rating_Banner_Check::AJAX_ACTION ); ?>',
						nonce: '<?php echo esc_js( wp_create_nonce( BBE_Rating_Banner_Check::AJAX_ACTION ) ); ?>',
						button_action: action
					}).always(function() {
						$notice.slideUp(function() {
							$(this).remove();
						});
					});
				}

				$notice.on('click', '.the7-rate-bbe-later', function(e) {
					e.preventDefault();
					handle_rate_bbe_action('maybe_later');
				});

				$notice.on('click', '.the7-rate-bbe-now', function(e) {
					// Link opens in new tab, but we also want to dismiss forever
					handle_rate_bbe_action('rate_forever');
				});
			});
		</script>
		<?php
	}

	/**
	 * @return string
	 */
	public function get_wrapper_class() {
		return 'the7-dashboard-notice notice-info the7-debug-notice';
	}
}
