<?php
/**
 * Setup Elementor widgets.
 *
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor;

use Elementor\Core\DynamicTags\Dynamic_CSS;
use Elementor\Plugin;
use Elementor\Widget_Base;
use ElementorPro\Modules\GlobalWidget\Widgets\Global_Widget;
use The7\Mods\Compatibility\Elementor\Modules\Extended_Widgets\Extend_Column;
use The7\Mods\Compatibility\Elementor\Modules\Extended_Widgets\Extend_Container;
use The7\Mods\Compatibility\Elementor\Modules\Extended_Widgets\Extend_Text_Editor;
use The7\Mods\Compatibility\Elementor\Modules\Sticky_Effects\Sticky_Effects;
use The7\Mods\Compatibility\Elementor\Pro\Modules\Query_Control\The7_Query_Control_Module;
use The7\Mods\Compatibility\Elementor\Modules\Extended_Widgets\The7_Exend_Image_Widget;
use The7\Mods\Compatibility\Elementor\Modules\Extended_Widgets\The7_Extend_Widgets_Buttons;
use The7\Mods\Compatibility\Elementor\Modules\Extended_Widgets\The7_Extend_Popup;
use The7\Mods\Compatibility\Elementor\Modules\Lazy_Loading\The7_Lazy_Loading_Support;
use The7_Admin_Dashboard_Settings;
use The7_Elementor_Compatibility;

defined( 'ABSPATH' ) || exit;

/**
 * Class The7_Elementor_Widgets
 */
class The7_Elementor_Widgets {

	const ELEMENTOR_WIDGETS_PATH             = '\ElementorPro\Modules\Woocommerce\Widgets\\';
	protected $widgets_collection_before     = [];
	protected $widgets_collection_after      = [];
	protected $unregister_widgets_collection = [];

	public static function display_inline_global_styles() {
		if ( ! Plugin::$instance->preview->is_preview_mode() ) {
			return;
		}

		$global_styles = new \The7\Mods\Compatibility\Elementor\Widgets\The7_Elementor_Style_Global_Widget();
		$css           = $global_styles->generate_inline_css();
		if ( $css ) {
			printf( "<style id='the7-elementor-dynamic-inline-css' type='text/css'>\n%s\n</style>\n", $css );
		}
	}

	/**
	 * Bootstrap widgets.
	 */
	public function bootstrap() {
		add_action( 'elementor/widgets/register', [ $this, 'register_widgets_before' ], 5 );
		add_action( 'elementor/widgets/register', [ $this, 'register_widgets_after' ], 50 );
		add_action( 'elementor/init', [ $this, 'elementor_add_custom_category' ] );
		add_action( 'elementor/init', [ $this, 'load_dependencies' ] );
		add_action( 'elementor/init', [ $this, 'register_assets' ] );
		add_action( 'elementor/preview/init', [ $this, 'turn_off_lazy_loading' ] );
		add_action( 'elementor/editor/init', [ $this, 'turn_off_lazy_loading' ] );
		// Turn off lazy loading in editor for dynamicly rendered widgets.
		add_action(
			'elementor/ajax/register_actions',
			function () {
				add_action(
					'elementor/widget/before_render_content',
					function () {
						if ( Plugin::$instance->editor->is_edit_mode() ) {
							$this->turn_off_lazy_loading();
						}
					}
				);
			}
		);
		add_action( 'elementor/element/parse_css', [ $this, 'add_widget_css' ], 10, 2 );

		if ( the7_is_elementor_schemes_disabled() || the7_is_elementor_buttons_integration_enabled() ) {
			add_action( 'wp_head', [ $this, 'display_inline_global_styles' ], 1000 );
		}

		presscore_template_manager()->add_path( 'elementor', [ 'template-parts/elementor' ] );
	}

	/**
	 * @param  \Elementor\Core\Files\CSS\Post $post_css  The post CSS object.
	 * @param  \Elementor\Element_Base        $element   The element.
	 *
	 * @return void
	 */
	public function add_widget_css( $post_css, $element ) {
		if ( $post_css instanceof Dynamic_CSS ) {
			return;
		}
		$css = '';
		if ( $element instanceof Global_Widget ) {
			if ( $element->get_original_element_instance() instanceof The7_Elementor_Widget_Base ) {
				$css = $element->get_original_element_instance()->generate_inline_css();
			}
		} elseif ( $element instanceof The7_Elementor_Widget_Base ) {
			$css = $element->generate_inline_css();
		}

		if ( empty( $css ) ) {
			return;
		}

		$css = str_replace( [ "\n", "\r" ], '', $css );
		$post_css->get_stylesheet()->add_raw_css( $css );
	}

	/**
	 * Disable lazy loading with filter.
	 */
	public function turn_off_lazy_loading() {
		add_filter( 'dt_of_get_option-general-images_lazy_loading', '__return_false' );
	}

	/**
	 * Load dependencies and populate widgets collection.
	 *
	 * @throws Exception
	 */
	public function load_dependencies() {
		require_once __DIR__ . '/modules/lazy-loading/class-the7-lazy-loading-support.php';
		new The7_Lazy_Loading_Support();

		require_once __DIR__ . '/modules/extended-widgets/class-the7-extend-image-widget.php';
		new The7_Exend_Image_Widget();

		require_once __DIR__ . '/modules/extended-widgets/class-the7-extend-popup.php';
		new The7_Extend_Popup();

		require_once __DIR__ . '/modules/extended-widgets/class-the7-extend-widgets-buttons.php';
		new The7_Extend_Widgets_Buttons();

		new Sticky_Effects();
		new Extend_Column();
		new Extend_Container();
		new Extend_Text_Editor();

		require_once __DIR__ . '/pro/modules/query-control/class-the7-group-contol-query.php';
		require_once __DIR__ . '/pro/modules/query-control/class-the7-control-query.php';
		require_once __DIR__ . '/pro/modules/query-control/class-the7-posts-query.php';

		require_once __DIR__ . '/pro/modules/query-control/class-the7-query-control-module.php';
		require_once __DIR__ . '/class-the7-elementor-widget-terms-selector-mutator.php';
		require_once __DIR__ . '/trait-with-post-excerpt.php';

		require_once __DIR__ . '/style/posts-masonry-style.php';

		require_once __DIR__ . '/class-the7-elementor-widget-base.php';
		require_once __DIR__ . '/the7-elementor-less-vars-decorator-interface.php';
		require_once __DIR__ . '/class-the7-elementor-less-vars-decorator.php';

		require_once __DIR__ . '/class-the7-elementor-shortcode-widget-base.php';
		require_once __DIR__ . '/shortcode-adapters/trait-elementor-shortcode-adapter.php';
		require_once __DIR__ . '/shortcode-adapters/class-the7-shortcode-adapter-interface.php';
		require_once __DIR__ . '/shortcode-adapters/class-the7-shortcode-query-interface.php';

		require_once __DIR__ . '/shortcode-adapters/query-adapters/Products_Query.php';
		require_once __DIR__ . '/shortcode-adapters/query-adapters/Products_Current_Query.php';

		require_once __DIR__ . '/widgets/class-the7-elementor-style-global-widget.php';

		The7_Query_Control_Module::get_instance();

		$terms_selector_mutator = new The7_Elementor_Widget_Terms_Selector_Mutator();
		$terms_selector_mutator->bootstrap();

		$init_widgets = [
			'button'                 => [],
			'icon'                   => [],
			'icon-box'               => [],
			'icon-box-grid'          => [],
			'image'                  => [],
			'image-box'              => [],
			'image-box-grid'         => [],
			'posts'                  => [],
			'posts-carousel'         => [],
			'breadcrumbs'            => [],
			'photo-scroller'         => [],
			'nav-menu'               => [],
			'horizontal-menu'        => [],
			'login'                  => [],
			'text-and-icon-carousel' => [],
			'testimonials-carousel'  => [],
			'accordion'              => [],
			'simple-posts'           => [],
			'simple-posts-carousel'  => [],
			'search-form'            => [],
			'search-form-expand'     => [],
			'taxonomy-filter'        => [],
			'tabs'                   => [],
			'slider'                 => [],
			'categories-list'        => [],
			'svg-image'              => [],
			'heading'                => [],
			'taxonomies'             => [],
			'logo'                   => [],
			'ticker'                 => [],
			'image-ticker'           => [],
		];

		if ( Plugin::$instance->experiments->is_feature_active( 'container' ) ) {
			$init_widgets['banner'] = [];
		}

		$init_widgets = $this->add_modules_widgets( [ 'loop','popup' ], $init_widgets );

		if ( class_exists( 'Woocommerce' ) ) {
			$init_widgets['woocommerce/products']            = [];
			$init_widgets['woocommerce/product-sorting']     = [];
			$init_widgets['woocommerce/products-carousel']   = [];
			$init_widgets['woocommerce/products-counter']    = [];
			$init_widgets['woocommerce/login-register-form'] = [];

			$document_types = Plugin::$instance->documents->get_document_types();
			if ( array_key_exists( 'product-post', $document_types ) ) {
				$sorted_wc_widgets = [
					'woocommerce/product-add-to-cart-v2',
					'Product_Add_To_Cart',
					'woocommerce/product-tabs',
					'Product_Data_Tabs',
					'woocommerce/product-related',
					'Product_Related',
					'woocommerce/product-upsells',
					'Product_Upsell',
					'woocommerce/product-meta',
					'Product_Meta',
					'woocommerce/product-images',
					'Product_Images',
					'woocommerce/product-images-list',
					'woocommerce/product-images-slider',
					'woocommerce/product-images-vertical-slider',
					'woocommerce/product-additional-information',
					'Product_Additional_Information',
					'woocommerce/product-navigation',
					'woocommerce/product-price',
					'woocommerce/menu-cart',
					'woocommerce/product-categories',
					'simple-products',
					'simple-products-carousel',
					'woocommerce/filter-attribute',
					'woocommerce/filter-price',
					'woocommerce/filter-active',
					'woocommerce/product-reviews',
					'simple-product-categories',
					'simple-product-categories-carousel',
					'woocommerce/cart-preview',
					'woocommerce/loop-product-image',
					'woocommerce/loop-add-to-cart',
					'woocommerce/product-sale-flash',
					'woocommerce/product-out-of-stock-label',
					'woocommerce/product-rating',
					'woocommerce/brand-filter',
					'woocommerce/brand-logos',
				];
				// initialize native and the7 woocommerce widgets
				foreach ( $sorted_wc_widgets as $class_name ) {
					$class_path = self::ELEMENTOR_WIDGETS_PATH . $class_name;

					if ( class_exists( $class_path ) ) {
						$native_widget = new $class_path();
						$this->collection_add_unregister_widget( $native_widget );
						$init_widgets[ $class_name ] = [
							'position'        => 'after',
							'widget_instance' => $native_widget,
						];
						continue;
					}
					// widget from theme
					$init_widgets[ $class_name ] = [ 'position' => 'after' ];
				}
			}
		}

		// Deprecated widgets.
		if ( The7_Admin_Dashboard_Settings::get( 'deprecated_elementor_widgets' ) ) {
			if ( class_exists( 'DT_Shortcode_Products_Carousel', false ) ) {
				$init_widgets['woocommerce/old-products-carousel'] = [];
			}
			if ( class_exists( 'DT_Shortcode_ProductsMasonry', false ) ) {
				$init_widgets['woocommerce/old-products-masonry'] = [];
			}

			$init_widgets['woocommerce/old-product-add-to-cart'] = [];
		}

		// Init all widgets.
		foreach ( $init_widgets as $widget_filename => $widget_params ) {
			$widget = null;
			if ( array_key_exists( 'widget_instance', $widget_params ) ) {
				$widget = $widget_params['widget_instance'];
			} else {
				require_once __DIR__ . '/widgets/' . $widget_filename . '.php';
				$class_name = str_replace( [ 'class-', '-', '/' ], [ '', '_', '\\' ], $widget_filename );
				$class_name = __NAMESPACE__ . '\Widgets\\' . $class_name;
				$widget     = new $class_name();
			}
			$widget_position = isset( $widget_params['position'] ) ? $widget_params['position'] : 'before';
			$this->collection_add_widget( $widget, $widget_position );
		}
	}

	public function add_modules_widgets( $modules, $widgets ) {
		foreach ( $modules as $module ) {
			// Get loop module.
			$loop_module = The7_Elementor_Compatibility::instance()->modules->get_modules( $module );
			if ( $loop_module ) {
				// Register widgets from loop module.
				$widgets = array_merge( $widgets, array_fill_keys( $loop_module::WIDGETS, [] ) );
			}
		}
		return $widgets;
	}

	/**
	 * Register common widgets assets.
	 */
	public function register_assets() {
		the7_register_style(
			'the7-filter-decorations-base',
			THE7_ELEMENTOR_CSS_URI . '/the7-filter-decorations-base.css'
		);

		the7_register_style(
			'the7-carousel-widget',
			PRESSCORE_THEME_URI . '/css/compatibility/elementor/the7-carousel-widget'
		);
		the7_register_style(
			'the7-carousel-navigation',
			PRESSCORE_THEME_URI . '/css/compatibility/elementor/the7-carousel-navigation'
		);

		the7_register_style(
			'the7-carousel-text-and-icon-widget',
			PRESSCORE_THEME_URI . '/css/compatibility/elementor/the7-carousel-text-and-icon-widget'
		);
		the7_register_style(
			'the7-slider-widget',
			PRESSCORE_THEME_URI . '/css/compatibility/elementor/the7-slider', ['e-swiper']
		);
		the7_register_style(
			'the7-vertical-menu-widget',
			PRESSCORE_THEME_URI . '/css/compatibility/elementor/the7-vertical-menu-widget'
		);

		the7_register_style(
			'the7-woocommerce-product-navigation-widget',
			PRESSCORE_THEME_URI . '/css/compatibility/elementor/the7-woocommerce-product-navigation'
		);

		the7_register_style(
			'the7-woocommerce-product-price-widget',
			PRESSCORE_THEME_URI . '/css/compatibility/elementor/the7-woocommerce-product-price'
		);

		the7_register_style(
			'the7-woocommerce-product-additional-information-widget',
			PRESSCORE_THEME_URI . '/css/compatibility/elementor/the7-woocommerce-product-additional-information-widget'
		);

		the7_register_style(
			'the7-woocommerce-filter-attribute',
			PRESSCORE_THEME_URI . '/css/compatibility/elementor/the7-woocommerce-filter-attribute'
		);
		the7_register_style(
			'the7-woocommerce-filter-brand',
			PRESSCORE_THEME_URI . '/css/compatibility/elementor/the7-woocommerce-filter-brand'
		);

		the7_register_style(
			'the7-icon-box-widget',
			PRESSCORE_THEME_URI . '/css/compatibility/elementor/the7-icon-box-widget'
		);

		the7_register_style(
			'the7-icon-box-grid-widget',
			PRESSCORE_THEME_URI . '/css/compatibility/elementor/the7-icon-box-grid-widget'
		);

		wp_register_script(
			'the7-carousel-widget-preview',
			PRESSCORE_ADMIN_URI . '/assets/js/elementor/elements-carousel-widget-preview.js',
			[],
			THE7_VERSION,
			true
		);

		the7_register_style(
			'the7-accordion-widget',
			PRESSCORE_THEME_URI . '/css/compatibility/elementor/the7-accordion-widget.css'
		);

		the7_register_style(
			'the7-tabs-widget',
			PRESSCORE_THEME_URI . '/css/compatibility/elementor/the7-tabs-widget.css'
		);

		the7_register_style(
			'the7-simple-common',
			PRESSCORE_THEME_URI . '/css/compatibility/elementor/the7-simple-common.css'
		);

		the7_register_style(
			'the7-vertical-list-common',
			PRESSCORE_THEME_URI . '/css/compatibility/elementor/the7-vertical-list-common.css'
		);

		the7_register_style(
			'the7-image-box-widget',
			THE7_ELEMENTOR_CSS_URI . '/the7-image-widget.css'
		);

		the7_register_script_in_footer(
			'the7-image-box-widget',
			THE7_ELEMENTOR_JS_URI . '/the7-image-widget.js',
			[ 'the7-elementor-frontend-common' ]
		);

		the7_register_script_in_footer(
			'the7-gallery-scroller',
			PRESSCORE_THEME_URI . '/js/compatibility/elementor/gallery-scroller.js',
			[ 'the7-elementor-frontend-common', 'flexslider', 'jquery-mousewheel', 'zoom' ]
		);

		the7_register_script_in_footer(
			'the7-accordion-widget',
			PRESSCORE_ADMIN_URI . '/assets/js/elementor/the7-accordion-widget.js'
		);

		the7_register_script_in_footer(
			'the7-elementor-masonry',
			PRESSCORE_THEME_URI . '/js/compatibility/elementor/the7-masonry-widget.js',
			[ 'the7-elementor-frontend-common' ]
		);

		the7_register_script_in_footer(
			'the7-woocommerce-filter-attribute',
			PRESSCORE_THEME_URI . '/js/compatibility/elementor/woocommerce-filter-attribute.js',
			[ 'jquery' ]
		);
		the7_register_script_in_footer(
			'the7-woocommerce-filter-brand',
			PRESSCORE_THEME_URI . '/js/compatibility/elementor/woocommerce-filter-brand.js',
			[ 'jquery' ]
		);
		the7_register_script_in_footer(
			'the7-categories-handler',
			PRESSCORE_THEME_URI . '/js/compatibility/elementor/the7-product-categories.js',
			[ 'jquery' ]
		);

		the7_register_script_in_footer(
			'the7-elementor-toggle',
			PRESSCORE_THEME_URI . '/js/compatibility/elementor/the7-widget-toggle.js',
			[ 'dt-main' ]
		);

		the7_register_script_in_footer(
			'the7-slider',
			THE7_ELEMENTOR_JS_URI . '/the7-slider.js',
			[ 'the7-elementor-frontend-common']
		);

		// frontend scripts.
		the7_register_script_in_footer(
			'the7-elementor-frontend-common',
			THE7_ELEMENTOR_JS_URI . '/frontend-common.js'
		);

		// Previews.
		the7_register_script_in_footer(
			'the7-elements-carousel-widget-preview',
			PRESSCORE_ADMIN_URI . '/assets/js/elementor/elements-carousel-widget-preview.js'
		);

		the7_register_script_in_footer(
			'the7-elements-widget-preview',
			PRESSCORE_ADMIN_URI . '/assets/js/elementor/elements-widget-preview.js'
		);

		the7_register_script_in_footer(
			'the7-photo-scroller-widget-preview',
			PRESSCORE_ADMIN_URI . '/assets/js/elementor/photo-scroller-widget-preview.js',
			[ 'dt-photo-scroller' ]
		);

		the7_register_script_in_footer(
			'the7-single-product-tab-preview',
			PRESSCORE_ADMIN_URI . '/assets/js/elementor/single-product-tab.js'
		);

		the7_register_script_in_footer(
			'the7-woocommerce-product-images-widget-preview',
			PRESSCORE_ADMIN_URI . '/assets/js/elementor/wc-widget-preview.js',
			[ 'the7-gallery-scroller' ]
		);

		the7_register_script_in_footer(
			'the7-woocommerce-product-variations',
			THE7_ELEMENTOR_JS_URI . '/the7-woocommerce-list-variations.js',
			[ 'the7-elementor-frontend-common', 'elementor-frontend' ]
		);

		wp_register_script(
			'the7-woocommerce-product-review',
			PRESSCORE_ADMIN_URI . '/assets/js/elementor/the7-woocommerce-product-review.js',
			[],
			THE7_VERSION,
			true
		);

		the7_register_style( 'the7-simple-grid', PRESSCORE_THEME_URI . '/lib/simple-grid/simple-grid' );
		the7_register_script_in_footer( 'the7-simple-grid', PRESSCORE_THEME_URI . '/lib/simple-grid/simple-grid', [ 'jquery' ] );

        the7_register_style( 'the7-multipurpose-scroller', PRESSCORE_THEME_URI . '/lib/multipurpose-scroller/multipurpose-scroller' );
        the7_register_script_in_footer( 'the7-multipurpose-scroller', PRESSCORE_THEME_URI . '/lib/multipurpose-scroller/multipurpose-scroller', [ 'dt-main'  ] );
	}

	protected function collection_add_widget( $widget, $widget_position ) {
		if ( $widget_position === 'before' ) {
			$this->widgets_collection_before[ $widget->get_name() ] = $widget;
		} else {
			$this->widgets_collection_after[ $widget->get_name() ] = $widget;
		}
	}

	/**
	 * @return array
	 */
	public function get_widgets_collection() {
		return $this->widgets_collection_after + $this->widgets_collection_before;
	}

	/**
	 * Register widgets before all elementor widgets were initialized
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
	 */
	public function register_widgets_before( $widgets_manager ) {
		foreach ( $this->widgets_collection_before as $widget ) {
			$widgets_manager->register( $widget );
		}
	}

	/**
	 * Register widgets.
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
	 */
	public function register_widgets_after( $widgets_manager ) {
		foreach ( $this->unregister_widgets_collection as $widget ) {
			$widgets_manager->unregister( $widget->get_name() );
		}
		foreach ( $this->widgets_collection_after as $widget ) {
			$widgets_manager->register( $widget );
		}
	}

	/**
	 * Add 'The7 elements' category.
	 */
	public function elementor_add_custom_category() {
		Plugin::$instance->elements_manager->add_category(
			'the7-elements',
			[
				'title' => esc_html__( 'The7 elements', 'the7mk2' ),
				'icon'  => 'fa fa-header',
			]
		);
	}

	protected function collection_add_unregister_widget( $widget ) {
		$this->unregister_widgets_collection[ $widget->get_name() ] = $widget;
	}

	public static function update_control_fields( $widget, $control_id, array $args ) {
		$control_data = Plugin::instance()->controls_manager->get_control_from_stack( $widget->get_unique_name(), $control_id );
		if ( ! is_wp_error( $control_data ) ) {
			$widget->update_control( $control_id, $args );
		}
	}

	public static function update_control_group_fields( Widget_Base $widget, $group_name, $control_data ) {
		$group = Plugin::$instance->controls_manager->get_control_groups( $group_name );
		if ( ! $group ) {
			return;
		}
		$fields         = $group->get_fields();
		$control_prefix = $control_data['name'] . '_';

		foreach ( $fields as $field_id => $field ) {
			$args = [];
			if ( ! empty( $field['selectors'] ) ) {
				$args['selectors'] = self::handle_selectors( $field['selectors'], $control_data, $control_prefix );
			}
			if ( count( $args ) ) {
				self::update_control_fields( $widget, $control_prefix . $field_id, $args );
			}
		}
	}

	private static function handle_selectors( $selectors, $args, $controls_prefix ) {
		if ( isset( $args['selector'] ) ) {
			$selectors = array_combine(
				array_map(
					function ( $key ) use ( $args ) {
						return str_replace( '{{SELECTOR}}', $args['selector'], $key );
					},
					array_keys( $selectors )
				),
				$selectors
			);
		}
		if ( ! $selectors ) {
			return $selectors;
		}

		foreach ( $selectors as &$selector ) {
			$selector = preg_replace_callback(
				'/\{\{\K(.*?)(?=}})/',
				function ( $matches ) use ( $controls_prefix ) {
					return preg_replace_callback(
						'/[^ ]+(?=\.)/',
						function ( $sub_matches ) use ( $controls_prefix ) {
							return $controls_prefix . $sub_matches[0];
						},
						$matches[1]
					);
				},
				$selector
			);
		}

		return $selectors;
	}

	public static function update_responsive_control_fields( $widget, $control_id, array $args ) {
		$devices = Plugin::$instance->breakpoints->get_active_devices_list( [ 'reverse' => true ] );
		foreach ( $devices as $device_name ) {
			$control_args = $args;

			if ( ! empty( $args['prefix_class'] ) ) {
				$device_to_replace            = $widget::RESPONSIVE_DESKTOP === $device_name ? '' : '-' . $device_name;
				$control_args['prefix_class'] = sprintf( $args['prefix_class'], $device_to_replace );
			}

			$id_suffix = $widget::RESPONSIVE_DESKTOP === $device_name ? '' : '_' . $device_name;
			self::update_control_fields( $widget, $control_id . $id_suffix, $control_args );
		}
	}
}
