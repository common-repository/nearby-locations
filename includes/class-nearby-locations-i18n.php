<?php

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Nearby_Locations
 * @subpackage Nearby_Locations/includes
 * @author     Aaron Frey <aaron.frey@gmail.com>
 */
class AJF_Nearby_Locations_i18n {

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'ajf-nearby-locations',
			false,
			dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
		);
	}
}