<?php
/**
 * The7 status screen.
 *
 * @package The7
 */

defined( 'ABSPATH' ) || exit;

global $wp_filesystem;
?>

<div id="the7-dashboard" class="wrap the7-status">
	<h1><?php esc_html_e( 'System Status', 'the7mk2' ); ?></h1>
	<div class="wp-header-end"></div>

	<div class="the7-postbox">

		<table class="the7-system-status">
			<?php
			$status_checks = The7\Admin\Status_Info::get_system_status();
			foreach ( $status_checks as $check ) {
				if ( ! $check ) {
					continue;
				}

				$is_php_modules = ! empty( $check['is_php_modules'] );
				if ( $is_php_modules ) {
					?>
					<style>
						.the7-php-modules-message p {
							display: none;
						}

						#the7-dashboard .the7-php-modules-message li {
							margin: 7px 0;
						}

						.the7-php-modules-message .warning {
							color: orange;
						}

						.the7-php-modules-message .error {
							color: red;
						}
					</style>
					<?php
				}
				?>
				<tr>
					<td><?php echo esc_html( $check['label'] ); ?></td>
					<td<?php echo $is_php_modules ? ' class="the7-php-modules-message"' : ''; ?>>
						<code class="<?php echo isset( $check['value_class'] ) ? esc_attr( $check['value_class'] ) : 'status-' . esc_attr( $check['status'] ); ?>"><?php echo $check['value']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></code><?php echo $check['description']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</td>
				</tr>
				<?php
			}
			?>
		</table>

		<p>
			<a href="<?php echo esc_url( admin_url( 'site-health.php?tab=debug' ) ); ?>" id="the7-service-info-toggler"><?php esc_html_e( 'Service information', 'the7mk2' ); ?></a>
		</p>

		<div id="the7-debug-report">
			<?php
			if ( ! class_exists( 'WP_Debug_Data' ) ) {
				require_once ABSPATH . 'wp-admin/includes/class-wp-debug-data.php';
			}

			// Who knows what this is for.
			if ( ! class_exists( 'WP_Site_Health' ) ) {
				require_once ABSPATH . 'wp-admin/includes/class-wp-site-health.php';
			}

			// Don't know why this is here. Just copied from site-health-info.php.
			$health_check_site_status = WP_Site_Health::get_instance();

			$info_raw = WP_Debug_Data::debug_data();

			// Remove sizes info because it require ajax to setup.
			unset( $info_raw['wp-paths-sizes'] );

			$info_formatted = WP_Debug_Data::format( $info_raw, 'debug' );
			// Replace ` with ``` for preatty pasting.
			$info_formatted = '```' . trim( $info_formatted, '`' ) . '```';
			?>
			<textarea readonly="readonly"><?php echo esc_textarea( $info_formatted ); ?></textarea>
			<p class="copy-error"><?php esc_html_e( 'Please press Ctrl/Cmd+C to copy.', 'the7mk2' ); ?>&nbsp;<a href="<?php echo esc_url( admin_url( 'admin.php?page=the7-dev' ) ); ?>"><?php esc_html_e( 'The7 debug tools', 'the7mk2' ); ?></a></p>
		</div>

	</div>
</div>
<script type="text/javascript">
	jQuery(function ($) {
		$("#the7-service-info-toggler").on("click", function (event) {
			event.preventDefault();

			const $reportContainer = $("#the7-debug-report");
			$reportContainer.toggle();
			$reportContainer.find( 'textarea' ).focus().select();
		})
	});
</script>
