<?php
/**
 * The7 icon widget for Elementor.
 *
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widgets;

use Elementor\Plugin;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;

defined( 'ABSPATH' ) || exit;

/**
 * Icon class.
 */
class Icon extends The7_Elementor_Widget_Base {

	const STICKY_WRAPPER = '.the7-e-sticky-effects .elementor-element.elementor-element-{{ID}}';

	/**
	 * Get element name.
	 *
	 * Retrieve the element name.
	 *
	 * @return string The name.
	 */
	public function get_name() {
		return 'the7_icon_widget';
	}

	/**
	 * @return string|void
	 */
	protected function the7_title() {
		return esc_html__( 'Icon', 'the7mk2' );
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
	protected function the7_keywords() {
		return [ 'icon' ];
	}

	/**
	 * @return string[]
	 */
	public function get_style_depends() {
		return [ 'the7-icon-widget' ];
	}

	/**
	 * Register assets.
	 */
	protected function register_assets() {
		the7_register_style( 'the7-icon-widget', THE7_ELEMENTOR_CSS_URI . '/the7-icon-widget.css' );
	}

	/**
	 * Register controls.
	 */
	protected function register_controls() {
		$this->add_content_controls();
		$this->add_icon_style_controls();
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
			'selected_icon',
			[
				'label'            => esc_html__( 'Icon', 'the7mk2' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default'          => [
					'value'   => 'fas fa-star',
					'library' => 'fa-solid',
				],
			]
		);

		$this->add_control(
			'link_heading',
			[
				'label'     => esc_html__( 'Link', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'link',
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
		    'description',
		    [
			    'label'       => esc_html__( 'Accessibility Description', 'the7mk2' ),
			    'type'        => Controls_Manager::TEXT,
			    'default'     => '',
			    'placeholder' => '',
			    'dynamic'     => [
				    'active' => true,
			    ],
		    ]
		);

		$this->end_controls_section();
	}

	/**
	 * Icon style controls.
	 */
	protected function add_icon_style_controls() {
		$this->start_controls_section(
			'section_style_icon',
			[
				'label' => esc_html__( 'Icon', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label'                => esc_html__( 'Alignment', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => [
					'left'    => [
						'title' => esc_html__( 'Left', 'the7mk2' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center'  => [
						'title' => esc_html__( 'Center', 'the7mk2' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'   => [
						'title' => esc_html__( 'Right', 'the7mk2' ),
						'icon'  => 'eicon-text-align-right',
					],
					'justify' => [
						'title' => esc_html__( 'Justified', 'the7mk2' ),
						'icon'  => 'eicon-text-align-justify',
					],
				],
				'prefix_class'         => 'elementor%s-align-',
				'selectors_dictionary' => [
					'left'    => 'display: inline-flex; justify-content: center; align-items: center;',
					'center'  => 'display: inline-flex; justify-content: center; align-items: center;',
					'right'   => 'display: inline-flex; justify-content: center; align-items: center;',
					'justify' => 'display: flex; justify-content: center; align-items: center;',
				],
				'default'              => 'center',
				'selectors'            => [
					'{{WRAPPER}} .elementor-icon' => '{{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'size',
			[
				'label'     => esc_html__( 'Size', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 6,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'icon_min_width',
			[
				'label'      => esc_html__( 'Min Width', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => '',
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 1,
						'max' => 500,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .elementor-icon' => 'min-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_min_height',
			[
				'label'      => esc_html__( 'Min Height', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => '',
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
					'{{WRAPPER}} .elementor-icon' => 'min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_padding',
			[
				'label'      => esc_html__( 'Padding', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'selectors'  => [
					'{{WRAPPER}} .elementor-icon' => 'padding: {{SIZE}}{{UNIT}};',
				],
				'size_units' => [ 'px', 'em' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
					'em' => [
						'min'  => 0.1,
						'max'  => 5,
						'step' => 0.01,
					],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'box_border',
				'label'    => esc_html__( 'Border', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .elementor-icon',
				'exclude'  => [
					'color',
				],
			]
		);

		$this->add_control(
			'border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .elementor-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'icon_colors' );

		$this->start_controls_tab(
			'icon_colors_normal',
			[
				'label' => esc_html__( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_control(
			'primary_color',
			[
				'label'     => esc_html__( 'Icon Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-icon i'   => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementor-icon svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'border_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-icon' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'box_border_border!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'           => 'icon_bg_color',
				'types'          => [ 'classic', 'gradient' ],
				'exclude'        => [ 'image' ],
				'fields_options' => [
					'background' => [
						'label' => esc_html__( 'Background', 'the7mk2' ),
					],
				],
				'selector'       => '{{WRAPPER}} .elementor-icon',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'icon_box_shadow',
				'label'    => esc_html__( 'Box Shadow', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .elementor-icon',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'icon_colors_hover',
			[
				'label' => esc_html__( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			'hover_primary_color',
			[
				'label'     => esc_html__( 'Icon Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-icon:hover i'     => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementor-icon:hover svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'hover_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-icon:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'box_border_border!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'           => 'icon_hover_bg_color',
				'types'          => [ 'classic', 'gradient' ],
				'exclude'        => [ 'image' ],
				'fields_options' => [
					'background' => [
						'label' => esc_html__( 'Background', 'the7mk2' ),
					],
					'color'      => [
						'selectors' => [
							'{{SELECTOR}}' => 'background: {{VALUE}}',
						],
					],
				],
				'selector'       => '{{WRAPPER}} .elementor-icon:hover',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'icon_hover_box_shadow',
				'label'    => esc_html__( 'Box Shadow', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .elementor-icon:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();
		$this->add_control(
			'icon_color_sticky',
			[
				'label'       => esc_html__( 'Change colors', 'the7mk2' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => esc_html__( 'When “Sticky” and “Change Styles when Sticky” are ON for the parent section.', 'the7mk2' ),
				'separator'   => 'before',
			]
		);

		$this->start_controls_tabs( 'sticky_icon_colors' );

		$this->start_controls_tab(
			'sticky_icon_colors_normal',
			[
				'label' => esc_html__( 'Normal', 'the7mk2' ),
				'condition' => [
					'icon_color_sticky' => 'yes',
				],
			]
		);

		$this->add_control(
			'sticky_primary_color',
			[
				'label'     => esc_html__( 'Icon Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					self::STICKY_WRAPPER . ' .elementor-icon i'   => 'color: {{VALUE}};',
					self::STICKY_WRAPPER . ' .elementor-icon svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
				'condition' => [
					'icon_color_sticky' => 'yes',
				],
			]
		);

		$this->add_control(
			'sticky_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					self::STICKY_WRAPPER . ' .elementor-icon' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'box_border_border!' => '',
					'icon_color_sticky' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'           => 'sticky_icon_bg_color',
				'types'          => [ 'classic', 'gradient' ],
				'exclude'        => [ 'image' ],
				'fields_options' => [
					'background' => [
						'label' => esc_html__( 'Background', 'the7mk2' ),
					],
				],
				'selector'       => self::STICKY_WRAPPER . ' .elementor-icon',
				'condition' => [
					'icon_color_sticky' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'sticky_icon_box_shadow',
				'label'    => esc_html__( 'Box Shadow', 'the7mk2' ),
				'selector' => self::STICKY_WRAPPER . ' .elementor-icon',
				'condition' => [
					'icon_color_sticky' => 'yes',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'sticky_icon_colors_hover',
			[
				'label' => esc_html__( 'Hover', 'the7mk2' ),
				'condition' => [
					'icon_color_sticky' => 'yes',
				],
			]
		);

		$this->add_control(
			'sticky_hover_primary_color',
			[
				'label'     => esc_html__( 'Icon Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'.the7-e-sticky-effects .elementor-element.elementor-element-{{ID}} .elementor-icon:hover i'     => 'color: {{VALUE}};',
					self::STICKY_WRAPPER . ' .elementor-icon:hover svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
				'condition' => [
					'icon_color_sticky' => 'yes',
				],
			]
		);

		$this->add_control(
			'sticky_hover_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					self::STICKY_WRAPPER . ' .elementor-icon:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'box_border_border!' => '',
					'icon_color_sticky' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'           => 'sticky_icon_hover_bg_color',
				'types'          => [ 'classic', 'gradient' ],
				'exclude'        => [ 'image' ],
				'fields_options' => [
					'background' => [
						'label' => esc_html__( 'Background', 'the7mk2' ),
					],
					'color'      => [
						'selectors' => [
							'{{SELECTOR}}' => 'background: {{VALUE}}',
						],
					],
				],
				'condition' => [
					'icon_color_sticky' => 'yes',
				],
				'selector'       => self::STICKY_WRAPPER . ' .elementor-icon:hover',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'sticky_icon_hover_box_shadow',
				'label'    => esc_html__( 'Box Shadow', 'the7mk2' ),
				'selector' => self::STICKY_WRAPPER . ' .elementor-icon:hover',
				'condition' => [
					'icon_color_sticky' => 'yes',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Render widget.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		echo '<div class="the7-icon-wrapper the7-elementor-widget">';
		if ( $settings['selected_icon']['value'] !== '' ) {
			$this->add_render_attribute( 'icon_wrapper', 'class', 'elementor-icon' );
			$tag = 'div';
			if ( ! empty( $settings['link']['url'] ) ) {
				$this->add_link_attributes( 'icon_wrapper', $settings['link'] );
				$tag = 'a';
			}

			echo "<{$tag} " . $this->get_render_attribute_string( 'icon_wrapper' ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			Icons_Manager::render_icon( $settings['selected_icon'], [ 'aria-hidden' => 'true' ] );
			$description = $this->get_settings_for_display( 'description' );
			if ( $description ) {
				echo '<span class="screen-reader-text">' . esc_html( $description ) . '</span>';
			}
			echo "</{$tag}>"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		echo '</div>';
	}

	/**
	 * Content template.
	 */
	protected function content_template() {
		?>
        <#
        var link = settings.link.url ? 'href="' + _.escape( settings.link.url ) + '"' : '',
        iconHTML = elementor.helpers.renderIcon( view, settings.selected_icon, { 'aria-hidden': true }, 'i' , 'object' ),
        iconTag = link ? 'a' : 'div';
        #>
        <div class="the7-icon-wrapper the7-elementor-widget">
            <{{{ iconTag }}} class="elementor-icon" {{{ link }}}>
            <# if ( iconHTML && iconHTML.rendered ) { #>
            {{{ iconHTML.value }}}
            <# } #>
        </{{{ iconTag }}}>
        </div>
		<?php
	}

}
