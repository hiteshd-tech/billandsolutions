<?php
/**
 * The7 svg widget for Elementor.
 *
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Svg_Image class.
 */
class Svg_Image extends The7_Elementor_Widget_Base {

	const STICKY_WRAPPER = '.the7-e-sticky-effects .elementor-element.elementor-element-{{ID}}';

	/**
	 * Get element name.
	 *
	 * Retrieve the element name.
	 *
	 * @return string The name.
	 */
	public function get_name() {
		return 'the7_svg_image_widget';
	}

	/**
	 * @return string|void
	 */
	protected function the7_title() {
		return esc_html__( 'Svg', 'the7mk2' );
	}

	/**
	 * @return string[]
	 */
	protected function the7_keywords() {
		return [ 'svg', 'image', 'picture', 'vector', 'graphics' ];
	}

	/**
	 * @return string
	 */
	protected function the7_icon() {
		return 'eicon-favorite';
	}

	/**
	 * @return string[]
	 */
	public function get_style_depends() {
		return [ 'the7-svg-widget' ];
	}

	/**
	 * Register assets.
	 */
	protected function register_assets() {
		the7_register_style( 'the7-svg-widget', THE7_ELEMENTOR_CSS_URI . '/the7-svg-widget.css' );
	}

	/**
	 * Register controls.
	 */
	protected function register_controls() {
		$this->add_content_controls();
		$this->add_svg_style_controls();
	}

	/**
	 * Content controls.
	 */
	protected function add_content_controls() {
		$this->start_controls_section(
			'section_icon',
			[
				'label' => esc_html__( 'Content', 'the7mk2' ),
			]
		);

		$this->add_control(
			'svg_image',
			[
				'label'       => esc_html__( 'SVG', 'the7mk2' ),
				'type'        => Controls_Manager::MEDIA,
				'media_types' => [
					'svg',
				],
				'dynamic'     => [
					'active' => true,
				],
				'default'     => [
					'url' => PRESSCORE_THEME_URI . '/images/art-default.svg',
				],
				'description' => sprintf(
					/* translators: 1: Link open tag, 2: Link close tag. */
					esc_html__( 'Want to create custom text paths with SVG? %1$sLearn More%2$s', 'the7mk2' ),
					'<a target="_blank" href="https://go.elementor.com/text-path-create-paths/">',
					'</a>'
				),
			]
		);

		$this->add_responsive_control(
			'svg_position',
			[
				'label'     => esc_html__( 'Position', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'center center' => esc_html__( 'Center Center', 'the7mk2' ),
					'center left'   => esc_html__( 'Center Left', 'the7mk2' ),
					'center right'  => esc_html__( 'Center Right', 'the7mk2' ),
					'top center'    => esc_html__( 'Top Center', 'the7mk2' ),
					'top left'      => esc_html__( 'Top Left', 'the7mk2' ),
					'top right'     => esc_html__( 'Top Right', 'the7mk2' ),
					'bottom center' => esc_html__( 'Bottom Center', 'the7mk2' ),
					'bottom left'   => esc_html__( 'Bottom Left', 'the7mk2' ),
					'bottom right'  => esc_html__( 'Bottom Right', 'the7mk2' ),
					'custom'        => esc_html__( 'Custom', 'the7mk2' ),
				],
				'default'   => 'center center',
				'selectors' => [
					'{{WRAPPER}}' => '--mask-position: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'svg_repeat',
			[
				'label'     => esc_html__( 'Repeat', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'no-repeat' => esc_html__( 'No-repeat', 'the7mk2' ),
					'repeat'    => esc_html__( 'Repeat', 'the7mk2' ),
					'repeat-x'  => esc_html__( 'Repeat-x', 'the7mk2' ),
					'repeat-Y'  => esc_html__( 'Repeat-y', 'the7mk2' ),
					'round'     => esc_html__( 'Round', 'the7mk2' ),
					'space'     => esc_html__( 'Space', 'the7mk2' ),
				],
				'default'   => 'no-repeat',
				'selectors' => [
					'{{WRAPPER}}' => '--mask-repeat: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'svg_size',
			[
				'label'                => esc_html__( 'Size', 'the7mk2' ),
				'type'                 => Controls_Manager::SELECT,
				'options'              => [
					'contain' => esc_html__( 'Fit', 'the7mk2' ),
					'cover'   => esc_html__( 'Fill', 'the7mk2' ),
					'custom'  => esc_html__( 'Custom', 'the7mk2' ),
				],
				'selectors_dictionary' => [
					'contain' => $this->combine_to_css_vars_definition_string(
						[
							'mask-size' => 'contain',
						]
					),
					'cover'   => $this->combine_to_css_vars_definition_string(
						[
							'mask-size' => 'cover',
						]
					),
					'custom'  => $this->combine_to_css_vars_definition_string(
						[
							'mask-size' => 'var(--custom-mask-size)',
						]
					),
				],
				'default'              => 'contain',
				'selectors'            => [
					'{{WRAPPER}}' => '{{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'svg_scale',
			[
				'label'      => esc_html__( 'Scale', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', '%', 'vw' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
					'em' => [
						'min' => 0,
						'max' => 100,
					],
					'%'  => [
						'min' => 0,
						'max' => 200,
					],
					'vw' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'    => [
					'unit' => '%',
					'size' => 100,
				],
				'selectors'  => [
					'{{WRAPPER}}' => '--custom-mask-size: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'svg_size' => 'custom',
				],
			]
		);

		$this->add_responsive_control(
			'svg_align',
			[
				'label'     => esc_html__( 'Alignment', 'the7mk2' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
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
				'selectors' => [
					'{{WRAPPER}}' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'svg_link',
			[
				'label'       => esc_html__( 'Link', 'the7mk2' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => [
					'active' => true,
				],
				'placeholder' => esc_html__( 'https://your-link.com', 'the7mk2' ),
			]
		);

		$this->add_control(
			'svg_view',
			[
				'label'   => esc_html__( 'View', 'the7mk2' ),
				'type'    => Controls_Manager::HIDDEN,
				'default' => 'traditional',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Image style controls.
	 */
	protected function add_svg_style_controls() {
		$this->start_controls_section(
			'section_style_svg',
			[
				'label' => esc_html__( 'Image', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'svg_width',
			[
				'label'      => esc_html__( 'Width', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => '%',
				],
				'size_units' => [ '%', 'px', 'vw' ],
				'range'      => [
					'%'  => [
						'min' => 1,
						'max' => 100,
					],
					'px' => [
						'min' => 1,
						'max' => 1000,
					],
					'vw' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}}' => '--svg-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'svg_max_width',
			[
				'label'      => esc_html__( 'Max Width', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => '%',
				],
				'size_units' => [ '%', 'px', 'vw' ],
				'range'      => [
					'%'  => [
						'min' => 1,
						'max' => 100,
					],
					'px' => [
						'min' => 1,
						'max' => 1000,
					],
					'vw' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}}' => '--image-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'svg_height',
			[
				'label'      => esc_html__( 'Height', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
				],
				'size_units' => [ 'px', 'vh' ],
				'range'      => [
					'px' => [
						'min' => 1,
						'max' => 500,
					],
					'vh' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}}' => '--height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'svg_border',
				'label'    => esc_html__( 'Border', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .the7-svg-wrapper',
				'exclude'  => [
					'color',
				],
			]
		);

		$this->add_control(
			'svg_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .the7-svg-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'svg_padding',
			[
				'label'      => esc_html__( 'Padding', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .the7-svg-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->start_controls_tabs( 'svg_colors' );

		$this->start_controls_tab(
			'svg_colors_normal',
			[
				'label' => esc_html__( 'Normal', 'the7mk2' ),
			]
		);
		$this->add_svg_color_controls( 'normal' );
		$this->end_controls_tab();

		$this->start_controls_tab(
			'svg_colors_hover',
			[
				'label' => esc_html__( 'Hover', 'the7mk2' ),
			]
		);
		$this->add_svg_color_controls( 'hover' );
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'svg_colors_sticky',
			[
				'label'       => esc_html__( 'Change colors', 'the7mk2' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => esc_html__( 'When “Sticky” and “Change Styles when Sticky” are ON for the parent section.', 'the7mk2' ),
				'separator'   => 'before',
			]
		);

		$this->start_controls_tabs(
			'sticky_svg_colors',
			[
				'condition' => [
					'svg_colors_sticky!' => '',
				],
			]
		);

		$this->start_controls_tab(
			'sticky_svg_colors_normal',
			[
				'label' => esc_html__( 'Normal', 'the7mk2' ),
			]
		);
		$this->add_svg_color_controls( 'normal', 'sticky', self::STICKY_WRAPPER );
		$this->end_controls_tab();

		$this->start_controls_tab(
			'sticky_svg_colors_hover',
			[
				'label' => esc_html__( 'Hover', 'the7mk2' ),
			]
		);
		$this->add_svg_color_controls( 'hover', 'sticky', self::STICKY_WRAPPER );
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * @param string $type    Controls group type.
	 * @param string $prefix  Controls prefix.
	 * @param string $wrapper Selectors wrapper.
	 *
	 * @return void
	 */
	protected function add_svg_color_controls( $type = '', $prefix = '', $wrapper = '{{WRAPPER}}' ) {
		if ( $type === 'hover' ) {
			$selectors = [
				"$wrapper .the7-svg-wrapper:hover",
			];
		} else {
			$type      = '';
			$selectors = [
				"$wrapper .the7-svg-wrapper",
			];
		}

		$svg_prefix = '';
		if ( $prefix ) {
			$svg_prefix .= "_{$prefix}";
		}
		if ( $type ) {
			$svg_prefix .= "_{$type}";
		}

		$this->add_control(
			'svg_color' . $svg_prefix,
			[
				'label'     => esc_html__( 'Image Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => $this->give_me_megaselectors(
					$selectors,
					[
						' span' => 'background: {{VALUE}};',
					]
				),
			]
		);

		$this->add_control(
			'svg_bg_color' . $svg_prefix,
			[
				'label'     => esc_html__( 'Background color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => $this->give_me_megaselectors( $selectors, 'background: {{VALUE}}' ),
			]
		);

		$this->add_control(
			'svg_border_color' . $svg_prefix,
			[
				'label'     => esc_html__( 'Border color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => $this->give_me_megaselectors( $selectors, 'border-color: {{VALUE}}' ),
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'svg_box_shadow' . $svg_prefix,
				'label'    => esc_html__( 'Shadow', 'the7mk2' ),
				'selector' => implode( ', ', $selectors ),
			]
		);
	}

	/**
	 * Render widget.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'svg_wrapper', 'class', 'the7-svg-wrapper the7-elementor-widget' );
		if ( ! empty( $settings['svg_link']['url'] ) ) {
			$this->add_link_attributes( 'svg_wrapper', $settings['svg_link'] );
			$tag = 'a';
		} else {
			$tag = 'div';
		}
		$path_url = wp_get_attachment_url( $settings['svg_image']['id'] );
		if ( $path_url ) {
			$svg_mask = 'url(' . $path_url . ')';
			$this->add_render_attribute( 'svg_span', 'style', '-webkit-mask-image:' . $svg_mask . '; mask-image:' . $svg_mask );
		}

		echo '<' . $tag . ' ' . $this->get_render_attribute_string( 'svg_wrapper' ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<span ' . $this->get_render_attribute_string( 'svg_span' ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo wp_kses_post( Group_Control_Image_Size::get_attachment_image_html( $settings, 'thumbnail', 'svg_image' ) );
		echo '</span>';
		echo '</' . $tag . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

}
