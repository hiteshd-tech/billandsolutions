<?php
/**
 * The7 Heading widget for Elementor.
 *
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widgets;

defined( 'ABSPATH' ) || exit;

use Elementor\Utils;
use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Text_Stroke;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;

class Heading extends The7_Elementor_Widget_Base {
	const STICKY_WRAPPER = '.the7-e-sticky-effects .elementor-element.elementor-element-{{ID}}';

	/**
	 * Get element name.
	 */
	public function get_name() {
		return 'the7-heading';
	}

	/**
	 * Get element title.
	 */
	public function the7_title() {
		return esc_html__( 'Heading', 'the7mk2' );
	}

	/**
	 * Get element icon.
	 */
	public function the7_icon() {
		return 'eicon-t-letter';
	}

	/**
	 * Get element keywords.
	 *
	 * @return string[] Element keywords.
	 */
	protected function the7_keywords() {
		return [ 'heading', 'title', 'text' ];
	}

	/**
	 * Register controls.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_title',
			[
				'label' => esc_html__( 'Title', 'the7mk2' ),
			]
		);

		$this->add_control(
			'title',
			[
				'label'       => esc_html__( 'Title', 'the7mk2' ),
				'type'        => Controls_Manager::TEXTAREA,
				'dynamic'     => [
					'active' => true,
				],
				'placeholder' => esc_html__( 'Enter your title', 'the7mk2' ),
				'default'     => esc_html__( 'Add Your Heading Text Here', 'the7mk2' ),
			]
		);

		$this->add_control(
			'link',
			[
				'label'     => esc_html__( 'Link', 'the7mk2' ),
				'type'      => Controls_Manager::URL,
				'dynamic'   => [
					'active' => true,
				],
				'default'   => [
					'url' => '',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'size',
			[
				'label'   => esc_html__( 'Size', 'the7mk2' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => esc_html__( 'Default', 'the7mk2' ),
					'small'   => esc_html__( 'Small', 'the7mk2' ),
					'medium'  => esc_html__( 'Medium', 'the7mk2' ),
					'large'   => esc_html__( 'Large', 'the7mk2' ),
					'xl'      => esc_html__( 'XL', 'the7mk2' ),
					'xxl'     => esc_html__( 'XXL', 'the7mk2' ),
				],
			]
		);

		$this->add_control(
			'header_size',
			[
				'label'   => esc_html__( 'HTML Tag', 'the7mk2' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'h1'   => 'H1',
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'h5'   => 'H5',
					'h6'   => 'H6',
					'div'  => 'div',
					'span' => 'span',
					'p'    => 'p',
				],
				'default' => 'h2',
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label'     => esc_html__( 'Alignment', 'the7mk2' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
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
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}}' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'view',
			[
				'label'   => esc_html__( 'View', 'the7mk2' ),
				'type'    => Controls_Manager::HIDDEN,
				'default' => 'traditional',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_title_style',
			[
				'label' => esc_html__( 'Title', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'typography',
				'selector' => '{{WRAPPER}} .the7-heading-title',
				'exclude'  => [ 'text_decoration' ],
			]
		);

		$this->start_controls_tabs( 'tabs_title_colors' );

		$this->start_controls_tab(
			'tab_title_normal',
			[
				'label' => esc_html__( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__( 'Text Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => [
					'default' => Global_Colors::COLOR_PRIMARY,
				],
				'selectors' => [
					'{{WRAPPER}} .the7-heading-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'decoration',
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
					'{{WRAPPER}} .the7-heading-title' => 'text-decoration: {{VALUE}}',
				],
				'separator' => 'none',
			]
		);

		$this->add_control(
			'decoration_style',
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
					'decoration!' => 'none',
				],
				'selectors' => [
					'{{WRAPPER}} .the7-heading-title' => 'text-decoration-style: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'decoration_thickness',
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
					'{{WRAPPER}} .the7-heading-title' => 'text-decoration-thickness: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'decoration!' => 'none',
				],
			]
		);

		$this->add_control(
			'decoration_color',
			[
				'label'     => esc_html__( 'Decoration Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => [
					'default' => Global_Colors::COLOR_PRIMARY,
				],
				'condition' => [
					'decoration!' => 'none',
				],
				'selectors' => [
					'{{WRAPPER}} .the7-heading-title' => 'text-decoration-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			[
				'name'     => 'text_stroke',
				'selector' => '{{WRAPPER}} .the7-heading-title',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'     => 'text_shadow',
				'selector' => '{{WRAPPER}} .the7-heading-title',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_title_hover',
			[
				'label' => esc_html__( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			'title_color_hover',
			[
				'label'     => esc_html__( 'Text Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => [
					'default' => Global_Colors::COLOR_PRIMARY,
				],
				'selectors' => [
					'{{WRAPPER}} .the7-heading-title:hover, {{WRAPPER}} .the7-heading-title:hover a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'decoration_hover',
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
					'{{WRAPPER}} .the7-heading-title:hover' => 'text-decoration: {{VALUE}}',
				],
				'separator' => 'none',
			]
		);

		$this->add_control(
			'decoration_style_hover',
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
					'decoration_hover!' => 'none',
				],
				'selectors' => [
					'{{WRAPPER}} .the7-heading-title:hover' => 'text-decoration-style: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'decoration_thickness_hover',
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
					'{{WRAPPER}} .the7-heading-title:hover' => 'text-decoration-thickness: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'decoration_hover!' => 'none',
				],
			]
		);

		$this->add_control(
			'decoration_color_hover',
			[
				'label'     => esc_html__( 'Decoration Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => [
					'decoration_hover' => Global_Colors::COLOR_PRIMARY,
				],
				'selectors' => [
					'{{WRAPPER}} .the7-heading-title:hover' => 'text-decoration-color: {{VALUE}};',
				],
				'condition' => [
					'decoration_hover!' => 'none',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			[
				'name'     => 'text_stroke_hover',
				'selector' => '{{WRAPPER}} .the7-heading-title:hover',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'     => 'text_shadow_hover',
				'selector' => '{{WRAPPER}} .the7-heading-title:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();
		$this->add_control(
			'header_color_sticky',
			[
				'label'       => esc_html__( 'Change colors', 'the7mk2' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => esc_html__( 'When “Sticky” and “Change Styles when Sticky” are ON for the parent section.', 'the7mk2' ),
				'separator'   => 'before',
			]
		);

		$this->start_controls_tabs(
			'sticky_icon_colors',
			[
				'condition' => [
					'header_color_sticky' => 'yes',
				],
			]
		);

		$this->start_controls_tab(
			'sticky_header_colors_normal',
			[
				'label' => esc_html__( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_control(
			'sticky_primary_color',
			[
				'label'     => esc_html__( 'Text Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					self::STICKY_WRAPPER . ' .the7-heading-title' => 'color: {{VALUE}};',
				],
				'condition' => [
					'header_color_sticky' => 'yes',
				],
			]
		);

		$this->add_control(
			'sticky_decoration_color',
			[
				'label'     => esc_html__( 'Decoration Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => [
					'default' => Global_Colors::COLOR_PRIMARY,
				],
				'condition' => [
					'decoration!' => 'none',
				],
				'selectors' => [
					self::STICKY_WRAPPER . ' .the7-heading-title' => 'text-decoration-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			[
				'name'     => 'sticky_text_stroke',
				'selector' => self::STICKY_WRAPPER . ' .the7-heading-title',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'     => 'sticky_text_shadow',
				'selector' => self::STICKY_WRAPPER . ' .the7-heading-title',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'sticky_header_colors_hover',
			[
				'label'     => esc_html__( 'Hover', 'the7mk2' ),
				'condition' => [
					'header_color_sticky' => 'yes',
				],
			]
		);

		$this->add_control(
			'sticky_hover_primary_color',
			[
				'label'     => esc_html__( 'Text Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					self::STICKY_WRAPPER . ' .the7-heading-title:hover' => 'color: {{VALUE}};',
					self::STICKY_WRAPPER . ' .the7-heading-title:hover a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'sticky_decoration_color_hover',
			[
				'label'     => esc_html__( 'Decoration Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => [
					'decoration_hover' => Global_Colors::COLOR_PRIMARY,
				],
				'selectors' => [
					self::STICKY_WRAPPER . ' .the7-heading-title:hover' => 'text-decoration-color: {{VALUE}};',
				],
				'condition' => [
					'decoration_hover!' => 'none',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			[
				'name'     => 'sticky_text_stroke_hover',
				'selector' => self::STICKY_WRAPPER . ' .the7-heading-title:hover',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'     => 'sticky_text_shadow_hover',
				'selector' => self::STICKY_WRAPPER . ' .the7-heading-title:hover',
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Render heading widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( $settings['title'] === '' ) {
			return;
		}

		$this->add_render_attribute( 'title', 'class', 'the7-heading-title' );

		if ( ! empty( $settings['size'] ) ) {
			$this->add_render_attribute( 'title', 'class', 'elementor-size-' . $settings['size'] );
		}

		$this->add_inline_editing_attributes( 'title' );

		$title = $settings['title'];

		if ( ! empty( $settings['link']['url'] ) ) {
			$this->add_link_attributes( 'url', $settings['link'] );

			$title = sprintf( '<a %1$s>%2$s</a>', $this->get_render_attribute_string( 'url' ), $title );
		}

		$title_html = sprintf( '<%1$s %2$s>%3$s</%1$s>', Utils::validate_html_tag( $settings['header_size'] ), $this->get_render_attribute_string( 'title' ), $title );
		// PHPCS - the variable $title_html holds safe data.
		echo $title_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Render heading widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 */
	protected function content_template() {
		?>
		<#
		let title = elementor.helpers.sanitize( settings.title, { ALLOW_DATA_ATTR: false } );

		if ( '' !== settings.link.url ) {
			title = '<a href="' + _.escape( settings.link.url ) + '">' + title + '</a>';
		}

		view.addRenderAttribute( 'title', 'class', [ 'the7-heading-title', 'elementor-size-' + settings.size ] );

		view.addInlineEditingAttributes( 'title' );

		var headerSizeTag = elementor.helpers.validateHTMLTag( settings.header_size ),
			title_html = '<' + headerSizeTag  + ' ' + view.getRenderAttributeString( 'title' ) + '>' + title + '</' + headerSizeTag + '>';

		print( title_html );
		#>
		<?php
	}
}
