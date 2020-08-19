<?php
/**
 * Show options for ordering
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/orderby.php.
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
 * @version     2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$shopLayout = FashionistOptions::get( 'shop_layout' );
$temp_list = 1;
$temp_gird = 2;
if($shopLayout != null && ( $shopLayout == 3 || $shopLayout == 4) ){
	$temp_list = 3;
	$temp_gird = 4;
}

if(isset($_GET['style']) && ($_GET['style'] == 3 || $_GET['style'] == 4) ) {
	$temp_list = 3;
	$temp_gird = 4;
}


?>
<div class="col-xs-12 col-sm-12 col-md-7">
	<form class="woocommerce-ordering" method="get">
		<div class="select-outer">
			<select name="orderby" class="orderby custom-select">
				<?php foreach ( $catalog_orderby_options as $id => $name ) : ?>
					<option value="<?php echo esc_attr( $id ); ?>" <?php selected( $orderby, $id ); ?>><?php echo esc_html( $name ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<?php
			// Keep query string vars intact
			foreach ( $_GET as $key => $val ) {
				if ( 'orderby' === $key || 'submit' === $key ) {
					continue;
				}
				if ( is_array( $val ) ) {
					foreach( $val as $innerVal ) {
						echo '<input type="hidden" name="' . esc_attr( $key ) . '[]" value="' . esc_attr( $innerVal ) . '" />';
					}
				} else {
					echo '<input type="hidden" name="' . esc_attr( $key ) . '" value="' . esc_attr( $val ) . '" />';
				}
			}
		?>
	</form>
	<a href="?&style=<?php echo esc_attr( $temp_gird ); ?>" class="change-view" id="pro_list"><span class="icon-grid"></span></a>
	<a href="?&style=<?php echo esc_attr( $temp_list ); ?>" class="change-view" id="pro_grid"><span class="icon-list"></span></a>
</div>