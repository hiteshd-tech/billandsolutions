<?php

defined( 'ABSPATH' ) || exit;

class The7_WC_Importer {

	/**
	 * @var The7_Content_Importer
	 */
	protected $importer;

	/**
	 * The7_WC_Importer constructor.
	 *
	 * @param The7_Content_Importer $importer Importer instance.
	 */
	public function __construct( $importer ) {
		$this->importer = $importer;
	}

	/**
	 * @return void
	 */
	public function fix_product_cat_thumbnail_id() {
		if ( ! the7_is_woocommerce_enabled() ) {
			return;
		}

		$terms = get_terms(
			[
				'taxonomy'   => 'product_cat',
				'hide_empty' => false,
				'include'    => array_values( $this->importer->processed_terms ),
			]
		);

		if ( is_wp_error( $terms ) ) {
			return;
		}

		$this->importer->log_add( ' ' ); // Add empty line.
		$this->importer->log_add( 'Fixing product_cat thumbnail ids...' );
		$this->importer->log_add( 'Processed terms count: ' . count( $this->importer->processed_terms ) );
		$this->importer->log_add( 'Found terms: ' . count( $terms ) );

		$meta_was_updated = false;
		foreach ( $terms as $term ) {
			$thumbnail_id = get_term_meta( $term->term_id, 'thumbnail_id', true );
			if ( $thumbnail_id ) {
				$processed_id = $this->importer->get_processed_post( $thumbnail_id );
				if ( $processed_id && (int) $processed_id !== (int) $thumbnail_id ) {
					$meta_was_updated = true;
					$this->importer->log_add( "Updating term {$term->slug} ({$term->term_id}) thumbnail id from {$thumbnail_id} to {$processed_id} ..." );

					if ( update_term_meta( $term->term_id, 'thumbnail_id', $processed_id ) ) {
						$this->importer->log_add( 'Success' );
					} else {
						$this->importer->log_add( 'Failed' );
					}
				}
			}
		}

		if ( ! $meta_was_updated ) {
			$this->importer->log_add( 'Nothing to update' );
		}
	}

	/**
	 * @param array $attributes WC attributes.
	 */
	public function import_wc_attributes( $attributes ) {
		if ( ! $attributes || ! the7_is_woocommerce_enabled() ) {
			return;
		}

		if ( ! function_exists( 'wc_create_attribute' ) || ! function_exists( 'wc_attribute_taxonomy_name' ) ) {
			return;
		}

		$this->importer->log_add( 'WC attributes importing...' );

		foreach ( $attributes as $attribute ) {
			$attribute_name = $attribute['attribute_name'];
			$raw_name       = $attribute['attribute_label'];
			$attribute_id   = wc_create_attribute(
				[
					'name'         => $raw_name,
					'slug'         => $attribute_name,
					'type'         => $attribute['attribute_type'],
					'order_by'     => $attribute['attribute_orderby'],
					'has_archives' => (bool) $attribute['attribute_public'],
				]
			);

			if ( is_wp_error( $attribute_id ) ) {
				continue;
			}

			// Register as taxonomy while importing.
			$taxonomy_name = wc_attribute_taxonomy_name( $attribute_name );
			// Important! Allows us to pass Envato Theme Checker tests.
			$func = 'register_taxonomy';
			$func(
				$taxonomy_name,
				apply_filters( 'woocommerce_taxonomy_objects_' . $taxonomy_name, [ 'product' ] ),
				apply_filters(
					'woocommerce_taxonomy_args_' . $taxonomy_name,
					[
						'labels'       => [
							'name' => $raw_name,
						],
						'hierarchical' => true,
						'show_ui'      => false,
						'query_var'    => true,
						'rewrite'      => false,
					]
				)
			);
		}

		the7_wc_flush_attributes_cache();

		$this->importer->log_add( 'Done' );
	}

	/**
	 * @param array $wc_settings WC settings.
	 *
	 * @return void
	 */
	public function import_wc_settings( $wc_settings ) {
		if ( ! $wc_settings || ! function_exists( 'WC' ) || ! the7_is_woocommerce_enabled() ) {
			return;
		}

		$this->importer->log_add( 'WC settings importing...' );

		$wc_settings = (array) $wc_settings;

		$wc_page_settings = [
			'woocommerce_shop_page_id',
			'woocommerce_cart_page_id',
			'woocommerce_checkout_page_id',
			'woocommerce_myaccount_page_id',
			'woocommerce_terms_page_id',
		];

		foreach ( $wc_page_settings as $id ) {
			if ( isset( $wc_settings[ $id ] ) ) {
				$val              = $wc_settings[ $id ];
				$imported_post_id = $this->importer->get_processed_post( $val );
				if ( $imported_post_id ) {
					$val = $imported_post_id;
				}

				update_option( $id, $val );
			}
		}

		$wc_image_settings = [
			'woocommerce_single_image_width',
			'woocommerce_thumbnail_image_width',
			'woocommerce_thumbnail_cropping',
			'woocommerce_thumbnail_cropping_custom_width',
			'woocommerce_thumbnail_cropping_custom_height',
		];

		foreach ( $wc_image_settings as $id ) {
			if ( isset( $wc_settings[ $id ] ) ) {
				update_option( $id, $wc_settings[ $id ] );
			}
		}

		// Clear any unwanted data and flush rules.
		update_option( 'woocommerce_queue_flush_rewrite_rules', 'yes' );
		WC()->query->init_query_vars();
		WC()->query->add_endpoints();

		$this->importer->log_add( 'Done' );
	}

	/**
	 * Recount WC terms and regenerate product lookup tables. Has to be launched after WC content import.
	 */
	public function regenerate_wc_cache() {
		if ( ! class_exists( '\WC_REST_System_Status_Tools_Controller' ) || ! the7_is_woocommerce_enabled() ) {
			return;
		}

		$this->importer->log_add( 'WC post-import sequence...' );

		$tools_controller = new \WC_REST_System_Status_Tools_Controller();
		$tools            = $tools_controller->get_tools();
		$actions          = [
			'recount_terms',
			'regenerate_product_lookup_tables',
			'clear_transients',
			'clear_template_cache',
		];

		foreach ( $actions as $action ) {
			if ( array_key_exists( $action, $tools ) ) {
				$this->importer->log_add( 'Doing: ' . $action );

				$result = $tools_controller->execute_tool( $action );

				if ( $result['success'] ) {
					$this->importer->log_add( 'Success. ' . $result['message'] );
				} else {
					$this->importer->log_add( 'Failed' );
				}
			} else {
				$this->importer->log_add( 'Skipping: ' . $action );
			}
		}

		if ( function_exists( 'wc_get_container' ) ) {
			try {
				$this->importer->log_add( 'Initiate attribute lookup tables regeneration...' );

				// Schedule product attributes lookup table regeneration.
				wc_get_container()->get( \Automattic\WooCommerce\Internal\ProductAttributesLookup\DataRegenerator::class )->initiate_regeneration();
			} catch ( \Exception $e ) {
				$this->importer->log_add( 'Error: ' . $e->getMessage() );
			}
		}

		$this->importer->log_add( 'Done' );
	}

	/**
	 * Turn on WC default payments if there are no available gateways.
	 *
	 * @return bool True if payments was enabled, false otherwise.
	 */
	public function maybe_enable_defualt_wc_payments(): bool {
		if (
			! function_exists( 'WC' )
			|| ! the7_is_woocommerce_enabled()
			|| ! is_object( WC() )
			|| ! method_exists( WC(), 'payment_gateways' )
			|| ! is_object( WC()->payment_gateways() )
			|| ! method_exists( WC()->payment_gateways, 'get_available_payment_gateways' )
		) {
			return false;
		}

		$was_enabled        = false;
		$available_gateways = WC()->payment_gateways->get_available_payment_gateways();
		if ( empty( $available_gateways ) && method_exists( WC()->payment_gateways, 'payment_gateways' ) ) {
			$gateways         = WC()->payment_gateways->payment_gateways();
			$default_gateways = [
				'cod',
				'bacs',
				'cheque',
			];
			foreach ( $default_gateways as $gateway_id ) {
				if ( ! isset( $gateways[ $gateway_id ] ) ) {
					continue;
				}

				$gateways[ $gateway_id ]->update_option( 'enabled', 'yes' );
				$was_enabled = true;
			}
		}

		if ( $was_enabled ) {
			$this->importer->log_add( 'WC default gateways enabled' );
		}

		return $was_enabled;
	}

	/**
	 * We should take care of product reviews meta since we do not import any comments.
	 *
	 * @param array $post_meta Imported post meta.
	 *
	 * @return array
	 */
	public function remove_wc_product_reviews_meta_filter( $post_meta ) {
		$reviews_meta = [
			'_wc_average_rating',
			'_wc_rating_count',
			'_wc_review_count',
		];

		foreach ( $post_meta as $meta_index => $meta ) {
			if ( in_array( $meta['key'], $reviews_meta, true ) ) {
				unset( $post_meta[ $meta_index ] );
			}
		}

		return $post_meta;
	}

	/**
	 * @return void
	 */
	public function add_hooks() {
		add_filter( 'wp_import_post_meta', [ $this, 'remove_wc_product_reviews_meta_filter' ] );
	}

	/**
	 * @return void
	 */
	public function remove_hooks() {
		remove_filter( 'wp_import_post_meta', [ $this, 'remove_wc_product_reviews_meta_filter' ] );
	}
}
