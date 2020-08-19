<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://scrybs.com/
 * @since      1.0.0
 *
 * @package    Scrybs
 * @subpackage Scrybs/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Scrybs
 * @subpackage Scrybs/includes
 * @author     Scrybs <info@scrybs.com>
 */
class Scrybs_Deactivator {
	public static function deactivate() {
		if(get_option('scrybs_old_permalink_structure_empty')=="on") {
			delete_option('scrybs_old_permalink_structure_empty');
			update_option('permalink_structure','');
		}
	}

}
