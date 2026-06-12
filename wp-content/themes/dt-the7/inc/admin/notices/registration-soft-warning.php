<?php

namespace The7\Admin\Notices;

defined( 'ABSPATH' ) || exit;

class Registration_Soft_Warning extends Abstract_Notice {

	public function render() {
		$domains_count = \The7_Registration_Warning::get_domains_count();
		?>

		<p role="heading">License usage reminder</p>
		<p>
			We noticed that this purchase code is being used on <?php echo esc_html( $domains_count ); ?> domain names (excluding the most common staging addresses, subdomains, and subfolders).
		</p>
		<p>
			This is a friendly reminder that under <a href="<?php echo esc_url( \The7_Remote_API::LICENSE_URL ); ?>" target="_blank" rel="nofollow">Envato Standard Licenses</a>, you can’t use one license for multiple projects, clients, or jobs. A separate license is required for each website you build. You also can’t transfer a license from one website to another, even if the previous website goes offline. Using the same purchase code across multiple domains will result in it being locked.
		</p>
		<p>
			You can purchase more licenses <a href="<?php echo esc_url( \The7_Remote_API::THEME_PURCHASE_URL ); ?>" target="_blank" rel="nofollow">here</a> and manage them at <a href="<?php echo esc_url( \The7_Remote_API::PURCHASE_CODES_MANAGE_URL ); ?>" target="_blank" rel="nofollow">my.the7.io</a>.
		</p>

		<?php
	}

	public function get_code() {
		return 'the7_registration_soft_warning';
	}

	public function is_visible() {
		return \The7_Registration_Warning::get_domains_count() > 2;
	}

	public function get_wrapper_class() {
		return 'the7-dashboard-notice notice-warning is-dismissible';
	}
}
