<?php
/**
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widget_Templates;

use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Box_Shadow;

defined( 'ABSPATH' ) || exit;

/**
 * Class Arrows.
 */
class Arrows extends Abstract_Template {

	/**
	 * @return void
	 */
	public function add_content_controls($condition = null) {
		$this->widget->start_controls_section(
			'arrows_section',
			[
				'label' => esc_html__( 'Arrows', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
				'condition' => $condition,
			]
		);

		$arrow_options = [
			'never'  => esc_html__( 'Never', 'the7mk2' ),
			'always' => esc_html__( 'Always', 'the7mk2' ),
			'hover'  => esc_html__( 'On Hover', 'the7mk2' ),
		];
		$this->widget->add_responsive_control(
			'arrows',
			[
				'label'                => esc_html__( 'Show Arrows', 'the7mk2' ),
				'type'                 => Controls_Manager::SELECT,
				'options'              => $arrow_options,
				'device_args'          => $this->widget->generate_device_args(
					[
						'default' => '',
						'options' => [ '' => esc_html__( 'Default', 'the7mk2' ) ] + $arrow_options,
					]
				),
				'default'              => 'always',
				'selectors'            => [
					'{{WRAPPER}}' => '{{VALUE}}',
				],
				'selectors_dictionary' => [
					'never'  => '--arrow-display: none;',
					'always' => '--arrow-display: inline-flex;--arrow-opacity:1;',
					'hover'  => '--arrow-display: inline-flex;--arrow-opacity:0;',
				],
			]
		);

		$arrow_position_options = [
			'box_area' => esc_html__( 'Box Area', 'the7mk2' ),
			'image'    => esc_html__( 'Image', 'the7mk2' ),
		];
		$this->widget->add_responsive_control(
			'arrows_position',
			[
				'label'                => esc_html__( 'Vertically aligned to', 'the7mk2' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => 'box_area',
				'options'              => $arrow_position_options,
				'device_args'          => $this->widget->generate_device_args(
					[
						'default' => '',
						'options' => [ '' => esc_html__( 'Default', 'the7mk2' ) ] + $arrow_position_options,
					]
				),
				'selectors_dictionary' => [
					'image'    => $this->widget->combine_to_css_vars_definition_string(
						[

							'offset-v-t-img' => 'var(--stage-top-gap) + var(--box-padding-top)',
							'offset-v-m-img' => 'calc(var(--stage-top-gap) + var(--box-padding-top) + var(--arrow-height)/2)',
							'arrow-height'   => 'var(--dynamic-img-height)',
							'top-b-img'      => '0px',
							'offset-v-b-img' => 'calc(var(--stage-top-gap) + var(--box-padding-top) + var(--arrow-height) - var(--arrow-bg-height, var(--arrow-icon-size)))',
						]
					),
					'box_area' => $this->widget->combine_to_css_vars_definition_string(
						[

							'offset-v-t-img' => '0px',
							'offset-v-m-img' => '50%',
							'top-b-img'      => '100%',
							'offset-v-b-img' => '0px',
						]
					),
				],
				'selectors'            => [
					'{{WRAPPER}} .owl-carousel'    => '{{VALUE}}',
					'{{WRAPPER}} .e-widget-swiper' => '{{VALUE}}',
				],
				'render_type'          => 'template',
				'prefix_class'         => 'arrows%s-relative-to-',
			]
		);

		$this->widget->end_controls_section();
	}

	/**
	 * @return void
	 */
	public function add_style_controls( $condition = null ) {
		$this->widget->start_controls_section(
			'arrows_style',
			[
				'label'      => esc_html__( 'Arrows', 'the7mk2' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => $this->widget->generate_conditions( 'arrows', '!=', 'never' ),
				'condition'  => $condition,
			]
		);

		$this->widget->add_control(
			'arrows_heading',
			[
				'label'     => esc_html__( 'Arrow Icon', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->widget->add_control(
			'next_icon',
			[
				'label'   => esc_html__( 'Next Arrow', 'the7mk2' ),
				'type'    => Controls_Manager::ICONS,
				'default' => [
					'value'   => 'fas fa-chevron-right',
					'library' => 'fa-solid',
				],
			]
		);

		$this->widget->add_control(
			'prev_icon',
			[
				'label'   => esc_html__( 'Previous Arrow', 'the7mk2' ),
				'type'    => Controls_Manager::ICONS,
				'default' => [
					'value'   => 'fas fa-chevron-left',
					'library' => 'fa-solid',
				],
			]
		);

		$this->widget->add_responsive_control(
			'arrow_icon_size',
			[
				'label'      => esc_html__( 'Arrow Icon Size', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 24,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}}' => '--arrow-icon-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->widget->add_control(
			'arrows_background_heading',
			[
				'label'     => esc_html__( 'Arrow style', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$arrow_selector = '{{WRAPPER}} .owl-nav div, {{WRAPPER}} .the7-swiper-button';

		$this->widget->add_responsive_control(
			'arrow_bg_width',
			[
				'label'      => esc_html__( 'Background Width', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 40,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
				],
				'selectors'  => [
					$arrow_selector => 'width: max({{SIZE}}{{UNIT}}, var(--arrow-icon-size, 1em))',
				],
			]
		);

		$this->widget->add_responsive_control(
			'arrow_bg_height',
			[
				'label'      => esc_html__( 'Background Height', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 40,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .owl-carousel' => '--arrow-bg-height: {{SIZE}}{{UNIT}};',
					$arrow_selector             => 'height: max({{SIZE}}{{UNIT}}, var(--arrow-icon-size, 1em))',
				],
			]
		);

		$this->widget->add_control(
			'arrow_border_radius',
			[
				'label'      => esc_html__( 'Arrow Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 0,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 500,
						'step' => 1,
					],
				],
				'selectors'  => [
					$arrow_selector => 'border-radius: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->widget->add_control(
			'arrow_border_width',
			[
				'label'      => esc_html__( 'Arrow Border Width', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 0,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 25,
						'step' => 1,
					],
				],
				'selectors'  => [
					$arrow_selector => 'border-width: {{SIZE}}{{UNIT}}; border-style: solid',
				],
			]
		);

		$this->widget->start_controls_tabs( 'arrows_style_tabs' );

		$this->add_arrow_style_states_controls( 'normal_', esc_html__( 'Normal', 'the7mk2' ) );
		$this->add_arrow_style_states_controls( 'hover_', esc_html__( 'Hover', 'the7mk2' ) );

		$this->widget->end_controls_tabs();

		$this->add_arrow_position_styles( 'prev_', esc_html__( 'Prev Arrow Position', 'the7mk2' ) );
		$this->add_arrow_position_styles( 'next_', esc_html__( 'Next Arrow Position', 'the7mk2' ) );

		$this->widget->end_controls_section();
	}

	/**
	 * @param string $prefix_name Prefix.
	 * @param string $box_name    Box.
	 *
	 * @return void
	 */
	protected function add_arrow_style_states_controls( $prefix_name, $box_name ) {
		$is_hover = '';
		if ( strpos( $prefix_name, 'hover_' ) === 0 ) {
			$is_hover = ':hover';
		}

		$selector_pattern = '{{WRAPPER}} .owl-nav div' . $is_hover . '%1$s, {{WRAPPER}} .the7-swiper-button' . $is_hover . '%1$s';
		$selector = sprintf($selector_pattern, "");

		$this->widget->start_controls_tab(
			$prefix_name . 'arrows_colors',
			[
				'label' => $box_name,
			]
		);

		$this->widget->add_control(
			$prefix_name . 'arrow_icon_color',
			[
				'label'     => esc_html__( 'Icon Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					sprintf($selector_pattern, '> i')  => 'color: {{VALUE}};',
					sprintf($selector_pattern, '> svg') => 'fill: {{VALUE}};color: {{VALUE}};',
				],
			]
		);

		$this->widget->add_control(
			$prefix_name . 'arrow_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					$selector => 'border-color: {{VALUE}}; outline-color: {{VALUE}};',
				],
			]
		);

		$this->widget->add_control(
			$prefix_name . 'arrow_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					$selector => 'background: {{VALUE}};',
				],
                'frontend_available' => true,
			]
		);
		$this->widget->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => $prefix_name . 'arrow_bg_shadow',
				'selector' => $selector,
			]
		);


		$this->widget->end_controls_tab();
	}

	/**
	 * @param string $prefix     Prefix.
	 * @param string $heading_name Heading name.
	 *
	 * @return void
	 */
	protected function add_arrow_position_styles( $prefix, $heading_name ) {
		$button_class  = '';
		$default_h_pos = 'left';
		if ( $prefix === 'next_' ) {
			$button_class  = '.owl-next';
			$swiper_button_class  = '.the7-swiper-button-next';
			$default_h_pos = 'right';
		} elseif ( $prefix === 'prev_' ) {
			$button_class = '.owl-prev';
			$swiper_button_class  = '.the7-swiper-button-prev';
		}

		$selector = '{{WRAPPER}} .owl-nav div' . $button_class . ',{{WRAPPER}} ' . $swiper_button_class;


		$this->widget->add_control(
			$prefix . 'arrow_position_heading',
			[
				'label'     => $heading_name,
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->widget->add_responsive_control(
			$prefix . 'arrow_v_position',
			[
				'label'                => esc_html__( 'Vertical Position', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'label_block'          => false,
				'options'              => [
					'top'    => [
						'title' => esc_html__( 'Top', 'the7mk2' ),
						'icon'  => 'eicon-v-align-top',
					],
					'center' => [
						'title' => esc_html__( 'Middle', 'the7mk2' ),
						'icon'  => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => esc_html__( 'Bottom', 'the7mk2' ),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'default'              => 'center',
				'selectors_dictionary' => [
					'top'    => 'top: calc(var(--arrow-v-offset) + var(--offset-v-t-img)); --arrow-translate-y:0;',
					'center' => 'top: var(--offset-v-m-img); --arrow-translate-y:calc(-50% + var(--arrow-v-offset));',
					'bottom' => 'top: calc(var(--top-b-img) + var(--arrow-v-offset) + var(--offset-v-b-img)); --arrow-translate-y:0;',
				],
				'selectors'            => [
					$selector => '{{VALUE}};',
				],
			]
		);

		$this->widget->add_responsive_control(
			$prefix . 'arrow_h_position',
			[
				'label'                => esc_html__( 'Horizontal Position', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'label_block'          => false,
				'options'              => [
					'left'   => [
						'title' => esc_html__( 'Left', 'the7mk2' ),
						'icon'  => 'eicon-h-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'the7mk2' ),
						'icon'  => 'eicon-h-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'the7mk2' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'default'              => $default_h_pos,
				'selectors_dictionary' => [
					'left'   => 'left: var(--arrow-h-offset); --arrow-translate-x:0;',
					'center' => 'left: calc(50% + var(--arrow-h-offset)); --arrow-translate-x:-50%;',
					'right'  => 'left: calc(100% - var(--arrow-h-offset)); --arrow-translate-x:-100%;',
				],
				'selectors'            => [
					$selector => '{{VALUE}};',
				],
			]
		);

		$this->widget->add_responsive_control(
			$prefix . 'arrow_v_offset',
			[
				'label'      => esc_html__( 'Vertical Offset', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 0,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => - 1000,
						'max'  => 1000,
						'step' => 1,
					],
				],
				'selectors'  => [
					$selector => '--arrow-v-offset: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->widget->add_responsive_control(
			$prefix . 'arrow_h_offset',
			[
				'label'      => esc_html__( 'Horizontal Offset', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 0,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => - 1000,
						'max'  => 1000,
						'step' => 1,
					],
				],
				'selectors'  => [
					$selector => '--arrow-h-offset: {{SIZE}}{{UNIT}};',
				],
			]
		);
	}

	/**
	 * @param string $element Element name.
	 *
	 * @return void
	 */
	public function add_container_render_attributes( $element ) {
		$settings = $this->widget->get_settings_for_display();

		if ( ! isset( $settings['normal_arrow_bg_color'], $settings['hover_arrow_bg_color'] ) || $settings['normal_arrow_bg_color'] === $settings['hover_arrow_bg_color'] ) {
			$this->widget->add_render_attribute( $element, 'class', 'disable-arrows-hover-bg' );
		}
	}

	/**
	 * @return void
	 */
	public function render() {
		echo '<div class="owl-nav disabled">';
		echo '<div class="owl-prev" role="button" tabindex="0" aria-label="Prev slide">';
		Icons_Manager::render_icon( $this->widget->get_settings_for_display( 'prev_icon' ) );
		echo '</div>';
		echo '<div class="owl-next" role="button" tabindex="0" aria-label="Next slide">';
		Icons_Manager::render_icon( $this->widget->get_settings_for_display( 'next_icon' ) );
		echo '</div>';
		echo '</div>';
	}
}
