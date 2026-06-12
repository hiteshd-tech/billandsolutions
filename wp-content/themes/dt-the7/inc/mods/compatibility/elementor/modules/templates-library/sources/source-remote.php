<?php

namespace The7\Mods\Compatibility\Elementor\Modules\Templates_Library\Sources;

use Elementor\Core\Common\Modules\Connect\Module as ConnectModule;
use Elementor\Plugin as Elementor;
use Elementor\TemplateLibrary\Source_Base;
use The7_Remote_API;
use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor template library remote source.
 * Elementor template library remote source handler class is responsible for
 * handling remote templates from Elementor.com servers.
 *
 * @since 1.0.0
 */
class Source_Remote extends Source_Base {

	const TEMPLATE_ID_PREFIX = 'the7_';
	const SOURCE_ID          = 'the7-remote';

	const TEMPLATES_DATA_TRANSIENT_KEY = 'the7_elementor_remote_templates_data_';

	/**
	 * @var The7_Remote_API
	 */
	protected $remote_api;

	public function __construct() {
		parent::__construct();
		$this->remote_api = new The7_Remote_API( presscore_get_purchase_code() );
	}

	/**
	 * Get remote template ID.
	 * Retrieve the remote template ID.
	 *
	 * @return string The remote template ID.
	 * @since  1.0.0
	 * @access public
	 */
	public function get_id() {
		return self::SOURCE_ID;
	}

	/**
	 * Get remote template title.
	 * Retrieve the remote template title.
	 *
	 * @return string The remote template title.
	 * @since  1.0.0
	 * @access public
	 */
	public function get_title() {
		return esc_html__( 'The7 Remote', 'the7mk2' );
	}

	/**
	 * Register remote template data.
	 * Used to register custom template data like a post type, a taxonomy or any
	 * other data.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function register_data() {
	}

	/**
	 * Get remote template.
	 * Retrieve a single remote template from Elementor.com servers.
	 *
	 * @param int $template_id The template ID.
	 *
	 * @return array Remote template.
	 * @since  1.0.0
	 * @access public
	 */
	public function get_item( $template_id ) {
		$templates = $this->get_items();

		return $templates[ $template_id ];
	}

	/**
	 * Get remote templates.
	 * Retrieve remote templates from Elementor.com servers.
	 *
	 * @param array $args Optional. Not used in remote source.
	 *
	 * @return array Remote templates.
	 * @since  1.0.0
	 * @access public
	 */
	public function get_items( $args = [] ) {
		$force_update = ! empty( $args['force_update'] ) && is_bool( $args['force_update'] );

		$templates_data = $this->get_templates_data( $force_update );

		$templates = [];

		foreach ( $templates_data as $template_data ) {
			$templates[] = $this->prepare_template( $template_data );
		}

		return $templates;
	}

	/**
	 * Get templates data from a transient or from a remote request.
	 * In any of the following 2 conditions, the remote request will be triggered:
	 * 1. Force update - "$force_update = true" parameter was passed.
	 * 2. The data saved in the transient is empty or not exist.
	 *
	 * @param bool $force_update
	 *
	 * @return array
	 */
	private function get_templates_data( bool $force_update ): array {
		$templates_data_cache_key = static::TEMPLATES_DATA_TRANSIENT_KEY . ELEMENTOR_VERSION;

		$experiments_manager = Elementor::$instance->experiments;
		$editor_layout_type  = $experiments_manager->is_feature_active( 'container' ) ? 'container_flexbox' : '';

		if ( $force_update ) {
			return $this->get_templates( $editor_layout_type );
		}

		$templates_data = get_transient( $templates_data_cache_key );

		if ( empty( $templates_data ) ) {
			return $this->get_templates( $editor_layout_type );
		}

		return $templates_data;
	}

	/**
	 * Get the templates from a remote server and set a transient.
	 *
	 * @param string $editor_layout_type
	 *
	 * @return array
	 */
	private function get_templates( string $editor_layout_type ): array {
		$templates_data_cache_key = static::TEMPLATES_DATA_TRANSIENT_KEY . ELEMENTOR_VERSION;

		$templates_data = $this->get_templates_remotely( $editor_layout_type );

		if ( empty( $templates_data ) ) {
			return [];
		}

		set_transient( $templates_data_cache_key, $templates_data, 12 * HOUR_IN_SECONDS );

		return $templates_data;
	}

	/**
	 * Fetch templates from the remote server.
	 *
	 * @param string $editor_layout_type
	 *
	 * @return array|false
	 */
	private function get_templates_remotely( string $editor_layout_type ) {
		$response = wp_remote_get(
			$this->remote_api->get_elementor_templates_list_url(),
			[
				'body' => [
					'plugin_version'     => ELEMENTOR_VERSION,
					'editor_layout_type' => $editor_layout_type,
				],
			]
		);

		if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
			return false;
		}

		$templates_data = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( empty( $templates_data ) || ! is_array( $templates_data ) ) {
			return [];
		}

		return $templates_data;
	}

	/**
	 * @since  2.2.0
	 * @access private
	 */
	private function prepare_template( array $template_data ) {
		$favorite_templates = $this->get_user_meta( 'favorites' );

		$access_tier = 'free';

		// BC: Support legacy APIs that don't have access tiers.
		if ( isset( $template_data['access_tier'] ) ) {
			$access_tier = $template_data['access_tier'];
		} elseif (
			defined( 'Elementor\Core\Common\Modules\Connect\Module::ACCESS_TIER_FREE' ) &&
			defined( 'Elementor\Core\Common\Modules\Connect\Module::ACCESS_TIER_ESSENTIAL' )
		) {
			$access_tier = 0 === $template_data['access_level']
				? ConnectModule::ACCESS_TIER_FREE
				: ConnectModule::ACCESS_TIER_ESSENTIAL;
		}

		return [
			'template_id'     => static::TEMPLATE_ID_PREFIX . $template_data['id'],
			'source'          => 'remote',
			'type'            => $template_data['type'],
			'subtype'         => $template_data['subtype'],
			'title'           => 'The7 - ' . $template_data['title'],
			'thumbnail'       => $template_data['thumbnail'],
			'date'            => $template_data['tmpl_created'],
			'author'          => $template_data['author'],
			'tags'            => is_array( $template_data['tags'] ) ? $template_data['tags'] : json_decode( $template_data['tags'], true ),
			'isPro'           => false,
			'the7_pro'        => (bool) $template_data['is_pro'],
			'accessLevel'     => $template_data['access_level'],
			'accessTier'      => $access_tier,
			'popularityIndex' => (int) $template_data['popularity_index'],
			'trendIndex'      => (int) $template_data['trend_index'],
			'hasPageSettings' => (bool) $template_data['has_page_settings'],
			'url'             => $template_data['url'],
			'favorite'        => ! empty( $favorite_templates[ $template_data['id'] ] ),
		];
	}

	/**
	 * Save remote template.
	 * Remote template from Elementor.com servers cannot be saved on the
	 * database as they are retrieved from remote servers.
	 *
	 * @param array $template_data Remote template data.
	 *
	 * @return \WP_Error
	 * @since  1.0.0
	 * @access public
	 */
	public function save_item( $template_data ) {
		return new \WP_Error( 'invalid_request', 'Cannot save template to a remote source' );
	}

	/**
	 * Update remote template.
	 * Remote template from Elementor.com servers cannot be updated on the
	 * database as they are retrieved from remote servers.
	 *
	 * @param array $new_data New template data.
	 *
	 * @return \WP_Error
	 * @since  1.0.0
	 * @access public
	 */
	public function update_item( $new_data ) {
		return new \WP_Error( 'invalid_request', 'Cannot update template to a remote source' );
	}

	/**
	 * Delete remote template.
	 * Remote template from Elementor.com servers cannot be deleted from the
	 * database as they are retrieved from remote servers.
	 *
	 * @param int $template_id The template ID.
	 *
	 * @return \WP_Error
	 * @since  1.0.0
	 * @access public
	 */
	public function delete_template( $template_id ) {
		return new \WP_Error( 'invalid_request', 'Cannot delete template from a remote source' );
	}

	/**
	 * Export remote template.
	 * Remote template from Elementor.com servers cannot be exported from the
	 * database as they are retrieved from remote servers.
	 *
	 * @param int $template_id The template ID.
	 *
	 * @return \WP_Error
	 * @since  1.0.0
	 * @access public
	 */
	public function export_template( $template_id ) {
		return new \WP_Error( 'invalid_request', 'Cannot export template from a remote source' );
	}

	/**
	 * Fetch template content from server.
	 *
	 * @param string $template_id Template ID.
	 *
	 * @return array|WP_Error Template content.
	 * @since  1.0.0
	 * @access public
	 */
	public function get_template_content( string $template_id ) {
		$url = $this->remote_api->get_elementor_template_download_url( $template_id );

		$response = wp_remote_get(
			$url,
			[
				'timeout' => 60,
			]
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$response_code = (int) wp_remote_retrieve_response_code( $response );

		if ( $response_code !== 200 ) {
			return new \WP_Error( 'response_code_error', sprintf( 'The request returned with a status code of %s.', $response_code ) );
		}

		$template_content = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( isset( $template_content['error'] ) ) {
			return new \WP_Error( 'response_error', $template_content['error'] );
		}

		if ( empty( $template_content['content'] ) ) {
			return new \WP_Error( 'template_data_error', 'An invalid data was returned.' );
		}

		if ( ! \is_array( $template_content['content'] ) ) {
			$template_content['content'] = json_decode( $template_content['content'], true );
		}

		return $template_content;
	}

	/**
	 * Get remote template data.
	 * Retrieve the data of a single remote template from server.
	 *
	 * @param array  $args    Custom template arguments.
	 * @param string $context Optional. The context. Default is `display`.
	 *
	 * @return array|\WP_Error Remote Template data.
	 * @since  1.5.0
	 * @access public
	 */
	public function get_data( array $args, $context = 'display' ) {
		$data = $this->get_template_content( $args['template_id'] );

		if ( is_wp_error( $data ) ) {
			return $data;
		}

		// Set the Request's state as an Elementor upload request, in order to support unfiltered file uploads.
		Elementor::$instance->uploads_manager->set_elementor_upload_state( true );

		// BC.
		$data = (array) $data;

		// Create related templates.
		if ( ! empty( $data['related_templates'] ) ) {
			foreach ( (array) $data['related_templates'] as $rel_template ) {
				$rel_template['content'] = $this->replace_elements_ids( $rel_template['content'] );
				$rel_template['content'] = $this->process_export_import_content( $rel_template['content'], 'on_import' );
				$type                    = $rel_template['type'];
				$title                   = $rel_template['title'];
				$template_id             = $rel_template['post_id'];

				$template_document = Elementor::$instance->documents->create(
					$type,
					[
						'post_title' => $title . ' (imported)',
					]
				);
				if ( ! is_wp_error( $template_document ) ) {
					$template_document->save(
						[
							'elements' => $rel_template['content'],
							'settings' => $rel_template['page_settings'],
						]
					);
					$data['content'] = Elementor::$instance->db->iterate_data(
						$data['content'],
						function ( $element_data ) use ( $template_document, $template_id ) {
							if ( ! empty( $element_data['settings']['the7_overlay_template'] ) && $element_data['settings']['the7_overlay_template'] === $template_id ) {
								$element_data['settings']['the7_overlay_template'] = $template_document->get_main_id();
							}

							return $element_data;
						}
					);
				}
			}
		}

		$data['content'] = $this->replace_elements_ids( $data['content'] );
		$data['content'] = $this->process_export_import_content( $data['content'], 'on_import' );

		$post_id  = $args['editor_post_id'];
		$document = Elementor::$instance->documents->get( $post_id );
		if ( $document ) {
			$data['content'] = $document->get_elements_raw_data( $data['content'], true );
		}

		// After the upload complete, set the elementor upload state back to false
		Elementor::$instance->uploads_manager->set_elementor_upload_state( false );

		return $data;
	}

	public function clear_cache() {
		delete_transient( static::TEMPLATES_DATA_TRANSIENT_KEY . ELEMENTOR_VERSION );
	}
}
