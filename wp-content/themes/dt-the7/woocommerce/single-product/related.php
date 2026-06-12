<?php
/**
 * Related Products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/related.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     10.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( $related_products ) :
	/**
	 * Ensure all images of related products are lazy loaded by increasing the
	 * current media count to WordPress's lazy loading threshold if needed.
	 * Because wp_increase_content_media_count() is a private function, we
	 * check for its existence before use.
	 */
	if ( function_exists( 'wp_increase_content_media_count' ) ) {
		$content_media_count = wp_increase_content_media_count( 0 );
		if ( $content_media_count < wp_omit_loading_attr_threshold() ) {
			wp_increase_content_media_count( wp_omit_loading_attr_threshold() - $content_media_count );
		}
	}
	?>

	<section class="related products">

		<?php
		$heading = apply_filters( 'woocommerce_product_related_products_heading', __( 'Related products', 'woocommerce' ) );

		if ( $heading ) :
			?>
			<h2><?php echo esc_html( $heading ); ?></h2>
		<?php endif; ?>

        <ul class="related-product cart-btn-below-img">

			<?php presscore_config()->set( 'product.preview.icons.show_cart', true ) ?>

			<?php foreach ( $related_products as $related_product ) : ?>
                <li>
					<?php
					global $product;

					$product = wc_get_product( $related_product->get_id() );
					if ( $product->is_on_sale() ) :
						?>
                        <span class="onsale"></span>
					<?php
					endif;
					?>
                    <a class="product-thumbnail" href="<?php echo esc_url( $product->get_permalink() ); ?>">
						<?php echo $product->get_image(); ?>
                    </a>
                    <div class="product-content">
                        <a class="product-title" href="<?php echo esc_url( $product->get_permalink() ); ?>">
							<?php echo $product->get_name(); ?>
                        </a>

                        <span class="price"><?php echo $product->get_price_html(); ?></span>

						<?php
						if ( wc_review_ratings_enabled() ) {
							echo wc_get_rating_html( $product->get_average_rating() );
						}
						if ( presscore_config()->get( 'product.related.show_cart_btn' ) ) {
							echo '<div class="woo-buttons">' . dt_woocommerce_get_product_add_to_cart_icon() . '</div>';
						}
					?>
                    </div>
                </li>

			<?php endforeach; ?>

        </ul>

	</section>
	<?php
endif;

wp_reset_postdata();
