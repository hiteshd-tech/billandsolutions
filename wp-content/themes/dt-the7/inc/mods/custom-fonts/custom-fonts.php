<?php
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Presscore_Modules_Custom_Fonts', false ) ) {

	class Presscore_Modules_Custom_Fonts {

		/**
		 * Execute module.
		 */
		public static function execute() {
			$fa_enqueue_optimizer = new The7_FontAwesome_Enqueue_Optimizer();
			$fa_enqueue_optimizer->run();

			The7_Icon_Manager::add_hooks();

			// Load custom icons for the WPB Builder.
			add_action( 'vc_backend_editor_enqueue_js_css', [ The7_Icon_Manager::class, 'enqueue_icon_fonts' ] );
			add_action( 'vc_frontend_editor_enqueue_js_css', [ The7_Icon_Manager::class, 'enqueue_icon_fonts' ] );

			// Load custom icons for the theme options.
			add_action( 'optionsframework_load_styles', [ The7_Icon_Manager::class, 'enqueue_icon_fonts' ] );

			add_filter( 'the7_icons_in_settings', array( __CLASS__, 'custom_icons_in_shortcodes' ) );
			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'dequeue_ua_icon_fonts' ), 9999 );
			add_action( 'presscore_js_composer_after_bridge_loaded', array(
				__CLASS__,
				'add_the7_icons_fields_in_vc_ui',
			) );
			add_filter( 'vc_iconpicker-type-the7_icons', array(
				__CLASS__,
				'populate_the7_icons_list_in_vc_ui',
			), 9999 );
		}

		/**
		 * Allow to use custom icons in the7 shortcodes.
		 *
		 * @param array $icons
		 *
		 * @return array
		 */
		public static function custom_icons_in_shortcodes( $icons ) {
			$custom_icons = The7_Icon_Manager::get_icons_classes();

			return array_merge( $icons, $custom_icons );
		}

		/**
		 * Dequeue UA icon fonts to prevent duplication.
		 */
		public static function dequeue_ua_icon_fonts() {
			$icons_list = The7_Icon_Manager::load_iconfont_list();
			foreach ( array_keys( $icons_list ) as $icon_font_name ) {
				wp_dequeue_style( "bsf-{$icon_font_name}" );
			}
		}

		/**
		 * Add the7 fonts in shortcodes VC UI.
		 *
		 * @param array $icons_classes
		 *
		 * @return array
		 */
		public static function populate_the7_icons_list_in_vc_ui( $icons_classes = array() ) {
			$custom_icons = The7_Icon_Manager::get_icon_fonts_list();
			foreach ( $custom_icons as $font => $icons ) {
				$icons_classes[ $font ] = array();
				foreach ( $icons as $key => $icon ) {
					$class_prefix           = '';
					if ( is_string( $key ) ) {
						$class_prefix = "{$font}-";
					}
					$icons_classes[ $font ][] = array( $class_prefix . $icon['class'] => $icon['class'] );
				}
			}

			return $icons_classes;
		}

		/**
		 * Allow to use custom icons in VC shortcodes.
		 */
		public static function add_the7_icons_fields_in_vc_ui() {
			$shortcodes_to_modify = [
				'vc_icon'        => [
					'type'  => 'type',
					'icons' => 'icon_the7',
				],
				'vc_btn'         => [
					'type'  => 'i_type',
					'icons' => 'i_icon_the7',
				],
				'vc_tta_section' => [
					'type'  => 'i_type',
					'icons' => 'i_icon_the7',
				],
				'vc_pricing_table' => [
					'type'              => 'btn_i_type',
					'icons'             => 'btn_i_icon_the7',
					'additional_params' => [
						'integrated_shortcode'       => 'vc_btn',
						'integrated_shortcode_field' => 'btn_',
						'group'                      => 'Button',
					],
				],
				'vc_message'     => [
					'type'  => 'icon_type',
					'icons' => 'icon_the7',
				],
			];

			foreach ( $shortcodes_to_modify as $tag => $params ) {
				self::add_the7_icons_to_vc_shortcode( $tag, $params );
			}
		}

		/**
		 * Add the7 icons type and selector in VC shortcode interface.
		 *
		 * @param  string  $tag  Shortcode tag.
		 * @param  array  $params  Icons type and selector.
		 */
		protected static function add_the7_icons_to_vc_shortcode( $tag, $params ) {
			$type_param = isset( $params['type'] ) ? $params['type'] : 'type';
			$icons_param = isset( $params['icons'] ) ? $params['icons'] : 'icon_the7';
			$the7_icons_title = [ esc_html__( 'The7 Icons', 'the7mk2' ) => 'the7' ];
			$the7_icons_param = [
				array_merge(
					[
						'type'        => 'iconpicker',
						'heading'     => esc_html__( 'Icon', 'the7mk2' ),
						'param_name'  => $icons_param,
						'value'       => 'vc-oi vc-oi-dial',
						'settings'    => [
							'emptyIcon'    => false,
							'type'         => 'the7_icons',
							'iconsPerPage' => 4000,
						],
						'dependency'  => [
							'element' => $type_param,
							'value'   => 'the7',
						],
						'description' => esc_html__( 'Select icon from library.', 'the7mk2' ),
					],
					isset( $params['additional_params'] ) ? $params['additional_params'] : []
				),
			];

			$settings = WPBMap::getShortCode( $tag );

			if ( ! isset( $settings['params'] ) || ! is_array( $settings['params'] ) ) {
				return;
			}

			$params   = $settings['params'];

			foreach ( $settings['params'] as $key => $param ) {
				if ( $param['param_name'] !== $type_param ) {
					continue;
				}

				$settings                      = wp_parse_args( $settings, [
					'admin_enqueue_css' => [],
					'front_enqueue_css' => [],
				] );
				$settings['admin_enqueue_css'] = the7_get_custom_icons_stylesheets( $settings['admin_enqueue_css'] );
				$settings['front_enqueue_css'] = the7_get_custom_icons_stylesheets( $settings['front_enqueue_css'] );

				$params[ $key ]['value'] = array_merge( $params[ $key ]['value'], $the7_icons_title );
				$params                  = dt_array_push_after( $params, $the7_icons_param, $key );
			}

			$settings['params'] = $params;
			unset( $settings['base'] );

			WPBMap::modify( $tag, $settings );
		}
	}

	Presscore_Modules_Custom_Fonts::execute();
}
