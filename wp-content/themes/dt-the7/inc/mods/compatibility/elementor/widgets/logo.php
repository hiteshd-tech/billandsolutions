<?php
/**
 * The7 "Logo" widget for Elementor.
 *
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Utils;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Image_Aspect_Ratio;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Image_Size;

defined( 'ABSPATH' ) || exit;

/**
 * Image class.
 */
class Logo extends The7_Elementor_Widget_Base {
	const STICKY_WRAPPER = '.the7-e-sticky-effects .elementor-element.elementor-element-{{ID}}';

	/**
	 * @return string
	 */
	public function get_name() {
		return 'the7-logo-widget';
	}

	/**
	 * @return string
	 */
	protected function the7_title() {
		return esc_html__( 'Site Logo', 'the7mk2' );
	}

	/**
	 * @return string[]
	 */
	protected function the7_keywords() {
		return [ 'logo' ];
	}

	/**
	 * @return string
	 */
	protected function the7_icon() {
		return 'eicon-site-logo';
	}

	/**
	 * @return void
	 */
	protected function register_controls() {
		// Content.
		$this->add_content_controls();

		// Style.
		$this->add_image_style_controls();
	}

	/**
	 * @return void
	 */
	protected function add_content_controls() {
		$this->start_controls_section(
			'section_image',
			[
				'label' => esc_html__( 'Content', 'the7mk2' ),
			]
		);

		$this->add_control(
			'image',
			[
				'label'   => esc_html__( 'Image', 'the7mk2' ),
				'type'    => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
			]
		);

		$this->template( Image_Size::class )->add_style_controls();

		$this->add_responsive_control(
			'image_align',
			[
				'label'                => esc_html__( 'Alignment', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => [
					'left'   => [
						'title' => esc_html__( 'Left', 'the7mk2' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'the7mk2' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'the7mk2' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'prefix_class'         => 'content-align%s-',
				'default'              => 'left',
				'selectors_dictionary' => [
					'left'   => 'justify-content: flex-start;',
					'center' => 'justify-content: center;',
					'right'  => 'justify-content: flex-end;',
				],
				'selectors'            => [
					'{{WRAPPER}} .elementor-widget-container' => ' {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'image_sticky_switcher',
			[
				'label'        => esc_html__( 'Change Image', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'return_value' => 'y',
				'separator'    => 'before',
				'prefix_class' => 'sticky-logo-',
				'render_type'  => 'template',
				'description'  => esc_html__( 'This setting will take effect when both the "sticky" and "change styles when sticky" options are enabled on the parent container.', 'the7mk2' ),
			]
		);

		$this->add_control(
			'image_sticky',
			[
				'label'     => esc_html__( 'Image', 'the7mk2' ),
				'type'      => Controls_Manager::MEDIA,
				'dynamic'   => [
					'active' => true,
				],
				'default'   => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'condition' => [
					'image_sticky_switcher' => 'y',
				],
			]
		);

		$this->template( Image_Size::class, 'sticky' )->add_style_controls(
			[
				'image_sticky_switcher' => 'y',
			]
		);

		$this->add_control(
			'image_link',
			[
				'label'       => esc_html__( 'Link', 'the7mk2' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => [
					'active' => true,
				],
				'separator'   => 'before',
				'placeholder' => esc_html__( 'https://your-link.com', 'the7mk2' ),
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function add_image_style_controls() {
		$this->start_controls_section(
			'section_style_image',
			[
				'label' => esc_html__( 'Image', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'image_size',
			[
				'label'      => esc_html__( 'Width', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min' => 5,
						'max' => 1030,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .the7-logo-wrap' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->template( Image_Aspect_Ratio::class )->add_style_controls();

		$this->add_control(
			'image_sticky_style_switcher',
			[
				'label'        => esc_html__( 'Change Style', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'return_value' => 'y',
				'separator'    => 'before',
				'prefix_class' => 'sticky-logo-style-',
				'render_type'  => 'template',
				'description'  => esc_html__( 'This setting will take effect when both the "sticky" and "change styles when sticky" options are enabled on the parent container.', 'the7mk2' ),
			]
		);

		$this->add_responsive_control(
			'image_size_sticky',
			[
				'label'      => esc_html__( 'Width', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min' => 5,
						'max' => 1030,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					self::STICKY_WRAPPER . ' .the7-logo-wrap' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'image_sticky_style_switcher' => 'y',
				],
			]
		);

		$this->add_responsive_control(
			'item_ratio_sticky',
			[
				'label'       => esc_html__( 'Image Ratio', 'the7mk2' ),
				'description' => esc_html__( 'Leave empty to use original proportions', 'the7mk2' ),
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
					'{{WRAPPER}}' => '--sticky-aspect-ratio: {{SIZE}};',
				],
				'condition'   => [
					'image_sticky_style_switcher' => 'y',
				],
			]
		);

		$object_fit_options_sticky = [
			'fill'    => esc_html__( 'Fill', 'the7mk2' ),
			'cover'   => esc_html__( 'Cover', 'the7mk2' ),
			'contain' => esc_html__( 'Contain', 'the7mk2' ),
		];
		$this->add_responsive_control(
			'object_fit_sticky',
			[
				'label'                => esc_html__( 'Object Fit', 'the7mk2' ),
				'type'                 => Controls_Manager::SELECT,
				'options'              => $object_fit_options_sticky,
				'device_args'          => $this->generate_device_args(
					[
						'default' => '',
						'options' => [ '' => esc_html__( 'Default', 'the7mk2' ) ] + $object_fit_options_sticky,
					]
				),
				'selectors_dictionary' => [
					'fill'    => $this->combine_to_css_vars_definition_string(
						[
							'sticky-position'   => 'static',
							'sticky-object-fit' => 'fill',
							'sticky-width'      => 'initial',
							'sticky-svg-width'  => '100%',
							'sticky-height'     => 'auto',
							'sticky-max-height' => 'unset',
							'sticky-max-width'  => '100%',
							'sticky-box-width'  => 'var(--sticky-image-size, auto)',
						]
					),
					'cover'   => $this->combine_to_css_vars_definition_string(
						[
							'sticky-position'   => 'absolute',
							'sticky-object-fit' => 'cover',
							'sticky-width'      => '100%',
							'sticky-svg-width'  => '100%',
							'sticky-height'     => '100%',
							'sticky-max-height' => '100%',
							'sticky-max-width'  => '100%',
							'sticky-box-width'  => 'var(--sticky-image-size, var(--sticky-img-width))',
						]
					),
					'contain' => $this->combine_to_css_vars_definition_string(
						[
							'sticky-position'   => 'static',
							'sticky-object-fit' => 'contain',
							'sticky-width'      => 'auto',
							'sticky-svg-width'  => '100%',
							'sticky-height'     => 'auto',
							'sticky-max-height' => '100%',
							'sticky-max-width'  => '100%',
							'sticky-box-width'  => 'var(--sticky-image-size, auto)',
						]
					),
				],
				'default'              => 'cover',
				'selectors'            => [
					'{{WRAPPER}}' => '{{VALUE}};',
				],
				'condition'            => [
					'item_ratio_sticky[size]!'    => '',
					'image_sticky_style_switcher' => 'y',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$image_id = empty( $settings['image']['id'] ) ? null : (int) $settings['image']['id'];
		if ( ! $image_id && ! empty( $settings['__dynamic__']['image'] ) && strpos( (string) $settings['__dynamic__']['image'], 'post-featured-image' ) !== false && is_attachment() && wp_attachment_is_image() ) {
			$image_id = get_the_ID();
		}

		if ( wp_attachment_is_image( $image_id ) ) {
			add_filter( 'dt_of_get_option-general-images_lazy_loading', '__return_false', 9997 );

			$img_wrapper_class = implode(
				' ',
				array_filter(
					[
						'the7-logo-wrap',
						$this->template( Image_Size::class )->get_wrapper_class(),
						$this->template( Image_Aspect_Ratio::class )->get_wrapper_class(),
					]
				)
			);

			$link_settings = $this->get_link_settings();
			if ( $link_settings ) {
				$this->add_link_attributes( 'link', $link_settings );
				$image_wrapper       = '<a class="' . esc_attr( $img_wrapper_class ) . '" ' . $this->get_render_attribute_string( 'link' ) . '>';
				$image_wrapper_close = '</a>';
			} else {
				$image_wrapper       = '<div class="' . esc_attr( $img_wrapper_class ) . '">';
				$image_wrapper_close = '</div>';
			}

			echo $image_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $this->template( Image_Size::class )->get_image( $image_id );

			if ( $settings['image_sticky_switcher'] === 'y' ) {
				$sticky_image_id = empty( $settings['image_sticky']['id'] ) ? null : (int) $settings['image_sticky']['id'];
				if ( ! $sticky_image_id && ! empty( $settings['__dynamic__']['image_sticky'] ) && strpos( (string) $settings['__dynamic__']['image_sticky'], 'post-featured-image' ) !== false && is_attachment() && wp_attachment_is_image() ) {
					$sticky_image_id = get_the_ID();
				}

				if ( $sticky_image_id ) {
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $this->template( Image_Size::class, 'sticky' )->get_image( $sticky_image_id );
				}
			}

			echo $image_wrapper_close; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			remove_filter( 'dt_of_get_option-general-images_lazy_loading', '__return_false', 9997 );
		}
	}

	/**
	 * Retrieve image link settings.
	 *
	 * @return array|false
	 */
	protected function get_link_settings() {
		$image_link_settings = $this->get_settings_for_display( 'image_link' );

		return empty( $image_link_settings['url'] ) ? false : $image_link_settings;
	}
}
