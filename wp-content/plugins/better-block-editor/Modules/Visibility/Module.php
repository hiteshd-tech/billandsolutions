<?php
/**
 * Adds responsive visibility settings to all blocks.
 * Standard approach with ResponsiveBlockModuleBase can not be used here
 * as visibility='hidden' must work even without any responsive settings
 *
 * @package BetterBlockEditor
 */

namespace BetterBlockEditor\Modules\Visibility;

use BetterBlockEditor\Base\ManagableModuleInterface;
use BetterBlockEditor\Base\ModuleBase;
use BetterBlockEditor\Core\BlockUtils;
use BetterBlockEditor\Core\CssMediaBreakpoints;
use BetterBlockEditor\Plugin;

defined( 'ABSPATH' ) || exit;

class Module extends ModuleBase implements ManagableModuleInterface {

	const MODULE_IDENTIFIER        = 'block-responsive-visibility';
	const ASSETS_BUILD_PATH        = 'editor/blocks/__all__/visibility/';
	const PLUGIN_ASSETS_BUILD_PATH = 'editor/plugins/visibility/';

	const SETTINGS_ORDER = 990;

	const ATTRIBUTES = 'wpbbeVisibility';

	public function process_assets(): void {
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

		if ( file_exists( WPBBE_DIST . $this::PLUGIN_ASSETS_BUILD_PATH . $this::EDITOR_ASSET_KEY . '.css' ) ) {
			wp_register_style(
				$this->build_style_handle( 'editor-plugin' ),
				WPBBE_URL_DIST . $this::PLUGIN_ASSETS_BUILD_PATH . $this::EDITOR_ASSET_KEY . '.css',
				array(),
				$asset_file['version']
			);
		}

		add_action(
			'enqueue_block_editor_assets',
			function () {
				$this->enqueue_assets( 'editor-plugin' );
			}
		);
	}

	public function setup_hooks(): void {
		add_filter( 'render_block', array( $this, 'render' ), 20, 2 );
	}

	public function render( $block_content, $block ) {
		$attributes = isset( $block['attrs'] ) ? $block['attrs'] : null;

		if ( ! isset( $attributes[ self::ATTRIBUTES ] ) || $block_content === '' ) {
			return $block_content;
		}

		list( $visibility, $breakpoint ) = $this->get_visibility_settings( $attributes );

		// always visible (normal state, visibility is not applied)
		if ( $visibility === 'visible' && $breakpoint === CssMediaBreakpoints::BREAKPOINT_NAME_OFF ) {
			return $block_content;
		}

		$class_id = BlockUtils::get_unique_class_id( $block_content );
		$block_content  = BlockUtils::append_classes( $block_content, $class_id );

		$this->add_styles( $attributes, $class_id );

		return $block_content;
	}

	/**
	 * Helper to get visibility settings from block attributes
	 *
	 * @return array [ visibility, breakpoint, breakpointCustomValue ]
	 */
	private function get_visibility_settings( $attributes ): array {
		return
			array(
				$attributes[ self::ATTRIBUTES ]['visibility'] ?? 'visible', // default to visible if not set
				$attributes[ self::ATTRIBUTES ]['breakpoint'] ?? null,
				$attributes[ self::ATTRIBUTES ]['breakpointCustomValue'] ?? null,
			);
	}

	private function add_styles( $attributes, $class_id ): void {
		list( $visibility, $breakpoint, $breakpoint_value ) = $this->get_visibility_settings( $attributes );

		$switch_width = CssMediaBreakpoints::getSwitchWidth( $breakpoint, $breakpoint_value );

		if ( null === $switch_width ) {
			if ( $visibility === 'hidden' ) {
				// if block is always hidden
				BlockUtils::add_styles_from_css_rules(
					array(
						array(
							'selector'     => ".{$class_id}.{$class_id}",
							'declarations' => array( 'display' => 'none !important' ),
						),
					)
				);
			}
			return;
		}

		BlockUtils::add_style_for_media_query(
			"@media screen and (width ".($visibility === 'visible' ? '<=' : '>=')." {$switch_width})",
			".{$class_id}.{$class_id}",
			array( 'display' => 'none !important' )
		);
	}

	public static function get_title() {
		return __( 'Blocks Visibility', 'better-block-editor' );
	}

	public static function get_label() {
		return __( 'Add Responsive Visibility Settings to all blocks.', 'better-block-editor' );
	}
}
