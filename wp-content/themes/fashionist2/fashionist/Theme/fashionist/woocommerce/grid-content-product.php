<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$temp = '';
$shopLayout = FashionistOptions::get( 'shop_layout' );
if($shopLayout != null){
	$temp = $shopLayout;
}
if(isset($_GET['style'])){
	$temp = $_GET['style'];
}

global $product;

// Ensure visibility
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}
$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'fashionist_product_list' );
$regular_price = get_post_meta( get_the_ID(), '_regular_price', true);

?>
<?php if($temp == 4) { ?>
<div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
<?php } else { ?>
<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
<?php } ?>
	<div class="product">
		<div class="product-img">
			<a href="<?php echo esc_url( $product->get_permalink() ); ?>"><img class="img-responsive" alt="Single product" src="<?php echo esc_url($image[0]); ?>"></a>
			<div class="actions">
				<a class="action" href="<?php echo esc_url($image[0]); ?>" data-lightbox="<?php echo rand(); ?>"><span class="icon-maximize-plus"></span></a>
				
				<?php if ($regular_price == ""){ ?>
					<a rel="nofollow" href="<?php echo esc_url( $product->get_permalink() ); ?>" class="action"><span class="icon-shoppingcart"></span></a>
				<?php } else { ?>
					<a rel="nofollow" href="/?add-to-cart=<?php echo intval($product->get_id()); ?>" data-quantity="1" data-product_id="<?php echo intval($product->get_id()); ?>" data-product_sku="" class="action add_to_cart_button ajax_add_to_cart added"><span class="icon-shoppingcart"></span></a>
				<?php } ?>
			</div>
		</div>
		<span class="name"><a href="<?php echo esc_url( $product->get_permalink() ); ?>"><?php echo esc_html( $product->get_title() ); ?></a></span>
		<span class="price"><?php echo WC()->cart->get_product_price( $product ); ?></span>
	</div>
</div>