<?php
/**
 * Rename dt-cr block namespace to wpbbe.
 *
 * @package The7
 */

namespace The7\Mods\Theme_Update\Migrations\v12_10_0\Post_Migrations;

use The7\Mods\Theme_Update\Base\Block_Editor\Wp_Block_Migration_Base;

defined( 'ABSPATH' ) || exit;

final class Block_Name_Prefix extends Wp_Block_Migration_Base {

	protected const BLOCK_NAMES = [
		'dt-cr/svg-inline',
		'dt-cr/simple-scroller',
		'dt-cr/simple-scroller-content',
		'dt-cr/simple-scroller-indicator',
		'dt-cr/simple-scroller-arrow-left',
		'dt-cr/simple-scroller-arrow-right',
	];

	public function process_block( array $block ): array {
		$block['blockName'] = str_replace( 'dt-cr/', 'wpbbe/', $block['blockName'] );

		return $block;
	}
}
