<?php

namespace The7\Admin\Notices;

defined( 'ABSPATH' ) || exit;

class Outdated_Elementor_Version extends Abstract_Notice {

	public function render() {
		?>

		<p role="heading"><?php echo esc_html_x( 'Important notice', 'admin', 'the7mk2' ); ?></p>
		<p>
			<?php
			echo wp_kses_post(
				sprintf(
					// translators: %s: plugin name.
					__(
						'You are using an outdated version of the %s plugin, which is not compatible with the current version of The7.',
						'the7mk2'
					),
					'<strong>Elementor</strong>'
				)
			);
			?>
		</p>
		<p>
			<?php
			echo wp_kses_post(
				sprintf(
					// translators: %s: Plugins admin page url.
					__(
						'Please %s to ensure full compatibility and optimal performance.',
						'the7mk2'
					),
					'<a href="' . admin_url( 'plugins.php' ) . '">' . esc_html__( 'update the plugin', 'the7mk2' ) . '</a>'
				)
			);
			?>
		</p>

		<?php
	}

	public function get_code() {
		return 'outdated_elementor_version_warning';
	}

	public function is_visible() {
		if ( ! defined( 'ELEMENTOR_VERSION' ) || ! class_exists( \The7_Elementor_Compatibility::class ) || ! current_user_can( 'update_plugins' ) ) {
			return false;
		}

		return version_compare( ELEMENTOR_VERSION, \The7_Elementor_Compatibility::MINIMAL_ELEMENTOR_VERSION, '<' );
	}

	public function get_wrapper_class() {
		return 'the7-dashboard-notice notice-error the7-debug-notice';
	}
}
