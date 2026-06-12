<?php

namespace The7\Mods\Compatibility\Elementor\Modules\Popup;

use Elementor\Core\Documents_Manager;
use Elementor\Plugin;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Module extends The7_Elementor_Module_Base {

	const DOCUMENT_TYPE = 'popup';

	public function __construct() {

		// Modify product controls.
		add_action( 'elementor/element/before_section_end', [ $this, 'update_controls' ], 10, 3 );
	}

	public function get_posts() {
		$source = Plugin::$instance->templates_manager->get_source( 'local' );
		$templates = $source->get_items( [ 'type' => self::DOCUMENT_TYPE ] );

		return wp_list_pluck( $templates, 'title', 'template_id' );
	}

	/**
	 * Get module name.
	 * Retrieve the module name.
	 * @access public
	 * @return string Module name.
	 */
	public function get_name() {
		return 'popup';
	}

	public static function is_active() {
		return the7_elementor_pro_is_active();
	}

	/**
	 * Popup widgets.
	 *
	 * @var array
	 */
	const WIDGETS = [
		'popup',
		'toggle-popup',
	];

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
	public function update_controls( $widget, $section_id, $args ) {
		$widgets = [
			'popup' => [
				'section_name' => [ 'preview_settings' ],
			],
		];

		if ( ! array_key_exists( $widget->get_name(), $widgets ) ) {
			return;
		}

		$curr_section = $widgets[ $widget->get_name() ]['section_name'];
		if ( ! in_array( $section_id, $curr_section ) ) {
			return;
		}
		$preview_type_widget =  $widget->get_controls('preview_type');

		$widget->update_control( 'preview_type', [
			'groups' => $this::get_preview_as_options($preview_type_widget['groups'] ),
		] );
	}

	/**
	 * add woocommerce posts to popup preview
	 * @return array[]
	 */
	public static function get_preview_as_options($options) {
		$post_types = \ElementorPro\Core\Utils::get_public_post_types();

		$post_types_options = [];

		foreach ( $post_types as $post_type => $label ) {
			if ($post_type == 'product' ) {
				$post_types_options[ 'single/' . $post_type ] = get_post_type_object( $post_type )->labels->singular_name;
			}
		}

		$options['single']['options'] += $post_types_options;

		return $options;
	}
}
