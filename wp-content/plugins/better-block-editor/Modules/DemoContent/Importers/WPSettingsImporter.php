<?php
/**
 * WP Settings importer helper.
 *
 * Imports WordPress and specific settings from demo packages.
 *
 * @package BetterBlockEditor
 */

namespace BetterBlockEditor\Modules\DemoContent\Importers;

use BetterBlockEditor\Modules\DemoContent\Trackers\ContentTracker;

defined( 'ABSPATH' ) || exit;

/**
 * Handles importing WordPress settings bundled with demo content.
 */
class WPSettingsImporter {

	/**
	 * Tracks original values so they can be reverted.
	 *
	 * @var ContentTracker
	 */
	private $content_tracker;

	/**
	 * Original option values grouped by setting type.
	 *
	 * @var array
	 */
	private $origin_settings;

	/**
	 * Main content importer instance.
	 *
	 * @var ContentImporter
	 */
	private $importer;

	/**
	 * WPSettingsImporter constructor.
	 *
	 * @param ContentImporter $importer         Content importer helper.
	 * @param ContentTracker  $content_tracker  Tracks values for potential rollback.
	 */
	public function __construct( $importer, $content_tracker ) {
		$this->content_tracker = $content_tracker;
		$this->importer        = $importer;

		$this->origin_settings = array(
			'wp_settings'    => array(),
			'menu_locations' => array(),
		);
	}

	/**
	 * Imports front page related WordPress settings.
	 *
	 * @param array $settings Demo-supplied settings data.
	 * @return void
	 */
	public function import_settings( $settings ) {
		$settings = wp_parse_args(
			$settings,
			array(
				'show_on_front'  => false,
				'page_on_front'  => false,
				'page_for_posts' => false,
			)
		);

		if ( 'page' === $settings['show_on_front'] ) {
			$page_on_front = $this->importer->get_processed_post( $settings['page_on_front'] );
			if ( 'page' === get_post_type( $page_on_front ) ) {
				$this->update_wp_setting( 'show_on_front', 'page' );
				$this->update_wp_setting( 'page_on_front', $page_on_front );
			}

			$page_for_posts = $this->importer->get_processed_post( $settings['page_for_posts'] );
			if ( 'page' === get_post_type( $page_for_posts ) ) {
				$origin_wp_settings['page_for_posts'] = get_option( 'page_for_posts' );

				$this->update_wp_setting( 'page_for_posts', $page_for_posts );
			}

			$this->save_in_tracker( 'wp_settings' );
		}
	}

	/**
	 * Imports theme menu location assignments.
	 *
	 * @param array $menu_locations Map of location slug to menu term IDs.
	 * @return void
	 */
	public function import_menu_locations( $menu_locations ) {
		$locations = get_theme_mod( 'nav_menu_locations' );

		foreach ( $menu_locations as $location => $menu_id ) {
			if ( isset( $locations[ $location ] ) ) {
				$this->origin_settings['menu_locations'][ $location ] = $locations[ $location ];
			}

			$locations[ $location ] = $this->importer->get_processed_term( $menu_id );
		}

		$this->save_in_tracker( 'menu_locations' );

		set_theme_mod( 'nav_menu_locations', $locations );
	}

	/**
	 * Imports widget settings and records originals for rollback.
	 *
	 * @param array $widgets_settings Widget settings keyed by widget identifier.
	 * @return void
	 */
	public function import_widgets( $widgets_settings ) {
		foreach ( $widgets_settings as $widget_id => $settings ) {
			$this->update_widget_setting( $widget_id, $this->filter_widget_settings( $widget_id, $settings ) );
		}

		$this->save_in_tracker( 'widgets_settings' );
	}

	/**
	 * Imports the custom logo option.
	 *
	 * @param int $custom_logo_id Custom logo attachment ID.
	 *
	 * @return void
	 */
	public function import_custom_logo( $custom_logo_id ) {
		$origin_custom_logo = get_theme_mod( 'custom_logo' );
		if ( $origin_custom_logo && $this->content_tracker ) {
			$this->content_tracker->add( 'custom_logo', $origin_custom_logo );
		}
		set_theme_mod( 'custom_logo', $custom_logo_id );
	}

	/**
	 * Imports the site icon option.
	 *
	 * @param int $site_icon_id Site icon attachment ID.
	 *
	 * @return void
	 */
	public function import_site_icon( $site_icon_id ) {
		$origin_site_icon = get_option( 'site_icon' );
		if ( $origin_site_icon && $this->content_tracker ) {
			$this->content_tracker->add( 'site_icon', $origin_site_icon );
		}
		update_option( 'site_icon', $site_icon_id );
	}

	/**
	 * Updates a WordPress option while storing its original value.
	 *
	 * @param string $key   Option name.
	 * @param mixed  $value New option value.
	 * @return void
	 */
	protected function update_wp_setting( $key, $value ) {
		$this->origin_settings['wp_settings'][ $key ] = get_option( $key );

		update_option( $key, $value );
	}

	/**
	 * Updates a widget option while storing its original value.
	 *
	 * @param string $key   Widget option name.
	 * @param mixed  $value New option value.
	 * @return void
	 */
	protected function update_widget_setting( $key, $value ) {
		$this->origin_settings['widgets_settings'][ $key ] = get_option( $key );

		update_option( $key, $value );
	}

	/**
	 * Normalises widget settings for imported menu references.
	 *
	 * @param string $widget_id Widget identifier.
	 * @param array  $settings  Raw widget settings.
	 * @return array Processed widget settings.
	 */
	protected function filter_widget_settings( $widget_id, $settings ) {
		if ( in_array(
			$widget_id,
			array( 'widget_presscore-custom-menu-one', 'widget_presscore-custom-menu-two' ),
			true
		) ) {
			foreach ( $settings as &$widget_settings ) {
				if ( isset( $widget_settings['menu'] ) ) {
					$widget_settings['menu'] = $this->importer->get_processed_term( $widget_settings['menu'] );
				}
			}
			unset( $widget_settings );
		}

		return $settings;
	}

	/**
	 * Records original settings in the tracker for potential restoration.
	 *
	 * @param string $key Settings bucket key.
	 * @return void
	 */
	protected function save_in_tracker( $key ) {
		if ( isset( $this->origin_settings[ $key ] ) && $this->content_tracker ) {
			$this->content_tracker->add( $key, $this->origin_settings[ $key ] );
		}
	}
}
