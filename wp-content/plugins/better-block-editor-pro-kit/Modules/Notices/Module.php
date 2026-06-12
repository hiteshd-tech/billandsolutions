<?php
/**
 * Module for displaying admin notices.
 *
 * @package BbeProKit
 */

namespace BbeProKit\Modules\Notices;

use BbeProKit\Base\ModuleBasePro;

defined( 'ABSPATH' ) || exit;

class Module extends ModuleBasePro{

	const EXCLUDED_PAGES = [ 'plugins', 'plugin-install', 'plugin-editor' ];

	const MODULE_IDENTIFIER = 'admin-notices';
	const IS_CORE_MODULE = true;
	const DISMISS_ADMIN_NOTICE_NONCE = 'bbe_pro_kit_dismiss_admin_notice';
	const AJAX_NOTICE_NONCE = '_ajax_notice_nonce';
	private $registered_notices = array();
	private $dismissed_notices = array();
	private $option_name = 'bbe_pro_kit_dismissed_admin_notices';

	public function setup_hooks() {
		add_action( 'wp_ajax_bbe-pro-kit-dismiss-admin-notice', array( $this, 'ajax_dismiss_notice' ) );
		add_action( 'admin_notices', array( $this, 'print_admin_notices' ), 40 );
		add_filter( 'bbe_pro_kit_admin_filter' , array( $this, 'add_notice_nonce' ), 40 );
		$this->setup_dismissed_notices();
	}
	public function add_notice_nonce( $args ) {
		$args[ self::AJAX_NOTICE_NONCE ] = $this->get_nonce();
		return $args;
	}

	public function add( $notice_id, $callback, $class, $excluded_pages = null ) {
		$this->registered_notices[ $notice_id ] = array(
			'callback' => $callback,
			'class'     => $class,
			'excluded_pages' => $excluded_pages
		);
	}

	public function print_admin_notices() {
		$dismissed_notices = $this->dismissed_notices ? array_combine( $this->dismissed_notices, $this->dismissed_notices ) : array();
		$notices_to_show = array_diff_key( $this->registered_notices, $dismissed_notices );

		foreach ( $notices_to_show as $id => $notice ) {
			$exclude_from_screen = $notice['excluded_pages'] ? $notice['excluded_pages'] : self::EXCLUDED_PAGES;
			if (in_array( get_current_screen()->parent_base, $exclude_from_screen )){
				continue;
			}

			$callback = $notice['callback'];
			if ( ! is_callable( $callback ) ) {
				continue;
			}
			ob_start();
			call_user_func( $callback );
			$ret = ob_get_contents();
			ob_end_clean();
			if ($ret) {
				$notice_id = esc_attr("bbe-pro-kit-notice-$id");
				$class = esc_attr($notice['class'] . ' bbe-pro-kit-notice notice');
				$data_id = esc_attr($id);
				?>
				<div id="<?= $notice_id; ?>" class="<?= $class; ?>" data-notice_id="<?= $data_id; ?>">
					<?= $ret; ?>
				</div>
				<?php
			}
		}
	}

	public function ajax_dismiss_notice() {
		if (! check_ajax_referer( self::DISMISS_ADMIN_NOTICE_NONCE, false, false )) {
			wp_send_json_error(['message' => 'Invalid request']);
		}
		$notice_id = $_POST['notice_id'];

		if ( ! $this->notice_is_dismissed( $notice_id ) ) {
			$this->dismiss_notice( $notice_id );
		}

		if ( ! wp_doing_ajax() ) {
			wp_safe_redirect( admin_url() );
			die;
		}

		wp_send_json_success();
	}

	public function get_nonce() {
		return wp_create_nonce( self::DISMISS_ADMIN_NOTICE_NONCE );
	}

	public function setup_dismissed_notices() {
		$dismissed_notices = get_option( $this->option_name );
		$this->dismissed_notices = ( $dismissed_notices ? (array) $dismissed_notices : array() );
	}

	public function reset_notices() {
		$this->dismissed_notices = array();
		delete_option( $this->option_name );
	}

	public function reset( $notice_id ) {
		$this->dismissed_notices = array_diff( $this->dismissed_notices, array( $notice_id ) );
		$this->save();
	}

	public function dismiss_notice( $notice_id ) {
		$this->dismissed_notices[] = (string) $notice_id;
		$this->save();
	}

	public function notice_is_dismissed( $notice_id ) {
		return in_array( $notice_id, $this->dismissed_notices );
	}

	protected function save() {
		update_option( $this->option_name, $this->dismissed_notices );
	}
}
