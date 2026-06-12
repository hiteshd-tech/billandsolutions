<?php

namespace The7\Admin\Notices;

use The7_Admin_Notices;

defined( 'ABSPATH' ) || exit;

abstract class Abstract_Notice {

	protected const NOTICE_ERROR = 'notice-error';

	abstract public function render();

	abstract public function get_code();

	public function is_visible() {
		return true;
	}

	public function get_wrapper_class() {
		return 'notice';
	}

	protected function should_display_once() {
		return The7_Admin_Notices::should_display_once( $this->get_code() );
	}
}
