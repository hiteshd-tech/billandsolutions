<?php
/**
 * Demo Content module bootstrap.
 *
 * Registers demo content related functionality and admin pages.
 *
 * @package BetterBlockEditor
 */

namespace BetterBlockEditor\Modules\DemoContent;

use BetterBlockEditor\Base\ManagableModuleInterface;
use BetterBlockEditor\Base\ModuleBase;
use BetterBlockEditor\Modules\DemoContent\AjaxHandlers\ImportContentAjaxHandler;
use BetterBlockEditor\Modules\DemoContent\AjaxHandlers\ThemeInstallAjaxHandler;
use BetterBlockEditor\Modules\DemoContent\AjaxHandlers\RemoveContentAjaxHandler;
use BetterBlockEditor\Modules\DemoContent\AjaxHandlers\KeepContentAjaxHandler;
use BetterBlockEditor\Modules\DemoContent\FontDownloader;
use BetterBlockEditor\Modules\DemoContent\FontDownloaderRestAPI;

defined( 'ABSPATH' ) || exit;

/**
 * Bootstraps Demo Content importer admin experience.
 */
class Module extends ModuleBase implements ManagableModuleInterface {

	const MODULE_IDENTIFIER = 'demo-content';
	const IS_CORE_MODULE    = true;

	/**
	 * Slug for the Site Templates admin page.
	 */
	const MENU_PAGE_SLUG = 'wpbbe-demo-content';

	/**
	 * Capability required to access the page.
	 */
	const CAPABILITY = 'manage_options';

	/**
	 * Stores the WP page hook suffix returned by add_menu_page.
	 * Used to conditionally enqueue assets only on our page.
	 *
	 * @var string|null
	 */
	protected $page_hook = null;

	public static function get_title() {
		return __( 'Site Templates', 'better-block-editor' );
	}

	public static function get_label() {
		return __('Enable BBE pre-made Site Templates', 'better-block-editor' );
	}

	public static function get_settings_order() {
		return 50;
	}


	/**
	 * Setup module hooks.
	 *
	 * @return void
	 */
	public function setup_hooks() {
		if ( defined( 'WPBBE_DISABLE_DEMO_CONTENT' ) && constant( 'WPBBE_DISABLE_DEMO_CONTENT' ) ) {
			return;
		}

		// Register admin menu and page.
		add_action( 'admin_menu', array( $this, 'register_admin_menu' ) );

		// Enqueue assets only on our admin page.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );

		// Show one-time notices.
		add_action( 'admin_notices', array( $this, 'maybe_show_a_notice' ) );

		ImportContentAjaxHandler::register();
		ThemeInstallAjaxHandler::register();
		RemoveContentAjaxHandler::register();
		KeepContentAjaxHandler::register();

		$font_downloader = FontDownloader::instance();
		$rest_api        = new FontDownloaderRestAPI( $font_downloader );
		$rest_api->init();
	}

	/**
	 * Register the "Site Templates" top-level admin menu and page.
	 */
	public function register_admin_menu() {
		$this->page_hook = add_menu_page(
			__( 'Site Templates', 'better-block-editor' ),
			__( 'Site Templates', 'better-block-editor' ),
			self::CAPABILITY,
			self::MENU_PAGE_SLUG,
			function () {
				include WPBBE_DIR . '/admin/templates/demo-content/page.php';
			},
			'dashicons-layout',
			58
		);
	}

	/**
	 * Enqueue admin assets for the Site Templates page.
	 *
	 * @param string $hook_suffix The current admin page hook suffix.
	 */
	public function enqueue_admin_assets( $hook_suffix ) {
		// Only load on our specific admin page.
		if ( $hook_suffix !== $this->page_hook ) {
			return;
		}

		wp_enqueue_style(
			WPBBE_PLUGIN_ID . '__demo-content__admin-style',
			WPBBE_URL . 'admin/css/demo-content/page.css',
			array(),
			WPBBE_VERSION
		);

		wp_enqueue_script(
			WPBBE_PLUGIN_ID . '__demo-content__font-downloader',
			WPBBE_URL . 'admin/js/demo-content/font-downloader.js',
			array( 'jquery', 'wp-api-request' ),
			WPBBE_VERSION,
			true
		);

		$admin_script_handle = WPBBE_PLUGIN_ID . '__demo-content__admin-script';
		wp_enqueue_script(
			$admin_script_handle,
			WPBBE_URL . 'admin/js/demo-content/page.js',
			array( 'jquery', 'wp-i18n' ),
			WPBBE_VERSION,
			true
		);

		wp_localize_script(
			$admin_script_handle,
			'wpbbeContentImportData',
			array(
				'nonces'                       => array(
					'keep'             => wp_create_nonce( 'wpbbe_keep_demo_content' ),
					'import'           => wp_create_nonce( 'wpbbe_import_demo' ),
					'remove'           => wp_create_nonce( 'wpbbe_remove_demo' ),
					'install_bb_theme' => wp_create_nonce( 'updates' ),
				),
				'better_block_theme_installed' => (bool) ( get_template() === 'better-block-theme' ),
				'admin_url'                    => self::get_admin_url(),
			)
		);
	}

	/**
	 * Get the URL for the admin page of this module.
	 *
	 * @return string The URL for the admin page.
	 */
	public static function get_admin_url() {
		return admin_url( 'admin.php?page=' . self::MENU_PAGE_SLUG );
	}

	/**
	 * Get the URL for step 2 of the demo content setup.
	 *
	 * @return string The URL for step 2.
	 */
	public static function get_admin_step2_url() {
		return self::get_admin_url() . '&step=2';
	}

	/**
	 * Displays success notices on the module admin page when needed.
	 *
	 * @return void
	 */
	public function maybe_show_a_notice() {
		// Only on our plugin page.
		if ( ! get_current_screen() || get_current_screen()->id !== $this->page_hook ) {
			return;
		}

		$notices = array(
			'keep'   => __( 'Website template was kept successfully.', 'better-block-editor' ),
			'remove' => __( 'Website template was removed successfully.', 'better-block-editor' ),
		);

		foreach ( $notices as $key => $message ) {
			$transient_key = self::MENU_PAGE_SLUG . '_' . $key . '_content_notice';
			if ( get_transient( $transient_key ) ) {
				delete_transient( $transient_key );
				wp_admin_notice(
					$message,
					array(
						'type'        => 'success',
						'dismissible' => true,
					)
				);
			}
		}
	}
}
