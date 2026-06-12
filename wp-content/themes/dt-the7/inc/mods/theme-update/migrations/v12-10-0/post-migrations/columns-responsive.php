<?php
/**
 * Normalize dtCrStackOn attributes into dtCrResponsive for columns block.
 *
 * @package The7
 */

namespace The7\Mods\Theme_Update\Migrations\v12_10_0\Post_Migrations;

use The7\Mods\Theme_Update\Base\Block_Editor\Wp_Block_Migration_Base;

defined( 'ABSPATH' ) || exit;

final class Columns_Responsive extends Wp_Block_Migration_Base {

	protected const BLOCK_NAMES     = [ 'core/columns' ];
	protected const ATTR_GROUP_NAME = 'dtCrStackOn';

	public function process_block( array $block ): array {
		$old = $block['attrs']['dtCrStackOn'];

		$new               = [];
		$new['breakpoint'] = $old['breakpoint'] ?? null;

		if ( array_key_exists( 'breakpointCustomValue', $old ) && null !== $old['breakpointCustomValue'] ) {
			$new['breakpointCustomValue'] = $old['breakpointCustomValue'];
		}

		$settings = $old;
		unset( $settings['breakpoint'], $settings['breakpointCustomValue'] );

		if ( $settings ) {
			$new['settings'] = $settings;
		}

		unset( $block['attrs']['dtCrStackOn'] );
		$block['attrs']['dtCrResponsive'] = $new;

		return $block;
	}
}
