<?php
/**
 * Single variation cart button
 *
 * @see 	https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.5.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;
$image = wp_get_attachment_image_src( get_post_thumbnail_id( $product->get_id() ), 'fashionist_product_list' );
$review_count = $product->get_review_count();
$regular_price = get_post_meta( get_the_ID(), '_regular_price', true);
?>
<span class="headline">Quantity</span>
<div class="woocommerce-variation-add-to-cart variations_button">
	<?php if ( ! $product->is_sold_individually() ) : ?>
		<?php woocommerce_quantity_input( array( 'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( $_POST['quantity'] ) : 1 ) ); ?>
	<?php endif; ?>
	<button type="submit" class="single_add_to_cart_button button alt add-to-cart hidden-xs"><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button>
	<div class="mini-btn">
		<a class="action" href="<?php echo esc_url($image[0]); ?>" data-lightbox="image">
			<span class="icon-maximize-plus"></span>
		</a>
	</div>
	<div class="mini-btn">
		<a class="action add_to_wishlist" href="?add_to_wishlist=<?php echo intval($product->get_id()); ?>" data-product-type="simple" data-product-id="<?php echo intval($product->get_id()); ?>" rel="nofollow">
			<span class="icon-heart"></span>
		</a>
	</div>
	<button type="submit" class="single_add_to_cart_button button alt add-to-cart icon-shoppingcart"></button>
	<input type="hidden" name="add-to-cart" value="<?php echo absint( $product->get_id() ); ?>" />
	<input type="hidden" name="product_id" value="<?php echo absint( $product->get_id() ); ?>" />
	<input type="hidden" name="variation_id" class="variation_id" value="0" />
</div>
