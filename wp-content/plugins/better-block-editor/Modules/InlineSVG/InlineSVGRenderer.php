<?php
/**
 * Renders the SVG icon block.
 *
 * @package BetterBlockEditor
 */

namespace BetterBlockEditor\Modules\InlineSVG;

use BetterBlockEditor\Core\BlockUtils;
use BetterBlockEditor\Core\ColorUtils;
use BetterBlockEditor\Modules\StyleEngine\Module as StyleEngineModule;
use WP_Block_Supports;

defined( 'ABSPATH' ) || exit;

class InlineSVGRenderer {
	/**
	 * Renders the SVG icon block.
	 *
	 * @param array  $attributes The block attributes.
	 * @param array  $custom_classes Custom classes to be added to the block.
	 * @param string $fallback_content Fallback SVG content.
	 * @param string $class_id Optional wrapper class id.
	 *
	 * @return string The rendered SVG icon block.
	 */
	public function render( $attributes, $custom_classes = array(), $fallback_content = '', $class_id = '' ) {
		if ( empty( $fallback_content ) ) {
			$image_id = $attributes['imageID'] ?? 0;

			if ( 'image/svg+xml' !== get_post_mime_type( $image_id ) ) {
				return '';
			}
			$image    = get_attached_file( $image_id );
			$contents = file_get_contents( $image );
		} else {
			$contents = $fallback_content;
		}
		if ( empty( $contents ) ) {
			return '';
		}

		$href          = $attributes['href'] ?? '';
		$aria_label    = $attributes['ariaLabel'] ?? '';
		$is_button     = in_array( 'nsArrow', $custom_classes, true );
		$is_decorative = ! $is_button && empty( $href ) && empty( $aria_label );

		$contents = $this->prepare_svg_accessibility( $contents, $is_decorative );

		if ( empty( $class_id ) ) {
			$class_id = BlockUtils::create_unique_class_id();
		}

		$base_classes    = array_merge( array( 'wpbbe-svg-icon', $class_id ), $custom_classes );
		$wrapper_classes = BlockUtils::append_block_wrapper_classes( $base_classes );

		$style = $attributes['style'] ?? array();

		$wrapper_style = array();
		$inner_style   = $style;

		// Move margin to wrapper
		if ( isset( $style['spacing']['margin'] ) ) {
			$wrapper_style['spacing']['margin'] = $style['spacing']['margin'];

			unset( $inner_style['spacing']['margin'] );
		}

		$options = array(
			'context'  => 'core',
			'prettify' => false,
			'selector' => ".{$class_id}.{$class_id}",
		);
		//add styles for wrapper.
		StyleEngineModule::get_styles( $wrapper_style, $options );
		$options['selector'] = ".{$class_id} .svg-wrapper";
		//add styles for inner element.
		StyleEngineModule::get_styles( $inner_style, $options );

		// prepare custom styles.
		$style = $this->collect_style_metadata( $attributes );

		$options['definitions_metadata'] = array(
			'color'      => array(
				'svgBackgroundColor' => array(
					'property_keys' => array( 'default' => 'background-color' ),
					'path'          => array(
						'color',
						'backgroundColor',
					),
					'css_vars'      => array( 'color' => '--wp--preset--color--$slug' ),
				),

				'svgBorderColor'     => array(
					'property_keys' => array( 'default' => 'border-color' ),
					'path'          => array(
						'color',
						'borderColor',
					),
					'classnames'    => array(
						'has-border-color' => true,
					),
					'css_vars'      => array( 'color' => '--wp--preset--color--$slug' ),
				),
			),
			'dimensions' => array(
				'width' => array(
					'property_keys' => array(
						'default' => '--svg-width',
					),
					'path'          => array( 'dimensions', 'imageWidth' ),
				),
			),
			'position'   => array(
				'height' => array(
					'property_keys' => array(
						'default' => '--svg-alignment',
					),
					'path'          => array( 'position', 'alignment' ),
				),
			),
		);

		$styles = StyleEngineModule::get_styles( $style, $options );
		if ( ! empty( $styles['classnames'] ) ) {
			$wrapper_classes[] = $styles['classnames'];
		}

		$options['selector']             = ".{$class_id} svg";
		$options['definitions_metadata'] = array(
			'color' => array(
				'svgColor'       => array(
					'property_keys' => array( 'default' => 'color' ),
					'path'          => array(
						'color',
						'color',
					),
					'css_vars'      => array( 'color' => '--wp--preset--color--$slug' ),
				),
				'svgColorStroke' => array(
					'property_keys' => array( 'default' => 'stroke' ),
					'path'          => array(
						'color',
						'color',
					),
					'css_vars'      => array( 'color' => '--wp--preset--color--$slug' ),
				),
				'svgFillColor'   => array(
					'property_keys' => array( 'default' => 'fill' ),
					'path'          => array(
						'color',
						'fillColor',
					),
					'css_vars'      => array( 'fill' => '--wp--preset--color--$slug' ),
				),
			),
		);

		StyleEngineModule::get_styles( $style, $options );

		$options['selector']             = ".{$class_id}:not(.nsDisabled) .svg-wrapper:hover";
		$options['definitions_metadata'] = array(
			'color' => array(
				'svgHoverBackgroundColor' => array(
					'property_keys' => array( 'default' => 'background-color' ),
					'path'          => array(
						'color',
						'hoverBackgroundColor',
					),
					'css_vars'      => array( 'color' => '--wp--preset--color--$slug' ),
				),
				'svgHoverBorderColor'     => array(
					'property_keys' => array( 'default' => 'border-color' ),
					'path'          => array(
						'color',
						'hoverBorderColor',
					),
					'classnames'    => array(
						'has-border-color' => true,
					),
					'css_vars'      => array( 'color' => '--wp--preset--color--$slug' ),
				),
			),
		);

		$styles = StyleEngineModule::get_styles( $style, $options );
		if ( ! empty( $styles['classnames'] ) ) {
			$wrapper_classes[] = $styles['classnames'];
		}

		$options['selector']             = ".{$class_id}:not(.nsDisabled) .svg-wrapper:hover svg";
		$options['definitions_metadata'] = array(
			'color' => array(
				'svgHoverColor'       => array(
					'property_keys' => array( 'default' => 'color' ),
					'path'          => array( 'color', 'hoverColor' ),
					'css_vars'      => array( 'color' => '--wp--preset--color--$slug' ),
				),
				'svgHoverStrokeColor' => array(
					'property_keys' => array( 'default' => 'stroke' ),
					'path'          => array( 'color', 'hoverColor' ),
					'css_vars'      => array( 'color' => '--wp--preset--color--$slug' ),
				),
				'svgHoverFillColor'   => array(
					'property_keys' => array( 'default' => 'fill' ),
					'path'          => array(
						'color',
						'hoverFillColor',
					),
					'css_vars'      => array( 'fill' => '--wp--preset--color--$slug' ),
				),
			),
		);

		StyleEngineModule::get_styles( $style, $options );

		$options['selector']             = ".{$class_id}";
		$options['definitions_metadata'] = array(
			'position' => array(
				'height' => array(
					'property_keys' => array(
						'default' => '--svg-alignment',
					),
					'path'          => array( 'position', 'alignment' ),
				),
			),
		);

		StyleEngineModule::get_styles( $style, $options );


		$extra_attr = '';

		if ( ! empty( $svg_wrapper_css ) ) {
			$extra_attr .= ' style="' . esc_attr( $svg_wrapper_css ) . '"';
		}


		$output = '<div class="' . esc_attr( implode( ' ', $wrapper_classes ) ) . '">';
		if ( ! empty( $href ) ) {

			$link_target = $attributes['linkTarget'] ?? '';
			$link_rel    = $attributes['rel'] ?? '';

			// Fallback accessible label if not provided
			if ( empty( $aria_label ) ) {
				$aria_label = __( 'Linked icon', 'better-block-editor' );
			}

			$output .= '<a href="' . esc_url( $href ) . '" '
			           . 'class="svg-wrapper svg-link" '
			           . 'aria-label="' . esc_attr( $aria_label ) . '" '
			           . ( ! empty( $link_target ) ? 'target="' . esc_attr( $link_target ) . '" ' : '' )
			           . ( ! empty( $link_rel ) ? 'rel="' . esc_attr( $link_rel ) . '" ' : '' )
			           . $extra_attr
			           . '>'
			           . $contents
			           . '</a>';
		} else {
			$tag = 'div';
			if ($is_button) {
				$tag = 'button';
					$extra_attr .= '  type="button"';
					if ( in_array( 'nsLeftArrow', $custom_classes, true ) ) {
						$extra_attr .= ' aria-label="' . esc_attr__( 'Previous', 'better-block-editor' ) . '"';
					} elseif ( in_array( 'nsRightArrow', $custom_classes, true ) ) {
						$extra_attr .= ' aria-label="' . esc_attr__( 'Next', 'better-block-editor' ) . '"';
					}
			}
			$output .= '<' . $tag . ' class="svg-wrapper"' . $extra_attr . '>' . $contents . '</' . $tag . '>';
		}

		$output .= '</div>';

		return $output;
	}

	/**
	 * Inject accessibility attributes into inline SVG.
	 *
	 * @param string $svg          Raw SVG markup.
	 * @param bool   $decorative   Whether the SVG is decorative.
	 * @return string Modified SVG.
	 */
	private function prepare_svg_accessibility( $svg, $decorative = true ) {

		if ( empty( $svg ) ) {
			return '';
		}

		// Remove existing aria-hidden or focusable to avoid duplication.
		$svg = preg_replace( '/\saria-hidden="[^"]*"/i', '', $svg );
		$svg = preg_replace( '/\sfocusable="[^"]*"/i', '', $svg );

		$attributes = ' focusable="false"';

		if ( $decorative ) {
			$attributes = ' aria-hidden="true" focusable="false"';
		}
		// Inject attributes into first <svg ...> occurrence.
		$svg = preg_replace(
			'/<svg\b/',
			'<svg' . $attributes,
			$svg,
			1
		);

		return $svg;
	}


	/**
	 * Collects style metadata from the attributes.
	 *
	 * @param array $attributes The block attributes.
	 *
	 * @return array The collected style metadata.
	 */
	private function collect_style_metadata( array $attributes ) {
		$style  = array();
		$colors = array(
			'color',
			'backgroundColor',
			'fillColor',
			'borderColor',
			'hoverColor',
			'hoverFillColor',
			'hoverBackgroundColor',
			'hoverBorderColor',
		);
		foreach ( $colors as $color ) {
			if ( array_key_exists( $color, $attributes ) ) {
				$style['color'][ $color ] = ColorUtils::color_attribute_to_css( $attributes[ $color ] );
			}
		}

		$dimensions = array( 'imageWidth' );
		foreach ( $dimensions as $dimension ) {
			if ( array_key_exists( $dimension, $attributes ) ) {
				$style['dimensions'][ $dimension ] = $attributes[ $dimension ];
			}
		}

		$alignment = $attributes['alignment'] ?? '';
		if ( ! empty( $alignment ) ) {
			$style['position']['alignment'] = $alignment;
		}
		return $style;
	}
}
