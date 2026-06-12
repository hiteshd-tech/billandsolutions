<?php
/**
 * Module for adding pinned block overlap to blocks
 *
 * @package BbeProKit
 */

namespace BbeProKit\Modules\PinnedBlockOverlap;

use BbeProKit\Base\ModuleBasePro;
use BetterBlockEditor\Base\ManagableModuleInterface;
use BetterBlockEditor\Core\BlockUtils;
use BetterBlockEditor\Core\BundledAssetsManager;
use BbeProKit\Plugin;

defined( 'ABSPATH' ) || exit;

class Module extends ModuleBasePro implements ManagableModuleInterface {

	const MODULE_IDENTIFIER = 'pinned-block-overlap';
	const ASSETS_BUILD_PATH = 'editor/blocks/__all__/pinned-block-overlap/';

	const SETTINGS_ORDER = 1300;

	const ATTRIBUTE_NAME = 'wpbbePinnedOverlap';

	// WP plugin check cries about heredoc, so we need to use a multi-line string
	const VIEW_JS = "
		function updateMargin(el) {
			const offset = '-' + el.getBoundingClientRect().height + 'px';
			el.style.setProperty('--wp--pinned-block-overlap', offset);
		}

		const resizeObserver = new ResizeObserver(
			(entries) => entries.forEach( (entry) => updateMargin(entry.target) )
		);

		window.wp.domReady( () => {
			document.querySelectorAll('.is-overlap-bottom, .is-overlap-top').forEach((el) => {
				// Update margin initially
				updateMargin(el);

				// observe with ResizeObserver to update the margin when the element's size changes
				resizeObserver.observe(el, {box: 'border-box'});
			});
		});

		document.querySelectorAll('.is-overlap-bottom, .is-overlap-top').forEach(( el ) => updateMargin( el ));
";

	public function setup_hooks() {
		add_filter( 'render_block', array( $this, 'render' ), 20, 3 );
	}

	public function render( $block_content, $block ) {
		$overlap = $block['attrs'][ self::ATTRIBUTE_NAME ] ?? false;

		if ( ! $overlap || $block_content === '' ) {
			return $block_content;
		}

		return BlockUtils::append_classes( $block_content, array( 'is-overlap-' . $overlap ) );
	}

	/**
	 * Add as inline script to ensure that there is no blinking caused by loading delay.
	 * Redefine process_assets() method to add inline script in asset bundle mode.
	 */
	protected function process_assets() {
		$plugin = Plugin::instance();
		// in asset bundle mode we need to add inline script to the view bundle
		if ( $plugin->is_asset_bundle_mode() ) {
			$plugin->bundled_assets_manager->add_inline_js_after_bundle(
				BundledAssetsManager::VIEW_BUNDLE,
				self::VIEW_JS
			);
		} else {
			parent::process_assets();
		}
	}

	/**
	 * Add as inline script to ensure that there is no blinking caused by loading delay.
	 * Works only in NON asset bundle mode.
	 *
	 * @param string $key
	 */
	protected function enqueue_assets( $key ) {
		parent::enqueue_assets( $key );

		if ( $key === $this::VIEW_ASSET_KEY ) {
			wp_add_inline_script( $this->build_script_handle( $key ), self::VIEW_JS, 'after' );
		}
	}

	public static function get_title() {
		return __( 'Overlap', 'better-block-editor' );
	}

	public static function get_label() {
		return __( 'Add Overlap setting to Group, Row, Stack, and Grid blocks when Position is set to Sticky.', 'better-block-editor' );
	}
}
