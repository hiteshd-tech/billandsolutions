<?php

namespace The7\Admin\Notices;

defined( 'ABSPATH' ) || exit;

class Dev_Tool_Error extends Abstract_Notice {

	public function render() {
		$error_notice = get_transient( 'the7_dev_tool_error' );
		delete_transient( 'the7_dev_tool_error' );

		if ( ! $error_notice ) {
			$error_notice = esc_html_x( 'Unknown Error', 'admin', 'the7mk2' );
		}
		?>

		<p role="heading"><?php echo esc_html_x( 'Dev tool error', 'admin', 'the7mk2' ); ?></p>
		<p><?php echo esc_html( $error_notice ); ?></p>

		<?php
	}

	public function get_code() {
		return 'the7_dev_tool_error';
	}

	public function is_visible() {
		return (bool) get_transient( 'the7_dev_tool_error' );
	}

	public function get_wrapper_class() {
		return 'notice-error the7-dashboard-notice';
	}
}
