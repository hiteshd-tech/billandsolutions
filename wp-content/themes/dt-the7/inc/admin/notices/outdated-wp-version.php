<?php

namespace The7\Admin\Notices;

defined( 'ABSPATH' ) || exit;

class Outdated_Wp_Version extends Abstract_Notice {

	public function render() {
		$required_version = $this->get_required_wp_version();

		if ( ! $required_version ) {
			return;
		}
		?>

		<p role="heading"><?php echo esc_html_x( 'The7 detected an incompatible WordPress version', 'admin', 'the7mk2' ); ?></p>
		<p>
			<?php
			echo esc_html(
				sprintf(
					// translators: %s: Minimum required WordPress version.
					_x( 'The minimum required WordPress version is %s. Please update your WordPress installation to use The7.', 'admin', 'the7mk2' ),
					$required_version
				)
			);
			?>
		</p>
		<p>
			<a href="<?php echo esc_url( admin_url( 'update-core.php' ) ); ?>"><?php echo esc_html_x( 'Update WordPress', 'admin', 'the7mk2' ); ?></a>
		</p>

		<?php
	}

	public function get_code() {
		return 'outdated_wp_version';
	}

	public function is_visible() {
		if ( ! current_user_can( 'update_core' ) ) {
			return false;
		}

		$required_version = $this->get_required_wp_version();
		if ( ! $required_version ) {
			return false;
		}

		return version_compare( get_bloginfo( 'version' ), $required_version, '<' );
	}

	public function get_wrapper_class() {
		return 'the7-dashboard-notice notice-error';
	}

	private function get_required_wp_version() {
		$theme = wp_get_theme( get_template() );

		return $theme->get( 'RequiresWP' );
	}
}
