<?php

namespace The7\Mods\Compatibility\Elementor\Modules\Slider;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Css_Filter;
use Elementor\Modules\Library\Documents\Library_Document;
use Elementor\Plugin as Elementor;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Document extends Library_Document {

	public function __construct( array $data = [] ) {
		if ( $data && the7_elementor_pro_is_active() ) {
			add_filter( 'body_class', [ $this, 'filter_body_classes' ] );
		}

		parent::__construct( $data );
	}

	public static function get_properties() {
		$properties = parent::get_properties();
		$properties['support_site_editor'] = false; //in order to hide doc on "website" builder
		$properties['support_wp_page_templates'] = true; // in order to use canvas template
		$properties['support_conditions'] = false;
		$properties['support_kit'] = true;
		$properties['show_in_finder'] = false;

		return $properties;
	}

	/**
	 * Get document title.
	 * Retrieve the document title.
	 * @return string Document title.
	 * @access public
	 * @static
	 */
	public static function get_title() {
		return esc_html__( 'The7 Slide', 'the7mk2' );
	}

	public static function get_plural_title() {
		return __( 'The7 Slides', 'the7mk2' );
	}

	/**
	 * Add body classes.
	 * Add the body classes for the `style` controls selector.
	 *
	 * @param $body_classes
	 *
	 * @return array
	 */
	public function filter_body_classes( $body_classes ) {
		if ( get_the_ID() === $this->get_main_id() || Elementor::$instance->preview->is_preview_mode( $this->get_main_id() ) ) {
			$body_classes[] = $this->get_name() . '-template';
		}

		return $body_classes;
	}

	/**
	 * Get element name.
	 * Retrieve the element name.
	 * @return string The name.
	 * @since  1.4.0
	 * @access public
	 */
	public function get_name() {
		return $this->get_type();
	}

	public static function get_type() {
		return Module::DOCUMENT_TYPE;
	}

	/**
	 * Get CSS wrapper selector.
	 * Retrieve the wrapper selector for the current menu.
	 * @since  1.6.0
	 * @access public
	 * @abstract
	 */
	public function get_css_wrapper_selector() {
		return '.elementor-' . $this->get_main_id();
	}

	public function save( $data ) {
		$data['settings']['post_status'] = Document::STATUS_PUBLISH;

		return parent::save( $data );
	}

	public function print_elements_with_wrapper( $elements_data = null ) {
		if ( ! $elements_data ) {
			$elements_data = $this->get_elements_data();
		}

		?>
        <div <?php Utils::print_html_attributes( $this->get_container_attributes() ); ?>>
            <div class="elementor-section-wrap">
				<?php $this->print_elements( $elements_data ); ?>
            </div>
        </div>
		<?php
	}


	public function get_container_attributes() {
		$attributes = parent::get_container_attributes();
		//$settings = $this->get_settings_for_display();
		$attributes['class'] .= ' the7-slide-content';

		return $attributes;
	}

	/**
	 * Override original `get_content` to prevent recursion
	 * @return string Megamenu HTML
	 */
	public function get_content( $with_css = false ) {
		if ( get_the_ID() === $this->get_main_id() ) {
			return '';
		}

		return parent::get_content();
	}

	protected function register_controls() {

		$this->start_controls_section( 'the7_slide_setting', [
			'label' => esc_html__( 'Slide Settings', 'the7mk2' ),
			'tab'   => Controls_Manager::TAB_SETTINGS,
		] );
		//handle optimized and not optimized menu template html, because preview html are generated via js
		$selector = '{{WRAPPER}}  > .elementor-section-wrap, {{WRAPPER}} > .elementor-inner > .elementor-section-wrap';

		/*$this->add_responsive_control( 'the7_slide_height', [
				'label'      => __( 'Slide Height', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'vh' ],
				'range'      => [
					'px' => [
						'min' => 1,
						'max' => 2000,
					],
					'vh' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 500,
				],
				'selectors'  => [
					$selector => '--slide-height: {{SIZE}}{{UNIT}}; flex-grow:0 !important;',
				],
			] );*/

		$this->add_responsive_control( 'content_align', [
			'label'                => esc_html__( 'Content Alignment', 'the7mk2' ),
			'type'                 => Controls_Manager::CHOOSE,
			'default'              => 'stretch',
			'options'              => [
				'top'    => [
					'title' => __( 'Top', 'the7mk2' ),
					'icon'  => 'eicon-v-align-top',
				],
				'center' => [
					'title' => __( 'Middle', 'the7mk2' ),
					'icon'  => 'eicon-v-align-middle',
				],
				'bottom' => [
					'title' => __( 'Bottom', 'the7mk2' ),
					'icon'  => 'eicon-v-align-bottom',
				],
			],
			'selectors_dictionary' => [
				'top'    => 'flex-start',
				'right'  => 'flex-end',
				'bottom' => 'flex-end',
			],
			'selectors'            => [
				$selector => 'justify-content:{{VALUE}};',
			],
		] );

		$this->end_controls_section();


		// Section background
		$this->start_controls_section( 'the7_slide_background', [
				'label' => esc_html__( 'Slide Background', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_SETTINGS,
			] );

		$this->add_group_control( Group_Control_Background::get_type(), [
				'name'     => 'background',
				'types'    => [ 'classic', 'gradient' ],
				'exclude'        => [ 'attachment' ],
				'selector' => $selector,
			] );

		$this->end_controls_section();

		// Background Overlay
		$this->start_controls_section( 'the7_slide_background_overlay', [
				'label' => esc_html__( 'Slide Background Overlay', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_SETTINGS,
			] );

		$selector = '{{WRAPPER}}  > .elementor-section-wrap:before, {{WRAPPER}} > .elementor-inner > .elementor-section-wrap:before';

		$this->add_group_control( Group_Control_Background::get_type(), [
				'name'           => 'background_overlay',
				'types'          => [ 'classic', 'gradient' ],
				'exclude'        => [ 'attachment' ],
				'fields_options' => [
					'background' => [ 
						'selectors' => [
							'{{SELECTOR}}' => 'content: ""',
						],
					],
				],
				'selector'       => $selector,
			] );

		$this->add_responsive_control('background_overlay_opacity', [
				'label'     => esc_html__( 'Opacity', 'elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => .5,
				],
				'range'     => [
					'px' => [
						'max'  => 1,
						'step' => 0.01,
					],
				],
				'selectors' => [
					$selector => 'opacity: {{SIZE}};',
				],
				'condition' => [
					'background_overlay_background' => [ 'classic', 'gradient' ],
				],
			] );

		$this->add_group_control( Group_Control_Css_Filter::get_type(), [
				'name'       => 'css_filters',
				'selector'   => $selector,
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'background_overlay_image[url]',
							'operator' => '!==',
							'value'    => '',
						],
						[
							'name'     => 'background_overlay_color',
							'operator' => '!==',
							'value'    => '',
						],
					],
				],
			] );

		$this->add_control( 'overlay_blend_mode', [
				'label'      => esc_html__( 'Blend Mode', 'elementor' ),
				'type'       => Controls_Manager::SELECT,
				'options'    => [
					''            => esc_html__( 'Normal', 'elementor' ),
					'multiply'    => esc_html__( 'Multiply', 'elementor' ),
					'screen'      => esc_html__( 'Screen', 'elementor' ),
					'overlay'     => esc_html__( 'Overlay', 'elementor' ),
					'darken'      => esc_html__( 'Darken', 'elementor' ),
					'lighten'     => esc_html__( 'Lighten', 'elementor' ),
					'color-dodge' => esc_html__( 'Color Dodge', 'elementor' ),
					'saturation'  => esc_html__( 'Saturation', 'elementor' ),
					'color'       => esc_html__( 'Color', 'elementor' ),
					'luminosity'  => esc_html__( 'Luminosity', 'elementor' ),
					'difference'  => esc_html__( 'Difference', 'elementor' ),
					'exclusion'   => esc_html__( 'Exclusion', 'elementor' ),
					'hue'         => esc_html__( 'Hue', 'elementor' ),
				],
				'selectors'  => [
					$selector=> 'mix-blend-mode: {{VALUE}}',
				],
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'background_overlay_image[url]',
							'operator' => '!==',
							'value'    => '',
						],
						[
							'name'     => 'background_overlay_color',
							'operator' => '!==',
							'value'    => '',
						],
					],
				],
			] );


		$this->end_controls_section();

		parent::register_controls();
	}
}
