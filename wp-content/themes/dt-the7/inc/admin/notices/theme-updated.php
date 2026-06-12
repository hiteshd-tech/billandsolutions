<?php

namespace The7\Admin\Notices;

use The7_Install;

defined( 'ABSPATH' ) || exit;

class Theme_Updated extends Abstract_Notice {

	public function render() {
		?>

		<p role="heading"><?php echo esc_html_x( 'The7 database update complete', 'admin', 'the7mk2' ); ?></p>
		<p>
			<?php esc_html_e( 'Thank you for updating to the latest version!', 'the7mk2' ); ?>
		</p>

		<?php
	}

	public function is_visible() {
		if ( ! current_user_can( 'switch_themes' ) ) {
			return false;
		}

		return ! The7_Install::db_update_is_needed();
	}

	public function get_code() {
		return 'the7_updated';
	}

	public function get_wrapper_class() {
		return 'the7-dashboard-notice notice-success is-dismissible';
	}
}
