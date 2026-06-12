<?php

namespace The7\Admin\Notices;

defined( 'ABSPATH' ) || exit;

class Import_Succeed extends Abstract_Notice {

	public const NOTICE_CODE = 'import_succeed';

	public function render() {
		?>

		<p>
			<?php esc_html_e( 'The pre-made website template has been successfully imported.', 'the7mk2' ); ?>
		</p>

		<?php
	}

	public function get_code() {
		return self::NOTICE_CODE;
	}

	public function is_visible() {
		return $this->should_display_once();
	}

	public function get_wrapper_class() {
		return 'the7-dashboard-notice notice-success the7-debug-notice';
	}
}
