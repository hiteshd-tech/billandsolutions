<?php

namespace The7\Mods\Compatibility\Elementor\Modules\Bulk_Edit_Globals;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Plugin as Elementor;
use The7\Mods\Compatibility\Elementor\Modules\Bulk_Edit_Globals\Control\Kit_Repeater;
use The7\Mods\Compatibility\Elementor\Modules\Bulk_Edit_Globals\Control\Kit_Switcher;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Module_Base;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widgets;

defined( 'ABSPATH' ) || exit;

/**
 * A class for the bulk edit globals.
 */
class Module extends The7_Elementor_Module_Base {

	// Event name dispatched by the buttons.
	const APPLY_EVENT_NAME = 'the7_bulk_edit:apply';
	const MENU_SLUG        = 'the7-bulk-edit-globals';

	/**
	 * Module instance.
	 */
	public function __construct() {
		add_action( 'elementor/editor/after_enqueue_scripts', [ $this, 'enqueue_editor_scripts' ] );
		add_action( 'elementor/editor/init', [ $this, 'register_templates' ] );

		add_action( 'elementor/element/before_section_end', [ $this, 'update_controls' ], 20, 3 );
		add_action( 'elementor/controls/register', [ $this, 'register_controls' ] );
	}

	/**
	 * Get module name.
	 *
	 * @return string Module name.
	 */
	public function get_name(): string {
		return 'bulk-edit-globals';
	}

	/**
	 * Enqueue editor scripts.
	 */
	public function enqueue_editor_scripts() {
		the7_register_script_in_footer(
			'the7-bulk-edit-preview',
			THE7_ELEMENTOR_JS_URI . '/bulk-edit-globals.js',
			[ 'elementor-editor' ]
		);
		wp_enqueue_script( 'the7-bulk-edit-preview' );
	}

	/**
	 * Register templates.
	 */
	public function register_templates() {
		Elementor::instance()->common->add_template( __DIR__ . '/views/templates.php' );
	}

	/**
	 * Inject bulk edit controls into kit widget.
	 *
	 * @param \Elementor\Widget_Base $widget     Widget.
	 * @param string                 $section_id Section ID.
	 * @param array                  $args       Args.
	 */
	public function update_controls( $widget, $section_id, $args ) {
		$widgets = [
			'kit' => [
				'section_name' => [ 'section_text_style' ],
			],
		];

		if ( ! array_key_exists( $widget->get_name(), $widgets ) ) {
			return;
		}

		$curr_section = $widgets[ $widget->get_name() ]['section_name'];
		if ( ! in_array( $section_id, $curr_section, true ) ) {
			return;
		}

		$control_data = [
			'type'         => Kit_Repeater::CONTROL_TYPE,
			'item_actions' => [
				'bulk_action' => true,
			],
		];
		The7_Elementor_Widgets::update_control_fields( $widget, 'custom_typography', $control_data );

		$widget->start_injection(
			[
				'of' => 'heading_custom_typography',
				'at' => 'before',
			]
		);

		$widget->add_control(
			'the7_bulk_edit',
			[
				'label'             => __( 'Bulk Edit', 'the7mk2' ),
				'type'              => Kit_Switcher::CONTROL_TYPE,
				'on_change_command' => 'the7-bulk-edit-globals/checkbox-switch',
				'label_on'          => esc_html__( 'On', 'the7mk2' ),
				'label_off'         => esc_html__( 'Off', 'the7mk2' ),
			]
		);

		$widget->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'the7_bulk_edit_typography',
				'global'    => [
					'active' => false,
				],
				'condition' => [
					'the7_bulk_edit!' => '',
				],
			]
		);

		$widget->add_control(
			'the7_bulk_edit_typography_button',
			[
				'type'        => Controls_Manager::BUTTON,
				'label'       => esc_html__( 'Apply Changes', 'the7mk2' ),
				'text'        => esc_html__( 'Apply', 'the7mk2' ),
				'button_type' => 'default',
				'event'       => static::APPLY_EVENT_NAME,
				'condition'   => [
					'the7_bulk_edit!' => '',
				],
			]
		);

		$widget->add_control(
			'the7_bulk_edit_apply_notice',
			[
				'type'       => Controls_Manager::ALERT,
				'alert_type' => 'info',
				'content'    => esc_html__( 'Changes were successfully applied', 'the7mk2' ),
			]
		);

		$widget->add_control(
			'the7_bulk_edit_description',
			[
				'raw'             => esc_html__( 'Select multiple Custom Fonts and simultaneously change their Typography settings. “Default” and blank values will not be applied.', 'the7mk2' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-descriptor',
				'separator'       => 'after',
			]
		);

		$widget->end_injection();
	}

	/**
	 * Register new controls that are used in bulk edit.
	 *
	 * Adds Kit_Repeater and Kit_Switcher controls.
	 */
	public function register_controls() {
		$controls_manager = Elementor::instance()->controls_manager;

		$controls_manager->register( new Kit_Repeater() );
		$controls_manager->register( new Kit_Switcher() );
	}

	/**
	 * @access public
	 * @static
	 */
	public static function is_active(): bool {
		return is_admin();
	}
}
