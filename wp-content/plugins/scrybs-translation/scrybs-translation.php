<?php
/**
 *
 * @link              https://scrybs.com/
 * @package           Scrybs
 *
 * @wordpress-plugin
 * Plugin Name:       Scrybs Translation - Easy WP Translation
 * Plugin URI:        https://scrybs.com/wordpress-multilingual-plugin/
 * Description:       Make your Wordpress Site multilingual in a few clicks. Automatic, manual or professional translation. Manage all of your translations using Scrybs. <a href="https://scrybs.com/en/auth/registration/plugin">Get your API key now</a> if you don't already have one.
 * Version:           1.3.3.3
 * Author:            Scrybs
 * Author URI:        https://scrybs.com/
 * Text Domain:       scrybs-translation
 * Domain Path:       /languages
 */

/* This plugin was forked from Weglot Translate and improved in many ways such as URL translation managed remotely, content cache system, 
*  translation cache system, ability to use the plugin if Wordpress is installed in a folder, possibility to exclude folders from 
*  translation.
*/

/*  Copyright 2016  Guilhem Fabre  (email : g@scrybs.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( ! defined( 'WPINC' ) ) {
	die;
}
define( 'SCRYBS_VERSION', '1.3.3.3' );

define( 'SCRYBS_PLUGIN_PATH', dirname( __FILE__ ) );
define( 'SCRYBS_PLUGIN_FILE', basename( __FILE__ ) );
define( 'SCRYBS_PLUGIN_FULL_PATH', basename( SCRYBS_PLUGIN_PATH ) . '/' . SCRYBS_PLUGIN_FILE );
define( 'SCRYBS_PLUGIN_FOLDER', basename( SCRYBS_PLUGIN_PATH ) );  
define( 'SCRYBS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'SCRYBS_PLUGIN_DIRURL', plugin_dir_url( __FILE__ ));
define( 'SCRYBS_CACHE_CONTENT_FOLDER', SCRYBS_PLUGIN_PATH.'/cache/content/');
define( 'SCRYBS_CONTENT_HASH', SCRYBS_PLUGIN_PATH.'/res/hashcontent.json');
define( 'SCRYBS_API_URL', 'https://scrybs.com/cloud/api/' );

require SCRYBS_PLUGIN_PATH . '/inc/simple_html_dom.php';
require SCRYBS_PLUGIN_PATH . '/inc/functions.php';
require SCRYBS_PLUGIN_PATH . '/inc/lang-data.php';

require plugin_dir_path( __FILE__ ) . 'includes/class-scrybs-client.php';    

global $scrybs, $wpdb;

function php_dependecy_admin_notice__error() {
	$class = 'notice notice-error';
	$message_one = __( 'Scrybs detected that you dont have the necessary php modules:', 'scrybs-domain' );
	$message_two = __( 'Please install <a href="http://php.net/manual/en/book.intl.php" target="_blank">php-intl</a>', 'scrybs-domain' );

    if( !function_exists( 'locale_accept_from_http' ) )  {

	   printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message_one );  
	   printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message_two );  
    }
}

add_action( 'admin_notices', 'php_dependecy_admin_notice__error' );

function activate_scrybs() {
    if( !function_exists( 'locale_accept_from_http' ) )
        return;
        
  	require_once plugin_dir_path( __FILE__ ) . 'includes/class-scrybs-activator.php';
  	Scrybs_Setup::activate();
  	Scrybs_Setup::fill_languages();
  	Scrybs_Setup::fill_languages_translations();
  	add_option('scrybs_url_exclusion','');
  	add_option('show_scrybs_box','no');
  	
  	/* This function was forked from Weglot */
  	if(get_option('permalink_structure')=="") {
  		add_option('scrybs_old_permalink_structure_empty','on');
  		update_option('permalink_structure','/%year%/%monthnum%/%day%/%postname%/');
  	}
    // check if file exists if not create it    
    if( !file_exists(SCRYBS_CONTENT_HASH) )    {
      $file = fopen(SCRYBS_CONTENT_HASH, 'w');
      fclose($file);
    }
}

register_activation_hook( __FILE__, 'activate_scrybs' );

function deactivate_scrybs() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-scrybs-deactivator.php';
	delete_option('scrybs_url_exclusion');
	delete_option('show_scrybs_box');
	delete_option('scrybs');
	Scrybs_Deactivator::deactivate();
}

register_deactivation_hook( __FILE__, 'deactivate_scrybs' );

function scrybs_add_settings_link( $links ) {
    $settings_link = '<a href="admin.php?page=Scrybs">' . __( 'Settings' ) . '</a>';
    array_push( $links, $settings_link );
  	return $links;
}
$plugin = plugin_basename( __FILE__ );
add_filter( "plugin_action_links_$plugin", 'scrybs_add_settings_link' );

require plugin_dir_path( __FILE__ ) . 'includes/class-scrybs-widget.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-scrybs.php';

$scrybs = new Scrybs();
$scrybs->run();