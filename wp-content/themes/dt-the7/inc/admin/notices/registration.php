<?php

namespace The7\Admin\Notices;

defined( 'ABSPATH' ) || exit;

class Registration extends Abstract_Notice {

	public function render() {
		$license_url = \The7_Remote_API::LICENSE_URL;
		$purchase_url = \The7_Remote_API::THEME_PURCHASE_URL;
		$register_url = admin_url( 'admin.php?page=the7-dashboard' );
		?>

		<p role="heading"><?php echo esc_html_x( 'Thank you for choosing The7!', 'admin', 'the7mk2' ); ?></p>
		<p>
			<?php
			echo wp_kses_post(
				sprintf(
					// translators: %s: register link.
					_x( '%s this copy of the theme to access premium plugins, pre-made website templates, one-click updates, and more.', 'admin', 'the7mk2' ),
					'<a href="' . esc_url( $register_url ) . '">' . esc_html_x( 'Register', 'admin', 'the7mk2' ) . '</a>'
				)
			);
			?>
		</p>
		<p>
			<?php
			echo wp_kses_post(
				sprintf(
					// translators: %1$s: ThemeForest's Standard Licenses link, %2$s: purchase link.
					_x( 'Please note — under %1$s, each site or project built with The7 requires its own license. You can purchase additional licenses %2$s.', 'admin', 'the7mk2' ),
					'<a href="' . esc_url( $license_url ) . '" target="_blank" rel="nofollow">' . esc_html_x( "ThemeForest's Standard Licenses", 'admin', 'the7mk2' ) . '</a>',
					'<a href="' . esc_url( $purchase_url ) . '" target="_blank" rel="nofollow">' . esc_html_x( 'here', 'admin', 'the7mk2' ) . '</a>'
				)
			);
			?>
		</p>

		<?php
	}

	public function get_code() {
		return 'the7_registration_notice';
	}

	public function is_visible() {
		return ! presscore_theme_is_activated();
	}

	public function get_wrapper_class() {
		return 'notice-info';
	}
}
