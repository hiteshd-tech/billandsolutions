<?php
/**
 * Builds the action list for importing demo content.
 *
 * @package BetterBlockEditor
 */

namespace BetterBlockEditor\Modules\DemoContent\ActionBuilders;

defined( 'ABSPATH' ) || exit;

/**
 * Configures the sequence of steps required to import demo content.
 */
class ImportContentActionBuilder extends ActionBuilderBase {

	/**
	 * Prepares initial messaging for the import UI.
	 *
	 * @return void
	 */
	protected function init() {
		if ( empty( $this->demo ) ) {
			return;
		}

		$this->setup_starting_text(
			sprintf(
				/* translators: %s: template name. */
				esc_html__( 'Importing %s template...', 'better-block-editor' ),
				$this->demo->title
			)
		);
	}

	/**
	 * Builds and localises the list of actions required for import.
	 *
	 * @return void
	 */
	protected function setup_data() {
		$actions = array();

		if ( get_template() !== 'better-block-theme' ) {
			$actions[] = 'install_bb_theme';
		}

		$actions[] = 'download_package';

		if ( ! get_option( 'permalink_structure' ) ) {
			$actions[] = 'setup_rewrite_rules';
		}

		$actions[] = 'turn_on_design_system';
		$actions[] = 'clear_importer_session';
		$actions[] = 'import_post_types';
		$actions[] = 'import_attachments';
		$actions[] = 'process_block_theme_data';
		$actions[] = 'download_fse_fonts';
		$actions[] = 'import_site_logo';
		$actions[] = 'cleanup';

		$actions = array_values( $actions );

		$plugins_to_install  = array();
		$plugins_to_activate = array();

		$users = array();
		if ( isset( $this->external_data['user'] ) ) {
			$users[] = $this->external_data['user'];
		}

		$demo_id = $this->demo->id;
		$this->localize_import_data(
			compact(
				'actions',
				'users',
				'plugins_to_install',
				'plugins_to_activate',
				'demo_id',
			)
		);
	}
}
