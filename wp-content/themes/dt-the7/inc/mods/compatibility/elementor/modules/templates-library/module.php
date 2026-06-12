<?php

namespace The7\Mods\Compatibility\Elementor\Modules\Templates_Library;

use Elementor\Plugin as Elementor;
use The7\Mods\Compatibility\Elementor\Modules\Templates_Library\Sources\Source_Remote;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Module extends The7_Elementor_Module_Base {

	public function __construct() {
		$this->register_default_sources();
		add_action( 'elementor/editor/init', [ $this, 'add_templates' ] );

		if ( defined( '\Elementor\Api::LIBRARY_OPTION_KEY' ) ) {
			add_filter( 'option_' . \Elementor\Api::LIBRARY_OPTION_KEY, [
				$this,
				'library_update_filter',
			] );
		}
		add_action( 'elementor/ajax/register_actions', [ $this, 'register_actions' ], 50 );
	}

	private function register_default_sources() {
		$sources = [
			'remote',
		];

		foreach ( $sources as $source_filename ) {
			$class_name = ucwords( $source_filename );
			$class_name = str_replace( '-', '_', $class_name );

			Elementor::$instance->templates_manager->register_source( __NAMESPACE__ . '\Sources\Source_' . $class_name );
		}
	}

	/**
	 * Get template data.
	 *
	 * @param array $args Request arguments.
	 *
	 * @return array Template data.
	 * @since 1.0.0
	 */
	public static function get_template_data( $args ) {
		$source = Elementor::instance()->templates_manager->get_source( Source_Remote::SOURCE_ID );

		$args['template_id'] = intval( str_replace( Source_Remote::TEMPLATE_ID_PREFIX, '', $args['template_id'] ) );

		$data = $source->get_data( $args );

		return $data;
	}

	public static function library_update_filter( $value ) {
		//here we can add own categories
		//$value['types_data']['lb']['categories'] = [ 'post', 'product' ];

		return $value;
	}

	public static function add_templates() {
		//should replace default elementor templates for library
		Elementor::instance()->common->add_template( __DIR__ . '/views/templates.php' );
	}

	public function register_actions( $ajax ) {
		if ( ! isset( $_REQUEST['action'] ) && 'elementor_ajax' !== $_REQUEST['action'] && ! isset( $_REQUEST['actions'] ) ) {
			return;
		}

		$actions = json_decode( stripslashes( $_REQUEST['actions'] ), true );

		$data = null;

		foreach ( $actions as $action) {
			if ( isset( $action['action']) && $action['action'] === 'get_template_data' ) {
				$data = $action;
			}
		}

		if ( ! $data || ! isset( $data['data'] ) ) {
			return;
		}

		$data = $data['data'];

		if ( empty( $data['template_id'] ) || strpos( $data['template_id'], Source_Remote::TEMPLATE_ID_PREFIX ) === false ) {
			return;
		}

		$ajax->register_ajax_action( 'get_template_data', [ $this, 'get_template_data' ] );
	}

	public function get_name() {
		return 'templates-library';
	}
}
