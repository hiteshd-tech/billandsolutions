<?php
/**
 * Move dtCrResponsiveText data to dtCrResponsive for text blocks.
 *
 * @package The7
 */

namespace The7\Mods\Theme_Update\Migrations\v12_10_0\Post_Migrations;

use The7\Mods\Theme_Update\Base\Block_Editor\Wp_Block_Migration_Base;

defined( 'ABSPATH' ) || exit;

final class Text_Responsive extends Wp_Block_Migration_Base {

	protected const BLOCK_NAMES     = [ 'core/post-title', 'core/post-excerpt', 'core/heading', 'core/paragraph' ];
	protected const ATTR_GROUP_NAME = 'dtCrResponsiveText';

	public function process_block( array $block ): array {
		$block['attrs']['dtCrResponsive'] = $block['attrs']['dtCrResponsiveText'];
		unset( $block['attrs']['dtCrResponsiveText'] );

		return $block;
	}
}
