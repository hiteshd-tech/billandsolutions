<?php
/**
 * Normalize dtCrResponsive for flex group layout.
 *
 * @package The7
 */

namespace The7\Mods\Theme_Update\Migrations\v12_10_0\Post_Migrations;

use The7\Mods\Theme_Update\Base\Block_Editor\Wp_Block_Migration_Base;

defined( 'ABSPATH' ) || exit;

final class Row_Migration extends Wp_Block_Migration_Base {

	protected const BLOCK_NAMES     = [ 'core/group' ];
	protected const ATTR_GROUP_NAME = 'dtCrResponsive';

	public function need_to_process_block( array $block ): bool {
		if ( ! parent::need_to_process_block( $block ) ) {
			return false;
		}

		if ( ( $block['attrs']['layout']['type'] ?? null ) !== 'flex' ) {
			return false;
		}

		return ! isset( $block['attrs']['dtCrResponsive']['settings'] );
	}

	public function process_block( array $block ): array {
		$old = $block['attrs']['dtCrResponsive'];

		$new = [
			'breakpoint' => $old['breakpoint'] ?? null,
		];

		if ( array_key_exists( 'breakpointCustomValue', $old ) && null !== $old['breakpointCustomValue'] ) {
			$new['breakpointCustomValue'] = $old['breakpointCustomValue'];
		}

		$settings = $old;
		unset( $settings['breakpoint'], $settings['breakpointCustomValue'] );

		if ( ! empty( $settings ) ) {
			$new['settings'] = $settings;
		}

		$block['attrs']['dtCrResponsive'] = $new;

		return $block;
	}
}
