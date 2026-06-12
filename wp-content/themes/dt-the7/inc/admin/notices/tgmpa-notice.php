<?php

namespace The7\Admin\Notices;

use The7_TGMPA;

defined( 'ABSPATH' ) || exit;

class TGMPA_Notice extends Abstract_Notice {

	public function render() {
		$notice_strings = The7_TGMPA::get_instance()->get_notice_strings();

		if ( ! $notice_strings ) {
			$notice_strings = [
				'TGMPA admin notice',
				'Some text for debugging purposes',
			];
		}

		$title = array_shift( $notice_strings );
		?>

		<p role="heading"><?php echo wp_kses_post( $title ); ?></p>

		<?php foreach ( $notice_strings as $notice_string ) : ?>

		<p>
			<?php echo wp_kses_post( $notice_string ); ?>
		</p>

		<?php endforeach; ?>

		<?php
	}

	public function get_code() {
		return 'tgmpa';
	}

	public function is_visible() {
		return The7_TGMPA::get_instance()->is_show_notice();
	}

	public function get_wrapper_class() {
		return 'the7-dashboard-notice notice-success';
	}
}
