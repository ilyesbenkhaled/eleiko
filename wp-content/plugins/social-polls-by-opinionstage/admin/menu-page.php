<?php
// block direct access to plugin PHP files:
defined( 'ABSPATH' ) or die();

require_once( plugin_dir_path( __FILE__ ).'../includes/opinionstage-client-session.php' );

add_action( 'admin_menu', 'opinionstage_register_menu_page' );

function opinionstage_register_menu_page() {
	if (function_exists('add_menu_page')) {

		add_menu_page(
			__('Opinion Stage', OPINIONSTAGE_TEXT_DOMAIN),
			__('Opinion Stage', OPINIONSTAGE_TEXT_DOMAIN),
			'edit_posts',
			OPINIONSTAGE_MENU_SLUG,
			'opinionstage_menu_page',
			plugins_url('admin/images/os.png', plugin_dir_path( __FILE__ )),
			'25.234323221'
		);
		add_submenu_page(OPINIONSTAGE_MENU_SLUG, 'Create...', 'Create...', 'manage_options', OPINIONSTAGE_MENU_SLUG);
		add_submenu_page(OPINIONSTAGE_MENU_SLUG, 'Placements', 'Placements', 'manage_options', 'opinionstage_my_placements' , 'opinionstage_my_placements' );
		add_submenu_page(OPINIONSTAGE_MENU_SLUG, 'Getting Started', 'Getting Started', 'manage_options', 'opinionstage_getting_started','opinionstage_getting_started' );
		add_submenu_page(OPINIONSTAGE_MENU_SLUG, 'Help Center', 'Help Center', 'manage_options', 'https://help.opinionstage.com/?utm_campaign=WPMainPI&utm_medium=linkhelpcenter&utm_source=wordpress&o=wp35e8' );
		add_submenu_page(OPINIONSTAGE_MENU_SLUG, 'Live Examples', 'Live Examples', 'manage_options', 'https://www.opinionstage.com/discover?utm_campaign=WPMainPI&utm_medium=linkexamples&utm_source=wordpress&o=wp35e8' );
	}
}

function opinionstage_menu_page() {
	$os_options = (array) get_option(OPINIONSTAGE_OPTIONS_KEY);

	$os_client_logged_in = opinionstage_user_logged_in();

	opinionstage_register_css_asset( 'menu-page', 'menu-page.css' );
	opinionstage_register_css_asset( 'icon-font', 'icon-font.css' );
	opinionstage_register_javascript_asset( 'menu-page', 'menu-page.js', array('jquery') );

	require( plugin_dir_path( __FILE__ ).'create-page-template.php' );
}

function opinionstage_my_placements(){
	$os_options = (array) get_option(OPINIONSTAGE_OPTIONS_KEY);

	$os_client_logged_in = opinionstage_user_logged_in();

	opinionstage_register_css_asset( 'menu-page', 'menu-page.css' );
	opinionstage_register_css_asset( 'icon-font', 'icon-font.css' );
	opinionstage_register_javascript_asset( 'menu-page', 'menu-page.js', array('jquery') );
    require( plugin_dir_path( __FILE__ ).'/views/placement-page-template.php' );
}

function opinionstage_getting_started(){
    $os_options = (array) get_option(OPINIONSTAGE_OPTIONS_KEY);

	$os_client_logged_in = opinionstage_user_logged_in();

	opinionstage_register_css_asset( 'menu-page', 'menu-page.css' );
	opinionstage_register_css_asset( 'icon-font', 'icon-font.css' );
	opinionstage_register_javascript_asset( 'menu-page', 'menu-page.js', array('jquery') );
    require( plugin_dir_path( __FILE__ ).'/views/getting-started-page-template.php' );
}

?>
