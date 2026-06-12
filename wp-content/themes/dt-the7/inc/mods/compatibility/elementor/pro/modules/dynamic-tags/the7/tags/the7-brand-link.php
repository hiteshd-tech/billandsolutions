<?php
namespace The7\Mods\Compatibility\Elementor\Pro\Modules\Dynamic_Tags\The7\Tags;


use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use The7\Mods\Compatibility\Elementor\Pro\Modules\Dynamic_Tags\The7\Module;

defined( 'ABSPATH' ) || exit;

class The7_Brand_Link extends Tag {

    public function get_name() {
        return 'the7-brand-link';
    }

    public function get_title() {
        return __( 'Product Brand Link', 'the7mk2' );
    }

    public function get_group() {
		return Module::THE7_GROUP;
	}

    public function get_categories() {
        return [ \Elementor\Modules\DynamicTags\Module::URL_CATEGORY ];
    }

    protected function register_controls() {
        $this->add_control('brand_link', [
            'label' => __( 'Brand Link', 'the7mk2' ),
            'type'  => \Elementor\Controls_Manager::TEXT,
            'default' => ', ',
        ]);
    }

    public function render() {
        $url = $this->get_brand_link();

        if ( $url ) {
            echo esc_url( $url );
        }
    }

    private function get_brand_link() {
        if ( ! is_singular( 'product' ) ) {
            return '';
        }

        global $product;
        if ( ! $product || ! is_a( $product, 'WC_Product' ) ) {
            $product = wc_get_product( get_the_ID() );
        }

        if ( ! $product ) {
            return '';
        }

        $brand_terms = wp_get_post_terms( $product->get_id(), 'product_brand' );
        if ( ! empty( $brand_terms ) && ! is_wp_error( $brand_terms ) ) {
            return get_term_link( $brand_terms[0] );
        }

        return '';
    }
}