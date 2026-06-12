<?php
/**
 * Integration with the SVG Sanitizer library.
 *
 * @package BetterBlockEditor
 */

namespace BetterBlockEditor\Modules\UploadSVG;

defined( 'ABSPATH' ) || exit;

class SvgAttributes extends \enshrined\svgSanitize\data\AllowedAttributes {

	/**
	 * Returns an array of attributes
	 *
	 * @return array
	 */
	public static function getAttributes() {
		return apply_filters( 'wpbbe-svg_allowed_attributes', parent::getAttributes() );
	}
}
