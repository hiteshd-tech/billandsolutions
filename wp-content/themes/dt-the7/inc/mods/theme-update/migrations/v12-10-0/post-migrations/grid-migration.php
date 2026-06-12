<?php
/**
 * Convert dtCrStackOn to dtCrResponsive for group grid layout.
 *
 * @package The7
 */

namespace The7\Mods\Theme_Update\Migrations\v12_10_0\Post_Migrations;

use The7\Mods\Theme_Update\Base\Block_Editor\Wp_Block_Migration_Base;

defined( 'ABSPATH' ) || exit;

final class Grid_Migration extends Wp_Block_Migration_Base {

	protected const BLOCK_NAMES     = [ 'core/group' ];
	protected const ATTR_GROUP_NAME = 'dtCrStackOn';

	public function need_to_process_block( array $block ): bool {
		if ( ! parent::need_to_process_block( $block ) ) {
			return false;
		}

		return ( $block['attrs']['layout']['type'] ?? null ) === 'grid';
	}

	public function process_block( array $block ): array {
		$old = $block['attrs']['dtCrStackOn'];

		$new                                      = $old;
		$new['settings']                          = $new['settings'] ?? [];
		$new['settings']['stack']                 = true;
		$new['settings']['disablePositionSticky'] = false;

		unset( $block['attrs']['dtCrStackOn'] );
		$block['attrs']['dtCrResponsive'] = $new;

		return $block;
	}
}
