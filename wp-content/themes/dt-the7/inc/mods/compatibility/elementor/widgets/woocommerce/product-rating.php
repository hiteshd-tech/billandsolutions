<?php
namespace The7\Mods\Compatibility\Elementor\Widgets\Woocommerce;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;
use ElementorPro\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Product_Rating extends The7_Elementor_Widget_Base {

	public function get_name() {
		return 'the7-woocommerce-product-rating';
	}

	public function get_title() {
		return esc_html__( 'Product Rating', 'the7mk2' );
	}

	public function the7_icon() {
		return 'eicon-product-rating';
	}

	public function get_keywords() {
		return [ 'woocommerce', 'shop', 'store', 'rating', 'review', 'comments', 'stars', 'product' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_product_rating',
			[
				'label' => esc_html__( 'General', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_responsive_control(
			'alignment',
			[
				'label' => esc_html__( 'Alignment', 'the7mk2' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'the7mk2' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'the7mk2' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'the7mk2' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-widget-container' => 'text-align: {{VALUE}}',
				],
				'selectors_dictionary' => [
					'left' => '--content-align: flex-start;',
					'center' => '--content-align: center;',
					'right' => '--content-align: flex-end;',
				],
				'selectors'            => [
					'{{WRAPPER}}' => '{{VALUE}}',
				],
			]
		);
		$this->add_control(
			'irating_title',
			[
				'label'     => esc_html__( 'Number of reviews', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'show_star_text',
			[
				'label'                => esc_html__( 'Visibility', 'the7mk2' ),
				'type'                 => Controls_Manager::SWITCHER,
				'label_on'             => esc_html__( 'Show', 'the7mk2' ),
				'label_off'            => esc_html__( 'Hide', 'the7mk2' ),
				'return_value'         => 'yes',
				'default'              => 'yes',
				'selectors_dictionary' => [
					'yes' => '--star-text-display: inline-flex; --show-star-text-spacing: var(--star-spacing, 5px)',
					''    => '--star-text-display: none; --show-star-text-spacing: 0px',
				],
				'selectors'            => [
					'{{WRAPPER}}' => '{{VALUE}}',
				],
			]
		);
		$this->add_control(
			'show_star_label',
			[
				'label'                => esc_html__( 'Label', 'the7mk2' ),
				'type'                 => Controls_Manager::SWITCHER,
				'label_on'             => esc_html__( 'Show', 'the7mk2' ),
				'label_off'            => esc_html__( 'Hide', 'the7mk2' ),
				'return_value'         => 'yes',
				'default'              => 'yes',
				'selectors_dictionary' => [
					'yes' => 'display: inline-flex;',
					''    => 'display: none;',
				],
				'selectors'            => [
					'{{WRAPPER}} .rating-text' => '{{VALUE}}',
				],
				'condition'            => [
					'show_star_text' => 'yes',
				],
			]
		);
		$this->add_control(
			'show_star_text_parenthesis',
			[
				'label'                => esc_html__( 'Round brackets', 'the7mk2' ),
				'type'                 => Controls_Manager::SWITCHER,
				'label_on'             => esc_html__( 'Show', 'the7mk2' ),
				'label_off'            => esc_html__( 'Hide', 'the7mk2' ),
				'return_value'         => 'yes',
				'default'              => 'yes',
				'selectors_dictionary' => [
					'yes' => 'display: inline-flex;',
					''    => 'display: none;',
				],
				'selectors'            => [
					'{{WRAPPER}} .parenthese' => '{{VALUE}}',
				],
				'condition'            => [
					'show_star_text' => 'yes',
				],
			]
		);
		$this->add_control(
			'rating_caption_single',
			[
				'label' => esc_html__( 'Singular', 'the7mk2' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Customer review', 'the7mk2' ),
				'dynamic' => [
					'active' => true,
				],
				'description' => esc_html__( 'Leave empty to remove text', 'the7mk2' ),
				'condition'            => [
					'show_star_text' => 'yes',
					'show_star_label' => 'yes',
				],
			]
		);

		$this->add_control(
			'rating_caption_plural',
			[
				'label' => esc_html__( 'Plural', 'the7mk2' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Customer reviews', 'the7mk2' ),
				'dynamic' => [
					'active' => true,
				],

				'description' => esc_html__( 'Leave empty to remove text', 'the7mk2' ),
				'condition'            => [
					'show_star_text'     => 'yes',
					'show_star_label' => 'yes',
				],
			]
		);

		$this->end_controls_section();
		$this->start_controls_section(
			'section_product_stars_style',
			[
				'label' => esc_html__( 'Stars', 'the7mk2' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'star_size',
			[
				'label' => esc_html__( 'Star Size', 'the7mk2' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'custom' ],
				'default' => [
					'unit' => 'em',
				],
				'range' => [
					'em' => [
						'min' => 0,
						'max' => 4,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'.woocommerce {{WRAPPER}} .star-rating, {{WRAPPER}} .star-rating' => 'font-size: {{SIZE}}{{UNIT}}',
				],
			]
		);


		$this->add_control(
			'star_color',
			[
				'label' => esc_html__( 'Star Color', 'the7mk2' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .star-rating span:before' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'empty_star_color',
			[
				'label' => esc_html__( 'Empty Star Color', 'the7mk2' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .star-rating:before' => 'color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_product_rating_style',
			[
				'label' => esc_html__( 'Number of reviews', 'the7mk2' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition'            => [
					'show_star_text' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'star_position',
			[
				'label'                => esc_html__( 'Position', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => [
					'inline'  => [
						'title' => esc_html__( 'Inline', 'the7mk2' ),
						'icon'  => 'eicon-h-align-left',
					],
					'stacked' => [
						'title' => esc_html__( 'Stacked', 'the7mk2' ),
						'icon'  => 'eicon-v-align-top',
					],
				],
				'default' => 'inline',
				'toggle'       => false,
				'selectors_dictionary' => [
					'stacked' => 'flex-flow: column; --star-gap: 0 0 var(--show-star-text-spacing, 20px) 0; align-items: var(--content-align, center);',
					'inline'  => 'flex-flow: row nowrap; align-items: center; --star-gap: 0 var(--show-star-text-spacing, 20px) 0 0; justify-content: var(--content-align);',
				],
				'selectors'            => [
					'{{WRAPPER}} .woocommerce-product-rating' => '{{VALUE}}',
				],
				'condition'            => [
					'show_star_text'     => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'star_spacing',
			[
				'label'      => esc_html__( 'Spacing', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					'{{WRAPPER}}' => '--star-spacing: {{SIZE}}{{UNIT}}',
				],
				'condition'            => [
					'show_star_text'     => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'text_typography',
				'selector' => '{{WRAPPER}} .woocommerce-review-link',
				'condition'            => [
					'show_star_text'     => 'yes',
				],
			]
		);
		$this->start_controls_tabs( 'tabs_link_colors' );

		$this->start_controls_tab(
			'tabs_link_color_normal',
			[
				'label' => esc_html__( 'Normal', 'the7mk2' ),
			]
		);
		$this->add_control(
			'link_color',
			[
				'label' => esc_html__( 'Link Color', 'the7mk2' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-review-link, {{WRAPPER}} .woocommerce-review-link span' => 'color: {{VALUE}}',
				],
				'condition'            => [
					'show_star_text'     => 'yes',
				],
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_link_color_hover',
			[
				'label' => esc_html__( 'Hover', 'the7mk2' ),
			]
		);
		$this->add_control(
			'link_color_hover',
			[
				'label' => esc_html__( 'Link Color', 'the7mk2' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-review-link:hover, {{WRAPPER}} .woocommerce-review-link:hover span' => 'color: {{VALUE}}',
				],
				'condition'            => [
					'show_star_text'     => 'yes',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function render() {
		if ( ! post_type_supports( 'product', 'comments' ) ) {
			return;
		}

		global $product;
		$product = wc_get_product();

		if ( ! $product || ! wc_review_ratings_enabled() ) {
			return;
		}

		$rating_count = $product->get_rating_count();
		$review_count = $product->get_review_count();
		$average      = $product->get_average_rating();
		$rating_caption_single = $this->get_settings_for_display( 'rating_caption_single' ) ? (string) $this->get_settings_for_display( 'rating_caption_single' ) : esc_html__( 'review', 'the7mk2' );
		$rating_caption_plural = $this->get_settings_for_display( 'rating_caption_plural' ) ? (string) $this->get_settings_for_display( 'rating_caption_plural' ) : esc_html__( 'reviews', 'the7mk2' );
		$rating_text = ( $review_count === 1 ? $rating_caption_single : $rating_caption_plural );
		if ( $rating_text ) {
			$rating_text = '<span class="rating-text">&nbsp;' . esc_html( $rating_text ) . '</span>';
		}
		if ( $rating_count > 0 ) :

			echo '<div class="woocommerce-product-rating">';
				echo wc_get_rating_html( $average, $rating_count );
				if ( comments_open() ) :
					//phpcs:disable
					echo '<a href="' . esc_url( get_permalink() ) . '#reviews" class="woocommerce-review-link" rel="nofollow">';

					 echo '<span class="parenthese">(</span><span class="count"> ' . (int) $review_count . '</span>' . $rating_text . '<span class="parenthese">)</span> ';
					echo '</a>';
					// phpcs:enable
				 endif;
			echo '</div>';

		endif;

	}


	public function get_group_name() {
		return 'woocommerce';
	}
}
