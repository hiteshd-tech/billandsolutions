<?php

namespace The7\Admin\Notices;

defined( 'ABSPATH' ) || exit;

class Registration_Deregistration_Success extends Abstract_Registration_Settings_Notice {

	public function render() {
		?>

		<p>
			<?php esc_html_e( 'Purchase code successfully de-registered.', 'the7mk2' ); ?>
		</p>

		<?php
	}

	public function get_code() {
		return 'the7_theme_deregistration_success';
	}

	public function get_wrapper_class() {
		return 'notice-success the7-dashboard-notice';
	}

	protected function get_error_codes() {
		return array( 'the7_deregistration_success' );
	}
}
