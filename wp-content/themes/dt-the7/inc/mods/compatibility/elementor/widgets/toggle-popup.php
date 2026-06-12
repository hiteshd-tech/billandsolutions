<?php
/**
 * The7 toggle widget for Elementor.
 *
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\TemplateLibrary\Source_Local;
use ElementorPro\Modules\Popup\Module as PopupModule;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Elementor toggle widget class.
 */
class Toggle_Popup extends The7_Elementor_Widget_Base {

	/**
	 * Get widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'the7-toggle-popup-widget';
	}

	/**
	 * Get widget title.
	 *
	 * @return string Widget title.
	 */
	protected function the7_title() {
		return esc_html__( 'Toggle Popup', 'the7mk2' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string Widget icon.
	 */
	protected function the7_icon() {
		return 'eicon-button';
	}

	/**
	 * Get widget categories.
	 *
	 * @return array Widget categories.
	 */
	protected function the7_keywords() {
		return [ 'toggle', 'hamburger', 'popup button' ];
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
		the7_register_style(
			$this->get_name(),
			THE7_ELEMENTOR_CSS_URI . '/the7-toggle-popup-widget.css'
		);
		the7_register_script_in_footer(
			$this->get_name(),
			THE7_ELEMENTOR_JS_URI . '/the7-toggle-popup-widget.js'
		);
	}

	/**
	 * Register button widget controls.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_toggle',
			[
				'label' => esc_html__( 'Toggle', 'the7mk2' ),
			]
		);
		$this->add_control(
			'skin',
			[
				'label'        => esc_html__( 'Icon skin', 'the7mk2' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'skin_1',
				'options'      => [
					'skin_1' => esc_html__( 'Skin 1', 'the7mk2' ),
					'skin_2' => esc_html__( 'Skin 2', 'the7mk2' ),
					'skin_3' => esc_html__( 'Skin 3', 'the7mk2' ),
					'skin_4' => esc_html__( 'Skin 4', 'the7mk2' ),
					'skin_5' => esc_html__( 'Skin 5', 'the7mk2' ),
					'skin_6' => esc_html__( 'Skin 6', 'the7mk2' ),
				],
				'render_type'  => 'template',
				'prefix_class' => 'hamburger-',
			]
		);

		$this->add_control(
			'popup_link',
			[
				'label'   => esc_html__( 'Popup', 'the7mk2' ),
				'type'    => Controls_Manager::SELECT2,
				'options' => $this->get_popups_list(),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_toggle',
			[
				'label' => esc_html__( 'Toggle Button', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'toggle_text',
			[
				'label'       => esc_html__( 'Toggle Text', 'the7mk2' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [
					'active' => true,
				],
				'default'     => '',
				'placeholder' => esc_html__( 'Toggle text', 'the7mk2' ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'toggle_typography',
				'selector'  => '{{WRAPPER}} .the7-hamburger-text',
				'global'    => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
				'condition' => [
					'toggle_text!' => '',
				],
			]
		);
		$selector = '{{WRAPPER}} .the7-hamburger';

		$this->add_responsive_control(
			'toggle_icon_space',
			[
				'label'     => esc_html__( 'Spacing', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					$selector => '--toggle-icon-spacing: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'toggle_text!' => '',
				],
			]
		);

		$this->add_control(
			'toggle_icon_align',
			[
				'label'                => esc_html__( 'Position', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => [
					'left'  => [
						'title' => esc_html__( 'Start', 'the7mk2' ),
						'icon'  => 'eicon-h-align-left',
					],
					'right' => [
						'title' => esc_html__( 'End', 'the7mk2' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'condition'            => [
					'toggle_text!' => '',
				],
				'selectors_dictionary' => [
					'left'  => 'order: 0; margin-right: var(--toggle-icon-spacing); margin-left: 0;',
					'right' => 'order: 2; margin-left: var(--toggle-icon-spacing); margin-right: 0;',
				],
				'selectors'            => [
					'{{WRAPPER}} .the7-hamburger-text' => ' {{VALUE}};',
				],
				'prefix_class'         => 'toggle-icon_position-',
				'default'              => is_rtl() ? 'left' : 'right',
				'toggle'               => false,
			]
		);
		$this->add_control(
			'toggle_icon_heading',
			[
				'label'     => esc_html__( 'Icon', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'toggle_icon_width',
			[
				'label'      => esc_html__( 'Size', 'the7mk2' ),
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
					'{{WRAPPER}}' => '--toggle-width: {{SIZE}}{{UNIT}}; --toggle-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'toggle_box_heading',
			[
				'label'     => esc_html__( 'Box', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'padding_toggle',
			[
				'label'      => esc_html__( 'Paddings', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'    => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					$selector => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'toggle_min_width',
			[
				'label'     => esc_html__( 'Min Width', 'the7mk2' ),
				'type'      => Controls_Manager::NUMBER,
				'selectors' => [
					$selector => 'min-width: {{SIZE}}px;',
				],
			]
		);

		$this->add_responsive_control(
			'toggle_min_height',
			[
				'label'     => esc_html__( 'Min Height', 'the7mk2' ),
				'type'      => Controls_Manager::NUMBER,
				'selectors' => [
					$selector => 'min-height: {{SIZE}}px;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'toggle_border',
				'selector' => $selector,
				'exclude'  => [ 'color' ],
			]
		);
		$this->add_responsive_control(
			'toggle_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					$selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'toggle_colors_heading',
			[
				'label'     => esc_html__( 'Colors', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->start_controls_tabs( 'tabs_toggle_colors' );

		$this->start_controls_tab(
			'tab_toggle_normal',
			[
				'label' => esc_html__( 'Normal', 'the7mk2' ),
			]
		);
		$this->add_control(
			'toggle_text_color',
			[
				'label'     => esc_html__( 'Text Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => [
					'default' => Global_Colors::COLOR_TEXT,
				],
				'selectors' => [
					'{{WRAPPER}} .the7-hamburger-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'toggle_icon_color',
			[
				'label'     => esc_html__( 'Icon color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .the7-hamburger-line span,  {{WRAPPER}} .the7-hamburger-cross span' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'toggle_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => [
					'default' => Global_Colors::COLOR_SECONDARY,
				],
				'selectors' => [
					'{{WRAPPER}} .the7-hamburger' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'toggle_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .the7-hamburger' => 'border-color: {{VALUE}}',
				],
				'condition' => [
					'toggle_border_border!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'           => 'toggle_box_shadow',
				'selector'       => '{{WRAPPER}} .the7-hamburger',
				'fields_options' => [
					'box_shadow_type' => [
						'separator' => 'default',
					],
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_toggle_hover',
			[
				'label' => esc_html__( 'Hover', 'the7mk2' ),
			]
		);
		$this->add_control(
			'toggle_text_color_hover',
			[
				'label'     => esc_html__( 'Text Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => [
					'default' => Global_Colors::COLOR_TEXT,
				],
				'selectors' => [
					'{{WRAPPER}} .the7-hamburger:hover .the7-hamburger-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'toggle_icon_color_hover',
			[
				'label'     => esc_html__( 'Icon color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .the7-hamburger:hover .the7-hamburger-line span, {{WRAPPER}} .the7-hamburger:hover .the7-hamburger-cross span' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'toggle_background_color_hover',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .the7-hamburger:hover' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'toggle_border_hover_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .the7-hamburger:hover' => 'border-color: {{VALUE}}',
				],
				'condition' => [
					'toggle_border_border!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'           => 'toggle_box_shadow_hover',
				'selector'       => '{{WRAPPER}} .the7-hamburger:hover',
				'fields_options' => [
					'box_shadow_type' => [
						'separator' => 'default',
					],
				],
			]
		);

		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_toggle_active',
			[
				'label' => esc_html__( 'Active', 'the7mk2' ),
			]
		);
		$this->add_control(
			'toggle_text_color_active',
			[
				'label'     => esc_html__( 'Text Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => [
					'default' => Global_Colors::COLOR_TEXT,
				],
				'selectors' => [
					'{{WRAPPER}}.active .the7-hamburger .the7-hamburger-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'toggle_icon_color_active',
			[
				'label'     => esc_html__( 'Icon color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}.active .the7-hamburger .the7-hamburger-line span, {{WRAPPER}}.active .the7-hamburger .the7-hamburger-cross span' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'toggle_background_color_active',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}.active .the7-hamburger' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'toggle_border_active_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}.active .the7-hamburger' => 'border-color: {{VALUE}}',
				],
				'condition' => [
					'toggle_border_border!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'           => 'toggle_box_shadow_active',
				'selector'       => '{{WRAPPER}}.active .the7-hamburger',
				'fields_options' => [
					'box_shadow_type' => [
						'separator' => 'default',
					],
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Render button widget output on the frontend.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$this->add_render_attribute(
			'the7-hamburger',
			[
				'href'  => $this->get_popup_url( $settings['popup_link'] ),
				'class' => 'the7-hamburger',
			]
		);
		?>
		<a <?php $this->print_render_attribute_string( 'the7-hamburger' ); ?>>
			<?php if ( $settings['toggle_text'] !== '' ) { ?>
				<span class="the7-hamburger-text"><?php echo esc_html( $settings['toggle_text'] ); ?></span>
			<?php } ?>
			<div class="the7-hamburger-icon">
				<span class="the7-hamburger-line"><span></span><span></span><span></span></span>
				<span class="the7-hamburger-cross"><span></span><span></span></span>
			</div>
		</a>
		<?php
	}

	/**
	 * @return array
	 */
	protected function get_popups_list() {
		$popups_query = new \WP_Query(
			[
				'post_type'      => Source_Local::CPT,
				'post_status'    => 'publish',
				'posts_per_page' => - 1,
				'meta_key'       => '_elementor_template_type',
				'meta_value'     => PopupModule::DOCUMENT_TYPE,
			]
		);

		return wp_list_pluck( $popups_query->posts, 'post_title', 'ID' );
	}

}
