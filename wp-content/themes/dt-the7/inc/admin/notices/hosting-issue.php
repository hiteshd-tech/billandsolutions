<?php

namespace The7\Admin\Notices;

use The7\Admin\Status_Info;

defined( 'ABSPATH' ) || exit;

class Hosting_Issue extends Abstract_Notice {

	public function render() {
		$status_page_url = admin_url( 'admin.php?page=the7-status' );
		?>

		<p role="heading"><?php esc_html_e( 'The7 detected an issue with your hosting', 'the7mk2' ); ?></p>
		<p>
			<?php esc_html_e( 'The7 has detected a hosting issue that prevents it from functioning as intended.', 'the7mk2' ); ?>
		</p>
		<p>
			<?php
			echo wp_kses_post(
				sprintf(
					// translators: %s: System Status page URL.
					__( 'Check the %s page for details.', 'the7mk2' ),
					'<a href="' . esc_url( $status_page_url ) . '">' . esc_html__( 'System Status', 'the7mk2' ) . '</a>'
				)
			);
			?>
		</p>

		<?php
	}

	public function get_code() {
		return 'hosting_issue';
	}

	public function is_visible() {
		$status_checks = Status_Info::get_system_status();
		foreach ( $status_checks as $check ) {
			if ( isset( $check['status'] ) && $check['status'] === 'bad' ) {
				return true;
			}
		}

		return false;
	}

	public function get_wrapper_class() {
		return 'the7-dashboard-notice notice-error is-dismissible';
	}
}
