<?php
/**
 * Remove dtCrStackOn remnants from constrained/default group layouts.
 *
 * @package The7
 */

namespace The7\Mods\Theme_Update\Migrations\v12_10_0\Post_Migrations;

use The7\Mods\Theme_Update\Base\Block_Editor\Wp_Block_Migration_Base;

defined( 'ABSPATH' ) || exit;

final class Group_Cleanup extends Wp_Block_Migration_Base {

	protected const BLOCK_NAMES     = [ 'core/group' ];
	protected const ATTR_GROUP_NAME = 'dtCrStackOn';

	public function need_to_process_block( array $block ): bool {
		if ( ! parent::need_to_process_block( $block ) ) {
			return false;
		}

		return in_array( $block['attrs']['layout']['type'] ?? null, [ 'constrained', 'default' ], true );
	}

	public function process_block( array $block ): array {
		unset( $block['attrs']['dtCrStackOn'] );

		return $block;
	}
}
