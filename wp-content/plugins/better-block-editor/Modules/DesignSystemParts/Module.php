<?php
/**
 * Design System Parts module.
 *
 * @package BetterBlockEditor
 */

namespace BetterBlockEditor\Modules\DesignSystemParts;

use BetterBlockEditor\Base\ModuleBase;
use BetterBlockEditor\Base\ManagableModuleInterface;
use BetterBlockEditor\Base\ConfigurableModuleInterface;
use BetterBlockEditor\Core\Settings;

defined( 'ABSPATH' ) || exit;

class Module extends ModuleBase implements ManagableModuleInterface, ConfigurableModuleInterface {

	const MODULE_IDENTIFIER = 'design-system-parts';
	const ACTIVE_PARTS = 'active-parts';

	const SETTINGS_ORDER = 1600;

	public static function get_default_state() {
		return false;
	}

	public static function get_tab() {
		return Settings::TAB_DESIGN;
	}

	public static function get_title() {
		return __( 'Design System', 'better-block-editor' );
	}

	public static function get_label() {
		return __( 'Turn on starter design system', 'better-block-editor' );
	}

	public function setup_hooks() {
		add_filter( 'block_editor_settings_all', array( $this, 'modify_block_editor_settings' ), 10 );
		// add filter on very late stage to change theme data just before using it in FE
		add_filter( 'wp_theme_json_data_theme', array( $this, 'modify_theme_json_values' ), 1000 );
		// exclude parts of design system
		add_filter( 'wpbbe_design_system_parts', array( $this, 'filter_design_system_parts' ), 10, 2 );
		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
	}

	public function filter_design_system_parts( $parts, $context ) {
		$active_parts = $this->get_option( self::ACTIVE_PARTS, self::get_settings()[ self::ACTIVE_PARTS ]['default'] );
		$map = array(
			'color'      => array( 'colorPalette', 'colorGradients' ),
			'typography' => array( 'fontSizes' ),
			'spacing'    => array( 'spacingSizes' ),
		);
		$active_parts['spacing'] = true; // always include spacing

		foreach ( $active_parts as $key => $enabled ) {
			if ( ! empty( $enabled ) && isset( $map[ $key ] ) ) {
				$parts = array_merge( $parts, $map[ $key ] );
			}
		}
		foreach ( $active_parts as $key => $enabled ) {
			if ( ! empty( $enabled ) ) {
				$parts[] = $key;
			}
		}

		return $parts;
	}


	/**
	 * Modify the block editor settings to include design system settings.
	 *
	 * @param array $settings The existing block editor settings.
	 *
	 * @return array The modified block editor settings.
	 */
	public function modify_block_editor_settings( $settings ) {
		$design_system = $this->get_design_system( 'editor' );

		foreach ( $design_system as $key => $new_settings ) {
			if ( ! isset( $settings[ $key ] ) || ! is_array( $settings[ $key ] ) ) {
				$settings[ $key ] = array();
			}

			$settings[ $key ] = $this->add_settings( $settings[ $key ], $new_settings );
		}

		return $settings;
	}

	/**
	 * Modify the theme JSON data to include design system settings.
	 *
	 * @param \WP_Theme_JSON_Data $theme_json_object
	 *
	 * @return \WP_Theme_JSON_Data
	 */
	public function modify_theme_json_values( $theme_json_object ) {
		$settings = $theme_json_object->get_data();
		$design_system = $this->get_design_system();
		$new_settings = $design_system;

		foreach ( $design_system['settings'] as $s_key => $s_settings ) {
			if ( ! is_array( $s_settings ) ) {
				continue;
			}

			foreach ( $s_settings as $ss_key => $ss_settings ) {
				if ( ! isset( $settings['settings'][ $s_key ][ $ss_key ]['theme'] ) || ! is_array( $settings['settings'][ $s_key ][ $ss_key ]['theme'] ) ) {
					$settings['settings'][ $s_key ][ $ss_key ]['theme'] = array();
				}

				$new_settings['settings'][ $s_key ][ $ss_key ] = array();
				$new_settings['settings'][ $s_key ][ $ss_key ]['theme'] = $this->add_settings( $settings['settings'][ $s_key ][ $ss_key ]['theme'], $ss_settings );
			}
		}

		$theme_json_object->update_with( $new_settings );

		return $theme_json_object;
	}

	/**
	 * Get the design system data.
	 *
	 * @param string $context The context in which the design system is used (e.g., 'front', 'editor').
	 *
	 * @return array The design system data.
	 */
	private function get_design_system( $context = 'front' ) {
		$data = json_decode( file_get_contents( __DIR__ . '/design-system.json' ), true );
		if ( ! is_array( $data ) ) {
			return array();
		}

		$data = wp_parse_args( $data, array(
				'version'  => 3,
				'settings' => array(
					'color'      => array(
						'palette'   => array(),
						'gradients' => array(),
					),
					'typography' => array(
						'fontSizes' => array(),
					),
					'spacing'    => array(
						'spacingSizes' => array(),
					),
				),
			) );

		// Active parts filtering (whitelist)
		$active_parts = apply_filters( 'wpbbe_design_system_parts', array(), $context );
		if ( ! empty( $active_parts ) && isset( $data['settings'] ) ) {
			foreach ( array_keys( $data['settings'] ) as $part ) {
				if ( ! in_array( $part, $active_parts, true ) ) {
					unset( $data['settings'][ $part ] );
				}
			}
		}

		// Editor context: return remapped structure
		if ( $context === 'editor' ) {
			return array(
				'spacingSizes'   => $data['settings']['spacing']['spacingSizes'] ?? array(),
				'colorPalette'   => $data['settings']['color']['palette'] ?? array(),
				'colorGradients' => $data['settings']['color']['gradients'] ?? array(),
				'fontSizes'      => $data['settings']['typography']['fontSizes'] ?? array(),
			);
		}

		return $data;
	}

	/**
	 * Add new settings to the existing settings array if they do not already exist.
	 *
	 * @param array $settings     The existing settings array.
	 * @param array $new_settings The new settings to add.
	 *
	 * @return array The updated settings array.
	 */
	private function add_settings( array $settings, array $new_settings ): array {
		$existing_slugs = array_map( function ( $setting ) {
			return $setting['slug'];
		}, $settings );

		foreach ( $new_settings as $new_setting ) {
			if ( ! in_array( $new_setting['slug'], $existing_slugs, true ) ) {
				$settings[] = $new_setting;
			}
		}

		return array_values( $settings );
	}

	public static function get_settings(): array {
		return array(
			self::ACTIVE_PARTS => array(
				'type'        => 'multicheckbox',
				'title'       => __( 'Design System Parts', 'better-block-editor' ),
				'options'     => array(
					'color'      => __( 'Colors', 'better-block-editor' ),
					'typography' => __( 'Typography', 'better-block-editor' ),
					'spacing'    => array( 'label' => __( 'Spacing', 'better-block-editor' ), 'disabled' => true )
				),
				'default'     => array(  'color'=> 1, 'typography' => 1, 'spacing' => 1 ),
				'description' => __( 'Choose active parts of the design system.', 'better-block-editor' ),
			),
		);
	}

	public function register_rest_routes() {
		register_rest_route( WPBBE_REST_BASE, '/design-system-settings', array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'update_settings' ),
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			) );
	}

	public function update_settings( \WP_REST_Request $request ) {
		$data = $request->get_param( self::ACTIVE_PARTS );
		if ( ! is_array( $data ) ) {
			return new \WP_Error( 'invalid_data', 'Invalid data', array( 'status' => 400 ) );
		}
		$this->set_option( self::ACTIVE_PARTS, $data );

		return array(
			'success'  => true,
			'settings' => $data,
		);
	}
}
