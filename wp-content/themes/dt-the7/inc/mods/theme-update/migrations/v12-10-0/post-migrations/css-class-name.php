<?php
/**
 * Update legacy dt-cr CSS class names to wpbbe equivalents inside block inner content.
 *
 * @package The7
 */

namespace The7\Mods\Theme_Update\Migrations\v12_10_0\Post_Migrations;

use The7\Mods\Theme_Update\Base\Block_Editor\Wp_Block_Migration_Base;

defined( 'ABSPATH' ) || exit;

final class Css_Class_Name extends Wp_Block_Migration_Base {

	private const RENAMED_CLASSES = [
		'format-library__inline-color-dt-cr-gradient' => 'format-library__inline-color-wpbbe-gradient',
		'dt-cr-svg-icon'                              => 'wpbbe-svg-icon',
		'dt-cr__flex-item-prevent-shrinking'          => 'wpbbe__flex-item-prevent-shrinking',
		'dt-cr-responsive-navigation'                 => 'wpbbe-responsive-navigation',
		'dt-cr-visibility-helper'                     => 'wpbbe-visibility-helper',
		'dt-cr-visibility-hidden'                     => 'wpbbe-visibility-hidden',
		'dt-cr-visibility-visible'                    => 'wpbbe-visibility-visible',
		'dt-cr-simple-scroller'                       => 'wpbbe-simple-scroller',
		'wp-block-dt-cr-simple-scroller'              => 'wp-block-wpbbe-simple-scroller',
		'wp-block-dt-cr-simple-scroller-arrow-left'   => 'wp-block-wpbbe-simple-scroller-arrow-left',
		'wp-block-dt-cr-simple-scroller-arrow-right'  => 'wp-block-wpbbe-simple-scroller-arrow-right',
	];

	public function process_block( array $block ): array {
		foreach ( self::RENAMED_CLASSES as $old => $new ) {
			foreach ( $block['innerContent'] ?? [] as $index => $content ) {
				if ( is_string( $content ) && strpos( $content, $old ) !== false ) {
					$block['innerContent'][ $index ] = str_replace( $old, $new, $content );
				}
			}
		}

		return $block;
	}
}
