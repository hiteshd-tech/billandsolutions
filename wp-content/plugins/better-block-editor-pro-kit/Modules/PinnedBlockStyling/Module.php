<?php
/**
 * Style blocks with position "sticky"
 *
 * @package BbeProKit
 */

namespace BbeProKit\Modules\PinnedBlockStyling;

use BbeProKit\Base\ModuleBasePro;
use BetterBlockEditor\Base\ManagableModuleInterface;
use BetterBlockEditor\Core\BlockUtils;
use BetterBlockEditor\Core\BundledAssetsManager;
use BetterBlockEditor\Core\ColorUtils;
use BbeProKit\Plugin;

defined( 'ABSPATH' ) || exit;

class Module extends ModuleBasePro implements ManagableModuleInterface {

	const MODULE_IDENTIFIER = 'pinned-block-styling';
	const ASSETS_BUILD_PATH = 'editor/blocks/__all__/pinned-block-styling/';

	const SETTINGS_ORDER = 1250;

	const ATTRIBUTES = 'wpbbePinnedStyling';

	public function setup_hooks() {
		add_filter( 'render_block', array( $this, 'render' ), 20, 3 );
		add_action( 'enqueue_block_editor_assets', array( $this, 'add_shadow_presets_definition' ) );
	}

	/**
	 * Build inline script for editor to set global variable with shadow presets
	 * It's important to call it with another hook (enqueue_block_editor_assets) because
	 * on_init is too early, wp_get_global_settings() cache settings on first call
	 * and it causes issues like FSE-140 (some settings added by core/plugins are missed in the cache)
	 *
	 * @return void
	 */
	public function add_shadow_presets_definition() {

		$settings = wp_get_global_settings( array( 'shadow' ) );

		$shadow_presets = 'const WPBBE_SHADOW_PRESETS=' . json_encode(
			array( 'shadow' => $settings ?? array() )
		);

		$plugin = Plugin::instance();

		if ( $plugin->is_asset_bundle_mode() ) {
			$plugin->bundled_assets_manager->add_inline_js_before_bundle(
				BundledAssetsManager::EDITOR_BUNDLE,
				$shadow_presets
			);
		} else {
			wp_add_inline_script(
				$this->build_script_handle( $this::EDITOR_ASSET_KEY ),
				$shadow_presets,
				'before'
			);
		}
	}

	function render( $block_content, $block ) {
		if ( $block_content === '' ) {
			return $block_content;
		}

		if ( 'sticky' !== ( $block['attrs']['style']['position']['type'] ?? null ) ) {
			return $block_content;
		}

		$settings = $block['attrs'][ self::ATTRIBUTES ] ?? array();
		if ( empty( $settings ) ) {
			return $block_content;
		}

		$block_content = BlockUtils::append_classes( $block_content, 'is-pin-ready' );

		$background_setting = $settings['background'] ?? array();
		if ( $background_setting['color'] ?? false ) {
			$background = ColorUtils::color_attribute_to_css( $background_setting['color'] );
		} elseif ( $background_setting['gradient'] ?? false ) {
			$background = ColorUtils::gradient_attribute_to_css( $background_setting['gradient'] );
		} else {
			$background = null;
		}

		$css_vars = array(
			'--wp-sticky--pinned-background'    => $background,
			'--wp-sticky--pinned-border-color'  => ColorUtils::color_attribute_to_css( $settings['borderColor'] ?? null ),
			'--wp-sticky--pinned-backdrop-blur' => $settings['backdropBlur'] ?? null,
			'--wp-sticky--pinned-shadow'        => $settings['shadow'] ?? null,
		);

		$css_classes = array(
			'background'    => 'has-pinned-background',
			'border-color'  => 'has-pinned-border',
			'backdrop-blur' => 'has-pinned-blur',
			'shadow'        => 'has-pinned-shadow',
		);
		// filter out classes without values in appropriate css variables
		$css_classes = array_filter(
			$css_classes,
			function ( $key ) use ( $css_vars ) {
				return null !== $css_vars[ '--wp-sticky--pinned-' . $key ];
			},
			ARRAY_FILTER_USE_KEY
		);

		return BlockUtils::append_classes(
			BlockUtils::append_inline_css_variables( $block_content, $css_vars ),
			$css_classes
		);
	}

	public static function get_title() {
		return __( 'Styles on Scroll', 'better-block-editor' );
	}

	public static function get_label() {
		return __( 'Add Styles on Scroll settings to Group, Row, Stack, and Grid blocks when Position is set to Sticky.', 'better-block-editor' );
	}
}
