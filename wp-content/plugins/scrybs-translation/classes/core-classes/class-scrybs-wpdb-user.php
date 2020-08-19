<?php

/**
 * Class SCRYBS_WPDB_User
 *
 * Superclass for all Scrybs classes using the @global wpdb $wpdb
 *
 */
abstract class SCRYBS_WPDB_User {

	/** @var  wpdb $wpdb */
	protected $wpdb;

	/**
	 * @param wpdb $wpdb
	 */
	public function __construct( &$wpdb ) {
		$this->wpdb = &$wpdb;
	}
}