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
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product, $woocommerce_loop;

if ( empty( $product ) || ! $product->exists() ) {
	return;
}

if ( ! $related = wc_get_related_products( $product->get_id(), $posts_per_page) ) {
	return;
}

?>
<?php
$args = apply_filters( 'woocommerce_related_products_args', array(
	'post_type'            => 'product',
	'ignore_sticky_posts'  => 1,
	'no_found_rows'        => 1,
	'posts_per_page'       => $posts_per_page,
	'orderby'              => $orderby,
	'post__in'             => $related,
	'post__not_in'         => array( $product->get_id() )
) );

$products                    = new WP_Query( $args );
$woocommerce_loop['name']    = 'related';
$woocommerce_loop['columns'] = apply_filters( 'woocommerce_related_products_columns', $columns );
wp_reset_postdata();
if ( $products->have_posts() ) : ?>

	<section id="related-products">

		<div class="col-xs-12"><h2><?php esc_html_e( 'Related Products', 'fashionist' ); ?></h2></div>

		<?php woocommerce_product_loop_start(); ?>

			<?php while ( $products->have_posts() ) : $products->the_post();
				$image = wp_get_attachment_image_src( get_post_thumbnail_id( $products->ID ), 'product_grid' );
				?>
				<div class="col-xs-12 col-sm-6 col-lg-3">
					<div class="product" id="p<?php echo get_post_thumbnail_id( $products->ID ); ?>">
						<div class="product-img">
							<img class="img-responsive" alt="Single product" src="<?php echo esc_url($image[0]); ?>">
							<div class="actions">
								<a class="action" href="<?php echo esc_url($image[0]); ?>" data-lightbox="image"><span class="icon-maximize-plus"></span></a>
								<a class="action add_to_wishlist" href="?add_to_wishlist=<?php echo get_post_thumbnail_id( $products->ID ); ?>" data-product-type="simple" data-product-id="<?php echo get_post_thumbnail_id( $products->ID ); ?>" rel="nofollow"><span class="icon-heart"></span></a>
								<a rel="nofollow" href="/?add-to-cart=<?php echo get_post_thumbnail_id( $products->ID ); ?>" data-quantity="1" data-product_id="<?php echo get_post_thumbnail_id( $products->ID ); ?>" data-product_sku="" class="action add_to_cart_button ajax_add_to_cart added"><span class="icon-shoppingcart"></span></a>
							</div>
							<div class="add-to-cart">
								<span class="icon-plus" onclick="show_actions('p<?php echo get_post_thumbnail_id( $products->ID ); ?>')"></span>
							</div>
							<div class="add-to-cart close">
								<span class="icon-plus" onclick="hide_actions('p<?php echo get_post_thumbnail_id( $products->ID ); ?>')"></span>
							</div>
						</div>
						<span class="name"><?php echo esc_html( $product->get_title() ); ?></span>
						<span class="price"><?php echo WC()->cart->get_product_price( $product ); ?></span>
					</div>
				</div>

			<?php endwhile; // end of the loop. ?>

		<?php woocommerce_product_loop_end(); ?>

	</section>

<?php endif;

wp_reset_postdata();
