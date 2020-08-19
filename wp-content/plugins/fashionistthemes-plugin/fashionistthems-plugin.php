<?php
/**
 * Plugin Name: Fashionist Thems Plugin
 * Description: An fashionist toolkit that helps you set theme. Beautifully.
 * Version: 1.0.0
 * Tested up to: 4.6
 *
 * Text Domain: fashionist
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

add_action('wp_head','fashionist_ajax_url');
function fashionist_ajax_url()
{ ?>
<script type="text/javascript">
	var ajaxThemeUrl = '<?php  echo plugins_url();?>';
</script>
<?php }

// Load the Main files init if it exists
if ( file_exists( dirname( __FILE__ ). '/admin/admin-init.php' ) ) {
    require_once dirname( __FILE__ ). '/admin/admin-init.php';
}

// Load the Redux Importer Extenssion Files
if ( file_exists( dirname( __FILE__ ). '/admin/wbc_importer/extension_wbc_importer.php' ) ) {
    require_once dirname( __FILE__ ). '/admin/wbc_importer/extension_wbc_importer.php';
}

// Load the Redux Importer Extenssion Files
if ( file_exists( dirname( __FILE__ ). '/admin/importer-functions.php' ) ) {
    require_once dirname( __FILE__ ). '/admin/importer-functions.php';
}


if ( file_exists( dirname( __FILE__ ). '/inc/MCAPI.class.php' ) ) {
	require_once(dirname( __FILE__ ).'/inc/MCAPI.class.php');
}
/*----------- Mailchimp Popup----------*/
/**
 * Implement the Custom Header feature.
 */
if ( file_exists( dirname( __FILE__ ). '/inc/custom-header.php' ) ) {
	require_once dirname( __FILE__ ). '/inc/custom-header.php';
}
/**
 * Custom functions that act independently of the theme templates.
 */
if ( file_exists( dirname( __FILE__ ). '/inc/extras.php' ) ) {
	require_once dirname( __FILE__ ). '/inc/extras.php';
}

/**
 * Load Jetpack compatibility file.
 */
if ( file_exists( dirname( __FILE__ ). '/inc/jetpack.php' ) ) {
	require_once dirname( __FILE__ ). '/inc/jetpack.php';
}
/**
 * Make Duplicate.
 */
if ( file_exists( dirname( __FILE__ ). '/inc/duplicate.php' ) ) {
	require_once dirname( __FILE__ ). '/inc/duplicate.php';
}
/**
 * Load custom composers.
 */
if ( file_exists( dirname( __FILE__ ). '/inc/custom_composers/composers.php' ) ) {
	require_once dirname( __FILE__ ). '/inc/custom_composers/composers.php';
}
/**
 * Load custom functions.
 */
if ( file_exists( dirname( __FILE__ ). '/inc/custom-functions.php' ) ) {
	require_once dirname( __FILE__ ). '/inc/custom-functions.php';
}
/**
 * Load and insert default theme for maxmage menu.
 */
if ( file_exists( dirname( __FILE__ ). '/inc/megamenu.php' ) ) {
	require_once dirname( __FILE__ ). '/inc/megamenu.php';
}
