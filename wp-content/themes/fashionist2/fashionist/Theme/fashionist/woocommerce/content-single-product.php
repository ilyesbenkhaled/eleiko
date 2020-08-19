<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
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
	exit; // Exit if accessed directly
}
global $product;
$cat_count = sizeof( get_the_terms( $post->ID, 'product_cat' ) );
$tag_count = sizeof( get_the_terms( $post->ID, 'product_tag' ) );
$style = 1;
$productLayout = FashionistOptions::get( 'product_layout' );
if($productLayout != null){
	$style = $productLayout;
}
if(isset($_GET['style'])){
	$style = $_GET['style'];
}
?>
<div class="container">
	<div class="row">
		<?php if($style == 1) { ?>
		<aside id="shop-sidebar" class="sidebar col-xs-12 col-md-3 woocommerce">
			<?php if ( is_active_sidebar( 'sidebar-2' ) ) : ?>
                <?php dynamic_sidebar('sidebar-2'); ?>
            <?php endif; ?>
		</aside>
		<div id="shop-list" class="content col-xs-12 col-md-9">
			<div class="row">
			<?php } ?>
				<section id="product">
					<div class="col-xs-12">
						<div class="row">
							<?php
								do_action( 'woocommerce_before_single_product' );

								 if ( post_password_required() ) {
								 	echo get_the_password_form();
								 	return;
								 }
							?>

							<div itemscope itemtype="<?php echo woocommerce_get_product_schema(); ?>" id="product-<?php the_ID(); ?>" <?php post_class(); ?>>

								<meta itemprop="url" content="<?php the_permalink(); ?>" />

								<div class="col-xs-12 col-sm-5">
									<div class="gallery">
										<?php do_action( 'woocommerce_before_single_product_summary' ); ?>
									</div>
								</div>
								<div class="col-xs-12 col-sm-7">
									<div class="summary entry-summary">
										<?php
											//do_action( 'woocommerce_single_product_summary' );
										?>
										<span class="name"><?php the_title(); ?></span>
										<span class="price">
											<?php echo wp_kses_post( $product->get_price_html() ); ?>
										</span>
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
											<span class="reviews">(<?php echo wp_kses_post( $product->get_review_count() ); esc_html_e(' reviews','fashionist'); ?>  )</span>

										</div>
										<span class="availability"><?php echo ($product->is_in_stock()) ? esc_html__('available in stock','fashionist') : esc_html__('not available in stock','fashionist'); ?></span>

										<span class="headline"><?php esc_html_e('Description','fashionist'); ?></span>
										<div class="description"><?php echo apply_filters( 'woocommerce_short_description', $post->post_excerpt ); ?></div>
										<?php woocommerce_template_single_add_to_cart();  ?>

										<div class="product-info">
											<span><b><?php esc_html_e('Product Number:','fashionist'); ?></b> <?php echo ( $sku = $product->get_sku() ) ? $sku : esc_html__( 'N/A', 'fashionist' ); ?></span>
											<?php
												echo $tmp = wp_kses_post( wc_get_product_category_list($product->get_id(),', ', '<span>' . _n( '<b>Category: </b>', '<b>Categories: </b>', $cat_count, 'fashionist' ) . ' ', '</span>'));
											?>
											<?php
												echo wp_kses_post(wc_get_product_tag_list($product->get_id(),', ','<span>' . _n( '<b>Tag:</b> ', '<b>Tags:</b> ', $tag_count, 'fashionist' ) . ' ', '</span>' ));
											?>

										</div>
									</div><!-- .summary -->
								</div>
								<div class="col-xs-12">
									<?php do_action( 'woocommerce_after_single_product_summary' ); ?>
								</div>

							</div>

							<?php do_action( 'woocommerce_after_single_product' ); ?>
						</div>
					</div>
				</section>
			<?php if($style == 2) { ?>
			</div>
		</div>
		<?php } ?>
	</div>
</div>