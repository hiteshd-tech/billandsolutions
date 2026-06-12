<?php
namespace The7\Mods\Compatibility\Elementor\Pro\Modules\Dynamic_Tags\The7\Tags;


use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use The7\Mods\Compatibility\Elementor\Pro\Modules\Dynamic_Tags\The7\Module;

defined( 'ABSPATH' ) || exit;

class The7_Brand_Name extends Tag {

    public function get_name() {
        return 'the7-brand-name';
    }

    public function get_title() {
        return __( 'Product Brand Name', 'the7mk2' );
    }

    public function get_group() {
		return Module::THE7_GROUP;
	}

    public function get_categories() {
        return [ \Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY ];
    }

    public function render() {
        // Ensure we're on a single product page
        if ( ! is_singular( 'product' ) ) {
            return;
        }
    
        global $product;
    
        // Ensure we have a valid WC_Product object
        if ( ! $product || ! is_a( $product, 'WC_Product' ) ) {
            $product = wc_get_product( get_the_ID() );
        }
    
        if ( ! $product ) {
            return;
        }
    
        // Get product brands
        $brands = wp_get_post_terms( $product->get_id(), 'product_brand' );
    
        if ( ! empty( $brands ) && ! is_wp_error( $brands ) ) {
            // Output only the first brand name
            echo esc_html( $brands[0]->name );
        }
    }
    
}