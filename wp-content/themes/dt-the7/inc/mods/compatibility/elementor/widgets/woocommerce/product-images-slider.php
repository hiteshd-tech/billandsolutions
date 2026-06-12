<?php

namespace The7\Mods\Compatibility\Elementor\Widgets\Woocommerce;

use Elementor\Controls_Manager;
use Elementor\Core\Breakpoints\Manager as Breakpoints;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Icons_Manager;
use Elementor\Embed;
use Elementor\Plugin as Elementor;
use Elementor\Plugin;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Image_Aspect_Ratio;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Image_Size;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Arrows;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Bullets;

defined( 'ABSPATH' ) || exit;

class Product_Images_Slider extends The7_Elementor_Widget_Base {
	const SLIDES_PER_VIEW_DEFAULT = '1';

	public function get_name() {
		return 'the7-woocommerce-product-images-slider';
	}

	protected function the7_title() {
		return esc_html__( 'Product Images Slider', 'the7mk2' );
	}

	protected function the7_icon() {
		return 'eicon-slides';
	}

	protected function the7_categories() {
		return [ 'woocommerce-elements-single' ];
	}

	public function get_style_depends() {
		return [ $this->get_name() ];
	}

	/**
	 * @return string[]
	 */
	public function get_script_depends() {
		return [ 'the7-slider', 'wc-single-product', 'the7-elementor-frontend-common' ];
	}

	/**
	 * Register widget assets.
	 */
	protected function register_assets() {
		the7_register_style(
			$this->get_name(),
			THE7_ELEMENTOR_CSS_URI . '/the7-woocommerce-product-image-slider',
			[ 'the7-simple-grid', 'the7-slider-widget' ]
		);
	}

	protected function the7_keywords() {
		return [ 'woocommerce', 'shop', 'store', 'image', 'product', 'gallery', 'lightbox', 'slider' ];
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		global $product;

		$product = wc_get_product();
		if ( ! $product ) {
			return;
		}

		$layout_class           = '';
		$video_lightbox_disable = '';
		$gallery_image_ids      = (array) $product->get_gallery_image_ids();
		$slider_wrap_class      = $this->get_swiper_container_class();
		if ( $settings['open_lightbox'] === 'y' && $settings['video_autoplay'] ) {
			$video_lightbox_disable = 'dt-pswp-item-no-click';
		}

		// Add featured image to the beginning of the list.
		if ( $product->get_image_id() ) {
			array_unshift( $gallery_image_ids, $product->get_image_id() );
		}

		$gallery_image_ids = array_map(
			static function ( $attachment ) {
				return apply_filters( 'wpml_object_id', $attachment, 'attachment', true );
			},
			array_filter( $gallery_image_ids )
		);

		$img_wrapper_class = implode(
			' ',
			array_filter(
				[
					$this->template( Image_Size::class )->get_wrapper_class(),
					$this->template( Image_Aspect_Ratio::class )->get_wrapper_class(),
				]
			)
		);
		if ( count( $gallery_image_ids ) === 1 && $settings['hide_one_img_thumbs'] === 'y' ) {
			$layout_class = ' one-product-img';
		}
		$this->add_render_attribute( '_wrapper', 'class', $layout_class );
		?>
		<div class="elementor-swiper">
			<div class="dt-product-gallery elementor-slides-wrapper <?php echo esc_attr( $slider_wrap_class ); ?>">
				<div class="swiper-wrapper the7-elementor-slides">
					<?php
					foreach ( $gallery_image_ids as $gallery_image_id ) {

						$video_url = presscore_get_image_video_url( $gallery_image_id );
						if ( ! empty( $video_url ) && $settings['enable_video'] === 'yes' ) {
							// Overlay.
							$video_overlay_html = '';
							if ( ! $settings['video_autoplay'] || ! $settings['play_on_mobile'] ) {
								$play_icon = '';
								if ( $settings['video_icon']['value'] !== '' ) {
									$play_icon = '<span class="play-icon elementor-icon">' . Icons_Manager::try_get_icon_html( $settings['video_icon'] ) . '</span>';
								}
								$video_overlay = wp_get_attachment_url( $gallery_image_id );
								$this->remove_render_attribute( 'the7-video-overlay' );
								$this->add_render_attribute(
									'the7-video-overlay',
									[
										'style' => 'background-image: url(' . $video_overlay . ');',
										'class' => 'the7-video-overlay',
									]
								);
								$video_overlay_html = '<div ' . $this->get_render_attribute_string( 'the7-video-overlay' ) . '>' . $play_icon . '</div>';
							}

							if ( $settings['open_lightbox'] === 'y' ) {
								$video_wrapper       = '<a href="' . $video_url . '" class="gallery-video-wrap  dt-pswp-item pswp-video ' . $video_lightbox_disable . ' ">';
								$video_wrapper_close = '</a>';
							} else {
								$video_wrapper       = '<div class="gallery-video-wrap">';
								$video_wrapper_close = '</div>';
							}

							$video_properties = Embed::get_video_properties( $video_url );
							if ( $video_properties ) {
								$embed_params  = $this->get_embed_params( $gallery_image_id );
								$embed_options = $this->get_embed_options();
								$video_attrs   = [ 'loading' => 'lazy' ];
								$video_html    = Embed::get_embed_html( $video_url, $embed_params, $embed_options, $video_attrs );
							} else {
								$attrs = [
									'class'       => 'elementor-video',
									'src'         => $video_url,
									'playsinline' => 'yes',
									'poster'      => $video_overlay,
									'muted'       => $settings['mute'],
									'loop'        => $settings['loop'],
									'controls'    => $settings['controls'],
								];
								$this->remove_render_attribute( 'video' );
								$this->add_render_attribute(
									'video',
									array_filter( $attrs )
								);

								$video_html = '<video ' . $this->get_render_attribute_string( 'video' ) . '></video>';
							}

							$video = $video_wrapper . $video_html . $video_overlay_html . $video_wrapper_close;
							echo '<div class="the7-swiper-slide"><div class="the7-image-wrapper ">' . $video . '</div></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						} else {
							echo '<div class="the7-swiper-slide"><div class="the7-image-wrapper">';
							$this->render_wrapped_image(
								$img_wrapper_class,
								$gallery_image_id,
								function ( $gallery_image_id ) {
									$img_att  = [ 'class' => '' ];
									$full_src = wp_get_attachment_image_src( $gallery_image_id, 'full' );
									if ( $full_src && isset( $full_src[0], $full_src[1], $full_src[2] ) ) {
										$img_att['data-src']                = esc_url( $full_src[0] );
										$img_att['data-large_image']        = esc_url( $full_src[0] );
										$img_att['data-large_image_width']  = esc_attr( $full_src[1] );
										$img_att['data-large_image_height'] = esc_attr( $full_src[2] );
										$img_att['data-caption']            = _wp_specialchars( get_post_field( 'post_excerpt', $gallery_image_id ), ENT_QUOTES, 'UTF-8', true );
									}
									echo $this->template( Image_Size::class )->get_image( $gallery_image_id, $img_att );
								}
							);
							echo '</div>';
							echo '</div>';
						}
					}
					?>
				</div>
				<div class="swiper-pagination owl-dots <?php echo esc_attr( $this->add_bullets_class() ); ?>"></div>
				<?php $this->render_arrows(); ?>
			</div>
		</div>
		<div class="thumbs-swiper">
			<div class="thumbs-slides-wrapper swiper elementor-slides-wrapper">
				<div class="swiper-wrapper the7-thumbs-slides the7-elementor-slides">
					<?php
					foreach ( $gallery_image_ids as $gallery_image_id ) {

						// Video icon.
						$play_icon = '';
						$video_url = presscore_get_image_video_url( $gallery_image_id );
						if ( $video_url && $settings['enable_video'] === 'yes' ) {
							$play_icon = '<span aria-hidden="true" class="play-icon elementor-icon">' . Icons_Manager::try_get_icon_html( $settings['thumbs_video_icon'], [ 'aria-hidden' => 'true' ] ) . '</span>';
						}
						echo '<div class="the7-image-wrapper the7-swiper-slide">';
						echo '<div class="' . esc_attr( $img_wrapper_class ) . '">';
						echo $this->template( Image_Size::class )->get_image( $gallery_image_id, [ 'class' => '' ] );
						echo $play_icon;
						echo '</div>';
						echo '</div>';
					}
					?>
				</div>
			</div>
			<?php $this->render_thumbs_arrows(); ?>
		</div>
		<?php
	}

	protected function render_wrapped_image( $wrapper_class, $img_id, $render_callback ) {
		$settings  = $this->get_settings_for_display();
		$link      = $this->get_link_url( $img_id );
		$zoom_attr = '';
		if ( $settings['zoom_on_hover'] === 'y' ) {
			$zoom_attr      = 'style="background-image: url(' . $link['url'] . ')"';
			$wrapper_class .= ' the7-zoom-on-hover ';
		}
		if ( $settings['open_lightbox'] === 'y' ) {
			$this->remove_render_attribute( 'link' );
			$this->add_link_attributes( 'link', $link );
			$image_wrapper       = '<a class="dt-pswp-item ' . esc_attr( $wrapper_class ) . '"  ' . $this->get_render_attribute_string( 'link' ) . ' data-elementor-open-lightbox="no" ' . $zoom_attr . '>';
			$image_wrapper_close = '</a>';
		} else {
			$image_wrapper       = '<div class="' . esc_attr( $wrapper_class ) . '"' . $zoom_attr . '>';
			$image_wrapper_close = '</div>';
		}

		echo $image_wrapper;

		$render_callback( $img_id );

		echo $image_wrapper_close;
	}

	protected function get_embed_options() {
		$embed_options = [];

		return $embed_options;
	}

	protected function get_embed_params( $attachment_id ) {
		$settings  = $this->get_settings_for_display();
		$video_url = presscore_get_image_video_url( $attachment_id );
		$params    = [];

		if ( $settings['video_autoplay'] ) {

			if ( $settings['play_on_mobile'] ) {
				$params['playsinline'] = '1';
			}
		}

		$params_dictionary = [];
		if ( strpos( $video_url, 'youtube' ) ) {
			$params_dictionary = [
				'loop',
				'mute',
				'controls',
			];

			if ( $settings['loop'] ) {
				$video_properties = Embed::get_video_properties( $video_url );

				$params['playlist'] = $video_properties['video_id'];
			}
			$params['wmode'] = 'opaque';

			$params['enablejsapi'] = '1';
		} elseif ( strpos( $video_url, 'vimeo' ) ) {
			$params_dictionary = [
				'loop',
				'mute' => 'muted',
			];

			$params['autopause'] = '0';
		}

		$params['start'] = '1';
		foreach ( $params_dictionary as $key => $param_name ) {
			$setting_name = $param_name;

			if ( is_string( $key ) ) {
				$setting_name = $key;
			}

			$setting_value = $settings[ $setting_name ] ? '1' : '0';

			$params[ $param_name ] = $setting_value;
		}

		return $params;
	}

	/**
	 * Retrieve image widget link URL.
	 *
	 * @param array $img_id Image ID.
	 *
	 * @return array An array with image link URL.
	 */
	protected function get_link_url( $img_id ) {
		return [
			'url' => wp_get_attachment_url( $img_id ),
		];
	}

	protected function render_arrows() {
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

	protected function render_thumbs_arrows() {
		?>
		<div class="the7-thumbs-swiper-button the7-thumbs-swiper-button-prev elementor-icon">
			<?php
			Icons_Manager::render_icon( $this->get_settings_for_display( 'thumbs_prev_icon' ) );
			?>
		</div>
		<div class="the7-thumbs-swiper-button the7-thumbs-swiper-button-next elementor-icon">
			<?php
			Icons_Manager::render_icon( $this->get_settings_for_display( 'thumbs_next_icon' ) )
			?>
		</div>
		<?php
	}

	/**
	 * @return string
	 */
	protected function get_swiper_container_class() {
		return Elementor::$instance->experiments->is_feature_active( 'e_swiper_latest' ) ? 'swiper' : 'swiper-container';
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

	protected function register_controls() {
		// Content Tab.
		$this->add_gallery_content_controls();
		$this->add_pagination_content_controls();
		$this->add_video_controls();
		// style tab
		$this->add_image_style_controls();
		$this->template( Arrows::class )->add_style_controls();

		/**
		 * Update section label.
		 *
		 * Updating section label with $this->update_control() causes some strange bugs with default values of all section controls in editor. So, we need to use controls_manager->update_control_in_stack() instead. It will update only section label without affecting other controls.
		 */
		if ( isset( Elementor::instance()->controls_manager ) ) {
			Elementor::instance()->controls_manager->update_control_in_stack(
				$this,
				'arrows_style',
				[
					'label' => esc_html__( 'Image Arrows', 'the7mk2' ),
				],
				[]
			);
		}

		$this->update_control(
			'arrow_icon_size',
			[
				'default' => [
					'unit' => 'px',
					'size' => 28,
				],
			]
		);
		$this->template( Bullets::class )->add_style_controls( null, null );
		$this->add_thumbs_style_controls();
		$this->add_thumbs_arrows_style_controls();
		$this->add_video_play_style_controls();
		$this->add_thumbs_video_play_style_controls();

		$this->start_injection(
			[
				'type' => 'control',
				'at'   => 'before',
				'of'   => 'bullets_v_position',
			]
		);
		$this->add_control(
			'bullets_direction',
			[
				'label'                => esc_html__( 'Direction', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'label_block'          => false,
				'options'              => [
					'vertical'   => [
						'title' => esc_html__( 'Vertical', 'the7mk2' ),
						'icon'  => 'eicon-navigation-vertical',
					],
					'horizontal' => [
						'title' => esc_html__( 'Horizontal', 'the7mk2' ),
						'icon'  => 'eicon-navigation-horizontal',
					],
				],
				'selectors_dictionary' => [
					'horizontal' => 'left: calc(50% + var(--bullets-h-offset, 0px)); --custom-bullets-width: 100%; --custom-bullets-height: auto;--custom-bullets-position-top: var(--bullet-position-top); --custom-bullets-position-left: 0; --bullets-direction: row wrap;',
					'vertical'   => 'top: calc(100% + var(--bullets-v-offset, 10px)); --bullets-direction: column nowrap; --custom-bullets-width: auto; --custom-bullets-height: 100%; --custom-bullets-position-top: 50%; --custom-bullets-position-left: var(--bullet-position-left); --custom-bullets-position-right: var(--bullet-position-left);',
				],
				'selectors'            => [
					'{{WRAPPER}} .owl-dots' => '{{VALUE}}',
				],
				'default'              => 'horizontal',
				'toggle'               => false,
				'prefix_class'         => 'bullets-',
			]
		);
		$this->update_control(
			'bullets_v_position',
			[
				'selectors_dictionary' => [
					'top'    => 'top: var(--bullet-v-offset, 10px); bottom: auto; --bullet-translate-y:0;--bullet-position-top: var(--bullet-v-offset, 10px);',
					'center' => 'top: calc(50% + var(--bullet-v-offset, 10px)); bottom: auto; --bullet-translate-y:-50%; --bullet-position-top:calc(50% + var(--bullet-v-offset, 10px));',
					'bottom' => 'top: auto; bottom: var(--bullet-v-offset, 10px); --bullet-translate-y:0; --bullet-position-top:calc(100% + var(--bullet-v-offset, 10px));',
				],
				'conditions'           => [
					'relation' => 'or',
					'terms'    => [
						[
							'relation' => 'and',
							'terms'    => [
								[
									'name'     => 'stretch_bullets',
									'operator' => '===',
									'value'    => 'y',
								],
								[
									'name'     => 'bullets_direction',
									'operator' => '==',
									'value'    => 'horizontal',
								],
								[
									'name'     => 'bullets_style',
									'operator' => '===',
									'value'    => 'custom',
								],
							],
						],
						[
							'relation' => 'and',
							'terms'    => [
								[
									'name'     => 'stretch_bullets',
									'operator' => '!==',
									'value'    => 'y',
								],
							],
						],
						[
							'relation' => 'and',
							'terms'    => [
								[
									'name'     => 'bullets_style',
									'operator' => '!==',
									'value'    => 'custom',
								],
							],
						],
					],
				],
			]
		);
		$this->update_control(
			'bullets_v_offset',
			[
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'relation' => 'and',
							'terms'    => [
								[
									'name'     => 'stretch_bullets',
									'operator' => '===',
									'value'    => 'y',
								],
								[
									'name'     => 'bullets_direction',
									'operator' => '==',
									'value'    => 'horizontal',
								],
								[
									'name'     => 'bullets_style',
									'operator' => '===',
									'value'    => 'custom',
								],
							],
						],
						[
							'relation' => 'and',
							'terms'    => [
								[
									'name'     => 'stretch_bullets',
									'operator' => '!==',
									'value'    => 'y',
								],
							],
						],
						[
							'relation' => 'and',
							'terms'    => [
								[
									'name'     => 'bullets_style',
									'operator' => '!==',
									'value'    => 'custom',
								],
							],
						],
					],
				],
			]
		);

		$this->end_injection();
	}

	protected function add_gallery_content_controls() {
		$this->start_controls_section(
			'gallery_section',
			[
				'label' => esc_html__( 'Image ', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->template( Image_Size::class )->add_style_controls();

		$this->add_control(
			'open_lightbox',
			[
				'label'              => esc_html__( 'Open Lightbox On Click', 'the7mk2' ),
				'type'               => Controls_Manager::SWITCHER,
				'label_off'          => esc_html__( 'No', 'the7mk2' ),
				'label_on'           => esc_html__( 'Yes', 'the7mk2' ),
				'return_value'       => 'y',
				'frontend_available' => true,
			]
		);
		$this->add_control(
			'zoom_on_hover',
			[
				'label'              => esc_html__( 'Zoom On Hover', 'the7mk2' ),
				'type'               => Controls_Manager::SWITCHER,
				'label_off'          => esc_html__( 'No', 'the7mk2' ),
				'label_on'           => esc_html__( 'Yes', 'the7mk2' ),
				'return_value'       => 'y',
				'frontend_available' => true,
			]
		);

		$selector = '{{WRAPPER}} .the7-elementor-slides';

		$this->add_control(
			'_wrap_helper',
			[
				'type'         => Controls_Manager::HIDDEN,
				'default'      => 'elementor-widget-the7-slider-common',
				'prefix_class' => '',
			]
		);

		$this->add_control(
			'slider_section',
			[
				'label'     => esc_html__( 'Scrolling', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'infinite',
			[
				'label'              => esc_html__( 'Infinite Loop', 'the7mk2' ),
				'type'               => Controls_Manager::SWITCHER,
				'default'            => 'yes',
				'frontend_available' => true,
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

		$this->add_control(
			'column_section',
			[
				'label'     => esc_html__( 'Layout', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'transition' => 'slide',
				],
			]
		);

		$slides_per_view = range( 1, 12 );
		$slides_per_view = array_combine( $slides_per_view, $slides_per_view );

		if ( ! Plugin::$instance->breakpoints->get_active_breakpoints( Breakpoints::BREAKPOINT_KEY_WIDESCREEN ) ) {
			$this->add_control(
				'wide_desk_columns',
				[
					'label'              => esc_html__( 'Columns On A Wide Desktop', 'the7mk2' ),
					'type'               => Controls_Manager::SELECT,
					'options'            => [ '' => esc_html__( 'Default', 'the7mk2' ) ] + $slides_per_view,
					'default'            => '',
					'frontend_available' => true,
					'condition'          => [
						'transition' => 'slide',
					],
				]
			);

			$this->add_control(
				'widget_columns_wide_desktop_breakpoint',
				[
					'label'              => esc_html__( 'Wide Desktop Breakpoint (px)', 'the7mk2' ),
					'description'        => the7_elementor_get_wide_columns_control_description(),
					'type'               => Controls_Manager::NUMBER,
					'default'            => '',
					'min'                => 0,
					'frontend_available' => true,
					'condition'          => [
						'transition' => 'slide',
					],
				]
			);
		}

		$this->add_responsive_control(
			'slides_per_view',
			[
				'type'                 => Controls_Manager::SELECT,
				'label'                => esc_html__( 'Columns', 'the7mk2' ),
				'options'              => [ '' => esc_html__( 'Default', 'the7mk2' ) ] + $slides_per_view,
				'default'              => static::SLIDES_PER_VIEW_DEFAULT,
				'inherit_placeholders' => false,
				'frontend_available'   => true,
				'render_type'          => 'none',
				'condition'            => [
					'transition' => 'slide',
				],
			]
		);

		$this->add_responsive_control(
			'slides_gap',
			[
				'label'              => esc_html__( 'Gap Between Images', 'the7mk2' ),
				'type'               => Controls_Manager::SLIDER,
				'default'            => [
					'unit' => 'px',
					'size' => 0,
				],
				'size_units'         => [ 'px' ],
				'range'              => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'selectors'          => [
					$selector => '--grid-column-gap: {{SIZE}}{{UNIT}}; --grid-row-gap: {{SIZE}}{{UNIT}};',
				],
				'frontend_available' => true,
				'condition'          => [
					'transition' => 'slide',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function add_video_controls() {
		$this->start_controls_section(
			'video_section',
			[
				'label' => esc_html__( 'Video ', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'enable_video',
			[
				'label'        => esc_html__( 'Enable videos', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_off'    => esc_html__( 'No', 'the7mk2' ),
				'label_on'     => esc_html__( 'Yes', 'the7mk2' ),
				'description'  => esc_html__( 'Insert Video link into ‘Video url’ field when adding/editing an image from Product Gallery.', 'the7mk2' ),
				'separator'    => 'before',
				'default'      => 'yes',
				'return_value' => 'yes',
				'render_type'  => 'template',
			]
		);

		$this->add_control(
			'video_autoplay',
			[
				'label'              => esc_html__( 'Autoplay', 'the7mk2' ),
				'type'               => Controls_Manager::SWITCHER,
				'description'        => esc_html__( 'Most of browsers autoplay muted videos only.', 'the7mk2' ),
				'frontend_available' => true,
				'condition'          => [
					'enable_video' => 'yes',
				],
				'render_type'        => 'template',
				'prefix_class'       => 'video-autoplay-',
			]
		);
		$this->add_control(
			'play_on_mobile',
			[
				'label'              => esc_html__( 'Play On Mobile', 'the7mk2' ),
				'type'               => Controls_Manager::SWITCHER,
				'frontend_available' => true,
				'condition'          => [
					'video_autoplay' => 'yes',
					'enable_video'   => 'yes',
				],
				'render_type'        => 'template',
				'prefix_class'       => 'video-mobile-autoplay-',

			]
		);

		$this->add_control(
			'mute',
			[
				'label'              => esc_html__( 'Mute', 'the7mk2' ),
				'type'               => Controls_Manager::SWITCHER,
				'frontend_available' => true,
				'condition'          => [
					'enable_video' => 'yes',
				],
			]
		);

		$this->add_control(
			'loop',
			[
				'label'              => esc_html__( 'Loop', 'the7mk2' ),
				'type'               => Controls_Manager::SWITCHER,
				'frontend_available' => true,
				'condition'          => [
					'enable_video' => 'yes',
				],
			]
		);
		$this->add_control(
			'controls',
			[
				'label'              => esc_html__( 'Player Controls', 'the7mk2' ),
				'type'               => Controls_Manager::SWITCHER,
				'frontend_available' => true,
				'condition'          => [
					'enable_video' => 'yes',
				],
			]
		);
		$this->end_controls_section();
	}

	/**
	 * Add slider controls.
	 */
	protected function add_pagination_content_controls() {
		$this->start_controls_section(
			'pagination_section',
			[
				'label' => esc_html__( 'Arrows & Navigation', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);
		$arrow_options = [
			'never'  => esc_html__( 'Never', 'the7mk2' ),
			'always' => esc_html__( 'Always', 'the7mk2' ),
			'hover'  => esc_html__( 'On Hover', 'the7mk2' ),
		];
		$this->add_responsive_control(
			'arrows',
			[
				'label'                => esc_html__( 'Arrows', 'the7mk2' ),
				'type'                 => Controls_Manager::SELECT,
				'options'              => $arrow_options,
				'device_args'          => $this->generate_device_args(
					[
						'default' => '',
						'options' => [ '' => esc_html__( 'Default', 'the7mk2' ) ] + $arrow_options,
					]
				),
				'default'              => 'always',
				'selectors'            => [
					'{{WRAPPER}}' => '{{VALUE}}',
				],
				'selectors_dictionary' => [
					'never'  => '--arrow-display: none;',
					'always' => '--arrow-display: inline-flex; --arrow-opacity:1;',
					'hover'  => '--arrow-display: inline-flex; --arrow-opacity:0;',
				],
			]
		);
		$pagination_options = [
			'thumbs'   => esc_html__( 'Thumbnails', 'the7mk2' ),
			'bullets'  => esc_html__( 'Bullets', 'the7mk2' ),
			'disabled' => esc_html__( 'Disabled', 'the7mk2' ),
		];
		$this->add_responsive_control(
			'pagination',
			[
				'label'                => esc_html__( 'Navigation', 'the7mk2' ),
				'type'                 => Controls_Manager::SELECT,
				'options'              => $pagination_options,
				'device_args'          => $this->generate_device_args(
					[
						'default' => '',
						'options' => [ '' => esc_html__( 'Default', 'the7mk2' ) ] + $pagination_options,
					]
				),
				'default'              => 'disabled',
				'render_type'          => 'template',
				'selectors'            => [
					'{{WRAPPER}}' => '{{VALUE}}',
				],
				'selectors_dictionary' => [
					'thumbs'   => '--bullet-display: none; --thumbs-display: flex;',
					'bullets'  => '--bullet-display: inline-flex; --thumbs-display: none;',
					'disabled' => '--bullet-display: none; --thumbs-display: none;',
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
				'label' => esc_html__( 'Big Image', 'the7mk2' ),
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
					'{{WRAPPER}} .dt-product-gallery .the7-image-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}}' => '--img-padding-top: {{TOP}}{{UNIT}}; --img-padding-right: {{RIGHT}}{{UNIT}}; --img-padding-bottom: {{BOTTOM}}{{UNIT}}; --img-padding-left: {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'image_border',
				'selector' => '{{WRAPPER}} .dt-product-gallery .the7-image-wrapper',
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
					'{{WRAPPER}} .dt-product-gallery .the7-image-wrapper, {{WRAPPER}} .dt-product-gallery .the7-image-wrapper img, {{WRAPPER}} .the7-overlay-content, {{WRAPPER}} .gallery-video-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
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
					'{{WRAPPER}} .dt-product-gallery .the7-image-wrapper' => 'border-color: {{VALUE}}',
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
					'{{WRAPPER}} .dt-product-gallery .the7-image-wrapper' => 'background: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function add_video_play_style_controls() {
		$this->start_controls_section(
			'video_play_style',
			[
				'label'     => esc_html__( 'Image Video Icon', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'enable_video' => 'yes',
				],
			]
		);
		$this->add_control(
			'video_icon',
			[
				'label'   => esc_html__( 'Video Icon', 'the7mk2' ),
				'type'    => Controls_Manager::ICONS,
				'default' => [
					'value'   => 'far fa-play-circle',
					'library' => 'fa-regular',
				],
			]
		);
		$this->add_control(
			'img_video_icon',
			[
				'label' => esc_html__( 'Icon Size', 'the7mk2' ),
				'type'  => Controls_Manager::HEADING,
			]
		);
		$this->add_responsive_control(
			'video_play_size',
			[
				'label'      => esc_html__( 'Icon Size', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range'      => [
					'em' => [
						'min'  => 0.5,
						'max'  => 4,
						'step' => 0.1,
					],
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .elementor-swiper .play-icon' => 'font-size: {{SIZE}}{{UNIT}}',
				],
			]
		);
		$this->add_responsive_control(
			'video_play_width',
			[
				'label'      => esc_html__( 'Background Width', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .elementor-swiper .play-icon' => 'min-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'video_play_height',
			[
				'label'      => esc_html__( 'Background Height', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .elementor-swiper .play-icon' => 'min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'video_icon_style',
			[
				'label'     => esc_html__( 'Icon Style', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'video_play_border',
				'label'    => esc_html__( 'Border', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .elementor-swiper .play-icon',
				'exclude'  => [
					'color',
				],
			]
		);

		$this->add_control(
			'video_play_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .elementor-swiper .play-icon' => 'border-radius: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'video_play_color',
			[
				'label'     => esc_html__( 'Icon Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .play-icon i'   => 'color: {{VALUE}}',
					'{{WRAPPER}} .play-icon svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'video_play_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .play-icon' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'video_play_border_border!' => [ '', 'none' ],
				],
			]
		);
		$this->add_control(
			'video_play_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .play-icon' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'video_play_shadow',
				'selector' => '{{WRAPPER}} .play-icon',
			]
		);

		$this->end_controls_section();
	}

	protected function add_thumbs_video_play_style_controls() {
		$this->start_controls_section(
			'thumbs_video_play_style',
			[
				'label'     => esc_html__( 'Thumbnails Video Icon', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'enable_video' => 'yes',
				],
			]
		);
		$this->add_control(
			'thumbs_video_icon',
			[
				'label'   => esc_html__( 'Video Icon', 'the7mk2' ),
				'type'    => Controls_Manager::ICONS,
				'default' => [
					'value'   => 'far fa-play-circle',
					'library' => 'fa-regular',
				],
			]
		);
		$this->add_control(
			'thumb_video_icon',
			[
				'label'     => esc_html__( 'Icon Size', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'thumb_video_play_size',
			[
				'label'      => esc_html__( 'Icon Size', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range'      => [
					'em' => [
						'min'  => 0.5,
						'max'  => 4,
						'step' => 0.1,
					],
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .thumbs-swiper .play-icon' => 'font-size: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'thumb_video_play_width',
			[
				'label'      => esc_html__( 'Background Width', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .thumbs-swiper .play-icon' => 'min-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'thumb_video_play_height',
			[
				'label'      => esc_html__( 'Background Height', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .thumbs-swiper .play-icon' => 'min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'thumb_video_icon_style',
			[
				'label'     => esc_html__( 'Icon Style', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'thumb_video_play_border',
				'label'    => esc_html__( 'Border', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .thumbs-swiper .play-icon',
				'exclude'  => [
					'color',
				],
			]
		);

		$this->add_control(
			'thumb_video_play_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .thumbs-swiper .play-icon' => 'border-radius: {{SIZE}}{{UNIT}}',
				],
			]
		);
		$this->add_control(
			'thumb_video_play_color',
			[
				'label'     => esc_html__( 'Icon Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thumbs-swiper .play-icon i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .thumbs-swiper .play-icon svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'thumb_video_play_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .thumbs-swiper .play-icon' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'thumb_video_play_border_border!' => [ '', 'none' ],
				],
			]
		);
		$this->add_control(
			'thumb_video_play_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thumbs-swiper .play-icon' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'thumb_video_play_shadow',
				'selector' => '{{WRAPPER}} .thumbs-swiper .play-icon',
			]
		);

		$this->end_controls_section();
	}

	protected function add_thumbs_style_controls() {
		$this->start_controls_section(
			'thumbs_style',
			[
				'label' => esc_html__( 'Thumbnails', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'thumbs_title',
			[
				'label' => esc_html__( 'Thumbnails Area', 'the7mk2' ),
				'type'  => Controls_Manager::HEADING,
			]
		);
		$this->add_control(
			'hide_one_img_thumbs',
			[
				'label'        => esc_html__( 'Hide if only one image', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_off'    => esc_html__( 'No', 'the7mk2' ),
				'label_on'     => esc_html__( 'Yes', 'the7mk2' ),
				'default'      => 'y',
				'return_value' => 'y',
			]
		);
		$this->add_control(
			'display_thumbs_ouside',
			[
				'label'        => esc_html__( 'Outside the slider', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_off'    => esc_html__( 'No', 'the7mk2' ),
				'label_on'     => esc_html__( 'Yes', 'the7mk2' ),
				'default'      => '',
				'return_value' => 'y',
				'prefix_class'       => 'display-thumbs-outside-',
				'render_type'          => 'template',
				'selectors_dictionary' => [
					'y'   => $this->combine_to_css_vars_definition_string(
						[
							'thumbs-swiper-position' => 'relative',
							'thumbs-swiper-height' => 'var(--widget-height)',
						]
					),
					'' => $this->combine_to_css_vars_definition_string(
						[
							'thumbs-swiper-position' => 'absolute',
							'thumbs-swiper-height' => '100%',
						]
					),
				],
				'selectors'            => [
					'{{WRAPPER}}' => '{{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'thumbs_direction',
			[
				'label'                => esc_html__( 'Direction', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'label_block'          => false,
				'options'              => [
					'vertical'   => [
						'title' => esc_html__( 'Vertical', 'the7mk2' ),
						'icon'  => 'eicon-navigation-vertical',
					],
					'horizontal' => [
						'title' => esc_html__( 'Horizontal', 'the7mk2' ),
						'icon'  => 'eicon-navigation-horizontal',
					],
				],
				'default'              => 'vertical',
				'selectors_dictionary' => [
					'vertical'   => $this->combine_to_css_vars_definition_string(
						[
							'thumbs-swiper-direction' => 'row nowrap',
							'thumbs-vertical-alignment' => 'var(--justify-thumb-content)',
							'thumbs-horizontal-alignment' => 'flex-start',
							'thumbs-swiper-top-position' => 'calc(100% + var(--thumbs-v-offset, 0px))',
							'thumbs-swiper-max-width'    => '100%',
							'thumbs-swiper-max-height'   => 'var(--thumbs-max-width, var(--thumbs-swiper-height))',
							'thumbs-swiper-top-position' => '0px',
							'thumbs-swiper-bottom-position' => 'auto',
							'thumbs-swiper-left-position' => 'var(--v-thumbs-position-left)',
							'thumbs-swiper-right-position' => 'var(--v-thumbs-position-right)',
							'thumb-width'                => 'auto',
							'thumb-height'               => 'var(--thumb-item-size, 80px)',
							'thumbs-wrap-width'          => 'calc(var(--thumb-item-size, 80px) + var(--thumbs-padding-right, 0px) + var(--thumbs-padding-left, 0px))',
							'elementor-slider-width'          => 'calc(100% - var(--thumbs-wrap-width, 150px))',
							'thumbs-wrap-height'         => 'var(--thumbs-max-width, 100%)',
							'thumbs-swiper-button-next-position-left' => 'calc(50% + var(--thumbs-arrow-next-h-offset, 0px) + var(--thumbs-offset-v-t-img))',
							'thumbs-arrow-translate-x'   => '-50%',
							'thumbs-arrow-translate-y'   => '0px',
							'thumbs-swiper-button-prev-position-left' => 'calc(50% + var(--thumbs-arrow-prev-h-offset, 0px) + var(--thumbs-offset-v-t-img))',
							'thumbs-swiper-button-prev-position-top' => 'var(--thumbs-arrow-prev-v-offset, 10px)',
							'thumbs-swiper-button-next-position-top' => 'calc(100% - var(--thumb-arrow-height) - var(--thumbs-arrow-next-v-offset, 10px))',
							'thumbs-swiper-button-transform' => 'translate3d(var(--thumbs-arrow-translate-x, 0px), var(--thumbs-arrow-translate-y), 0px) rotate(90deg);',
						]
					),
					'horizontal' => $this->combine_to_css_vars_definition_string(
						[
							'thumbs-swiper-direction' => 'column wrap',
							'thumbs-vertical-alignment' => 'var(--justify-thumb-content)',
							'thumbs-horizontal-alignment' => 'var(--justify-thumb-content)',
							'thumbs-swiper-max-width'    => 'var(--thumbs-max-width, 100%)',
							'thumbs-swiper-max-height'   => 'var(--thumbs-max-width, 100%)',
							'thumbs-swiper-top-position' => 'var(--h-thumbs-position-top)',
							'thumbs-swiper-bottom-position' => 'var(--h-thumbs-position-bottom)',
							'thumbs-swiper-left-position' => '0px',
							'thumbs-swiper-right-position' => 'auto',
							'thumb-width'                => 'var(--thumb-item-size, 80px)',
							'thumb-height'               => 'auto',
							'thumbs-wrap-width'          => '100%',
							'elementor-slider-width'     => '100%',
							'thumbs-wrap-height'         => 'auto',
							'thumbs-swiper-button-next-position-top' => 'calc(50% + var(--thumbs-arrow-next-v-offset, 0px) + var(--thumbs-offset-v-t-img))',
							'thumbs-swiper-button-position-left' => 'auto',
							'thumbs-arrow-translate-x'   => '0',
							'thumbs-arrow-translate-y'   => '-50%',
							'thumbs-swiper-button-prev-position-left' => 'var(--thumbs-arrow-prev-h-offset, 10px)',
							'thumbs-swiper-button-prev-position-top' => 'calc(50% + var(--thumbs-arrow-prev-v-offset, 0px) + var(--thumbs-offset-v-t-img))',
							'thumbs-swiper-button-next-position-left' => 'calc(100% - var(--thumbs-arrow-next-h-offset, 10px) - var(--thumb-arrow-width, 40px))',
							'thumbs-swiper-button-transform' => 'translate3d(var(--thumbs-arrow-translate-x, 0px), var(--thumbs-arrow-translate-y, 0px), 0px)',
						]
					),
				],
				'selectors'            => [
					'{{WRAPPER}}' => '{{VALUE}};',
				],
				'toggle'               => false,
				'frontend_available'   => true,
				'render_type'          => 'template',
			]
		);

		$this->add_responsive_control(
			'thumbs_v_position',
			[
				'label'                => esc_html__( 'Position', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'label_block'          => false,
				'options'              => [
					'top'    => [
						'title' => esc_html__( 'Start', 'the7mk2' ),
						'icon'  => 'eicon-h-align-left',
					],
					'bottom' => [
						'title' => esc_html__( 'End', 'the7mk2' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'selectors_dictionary' => [
					'top'    => '--thumbs-order: 0; --v-thumbs-position-left: 0px; --v-thumbs-position-right: auto;',
					'bottom' => '--thumbs-order: 2; --v-thumbs-position-left: auto; --v-thumbs-position-right: 0px;',
				],

				'toggle'               => false,
				'selectors'            => [
					'{{WRAPPER}}' => '{{VALUE}};',
				],

				'default'              => 'top',
				'frontend_available'   => true,
				'condition' => [
					'thumbs_direction' => 'vertical',
				],
			]
		);
		$this->add_responsive_control(
			'thumbs_v_alignment',
			[
				'label'                => esc_html__( 'Alignment', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'label_block'          => false,
				'options'              => [
					'left'   => [
						'title' => esc_html__( 'Start', 'the7mk2' ),
						'icon'  => 'eicon-v-align-top',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'the7mk2' ),
						'icon'  => 'eicon-v-align-middle',
					],
					'right'  => [
						'title' => esc_html__( 'End', 'the7mk2' ),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'toggle'               => false,
				'default'              => 'right',
				'selectors_dictionary' => [
					'left'   => '--justify-thumb-content: flex-start; --thumbs-v-top-position: 0px; --thumbs-v-bottom-position: auto; --thumbs-v-translate-y: 0px;',
					'center' => '  --justify-thumb-content: center; --thumbs-v-top-position: 50%; --thumbs-v-bottom-position: auto; --thumbs-v-translate-y: -50%;',
					'right'  => ' --justify-thumb-content: flex-end; --thumbs-v-top-position: auto; --thumbs-v-bottom-position: 0px; --thumbs-v-translate-y: 0px;',
				],
				'selectors'            => [
					'{{WRAPPER}} ' => '{{VALUE}};',
				],

				'condition' => [
					'thumbs_direction' => 'vertical',
				],
			]
		);
		$this->add_responsive_control(
			'thumbs_h_position',
			[
				'label'                => esc_html__( 'Position', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'label_block'          => false,
				'options'              => [
					'top'    => [
						'title' => esc_html__( 'Start', 'the7mk2' ),
						'icon'  => 'eicon-v-align-top',
					],
					'bottom' => [
						'title' => esc_html__( 'End', 'the7mk2' ),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'selectors_dictionary' => [
					'top'    => '--thumbs-order: 0; -thumbs-v-top-position: 0px;--thumbs-v-bottom-position: auto;  --h-thumbs-position-top: 0px; --h-thumbs-position-bottom: auto;',
					'bottom' => '--thumbs-order: 2;  --thumbs-v-top-position: auto; --thumbs-v-bottom-position: 0px; --h-thumbs-position-top: auto; --h-thumbs-position-bottom: 0px;',
				],
				'toggle'               => false,
				'selectors'            => [
					'{{WRAPPER}}' => '{{VALUE}};',
				],
				'default'              => 'bottom',
				'condition' => [
					'thumbs_direction' => 'horizontal',
				],
			]
		);

		$this->add_responsive_control(
			'thumbs_h_alignment',
			[
				'label'                => esc_html__( 'Alignment', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'label_block'          => false,
				'options'              => [
					'left'   => [
						'title' => esc_html__( 'Start', 'the7mk2' ),
						'icon'  => 'eicon-h-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'the7mk2' ),
						'icon'  => 'eicon-h-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'End', 'the7mk2' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'toggle'               => false,
				'default'              => 'left',
				'selectors_dictionary' => [
					'left'   => '--justify-thumb-content: flex-start; --thumbs-h-left-position: 0px; --thumbs-v-translate-x: 0px; ',
					'center' => '  --justify-thumb-content: center; --thumbs-h-left-position: 50%; --thumbs-v-translate-x: -50%;',
					'right'  => ' --justify-thumb-content: flex-end; --thumbs-h-left-position: 100%; --thumbs-v-translate-x: -100%;',
				],
				'selectors'            => [
					'{{WRAPPER}}' => '{{VALUE}};',
				],
				'condition' => [
					'thumbs_direction' => 'horizontal',
				],
			]
		);
		$this->add_responsive_control(
			'thumbs_width',
			[
				'label'      => esc_html__( 'Thumbnails Area Max Length', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 1000,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}}' => '--thumbs-max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'thumbs_padding',
			[
				'label'      => esc_html__( 'Thumbnails Area Padding', 'the7mk2' ),
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
					'{{WRAPPER}} .thumbs-swiper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}}'                => '--thumbs-padding-top: {{TOP}}{{UNIT}}; --thumbs-padding-right: {{RIGHT}}{{UNIT}}; --thumbs-padding-bottom: {{BOTTOM}}{{UNIT}}; --thumbs-padding-left: {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'thumbs_margin',
			[
				'label'      => esc_html__( 'Thumbnails Area Margins', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min' => -1000,
						'max' => 1000,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}}' => '--thumbs-margin-top: {{TOP}}{{UNIT}}; --thumbs-margin-right:{{RIGHT}}{{UNIT}}; --thumbs-margin-bottom: {{BOTTOM}}{{UNIT}}; --thumbs-margin-left: {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .thumbs-swiper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'thumbs_spacing',
			[
				'label'              => esc_html__( 'Distance Between Thumbnails', 'the7mk2' ),
				'type'               => Controls_Manager::SLIDER,
				'size_units'         => [ 'px' ],
				'frontend_available' => true,
				'render_type'        => 'template',
			]
		);

		$this->add_control(
			'thumbs_items_title',
			[
				'label'     => esc_html__( 'Separate Thumbnails', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'thumb_ratio',
			[
				'label'       => esc_html__( 'Image Ratio', 'the7mk2' ),
				'description' => esc_html__( 'Leave empty to use original proportions', 'the7mk2' ),
				'type'        => Controls_Manager::SLIDER,
				'default'     => [
					'size' => '',
				],
				'range'       => [
					'px' => [
						'min'  => 0.1,
						'max'  => 2,
						'step' => 0.01,
					],
				],
				'selectors'   => [
					'{{WRAPPER}}' => '--thumb-aspect-ratio: {{SIZE}};',
				],
			]
		);

		$this->add_responsive_control(
			'thumbs_item_width',
			[
				'label'      => esc_html__( 'Item Width', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}}' => '--thumb-item-size: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'thumbs_item_padding',
			[
				'label'      => esc_html__( 'Item Padding', 'the7mk2' ),
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
					'{{WRAPPER}} .thumbs-swiper .the7-swiper-slide' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'thumbs_border',
				'selector' => '{{WRAPPER}} .thumbs-slides-wrapper .the7-swiper-slide',
				'exclude'  => [
					'color',
				],
			]
		);

		$this->add_responsive_control(
			'thumbs_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .thumbs-slides-wrapper .the7-swiper-slide' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->start_controls_tabs( 'thumbs_colors' );

		$this->start_controls_tab(
			'thumbs_colors_normal',
			[
				'label' => esc_html__( 'Normal', 'the7mk2' ),

			]
		);
		$this->add_control(
			'thumbs_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .thumbs-slides-wrapper .the7-swiper-slide' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'thumbs_border_border!' => [ '', 'none' ],
				],
			]
		);
		$this->add_control(
			'thumbs_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .thumbs-slides-wrapper .the7-swiper-slide' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'thumbs_opacity',
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
					'{{WRAPPER}}' => '--thumbs-opacity: calc({{SIZE}}/100)',
					'{{WRAPPER}} .thumbs-slides-wrapper .the7-swiper-slide:not(.swiper-slide-thumb-active)' => 'opacity: calc({{SIZE}}/100)',
				],
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab(
			'thumbs_colors_hover',
			[
				'label' => esc_html__( 'Hover', 'the7mk2' ),
			]
		);
		$this->add_control(
			'thumbs_border_color_hover',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .thumbs-slides-wrapper .the7-swiper-slide:not(.swiper-slide-thumb-active):hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'thumbs_border_border!' => [ '', 'none' ],
				],
			]
		);
		$this->add_control(
			'thumbs_bg_color_hover',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .thumbs-slides-wrapper .the7-swiper-slide:not(.swiper-slide-thumb-active):hover' => 'background: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'thumbs_opacity_hover',
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
					'{{WRAPPER}} .thumbs-slides-wrapper .the7-swiper-slide:not(.swiper-slide-thumb-active):hover' => 'opacity: calc({{SIZE}}/100)',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'thumbs_colors_active',
			[
				'label' => esc_html__( 'Active', 'the7mk2' ),
			]
		);
		$this->add_control(
			'thumbs_border_color_active',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .thumbs-slides-wrapper .swiper-slide-thumb-active' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'thumbs_border_border!' => [ '', 'none' ],
				],
			]
		);
		$this->add_control(
			'thumbs_bg_color_active',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .thumbs-slides-wrapper .swiper-slide-thumb-active' => 'background: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'thumbs_active',
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
					'{{WRAPPER}} .thumbs-slides-wrapper .swiper-slide-thumb-active' => 'opacity: calc({{SIZE}}/100)',
				],
			]
		);
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function add_thumbs_arrows_style_controls() {
		$this->start_controls_section(
			'thumbs_arrows_style',
			[
				'label' => esc_html__( 'Thumbnails arrows', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'thumbs_arrows_heading',
			[
				'label'     => esc_html__( 'Arrow Icon', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'thumbs_next_icon',
			[
				'label'   => esc_html__( 'Next Arrow', 'the7mk2' ),
				'type'    => Controls_Manager::ICONS,
				'default' => [
					'value'   => 'fas fa-chevron-right',
					'library' => 'fa-solid',
				],
			]
		);

		$this->add_control(
			'thumbs_prev_icon',
			[
				'label'   => esc_html__( 'Previous Arrow', 'the7mk2' ),
				'type'    => Controls_Manager::ICONS,
				'default' => [
					'value'   => 'fas fa-chevron-left',
					'library' => 'fa-solid',
				],
			]
		);

		$this->add_responsive_control(
			'thumbs_arrow_icon_size',
			[
				'label'      => esc_html__( 'Arrow Icon Size', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 20,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .thumbs-swiper' => '--arrow-icon-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'thumbs_arrows_background_heading',
			[
				'label'     => esc_html__( 'Arrow style', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$arrow_selector = '{{WRAPPER}} .thumbs-swiper .the7-thumbs-swiper-button';

		$this->add_responsive_control(
			'thumbs_arrow_bg_width',
			[
				'label'      => esc_html__( 'Background Width', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 40,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .thumbs-swiper' => '--thumb-arrow-width:  max({{SIZE}}{{UNIT}}, var(--arrow-icon-size, 20px));',
					$arrow_selector              => 'width: max({{SIZE}}{{UNIT}}, var(--arrow-icon-size, 20px))',
				],
			]
		);

		$this->add_responsive_control(
			'thumbs_arrow_bg_height',
			[
				'label'      => esc_html__( 'Background Height', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 40,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}}' => '--thumb-arrow-height:  max({{SIZE}}{{UNIT}}, var(--arrow-icon-size, 20px));',
					$arrow_selector              => 'height: max({{SIZE}}{{UNIT}}, var(--arrow-icon-size, 20px))',
				],
			]
		);

		$this->add_control(
			'thumbs_arrow_border_radius',
			[
				'label'      => esc_html__( 'Arrow Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 0,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 500,
						'step' => 1,
					],
				],
				'selectors'  => [
					$arrow_selector => 'border-radius: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'thumbs_arrow_border_width',
			[
				'label'      => esc_html__( 'Arrow Border Width', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 0,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 25,
						'step' => 1,
					],
				],
				'selectors'  => [
					$arrow_selector => 'border-width: {{SIZE}}{{UNIT}}; border-style: solid',
				],
			]
		);

		$this->start_controls_tabs( 'thumbs_arrows_style_tabs' );

		$this->add_thumb_arrow_style_states_controls( 'normal_', esc_html__( 'Normal', 'the7mk2' ) );
		$this->add_thumb_arrow_style_states_controls( 'hover_', esc_html__( 'Hover', 'the7mk2' ) );

		$this->end_controls_tabs();

		$this->add_thumb_arrow_position_styles();

		$this->end_controls_section();
	}

	/**
	 * @param string $prefix_name Prefix.
	 * @param string $box_name    Box.
	 *
	 * @return void
	 */
	protected function add_thumb_arrow_style_states_controls( $prefix_name, $box_name ) {
		$is_hover = '';
		if ( strpos( $prefix_name, 'hover_' ) === 0 ) {
			$is_hover = ':hover';
		}

		$selector_pattern = '{{WRAPPER}} .thumbs-swiper .the7-thumbs-swiper-button' . $is_hover . '%1$s';
		$selector         = sprintf( $selector_pattern, '' );

		$this->start_controls_tab(
			$prefix_name . 'thumbs_arrows_colors',
			[
				'label' => $box_name,
			]
		);

		$this->add_control(
			$prefix_name . 'thumbs_arrow_icon_color',
			[
				'label'     => esc_html__( 'Icon Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					sprintf( $selector_pattern, '> i' )   => 'color: {{VALUE}};',
					sprintf( $selector_pattern, '> svg' ) => 'fill: {{VALUE}};color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			$prefix_name . 'thumbs_arrow_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					$selector => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			$prefix_name . 'thumbs_arrow_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					$selector => 'background: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
	}

	/**
	 * @return void
	 */
	protected function add_thumb_arrow_position_styles() {
		$this->add_control(
			'prev_thumbs_arrow_position_heading',
			[
				'label'     => esc_html__( 'Prev Arrow Position', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'prev_thumbs_arrow_v_offset',
			[
				'label'      => esc_html__( 'Vertical Offset', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 0,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => -1000,
						'max'  => 1000,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}}' => '--thumbs-arrow-prev-v-offset: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'prev_thumbs_arrow_h_offset',
			[
				'label'      => esc_html__( 'Horizontal Offset', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 0,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => -1000,
						'max'  => 1000,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}}' => '--thumbs-arrow-prev-h-offset: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'next_thumbs_arrow_position_heading',
			[
				'label'     => esc_html__( 'Next Arrow Position', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'next_thumbs_arrow_v_offset',
			[
				'label'      => esc_html__( 'Vertical Offset', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 0,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => -1000,
						'max'  => 1000,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}}' => '--thumbs-arrow-next-v-offset: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'next_thumbs_arrow_h_offset',
			[
				'label'      => esc_html__( 'Horizontal Offset', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 0,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => -1000,
						'max'  => 1000,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}}' => '--thumbs-arrow-next-h-offset: {{SIZE}}{{UNIT}};',
				],
			]
		);
	}
}
