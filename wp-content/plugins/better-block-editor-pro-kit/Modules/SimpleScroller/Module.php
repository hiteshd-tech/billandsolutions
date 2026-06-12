<?php
/**
 * Simple Scroller module.
 *
 * @package BbeProKit
 */

namespace BbeProKit\Modules\SimpleScroller;

use BetterBlockEditor\Core\BlockUtils;
use BetterBlockEditor\Core\ColorUtils;
use BetterBlockEditor\Core\CssMediaBreakpoints;
use BetterBlockEditor\Modules\InlineSVG\InlineSVGRenderer;
use BbeProKit\Plugin;
use BbeProKit\Base\ModuleBasePro;
use BetterBlockEditor\Base\ManagableModuleInterface;
use BetterBlockEditor\Modules\StyleEngine\Module as StyleEngineModule;

defined( 'ABSPATH' ) || exit;

class Module extends ModuleBasePro implements ManagableModuleInterface {

	const MODULE_IDENTIFIER = 'simple-scroller';

	// we need it to create multipurpose scroller package
	const ASSETS_BUILD_PATH = 'libs/multipurpose-scroller/';

	const BLOCK_NAME_ARROW_RIGHT = 'wpbbe/simple-scroller-arrow-right';
	const BLOCK_NAME_ARROW_LEFT  = 'wpbbe/simple-scroller-arrow-left';

	const UPLOAD_SVG_FEATURE = 'upload-svg';

	const RESPONSIVE_ATTRIBUTES = 'wpbbeResponsive';

	const SCROLLER_CONTENT_CSS_VARS = array(
		'items'             => 'items',
		'itemMinWidth'      => 'item-min-width',
		'itemMinHeight'     => 'item-min-height',
		'itemGap'           => 'item-gap',
		'justifyContent'    => 'item-justify',
		'verticalAlignment' => 'item-alignment',
		'scrollPadding'     => 'scroll-padding',
		'scrollSnapAlign'   => 'scroll-snap-mode',
	);

	const SCROLLER_INDICATOR_CSS_VARS = array(
		'trackWidth'          => 'progress-width',
		'trackHeight'         => 'progress-height',
		'indicatorHeight'     => 'indicator-height',
		'indicatorColor'      => 'indicator-color',
		'trackColor'          => 'progress-color',
		'alignment'           => 'progress-alignment',
		'overlapTopOffset'    => 'overlap-top-offset',
		'overlapBottomOffset' => 'overlap-bottom-offset',
		'overlapPosition'     => 'overlap-position',
		'overlapWidth'        => 'overlap-width',
	);

	const SCROLLER_ARROW_CSS_VARS = array(
		'overlapLeftOffset'  => 'overlap-left-offset',
		'overlapRightOffset' => 'overlap-right-offset',
		'overlapPosition'    => 'overlap-position',
	);

	const SVG_ICON_LEFT = '<svg
		xmlns="http://www.w3.org/2000/svg"
		width="68"
		height="68"
		fill="#000000"
		viewBox="0 0 256 256"
	>
		<path d="M168.49,199.51a12,12,0,0,1-17,17l-80-80a12,12,0,0,1,0-17l80-80a12,12,0,0,1,17,17L97,128Z"></path>
	</svg>';

	const SVG_ICON_RIGHT = '<svg
		xmlns="http://www.w3.org/2000/svg"
		width="68"
		height="68"
		fill="#000000"
		viewBox="0 0 256 256"
	>
		<path d="M184.49,136.49l-80,80a12,12,0,0,1-17-17L159,128,87.51,56.49a12,12,0,1,1,17-17l80,80A12,12,0,0,1,184.49,136.49Z"></path>
	</svg>';

	const PROGRESS_TRACK_WRAPPER = 'nsProgressTrackWrapper';

	public function init() {
		parent::init();
		register_block_type(
			BBE_PRO_KIT_BLOCKS_DIR . 'simple-scroller',
			array(
				'render_callback' => array( $this, 'render_scroller' ),
			)
		);

		register_block_type(
			BBE_PRO_KIT_BLOCKS_DIR . 'simple-scroller-content',
			array(
				'render_callback' => array( $this, 'render_scroller_content' ),
			)
		);

		register_block_type(
			BBE_PRO_KIT_BLOCKS_DIR . 'simple-scroller-indicator',
			array(
				'render_callback' => array( $this, 'render_scroller_indicator' ),
			)
		);

		register_block_type(
			BBE_PRO_KIT_BLOCKS_DIR . 'simple-scroller-arrow-left',
			array(
				'render_callback' => array( $this, 'render_arrow' ),
			)
		);
		register_block_type(
			BBE_PRO_KIT_BLOCKS_DIR . 'simple-scroller-arrow-right',
			array(
				'render_callback' => array( $this, 'render_arrow' ),
			)
		);
	}

	public static function get_title() {
		return __( 'Scroller', 'better-block-editor' );
	}

	public static function get_label() {
		return __( 'A scrollable container for different blocks.', 'better-block-editor' );
	}

	// region multipurpose scroller package

	/**
	 * rewrite to match handle in webpack settings
	 *
	 * @see parent::build_script_handle() comment
	 */
	protected function build_script_handle( $key ) {
		return 'wpbbe-multipurpose-scroller';
	}

	/**
	 * Add a package that is included in dependency lists
	 */
	protected function process_assets() {
		parent::process_assets();//TODO remove this because we load lib manually

		$asset_file = require $this->get_assets_full_path() . 'index.asset.php';

		wp_register_script(
			$this->build_script_handle( 'index' ),
			BBE_PRO_KIT_URL_DIST . $this::ASSETS_BUILD_PATH . 'index.js',
			$asset_file['dependencies'],
			$asset_file['version'],
			array(
				// 'strategy'  => 'defer',
				'in_footer' => true,
			)
		);

		wp_register_style(
			$this->build_style_handle( 'index' ),
			BBE_PRO_KIT_URL_DIST . $this::ASSETS_BUILD_PATH . 'index.css',
			array(),
			$asset_file['version']
		);

		add_action(
			is_admin() ? 'enqueue_block_assets' : 'wp_enqueue_scripts',
			function () {
				$this->enqueue_assets( 'index' );
			}
		);
	}
	// endregion multipurpose scroller package

	/**
	 * Generate CSS variables based on the settings and attributes.
	 *
	 * @param array $settings The settings to generate CSS variables for.
	 * @param array $attributes The block attributes.
	 *
	 * @return array An associative array of CSS variable definitions.
	 */
	private function generate_css_variables( $settings, $attributes ) {
		$var_definitions = array();

		foreach ( $settings as $key => $suffix ) {
			if ( isset( $attributes[ $key ] ) && $attributes[ $key ] !== '' ) {
				$var_definitions[ "--ns-$suffix" ] = $attributes[ $key ];
			}
		}

		return $var_definitions;
	}

	/**
	 * Get responsive settings from the block for scroller block.
	 *
	 * @param array $attributes The block attributes.
	 *
	 * @return array An associative array containing responsive settings.
	 */
	private function get_scroller_responsive_settings( $attributes ) {
		$responsive = $attributes[ self::RESPONSIVE_ATTRIBUTES ] ?? array();

		$breakpoint              = $responsive['breakpoint'] ?? null;
		$breakpoint_custom_value = $responsive['breakpointCustomValue'] ?? null;

		$settings = $responsive['settings'] ?? array();

		return array(
			'breakpoint'            => $breakpoint,
			'breakpointCustomValue' => $breakpoint_custom_value,
			'settings'              => array(
				'isWideWidth' => $settings['isWideWidth'] ?? null,
			),
		);
	}

	/**
	 * Get responsive settings from the block  for content block.
	 *
	 * @param array $attributes The block attributes.
	 *
	 * @return array An associative array containing responsive settings.
	 */
	private function get_content_responsive_settings( $attributes ) {
		$responsive = $attributes[ self::RESPONSIVE_ATTRIBUTES ] ?? array();

		$breakpoint              = $responsive['breakpoint'] ?? null;
		$breakpoint_custom_value = $responsive['breakpointCustomValue'] ?? null;

		$settings = $responsive['settings'] ?? array();

		return array(
			'breakpoint'            => $breakpoint,
			'breakpointCustomValue' => $breakpoint_custom_value,
			'settings'              => array(
				'items'           => $settings['items'] ?? null,
				'itemMinWidth'    => $settings['itemMinWidth'] ?? null,
				'itemMinHeight'   => $settings['itemMinHeight'] ?? null,
				'itemGap'         => $settings['itemGap'] ?? null,
				'scrollPadding'   => $settings['scrollPadding'] ?? null,
				'scrollSnapAlign' => $settings['scrollSnapAlign'] ?? null,
			),
		);
	}

	/**
	 * Get overlap responsive settings from the block
	 *
	 * @param array $attributes The block attributes.
	 *
	 * @return array An associative array containing responsive settings.
	 */
	private function get_overlap_responsive_settings( $attributes ) {
		$responsive = $attributes[ self::RESPONSIVE_ATTRIBUTES ] ?? array();

		$breakpoint              = $responsive['breakpoint'] ?? null;
		$breakpoint_custom_value = $responsive['breakpointCustomValue'] ?? null;

		$settings = $responsive['settings'] ?? array();

		return array(
			'breakpoint'            => $breakpoint,
			'breakpointCustomValue' => $breakpoint_custom_value,
			'settings'              => array(
				'overlap'       => $settings['overlap'] ?? null,
				'overlapOffset' => $settings['overlapOffset'] ?? null,
			),
		);
	}

	/**
	 * Render the scroller indicator block.
	 *
	 * @param array  $attributes The block attributes.
	 * @param string $content The block content.
	 * @param object $block The block instance.
	 *
	 * @return string The rendered scroller indicator block.
	 */
	public function render_scroller_indicator( $attributes, $content ) {
		$class_id = BlockUtils::create_unique_class_id();

		$base_classes = array( self::PROGRESS_TRACK_WRAPPER, $class_id );

		$overlap = $attributes['overlap'] ?? '';
		if ( in_array( $overlap, array( 'top', 'bottom' ), true ) ) {
			$base_classes[] = 'ns-overlap-' . $overlap;
		} else {
			unset( $attributes['overlapOffset'] );
		}

		$wrapper_classes = BlockUtils::append_block_wrapper_classes( $base_classes );

		$options = array(
			'context'  => 'core',
			'prettify' => false,
			'selector' => ".nsProgressTrackWrapper.{$class_id}",
		);
		// apply native styles.
		$style = $attributes['style'] ?? array();
		StyleEngineModule::get_styles( $style, $options );

		$attributes = $this->prepare_color_attributes_for_indicator( $attributes );

		// apply styles for indicator
		$options = array(
			'context'  => 'core',
			'prettify' => false,
			'selector' => ".nsProgressTrackWrapper.{$class_id} .nsProgressIndicator",
		);
		$style   = array();
		if ( isset( $attributes['indicatorBorder'] ) ) {
			$style['border'] = $attributes['indicatorBorder'];
		}
		StyleEngineModule::get_styles( $style, $options );

		// apply styles for track
		$options = array(
			'context'  => 'core',
			'prettify' => false,
			'selector' => ".nsProgressTrackWrapper.{$class_id} .nsProgressTrack",
		);
		$style   = array();
		if ( isset( $attributes['trackBorder'] ) ) {
			$style['border'] = $attributes['trackBorder'];
		}
		StyleEngineModule::get_styles( $style, $options );
		$attributes = $this->apply_overlap_attributes_indicator( $attributes );
		$vars       = $this->generate_css_variables( self::SCROLLER_INDICATOR_CSS_VARS, $attributes );
		if ( $vars ) {
			BlockUtils::add_styles_from_css_rules(
				array(
					array(
						'selector'     => '.nsProgressTrackWrapper.' . $class_id,
						'declarations' => $vars,
					),
				)
			);
		}
		$responsive_attributes = $this->get_overlap_responsive_settings( $attributes );
		$switch_width          = CssMediaBreakpoints::getSwitchWidth( $responsive_attributes['breakpoint'], $responsive_attributes['breakpointCustomValue'] );
		if ( $switch_width ) {
			$responsive_attributes_settings = $this->apply_overlap_attributes_indicator( $responsive_attributes['settings'], true );
			$vars                           = $this->generate_css_variables( self::SCROLLER_INDICATOR_CSS_VARS, $responsive_attributes_settings );
			if ( $vars ) {
				BlockUtils::add_styles_from_css_rules(
					array(
						array(
							'selector'     => "@media screen and (width <= {$switch_width})",
							'declarations' => array(
								array(
									'selector'     => ".nsProgressTrackWrapper.{$class_id}.{$class_id}",
									'declarations' => $vars,
								),
							),
						),
					)
				);
			}
		}
		ob_start();
		?>
		<div class="<?php echo esc_attr( implode( ' ', $wrapper_classes ) ); ?>">
			<div class="nsProgressTrack">
				<div class="nsProgressIndicator"></div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render the scroller block.
	 *
	 * @param array  $attributes The block attributes.
	 * @param string $content The block content.
	 * @param object $block The block instance.
	 *
	 * @return string The rendered block.
	 */
	public function render_scroller( $attributes, $content ) {
		$class_id = BlockUtils::get_unique_class_id( $content );
		$content  = BlockUtils::append_classes( $content, $class_id );

		$align = isset( $attributes['align'] ) && $attributes['align'];
		// If align is 'full', we don't need to add any styles.
		if ( $align === 'full' ) {
			return $content;
		}

		$responsive_attributes = $this->get_scroller_responsive_settings( $attributes );
		$switch_width          = CssMediaBreakpoints::getSwitchWidth( $responsive_attributes['breakpoint'], $responsive_attributes['breakpointCustomValue'] );
		if ( $switch_width ) {
			$isWideWidth = $responsive_attributes['settings']['isWideWidth'] ?? false;
			if ( $isWideWidth ) {
				BlockUtils::add_styles_from_css_rules(
					array(
						array(
							'selector'     => "@media screen and (width <= {$switch_width})",
							'declarations' => array(
								array(
									'selector'     => 'body .' . $class_id,
									'declarations' => array( 'max-width' => 'none !important' ),
								),
								array(
									'selector'     => '.has-global-padding > .' . $class_id,
									'declarations' => array(
										'margin-right:' => 'calc(var(--wp--style--root--padding-right) * -1) !important',
										'margin-left:'  => 'calc(var(--wp--style--root--padding-left) * -1) !important',
									),
								),
								array(
									'selector'     => '.has-global-padding :where(:not(.alignfull.is-layout-flow) > .has-global-padding:not(.wp-block-block, .alignfull)) > .' . $class_id,
									'declarations' => array(
										'margin-right:' => '0 !important',
										'margin-left:'  => '0 !important',
									),
								),
							),
						),
					)
				);
			}
		}
		return $content;
	}

	/**
	 * Render the scroller content block.
	 *
	 * @param array  $attributes The block attributes.
	 * @param string $content The block content.
	 * @param object $block The block instance.
	 *
	 * @return string The rendered block.
	 */
	public function render_scroller_content( $attributes, $content ) {
		$class_id = BlockUtils::create_unique_class_id();

		$base_classes    = array( 'nsContent', $class_id );
		$wrapper_classes = BlockUtils::append_block_wrapper_classes( $base_classes );

		$options = array(
			'context'  => 'core',
			'prettify' => false,
			'selector' => ".nsContent.{$class_id}",
		);
		// apply native styles.
		$style = $attributes['style'] ?? array();
		StyleEngineModule::get_styles( $style, $options );
		isset( $attributes['itemGap'] ) &&  $attributes['itemGap'] === "0" ? $attributes['itemGap'] = '0px' : null; //apply unit to avoid invalid css calculation
		$vars = $this->generate_css_variables( self::SCROLLER_CONTENT_CSS_VARS, $attributes );
		if ( $vars ) {
			BlockUtils::add_styles_from_css_rules(
				array(
					array(
						'selector'     => 'body .' . $class_id,
						'declarations' => $vars,
					),
				)
			);
		}

		$responsive_attributes = $this->get_content_responsive_settings( $attributes );

		$switch_width = CssMediaBreakpoints::getSwitchWidth( $responsive_attributes['breakpoint'], $responsive_attributes['breakpointCustomValue'] );

		if ( $switch_width ) {
				$vars = $this->generate_css_variables( self::SCROLLER_CONTENT_CSS_VARS, $responsive_attributes['settings'] );
			if ( $vars ) {
				BlockUtils::add_styles_from_css_rules(
					array(
						array(
							'selector'     => "@media screen and (width <= {$switch_width})",
							'declarations' => array(
								array(
									'selector'     => "body .{$class_id}.{$class_id}",
									'declarations' => $vars,
								),
							),
						),
					)
				);
			}
		}

		return sprintf(
			'<div tabindex="0" class="%s">%s</div>',
			esc_attr( implode( ' ', $wrapper_classes ) ),
			$content
		);
	}

	/**
	 * Render the arrows block.
	 *
	 * @param array  $attributes The block attributes.
	 * @param string $content The block content.
	 * @param object $block The block instance.
	 *
	 * @return string The rendered arrow block.
	 */
	public function render_arrow( $attributes, $content, $block ) {
		$block_name       = $block->name;
		$class_id         = BlockUtils::create_unique_class_id();
		$image_exist      = isset( $attributes['imageID'] ) && $attributes['imageID'];
		$fallback_content = '';
		if ( ! $image_exist || ! Plugin::instance()->is_feature_active( self::UPLOAD_SVG_FEATURE ) ) {
			if ( self::BLOCK_NAME_ARROW_LEFT === $block_name ) {
				$fallback_content = self::SVG_ICON_LEFT;
			} elseif ( self::BLOCK_NAME_ARROW_RIGHT === $block_name ) {
				$fallback_content = self::SVG_ICON_RIGHT;
			}
		}
		$renderer       = new InlineSVGRenderer();
		$custom_classes = array( 'nsArrow' );

		$attributes = $this->apply_overlap_attributes_arrow( $attributes );
		$vars       = $this->generate_css_variables( self::SCROLLER_ARROW_CSS_VARS, $attributes );
		if ( $vars ) {
			BlockUtils::add_styles_from_css_rules(
				array(
					array(
						'selector'     => '.nsArrow.' . $class_id,
						'declarations' => $vars,
					),
				)
			);
		}
		$responsive_attributes = $this->get_overlap_responsive_settings( $attributes );
		$switch_width          = CssMediaBreakpoints::getSwitchWidth( $responsive_attributes['breakpoint'], $responsive_attributes['breakpointCustomValue'] );
		if ( $switch_width ) {
			$responsive_attributes_settings = $this->apply_overlap_attributes_arrow( $responsive_attributes['settings'], true );
			$vars                           = $this->generate_css_variables( self::SCROLLER_ARROW_CSS_VARS, $responsive_attributes_settings );
			if ( $vars ) {
				BlockUtils::add_styles_from_css_rules(
					array(
						array(
							'selector'     => "@media screen and (width <= {$switch_width})",
							'declarations' => array(
								array(
									'selector'     => ".nsArrow.{$class_id}.{$class_id}",
									'declarations' => $vars,
								),
							),
						),
					)
				);
			}
		}

		if ( self::BLOCK_NAME_ARROW_LEFT === $block_name ) {
			$custom_classes[] = 'nsLeftArrow';
		} elseif ( self::BLOCK_NAME_ARROW_RIGHT === $block_name ) {
			$custom_classes[] = 'nsRightArrow';
		}
		$style                           = $this->collect_arrow_style_metadata( $attributes );
		$options                         = array(
			'context'  => 'core',
			'prettify' => false,
			'selector' => ".{$class_id}.nsArrow.nsDisabled",
		);
		$options['definitions_metadata'] = array(
			'color' => array(
				'opacity' => array(
					'property_keys' => array( 'default' => 'opacity' ),
					'path'          => array(
						'color',
						'opacity',
					),
				),
			),
		);
		StyleEngineModule::get_styles( $style, $options );
		return $renderer->render( $attributes, $custom_classes, $fallback_content, $class_id );
	}

	/**
	 * Collects style metadata from the attributes.
	 *
	 * @param array $attributes The block attributes.
	 *
	 * @return array The collected style metadata.
	 */
	private function collect_arrow_style_metadata( array $attributes ) {
		$style = array();

		if ( isset( $attributes['inactiveOpacity'] ) ) {
			$style['color']['opacity'] = (string) $attributes['inactiveOpacity'];
		}
		return $style;
	}

	/**
	 * Prepares and normalizes color-related attributes for the indicator component.
	 *
	 * Converts any color presets or values (e.g., slugs, theme tokens) into valid CSS color values
	 * for both top-level color attributes and nested border color values.
	 *
	 * @param array $attributes Block attributes to normalize.
	 * @return array Normalized attributes with CSS color values.
	 */
	private function prepare_color_attributes_for_indicator( array $attributes ) {
		// Patch top-level color attributes
		foreach ( array( 'trackColor', 'indicatorColor' ) as $color_key ) {
			if ( isset( $attributes[ $color_key ] ) ) {
				$attributes[ $color_key ] = ColorUtils::color_attribute_to_css( $attributes[ $color_key ] );
			}
		}

		// Patch nested color values in indicatorBorder
		ColorUtils::patch_border_colors( $attributes, 'indicatorBorder' );
		ColorUtils::patch_border_colors( $attributes, 'trackBorder' );

		return $attributes;
	}

	/**
	 * Apply overlap attributes for the arrow block.
	 *
	 * @param array $attributes The block attributes.
	 * @param bool  $is_responsive Whether the attributes are for responsive design.
	 *
	 * @return array The modified attributes with overlap properties applied.
	 */
	private function apply_overlap_attributes_arrow( array $attributes, $is_responsive = false ): array {
		$overlap = $attributes['overlap'] ?? '';

		if ( 'left' === $overlap || 'right' === $overlap ) {
			$is_left = 'left' === $overlap;

			$overlap_offset = ( isset( $attributes['overlapOffset'] ) && '' !== $attributes['overlapOffset'] )
				? $attributes['overlapOffset']
				: '0px';

			$attributes['overlapLeftOffset']  = $is_left ? $overlap_offset : 'auto';
			$attributes['overlapRightOffset'] = $is_left ? 'auto' : $overlap_offset;
			$attributes['overlapPosition']    = 'absolute';
		} elseif ( $is_responsive && '' === $overlap ) {
			$attributes['overlapPosition']    = 'relative';
			$attributes['overlapLeftOffset']  = 'auto';
			$attributes['overlapRightOffset'] = 'auto';
		}

		return $attributes;
	}

	/**
	 * Apply overlap attributes for the indicator block.
	 *
	 * @param array $attributes The block attributes.
	 * @param bool  $is_responsive Whether the attributes are for responsive design.
	 *
	 * @return array The modified attributes with overlap properties applied.
	 */
	private function apply_overlap_attributes_indicator( array $attributes, $is_responsive = false ): array {
		$overlap = $attributes['overlap'] ?? '';

		if ( 'top' === $overlap || 'bottom' === $overlap ) {
			$is_top         = 'top' === $overlap;
			$overlap_offset = ( isset( $attributes['overlapOffset'] ) && '' !== $attributes['overlapOffset'] )
				? $attributes['overlapOffset']
				: '0px';

			$attributes['overlapTopOffset']    = $is_top ? $overlap_offset : 'auto';
			$attributes['overlapBottomOffset'] = $is_top ? 'auto' : $overlap_offset;
			$attributes['overlapPosition']     = 'absolute';
			$attributes['overlapWidth']        = '100%';
		} elseif ( $is_responsive && '' === $overlap ) {
				$attributes['overlapPosition']     = 'relative';
				$attributes['overlapWidth']        = 'auto';
				$attributes['overlapTopOffset']    = 'auto';
				$attributes['overlapBottomOffset'] = 'auto';
		}

		return $attributes;
	}
}
