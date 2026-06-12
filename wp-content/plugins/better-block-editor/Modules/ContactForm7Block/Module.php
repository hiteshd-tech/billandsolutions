<?php
/**
 * Module for Contact Form 7 block.
 *
 * @package BetterBlockEditor
 */

namespace BetterBlockEditor\Modules\ContactForm7Block;

use BetterBlockEditor\Base\ConfigurableModuleInterface;
use BetterBlockEditor\Base\ManagableModuleInterface;
use BetterBlockEditor\Core\BlockUtils;
use BetterBlockEditor\Base\ModuleBase;
use BetterBlockEditor\Core\Settings;
use BetterBlockEditor\Modules\StyleEngine\Module as StyleEngineModule;

defined( 'ABSPATH' ) || exit;

class Module extends ModuleBase implements ManagableModuleInterface, ConfigurableModuleInterface {

	const ASSETS_BUILD_PATH = 'blocks/contact-form-7/';

	const MODULE_IDENTIFIER = 'cf7block';

	const SETTINGS_ORDER   = 1500;

	const STRETCH_ALL_CLASS = 'has-stretch-all';

	const CSS_VAR_SETTINGS = array(
		'gap'                        => array(
			'attr' => 'style.spacing.blockGap',
			'var'  => 'gap',
			'type' => 'spacing',
		),
		'justification'              => array(
			'var'  => 'justify',
			'type' => 'justify',
		),
		'fieldFontSize'              => array(
			'var'  => 'font-size',
			'type' => 'number',
		),
		'fieldBorderRadius'          => array(
			'var'  => 'border-radius',
			'type' => 'number',
		),
		'fieldBorderWidth'           => array(
			'var'  => 'border-width',
			'type' => 'number',
		),
		'fieldSpacing'           => array(
			'var'  => 'spacing',
			'type' => 'spacing',
		),
		'fieldTextColor'             => array(
			'var'  => 'text-color',
			'type' => 'color',
		),
		'fieldBorderColor'           => array(
			'var'  => 'border-color',
			'type' => 'color',
		),
		'fieldBackgroundColor'       => array(
			'var'  => 'background-color',
			'type' => 'color',
		),
		'fieldTextAccentColor'       => array(
			'var'  => 'text-accent',
			'type' => 'color',
		),
		'buttonFontSize'             => array(
			'var'  => 'button-font-size',
			'type' => 'number',
		),
		'buttonFontWeight'           => array(
			'var'  => 'button-font-weight',
			'type' => 'number',
		),
		'buttonBorderRadius'         => array(
			'var'  => 'button-border-radius',
			'type' => 'number',
		),
		'buttonBorderWidth'          => array(
			'var'  => 'button-border-width',
			'type' => 'number',
		),
		'buttonTextColor'            => array(
			'var'  => 'button-text-color',
			'type' => 'color',
		),
		'buttonBackgroundColor'      => array(
			'var'  => 'button-background-color',
			'type' => 'color',
		),
		'buttonBorderColor'          => array(
			'var'  => 'button-border-color',
			'type' => 'color',
		),
		'buttonTextHoverColor'       => array(
			'var'  => 'button-text-hover-color',
			'type' => 'color',
		),
		'buttonBackgroundHoverColor' => array(
			'var'  => 'button-background-hover-color',
			'type' => 'color',
		),
		'buttonBorderHoverColor'     => array(
			'var'  => 'button-border-hover-color',
			'type' => 'color',
		),
		'msgFontSize'                => array(
			'var'  => 'msg-font-size',
			'type' => 'number',
		),
		'msgSpacing'           => array(
			'var'  => 'msg-spacing',
			'type' => 'spacing',
		),
		'mgsSuccessColor'            => array(
			'var'  => 'success',
			'type' => 'color',
		),
		'mgsWarningColor'            => array(
			'var'  => 'warning',
			'type' => 'color',
		),
		'mgsErrorColor'              => array(
			'var'  => 'error',
			'type' => 'color',
		),
	);

	const DESIGN_STYLES_OPTION = 'design-styles';
	const DESIGN_STYLES_CLASS  = 'has-wpbbe-cf7-styles';

	public static function is_active() {
		// Check if Contact Form 7 is active
		return class_exists( 'WPCF7' );
	}

	public function init() {
		register_block_type(
			WPBBE_BLOCKS_DIR . 'contact-form-7',
			array(
				'render_callback' => array(
					$this,
					'render',
				),
			)
		);
	}
	public function setup_hooks() {
		add_filter( 'wpbbe_script_data', array( $this, 'add_script_data' ) );
	}

	private function is_design_styles_enabled() {
		return (bool) $this->get_option( self::DESIGN_STYLES_OPTION, 1 );
	}

	public function add_script_data( $data ) {
		$data[ static::MODULE_IDENTIFIER ] = array(
			'editFormUrl'         => admin_url( 'admin.php?page=wpcf7&post=%d&action=edit' ),
			'designStylesEnabled' => $this->is_design_styles_enabled(),
			'cssVarSettings'      => self::CSS_VAR_SETTINGS,
		);
		return $data;
	}

	public static function get_tab() {
		return Settings::TAB_BLOCKS;
	}

	public static function get_title() {
		return __( 'BBE Contact Form 7', 'better-block-editor' );
	}

	public static function get_label() {
		return __( 'Enable BBE Contact Form 7 block.', 'better-block-editor' );
	}

	public function render( $attributes ) {
		$id = intval( $attributes['id'] ?? 0 );
		if ( ! $id ) {
			return '';
		}
		// Check if we are requesting preview via ServerSideRender in the editor
		$is_editor = ! empty( $_GET['__editor'] ) && '1' === $_GET['__editor'];
		$form_html  = do_shortcode( "[contact-form-7 id=\"{$id}\"]" );
		// output raw form in the editor preview
		if ( $is_editor ) {
			return $form_html;
		}

		$class_id        = BlockUtils::create_unique_class_id();
		$custom_classes  = array();

		$justification    = $attributes[ 'justification' ] ?? null;
		$has_stretch_fields = $attributes[ 'hasStretchFields' ] ?? null;

		if ( $justification === 'stretch' && $has_stretch_fields ) {
			$custom_classes[] = self::STRETCH_ALL_CLASS;
		}
		$base_classes    = array_merge( array( $class_id ), $custom_classes );
		$wrapper_classes = BlockUtils::append_block_wrapper_classes( $base_classes );
		// apply design system styles class
		if ( $this->is_design_styles_enabled() ) {
			$wrapper_classes[] = self::DESIGN_STYLES_CLASS;
		}

		$options = array(
			'context'  => 'core',
			'prettify' => false,
			'selector' => ".{$class_id}",
		);
		// apply native styles.
		$style = ( $attributes['style'] ?? array() );
		StyleEngineModule::get_styles( $style, $options );

		$attributesNormalized = BlockUtils::normalize_attributes_for_css( self::CSS_VAR_SETTINGS, $attributes );
		$vars                 = BlockUtils::generate_css_variables( self::CSS_VAR_SETTINGS, $attributesNormalized, '--form-' );
		if ( $vars ) {
			BlockUtils::add_styles_from_css_rules(
				array(
					array(
						'selector'     => '.' . $class_id,
						'declarations' => $vars,
					),
				)
			);
		}

		return sprintf(
			'<div class="%s">%s</div>',
			esc_attr( implode( ' ', $wrapper_classes ) ),
			$form_html
		);
	}

	public static function get_settings(): array {
		return array(
			self::DESIGN_STYLES_OPTION => array(
				'type'        => 'checkbox',
				'label'       => __( 'Enable form styling.', 'better-block-editor' ),
				'default'     => 1,
				'description' => __( 'When enabled, form styling settings become available in the BBE Contact Form 7 block. If disabled, default theme styles will be used.', 'better-block-editor' ),
			),
		);
	}
}
