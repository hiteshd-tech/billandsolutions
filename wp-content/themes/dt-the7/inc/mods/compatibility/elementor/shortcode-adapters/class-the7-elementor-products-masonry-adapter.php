<?php

namespace The7\Mods\Compatibility\Elementor\Shortcode_Adapters;

defined( 'ABSPATH' ) || exit;

use The7\Mods\Compatibility\Elementor\Shortcode_Adapters\Query_Adapters\Products_Current_Query;
use The7\Mods\Compatibility\Elementor\Shortcode_Adapters\Query_Adapters\Products_Query;
use WP_Query;

class DT_Shortcode_Products_Masonry_Adapter extends \DT_Shortcode_ProductsMasonry implements The7_Shortcode_Adapter_Interface {

	use Trait_Elementor_Shortcode_Adapter;

	public function __construct() {
		parent::__construct();
		$prefix = self::QUERY_CONTROL_NAME . '_';
		$default_atts = array(
			$prefix . 'order'            => 'desc',
			$prefix . 'orderby'          => 'date',
			$prefix . 'post_type'        => '',
			$prefix . 'posts_ids'        => '',
			$prefix . 'include'          => '',
			$prefix . 'include_term_ids' => '',
			$prefix . 'include_authors'  => '',
			$prefix . 'exclude'          => '',
			$prefix . 'exclude_ids'      => '',
			$prefix . 'exclude_term_ids' => '',
		);

		$this->default_atts = array_merge( $this->default_atts, $default_atts );
	}

	/**
	 * Return products query.
	 *
	 * @return mixed|WP_Query
	 */
	protected function get_query() {
		if ( 'current_query' === $this->get_att( self::QUERY_CONTROL_NAME . '_post_type' ) ) {
			return $GLOBALS['wp_query'];
		}

		$query = new Products_Query( $this->get_atts(), self::QUERY_CONTROL_NAME . '_' );

		return new WP_Query( $query->parse_query_args() );
	}

	protected function get_term_ids($query){
        $ids= [];
        //convert term_taxonomy_id to term_id
        if (array_key_exists( 'field', $query ) && $query['field'] === "term_taxonomy_id" ){
            foreach( $query['terms'] as $term_taxonomy_id ){
                $term = get_term_by( 'term_taxonomy_id', $term_taxonomy_id );
                if($term) {
                    $ids[] = $term->term_id;
                }
            }
        }
        else {
            $ids = $query['terms'];
        }
        return $ids;
    }

	protected function get_posts_filter_terms( $query ) {
		$product_query = new Products_Query( $this->get_atts(), self::QUERY_CONTROL_NAME . '_' );
		$query_args = $product_query->parse_query_args();
		$query_args['fields'] = 'ids';
		unset( $query_args['posts_per_page'] );
		unset( $query_args['paged'] );

		$tags = false;
		$product_cat = [];
		$product_exclude_cat = [];
		if ( array_key_exists( 'tax_query', $query_args ) ) {
			foreach ( $query_args['tax_query'] as $query ) {
                if ($tags) {
                    break;
                }
			    if ( ! is_array( $query ) ) {
					continue;
				}
				if ( ! array_key_exists( 'taxonomy', $query ) ) {
				    foreach ($query as $_query){
						if ($_query['taxonomy'] === 'product_cat' ) {

						    $ids = $this->get_term_ids($_query);

							if ( array_key_exists( 'operator', $_query ) && $_query['operator'] === 'NOT IN' ) {
								$product_exclude_cat = array_merge($product_exclude_cat, $ids);
							} else {
                                $product_cat = array_merge($product_cat, $ids);
                            }
						}
						else {
                            if ( $_query['taxonomy'] !== 'product_visibility') {
                                $tags = true;
                                break;
                            }
                        }
					}
				    continue;
				}

				if ( $query['taxonomy'] !== 'product_visibility' && $query['taxonomy'] !== 'product_cat' ) {
					$tags = true;
                    break;
				}
				if ( $query['taxonomy'] === 'product_cat' ) {
                    $ids = $this->get_term_ids($_query);
                    if ( array_key_exists( 'operator', $_query ) && $_query['operator'] === 'NOT IN' ) {
                        $product_exclude_cat = array_merge($product_exclude_cat, $ids);
                    } else {
                        $product_cat = array_merge($product_cat, $ids);
                    }
				}
			}
		}

		// If only categories selected.
		if ( ! $tags ) {

            $get_terms_args = array(
                'taxonomy'   => 'product_cat',
                'hide_empty' => true,
            );
			if ( empty( $product_cat ) && empty( $product_exclude_cat ) ) {
				// If empty - return all categories.
				return get_terms( $get_terms_args );
			} elseif ( ! empty( $product_cat ) ) {
				//exclude categories
                $categories = array_diff( $product_cat, $product_exclude_cat);
				if ( ! empty( $categories ) && ! is_numeric( $categories[0] ) ) {
					$get_terms_args['slug'] = $categories;
				} else {
					$get_terms_args['include'] = $categories;
				}
			} elseif ( ! empty( $product_exclude_cat ) ) {
                $get_terms_args['exclude'] = $product_exclude_cat;
			}

			return get_terms( $get_terms_args );
		}

		$posts_query = new WP_Query( $query_args );

		//return corresponded categories.
		return wp_get_object_terms( $posts_query->posts, 'product_cat', array( 'fields' => 'all_with_object_id' ) );
	}
}
