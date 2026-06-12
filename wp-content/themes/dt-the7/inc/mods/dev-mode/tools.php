<?php
/**
 * The7 dev tools class.
 *
 * @package The7
 */

namespace The7\Mods\Dev_Mode;

defined( 'ABSPATH' ) || exit;

/**
 * Tools class.
 */
class Tools {

	/**
	 * Form post action handler.
	 */
	public static function use_tool() {
		if ( ! check_ajax_referer( 'the7-dev-tools', false, false ) ) {
			return;
		}

		if ( ! current_user_can( 'switch_themes' ) ) {
			return;
		}

		if ( empty( $_POST['tool'] ) ) {
			return;
		}

		$tool = $_POST['tool'];
		if ( is_callable( __CLASS__ . "::tool_$tool" ) ) {
			call_user_func( __CLASS__ . "::tool_$tool" );
		}

		$referer = wp_get_referer();
		if ( $referer ) {
			wp_safe_redirect( esc_url( $referer ) );
		} else {
			wp_safe_redirect( admin_url( 'admin.php?page=the7-dev' ) );
		}
		exit();
	}

	/**
	 * Regenerate shortcodes css.
	 */
	public static function tool_regenerate_shortcodes_css() {
		include_once PRESSCORE_MODS_DIR . '/theme-update/the7-update-utility-functions.php';
		the7_mass_regenerate_short_codes_inline_css();
		self::set_message( '<p>Shortcodes css was regenerated.</p>' );
	}

	/**
	 * Restore theme options from a backup.
	 */
	public static function tool_restore_theme_options_from_backup() {
		if ( ! isset( $_POST['theme_options_backup'] ) ) {
			self::set_message( '<p>There is no backup selected.</p>' );
			return;
		}

		$record_name = $_POST['theme_options_backup'];

		unset( $_POST['_wp_http_referer'] );
		if ( \The7_Options_Backup::restore( $record_name ) ) {
			$message = '<p>Theme options successfully restored from backup <code>' . esc_html( $record_name ) . '</code>.</p>';
		} else {
			$message = '<p>Selected backup is not valid, please search for <code>%' . esc_html( $record_name ) . '%</code> option in DB.</p>';
		}

		self::set_message( $message );
	}

	/**
	 * Delete all theme options backups.
	 */
	public static function tool_delete_all_theme_options_backups() {
		$count = \The7_Options_Backup::delete_all_records();
		self::set_message( "<p>Successfully deleted $count backups.</p>" );
	}

	/**
	 * @return void
	 */
	public static function tool_delete_post_type_builder_data() {
		if ( class_exists( '\The7_Core\Mods\Post_Type_Builder\Models\Post_Types' ) ) {
			\The7_Core\Mods\Post_Type_Builder\Models\Post_Types::save( [] );
		}

		if ( class_exists( '\The7_Core\Mods\Post_Type_Builder\Models\Taxonomies' ) ) {
			\The7_Core\Mods\Post_Type_Builder\Models\Taxonomies::save( [] );
		}

		if ( class_exists( '\The7_Post_Types\Models\Post_Types' ) ) {
			\The7_Post_Types\Models\Post_Types::save( [] );
		}

		if ( class_exists( '\The7_Post_Types\Models\Taxonomies' ) ) {
			\The7_Post_Types\Models\Taxonomies::save( [] );
		}

		self::set_message( '<p>Post Type Builder data was deleted.</p>' );
	}

	/**
	 * Run theme migration.
	 */
	public static function tool_run_migration() {
		$migration_version = isset( $_POST['migration'] ) ? $_POST['migration'] : null;
		$migrations        = \The7_Install::get_update_callbacks();

		if ( ! array_key_exists( $migration_version, $migrations ) ) {
			self::set_message( '<p>Error. Wrong migration.</p>' );
			return;
		}

		if ( \The7_Install::db_is_updating() ) {
			self::set_message( '<p>DB is updating. Please, wait untill it is done.</p>' );
			return;
		}

		$migrations_to_run = $migrations[ $migration_version ];

		// Bump DB version if needed.
		if ( version_compare( $migration_version, \The7_Install::get_db_version(), '>' ) ) {
			$migrations_to_run[] = 'bump_db_version_to_' . $migration_version;
		}

		$migrations_to_run[] = 'presscore_refresh_dynamic_css';
		$migrations_to_run[] = 'the7_elementor_flush_css_cache';

		\The7_Install::register_update_callbacks( $migrations_to_run );
		\The7_Install::updater_dispatch();

		the7_admin_notices()->reset( 'the7_updated' );
	}

	/**
	 * Change theme.json version.
	 */
	public static function tool_change_themejson_version() {
		if ( ! the7_is_gutenberg_theme_mode_active() ) {
			return;
		}

		$themejson_version = isset( $_POST['themejson_version'] ) ? $_POST['themejson_version'] : null;
		if ( ! $themejson_version ) {
			self::set_message( '<p>Error. Wrong theme.json version.</p>' );
			return;
		}

		$the7_compat_obj = \The7\Mods\Compatibility\Gutenberg\Block_Theme\The7_Block_Theme_Compatibility::instance();
		if ( $the7_compat_obj && $the7_compat_obj->set_fse_version( $themejson_version ) ) {
			self::set_message( '<p>Theme.json version was changed to ' . esc_html( $themejson_version ) . '.</p>' );
		} else {
			self::set_message( '<p>Error. Wrong theme.json version.</p>' );
		}
	}

	/**
	 * @return void
	 */
	public static function tool_trigger_fse_styles_font_download() {
		if ( ! the7_is_gutenberg_theme_mode_active() ) {
			return;
		}

		\The7\Mods\Compatibility\Gutenberg\Block_Theme\The7_FSE_Font_Manager::instance()->reset_fonts_to_download();
		self::set_message( '<p>Font download triggered.</p>' );
	}

	/**
	 * Store message to be published on the7 tools admin page.
	 *
	 * @param string $message Message text.
	 */
	protected static function set_message( $message ) {
		set_transient( 'the7-dev-tools-message', $message, 60 );
	}
}
