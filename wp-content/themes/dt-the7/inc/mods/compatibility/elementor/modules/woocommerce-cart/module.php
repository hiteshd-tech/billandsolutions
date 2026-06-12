<?php

namespace The7\Mods\Compatibility\Elementor\Modules\Woocommerce_Cart;

use Elementor\Element_Base;
use Elementor\Plugin as Elementor;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Module_Base;
use The7\Mods\Compatibility\Elementor\Widgets\Woocommerce\Cart_Preview;
use WC_AJAX;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Module extends The7_Elementor_Module_Base {

	protected $is_inject_wc_cart_template = false;

	public function __construct() {
		add_filter( 'woocommerce_add_to_cart_fragments', [ $this, 'menu_cart_fragments' ] );
		add_action( 'elementor/frontend/widget/after_render', [ $this, 'after_cart_widget_render' ] );
		add_action( 'wp_footer', [ $this, 'inject_wc_cart_template' ], 100 );

		add_action( 'wp_ajax_the7_update_cart_item', [ $this, 'update_cart_item' ] );
		add_action( 'wp_ajax_nopriv_the7_update_cart_item', [ $this, 'update_cart_item' ] );
	}


	public function update_cart_item() {
		if ( ( isset( $_GET['item_id'] ) && $_GET['item_id'] ) && ( isset( $_GET['quantity'] ) ) ) {
			global $woocommerce;
			if ( $_GET['quantity'] ) {
				$woocommerce->cart->set_quantity( $_GET['item_id'], $_GET['quantity'] );
			} else {
				$woocommerce->cart->remove_cart_item( $_GET['item_id'] );
			}
		}
		if ( isset( $_GET['get_fragments'] ) && $_GET['get_fragments'] ) {
			WC_AJAX::get_refreshed_fragments();
		}
		wp_send_json_success();
	}

	/**
	 * @param Element_Base $widget The widget.
	 */
	public function after_cart_widget_render( Element_Base $widget ) {
		if ( $widget instanceof Cart_Preview ) {
			$this->is_inject_wc_cart_template = true;
		}
	}

	public function inject_wc_cart_template() {
		if ( $this->is_inject_wc_cart_template ) {
			self::display_cart_template();
		}
	}

	public static function display_cart_template() {
		?>
        <div class="the7-e-mini-cart-template">
			<?php echo self::get_cart_content(); ?>
			<?php echo self::get_cart_subtotal(); ?>
        </div>
		<?php
	}

	/**
	 * Render menu cart markup.
	 * The `the7-e-woo-cart-widget-content` div will be populated by woocommerce js.
	 * When in the editor we populate this on page load as we can't rely on the woocoommerce js to re-add the fragments
	 * each time a widget us re-rendered.
	 */
	public static function get_cart_content() {
		if ( null === WC()->cart ) {
			return '';
		}
		ob_start();
		$cart_status = '';
		if ( empty( WC()->cart->get_cart() ) ) {
			$cart_status = 'the7-e-woo-cart-status-cart-empty';
		}
		?><div class="the7-e-woo-cart-fragment the7-e-woo-cart-fragment-content the7-e-woo-cart-content <?php echo $cart_status ?>"><?php
		$cart_items = WC()->cart->get_cart();
		if ( ! empty( $cart_items ) ) {
                do_action( 'woocommerce_before_mini_cart_contents' ); ?>
                <span class="item-divider" aria-hidden="true"></span>
                <?php
				foreach ( $cart_items as $cart_item_key => $cart_item ) {
					self::render_mini_cart_item( $cart_item_key, $cart_item );
					?>
                    <span class="item-divider" aria-hidden="true"></span>
                    <?php
				}
				do_action( 'woocommerce_mini_cart_contents' );
		}
		?></div><?php
		return ob_get_clean();
	}

	protected static function render_mini_cart_item( $cart_item_key, $cart_item ) {
		$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
		$is_product_visible = ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key ) );

		if ( ! $is_product_visible ) {
			return;
		}

		$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
		$product_price = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
		$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );


		$class = '';
		if ( $_product->is_sold_individually() ) {
			$class = 'individual';
		}

		?>
        <div class="the7-e-mini-cart-product woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>  <?php echo $class; ?>">

            <div class="product-thumbnail">
				<?php
				$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );

				if ( $product_permalink ) {
					/**
					 * @see \The7\Mods\Compatibility\Elementor\Widget_Templates\Image_Aspect_Ratio::get_wrapper_class()
					 */
					printf( '<a class="img-css-resize-wrapper" href="%s">%s</a>', esc_url( $product_permalink ), wp_kses_post( $thumbnail ) );
				} else {
					echo wp_kses_post( $thumbnail );
				}
				?>
            </div>
            <div class="cart-info">
                <div class="product-name"
                     data-title="<?php esc_attr_e( 'Product', 'the7mk2' ); ?>">
					<?php
					if ( ! $product_permalink ) :
						echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . '&nbsp;' );
					else :
						echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $cart_item, $cart_item_key ) );
					endif;

					do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );

					// Meta data.
					echo wc_get_formatted_cart_item_data( $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					?>
                </div>

                <div class="product-price"
                     data-title="<?php esc_attr_e( 'Price', 'the7mk2' ); ?>">

                    <div class="product-quantity" data-title="<?php esc_attr_e( 'Quantity', 'woocommerce' ); ?>">
						<?php
                        if ( $_product->is_sold_individually() ) {
                            $product_quantity = sprintf( '<input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
                        } else {
							$product_quantity = woocommerce_quantity_input( array(
								'input_name'   => "cart[{$cart_item_key}][qty]",
								'input_value'  => $cart_item['quantity'],
								'max_value'    => $_product->get_max_purchase_quantity(),
								'min_value'    => '0',
								'product_name' => $_product->get_name(),
							), $_product, false );
						}

						echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item ); // PHPCS: XSS ok.
						?>
                    </div>
	                <?php
                    $q_content = '%2$s';
                    if ( !$_product->is_sold_individually()){
	                    $q_content = '<span class="product-item-quantity">%1$s</span><span class="quantity-separator">&nbsp;&times;&nbsp;</span>' . $q_content;
                    }
					 echo apply_filters( 'woocommerce_widget_cart_item_quantity', '<span class="quantity">' . sprintf( $q_content, $cart_item['quantity'], $product_price ) . '</span>', $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					?>
                </div>
            </div>
            <div class="product-remove">
				<?php echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					'woocommerce_cart_item_remove_link', sprintf( '<a href="%s" class="remove remove_from_cart_button" aria-label="%s" data-product_id="%s" data-cart_item_key="%s" data-product_sku="%s"><div class="the7_template_icon_remove"></div></a>', esc_url( wc_get_cart_remove_url( $cart_item_key ) ), esc_attr__( 'Remove this item', 'woocommerce' ), esc_attr( $product_id ), esc_attr( $cart_item_key ), esc_attr( $_product->get_sku() ) ), $cart_item_key ); ?>
            </div>
        </div>
		<?php
	}

	public static function get_cart_subtotal() {
		if ( null === WC()->cart ) {
			return '';
		}
		ob_start(); ?>
        <div class="the7-e-woo-cart-fragment the7-e-woo-cart-fragment-subtotal">
            <?php $cart_items = WC()->cart->get_cart();
                if ( ! empty( $cart_items ) ) {
                     echo WC()->cart->get_cart_subtotal();
                }
            ?>
        </div>
		<?php
		return ob_get_clean();
	}

	public function menu_cart_fragments( $fragments ) {
		$has_cart = is_a( WC()->cart, 'WC_Cart' );
		if ( ! $has_cart ) {
			return $fragments;
		}

		$html = self::get_cart_subtotal( );
		if ( ! empty( $html ) ) {
			$fragments['.the7-e-woo-cart-fragment-subtotal'] = $html;
		}

		$html = self::get_cart_content( );
		if ( ! empty( $html ) ) {
			$fragments['.the7-e-woo-cart-fragment-content'] = $html;
		}
		return $fragments;
	}

	public function get_name() {
		return 'woocommerce-cart';
	}
}
