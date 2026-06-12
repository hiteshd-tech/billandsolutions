<?php
/**
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widget_Templates;

use Elementor\Controls_Manager;
use Elementor\Plugin;

defined( 'ABSPATH' ) || exit;

/**
 * Class Image_Size.
 *
 * @package The7\Mods\Compatibility\Elementor\Widget_Templates
 */
class Image_Size extends Abstract_Template {

	/**
	 * @return void
	 */
	public function add_style_controls($condition = []) {
		$active_breakpoints = array_keys( Plugin::$instance->breakpoints->get_active_breakpoints() );
		$options            = $this->get_image_size_options();
		$device_options     = $options + [ '' => esc_html__( 'Inherit', 'the7mk2' ) ];
		$device_args        = [];
		foreach ( $active_breakpoints as $active_breakpoint ) {
			$device_args[ $active_breakpoint ] = [
				'options' => $device_options,
			];
		}

		$this->widget->add_responsive_control(
			$this->prefix . 'item_size',
			[
				'label'       => esc_html__( 'Image Size', 'the7mk2' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'full',
				'options'     => $options,
				'device_args' => $device_args,
				'condition' => $condition,
			]
		);
	}

	/**
	 * @param int          $attachment_id Image attachment ID.
	 * @param string|array $attr {
	 *     Optional. Attributes for the image markup.
	 *
	 *     @type string       $src      Image attachment URL.
	 *     @type string       $class    CSS class name or space-separated list of classes.
	 *                                  Default `attachment-$size_class size-$size_class`,
	 *                                  where `$size_class` is the image size being requested.
	 *     @type string       $alt      Image description for the alt attribute.
	 *     @type string       $srcset   The 'srcset' attribute value.
	 *     @type string       $sizes    The 'sizes' attribute value.
	 *     @type string|false $loading  The 'loading' attribute value. Passing a value of false
	 *                                  will result in the attribute being omitted for the image.
	 *                                  Defaults to 'lazy', depending on wp_lazy_loading_enabled().
	 *     @type string       $decoding The 'decoding' attribute value. Possible values are
	 *                                  'async' (default), 'sync', or 'auto'.
	 * }
	 * @return string HTML img element or empty string on failure.
	 */
	public function get_image( $attachment_id, $attr = '' ) {
		if ( ! $attachment_id ) {
			return $this->get_default_image_html( $attr );
		}

		return $this->apply_filters(
			function ( $size ) use ( $attachment_id, $attr ) {
				return wp_get_attachment_image( $attachment_id, $size, false, $attr );
			}
		);
	}

	/**
	 * @return string
	 */
	public function get_wrapper_class() {
		$class = '';

		if ( presscore_lazy_loading_enabled() ) {
			$class .= 'layzr-bg';
		}

		return $class;
	}

	/**
	 * @param callable $callback Callback to apply filters to. Should return string.
	 *
	 * @return string
	 */
	public function apply_filters( $callback ) {
		if ( ! is_callable( $callback ) ) {
			return '';
		}

		add_filter( 'wp_get_attachment_image_attributes', [ $this, 'wp_get_attachment_image_attributes_filter' ], 10, 3 );
		add_filter( 'wp_calculate_image_sizes', [ $this, 'wp_calculate_image_sizes_filter' ], 10, 5 );

		$size   = $this->widget->get_settings_for_display( $this->prefix . 'item_size' );
		$result = $callback( $size );

		remove_filter( 'wp_calculate_image_sizes', [ $this, 'wp_calculate_image_sizes_filter' ], 10 );
		remove_filter( 'wp_get_attachment_image_attributes', [ $this, 'wp_get_attachment_image_attributes_filter' ], 10 );

		return $result;
	}

	/**
	 * @param string[]     $attr       Array of attribute values for the image markup, keyed by attribute name.
	 *                                 See wp_get_attachment_image().
	 * @param \WP_Post     $attachment Image attachment post.
	 * @param string|int[] $size       Requested image size. Can be any registered image size name, or
	 *                                 an array of width and height values in pixels (in that order).
	 *
	 * @return string[]
	 */
	public function wp_get_attachment_image_attributes_filter( $attr, $attachment, $size ) {
		$image_meta = wp_get_attachment_metadata( $attachment->ID );
		$image_size = $this->get_image_size_from_meta( $size, $image_meta );
		if ( $image_size ) {
			list( $w, $h ) = $image_size;

			if ( ! isset( $attr['style'] ) ) {
				$attr['style'] = '';
			}
			$attr['style'] .= $this->get_ratio_style_attr( $w, $h );
		} else {
			// Default lazy bg size.
			list( $w, $h ) = [ 1, 1 ];
		}

		$attr['class'] .= ' preload-me ' . $this->get_ratio_class();

		if ( get_post_mime_type( $attachment ) === 'image/svg+xml' ) {
			$attr['class'] .= ' the7-svg-image';
		}

		if ( presscore_lazy_loading_enabled() ) {
			$attr['class'] .= ' lazy lazy-load';
			if ( isset( $attr['src'] ) ) {
				$attr['data-src'] = $attr['src'];
				$attr['src']      = "data:image/svg+xml,%3Csvg%20xmlns%3D&#39;http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg&#39;%20viewBox%3D&#39;0%200%20{$w}%20{$h}&#39;%2F%3E";
				$attr['loading']  = 'eager';
			}
			if ( isset( $attr['srcset'] ) ) {
				$attr['data-srcset'] = $attr['srcset'];
				unset( $attr['srcset'] );
			}
		}

		return $attr;
	}

	/**
	 * @param string       $sizes         A source size value for use in a 'sizes' attribute.
	 * @param string|int[] $size          Requested image size. Can be any registered image size name, or
	 *                                    an array of width and height values in pixels (in that order).
	 * @param string|null  $image_src     The URL to the image file or null.
	 * @param array|null   $image_meta    The image meta data as returned by wp_get_attachment_metadata() or null.
	 * @param int          $attachment_id Image attachment ID of the original image or 0.
	 *
	 * @return string
	 */
	public function wp_calculate_image_sizes_filter( $sizes, $size, $image_src, $image_meta, $attachment_id ) {
		$devices   = Plugin::$instance->breakpoints->get_active_breakpoints();
		$sizes_tpl = '(max-width: %1$s) %2$s';
		$sizes_arr = [];
		foreach ( $devices as $device_name => $device ) {
			$device_item_size = $this->widget->get_settings_for_display( $this->prefix . 'item_size_' . $device_name );
			if ( ! $device_item_size ) {
				continue;
			}

			$image_size = $this->get_image_size_from_meta( $device_item_size, $image_meta );
			if ( $image_size ) {
				$sizes_arr[] = sprintf( $sizes_tpl, $device->get_value() . 'px', $image_size[0] . 'px' );
			}
		}

		if ( ! $sizes_arr ) {
			return $sizes;
		}

		// Add desktop size.
		if ( is_array( $size ) ) {
			$sizes_arr[] = $size[0] . 'px';
		}

		return implode( ', ', $sizes_arr );
	}

	/**
	 * Gets the image size as array from its meta data.
	 *
	 * @param string $size_name  Image size. Accepts any registered image size name.
	 * @param array  $image_meta The image meta data.
	 * @return array|false {
	 *     Array of width and height or false if the size isn't present in the meta data.
	 *
	 *     @type int $0 Image width.
	 *     @type int $1 Image height.
	 * }
	 */
	protected function get_image_size_from_meta( $size_name, $image_meta ) {
		if ( $size_name === 'full' && isset( $image_meta['width'], $image_meta['height'] ) ) {
			return [
				absint( $image_meta['width'] ),
				absint( $image_meta['height'] ),
			];
		}

		if ( isset( $image_meta['sizes'][ $size_name ]['width'], $image_meta['sizes'][ $size_name ]['height'] ) ) {
			return [
				absint( $image_meta['sizes'][ $size_name ]['width'] ),
				absint( $image_meta['sizes'][ $size_name ]['height'] ),
			];
		}

		return false;
	}

	/**
	 * @return array
	 */
	protected function get_image_size_options() {
		$wp_image_sizes = $this->get_image_sizes();
		$image_sizes    = [];

		foreach ( $wp_image_sizes as $size_key => $size_attributes ) {
			$control_title = ucwords( str_replace( '_', ' ', $size_key ) );
			if ( is_array( $size_attributes ) ) {
				$control_title .= sprintf( ' - %d x %d', $size_attributes['width'], $size_attributes['height'] );
			}

			$image_sizes[ $size_key ] = $control_title;
		}

		$image_sizes['full'] = esc_html_x( 'Full', 'Image Size Control', 'the7mk2' );

		return $image_sizes;
	}

	/**
	 * @return mixed|null
	 */
	protected function get_image_sizes() {
		$wp_image_sizes = wp_get_registered_image_subsizes();

		/** This filter is documented in wp-admin/includes/media.php */
		return apply_filters( 'image_size_names_choose', $wp_image_sizes );
	}

	/**
	 * Returns default image HTML.
	 *
	 * @param array|string|null $attr Optional image attributes.
	 *
	 * @return string
	 */
	protected function get_default_image_html( $attr = null ) {
		$attr = wp_parse_args(
			$attr ?: [],
			[
				'class' => '',
				'style' => '',
			]
		);

		list( $attr['src'], $attr['width'], $attr['height'] ) = presscore_get_default_image();

		$attr['class']  = trim( $attr['class'] . ' ' . $this->get_ratio_class() );
		$attr['style'] .= $this->get_ratio_style_attr( $attr['width'], $attr['height'] );

		return '<img ' . the7_get_html_attributes_string( $attr ) . '/>';
	}

	/**
	 * @param int $w    Image width.
	 * @param int $h    Image height.
	 *
	 * @return string
	 */
	protected function get_ratio_style_attr( $w, $h ) {
		return "--ratio: $w / $h;";
	}

	/**
	 * @return string
	 */
	protected function get_ratio_class() {
		return 'aspect';
	}
}