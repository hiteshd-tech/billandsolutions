<?php

namespace The7\Admin\Notices;

defined( 'ABSPATH' ) || exit;

class Import_Failed extends Abstract_Notice {

	public const NOTICE_CODE = 'import_failed';

	public function render() {
		?>

		<p>
			<?php esc_html_e( 'The pre-made website template import has failed.', 'the7mk2' ); ?>
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
		return 'the7-dashboard-notice notice-error the7-debug-notice';
	}
}
