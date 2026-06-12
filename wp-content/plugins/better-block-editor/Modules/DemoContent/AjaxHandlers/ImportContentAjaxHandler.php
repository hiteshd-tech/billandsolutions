<?php
/**
 * AJAX handler for demo content import actions.
 *
 * @package BetterBlockEditor
 */

namespace BetterBlockEditor\Modules\DemoContent\AjaxHandlers;

use BetterBlockEditor\Modules\DemoContent\Demo\Factory as DemoFactory;
use BetterBlockEditor\Modules\DemoContent\Demo\Demo;
use BetterBlockEditor\Modules\DemoContent\Module;
use BetterBlockEditor\Modules\DemoContent\RemoteAPI\Demo as RemoteApiDemo;
use BetterBlockEditor\Modules\DemoContent\Importers\ContentImporter;
use BetterBlockEditor\Modules\DemoContent\Importers\WPSettingsImporter;
use BetterBlockEditor\Modules\DemoContent\Importers\FSEImporter;
use BetterBlockEditor\Modules\DemoContent\Trackers\ContentTracker;
use BetterBlockEditor\Core\Settings;

defined( 'ABSPATH' ) || exit;

/**
 * Coordinates AJAX endpoints for importing demo content.
 */
class ImportContentAjaxHandler extends AjaxHandlerBase {

	/**
	 * Registers AJAX hooks for demo import handling.
	 *
	 * @return void
	 */
	public static function register() {
		add_action( 'wp_ajax_wpbbe_import_demo', array( self::class, 'ajax_import_demo' ) );
	}

	/**
	 * Entry point for demo import AJAX requests.
	 *
	 * Validates nonce, capability, and dispatches sub-actions.
	 *
	 * @return void
	 */
	public static function ajax_import_demo() {
		if ( ! check_ajax_referer( 'wpbbe_import_demo', false, false ) || ! current_user_can( Module::CAPABILITY ) ) {
			wp_send_json_error( array( 'error_msg' => '<p>' . esc_html__( 'Insufficient user rights.', 'better-block-editor' ) . '</p>' ) );
		}

		if ( empty( $_POST['sub_action'] ) ) {
			wp_send_json_error( array( 'error_msg' => '<p>' . esc_html__( 'Unable to find demo.', 'better-block-editor' ) . '</p>' ) );
		}

		wp_raise_memory_limit( 'admin' );

		$demo_id = isset( $_POST['content_part_id'] ) ? sanitize_key( $_POST['content_part_id'] ) : '';

		$demo = DemoFactory::create( $demo_id );
		if ( ! $demo ) {
			wp_send_json_error( array( 'error_msg' => '<p>' . esc_html__( 'Unable to recognise demo.', 'better-block-editor' ) . '</p>' ) );
		}

		$retval = null;

		$sub_action = isset( $_POST['sub_action'] ) ? sanitize_key( wp_unslash( $_POST['sub_action'] ) ) : '';
		$method     = $sub_action ? 'sub_action_' . $sub_action : '';
		if ( $method && method_exists( self::class, $method ) ) {
			$content_tracker = new ContentTracker( $demo_id );

			$retval = \call_user_func( array( self::class, $method ), $demo, $content_tracker );
		} else {
			wp_send_json_error( array( 'error_msg' => '<p>' . esc_html__( 'Unknown action.', 'better-block-editor' ) . '</p>' ) );
		}

		wp_send_json_success( $retval );
	}

	/**
	 * Downloads the selected demo package into the uploads directory.
	 *
	 * @param Demo           $demo Demo instance being imported.
	 * @param ContentTracker $content_tracker Content tracker for the demo.
	 * @return string|false Absolute path to the package directory or false on failure.
	 */
	private static function sub_action_download_package( $demo, $content_tracker ) {
		$import_content_dir = $demo->get_demo_uploads_dir();
		$item               = basename( $import_content_dir );
		$download_dir       = dirname( $import_content_dir );
		$download_response  = RemoteApiDemo::download_demo( $item, $download_dir );

		if ( is_wp_error( $download_response ) ) {
			return false;
		}

		return trailingslashit( $download_response );
	}

	/**
	 * Enables the Design System module when available.
	 *
	 * @param Demo           $demo Demo instance being imported.
	 * @param ContentTracker $content_tracker Content tracker for the demo.
	 * @return null Always returns null for JSON success payload.
	 */
	private static function sub_action_turn_on_design_system( $demo, $content_tracker ) {
		$design_system_module = '\BetterBlockEditor\Modules\DesignSystemParts\Module';
		if ( class_exists( $design_system_module ) ) {
			$module_identifier = constant( $design_system_module . '::MODULE_IDENTIFIER' );
			update_option( WPBBE_PLUGIN_ID . '__module__' . $module_identifier . '__enabled', true );
			$option_name = Settings::build_module_option_name( $module_identifier );
			$value       = array(
				'active-parts' => array(
					'color'      => 1,
					'typography' => 1,
				),
			);

			update_option( $option_name, $value );
		}

		return null;
	}

	/**
	 * Clears the content importer session cache.
	 *
	 * @param Demo           $demo Demo instance being imported.
	 * @param ContentTracker $content_tracker Content tracker for the demo.
	 * @return null Always returns null.
	 */
	private static function sub_action_clear_importer_session( $demo, $content_tracker ) {
		ContentImporter::clear_session();
		return null;
	}

	/**
	 * Configures permalink structure required for demo content.
	 *
	 * @param Demo           $demo Demo instance being imported.
	 * @param ContentTracker $content_tracker Content tracker for the demo.
	 * @return null Always returns null.
	 */
	private static function sub_action_setup_rewrite_rules( $demo, $content_tracker ) {
		$permalink_structure = '/%year%/%monthnum%/%day%/%postname%/';
		$permalink_structure = sanitize_option( 'permalink_structure', $permalink_structure );

		global $wp_rewrite;
		$wp_rewrite->set_permalink_structure( $permalink_structure );
		return null;
	}

	/**
	 * Imports posts, pages, and related settings from the demo XML file.
	 *
	 * @param Demo           $demo Demo instance being imported.
	 * @param ContentTracker $content_tracker Content tracker for the demo.
	 * @return null|false Null on success, false when prerequisites are missing.
	 */
	private static function sub_action_import_post_types( Demo $demo, $content_tracker ) {
		$file_to_import = $demo->get_import_xml_file();

		if ( ! is_file( $file_to_import ) ) {
			return false;
		}

		if ( $content_tracker ) {
			$content_tracker->track_imported_items();
		}

		$menus = wp_get_nav_menus();
		if ( ! empty( $menus ) ) {
			foreach ( $menus as $menu ) {
				$updated = false;
				$i       = 0;

				while ( ! is_numeric( $updated ) ) {
					++$i;
					$args['menu-name']   = __( 'Previously used menu', 'better-block-editor' ) . ' ' . $i;
					$args['description'] = $menu->description;
					$args['parent']      = $menu->parent;

					$updated = wp_update_nav_menu_object( $menu->term_id, $args );

					if ( $i > 100 ) {
						$updated = 1;
					}
				}
			}
		}

		$importer = new ContentImporter();
		$importer->log_reset();

		$demo_title = isset( $demo->title ) ? $demo->title : 'Demo';
		$importer->log_add( "Importing {$demo_title}\n" );

		$start = microtime( true );

		$fse_importer = new FSEImporter( $importer, $content_tracker );
		$fse_importer->do_before_importing_content();

		$importer->fetch_attachments = false;
		$importer->import( $file_to_import );

		$importer->log_add( 'Content was imported in: ' . ( microtime( true ) - $start ) . "\n" );

		$import_meta = $demo->get_import_meta();

		$importer->log_add( 'WP settings importing...' );

		$wp_settings_importer = new WPSettingsImporter( $importer, $content_tracker );

		if ( ! empty( $import_meta['wp_settings'] ) ) {
			$wp_settings_importer->import_settings( $import_meta['wp_settings'] );

			$importer->log_add( 'WP settings were imported.' );
		}

		if ( ! empty( $import_meta['nav_menu_locations'] ) ) {
			$wp_settings_importer->import_menu_locations( $import_meta['nav_menu_locations'] );

			$importer->log_add( 'Menu locations were imported.' );
		}

		if ( ! empty( $import_meta['widgets_settings'] ) ) {
			$wp_settings_importer->import_widgets( $import_meta['widgets_settings'] );

			$importer->log_add( 'Widgets were imported.' );
		}

		$importer->log_add( 'Done' );

		if ( $content_tracker && is_object( $content_tracker ) ) {
			$content_tracker->add( 'post_types', true );
		}

		return null;
	}

	/**
	 * Imports media attachments incrementally.
	 *
	 * @param Demo           $demo Demo instance being imported.
	 * @param ContentTracker $content_tracker Content tracker for the demo.
	 * @return array|false Import status data array or false on failure.
	 */
	private static function sub_action_import_attachments( $demo, $content_tracker ) {
		$file_to_import = $demo->get_import_xml_file();

		if ( ! is_file( $file_to_import ) ) {
			return false;
		}

		if ( $content_tracker && is_object( $content_tracker ) && method_exists( $content_tracker, 'track_imported_items' ) ) {
			$content_tracker->track_imported_items();
		}

		add_filter( 'wp_import_tags', '__return_empty_array' );
		add_filter( 'wp_import_categories', '__return_empty_array' );
		add_filter( 'wp_import_terms', '__return_empty_array' );

		$importer = new ContentImporter();
		$importer->read_processed_data_from_cache();
		$retval = $importer->import_batch( $file_to_import, (int) $demo->attachments_batch );

		$widgets = get_option( 'widget_text', array() );
		if ( $widgets ) {
			$widgets_str = wp_json_encode( $widgets );

			$url_remap = $importer->url_remap;
			uksort( $url_remap, array( $importer, 'cmpr_strlen' ) );

			foreach ( $url_remap as $old_url => $new_url ) {
				$old_url     = str_replace( '"', '', wp_json_encode( $old_url ) );
				$new_url     = str_replace( '"', '', wp_json_encode( $new_url ) );
				$widgets_str = str_replace( $old_url, $new_url, $widgets_str );
			}

			update_option( 'widget_text', json_decode( $widgets_str, true ) );
		}

		if ( isset( $retval['imported'] ) && $content_tracker && is_object( $content_tracker ) ) {
			$content_tracker->add( 'attachments_in_process', $retval['imported'] );
		}

		if ( isset( $retval['left'] ) && $retval['left'] === 0 && $content_tracker && is_object( $content_tracker ) ) {
			$content_tracker->add( 'attachments', $demo->include_attachments ? 'original' : 'placeholders' );
			if ( method_exists( $content_tracker, 'remove' ) ) {
				$content_tracker->remove( 'attachments_imported' );
			}
		}

		return $retval;
	}

	/**
	 * Imports site identity assets (logo and icon).
	 *
	 * @param Demo           $demo Demo instance being imported.
	 * @param ContentTracker $content_tracker Content tracker for the demo.
	 * @return null Always returns null.
	 */
	private static function sub_action_import_site_logo( $demo, $content_tracker ) {
		$importer = new ContentImporter();
		$importer->read_processed_data_from_cache();

		$wp_settings_importer = new WPSettingsImporter( $importer, $content_tracker );

		$site_identity = $demo->get_import_meta( 'site_identity' );

		if ( ! empty( $site_identity['custom_logo'] ) ) {
			$wp_settings_importer->import_custom_logo(
				$importer->get_processed_post( (int) $site_identity['custom_logo'] )
			);
		}

		if ( ! empty( $site_identity['site_icon'] ) ) {
			$wp_settings_importer->import_site_icon(
				$importer->get_processed_post( (int) $site_identity['site_icon'] )
			);
		}
		return null;
	}

	/**
	 * Finalises block theme data post-import.
	 *
	 * @param Demo           $demo Demo instance being imported.
	 * @param ContentTracker $content_tracker Content tracker for the demo.
	 * @return null Always returns null.
	 */
	private static function sub_action_process_block_theme_data( $demo, $content_tracker ) {
		$importer = new ContentImporter();
		$importer->read_processed_data_from_cache();

		$fse_importer = new FSEImporter( $importer, $content_tracker );
		$fse_importer->remap_post_ids_and_urls_in_blocks();
		$fse_importer->import_block_editor_settings( $demo->get_import_meta() );

		return null;
	}

	/**
	 * Cleans up temporary demo files and flushes rewrite rules.
	 *
	 * @param Demo           $demo Demo instance being imported.
	 * @param ContentTracker $content_tracker Content tracker for the demo.
	 * @return array|false Status payload or false if deletion not permitted.
	 */
	private static function sub_action_cleanup( $demo, $content_tracker ) {
		ContentImporter::clear_session();

		$wp_uploads = wp_get_upload_dir();

		$dir_to_delete = dirname( $demo->get_demo_uploads_dir() );
		if ( untrailingslashit( $wp_uploads['basedir'] ) === untrailingslashit( $dir_to_delete ) ) {
			return false;
		}

		if ( false === strpos( $dir_to_delete, $wp_uploads['basedir'] ) ) {
			return false;
		}

		global $wp_filesystem;

		if ( ! $wp_filesystem && ! WP_Filesystem() ) {
			return false;
		}

		$wp_filesystem->delete( $dir_to_delete, true );

		flush_rewrite_rules();
		return array(
			'status' => $demo->get_import_status_text(),
		);
	}
}
