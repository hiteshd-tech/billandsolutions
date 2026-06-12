<?php
/**
 * Products category walker.
 *
 * @see \The7\Mods\Compatibility\Elementor\Widgets\Woocommerce\Product_Categories
 *
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Walkers;

use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Product_Cat_List class.
 */
class Product_Cat_List extends Custom_Taxonomy_List {

    /**
	 * @param  The7_Elementor_Widget_Base $widget Widget to operate.
     * @param  string                     $taxonomy Taxonomy to walk.
	 */
	public function __construct( The7_Elementor_Widget_Base $widget, $taxonomy = 'product_cat' ) {
		parent::__construct( $widget, $taxonomy );
	}

    /**
     * @param \WP_Term $term Term object.
     */
    protected function get_term_name( $term ) {
        return apply_filters( 'list_product_cats', $term->name, $term );
    }

}
