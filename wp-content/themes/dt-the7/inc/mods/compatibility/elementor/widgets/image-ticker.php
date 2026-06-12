<?php
/**
 * The7 image ticker widget for Elementor.
 *
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Repeater;
use Elementor\Utils;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Image_Aspect_Ratio;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Image_Size;

defined( 'ABSPATH' ) || exit;

/**
 * Class Image_Ticker
 */
class Image_Ticker extends The7_Elementor_Widget_Base {

	/**
	 * Get element name.
	 *
	 * Retrieve the element name.
	 *
	 * @return string The name.
	 */
	public function get_name() {
		return 'the7-image-ticker-widget';
	}

	/**
	 * @return string
	 */
	protected function the7_title() {
		return esc_html__( 'Image Ticker', 'the7mk2' );
	}

	/**
	 * @return string
	 */
	protected function the7_icon() {
		return 'eicon-slider-push';
	}

	/**
	 * @return string[]
	 */
	protected function the7_keywords() {
		return [ 'ticker', 'image' ];
	}

	/**
	 * @return string[]
	 */
	public function get_style_depends() {
		return [ $this->get_name() ];
	}
	/**
	 * @return string[]
	 */
	public function get_script_depends() {
		return [ $this->get_name() ];
	}


	/**
	 * Register widget assets.
	 */
	protected function register_assets() {
		the7_register_script_in_footer(
			$this->get_name(),
			THE7_ELEMENTOR_JS_URI . '/the7-image-ticker-widget.js'
		);
		the7_register_style(
			$this->get_name(),
			THE7_ELEMENTOR_CSS_URI . '/the7-image-ticker-widget.css'
		);
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
			'ticker_section',
			[
				'label' => esc_html__( 'Items', 'the7mk2' ),
			]
		);
		$repeater = new Repeater();
		$repeater->add_control(
			'ticker_image',
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
		$repeater->add_control(
			'link',
			[
				'label'       => esc_html__( 'Link', 'the7mk2' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => [
					'active' => true,
				],
				'placeholder' => esc_html( 'https://your-link.com' ),
			]
		);
		$this->add_control(
			'ticker_items',
			[
				'type'   => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
			]
		);
		$this->add_control(
			'ticker_pause_animation',
			[
				'label'        => esc_html__( 'Pause on hover', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'yes'          => esc_html__( 'Yes', 'the7mk2' ),
				'no'           => esc_html__( 'No', 'the7mk2' ),
				'prefix_class' => 'pause_animation-',
			]
		);
		$this->add_control(
			'ticker_transition_speed',
			[
				'label'              => esc_html__( 'Transition Speed', 'the7mk2' ) . '(s)',
				'type'               => Controls_Manager::NUMBER,
				'default'            => 30,
				'frontend_available' => true,
				'selectors'          => [
					'{{WRAPPER}}' => '--transition-speed: {{SIZE}}s;',
				],
			]
		);
		$this->add_responsive_control(
			'ticker_gap_between_items',
			[
				'label'      => esc_html__( 'Items Gap', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [
					'size' => '10',
				],
				'range'      => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}}' => '--ticker-gap: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}  .the7-ticker-content, {{WRAPPER}}  .the7-ticker' => 'gap: {{SIZE}}{{UNIT}};',
				],
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
					'{{WRAPPER}} .the7-image-wrapper' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->template( Image_Aspect_Ratio::class )->add_style_controls();

		$this->add_control(
			'image_style_title',
			[
				'label'     => esc_html__( 'Style', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'image_padding',
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
					'{{WRAPPER}} .the7-image-wrapper img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'image_border_width',
			[
				'label'      => esc_html__( 'Border Width', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .the7-image-wrapper img' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; border-style:solid;',
				],
			]
		);

		$this->add_responsive_control(
			'image_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .the7-image-wrapper, {{WRAPPER}} .the7-image-wrapper img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'image_overlay_colors' );

		$this->start_controls_tab(
			'image_overlay_colors_normal',
			[
				'label' => esc_html__( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'           => 'overlay_background',
				'types'          => [ 'classic', 'gradient' ],
				'exclude'        => [ 'image' ],
				'fields_options' => [
					'background' => [
						'label' => esc_html__( 'Overlay', 'the7mk2' ),
					],
				],
				'selector'       => '{{WRAPPER}} .post-thumbnail-rollover:before, {{WRAPPER}} .post-thumbnail-rollover:after { transition: none; }
				{{WRAPPER}} .post-thumbnail-rollover:before,
				{{WRAPPER}} .post-thumbnail-rollover:after
				',
			]
		);

		$this->add_control(
			'border_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .the7-image-wrapper img' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'image_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .the7-image-wrapper img' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'image_shadow',
				'selector' => '{{WRAPPER}} .the7-image-wrapper',
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'     => 'image_filters',
				'selector' => '
				{{WRAPPER}} .the7-image-wrapper img
				',
			]
		);

		$this->add_control(
			'image_opacity',
			[
				'label'      => esc_html__( 'Image opacity', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => '%',
					'size' => '100',
				],
				'size_units' => [ '%' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .the7-image-wrapper img' => 'opacity: calc({{SIZE}}/100)',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'image_overlay_colors_hover',
			[
				'label' => esc_html__( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'           => 'overlay_hover_background',
				'types'          => [ 'classic', 'gradient' ],
				'exclude'        => [ 'image' ],
				'fields_options' => [
					'background' => [
						'label' => esc_html__( 'Overlay', 'the7mk2' ),
					],
					'color'      => [
						'selectors' => [
							'
							{{SELECTOR}},
							{{WRAPPER}} .post-thumbnail-rollover:before { transition: all var(--transition-overlay-duration-out, 0.3s) var(--transition-overlay-timing, ease); } {{WRAPPER}} .post-thumbnail-rollover:after { transition: all var(--transition-overlay-duration, 0.3s) var(--transition-overlay-timing, ease); } {{SELECTOR}}' => 'background: {{VALUE}};',
						],
					],

				],
				'selector'       => '{{WRAPPER}} .post-thumbnail-rollover:after',
			]
		);

		$this->add_control(
			'hover_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .the7-image-wrapper:hover img' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'image_hover_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .the7-image-wrapper:hover img' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'image_hover_shadow',
				'selector' => '{{WRAPPER}} .the7-image-wrapper:hover',
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'     => 'image_hover_filters',
				'selector' => '{{WRAPPER}} .the7-image-wrapper:hover img',
			]
		);

		$this->add_control(
			'image_hover_opacity',
			[
				'label'      => esc_html__( 'Image opacity', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => '%',
					'size' => '100',
				],
				'size_units' => [ '%' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .the7-image-wrapper:hover img ' => 'opacity: calc({{SIZE}}/100)',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
	}

	/**
	 * Render widget output on the frontend.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'ticker_text', 'class', 'ticker-content' );

		ob_start();
		foreach ( $settings['ticker_items'] as $index => $item ) {
			$tab_content_setting_key = $this->get_repeater_setting_key( 'ticker_content', 'tabs', $index );
			$this->add_inline_editing_attributes( $tab_content_setting_key );
			$img_wrapper_class = implode(
				' ',
				array_filter(
					[
						'the7-image-wrapper',
						'post-thumbnail-rollover',
						$this->template( Image_Size::class )->get_wrapper_class(),
						$this->template( Image_Aspect_Ratio::class )->get_wrapper_class(),
					]
				)
			);

			$wrap_key = 'wrap_' . $index;
			$this->add_render_attribute( $wrap_key, 'class', $img_wrapper_class );

			$tag = 'div';
			if ( ! empty( $item['link']['url'] ) ) {
				$tag = 'a';
				$this->add_link_attributes( $wrap_key, $item['link'] );
			}

			echo '<' . $tag . ' ' . $this->get_render_attribute_string( $wrap_key ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			if ( ! empty( $item['ticker_image']['id'] ) ) {
				echo $this->template( Image_Size::class )->get_image( $item['ticker_image']['id'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			echo '</' . $tag . '>';
		}
		$ticker_html = ob_get_clean();

		echo '<div class="the7-ticker">';
			echo '<div class="the7-ticker-content">';
				echo $ticker_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo '</div>';
		echo '</div>';
	}
}
