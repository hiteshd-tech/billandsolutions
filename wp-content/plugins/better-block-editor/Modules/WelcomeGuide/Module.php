<?php
/**
 * Adds Better Block Editor specific welcome guide to Block Editor.
 *
 * @package BetterBlockEditor
 */

namespace BetterBlockEditor\Modules\WelcomeGuide;

use BetterBlockEditor\Base\ModuleBase;
use BetterBlockEditor\Base\ModuleInterface;
use BetterBlockEditor\Plugin;

defined( 'ABSPATH' ) || exit;

class Module extends ModuleBase implements ModuleInterface {

	const RESET_QUERY_PARAM        = 'better-block-editor_reset-welcome-guide';
	const METADATA_PREFERENCES_KEY = 'wpbbe/welcome-guide';

	const MODULE_IDENTIFIER        = 'core-welcome-guide';
	const PLUGIN_ASSETS_BUILD_PATH = 'editor/plugins/welcome-guide/';

	const IS_CORE_MODULE = true;

	public function init() {
		parent::init();

		add_action( 'admin_init', array( $this, 'maybe_reset_welcome_guide' ) );
	}

	/**
	 * Checks if the welcome guide needs to be reset and performs the reset if necessary.
	 */
	public function maybe_reset_welcome_guide(): void {
		global $pagenow;

		if ( ( ! current_user_can( 'edit_user', get_current_user_id() ) ) || $pagenow !== 'profile.php' ) {
			return;
		}

		if ( ! sanitize_key( wp_unslash( $_GET[ self::RESET_QUERY_PARAM ] ?? '' ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}

		$this->reset_welcome_guide();
	}

	/**
	 * Resets the welcome guide for the current user by removing the relevant metadata.
	 */
	protected function reset_welcome_guide(): void {
		// User is on their profile page and can edit it
		$new_value = $this->get_current_user_preferences();

		unset( $new_value[ self::METADATA_PREFERENCES_KEY ] );

		// this function will return false if there is no such key in user meta
		update_user_meta( get_current_user_id(), 'wp_persisted_preferences', $new_value );

		$current_value = $this->get_current_user_preferences();

		$message = __( 'Now Welcome Guide for Better Block Editor will be shown again.', 'better-block-editor' );

		if ( ! array_key_exists( self::METADATA_PREFERENCES_KEY, $current_value ) ) {
			add_action(
				'admin_notices',
				function () use ( $message ) {
					echo '<div class="notice notice-success"><p>' . esc_html( $message ) . '</p></div>';
				}
			);
			// we don't do redirect here as this URL is more for service purposes than for user experience
			// so to avoid tricks with notice disappearing on redirect we just show it on the same page
		}
	}

	protected function get_current_user_preferences(): array {
		$user_id = get_current_user_id();

		$preferences = get_user_meta( $user_id, 'wp_persisted_preferences', true );
		if ( ! is_array( $preferences ) ) {
			$preferences = array();
		}

		return $preferences;
	}

	public function process_assets() {
		parent::process_assets();

		// in asset bundle mode plugin assets are already registered
		if ( Plugin::instance()->is_asset_bundle_mode() ) {
			return;
		}

		$asset_file = require WPBBE_DIST . $this::PLUGIN_ASSETS_BUILD_PATH . 'editor.asset.php';

		wp_register_script(
			$this->build_script_handle( 'editor-plugin' ),
			WPBBE_URL_DIST . $this::PLUGIN_ASSETS_BUILD_PATH . $this::EDITOR_ASSET_KEY . '.js',
			$asset_file['dependencies'],
			$asset_file['version'],
			array(
				'strategy'  => 'defer',
				'in_footer' => true,
			)
		);

		add_action(
			'enqueue_block_editor_assets',
			function () {
				$this->enqueue_assets( 'editor-plugin' );
			}
		);
	}
}
