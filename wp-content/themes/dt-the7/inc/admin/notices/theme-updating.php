<?php

namespace The7\Admin\Notices;

use The7_Install;

defined( 'ABSPATH' ) || exit;

class Theme_Updating extends Abstract_Notice {

	public function render() {
		$force_update_url = wp_nonce_url( add_query_arg( 'force_update_the7', 'true', admin_url( 'admin.php?page=the7-dashboard' ) ), 'force_update_the7_nonce' );
		?>

		<p role="heading"><?php echo esc_html_x( 'The7 database update', 'admin', 'the7mk2' ); ?></p>
		<p>
			<?php esc_html_e( 'Your database is being updated in the background.', 'the7mk2' ); ?>

		</p>
		<p>
			<?php
			echo wp_kses_post(
				sprintf(
					// translators: %s: link to run update now.
					_x( 'Taking a while? %s', 'admin', 'the7mk2' ),
					'<a href="' . esc_url( $force_update_url ) . '">' . esc_html_x( 'Click here to run it now', 'admin', 'the7mk2' ) . '</a>'
				)
			);
			?>
		</p>

		<?php
	}

	public function is_visible() {
		if ( ! current_user_can( 'switch_themes' ) ) {
			return false;
		}

		return The7_Install::db_update_is_needed() && The7_Install::db_is_updating();
	}

	public function get_code() {
		return 'the7_updating';
	}

	public function get_wrapper_class() {
		return 'the7-dashboard-notice notice-info';
	}
}
