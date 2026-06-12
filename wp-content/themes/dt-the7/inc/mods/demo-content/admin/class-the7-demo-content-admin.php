<?php
/**
 * @package The7
 */

use The7\Admin\Notices\Import_Failed;
use The7\Admin\Notices\Import_Succeed;

defined( 'ABSPATH' ) || exit;

/**
 * The dashboard-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    DT_Dummy
 * @subpackage DT_Dummy/admin
 * @author     Dream-Theme
 */
class The7_Demo_Content_Admin {

	const REQUIRED_USER_CAPABILITY = 'edit_theme_options';

	/**
	 * Register scripts.
	 *
	 * @since 7.0.0
	 */
	public function register_scripts() {
		the7_register_style( 'the7-demo-content', PRESSCORE_ADMIN_URI . '/assets/css/demo-content' );

		the7_register_script( 'the7-demo-content', PRESSCORE_ADMIN_URI . '/assets/js/demo-content', [ 'jquery', 'jquery-ui-progressbar' ], false, true );
	}

	/**
	 * Register the stylesheets for the Dashboard.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( 'the7-demo-content' );
	}

	/**
	 * Register the JavaScript for the dashboard.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		global $the7_tgmpa, $typenow;

		wp_enqueue_script( 'the7-demo-content' );

		$plugins          = [];
		$plugins_page_url = '';
		if ( class_exists( 'Presscore_Modules_TGMPAModule' ) ) {
			$plugins = Presscore_Modules_TGMPAModule::get_plugins_list_cache();
			$plugins = wp_list_pluck( $plugins, 'name', 'slug' );

			if ( ! $the7_tgmpa->is_tgmpa_complete() ) {
				$plugins_page_url = $the7_tgmpa->get_bulk_action_link();
			}
		}

		$post_type_object = get_post_type_object( $typenow ? $typenow : 'post' );

		$strings = [
			'keep_content_confirm'                   => esc_html_x(
				'If you choose to keep the demo, you will no longer be able to bulk remove it.  Do you wish to continue?',
				'admin',
				'the7mk2'
			),
			'remove_content_confirm'                 => esc_html_x(
				'Attention! This action will remove all the demo content, including pages, posts, images, menus, theme options, etc. Do you wish to continue?',
				'admin',
				'the7mk2'
			),
			'btn_import'                             => esc_html_x( 'Importing...', 'admin', 'the7mk2' ),
			'msg_import_success'                     => esc_html_x(
				'Demo content successfully imported.',
				'admin',
				'the7mk2'
			),
			'msg_import_fail'                        => esc_html_x( 'Import Fail!', 'admin', 'the7mk2' ),
			'setup_rewrite_rules'                    => esc_html_x( 'Setup Rewrite Rules', 'admin', 'the7mk2' ),
			'download_package'                       => esc_html_x( 'Downloading package.', 'admin', 'the7mk2' ),
			'import_the7_dashboard_settings'         => esc_html_x(
				'Importing The7 dashboard settings.',
				'admin',
				'the7mk2'
			),
			'add_the7_dashboard_settings'            => esc_html_x(
				'Adding The7 dashboard settings.',
				'admin',
				'the7mk2'
			),
			'import_post_types'                      => esc_html_x( 'Importing content.', 'admin', 'the7mk2' ),
			'import_post_types_builder_data'         => esc_html_x(
				'Importing The7 Post Type Builder data.',
				'admin',
				'the7mk2'
			),
			'import_post_types_plugin_data'          => esc_html_x(
				'Importing The7 Post Types plugin data.',
				'admin',
				'the7mk2'
			),
			'import_attachments'                     => esc_html_x( 'Importing attachments.', 'admin', 'the7mk2' ),
			'import_theme_options'                   => esc_html_x( 'Importing theme options.', 'admin', 'the7mk2' ),
			'import_elementor_settings_for_one_page' => esc_html_x(
				'Importing Elementor settings.',
				'admin',
				'the7mk2'
			),
			'import_site_logo'                       => esc_html_x( 'Importing Site logo.', 'admin', 'the7mk2' ),
			'process_block_theme_data'               => esc_html_x( 'Process Imported data.', 'admin', 'the7mk2' ),
			'import_rev_sliders'                     => esc_html_x( 'Importing slider(s).', 'admin', 'the7mk2' ),
			'cleanup'                                => esc_html_x( 'Final cleanup.', 'admin', 'the7mk2' ),
			'installing_plugin'                      => esc_html_x( 'Installing', 'admin', 'the7mk2' ),
			'activating_plugin'                      => esc_html_x( 'Activating plugin(s)', 'admin', 'the7mk2' ),
			'plugins_activated'                      => esc_html_x(
				'Plugin(s) activated successfully.',
				'admin',
				'the7mk2'
			),
			'download_fse_fonts'                     => esc_html_x( 'Downloading fonts.', 'admin', 'the7mk2' ),
			'import_by_url'                          => esc_html_x( 'Importing post.', 'admin', 'the7mk2' ),
			'import_one_post'                        => esc_html_x( 'Importing post.', 'admin', 'the7mk2' ),
			'plugins_installation_error'             => esc_html_x( 'Server error.', 'admin', 'the7mk2' ),
			'rid_of_redirects'                       => esc_html_x(
				'Cleanup after plugins installation.',
				'admin',
				'the7mk2'
			),
			'clear_importer_session'                 => esc_html_x( 'Clear importer session.', 'admin', 'the7mk2' ),
			'get_posts'                              => esc_html_x( 'Parsing content.', 'admin', 'the7mk2' ),
			'loading'                                => esc_html_x( 'Loading...', 'admin', 'the7mk2' ),
			'remove_content'                         => esc_html_x( 'Removing content...', 'admin', 'the7mk2' ),
			'keep_content'                           => esc_html_x( 'Keeping content...', 'admin', 'the7mk2' ),
			'one_post_importing_msg'                 => esc_html_x( 'Importing', 'admin', 'the7mk2' ),
			'one_post_importing_choose_post_type'    => esc_html_x( 'Choose post type', 'admin', 'the7mk2' ),
			'one_post_importing_choose_post'         => esc_html_x( 'Choose post', 'admin', 'the7mk2' ),
			'one_post_importing_import'              => esc_html_x( 'Import post', 'admin', 'the7mk2' ),
			'one_post_importing_url_msg'             => esc_html_x( 'example', 'admin', 'the7mk2' ),
			'one_post_importing_success'             => esc_html_x(
				'Demo page successfully imported.',
				'admin',
				'the7mk2'
			),
			'cannot_found_page_by_url_error'         => esc_html(
				sprintf( // translators: %s: post type name.
					_x( '%%url%% is not a %s or does not exist.', 'admin', 'the7mk2' ),
					strtolower( $post_type_object->labels->singular_name )
				)
			),
			'cannot_get_posts_list_error'            => esc_html_x(
				'Cannot get posts lists from package.',
				'admin',
				'the7mk2'
			),
			'invalid_url_error'                      => sprintf( // translators: %s: admin demo page link.
				esc_html_x(
					'Provided URL (link) is not valid. Please copy a valid URL (link) from one of %s.',
					'admin',
					'the7mk2'
				),
				'<a href="https://the7.io/#!/demos" target="_blank">The7 pre-made websites</a>'
			),
			'action_error'                           => esc_html_x(
				'Error. Cannot complete following action',
				'admin',
				'the7mk2'
			),
			'go_back_with_error'                     => sprintf(
				'<a href="' . esc_url( admin_url( 'admin.php?page=the7-dashboard' ) ) . '">' . esc_html_x(
					'Back to The7 Dashboard',
					'admin',
					'the7mk2'
				) . '</a>'
			),
			// translators: %s: website template name.
			'keep_confirm'                           => esc_html_x(
				'Do you want to keep %s website template? It will no longer be removable in bulk.',
				'admin',
				'the7mk2'
			),
			// translators: %s: website template name.
			'remove_confirm'                         => esc_html_x(
				'Do you want to remove %s website template? This will delete all related template content.',
				'admin',
				'the7mk2'
			),
		];

		wp_localize_script(
			'the7-demo-content',
			'dtDummy',
			[
				'nonces'             => [
					'keep_demo_content' => wp_create_nonce( 'the7_keep_demo_content' ),
					'import_nonce'      => wp_create_nonce( 'the7_import_demo' ),
					'remove_demo_nonce' => wp_create_nonce( 'the7_remove_demo' ),
					'status_nonce'      => wp_create_nonce( 'the7_php_ini_status' ),
				],
				'plugins'            => $plugins,
				'plugins_page_url'   => $plugins_page_url,
				'strings'            => $strings,
				'dashboard_page_url' => admin_url( 'admin.php?page=the7-dashboard' ),
			]
		);
	}

	/**
	 * @since 1.0.0
	 */
	public function ajax_import_demo_content() {
		if ( ! check_ajax_referer( 'the7_import_demo', false, false ) || ! current_user_can( static::REQUIRED_USER_CAPABILITY ) ) {
			wp_send_json_error( [ 'error_msg' => '<p>' . esc_html_x( 'Insufficient user rights.', 'admin', 'the7mk2' ) . '</p>' ] );
		}

		if ( empty( $_POST['dummy'] ) ) {
			wp_send_json_error( [ 'error_msg' => '<p>' . esc_html_x( 'Unable to find dummy content.', 'admin', 'the7mk2' ) . '</p>' ] );
		}

		wp_raise_memory_limit( 'admin' );
		if ( (int) ini_get( 'max_execution_time' ) < 300 ) {
			the7_set_time_limit( 300 );
		}

		$import_type = isset( $_POST['import_type'] ) ? $_POST['import_type'] : 'full_import';
		$demo_id     = isset( $_POST['content_part_id'] ) ? sanitize_key( $_POST['content_part_id'] ) : '';

		if ( $import_type === 'full_import' ) {
			$content_tracker = new The7_Demo_Content_Tracker( $demo_id );
		} else {
			$content_tracker = new The7_Demo_Null_Tracker( $demo_id );
		}

		$wp_uploads         = wp_get_upload_dir();
		$import_content_dir = trailingslashit( $wp_uploads['basedir'] ) . "the7-demo-content-tmp/{$demo_id}";
		$import_manager     = new The7_Demo_Content_Import_Manager( $import_content_dir, the7_demo_content()->get_raw_demo( $demo_id ), $content_tracker );

		do_action( 'the7_demo_content_before_content_import', $import_manager );

		if ( $import_manager->has_errors() ) {
			wp_send_json_error( [ 'error_msg' => $import_manager->get_errors_string() ] );
		}

		$demo = the7_demo_content()->get_demo( $demo_id );
		if ( ! $demo ) {
			wp_send_json_error( [ 'error_msg' => '<p>' . esc_html_x( 'Unable to recognise demo.', 'admin', 'the7mk2' ) . '</p>' ] );
		}

		$retval = null;

		switch ( $_POST['dummy'] ) {
			case 'download_package':
				$source = isset( $_POST['demo_page_url'] ) ? $_POST['demo_page_url'] : '';
				$import_manager->download_dummy( $source );
				break;

			case 'clear_importer_session':
				\The7_Content_Importer::clear_session();
				break;

			case 'setup_rewrite_rules':
				$permalink_structure = '/%year%/%monthnum%/%day%/%postname%/';
				$permalink_structure = sanitize_option( 'permalink_structure', $permalink_structure );

				global $wp_rewrite;
				$wp_rewrite->set_permalink_structure( $permalink_structure );
				break;

			case 'import_the7_dashboard_settings':
				// Must be launched in a separate process to make a difference.
				$import_manager->import_the7_dashboard_settings();

				if ( $demo->is_fse() ) {
					$fse_importer = new The7_FSE_Importer( $import_manager->importer(), $import_manager->tracker() );
					$fse_importer->import_fse_version( $import_manager->get_site_meta() );
					$fse_importer->import_better_block_editor_settings( $import_manager->get_site_meta() );
					\The7\Mods\Compatibility\Gutenberg\Block_Theme\The7_FSE_Font_Manager::instance()->reset_fonts_to_download();
				}
				break;

			case 'add_the7_dashboard_settings':
				$import_manager->add_the7_dashboard_settings();
				break;

			case 'import_post_types':
				$import_manager->import_post_types();
				$import_manager->import_wp_settings();
				$import_manager->import_vc_settings();
				$import_manager->import_the7_fontawesome();

				$content_tracker->add( 'post_types', true );
				break;

			case 'import_post_types_plugin_data':
				$data = (array) $import_manager->get_site_meta( 'the7_core_post_types_builder' );

				( new \The7_Post_Types_Plugin_Importer( $import_manager->tracker() ) )->import( $data );
				break;

			case 'import_post_types_builder_data':
				$import_manager->import_the7_core_post_types_builder_data();
				break;

			case 'import_attachments':
				$content_tracker->track_imported_items();

				// In case it's an one post installation.
				$imported_post_id = isset( $_POST['imported_post_id'] ) ? (int) $_POST['imported_post_id'] : 0;
				if ( $imported_post_id ) {
					$import_manager->importer()->add_filter_by_id( $imported_post_id );
				}

				$retval = $import_manager->import_attachments( $demo->include_attachments, $demo->attachments_batch );

				if ( isset( $retval['imported'] ) ) {
					$content_tracker->add( 'attachments_in_process', $retval['imported'] );
				}

				if ( isset( $retval['left'] ) && $retval['left'] === 0 ) {
					$content_tracker->add( 'attachments', $demo->include_attachments ? 'original' : 'placeholders' );
					$content_tracker->remove( 'attachments_imported' );
				}
				break;

			case 'import_theme_options':
				$import_manager->importer()->read_processed_data_from_cache();
				$import_manager->import_theme_option();
				$import_manager->import_ultimate_addons_settings();

				if ( the7_elementor_is_active() ) {
					$import_manager->import_elementor_settings();
				}

				if ( defined( 'TINVWL_FVERSION' ) ) {
					$import_manager->import_tinvwl_settings();
				}
				break;

			case 'import_elementor_settings_for_one_page':
				if ( the7_is_elementor3() ) {
					$elementor_importer = new \The7_Elementor_Importer( $import_manager->importer(), $import_manager->tracker() );

					$elementor_kit_settings = $import_manager->get_site_meta( 'elementor_kit_settings' );
					if ( isset( $elementor_kit_settings['custom_colors'] ) ) {
						$elementor_importer->append_kit_custom_colors( $elementor_kit_settings['custom_colors'] );
					}

					$import_manager->importer()->read_processed_data_from_cache();
					$wp_settings_importer = new The7_WP_Settings_Importer( $import_manager->importer(), $import_manager->tracker() );

					$site_identity = $import_manager->get_site_meta( 'site_identity' );

					// Maybe import custom logo.
					if ( ! empty( $site_identity['custom_logo'] ) && ! get_theme_mod( 'custom_logo' ) ) {
						$wp_settings_importer->import_custom_logo(
							$import_manager->importer()->get_processed_post( (int) $site_identity['custom_logo'] )
						);
					}

					// Maybe import site icon.
					if ( ! empty( $site_identity['site_icon'] ) && ! get_option( 'site_icon' ) ) {
						$wp_settings_importer->import_site_icon(
							$import_manager->importer()->get_processed_post( (int) $site_identity['site_icon'] )
						);
					}
				}
				break;

			case 'import_site_logo':
				$import_manager->importer()->read_processed_data_from_cache();
				$wp_settings_importer = new The7_WP_Settings_Importer( $import_manager->importer(), $import_manager->tracker() );

				$site_identity = $import_manager->get_site_meta( 'site_identity' );

				if ( ! empty( $site_identity['custom_logo'] ) ) {
					$wp_settings_importer->import_custom_logo(
						$import_manager->importer()->get_processed_post( (int) $site_identity['custom_logo'] )
					);
				}

				if ( ! empty( $site_identity['site_icon'] ) ) {
					$wp_settings_importer->import_site_icon(
						$import_manager->importer()->get_processed_post( (int) $site_identity['site_icon'] )
					);
				}
				break;

			case 'process_block_theme_data':
				$import_manager->importer()->read_processed_data_from_cache();
				$fse_importer = new The7_FSE_Importer( $import_manager->importer(), $import_manager->tracker() );
				$fse_importer->remap_post_ids_and_urls_in_blocks();
				break;

			case 'import_rev_sliders':
				$imported_sliders = $import_manager->import_rev_sliders();
				if ( $import_type === 'full_import' ) {
					$content_tracker->add( 'rev_sliders', $imported_sliders );
				}
				break;

			case 'get_posts':
				$post_types_white_list = [
					'page',
					'post',
					'product',
					'dt_portfolio',
					'dt_testimonials',
					'dt_gallery',
					'dt_team',
					'dt_slideshow',
				];

				$post_types_builder_data = $import_manager->get_site_meta( 'the7_core_post_types_builder' );
				if ( ! empty( $post_types_builder_data['post_types'] ) ) {
					$post_types_white_list += wp_list_pluck( (array) $post_types_builder_data['post_types'], 'name' );
				}

				$retval = $import_manager->get_posts_list( $post_types_white_list );

				if ( is_array( $retval ) && ! $demo->plugins()->is_plugins_active() ) {
					$retval['plugins_to_install']  = array_keys( $demo->plugins()->get_plugins_to_install() );
					$retval['plugins_to_activate'] = array_keys( $demo->plugins()->get_inactive_plugins() );
				}
				break;

			case 'import_one_post':
				$post_id = 0;
				if ( ! empty( $_POST['post_to_import'] ) ) {
					$post_id = $import_manager->import_one_post( (int) $_POST['post_to_import'] );
				}

				$retval = [
					'postPermalink'     => get_permalink( $post_id ),
					'postEditLink'      => get_edit_post_link( $post_id, 'return' ),
					'step2Links'        => $this->get_step_2_post_links( $post_id ),
					'postImportActions' => $this->determine_post_import_actions( $post_id, $demo ),
					'imported_post_id'  => $import_manager->importer()->get_filtered_post_id(),
				];
				break;

			case 'import_by_url':
				if ( ! isset( $_POST['provided_url'] ) ) {
					$import_manager->add_error( esc_html_x( 'Cannot import because no url provided.', 'admin', 'the7mk2' ) );
				}

				$post_id = $import_manager->import_one_post_by_url( (string) $_POST['provided_url'] );
				if ( ! $post_id ) {
					$import_manager->add_error( esc_html_x( 'Cannot find the post with provided url.', 'admin', 'the7mk2' ) );
				}

				$retval = [
					'postPermalink'     => get_permalink( $post_id ),
					'postEditLink'      => get_edit_post_link( $post_id, 'return' ),
					'step2Links'        => $this->get_step_2_post_links( $post_id ),
					'postImportActions' => $this->determine_post_import_actions( $post_id, $demo ),
					'imported_post_id'  => $import_manager->importer()->get_filtered_post_id(),
				];
				break;

			case 'cleanup':
				\The7_Content_Importer::clear_session();
				$import_manager->cleanup_temp_dir();
				flush_rewrite_rules();
				$retval = [
					'status' => $demo->get_import_status_text(),
				];
				break;
		}

		do_action( 'the7_demo_content_after_content_import', $import_manager );

		if ( $import_manager->has_errors() ) {
			the7_admin_notices()->display_once( Import_Failed::NOTICE_CODE );
			wp_send_json_error( [ 'error_msg' => $import_manager->get_errors_string() ] );
		}

		the7_admin_notices()->display_once( Import_Succeed::NOTICE_CODE );
		wp_send_json_success( $retval );
	}

	/**
	 * @return void
	 */
	public function ajax_remove_demo_content() {
		if ( ! check_admin_referer( 'the7_remove_demo' ) ) {
			wp_send_json_error();
		}

		if ( ! current_user_can( static::REQUIRED_USER_CAPABILITY ) ) {
			wp_send_json_error();
		}

		$demo = the7_demo_content()->get_demo( isset( $_POST['demo'] ) ? $_POST['demo'] : null );

		if ( ! $demo ) {
			wp_send_json_error();
		}

		$demo_to_remove         = $demo->id;
		$rollback_site_settings = true;
		$history                = get_option( The7_Demo_Content_Tracker::HISTORY_OPTION_ID, [] );
		if ( count( $history ) > 1 ) {
			$history = array_reverse( $history );
			reset( $history );
			$latest_installed_demo_id = key( $history );

			if ( $latest_installed_demo_id !== $demo_to_remove ) {
				$rollback_site_settings = false;
			}
		}

		$content_tracker = new The7_Demo_Content_Tracker( $demo_to_remove );
		$demo_remover    = new The7_Demo_Remover( $content_tracker );

		if ( $content_tracker->get( 'post_types' ) || $content_tracker->get( 'attachments' ) ) {
			$demo_remover->remove_content();
		}

		if ( $rollback_site_settings ) {
			$demo_remover->revert_site_settings();

			if ( $content_tracker->get( 'theme_options' ) ) {
				$demo_remover->remove_theme_options();
			}
		}

		if ( $content_tracker->get( 'rev_sliders' ) ) {
			$demo_remover->remove_rev_sliders();
		}

		$content_tracker->remove_demo();

		// Since we changed content tracker history.
		$demo->refresh_import_status();

		wp_send_json_success(
			[
				'status' => $demo->get_import_status_text( $content_tracker ),
			]
		);
	}

	/**
	 * @return void
	 */
	public function ajax_keep_demo_content() {
		if ( ! check_admin_referer( 'the7_keep_demo_content' ) ) {
			wp_send_json_error();
		}

		if ( ! current_user_can( static::REQUIRED_USER_CAPABILITY ) ) {
			wp_send_json_error();
		}

		$demo = the7_demo_content()->get_demo( isset( $_POST['demo'] ) ? $_POST['demo'] : null );

		if ( ! $demo ) {
			wp_send_json_error();
		}

		$content_tracker = new The7_Demo_Content_Tracker( $demo->id );
		$content_tracker->keep_demo_content();

		// Since we changed content tracker history.
		$demo->refresh_import_status();

		wp_send_json_success(
			[
				'status' => $demo->get_import_status_text(),
			]
		);
	}

	/**
	 * Check if php.ini have proper params values. Ajax response.
	 */
	public function ajax_get_php_ini_status() {
		if ( ! check_ajax_referer( 'the7_php_ini_status', false, false ) || ! current_user_can( static::REQUIRED_USER_CAPABILITY ) ) {
			wp_send_json_error();
		}

		ob_start();
		include PRESSCORE_ADMIN_DIR . '/screens/partials/dashboard/import-php-error-notice.php';
		$status = ob_get_clean();

		wp_send_json_success( $status );
	}

	/**
	 * @param string $import_type Import type.
	 * @param array  $external_data Data array.
	 *
	 * @return The7_Demo_Actions_Builder_Base
	 */
	public function get_actions_builder( $import_type, $external_data = [] ) {
		if ( $import_type ) {
			$class_name = implode( '_', array_map( 'ucfirst', explode( '_', $import_type ) ) );
			$class_name = "The7_Demo_{$class_name}_Actions_Builder";
			if ( class_exists( $class_name ) ) {
				return new $class_name( $external_data );
			}
		}

		return new The7_Demo_Null_Actions_Builder();
	}

	/**
	 * Determine post-import actions based on provided post id.
	 *
	 * @since 7.0.0
	 *
	 * @param int            $post_id Post ID.
	 * @param The7_Demo|null $demo Demo object.
	 *
	 * @return array
	 */
	protected function determine_post_import_actions( $post_id, $demo = null ) {
		$post = get_post( $post_id );

		if ( ! $post ) {
			return [ 'cleanup' ];
		}

		$actions = [
			'import_attachments',
		];

		// Check the revslider mention in the shortcodes.
		if ( preg_match( '/' . get_shortcode_regex( [ 'rev_slider_vc', 'rev_slider' ] ) . '/', $post->post_content ) ) {
			$actions[] = 'import_rev_sliders';
		}

		// Check the revslider mention in the meta fields.
		if (
			get_post_meta( $post_id, '_dt_header_title', true ) === 'slideshow'
			&& get_post_meta( $post_id, '_dt_slideshow_mode', true ) === 'revolution'
		) {
			$actions[] = 'import_rev_sliders';
		}

		// Append Kit settings if page was built with Elementor. The result is a more accurate coloration.
		if ( get_post_meta( $post_id, '_elementor_edit_mode', true ) === 'builder' ) {
			$actions[] = 'import_elementor_settings_for_one_page';
		}

		if ( $demo && $demo instanceof The7_Demo && $demo->is_fse() ) {
			$actions[] = 'process_block_theme_data';
		}

		$actions[] = 'cleanup';

		return array_unique( $actions );
	}

	/**
	 * @param int $post_id Post ID.
	 *
	 * @return string
	 */
	protected function get_step_2_post_links( $post_id ) {
		$output  = '<a href="' . esc_url( get_permalink( $post_id ) ) . '">' . esc_html_x( 'See post', 'admin', 'the7mk2' ) . '</a>';
		$output .= ' | ';
		$output .= '<a href="' . esc_url( get_edit_post_link( $post_id, 'return' ) ) . '">' . esc_html_x( 'Edit post', 'admin', 'the7mk2' ) . '</a>';

		return $output;
	}
}
