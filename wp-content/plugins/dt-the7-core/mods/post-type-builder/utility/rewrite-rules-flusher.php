<?php

namespace The7_Core\Mods\Post_Type_Builder\Utility;

defined( 'ABSPATH' ) || exit;

class Rewrite_Rules_Flusher {

	const TRANSIENT_NAME = 'the7_core_flush_rewrite_rules';

	public static function setup() {
		add_action( 'admin_init', [ __CLASS__, 'maybe_flush_rewrite_rules' ] );
	}

	public static function maybe_flush_rewrite_rules() {
		if ( wp_doing_ajax() ) {
			return;
		}

		if ( get_transient( self::TRANSIENT_NAME ) ) {
			flush_rewrite_rules( false );
			delete_transient( self::TRANSIENT_NAME );
		}
	}

	public static function schedule_flush() {
		set_transient( self::TRANSIENT_NAME, true, 5 * MINUTE_IN_SECONDS );
	}
}
