<?php
/**
 * @package The7
 */

defined( 'ABSPATH' ) || exit;

class The7_Core_Compatibility {

	/**
	 * Initiate compatibility actions.
	 */
	public static function setup() {
		$plugin_version = The7PT()->version();

		if ( version_compare( $plugin_version, '1.4.1', '>=' ) ) {
			self::hide_modules_options();
			add_action( 'the7_before_meta_box_registration', [ __CLASS__, 'exclude_meta_fields_from_presets' ], 9999 );
		}

		if ( the7_is_elementor_theme_mode_active() ) {
			add_action( 'the7_before_meta_box_registration', [ __CLASS__, 'exclude_legacy_meta_fields' ], 9999 );
		}
	}

	/**
	 * Determine is dt-the7-core has compatible version or not.
	 *
	 * @return bool
	 */
	public static function plugin_is_compatible() {
		return version_compare( The7PT()->version(), THE7_CORE_COMPATIBLE_VERSION, '>=' );
	}

	/**
	 * Remove theme options for backwars compatibility.
	 */
	public static function hide_modules_options() {
		remove_filter( 'presscore_options_files_to_load', array( 'The7PT_Admin', 'add_module_options' ) );
	}

	/**
	 * Exclude some fields from presets and defaults.
	 */
	public static function exclude_meta_fields_from_presets() {
		global $DT_META_BOXES;

		foreach ( $DT_META_BOXES as &$meta_box ) {
			// Exclude albums media.
			if ( $meta_box['id'] === 'dt_page_box-album_post_media' && isset( $meta_box['fields'][0]['id'] ) ) {
				$meta_box['fields'][0]['exclude_from_presets'] = true;
				// Exclude project media.
			} elseif ( $meta_box['id'] === 'dt_page_box-portfolio_post_media' && isset( $meta_box['fields'][0]['id'] ) ) {
				$meta_box['fields'][0]['exclude_from_presets'] = true;
				// Exclude teammate options.
			} elseif ( $meta_box['id'] === 'dt_page_box-testimonial_options' && ! empty( $meta_box['fields'] ) ) {
				foreach ( $meta_box['fields'] as &$field ) {
					if ( $field['id'] === '_dt_teammate_options_go_to_single' ) {
						continue;
					}

					$field['exclude_from_presets'] = true;
				}
			}
		}
	}

	/**
	 * Exclude meta fields not used in Elementor context.
	 *
	 * @return void
	 */
	public static function exclude_legacy_meta_fields() {
		global $DT_META_BOXES;

		foreach ( $DT_META_BOXES as &$meta_box ) {
			if ( $meta_box['id'] === 'dt_page_box-portfolio_post' && isset( $meta_box['fields'] ) ) {
				$meta_box['fields'] = array_filter(
					$meta_box['fields'],
					function( $field ) {
						$include = [
							'_dt_project_options_show_link',
							'_dt_project_options_link',
							'_dt_project_options_link_target',
						];
						return in_array( $field['id'], $include, true );
					}
				);
				$meta_box['fields'] = array_values( $meta_box['fields'] );
				unset( $meta_box['fields'][0]['options']['1'] );
			}
		}
	}
}
