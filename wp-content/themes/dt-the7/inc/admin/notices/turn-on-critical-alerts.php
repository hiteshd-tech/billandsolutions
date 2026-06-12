<?php

namespace The7\Admin\Notices;

defined( 'ABSPATH' ) || exit;

class Turn_On_Critical_Alerts extends Abstract_Notice {

	public function render() {
		?>

		<p role="heading"><?php echo esc_html_x( 'Critical email alerts disabled', 'admin', 'the7mk2' ); ?></p>
		<p>
			<?php echo esc_html_x( 'The "Allow sending critical alerts by email" setting is off. Enable it to get urgent bug and security notices. We do not collect your email and we never spam.', 'admin', 'the7mk2' ); ?>
		</p>
		<p>
			<a href="<?php echo wp_nonce_url(
				admin_url( 'admin.php?page=the7-dashboard&the7_dashboard_settings[critical-alerts]=true' ),
				\The7_Admin_Dashboard::UPDATE_DASHBOARD_SETTINGS_NONCE_ACTION
			); ?>"><?php echo esc_html_x( 'Enable email alerts', 'admin', 'the7mk2' ); ?></a>
		</p>
		<p>
			<?php echo esc_html_x( 'You can change this anytime under The7 > Settings.', 'admin', 'the7mk2' ); ?>
		</p>

		<?php
	}

	public function get_code() {
		return 'turn-on-critical-alerts';
	}

	public function is_visible() {
		return ! \The7_Admin_Dashboard_Settings::get( 'critical-alerts' );
	}

	public function get_wrapper_class() {
		return 'the7-dashboard-notice notice-warning is-dismissible';
	}
}
