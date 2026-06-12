<?php
/**
 * Module for adding backdrop blur to blocks
 *
 * @package BbeProKit
 */

namespace BbeProKit\Modules\BackdropBlur;

use BbeProKit\Base\ModuleBasePro;
use BetterBlockEditor\Base\ManagableModuleInterface;
use BetterBlockEditor\Core\BlockUtils;

defined( 'ABSPATH' ) || exit;

class Module extends ModuleBasePro implements ManagableModuleInterface {

	const MODULE_IDENTIFIER = 'backdrop-blur';
	const ASSETS_BUILD_PATH = 'editor/blocks/__all__/backdrop-blur/';

	const SETTINGS_ORDER = 1200;

	const ATTRIBUTE_NAME = 'wpbbeBackdropBlur';

	const BlOCK_NAMES = array( 'core/group', 'core/columns', 'core/column', 'wpbbe/simple-scroller-arrow-left', 'wpbbe/simple-scroller-arrow-right', 'wpbbe/svg-inline');

	public function setup_hooks() {
		add_filter( 'render_block', array( $this, 'render' ), 20, 3 );
	}

	public function render( $block_content, $block ) {
		$backdrop_blur = $block['attrs'][ self::ATTRIBUTE_NAME ] ?? '0px';

		if ( ! in_array( $block['blockName'] ?? null, self::BlOCK_NAMES )
			|| $backdrop_blur === '0px'
			|| $block_content === ''
		) {
			return $block_content;
		}

		$block_content = BlockUtils::append_inline_styles(
			$block_content,
			array( '--wp--backdrop-blur' => $backdrop_blur )
		);

		return BlockUtils::append_classes( $block_content, array( 'has-backdrop-blur' ) );
	}

	public static function get_title() {
		return __( 'Backdrop Blur', 'better-block-editor' );
	}

	public static function get_label() {
		return __( 'Add Backdrop Blur setting to Group, Row, Stack, and Grid blocks.', 'better-block-editor' );
	}
}
