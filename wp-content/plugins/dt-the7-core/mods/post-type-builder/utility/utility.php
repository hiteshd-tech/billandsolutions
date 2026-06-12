<?php

namespace The7_Core\Mods\Post_Type_Builder\Utility;

defined( 'ABSPATH' ) || exit;

class Utility {

	public static function get_disp_boolean( $bool_text ) {
		$bool_text = (string) $bool_text;
		if ( empty( $bool_text ) || '0' === $bool_text || 'false' === $bool_text ) {
			return false;
		}

		return true;
	}

	public static function disp_boolean( $bool_text ) {
		$bool_text = (string) $bool_text;
		if ( empty( $bool_text ) || '0' === $bool_text || 'false' === $bool_text ) {
			return 'false';
		}

		return 'true';
	}

	public static function escape_label( $label, $default = '' ) {
		// Quick return in case empty label.
		if ( ! $label ) {
			return $default;
		}

		$label = str_replace( '"', '', htmlspecialchars_decode( trim( $label ) ) );
		$label = htmlspecialchars( stripslashes( $label ), ENT_QUOTES );

		return $label ?: $default;
	}

}
