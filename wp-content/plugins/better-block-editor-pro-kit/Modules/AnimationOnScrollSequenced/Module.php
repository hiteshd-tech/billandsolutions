<?php
/**
 * Adds a sequenced animation (trigger children animation) to the animation-on-scroll feature.
 *
 * @package BbeProKit
 */

namespace BbeProKit\Modules\AnimationOnScrollSequenced;

use BbeProKit\Base\ModuleBasePro;
use BetterBlockEditor\Core\BlockUtils;
use BbeProKit\Plugin;

defined( 'ABSPATH' ) || exit;

class Module extends ModuleBasePro {

	const MODULE_IDENTIFIER = 'animation-on-scroll-sequenced';
	const ASSETS_BUILD_PATH = 'editor/blocks/__all__/animation-on-scroll-sequenced/';

	const ANIMATION_ON_SCROLL_FEATURE = 'animation-on-scroll';

	const ATTRIBUTES_GROUP_NAME       = 'wpbbeAnimationOnScrollSequenced';
	const ATTRIBUTE_TRIGGER_ANIMATION = 'triggerAnimation';

	const SUPPORTED_BLOCK_TYPES = array( 'core/group', 'core/cover' );

	/**
	 * Checks if the animation on scroll feature is enabled and initializes the module ONLY if it is.
	 *
	 * @return void
	 */
	public function init(): void {
		if ( ! $this->is_animation_on_scroll_enabled() ) {
			return;
		}

		parent::init();
	}


	/**
	 * Sets up the hooks for the module - only if the animation on scroll feature is enabled.
	 *
	 * @return void
	 */
	public function setup_hooks(): void {
		if ( ! $this->is_animation_on_scroll_enabled() ) {
			return;
		}

		add_filter( 'render_block', array( $this, 'render' ), 20, 3 );
	}

	/**
	 * Appends the 'aos-root' class to the block content if the animation on scroll is enabled for the block.
	 *
	 * @param string $block_content The original block content.
	 * @param array  $block         The block data.
	 *
	 * @return string The modified block content with the 'aos-root' class if the animation on scroll is enabled, or the original block content if not.
	 */
	public function render( $block_content, $block ): string {
		if ( $block_content === '' ) {
			return $block_content;
		}

		$trigger_animation = $block['attrs'][ self::ATTRIBUTES_GROUP_NAME ][ self::ATTRIBUTE_TRIGGER_ANIMATION ] ?? false;
		if ( ! $trigger_animation || ! in_array( $block['blockName'] ?? null, self::SUPPORTED_BLOCK_TYPES ) ) {
			return $block_content;
		}

		$block_content = BlockUtils::append_classes( $block_content, 'aos-root' );

		return $block_content;
	}

	protected function process_assets() {

		parent::process_assets();

		$asset_file = require $this->get_assets_full_path() . 'common.asset.php';
		// ReRegister as we need add handler to globalCallbacks as early as possible
		// so load it in header and in sync mode (defaults)
		wp_deregister_script( $this->build_script_handle( self::COMMON_ASSET_KEY ) );
		wp_register_script(
			$this->build_script_handle( self::COMMON_ASSET_KEY ),
			$this->get_asset_url( self::COMMON_ASSET_KEY ) . '.js',
			$asset_file['dependencies'],
			$asset_file['version'],
			array(
				'strategy'  => 'defer',
				'in_footer' => false,
			)
		);

		add_action(
			is_admin() ? 'enqueue_block_assets' : 'wp_enqueue_scripts',
			function () {
				$this->enqueue_assets( self::COMMON_ASSET_KEY );
			}
		);
	}

	/**
	 * Checks if the animation on scroll feature is enabled.
	 *
	 * @return bool
	 */
	private function is_animation_on_scroll_enabled(): bool {
		return Plugin::instance()->is_feature_active( self::ANIMATION_ON_SCROLL_FEATURE );
	}
}
