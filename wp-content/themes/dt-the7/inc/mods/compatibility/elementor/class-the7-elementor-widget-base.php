<?php
/**
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor;

use Elementor\Icons_Manager;
use Elementor\Plugin;
use Elementor\TemplateLibrary\Source_Local;
use Elementor\Widget_Base;
use ElementorPro\Modules\Popup\Module as PopupModule;
use The7_Less_Compiler;
use Elementor\Core\Breakpoints\Manager as Breakpoints_Manager;

defined( 'ABSPATH' ) || exit;

abstract class The7_Elementor_Widget_Base extends Widget_Base {

	const WIDGET_CSS_CACHE_ID = '_the7_elementor_widgets_css';

	/**
	 * Widget templates.
	 *
	 * @var array
	 */
	protected $widget_templates = [];

	/**
	 * @var bool
	 */
	protected $has_img_preload_me_filter = false;

	protected $less_manager;

	/**
	 * @var array
	 */
	protected $generated_conditions_cache = [];

	/**
	 * @var array
	 */
	protected static $registered_assets = [];

	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );

		$this->widget_templates = $this->register_widget_templates();

		$this->register_assets();
		if ( $data && ! in_array( $this->get_name(), static::$registered_assets, true ) ) {
			// Enqueue after dt-main (15) to maintain maximum compatibility.
			$widget = $this;
			add_action(
				'wp_enqueue_scripts',
				function () use ( $widget ) {
					$widget->enqueue_styles();
				},
				40
			);
			static::$registered_assets[] = $this->get_name();
		}
	}

	/**
	 * Get button sizes.
	 *
	 * Retrieve an array of button sizes for the button widget.
	 *
	 * @since 9.15.0
	 * @access public
	 * @static
	 *
	 * @return array An array containing button sizes.
	 */
	public static function get_button_sizes() {
		return [
			'xs' => __( 'Extra Small', 'the7mk2' ),
			'sm' => __( 'Small', 'the7mk2' ),
			'md' => __( 'Medium', 'the7mk2' ),
			'lg' => __( 'Large', 'the7mk2' ),
			'xl' => __( 'Extra Large', 'the7mk2' ),
		];
	}

	protected function the7_keywords() {
		return [];
	}

	protected function the7_icon() {
		return '';
	}

	protected function the7_title() {
		return '';
	}

	/**
	 * Get the7 widget categories.
	 *
	 * @return string[]
	 */
	protected function the7_categories() {
		return [];
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the widget keywords.
	 *
	 * @since 1.0.10
	 * @access public
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return array_merge( $this->the7_keywords(), [ 'the7' ] );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'the7-widget ' . $this->the7_icon();
	}

	/**
	 * Get element title.
	 *
	 * Retrieve the element title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Element title.
	 */
	public function get_title() {
		return $this->the7_title();
	}

	/**
	 * Get widget category.
	 *
	 * @return array
	 */
	public function get_categories() {
		return array_merge( [ 'the7-elements', 'pro-elements' ], $this->the7_categories() );
	}

	/**
	 * Register widget templates.
	 *
	 * @return array
	 */
	public function register_widget_templates() {
		return [];
	}

	/**
	 * Get all devices args.
	 *
	 * @param array $args Args.
	 *
	 * @return array
	 */
	public function generate_device_args( $args ) {
		$device_args = [];
		foreach ( Breakpoints_Manager::get_default_config() as $breakpoint_name => $breakpoint_config ) {
			$device_args[ $breakpoint_name ] = $args;
		}

		return $device_args;
	}

	/**
	 * Generate control conditions based on active breakpoints.
	 *
	 * @param string $name     Control name.
	 * @param string $operator Compare operator.
	 * @param mixed  $value    Value to compare to.
	 * @param string $relation Condition relation. Default is 'or'.
	 *
	 * @return array
	 */
	public function generate_conditions( string $name, string $operator, $value, string $relation = 'or' ): array {
		$hash = $name . '__' . $operator . '__' . $value . '__' . $relation;

		if ( isset( $this->generated_conditions_cache[ $hash ] ) ) {
			return $this->generated_conditions_cache[ $hash ];
		}

		$terms = [
			compact( 'name', 'operator', 'value' ),
		];

		$active_breakpoints = Plugin::$instance->breakpoints->get_active_breakpoints();
		foreach ( $active_breakpoints as $breakpoint_name => $breakpoint_config ) {
			$r_name  = $name . '_' . $breakpoint_name;
			$terms[] = [
				'name'     => $r_name,
				'operator' => $operator,
				'value'    => $value,
			];
		}

		$result = [
			'relation' => $relation,
			'terms'    => $terms,
		];

		$this->generated_conditions_cache[ $hash ] = $result;

		return $result;
	}

	/**
	 * Return unique shortcode class like {$unique_class_base}-{$sc_id}.
	 *
	 * @return string
	 */
	public function get_unique_class() {
		return $this->get_name() . '-' . $this->get_id();
	}

	protected function print_inline_css() {
		if ( Plugin::$instance->editor->is_edit_mode() ) {
			add_filter( 'dt_of_get_option-general-images_lazy_loading', '__return_false' );
			echo '<style type="text/css">';
			echo $this->generate_inline_css();
			echo '</style>';
		}
	}

	/**
	 * @return false|string
	 * @throws \Exception
	 */
	public function generate_inline_css() {
		$less_file = $this->get_less_file_name();

		if ( ! $less_file ) {
			return '';
		}

		$lessc = new The7_Less_Compiler( (array) $this->get_less_vars(), (array) $this->get_less_import_dir() );

		return $lessc->compile_file( $less_file, $this->get_less_imports() );
	}

		/**
		 * Return less import dir.
		 *
		 * @return array
		 */
	protected function get_less_import_dir() {
		return [ PRESSCORE_THEME_DIR . '/css/dynamic-less/elementor' ];
	}

	/**
	 * @return array
	 */
	protected function get_less_vars() {
		$this->less_manager = new The7_Elementor_Less_Vars_Decorator( the7_get_new_shortcode_less_vars_manager() );

		// Register common less vars.
		if ( function_exists( 'the7_get_deprecated_elementor_breakpoints' ) ) {
			foreach ( the7_get_deprecated_elementor_breakpoints() as $size => $value ) {
				$this->less_manager->add_pixel_number( "elementor-{$size}-breakpoint", $value );
			}
		}

		$this->less_manager->add_pixel_number( 'wide-desktop-width', the7_elementor_get_content_width_string() );
		$this->less_manager->add_keyword( 'widget-class', $this->get_unique_class(), '~"%s"' );

		// Register custom less vars.
		$this->less_vars( $this->less_manager );

		return $this->less_manager->get_vars();
	}

	protected function less_vars( The7_Elementor_Less_Vars_Decorator_Interface $less_vars ) {
		// Do nothing.
	}

	/**
	 * @return array
	 */
	protected function get_less_imports() {
		return [];
	}

	/**
	 * @return bool|string
	 */
	protected function get_less_file_name() {
		return false;
	}

	/**
	 * @param $dim
	 *
	 * @return string
	 */
	protected function combine_dimensions( $dim ) {
		$units = $dim['unit'];

		return "{$dim['top']}{$units} {$dim['right']}{$units} {$dim['bottom']}{$units} {$dim['left']}{$units}";
	}

	/**
	 * @param array $val
	 * @param int   $default
	 *
	 * @return int|string
	 */
	protected function combine_slider_value( $val, $default = 0 ) {
		if ( empty( $val['size'] ) || ! isset( $val['unit'] ) ) {
			return $default;
		}

		return $val['size'] . $val['unit'];
	}

	/**
	 * @return false|int
	 */
	protected function get_current_post_id() {
		// Elementor Pro >= 2.9.1
		if ( class_exists( 'ElementorPro\Core\Utils' ) ) {
			return \ElementorPro\Core\Utils::get_current_post_id();
		}

		// Elementor Pro < 2.9.1
		if ( class_exists( 'ElementorPro\Classes\Utils' ) ) {
			return \ElementorPro\Classes\Utils::get_current_post_id();
		}

		return get_the_ID();
	}

	protected function add_image_hooks() {
		$this->has_img_preload_me_filter && add_filter(
			'dt_get_thumb_img-args',
			'presscore_add_preload_me_class_to_images',
			15
		);
	}

	protected function remove_image_hooks() {
		$this->has_img_preload_me_filter = has_filter(
			'dt_get_thumb_img-args',
			'presscore_add_preload_me_class_to_images'
		);
		remove_filter( 'dt_get_thumb_img-args', 'presscore_add_preload_me_class_to_images' );
	}

	/**
	 * @return bool
	 */
	protected function is_preview_mode() {
		return Plugin::$instance->preview->is_preview_mode();
	}

	/**
	 * Proxy. Used to determine whether we are in the edit mode.
	 *
	 * @see Editor::is_edit_mode()
	 *
	 * @return bool
	 */
	protected function is_edit_mode() {
		return Plugin::$instance->editor->is_edit_mode();
	}

	/**
	 * Return widget template object.
	 *
	 * @template T
	 * @param class-string<T> $name Template name.
	 * @param string|null     $variation Template variation aka prefix base.
	 *
	 * @return T
	 */
	public function template( $name, $variation = null ) {
		$t_hash = $name;
		if ( $variation ) {
			$t_hash .= '--' . $variation;
		}

		if ( ! isset( $this->widget_templates[ $t_hash ] ) ) {
			$this->widget_templates[ $t_hash ] = new $name( $this, $variation );
		}

		return $this->widget_templates[ $t_hash ];
	}

	/**
	 * Check if widget has template.
	 *
	 * @param class-string $name Template name.
	 *
	 * @return bool
	 */
	public function has_template( $name ) {
		return isset( $this->widget_templates[ $name ] );
	}

	/**
	 * Return array of supported devices with dependencies.
	 *
	 * @since 5.6.0
	 *
	 * @return array
	 */
	public function get_supported_devices() {
		return [
			''       => [],
			'tablet' => [ '' ],
			'mobile' => [ 'tablet', '' ],
		];
	}

	/**
	 * Retrun array of device dependencies or an empty array.
	 *
	 * @since 5.6.0
	 *
	 * @param string $device Device.
	 *
	 * @return array
	 */
	protected function get_device_dependencies( $device ) {
		$devices = $this->get_supported_devices();

		if ( isset( $devices[ $device ] ) ) {
			return (array) $devices[ $device ];
		}

		return [];
	}

	/**
	 * Return responsive setting value for current device, based on complex inheritance rules.
	 *
	 * @since 5.6.0
	 *
	 * @param string      $setting        Elementor setting id.
	 * @param null|string $current_device Current device.
	 *
	 * @return mixed
	 */
	public function get_responsive_setting( $setting, $current_device = null ) {
		if ( $current_device === null && $this->less_manager instanceof The7_Elementor_Less_Vars_Decorator_Interface ) {
			$current_device = $this->less_manager->get_current_device();
		}

		$setting_value = $this->get_settings_for_display( $setting . ( $current_device ? "_{$current_device}" : '' ) );

		if ( $current_device ) {
			foreach ( $this->get_device_dependencies( $current_device ) as $device ) {
				$device_suffix        = $device ? "_{$device}" : '';
				$device_setting_value = $this->get_settings_for_display( $setting . $device_suffix );
				$inherit_value        = $this->inherint_value( $setting_value, $device_setting_value );

				if ( $inherit_value !== $setting_value ) {
					$setting_value = $inherit_value;
					break;
				}
			}
		}

		return $setting_value;
	}

	/**
	 * Inherit another setting value if original is empty.
	 *
	 * @since 5.6.0
	 *
	 * @param array|string $val Original Elementor setting value.
	 * @param array|string $inherit_val Elementor settings value that may be inherited.
	 *
	 * @return mixed
	 */
	protected function inherint_value( $val, $inherit_val ) {
		if ( is_array( $inherit_val ) ) {
			return array_merge( $inherit_val, $this->unset_empty_value( (array) $val ) );
		}

		if ( $val === null || $val === '' ) {
			return $inherit_val;
		}

		return $val;
	}

	/**
	 * Unset empty elementor setting value. Can be applied to complex data like [ 'unit' => '', 'size' => ''  ].
	 *
	 * @since 5.6.0
	 *
	 * @param array $setting Elementor settings value.
	 *
	 * @return array
	 */
	protected function unset_empty_value( $setting ) {
		if ( ! is_array( $setting ) ) {
			return [];
		}

		$maybe_dimesions = array_intersect_key(
			$setting,
			[
				'top'    => '',
				'bottom' => '',
				'left'   => '',
				'right'  => '',
			]
		);

		if ( $maybe_dimesions && implode( '', $maybe_dimesions ) === '' ) {
			$setting = the7_array_filter_non_empty_string( $setting );
			unset( $setting['unit'] );
		} elseif ( isset( $setting['size'] ) && $setting['size'] === '' ) {
			unset( $setting['size'], $setting['unit'] );
		} elseif ( isset( $setting['value'] ) && $setting['value'] === '' ) {
			unset( $setting['value'] );
		}

		return $setting;
	}

	/**
	 * Return elementor icon HTML.
	 *
	 * @param array  $icon Icon Type, Icon value.
	 * @param string $tag  Icon HTML tag, defaults to <i>.
	 * @param array  $attributes Icon attributes.
	 *
	 * @return mixed|string
	 * @see Icons_Manager::render_icon
	 */
	public function get_elementor_icon_html( $icon, $tag = 'i', $attributes = [] ) {
		$attributes = (array) wp_parse_args( $attributes, [ 'aria-hidden' => 'true' ] );

		if ( isset( $attributes['class'] ) && ! is_array( $attributes['class'] ) ) {
			$attributes['class'] = [ $attributes['class'] ];
		}

		ob_start();
		Icons_Manager::render_icon( $icon, $attributes, $tag );

		return ob_get_clean();
	}

	/**
	 * @param array $array Array of css vars like ['var' => 'value'].
	 *
	 * @return string
	 */
	public function combine_to_css_vars_definition_string( $array ) {
		return implode( ' ', presscore_convert_indexed2numeric_array( ':', $array, '--', '%s;' ) );
	}

	/**
	 * Register widget assets.
	 *
	 * @see The7_Elementor_Widget_Base::__construct()
	 */
	protected function register_assets() {}

	/**
	 * Add new responsive control to stack.
	 *
	 * Register a set of controls to allow editing based on user screen size.
	 * This method registers one or more controls per screen size/device, depending on the current Responsive Control
	 * Duplication Mode. There are 3 control duplication modes:
	 * * 'off' - Only a single control is generated. In the Editor, this control is duplicated in JS.
	 * * 'on' - Multiple controls are generated, one control per enabled device/breakpoint + a default/desktop control.
	 * * 'dynamic' - If the control includes the `'dynamic' => 'active' => true` property - the control is duplicated,
	 *               once for each device/breakpoint + default/desktop.
	 *               If the control doesn't include the `'dynamic' => 'active' => true` property - the control is not duplicated.
	 *
	 * @since 1.4.0
	 * @access public
	 *
	 * @param string $id      Responsive control ID.
	 * @param array  $args    Responsive control arguments.
	 * @param array  $options Optional. Responsive control options. Default is
	 *                        an empty array.
	 */
	final public function add_basic_responsive_control( $id, array $args, $options = [] ) {
		if ( the7_is_elementor3_4() ) {
			$args['the7_is_basic_responsive'] = true;
			$args['devices']                  = [
				Breakpoints_Manager::BREAKPOINT_KEY_DESKTOP,
				Breakpoints_Manager::BREAKPOINT_KEY_TABLET,
				Breakpoints_Manager::BREAKPOINT_KEY_MOBILE,
			];
		}
		parent::add_responsive_control( $id, $args, $options );
	}

	/**
	 * @return array
	 */
	protected function get_init_settings() {
		$settings = parent::get_init_settings();
		foreach ( $this->get_controls() as $control ) {
			if ( ! empty( $control['the7_is_basic_responsive'] ) ) {
				$setting             = $control['name'];
				$tablet              = "{$setting}_tablet";
				$mobile              = "{$setting}_mobile";
				$settings[ $tablet ] = $settings[ $tablet ] ?? null;
				$settings[ $mobile ] = $settings[ $mobile ] ?? null;
			}
		}

		return apply_filters( 'the7_elementor_widget_init_settings', $settings, $this );
	}

	/**
	 * Output `Nothing found` message.
	 */
	protected function render_nothing_found_message() {
		echo '<p>' . esc_html__( 'Nothing found', 'the7mk2' ) . '</p>';
	}

	/**
	 * Return `current_query` based on $settings. In most cases it is `$GLOBALS['wp_query']`.
	 *
	 * @param array $settings
	 *
	 * @return WP_Query
	 */
	public static function get_current_query( $settings ) {
		if ( ! empty( $settings['archive_posts_per_page'] ) ) {
			$is_preview = (
				isset( $_GET['preview'], $_GET['preview_id'], $_GET['preview_nonce'] )
				&& wp_verify_nonce( $_GET['preview_nonce'], 'post_preview_' . (int) $_GET['preview_id'] )
			);

			// Fix pagination in editor and preview.
			if ( $is_preview || \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				$query = $GLOBALS['wp_query']->query;

				$query['posts_per_page'] = (int) $settings['archive_posts_per_page'];

				return new \WP_Query( $query );
			}
		}

		// Restart internal post counters to properly loop.
		$GLOBALS['wp_query']->rewind_posts();

		return $GLOBALS['wp_query'];
	}

	public static function get_device_options() {
		// TODO: Once Core 3.4.0 is out, get the active devices using Breakpoints/Manager::get_active_devices_list().
		$active_breakpoint_instances = \Elementor\Plugin::$instance->breakpoints->get_active_breakpoints();
		// Devices need to be ordered from largest to smallest.
		$active_devices = array_reverse( array_keys( $active_breakpoint_instances ) );

		// Add desktop in the correct position.
		if ( in_array( 'widescreen', $active_devices, true ) ) {
			$active_devices = array_merge( array_slice( $active_devices, 0, 1 ), [ 'desktop' ], array_slice( $active_devices, 1 ) );
		} else {
			$active_devices = array_merge( [ 'desktop' ], $active_devices );
		}

		$devices_options = [];

		foreach ( $active_devices as $device_key ) {
			$device_label = 'desktop' === $device_key ? esc_html__( 'Desktop', 'the7mk2' ) : $active_breakpoint_instances[ $device_key ]->get_label();

			$devices_options[ $device_key ] = $device_label;
		}

		return [
			'active_devices'  => $active_devices,
			'devices_options' => $devices_options,
		];
	}

	/**
	 * @param string[]     $selectors Selectors array.
	 * @param array|string $values Common values with selector prefix.
	 *
	 * @return array
	 */
	protected function give_me_megaselectors( $selectors, $values ) {
		if ( ! is_array( $values ) ) {
			$values = [ '' => $values ];
		}

		$megaselectors = [];
		foreach ( $values as $selector_prefix => $value ) {
			$updated_selectors = $selectors;
			if ( $selector_prefix ) {
				$updated_selectors = array_map(
					static function ( $e ) use ( $selector_prefix ) {
						return $e . $selector_prefix;
					},
					$selectors
				);
			}

			$megaselectors += array_fill_keys( $updated_selectors, $value );
		}

		return $megaselectors;
	}

	/**
	 * Returns true if any of responsive settings equals to the given value.
	 *
	 * @param  string $key  Setting key.
	 * @param  mixed  $val  Value to compare.
	 *
	 * @return bool
	 */
	protected function any_responsive_setting_equals( $key, $val ) {
		$settings = $this->get_settings_for_display();

		if ( isset( $settings[ $key ] ) && $settings[ $key ] === $val ) {
			return true;
		}

		$active_breakpoints = Plugin::$instance->breakpoints->get_active_breakpoints();
		foreach ( $active_breakpoints as $breakpoint_name => $breakpoint_config ) {
			$b_key = $key . '_' . $breakpoint_name;
			if ( isset( $settings[ $b_key ] ) && $settings[ $b_key ] === $val ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @param string|int $popup_id Popup id.
	 *
	 * @return string
	 */
	protected function get_popup_url( $popup_id ): string {
		if ( ! $popup_id ) {
			return '';
		}

		$popup_id = (int) apply_filters( 'wpml_object_id', $popup_id, Source_Local::CPT, true );

		$link_action_url = Plugin::instance()->frontend->create_action_hash(
			'popup:open',
			[
				'id'     => $popup_id,
				'toggle' => false,
			]
		);

		PopupModule::add_popup_to_location( $popup_id );

		return $link_action_url;
	}
}
