<?php
/**
 * The7 "Image Box Grid" widget for Elementor.
 *
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Utils;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Button;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Less_Vars_Decorator_Interface;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Image_Aspect_Ratio;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Image_Size;

defined( 'ABSPATH' ) || exit;

/**
 * Image_Box_Grid class.
 */
class Image_Box_Grid extends The7_Elementor_Widget_Base {

	/**
	 * Get element name.
	 *
	 * Retrieve the element name.
	 *
	 * @return string The name.
	 */
	public function get_name() {
		return 'the7_image_box_grid_widget';
	}

	/**
	 * @return string
	 */
	protected function the7_title() {
		return esc_html__( 'Image Box Grid', 'the7mk2' );
	}

	/**
	 * @return string
	 */
	protected function the7_icon() {
		return 'eicon-icon-box';
	}

	/**
	 * @return string[]
	 */
	protected function the7_keywords() {
		return [ 'image', 'box', 'grid' ];
	}

	/**
	 * @return string
	 */
	protected function get_less_file_name() {
		return PRESSCORE_THEME_DIR . '/css/dynamic-less/elementor/the7-image-box-widget.less';
	}

	/**
	 * @return string[]
	 */
	public function get_style_depends() {
		return [ 'the7-image-box-widget' ];
	}

	/**
	 * @return string[]
	 */
	public function get_script_depends() {
		return [ 'the7-image-box-widget' ];
	}

	/**
	 * @return void
	 */
	protected function register_controls() {
		// Content.
		$this->add_content_controls();
		$this->add_layout_content_controls();

		// Style.
		$this->add_widget_title_style_controls();
		$this->add_box_content_style_controls();
		$this->add_divider_style_controls();
		$this->add_icon_style_controls();
		$this->add_title_style_controls();
		$this->add_description_style_controls();
		$this->template( Button::class )->add_style_controls();
	}

	/**
	 * @return void
	 */
	protected function add_content_controls() {
		$this->start_controls_section(
			'section_icon',
			[
				'label' => esc_html__( 'Items', 'the7mk2' ),
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
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

		$repeater->add_control(
			'title_text',
			[
				'label'       => esc_html__( 'Title & Description', 'the7mk2' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [
					'active' => true,
				],
				'default'     => esc_html__( 'This is the heading', 'the7mk2' ),
				'placeholder' => esc_html__( 'Enter your title', 'the7mk2' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'description_text',
			[
				'label'       => '',
				'type'        => Controls_Manager::TEXTAREA,
				'dynamic'     => [
					'active' => true,
				],
				'default'     => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'the7mk2' ),
				'placeholder' => esc_html__( 'Enter your description', 'the7mk2' ),
				'rows'        => 10,
				'separator'   => 'none',
				'show_label'  => false,
			]
		);

		$repeater->add_control(
			'button_text',
			[
				'label'   => esc_html__( 'Button Text', 'the7mk2' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Click Here', 'the7mk2' ),
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
				'placeholder' => esc_html__( 'https://your-link.com', 'the7mk2' ),
			]
		);

		$tab_default_content = esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'the7mk2' );

		$this->add_control(
			'icon_boxes_items',
			[
				'label'       => esc_html__( 'Items', 'the7mk2' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[
						'title_text'       => esc_html__( 'Item Title #1', 'the7mk2' ),
						'description_text' => $tab_default_content,
						'button_text'      => esc_html__( 'Click Here', 'the7mk2' ),
						'link'             => '#',
					],
					[
						'title_text'       => esc_html__( 'Item Title #2', 'the7mk2' ),
						'description_text' => $tab_default_content,
						'button_text'      => esc_html__( 'Click Here', 'the7mk2' ),
						'link'             => '#',
					],
				],
				'title_field' => '{{{ title_text }}}',
			]
		);

		$this->add_control(
			'content_heading',
			[
				'label'     => esc_html__( 'Content', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'title_html_tag',
			[
				'label'   => esc_html__( 'Title HTML Tag', 'the7mk2' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'h1'  => 'H1',
					'h2'  => 'H2',
					'h3'  => 'H3',
					'h4'  => 'H4',
					'h5'  => 'H5',
					'h6'  => 'H6',
					'div' => 'div',
				],
				'default' => 'h4',
			]
		);

		$this->add_basic_responsive_control(
			'text_align',
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
				'prefix_class'         => 'content-align%s-',
				'default'              => 'left',
				'selectors_dictionary' => [
					'left'    => 'align-items: flex-start; text-align: left;',
					'center'  => 'align-items: center; text-align: center;',
					'right'   => 'align-items: flex-end; text-align: right;',
					'justify' => 'align-items: stretch; text-align: justify;',
				],
				'selectors'            => [
					'{{WRAPPER}} .box-content' => ' {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function add_layout_content_controls() {
		$this->start_controls_section(
			'layout_content_section',
			[
				'label' => esc_html__( 'Layout', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'show_widget_title',
			[
				'label'        => esc_html__( 'Widget Title', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the7mk2' ),
				'label_off'    => esc_html__( 'Hide', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => '',
			]
		);

		$this->add_control(
			'widget_title_text',
			[
				'label'     => esc_html__( 'Title', 'the7mk2' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => 'Widget title',
				'condition' => [
					'show_widget_title' => 'y',
				],
			]
		);

		$this->add_control(
			'widget_title_tag',
			[
				'label'     => esc_html__( 'Title HTML Tag', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
				],
				'default'   => 'h3',
				'condition' => [
					'show_widget_title' => 'y',
				],
			]
		);

		$this->add_control(
			'widget_columns_wide_desktop',
			[
				'label'       => esc_html__( 'Columns On A Wide Desktop', 'the7mk2' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => '',
				'min'         => 1,
				'max'         => 12,
				'separator'   => 'before',
				'selectors'   => [
					'{{WRAPPER}} .dt-css-grid' => '--wide-desktop-columns: {{SIZE}}',
				],
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'widget_columns_wide_desktop_breakpoint',
			[
				'label'       => esc_html__( 'Wide Desktop Breakpoint (px)', 'the7mk2' ),
				'description' => the7_elementor_get_wide_columns_control_description(),
				'type'        => Controls_Manager::NUMBER,
				'default'     => '',
				'min'         => 0,
			]
		);

		$this->add_basic_responsive_control(
			'widget_columns',
			[
				'label'          => esc_html__( 'Columns', 'the7mk2' ),
				'type'           => Controls_Manager::NUMBER,
				'default'        => 1,
				'tablet_default' => 1,
				'mobile_default' => 1,
				'min'            => 1,
				'max'            => 12,
				'selectors'      => [
					'{{WRAPPER}} .dt-css-grid' => 'grid-template-columns: repeat({{SIZE}},1fr)',
					'{{WRAPPER}}'              => '--wide-desktop-columns: {{SIZE}}',
				],
				'render_type'    => 'template',
			]
		);

		$this->template( Image_Size::class )->add_style_controls();

		$this->add_basic_responsive_control(
			'gap_between_posts',
			[
				'label'      => esc_html__( 'Columns Gap', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [
					'size' => '40',
				],
				'range'      => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .dt-css-grid' => 'grid-column-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_basic_responsive_control(
			'rows_gap',
			[
				'label'      => esc_html__( 'Rows Gap', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [
					'size' => '20',
				],
				'range'      => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .dt-css-grid' => 'grid-row-gap: {{SIZE}}{{UNIT}}; --grid-row-gap: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'divider',
			[
				'label'     => esc_html__( 'Dividers', 'the7mk2' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'Off', 'the7mk2' ),
				'label_on'  => esc_html__( 'On', 'the7mk2' ),
				'separator' => 'before',
			]
		);

		$this->add_control(
			'link_click',
			[
				'label'     => esc_html__( 'Apply Link & Hover', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'button',
				'separator' => 'before',
				'options'   => [
					'box'    => esc_html__( 'Whole box', 'the7mk2' ),
					'button' => esc_html__( "Separate element's", 'the7mk2' ),
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function add_box_content_style_controls() {
		$this->start_controls_section(
			'section_design_box',
			[
				'label' => esc_html__( 'Box', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_basic_responsive_control(
			'box_height',
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
					'{{WRAPPER}} .wf-cell .the7-image-box-wrapper' => 'min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_basic_responsive_control(
			'box_fixed_height',
			[
				'label'      => esc_html__( 'Height', 'the7mk2' ),
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
					'{{WRAPPER}} .wf-cell .the7-image-box-wrapper' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_basic_responsive_control(
			'content_position',
			[
				'label'                => esc_html__( 'Content Position', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
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
				'toggle'               => false,
				'device_args'          => [
					'tablet' => [
						'toggle' => true,
					],
					'mobile' => [
						'toggle' => true,
					],
				],
				'default'              => 'top',
				'prefix_class'         => 'icon-box-vertical-align%s-',
				'selectors_dictionary' => [
					'top'    => 'align-items: flex-start; align-content: flex-start; justify-content: flex-start;',
					'center' => 'align-items: center; align-content: center; justify-content: center;',
					'bottom' => 'align-items: flex-end; align-content: flex-end; justify-content: flex-end;',
				],
				'selectors'            => [
					'{{WRAPPER}} .wf-cell .the7-image-box-wrapper' => '{{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'     => 'box_border',
				'label'    => esc_html__( 'Border', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .wf-cell .the7-image-box-wrapper',
				'exclude'  => [
					'color',
				],
			]
		);

		$this->add_basic_responsive_control(
			'box_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .wf-cell .the7-image-box-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_basic_responsive_control(
			'box_padding',
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
					'{{WRAPPER}} .wf-cell .the7-image-box-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_icon_box_style' );

		$this->start_controls_tab(
			'tab_color_normal',
			[
				'label' => esc_html__( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_control(
			'box_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wf-cell .the7-image-box-wrapper' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'box_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wf-cell .the7-image-box-wrapper' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'box_shadow',
				'label'    => esc_html__( 'Box Shadow', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .wf-cell .the7-image-box-wrapper',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_color_hover',
			[
				'label' => esc_html__( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			'bg_hover_color',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wf-cell .the7-image-box-wrapper:hover' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'box_hover_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wf-cell .the7-image-box-wrapper:hover' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'box_hover_shadow',
				'label'    => esc_html__( 'Box Shadow', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .wf-cell .the7-image-box-wrapper:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function add_widget_title_style_controls() {
		$this->start_controls_section(
			'widget_style_section',
			[
				'label'     => esc_html__( 'Widget Title', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_widget_title' => 'y',
				],
			]
		);

		$this->add_basic_responsive_control(
			'widget_title_align',
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
					'{{WRAPPER}} .rp-heading' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'widget_title_typography',
				'selector' => '{{WRAPPER}} .rp-heading',
			]
		);

		$this->add_control(
			'widget_title_color',
			[
				'label'     => esc_html__( 'Font Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .rp-heading' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'widget_title_bottom_margin',
			[
				'label'      => esc_html__( 'Spacing Below Title', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 20,
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
					'{{WRAPPER}} .rp-heading' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function add_divider_style_controls() {
		$this->start_controls_section(
			'widget_divider_section',
			[
				'label'     => esc_html__( 'Dividers', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'divider' => 'yes',
				],
			]
		);

		$this->add_control(
			'divider_style',
			[
				'label'     => esc_html__( 'Style', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'solid'  => esc_html__( 'Solid', 'the7mk2' ),
					'double' => esc_html__( 'Double', 'the7mk2' ),
					'dotted' => esc_html__( 'Dotted', 'the7mk2' ),
					'dashed' => esc_html__( 'Dashed', 'the7mk2' ),
				],
				'default'   => 'solid',
				'condition' => [
					'divider' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .widget-divider-on .wf-cell:before' => 'border-bottom-style: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'divider_weight',
			[
				'label'     => esc_html__( 'Width', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 1,
				],
				'range'     => [
					'px' => [
						'min' => 1,
						'max' => 20,
					],
				],
				'condition' => [
					'divider' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .widget-divider-on' => '--divider-width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'divider_color',
			[
				'label'     => esc_html__( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'divider' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .widget-divider-on .wf-cell:before' => 'border-bottom-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function add_icon_style_controls() {
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
				'label'      => esc_html__( 'Max Width', 'the7mk2' ),
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
					'{{WRAPPER}}' => '--image-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->template( Image_Aspect_Ratio::class )->add_style_controls();

		$this->add_responsive_control(
			'position',
			[
				'label'                => esc_html__( 'Position', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => [
					'left'  => [
						'title' => esc_html__( 'Left', 'the7mk2' ),
						'icon'  => 'eicon-h-align-left',
					],
					'top'   => [
						'title' => esc_html__( 'Top', 'the7mk2' ),
						'icon'  => 'eicon-v-align-top',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'the7mk2' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'default'              => 'left',
				'toggle'               => false,
				'device_args'          => [
					'tablet' => [
						'toggle' => true,
					],
					'mobile' => [
						'toggle' => true,
					],
				],
				'selectors_dictionary' => [
					'top'   => $this->combine_to_css_vars_definition_string(
						[
							'flex-flow'     => 'column wrap',
							'img-space'     => '0 0 var(--icon-spacing, 15px) 0',
							'img-order'     => '0',
							'img-width'     => '100%',
							'content-width' => ' width: 100%',
						]
					),
					'left'  => $this->combine_to_css_vars_definition_string(
						[
							'flex-flow'     => 'row nowrap',
							'img-space'     => '0 var(--icon-spacing, 15px) 0 0',
							'img-order'     => '0',
							'img-width'     => '30%',
							'content-width' => ' width: calc(100% - var(--image-size) - var(--icon-spacing, 15px))',
						]
					),
					'right' => $this->combine_to_css_vars_definition_string(
						[
							'flex-flow'     => 'row nowrap',
							'img-space'     => '0 0 0 var(--icon-spacing, 15px)',
							'img-order'     => '2',
							'img-width'     => '30%',
							'content-width' => ' width: calc(100% - var(--image-size) - var(--icon-spacing, 15px))',
						]
					),
				],
				'selectors'            => [
					'{{WRAPPER}}' => '{{VALUE}}',
				],
			]
		);

		$icon_position_options            = [
			'start'  => esc_html__( 'Start', 'the7mk2' ),
			'center' => esc_html__( 'Center', 'the7mk2' ),
			'end'    => esc_html__( 'End', 'the7mk2' ),
		];
		$icon_position_options_on_devices = [ '' => esc_html__( 'Default', 'the7mk2' ) ] + $icon_position_options;

		$this->add_responsive_control(
			'icon_position',
			[
				'label'                => esc_html__( 'Align', 'the7mk2' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => 'start',
				'options'              => $icon_position_options,
				'device_args'          => [
					'tablet' => [
						'default' => '',
						'options' => $icon_position_options_on_devices,
					],
					'mobile' => [
						'default' => '',
						'options' => $icon_position_options_on_devices,
					],
				],
				'prefix_class'         => 'icon-vertical-align%s-',
				'selectors_dictionary' => [
					'start'  => 'align-self: flex-start;',
					'center' => 'align-self: center;',
					'end'    => 'align-self: flex-end;',
				],
				'selectors'            => [
					'{{WRAPPER}} .elementor-image-div' => '{{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'icon_padding',
			[
				'label'      => esc_html__( 'Padding', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'selectors'  => [
					'{{WRAPPER}} .elementor-image-div img' => 'padding: {{SIZE}}{{UNIT}};',
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

		$this->add_control(
			'icon_title',
			[
				'label'     => esc_html__( 'Hover icon', 'the7mk2' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'hover_icon',
			[
				'label' => esc_html__( 'Icon', 'the7mk2' ),
				'type'  => Controls_Manager::ICONS,
				'skin'  => 'inline',
			]
		);

		$this->add_responsive_control(
			'hover_icon_size',
			[
				'label'      => esc_html__( 'Icon Size', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => '24',
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
					'{{WRAPPER}} .the7-hover-icon'     => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .the7-hover-icon svg' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'hover_icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'hover_icon_color',
			[
				'label'     => esc_html__( 'Icon Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '#FFFFFF',
				'selectors' => [
					'{{WRAPPER}} .the7-hover-icon'     => 'color: {{VALUE}};',
					'{{WRAPPER}} .the7-hover-icon svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
				'condition' => [
					'hover_icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'style_title',
			[
				'label'     => esc_html__( 'Style', 'the7mk2' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'border_width',
			[
				'label'      => esc_html__( 'Border Width', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .elementor-image-div' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; border-style:solid;',
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
					'{{WRAPPER}} .elementor-image-div' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .elementor-image-div' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-image-div' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'img_shadow',
				'selector' => '{{WRAPPER}} .elementor-image-div',
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'     => 'thumbnail_filters',
				'selector' => '
				{{WRAPPER}} .post-thumbnail-rollover img
				',
			]
		);

		$this->add_control(
			'thumbnail_opacity',
			[
				'label'      => esc_html__( 'Image opacity (%)', 'the7mk2' ),
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
					'{{WRAPPER}} .post-thumbnail-rollover img' => 'opacity: calc({{SIZE}}/100)',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'icon_colors_hover',
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
							{{WRAPPER}} .post-thumbnail-rollover:before, {{WRAPPER}} .post-thumbnail-rollover:after { transition: opacity 0.3s ease; } {{SELECTOR}}' => 'background: {{VALUE}};',
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
					'{{WRAPPER}} .elementor-image-div:hover, {{WRAPPER}} a.the7-box-wrapper:hover .elementor-image-div' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_hover_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-image-div:hover, {{WRAPPER}} a.the7-box-wrapper:hover .elementor-image-div' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'img_hover_shadow',
				'selector' => '
					{{WRAPPER}} a:hover .elementor-image-div,
					{{WRAPPER}} .elementor-image-div:hover',
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'     => 'thumbnail_hover_filters',
				'selector' => '{{WRAPPER}} a:hover .elementor-image-div img,
					{{WRAPPER}} .post-thumbnail-rollover:hover img
				',
			]
		);

		$this->add_control(
			'thumbnail_hover_opacity',
			[
				'label'      => esc_html__( 'Image opacity (%)', 'the7mk2' ),
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
					'
					{{WRAPPER}} .elementor-image-div img { transition: opacity 0.3s ease; }
					{{WRAPPER}} a:hover .the7-simple-post-thumb img,
					{{WRAPPER}} .post-thumbnail-rollover:hover img ' => 'opacity: calc({{SIZE}}/100)',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'icon_spacing',
			[
				'label'      => esc_html__( 'Spacing', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 15,
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
					'{{WRAPPER}}' => '--icon-spacing: {{SIZE}}{{UNIT}}',
				],
				'separator'  => 'before',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function add_title_style_controls() {
		// Title Style.
		$this->start_controls_section(
			'title_style',
			[
				'label' => esc_html__( 'Title', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .box-content-wrapper .box-heading, {{WRAPPER}} .box-content-wrapper .box-heading a',
			]
		);

		$this->start_controls_tabs( 'tabs_title_style' );

		$this->start_controls_tab(
			'tab_title_color_normal',
			[
				'label' => esc_html__( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_control(
			'tab_title_text_color',
			[
				'label'     => esc_html__( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .box-content-wrapper .box-heading, {{WRAPPER}} .box-content-wrapper .box-heading a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_title_color_hover',
			[
				'label' => esc_html__( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			'tab_title_hover_color',
			[
				'label'     => esc_html__( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .the7-image-box-wrapper .box-heading:hover, {{WRAPPER}} .the7-image-box-wrapper .box-heading:hover a' => 'color: {{VALUE}};',
					'{{WRAPPER}} a.the7-image-box-wrapper:hover .box-heading, {{WRAPPER}} a.the7-image-box-wrapper:hover .box-heading a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function add_description_style_controls() {
		$this->start_controls_section(
			'section_style_desc',
			[
				'label' => esc_html__( 'Description', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'description_typography',
				'selector' => '{{WRAPPER}} .box-description',
			]
		);

		$this->start_controls_tabs( 'tabs_description_style' );

		$this->start_controls_tab(
			'tab_desc_color_normal',
			[
				'label' => esc_html__( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_control(
			'short_desc_color',
			[
				'label'     => esc_html__( 'Font Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .box-description' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_desc_color_hover',
			[
				'label' => esc_html__( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			'short_desc_color_hover',
			[
				'label'     => esc_html__( 'Font Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .box-description:hover, {{WRAPPER}} a.the7-image-box-wrapper:hover .box-description' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'gap_above_description',
			[
				'label'      => esc_html__( 'Description Spacing Above', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 10,
				],
				'selectors'  => [
					'{{WRAPPER}} .box-description' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @param string $text Text.
	 * @param string $tag HTML tag.
	 *
	 * @return string
	 */
	protected function display_widget_title( $text, $tag = 'h3' ) {
		$tag = Utils::validate_html_tag( $tag );

		$output  = '<' . $tag . ' class="rp-heading">';
		$output .= esc_html( $text );
		$output .= '</' . $tag . '>';

		return $output;
	}

	/**
	 * @param sring $element Element name.
	 *
	 * @return void
	 */
	protected function add_main_wrapper_class_render_attribute_for( $element ) {
		$class = [
			'the7-box-grid-wrapper',
			'the7-elementor-widget',
			'loading-effect-none',
		];

		// Unique class.
		$class[] = $this->get_unique_class();

		$settings = $this->get_settings_for_display();

		if ( $settings['divider'] ) {
			$class[] = 'widget-divider-on';
		}

		$this->add_render_attribute( $element, 'class', $class );
	}

	/**
	 * @return void
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->print_inline_css();
		$this->add_main_wrapper_class_render_attribute_for( 'wrapper' );

        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<div ' . $this->get_render_attribute_string( 'wrapper' ) . '>';

		if ( $settings['show_widget_title'] === 'y' && $settings['widget_title_text'] ) {
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $this->display_widget_title( $settings['widget_title_text'], $settings['widget_title_tag'] );
		}

		$icons_html = $this->get_hover_icons_html_template();

		if ( $settings['icon_boxes_items'] ) : ?>
			<div class="dt-css-grid">
				<?php
				foreach ( $settings['icon_boxes_items'] as $index => $item ) :
					$repeater_setting_key = $this->get_repeater_setting_key( 'text', 'icon_list', $index );

					$this->add_render_attribute( $repeater_setting_key, 'class', 'wf-cell shown' );

					$tab_content_setting_key = $this->get_repeater_setting_key( 'description_text', 'tabs', $index );
					$this->add_render_attribute( $tab_content_setting_key, 'class', 'box-description' );
					$this->add_inline_editing_attributes( $tab_content_setting_key );

					$link_key = 'link_' . $index;

					$this->add_link_attributes( $link_key, $item['link'] );

					$btn_attributes    = $this->get_render_attribute_string( $link_key );
					$img_wrapper_class = implode( ' ', array_filter( [
						'post-thumbnail-rollover',
						$this->template( Image_Size::class )->get_wrapper_class(),
						$this->template( Image_Aspect_Ratio::class )->get_wrapper_class(),
					] ) );

					if ( 'button' === $settings['link_click'] ) {
						$title_link           = '<a ' . $btn_attributes . '>';
						$title_link_close     = '</a>';
						$parent_wrapper       = '<div class="the7-image-box-wrapper the7-box-wrapper the7-elementor-widget">';
						$parent_wrapper_close = '</div>';
						$icon_wrapper         = '<a class="' . $img_wrapper_class . '" ' . $btn_attributes . '>';
						$icon_wrapper_close   = '</a>';
						$btn_element          = 'a';
						$btn_link_attributes  = $this->get_render_attributes( $link_key );
					} else {
						$title_link           = '';
						$title_link_close     = '';
						$parent_wrapper       = '<a class="the7-image-box-wrapper the7-box-wrapper the7-elementor-widget box-hover" ' . $btn_attributes . '>';
						$parent_wrapper_close = '</a>';
						$icon_wrapper         = '<div class="' . $img_wrapper_class . '">';
						$icon_wrapper_close   = '</div>';
						$btn_element          = 'div';
						$btn_link_attributes  = [];
					}
					?>
					<div <?php $this->print_render_attribute_string( $repeater_setting_key ); ?>>
						<?php echo $parent_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<div class="box-content-wrapper">

								<?php if ( ! empty( $item['image']['id'] ) ) {?>
									<div class="elementor-image-div ">
										<?php
										echo $icon_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
										echo $this->template( Image_Size::class )->get_image( $item['image']['id'] );
										echo $icons_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
										echo $icon_wrapper_close; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
										?>
									</div>
								<?php }	?>
								<div class="box-content">
									<?php
									if ( $item['title_text'] ) {
										$title_html_tag = Utils::validate_html_tag( $settings['title_html_tag'] );

										echo '<' . $title_html_tag . ' class="box-heading">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
										echo $title_link; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
										echo wp_kses_post( $item['title_text'] );
										echo $title_link_close; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
										echo '</' . $title_html_tag . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									}

									if ( ! Utils::is_empty( $item['description_text'] ) ) {
										echo '<div ' . $this->get_render_attribute_string( $tab_content_setting_key ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
										echo wp_kses_post( $item['description_text'] );
										echo '</div>';
									}

									if ( $item['button_text'] || $this->template( Button::class )->is_icon_visible() ) {
										// Cleanup button render attributes.
										$this->remove_render_attribute( 'box-button' );

										$this->add_render_attribute( 'box-button', $btn_link_attributes ?: [] );

										$this->template( Button::class )->render_button(
											'box-button',
											esc_html( $item['button_text'] ),
											$btn_element
										);
									}
									?>
								</div>
							</div>
						<?php echo $parent_wrapper_close; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
					<?php
				endforeach;
				?>
			</div>
			<?php
		endif;

		echo '</div>';
	}

	/**
	 * @return string
	 */
	protected function get_hover_icons_html_template() {
        $elementor_icon_html = $this->get_elementor_icon_html( $this->get_settings_for_display( 'hover_icon' ), 'i' );

		if ( ! $elementor_icon_html ) {
			return '';
		}

		$a_atts = [
			'class' => 'the7-hover-icon',
		];

		return sprintf(
			'<span %s>%s</span>',
			the7_get_html_attributes_string( $a_atts ),
			$elementor_icon_html
		);
	}

	/**
	 * @param The7_Elementor_Less_Vars_Decorator_Interface $less_vars Less vars manager.
	 *
	 * @return void
	 */
	protected function less_vars( The7_Elementor_Less_Vars_Decorator_Interface $less_vars ) {
		$settings = $this->get_settings_for_display();

		$less_vars->add_keyword(
			'unique-shortcode-class-name',
			$this->get_unique_class() . '.the7-box-grid-wrapper',
			'~"%s"'
		);

		foreach ( $this->get_supported_devices() as $device => $dep ) {
			$less_vars->start_device_section( $device );
			$less_vars->add_keyword(
				'grid-columns',
				$this->get_responsive_setting( 'widget_columns' ) ?: 3
			);
			$less_vars->close_device_section();
		}

		$less_vars->add_keyword( 'grid-wide-columns', $settings['widget_columns_wide_desktop'] ?: $settings['widget_columns'] );

		if ( ! empty( $settings['widget_columns_wide_desktop_breakpoint'] ) ) {
			$less_vars->add_pixel_number( 'wide-desktop-width', $settings['widget_columns_wide_desktop_breakpoint'] );
		}
	}
}
