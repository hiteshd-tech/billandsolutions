<?php
/**
 * The7 Elementor plugin compatibility class.
 *
 * @since   7.7.0
 * @package The7
 */

use Elementor\Core\Settings\Manager as Settings_Manager;
use Elementor\Plugin as Elementor;
use ElementorPro\Modules\LoopBuilder\Documents\Loop as LoopDocument;
use ElementorPro\Modules\ThemeBuilder\Documents\Theme_Document;
use ElementorPro\Modules\ThemeBuilder\Module as ThemeBuilderModule;
use The7\Mods\Compatibility\Elementor\Pro\Modules\Dynamic_Tags\The7\Module as DynamicTagsModule;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Modules;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Page_Settings;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Template_Manager;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widgets;
use The7\Mods\Compatibility\Elementor\The7_Kit_Manager_Control;
use The7\Mods\Compatibility\Elementor\The7_Schemes_Manager_Control;
use Elementor\Core\Frontend\Performance;

defined( 'ABSPATH' ) || exit;

/**
 * Class The7_Elementor_Compatibility
 */
class The7_Elementor_Compatibility {

	const MINIMAL_ELEMENTOR_VERSION = '3.25.0';

	/**
	 * Instance.
	 * Holds the plugin instance.
	 *
	 * @since  1.0.0
	 * @access public
	 * @static
	 * @var The7_Elementor_Compatibility
	 */
	public static $instance = null;

	public $page_settings;
	public $widgets;
	public $edit_mode_backup;

	/**
	 * Modules manager.
	 * Holds the plugin modules manager.
	 *
	 * @access public
	 * @var The7_Elementor_Modules
	 */
	public $modules;
	public $template_manager;
	public $theme_builder_adapter;
	public $kit_manager_control;
	public $scheme_manager_control;

	/**
	 * Instance.
	 * Ensures only one instance of the plugin class is loaded or can be loaded.
	 *
	 * @return The7_Elementor_Compatibility An instance of the class.
	 * @since  1.0.0
	 * @access public
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
			self::$instance->bootstrap();
		}

		return self::$instance;
	}

	/**
	 * Bootstrap module.
	 */
	public function bootstrap() {
		require_once __DIR__ . '/elementor-functions.php';
		require_once __DIR__ . '/class-the7-elementor-widgets.php';
		require_once __DIR__ . '/class-the7-elementor-page-settings.php';
		require_once __DIR__ . '/meta-adapters/class-the7-elementor-color-meta-adapter.php';
		require_once __DIR__ . '/meta-adapters/class-the7-elementor-padding-meta-adapter.php';
		require_once __DIR__ . '/class-the7-elementor-kit-manager-control.php';
		require_once __DIR__ . '/class-the7-elementor-schemes-manager-control.php';
		require_once __DIR__ . '/class-the7-elementor-template-manager.php';

		// Should be on top because of The7_Elementor_Widgets::load_dependencies().
		add_action( 'elementor/init', [ $this, 'on_elementor_init' ] );

		/**
		 * Filter wp_calculate_image_srcset_meta is used to remove Elementor custom image sizes from image meta.
		 *
		 * Do not edit filter hook name definition. It's on purpose!
		 */
		add_filter( 'wp_calculate' . '_image_srcset_meta', [ $this, 'remove_elementor_image_srcset_meta_filter' ] );

		$this->page_settings = new The7_Elementor_Page_Settings();
		$this->page_settings->bootstrap();

		$icons_integration_module = new \The7\Mods\Compatibility\Elementor\The7_Icons_For_Elementor();
		if ( the7_is_icons_manager_enabled() ) {
			$icons_integration_module->use_the7_icons_in_elementor();
		} else {
			// Theme Options Should be disabled by now.
			$icons_integration_module->use_elementor_icons_in_mega_menu();
		}

		$this->widgets = new The7_Elementor_Widgets();
		$this->widgets->bootstrap();

		$this->template_manager = new The7_Elementor_Template_Manager();
		$this->template_manager->bootstrap();

		if ( ! defined( 'THE7_DISABLE_KIT_MANAGER' ) || ( defined( 'THE7_DISABLE_KIT_MANAGER' ) && ! THE7_DISABLE_KIT_MANAGER ) ) {
			$this->kit_manager_control = new The7_Kit_Manager_Control();
			$this->kit_manager_control->bootstrap();
		}

		if ( the7_is_elementor2() ) {
			$this->scheme_manager_control = new The7_Schemes_Manager_Control();
			$this->scheme_manager_control->bootstrap();
		}
		$this->modules = new The7_Elementor_Modules();

		if ( the7_elementor_pro_is_active() ) {
			$this->bootstrap_pro();
		}
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_elementor_global_style_css' ], 30 );
		add_action( 'elementor/theme/before_do_popup', [ $this, 'enqueue_elementor_popup' ] );
		add_filter( 'presscore_localized_script', [ $this, 'extract_elementor_settings_to_js' ] );
		add_action(
			'elementor/editor/before_enqueue_styles',
			function () {
				wp_enqueue_style(
					'the7-broccoli-icons',
					PRESSCORE_ADMIN_URI . '/assets/fonts/the7-broccoli-editor-fonts-v1.0/style.min.css',
					[],
					THE7_VERSION
				);
			}
		);

		/**
		 * Fixes a glitch with svg placeholders in image widget.
		 * Kses removes unsupported protocols.
		 *
		 * @see Group_Control_Image_Size::print_attachment_image_html
		 */
		add_filter( 'kses_allowed_protocols', 'the7_add_data_to_kses_allowed_protocols' );
		wp_allowed_protocols(); // call function before wp_loaded to initialize allowed protocols

		add_action(
			'elementor/experiments/default-features-registered',
			[
				$this,
				'adjust_default_experiments',
			]
		);

		// Enqueue the7 common editor js when elementor editor is loaded.
		add_action(
			'elementor/preview/enqueue_scripts',
			function () {
				the7_register_script( 'the7-elementor-editor-common', THE7_ELEMENTOR_ADMIN_JS_URI . '/editor-common.js' );
				wp_enqueue_script( 'the7-elementor-editor-common' );
			}
		);

		add_filter(
			'body_class',
			function ( $classes ) {
				if ( \The7_Admin_Dashboard_Settings::get( 'elementor-the7-typography-fix' ) ) {
					$classes[] = 'the7-elementor-typography';
				}

				return $classes;
			}
		);

		add_filter( 'pre_handle_404', [ $this, 'allow_posts_widget_pagination' ], 10, 2 );

		// Outdated Elementor version notice is now registered via the7_add_admin_notices() in inc/admin/admin-notices.php

		// Rename 'Unknown' option to 'The7 Elements', on 'Element Manager' page.
		add_action(
			'admin_print_scripts-elementor_page_elementor-element-manager',
			static function () {
				$output = <<<JS
document.addEventListener('DOMContentLoaded', function() {
	const replacementText = 'The7 Elements';
	let optionChanged = false;
	let tableColumnChanged = false;
    let maxAttempts = 15;
    let interval = setInterval(function() {
        if (maxAttempts <= 0) {
            clearInterval(interval);
            return;
        }
        maxAttempts--;
        
        // Rename option.
        const optionToChange = document.querySelector('#inspector-select-control-0 option[value="Unknown"]');
        if (optionToChange && !optionChanged) {
            optionToChange.text = replacementText;
            optionChanged = true;
        }
        
        // Rename column text.
        const columnItemsToChange = document.querySelectorAll('.wp-list-table tr td:nth-child(4)');
        if (columnItemsToChange && !tableColumnChanged) {
        	// Loop through elements.
        	columnItemsToChange.forEach(function(item) {
				if (item.innerText === 'Unknown') {
					item.innerText = replacementText;
					tableColumnChanged = true;
				}
			});
		}
        
        if (optionChanged && tableColumnChanged) {
			clearInterval(interval);
		}
    }, 300);
});
JS;
				// Actual output.
				if ( function_exists( 'wp_print_inline_script_tag' ) ) {
					wp_print_inline_script_tag( $output );
				}
			},
			99
		);
	}

	public function on_elementor_init() {
		require_once __DIR__ . '/pro/modules/dynamic-tags/the7/module.php';
		new DynamicTagsModule();
		
		$this->modules->bootstrap();
	}

	/**
	 * Remove Elementor custom image sizes from image meta. Sometimes there are no images for these sizes.
	 *
	 * @param array $image_meta Image meta data.
	 *
	 * @return array
	 */
	public function remove_elementor_image_srcset_meta_filter( $image_meta ) {
		if ( isset( $image_meta['sizes'] ) && is_array( $image_meta['sizes'] ) ) {
			$image_meta['sizes'] = array_filter(
				$image_meta['sizes'],
				function ( $key ) {
					return strpos( $key, 'elementor_custom_' ) !== 0;
				},
				ARRAY_FILTER_USE_KEY
			);
		}

		return $image_meta;
	}

	/**
	 * @param array $dt_local
	 *
	 * @return array
	 */
	public function extract_elementor_settings_to_js( $dt_local ) {
		$dt_local['elementor'] = [
			'settings' => [
				'container_width' => (int) the7_elementor_get_content_width_string(),
			],
		];

		return $dt_local;
	}

	/**
	 * Adjust default Elementor Experiments.
	 *
	 * @sine 10.4.3
	 *
	 * @param \Elementor\Core\Experiments\Manager $experiments
	 */
	public function adjust_default_experiments( $experiments ) {
		// Turn off Additional Custom Breakpoints by default.
		$experiments->set_feature_default_state( 'additional_custom_breakpoints', false );

		// Turn off Improved CSS Loading by default.
		$experiments->set_feature_default_state( 'e_optimized_css_loading', false );

		$experiments->set_feature_default_state( 'e_css_smooth_scroll', $experiments::STATE_INACTIVE );
	}

	/**
	 * @return array|mixed|null
	 */
	public static function get_elementor_settings( $key = null ) {
		// TODO: Remove after elementor 3.4.0
		if ( the7_is_elementor2() ) {
			return Settings_Manager::get_settings_managers( 'general' )->get_model()->get_settings( 'elementor_' . $key );
		}

		return Elementor::$instance->kits_manager->get_current_settings( $key );
	}

	protected function bootstrap_pro() {
		require_once __DIR__ . '/pro/class-the7-elementor-theme-builder-adapter.php';

		$this->theme_builder_adapter = new \The7\Mods\Compatibility\Elementor\Pro\The7_Elementor_Theme_Builder_Adapter();
		$this->theme_builder_adapter->bootstrap();
		if ( the7_is_woocommerce_enabled() ) {
			require_once __DIR__ . '/pro/modules/woocommerce/class-the7-woocommerce-support.php';
			new \The7\Mods\Compatibility\Elementor\Pro\Modules\Woocommerce\Woocommerce_Support();
		}
	}

	public static function get_applied_archive_page_id( $page_id = null ) {
		$document = false;
		$location = '';
		if ( is_singular() ) {
			$document = self::get_frontend_document();
		}
		if ( $document && $document instanceof Theme_Document ) {
			// For editor preview iframe.
			$location = $document->get_location();
		} elseif ( function_exists( 'is_shop' ) && is_shop() ) {
			$location = 'archive';
		} elseif ( is_archive() || is_tax() || is_home() || is_search() ) {
			$location = 'archive';
		} elseif ( is_singular() || is_404() ) {
			$location = 'single';
		}
		if ( ! empty( $location ) ) {
			return self::get_document_id_for_location( $location, $page_id );
		}

		return $page_id;
	}

	public static function get_frontend_document() {
		return Elementor::$instance->documents->get_doc_for_frontend( get_the_ID() );
	}


	/**
	 * @param string $location
	 * @param null   $page_id
	 *
	 * @return int|null
	 */
	public static function get_document_id_for_location( $location, $page_id = null ) {
		$document = self::get_document_applied_for_location( $location );
		if ( $document ) {
			$page_id = $document->get_post()->ID;
		}

		return $page_id;
	}

	/**
	 * @return \Elementor\Core\Base\Document|false
	 */
	public static function get_document_applied_for_location( $location ) {
		$document = null;
		if ( the7_elementor_pro_is_active() ) {
			$documents = ThemeBuilderModule::instance()->get_conditions_manager()->get_documents_for_location( $location );
			foreach ( $documents as $document ) {
				if ( is_preview() || Elementor::$instance->preview->is_preview_mode() ) {
					$document = Elementor::$instance->documents->get_doc_or_auto_save( $document->get_id(), get_current_user_id() );
				} else {
					$document = Elementor::$instance->documents->get( $document->get_id() );
				}
				break;
			}
		}

		return $document;
	}

	/**
	 * Retrieve builder content for display.
	 * Used to render and return the post content with all the Elementor elements.
	 *
	 * @param int  $post_id  Post ID.
	 * @param bool $with_css Optional. Whether to include CSS files. Default is false.
	 *
	 * @return string The post content.
	 * @see \Elementor\Frontend::get_builder_content_for_display()
	 */
	public static function get_builder_content_for_display( $post_id, $with_css = false ) {
		$is_edit_mode = self::instance()->is_edit_mode();

		$with_css = $with_css ? true : $is_edit_mode;

		if ( $with_css ) {
			add_filter( 'elementor/frontend/builder_content/before_print_css', '__return_true', 999 );
		}

		$perf_exist = class_exists( 'Elementor\Core\Frontend\Performance', true );

		if ( $perf_exist ) {
			$_use_style_controls = Performance::is_use_style_controls();
		}
		$content = Elementor::instance()->frontend->get_builder_content_for_display( $post_id, $with_css );
		if ( $perf_exist ) {
			Performance::set_use_style_controls( $_use_style_controls );
		}
		if ( $with_css ) {
			remove_filter( 'elementor/frontend/builder_content/before_print_css', '__return_true', 999 );
		}

		return $content;
	}

	public static function is_assets_loader_exist() {
		return (bool) Elementor::$instance->assets_loader;
	}

	public static function enqueue_elementor_popup() {
		wp_enqueue_style( 'the7-custom-scrollbar' );
		wp_enqueue_script( 'the7-custom-scrollbar' );
	}

	public static function enqueue_elementor_global_style_css() {
		the7_register_style(
			'the7-elementor-global',
			PRESSCORE_THEME_URI . '/css/compatibility/elementor/elementor-global'
		);

		wp_enqueue_style( 'the7-elementor-global' );

		if ( self::get_document_applied_for_location( 'popup' ) ) {
			self::enqueue_elementor_popup();
		}
	}

	public function is_edit_mode() {
		if ( $this->edit_mode_backup ) {
			return true;
		}
		$edit_mode = Elementor::$instance->editor->is_edit_mode();

		return $edit_mode;
	}

	public function backup_edit_mode() {
		$this->edit_mode_backup = Elementor::$instance->editor->is_edit_mode();
	}

	public function restore_edit_mode() {
		$this->edit_mode_backup = false;
	}

	/**
	 * Print document content and backup edit mode for nested templates
	 *
	 * @param \ElementorPro\Modules\LoopBuilder\Documents\Loop $document Elementor document class.
	 */
	public function print_loop_document( $document ) {

		// Bail if document is not an instance of LoopDocument.
		if ( ! $document instanceof LoopDocument ) {
			return;
		}
		$this->backup_edit_mode();
		$document->print_content();
		$this->restore_edit_mode();
	}

	/**
	 * Fix WP 5.5 pagination issue.
	 * Return true to mark that it's handled and avoid WP to set it as 404.
	 *
	 * @see https://core.trac.wordpress.org/ticket/50976
	 * Based on the logic at \WP::handle_404.
	 *
	 * @param $handled - Default false.
	 * @param $wp_query
	 *
	 * @return bool
	 */
	public function allow_posts_widget_pagination( $handled, $wp_query ) {
		// Check it's not already handled and it's a single paged query.
		if ( $handled || empty( $wp_query->query_vars['page'] ) || ! is_singular() || empty( $wp_query->post ) ) {
			return $handled;
		}

		$document = Elementor::$instance->documents->get( $wp_query->post->ID );

		return $this->is_valid_pagination( $document->get_elements_data(), $wp_query->query_vars['page'] );
	}

	public function is_valid_pagination( array $elements, $current_page ) {
		$is_valid = false;

		Elementor::$instance->db->iterate_data(
			$elements,
			$this->check_pagination_handler( $current_page, $is_valid )
		);

		return $is_valid;
	}

	/**
	 * @return void
	 */
	public function check_pagination_handler( $current_page, &$is_valid ) {
		return function ( $element ) use ( &$is_valid, $current_page ) {
			if ( $is_valid || ! $this->is_valid_post_widget( $element ) ) {
				return;
			}

			$is_valid = $this->should_allow_pagination( $element, $current_page );
		};
	}

	/**
	 * @return bool
	 */
	private function is_valid_post_widget( $element ) {
		$prefix = 'the7';

		return isset( $element['widgetType'] ) && substr( $element['widgetType'], 0, strlen( $prefix ) ) === $prefix;
	}


	/**
	 * @return bool
	 */
	private function should_allow_pagination( $element ) {
		$post_type = 'post_type';

		if ( ! empty( $element['settings']['template_type'] ) && $element['settings']['template_type'] === 'products' ) {
			$post_type = 'query_post_type';
		}

		if ( ! empty( $element['settings'][ $post_type ] ) && $element['settings'][ $post_type ] === 'current_query' ) {
			return true;
		}

		return ! empty( $element['settings']['loading_mode'] ) && $element['settings']['loading_mode'] === 'standard';
	}
}
