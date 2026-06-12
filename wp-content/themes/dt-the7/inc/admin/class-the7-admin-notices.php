<?php
/**
 * The7 admin notices class.
 *
 * @package The7
 */

use The7\Admin\Notices\Abstract_Notice;

defined( 'ABSPATH' ) || exit;

/**
 * Class The7_Admin_Notices
 */
class The7_Admin_Notices {

	private $registered_notices = array();
	private $dismissed_notices = array();
	private $option_name = 'the7_dismissed_admin_notices';
	protected const DISPLAY_ONCE_OPTION_KEY_PREFIX = 'the7_display_once_admin_notices';

	public function __construct() {
		$this->setup_dismissed_notices();
	}

	public function add( $code, $callback, $type ) {
		$this->registered_notices[ $code ] = array(
			'callback' => $callback,
			'type'     => $type,
		);
	}

	public function add_object( Abstract_Notice $notice ) {
		$this->registered_notices[ $notice->get_code() ] = [
			'object' => $notice,
		];
	}

	public function print_admin_notices() {
		$dismissed_notices = $this->dismissed_notices ? array_combine( $this->dismissed_notices, $this->dismissed_notices ) : array();
		$notices_to_show = array_diff_key( $this->registered_notices, $dismissed_notices );
		$exclude_from_screen = apply_filters( 'the7_admin_notices_exclude_from_screen', array( 'options-general' ) );

		if ( $notices_to_show && ! in_array( get_current_screen()->parent_base, $exclude_from_screen ) ) {
			foreach ( $notices_to_show as $code => $notice ) {
				$id = 'the7-notice-' . $code;

				$notice_obj = $notice['object'] ?? null;
				if ( is_a( $notice_obj, Abstract_Notice::class ) ) {
					$is_visible = apply_filters( 'the7_admin_notice_is_visible', $notice_obj->is_visible(), $notice_obj );
					if ( $is_visible ) {
						$class = $notice_obj->get_wrapper_class() . ' the7-notice notice';
						printf( '<div id="%s" class="%s" data-code="%s">', esc_attr( $id ), esc_attr( $class ), esc_attr( $code ) );
						$notice_obj->render();
						echo '</div>';
					}
					continue;
				}

				$callback = $notice['callback'];
				if ( ! is_callable( $callback ) ) {
					continue;
				}

				$id = 'the7-notice-' . $code;
				$class = $notice['type'] . ' the7-notice notice';
				printf( '<div id="%s" class="%s" data-code="%s">', esc_attr( $id ), esc_attr( $class ), esc_attr( $code ) );
				call_user_func( $callback );
				echo '</div>';
			}
		}
	}

	public function dismiss_notices() {
		check_ajax_referer( 'the7_dismiss_admin_notice' );

		$code = $_POST['code'];

		if ( ! $this->notice_is_dismissed( $code ) ) {
			$this->dismiss_notice( $code );
		}
		wp_die();
	}

	public function get_nonce() {
		return wp_create_nonce( 'the7_dismiss_admin_notice' );
	}

	public function setup_dismissed_notices() {
		$dismissed_notices = get_option( $this->option_name );
		$this->dismissed_notices = ( $dismissed_notices ? (array) $dismissed_notices : array() );
	}

	public function reset_notices() {
		$this->dismissed_notices = array();
		delete_option( $this->option_name );
	}

	public function reset( $code ) {
		$this->dismissed_notices = array_diff( $this->dismissed_notices, array( $code ) );
		$this->save();
	}

	public function dismiss_notice( $code ) {
		$this->dismissed_notices[] = (string) $code;
		$this->save();
	}

	public function notice_is_dismissed( $code ) {
		return in_array( $code, $this->dismissed_notices );
	}

	public function display_once( $notice_code ) {
		if ( empty( $notice_code ) ) {
			return;
		}

		$notice_code = sanitize_key( $notice_code );
		if ( $notice_code === '' ) {
			return;
		}

		$transient_key = self::get_display_once_transient_key( $notice_code );

		set_transient( $transient_key, 1, 5 * MINUTE_IN_SECONDS );
	}

	public static function should_display_once( $notice_code ) {
		$transient_key  = self::get_display_once_transient_key( $notice_code );
		$should_display = (bool) get_transient( $transient_key );
		if ( $should_display ) {
			delete_transient( $transient_key );
		}

		return $should_display;
	}

	protected function save() {
		update_option( $this->option_name, $this->dismissed_notices );
	}

	static protected function get_display_once_transient_key( $notice_code ) {
		return self::DISPLAY_ONCE_OPTION_KEY_PREFIX . '_' . $notice_code;
	}
}
