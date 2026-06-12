<?php
/**
 * Rename dtCr* attribute groups to wpbbe* equivalents.
 *
 * @package The7
 */

namespace The7\Mods\Theme_Update\Migrations\v12_10_0\Post_Migrations;

use The7\Mods\Theme_Update\Base\Block_Editor\Wp_Block_Migration_Base;

defined( 'ABSPATH' ) || exit;

final class Attribute_Group_Prefix extends Wp_Block_Migration_Base {

	private const REPLACEMENTS = [
		'dtCrResponsive'               => 'wpbbeResponsive',
		'dtCrAnimationOnScroll'        => 'wpbbeAnimationOnScroll',
		'dtCrBackdropBlur'             => 'wpbbeBackdropBlur',
		'dtCrHoverColor'               => 'wpbbeHoverColor',
		'dtCrFlexItemPreventShrinking' => 'wpbbeFlexItemPreventShrinking',
		'dtCrMenuHoverColor'           => 'wpbbeMenuHoverColor',
		'dtCrSubmenuHoverColor'        => 'wpbbeSubmenuHoverColor',
		'dtCrOverlayMenu'              => 'wpbbeOverlayMenu',
		'dtCrPinnedOverlap'            => 'wpbbePinnedOverlap',
		'dtCrPinnedStyling'            => 'wpbbePinnedStyling',
		'dtCrVisibility'               => 'wpbbeVisibility',
	];

	public function process_block( array $block ): array {
		foreach ( self::REPLACEMENTS as $old => $new ) {
			if ( isset( $block['attrs'][ $old ] ) ) {
				$block['attrs'][ $new ] = $block['attrs'][ $old ];
				unset( $block['attrs'][ $old ] );
			}
		}

		return $block;
	}
}
