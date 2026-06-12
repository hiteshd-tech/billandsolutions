<?php

namespace The7_Core\Mods\Post_Type_Builder\Handlers;

use The7_Core\Mods\Post_Type_Builder\Admin_Page;

defined( 'ABSPATH' ) || exit;

abstract class Handler {

	const UPDATE_NONCE_ACTION = 'the7_core_update_post_type_builder_nonce_action';

	const POST_DELETE = 'cpt_delete';
	const POST_UPDATE = 'cpt_submit';

	/**
	 * @return void
	 */
	public function setup() {
		add_action( 'init', [ $this, 'handle' ], 8 );
	}

	/**
	 * @return void
	 */
	public static function nonce_field() {
		wp_nonce_field( static::UPDATE_NONCE_ACTION );
	}

	/**
	 * @param  string       $url
	 * @param  string|null  $action
	 *
	 * @return string
	 */
	public static function nonce_url( $url, $action = null ) {
		if ( ! $action ) {
			$action = static::UPDATE_NONCE_ACTION;
		}

		return wp_nonce_url( $url, $action );
	}

	/**
	 * @return string
	 */
	public static function get_type() {
		return '';
	}

	/**
	 * @return bool
	 */
	public function is_the_right_page() {
		if ( wp_doing_ajax() ) {
			return false;
		}

		if ( ! is_admin() ) {
			return false;
		}

		if ( ! isset( $_GET['page'] ) || Admin_Page::MENU_SLUG !== $_GET['page'] ) {
			return false;
		}

		$filter_type = static::get_type();
		if ( $filter_type ) {
			$type = filter_input( INPUT_GET, 'type', FILTER_UNSAFE_RAW );

			if ( $type !== $filter_type ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * @return void
	 */
	public function security_check() {
		check_admin_referer( static::UPDATE_NONCE_ACTION );

		if ( ! current_user_can( 'switch_themes' ) ) {
			wp_die( __( 'Sorry, you are not allowed to access this page.', 'dt-the7-core' ) );
		}
	}

	/**
	 * @return void
	 */
	public function handle() {
		if ( ! $this->is_the_right_page() ) {
			return;
		}

		$action = filter_input( INPUT_GET, Admin_Page::INPUT_ACTION, FILTER_UNSAFE_RAW );
		$actions = $this->get_supported_actions();

		if ( array_key_exists( $action, $actions ) ) {
			$method = $actions[ $action ];
			$this->$method();
		}
	}

	abstract protected function get_supported_actions();
}
