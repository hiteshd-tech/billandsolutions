<?php

namespace The7\Admin\Notices;

defined( 'ABSPATH' ) || exit;

abstract class Abstract_Registration_Settings_Notice extends Abstract_Notice {

	/**
	 * Cached settings errors.
	 *
	 * @var array|null
	 */
	protected $errors = null;

	/**
	 * Notice visibility.
	 *
	 * @return bool
	 */
	public function is_visible() {
		return (bool) $this->get_errors();
	}

	/**
	 * Notice wrapper CSS class.
	 *
	 * @return string
	 */
	public function get_wrapper_class() {
		return self::NOTICE_ERROR . ' the7-dashboard-notice';
	}

	/**
	 * Setting slug to read errors from.
	 *
	 * @return string
	 */
	protected function get_setting() {
		return 'the7_theme_registration';
	}

	/**
	 * Restrict to specific error codes. Empty array means any.
	 *
	 * @return array
	 */
	protected function get_error_codes() {
		return array();
	}

	/**
	 * Restrict to specific types. Empty array means any.
	 *
	 * @return array
	 */
	protected function get_allowed_types() {
		return array();
	}

	/**
	 * Retrieve cached errors.
	 *
	 * @return array
	 */
	protected function get_errors() {
		if ( $this->errors !== null ) {
			return $this->errors;
		}

		$errors = get_settings_errors( $this->get_setting() );

		$codes = $this->get_error_codes();
		$types = $this->get_allowed_types();

		$errors = array_filter(
			$errors,
			static function ( $error ) use ( $codes, $types ) {
				if ( empty( $error['message'] ) ) {
					return false;
				}

				if ( $codes && ! in_array( $error['code'], $codes, true ) ) {
					return false;
				}

				if ( $types ) {
					$type = isset( $error['type'] ) ? $error['type'] : '';
					if ( ! in_array( $type, $types, true ) ) {
						return false;
					}
				}

				return true;
			}
		);

		$this->errors = $errors;

		return $this->errors;
	}
}
