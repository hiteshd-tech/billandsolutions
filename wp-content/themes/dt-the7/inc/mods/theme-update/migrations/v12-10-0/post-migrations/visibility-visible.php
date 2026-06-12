<?php
/**
 * Ensure dtCrVisibility visibility defaults to visible.
 *
 * @package The7
 */

namespace The7\Mods\Theme_Update\Migrations\v12_10_0\Post_Migrations;

use The7\Mods\Theme_Update\Base\Block_Editor\Wp_Block_Migration_Base;

defined( 'ABSPATH' ) || exit;

final class Visibility_Visible extends Wp_Block_Migration_Base {

	protected const ATTR_GROUP_NAME = 'dtCrVisibility';

	public function process_block( array $block ): array {
		$visibility = $block['attrs']['dtCrVisibility']['visibility'] ?? null;

		if ( null === $visibility ) {
			$block['attrs']['dtCrVisibility']['visibility'] = 'visible';
		}

		return $block;
	}
}
