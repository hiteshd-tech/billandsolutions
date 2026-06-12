<?php

namespace The7\Admin\Notices;

defined( 'ABSPATH' ) || exit;

class Theme_Auto_Deactivation extends Abstract_Notice {

	public function render() {
		?>

		<p role="heading"><?php echo esc_html_x( 'Theme was remotely deregistered', 'admin', 'the7mk2' ); ?></p>
		<p>
			<?php echo esc_html_x( 'The theme was remotely deregistered because the current purchase code is registered to another domain.', 'admin', 'the7mk2' ); ?>
		</p>
		<p>
			<?php
			echo wp_kses_post(
				sprintf(
					// translators: %1$s: purchase codes manage link, %2$s: theme purchase link.
					_x( 'Confused by multiple licenses and websites? Manage them %1$s. You can buy another theme copy %2$s.', 'admin', 'the7mk2' ),
					'<a href="' . esc_url( \The7_Remote_API::PURCHASE_CODES_MANAGE_URL ) . '" target="_blank" rel="nofollow">' . esc_html_x( 'here', 'admin', 'the7mk2' ) . '</a>',
					'<a href="' . esc_url( \The7_Remote_API::THEME_PURCHASE_URL ) . '" target="_blank" rel="nofollow">' . esc_html_x( 'here', 'admin', 'the7mk2' ) . '</a>'
				)
			);
			?>
		</p>

		<?php
	}

	public function get_code() {
		return 'the7_auto_deactivation';
	}

	public function is_visible() {
		if ( the7_admin_notices()->notice_is_dismissed( 'the7_auto_deactivation' ) ) {
			delete_site_option( 'the7_auto_deactivated' );
		}

		return (bool) get_site_option( 'the7_auto_deactivated' );
	}

	public function get_wrapper_class() {
		return 'the7-dashboard-notice notice-warning the7-debug-notice is-dismissible';
	}
}
