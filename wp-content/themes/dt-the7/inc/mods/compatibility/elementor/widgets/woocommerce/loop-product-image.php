<?php
/**
 * Loop product image widget.
 *
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widgets\Woocommerce;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Icons_Manager;
use Elementor\Plugin as Elementor;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;
use ElementorPro\Modules\DynamicTags\Tags\Base\Data_Tag;
use ElementorPro\Modules\DynamicTags\Module;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Arrows;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Bullets;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Image_Size;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Image_Transform;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Overlay_Template;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Image_Aspect_Ratio;

defined( 'ABSPATH' ) || exit;

/**
 * Class Loop_Product_Image.
 */
class Loop_Product_Image extends The7_Elementor_Widget_Base {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'the7-woocommerce-loop-product-image';
	}

	/**
	 * @return string
	 */
	protected function the7_title() {
		return esc_html__( 'Loop Product Image', 'the7mk2' );
	}

	/**
	 * @return string
	 */
	protected function the7_icon() {
		return 'eicon-product-images';
	}

	/**
	 * @return array|string[]
	 */
	protected function the7_categories() {
		return [ Module::IMAGE_CATEGORY ];
	}

	/**
	 * @return string[]
	 */
	public function get_style_depends() {
		return [ $this->get_name() ];
	}

	/**
	 * @return string[]
	 */
	public function get_script_depends() {
		return [ $this->get_name() ];
	}

	/**
	 * Register widget assets.
	 */
	protected function register_assets() {
		the7_register_script_in_footer(
			$this->get_name(),
			THE7_ELEMENTOR_JS_URI . '/the7-woocommerce-product-image-loop.js',
			[ 'the7-elementor-frontend-common' ]
		);
		the7_register_style(
			$this->get_name(),
			THE7_ELEMENTOR_CSS_URI . '/the7-woocommerce-product-image-loop',
			['the7-slider-widget']
		);
	}

	/**
	 * @return string[]
	 */
	protected function the7_keywords() {
		return [ 'woocommerce', 'shop', 'store', 'image', 'product', 'gallery', 'lightbox', 'loop' ];
	}

	/**
	 * @return void
	 */
	public function render() {
		global $product;

		$product = wc_get_product();
		if ( ! $product ) {
			return;
		}

		$this->add_container_class_render_attribute( 'wrapper' );

		$settings = $this->get_settings_for_display();
		if ( $settings['layout'] === 'slider' ) {
			$this->template( Arrows::class )->add_container_render_attributes( 'wrapper' );

			$class  = $this->template( Image_Transform::class )->get_wrapper_class();
			$class .= ' ' . $this->get_swiper_container_class();

			echo '<div class="elementor-slides-wrapper ' . esc_attr( $class ) . '">';

			$overlay_template_id = $this->template( Overlay_Template::class )->get_template_id();
			if ( $overlay_template_id ) {
				wp_enqueue_script( 'the7-overlay-template' );
				echo $this->template( Overlay_Template::class )->get_render(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			// Render image in wrapper.
			echo '<div ' . $this->get_render_attribute_string( 'wrapper' ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				$this->render_product_image( $product );
			echo '</div>';

			echo '</div>';

			echo '<div class="swiper-pagination owl-dots ' . $this->add_bullets_class() . '"></div>';
			$this->render_arrows( $settings );
		} else {
			echo '<div ' . $this->get_render_attribute_string( 'wrapper' ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				$this->render_product_image( $product );
			echo '</div>';
		}
	}

	/**
	 * @param string $element Element name.
	 *
	 * @return void
	 */
	protected function add_container_class_render_attribute( $element ) {
		$settings = $this->get_settings_for_display();

		$class[] = 'the7-woocommerce-loop-product-image';
		if ( $settings['layout'] === 'hover_image' ) {
			$class[] = 'wc-img-hover';
		} elseif ( $settings['layout'] === 'slider' ) {
			$class[] = 'swiper-wrapper the7-elementor-slides';
		}

		// Unique class.
		$class[] = $this->get_unique_class();

		$this->add_render_attribute( $element, 'class', $class );
	}

	/**
	 * @return string
	 */
	protected function get_swiper_container_class() {
		return Elementor::$instance->experiments->is_feature_active( 'e_swiper_latest' ) ? 'swiper' : 'swiper-container';
	}

	/**
	 * @param $settings
	 *
	 * @return void
	 */
	protected function render_arrows( $settings ) {
		?>
		<div class="the7-swiper-button the7-swiper-button-prev elementor-icon">
			<?php
			Icons_Manager::render_icon( $this->get_settings_for_display( 'prev_icon' ) );
			?>
		</div>
		<div class="the7-swiper-button the7-swiper-button-next elementor-icon">
			<?php
			Icons_Manager::render_icon( $this->get_settings_for_display( 'next_icon' ) )
			?>
		</div>
		<?php
	}

	/**
	 * @param $class
	 *
	 * @return mixed|string
	 */
	protected function add_bullets_class( $class = '' ) {
		$settings      = $this->get_settings_for_display();
		$style_classes = [
			'small-dot-stroke' => 'bullets-small-dot-stroke',
			'scale-up'         => 'bullets-scale-up',
			'stroke'           => 'bullets-stroke',
			'fill-in'          => 'bullets-fill-in',
			'ubax'             => 'bullets-ubax',
			'etefu'            => 'bullets-etefu',
		];

		$layout = $settings['bullets_style'];
		if ( array_key_exists( $layout, $style_classes ) ) {
			$class = $style_classes[ $layout ];
		}

		return $class;
	}

	/**
	 * @param \WC_Product $product Product.
	 */
	protected function render_product_image( $product ) {
		$settings            = $this->get_settings_for_display();
		$wrapper_class       = '';
		$wrapper_link        = '';
		$overlay_template_id = $this->template( Overlay_Template::class )->get_template_id();

		$img_wrapper_class = implode(
			' ',
			array_filter(
				[
					'post-thumbnail-rollover',
					$this->template( Image_Size::class )->get_wrapper_class(),
					$this->template( Image_Aspect_Ratio::class )->get_wrapper_class(),
				]
			)
		);
		if ( $overlay_template_id ) {
			$wrapper_class = $this->template( Overlay_Template::class )->get_wrapper_class();
		}
		$wrapper_class .= ' ' . $this->template( Image_Transform::class )->get_wrapper_class();

		if ( $settings['layout'] === 'slider' ) {
			$gallery_image_ids = (array) $product->get_gallery_image_ids();

			// Add featured image to the beginning of the list.
			if ( $product->get_image_id() ) {
				array_unshift( $gallery_image_ids, $product->get_image_id() );
			}

			// Filter out empty ids.
			if ( (int) $settings['dis_images_total'] ) {
				$gallery_image_ids = array_slice( $gallery_image_ids, 0, (int) $settings['dis_images_total'] );
			}
			foreach ( $gallery_image_ids as $gallery_image_id ) {
				echo '<div class="woocom-project trigger-img-hover the7-image-wrapper the7-swiper-slide">';
					$this->render_wrapped_image(
						$img_wrapper_class,
						$gallery_image_id,
						function ( $gallery_image_id ) {
							echo $this->template( Image_Size::class )->get_image( $gallery_image_id, [ 'class' => '' ] );
						}
					);

				echo '</div>';
			}
		} else {
			echo '<div class="woocom-project trigger-img-hover the7-image-wrapper ' . $wrapper_class . ' ">';

				if ( $overlay_template_id ) {
					wp_enqueue_script( 'the7-overlay-template' );
					echo $this->template( Overlay_Template::class )->get_render( (int) $product->get_image_id() );
				}
			 // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				$this->render_wrapped_image(
					$img_wrapper_class,
					$product->get_image_id(),
					function () use ( $product, $settings ) {
						echo $this->template( Image_Size::class )->apply_filters(
							function ( $size ) use ( $product, $settings ) {
								$result = woocommerce_get_product_thumbnail( $size );
								if ( $settings['layout'] === 'hover_image' ) {
										$result .= the7_wc_get_the_first_product_gallery_image_html( $product, $size, $class = 'show-on-hover' );
								}
								return $result;
							}
						);
					}
				);
			echo '</div>';
		}

		if ( ! $product->is_in_stock() ) {
			echo '<span class="out-stock-label">' . esc_html__( 'Out Of Stock', 'the7mk2' ) . '</span>';
		}
	}

	/**
	 * Retrieve image widget link URL.
	 *
	 * @param array $settings Widget settings.
	 *
	 * @return array|string|false An array/string containing the link URL, or false if no link.
	 */
	protected function get_link_url( $settings, $img_id ) {
		if ( ! $settings['link_to'] || $settings['link_to'] === 'none' ) {
			return false;
		}

		if ( $settings['link_to'] === 'custom' ) {
			if ( empty( $settings['image_link']['url'] ) ) {
				return false;
			}

			return $settings['image_link'];
		}

		return [
			'url' => wp_get_attachment_url( $img_id ),
		];
	}

	/**
	 * @param string   $wrapper_class
	 * @param int      $img_id
	 * @param callable $render_callback
	 *
	 * @return void
	 */
	protected function render_wrapped_image( $wrapper_class, $img_id, $render_callback ) {
		$settings = $this->get_settings_for_display();
		$link     = $this->get_link_url( $settings, $img_id );
		if ( $link ) {
			$this->remove_render_attribute( 'link' );
			$this->add_link_attributes( 'link', $link );
			if ( $settings['link_to'] === 'file' && ! empty( $img_id ) ) {
				$this->add_lightbox_data_attributes( 'link', $img_id, $settings['open_lightbox'] );
			}
			$image_wrapper       = '<a class="' . esc_attr( $wrapper_class ) . '" ' . $this->get_render_attribute_string( 'link' ) . '>';
			$image_wrapper_close = '</a>';
		} else {
			$image_wrapper       = '<div class="' . esc_attr( $wrapper_class ) . '">';
			$image_wrapper_close = '</div>';
		}
		echo $image_wrapper;

		$render_callback( $img_id );

		echo $image_wrapper_close;
	}

	/**
	 * @return void
	 */
	protected function register_controls() {
		$this->add_gallery_content_controls();
		$this->add_scrolling_content_controls();
		$this->template( Overlay_Template::class )->add_controls();
		$this->template( Arrows::class )->add_content_controls(
			[
				'layout' => 'slider',
			]
		);
		$this->remove_control( 'arrows_position' );
		$this->template( Bullets::class )->add_content_controls(
			[
				'layout' => 'slider',
			]
		);

		// Style tab.
		$this->add_image_style_controls();

		$this->start_injection(
			[
				'type' => 'control',
				'at'   => 'after',
				'of'   => 'item_ratio',
			]
		);
		$this->end_injection();

		$this->template( Image_Transform::class )->add_style_controls(
			[
				'layout!' => 'slider',
			]
		);
		$this->template( Arrows::class )->add_style_controls(
			[
				'layout' => 'slider',
			]
		);
		$this->template( Bullets::class )->add_style_controls(
			[
				'layout' => 'slider',
			]
		);
	}

	/**
	 * @return void
	 */
	protected function add_gallery_content_controls() {
		$this->start_controls_section(
			'gallery_section',
			[
				'label' => esc_html__( 'Image ', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'layout',
			[
				'label'       => esc_html__( 'Layout', 'the7mk2' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => '',
				'options'     => [
					''            => esc_html__( 'Single Image', 'the7mk2' ),
					'hover_image' => esc_html__( 'Hover Image', 'the7mk2' ),
					'slider'      => esc_html__( 'Slider', 'the7mk2' ),
				],
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'_wrap_helper',
			[
				'type'         => Controls_Manager::HIDDEN,
				'default'      => 'elementor-widget-the7-slider-common the7-overlay-container',
				'prefix_class' => '',
				'condition'    => [
					'layout' => 'slider',
				],
			]
		);

		$this->template( Image_Size::class )->add_style_controls();

		$this->add_control(
			'dis_images_total',
			[
				'label'       => esc_html__( 'Total Number Of Images', 'the7mk2' ),
				'description' => esc_html__( 'Leave empty to display all images.', 'the7mk2' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => '12',
				'condition'   => [
					'layout' => 'slider',
				],
			]
		);

		$this->add_control(
			'image_link_heading',
			[
				'label'     => esc_html__( 'Link & Hover', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'link_to',
			[
				'label'   => esc_html__( 'Link', 'the7mk2' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'none'   => esc_html__( 'None', 'the7mk2' ),
					'file'   => esc_html__( 'Media File', 'the7mk2' ),
					'custom' => esc_html__( 'Custom URL', 'the7mk2' ),
				],
			]
		);

		$this->add_control(
			'image_link',
			[
				'label'       => esc_html__( 'Link', 'the7mk2' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => [
					'active' => true,
				],
				'condition'   => [
					'link_to' => 'custom',
				],
				'placeholder' => esc_html__( 'https://your-link.com', 'the7mk2' ),
			]
		);

		$this->add_control(
			'open_lightbox',
			[
				'label'       => esc_html__( 'Lightbox', 'the7mk2' ),
				'type'        => Controls_Manager::SELECT,
				'description' => sprintf(
					/* translators: 1: Link open tag, 2: Link close tag. */
					esc_html__( 'Manage your site’s lightbox settings in the %1$sLightbox panel%2$s.', 'the7mk2' ),
					'<a href="javascript: $e.run( \'panel/global/open\' ).then( () => $e.route( \'panel/global/settings-lightbox\' ) )">',
					'</a>'
				),
				'default'     => 'default',
				'options'     => [
					'default' => esc_html__( 'Default', 'the7mk2' ),
					'yes'     => esc_html__( 'Yes', 'the7mk2' ),
					'no'      => esc_html__( 'No', 'the7mk2' ),
				],
				'condition'   => [
					'link_to' => 'file',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Add slider controls.
	 */
	protected function add_scrolling_content_controls() {
		$this->start_controls_section(
			'scrolling_section',
			[
				'label'     => esc_html__( 'Scrolling', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'layout' => 'slider',
				],
			]
		);

		$this->add_control(
			'transition',
			[
				'label'              => esc_html__( 'Transition', 'the7mk2' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => 'slide',
				'options'            => [
					'slide' => esc_html__( 'Slide', 'the7mk2' ),
					'fade'  => esc_html__( 'Fade', 'the7mk2' ),
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'transition_speed',
			[
				'label'              => esc_html__( 'Transition Speed', 'the7mk2' ) . '(ms)',
				'type'               => Controls_Manager::NUMBER,
				'default'            => 600,
				'frontend_available' => true,
				'selectors'          => [
					'{{WRAPPER}}' => '--transition-speed: {{SIZE}}ms;',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Add image style controls.
	 */
	protected function add_image_style_controls() {
		$this->start_controls_section(
			'section_design_image',
			[
				'label' => esc_html__( 'Image', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->template( Image_Aspect_Ratio::class )->add_style_controls();

		$this->add_control(
			'image_style_title',
			[
				'label'     => esc_html__( 'Style', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'image_padding',
			[
				'label'      => esc_html__( 'Padding', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .the7-image-wrapper img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'image_border',
				'selector' => '{{WRAPPER}} .the7-image-wrapper img',
				'exclude'  => [ 'color' ],
			]
		);

		$this->add_control(
			'image_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .the7-image-wrapper, {{WRAPPER}} .the7-image-wrapper img, {{WRAPPER}} .the7-overlay-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'image_effects_tabs' );

		$this->start_controls_tab(
			'normal',
			[
				'label' => esc_html__( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'           => 'overlay_background',
				'types'          => [ 'classic', 'gradient' ],
				'exclude'        => [ 'image' ],
				'fields_options' => [
					'background' => [
						'label' => esc_html__( 'Overlay', 'the7mk2' ),
					],
				],
				'selector'       => '{{WRAPPER}} .post-thumbnail-rollover:before, {{WRAPPER}} .post-thumbnail-rollover:after { transition: none; }
				{{WRAPPER}} .post-thumbnail-rollover:before,
				{{WRAPPER}} .post-thumbnail-rollover:after
				',
			]
		);

		$this->add_control(
			'image_border_color',
			[
				'label'     => esc_html__( 'Border', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} img' => 'border-color: {{VALUE}}',
				],
				'condition' => [
					'image_border_border!' => [ '', 'none' ],
				],
			]
		);

		$this->add_control(
			'image_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .the7-image-wrapper img' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'image_shadow',
				'selector' => '{{WRAPPER}} img',
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'     => 'image_filters',
				'selector' => '{{WRAPPER}} img',
			]
		);

		$this->add_control(
			'image_opacity',
			[
				'label'      => esc_html__( 'Opacity', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} img:not(.show-on-hover)' => 'opacity: calc({{SIZE}}/100)',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'hover',
			[
				'label' => esc_html__( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'           => 'overlay_hover_background',
				'types'          => [ 'classic', 'gradient' ],
				'exclude'        => [ 'image' ],
				'fields_options' => [
					'background' => [
						'label' => esc_html__( 'Overlay', 'the7mk2' ),
					],
					'color'      => [
						'selectors' => [
							'
							{{SELECTOR}},
							{{WRAPPER}} .woocom-project:hover .post-thumbnail-rollover:before { transition: all var(--transition-overlay-duration, 0.3s) var(--transition-overlay-timing, ease); } {{WRAPPER}} .woocom-project:hover .post-thumbnail-rollover:after { transition: all var(--transition-overlay-duration, 0.3s); } {{WRAPPER}} .woocom-project:not(:hover) .post-thumbnail-rollover:before { transition: all var(--transition-overlay-duration-out, 0.3s) var(--transition-overlay-timing, ease); }{{WRAPPER}} .woocom-project:not(:hover) .post-thumbnail-rollover:after { transition: all var(--transition-overlay-duration-out, 0.3s) var(--transition-overlay-timing, ease); } {{SELECTOR}}' => 'background: {{VALUE}};',
						],
					],

				],
				'selector'       => '{{WRAPPER}} .post-thumbnail-rollover:after',
			]
		);

		$this->add_control(
			'image_hover_border_color',
			[
				'label'     => esc_html__( 'Border', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}}:hover img' => 'border-color: {{VALUE}}',
				],
				'condition' => [
					'image_border_border!' => [ '', 'none' ],
				],
			]
		);

		$this->add_control(
			'image_hover_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}}:hover img' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'image_hover_shadow',
				'selector' => '{{WRAPPER}}:hover img',
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'     => 'image_hover_filters',
				'selector' => '{{WRAPPER}}:hover img',
			]
		);

		$this->add_control(
			'image_hover_opacity',
			[
				'label'      => esc_html__( 'Opacity', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}}:hover img:not(.show-on-hover)' => 'opacity: calc({{SIZE}}/100)',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}
}
