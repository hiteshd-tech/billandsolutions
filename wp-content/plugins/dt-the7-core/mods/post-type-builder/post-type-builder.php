<?php

namespace The7_Core\Mods\Post_Type_Builder;

use The7_Core\Mods\Post_Type_Builder\Models\Post_Types;
use The7_Core\Mods\Post_Type_Builder\Models\Taxonomies;
use The7_Core\Mods\Post_Type_Builder\Handlers\Post_Types_Handler;
use The7_Core\Mods\Post_Type_Builder\Handlers\Taxonomies_Handler;
use The7_Core\Mods\Post_Type_Builder\Utility\Rewrite_Rules_Flusher;
use The7_Core\Mods\Post_Type_Builder\Utility\Utility;

defined( 'ABSPATH' ) || exit;

class Post_Type_Builder {

	/**
	 * @return void
	 */
	public function __construct() {
		require_once plugin_dir_path( __FILE__ ) . 'admin-page.php';

		require_once plugin_dir_path( __FILE__ ) . 'screens/edit-post-type.php';
		require_once plugin_dir_path( __FILE__ ) . 'screens/edit-taxonomy.php';
		require_once plugin_dir_path( __FILE__ ) . 'screens/items-list.php';

		require_once plugin_dir_path( __FILE__ ) . 'bundled/bundled-item.php';
		require_once plugin_dir_path( __FILE__ ) . 'bundled/portfolio-post-type.php';
		require_once plugin_dir_path( __FILE__ ) . 'bundled/portfolio-category-taxonomy.php';
		require_once plugin_dir_path( __FILE__ ) . 'bundled/portfolio-tags-taxonomy.php';
		require_once plugin_dir_path( __FILE__ ) . 'bundled/albums-post-type.php';
		require_once plugin_dir_path( __FILE__ ) . 'bundled/albums-category-taxonomy.php';
		require_once plugin_dir_path( __FILE__ ) . 'bundled/testimonials-post-type.php';
		require_once plugin_dir_path( __FILE__ ) . 'bundled/testimonials-category-taxonomy.php';
		require_once plugin_dir_path( __FILE__ ) . 'bundled/team-post-type.php';
		require_once plugin_dir_path( __FILE__ ) . 'bundled/team-category-taxonomy.php';
		require_once plugin_dir_path( __FILE__ ) . 'bundled/slideshow-post-type.php';

		require_once plugin_dir_path( __FILE__ ) . 'models/composition-model.php';
		require_once plugin_dir_path( __FILE__ ) . 'models/post-types.php';
		require_once plugin_dir_path( __FILE__ ) . 'models/taxonomies.php';

		require_once plugin_dir_path( __FILE__ ) . 'models/items/item-model.php';
		require_once plugin_dir_path( __FILE__ ) . 'models/items/bundled-item-model.php';
		require_once plugin_dir_path( __FILE__ ) . 'models/items/post-type.php';
		require_once plugin_dir_path( __FILE__ ) . 'models/items/taxonomy.php';
		require_once plugin_dir_path( __FILE__ ) . 'models/items/bundled-post-type.php';
		require_once plugin_dir_path( __FILE__ ) . 'models/items/bundled-taxonomy.php';

		require_once plugin_dir_path( __FILE__ ) . 'handlers/handler.php';
		require_once plugin_dir_path( __FILE__ ) . 'handlers/post-types-handler.php';
		require_once plugin_dir_path( __FILE__ ) . 'handlers/taxonomies-handler.php';

		require_once plugin_dir_path( __FILE__ ) . 'utility/utility.php';
		require_once plugin_dir_path( __FILE__ ) . 'utility/rewrite-rules-flusher.php';
		require_once plugin_dir_path( __FILE__ ) . 'utility/ui.php';

		Admin_Page::instance()->setup();
		Post_Types_Handler::instance()->setup();
		Taxonomies_Handler::instance()->setup();
		Rewrite_Rules_Flusher::setup();

		add_action( 'init', [ $this, 'create_custom_post_types' ], 10 );
		add_action( 'init', [ $this, 'create_custom_taxonomies' ], 9 );
	}

	/**
	 * Register custom post types.
	 *
	 * @return void
	 */
	public function create_custom_post_types() {
		$cpts = Post_Types::get_for_display_objects();

		if ( empty( $cpts ) || ! is_array( $cpts ) ) {
			return;
		}

		foreach ( $cpts as $post_type ) {
			if ( $post_type->get_prop( 'disabled' ) ) {
				continue;
			}

			$this->register_single_post_type( $post_type->get_raw() );
		}
	}

	/**
	 * Helper function to register the actual post_type.
	 *
	 * @since 1.0.0
	 *
	 * @internal
	 *
	 * @param array $post_type Post type array to register. Optional.
	 * @return null Result of register_post_type.
	 */
	function register_single_post_type( $post_type = [] ) {

		/**
		 * Filters the map_meta_cap value.
		 *
		 * @since 1.0.0
		 *
		 * @param bool   $value     True.
		 * @param string $name      Post type name being registered.
		 * @param array  $post_type All parameters for post type registration.
		 */
		$post_type['map_meta_cap'] = apply_filters( 'cptui_map_meta_cap', true, $post_type['name'], $post_type );

		if ( empty( $post_type['supports'] ) ) {
			$post_type['supports'] = [];
		}

		/**
		 * Filters custom supports parameters for 3rd party plugins.
		 *
		 * @since 1.0.0
		 *
		 * @param array  $value     Empty array to add supports keys to.
		 * @param string $name      Post type slug being registered.
		 * @param array  $post_type Array of post type arguments to be registered.
		 */
		$user_supports_params = apply_filters( 'cptui_user_supports_params', [], $post_type['name'], $post_type );

		if ( is_array( $user_supports_params ) && ! empty( $user_supports_params ) ) {
			if ( is_array( $post_type['supports'] ) ) {
				$post_type['supports'] = array_merge( $post_type['supports'], $user_supports_params );
			} else {
				$post_type['supports'] = [ $user_supports_params ];
			}
		}

		$yarpp = false; // Prevent notices.
		if ( ! empty( $post_type['custom_supports'] ) ) {
			$custom = explode( ',', $post_type['custom_supports'] );
			foreach ( $custom as $part ) {
				// We'll handle YARPP separately.
				if ( in_array( $part, [ 'YARPP', 'yarpp' ], true ) ) {
					$yarpp = true;
					continue;
				}
				$post_type['supports'][] = trim( $part );
			}
		}

		if ( isset( $post_type['supports'] ) && is_array( $post_type['supports'] ) && in_array( 'none', $post_type['supports'], true ) ) {
			$post_type['supports'] = false;
		}

		$labels = [
			'name'          => $post_type['label'],
			'singular_name' => $post_type['singular_label'],
		];

		$preserved        = $this->get_preserved_keys( 'post_types' );
		$preserved_labels = $this->get_preserved_labels();
		foreach ( $post_type['labels'] as $key => $label ) {

			if ( ! empty( $label ) ) {
				if ( 'parent' === $key ) {
					$labels['parent_item_colon'] = $label;
				} else {
					$labels[ $key ] = $label;
				}
			} elseif ( empty( $label ) && in_array( $key, $preserved, true ) ) {
				$singular_or_plural = ( in_array( $key, array_keys( $preserved_labels['post_types']['plural'] ) ) ) ? 'plural' : 'singular';
				$label_plurality    = ( 'plural' === $singular_or_plural ) ? $post_type['label'] : $post_type['singular_label'];
				$labels[ $key ]     = sprintf( $preserved_labels['post_types'][ $singular_or_plural ][ $key ], $label_plurality );
			}
		}

		$has_archive = isset( $post_type['has_archive'] ) ?  Utility::get_disp_boolean( $post_type['has_archive'] ) : false;
		if ( $has_archive && ! empty( $post_type['has_archive_string'] ) ) {
			$has_archive = $post_type['has_archive_string'];
		}

		$show_in_menu =  Utility::get_disp_boolean( $post_type['show_in_menu'] );
		if ( ! empty( $post_type['show_in_menu_string'] ) ) {
			$show_in_menu = $post_type['show_in_menu_string'];
		}

		$rewrite =  Utility::get_disp_boolean( $post_type['rewrite'] );
		if ( false !== $rewrite ) {
			// Core converts to an empty array anyway, so safe to leave this instead of passing in boolean true.
			$rewrite         = [];
			$rewrite['slug'] = ! empty( $post_type['rewrite_slug'] ) ? $post_type['rewrite_slug'] : $post_type['name'];

			$rewrite['with_front'] = true; // Default value.
			if ( isset( $post_type['rewrite_withfront'] ) ) {
				$rewrite['with_front'] = 'false' === Utility::disp_boolean( $post_type['rewrite_withfront'] ) ? false : true;
			}
		}

		$menu_icon = ! empty( $post_type['menu_icon'] ) ? $post_type['menu_icon'] : null;

		if ( in_array( $post_type['query_var'], [ 'true', 'false', '0', '1' ], true ) ) {
			$post_type['query_var'] =  Utility::get_disp_boolean( $post_type['query_var'] );
		}
		if ( ! empty( $post_type['query_var_slug'] ) ) {
			$post_type['query_var'] = $post_type['query_var_slug'];
		}

		$menu_position = null;
		if ( ! empty( $post_type['menu_position'] ) ) {
			$menu_position = (int) $post_type['menu_position'];
		}

		$delete_with_user = null;
		if ( ! empty( $post_type['delete_with_user'] ) ) {
			$delete_with_user =  Utility::get_disp_boolean( $post_type['delete_with_user'] );
		}

		$capability_type = 'post';
		if ( ! empty( $post_type['capability_type'] ) ) {
			$capability_type = $post_type['capability_type'];
			if ( false !== strpos( $post_type['capability_type'], ',' ) ) {
				$caps = array_map( 'trim', explode( ',', $post_type['capability_type'] ) );
				if ( count( $caps ) > 2 ) {
					$caps = array_slice( $caps, 0, 2 );
				}
				$capability_type = $caps;
			}
		}

		$public =  Utility::get_disp_boolean( $post_type['public'] );
		if ( ! empty( $post_type['exclude_from_search'] ) ) {
			$exclude_from_search =  Utility::get_disp_boolean( $post_type['exclude_from_search'] );
		} else {
			$exclude_from_search = false === $public;
		}

		$queryable = isset( $post_type['publicly_queryable'] ) ?  Utility::get_disp_boolean( $post_type['publicly_queryable'] ) : $public;

		if ( empty( $post_type['show_in_nav_menus'] ) ) {
			// Defaults to value of public.
			$post_type['show_in_nav_menus'] = $public;
		}

		if ( empty( $post_type['show_in_rest'] ) ) {
			$post_type['show_in_rest'] = false;
		}

		$rest_base = null;
		if ( ! empty( $post_type['rest_base'] ) ) {
			$rest_base = $post_type['rest_base'];
		}

		$rest_controller_class = null;
		if ( ! empty( $post_type['rest_controller_class'] ) ) {
			$rest_controller_class = $post_type['rest_controller_class'];
		}

		$args = [
			'labels'                => $labels,
			'description'           => $post_type['description'],
			'public'                =>  Utility::get_disp_boolean( $post_type['public'] ),
			'publicly_queryable'    => $queryable,
			'show_ui'               =>  Utility::get_disp_boolean( $post_type['show_ui'] ),
			'show_in_nav_menus'     =>  Utility::get_disp_boolean( $post_type['show_in_nav_menus'] ),
			'has_archive'           => $has_archive,
			'show_in_menu'          => $show_in_menu,
			'delete_with_user'      => $delete_with_user,
			'show_in_rest'          =>  Utility::get_disp_boolean( $post_type['show_in_rest'] ),
			'rest_base'             => $rest_base,
			'rest_controller_class' => $rest_controller_class,
			'exclude_from_search'   => $exclude_from_search,
			'capability_type'       => $capability_type,
			'map_meta_cap'          => $post_type['map_meta_cap'],
			'hierarchical'          =>  Utility::get_disp_boolean( $post_type['hierarchical'] ),
			'rewrite'               => $rewrite,
			'menu_position'         => $menu_position,
			'menu_icon'             => $menu_icon,
			'query_var'             => $post_type['query_var'],
			'supports'              => $post_type['supports'],
			'taxonomies'            => $post_type['taxonomies'],
		];

		if ( true === $yarpp ) {
			$args['yarpp_support'] = $yarpp;
		}

		// Show thumbnail column.
		if ( ! empty( $post_type['show_thumbnail_admin_column'] ) && function_exists( 'presscore_admin_add_thumbnail_column' ) ) {
			add_filter( 'manage_edit-' . $post_type['name'] . '_columns', 'presscore_admin_add_thumbnail_column' );
		}

		/**
		 * Filters the arguments used for a post type right before registering.
		 *
		 * @param array  $args      Array of arguments to use for registering post type.
		 * @param string $value     Post type slug to be registered.
		 */
		$args = apply_filters( 'the7_core_pre_register_post_type', $args, $post_type['name'] );

		return register_post_type( $post_type['name'], $args );
	}

	/**
	 * Register our users' custom taxonomies.
	 *
	 * @return void
	 */
	function create_custom_taxonomies() {
		$taxes = Taxonomies::get_for_display();

		if ( empty( $taxes ) || ! is_array( $taxes ) ) {
			return;
		}

		foreach ( $taxes as $tax ) {
			if ( ! empty( $tax['disabled'] ) ) {
				continue;
			}

			$this->register_single_taxonomy( $tax );
		}
	}

	/**
	 * Helper function to register the actual taxonomy.
	 *
	 * @since 1.0.0
	 *
	 * @internal
	 *
	 * @param array $taxonomy Taxonomy array to register. Optional.
	 * @return null Result of register_taxonomy.
	 */
	function register_single_taxonomy( $taxonomy = [] ) {

		$labels = [
			'name'          => $taxonomy['label'],
			'singular_name' => $taxonomy['singular_label'],
		];

		$description = '';
		if ( ! empty( $taxonomy['description'] ) ) {
			$description = $taxonomy['description'];
		}

		$preserved        = $this->get_preserved_keys( 'taxonomies' );
		$preserved_labels = $this->get_preserved_labels();
		foreach ( $taxonomy['labels'] as $key => $label ) {

			if ( ! empty( $label ) ) {
				$labels[ $key ] = $label;
			} elseif ( empty( $label ) && in_array( $key, $preserved, true ) ) {
				$singular_or_plural = ( in_array( $key, array_keys( $preserved_labels['taxonomies']['plural'] ) ) ) ? 'plural' : 'singular';
				$label_plurality    = ( 'plural' === $singular_or_plural ) ? $taxonomy['label'] : $taxonomy['singular_label'];
				$labels[ $key ]     = sprintf( $preserved_labels['taxonomies'][ $singular_or_plural ][ $key ], $label_plurality );
			}
		}

		$rewrite =  Utility::get_disp_boolean( $taxonomy['rewrite'] );
		if ( false !==  Utility::get_disp_boolean( $taxonomy['rewrite'] ) ) {
			$rewrite               = [];
			$rewrite['slug']       = ! empty( $taxonomy['rewrite_slug'] ) ? $taxonomy['rewrite_slug'] : $taxonomy['name'];
			$rewrite['with_front'] = true;
			if ( isset( $taxonomy['rewrite_withfront'] ) ) {
				$rewrite['with_front'] = ( 'false' === Utility::disp_boolean( $taxonomy['rewrite_withfront'] ) ) ? false : true;
			}
			$rewrite['hierarchical'] = false;
			if ( isset( $taxonomy['rewrite_hierarchical'] ) ) {
				$rewrite['hierarchical'] = ( 'true' === Utility::disp_boolean( $taxonomy['rewrite_hierarchical'] ) ) ? true : false;
			}
		}

		if ( in_array( $taxonomy['query_var'], [ 'true', 'false', '0', '1' ], true ) ) {
			$taxonomy['query_var'] =  Utility::get_disp_boolean( $taxonomy['query_var'] );
		}
		if ( true === $taxonomy['query_var'] && ! empty( $taxonomy['query_var_slug'] ) ) {
			$taxonomy['query_var'] = $taxonomy['query_var_slug'];
		}

		$public             = ( ! empty( $taxonomy['public'] ) && false ===  Utility::get_disp_boolean( $taxonomy['public'] ) ) ? false : true;
		$publicly_queryable = isset( $taxonomy['publicly_queryable'] ) ? Utility::get_disp_boolean( $taxonomy['publicly_queryable'] ) : $public;

		$show_admin_column = ( ! empty( $taxonomy['show_admin_column'] ) && false !==  Utility::get_disp_boolean( $taxonomy['show_admin_column'] ) ) ? true : false;

		$show_in_menu = ( ! empty( $taxonomy['show_in_menu'] ) && false !==  Utility::get_disp_boolean( $taxonomy['show_in_menu'] ) ) ? true : false;

		if ( empty( $taxonomy['show_in_menu'] ) ) {
			$show_in_menu =  Utility::get_disp_boolean( $taxonomy['show_ui'] );
		}

		$show_in_nav_menus = ( ! empty( $taxonomy['show_in_nav_menus'] ) && false !==  Utility::get_disp_boolean( $taxonomy['show_in_nav_menus'] ) ) ? true : false;
		if ( empty( $taxonomy['show_in_nav_menus'] ) ) {
			$show_in_nav_menus = $public;
		}

		$show_tagcloud = ( ! empty( $taxonomy['show_tagcloud'] ) && false !==  Utility::get_disp_boolean( $taxonomy['show_tagcloud'] ) ) ? true : false;
		if ( empty( $taxonomy['show_tagcloud'] ) ) {
			$show_tagcloud =  Utility::get_disp_boolean( $taxonomy['show_ui'] );
		}

		$show_in_rest = ( ! empty( $taxonomy['show_in_rest'] ) && false !==  Utility::get_disp_boolean( $taxonomy['show_in_rest'] ) ) ? true : false;

		$show_in_quick_edit = ( ! empty( $taxonomy['show_in_quick_edit'] ) && false !==  Utility::get_disp_boolean( $taxonomy['show_in_quick_edit'] ) ) ? true : false;

		$rest_base = null;
		if ( ! empty( $taxonomy['rest_base'] ) ) {
			$rest_base = $taxonomy['rest_base'];
		}

		$rest_controller_class = null;
		if ( ! empty( $taxonomy['rest_controller_class'] ) ) {
			$rest_controller_class = $taxonomy['rest_controller_class'];
		}

		$meta_box_cb = null;
		if ( ! empty( $taxonomy['meta_box_cb'] ) ) {
			$meta_box_cb = ( false !==  Utility::get_disp_boolean( $taxonomy['meta_box_cb'] ) ) ? $taxonomy['meta_box_cb'] : false;
		}
		$default_term = null;
		if ( ! empty( $taxonomy['default_term'] ) ) {
			$term_parts = explode(',', $taxonomy['default_term'] );
			if ( ! empty( $term_parts[0] ) ) {
				$default_term['name'] = trim( $term_parts[0] );
			}
			if ( ! empty( $term_parts[1] ) ) {
				$default_term['slug'] = trim( $term_parts[1] );
			}
			if ( ! empty( $term_parts[2] ) ) {
				$default_term['description'] = trim( $term_parts[2] );
			}
		}

		$args = [
			'labels'                => $labels,
			'label'                 => $taxonomy['label'],
			'description'           => $description,
			'public'                => $public,
			'publicly_queryable'    => $publicly_queryable,
			'hierarchical'          =>  Utility::get_disp_boolean( $taxonomy['hierarchical'] ),
			'show_ui'               =>  Utility::get_disp_boolean( $taxonomy['show_ui'] ),
			'show_in_menu'          => $show_in_menu,
			'show_in_nav_menus'     => $show_in_nav_menus,
			'show_tagcloud'         => $show_tagcloud,
			'query_var'             => $taxonomy['query_var'],
			'rewrite'               => $rewrite,
			'show_admin_column'     => $show_admin_column,
			'show_in_rest'          => $show_in_rest,
			'rest_base'             => $rest_base,
			'rest_controller_class' => $rest_controller_class,
			'show_in_quick_edit'    => $show_in_quick_edit,
			'meta_box_cb'           => $meta_box_cb,
			'default_term'          => $default_term,
		];

		$object_type = ! empty( $taxonomy['object_types'] ) ? $taxonomy['object_types'] : '';

		/**
		 * Filters the arguments used for a taxonomy right before registering.
		 *
		 * @since 1.0.0
		 * @since 1.3.0 Added original passed in values array
		 * @since 1.6.0 Added $obect_type variable to passed parameters.
		 *
		 * @param array  $args        Array of arguments to use for registering taxonomy.
		 * @param string $value       Taxonomy slug to be registered.
		 * @param array  $taxonomy    Original passed in values for taxonomy.
		 * @param array  $object_type Array of chosen post types for the taxonomy.
		 */
		$args = apply_filters( 'cptui_pre_register_taxonomy', $args, $taxonomy['name'], $taxonomy, $object_type );

		return register_taxonomy( $taxonomy['name'], $object_type, $args );
	}

	/**
	 * Return array of keys needing preserved.
	 *
	 * @since 1.0.5
	 *
	 * @param string $type Type to return. Either 'post_types' or 'taxonomies'. Optional. Default empty string.
	 * @return array Array of keys needing preservered for the requested type.
	 */
	function get_preserved_keys( $type = '' ) {

		$preserved_labels = [
			'post_types' => [
				'add_new_item',
				'edit_item',
				'new_item',
				'view_item',
				'view_items',
				'all_items',
				'search_items',
				'not_found',
				'not_found_in_trash',
			],
			'taxonomies' => [
				'search_items',
				'popular_items',
				'all_items',
				'parent_item',
				'parent_item_colon',
				'edit_item',
				'update_item',
				'add_new_item',
				'new_item_name',
				'separate_items_with_commas',
				'add_or_remove_items',
				'choose_from_most_used',
			],
		];
		return ! empty( $type ) ? $preserved_labels[ $type ] : [];
	}

	/**
	 * Returns an array of translated labels, ready for use with sprintf().
	 *
	 * Replacement for cptui_get_preserved_label for the sake of performance.
	 *
	 * @since 1.6.0
	 *
	 * @return array
	 */
	function get_preserved_labels() {
		return [
			'post_types' => [
				'singular' => [
					'add_new_item' => __( 'Add new %s', 'dt-the7-core' ),
					'edit_item'    => __( 'Edit %s', 'dt-the7-core' ),
					'new_item'     => __( 'New %s', 'dt-the7-core' ),
					'view_item'    => __( 'View %s', 'dt-the7-core' ),
				],
				'plural' => [
					'view_items'         => __( 'View %s', 'dt-the7-core' ),
					'all_items'          => __( 'All %s', 'dt-the7-core' ),
					'search_items'       => __( 'Search %s', 'dt-the7-core' ),
					'not_found'          => __( 'No %s found.', 'dt-the7-core' ),
					'not_found_in_trash' => __( 'No %s found in trash.', 'dt-the7-core' ),
				],
			],
			'taxonomies' => [
				'singular' => [
					'parent_item'       => __( 'Parent %s', 'dt-the7-core' ),
					'parent_item_colon' => __( 'Parent %s:', 'dt-the7-core' ),
					'edit_item'         => __( 'Edit %s', 'dt-the7-core' ),
					'update_item'       => __( 'Update %s', 'dt-the7-core' ),
					'add_new_item'      => __( 'Add new %s', 'dt-the7-core' ),
					'new_item_name'     => __( 'New %s name', 'dt-the7-core' ),
				],
				'plural' => [
					'search_items'               => __( 'Search %s', 'dt-the7-core' ),
					'popular_items'              => __( 'Popular %s', 'dt-the7-core' ),
					'all_items'                  => __( 'All %s', 'dt-the7-core' ),
					'separate_items_with_commas' => __( 'Separate %s with commas', 'dt-the7-core' ),
					'add_or_remove_items'        => __( 'Add or remove %s', 'dt-the7-core' ),
					'choose_from_most_used'      => __( 'Choose from the most used %s', 'dt-the7-core' ),
				],
			],
		];
	}
}

new Post_Type_Builder();
