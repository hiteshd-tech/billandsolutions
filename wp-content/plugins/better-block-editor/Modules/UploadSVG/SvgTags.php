<?php
/**
 * Integration with the SVG Sanitizer library.
 *
 * @package BetterBlockEditor
 */

namespace BetterBlockEditor\Modules\UploadSVG;

defined( 'ABSPATH' ) || exit;

class SvgTags extends \enshrined\svgSanitize\data\AllowedTags {

	/**
	 * Returns an array of tags
	 *
	 * @return array
	 */
	public static function getTags() {
		return apply_filters( 'wpbbe-svg_allowed_tags', parent::getTags() );
	}
}
