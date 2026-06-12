<?php

namespace The7\Mods\Compatibility\Elementor\Widgets\Woocommerce;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Plugin;
use Elementor\Utils;
use Elementor\Embed;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Product_Images extends The7_Elementor_Widget_Base {

	public function get_name() {
		return 'the7-woocommerce-product-images';
	}

	protected function the7_title() {
		return esc_html__( 'Product Images', 'the7mk2' );
	}

	protected function the7_icon() {
		return 'eicon-product-images';
	}

	public function get_categories() {
		return [ 'woocommerce-elements-single' ];
	}

	public function get_script_depends() {
		if ( Plugin::$instance->preview->is_preview_mode() ) {
			return [ 'the7-woocommerce-product-images-widget-preview' ];
		}

		return [ 'the7-gallery-scroller' ];
	}

	protected function the7_keywords() {
		return [ 'woocommerce', 'shop', 'store', 'image', 'product', 'gallery', 'lightbox' ];
	}

	public function render() {
		global $product;
		global $woocommerce;
		global $post;

		$isPreview = Plugin::$instance->editor->is_edit_mode();

		$settings = $this->get_settings_for_display();
		$version = '3.0.0';

		$product = wc_get_product();

		if ( $product == false ) {
			return;
		}
		if ( version_compare( $woocommerce->version, $version, ">=" ) ) {
			$attachment_ids = $product->get_gallery_image_ids();
		} else {
			$attachment_ids = $product->get_gallery_attachment_ids();
		}

		$attachment_ids = array_map(
			function ( $attachment ) {
				return apply_filters( 'wpml_object_id', $attachment, 'attachment', true );
			},
			array_filter( $attachment_ids )
		);

		if ( has_post_thumbnail() ) {
			$very_post_thumbnail_id  = apply_filters( 'wpml_object_id', get_post_thumbnail_id(), 'attachment', true );
			$combined_attachment_ids = array_merge( [ $very_post_thumbnail_id ], $attachment_ids );
		}

		if ( empty( $product ) ) {
			return;
		}
		echo '<div ' . $this->container_class( [ 'dt-wc-product-gallery' ] ) . $this->get_slider_data_atts() . ' >';

		ob_start();
		$this->render_navigation( $settings );
		$nav = ob_get_clean();


		?>
        <div class="dt-product-gallery">
            <div class="dt-product-gallery-wrap">
				<?php
				if ( 'yes' === $settings['show_onsale_flash'] || $isPreview ) {
					wc_get_template( 'loop/sale-flash.php' );
				}
				if ( 'yes' === $settings['show_zoom'] ) {
					?>
                        <a aria-hidden="true" href="#" class="zoom-flash elementor-icon">
                            <?php Icons_Manager::render_icon( $settings['zoom_icon'], [ 'aria-hidden' => 'true' ] ); ?>
                        </a>
					    <a aria-hidden="true" style="display:none" class="woocommerce-product-gallery__trigger" href="#" data-fancybox="product-gallery"></a>
					<?php
				}
				?>
                <div class="flexslider">
                    <ul class="slides dt-gallery-container">
						<?php
						$post_thumbnail_id = apply_filters( 'wpml_object_id', $product->get_image_id(), 'attachment', true );
						if ( $product->get_image_id() ) {
							$html = $this->get_gallery_image_html( $post_thumbnail_id, true );
						} else {
							$html = '<li>';
							$html .= sprintf( '<img src="%s" alt="%s" class="wp-post-image" />', esc_url( wc_placeholder_img_src( 'woocommerce_single' ) ), esc_html__( 'Awaiting product image', 'woocommerce' ) );
							$html .= '</li>';
						}

						echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', $html, $post_thumbnail_id );

						foreach ( $attachment_ids as $attachment_id ) {
							echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', $this->get_gallery_image_html( $attachment_id, true ), $attachment_id );
						}
						?>
                    </ul>

					<?php echo $nav; ?>
                </div>
            </div>
        </div>
		<?php
		$classThumbs = '';

		if ( ! empty( $combined_attachment_ids ) && count($combined_attachment_ids) > 1 && 'disabled' !== $settings['thumbs_direction'] ) {
			ob_start();
			$item_exist = false;
			?>
            <div class="dt-product-thumbs <?php echo $classThumbs ?> " <?php echo $this->get_container_data_atts(); ?>>
                <div class="flexslider">
                    <ul class="slides">
						<?php
						foreach ( $combined_attachment_ids as $attachment_id ) {
							$url = wp_get_attachment_url( $attachment_id );
							if ( ! $url ) {
								continue;
							}
							$item_exist = true;
							echo apply_filters( 'woocommerce_single_product_image_thumbnail_html',  $this->get_thumbs_image_html($attachment_id), $attachment_id, $post->ID );
						}
						?>
                    </ul>
					<?php echo $nav; ?>
                </div>
            </div>
			<?php
			$thumbs = ob_get_clean();
			if ( $item_exist ) {
				echo $thumbs;
			}
		}
		echo '</div>';
	}

	/**
	 * Return container class attribute.
	 *
	 * @param array $class
	 *
	 * @return string
	 */
	protected function container_class( $class = [] ) {
		$class[] = 'the7-elementor-widget';
		// Unique class.
		$class[] = $this->get_unique_class();

		$settings = $this->get_settings_for_display();

		$class[] = the7_array_match( $settings['thumbs_direction'], [
			'bottom'   => 'thumb-position-bottom',
			'left'     => 'thumb-position-left',
			'right'    => 'thumb-position-right',
			'disabled' => 'thumb-position-disabled',
		] );

		return sprintf( ' class="%s" ', esc_attr( implode( ' ', $class ) ) );
	}

	protected function get_slider_data_atts() {
		$settings = $this->get_settings_for_display();

		$data_atts = [
			'animation' => $settings['slider_animation'],
		];

		return ' ' . presscore_get_inlide_data_attr( $data_atts );
	}

	protected function render_navigation( $settings ) {
		?>
        <ul class="flex-direction-nav">
            <li class="flex-nav-prev"><a href="#" class="flex-prev" aria-hidden ="true">
				<?php
				Icons_Manager::render_icon( $settings['arrow_prev'], [
					'aria-hidden' => 'true',
				] ); ?>
            </a></li>
            <li class="flex-nav-next"><a href="#" class="flex-next" aria-hidden ="true">
				<?php
				Icons_Manager::render_icon( $settings['arrow_next'], [
					'aria-hidden' => 'true',
				]); ?>
            </a></li>
        </ul>
		<?php
	}

	private function get_gallery_image_html( $attachment_id, $main_image = false ) {
		$settings = $this->get_settings_for_display();
		$gallery_thumbnail = wc_get_image_size( 'gallery_thumbnail' );
		$thumbnail_size = apply_filters( 'woocommerce_gallery_thumbnail_size', array(
			$gallery_thumbnail['width'],
			$gallery_thumbnail['height'],
		) );
		$image_size = apply_filters( 'woocommerce_gallery_image_size', $main_image ? 'woocommerce_single' : $thumbnail_size );
		$full_size = apply_filters( 'woocommerce_gallery_full_size', apply_filters( 'woocommerce_product_thumbnails_large_size', 'full' ) );
		$full_src = wp_get_attachment_image_src( $attachment_id, $full_size );

		if ( ! isset( $full_src[0], $full_src[1], $full_src[2] ) ) {
			return '';
		}

		$video_url = presscore_get_image_video_url( $attachment_id );
		if ( ! empty( $video_url ) && $settings['enable_video'] === 'yes' ) {
			$video_overlay_html = '';
			if ( ! $settings['autoplay'] ) {
				$this->remove_render_attribute( 'the7-video-overlay' );
				$video_overlay = wp_get_attachment_url( $attachment_id );
				$this->add_render_attribute(
            'the7-video-overlay',
					[
						'style' => 'background-image: url(' . $video_overlay . ');',
						'class' => 'the7-video-overlay',
					]
				);
				$video_overlay_html = '<span ' . $this->get_render_attribute_string( 'the7-video-overlay' ) . '><span class="play-icon elementor-icon">' . Icons_Manager::try_get_icon_html( $settings['video_icon'], [ 'aria-hidden' => 'true' ] ) . '</span></span>';
			}

			$video_wrapper       = '<div class="gallery-video-wrap">';
			$video_wrapper_close = '</div>';
			if ( $settings['lightbox_on_click'] === 'y' || $settings['show_zoom'] === 'yes' ) {
				$video_wrapper       = '<a href="' . $video_url . '" class="gallery-video-wrap dt-pswp-item dt-pswp-item-no-click pswp-video">';
				$video_wrapper_close = '</a>';
			}

			$video_html = '';
			if ( Embed::get_video_properties( $video_url ) ) {
				$video_attrs   = [ 'loading' => 'lazy' ];
				$embed_params  = $this->get_embed_params( $attachment_id );
				$embed_options = $this->get_embed_options();
				$video_html    = Embed::get_embed_html( $video_url, $embed_params, $embed_options, $video_attrs );
			}

			if ( ! $video_html ) {
				$this->remove_render_attribute( 'video' );
				$attrs = [
					'class'       => 'elementor-video',
					'src'         => $video_url,
					'playsinline' => $settings['play_on_mobile'],
					'muted'       => $settings['mute'],
					'loop'        => $settings['loop'],
					'controls'    => $settings['controls'],
				];
				$this->add_render_attribute(
					'video',
					array_filter( $attrs )
				);
				$video_html = '<video ' . $this->get_render_attribute_string( 'video' ) . '></video>';
			}
			$image      = $video_wrapper . $video_html . $video_overlay_html . $video_wrapper_close;
			$item_class = 'woocommerce-product-gallery_video';
		} else {
            $image = wp_get_attachment_image( $attachment_id, $image_size, false, apply_filters( 'woocommerce_gallery_image_html_attachment_image_params', array(
				'title'                   => _wp_specialchars( get_post_field( 'post_title', $attachment_id ), ENT_QUOTES, 'UTF-8', true ),
				'data-caption'            => _wp_specialchars( get_post_field( 'post_excerpt', $attachment_id ), ENT_QUOTES, 'UTF-8', true ),
				'data-src'                => esc_url( $full_src[0] ),
				'data-large_image'        => esc_url( $full_src[0] ),
				'data-large_image_width'  => esc_attr( $full_src[1] ),
				'data-large_image_height' => esc_attr( $full_src[2] ),
				'class'                   => esc_attr( $main_image ? 'wp-post-image' : '' ),
			), $attachment_id, $image_size, $main_image ) );

			if ( empty( $image ) ) {
				return '';
			}

            $image      = '<a class="dt-pswp-item" data-elementor-open-lightbox="no" href="' . esc_url( $full_src[0] ) . '">' . $image . '</a>';
			$item_class = 'woocommerce-product-gallery__image';
		}

		return '<li class="' . esc_attr( $item_class ) . '">' . $image . '</li>';
	}

	private function get_thumbs_image_html( $attachment_id) {
		$settings = $this->get_settings_for_display();
		presscore_remove_lazy_load_attrs();
		$options = apply_filters( 'elementor_product_image_thumbnail_options', [ 'w' => 500 ] );
		$caption = _wp_specialchars( get_post_field( 'post_excerpt', $attachment_id ), ENT_QUOTES, 'UTF-8', true );
		$full_size = apply_filters( 'woocommerce_gallery_full_size', apply_filters( 'woocommerce_product_thumbnails_large_size', 'full' ) );
		$src                 = wp_get_attachment_image_src( $attachment_id, $full_size );

		if ( ! $src ) {
			return '';
		}

		$video_url = presscore_get_image_video_url( $attachment_id );
		$play_icon = '';
		if ( $video_url && $settings['enable_video'] === 'yes' ) {
			$play_icon = '<a href="#" class="play-icon elementor-icon">' . Icons_Manager::try_get_icon_html( $settings['video_icon'], [ 'aria-hidden' => 'true' ] ) . '</a>';
		}

        $img_html = dt_get_thumb_img( [
            'img_id'  => $attachment_id,
            'custom' => 'data-src="' .  $src[0] . '"',
            'wrap'    => '<li title="' . $caption . '"><div class="slide-wrapper"><img %IMG_CLASS% %SRC% %ALT% %IMG_TITLE% %SIZE% %CUSTOM%/></div>' . $play_icon . '</li>',
            'options' => $options,
            'echo'    => false,
        ] );

		presscore_add_lazy_load_attrs();
		return $img_html;

	/*	$gallery_thumbnail = wc_get_image_size( 'gallery_thumbnail' );
		$thumbnail_size = apply_filters( 'woocommerce_gallery_thumbnail_size', array(
			$gallery_thumbnail['width'],
			$gallery_thumbnail['height'],
		) );
		$image_size = apply_filters( 'woocommerce_gallery_image_size', $thumbnail_size );
		$image = wp_get_attachment_image( $attachment_id, $image_size, false, apply_filters( 'woocommerce_gallery_image_html_attachment_image_params', array(
			'title'                   => _wp_specialchars( get_post_field( 'post_title', $attachment_id ), ENT_QUOTES, 'UTF-8', true ),
			'data-caption'            => _wp_specialchars( get_post_field( 'post_excerpt', $attachment_id ), ENT_QUOTES, 'UTF-8', true ),
		), $attachment_id, $image_size ) );
		if ( empty( $image ) ) {
			return '';
		}

		return '<li class="" ><div class="slide-wrapper">' . $image . '</div></li>';*/
	}

	protected function get_embed_options() {
		return [];
	}

	protected function get_embed_params( $attachment_id ) {
		$settings  = $this->get_settings_for_display();
		$video_url = presscore_get_image_video_url( $attachment_id );

		$params = [];

		if ( $settings['autoplay'] ) {
			//$params['autoplay'] = '1';
			//$params['allow'] = "autoplay";

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
				//'vimeo_title' => 'title',
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

	protected function get_container_data_atts() {
		$settings = $this->get_settings_for_display();

		$data_atts = [
			'scroll-mode' => $settings['thumbs_direction'] === 'bottom' ? 'horizontal' : 'vertical',
		];

		return ' ' . presscore_get_inlide_data_attr( $data_atts );
	}

	protected function register_controls() {
		// Content Tab.
		$this->add_gallery_content_controls();
		$this->add_video_controls();
		$this->add_thumbnails_content_controls();
		$this->add_arrows_content_controls();
		//style tab
		$this->add_big_image_style_controls();
		$this->add_thumbnails_style_controls();
		$this->add_sale_flash_style_controls();
		$this->add_zoom_flash_style_controls();
		$this->add_video_play_style_controls();
		$this->add_arrows_style_controls();
	}

	protected function add_gallery_content_controls() {
		$this->start_controls_section( 'gallery_section', [
			'label' => esc_html__( 'Big image ', 'the7mk2' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
		] );

		$this->add_control( 'gallery_preserve_ratio', [
			'label'        => esc_html__( 'Preserve Image Proportions', 'the7mk2' ),
			'type'         => Controls_Manager::SWITCHER,
			'default'      => 'y',
			'return_value' => 'y',
			'prefix_class' => 'preserve-gallery-ratio-',
		] );

		$this->add_control( 'gallery_ratio', [
			'label'     => esc_html__( 'Big Image Container Ratio', 'the7mk2' ),
			'type'      => Controls_Manager::SLIDER,
			'default'   => [
				'size' => 1,
			],
			'required'  => true,
			'range'     => [
				'px' => [
					'min'  => 0.1,
					'max'  => 2,
					'step' => 0.01,
				],
			],
			'selectors' => [
				'{{WRAPPER}} .dt-product-gallery:before' => 'padding-bottom:  calc( {{SIZE}} * 100% )',
				'{{WRAPPER}}'                            => '--gallery-ratio: {{SIZE}}',
			],
		] );

		$this->add_control( 'gallery_ratio_helper', [
			'label'                                                  => esc_html__( 'Big Image Ratio helper', 'the7mk2' ),
			'type'                                                   => Controls_Manager::HIDDEN,
			'condition'                                              => [
				'gallery_ratio[size]!' => '',
			],
			'default'                                                => 'y',
			'return_value'                                           => 'y',
			'selectors' => [
				'{{WRAPPER}} .dt-product-gallery .dt-product-gallery-wrap' => 'position:absolute',
			],
		] );



		$this->add_control( 'slider_animation', [
			'label'   => esc_html__( 'Animation On Scroll:', 'the7mk2' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'slide',
			'options' => [
				'slide' => 'Slide',
				'fade'  => 'Fade',
			],
		] );

		$this->add_control( 'show_image_zoom', [
			'label'        => esc_html__( 'Zoom Image On Hover', 'the7mk2' ),
			'type'         => Controls_Manager::SWITCHER,
			'label_off'    => esc_html__( 'No', 'the7mk2' ),
			'label_on'     => esc_html__( 'Yes', 'the7mk2' ),
			'separator'    => 'before',
			'default'      => 'yes',
			'return_value' => 'yes',
			'prefix_class' => 'show-image-zoom-',
		] );

		$this->add_control( 'lightbox_on_click', [
			'label'        => esc_html__( 'Open Lightbox On Click', 'the7mk2' ),
			'type'         => Controls_Manager::SWITCHER,
			'label_off'    => esc_html__( 'No', 'the7mk2' ),
			'label_on'     => esc_html__( 'Yes', 'the7mk2' ),
			'separator'    => 'before',
			'return_value' => 'y',
			'prefix_class' => 'lightbox-on-click-',
		] );

		$this->add_control( 'show_onsale_flash', [
			'label'        => esc_html__( 'Sale Flash', 'the7mk2' ),
			'type'         => Controls_Manager::SWITCHER,
			'label_off'    => esc_html__( 'No', 'the7mk2' ),
			'label_on'     => esc_html__( 'Yes', 'the7mk2' ),
			'separator'    => 'before',
			'default'      => 'yes',
			'return_value' => 'yes',
            'render_type' => 'template',
			'selectors'    => [
				'{{WRAPPER}} .dt-product-gallery .onsale' => 'display:block',
			],
		] );

		$this->add_control( 'show_zoom', [
			'label'        => esc_html__( 'Zoom Flash', 'the7mk2' ),
			'type'         => Controls_Manager::SWITCHER,
			'label_off'    => esc_html__( 'No', 'the7mk2' ),
			'label_on'     => esc_html__( 'Yes', 'the7mk2' ),
			'separator'    => 'before',
			'default'      => 'yes',
			'return_value' => 'yes',
		] );

		$this->add_control( 'zoom_icon', [
			'label'     => esc_html__( 'Zoom Icon', 'the7mk2' ),
			'type'      => Controls_Manager::ICONS,
			'default'   => [
				'value'   => 'fas fa-search-plus',
				'library' => 'fa-solid',
			],
			'condition' => [
				'show_zoom' => 'yes',
			],
		] );

		$this->end_controls_section();
	}
	protected function add_video_controls() {
		$this->start_controls_section( 'video_section', [
			'label' => esc_html__( 'Video ', 'the7mk2' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
		] );
		$this->add_control( 'enable_video', [
			'label'        => esc_html__( 'Enable videos', 'the7mk2' ),
			'type'         => Controls_Manager::SWITCHER,
			'label_off'    => esc_html__( 'No', 'the7mk2' ),
			'label_on'     => esc_html__( 'Yes', 'the7mk2' ),
			'description' => esc_html__( 'Insert Video link into ‘Video url’ field when adding/editing an image from Product Gallery.', 'the7mk2' ),
			'separator'    => 'before',
			'default'      => 'yes',
			'return_value' => 'yes',
			'render_type'  => 'template',
		] );
		$this->add_control( 'video_icon', [
			'label'     => esc_html__( 'Video Icon', 'the7mk2' ),
			'type'      => Controls_Manager::ICONS,
			'default'   => [
				'value'   => 'far fa-play-circle',
				'library' => 'fa-regular',
			],
			'condition' => [
				'enable_video' => 'yes',
			],
		] );

		$this->add_control(
			'autoplay',
			[
				'label' => esc_html__( 'Autoplay', 'the7mk2' ),
				'type' => Controls_Manager::SWITCHER,
				'description' => esc_html__( 'Most of browsers autoplay muted videos only.', 'the7mk2' ),
				'frontend_available' => true,
				'condition' => [
					'enable_video' => 'yes',
				],
				'render_type'  => 'template',
				'prefix_class' => 'video-autoplay-',
			]
		);
		$this->add_control(
			'play_on_mobile',
			[
				'label' => esc_html__( 'Play On Mobile', 'the7mk2' ),
				'type' => Controls_Manager::SWITCHER,
				'frontend_available' => true,
				'condition' => [
					'autoplay' => 'yes',
					'enable_video' => 'yes',
				],
			]
		);

		$this->add_control(
			'mute',
			[
				'label' => esc_html__( 'Mute', 'the7mk2' ),
				'type' => Controls_Manager::SWITCHER,
				'frontend_available' => true,
				'condition' => [
					'enable_video' => 'yes',
				],
			]
		);

		$this->add_control(
			'loop',
			[
				'label' => esc_html__( 'Loop', 'the7mk2' ),
				'type' => Controls_Manager::SWITCHER,
				'frontend_available' => true,
				'condition' => [
					'enable_video' => 'yes',
				],
			]
		);
		$this->add_control(
			'controls',
			[
				'label' => esc_html__( 'Player Controls', 'the7mk2' ),
				'type' => Controls_Manager::SWITCHER,
				'frontend_available' => true,
				'condition' => [
					'enable_video' => 'yes',
				],
			]
		);
		$this->end_controls_section();
	}

	protected function add_thumbnails_content_controls() {
		$this->start_controls_section( 'thumbnails_section', [
			'label' => esc_html__( 'Thumbnails ', 'the7mk2' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
		] );

		$this->add_control( 'thumbs_direction', [
			'label'                => esc_html__( 'Thumbnails Position', 'the7mk2' ),
			'type'                 => Controls_Manager::SELECT,
			'default'              => 'bottom',
			'options'              => [
				'left'     => 'Left',
				'bottom'   => 'Bottom',
				'right'    => 'Right',
				'disabled' => 'Disabled',
			],
			'selectors_dictionary' => [
				'bottom'   => 'flex-flow: row wrap; ',
				'left'     => '',
				'right'    => '',
				'disabled' => '',
			],
		] );

		$this->add_control( 'thumbs_items', [
			'label' => esc_html__( 'Number Of Thumbnails', 'the7mk2' ),

			'type'         => Controls_Manager::NUMBER,
			'default'      => 4,
			'min'          => 2,
			'max'          => 20,
			'step'         => 1,
			'required'     => true,
			'conditions'   => [
				'terms' => [
					[
						'name'     => 'thumbs_direction',
						'operator' => '!=',
						'value'    => 'disabled',
					],
				],
			],
			'prefix_class' => 'thumbs-col-num-',
			'selectors'    => [
				'{{WRAPPER}}' => '--thumbs-items: {{VALUE}}',
			],
		] );

		$this->add_control( 'thumbs_preserve_ratio', [
			'label'        => esc_html__( 'Preserve Image Proportions', 'the7mk2' ),
			'type'         => Controls_Manager::SWITCHER,
			'default'      => 'y',
			'return_value' => 'y',
			'prefix_class' => 'preserve-thumb-ratio-',
			'conditions'   => [
				'terms' => [
					[
						'name'     => 'thumbs_direction',
						'operator' => '!=',
						'value'    => 'disabled',
					],
				],
			],
		] );

		$this->add_control( 'thumbs_side_ratio', [
			'label'      => esc_html__( 'Thumbnails Ratio', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'default'    => [
				'size' => 1,
			],
			'required'   => true,
			'range'      => [
				'px' => [
					'min'  => 0.1,
					'max'  => 2,
					'step' => 0.01,
				],
			],
			'conditions' => [
				'terms' => [
					[
						'name'     => 'thumbs_direction',
						'operator' => '!=',
						'value'    => 'bottom',
					],
					[
						'name'     => 'thumbs_direction',
						'operator' => '!=',
						'value'    => 'disabled',
					],
				],
			],

			'selectors' => [
				'{{WRAPPER}}' => '--thumbs_ratio:{{SIZE}}',
			],
		] );

		$this->add_control( 'thumbnails_ratio', [
			'label'      => esc_html__( 'Thumbnails Ratio', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'default'    => [
				'size' => 0.66,
			],
			'range'      => [
				'px' => [
					'min'  => 0.1,
					'max'  => 2,
					'step' => 0.01,
				],
			],
			'conditions' => [
				'terms' => [
					[
						'name'     => 'thumbs_direction',
						'operator' => '==',
						'value'    => 'bottom',
					],
					[
						'name'     => 'thumbs_direction',
						'operator' => '!=',
						'value'    => 'disabled',
					],
					[
						'name'     => 'thumbs_preserve_ratio',
						'operator' => '!=',
						'value'    => 'y',
					],
				],
			],

			'selectors' => [
				'{{WRAPPER}} .dt-product-thumbs .slides li .slide-wrapper' => 'padding-bottom:  calc( {{SIZE}} * 100% )',
			],
		] );

		$this->end_controls_section();
	}

	protected function add_arrows_content_controls() {
		$this->start_controls_section( 'arrows_section', [
			'label' => esc_html__( 'Arrows', 'the7mk2' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
		] );

		$this->add_control( 'arrow_next', [
			'label'   => esc_html__( 'Choose Next Arrow Icon', 'the7mk2' ),
			'type'    => Controls_Manager::ICONS,
			'default' => [
				'value'   => 'fas fa-chevron-right',
				'library' => 'fa-solid',
			],

		] );

		$this->add_control( 'arrow_prev', [
			'label'   => esc_html__( 'Choose Previous Arrow Icon', 'the7mk2' ),
			'type'    => Controls_Manager::ICONS,
			'default' => [
				'value'   => 'fas fa-chevron-left',
				'library' => 'fa-solid',
			],
		] );

		$this->add_control( 'gallery_arrows_display', [
			'label'        => esc_html__( 'Show Big Image Arrows', 'the7mk2' ),
			'type'         => Controls_Manager::SELECT,
			'options'      => [
				'never'  => esc_html__( 'Never', 'the7mk2' ),
				'always' => esc_html__( 'Always', 'the7mk2' ),
				'hover'  => esc_html__( 'On Hover', 'the7mk2' ),
			],
			'default'      => 'hover',
			'prefix_class' => 'gallery-nav-display-',
		] );

		$this->add_control( 'thumbs_arrows_display', [
			'label'        => esc_html__( 'Show Thumbnail Arrows', 'the7mk2' ),
			'type'         => Controls_Manager::SELECT,
			'options'      => [
				'never'  => esc_html__( 'Never', 'the7mk2' ),
				'always' => esc_html__( 'Always', 'the7mk2' ),
				'hover'  => esc_html__( 'On Hover', 'the7mk2' ),
			],
			'default'      => 'hover',
			'prefix_class' => 'thumbs-nav-display-',
		] );

		$this->end_controls_section();
	}

	protected function add_big_image_style_controls() {
		$this->start_controls_section( 'section_gallery_style', [
			'label' => esc_html__( 'Big Image', 'the7mk2' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'      => 'gallery_image_border',
			'selector'  => '#main {{WRAPPER}} .dt-product-gallery .flexslider .flex-viewport, #main {{WRAPPER}} .dt-product-gallery .flexslider > .slides',
			'separator' => 'before',
		] );

		$this->add_responsive_control( 'gallery_image_border_radius', [
			'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors'  => [
				'#main {{WRAPPER}} .dt-product-gallery .flexslider .flex-viewport, #main {{WRAPPER}} .dt-product-gallery .flexslider > .slides' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important',
			],
		] );

		$this->add_control( 'gallery_spacing', [
			'label'      => esc_html__( 'Distance To Thumbnails', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'selectors'  => [
				'{{WRAPPER}} .thumb-position-right .dt-product-thumbs'  => 'margin-left: {{SIZE}}{{UNIT}}',
				'{{WRAPPER}} .thumb-position-left .dt-product-thumbs'   => 'margin-right: {{SIZE}}{{UNIT}}',
				'{{WRAPPER}} .thumb-position-bottom .dt-product-thumbs' => 'margin-top: {{SIZE}}{{UNIT}}',
				'{{WRAPPER}}'                                            => '--gallery-spacing: {{SIZE}}{{UNIT}}',
			],
		] );

		$this->add_control( 'gallery_spacing_empty', [
			'type'         => Controls_Manager::HIDDEN,
			'condition'    => [
				'gallery_spacing[size]' => '',
			],
			'default'      => 'y',
			'return_value' => 'y',
			'selectors'    => [
				'{{WRAPPER}}' => '--gallery-spacing:0px',
			],
		] );

		$this->end_controls_section();
	}
	protected function add_video_style_controls() {
		$this->start_controls_section( 'section_video_style', [
			'label' => esc_html__( 'Video', 'the7mk2' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'      => 'gallery_image_border',
			'selector'  => '#main {{WRAPPER}} .dt-product-gallery .flexslider .flex-viewport, #main {{WRAPPER}} .dt-product-gallery .flexslider > .slides',
			'separator' => 'before',
		] );

		$this->add_responsive_control( 'gallery_image_border_radius', [
			'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors'  => [
				'#main {{WRAPPER}} .dt-product-gallery .flexslider .flex-viewport, #main {{WRAPPER}} .dt-product-gallery .flexslider > .slides' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important',
			],
		] );

		$this->add_control( 'gallery_spacing', [
			'label'      => esc_html__( 'Distance To Thumbnails', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'selectors'  => [
				'{{WRAPPER}} .thumb-position-right .dt-product-thumbs'  => 'margin-left: {{SIZE}}{{UNIT}}',
				'{{WRAPPER}} .thumb-position-left .dt-product-thumbs'   => 'margin-right: {{SIZE}}{{UNIT}}',
				'{{WRAPPER}} .thumb-position-bottom .dt-product-thumbs' => 'margin-top: {{SIZE}}{{UNIT}}',
				'{{WRAPPER}}'                                            => '--gallery-spacing: {{SIZE}}{{UNIT}}',
			],
		] );

		$this->add_control( 'gallery_spacing_empty', [
			'type'         => Controls_Manager::HIDDEN,
			'condition'    => [
				'gallery_spacing[size]' => '',
			],
			'default'      => 'y',
			'return_value' => 'y',
			'selectors'    => [
				'{{WRAPPER}}' => '--gallery-spacing:0px',
			],
		] );

		$this->end_controls_section();
	}

	protected function add_thumbnails_style_controls() {
		$this->start_controls_section( 'section_thumbs_style', [
			'label'      => esc_html__( 'Thumbnails', 'the7mk2' ),
			'tab'        => Controls_Manager::TAB_STYLE,
			'conditions' => [
				'terms' => [
					[
						'name'     => 'thumbs_direction',
						'operator' => '!=',
						'value'    => 'disabled',
					],
				],
			],
		] );

		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => 'thumbs_border',
			'selector' => '.woocommerce {{WRAPPER}} .dt-product-thumbs .slides .slide-wrapper',
			'exclude'  => [
				'color',
			],
		] );

		$this->add_responsive_control( 'thumbs_border_radius', [
			'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors'  => [
				'.woocommerce {{WRAPPER}} .dt-product-thumbs .slides .slide-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
			],
		] );


		$this->add_control( 'thumbs_spacing', [
			'label'      => esc_html__( 'Distance Between Thumbnails', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'selectors'  => [
				'{{WRAPPER}} .thumb-position-bottom .dt-product-thumbs .slides li' => 'margin-right: {{SIZE}}{{UNIT}} !important',
				'{{WRAPPER}} .thumb-position-left .dt-product-thumbs .slides li'   => 'margin-bottom: {{SIZE}}{{UNIT}}',
				'{{WRAPPER}} .thumb-position-right .dt-product-thumbs .slides li'  => 'margin-bottom: {{SIZE}}{{UNIT}}',
				'{{WRAPPER}}'                                                      => '--thumbs-spacing: {{SIZE}}{{UNIT}}',
			],
		] );


		$this->add_control( 'thumbs_spacing_empty', [
			'type'         => Controls_Manager::HIDDEN,
			'condition'    => [
				'thumbs_spacing[size]' => '',
			],
			'default'      => 'y',
			'return_value' => 'y',
			'selectors'    => [
				'{{WRAPPER}}' => '--thumbs-spacing:0px',
			],
		] );

		$this->start_controls_tabs(
			'thumbs_colors',[]
		);

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
					'.woocommerce {{WRAPPER}} .dt-product-thumbs .slides .slide-wrapper' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'thumbs_border_border!' => [ '', 'none' ],
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
					'{{WRAPPER}} .dt-product-thumbs .slides li:not(.flex-active-slide)' => 'opacity: calc({{SIZE}}/100)',
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
					'.woocommerce {{WRAPPER}} .dt-product-thumbs .slides .slide-wrapper:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'thumbs_border_border!' => [ '', 'none' ],
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
					'{{WRAPPER}} .dt-product-thumbs .slides li:hover' => 'opacity: calc({{SIZE}}/100)',
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
					'.woocommerce {{WRAPPER}} .dt-product-thumbs .slides .flex-active-slide .slide-wrapper' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'thumbs_border_border!' => [ '', 'none' ],
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
					'{{WRAPPER}} .dt-product-thumbs .slides li.flex-active-slide' => 'opacity: calc({{SIZE}}/100)',
				],
			]
		);
		$this->end_controls_tab();

		$this->end_controls_tabs();


		$this->end_controls_section();
	}

	protected function add_sale_flash_style_controls() {
		$this->start_controls_section( 'sale_flash_style', [
			'label'     => esc_html__( 'Sale Flash', 'the7mk2' ),
			'tab'       => Controls_Manager::TAB_STYLE,
			'condition' => [
				'show_onsale_flash' => 'yes',
			],
		] );

		$this->add_control( 'onsale_text_color', [
			'label'     => esc_html__( 'Text Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .onsale' => 'color: {{VALUE}}',
			],
		] );

		$this->add_control( 'onsale_text_background_color', [
			'label'     => esc_html__( 'Background Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .onsale' => 'background-color: {{VALUE}}',
			],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'onsale_typography',
			'selector' => '{{WRAPPER}} .onsale',
			'exclude'  => [ 'line_height' ],
		] );

		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => 'onsale_border',
			'selector' => '{{WRAPPER}} .onsale',
			/*'exclude'  => [
				'color',
			],*/
		] );

		$this->add_control( 'onsale_border_radius', [
			'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px', 'em' ],
			'selectors'  => [
				'{{WRAPPER}} .onsale' => 'border-radius: {{SIZE}}{{UNIT}}',
			],
		] );

		$this->add_responsive_control( 'onsale_width', [
			'label'      => esc_html__( 'Width', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px', 'em' ],
			'selectors'  => [
				'{{WRAPPER}} .onsale' => 'min-width: {{SIZE}}{{UNIT}};padding-left: 0; padding-right: 0;',
			],
		] );

		$this->add_responsive_control( 'onsale_height', [
			'label'      => esc_html__( 'Height', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px', 'em' ],
			'selectors'  => [
				'{{WRAPPER}} .onsale' => 'min-height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};padding-top: 0;padding-bottom: 0;',
			],
		] );

		$this->add_control( 'onsale_horizontal_position', [
			'label'                => esc_html__( 'Horizontal Position', 'the7mk2' ),
			'type'                 => Controls_Manager::CHOOSE,
			'options'              => [
				'left'  => [
					'title' => esc_html__( 'Left', 'the7mk2' ),
					'icon'  => 'eicon-h-align-left',
				],
				'right' => [
					'title' => esc_html__( 'Right', 'the7mk2' ),
					'icon'  => 'eicon-h-align-right',
				],
			],
			'selectors'            => [
				'{{WRAPPER}} .onsale' => '{{VALUE}}',
			],
			'selectors_dictionary' => [
				'left'  => 'right: auto; left: 0',
				'right' => 'left: auto; right: 0',
			],
			'default'              => 'left',
			'toggle'               => false,
			'prefix_class'         => 'onsale-h-position-',
		] );

		$this->add_responsive_control( 'onsale_horizontal_distance', [
			'label'      => esc_html__( 'Offset', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px', 'em' ],
			'range'      => [
				'px' => [
					'min' => - 100,
					'max' => 100,
				],
				'em' => [
					'min' => - 5,
					'max' => 5,
				],
			],
			'selectors'  => [
				'{{WRAPPER}}.onsale-h-position-right .onsale' => 'margin-right: {{SIZE}}{{UNIT}};',
				'{{WRAPPER}}.onsale-h-position-left .onsale'  => 'margin-left: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->add_control( 'onsale_vertical_position', [
			'label'                => esc_html__( 'Vertical Position', 'the7mk2' ),
			'type'                 => Controls_Manager::CHOOSE,
			'options'              => [
				'top'    => [
					'title' => esc_html__( 'Top', 'the7mk2' ),
					'icon'  => 'eicon-v-align-top',
				],
				'bottom' => [
					'title' => esc_html__( 'Bottom', 'the7mk2' ),
					'icon'  => 'eicon-v-align-bottom',
				],
			],
			'selectors'            => [
				'{{WRAPPER}} .onsale' => '{{VALUE}}',
			],
			'selectors_dictionary' => [
				'top'    => 'bottom: auto; top: 0',
				'bottom' => 'top: auto; bottom: 0',
			],
			'default'              => 'top',
			'toggle'               => false,
			'prefix_class'         => 'onsale-v-position-',
		] );

		$this->add_responsive_control( 'onsale_vertical_distance', [
			'label'      => esc_html__( 'Offset', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px', 'em' ],
			'range'      => [
				'px' => [
					'min' => - 100,
					'max' => 100,
				],
				'em' => [
					'min' => - 5,
					'max' => 5,
				],
			],
			'selectors'  => [
				'{{WRAPPER}}.onsale-v-position-top .onsale'    => 'margin-top: {{SIZE}}{{UNIT}};',
				'{{WRAPPER}}.onsale-v-position-bottom .onsale' => 'margin-bottom: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'onsale_shadow',
				'selector' => '{{WRAPPER}} .onsale',
			]
		);

		$this->end_controls_section();
	}

	protected function add_zoom_flash_style_controls() {
		$this->start_controls_section( 'zoom_style', [
			'label'     => esc_html__( 'Zoom Flash', 'the7mk2' ),
			'tab'       => Controls_Manager::TAB_STYLE,
			'condition' => [
				'show_zoom' => 'yes',
			],
		] );

		$this->start_controls_tabs( 'zoom_colors' );

		$this->start_controls_tab(
			'zoom_colors_normal',
			[
				'label' => esc_html__( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_control( 'zoom_text_color', [
			'label'     => esc_html__( 'Icon Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .zoom-flash i' => 'color: {{VALUE}}',
				'{{WRAPPER}} .zoom-flash svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
			],
		] );

		$this->add_control( 'zoom_background_color', [
			'label'     => esc_html__( 'Background Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .zoom-flash' => 'background-color: {{VALUE}}',
			],
		] );

		$this->end_controls_tab();

		$this->start_controls_tab(
			'zoom_colors_hover',
			[
				'label' => esc_html__( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control( 'zoom_text_color_hover', [
			'label'     => esc_html__( 'Icon Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .zoom-flash:hover i' => 'color: {{VALUE}}',
				'{{WRAPPER}} .zoom-flash:hover svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
			],
		] );

		$this->add_control( 'zoom_background_color_hover', [
			'label'     => esc_html__( 'Background Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .zoom-flash:hover' => 'background-color: {{VALUE}}',
			],
		] );

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control( 'zoom_text_size', [
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
				'{{WRAPPER}} .zoom-flash' => 'font-size: {{SIZE}}{{UNIT}}',
			],
		] );

		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => 'zoom_border',
			'selector' => '{{WRAPPER}} .zoom-flash',
		] );

		$this->add_control( 'zoom_border_radius', [
			'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px', 'em' ],
			'selectors'  => [
				'{{WRAPPER}} .zoom-flash' => 'border-radius: {{SIZE}}{{UNIT}}',
			],
		] );

		$this->add_responsive_control( 'zoom_width', [
			'label'      => esc_html__( 'Width', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px', 'em' ],
			'selectors'  => [
				'{{WRAPPER}} .zoom-flash' => 'width: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->add_responsive_control( 'zoom_height', [
			'label'      => esc_html__( 'Height', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px', 'em' ],
			'selectors'  => [
				'{{WRAPPER}} .zoom-flash' => 'height: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->add_control( 'zoom_horizontal_position', [
			'label'                => esc_html__( 'Horizontal Position', 'the7mk2' ),
			'type'                 => Controls_Manager::CHOOSE,
			'options'              => [
				'left'  => [
					'title' => esc_html__( 'Left', 'the7mk2' ),
					'icon'  => 'eicon-h-align-left',
				],
				'right' => [
					'title' => esc_html__( 'Right', 'the7mk2' ),
					'icon'  => 'eicon-h-align-right',
				],
			],
			'selectors'            => [
				'{{WRAPPER}} .zoom-flash' => '{{VALUE}}',
			],
			'selectors_dictionary' => [
				'left'  => 'right: auto; left: 0',
				'right' => 'left: auto; right: 0',
			],
			'default'              => 'right',
			'toggle'               => false,
			'prefix_class'         => 'zoom-h-position-',
		] );

		$this->add_responsive_control( 'zoom_horizontal_distance', [
			'label'      => esc_html__( 'Offset', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px', 'em' ],
			'range'      => [
				'px' => [
					'min' => - 100,
					'max' => 100,
				],
				'em' => [
					'min' => - 5,
					'max' => 5,
				],
			],
			'selectors'  => [
				'{{WRAPPER}}.zoom-h-position-right .zoom-flash' => 'margin-right: {{SIZE}}{{UNIT}};',
				'{{WRAPPER}}.zoom-h-position-left .zoom-flash'  => 'margin-left: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->add_control( 'zoom_vertical_position', [
			'label'                => esc_html__( 'Vertical Position', 'the7mk2' ),
			'type'                 => Controls_Manager::CHOOSE,
			'options'              => [
				'top'    => [
					'title' => esc_html__( 'Top', 'the7mk2' ),
					'icon'  => 'eicon-v-align-top',
				],
				'bottom' => [
					'title' => esc_html__( 'Bottom', 'the7mk2' ),
					'icon'  => 'eicon-v-align-bottom',
				],
			],
			'selectors'            => [
				'{{WRAPPER}} .zoom-flash' => '{{VALUE}}',
			],
			'selectors_dictionary' => [
				'top'    => 'bottom: auto; top: 0',
				'bottom' => 'top: auto; bottom: 0',
			],
			'default'              => 'top',
			'toggle'               => false,
			'prefix_class'         => 'zoom-v-position-',
		] );

		$this->add_responsive_control( 'zoom_vertical_distance', [
			'label'      => esc_html__( 'Offset', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px', 'em' ],
			'range'      => [
				'px' => [
					'min' => - 100,
					'max' => 100,
				],
				'em' => [
					'min' => - 5,
					'max' => 5,
				],
			],
			'selectors'  => [
				'{{WRAPPER}}.zoom-v-position-top .zoom-flash'    => 'margin-top: {{SIZE}}{{UNIT}};',
				'{{WRAPPER}}.zoom-v-position-bottom .zoom-flash' => 'margin-bottom: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'zoom_shadow',
				'selector' => '{{WRAPPER}} .zoom-flash',
			]
		);

		$this->end_controls_section();
	}
	protected function add_video_play_style_controls() {
		$this->start_controls_section( 'video_play_style', [
			'label'     => esc_html__( 'Video Icon', 'the7mk2' ),
			'tab'       => Controls_Manager::TAB_STYLE,
			'condition' => [
				'enable_video' => 'yes',
			],
		] );

		$this->add_responsive_control( 'video_play_size', [
			'label'      => esc_html__( 'Big Image Play Icon Size', 'the7mk2' ),
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
				'{{WRAPPER}} .dt-product-gallery .play-icon' => 'font-size: {{SIZE}}{{UNIT}}',
			],
		] );
		$this->add_responsive_control( 'thumb_video_play_size', [
			'label'      => esc_html__( 'Thumbnail Play Icon Size', 'the7mk2' ),
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
				'{{WRAPPER}} .dt-product-thumbs .play-icon' => 'font-size: {{SIZE}}{{UNIT}}',
			],
		] );
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'video_play_border',
				'label'    => esc_html__( 'Border', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .play-icon',
				'exclude'  => [
					'color',
				],
			]
		);

		$this->add_control( 'video_play_border_radius', [
			'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px', 'em' ],
			'selectors'  => [
				'{{WRAPPER}} .play-icon' => 'border-radius: {{SIZE}}{{UNIT}}',
			],
		] );

		$this->add_responsive_control( 'video_play_width', [
			'label'      => esc_html__( 'Big Image Play Width', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px', 'em' ],
			'selectors'  => [
				'{{WRAPPER}} .dt-product-gallery .play-icon' => 'min-width: {{SIZE}}{{UNIT}};',
			],
		] );
		$this->add_responsive_control( 'thumb_video_play_width', [
			'label'      => esc_html__( 'Thumbnail Play Width', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px', 'em' ],
			'selectors'  => [
				'{{WRAPPER}} .dt-product-thumbs .play-icon' => 'min-width: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->add_responsive_control( 'video_play_height', [
			'label'      => esc_html__( 'Big Image Play Height', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px', 'em' ],
			'selectors'  => [
				'{{WRAPPER}} .dt-product-gallery .play-icon' => 'min-height: {{SIZE}}{{UNIT}};',
			],
		] );
		$this->add_responsive_control( 'thumb_video_play_height', [
			'label'      => esc_html__( 'Thumbnail Play Height', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px', 'em' ],
			'selectors'  => [
				'{{WRAPPER}} .dt-product-thumbs .play-icon' => 'min-height: {{SIZE}}{{UNIT}};',
			],
		] );
		$this->start_controls_tabs( 'video_play_colors' );

		$this->start_controls_tab(
			'video_play_colors_normal',
			[
				'label' => esc_html__( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_control( 'video_play_color', [
			'label'     => esc_html__( 'Icon Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .play-icon i' => 'color: {{VALUE}}',
				'{{WRAPPER}} .play-icon svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
			],
		] );
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
		$this->add_control( 'video_play_background_color', [
			'label'     => esc_html__( 'Background Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .play-icon' => 'background-color: {{VALUE}}',
			],
		] );

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'video_play_shadow',
				'selector' => '{{WRAPPER}} .play-icon',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'video_play_colors_hover',
			[
				'label' => esc_html__( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control( 'video_play_color_hover', [
			'label'     => esc_html__( 'Icon Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .woocommerce-product-gallery_video:hover .play-icon i, {{WRAPPER}} .dt-product-thumbs .slides li:hover .play-icon i' => 'color: {{VALUE}}',
				'{{WRAPPER}} .woocommerce-product-gallery_video:hover .play-icon svg, {{WRAPPER}} .dt-product-thumbs .slides li:hover .play-icon svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
			],
		] );
		$this->add_control(
			'video_play_border_color_hover',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .woocommerce-product-gallery_video:hover .play-icon, {{WRAPPER}} .dt-product-thumbs .slides li:hover .play-icon' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'video_play_border_border!' => [ '', 'none' ],
				],
			]
		);
		$this->add_control( 'video_play_background_color_hover', [
			'label'     => esc_html__( 'Background Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .woocommerce-product-gallery_video:hover .play-icon, {{WRAPPER}} .dt-product-thumbs .slides li:hover .play-icon' => 'background-color: {{VALUE}}',
			],
		] );

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'video_play_shadow_hover',
				'selector' => '{{WRAPPER}} .woocommerce-product-gallery_video:hover .play-icon, {{WRAPPER}} .dt-product-thumbs .slides li:hover .play-icon',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function add_arrows_style_controls() {
		$this->start_controls_section( 'arrows_style', [
			'label'      => esc_html__( 'Arrows', 'the7mk2' ),
			'tab'        => Controls_Manager::TAB_STYLE,
			'conditions' => [
				'relation' => 'or',
				'terms'    => [
					[
						'name'     => 'gallery_arrows_display',
						'operator' => '!=',
						'value'    => 'never',
					],
					[
						'name'     => 'thumbs_arrows_display',
						'operator' => '!=',
						'value'    => 'never',
					],
				],
			],
		] );


		$this->add_responsive_control( 'gallery_arrow_icon_size', [
			'label'      => esc_html__( 'Big Image Arrows Icon Size', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'default'    => [
				'unit' => 'px',
				'size' => 16,
			],
			'size_units' => [ 'px' ],
			'range'      => [
				'px' => [
					'min'  => 0,
					'max'  => 200,
					'step' => 1,
				],
			],
			'conditions' => [
				'relation' => 'or',
				'terms'    => [
					[
						'name'     => 'gallery_arrows_display',
						'operator' => '!=',
						'value'    => 'never',
					],
				],
			],
			'selectors'  => [
				'{{WRAPPER}}  .dt-product-gallery .flex-direction-nav > li > a' => 'font-size: {{SIZE}}{{UNIT}};',
				'{{WRAPPER}}  .dt-product-gallery .flex-direction-nav > li > a svg' => 'min-width: {{SIZE}}{{UNIT}};min-height: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->add_responsive_control( 'gallery_arrow_h_offset', [
			'label'      => esc_html__( 'Big Image Arrows Offset', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'default'    => [
				'unit' => 'px',
				'size' => 0,
			],
			'conditions' => [
				'relation' => 'or',
				'terms'    => [
					[
						'name'     => 'gallery_arrows_display',
						'operator' => '!=',
						'value'    => 'never',
					],
				],
			],
			'size_units' => [ 'px' ],
			'range'      => [
				'px' => [
					'min'  => - 200,
					'max'  => 200,
					'step' => 1,
				],
			],
			'selectors'  => [
				'{{WRAPPER}} .dt-product-gallery .flex-direction-nav > .flex-nav-prev' => 'left: {{SIZE}}{{UNIT}};',
				'{{WRAPPER}} .dt-product-gallery .flex-direction-nav > .flex-nav-next' => 'right: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->add_responsive_control( 'thumbs_arrow_icon_size', [
			'label'      => esc_html__( 'Thumbnail Arrows Icon Size', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'default'    => [
				'unit' => 'px',
				'size' => 16,
			],
			'size_units' => [ 'px' ],
			'range'      => [
				'px' => [
					'min'  => 0,
					'max'  => 200,
					'step' => 1,
				],
			],
			'conditions' => [
				'terms' => [
					[
						'name'     => 'thumbs_direction',
						'operator' => '!=',
						'value'    => 'disabled',
					],
					[
						'name'     => 'thumbs_arrows_display',
						'operator' => '!=',
						'value'    => 'never',
					],
				],
			],
			'selectors'  => [
				'{{WRAPPER}} .dt-product-thumbs .flex-direction-nav > li > a' => 'font-size: {{SIZE}}{{UNIT}};',
				'{{WRAPPER}} .dt-product-thumbs .flex-direction-nav > li > a svg' => 'min-width: {{SIZE}}{{UNIT}};min-height: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->add_responsive_control( 'thumbs_arrow_h_offset_bottom', [
			'label'      => esc_html__( 'Thumbnail Arrows Offset', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'default'    => [
				'unit' => 'px',
				'size' => 0,
			],
			'size_units' => [ 'px' ],
			'range'      => [
				'px' => [
					'min'  => - 200,
					'max'  => 200,
					'step' => 1,
				],
			],
			'conditions' => [
				'terms' => [
					[
						'name'     => 'thumbs_direction',
						'operator' => '==',
						'value'    => 'bottom',
					],
					[
						'name'     => 'thumbs_direction',
						'operator' => '!=',
						'value'    => 'disabled',
					],
					[
						'name'     => 'thumbs_arrows_display',
						'operator' => '!=',
						'value'    => 'never',
					],
				],
			],
			'selectors'  => [
				'{{WRAPPER}} .dt-product-thumbs .flex-direction-nav > .flex-nav-prev' => 'left: {{SIZE}}{{UNIT}};',
				'{{WRAPPER}} .dt-product-thumbs .flex-direction-nav > .flex-nav-next' => 'right: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->add_responsive_control( 'thumbs_arrow_v_offset_not_bottom', [
			'label'      => esc_html__( 'Thumbnail Arrows Offset', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'default'    => [
				'unit' => 'px',
				'size' => 0,
			],
			'size_units' => [ 'px' ],
			'range'      => [
				'px' => [
					'min'  => - 1000,
					'max'  => 1000,
					'step' => 1,
				],
			],
			'conditions' => [
				'terms' => [
					[
						'name'     => 'thumbs_direction',
						'operator' => '!=',
						'value'    => 'bottom',
					],
					[
						'name'     => 'thumbs_direction',
						'operator' => '!=',
						'value'    => 'disabled',
					],
				],
			],
			'selectors'  => [
				'{{WRAPPER}} .dt-product-thumbs .flex-direction-nav > .flex-nav-prev' => 'top: {{SIZE}}{{UNIT}};',
				'{{WRAPPER}} .dt-product-thumbs .flex-direction-nav > .flex-nav-next' => 'bottom: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->start_controls_tabs( 'arrows_style_tabs' );

		$this->start_controls_tab( 'arrows_colors', [
			'label' => esc_html__( 'Normal', 'the7mk2' ),
		] );

		$this->add_control( 'arrow_icon_color', [
			'label'     => esc_html__( 'Arrow Icon Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'alpha'     => true,
			'default'   => '',
			'selectors' => [
				'{{WRAPPER}} .flex-direction-nav > li > a' => 'color: {{VALUE}};',
				'{{WRAPPER}} .flex-direction-nav > li > a svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
			],
		] );
		$this->end_controls_tab();

		$this->start_controls_tab( 'arrows_hover_colors', [
			'label' => esc_html__( 'Hover', 'the7mk2' ),
		] );

		$this->add_control( 'arrow_icon_color_hover', [
			'label'     => esc_html__( 'Arrow Icon Color Hover', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'alpha'     => true,
			'default'   => '',
			'selectors' => [
				'{{WRAPPER}} .flex-direction-nav > li > a:hover' => 'color: {{VALUE}};',
				'{{WRAPPER}} .flex-direction-nav > li > a:hover svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
			],
		] );
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}
}
