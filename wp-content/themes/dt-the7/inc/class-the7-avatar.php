<?php
/**
 * The7 avatar class.
 *
 * @package The7
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class The7_Avatar
 */
class The7_Avatar {

	const CACHE_KEY = 'the7_avatar_cache';

	/**
	 * Wrapper for get_avatar() with some filters.
	 *
	 * @param mixed      $id_or_email ID or Email.
	 * @param int        $size Image size.
	 * @param string     $default Default avatar image url.
	 * @param string     $alt Avatar image alt.
	 * @param null|array $args Arguments.
	 *
	 * @return false|string
	 */
	public static function get_avatar( $id_or_email, $size = 96, $default = '', $alt = '', $args = null ) {
		add_filter( 'get_avatar', [ __CLASS__, 'check_gravatar_existence_filter' ], 10, 6 );
		$avatar = get_avatar( $id_or_email, $size, $default, $alt, $args );
		remove_filter( 'get_avatar', [ __CLASS__, 'check_gravatar_existence_filter' ] );

		return $avatar;
	}

	/**
	 * Return false if gravatar in use and user do not have one.
	 *
	 * @param string $avatar Avatar url.
	 * @param string $id_or_email Uler ID or Email.
	 * @param int    $args_size Image size.
	 * @param string $args_default Default avatar.
	 * @param string $args_alt Avatar image alt.
	 * @param array  $args Arguments.
	 *
	 * @return bool
	 */
	public static function check_gravatar_existence_filter( $avatar, $id_or_email, $args_size, $args_default, $args_alt, $args = [] ) {
		$args = wp_parse_args(
			$args,
			[
				'url' => '',
			]
		);

		if ( ! preg_match( '/.*\.gravatar\.com.*/', $avatar ) || self::is_gravatar_exists( $args['url'] ) ) {
			// non gravatar or gravatar exists.
			return $avatar;
		}

		return false;
	}

	/**
	 * Check if provided gravatar url response with 200.
	 *
	 * Cache result for $url in wp_cache for one week.
	 *
	 * @param string $url Gravatar url.
	 *
	 * @return bool
	 */
	public static function is_gravatar_exists( $url ) {
		if ( ! $url ) {
			return false;
		}

		$test_url = remove_query_arg( [ 's', 'd', 'f', 'r' ], $url );
		$code     = self::cache_get( $test_url );
		if ( empty( $code ) ) {
			$response = wp_remote_head( add_query_arg( 'd', '404', $test_url ) );
			if ( is_wp_error( $response ) ) {
				$code = 'not200';
			} else {
				$code = (string) $response['response']['code'];
			}

			self::cache_add( $test_url, $code );
		}

		return $code === '200';
	}

	/**
	 * @param string $url Gravatar url.
	 * @param string $code Response code.
	 *
	 * @return void
	 */
	protected static function cache_add( $url, $code ) {
		$hash  = self::hash_url( $url );
		$cache = get_transient( self::CACHE_KEY );
		if ( ! is_array( $cache ) ) {
			$cache = [];
		}
		$cache[ $hash ] = $code;

		set_transient( self::CACHE_KEY, $cache, WEEK_IN_SECONDS );
	}

	/**
	 * @param string $url Gravatar url.
	 *
	 * @return string|null
	 */
	protected static function cache_get( $url ) {
		$hash  = self::hash_url( $url );
		$cache = get_transient( self::CACHE_KEY );

		return isset( $cache[ $hash ] ) ? (string) $cache[ $hash ] : null;
	}

	/**
	 * @param string $url Gravatar url.
	 *
	 * @return string
	 */
	protected static function hash_url( $url ) {
		return md5( strtolower( trim( $url ) ) );
	}
}
