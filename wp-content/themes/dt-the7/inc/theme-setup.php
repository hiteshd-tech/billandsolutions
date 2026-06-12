<?php
/**
 * Theme setup.
 *
 * @since 1.0.0
 *
 * @package The7
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'presscore_load_theme_modules' ) ) :

	/**
	 * Load supported modules.
	 *
	 * @since 3.0.0
	 */
	function presscore_load_theme_modules() {
		$supported_modules = get_theme_support( 'presscore-modules' );
		if ( empty( $supported_modules[0] ) ) {
			return;
		}

		foreach ( $supported_modules[0] as $module ) {
			locate_template( "inc/mods/{$module}/{$module}.php", true );
		}
	}

	add_action( 'after_setup_theme', 'presscore_load_theme_modules', 10 );

endif;

if ( ! function_exists( 'presscore_setup' ) ) :

	/**
	 * Theme setup.
	 *
	 * @since 1.0.0
	 */
	function presscore_setup() {
		/**
		 * Load child theme text domain.
		 */
		if ( is_child_theme() ) {
			load_child_theme_textdomain( 'the7mk2', get_stylesheet_directory() . '/languages' );
		}

		/**
		 * Load theme text domain.
		 */
		load_theme_textdomain( 'the7mk2', get_template_directory() . '/languages' );

		$menus = [
			'primary' => esc_html_x( 'Primary Menu', 'backend', 'the7mk2' ),
		];

		if ( ! the7_is_elementor_theme_mode_active() ) {
			$menus['split_left']          = esc_html_x( 'Split Menu Left', 'backend', 'the7mk2' );
			$menus['split_right']         = esc_html_x( 'Split Menu Right', 'backend', 'the7mk2' );
			$menus['mobile']              = esc_html_x( 'Mobile Menu', 'backend', 'the7mk2' );
			$menus['top']                 = esc_html_x( 'Header Microwidget 1', 'backend', 'the7mk2' );
			$menus['header_microwidget2'] = esc_html_x( 'Header Microwidget 2', 'backend', 'the7mk2' );
			$menus['bottom']              = esc_html_x( 'Bottom Menu', 'backend', 'the7mk2' );
		}

		/**
		 * Register custom menu.
		 */
		register_nav_menus( $menus );

		/**
		 * Add default posts and comments RSS feed links to head.
		 */
		add_theme_support( 'automatic-feed-links' );

		/**
		 * Enable support for Post Thumbnails.
		 */
		add_theme_support( 'post-thumbnails' );

		/**
		 * Add title tag support.
		 */
		add_theme_support( 'title-tag' );

		add_theme_support( 'align-wide' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'wp-block-styles' );
		add_theme_support( 'editor-styles' );
		add_theme_support(
			'html5',
			[
				'script',
				'style',
			]
		);

		if ( ! the7_is_gutenberg_theme_mode_active() ) {
			$style_editor_suffix = ( defined( 'THE7_DEV_ENV' ) && THE7_DEV_ENV ) ? '' : '.min';
			add_editor_style( "inc/admin/assets/css/style-editor{$style_editor_suffix}.css" );

			$less_vars 										  = the7_get_new_less_vars_manager();
			list( $first_accent_color, $accent_gradient_obj ) = the7_less_get_accent_colors( $less_vars );

			// Editor color palette.
			add_theme_support(
				'editor-color-palette',
				[
					[
						'name'  => __( 'Accent', 'the7mk2' ),
						'slug'  => 'accent',
						'color' => $first_accent_color,
					],
					[
						'name'  => __( 'Dark Gray', 'the7mk2' ),
						'slug'  => 'dark-gray',
						'color' => '#111',
					],
					[
						'name'  => __( 'Light Gray', 'the7mk2' ),
						'slug'  => 'light-gray',
						'color' => '#767676',
					],
					[
						'name'  => __( 'White', 'the7mk2' ),
						'slug'  => 'white',
						'color' => '#FFF',
					],
				]
			);
		}

		/**
		 * Enable support for various theme modules.
		 */
		if ( the7_is_gutenberg_theme_mode_active() ) {
			the7_enable_gutenberg_compatible_modules();
		} else {
			presscore_enable_theme_modules();
		}

		/**
		 * Allow shortcodes in widgets.
		 */
		add_filter( 'widget_text', 'do_shortcode' );

		/**
		 * Create upload dir.
		 */
		wp_upload_dir();

		/**
		 * Register theme template parts dir.
		 */
		presscore_template_manager()->add_path( 'theme', 'template-parts' );
		presscore_template_manager()->add_path( 'the7_admin', 'inc/admin/screens' );
	}

	add_action( 'after_setup_theme', 'presscore_setup', 5 );

endif;

/**
 * Enqueue supplemental block editor styles
 *
 * TODO: Maybe move into block-theme compatibility module.
 */
function presscore_editor_frame_styles() {
	the7_register_style( 'the7-editor-frame-styles', PRESSCORE_ADMIN_URI . '/assets/css/style-editor-frame' );
	wp_enqueue_style( 'the7-editor-frame-styles' );
	presscore_enqueue_web_fonts();

	$css_cache   = presscore_get_dynamic_css_cache();
	$css_version = presscore_get_dynamic_css_version();

	$dynamic_stylesheets = presscore_get_admin_dynamic_stylesheets_list();
	foreach ( $dynamic_stylesheets as $handle => $stylesheet ) {
		$stylesheet_obj = new The7_Dynamic_Stylesheet( $handle, $stylesheet['src'] );
		$stylesheet_obj->setup_with_array( $stylesheet );
		$stylesheet_obj->set_version( $css_version );

		if ( is_array( $css_cache ) && array_key_exists( $handle, $css_cache ) ) {
			$stylesheet_obj->set_css_body( $css_cache[ $handle ] );
		}

		$stylesheet_obj->enqueue();
	}
}

/**
 * Flush rewrite rules after theme switch.
 *
 * @since 1.0.0
 */
add_action( 'after_switch_theme', 'flush_rewrite_rules' );

if ( ! function_exists( 'presscore_enable_theme_modules' ) ) :

	/**
	 * This function add support for various theme modules.
	 *
	 * @since 3.1.4
	 */
	function presscore_enable_theme_modules() {
		$always_load = [
			'compatibility',
			'theme-update',
			'tgmpa',
			'demo-content',
			'bundled-content',
			'dev-mode',
			'dev-tools',
			'remove-customizer',
		];

		$load_conditionally = [
			'portfolio',
			'mega-menu',
			'admin-icons-bar',
		];

		if ( the7_is_elementor_theme_mode_active() ) {
			add_filter(
				'the7_core_bundled_post_types_list',
				function( $post_types ) {
					return isset( $post_types['dt_portfolio'] ) ? [ 'dt_portfolio' => $post_types['dt_portfolio'] ] : $post_types;
				}
			);
		} else {
			// No use with Elementor or Gutenberg.
			$always_load[] = 'archive-ext';
			$always_load[] = 'posts-defaults';
			$always_load[] = 'options-wizard';

			$load_conditionally[] = 'albums';
			$load_conditionally[] = 'team';
			$load_conditionally[] = 'testimonials';
			$load_conditionally[] = 'slideshow';
			$load_conditionally[] = 'benefits';
			$load_conditionally[] = 'logos';
		}

		if ( the7_is_icons_manager_enabled() ) {
			$always_load[] = 'custom-fonts';
		}

		$modules_to_load = $always_load;

		// Load modules that was enabled on dashboard.
		foreach ( $load_conditionally as $module_name ) {
			if ( The7_Admin_Dashboard_Settings::get( $module_name ) ) {
				$modules_to_load[] = $module_name;
			}
		}

		/**
		 * Allow to manage theme active modules.
		 *
		 * @since 6.4.1
		 */
		$modules_to_load = apply_filters( 'the7_active_modules', $modules_to_load );

		add_theme_support( 'presscore-modules', $modules_to_load );
	}

endif;

/**
 * Enable only base modules in Gutenberg mode.
 *
 * @since 11.16.1
 */
function the7_enable_gutenberg_compatible_modules() {
	$modules_to_load = [
		'compatibility',
		'theme-update',
		'tgmpa',
		'demo-content',
		'bundled-content',
		'dev-mode',
		'dev-tools',
	];

	/**
	 * Allow to manage theme active modules.
	 *
	 * @since 6.4.1
	 */
	$modules_to_load = apply_filters( 'the7_active_modules', $modules_to_load );

	add_theme_support( 'presscore-modules', $modules_to_load );
}

if ( ! function_exists( 'presscore_widgets_init' ) ) :

	/**
	 * Register widgetized areas.
	 *
	 * @since 1.0.0
	 */
	function presscore_widgets_init() {
		if ( function_exists( 'of_get_option' ) ) {
			$w_params = array(
				'before_widget' => '<section id="%1$s" class="widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<div class="widget-title">',
				'after_title'   => '</div>',
			);

			$w_areas = apply_filters( 'presscore_widgets_init-sidebars', of_get_option( 'widgetareas' ) );

			if ( ! empty( $w_areas ) && is_array( $w_areas ) ) {
				$prefix = 'sidebar_';

				foreach ( $w_areas as $sidebar_id => $sidebar ) {
					$sidebar_args = array(
						'name'          => ( isset( $sidebar['sidebar_name'] ) ? $sidebar['sidebar_name'] : '' ),
						'id'            => $prefix . $sidebar_id,
						'description'   => ( isset( $sidebar['sidebar_desc'] ) ? $sidebar['sidebar_desc'] : '' ),
						'before_widget' => $w_params['before_widget'],
						'after_widget'  => $w_params['after_widget'],
						'before_title'  => $w_params['before_title'],
						'after_title'   => $w_params['after_title'],
					);

					$sidebar_args = apply_filters( 'presscore_widgets_init-sidebar_args', $sidebar_args, $sidebar_id, $sidebar );

					register_sidebar( $sidebar_args );
				}
			}
		}
	}

	if ( ! the7_is_gutenberg_theme_mode_active() ) {
		add_action( 'widgets_init', 'presscore_widgets_init' );
	}

endif;

if ( ! function_exists( 'presscore_post_types_author_archives' ) ) :

	/**
	 * Add custom post types to author archives.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Query $query WP_Query object.
	 */
	function presscore_post_types_author_archives( $query ) {
		/**
		 * To avoid conflicts, run this hack in frontend only.
		 */
		if ( is_admin() ) {
			return;
		}

		if ( $query->is_main_query() && $query->is_author ) {
			$new_post_types = (array) apply_filters( 'presscore_author_archive_post_types', array() );
			if ( $new_post_types ) {
				array_unshift( $new_post_types, 'post' );
				$post_type = $query->get( 'post_type' );
				if ( ! $post_type ) {
					$post_type = array();
				}
				$query->set( 'post_type', array_merge( (array) $post_type, $new_post_types ) );
			}
		}
	}

	if ( ! the7_is_gutenberg_theme_mode_active() ) {
		add_action( 'pre_get_posts', 'presscore_post_types_author_archives' );
	}

endif;

/**
 * Return The7 rest namespace.
 *
 * @since 7.8.0
 *
 * @return string
 */
function the7_get_rest_namespace() {
	return (string) apply_filters( 'the7_rest_namespace', 'the7/v1' );
}

/**
 * Initialise The7 REST API.
 *
 * @since 7.8.0
 */
function the7_rest_api_init() {
	$rest_namespace       = the7_get_rest_namespace();
	$the7_mail_controller = new The7_REST_Mail_Controller( $rest_namespace, new The7_ReCaptcha() );
	$the7_mail_controller->register_routs();
}

/**
 * Return post types with default meta boxes.
 *
 * @return array
 */
function presscore_get_pages_with_basic_meta_boxes() {
	return apply_filters( 'presscore_pages_with_basic_meta_boxes', array( 'page', 'post' ) );
}

if ( ! the7_is_gutenberg_theme_mode_active() ) {
	add_action( 'rest_api_init', 'the7_rest_api_init' );
	add_action( 'enqueue_block_editor_assets', 'presscore_editor_frame_styles' );
}

// Ensure that resizes will be deleted when image is deleted.
if (
	( ! defined( 'THE7_FEATURE_FLAG_IMAGE_RESIZE_DELETION' ) || THE7_FEATURE_FLAG_IMAGE_RESIZE_DELETION )
	&&
	( is_admin() || defined( 'WP_CLI' ) )
	&&
	! the7_is_gutenberg_theme_mode_active()
) {
	add_action(
		'init',
		[ The7_Aq_Resize::class, 'setup_resizes_deleteion' ],
		20
	);
}

\The7\Mods\Compatibility\Gutenberg\Block_Theme\The7_Block_Theme_Compatibility::instance();
