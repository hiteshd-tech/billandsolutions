<?php

namespace The7\Mods\Compatibility\Elementor\Pro\Modules\Theme_Support;

use Elementor\Plugin;
use ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager;
use The7_Elementor_Compatibility;

defined( 'ABSPATH' ) || exit;

class The7_Theme_Support {

	/**
	 * @var The7_Theme_Support
	 */
	public static $instance;

	/**
	 * @var bool
	 */
	public $print_header_and_footer = true;

	/**
	 * The7_Theme_Support constructor.
	 *
	 * @return The7_Theme_Support
	 */
	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new static();
		}

		return self::$instance;
	}

	/**
	 * @param Locations_Manager $manager Elementor locations manager.
	 *
	 * @return void
	 */
	public function register_locations( $manager ) {
		$manager->register_core_location( 'header' );
		$manager->register_core_location( 'footer' );
	}

	/**
	 * Turn off theme header and footer, use Elementor core locations instead.
	 *
	 * @return void
	 */
	public function overwrite_config_base_init() {
		$header_id = The7_Elementor_Compatibility::get_document_id_for_location( 'header' );
		if ( $header_id ) {
			if ( the7_is_elementor_theme_mode_active() ) {
				presscore_config()->set( 'header_title', 'false' );
				presscore_config()->set( 'header.floating_navigation.enabled', false );
				add_filter( 'presscore_show_header', '__return_false' );
			} else {
				presscore_config_populate_header_options( $header_id );
			}
			add_action( 'presscore_before_main_container', [ $this, 'do_header' ], 17 );
		}

		$footer_id = The7_Elementor_Compatibility::get_document_id_for_location( 'footer' );
		if ( $footer_id ) {
			presscore_config()->set( 'template.bottom_bar.enabled', false );
			add_filter( 'presscore_replace_footer', '__return_true' );
			add_action( 'presscore_before_footer_widgets', [ $this, 'do_footer' ], 0 );
			add_action(
				'presscore_footer_html_class',
				static function ( $output ) {
					$output[] = 'elementor-footer';

					return $output;
				}
			);
		}

		if ( the7_is_elementor_theme_mode_active() ) {
			presscore_config()->set( 'sidebar_position', 'disabled' );
		}
	}

	/**
	 * Turn off header and footer.
	 *
	 * @return void
	 */
	public function turn_off_header_and_footer() {
		$this->print_header_and_footer = false;

		if ( ! has_action( 'body_class', [ $this, 'hide_header_footer_via_body_class' ] ) ) {
			add_filter( 'body_class', [ $this, 'hide_header_footer_via_body_class' ], 99 );
		}
	}

	/**
	 * Hide header and footer via body class.
	 *
	 * @param array $classes Body classes.
	 *
	 * @return array
	 */
	public function hide_header_footer_via_body_class( $classes ) {
		$classes[] = 'the7-hide-header';
		$classes[] = 'the7-hide-footer';

		return $classes;
	}

	/**
	 * Print header.
	 *
	 * @return void
	 */
	public function do_header() {
		if ( $this->print_header_and_footer ) {
			elementor_theme_do_location( 'header' );
		}
	}

	/**
	 * Print footer.
	 *
	 * @return void
	 */
	public function do_footer() {
		if ( $this->print_header_and_footer ) {
			elementor_theme_do_location( 'footer' );
		}
	}

	/**
	 * Alter current page value with archive template id in the theme config.
	 *
	 * @param int|null $page_id Page ID.
	 *
	 * @return int|null|false
	 */
	public function config_page_id_filter( $page_id = null ) {
		if ( is_singular() ) {
			$document = Plugin::instance()->documents->get_doc_for_frontend( get_the_ID() );
			if ( $document && $document::get_property( 'support_wp_page_templates' ) ) {
				$wp_page_template = $document->get_meta( '_wp_page_template' );
				if ( $wp_page_template && 'default' !== $wp_page_template ) {
					return $page_id;
				}
			}
		}

		return The7_Elementor_Compatibility::get_applied_archive_page_id( $page_id );
	}

	/**
	 * Add handlers to launch the module.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'elementor/theme/register_locations', [ $this, 'register_locations' ] );
		add_action( 'presscore_config_base_init', [ $this, 'overwrite_config_base_init' ] );
		add_filter( 'presscore_config_post_id_filter', [ $this, 'config_page_id_filter' ], 5 );
	}
}
