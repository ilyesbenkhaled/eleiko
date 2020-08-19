<?php
/**
 * Simple product add to cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/simple.php.
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
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

$image = wp_get_attachment_image_src( get_post_thumbnail_id( $product->get_id() ), 'fashionist_product_list' );
$review_count = $product->get_review_count();
$regular_price = get_post_meta( get_the_ID(), '_regular_price', true);

if ( ! $product->is_purchasable() ) {
	return;
}

?>
<div class="addtocart">
<span class="headline"><?php esc_html_e('Quantity','fashionist'); ?></span>
	<?php
		// Availability
		$availability      = $product->get_availability();
		$availability_html = empty( $availability['availability'] ) ? '' : '<p class="stock ' . esc_attr( $availability['class'] ) . '">' . esc_html( $availability['availability'] ) . '</p>';

		echo apply_filters( 'woocommerce_stock_html', $availability_html, $availability['availability'], $product );
	?>

	<?php if ( $product->is_in_stock() ) : ?>

		<?php do_action( 'woocommerce_before_add_to_cart_form' ); ?>

		<form class="cart" method="post" enctype='multipart/form-data'>
		 	<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

		 	<?php
		 		if ( ! $product->is_sold_individually() ) {
		 			woocommerce_quantity_input( array(
		 				'min_value'   => apply_filters( 'woocommerce_quantity_input_min', 1, $product ),
		 				'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->backorders_allowed() ? '' : $product->get_stock_quantity(), $product ),
		 				'input_value' => ( isset( $_POST['quantity'] ) ? wc_stock_amount( $_POST['quantity'] ) : 1 )
		 			) );
		 		}
		 	?>

		 	<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" />
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
			<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
		</form>

		<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>

	<?php endif; ?>
</div>