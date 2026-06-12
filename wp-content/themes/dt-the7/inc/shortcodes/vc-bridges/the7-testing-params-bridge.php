<?php

defined( 'ABSPATH' ) || exit;

return array(
	'weight'     => -1,
	'name'       => __( 'Params Debug Helper', 'the7mk2' ),
	'base'       => 'dt_testing_params',
	'description'=> __( 'Debug-only shortcode to inspect The7 Visual Composer param types.', 'the7mk2' ),
	'icon'       => 'dt_vc_ico_gap',
	'class'      => 'dt_vc_sc_testing_params',
	'category'   => __( 'by Dream-Theme', 'the7mk2' ),
	'params'     => array(
		array(
			'heading'    => __( 'Title Divider', 'the7mk2' ),
			'param_name' => 'dt_title',
			'type'       => 'dt_title',
		),
		array(
			'heading'    => __( 'Subtitle Divider', 'the7mk2' ),
			'param_name' => 'dt_subtitle',
			'type'       => 'dt_subtitle',
		),
		array(
			'heading'    => __( 'Taxonomy Selector', 'the7mk2' ),
			'param_name' => 'debug_taxonomy',
			'type'       => 'dt_taxonomy',
			'taxonomy'   => 'category',
			'description'=> __( 'Select categories to populate the value.', 'the7mk2' ),
		),
		array(
			'heading'    => __( 'Post Selector', 'the7mk2' ),
			'param_name' => 'debug_post_selection',
			'type'       => 'dt_posttype',
			'posttype'   => 'post',
			'description'=> __( 'Choose individual posts to see comma separated slugs.', 'the7mk2' ),
		),
		array(
			'heading'    => __( 'Spacing Control', 'the7mk2' ),
			'param_name' => 'debug_spacing',
			'type'       => 'dt_spacing',
			'units'      => array( 'px', '%' ),
			'value'      => '10px 15px 10px 15px',
		),
		array(
			'heading'    => __( 'Responsive Columns', 'the7mk2' ),
			'param_name' => 'debug_columns',
			'type'       => 'dt_responsive_columns',
			'value'      => 'desktop:4|h_tablet:3|v_tablet:2|phone:1',
		),
		array(
			'heading'    => __( 'Dimensions Control', 'the7mk2' ),
			'param_name' => 'debug_dimensions',
			'type'       => 'dt_dimensions',
			'value'      => '320x180',
			'headings'   => array(
				__( 'Width', 'the7mk2' ),
				__( 'Height', 'the7mk2' ),
			),
		),
		array(
			'heading'    => __( 'Number Input', 'the7mk2' ),
			'param_name' => 'debug_number',
			'type'       => 'dt_number',
			'value'      => '24px',
			'units'      => 'px',
			'min'        => 0,
			'max'        => 200,
		),
		array(
			'heading'    => __( 'Number With Icon', 'the7mk2' ),
			'param_name' => 'debug_number_icon',
			'type'       => 'dt_number_with_icon',
			'value'      => '3px',
			'units'      => array( 'px', '%' ),
			'icon'       => '<span class="dashicons dashicons-admin-generic"></span>',
			'min'        => 0,
			'max'        => 100,
		),
		array(
			'heading'    => __( 'Font Style Picker', 'the7mk2' ),
			'param_name' => 'debug_font_style',
			'type'       => 'dt_font_style',
			'value'      => 'italic:bold:uppercase',
		),
		array(
			'heading'    => __( 'Switch Control', 'the7mk2' ),
			'param_name' => 'debug_switch',
			'type'       => 'dt_switch',
			'value'      => 'y',
			'options'    => array(
				__( 'Enabled', 'the7mk2' )  => 'y',
				__( 'Disabled', 'the7mk2' ) => 'n',
			),
		),
		array(
			'heading'    => __( 'Navigation Icon', 'the7mk2' ),
			'param_name' => 'debug_navigation_icon',
			'type'       => 'dt_navigation',
			'value'      => 'icon-ar-017-r',
		),
		array(
			'heading'    => __( 'Icon Manager', 'the7mk2' ),
			'param_name' => 'debug_icon_manager',
			'type'       => 'icon_manager',
			'value'      => '',
		),
		array(
			'heading'    => __( 'Social Icon Manager', 'the7mk2' ),
			'param_name' => 'debug_soc_icon_manager',
			'type'       => 'dt_soc_icon_manager',
			'value'      => 'dt-icon-facebook',
		),
		array(
			'heading'    => __( 'Radio Image Selector', 'the7mk2' ),
			'param_name' => 'debug_radio_image',
			'type'       => 'dt_radio_image',
			'value'      => 'layout_1',
			'options'    => array(
				'layout_1' => array(
					'title' => __( 'Layout 1', 'the7mk2' ),
					'src'   => '/inc/shortcodes/images/l-01.gif',
				),
				'layout_2' => array(
					'title' => __( 'Layout 2', 'the7mk2' ),
					'src'   => '/inc/shortcodes/images/l-02.gif',
				),
				'layout_3' => array(
					'title' => __( 'Layout 3', 'the7mk2' ),
					'src'   => '/inc/shortcodes/images/l-03.gif',
				),
			),
		),
		array(
			'heading'    => __( 'Gradient Picker', 'the7mk2' ),
			'param_name' => 'debug_gradient',
			'type'       => 'dt_gradient_picker',
			'value'      => '90deg|rgba(12,239,154,0.8) 0%|rgba(0,108,220,0.8) 50%|rgba(184,38,220,0.8) 100%',
		),
		array(
			'heading'    => __( 'Autocomplete', 'the7mk2' ),
			'param_name' => 'debug_autocomplete',
			'type'       => 'autocomplete',
			'settings'   => array(
				'multiple'   => true,
				'min_length' => 0,
			),
			'save_always' => true,
			'description' => __( 'Start typing to test the autocomplete parameter.', 'the7mk2' ),
		),
	),
);
