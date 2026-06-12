<?php

namespace The7\Mods\Compatibility\Elementor\Pro\Modules\Dynamic_Tags\The7\Tags;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Data_Tag;
use The7\Mods\Compatibility\Elementor\Pro\Modules\Dynamic_Tags\The7\Module;

defined('ABSPATH') || exit;

class The7_Brand_Image extends Data_Tag {

    public function get_name() {
        return 'product-brand-image';
    }

    public function get_title() {
        return __('Product Brand Image', 'the7mk2');
    }

    public function get_group() {
        return Module::THE7_GROUP;
    }

    public function get_categories() {
        return [ \Elementor\Modules\DynamicTags\Module::IMAGE_CATEGORY ];
    }

    
    private function get_brand_image_id() {
        // Ensure we're on a single product page
        if ( ! is_singular( 'product' ) ) {
            return 0;
        }
    
        global $product;
    
        // Ensure we have a valid WC_Product object
        if ( ! $product || ! is_a( $product, 'WC_Product' ) ) {
            $product = wc_get_product( get_the_ID() );
        }
    
        if ( ! $product ) {
            return 0;
        }
    
        // Fetch the brand term for this product
        $brand_terms = wp_get_post_terms( $product->get_id(), 'product_brand' );
        if ( ! empty( $brand_terms ) && ! is_wp_error( $brand_terms ) ) {
            $thumbnail_id = get_term_meta( $brand_terms[0]->term_id, 'thumbnail_id', true );
            return $thumbnail_id ? (int) $thumbnail_id : 0;
        }
    
        return 0;
    }
    public function get_value( array $options = [] ) {
        $image_id = $this->get_brand_image_id();
    
        if ( $image_id ) {
            return [
                'id'  => $image_id,
                'url' => wp_get_attachment_url( $image_id ),
            ];
        }
    
        // fallback
        $fallback = $this->get_settings( 'fallback' );
        return ! empty( $fallback['id'] ) ? $fallback : [];
    }
    
    

    protected function register_controls() {
        $this->add_control(
            'fallback',
            [
                'label' => esc_html__( 'Fallback Image', 'the7mk2' ),
                'type'  => Controls_Manager::MEDIA,
            ]
        );
    }
}
