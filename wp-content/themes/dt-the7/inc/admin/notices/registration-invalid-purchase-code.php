<?php

namespace The7\Admin\Notices;

defined( 'ABSPATH' ) || exit;

class Registration_Invalid_Purchase_Code extends Abstract_Registration_Settings_Notice {

	public function render() {
		?>

		<p>
			<?php esc_html_e( 'Purchase code is not valid.', 'the7mk2' ); ?>
		</p>

		<?php
	}

	public function get_code() {
		return 'the7_invalid_purchase_code_notice';
	}

	protected function get_error_codes() {
		return [ 'the7_invalid_purchase_code' ];
	}
}
