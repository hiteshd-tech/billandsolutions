<?php
/**
 * Integrate The7 hover template with Elementor image widget.
 *
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Modules\Overlay;

use Elementor\Controls_Stack;
use Elementor\Widget_Base;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Overlay_Template;

defined( 'ABSPATH' ) || exit;

class Elementor_Image_Widget_Integration {

	/**
	 * Elementor_Image_Widget_Integration constructor.
	 */
	public function __construct() {
		add_action( 'elementor/element/after_section_end', [ $this, 'add_controls' ], 20, 3 );
		add_filter( 'elementor/widget/render_content', [ $this, 'render' ], 20, 2 );
		add_filter( 'elementor/widget/print_template', [ $this, 'turn_off_image_js_template_in_editor' ], 20, 2 );
	}

	/**
	 * @param Controls_Stack $widget     Widget instance.
	 * @param string         $section_id Section ID.
	 * @param array          $args       Section arguments.
	 *
	 * @return void
	 */
	public function add_controls( $widget, $section_id, $args ) {
		if ( $section_id !== 'section_image' || $widget->get_name() !== 'image' ) {
			return;
		}

		$hover_template = new Overlay_Template( $widget );
		$hover_template->add_controls();
	}

	/**
	 * Add hover template control to image widget.
	 *
	 * @param string      $widget_content Widget content string.
	 * @param Widget_Base $widget         Widget object.
	 *
	 * @return string
	 */
	public function render( $widget_content, Widget_Base $widget ) {
		static $already_rendered = false;

		if ( $already_rendered || $widget->get_name() !== 'image' ) {
			return $widget_content;
		}

		$hover_template = new Overlay_Template( $widget );
		if ( ! $hover_template->get_template_id() ) {
			return $widget_content;
		}

		$already_rendered = true;

		$image            = $widget->get_settings_for_display( 'image' );
		$template         = $hover_template->get_render( isset( $image['id'] ) ? $image['id'] : null );
		$already_rendered = false;

		if ( $template ) {
			wp_enqueue_script( 'the7-overlay-template' );
			$widget_content = preg_replace( '/(<a\s[^>]*>[\s]*?)?(<img\s[^>]*>)([\s]*?<\/a>)?/i', '<div class="' . $hover_template->get_wrapper_class() . '">' . $template . '$1$2$3</div>', $widget_content );
		}

		return $widget_content;
	}

	/**
	 * Turn off image js template in editor to allow hover template to show up.
	 *
	 * @param string      $template_content Template content.
	 * @param Widget_Base $widget     Widget object.
	 *
	 * @return string
	 */
	public function turn_off_image_js_template_in_editor( $template_content, Widget_Base $widget ) {
		if ( $widget->get_name() === 'image' ) {
			$template_content = '';
		}

		return $template_content;
	}

}
