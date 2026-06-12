<?php
/**
 * The7 extension which brings Sticky Effects to elementor sections.
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Modules\Sticky_Effects;

use Elementor\Controls_Manager;
use Elementor\Element_Base;
use Elementor\Element_Section;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Plugin as Elementor;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;
use The7_Elementor_Compatibility;


defined( 'ABSPATH' ) || exit;

class Sticky_Effects {

	const JS_STICKY_SRC = PRESSCORE_THEME_URI . '/lib/jquery-sticky/jquery-sticky';
	const JS_SRC = PRESSCORE_THEME_URI . '/js/compatibility/elementor/sticky-effects';
	const CSS_SRC = PRESSCORE_THEME_URI . '/css/compatibility/elementor/the7-sticky-effects';

	public function __construct() {
		$this->add_actions();
	}

	private function add_actions() {
		if ( the7_elementor_pro_is_active() ) {
			add_action( 'elementor/element/section/section_effects/after_section_end', [ $this, 'register_controls' ] );
		} else {
			add_action( 'elementor/element/section/section_advanced/after_section_end', [
				$this,
				'register_controls',
			] );
		}

		add_action( 'elementor/element/container/section_effects/after_section_end', [ $this, 'register_controls' ] );

		//add_action( 'elementor/element/before_section_end', [ $this, 'register_advanced_controls' ], 10, 3 );

		if ( ! The7_Elementor_Compatibility::is_assets_loader_exist() ) {
			add_action( 'elementor/frontend/after_enqueue_styles', [ $this, 'enqueue_styles' ] );
		}
		add_action( 'elementor/frontend/before_register_scripts', [ $this, 'register_scripts' ] );
		add_action( 'elementor/frontend/before_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	}

	public function register_scripts() {
		the7_register_script_in_footer( 'the7-e-sticky', self::JS_STICKY_SRC, [ 'jquery' ] );
		the7_register_script_in_footer( 'the7-e-sticky-effect', self::JS_SRC, [
			'the7-elementor-frontend-common',
			'the7-e-sticky',
		] );
        the7_register_style( 'the7-e-sticky-effect', self::CSS_SRC );
	}

	public function enqueue_scripts() {
		if ( The7_Elementor_Compatibility::is_assets_loader_exist() ) {
			$this->register_assets();
		} else {
			wp_enqueue_script( 'the7-e-sticky-effect' );
		}
	}

	private function register_assets() {
		$assets = $this->get_assets();

		if ( $assets ) {
			Elementor::$instance->assets_loader->add_assets( $assets );
		}
	}

	private function get_assets() {
		return [
			'scripts' => [
				'the7-e-sticky-effect' => [
					'src'          => the7_add_asset_suffix( self::JS_SRC, '.js' ),
					'version'      => THE7_VERSION,
					'dependencies' => [
						'the7-elementor-frontend-common',
						'the7-e-sticky',
					],
				],
			],
			'styles'  => [
				'the7-e-sticky-effect' => [
					'src'          => the7_add_asset_suffix( self::CSS_SRC, '.css' ),
					'version'      => THE7_VERSION,
					'dependencies' => [],
				],
			],
		];
	}

	public function enqueue_styles() {
		the7_register_style( 'the7-e-sticky-effect', self::CSS_SRC );
		wp_enqueue_style( 'the7-e-sticky-effect' );
	}

	public function getConditions($el_name, $filed_name){
		$condition = [
			'relation' => 'or',
			'terms'    => [
				[
					'relation' => 'and',
					'terms'    => [
						[
							'name'     => 'the7_sticky_row',
							'operator' => '!==',
							'value'    => '',
						],
						[
							'name'     => $filed_name,
							'operator' => '!==',
							'value'    => '',
						],
					],
				],
			],
		];

		if ( the7_elementor_pro_is_active() ) {
			$condition['terms'][0]['terms'][] = [
				'name'     => 'sticky',
				'operator' => '==',
				'value'    => '',
			];
		}

		if ( $el_name == 'section' && Elementor::$instance->editor->is_edit_mode() ) {
			$condition['terms'][] = [
				'relation' => 'and',
				'terms'    => [
					[
						'name'     => 'isInner',
						'operator' => '==',
						'value'    => true,
					],
					[
						'name'     => $filed_name,
						'operator' => '!==',
						'value'    => '',
					],
				],
			];
		} else {
			$condition['terms'][] = [
				'terms' => [
					[
						'name'     => $filed_name,
						'operator' => '!==',
						'value'    => '',
					],
				],
			];
		}
		return $condition;
	}

	public function register_controls( Element_Base $element ) {
		$el_name = $element->get_name();
		$devices_options = The7_Elementor_Widget_Base::get_device_options();

		$cond_sticky = [];
		$cond_overlap = [];
		// Target only main sections.
		if ( $el_name == 'section' && Elementor::$instance->editor->is_edit_mode() ) {
			$cond_sticky['isInner'] = false;
			$cond_overlap['isInner'] = false;
		}


		if ( the7_elementor_pro_is_active() ) {
			$cond_sticky['sticky'] = '';
		}

		$element->start_controls_section( 'the7_section_sticky_row', [
			'label'     => __( 'Sticky Section & Overlap<i></i>', 'the7mk2' ),
			'tab'       => Controls_Manager::TAB_ADVANCED,
			'classes'   => 'the7-control',
		] );

		if ( $el_name == 'section' && Elementor::$instance->editor->is_edit_mode() && $element->get_control_index( 'isInner' ) === false ) {
			$element->add_control( 'isInner', [
				'label'        => '',
				'type'         => Controls_Manager::HIDDEN,
				'default'      => false,
				'return_value' => true,
			] );
		}

		$element->add_control( 'the7_sticky_row_overlap', [
			'label'              => esc_html__( 'Overlap', 'the7mk2' ),
			'type'               => Controls_Manager::SWITCHER,
			'label_on'           => esc_html__( 'On', 'the7mk2' ),
			'label_off'          => esc_html__( 'Off', 'the7mk2' ),
			'default'            => '',
			'frontend_available' => true,
			'condition'          => $cond_overlap,
			'prefix_class'       => 'the7-e-sticky-overlap-',
			'description'        => esc_html__( 'When enabled, the row will not take any vertical space on the page and will overlap the content that comes after it.', 'the7mk2' ),
		] );

		$element->add_control( 'the7_sticky_row', [
			'label'              => esc_html__( 'Make Section Sticky', 'the7mk2' ),
			'type'               => Controls_Manager::SWITCHER,
			'default'            => '',
			'frontend_available' => true,
			'assets'             => $this->get_asset_conditions_data(),
			'prefix_class'       => 'the7-e-sticky-row-',
			'condition'          => $cond_sticky,
			'separator'          => 'before',
		] );

		if ( the7_elementor_pro_is_active() ) {
			$element->add_control( 'the7_sticky_row_notice', [
				'raw'             => esc_html__( 'The7 Sticky Row settings not available while Sticky option is enabled in Motion Effects panel', 'the7mk2' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => [
					'sticky!' => '',
				],
			] );
		}
		$condition_sticky_parent = array_merge( $cond_sticky, [ 'the7_sticky_row!' => '' ] );

		$element->add_control( 'the7_sticky_row_devices', [
			'label'              => esc_html__( 'Sticky On', 'the7mk2' ),
			'type'               => Controls_Manager::SELECT2,
			'multiple'           => true,
			'label_block'        => true,
			'default'            => $devices_options['active_devices'],
			'options'            => $devices_options['devices_options'],
			'condition'          => $condition_sticky_parent,
			'render_type'        => 'none',
			'frontend_available' => true,
		] );

		$element->add_responsive_control( 'the7_sticky_row_offset', [
			'label'              => esc_html__( 'Offset (px)', 'the7mk2' ),
			'type'               => Controls_Manager::NUMBER,
			'default'            => 0,
			'min'                => 0,
			'max'                => 500,
			'required'           => true,
			'condition'          => $condition_sticky_parent,
			'render_type'        => 'none',
			'frontend_available' => true,
			'description'        => esc_html__( 'Offset is a minimal distance (in pixels) that the row will maintain from the top of the browser window.', 'the7mk2' ),
		] );

		$cond_on_sticky = [
			'relation' => 'or',
			'terms'    => [
				[
					'relation' => 'and',
					'terms'    => [
						[
							'name'     => 'the7_sticky_row',
							'operator' => '!==',
							'value'    => '',
						],
					],
				],
			],
		];

		if ( the7_elementor_pro_is_active() ) {
			$cond_on_sticky['terms'][0]['terms'][] = [
				'name'     => 'sticky',
				'operator' => '==',
				'value'    => '',
			];
		}
		if ( $el_name == 'section' ) {
			if ( Elementor::$instance->editor->is_edit_mode() ) {
				$cond_on_sticky['terms'][] = [
					'relation' => 'and',
					'terms'    => [
						[
							'name'     => 'isInner',
							'operator' => '==',
							'value'    => true,
						],
					],
				];
			} else {
				$cond_on_sticky['terms'][] = [
					'terms' => [
						[
							'name'     => 'the7_sticky_row',
							'operator' => '!=',
							'value'    => '100',
						],
					],
				];
			}
		}

        $element->add_control(
            'the7_sticky_parent',
            [
                'label' => esc_html__( 'Stay in parent container', 'the7mk2' ),
                'type' => Controls_Manager::SWITCHER,
                'condition' => $condition_sticky_parent,
                'render_type' => 'none',
                'frontend_available' => true,
            ]
        );

        $condition_parent = array_merge( $condition_sticky_parent, [ 'the7_sticky_parent!' => '' ] );

        $element->add_responsive_control( 'the7_sticky_parent_bottom_offset', [
            'label'              => esc_html__( 'Bottom Offset (px)', 'the7mk2' ),
            'type'               => Controls_Manager::NUMBER,
            'default'            => 0,
            'min'                => 0,
            'max'                => 500,
            'required'           => true,
            'condition'          => $condition_parent,
            'render_type'        => 'none',
            'frontend_available' => true,
            'description'        => esc_html__( 'Bottom offset is a minimum distance (in pixels) that a container maintains from the bottom of its parent container.', 'the7mk2' ),
        ] );

		$element->add_control( 'the7_sticky_effects', [
			'label'              => esc_html__( 'Change Styles When Sticky', 'the7mk2' ),
			'type'               => Controls_Manager::SWITCHER,
			'label_on'           => esc_html__( 'On', 'the7mk2' ),
			'label_off'          => esc_html__( 'Off', 'the7mk2' ),
			'default'            => '',
			'frontend_available' => true,
			'assets'             => $this->get_asset_conditions_data(),
			'prefix_class'       => 'the7-e-sticky-effect-',
			'conditions'         => $cond_on_sticky,
		] );

		$condition = $this->getConditions($el_name, 'the7_sticky_effects');

		$condition_device = $condition;

		if ( Elementor::$instance->editor->is_edit_mode() ) {
			$condition_device['terms'][1]['terms'][] = [
				'name'     => 'the7_sticky_row',
				'operator' => '!==',
				'value'    => '',
			];

			if ( the7_elementor_pro_is_active() ) {
				$condition_device['terms'][1]['terms'][] = [
					'name'     => 'sticky',
					'operator' => '==',
					'value'    => '',
				];
			}
		}

		$element->add_control( 'the7_sticky_effects_devices', [
			'label'              => esc_html__( 'Change Styles On', 'the7mk2' ),
			'type'               => Controls_Manager::SELECT2,
			'multiple'           => true,
			'label_block'        => true,
			'default'            => $devices_options['active_devices'],
			'options'            => $devices_options['devices_options'],
			'conditions'         => $condition_device,
			'render_type'        => 'none',
			'frontend_available' => true,
		] );

		$element->add_responsive_control( 'the7_sticky_effects_offset', [
			'label'              => esc_html__( 'Scroll Offset (px)', 'the7mk2' ),
			'type'               => Controls_Manager::NUMBER,
			'default'            => 0,
			'min'                => 0,
			'max'                => 1000,
			'required'           => true,
			'conditions'         => $condition_device,
			'render_type'        => 'none',
			'frontend_available' => true,
			'description'        => esc_html__( 'Scroll offset is a distance (in pixels) a page has to be scrolled before the style changes will be applied.', 'the7mk2' ),
		] );

		$selector = '{{WRAPPER}}.the7-e-sticky-effects, .the7-e-sticky-effects .elementor-element.elementor-element-{{ID}}:not(.fix)';
		$element->add_responsive_control( 'the7_sticky_effects_height', [
			'label'       => esc_html__( 'Row Height (px)', 'the7mk2' ),
			'type'        => Controls_Manager::SLIDER,
			'range'       => [
				'px' => [
					'min' => 0,
					'max' => 500,
				],
			],
			'size_units'  => [ 'px' ],
			'selectors'   => [
				'{{WRAPPER}}:not(.the7-e-sticky-spacer).the7-e-sticky-effects > .elementor-container, .the7-e-sticky-effects:not(.the7-e-sticky-spacer) .elementor-element.elementor-element-{{ID}}:not(.fix) > .elementor-container' => 'min-height: {{SIZE}}{{UNIT}};',
				'.elementor-element-{{ID}} > .elementor-container'                                                                                                                                         => 'min-height: 0;',
				'{{WRAPPER}}.e-container.the7-e-sticky-effects:not(.the7-e-sticky-spacer)'                                                                                                                => '--min-height: {{SIZE}}{{UNIT}};',
				'{{WRAPPER}}.e-con.the7-e-sticky-effects:not(.the7-e-sticky-spacer)'                                                                                                                 => '--min-height: {{SIZE}}{{UNIT}};',
			],
			'description' => esc_html__( 'Note that the row height will not get smaller than the elements inside of it.', 'the7mk2' ),
			'conditions'  => $condition,
		] );

		$element->add_control( 'the7_sticky_effects_background', [
			'label'      => esc_html__( 'Background Color', 'the7mk2' ),
			'type'       => Controls_Manager::COLOR,
			'conditions' => $condition,
			'selectors'  => [
				$selector . ', {{WRAPPER}}.the7-e-sticky-effects > .elementor-motion-effects-container > .elementor-motion-effects-layer,
				.the7-e-sticky-effects .elementor-element.elementor-element-{{ID}}:not(.fix) > .elementor-motion-effects-container > .elementor-motion-effects-layer' => 'background-color: {{VALUE}}; background-image: none;',
			],
		] );

		$element->add_control( 'the7_sticky_effects_border_color', [
			'label'      => esc_html__( 'Border Color', 'the7mk2' ),
			'type'       => Controls_Manager::COLOR,
			'conditions' => $condition,
			'selectors'  => [
				$selector => 'border-color: {{VALUE}}',
			],
		] );

		$element->add_control( 'the7_sticky_effects_border_width', [
			'label'      => esc_html__( 'Border Width', 'the7mk2' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px' ],
			'range'      => [
				'px' => [
					'min' => 0,
					'max' => 50,
				],
			],
			'conditions' => $condition,
			'selectors'  => [
				$selector => 'border-style: solid; border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
			],
		] );

		$element->add_group_control( Group_Control_Box_Shadow::get_type(), [
			'name'       => 'the7_sticky_effects_shadow',
			'selector'   => $selector,
			'conditions' => $condition,
		] );

		$conditions = [];
		// Target only inner sections.
		// Checking for `$element->get_data( 'isInner' )` in both editor & frontend causes it to work properly on the frontend but
		// break on the editor, because the inner section is created in JS and not rendered in PHP.
		// So this is a hack to force the editor to show the `sticky_parent` control, and still make it work properly on the frontend.
		 if ($el_name != 'container' ) {

			 if ( $el_name == 'section' && Elementor::$instance->editor->is_edit_mode() ) {
				 $conditions['isInner'] = true;
			 }
			 /*if($el_name == 'container'){
				 $conditions['_is_row'] = 'row';
			 }*/

			 $element->add_responsive_control( 'the7_hide_on_sticky_effect', [
				 'label'              => esc_html__( 'Visibility', 'the7mk2' ),
				 'type'               => Controls_Manager::SELECT,
				 'default'            => '',
				 'options'            => [
					 ''     => esc_html__( 'Do Nothing', 'the7mk2' ),
					 'hide' => esc_html__( 'Hide When Sticky', 'the7mk2' ),
					 'show' => esc_html__( 'Show When Sticky', 'the7mk2' ),
				 ],
				 'render_type'        => 'none',
				 'frontend_available' => true,
				 'description'        => sprintf( esc_html__( 'When "Sticky" and "Change Styles when Sticky" are ON for the parent section.', 'the7mk2' ) ),
				 'condition'          => $conditions,
				 'separator'          => 'before',
			 ] );

		 }

		$element->add_control( 'the7_sticky_scroll_up', [
			'label'              => esc_html__( 'Show on Scroll Up', 'the7mk2' ),
			'type'               => Controls_Manager::SWITCHER,
			'label_on'           => esc_html__( 'On', 'the7mk2' ),
			'label_off'          => esc_html__( 'Off', 'the7mk2' ),
			'default'            => '',
			'frontend_available' => true,
			'condition'         => $condition_sticky_parent,
			'prefix_class'       => 'the7-e-sticky-scrollup-',
			'separator'          => 'before',
		] );


		$conditions = $this->getConditions($el_name, 'the7_sticky_scroll_up');

        $element->add_control( 'the7_sticky_scroll_up_devices', [
            'label'              => esc_html__( 'Active On', 'the7mk2' ),
            'type'               => Controls_Manager::SELECT2,
            'multiple'           => true,
            'label_block'        => true,
            'default'            => $devices_options['active_devices'],
            'options'            => $devices_options['devices_options'],
            'conditions'         => $conditions,
            'render_type'        => 'none',
            'frontend_available' => true,
        ] );

		$element->add_responsive_control( 'the7_sticky_scroll_up_translate', [
			'label'      => esc_html__( 'Hide by (px)', 'the7mk2' ),
			'type'        => Controls_Manager::SLIDER,
			'range'       => [
				'px' => [
					'min' => 0,
					'max' => 500,
				],
			],
			'size_units'  => [ 'px' ],
			'conditions'       => $conditions,
            'frontend_available' => true,
			'selectors'  => [
				'{{WRAPPER}}' => '--the7-sticky-scroll-up-translate: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}}.the7-e-sticky-scrollup-yes:not(.the7-e-sticky-spacer).the7-e-scroll-down' => ' opacity: 1; pointer-events: initial;'
			],
			'description'        => esc_html__( ' Custom value (in pixels) that determines how much the container will be hidden on scroll down. Leave empty for automatic calculation of the full container height.', 'the7mk2' ),
		] );


		$element->end_controls_section();
	}


	private function get_asset_conditions_data() {
		return [
			'scripts' => [
				[
					'name'       => 'the7-e-sticky-effect',
					'conditions' => [
						'relation' => 'or',
						'terms'    => [
							[
								'name'     => 'the7_sticky_effects',
								'operator' => '!==',
								'value'    => '',
							],
							[
								'name'     => 'the7_sticky_row',
								'operator' => '!==',
								'value'    => '',
							],
						],
					],
				],
			],
			'styles'  => [
				[
					'name'       => 'the7-e-sticky-effect',
					'conditions' => [
						'relation' => 'or',
						'terms'    => [
							[
								'name'     => 'the7_sticky_effects',
								'operator' => '!==',
								'value'    => '',
							],
							[
								'name'     => 'the7_sticky_row',
								'operator' => '!==',
								'value'    => '',
							],
							[
								'name'     => 'the7_sticky_row_overlap',
								'operator' => '!==',
								'value'    => '',
							],
						],
					],
				],
			],
		];
	}
}
