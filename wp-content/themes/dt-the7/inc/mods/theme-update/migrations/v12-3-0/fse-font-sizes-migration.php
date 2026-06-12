<?php
/**
 * Migrate FSE Font Sizes.
 *
 * @package The7
 */

namespace The7\Mods\Theme_Update\Migrations\v12_3_0;

use WP_Theme_JSON_Resolver;
use WP_REST_Request;

defined( 'ABSPATH' ) || exit;

/**
 * Fse_Font_Sizes_Migration class.
 */
class Fse_Font_Sizes_Migration {

	/**
	 * @return void
	 */
	public static function migrate() {
		if ( ! the7_is_gutenberg_theme_mode_active() ) {
			return;
		}

		if ( ! class_exists( WP_Theme_JSON_Resolver::class ) || ! class_exists( WP_REST_Request::class ) ) {
			return;
		}

		WP_Theme_JSON_Resolver::clean_cached_data();
		$global_styles_id     = WP_Theme_JSON_Resolver::get_user_global_styles_post_id();
		$request              = new WP_REST_Request( 'POST', '/wp/v2/global-styles/' . $global_styles_id );
		$settings_json_object = WP_Theme_JSON_Resolver::get_user_data();
		$raw_settings         = $settings_json_object->get_raw_data();
		$user_settings        = $raw_settings['settings'] ?? [];

		if ( ! isset( $user_settings['typography'] ) ) {
			$user_settings['typography'] = [];
		}

		if ( ! isset( $user_settings['typography']['fontSizes'] ) ) {
			$user_settings['typography']['fontSizes'] = [];
		}

		if ( ! isset( $user_settings['typography']['fontSizes']['theme'] ) ) {
			$user_settings['typography']['fontSizes']['theme'] = [];
		}

		$font_sizes = array_column( $user_settings['typography']['fontSizes']['theme'], null, 'slug' );

		$default_fonts = static::get_font_sizes();

		foreach ( $default_fonts as $font ) {
			if ( ! isset( $font_sizes[ $font['slug'] ] ) ) {
				$font_sizes[ $font['slug'] ] = $font;
			}
		}

		$user_settings['typography']['fontSizes']['theme'] = array_values( $font_sizes );

		$request->set_param( 'settings', $user_settings );
		rest_do_request( $request );
		WP_Theme_JSON_Resolver::clean_cached_data();
	}

	/**
	 * @return array[]
	 */
	public static function get_font_sizes() {
		return [
			[
				'fluid' => false,
				'name'  => 'XS',
				'size'  => '0.75rem',
				'slug'  => 'x-small',
			],
			[
				'fluid' => false,
				'name'  => 'S',
				'size'  => '0.875rem',
				'slug'  => 'small',
			],
			[
				'fluid' => false,
				'name'  => 'M (Base)',
				'size'  => '1rem',
				'slug'  => 'medium',
			],
			[
				'fluid' => [
					'max' => '1.19rem',
					'min' => '1.1rem',
				],
				'name'  => 'L',
				'size'  => '1.19rem',
				'slug'  => 'large',
			],
			[
				'fluid' => [
					'max' => '1.42rem',
					'min' => '1.22rem',
				],
				'name'  => 'XL',
				'size'  => '1.42rem',
				'slug'  => 'x-large',
			],
			[
				'fluid' => [
					'max' => '1.691rem',
					'min' => '1.35rem',
				],
				'name'  => '2XL',
				'size'  => '1.691rem',
				'slug'  => '2-x-large',
			],
			[
				'fluid' => [
					'max' => '2.014rem',
					'min' => '1.49rem',
				],
				'name'  => '3XL',
				'size'  => '2.014rem',
				'slug'  => '3-x-large',
			],
			[
				'fluid' => [
					'max' => '2.4rem',
					'min' => '1.65rem',
				],
				'name'  => '4XL',
				'size'  => '2.4rem',
				'slug'  => '4-x-large',
			],
			[
				'fluid' => [
					'max' => '2.9rem',
					'min' => '1.82rem',
				],
				'name'  => '5XL',
				'size'  => '2.9rem',
				'slug'  => '5-x-large',
			],
			[
				'fluid' => [
					'max' => '3.6rem',
					'min' => '2rem',
				],
				'name'  => '6XL',
				'size'  => '3.6rem',
				'slug'  => '6-x-large',
			],
		];
	}

}
