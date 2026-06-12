<?php
/**
 * Module for Inline SVG block.
 *
 * @package BetterBlockEditor
 */

namespace BetterBlockEditor\Modules\InlineSVG;

use BetterBlockEditor\Plugin;
use BetterBlockEditor\Base\ModuleBase;

defined( 'ABSPATH' ) || exit;

class Module extends ModuleBase {

	const MODULE_IDENTIFIER  = 'inline-svg';
	const UPLOAD_SVG_FEATURE = 'upload-svg';

	public function init() {
		if ( ! Plugin::instance()->is_feature_active( self::UPLOAD_SVG_FEATURE ) ) {
			return;
		}
		register_block_type(
			WPBBE_BLOCKS_DIR . 'svg-inline',
			array(
				'render_callback' => array( $this, 'render' ),
			)
		);
	}

	public static function get_title() {
		return __( 'SVG Icon', 'better-block-editor' );
	}

	public static function get_label() {
		return __( 'Allow to upload and display an SVG icon', 'better-block-editor' );
	}

	public function render( $attributes ) {
		$renderer = new InlineSVGRenderer();
		return $renderer->render( $attributes );
	}
}
