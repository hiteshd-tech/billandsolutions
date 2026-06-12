<?php

namespace The7\Admin\Notices;

defined( 'ABSPATH' ) || exit;

class Registration_Error extends Abstract_Registration_Settings_Notice {

	public function render() {
		$errors = $this->get_errors();

		if ( ! $errors ) {
			$errors = [
				[
					'message' => 'Dynamic registration notice for testing',
				],
			];
		}
		?>

		<?php foreach ( $errors as $error ) : ?>
			<p><?php echo wp_kses_post( $error['message'] ); ?></p>
		<?php endforeach; ?>

		<?php
	}

	public function get_code() {
		return 'the7_theme_registration_error';
	}

	protected function get_error_codes() {
		return array( 'the7_registration_error' );
	}
}
