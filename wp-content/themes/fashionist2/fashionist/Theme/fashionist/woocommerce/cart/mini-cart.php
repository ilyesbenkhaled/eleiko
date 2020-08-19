<?php
/**
 * Mini-cart
 *
 * Contains the markup for the mini-cart, used by the cart widget.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/mini-cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
<?php do_action( 'woocommerce_before_mini_cart' ); ?>
	<?php if ( ! WC()->cart->is_empty() ) : ?>

		<?php
			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
				//var_dump($cart_item_key);
				
				$tempImg = wp_get_attachment_url( get_post_thumbnail_id($cart_item['product_id'],'large') );
				$_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
				$product_id   = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

				if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
					$product_name      = apply_filters( 'woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key );
					$thumbnail         = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
					$product_price     = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
					$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
					?>
					<div class="item">
						<img src="<?php echo esc_url($tempImg); ?>" alt="" class="item-img" />
						<div class="item-details">
							<span class="item-name"><?php echo esc_html( $product_name ); ?></span>
							<span class="item-price"><?php echo wp_kses_post( $product_price ); ?></span>
						
							<?php
							echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf(
								'<a href="%s" class="btn-item-delete" title="%s" data-product_id="%s" data-product_sku="%s">Delete</a>',
								esc_url( WC()->cart->get_remove_url( $cart_item_key ) ),
								__( 'Remove this item', 'fashionist' ),
								esc_attr( $product_id ),
								esc_attr( $_product->get_sku() )
							), $cart_item_key );
							?>
						</div>						
					</div>						
					<?php
				}
			}
		?>

	<?php else : ?>

		<li class="empty"><?php esc_html_e( 'No products in the cart.', 'fashionist' ); ?></li>

	<?php endif; ?>

<?php if ( ! WC()->cart->is_empty() ) : ?>
	<?php do_action( 'woocommerce_widget_shopping_cart_before_buttons' ); ?>
	
	<div class="item">
		<div class="item-details item-detailsbtn">
			<h5 class="title"><?php esc_html_e('Total On Your Cart','fashionist'); ?><span class="amount"><?php echo WC()->cart->get_cart_subtotal(); ?></span></h5>
			<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="btn-item-delete"><?php esc_html_e('View Cart','fashionist'); ?></a>
			<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="btn-item-delete"><?php esc_html_e('Checkout','fashionist'); ?></a>   
		</div>	
	</div>
<?php endif; ?>

<?php do_action( 'woocommerce_after_mini_cart' ); ?>
