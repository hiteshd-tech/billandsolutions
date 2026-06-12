<?php
/**
 * Allow applying heading typography styles to other blocks
 *
 * @package BetterBlockEditor
 */

namespace BetterBlockEditor\Modules\TextStyleFromElement;

use BetterBlockEditor\Base\ModuleBase;
use BetterBlockEditor\Base\ManagableModuleInterface;
use BetterBlockEditor\Core\BlockUtils;
use BetterBlockEditor\Modules\StyleEngine\Module as StyleEngineModule;

defined( 'ABSPATH' ) || exit;

class Module extends ModuleBase implements ManagableModuleInterface {

	const MODULE_IDENTIFIER = 'text-style-from-element';
	const ASSETS_BUILD_PATH = 'editor/blocks/__all__/text-style-from-element/';

	const SETTINGS_ORDER = 930;

	const ATTRIBUTE_TEXT_STYLE   = 'wpbbeTextStyleFromElement';
	const ATTRIBUTE_ROLE_HEADING = 'wpbbeRoleHeading';

	const BLOCK_NAMES = array( 'core/post-title', 'core/post-excerpt', 'core/heading', 'core/paragraph' );

	const HEADING_ELEMENTS     = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' );
	const ALL_HEADINGS_ELEMENT = 'heading';

	// this class is used in WP core to apply styles to all headings
	const WP_CORE_HEADING_CLASSNAME = 'wp-block-heading';

	const TEXT_STYLE_CLASSNAME_PREFIX = 'wpbbe-text-style-from-element-';

	public function init(): void {

		parent::init();

		$this->set_text_styling_classes();
	}

	public function setup_hooks(): void {
		add_filter( 'render_block', array( $this, 'render' ), 20, 3 );
	}

	/**
	 * Generate text styling css classes based on theme.json styles
	 * saves the styles to be displayed later in the stylesheet with id="wpbbe-core-inline-css"
	 * duplicate class names to increase specificity and override other styles
	 */
	public function set_text_styling_classes() {
		// we do need merged theme json data here
		$theme_json_object = \WP_Theme_JSON_Resolver::get_merged_data();
		$theme_json_data = $theme_json_object->get_data();

		// settings from the "All Headings" element
		$all_headings_styling_data = $this->get_text_style_to_borrow(
			$theme_json_data['styles']['elements'][ self::ALL_HEADINGS_ELEMENT ] ?? array()
		);

		if ( ! empty( $all_headings_styling_data ) ) {
			$selectors = array();
			foreach(self::HEADING_ELEMENTS as $heading_element) {
				$selectors[] = '.' . self::TEXT_STYLE_CLASSNAME_PREFIX . $heading_element;
			}
			StyleEngineModule::get_styles(
				$all_headings_styling_data,
				array(
					'selector' =>  implode( ', ',
						array_map(
							function ( $heading_element ) {
								return str_repeat('.' . self::TEXT_STYLE_CLASSNAME_PREFIX . $heading_element, 2);
							},
							self::HEADING_ELEMENTS
						)
					),
					'context'  => 'core',
				)
			);
		}


		// individual heading (h1, h2, etc.) settings
		foreach ( $theme_json_data['styles']['elements'] ?? array() as $element_name => $element_settings ) {
			if ( in_array( $element_name, self::HEADING_ELEMENTS, true ) ) {
				$heading_element_styling_data = $this->get_text_style_to_borrow( $element_settings );

				if ( empty( $heading_element_styling_data ) ) {
					continue;
				}

				StyleEngineModule::get_styles(
					$heading_element_styling_data,
					array(
						'selector' => str_repeat('.' . self::TEXT_STYLE_CLASSNAME_PREFIX . $element_name, 2),
						'context'  => 'core',
					)
				);
			}
		}

		// add plain text (paragraph) styles
		$plain_text_styling_data = $this->get_text_style_to_borrow( $theme_json_data['styles'] ?? array() );

		if ( ! empty( $plain_text_styling_data ) ) {
			StyleEngineModule::get_styles(
				$plain_text_styling_data,
				array(
					'selector' => str_repeat('.' . self::TEXT_STYLE_CLASSNAME_PREFIX . 'p', 2),
					'context'  => 'core',
				)
			);
		}
	}

	public function render( $block_content, $block ): string {
		if ( ! in_array( $block['blockName'] ?? null, self::BLOCK_NAMES ) || $block_content === '' ) {
			return $block_content;
		}

		if ( true === ( $block['attrs'][ self::ATTRIBUTE_ROLE_HEADING ] ?? false ) ) {
			$block_content = BlockUtils::set_attribute( $block_content, 'role', 'heading' );
		}

		$use_style_from_element = $block['attrs'][ self::ATTRIBUTE_TEXT_STYLE ] ?? null;

		if ( null === $use_style_from_element ) {
			return $block_content;
		}

		$cssClasses = array(self::TEXT_STYLE_CLASSNAME_PREFIX . $use_style_from_element);

		if ( in_array( $use_style_from_element, self::HEADING_ELEMENTS, true ) ) {
			// also add the core heading class if the style is taken from a heading element
			$cssClasses[] = self::WP_CORE_HEADING_CLASSNAME;
		}

		$block_content = BlockUtils::append_classes( $block_content, $cssClasses);

		return $block_content;
	}

	public static function get_title(): string {
		return __( 'Change Style for Text Blocks', 'better-block-editor' );
	}

	public static function get_label(): string {
		return __( 'Add a "Change style" setting for Heading, Paragraph, Post Title, and Post Excerpt blocks. Select a visual style (H1–H6, Paragraph) for text blocks without changing their HTML tag.', 'better-block-editor' );
	}

	/**
	 * Extracts typography and text color styles from a theme.json section
	 *
	 * @param array $theme_data_section The theme.json section to extract styles from.
	 * @return array The extracted styles to borrow.
	 */
	private function get_text_style_to_borrow(array $theme_data_section): array {
		$styles_to_borrow = array();

		if ( ! empty( $theme_data_section['typography'] ?? array() ) ) {
			$styles_to_borrow['typography'] = $theme_data_section['typography'];
		}

		return $styles_to_borrow;
	}
}
