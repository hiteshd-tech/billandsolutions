<?php
/**
 * @package The7
 */

defined( 'ABSPATH' ) || exit;

/**
 * @see The7_Demo_Content_Admin::get_actions_builder()
 */
class The7_Demo_Full_Import_Actions_Builder extends The7_Demo_Actions_Builder_Base {

	protected function init() {
		if ( empty( $this->external_data['demo_id'] ) ) {
			$this->add_nothing_to_import_error();

			return;
		}

		$demo = $this->setup_demo( $this->external_data['demo_id'] );

		if ( ! $demo ) {
			$this->add_nothing_to_import_error();

			return;
		}

		$this->setup_starting_text(
			sprintf(
			// translators: %s: demo name
				esc_html_x( 'Importing %s demo...', 'admin', 'the7mk2' ),
				$demo->title
			)
		);
	}

	protected function setup_data() {
		$actions = [
			'install_plugins',
			'download_package',
		];

		if ( ! get_option( 'permalink_structure' ) ) {
			$actions[] = 'setup_rewrite_rules';
		}

		$actions[] = 'import_the7_dashboard_settings';

		$demo = $this->get_demo();

		if ( in_array( 'dt-the7-core', $demo->required_plugins, true ) ) {
			$actions[] = 'import_post_types_builder_data';
		} elseif ( in_array( 'dt-the7-post-types', $demo->required_plugins, true ) ) {
			$actions[] = 'import_post_types_plugin_data';
		}

		if ( $demo->is_fse() ) {
			$actions[] = 'download_fse_fonts';
		}

		$demo_id          = $demo->id;
		$demo_history     = The7_Demo_Tracker::get_demo_history( $demo_id );

		if ( ! isset( $demo_history['attachments_in_process'] ) ) {
			$actions[] = 'clear_importer_session';
		}

		$actions[] = 'import_post_types';
		$actions[] = 'import_attachments';

		if ( in_array( 'revslider', $demo->required_plugins, true ) ) {
			$actions[] = 'import_rev_sliders';
		}

		if ( !$demo->is_fse() ) {
			$actions[] = 'import_theme_options';
		}

		$actions[] = 'import_site_logo';

		if ( $demo->is_fse() ) {
			$actions[] = 'process_block_theme_data';
		}

		$actions[] = 'cleanup';

		$actions   = array_values( $actions );

		$plugins_to_install  = array_keys( $demo->plugins()->get_plugins_to_install() );
		$plugins_to_activate = array_keys( $demo->plugins()->get_inactive_plugins() );

		$users = [];

		$import_type = 'full_import';

		$this->localize_the7_import_data(
			compact(
				'actions',
				'users',
				'plugins_to_install',
				'plugins_to_activate',
				'demo_id',
				'import_type'
			)
		);
	}
}
