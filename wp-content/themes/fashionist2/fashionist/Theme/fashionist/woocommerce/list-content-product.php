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
$review_count = $product->get_review_count();
$regular_price = get_post_meta( get_the_ID(), '_regular_price', true);
?>

<div class="col-xs-12 col-sm-6 col-md-12">
	<div class="row">
		<div class="product">
			<?php if($temp == 3) { ?>
			<div class="col-xs-12 col-md-4">
			<?php } else { ?>
			<div class="col-xs-12 col-md-3">
			<?php } ?>
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
			</div>
			<?php if($temp == 3) { ?>
			<div class="col-xs-12 col-md-8">
			<?php } else { ?>
			<div class="col-xs-12 col-md-9">
			<?php } ?>
				<div class="product-info">
					<span class="name"><a href="<?php echo esc_url( $product->get_permalink() ); ?>"><?php echo esc_html( $product->get_title() ); ?></a></span>
					<span class="price"><?php echo WC()->cart->get_product_price( $product ); ?></span>

					<div class="rating">
						<?php $average = $product->get_average_rating();
	                    for ($i=1; $i <= 5; $i++) {
	                        if($average > 1){ ?>
	                        <span class="icon-star active"></span>
	                        <?php } else { ?>
	                        <span class="icon-star"></span>
	                        <?php }
	                        $average --;
	                    }?>

						<span class="reviews">(<?php echo esc_html( $review_count ).esc_html__(' reviews','fashionist');?>)</span>
					</div>

					<p class="description hidden-xs hidden-sm"><?php echo fashionist_excerpt(50); ?></p>

					<div class="actions">
						<?php if ($regular_price == ""){ ?>
							<a rel="nofollow" href="<?php echo esc_url( $product->get_permalink() ); ?>" class="action text"><?php echo esc_html__('Add to cart','fashionist'); ?></a>
						<?php } else { ?>
							<a rel="nofollow" href="/?add-to-cart=<?php echo intval($product->get_id()); ?>" data-quantity="1" data-product_id="<?php echo intval($product->get_id()); ?>" data-product_sku="" class="action text add_to_cart_button ajax_add_to_cart added"><?php echo esc_html__('Add to cart','fashionist'); ?></a>
						<?php } ?>

						<a class="action hidden-xs" href="<?php echo esc_url($image[0]); ?>" data-lightbox="<?php echo rand(); ?>"><span class="icon-maximize-plus"></span></a>
						<a class="action hidden-xs add_to_wishlist" href="?add_to_wishlist=<?php echo intval($product->get_id()); ?>" data-product-type="simple" data-product-id="<?php echo intval($product->get_id()); ?>" rel="nofollow"><span class="icon-heart"></span></a>
						<?php if ($regular_price == ""){ ?>
							<a rel="nofollow" href="<?php echo esc_url( $product->get_permalink() ); ?>" class="action hidden-xs"><span class="icon-shoppingcart"></span></a>
						<?php } else { ?>
							<a rel="nofollow" href="/?add-to-cart=<?php echo intval($product->get_id()); ?>" data-quantity="1" data-product_id="<?php echo intval($product->get_id()); ?>" data-product_sku="" class="action hidden-xs add_to_cart_button ajax_add_to_cart added"><span class="icon-shoppingcart"></span></a>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>