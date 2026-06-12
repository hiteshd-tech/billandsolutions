<?php
/**
 * The7 elements scroller widget for Elementor.
 *
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widgets;

use Elementor\Plugin;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;
use Elementor\Utils;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Text_Stroke;
use Elementor\Repeater;
use Elementor\Icons_Manager;

defined( 'ABSPATH' ) || exit;

class Ticker extends The7_Elementor_Widget_Base {

	/**
	 * Get element name.
	 *
	 * Retrieve the element name.
	 *
	 * @return string The name.
	 */
	public function get_name() {
		return 'the7-ticker-widget';
	}

	protected function the7_title() {
		return esc_html__( 'Ticker', 'the7mk2' );
	}

	protected function the7_icon() {
		return 'eicon-animation-text';
	}

	protected function the7_keywords() {
		return [ 'ticker', 'text' ];
	}

	public function get_style_depends() {
		return [ $this->get_name() ];
	}

	/**
	 * Register widget assets.
	 */
	protected function register_assets() {
		the7_register_style(
			$this->get_name(),
			THE7_ELEMENTOR_CSS_URI . '/the7-ticker-widget.css'
		);
	}


	protected function register_controls() {
		// Content.
		$this->add_content_controls();

		// Style.
		$this->add_content_style_controls();
	}

	protected function add_content_controls() {
		$this->start_controls_section(
			'ticker_section',
			[
				'label' => esc_html__( 'Items', 'the7mk2' ),
			]
		);
		$repeater = new Repeater();
		$repeater->add_control(
			'ticker_content',
			[
				'label'       => esc_html__( 'Content', 'the7mk2' ),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'default' => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'the7mk2' ),
				'placeholder' => esc_html__( 'Enter your description', 'the7mk2' ),
				'rows' => 10,
			]
		);
		$repeater->add_control(
			'link',
			[
				'label' => esc_html__( 'Link', 'the7mk2' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( 'https://your-link.com', 'the7mk2' ),
			]
		);
		$this->add_control(
			'ticker_items',
			[
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[
						'ticker_content' => esc_html__( 'Lorem ipsum dolor sit amet.', 'the7mk2' ),
					],
					[
						'ticker_content' => esc_html__( 'Lorem ipsum dolor sit amet.', 'the7mk2' ),
					],
					[
						'ticker_content' => esc_html__( 'Lorem ipsum dolor sit amet.', 'the7mk2' ),
					],
				],
			]
		);
		$this->add_control(
			'ticker_pause_animation',
			[
				'label'     => esc_html__( 'Pause on hover', 'the7mk2' ),
				'type'      => Controls_Manager::SWITCHER,
				'yes' => esc_html__( 'Yes', 'the7mk2' ),
				'no'  => esc_html__( 'No', 'the7mk2' ),
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
		$this->add_control(
			'ticker_separator',
			[
				'label'   => esc_html__( 'Separator Between', 'the7mk2' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'text' => [
						'title' => esc_html__( 'Text', 'the7mk2' ),
						'icon'  => 'eicon-font',
					],
					'icon' => [
						'title' => esc_html__( 'Icon', 'the7mk2' ),
						'icon'  => 'eicon-star',
					],
				],
				'default' => 'text',
			]
		);
		$this->add_control(
			'ticker_meta_separator',
			[
				'label'     => esc_html__( 'Text', 'the7mk2' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '/',
				'selectors' => [
					'{{WRAPPER}} .the7-ticker-item:before' => 'content: "{{VALUE}}"',
				],
				'condition' => [
					'ticker_separator' => 'text',
				],
			]
		);
		$this->add_control(
			'ticker_icon_separator',
			[
				'label'            => esc_html__( 'Icon', 'the7mk2' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default'          => [
					'value'   => 'fas fa-star',
					'library' => 'fa-solid',
				],
				'selectors'        => [
					'{{WRAPPER}} .the7-ticker-item:before' => 'display: none',
				],
				'condition'        => [
					'ticker_separator' => 'icon',
				],
				'render_type'      => 'template',
			]
		);

		$this->end_controls_section();
	}



	protected function add_content_style_controls() {
		$this->start_controls_section(
			'section_style_ticker',
			[
				'label' => esc_html__( 'Content', 'the7mk2' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'ticker_typography',
				'selector' => '{{WRAPPER}} .the7-ticker-item',
			]
		);
		$this->start_controls_tabs( 'tabs_title_colors' );

		$this->start_controls_tab(
			'tab_ticker_normal',
			[
				'label' => esc_html__( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_control(
			'ticker_color',
			[
				'label'     => esc_html__( 'Text Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .the7-ticker-item' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'ticker_decoration',
			[
				'label'     => esc_html__( 'Decoration', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					''             => esc_html__( 'Default', 'the7mk2' ),
					'underline'    => esc_html__( 'Underline', 'the7mk2' ),
					'overline'     => esc_html__( 'Overline', 'the7mk2' ),
					'line-through' => esc_html__( 'Line Through', 'the7mk2' ),
					'none'         => esc_html__( 'None', 'the7mk2' ),
				],
				'selectors' => [
					'{{WRAPPER}} .ticker-content' => 'text-decoration: {{VALUE}}',
				],
				'separator' => 'none',
			]
		);

		$this->add_control(
			'ticker_decoration_style',
			[
				'label'     => esc_html__( 'Decoration Style', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'solid'  => esc_html__( 'Solid', 'the7mk2' ),
					'dotted' => esc_html__( 'Dotted', 'the7mk2' ),
					'dashed' => esc_html__( 'Dashed', 'the7mk2' ),
					'wavy'   => esc_html__( 'Wavy', 'the7mk2' ),
				],
				'default'   => 'solid',
				'condition' => [
					'ticker_decoration!' => 'none',
				],
				'selectors' => [
					'{{WRAPPER}} .ticker-content' => 'text-decoration-style: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'ticker_decoration_thickness',
			[
				'label'      => esc_html__( 'Decoration Thickness', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 25,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .ticker-content' => 'text-decoration-thickness: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'ticker_decoration!' => 'none',
				],
			]
		);

		$this->add_control(
			'ticker_decoration_color',
			[
				'label'     => esc_html__( 'Decoration Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'ticker_decoration!' => 'none',
				],
				'selectors' => [
					'{{WRAPPER}} .ticker-content' => 'text-decoration-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			[
				'name'     => 'ticker_text_stroke',
				'selector' => '{{WRAPPER}} .ticker-content',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'     => 'ticker_text_shadow',
				'selector' => '{{WRAPPER}} .ticker-content',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_ticker_hover',
			[
				'label' => esc_html__( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			'ticker_color_hover',
			[
				'label'     => esc_html__( 'Text Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .the7-ticker-item:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'ticker_decoration_hover',
			[
				'label'     => esc_html__( 'Decoration', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					''             => esc_html__( 'Default', 'the7mk2' ),
					'underline'    => esc_html__( 'Underline', 'the7mk2' ),
					'overline'     => esc_html__( 'Overline', 'the7mk2' ),
					'line-through' => esc_html__( 'Line Through', 'the7mk2' ),
					'none'         => esc_html__( 'None', 'the7mk2' ),
				],
				'selectors' => [
					'{{WRAPPER}} .ticker-content:hover' => 'text-decoration: {{VALUE}}',
				],
				'separator' => 'none',
			]
		);

		$this->add_control(
			'ticker_decoration_style_hover',
			[
				'label'     => esc_html__( 'Decoration Style', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'solid'  => esc_html__( 'Solid', 'the7mk2' ),
					'dotted' => esc_html__( 'Dotted', 'the7mk2' ),
					'dashed' => esc_html__( 'Dashed', 'the7mk2' ),
					'wavy'   => esc_html__( 'Wavy', 'the7mk2' ),
				],
				'condition' => [
					'ticker_decoration_hover!' => 'none',
				],
				'selectors' => [
					'{{WRAPPER}} .ticker-content:hover' => 'text-decoration-style: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'ticker_decoration_thickness_hover',
			[
				'label'      => esc_html__( 'Decoration Thickness', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 25,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .ticker-content:hover' => 'text-decoration-thickness: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'ticker_decoration_hover!' => 'none',
				],
			]
		);

		$this->add_control(
			'ticker_decoration_color_hover',
			[
				'label'     => esc_html__( 'Decoration Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ticker-content:hover' => 'text-decoration-color: {{VALUE}};',
				],
				'condition' => [
					'ticker_decoration_hover!' => 'none',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			[
				'name'     => 'ticker_stroke_hover',
				'selector' => '{{WRAPPER}} .ticker-content:hover',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'     => 'ticker_text_shadow_hover',
				'selector' => '{{WRAPPER}} .ticker-content:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();
		$this->add_control(
			'ticker_separator-heading',
			[
				'type'      => Controls_Manager::HEADING,
				'label'     => esc_html__( 'Separator', 'the7mk2' ),
				'separator' => 'before',
			]
		);

		$this->add_control(
			'ticker_divider_color',
			[
				'label'     => esc_html__( 'Separator Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .the7-ticker-item:before, {{WRAPPER}} .the7-ticker-item i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .the7-ticker-item svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'ticker_divider_size',
			[
				'label'      => esc_html__( 'Separator size', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'vw' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .the7-ticker-item' => '--sub-icon-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .the7-ticker-item:before, {{WRAPPER}} .the7-ticker-item i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .the7-ticker-item svg' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);


		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'ticker_text', 'class', 'ticker-content' );

		$icon_separator = '';
		if ( $settings['ticker_icon_separator'] !== '' && $settings['ticker_separator'] === 'icon' ) {
			$icon_separator = $this->get_elementor_icon_html( $settings['ticker_icon_separator'] );
		}
		ob_start();
        foreach ( $settings['ticker_items'] as $index => $item ) {
            $tab_content_setting_key = $this->get_repeater_setting_key( 'ticker_content', 'tabs', $index );
            $this->add_render_attribute( $tab_content_setting_key, 'class', 'ticker-content' );
            $this->add_inline_editing_attributes( $tab_content_setting_key );
            $wrap_key = 'wrap_' . $index;
            $this->add_render_attribute( $wrap_key, 'class', 'the7-ticker-item' );
            $tag = 'div';
            if ( ! empty( $item['link']['url'] ) ) {
                $tag = 'a';
                $this->add_link_attributes( $wrap_key, $item['link'] );
            }
            echo '<' . $tag . ' ' . $this->get_render_attribute_string( $wrap_key ) . '>';
                echo $icon_separator;
                if ( ! Utils::is_empty( $item['ticker_content'] ) ) {
                    echo '<span ' . $this->get_render_attribute_string( $tab_content_setting_key ) . '>';
                     echo esc_html($item['ticker_content']);
                    echo '</span>';
                }
            echo '</' . $tag . '>';
        }
        $ticker_html = ob_get_clean();
        echo '<div class="the7-ticker">';
			echo '<div class="the7-ticker-content">';
				echo $ticker_html;
			echo '</div>';
			echo '<div class="the7-ticker-content" aria-hidden="true">';
				echo $ticker_html;
			echo '</div>';
		echo '</div>';
	}
}
