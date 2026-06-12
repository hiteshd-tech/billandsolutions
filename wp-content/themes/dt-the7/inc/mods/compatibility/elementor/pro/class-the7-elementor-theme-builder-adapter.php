<?php
/**
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Pro;

use ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager;
use ElementorPro\Modules\ThemeBuilder\Module;
use The7\Mods\Compatibility\Elementor\Pro;
use The7\Mods\Compatibility\Elementor\Pro\Modules\Theme_Support\The7_Theme_Support;

defined( 'ABSPATH' ) || exit;

/**
 * Elementor theme builder adapter class.
 */
class The7_Elementor_Theme_Builder_Adapter {

	/**
	 * @return void
	 */
	public function bootstrap() {
		// Locations registration fire on priority 99, so we need to override them later.
		add_action( 'init', [ $this, 'on_elementor_init' ], 1 );

		// Launch before base menus override (10) to apply cascade rules.
		add_action( 'presscore_config_base_init', [ $this, 'header_template_menus_overrides' ], 9 );

		add_action( 'elementor/theme/register_locations', [ $this, 'allow_indepndent_header_and_footer_override' ], 999 );
		add_filter( 'theme_mod_custom_logo', [ $this, 'replace_site_logo_with_the_main_logo_from_theme_options' ] );
	}

	/**
	 * @return void
	 */
	public function on_elementor_init() {
		The7_Theme_Support::instance()->init();
		new \The7\Mods\Compatibility\Elementor\Pro\Modules\Archive\Custom_Pagination_Query_Handler();
	}

	/**
	 * @return void
	 */
	public function header_template_menus_overrides() {
		$header_id             = \The7_Elementor_Compatibility::get_document_id_for_location( 'header' );
		$header_menus_override = new \The7_Header_Menus_Override_Handler( $header_id );
		$header_menus_override->bootstrap();
	}

	/**
	 * @param Locations_Manager $location_manager Elementor locations manager object.
	 */
	public function allow_indepndent_header_and_footer_override( $location_manager ) {
		$overwrite_header_location = ! empty( $location_manager->get_location( 'header' ) );
		$overwrite_footer_location = ! empty( $location_manager->get_location( 'footer' ) );

		if ( $overwrite_header_location || $overwrite_footer_location ) {
			/** @var Module $theme_builder_module */
			$theme_builder_module = Module::instance();

			$conditions_manager = $theme_builder_module->get_conditions_manager();
			$theme_support      = $theme_builder_module->get_component( 'theme_support' );

			if ( empty( $conditions_manager->get_documents_for_location( 'header' ) ) ) {
				remove_action( 'get_header', [ $theme_support, 'get_header' ] );
			}

			if ( empty( $conditions_manager->get_documents_for_location( 'footer' ) ) ) {
				remove_action( 'get_footer', [ $theme_support, 'get_footer' ] );
			}
		}
	}

	/**
	 * @param bool|int $logo_id Logo id.
	 *
	 * @return bool|int
	 */
	public function replace_site_logo_with_the_main_logo_from_theme_options( $logo_id ) {
		$logo = (array) of_get_option( 'header-logo_regular', [ '', 0 ] );
		if ( ! empty( $logo[1] ) ) {
			return $logo[1];
		}

		$logo = (array) of_get_option( 'header-logo_hd', [ '', 0 ] );
		if ( ! empty( $logo[1] ) ) {
			return $logo[1];
		}

		return $logo_id;
	}

}
