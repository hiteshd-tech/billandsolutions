<?php

namespace The7\Mods\Compatibility\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Plugin;
use Elementor\Plugin as Elementor;
use ElementorPro\Modules\LoopBuilder\Files\Css\Loop_Dynamic_CSS;
use The7\Mods\Compatibility\Elementor\Modules\Popup\Module as PopupModule;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;
use The7_Elementor_Compatibility;

/**
 * Slider widget class.
 */
class Popup extends The7_Elementor_Widget_Base {

	const WIDGET_NAME = 'the7-popup';

	public function get_categories() {
		return [ 'theme-elements' ];
	}

	/**
	 * @return string|void
	 */
	protected function the7_title() {
		return esc_html__( 'Loop Popup', 'the7mk2' );
	}

	/**
	 * @return string
	 */
	protected function the7_icon() {
		return 'eicon-carousel-loop';
	}

	/**
	 * @return string[]
	 */
	protected function the7_keywords() {
		return [ 'popup', 'lightbox', 'modal'];
	}

	/**
	 * @return string
	 */
	public function get_name() {
		return self::WIDGET_NAME;
	}

	protected function register_controls() {
		// Content Tab.
		$this->add_content_controls();
	}

	protected function add_content_controls() {
		//'section_layout' name is important for createTemplate js function
		$this->start_controls_section( 'section_layout', [
			'label' => esc_html__( 'Content', 'the7mk2' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
		] );

		$slider_module = The7_Elementor_Compatibility::instance()->modules->get_modules( 'popup' );
		$library_ids = $slider_module->get_posts();

		$this->add_control( 'popup_id', [
			'label'       => esc_html__( 'Select popup', 'the7mk2' ),
			'type'        => Controls_Manager::SELECT,
			'options'     => $library_ids,
			'label_block' => false,
		] );

		$this->add_control(
			'popup_help_link',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => '<a href="https://guide.the7.io/elementor/user-guide/the7-widgets-in-elementor/the7-popup/" target="_blank" rel="nofollow"><u>' . esc_html__( 'Read the tutorial.', 'the7mk2' ) . '</u></a>',
				'separator'       => 'none',
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Slides content to display
	 */
	protected function render() {
		$settings = $this->get_settings();

		if ( ! $settings['popup_id'] ) {
			echo '<div class="the7-error-template">' . esc_html__( 'Popup template not selected', 'the7mk2' ) . '</div>';
			return;
		}
		$popup_id = $settings['popup_id'];

		if ( 'publish' !== get_post_status( $popup_id ) ) {
			echo '<div class="the7-error-template">' . esc_html__( 'Slide template not exist', 'the7mk2' ) . '</div>';
			return;
		}
		$this->print_dynamic_css( get_the_ID(), $popup_id );
		$this->before_skin_render();
		echo The7_Elementor_Compatibility::get_builder_content_for_display( $popup_id, true );
		$this->after_skin_render();
	}

	public function before_skin_render() {
		add_filter( 'elementor/document/wrapper_attributes', [ $this, 'add_class_to_popup_item' ], 10, 2 );
	}

	public function after_skin_render() {
		remove_filter( 'elementor/document/wrapper_attributes', [ $this, 'add_class_to_popup_item' ] );
	}

	public function add_class_to_popup_item( $attributes, $document ) {
		if ( PopupModule::DOCUMENT_TYPE === $document::get_type() ) {
			$post_id = get_the_ID();
			$open_selector = '#popup-post-id-' . $post_id;
			if ($attributes['data-elementor-settings']) {
				$elem_settings = json_decode( $attributes['data-elementor-settings'], true );
				if (isset($elem_settings['open_selector']) && $elem_settings['open_selector']){
					$elem_settings['open_selector'] .= ', '. $open_selector;
				}
				else {
					$elem_settings['open_selector'] = $open_selector;
				}
				$attributes['data-elementor-settings'] = wp_json_encode($elem_settings);
			}
			$attributes['data-post-id'] = $post_id;
			$attributes['class'] .= ' ' . implode( ' ', ['e-popup-item-' . $post_id] );
		}

		return $attributes;
	}

	protected function print_dynamic_css( $post_id, $post_id_for_data ) {
		$document = Elementor::instance()->documents->get_doc_for_frontend( $post_id_for_data );

		if ( ! $document ) {
			return;
		}

		Elementor::instance()->documents->switch_to_document( $document );

		$css_file = Loop_Dynamic_CSS::create( $post_id, $post_id_for_data );
		$post_css = $css_file->get_content();

		if ( ! empty( $post_css ) ) {
			$css = str_replace( '.elementor-' . $post_id, '.e-popup-item-' . $post_id, $post_css );
			$css = sprintf( '<style id="%s">%s</style>', 'popup-dynamic-' . $post_id_for_data, $css );

			echo $css; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		Elementor::instance()->documents->restore_document();
	}
}
