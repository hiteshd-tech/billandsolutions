<?php
/**
 * Style Engine module.
 *
 * @package BetterBlockEditor
 */

namespace BetterBlockEditor\Modules\StyleEngine;

use BetterBlockEditor\Base\ModuleBase;

defined( 'ABSPATH' ) || exit;

class Module extends ModuleBase {

	const MODULE_IDENTIFIER = 'core-style-engine';
	const IS_CORE_MODULE    = true;

	public function setup_hooks() {
		// Block supports, and other styles parsed and stored in the Style Engine.
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_stored_styles' ), 20 );
		add_action( 'wp_footer', array( $this, 'wp_enqueue_stored_styles' ), 2 );
	}

	/**
	 * This is not standard tool but created for wpbbe features implementation.
	 * We need to convert somehow presets provided in attributes to CSS variables and
	 * there is no core function for it.
	 */
	public static function preprocess_css_rules( $css_rules ) {
		$return_value = array();

		foreach ( $css_rules as $css_rule ) {
			$preprocessed_css_rule = array(
				'selector' => $css_rule['selector'],
			);

			foreach ( $css_rule['declarations'] as $property => $value ) {
				// we use media query so have few levels and need recursion
				if ( is_array( $value ) && is_array( $value['declarations'] ?? null ) ) {
					$value = self::preprocess_css_rules( array( $value ) )[0];
				} else {
					// Convert preset identifier to CSS variable
					$value = preg_replace( '/var:preset\|(.+)\|([a-z0-9-]+)/', 'var(--wp--preset--$1--$2)', $value );
				}

				$preprocessed_css_rule['declarations'][ $property ] = $value;
			}

			$return_value[] = $preprocessed_css_rule;
		}

		return $return_value;
	}

	/**
	 * Global public interface method to generate styles from a single style object,
	 * e.g. the value of a block's attributes.style object or the top level styles in theme.json.
	 * Example usage:
	 *     $styles = wp_style_engine_get_styles(
	 *         array(
	 *             'color' => array( 'text' => '#cccccc' ),
	 *         )
	 *     );
	 * Returns:
	 *     array(
	 *         'css'          => 'color: #cccccc',
	 *         'declarations' => array( 'color' => '#cccccc' ),
	 *         'classnames'   => 'has-color',
	 *     )
	 *
	 * @param array $block_styles               The style object.
	 * @param array $options                    {
	 *                                          Optional. An array of options. Default empty array.
	 *
	 * @type string|null $context                    An identifier describing the origin of the style object,
	 *                                                   e.g. 'block-supports' or 'global-styles'. Default null.
	 *                                                   When set, the style engine will attempt to store the CSS rules,
	 *                                                   where a selector is also passed.
	 * @type bool        $convert_vars_to_classnames Whether to skip converting incoming CSS var patterns,
	 *                                                   e.g. `var:preset|<PRESET_TYPE>|<PRESET_SLUG>`,
	 *                                                   to `var( --wp--preset--* )` values. Default false.
	 * @type string      $selector                   Optional. When a selector is passed,
	 *                                                   the value of `$css` in the return value will comprise
	 *                                                   a full CSS rule `$selector { ...$css_declarations }`,
	 *                                                   otherwise, the value will be a concatenated string
	 *                                                   of CSS declarations.
	 * }
	 * @return array {
	 * @type string      $css                        A CSS ruleset or declarations block
	 *                                  formatted to be placed in an HTML `style` attribute or tag.
	 * @type string[]    $declarations               An associative array of CSS definitions,
	 *                                  e.g. `array( "$property" => "$value", "$property" => "$value" )`.
	 * @type string      $classnames                 Classnames separated by a space.
	 * }
	 * @since 6.1.0
	 * @see   https://developer.wordpress.org/block-editor/reference-guides/theme-json-reference/theme-json-living/#styles
	 * @see   https://developer.wordpress.org/block-editor/reference-guides/block-api/block-supports/
	 */
	public static function get_styles( $block_styles, $options = array() ) {
		$options = wp_parse_args(
			$options,
			array(
				'selector'                   => null,
				'context'                    => null,
				'convert_vars_to_classnames' => false,
			)
		);

		$parsed_styles = StyleEngine::parse_block_styles( $block_styles, $options );

		// Output.
		$styles_output = array();

		if ( ! empty( $parsed_styles['declarations'] ) ) {
			$styles_output['css']          = StyleEngine::compile_css( $parsed_styles['declarations'], $options['selector'] );
			$styles_output['declarations'] = $parsed_styles['declarations'];
			if ( ! empty( $options['context'] ) ) {
				StyleEngine::store_css_rule( $options['context'], $options['selector'], $parsed_styles['declarations'] );
			}
		}

		if ( ! empty( $parsed_styles['classnames'] ) ) {
			$styles_output['classnames'] = implode( ' ', array_unique( $parsed_styles['classnames'] ) );
		}

		return array_filter( $styles_output );
	}

	/**
	 * Returns compiled CSS from a collection of selectors and declarations.
	 * Useful for returning a compiled stylesheet from any collection of CSS selector + declarations.
	 * Example usage:
	 *     $css_rules = array(
	 *         array(
	 *             'selector'     => '.elephant-are-cool',
	 *             'declarations' => array(
	 *                 'color' => 'gray',
	 *                 'width' => '3em',
	 *             ),
	 *         ),
	 *     );
	 *     $css = wp_style_engine_get_stylesheet_from_css_rules( $css_rules );
	 * Returns:
	 *     .elephant-are-cool{color:gray;width:3em}
	 *
	 * @param array $css_rules    {
	 *                            Required. A collection of CSS rules.
	 *
	 * @type array ...$0 {
	 * @type string      $selector     A CSS selector.
	 * @type string[]    $declarations An associative array of CSS definitions,
	 *                                      e.g. `array( "$property" => "$value", "$property" => "$value" )`.
	 *     }
	 * }
	 *
	 * @param array $options      {
	 *                            Optional. An array of options. Default empty array.
	 *
	 * @type string|null $context      An identifier describing the origin of the style object,
	 *                                 e.g. 'block-supports' or 'global-styles'. Default 'block-supports'.
	 *                                 When set, the style engine will attempt to store the CSS rules.
	 * @type bool        $optimize     Whether to optimize the CSS output, e.g. combine rules.
	 *                                 Default false.
	 * @type bool        $prettify     Whether to add new lines and indents to output.
	 *                                 Defaults to whether the `SCRIPT_DEBUG` constant is defined.
	 * }
	 * @return string A string of compiled CSS declarations, or empty string.
	 * @since 6.1.0
	 */
	public static function get_stylesheet_from_css_rules( $css_rules, $options = array() ) {
		if ( empty( $css_rules ) ) {
			return '';
		}

		$options = wp_parse_args(
			$options,
			array(
				'context' => null,
			)
		);

		$css_rule_objects = array();
		foreach ( $css_rules as $css_rule ) {
			if ( empty( $css_rule['selector'] ) || empty( $css_rule['declarations'] ) || ! is_array( $css_rule['declarations'] ) ) {
				continue;
			}

			if ( ! empty( $options['context'] ) ) {
				StyleEngine::store_css_rule( $options['context'], $css_rule['selector'], $css_rule['declarations'] );
			}

			$css_rule_objects[] = new CSSRule( $css_rule['selector'], $css_rule['declarations'] );
		}

		if ( empty( $css_rule_objects ) ) {
			return '';
		}

		return StyleEngine::compile_stylesheet_from_css_rules( $css_rule_objects, $options );
	}

	/**
	 * Fetches, processes and compiles stored core styles, then combines and renders them to the page.
	 * Styles are stored via the style engine API.
	 *
	 * @link  https://developer.wordpress.org/block-editor/reference-guides/packages/packages-style-engine/
	 * @since 6.1.0
	 *
	 * @param array $options  {
	 *                        Optional. An array of options to pass to wp_style_engine_get_stylesheet_from_context().
	 *                        Default empty array.
	 *
	 * @type bool   $optimize Whether to optimize the CSS output, e.g., combine rules.
	 *                          Default false.
	 * @type bool   $prettify Whether to add new lines and indents to output.
	 *                          Default to whether the `SCRIPT_DEBUG` constant is defined.
	 * }
	 */
	function wp_enqueue_stored_styles( $options = array() ) {
		$is_block_theme   = wp_is_block_theme();
		$is_classic_theme = ! $is_block_theme;

		/*
		 * For block themes, this function prints stored styles in the header.
		 * For classic themes, in the footer.
		 */
		if ( ( $is_block_theme && doing_action( 'wp_footer' ) ) || ( $is_classic_theme && doing_action( 'wp_enqueue_scripts' ) ) ) {
			return;
		}

		$core_styles_keys         = array( 'core' );
		$compiled_core_stylesheet = '';
		$style_tag_id             = 'wpbbe';
		// Adds comment if code is prettified to identify core styles sections in debugging.
		$should_prettify = isset( $options['prettify'] ) ? true === $options['prettify'] : defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG;
		foreach ( $core_styles_keys as $style_key ) {

			if ( $should_prettify ) {
				$compiled_core_stylesheet .= "/**\n * wpbbe block styles: $style_key\n */\n";
			}
			// Chains core store ids to signify what the styles contain.
			$style_tag_id             .= '-' . $style_key;
			$compiled_core_stylesheet .= self::get_stylesheet_from_context( $style_key, $options );
		}

		// Combines Core styles.
		if ( ! empty( $compiled_core_stylesheet ) ) {
			// use null as version coz we don't have file to version (only content of <style> tag)
			wp_register_style( $style_tag_id, false, array(), null, 'all' ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
			wp_add_inline_style( $style_tag_id, $compiled_core_stylesheet );
			wp_enqueue_style( $style_tag_id );
		}
	}

	/**
	 * Returns compiled CSS from a store, if found.
	 *
	 * @param string $context  A valid context name, corresponding to an existing store key.
	 * @param array  $options  {
	 *                         Optional. An array of options. Default empty array.
	 *
	 * @type bool    $optimize Whether to optimize the CSS output, e.g. combine rules.
	 *                          Default false.
	 * @type bool    $prettify Whether to add new lines and indents to output.
	 *                          Defaults to whether the `SCRIPT_DEBUG` constant is defined.
	 * }
	 * @return string A compiled CSS string.
	 * @since 6.1.0
	 */
	public static function get_stylesheet_from_context( $context, $options = array() ) {
		return StyleEngine::compile_stylesheet_from_css_rules( StyleEngine::get_store( $context )->get_all_rules(), $options );
	}
}
