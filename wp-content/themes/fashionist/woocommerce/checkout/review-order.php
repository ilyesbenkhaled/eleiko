<?php
/**
 * Review order table
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/review-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="shop_table woocommerce-checkout-review-order-table row table">
		<div class="row tr">
			<div class="col-xs-8 td"><span class="title"><?php esc_html_e( 'Subtotal', 'fashionist' ); ?></span></div>
			<div class="col-xs-4 td"><span class="price"><?php wc_cart_totals_subtotal_html(); ?></span></div>
		</div>

		<?php
			do_action( 'woocommerce_review_order_before_cart_contents' );

			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
				$_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

				if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
					?>
					<div class="row tr <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">					
						<div class="col-xs-8 td">	
							<?php echo apply_filters( 'woocommerce_checkout_cart_item_quantity', ' <span class="title">' .sprintf( '%s &times;', $cart_item['quantity'] ), $cart_item_key ,$cart_item); ?>
							<?php echo apply_filters( 'woocommerce_cart_item_name', $_product->get_title() . '</span>', $cart_item, $cart_item_key ) . '&nbsp;'; ?>
							
							<?php echo WC()->cart->get_item_data( $cart_item ); ?>
						</div>
						<div class="col-xs-4 td">
							<span class="price"><?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); ?></span>
						</div>
					</div>
					<?php
				}
			}

			do_action( 'woocommerce_review_order_after_cart_contents' );
		?>

		<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
			<div class="row tr cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
				<div class="col-xs-8 td"><span class="title"><?php wc_cart_totals_coupon_label( $coupon ); ?></span></div>
				<div class="col-xs-4 td"><span class="price"><?php wc_cart_totals_coupon_html( $coupon ); ?></span></div>
			</div>
		<?php endforeach; ?>

		<?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>

			<?php do_action( 'woocommerce_review_order_before_shipping' ); ?>
			<div class="row tr">
				<?php //wc_cart_totals_shipping_html(); 
					$packages = WC()->shipping->get_packages();
					foreach ( $packages as $i => $package ) {
						$chosen_method = isset( WC()->session->chosen_shipping_methods[ $i ] ) ? WC()->session->chosen_shipping_methods[ $i ] : '';
						$product_names = array();
						if ( sizeof( $packages ) > 1 ) {
							foreach ( $package['contents'] as $item_id => $values ) {
								$product_names[] = $values['data']->get_title() . ' &times;' . $values['quantity'];
							}
						}
						wc_get_template( 'cart/cart-shipping.php', array(
							'package'              => $package,
							'available_methods'    => $package['rates'],
							'show_package_details' => sizeof( $packages ) > 1,
							'package_details'      => implode( ', ', $product_names ),
							// @codingStandardsIgnoreStart
							'package_name'         => apply_filters( 'woocommerce_shipping_package_name', sprintf( _n( '<div class="col-xs-8 td"><span class="title">Shipping</span></div>', '<div class="col-xs-4 td"><span class="price">Shipping %d</span></div>', ( $i + 1 ), 'fashionist' ), ( $i + 1 ) ), $i, $package ),
							// @codingStandardsIgnoreEnd
							'index'                => $i,
							'chosen_method'        => $chosen_method,
						) );
					}
				?>	
			</div>
			<?php do_action( 'woocommerce_review_order_after_shipping' ); ?>

		<?php endif; ?>

		<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
			<div class="row tr">
				<div class="col-xs-8 td"><span class="title"><?php echo esc_html( $fee->name ); ?></span></div>
				<div class="col-xs-4 td"><span class="price"><?php wc_cart_totals_fee_html( $fee ); ?></span></div>
			</div>
		<?php endforeach; ?>

		<?php if ( wc_tax_enabled() && 'excl' === WC()->cart->tax_display_cart ) : ?>
			<?php if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) : ?>
				<?php foreach ( WC()->cart->get_tax_totals() as $code => $tax ) : ?>
					<div class="row tr tax-rate tax-rate-<?php echo sanitize_title( $code ); ?>">
						<div class="col-xs-8 td"><span class="title"><?php echo esc_html( $tax->label ); ?></span></div>
						<div class="col-xs-4 td"><span class="price"><?php echo wp_kses_post( $tax->formatted_amount ); ?></span></div>
					</div>
				<?php endforeach; ?>
			<?php else : ?>
				<div class="row tr">
					<div class="col-xs-8 td"><span class="title"><?php echo esc_html( WC()->countries->tax_or_vat() ); ?></span></div>
					<div class="col-xs-4 td"><span class="price"><?php wc_cart_totals_taxes_total_html(); ?></span></div>
				</div>
			<?php endif; ?>
		<?php endif; ?>

		<?php do_action( 'woocommerce_review_order_before_order_total' ); ?>

		<div class="row tr">
			<div class="col-xs-8 td"><span class="title"><?php esc_html_e( 'Total', 'fashionist' ); ?></span></div>
			<div class="col-xs-4 td"><span class="price"><?php wc_cart_totals_order_total_html(); ?></span></div>
		</div>

		<?php do_action( 'woocommerce_review_order_after_order_total' ); ?>
</div>
