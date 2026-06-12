<?php
/**
 * Posts Masonry style.
 *
 * @package The7\Elementor
 */

namespace The7\Mods\Compatibility\Elementor\Style;

use Elementor\Utils;
use Elementor\Icons_Manager;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Image_Aspect_Ratio;
use The7\Mods\Compatibility\Elementor\Widgets\Posts;

defined( 'ABSPATH' ) || exit;

trait Posts_Masonry_Style {

	/**
	 * @param array $required_meta
	 *
	 * @return mixed
	 */
	protected function get_post_meta_html( $required_meta = [] ) {
		static $post_type_taxonomy_cache = [];

		$parts = [];
		foreach ( $required_meta as $meta ) {
			$with_link = ! empty( $meta['link'] );
			switch ( $meta['type'] ) {
				case 'terms':
					$current_post_type = get_post_type();
					if ( array_key_exists( $current_post_type, $post_type_taxonomy_cache ) ) {
						$terms_taxonomy = $post_type_taxonomy_cache[ $current_post_type ];
					} else {
						$terms_taxonomy = $this->guess_the_right_post_type_taxonomy( $current_post_type );
						$post_type_taxonomy_cache[ $current_post_type ] = $terms_taxonomy;
					}
					$terms    = the7_get_post_terms( null, $terms_taxonomy, ', ', $with_link );
					$parts[]  = $terms ? '<span class="meta-item category-link">' . $terms . '</span>' : '';
					break;
				case 'author':
					$parts[] = the7_get_post_author( null, $with_link );
					break;
				case 'date':
					$parts[] = the7_get_post_date( null, $with_link );
					break;
				case 'comments':
					$parts[] = the7_get_post_comments( null, $with_link );
					break;
			}
		}

		$html = '';
		if ( $parts ) {
			$html = '<div class="entry-meta">' . implode( '', $parts ) . '</div>';
		}

		return apply_filters( 'presscore_posted_on_html', $html, [] );
	}

	protected function guess_the_right_post_type_taxonomy( $post_type ) {
		$post_taxonomies = get_object_taxonomies( $post_type, 'objects' );

		$maybe_category = wp_filter_object_list(
			$post_taxonomies,
			[
				'name'               => "{$post_type}_category",
				'hierarchical'       => true,
				'public'             => true,
				'publicly_queryable' => true,
			],
			'and'
		);

		$maybe_category_names = array_keys( $maybe_category );
		if ( isset( $maybe_category_names[0] ) ) {
			return $maybe_category_names[0];
		}

		$hierarchical_public_taxonomies = wp_filter_object_list(
			$post_taxonomies,
			[
				'hierarchical'       => true,
				'public'             => true,
				'publicly_queryable' => true,
			],
			'and'
		);

		$hierarchical_public_taxonomy_names = array_keys( $hierarchical_public_taxonomies );
		if ( isset( $hierarchical_public_taxonomy_names[0] ) ) {
			return $hierarchical_public_taxonomy_names[0];
		}

		return 'category';
	}

	protected function get_post_meta_html_based_on_settings( $settings ) {
		$post_meta_types    = [
			'terms',
			'author',
			'date',
			'comments',
		];
		$required_post_meta = [];
		foreach ( $post_meta_types as $post_meta_type ) {
			if ( ! empty( $settings["post_{$post_meta_type}"] ) ) {
				$link = '';
				if ( ! isset( $settings['link_click'] ) || $settings['link_click'] === 'button' ) {
					$link = ! empty( $settings[ "post_{$post_meta_type}_link" ] );
				}

				$meta = [
					'type' => $post_meta_type,
					'link' => $link,
				];

				$required_post_meta[] = $meta;
			}
		}

		return $this->get_post_meta_html( $required_post_meta );
	}

	protected function is_overlay_post_layout( $settings ) {
		return in_array(
			$settings['post_layout'],
			[ 'gradient_rollover', 'gradient_overlay' ],
			true
		);
	}

	/**
	 * @return bool
	 */
	protected function is_masonry_layout( $settings ) {
		return $settings['layout'] === 'masonry';
	}

	protected function get_post_image( $settings ) {
		$show_image             = in_array( $settings['classic_image_visibility'], [ null, 'show' ], true );
		$link_attridutes        = $this->get_link_attributes( $settings );
		$post_media             = '';

		$img_wrap_class = 'post-thumbnail-rollover ' . $this->template( Image_Aspect_Ratio::class )->get_wrapper_class();
		if ( $show_image && has_post_thumbnail() ) {
			$thumb_args = [
				'img_id' => get_post_thumbnail_id(),
				'class'  => $img_wrap_class,
				'echo'   => false,
			];

		if ( $link_attridutes['href'] ) {
				$thumb_args['href']   = $link_attridutes['href'];
				$thumb_args['custom'] = the7_get_html_attributes_string(
					[
						'aria-label' => __( 'Post image', 'the7mk2' ),
						'target'     => $link_attridutes['target'],
					]
				);
				$thumb_args['wrap']   = '<a %HREF% %CLASS% %CUSTOM%><img %IMG_CLASS% %SRC% %ALT% %IMG_TITLE% %SIZE% /></a>';
		} else {
				$thumb_args['class'] .= ' not-clickable-item';
				$thumb_args['wrap']  = '<div %CLASS% %CUSTOM%><img %IMG_CLASS% %SRC% %ALT% %IMG_TITLE% %SIZE% /></div>';
		}

			$thumb_args['img_class'] = 'preload-me';

			if ( presscore_lazy_loading_enabled() ) {
				$thumb_args['lazy_loading'] = true;
				if ( $this->is_masonry_layout( $settings ) ) {
					$thumb_args['lazy_class'] = 'iso-lazy-load';
				}
			}

			$post_media = dt_get_thumb_img( $thumb_args );
		} elseif ( $this->is_overlay_post_layout( $settings ) ) {
			$image = sprintf(
				'<img class="%s" src="%s" width="%s" height="%s">',
				'preload-me',
				get_template_directory_uri() . '/images/gray-square.svg',
				1500,
				1500
			);

			$link_atts               = $link_attridutes;
			$link_atts['class']      = $img_wrap_class;
			$link_atts['aria-label'] = __( 'Post image', 'the7mk2' );

			$post_media = sprintf( '<a %s>%s</a>', the7_get_html_attributes_string( $link_atts ), $image );
		}

		return $post_media;
	}

	protected function get_link_attributes( $settings ) {
		if ( empty( $settings['article_links'] ) ) {
			return [
				'href'   => '',
				'target' => '',
			];
		}

		$links_goes_to = $settings['article_links_goes_to'];
		if ( $links_goes_to ) {
			$external_link = null;
			$link_target   = '';

			if ( ! isset( $settings['article_link_meta_field'] ) && '0' !== get_post_meta( get_the_ID(), '_dt_project_options_show_link', true ) ) {
				$external_link = esc_url( (string) get_post_meta( get_the_ID(), '_dt_project_options_link', true ) );
				$link_target   = esc_html( (string) get_post_meta( get_the_ID(), '_dt_project_options_link_target', true ) );
			} elseif ( $settings['article_link_meta_field'] ) {
				$external_link = esc_url( (string) get_post_meta( get_the_ID(), sanitize_key( trim( $settings['article_link_meta_field'] ) ), true ) );
				$link_target   = '_blank';
			}

			if ( $links_goes_to === 'external_or_posts' ) {
				return [
					'href'   => $external_link ?: get_the_permalink(),
					'target' => $link_target,
				];
			}

			if ( $links_goes_to === 'external_or_disabled' ) {
				return [
					'href'   => $external_link ?: '',
					'target' => $external_link ? $link_target : '',
				];
			}
		}

		return [
			'href'   => get_the_permalink(),
			'target' => '',
		];
	}

	protected function current_post_is_wide( $settings ) {
		global $post;

		if ( $settings['all_posts_the_same_width'] ) {
			return false;
		}

		switch ( get_post_type( $post ) ) {
			case 'post':
				return get_post_meta( $post->ID, '_dt_post_options_preview', true ) === 'wide';
			case 'dt_gallery':
				return get_post_meta( $post->ID, '_dt_album_options_preview', true ) === 'wide';
			case 'dt_portfolio':
				return get_post_meta( $post->ID, '_dt_project_options_preview', true ) === 'wide';
		}

		return false;
	}

	protected function get_details_btn( $settings ) {
		$icon = '';
		if ( $settings['show_read_more_button_icon'] ) {
			$icon = $this->get_elementor_icon_html( $settings['read_more_button_icon'] );
		}

		$link_attributes = $this->get_link_attributes( $settings );

		ob_start();
		presscore_get_template_part(
			'elementor',
			'the7-elements/read-more-button',
			null,
			[
				'settings'      => $settings,
				'follow_link'   => $link_attributes['href'],
				'target'        => $link_attributes['target'],
				'caption'       => $settings['read_more_button_text'],
				'icon_position' => $settings['read_more_button_icon_position'],
				'icon'          => $icon,
				'aria_label'    => the7_get_read_more_aria_label(),
			]
		);

		return ob_get_clean();
	}

	/**
	 * @param string $tag
	 *
	 * @return string
	 */
	protected function get_post_title( $settings, $tag = 'h3' ) {
		$title_link = $this->get_link_attributes( $settings );
		$link_tag   = 'span';
		if ( $title_link['href'] ) {
			$title_link['rel']   = 'bookmark';
			$title_link['title'] = the_title_attribute( 'echo=0' );
			$link_tag            = 'a';
		}

        $tag = Utils::validate_html_tag( $tag );

		$output = '';
		$output .= '<' . $tag . ' class="ele-entry-title">';
		$output .= '<' . $link_tag . ' ' . the7_get_html_attributes_string( $title_link ) . '>' . get_the_title() . '</' . $link_tag . '>';
		$output .= '</' . $tag . '>';

		return $output;
	}

	protected function get_hover_icons_html_template( $settings ) {
		if ( ! $settings['show_details_icon'] ) {
			return '';
		}

		$a_atts               = $this->get_link_attributes( $settings );
		$a_atts['class']      = 'project-details';
		$a_atts['aria-label'] = __( 'Details link', 'the7mk2' );

		return sprintf(
			'<a %s>%s</a>',
			the7_get_html_attributes_string( $a_atts ),
			$this->get_elementor_icon_html( $settings['project_link_icon'], 'span' )
		);
	}

}
