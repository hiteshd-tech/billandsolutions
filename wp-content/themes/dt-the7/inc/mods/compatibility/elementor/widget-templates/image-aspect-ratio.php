<?php
/**
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widget_Templates;

use Elementor\Controls_Manager;

defined( 'ABSPATH' ) || exit;

/**
 * Class Image_Aspect_Ratio.
 *
 * @package The7\Mods\Compatibility\Elementor\Widget_Templates
 */
class Image_Aspect_Ratio extends Abstract_Template {

	/**
	 * @return void
	 */
	public function add_style_controls() {
		$original_proportion_options = [
			'y' => esc_html__( 'Original', 'the7mk2' ),
			'n' => esc_html__( 'Custom', 'the7mk2' ),
		];
		$this->widget->add_responsive_control(
			'original_proportion',
			[
				'label'                => esc_html__( 'Image Aspect Ratio', 'the7mk2' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => 'n',
				'return_value'         => 'y',
				'options'              => $original_proportion_options,
				'device_args'          => $this->widget->generate_device_args(
					[
						'options' => [ '' => esc_html__( 'No change', 'the7mk2' ) ] + $original_proportion_options,
					]
				),
				'selectors_dictionary' => [
					'n'  => $this->widget->combine_to_css_vars_definition_string(
						[
							'the7-img-object-fit' => 'var(--object-fit)',
							'the7-img-width'      => 'var(--ratio-img-width)',
							'the7-img-height'     => 'var(--ratio-img-height)',
							'the7-img-max-height' => 'var(--max-height)',
							'the7-img-max-width'  => 'var(--max-width)',
							'box-width'           => 'var(--image-size, var(--ratio-img-width))',
							'the7-img-ratio'      => 'var(--aspect-ratio, var(--ratio))',
						]
					),
					'y' => $this->widget->combine_to_css_vars_definition_string(
						[
							'the7-img-object-fit' => 'cover',
							'the7-img-width'      => 'inherit',
							'the7-img-height'     => 'auto',
							'the7-img-max-height' => 'unset',
							'the7-img-max-width'  => '100%',
							'box-width'           => 'var(--image-size, auto)',
							'the7-img-ratio'      => 'var(--ratio, initial)',
						]
					),
				],
				'selectors'            => [
					'{{WRAPPER}}' => '{{VALUE}};',
				],
				'render_type'  => 'template',
			]
		);

		$this->widget->add_responsive_control(
			'item_ratio',
			[
				'label'       => esc_html__( 'Custom Aspect Ratio', 'the7mk2' ),
				'type'        => Controls_Manager::SLIDER,
				'default'     => [
					'size' => '',
				],
				'range'       => [
					'px' => [
						'min'  => 0.1,
						'max'  => 2,
						'step' => 0.01,
					],
				],
				'selectors'   => [
					'{{WRAPPER}}' => '--aspect-ratio: {{SIZE}};',
				],

			]
		);
		$object_fit_options = [
			'fill'    => esc_html__( 'Fill', 'the7mk2' ),
			'cover'   => esc_html__( 'Cover', 'the7mk2' ),
			'contain' => esc_html__( 'Contain', 'the7mk2' ),
		];
		$this->widget->add_responsive_control(
			'object_fit',
			[
				'label'                => esc_html__( 'Object Fit', 'the7mk2' ),
				'type'                 => Controls_Manager::SELECT,
				'condition'            => [
					'item_ratio[size]!' => '',
				],
				'default'              => 'cover',
				'options'              => $object_fit_options,
				'device_args'          => $this->widget->generate_device_args(
					[
						'default' => '',
						'options' => [ '' => esc_html__( 'Default', 'the7mk2' ) ] + $object_fit_options,
					]
				),
				'selectors_dictionary' => [
					'fill'    => $this->widget->combine_to_css_vars_definition_string(
						[
							'object-fit'      => 'fill',
							'ratio-img-width' => 'initial',
							'svg-width'       => '100%',
							'height'          => 'auto',
							'max-height'      => 'unset',
							'max-width'       => '100%',
							'box-width'       => 'var(--image-size, auto)',
						]
					),
					'cover'   => $this->widget->combine_to_css_vars_definition_string(
						[
							'object-fit'      => 'cover',
							'ratio-img-width' => '100%',
							'svg-width'       => '100%',
							'height'          => '100%',
							'max-height'      => '100%',
							'max-width'       => '100%',
							'box-width'       => 'var(--image-size, var(--ratio-img-width))',
						]
					),
					'contain' => $this->widget->combine_to_css_vars_definition_string(
						[
							'object-fit'      => 'contain',
							'ratio-img-width' => 'auto',
							'svg-width'       => '100%',
							'height'          => 'auto',
							'max-height'      => '100%',
							'max-width'       => '100%',
							'box-width'       => 'var(--image-size, auto)',
						]
					),
				],
				'selectors'            => [
					'{{WRAPPER}}' => '{{VALUE}};',
				],
				'prefix_class'         => 'preserve-img-ratio-',
			]
		);
	}

	/**
	 * @return string
	 */
	public function get_wrapper_class() {
		return 'img-css-resize-wrapper';
	}

}
