<?php
/**
 * Rename legacy dt-cr options to wpbbe equivalents.
 *
 * @package The7
 */

namespace The7\Mods\Theme_Update\Migrations\v12_10_0;

use The7\Mods\Theme_Update\Base\Migration_Base;
use WP_REST_Request;
use WP_Theme_JSON_Resolver;

defined( 'ABSPATH' ) || exit;

final class Global_Styles_Migration extends Migration_Base {

	public static function migrate(): bool {
		if ( ! class_exists( WP_Theme_JSON_Resolver::class ) ) {
			return false;
		}

		$global_styles_id = WP_Theme_JSON_Resolver::get_user_global_styles_post_id();
		if ( ! $global_styles_id ) {
			return false;
		}

		$global_styles = get_post_field( 'post_content', $global_styles_id );
		if ( ! $global_styles ) {
			return false;
		}

		$global_styles = str_replace( 'dt-cr\/', 'wpbbe\/', $global_styles );

		add_filter( 'wp_revisions_to_keep', '__return_zero', 99 );
		wp_update_post(
			[
				'ID'           => $global_styles_id,
				'post_content' => wp_slash( $global_styles ),
			]
		);
		remove_filter( 'wp_revisions_to_keep', '__return_zero', 99 );

		WP_Theme_JSON_Resolver::clean_cached_data();

		return false;
	}
}
