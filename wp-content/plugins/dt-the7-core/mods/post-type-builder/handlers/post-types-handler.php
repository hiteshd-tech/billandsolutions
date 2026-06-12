<?php

namespace The7_Core\Mods\Post_Type_Builder\Handlers;

use The7_Core\Mods\Post_Type_Builder\Admin_Page;
use The7_Core\Mods\Post_Type_Builder\Models\Post_Types;
use The7_Core\Mods\Post_Type_Builder\Models\Taxonomies;
use The7_Core\Mods\Post_Type_Builder\Utility\Rewrite_Rules_Flusher;
use The7_Core\Mods\Post_Type_Builder\Utility\Utility;

defined( 'ABSPATH' ) || exit;

class Post_Types_Handler extends Handler {

	// Form fields.
	const FIELD_POST_TYPE_ARGS = 'cpt_custom_post_type';
	const FIELD_POST_TYPE_UPDATE = 'update_post_types';
	const FIELD_POST_TYPE_LABELS = 'cpt_labels';
	const FIELD_POST_TYPE_SUPPORTS = 'cpt_supports';
	const FIELD_POST_TYPE_RELATIONS = 'cpt_addon_taxes';
	const FIELD_POST_TYPE_ORIGINAL = 'cpt_original';
	const FIELD_POST_TYPE_STATUS = 'cpt_type_status';

	/**
	 * @var Post_Types_Handler
	 */
	public static $instance;

	protected function __construct() {}

	/**
	 * @return string
	 */
	public static function get_type() {
		return 'post_type';
	}

	/**
	 * @return string[]
	 */
	protected function get_supported_actions() {
		return [
			Admin_Page::ACTION_EDIT      => 'handle_update',
			Admin_Page::ACTION_NEW       => 'handle_update',
			Admin_Page::ACTION_DELETE    => 'handle_delete',
			Admin_Page::ACTION_ACTIVATE  => 'handle_activate',
			Admin_Page::ACTION_DISABLE   => 'handle_deactivate',
			Admin_Page::ACTION_QUICK_ADD => 'handle_quick_add',
		];
	}

	/**
	 * @return array
	 */
	protected function get_submitted_data() {
		$filtered_data = [];

		$items_arrays = [
			self::FIELD_POST_TYPE_ARGS,
			self::FIELD_POST_TYPE_LABELS,
			self::FIELD_POST_TYPE_SUPPORTS,
			self::FIELD_POST_TYPE_RELATIONS,
			self::FIELD_POST_TYPE_UPDATE,
		];

		foreach ( $items_arrays as $item ) {
			$filtered_data[ $item ] = filter_input( INPUT_POST, $item, FILTER_UNSAFE_RAW, FILTER_REQUIRE_ARRAY ) ?: [];
		}

		$items_string = [
			self::FIELD_POST_TYPE_ORIGINAL,
			self::FIELD_POST_TYPE_STATUS,
		];

		foreach ( $items_string as $item ) {
			$filtered_data[ $item ] = filter_input( INPUT_POST, $item, FILTER_UNSAFE_RAW ) ?: '';
		}

		return $filtered_data;
	}

	/**
	 * @param array $data
	 *
	 * @return bool
	 */
	protected function validate( $data ) {
		// They need to provide a name.
		if ( empty( $data[ self::FIELD_POST_TYPE_ARGS ]['name'] ) ) {
			the7_admin_notices()->add( 'the7_core_no_post_type_name', function() {
				echo '<p>' . esc_html__( 'Please provide a post type name', 'dt-the7-core' ) . '</p>';
			}, 'error' );

			return false;
		}

		// Check if they didn't put quotes in the name or rewrite slug.
		if ( false !== strpos( $data[ self::FIELD_POST_TYPE_ARGS ]['name'], '\'' ) ||
		     false !== strpos( $data[ self::FIELD_POST_TYPE_ARGS ]['name'], '\"' ) ||
		     false !== strpos( $data[ self::FIELD_POST_TYPE_ARGS ]['rewrite_slug'], '\'' ) ||
		     false !== strpos( $data[ self::FIELD_POST_TYPE_ARGS ]['rewrite_slug'], '\"' ) ) {

			the7_admin_notices()->add( 'the7_core_quotes_in_post_type_name', function() {
				echo '<p>' . esc_html__( 'Please do not use quotes in post type names or rewrite slugs', 'dt-the7-core' ) . '</p>';
			}, 'error' );

			return false;
		}

		return true;
	}

	/**
	 * @param string $post_type_slug
	 *
	 * @return bool
	 */
	protected function maybe_new_entry( $post_type_slug = '' ) {
		if (
			( ! empty( $_POST[ self::FIELD_POST_TYPE_STATUS ] ) && 'edit' === $_POST[ self::FIELD_POST_TYPE_STATUS ] ) &&
			! in_array( $post_type_slug, $this->reserved_post_types() ) &&
			( ! empty( $_POST[ self::FIELD_POST_TYPE_ORIGINAL ] ) && $post_type_slug === $_POST[ self::FIELD_POST_TYPE_ORIGINAL ] )
		)
		{
			return false;
		}

		return true;
	}

	/**
	 * @param string $post_type_slug
	 * @param array $post_types
	 *
	 * @return bool
	 */
	protected function slug_is_used_by_another_post_type( $post_type_slug = '', $post_types = [] ) {
		// Check if CPTUI has already registered this slug.
		if ( array_key_exists( strtolower( $post_type_slug ), $post_types ) ) {
			return true;
		}

		// Check if we're registering a reserved post type slug.
		if ( in_array( $post_type_slug, $this->reserved_post_types() ) ) {
			return true;
		}

		// Check if other plugins have registered non-public this same slug.
		$public = get_post_types( [ '_builtin' => false, 'public' => true ] );
		$private = get_post_types( [ '_builtin' => false, 'public' => false ] );
		$registered_post_types = array_merge( $public, $private );
		if ( in_array( $post_type_slug, $registered_post_types ) ) {
			return true;
		}

		// If we're this far, it's false.
		return false;
	}

	/**
	 * @param string $post_type_slug
	 *
	 * @return false|\WP_Post
	 */
	protected function slug_is_used_by_post( $post_type_slug = '' ) {
		$page = get_page_by_path( $post_type_slug );

		if ( null === $page ) {
			return false;
		}

		if ( is_object( $page ) && ( true === $page instanceof \WP_Post ) ) {
			return $page;
		}

		return false;
	}

	/**
	 * @return string[]
	 */
	protected function reserved_post_types() {
		$reserved = [
			'post',
			'page',
			'attachment',
			'revision',
			'nav_menu_item',
			'action',
			'order',
			'theme',
			'themes',
			'fields',
			'custom_css',
			'customize_changeset',
			'author',
			'post_type',
		];

		return $reserved;
	}

	/**
	 * @return void
	 */
	protected function handle_update() {
		if ( isset( $_POST[ static::POST_DELETE ] ) ) {
			$data = filter_input( INPUT_POST, self::FIELD_POST_TYPE_ARGS, FILTER_UNSAFE_RAW, FILTER_REQUIRE_ARRAY );
			if ( isset( $data['name'] ) ) {
				$this->delete_post_type( $data['name'] );
			}

			return;
		}

		if ( ! isset( $_POST[ static::POST_UPDATE ] ) ) {
			return;
		}

		$this->security_check();

		$data = $this->get_submitted_data();

		if ( ! $this->validate( $data ) ) {
			return;
		}

		$post_type_slug = $data[ self::FIELD_POST_TYPE_ARGS ]['name'];

		if ( $this->maybe_new_entry( $post_type_slug ) ) {

			if ( $this->slug_is_used_by_another_post_type( $post_type_slug, Post_Types::get() ) ) {

				the7_admin_notices()->add( 'the7_core_slug_is_used_by_another_post_type', function () use ( $post_type_slug ) {
					printf( '<p>' . esc_html__( 'Please choose a different post type name. %s is already registered.', 'dt-the7-core' ) . '</p>', $post_type_slug );
				}, 'error' );

				return;
			}

			$slug_is_used_by_post = $this->slug_is_used_by_post( $post_type_slug );
			if ( $slug_is_used_by_post ) {

				/**
				 * @var \WP_Post $slug_is_used_by_post
				 */

				the7_admin_notices()->add( 'the7_core_slug_is_used_by_post', function () use ( $slug_is_used_by_post ) {
					printf( '<p>' . esc_html__( 'Please choose a different post type name. %s matches an existing page slug, which can cause conflicts.', 'dt-the7-core' ) . '</p>', '<a href="' . esc_url( get_permalink( $slug_is_used_by_post ) ) . '">' . esc_html( $slug_is_used_by_post->post_name ) . '</a>' );
				}, 'error' );

				return;
			}
		}

		if ( Post_Types::update( $this->prepare_data_to_save( $data ) ) ) {
			$post_type = Post_Types::get_for_display_objects( $post_type_slug );
			Taxonomies::mass_attach( $post_type_slug, $post_type->get_relations() );

			Rewrite_Rules_Flusher::schedule_flush();

			// Slug was changed.
			if ( ! empty( $data[ self::FIELD_POST_TYPE_ORIGINAL ] ) && $data[ self::FIELD_POST_TYPE_ORIGINAL ] != $post_type_slug ) {

				// Maybe convert data.
				if ( ! empty( $data[ self::FIELD_POST_TYPE_UPDATE ] ) ) {
					Post_Types::convert_posts( $data[ self::FIELD_POST_TYPE_ORIGINAL ], $post_type_slug );
					Post_Types::delete( $data[ self::FIELD_POST_TYPE_ORIGINAL ] );
					Taxonomies::mass_detach( $post_type_slug );
				}
			}
		}

		wp_safe_redirect( Admin_Page::get_link() );
		exit;
	}

	/**
	 * @return void
	 */
	protected function handle_delete() {
		$id = filter_input( INPUT_GET, Admin_Page::INPUT_ID, FILTER_UNSAFE_RAW );

		if ( $id ) {
			$this->delete_post_type( $id );
		}
	}

	/**
	 * @return void
	 */
	protected function handle_quick_add() {
		$this->security_check();

		$data                                         = $this->get_submitted_data();
		$id                                           = filter_input( INPUT_GET, Admin_Page::INPUT_ID, FILTER_UNSAFE_RAW );
		$data[ self::FIELD_POST_TYPE_ARGS ]['name']   = $id;
		$data                                         = $this->prepare_data_to_save( $data );

		unset( $data['labels'], $data['supports'], $data['taxonomies'] );

		if ( Post_Types::update( $data ) ) {
			Rewrite_Rules_Flusher::schedule_flush();
		}

		wp_safe_redirect( Admin_Page::get_link() );
		exit;
	}

	/**
	 * @return void
	 */
	public function handle_activate() {
		$this->security_check();

		$id = filter_input( INPUT_GET, Admin_Page::INPUT_ID, FILTER_UNSAFE_RAW );

		$module_name = $this->get_module_name( $id );

		if ( $module_name ) {
			\The7_Admin_Dashboard_Settings::set( $module_name, true );

			wp_safe_redirect( Admin_Page::get_link() );
			exit;
		}
	}

	/**
	 * @return void
	 */
	public function handle_deactivate() {
		$this->security_check();

		$post_type_slug = filter_input( INPUT_GET, Admin_Page::INPUT_ID, FILTER_UNSAFE_RAW );

		$module_name = $this->get_module_name( $post_type_slug );

		if ( $module_name ) {
			// Cleanup database.
			$post_type = Post_Types::get_for_display_objects( $post_type_slug );
			$bundled_taxonomies = $post_type->get_prop( 'bundled_taxonomies' );
			if ( $bundled_taxonomies && is_array( $bundled_taxonomies ) ) {
				foreach ( $bundled_taxonomies as $slug ) {
					Taxonomies::delete( $slug );
				}
			}
			Taxonomies::mass_detach( $post_type_slug );
			Post_Types::delete( $post_type_slug );

			// Turn off the module.
			\The7_Admin_Dashboard_Settings::set( $module_name, false );

			wp_safe_redirect( Admin_Page::get_link() );
			exit;
		}
	}

	/**
	 * @return void
	 */
	protected function delete_post_type( $slug ) {
		$this->security_check();

		if ( Post_Types::delete( $slug ) ) {
			Taxonomies::mass_detach( $slug );
			Rewrite_Rules_Flusher::schedule_flush();
		}

		wp_safe_redirect( Admin_Page::get_link() );
		exit;
	}

	/**
	 * @param string $post_type
	 *
	 * @return string|null
	 */
	protected function get_module_name( $post_type ) {
		$modules = [
			'dt_portfolio'    => 'portfolio',
			'dt_testimonials' => 'testimonials',
			'dt_team'         => 'team',
			'dt_gallery'      => 'albums',
			'dt_slideshow'    => 'slideshow',
		];

		return isset( $modules[ $post_type ] ) ? $modules[ $post_type ] : null;
	}

	/**
	 * @param array $data
	 *
	 * @return array
	 */
	protected function prepare_data_to_save( $data ) {
		$post_type = $data[ self::FIELD_POST_TYPE_ARGS ];

		if ( ! isset( $post_type['name'] ) ) {
			return [];
		}

		$labels = array_filter( $data[ self::FIELD_POST_TYPE_LABELS ] );

		if ( isset( $labels['parent'] ) ) {
			$labels['parent_item_colon'] = $labels['parent'];
			unset( $labels['parent'] );
		}

		$labels = array_map( function( $label ) {
			return Utility::escape_label( $label );
		}, $labels );

		$result               = $post_type;
		$result['labels']     = $labels;
		$result['supports']   = $data[ self::FIELD_POST_TYPE_SUPPORTS ];
		$result['taxonomies'] = $data[ self::FIELD_POST_TYPE_RELATIONS ];

		$default_label = ucwords( str_replace( '_', ' ', $result['name'] ) );

		$label_fields = [
			'label',
			'singular_label',
		];

		foreach ( $label_fields as $field ) {
			$value = isset( $result[ $field ] ) ? $result[ $field ] : '';

			$result[ $field ] = Utility::escape_label( $value, $default_label );
		}

		if ( isset( $result['description'] ) ) {
			$result['description'] = stripslashes_from_strings_only( $result['description'] );
		}

		return $result;
	}

	/**
	 * @return Post_Types_Handler
	 */
	public static function instance() {
		if ( ! static::$instance ) {
			static::$instance = new static;
		}

		return static::$instance;
	}
}
