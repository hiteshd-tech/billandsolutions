<?php

namespace The7\Admin\Notices;

use The7_Install;

defined( 'ABSPATH' ) || exit;

class Theme_Update extends Abstract_Notice {

	public function render() {
		$update_url = wp_nonce_url( add_query_arg( 'do_update_the7', 'true', admin_url( 'admin.php?page=the7-dashboard' ) ), 'do_update_the7_nonce' );
		?>

		<p role="heading"><?php echo esc_html_x( 'The7 database update', 'admin', 'the7mk2' ); ?></p>
		<p>
			<?php esc_html_e( 'Your site database needs to be updated to match the latest version of The7.', 'the7mk2' ); ?>
		</p>
		<p>
			<a href="<?php echo esc_url( $update_url ); ?>" class="the7-update-now button-primary">
				<?php esc_html_e( 'Run the update', 'the7mk2' ); ?>
			</a>
		</p>
		<script type="text/javascript">
			jQuery( '.the7-update-now' ).click( 'click', function() {
				return window.confirm( '<?php echo esc_js( __( 'It is strongly recommended that you backup your database before proceeding. Are you sure you wish to run the updater now?', 'the7mk2' ) ); ?>' ); // jshint ignore:line
			});
		</script>

		<?php
	}

	public function is_visible() {
		if ( ! current_user_can( 'switch_themes' ) ) {
			return false;
		}

		return The7_Install::db_update_is_needed() && ! The7_Install::db_is_updating();
	}

	public function get_code() {
		return 'the7_update';
	}

	public function get_wrapper_class() {
		return 'the7-dashboard-notice notice-warning';
	}
}
