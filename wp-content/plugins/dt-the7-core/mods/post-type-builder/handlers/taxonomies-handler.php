<?php

namespace The7_Core\Mods\Post_Type_Builder\Handlers;

use The7_Core\Mods\Post_Type_Builder\Admin_Page;
use The7_Core\Mods\Post_Type_Builder\Models\Post_Types;
use The7_Core\Mods\Post_Type_Builder\Models\Taxonomies;
use The7_Core\Mods\Post_Type_Builder\Utility\Rewrite_Rules_Flusher;
use The7_Core\Mods\Post_Type_Builder\Utility\Utility;

defined( 'ABSPATH' ) || exit;

class Taxonomies_Handler extends Handler {

	const FIELD_TAX_ARGS = 'cpt_custom_tax';
	const FIELD_TAX_LABELS = 'cpt_tax_labels';
	const FIELD_TAX_RELATIONS = 'cpt_post_types';
	const FIELD_TAX_UPDATE = 'update_taxonomy';
	const FIELD_TAX_ORIGINAL = 'tax_original';
	const FIELD_TAX_STATUS = 'cpt_tax_status';

	/**
	 * @var Taxonomies_Handler
	 */
	public static $instance;

	protected function __construct() {}

	/**
	 * @return string
	 */
	public static function get_type() {
		return 'taxonomy';
	}

	protected function get_supported_actions() {
		return [
			Admin_Page::ACTION_EDIT      => 'handle_update',
			Admin_Page::ACTION_NEW       => 'handle_update',
			Admin_Page::ACTION_DELETE    => 'handle_delete',
			Admin_Page::ACTION_QUICK_ADD => 'handle_quick_add',
		];
	}

	public function validate( $data ) {
		// They need to provide a name.
		if ( empty( $data[ self::FIELD_TAX_ARGS ]['name'] ) ) {
			the7_admin_notices()->add( 'the7_core_no_taxonomy_name', function() {
				echo '<p>' . esc_html__( 'Please provide a taxonomy name', 'dt-the7-core' ) . '</p>';
			}, 'error' );

			return false;
		}

		if ( false !== strpos( $data[ self::FIELD_TAX_ARGS ]['name'], '\'' ) ||
		     false !== strpos( $data[ self::FIELD_TAX_ARGS ]['name'], '\"' ) ||
		     false !== strpos( $data[ self::FIELD_TAX_ARGS ]['rewrite_slug'], '\'' ) ||
		     false !== strpos( $data[ self::FIELD_TAX_ARGS ]['rewrite_slug'], '\"' ) ) {

			the7_admin_notices()->add( 'the7_core_quotes_in_taxonomy_name', function() {
				echo '<p>' . esc_html__( 'Please do not use quotes in taxonomy names or rewrite slugs', 'dt-the7-core' ) . '</p>';
			}, 'error' );

			return false;
		}

		return true;
	}

	public function taxonomy_is_already_registered( $taxonomy_slug ) {
		// Already registered.
		if ( Taxonomies::get( $taxonomy_slug ) ) {
			return true;
		}

		// Check if we're registering a reserved post type slug.
		if ( $this->is_reserved( $taxonomy_slug ) ) {
			return true;
		}

		// Check if other plugins have registered this same slug.
		$public  = get_taxonomies( [ '_builtin' => false, 'public' => true ] );
		$private = get_taxonomies( [ '_builtin' => false, 'public' => false ] );
		$registered_taxonomies = array_merge( $public, $private );
		if ( in_array( $taxonomy_slug, $registered_taxonomies ) ) {
			return true;
		}

		return false;
	}

	public function maybe_new_entry( $taxonomy_slug ) {
		if (
			( ! empty( $_POST[ self::FIELD_TAX_STATUS ] ) && 'edit' === $_POST[ self::FIELD_TAX_STATUS ] ) &&
			! $this->is_reserved( $taxonomy_slug ) &&
			( ! empty( $_POST[ self::FIELD_TAX_ORIGINAL ] ) && $taxonomy_slug === $_POST[ self::FIELD_TAX_ORIGINAL ] )
		) {
			return false;
		}

		return true;
	}

	public function is_reserved( $slug ) {
		$reserved = [
			'action',
			'attachment',
			'attachment_id',
			'author',
			'author_name',
			'calendar',
			'cat',
			'category',
			'category__and',
			'category__in',
			'category__not_in',
			'category_name',
			'comments_per_page',
			'comments_popup',
			'customize_messenger_channel',
			'customized',
			'cpage',
			'day',
			'date',
			'debug',
			'error',
			'exact',
			'feed',
			'fields',
			'hour',
			'include',
			'link_category',
			'm',
			'minute',
			'monthnum',
			'more',
			'name',
			'nav_menu',
			'nonce',
			'nopaging',
			'offset',
			'order',
			'orderby',
			'p',
			'page',
			'page_id',
			'paged',
			'pagename',
			'pb',
			'perm',
			'post',
			'post__in',
			'post__not_in',
			'post_format',
			'post_mime_type',
			'post_status',
			'post_tag',
			'post_type',
			'posts',
			'posts_per_archive_page',
			'posts_per_page',
			'preview',
			'robots',
			's',
			'search',
			'second',
			'sentence',
			'showposts',
			'static',
			'subpost',
			'subpost_id',
			'tag',
			'tag__and',
			'tag__in',
			'tag__not_in',
			'tag_id',
			'tag_slug__and',
			'tag_slug__in',
			'taxonomy',
			'tb',
			'term',
			'theme',
			'type',
			'types',
			'w',
			'withcomments',
			'withoutcomments',
			'year',
			'output',
		];

		return in_array( $slug, $reserved, true );
	}

	public function get_submitted_data() {
		$filtered_data = [];

		$input_array = [
			self::FIELD_TAX_ARGS,
			self::FIELD_TAX_LABELS,
			self::FIELD_TAX_RELATIONS,
			self::FIELD_TAX_UPDATE,
		];
		foreach ( $input_array as $item ) {
			$filtered_data[ $item ] = filter_input( INPUT_POST, $item, FILTER_UNSAFE_RAW, FILTER_REQUIRE_ARRAY ) ?: [];
		}

		$input_string = [
			self::FIELD_TAX_ORIGINAL,
			self::FIELD_TAX_STATUS,
		];
		foreach ( $input_string as $item ) {
			$filtered_data[ $item ] = filter_input( INPUT_POST, $item, FILTER_UNSAFE_RAW ) ?: '';
		}

		return $filtered_data;
	}

	/**
	 * @return void
	 */
	protected function handle_update() {
		if ( isset( $_POST[ static::POST_DELETE ] ) ) {
			$data = filter_input( INPUT_POST, self::FIELD_TAX_ARGS, FILTER_UNSAFE_RAW, FILTER_REQUIRE_ARRAY );
			if ( isset( $data['name'] ) ) {
				$this->delete_taxonomy( $data['name'] );
			}

			return;
		}

		if ( ! isset( $_POST[ self::POST_UPDATE ] ) ) {
			return;
		}

		$this->security_check();

		$data = $this->get_submitted_data();

		if ( ! $this->validate( $data ) ) {
			return;
		}

		$taxonomy_slug = $data[ self::FIELD_TAX_ARGS ]['name'];

		if ( $this->maybe_new_entry( $taxonomy_slug ) ) {

			if ( $this->taxonomy_is_already_registered( $taxonomy_slug ) ) {

				the7_admin_notices()->add( 'the7_core_slug_is_used_by_another_taxonomy', function() use ( $taxonomy_slug ) {
					printf(
						'<p>' . esc_html__( 'Please choose a different taxonomy name. %s is already registered.', 'dt-the7-core' ) . '</p>',
						$taxonomy_slug
					);
				}, 'error' );

				return;
			}
		}

		if ( Taxonomies::update( $this->prepare_data_to_save( $data ) ) ) {
			$taxonomy = Taxonomies::get_for_display_objects( $taxonomy_slug );
			Post_Types::mass_attach( $taxonomy_slug, $taxonomy->get_relations() );

			Rewrite_Rules_Flusher::schedule_flush();

			// Slug was changed.
			if ( ! empty( $data[ self::FIELD_TAX_ORIGINAL ] ) && $data[ self::FIELD_TAX_ORIGINAL ] !== $taxonomy_slug ) {

				// Maybe convert.
				if ( ! empty( $data[ self::FIELD_TAX_UPDATE ] ) ) {
					Taxonomies::convert( $data[ self::FIELD_TAX_ORIGINAL ], $taxonomy_slug );
					Taxonomies::delete( $data[ self::FIELD_TAX_ORIGINAL ] );
					Post_Types::mass_detach( $taxonomy_slug );
				}
			}
		}

		wp_safe_redirect( Admin_Page::get_link() );
		exit;
	}

	/**
	 * @return void
	 */
	protected function handle_quick_add() {
		$this->security_check();

		$data                                 = $this->get_submitted_data();
		$id                                   = filter_input( INPUT_GET, Admin_Page::INPUT_ID, FILTER_UNSAFE_RAW );
		$data[ self::FIELD_TAX_ARGS ]['name'] = $id;
		$data                                 = $this->prepare_data_to_save( $data );

		unset( $data['labels'], $data['object_types'] );

		if ( Taxonomies::update( $data ) ) {
			Rewrite_Rules_Flusher::schedule_flush();
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
			$this->delete_taxonomy( $id );
		}
	}

	/**
	 * @return void
	 */
	protected function delete_taxonomy( $slug ) {
		$this->security_check();

		if ( Taxonomies::delete( $slug ) ) {
			Post_Types::mass_detach( $slug );
			Rewrite_Rules_Flusher::schedule_flush();
		}

		wp_safe_redirect( Admin_Page::get_link() );
		exit;
	}

	/**
	 * @param array $data
	 *
	 * @return array
	 */
	protected function prepare_data_to_save( $data ) {
		$item = $data[ static::FIELD_TAX_ARGS ];

		if ( ! isset( $item['name'] ) ) {
			return [];
		}

		$labels = array_filter( $data[ self::FIELD_TAX_LABELS ] );

		$labels = array_map( function( $label ) {
			return Utility::escape_label( $label );
		}, $labels );

		$result                 = $item;
		$result['labels']       = $labels;
		$result['object_types'] = $data[ self::FIELD_TAX_RELATIONS ];

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
	 * @return Taxonomies_Handler
	 */
	public static function instance() {
		if ( ! static::$instance ) {
			static::$instance = new static;
		}

		return static::$instance;
	}
}
