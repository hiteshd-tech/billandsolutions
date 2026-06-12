<?php
/**
 * Utility class to assist with block-related logic.
 *
 * @package BetterBlockEditor
 */

namespace BetterBlockEditor\Core;

use BetterBlockEditor\Modules\StyleEngine\Module as StyleEngineModule;
use WP_Block_Supports;
use WP_HTML_Tag_Processor;

defined( 'ABSPATH' ) || exit;

class BlockUtils {

	const BLOCK_UNIQUE_CLASSNAME_PREFIX = 'wpbbe-';

	static function create_unique_class_id(): string {
		return wp_unique_prefixed_id( self::BLOCK_UNIQUE_CLASSNAME_PREFIX );
	}

	static function get_unique_class_id( $block_content ): string {
		$prefix = self::BLOCK_UNIQUE_CLASSNAME_PREFIX;

		$tags = new WP_HTML_Tag_Processor( $block_content );
		if ( $tags->next_tag() ) {
			foreach ( $tags->class_list() as $class_name ) {
				$prefix_fine = $prefix === substr( $class_name, 0, strlen( $prefix ) );
				$sufix_fine  = preg_match( '/\d/', substr( $class_name, strlen( $prefix ) ) );
				if ( $prefix_fine && $sufix_fine ) {
					return $class_name;
				}
			}
		}

		return self::create_unique_class_id();
	}

	/**
	 * Appends classes to first tag of block content.
	 *
	 * @param string       $block_content The block content.
	 * @param array|string $content_classes The classes to add.
	 *
	 * @return string The modified block content.
	 */
	static function append_classes( $block_content, $content_classes ) {
		$tag = self::get_tag_to_modify( $block_content );
		if ( empty( $content_classes ) || ! $tag ) {
			return $block_content;
		}

		foreach ( (array) $content_classes as $class_name ) {
			$tag->add_class( $class_name );
		}

		return $tag->get_updated_html();
	}

	static function remove_classes( $block_content, $content_classes ) {
		$tag = self::get_tag_to_modify( $block_content );
		if ( empty( $content_classes ) || ! $tag ) {
			return $block_content;
		}

		foreach ( $content_classes as $class_name ) {
			$tag->remove_class( $class_name );
		}

		return $tag->get_updated_html();
	}

	static function get_tag_to_modify( $block_content ) {
		$p = new WP_HTML_Tag_Processor( $block_content );
		while ( $p->next_tag() ) {
			$tag_name = $p->get_tag();
			if ( $tag_name !== 'STYLE' && $tag_name !== 'SCRIPT' ) {
				return $p;
			}
		}

		return null;
	}

	/**
	 * Sets an attribute on the first tag in the given block content.
	 *
	 * @param string $block_content The HTML content of the block.
	 * @param string $attribute     The attribute name to set.
	 * @param string $value         The value to set for the attribute.
	 *
	 * @return string The modified block content with the updated attribute.
	 */
	static function set_attribute( $block_content, $attribute, $value ) {
		$tag = self::get_tag_to_modify( $block_content );
		if ( ! $tag ) {
			return $block_content;
		}

		$tag->set_attribute( $attribute, $value );

		return $tag->get_updated_html();
	}

	/**
	 * Appends inline CSS styles to the first tag in the given block content.
	 *
	 * @param string $block_content   The HTML content of the block.
	 * @param array  $css_style_rules An associative array of CSS property-value pairs to be added.
	 *
	 * @return string The modified block content with appended inline styles.
	 */
	static function append_inline_styles( $block_content, $css_style_rules ) {
		$tag = self::get_tag_to_modify( $block_content );
		if ( empty( $css_style_rules ) || ! $tag ) {
			return $block_content;
		}

		foreach ( $css_style_rules as $property => $value ) {
			$tag->set_attribute( 'style', $tag->get_attribute( 'style' ) . '; ' . $property . ': ' . $value . ';' );
		}

		return $tag->get_updated_html();
	}

	static function append_inline_css_variables( $block_content, $css_variables ) {
		$tag = self::get_tag_to_modify( $block_content );
		if ( empty( $css_variables ) || ! $tag ) {
			return $block_content;
		}

		$var_string = '';
		foreach ( $css_variables as $name => $value ) {
			$var_string .= $name . ':' . $value . ';';
		}

		$tag->set_attribute( 'style', $tag->get_attribute( 'style' ) . '; ' . $var_string );

		return $tag->get_updated_html();
	}

	static function add_styles_from_css_rules( $css_rules ) {
		if ( ! empty( $css_rules ) ) {
			/*
			 * Add to the style engine store to enqueue and render layout styles.
			 * Return compiled layout styles to retain backwards compatibility.
			 * Since https://github.com/WordPress/gutenberg/pull/42452,
			 * wp_enqueue_block_support_styles is no longer called in this block supports file.
			 */
			return StyleEngineModule::get_stylesheet_from_css_rules(
				StyleEngineModule::preprocess_css_rules( $css_rules ),
				array(
					'context'  => 'core',
					'prettify' => false,
				)
			);
		}

		return '';
	}

	/**
	 * Adds CSS style declarations for a specific media query and selector.
	 * Just a useful wrapper for self::add_styles_from_css_rules().
	 *
	 * @param string $media_query   The media query condition (e.g., '@media screen and (width <= 500px)').
	 * @param string $selector      The CSS selector to which the declarations will apply.
	 * @param array  $css_rules     An associative array of CSS properties and their values.
	 * @return void
	 */
	static function add_style_for_media_query( $media_query, $selector, $css_rules ) {
		return self::add_styles_from_css_rules(
			array(
				array(
					'selector'     => $media_query,
					'declarations' => array(
						array(
							'selector'     => $selector,
							'declarations' => $css_rules,
						),
					),
				),
			)
		);
	}

	/**
	 * Returns the corresponding CSS flexbox horizontal alignment value based on the given attribute value.
	 *
	 * Maps alignment attribute to their respective CSS flexbox alignment values.
	 * If $reverse_orientation is true, 'left' and 'right' are swapped.
	 *
	 * @param string $attribute_value      The alignment attribute value ('left', 'right', 'center', 'stretch', 'space-between').
	 * @param bool   $reverse_orientation  Whether to reverse the alignment for orientation.
	 *
	 * @return string|null The CSS flexbox alignment value, or null if the attribute value is not recognized.
	 */
	static function get_horizontal_alignment_by_attribute( $attribute_value, $reverse_orientation = false ) {
		// Used with the default, horizontal(row) flex orientation.
		$horizontal_alignment_map = array(
			'left'          => 'flex-start',
			'right'         => 'flex-end',
			'center'        => 'center',
			'stretch'       => 'stretch',
			'space-between' => 'space-between',
		);

		$horizontal_alignment_reverse_map = array_merge(
			$horizontal_alignment_map,
			array(
				'left'  => 'flex-end',
				'right' => 'flex-start',
			)
		);

		return $reverse_orientation
			? $horizontal_alignment_reverse_map[ $attribute_value ]
			: $horizontal_alignment_map[ $attribute_value ];
	}


	/**
	 * Generates an array of wrapper classes for a block.
	 *
	 * @param array $base_classes Initial classes to include.
	 * @return array Final array of wrapper classes.
	 */
	static function append_block_wrapper_classes( array $base_classes ) {
		$wrapper_classes = $base_classes;

		$block_supports = WP_Block_Supports::get_instance();

		if ( method_exists( $block_supports, 'apply_block_supports' ) ) {
			$wrapper_attributes = $block_supports->apply_block_supports();

			if ( isset( $wrapper_attributes['class'] ) && ! empty( $wrapper_attributes['class'] ) ) {
				$wrapper_classes[] = $wrapper_attributes['class'];
			}
		}

		return $wrapper_classes;
	}

	/**
	 * Generate CSS variables based on the settings and attributes.
	 *
	 * @param array $settings The settings to generate CSS variables for.
	 * @param array $attributes The block attributes.
	 * @param array $pefix Prefix for variable name
	 *
	 * @return array An associative array of CSS variable definitions.
	 */
	static function generate_css_variables( $settings, $attributes, $prefix ='') {
		$vars = array();

		foreach ( $settings as $key => $suffix ) {
			// Simple direct mapping
			if ( is_string( $suffix ) ) {
				if ( isset( $attributes[ $key ] ) && $attributes[ $key ] !== '' ) {
					$vars[ $prefix . $suffix ] = $attributes[ $key ];
				}
				continue;
			}
			// Mapping with 'var' key
			if ( isset( $suffix['var'] ) && isset( $attributes[ $key ] ) ) {
				$vars[ $prefix . $suffix['var'] ] = $attributes[ $key ];
			}
		}
		return $vars;
	}

	/**
	 * Normalize block attributes into CSS-ready values based on a settings schema.
	 *
	 * This function reads attribute values (including nested ones via dot notation),
	 * converts them according to their configured type (e.g. color, spacing, number,
	 * justify, border), and returns a flat array suitable for CSS variable generation.
	 *
	 * The same settings schema is intended to be shared between PHP and JS.
	 *
	 * @param array $settings   Map of attribute keys to normalization configuration.
	 *                          Each item may contain:
	 *                          - 'attr'   (string)  Path to attribute using dot notation.
	 *                          - 'var'    (string)  CSS variable name.
	 *                          - 'type'   (string)  Normalization type (color, spacing, number, justify, border, etc.).
	 *                          - 'unit'   (string)  Optional unit for numeric values.
	 *                          - 'reverse'(bool)    Optional flag for justify alignment.
	 * @param array $attributes Raw block attributes.
	 *
	 * @return array Flat array of normalized CSS values keyed by setting key (and
	 *               additional keys for composite types like border).
	 */
	public static function normalize_attributes_for_css(
		array $settings,
		array $attributes
	): array {
		$result = array();

		foreach ( $settings as $key => $config ) {
			$attr_path = $config['attr'] ?? $key;
			$value     = self::get_attr_by_path( $attributes, $attr_path );

			if ( $value === null ) {
				continue;
			}
			switch ( $config['type'] ) {
				case 'color':
					$value = ColorUtils::color_attribute_to_css( $value );
					break;
				case 'spacing':
					$value = (string) $value;
					break;
				case 'justify':
					$value = self::get_horizontal_alignment_by_attribute(
						$value,
						$config['reverse'] ?? false
					);
					break;
				case 'number':
					$value = isset( $config['unit'] )
						? $value . $config['unit']
						: (string) $value;
					break;
				case 'border':
					// Mutates border array in place
					ColorUtils::patch_border_colors( $attributes, $key );

					$border = $attributes[ $key ];
					$result[ $key ] = $border;

					foreach ( $border as $border_key => $border_value ) {
						if ( $border_value !== null ) {
							$result[ "{$key}-{$border_key}" ] = $border_value;
						}
					}
					break;
				default:
					$value = (string) $value;
			}
			$result[ $key ] = $value;
		}

		return $result;
	}

	/**
	 * Retrieve a nested attribute value using dot notation.
	 * @param array  $attributes
	 * @param string $path
	 *
	 * @return array|mixed|null
	 */
	 private static function get_attr_by_path( array $attributes, string $path ) {
		$parts = explode( '.', $path );
		$value = $attributes;

		foreach ( $parts as $part ) {
			if ( ! is_array( $value ) || ! array_key_exists( $part, $value ) ) {
				return null;
			}
			$value = $value[ $part ];
		}

		return $value;
	}

}
