<?php

namespace The7_Core\Mods\Post_Type_Builder;

use The7_Core\Mods\Post_Type_Builder\Handlers\Post_Types_Handler;
use The7_Core\Mods\Post_Type_Builder\Handlers\Taxonomies_Handler;
use The7_Core\Mods\Post_Type_Builder\Models\Taxonomies;
use The7_Core\Mods\Post_Type_Builder\Models\Post_Types;
use The7_Core\Mods\Post_Type_Builder\Screens\Edit_Post_Type;
use The7_Core\Mods\Post_Type_Builder\Screens\Edit_Taxonomy;
use The7_Core\Mods\Post_Type_Builder\Screens\Items_List;

defined( 'ABSPATH' ) || exit;

class Admin_Page {

	const MENU_SLUG = 'the7-post-type-builder';

	const ACTION_EDIT = 'the7_pt_edit';
	const ACTION_NEW = 'the7_pt_new';
	const ACTION_DELETE = 'the7_pt_delete';
	const ACTION_DISABLE = 'the7_pt_disable';
	const ACTION_ACTIVATE = 'the7_pt_activate';
	const ACTION_QUICK_ADD = 'the7_pt_quick_add';

	// Query vars.
	const INPUT_ACTION = 'action';
	const INPUT_TYPE = 'type';
	const INPUT_ID = 'id';

	/**
	 * @var Admin_Page
	 */
	public static $instance;

	protected function __construct() {
	}

	public function render() {
		$action = filter_input( INPUT_GET, self::INPUT_ACTION, FILTER_UNSAFE_RAW );
		$type = filter_input( INPUT_GET, self::INPUT_TYPE, FILTER_UNSAFE_RAW );
		$id = filter_input( INPUT_GET, self::INPUT_ID, FILTER_UNSAFE_RAW );

		switch ( $action ) {
			case self::ACTION_EDIT:
				if ( $type === Post_Types_Handler::get_type() ) {
					Edit_Post_Type::render( Post_Types::get_for_display( $id ) );
				} elseif ( $type === Taxonomies_Handler::get_type() ) {
					Edit_Taxonomy::render( Taxonomies::get_for_display( $id ) );
				}
				break;
			case self::ACTION_NEW:
				if ( $type === Post_Types_Handler::get_type() ) {
					Edit_Post_Type::render();
				} elseif ( $type === Taxonomies_Handler::get_type() ) {
					Edit_Taxonomy::render();
				}
				break;
			default:
				Items_List::render();
		}
	}

	public function setup() {
		add_action( 'admin_menu', [ $this, 'register_menu_item' ], 20 );
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );
	}

	public function register_menu_item() {
		add_submenu_page(
			'the7-dashboard',
			__( 'Post Type Builder', 'dt-the7-core' ),
			__( 'Post Type Builder', 'dt-the7-core' ),
			'switch_themes',
			self::MENU_SLUG,
			[ $this, 'render' ]
		);
	}

	/**
	 * @return void
	 */
	function admin_enqueue_scripts() {
		if ( wp_doing_ajax() ) {
			return;
		}

		$current_screen = get_current_screen();

		if ( ! is_object( $current_screen ) || $current_screen->base !== 'the7_page_' . self::MENU_SLUG ) {
			return;
		}

		wp_enqueue_media();

		$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_enqueue_script(
			'the7-core-post-types-builder',
			The7PT()->assets_url( "js/post-types-builder{$min}.js" ),
			[ 'jquery', 'jquery-ui-dialog', 'postbox' ],
			The7PT()->version(),
			true
		);
		wp_enqueue_script(
			'the7-core-dashicons-picker',
			The7PT()->assets_url( "js/dashicons-picker{$min}.js" ),
			[ 'jquery'],
			'1.0.0',
			true
		);
		wp_enqueue_style(
			'the7-core-post-types-builder',
			The7PT()->assets_url( "css/post-types-builder{$min}.css" ),
			[ 'wp-jquery-ui-dialog' ],
			The7PT()->version()
		);

		$core                  = get_post_types( [ '_builtin' => true ] );
		$public                = get_post_types( [ '_builtin' => false, 'public' => true ] );
		$private               = get_post_types( [ '_builtin' => false, 'public' => false ] );
		$registered_post_types = array_merge( $core, $public, $private );

		wp_localize_script( 'the7-core-post-types-builder', 'the7_type_data',
			[
				'confirm'             => esc_html__( 'Are you sure you want to delete this? Deleting will NOT remove created content.', 'dt-the7-core' ),
				'existing_post_types' => $registered_post_types,
			]
		);

		$core                  = get_taxonomies( [ '_builtin' => true ] );
		$public                = get_taxonomies( [ '_builtin' => false, 'public'   => true ] );
		$private               = get_taxonomies( [ '_builtin' => false, 'public'   => false ] );
		$registered_taxonomies = array_merge( $core, $public, $private );
		wp_localize_script( 'the7-core-post-types-builder', 'the7_tax_data',
			[
				'confirm'             => esc_html__( 'Are you sure you want to delete this? Deleting will NOT remove created content.', 'dt-the7-core' ),
				'no_associated_type'  => esc_html__( 'Please select a post type to associate with.', 'dt-the7-core' ),
				'existing_taxonomies' => $registered_taxonomies,
			]
		);
	}

	public static function get_link( $action = '', $args = [] ) {
		$link = 'admin.php?page=' . self::MENU_SLUG;
		if ( $action ) {
			$link .= "&action={$action}";
		}

		return admin_url( add_query_arg( $args, $link ) );
	}

	public static function get_post_type_link( $action = '', $id = null ) {
		$args = [ self::INPUT_TYPE => Post_Types_Handler::get_type() ];
		if ( $id ) {
			$args[ self::INPUT_ID ] = $id;
		}

		return self::get_link( $action, $args );
	}

	public static function get_taxonomy_link( $action = '', $id = null ) {
		$args = [ self::INPUT_TYPE => Taxonomies_Handler::get_type() ];
		if ( $id ) {
			$args[ self::INPUT_ID ] = $id;
		}

		return self::get_link( $action, $args );
	}

	public static function instance() {
		if ( ! static::$instance ) {
			static::$instance = new static;
		}

		return static::$instance;
	}
}
