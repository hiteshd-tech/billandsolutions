<?php
/**
 * Rename dtCrStackOn to dtCrResponsive for post template block.
 *
 * @package The7
 */

namespace The7\Mods\Theme_Update\Migrations\v12_10_0\Post_Migrations;

use The7\Mods\Theme_Update\Base\Block_Editor\Wp_Block_Migration_Base;

defined( 'ABSPATH' ) || exit;

final class Post_Template_Responsive extends Wp_Block_Migration_Base {

	protected const BLOCK_NAMES     = [ 'core/post-template' ];
	protected const ATTR_GROUP_NAME = 'dtCrStackOn';

	public function process_block( array $block ): array {
		$block['attrs']['dtCrResponsive'] = $block['attrs']['dtCrStackOn'];
		unset( $block['attrs']['dtCrStackOn'] );

		return $block;
	}
}
