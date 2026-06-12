<?php

namespace The7\Mods\Compatibility\Elementor\Modules\Overlay;

use Elementor\Core\Documents_Manager;
use Elementor\Plugin;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Module_Base;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Overlay_Template;

defined( 'ABSPATH' ) || exit;

class Module extends The7_Elementor_Module_Base {

	const DOCUMENT_TYPE = 'the7-overlay-template';

	const SUPPORTED_WIDGETS = [
		'the7-image-widget',
	];

	/**
	 * Module constructor.
	 */
	public function __construct() {
		add_action( 'elementor/documents/register', [ $this, 'register_document' ] );
		// Add template css in editor.
		add_action( 'elementor/element/parse_css', [ $this, 'add_template_css_in_edit_context' ], 10, 2 );

		add_action(
			'wp_enqueue_scripts',
			function() {
				the7_register_script( 'the7-overlay-template', THE7_ELEMENTOR_JS_URI . '/hover-template.js' );
			}
		);

		// new Elementor_Image_Widget_Integration();
	}

	/**
	 * @param Documents_Manager $documents_manager Document manager.
	 */
	public function register_document( $documents_manager ) {
		$documents_manager->register_document_type(
			self::DOCUMENT_TYPE,
			\The7\Mods\Compatibility\Elementor\Modules\Overlay\Document::get_class_full_name()
		);
	}

	/**
	 * @param  \Elementor\Core\Files\CSS\Post $post_css  The post CSS object.
	 * @param  \Elementor\Element_Base        $element   The element.
	 *
	 * @return void
	 */
	public function add_template_css_in_edit_context( $post_css, $element ) {
		if ( $post_css instanceof \Elementor\Core\DynamicTags\Dynamic_CSS ) {
			return;
		}

		if ( in_array( $element->get_name(), self::SUPPORTED_WIDGETS, true ) && Plugin::$instance->editor->is_edit_mode() ) {
			$hover_template = (int) $element->get_settings( Overlay_Template::TEMPLATE_CONTROL_KEY );
			if ( $hover_template && get_post_status($hover_template) === 'publish' ) {
				$css_file = \Elementor\Core\Files\CSS\Post::create( $hover_template );
				if ( $css_file ) {
					$css = $css_file->get_content();
					$css = str_replace( [ "\n", "\r" ], '', $css );
					$post_css->get_stylesheet()->add_raw_css( $css );
				}
			}
		}
	}

	/**
	 * @return array
	 */
	public function get_posts() {
		$source    = Plugin::$instance->templates_manager->get_source( 'local' );
		$templates = $source->get_items( [ 'type' => self::DOCUMENT_TYPE ] );

		return wp_list_pluck( $templates, 'title', 'template_id' );
	}

	/**
	 * Get module name.
	 * Retrieve the module name.
	 *
	 * @access public
	 * @return string Module name.
	 */
	public function get_name() {
		return 'overlay';
	}

	/**
	 * @return bool
	 */
	public static function is_active() {
		return the7_elementor_pro_is_active();
	}
}
