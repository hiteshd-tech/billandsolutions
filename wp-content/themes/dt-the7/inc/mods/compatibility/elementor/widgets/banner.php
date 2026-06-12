<?php
/**
 * The7 Elementor Banner widget.
 *
 * @package The7\Mods\Compatibility\Elementor\Widgets
 */

namespace The7\mods\compatibility\elementor\widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Icons_Manager;
use Elementor\Plugin;
use Elementor\TemplateLibrary\Source_Local;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;
use The7_Elementor_Compatibility;

/**
 * Banner widget class.
 */
class Banner extends The7_Elementor_Widget_Base {

	const WIDGET_NAME   = 'the7-banner';
	const DOCUMENT_TYPE = 'container';

	/**
	 * @return string|void
	 */
	protected function the7_title() {
		return esc_html__( 'Collapsible banner', 'the7mk2' );
	}

	/**
	 * @return string
	 */
	protected function the7_icon() {
		return 'eicon-image-rollover';
	}

	/**
	 * @return string[]
	 */
	protected function the7_keywords() {
		return [ 'banner', 'call to action', 'cta', 'collaps' ];
	}

	/**
	 * @return string
	 */
	public function get_name() {
		return self::WIDGET_NAME;
	}

	/**
	 * Get style dependencies.
	 *
	 * Retrieve the list of style dependencies the element requires.
	 *
	 * @return array Element styles dependencies.
	 * @access public
	 */
	public function get_style_depends() {
		$styles   = parent::get_style_depends();
		$styles[] = 'the7-banner-widget';

		return $styles;
	}

	/**
	 * Get script dependencies.
	 *
	 * Retrieve the list of script dependencies the element requires.
	 *
	 * @return array Element scripts dependencies.
	 * @access public
	 */
	public function get_script_depends() {
		$scripts   = parent::get_script_depends();
		$scripts[] = 'the7-banner-widget';
		$scripts[] = 'the7-cookies';

		return $scripts;
	}

	/**
	 * Register widget assets.
	 *
	 * @see The7_Elementor_Widget_Base::__construct()
	 */
	protected function register_assets() {
		the7_register_style( 'the7-banner-widget', THE7_ELEMENTOR_CSS_URI . '/the7-banner.css' );
		the7_register_script_in_footer( 'the7-banner-widget', THE7_ELEMENTOR_JS_URI . '/the7-banner.js' );
	}

	/**
	 * Register widget controls.
	 */
	protected function register_controls() {
		$this->add_content_controls();
		$this->add_style_controls();
	}

	/**
	 * Add content controls.
	 */
	protected function add_content_controls() {
		// 'section_layout' name is important for createTemplate js function
		$this->start_controls_section(
			'section_layout',
			[
				'label' => esc_html__( 'Content', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$source      = Plugin::$instance->templates_manager->get_source( 'local' );
		$templates   = $source->get_items( [ 'type' => self::DOCUMENT_TYPE ] );
		$library_ids = wp_list_pluck( $templates, 'title', 'template_id' );

		$this->add_control(
			'template_id',
			[
				'label'       => esc_html__( 'Select Template', 'the7mk2' ),
				'description' => esc_html__( 'Select a container template to display.', 'the7mk2' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => $library_ids,
				'label_block' => false,
			]
		);

		$template_edit_url = admin_url( Source_Local::ADMIN_MENU_SLUG . '&elementor_library_type=' . self::DOCUMENT_TYPE );
		$this->add_control(
			'template_edit_link',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(
					// translators: %1$s: link start, %2$s: link end.
					esc_html__( 'Add/Edit container template %1$s here %2$s', 'the7mk2' ),
					'<a href="' . $template_edit_url . '" target="_blank">',
					'</a>'
				),
				'separator'       => 'none',
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			]
		);

		$this->add_control(
			'close_button_icon',
			[
				'label'   => esc_html__( 'Icon', 'the7mk2' ),
				'type'    => Controls_Manager::ICONS,
				'default' => [
					'value'   => 'fas fa-times',
					'library' => 'fa-solid',
				],
			]
		);

		$this->add_responsive_control(
			'close_button_icon_margin',
			[
				'label'      => esc_html__( 'Margin', 'the7mk2' ),
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
					'{{WRAPPER}} .close-button-container .elementor-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'close_button_vertical_position',
			[
				'label'                => esc_html__( 'Vertical Position', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => [
					'top'    => [
						'title' => esc_html__( 'Top', 'the7mk2' ),
						'icon'  => 'eicon-v-align-top',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'the7mk2' ),
						'icon'  => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => esc_html__( 'Bottom', 'the7mk2' ),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'default'              => 'top',
				'selectors_dictionary' => [
					'top'    => 'start',
					'center' => 'center',
					'bottom' => 'end',
				],
				'selectors'            => [
					'{{WRAPPER}} .close-button-container' => 'align-items: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'close_button_horizontal_position',
			[
				'label'                => esc_html__( 'Horizontal Position', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => [
					'left'  => [
						'title' => esc_html__( 'Left', 'the7mk2' ),
						'icon'  => 'eicon-h-align-left',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'the7mk2' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'default'              => 'right',
				'selectors_dictionary' => [
					'left'  => 'left: 0;',
					'right' => 'right: 0;',
				],
				'selectors'            => [
					'{{WRAPPER}} .close-button-container' => '{{VALUE}};',
				],
			]
		);

		$this->add_control(
			'close_banner_for_session',
			[
				'label'              => esc_html__( 'Stay Closed After Page Reload', 'the7mk2' ),
				'type'               => Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'Yes', 'the7mk2' ),
				'label_off'          => esc_html__( 'No', 'the7mk2' ),
				'default'            => '',
				'frontend_available' => true,
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Add style controls.
	 */
	protected function add_style_controls() {
		$this->start_controls_section(
			'section_style_close_button',
			[
				'label' => esc_html__( 'Close Button', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'close_button_icon_size',
			[
				'label'      => esc_html__( 'Size', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => '20',
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 6,
						'max'  => 100,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .close-button-container .elementor-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'close_button_icon_min_width',
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
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .close-button-container .elementor-icon' => 'min-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'close_button_icon_min_height',
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
						'max' => 100,
					],
					'vh' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .close-button-container .elementor-icon' => 'min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'close_button_icon_padding',
			[
				'label'      => esc_html__( 'Padding', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'selectors'  => [
					'{{WRAPPER}} .close-button-container .elementor-icon' => 'padding: {{SIZE}}{{UNIT}};',
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
				'name'     => 'close_button_icon_border',
				'label'    => esc_html__( 'Border', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .close-button-container .elementor-icon',
				'exclude'  => [
					'color',
				],
			]
		);

		$this->add_control(
			'close_button_icon_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .close-button-container .elementor-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'button_icon_colors' );

		$this->start_controls_tab(
			'close_button_icon_colors_normal',
			[
				'label' => esc_html__( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_control(
			'close_button_icon_primary_color',
			[
				'label'     => esc_html__( 'Icon Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .close-button-container .elementor-icon i'   => 'color: {{VALUE}};',
					'{{WRAPPER}} .close-button-container .elementor-icon svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'close_button_icon_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .close-button-container .elementor-icon' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'close_button_icon_border_border!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'           => 'close_button_icon_bg_color',
				'types'          => [ 'classic', 'gradient' ],
				'exclude'        => [ 'image' ],
				'fields_options' => [
					'background' => [
						'label' => esc_html__( 'Background', 'the7mk2' ),
					],
				],
				'selector'       => '{{WRAPPER}} .close-button-container .elementor-icon',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'close_button_icon_box_shadow',
				'label'    => esc_html__( 'Box Shadow', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .close-button-container .elementor-icon',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'close_button_icon_colors_hover',
			[
				'label' => esc_html__( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			'close_button_icon_hover_primary_color',
			[
				'label'     => esc_html__( 'Icon Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .close-button-container .elementor-icon:hover i'   => 'color: {{VALUE}};',
					'{{WRAPPER}} .close-button-container .elementor-icon:hover svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'close_button_icon_hover_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .close-button-container .elementor-icon:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'close_button_icon_border_border!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'           => 'close_button_icon_hover_bg_color',
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
				'selector'       => '{{WRAPPER}} .close-button-container .elementor-icon:hover',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'close_button_icon_hover_box_shadow',
				'label'    => esc_html__( 'Box Shadow', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .close-button-container .elementor-icon:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Slides content to display
	 */
	protected function render() {
		$settings = $this->get_settings();

		if ( ! $settings['template_id'] ) {
			echo '<div class="the7-error-template">' . esc_html__( 'Template not selected', 'the7mk2' ) . '</div>';

			return;
		}
		$template_id = $settings['template_id'];

		if ( get_post_status( $template_id ) !== 'publish' ) {
			echo '<div class="the7-error-template">' . esc_html__( 'Template not exist', 'the7mk2' ) . '</div>';

			return;
		}

		if ( ! $this->is_edit_mode() && ! $this->is_preview_mode() && ! is_preview() ) {
			$this->add_render_attribute( '_wrapper', 'class', 'hidden' );
		}

        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo The7_Elementor_Compatibility::get_builder_content_for_display( $template_id, true );

		$close_icon = $this->get_settings_for_display( 'close_button_icon' );
		if ( $close_icon ) {
			?>
			<div class="close-button-container">
				<button class="close-button elementor-icon">
					<?php Icons_Manager::render_icon( $close_icon, [ 'class' => 'close-icon' ] ); ?>
				</button>
			</div>
			<?php
		}
	}
}
