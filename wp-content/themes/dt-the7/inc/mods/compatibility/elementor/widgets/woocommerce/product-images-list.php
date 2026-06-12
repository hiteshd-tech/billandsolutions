<?php

namespace The7\Mods\Compatibility\Elementor\Widgets\Woocommerce;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Icons_Manager;
use Elementor\Embed;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Image_Aspect_Ratio;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Image_Size;

defined( 'ABSPATH' ) || exit;

/**
 * Product Images List widget class.
 */
class Product_Images_List extends The7_Elementor_Widget_Base {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'the7-woocommerce-product-images-list';
	}

	/**
	 * @return string
	 */
	protected function the7_title() {
		return esc_html__( 'Product Images List', 'the7mk2' );
	}

	/**
	 * @return string
	 */
	protected function the7_icon() {
		return 'eicon-gallery-grid';
	}

	/**
	 * @return string[]
	 */
	protected function the7_categories() {
		return [ 'woocommerce-elements-single' ];
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
		the7_register_style(
			$this->get_name(),
			THE7_ELEMENTOR_CSS_URI . '/the7-woocommerce-product-image-list',
			[ 'the7-simple-grid' ]
		);

		the7_register_script_in_footer(
			$this->get_name(),
			THE7_ELEMENTOR_JS_URI . '/the7-woocommerce-image-list-widget.js',
			[ 'wc-single-product', 'the7-elementor-frontend-common' ]
		);
	}

	/**
	 * @return string[]
	 */
	protected function the7_keywords() {
		return [ 'woocommerce', 'shop', 'store', 'image', 'product', 'gallery', 'lightbox' ];
	}

	/**
	 * @return void
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		global $product;

		$product = wc_get_product();
		if ( ! $product ) {
			return;
		}

		$video_lightbox_disable = '';
		if ( $settings['open_lightbox'] === 'y' && $settings['video_autoplay'] ) {
			$video_lightbox_disable = 'dt-pswp-item-no-click';
		}

		$gallery_image_ids = (array) $product->get_gallery_image_ids();
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
					'post-thumbnail-rollover',
					$this->template( Image_Size::class )->get_wrapper_class(),
					$this->template( Image_Aspect_Ratio::class )->get_wrapper_class(),
				]
			)
		);

		$layout_class = '';
		if ( count( $gallery_image_ids ) === 1 ) {
			$layout_class = ' one-product-img';
		}
		$this->add_render_attribute( '_wrapper', 'class', $layout_class );
		?>
		<div class="dt-product-gallery the7-scrollbar">
			<div class="product-list-wrap dt-gallery-container sGrid-container">
					<?php
					foreach ( $gallery_image_ids as $gallery_image_id ) {
						$video_url = presscore_get_image_video_url( $gallery_image_id );
						if ( ! empty( $video_url ) && $settings['enable_video'] === 'yes' ) {
							// Overlay.
							$video_overlay      = '';
							$video_overlay_html = '';
							if ( ! $settings['video_autoplay'] || ! $settings['play_on_mobile'] ) {
								$play_icon = '';
								if ( $settings['video_icon']['value'] !== '' ) {
									$play_icon = '<span class="play-icon elementor-icon">' . Icons_Manager::try_get_icon_html( $settings['video_icon'], [ 'aria-hidden' => 'true' ] ) . '</span>';
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
								$embed_options = [];
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
							echo '<div class="the7-grid-item "><div class="the7-image-wrapper">' . $video . '</div></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						} else {
							echo '<div class="the7-grid-item "><div class="the7-image-wrapper">';
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
									echo $this->template( Image_Size::class )->get_image( $gallery_image_id, $img_att ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								}
							);
							echo '</div>';
							echo '</div>';
						}
					}
					?>
			</div>
		</div>
		<?php
	}

	/**
	 * @param string   $wrapper_class   The wrapper class.
	 * @param int      $img_id          The image ID.
	 * @param callable $render_callback The render callback.
	 *
	 * @return void
	 */
	protected function render_wrapped_image( $wrapper_class, $img_id, $render_callback ) {
		$settings = $this->get_settings_for_display();
		if ( $settings['open_lightbox'] === 'y' ) {
			$this->remove_render_attribute( 'link' );
			$this->add_link_attributes(
				'link',
				[
					'url' => wp_get_attachment_url( $img_id ),
				]
			);
			$image_wrapper       = '<a class="dt-pswp-item ' . esc_attr( $wrapper_class ) . '" ' . $this->get_render_attribute_string( 'link' ) . ' data-elementor-open-lightbox="no" data-e-disable-page-transition="true">';
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
	 * @param int $attachment_id The attachment ID.
	 *
	 * @return string[]
	 */
	protected function get_embed_params( $attachment_id ) {
		$settings  = $this->get_settings_for_display();
		$video_url = presscore_get_image_video_url( $attachment_id );
		$params    = [
			'start' => '1',
		];
		if ( $settings['video_autoplay'] && $settings['play_on_mobile'] ) {
			$params['playsinline'] = '1';
		}

		$params_dictionary = [];
		if ( strpos( $video_url, 'youtube' ) !== false ) {
			$params_dictionary = [
				'loop',
				'mute',
				'controls',
			];

			$params['wmode']       = 'opaque';
			$params['enablejsapi'] = '1';
			if ( $settings['loop'] ) {
				$video_properties   = Embed::get_video_properties( $video_url );
				$params['playlist'] = $video_properties['video_id'];
			}
		} elseif ( strpos( $video_url, 'vimeo' ) !== false ) {
			$params_dictionary = [
				'loop',
				'mute' => 'muted',
			];

			$params['autopause'] = '0';
		}

		foreach ( $params_dictionary as $key => $param_name ) {
			$setting_name          = is_string( $key ) ? $key : $param_name;
			$params[ $param_name ] = $settings[ $setting_name ] ? '1' : '0';
		}

		return $params;
	}

	/**
	 * @return void
	 */
	protected function register_controls() {
		// Content Tab.
		$this->add_gallery_content_controls();
		$this->add_video_controls();

		// Style Tab.
		$this->add_image_style_controls();
		$this->add_video_play_style_controls();
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

		$selector       = '{{WRAPPER}} .sGrid-container';
		$layout_options = [
			'list'     => esc_html__( 'List', 'the7mk2' ),
			'scroller' => esc_html__( 'Scroller', 'the7mk2' ),
		];
		$this->add_responsive_control(
			'layout',
			[
				'label'                => esc_html__( 'Layout', 'the7mk2' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => 'list',
				'frontend_available'   => true,
				'options'              => $layout_options,
				'device_args'          => $this->generate_device_args(
					[
						'default' => '',
						'options' => [ '' => esc_html__( 'Default', 'the7mk2' ) ] + $layout_options,
					]
				),
				'selectors_dictionary' => [
					'list'     => $this->combine_to_css_vars_definition_string(
						[
							'list-display'          => 'grid',
							'row-width'             => '100%',
							'grid-auto-flow'        => 'unset',
							'grid-auto-columns'     => 'var(--alternation-template-columns, unset)',
							'overflow-x'            => 'visible',
							'overflow-y'            => 'visible',
							'grid-template-columns' => 'var(--alternation-template-columns, repeat(var(--grid-columns), 1fr))',
							'arrows-show'           => 'none',
							'bullets-show'          => 'none',
							'grid-template-areas'   => 'var(--grid-template)',
							'img-grid-area'         => 'unset',
							'img-wrap-shadow'       => 'var(--img-box-shadow, none)',
							'img-wrap-shadow-hover' => 'var(--img-box-shadow-hover, none)',
						]
					),
					'scroller' => $this->combine_to_css_vars_definition_string(
						[
							'list-display'          => 'flex',
							'row-width'             => 'max-content;',
							'grid-auto-flow'        => 'column',
							'grid-auto-columns'     => 'var(--columns_width)',
							'overflow-x'            => 'scroll',
							'overflow-y'            => 'hidden',
							'grid-template-columns' => 'none',
							'arrows-show'           => 'none',
							'bullets-show'          => 'none',
							'img-grid-area'         => 'auto',
							'grid-template-areas'   => 'none',
							'img-wrap-shadow'       => 'none',
							'img-wrap-shadow-hover' => 'none',
						]
					),
				],
				'selectors'            => [
					'{{WRAPPER}}' => '{{VALUE}}',
				],
				'render_type'          => 'template',
				'prefix_class'         => 'layout-',
			]
		);

		$this->add_responsive_control(
			'slides_gap',
			[
				'label'              => esc_html__( 'Gap Between Images', 'the7mk2' ),
				'type'               => Controls_Manager::SLIDER,
				'default'            => [
					'unit' => 'px',
					'size' => 30,
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
			]
		);

		$this->add_control(
			'list_section',
			[
				'label'     => esc_html__( 'List Layout', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'alternation',
			[
				'label'              => esc_html__( 'Photos Order Alternation', 'the7mk2' ),
				'type'               => Controls_Manager::SWITCHER,
				'description'        => esc_html__( 'Works only if “List” is chosen in Layout settings', 'the7mk2' ),
				'label_off'          => esc_html__( 'No', 'the7mk2' ),
				'label_on'           => esc_html__( 'Yes', 'the7mk2' ),
				'default'            => 'no',
				'return_value'       => 'yes',
				'prefix_class'       => 'alternation-',
				'frontend_available' => true,
			]
		);

		$alternation_layout_options = [
			'1_1'   => esc_html__( '1/1/1/1', 'the7mk2' ),
			'1_2'   => esc_html__( '1/2/1/2', 'the7mk2' ),
			'2_1'   => esc_html__( '2/1/2/1', 'the7mk2' ),
			'1_3'   => esc_html__( '1/3/1/3', 'the7mk2' ),
			'2_3'   => esc_html__( '2/3/2/3', 'the7mk2' ),
			'2_4'   => esc_html__( '2/4/2/4', 'the7mk2' ),
			'1_2_3' => esc_html__( '1/2/3/1/2/3', 'the7mk2' ),
			'1_3_2' => esc_html__( '1/3/2/1/3/2', 'the7mk2' ),
			'2_1_3' => esc_html__( '2/1/3/2/1/3', 'the7mk2' ),
		];
		$this->add_responsive_control(
			'alternation_layout',
			[
				'label'                => esc_html__( 'Alternation Layout', 'the7mk2' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => '1_2',
				'options'              => $alternation_layout_options,
				'device_args'          => $this->generate_device_args(
					[
						'default' => '',
						'options' => [ '' => esc_html__( 'Default', 'the7mk2' ) ] + $alternation_layout_options,
					]
				),
				'selectors_dictionary' => [
					'1_1'   => $this->combine_to_css_vars_definition_string(
						[
							'alternation-template-columns' => 'auto',
							'grid-area-img1'               => '1 / 1 / 1 / 7',
							'grid-area-img2'               => '2 / 1 / 2 / 7',
							'grid-area-img3'               => '3 / 1 / 3 / 7',
							'grid-area-img4'               => '4 / 1 / 4 / 7',
							'grid-area-img5'               => '5 / 1 / 5 / 7',
							'grid-area-img6'               => '6 / 1 / 6 / 7',
							'grid-area-img7'               => '7 / 1 / 7 / 7',
							'grid-area-img8'               => '8 / 1 / 8 / 7',
							'grid-area-img9'               => '9 / 1 / 9 / 7',
							'grid-area-img10'              => '10 / 1 / 10 / 7',
							'grid-area-img11'              => '11 / 1 / 11 / 7',
							'grid-area-img12'              => '12 / 1 / 12 / 7',
						]
					),
					'1_2'   => $this->combine_to_css_vars_definition_string(
						[
							'alternation-template-columns' => 'auto',
							'grid-area-img1'               => '1 / 1 / 1 / 7',
							'grid-area-img2'               => '2 / 1 / 2 / 4',
							'grid-area-img3'               => '2 / 4 / 2 / 7',
							'grid-area-img4'               => '3 / 1 / 3 / 7',
							'grid-area-img5'               => '4 / 1 / 4 / 4',
							'grid-area-img6'               => '4 / 4 / 4 / 7',
							'grid-area-img7'               => '5 / 1 / 5 / 7',
							'grid-area-img8'               => '6 / 1 / 6 / 4',
							'grid-area-img9'               => '6 / 4 / 6 / 7',
							'grid-area-img10'              => '7 / 1 / 7 / 7',
							'grid-area-img11'              => '8 / 1 / 8 / 4',
							'grid-area-img12'              => '8 / 4 / 8 / 7',
						]
					),
					'2_1'   => $this->combine_to_css_vars_definition_string(
						[
							'alternation-template-columns' => 'auto',
							'grid-area-img1'               => '1 / 1 / 1 / 4',
							'grid-area-img2'               => '1 / 4 / 1 / 7',
							'grid-area-img3'               => '2 / 1 / 2 / 7',
							'grid-area-img4'               => '3 / 1 / 3 / 4',
							'grid-area-img5'               => '3 / 4 / 3 / 7',
							'grid-area-img6'               => '4 / 1 / 4 / 7',
							'grid-area-img7'               => '5 / 1 / 5 / 4',
							'grid-area-img8'               => '5 / 4 / 5 / 7',
							'grid-area-img9'               => '6 / 1 / 6 / 7',
							'grid-area-img10'              => '7 / 1 / 7 / 4',
							'grid-area-img11'              => '7 / 4 / 7 / 7',
							'grid-area-img12'              => '8 / 1 / 8 / 7',
						]
					),
					'1_3'   => $this->combine_to_css_vars_definition_string(
						[
							'alternation-template-columns' => 'auto',
							'grid-area-img1'               => '1 / 1 / 1 / 7',
							'grid-area-img2'               => '2 / 1 / 2 / 3',
							'grid-area-img3'               => '2 / 3 / 2 / 5',
							'grid-area-img4'               => '2 / 5 / 2 / 7',
							'grid-area-img5'               => '3 / 1 / 3 / 7',
							'grid-area-img6'               => '4 / 1 / 4 / 3',
							'grid-area-img7'               => '4 / 3 / 4 / 5',
							'grid-area-img8'               => '4 / 5 / 4 / 7',
							'grid-area-img9'               => '5 / 1 / 5 / 7',
							'grid-area-img10'              => '6 / 1 / 6 / 3',
							'grid-area-img11'              => '6 / 3 / 6 / 5',
							'grid-area-img12'              => '6 / 5 / 6 / 7',
						]
					),
					'2_3'   => $this->combine_to_css_vars_definition_string(
						[
							'alternation-template-columns' => 'auto',
							'grid-area-img1'               => '1 / 1 / 1 / 4',
							'grid-area-img2'               => '1 / 4 / 1 / 7',
							'grid-area-img3'               => '2 / 1 / 2 / 3',
							'grid-area-img4'               => '2 / 3 / 2 / 5',
							'grid-area-img5'               => '2 / 5 / 2 / 7',
							'grid-area-img6'               => '3 / 1 / 3 / 4',
							'grid-area-img7'               => '3 / 4 / 3 / 7',
							'grid-area-img8'               => '4 / 1 / 4 / 3',
							'grid-area-img9'               => '4 / 3 / 4 / 5',
							'grid-area-img10'              => '4 / 4 / 4 / 7',
							'grid-area-img11'              => '5 / 1 / 5 / 4',
							'grid-area-img12'              => '5 / 4 / 5 / 7',
						]
					),
					'2_4'   => $this->combine_to_css_vars_definition_string(
						[
							'alternation-template-columns' => 'auto',
							'grid-area-img1'               => '1 / 1 / 1 / 5',
							'grid-area-img2'               => '1 / 5 / 1 / 9',
							'grid-area-img3'               => '2 / 1 / 2 / 3',
							'grid-area-img4'               => '2 / 3 / 2 / 5',
							'grid-area-img5'               => '2 / 5 / 2 / 7',
							'grid-area-img6'               => '2 / 7 / 2 / 9',
							'grid-area-img7'               => '3 / 1 / 3 / 5',
							'grid-area-img8'               => '3 / 5 / 3 / 9',
							'grid-area-img9'               => '4 / 1 / 4 / 3',
							'grid-area-img10'              => '4 / 3 / 4 / 5',
							'grid-area-img11'              => '4 / 5 / 4 / 7',
							'grid-area-img12'              => '4 / 7 / 4 / 9',
						]
					),
					'1_2_3' => $this->combine_to_css_vars_definition_string(
						[
							'alternation-template-columns' => 'auto',
							'grid-area-img1'               => '1 / 1 / 1 / 7',
							'grid-area-img2'               => '2 / 1 / 2 / 4',
							'grid-area-img3'               => '2 / 4 / 2 / 7',
							'grid-area-img4'               => '3 / 1 /3 / 3',
							'grid-area-img5'               => '3 / 3 / 3/ 5',
							'grid-area-img6'               => '3 / 5 / 3 / 7',
							'grid-area-img7'               => '4 / 1 / 4 / 7',
							'grid-area-img8'               => '4 / 1 / 4 / 3',
							'grid-area-img9'               => '4 / 3 / 4 / 5',
							'grid-area-img10'              => '4 / 4 / 4 / 7',
							'grid-area-img11'              => '5 / 1 / 5 / 4',
							'grid-area-img12'              => '5 / 4 / 5 / 7',
						]
					),
					'1_3_2' => $this->combine_to_css_vars_definition_string(
						[
							'alternation-template-columns' => 'auto',
							'grid-area-img1'               => '1 / 1 / 1 / 7',
							'grid-area-img2'               => '2 / 1 / 2 / 3',
							'grid-area-img3'               => '2 / 3 / 2 / 5',
							'grid-area-img4'               => '2 / 5 /2 / 7',
							'grid-area-img5'               => '3 / 1 / 3/ 4',
							'grid-area-img6'               => '3 / 4 / 3 / 7',
							'grid-area-img7'               => '4 / 1 / 4 / 7',
							'grid-area-img8'               => '5 / 1 / 5 / 3',
							'grid-area-img9'               => '5 / 3 / 5 / 5',
							'grid-area-img10'              => '5 / 4 / 5 / 7',
							'grid-area-img11'              => '6 / 1 / 6 / 4',
							'grid-area-img12'              => '6 / 4 / 6 / 7',
						]
					),
					'2_1_3' => $this->combine_to_css_vars_definition_string(
						[
							'alternation-template-columns' => 'auto',
							'grid-area-img1'               => '1 / 1 / 1 / 4',
							'grid-area-img2'               => '1 / 4 / 1 / 7',
							'grid-area-img3'               => '2 / 1 / 2 / 7',
							'grid-area-img4'               => '3 / 1 / 3 / 3',
							'grid-area-img5'               => '3 / 3 / 3/ 5',
							'grid-area-img6'               => '3 / 5 / 3 / 7',
							'grid-area-img7'               => '4 / 1 / 4 / 4',
							'grid-area-img8'               => '4 / 4 / 4 / 7',
							'grid-area-img9'               => '5 / 1 / 5 / 7',
							'grid-area-img10'              => '6 / 1 / 6 / 3',
							'grid-area-img11'              => '6 / 3 / 6 / 5',
							'grid-area-img12'              => '6 / 5 / 6 / 7',
						]
					),
				],
				'selectors'            => [
					'{{WRAPPER}}' => '{{VALUE}}',
				],
				'condition'            => [
					'alternation' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label'          => esc_html__( 'Columns', 'the7mk2' ),
				'type'           => Controls_Manager::NUMBER,
				'description'    => esc_html__( 'Works only if “List” is chosen in Layout settings', 'the7mk2' ),
				'default'        => 3,
				'tablet_default' => 2,
				'mobile_default' => 1,
				'min'            => 1,
				'max'            => 12,
				'selectors'      => [
					$selector => '--grid-columns: {{SIZE}}; --grid-template: none;',
				],
				'condition'      => [
					'alternation!' => 'yes',
				],
			]
		);

		$this->add_control(
			'full_width_first_img',
			[
				'label'        => esc_html__( 'Make first image fullwidth', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'description'  => esc_html__( 'Works only if “List” is chosen in Layout settings', 'the7mk2' ),
				'label_off'    => esc_html__( 'No', 'the7mk2' ),
				'label_on'     => esc_html__( 'Yes', 'the7mk2' ),
				'default'      => 'yes',
				'return_value' => 'yes',
				'selectors'    => [
					'{{WRAPPER}} .sGrid-container :first-child' => 'grid-column: 1 / -1',
				],
				'condition'    => [
					'alternation!' => 'yes',
				],
			]
		);

		$this->add_control(
			'scroller_section',
			[
				'label'     => esc_html__( 'Scroller Layout', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'columns_width',
			[
				'label'       => esc_html__( 'Max Width', 'the7mk2' ),
				'type'        => Controls_Manager::SLIDER,
				'description' => esc_html__( 'Works only if “Scroller” is chosen in Layout settings', 'the7mk2' ),
				'default'     => [
					'unit' => '%',
					'size' => 90,
				],
				'size_units'  => [ 'px', '%' ],
				'selectors'   => [
					'{{WRAPPER}}' => '--columns_width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
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
					'{{WRAPPER}} .dt-product-gallery .the7-image-wrapper,  {{WRAPPER}} .dt-product-gallery .post-thumbnail-rollover, {{WRAPPER}} .dt-product-gallery .the7-image-wrapper img, {{WRAPPER}} .gallery-video-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
				'selector'       => '{{WRAPPER}} .post-thumbnail-rollover:before, {{WRAPPER}} .post-thumbnail-rollover:after, {{WRAPPER}} .the7-video-overlay:before, {{WRAPPER}} .the7-video-overlay:after { transition: none; }
				{{WRAPPER}} .post-thumbnail-rollover:before,
				{{WRAPPER}} .post-thumbnail-rollover:after,
				{{WRAPPER}} .the7-video-overlay:before,
				{{WRAPPER}} .the7-video-overlay:after
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

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'image_shadow',
				'selector' => '{{WRAPPER}} .the7-image-wrapper',
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'     => 'image_filters',
				'selector' => '
					{{WRAPPER}} .dt-product-gallery img, 
					{{WRAPPER}} .the7-video-overlay, 
					{{WRAPPER}} video, 
					{{WRAPPER}} iframe',
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
					'{{WRAPPER}} .dt-product-gallery img, 
					{{WRAPPER}} .the7-video-overlay, 
					{{WRAPPER}} .playing-video video, 
					{{WRAPPER}} .playing-video iframe' => 'opacity: calc({{SIZE}}/100)',
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
							{{WRAPPER}} .post-thumbnail-rollover:before { transition: all 0.3s ease; } {{WRAPPER}} .post-thumbnail-rollover:after { transition: all 0.3s ease; } {{WRAPPER}} .the7-video-overlay:before { transition: all 0.3s ease; } {{WRAPPER}} .the7-video-overlay:after { transition: all 0.3s ease; }{{SELECTOR}}' => 'background: {{VALUE}};',
						],
					],

				],
				'selector'       => '{{WRAPPER}} .post-thumbnail-rollover:after, {{WRAPPER}} .the7-video-overlay:after',
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
					'{{WRAPPER}} .dt-product-gallery .the7-image-wrapper:hover' => 'border-color: {{VALUE}}',
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
					'{{WRAPPER}} .dt-product-gallery .the7-image-wrapper:hover' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'image_hover_shadow',
				'selector' => '{{WRAPPER}} .the7-image-wrapper:hover',
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'     => 'image_hover_filters',
				'selector' => '{{WRAPPER}} .dt-product-gallery .the7-image-wrapper:hover img, 
					{{WRAPPER}} .the7-image-wrapper:hover .the7-video-overlay, 
					{{WRAPPER}} .the7-image-wrapper:hover video, 
					{{WRAPPER}} .the7-image-wrapper:hover iframe',
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
					'{{WRAPPER}} .dt-product-gallery .the7-image-wrapper:hover img, 
					{{WRAPPER}} .the7-image-wrapper:hover .the7-video-overlay, 
					{{WRAPPER}} .the7-image-wrapper:hover .playing-video video, 
					{{WRAPPER}} .the7-image-wrapper:hover .playing-video iframe' => 'opacity: calc({{SIZE}}/100)',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function add_video_play_style_controls() {
		$this->start_controls_section(
			'video_play_style',
			[
				'label'     => esc_html__( 'Video Icon', 'the7mk2' ),
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
				'label' => esc_html__( 'Image Icon Size', 'the7mk2' ),
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
					'{{WRAPPER}} .play-icon' => 'font-size: {{SIZE}}{{UNIT}}',
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
					'{{WRAPPER}} .play-icon' => 'min-width: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .play-icon' => 'min-height: {{SIZE}}{{UNIT}};',
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
				'selector' => '{{WRAPPER}} .play-icon',
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
					'{{WRAPPER}} .play-icon' => 'border-radius: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->start_controls_tabs( 'video_play_colors' );

		$this->start_controls_tab(
			'video_play_colors_normal',
			[
				'label' => esc_html__( 'Normal', 'the7mk2' ),
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

		$this->end_controls_tab();

		$this->start_controls_tab(
			'video_play_colors_hover',
			[
				'label' => esc_html__( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			'video_play_color_hover',
			[
				'label'     => esc_html__( 'Icon Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .gallery-video-wrap:hover .play-icon i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .gallery-video-wrap:hover .play-icon svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'video_play_border_color_hover',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .gallery-video-wrap:hover .play-icon' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'video_play_border_border!' => [ '', 'none' ],
				],
			]
		);

		$this->add_control(
			'video_play_background_color_hover',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .gallery-video-wrap:hover .play-icon' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'video_play_shadow_hover',
				'selector' => '{{WRAPPER}} .gallery-video-wrap:hover .play-icon',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}
}
