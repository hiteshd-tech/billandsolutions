<?php
/**
 * Dashboard admin page.
 *
 * @package The7\Admin\Pages
 */

namespace The7\Admin\Pages;

defined( 'ABSPATH' ) || exit;

class Dashboard extends Admin_Page {

	protected $action;

	public function __construct() {
		$this->slug       = 'the7-dashboard';
		$this->title      = __( 'My The7', 'the7mk2' );
		$this->capability = 'switch_themes';
	}

	public function enqueue_styles() {
		the7_register_style( 'the7-dashboard-icons', PRESSCORE_ADMIN_URI . '/assets/fonts/the7-dashboard-icons/the7-dashboard-icons.css' );
		wp_enqueue_style( 'the7-dashboard-icons' );

		if ( $this->action === 'demo_import' ) {
			// Hide all notices except about elementor pro on demo import action screen.
			wp_add_inline_style(
				'the7-dashboard',
				'
				.notice:not(#elementor-pro-notice) {
					display: none !important;
				}
				'
			);
		}
	}

	public function enqueue_scripts() {
		\The7\Mods\Compatibility\Gutenberg\Block_Theme\The7_FSE_Font_Manager::instance()->admin_enqueue_scripts();
	}

	/**
	 * Render page content.
	 */
	public function render() {
		if ( $this->action === 'demo_import' ) {
			$this->render_demo_import_page();
		} else {
			presscore_get_template_part( 'the7_admin', 'dashboard' );
		}
	}

	protected function render_demo_import_page() {
		if ( ! current_user_can( $this->capability ) || ! check_admin_referer( 'the7_import_demo' ) ) {
			return;
		}

		$import_type = sanitize_text_field( wp_unslash( $_POST['import_type'] ?? '' ) );

		$actions_builder = the7_demo_content()->admin->get_actions_builder(
			$import_type,
			[
				'demo_id'       => sanitize_text_field( wp_unslash( $_POST['demo_id'] ?? '' ) ),
				'the7_post_url' => esc_url_raw( wp_unslash( $_POST['the7_post_url'] ?? '' ) ),
			]
		);
		$actions_builder->localize_data_to_js();

		$error = $actions_builder->get_error();
		if ( empty( $error ) ) {
			$demo          = $actions_builder->get_demo();
			$template_vars = [
				'starting_text'         => $actions_builder->get_starting_text(),
				'require_elementor_pro' => $demo->require_elementor_pro() && ! $demo->import_allowed(),
				'admin_page_url'        => admin_url( 'admin.php?page=the7-dashboard' ),
			];
		} else {
			$template_vars = [
				'error' => $error,
			];
		}

		presscore_get_template_part(
			'the7_admin',
			'partials/dashboard/import-action-screen',
			null,
			$template_vars
		);
	}

	protected function on_after_add_menu_page( $hook_suffix ) {
		// Demo content.
		if ( function_exists( 'the7_demo_content' ) ) {
			the7_demo_content()->setup_admin_page_hooks( $hook_suffix );
		}

		// Theme registration.
		if ( class_exists( \Presscore_Modules_ThemeUpdateModule::class ) ) {
			\Presscore_Modules_ThemeUpdateModule::setup_hooks( $hook_suffix );
		}

		add_action(
			'load-' . $hook_suffix,
			function () {
				if ( isset( $_GET['action'] ) && $_GET['action'] === 'demo_import' ) {
					if ( ! isset( $_POST['import_type'] ) || ! check_admin_referer( 'the7_import_demo' ) ) {
						// Redirect early to prevent confusion.
						wp_safe_redirect( admin_url( 'admin.php?page=the7-dashboard' ) );
						exit();
					}

					$this->action = 'demo_import';
				}
			}
		);

		parent::on_after_add_menu_page( $hook_suffix );
	}
}
