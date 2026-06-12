<?php

namespace The7\Mods\Compatibility\Elementor\Modules\Extended_Widgets;

use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use Elementor\Plugin;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Extend_Text_Editor {

	/**
	 * Before section end.
	 * Fires before Elementor section ends in the editor panel.
	 *
	 * @param Controls_Stack $widget     The control.
	 * @param string         $section_id Section ID.
	 * @param array          $args       Section arguments.
	 *
	 * @since 1.4.0
	 */
	public function __construct() {
		// inject controls
		add_action( 'elementor/element/before_section_end', [ $this, 'update_controls' ], 20, 3 );
	}
	public function update_controls( $widget, $section_id, $args ) {
		$widgets = [
			'text-editor'                    => [
				'section_name' => [ 'section_style', ],
			],
		];
		if ( ! array_key_exists( $widget->get_name(), $widgets ) ) {
			return;
		}
		$curr_section = $widgets[ $widget->get_name() ]['section_name'];
		if ( ! in_array( $section_id, $curr_section ) ) {
			return;
		}
		if ( $section_id == 'section_style' ) {
			$control_data = [
				'selectors' => [
					'{{WRAPPER}}' => 'color: {{VALUE}}; --textColor: {{VALUE}};',
				],
			];
			The7_Elementor_Widgets::update_control_fields( $widget, 'text_color', $control_data );
		}
	}
}
