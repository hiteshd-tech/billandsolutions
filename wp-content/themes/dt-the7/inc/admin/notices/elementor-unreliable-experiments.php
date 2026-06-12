<?php

namespace The7\Admin\Notices;

defined( 'ABSPATH' ) || exit;

class Elementor_Unreliable_Experiments extends Abstract_Notice {

	public function render() {
		?>

		<p role="heading"><?php echo esc_html_x( 'Recommendation', 'admin', 'the7mk2' ); ?></p>
		<p>
			<?php
			echo wp_kses_post(
				sprintf(
					// translators: %s: settings link.
					_x( 'Elementor "Advanced Custom Breakpoints" may cause noticeable slowdowns when editing larger pages. If you experience this, you can disable "Advanced Custom Breakpoints" in Elementor %s to improve performance.', 'admin', 'the7mk2' ),
					'<a href="' . esc_url( admin_url( 'admin.php?page=elementor-settings#tab-experiments' ) ) . '">' . esc_html_x( 'settings', 'admin', 'the7mk2' ) . '</a>'
				)
			);
			?>
		</p>

		<?php
	}

	public function get_code() {
		return 'the7_elementor_unreliable_experiments';
	}

	public function is_visible() {
		return the7_elementor_is_active() && get_option( 'elementor_experiment-additional_custom_breakpoints' ) === 'active';
	}

	public function get_wrapper_class() {
		return 'the7-dashboard-notice notice-info the7-debug-notice is-dismissible';
	}
}



