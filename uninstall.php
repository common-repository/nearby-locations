<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * @link       http://www.aaronjfrey.com/
 * @since      1.0.0
 *
 * @package    Nearby_Locations
 */

// If uninstall not called from WordPress, then exit.
if (!defined('WP_UNINSTALL_PLUGIN')) {
	exit;
}

global $wpdb;

// Delete the locations table
$table_name = $wpdb->prefix . 'ajf_nl_locations';
$wpdb->query("DROP TABLE IF EXISTS $table_name");

// Delete the sections table
$table_name = $wpdb->prefix . 'ajf_nl_sections';
$wpdb->query("DROP TABLE IF EXISTS $table_name");

// Delete Options
delete_option('ajf-nl-version');
delete_option('ajf-nl-google-api-key');
delete_option('ajf-nl-center-address');
delete_option('ajf-nl-color-background');
delete_option('ajf-nl-color-panel');
delete_option('ajf-nl-color-text');