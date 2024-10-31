<?php

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Nearby_Locations
 * @subpackage Nearby_Locations/includes
 * @author     Aaron Frey <aaron.frey@gmail.com>
 */
class AJF_Nearby_Locations_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		// Create the locations table
		$table_name = $wpdb->prefix . "ajf_nl_locations"; 
		$sql = "CREATE TABLE $table_name (
		  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
		  `section_id` mediumint(9) NOT NULL,
		  `name` text NOT NULL,
		  `formatted` text NOT NULL,
		  `lat` FLOAT(10, 6) NOT NULL,
 			`lng` FLOAT(10, 6) NOT NULL,
 			`post_date` datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		  PRIMARY KEY  (id)
		) $charset_collate;";

		dbDelta($sql);

		// Create the sections table
		$table_name = $wpdb->prefix . "ajf_nl_sections"; 

		$sql = "CREATE TABLE $table_name (
		  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
		  `name` text NOT NULL,
		  `order` mediumint(9) NOT NULL,
		  PRIMARY KEY  (id)
		) $charset_collate;";

		dbDelta($sql);
	}
}