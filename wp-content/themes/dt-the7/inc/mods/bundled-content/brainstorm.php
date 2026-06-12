<?php
/**
 * @package The7
 */

namespace The7\Mods\Bundled_Content;

defined( 'ABSPATH' ) || exit;

/**
 * Brainstorm class.
 */
class Brainstorm extends Abstract_Bundled_Plugin {

	/**
	 * @var int
	 */
	private $ultimate_addon_id = 6892199;

	/**
	 * @var int
	 */
	private $convert_plus_id = 14058953;

	/**
	 * @return void
	 */
	public function activate_plugin() {
		$this->activate_brainstorm_plugins();
	}

	/**
	 * @return void
	 */
	public function deactivate_plugin() {
	}

	/**
	 * @return false
	 */
	public function is_activated_plugin() {
		return false;
	}

	/**
	 * @return string
	 */
	protected function get_bundled_plugin_code() {
		return '';
	}

	/**
	 * @return bool
	 */
	public function is_active() {
		return class_exists( 'Ultimate_VC_Addons', false ) || class_exists( 'Convert_Plug', false );
	}

	/**
	 * @return void
	 */
	private function activate_brainstorm_plugins() {
		add_filter( "envato_active_oauth_title_{$this->ultimate_addon_id}", [ $this, 'the7_ultimate_addon_oauth_title' ], 30 );
		add_filter( 'agency_updater_request_support', [ $this, 'the7_ultimate_addon_agency_updater_request_support' ], 30 );

		if ( ! defined( 'BSF_UNREG_MENU' ) ) {
			define( 'BSF_UNREG_MENU', true );
		}

		if ( presscore_is_silence_enabled() ) {
			if ( $this->is_bundled_plugin( 'Ultimate_VC_Addons' ) ) {
				if ( ! defined( 'ULTIMATE_THEME_ACT' ) ) {
					define( 'ULTIMATE_THEME_ACT', true );
				}
				add_filter( "bsf_display_product_activation_notice_{$this->ultimate_addon_id}", '__return_false' );
			}

			if ( $this->is_bundled_plugin( 'convertplug' ) ) {
				if ( ! defined( 'CONVERTPLUS_THEME_ACT' ) ) {
					define( 'CONVERTPLUS_THEME_ACT', true );
				}
				add_filter( "bsf_display_product_activation_notice_{$this->convert_plus_id}", '__return_false' );
			}

			if ( defined( 'ULTIMATE_THEME_ACT' ) || defined( 'CONVERTPLUS_THEME_ACT' ) ) {
				add_action( 'admin_head', array( $this, 'print_inline_admin_css' ), 100 );
			}
		}
	}

	/**
	 * @return bool
	 */
	public function is_activated_by_theme() {
		return (bool) presscore_get_purchase_code();
	}

	/**
	 * @return string
	 */
	public function the7_ultimate_addon_oauth_title() {
		return '<span class="active">Active!</span>';
	}

	/**
	 * @return string
	 */
	public function the7_ultimate_addon_agency_updater_request_support() {
		return 'https://support.dream-theme.com';
	}

	/**
	 * @return void
	 */
	public function print_inline_admin_css() {
		?>
		<style type="text/css">
			<?php if ( defined( 'ULTIMATE_THEME_ACT' ) ) : ?>
			#the-list > tr[data-slug=Ultimate_VC_Addons] .license {
				display: none;
			}

			<?php endif ?>
			<?php if ( defined( 'CONVERTPLUS_THEME_ACT' ) ) : ?>
			#the-list > tr[data-slug=convertplug] .license {
				display: none;
			}

			<?php endif ?>
		</style>
		<?php
	}
}
