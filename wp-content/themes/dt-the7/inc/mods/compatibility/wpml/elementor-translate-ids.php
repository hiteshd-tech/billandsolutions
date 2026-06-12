<?php
/**
 * WPML Elementor compatibility.
 *
 * @package The7
 */

namespace The7\Mods\Compatibility\WPML;

defined( 'ABSPATH' ) || exit;

/**
 * Class Elementor_Translate_Ids.
 *
 * Handles Elementor template IDs translation via hooks. Based on WPML_Elementor_Translate_IDs class.
 *
 * @see WPML_Elementor_Translate_IDs
 */
class Elementor_Translate_Ids {

	/** @var \WPML\Utils\DebugBackTrace */
	private $debug_backtrace;

	/**
	 * Elementor_Translate_Ids constructor.
	 *
	 * @param \WPML\Utils\DebugBackTrace $debug_backtrace Debug backtrace.
	 */
	public function __construct( \WPML\Utils\DebugBackTrace $debug_backtrace ) {
		$this->debug_backtrace = $debug_backtrace;
	}

	/**
	 * @return void
	 */
	public function add_hooks() {
		add_filter(
			'elementor/documents/get/post_id',
			[
				$this,
				'translate_template_id',
			]
		);
	}

	/**
	 * @param int|string $template_id Template ID.
	 *
	 * @return int|string
	 */
	public function translate_template_id( $template_id ) {
		if ( $this->is_the7_template_call() ) {
			$template_id = $this->translate_id( $template_id );
		}

		return $template_id;
	}

	/**
	 * @return bool
	 */
	private function is_the7_template_call() {
		return $this->debug_backtrace->is_class_function_in_call_stack(
			'The7_Elementor_Compatibility',
			'get_builder_content_for_display'
		);
	}

	/**
	 * @param int|string $element_id Element ID.
	 *
	 * @return int|string
	 */
	private function translate_id( $element_id ) {
		$element_type = get_post_type( $element_id );

		$translated_id = apply_filters( 'wpml_object_id', $element_id, $element_type, true );

		if ( is_string( $element_id ) ) {
			$translated_id = (string) $translated_id;
		}

		return $translated_id;
	}

}
