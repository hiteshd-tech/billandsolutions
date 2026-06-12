<?php

namespace The7\Admin\Notices;

defined( 'ABSPATH' ) || exit;

class Outdated_Core_Version extends Abstract_Notice {

	public function render() {
		?>

		<p role="heading"><?php echo esc_html_x( 'Important notice', 'admin', 'the7mk2' ); ?></p>
		<p>
			<?php
			echo wp_kses_post(
				sprintf(
					__(
						'You’re using an out-of-date version of %s. For full compatibility with the theme, please %s.',
						'the7mk2'
					),
					'<strong>The7 Elements</strong>',
					'<a href="' . admin_url( 'admin.php?page=the7-plugins' ) . '">' . esc_html__( 'update the plugin', 'the7mk2' ) . '</a>'
				)
			);
			?>
		</p>

		<?php
	}

	public function get_code() {
		return 'outdated_core_plugin';
	}

	public function is_visible() {
		if ( ! current_user_can( 'update_plugins' ) ) {
			return false;
		}

		if ( ! class_exists( 'The7PT' ) || ! function_exists( 'The7PT' ) ) {
			return false;
		}

		if ( ! defined( 'THE7_CORE_COMPATIBLE_VERSION' ) ) {
			return false;
		}

		return version_compare( \The7PT()->version(), THE7_CORE_COMPATIBLE_VERSION, '<' );
	}

	public function get_wrapper_class() {
		return 'the7-dashboard-notice the7-notice notice-error';
	}
}
