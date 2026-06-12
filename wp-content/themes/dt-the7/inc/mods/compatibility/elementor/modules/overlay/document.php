<?php

namespace The7\Mods\Compatibility\Elementor\Modules\Overlay;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Css_Filter;
use Elementor\Plugin as Elementor;
use Elementor\Utils;
use ElementorPro\Modules\LoopBuilder\Files\Css\Loop_Dynamic_CSS;
use ElementorPro\Modules\LoopBuilder\Module as LoopBuilderModule;
use ElementorPro\Modules\ThemeBuilder\Documents\Theme_Document;
use ElementorPro\Plugin;
use The7\Mods\Compatibility\Elementor\Pro\Modules\Theme_Support\The7_Theme_Support;

defined( 'ABSPATH' ) || exit;

class Document extends Theme_Document {

	const RECOMMENDED_WIDGET_NAMES = [
		'text-editor',
		'divider',
		'spacer',
		'the7-heading',
		'the7-image-widget',
		'the7_svg_image_widget',
		'the7_icon_widget',
		'the7_button_widget',
		'the7-taxonomies',
		'the7-woocommerce-loop-add-to-cart',
		'the7-woocommerce-product-sale-flash',
		'the7-woocommerce-product-rating',
		'the7-woocommerce-product-price',
		'the7-woocommerce-product-meta',
	];

	/**
	 * Allows us to render the document even if it's empty, coz it can have some backgrounds.
	 *
	 * @param  string $status Document status.
	 *
	 * @return array
	 */
	public function get_elements_data( $status = \Elementor\Core\Base\Document::STATUS_PUBLISH ) {
		$elements_data = parent::get_elements_data( $status );

		// Force rendering of the document.
		if ( empty( $elements_data ) ) {
			return [ 'custom_data_to_force_rendering' => [ 'elType' => 'yes_please' ] ];
		}

		return $elements_data;
	}

	/**
	 * @param  array $data Document data.
	 *
	 * @throws \Exception If document type not found.
	 */
	public function __construct( array $data = [] ) {
		if ( $data ) {
			add_filter( 'body_class', [ $this, 'filter_body_classes' ] );
		}

		parent::__construct( $data );
	}

	/**
	 * @return array
	 */
	public static function get_properties() {
		$properties                              = parent::get_properties();
		$properties['admin_tab_group']           = 'library';
		$properties['support_site_editor']       = false; // in order to hide doc on "website" builder.
		$properties['support_wp_page_templates'] = true; // in order to use canvas template.
		$properties['support_conditions']        = false;
		$properties['support_kit']               = true;
		$properties['show_in_finder']            = false;

		return $properties;
	}

	/**
	 * @return string
	 */
	public static function get_title() {
		return esc_html__( 'The7 Overlay', 'the7mk2' );
	}

	/**
	 * @return string
	 */
	public static function get_plural_title() {
		return esc_html__( 'The7 Overlays', 'the7mk2' );
	}

	/**
	 * @return string
	 */
	public function get_location_label() {
		return '';
	}

	/**
	 * @param array $body_classes Body classes.
	 *
	 * @return array
	 */
	public function filter_body_classes( $body_classes ) {
		if ( get_the_ID() === $this->get_main_id() || Elementor::$instance->preview->is_preview_mode( $this->get_main_id() ) ) {
			$body_classes[] = $this->get_name();

			The7_Theme_Support::instance()->turn_off_header_and_footer();
		}

		return $body_classes;
	}

	/**
	 * @return string
	 */
	public function get_name() {
		return static::get_type();
	}

	/**
	 * @return string
	 */
	public static function get_type() {
		return Module::DOCUMENT_TYPE;
	}

	/**
	 * @return string
	 */
	public function get_css_wrapper_selector() {
		return '.elementor-' . $this->get_main_id();
	}

	/**
	 * @param array $data Document data.
	 *
	 * @return bool
	 */
	public function save( $data ) {
		$data['settings']['post_status'] = self::STATUS_PUBLISH;

		return parent::save( $data );
	}

	/**
	 * @return array
	 */
	public function get_initial_config() {
		$config = parent::get_initial_config();

		foreach ( $config['widgets'] as &$widget ) {
			// Hide all widgets except recommended.
			if ( $widget['elType'] === 'widget' && ! in_array( $widget['name'], self::RECOMMENDED_WIDGET_NAMES, true ) ) {
				$widget['show_in_panel'] = false;
			}
		}
		unset( $widget );

		return $config;
	}

	/**
	 * @return array[]
	 */
	public static function get_preview_as_options() {
		$post_types = \ElementorPro\Core\Utils::get_public_post_types();

		$post_types_options = [];

		foreach ( $post_types as $post_type => $label ) {
			$post_types_options[ 'single/' . $post_type ] = get_post_type_object( $post_type )->labels->singular_name;
		}

		return [
			'single' => [
				'label'   => esc_html__( 'Single', 'the7mk2' ),
				'options' => $post_types_options,
			],
		];
	}

	/**
	 * @param array $elements_data Element data.
	 *
	 * @return void
	 */
	public function print_elements_with_wrapper( $elements_data = null ) {
		if ( ! $elements_data ) {
			$elements_data = $this->get_elements_data();
		}
		?>
		<div <?php Utils::print_html_attributes( $this->get_container_attributes() ); ?>>
			<?php $this->print_dynamic_css( get_the_ID(), $this->get_main_id() ); ?>
			<?php $this->print_elements( $elements_data ); ?>
		</div>
		<?php
	}

	/**
	 * @param int $post_id Post ID.
	 * @param int $post_id_for_data Post ID for data.
	 *
	 * @return void
	 */
	protected function print_dynamic_css( $post_id, $post_id_for_data ) {
		// Get parent document.
        $document = Elementor::instance()->documents->get( $post_id );

        if ( ! $document ) {
            return;
        }

        Elementor::instance()->documents->switch_to_document( $document );

		$css_file = Loop_Dynamic_CSS::create( $post_id, $post_id_for_data );
		$post_css = $css_file->get_content();

		if ( $post_css ) {
			$css = str_replace( '.elementor-' . $post_id, '.the7-overlay-item-' . $post_id, $post_css );
			$css = sprintf( '<style id="%s">%s</style>', 'loop-dynamic-' . $post_id_for_data, $css );

			echo $css; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

        Elementor::instance()->documents->restore_document();
	}

	/**
	 * @return array
	 */
	public function get_container_attributes() {
		$attributes                            = parent::get_container_attributes();
		$attributes['class']                  .= ' the7-overlay-content the7-overlay-item-' . get_the_ID();
		$attributes['data-custom-edit-handle'] = true;

		return $attributes;
	}

	/**
	 * Override original `get_content` to prevent recursion
	 *
	 * @param bool $with_css Include CSS.
	 *
	 * @return string Megamenu HTML
	 */
	public function get_content( $with_css = false ) {
		if ( get_the_ID() === $this->get_main_id() ) {
			return '';
		}

		return parent::get_content();
	}

	/**
	 * @return void
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'the7_overlay_template_setting_section',
			[
				'label' => esc_html__( 'Content', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_SETTINGS,
			]
		);

		$selector = '.the7-overlay-container {{WRAPPER}}.the7-overlay-content, {{WRAPPER}}.the7-overlay-content .elementor-section-wrap:first-child';

		$this->add_responsive_control(
			'content_vertical_align',
			[
				'label'                => esc_html__( 'Vertical Alignment', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'default'              => 'center',
				'options'              => [
					'top'    => [
						'title' => esc_html__( 'Top', 'the7mk2' ),
						'icon'  => 'eicon-v-align-top',
					],
					'center' => [
						'title' => esc_html__( 'Middle', 'the7mk2' ),
						'icon'  => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => esc_html__( 'Bottom', 'the7mk2' ),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'selectors_dictionary' => [
					'top'    => 'flex-start',
					'bottom' => 'flex-end',
				],
				'selectors'            => [
					$selector => 'justify-content: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'the7_overlay_template_background_section',
			[
				'label' => esc_html__( 'Background', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_SETTINGS,
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'           => 'background',
				'types'          => [ 'classic', 'gradient' ],
				'exclude'        => [ 'attachment' ],
				'selector'       => $selector,
				'fields_options' => [
					'image' => [
						'dynamic' => [
							'active' => false,
						],
					],
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'the7_overlay_template_background_overlay_section',
			[
				'label' => esc_html__( 'Background Overlay', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_SETTINGS,
			]
		);

		$bg_overlay_selector = '.the7-overlay-container {{WRAPPER}}.the7-overlay-content:before, {{WRAPPER}}.the7-overlay-content .elementor-section-wrap:first-child:before';

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'           => 'background_overlay',
				'types'          => [ 'classic', 'gradient' ],
				'exclude'        => [ 'attachment' ],
				'fields_options' => [
					'background' => [
						'selectors' => [
							'{{SELECTOR}}' => 'content: ""',
						],
					],
					'image'      => [
						'dynamic' => [
							'active' => false,
						],
					],
				],
				'selector'       => $bg_overlay_selector,
			]
		);

		$this->add_responsive_control(
			'background_overlay_opacity',
			[
				'label'     => esc_html__( 'Opacity', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => .5,
				],
				'range'     => [
					'px' => [
						'max'  => 1,
						'step' => 0.01,
					],
				],
				'selectors' => [
					$bg_overlay_selector => 'opacity: {{SIZE}};',
				],
				'condition' => [
					'background_overlay_background' => [ 'classic', 'gradient' ],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'       => 'css_filters',
				'selector'   => $bg_overlay_selector,
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'background_overlay_image[url]',
							'operator' => '!==',
							'value'    => '',
						],
						[
							'name'     => 'background_overlay_color',
							'operator' => '!==',
							'value'    => '',
						],
					],
				],
			]
		);

		$this->add_control(
			'overlay_blend_mode',
			[
				'label'      => esc_html__( 'Blend Mode', 'the7mk2' ),
				'type'       => Controls_Manager::SELECT,
				'options'    => [
					''            => esc_html__( 'Normal', 'the7mk2' ),
					'multiply'    => esc_html__( 'Multiply', 'the7mk2' ),
					'screen'      => esc_html__( 'Screen', 'the7mk2' ),
					'overlay'     => esc_html__( 'Overlay', 'the7mk2' ),
					'darken'      => esc_html__( 'Darken', 'the7mk2' ),
					'lighten'     => esc_html__( 'Lighten', 'the7mk2' ),
					'color-dodge' => esc_html__( 'Color Dodge', 'the7mk2' ),
					'saturation'  => esc_html__( 'Saturation', 'the7mk2' ),
					'color'       => esc_html__( 'Color', 'the7mk2' ),
					'luminosity'  => esc_html__( 'Luminosity', 'the7mk2' ),
					'difference'  => esc_html__( 'Difference', 'the7mk2' ),
					'exclusion'   => esc_html__( 'Exclusion', 'the7mk2' ),
					'hue'         => esc_html__( 'Hue', 'the7mk2' ),
				],
				'selectors'  => [
					$bg_overlay_selector => 'mix-blend-mode: {{VALUE}}',
				],
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'background_overlay_image[url]',
							'operator' => '!==',
							'value'    => '',
						],
						[
							'name'     => 'background_overlay_color',
							'operator' => '!==',
							'value'    => '',
						],
					],
				],
			]
		);

		$this->end_controls_section();

		parent::register_controls();

		$this->remove_control( 'content_wrapper_html_tag' );

		$this->update_preview_control();

		$this->inject_width_control();

		Plugin::elementor()->controls_manager->add_custom_css_controls( $this );
	}

	/**
	 * @return void
	 */
	protected function inject_width_control() {
		$this->start_injection(
			[
				'type' => 'section',
				'at'   => 'start',
				'of'   => 'preview_settings',
			]
		);

		$this->add_responsive_control(
			'preview_width',
			[
				'label'      => esc_html__( 'Width', 'elementor-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'range'      => [
					'px' => [
						'min' => 200,
						'max' => 1140,
					],
				],
				'selectors'  => [
					'{{WRAPPER}}' => '--preview-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_injection();
	}

	/**
	 * @return void
	 */
	protected function update_preview_control() {
		$loop_builder_module = new LoopBuilderModule();
		$source_type         = $loop_builder_module->get_source_type_from_post_meta( $this->get_main_id() );

		$this->update_control(
			'preview_type',
			[
				'default' => 'single/' . $source_type,
				'label'   => esc_html__( 'Preview a specific post or item', 'elementor-pro' ),
			]
		);

		$latest_posts = get_posts(
			[
				'posts_per_page' => 1,
				'post_type'      => $source_type,
			]
		);

		if ( ! empty( $latest_posts ) ) {
			$this->update_control(
				'preview_id',
				[
					'default' => $latest_posts[0]->ID,
				]
			);
		}
	}

}
