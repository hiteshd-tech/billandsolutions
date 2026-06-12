<?php
/**
 * Module for prevent shrinking of flex items
 *
 * @package BetterBlockEditor
 */

namespace BetterBlockEditor\Modules\FlexItemPreventShrinking;

use BetterBlockEditor\Base\ManagableModuleInterface;
use BetterBlockEditor\Base\ModuleBase;
use BetterBlockEditor\Core\BlockUtils;

defined( 'ABSPATH' ) || exit;

class Module extends ModuleBase implements ManagableModuleInterface {

	const MODULE_IDENTIFIER = 'block-flex-item-prevent-shrinking';
	const ASSETS_BUILD_PATH = 'editor/blocks/__all__/flex-item-prevent-shrinking/';

	const SETTINGS_ORDER = 500;

	const ATTRIBUTES = 'wpbbeFlexItemPreventShrinking';



	public function setup_hooks() {
		add_filter( 'render_block', array( $this, 'render' ), 20, 3 );
	}


	function render( $block_content, $block ) {
		if ( $block_content === '' ) {
			return $block_content;
		}

		$is_parent_layout_flex = ( $block['parentLayout']['type'] ?? null !== 'flex' );
		if ( $is_parent_layout_flex && ( $block['attrs'][ self::ATTRIBUTES ] ?? null === true ) ) {
			$block_content = BlockUtils::append_classes( $block_content, array( 'wpbbe__flex-item-prevent-shrinking' ) );
		}

		return $block_content;
	}

	public static function get_title() {
		return __( 'Prevent Shrinking', 'better-block-editor' );
	}

	public static function get_label() {
		return __( 'Add Prevent Shrinking option to blocks with Width / Height set to Fit Content / Fixed.', 'better-block-editor' );
	}
}
