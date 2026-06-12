<?php
/**
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Upgrade;

use Elementor\Core\Breakpoints\Manager as Breakpoints_Manager;

defined( 'ABSPATH' ) || exit;

abstract class Widget_Migration {

	const GLOBALS_KEY     = '__globals__';
	const DYNAMIC_TAG_KEY = '__dynamic__';
	const DEVICES         = [
		'',
		'_tablet',
		'_mobile',
	];

	protected $settings = [];

	/**
	 * @var string|null
	 */
	protected $current_widget_name;

	public static function get_widget_name() {
		return '';
	}

	/**
	 * @return array
	 */
	public static function get_callback_args_array() {
		return [];
	}

	/**
	 * Return responsive device control prefixes.
	 *
	 * @return string[]
	 */
	public static function get_responsive_devices() {
		if ( class_exists( Breakpoints_Manager::class ) ) {
			$breakpoints = [ '' ];
			foreach ( Breakpoints_Manager::get_default_config() as $breakpoint => $config ) {
				$breakpoints[] = "_{$breakpoint}";
			}

			return $breakpoints;
		}

		return self::DEVICES;
	}

	/**
	 * Default widget migration logic here.
	 *
	 * @see Widget_Migration::migrate()
	 */
	public function do_apply() {
		// Do nothing by default.
	}

	public static function run( $migration, $updater = null ) {
		if ( $updater === null ) {
			$updater = new \The7\Mods\Compatibility\Elementor\Upgrade\Updater();
		}

		$changes = [
			[
				'callback'    => [ static::class, $migration ],
				'control_ids' => [],
			],
		];
		\Elementor\Core\Upgrade\Upgrade_Utils::_update_widget_settings( static::get_widget_name(), $updater, $changes );
	}

	/**
	 * Migrate with `do_apply` method.
	 *
	 * @param null|string $widget_name Widget name.
	 * @param null|object $updater     Updater class.
	 *
	 * @see Widget_Migration::apply()
	 *
	 * @return bool Return true if migration was launched and false otherwise.
	 */
	public static function migrate( $widget_name = null, $updater = null ) {
		if ( ! class_exists( 'Elementor\Core\Upgrade\Upgrade_Utils' ) ) {
			return false;
		}

		if ( ! $widget_name ) {
			$widget_name = static::get_widget_name();
		}

		if ( ! $updater ) {
			$updater = new \The7\Mods\Compatibility\Elementor\Upgrade\Updater();
		}

		$changes = [
			[
				'callback'    => [ new static(), 'apply' ],
				'control_ids' => [],
			],
		];

		\Elementor\Core\Upgrade\Upgrade_Utils::_update_widget_settings( $widget_name, $updater, $changes );

		return true;
	}

	public function apply( $element, $args ) {
		if ( ! static::is_the_right_widget( $element, $args ) ) {
			return $element;
		}

		$this->current_widget_name = isset( $element['widgetType'] ) ? $element['widgetType'] : null;
		$this->settings            = $element['settings'];

		$this->do_apply();

		if ( $this->settings === $element['settings'] ) {
			return $element;
		}

		$element['settings'] = $this->settings;
		$args['do_update']   = true;

		return $element;
	}

	/**
	 * @param array $element
	 *
	 * @return bool
	 */
	protected static function is_the_right_widget( $element, $args = [] ) {
		$widget_name = isset( $args['widget_id'] ) ? $args['widget_id'] : null;

		return ! empty( $element['widgetType'] ) && $element['widgetType'] === $widget_name;
	}

	/**
	 * @return string|null
	 */
	protected function get_current_widget_name() {
		return $this->current_widget_name;
	}

	protected function rename_typography( $from, $to ) {
		$this->rename( "{$from}_typography", "{$to}_typography" );

		$typography_fields = $this->get_typography_fields();

		$is_global = $this->get_global( "{$to}_typography" );

		foreach ( $typography_fields as $field ) {
			if ( $is_global ) {
				$this->remove( "{$from}_{$field}" );
			} else {
				$this->rename( "{$from}_{$field}", "{$to}_{$field}" );
			}
		}
	}

	/**
	 * Remove typography settings.
	 *
	 * @param string $key Setting key.
	 */
	protected function remove_typography( $key ) {
		$this->remove( "{$key}_typography" );
		$typography_fields = $this->get_typography_fields();
		foreach ( $typography_fields as $field ) {
			$this->remove( "{$key}_{$field}" );
		}
	}

	/**
	 * Rename control by adding the $new.
	 *
	 * @param string $old Old control name.
	 * @param string $new New control name.
	 *
	 * @return bool
	 */
	protected function rename( $old, $new ) {
		$result = false;

		if ( $this->exists( $old ) ) {
			$result = $this->add( $new, $this->get( $old ) );
		}

		if ( $this->is_global( $old ) ) {
			$result = $this->add_global( $new, $this->get_global( $old ) );
		}

		$dynamic_value = $this->get_dynamic_tag( $old );
		if ( $dynamic_value ) {
			$result = $this->add_dynamic_tag( $new, $dynamic_value );
		}

		$this->remove( $old );

		return $result;
	}

	/**
	 * Rename control by overriding the $new.
	 *
	 * @param string $old Old control name.
	 * @param string $new New control name.
	 */
	protected function force_rename( $old, $new ) {
		$this->remove( $new );

		return $this->rename( $old, $new );
	}

	/**
	 * Copy option value to another option, if value option exists.
	 *
	 * @param string $from Copy this control value.
	 * @param string $to Copy the value to this control.
	 */
	protected function copy( $from, $to ) {
		$result = false;
		foreach ( self::get_responsive_devices() as $device ) {
			$device_from = $from . $device;
			$device_to   = $to . $device;

			if ( $this->exists( $device_from ) ) {
				$result = $this->add( $device_to, $this->get( $device_from ) );
			}

			if ( $this->is_global( $device_from ) ) {
				$result = $this->add_global( $device_to, $this->get_global( $device_from ) );
			}

			$dynamic_value = $this->get_dynamic_tag( $device_from );
			if ( $dynamic_value ) {
				$result = $this->add_dynamic_tag( $device_to, $dynamic_value );
			}
		}

		return $result;
	}

	/**
	 * @param string $from Copy this control value.
	 * @param string $to Copy the value to this control.
	 */
	protected function force_copy( $from, $to ) {
		$this->remove( $to );
		$this->copy( $from, $to );
	}

	protected function remove( $key ) {
		$this->settings = $this->unset_array_key( $this->settings, $key );
		$this->remove_global( $key );
		$this->remove_dynamic_tag( $key );
	}

	protected function set( $key, $val ) {
		$this->settings[ $key ] = $val;
	}

	/**
	 * @param  string  $key
	 * @param  string  $subkey
	 * @param  mixed   $val
	 *
	 * @return void
	 */
	protected function set_subkey( $key, $subkey, $val ) {
		$key_value = $this->get( $key );

		if ( is_array( $key_value ) ) {
			$key_value[ $subkey ] = $val;
			$this->set( $key, $key_value );
		}
	}

	/**
	 * @param string $key
	 * @param mixed $val
	 *
	 * @return bool
	 */
	protected function add( $key, $val ) {
		if ( ! $this->exists( $key ) ) {
			$this->set( $key, $val );

			return true;
		}

		return false;
	}

	protected function get( $key ) {
		if ( $this->exists( $key ) ) {
			return $this->settings[ $key ];
		}

		return null;
	}

	protected function get_subkey( $key, $subkey ) {
		$val = $this->get( $key );

		if ( isset( $val[ $subkey ] ) ) {
			return $val[ $subkey ];
		}

		return null;
	}

	protected function exists( $key ) {
		return array_key_exists( $key, $this->settings );
	}

	protected function is_global( $key ) {
		$global = $this->get_global( $key );

		if ( ! $global ) {
			return false;
		}

		$value = null;
		if ( isset( \Elementor\Plugin::$instance->data_manager_v2 ) ) {
			$value = \Elementor\Plugin::$instance->data_manager_v2->run( $global );
		} elseif ( property_exists( \Elementor\Plugin::$instance, 'data_manager' ) && is_object( \Elementor\Plugin::$instance->data_manager ) ) {
			// Prevent fatal errors with Elementor 2.9.x.
			$value = \Elementor\Plugin::$instance->data_manager->run( $global );
		}

		return ! empty( $value );
	}

	/**
	 * @param string $global_endpoint Endpoint to get global value.
	 *
	 * @return null|mixed Global value.
	 */
	protected function get_global_value( $global_endpoint ) {
		$value = null;
		if ( isset( \Elementor\Plugin::$instance->data_manager_v2 ) ) {
			$value = \Elementor\Plugin::$instance->data_manager_v2->run( $global_endpoint );
		} elseif ( property_exists( \Elementor\Plugin::$instance, 'data_manager' ) && is_object( \Elementor\Plugin::$instance->data_manager ) ) {
			// Prevent fatal errors with Elementor 2.9.x.
			$value = \Elementor\Plugin::$instance->data_manager->run( $global_endpoint );
		}

		return isset( $value['value'] ) ? $value['value'] : null;
	}

	protected function get_global( $key ) {
		return $this->get_subkey( static::GLOBALS_KEY, $key );
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 *
	 * @return bool
	 */
	protected function add_global( $key, $value ) {
		if ( ! $this->get_global( $key ) ) {
			$globals         = $this->get( static::GLOBALS_KEY );
			$globals[ $key ] = $value;
			ksort( $globals );
			$this->set( static::GLOBALS_KEY, $globals );

			return true;
		}

		return false;
	}

	protected function remove_global( $key ) {
		$globals = $this->get( static::GLOBALS_KEY );

		if ( ! $globals ) {
			return;
		}

		$globals = $this->unset_array_key( $globals, $key );

		$this->set( static::GLOBALS_KEY, $globals );
	}

	/**
	 * @param string $key
	 *
	 * @return bool
	 */
	protected function is_dynamic_tag( $key ) {
		$dynamic = $this->get( static::DYNAMIC_TAG_KEY );

		if ( ! $dynamic ) {
			return false;
		}

		return array_key_exists( $key, $dynamic );
	}

	/**
	 * @param string $key
	 * @param string $value
	 *
	 * @return bool
	 */
	protected function add_dynamic_tag( $key, $value ) {
		$dynamic = $this->get( static::DYNAMIC_TAG_KEY );

		if ( ! $dynamic ) {
			$dynamic = [];
		}

		$dynamic[ $key ] = $value;

		$this->set( static::DYNAMIC_TAG_KEY, $dynamic );

		return true;
	}

	/**
	 * @param string $key
	 *
	 * @return array|null
	 */
	protected function get_dynamic_tag( $key ) {
		if ( ! $this->is_dynamic_tag( $key ) ) {
			return null;
		}

		return $this->get_subkey( static::DYNAMIC_TAG_KEY, $key );
	}

	/**
	 * @param string $key
	 *
	 * @return array|null
	 */
	protected function get_dynamic_tag_decoded( $key ) {
		if ( ! $this->is_dynamic_tag( $key ) ) {
			return null;
		}

		$tag_text = $this->get_subkey( static::DYNAMIC_TAG_KEY, $key );

		preg_match( '/name="(.*?(?="))"/', $tag_text, $tag_name_match );
		preg_match( '/settings="(.*?(?="]))/', $tag_text, $tag_settings_match );

		return [
			'name'     => $tag_name_match[1],
			'settings' => $tag_settings_match[1],
		];
	}

	/**
	 * @param string|array $key
	 */
	protected function remove_dynamic_tag( $key ) {
		$dynamic = $this->get( static::DYNAMIC_TAG_KEY );
		if ( $dynamic ) {
			$dynamic = $this->unset_array_key( $dynamic, $key );
			$this->set( static::DYNAMIC_TAG_KEY, $dynamic );
		}
	}

	/**
	 * @param array            $array
	 * @param array|string|int $key
	 *
	 * @return array
	 */
	protected function unset_array_key( array $array, $key ) {
		if ( is_array( $key ) ) {
			foreach ( $key as $k ) {
				unset( $array[ $k ] );
			}
		} else {
			unset( $array[ $key ] );
		}

		return $array;
	}

	/**
	 * @return string[]
	 */
	protected function get_typography_fields() {
		return [
			'font_family',
			'font_size',
			'font_size_tablet',
			'font_size_mobile',
			'font_weight',
			'line_height',
			'line_height_tablet',
			'line_height_mobile',
			'text_transform',
			'font_style',
			'text_decoration',
			'letter_spacing',
			'letter_spacing_tablet',
			'letter_spacing_mobile',
			'word_spacing',
			'word_spacing_tablet',
			'word_spacing_mobile',
		];
	}
}
