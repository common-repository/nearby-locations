<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://www.aaronjfrey.com/
 * @since      1.0.0
 *
 * @package    Nearby_Locations
 * @subpackage Nearby_Locations/admin/partials
 */

// Get all of the location types
global $wpdb;
$table_name = $wpdb->prefix . "ajf_nl_sections"; 
$location_types = $wpdb->get_results("SELECT * FROM $table_name ORDER BY `order` ASC", OBJECT);

$api_key = get_option("ajf-nl-google-api-key");

?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<h1>Add Location</h1>

<div id="ajf-nearby-locations-message"></div>

<br>

<?php if ($api_key) : ?>

<form id="location-form">

	<div class="form-control">
		<label for="name">Location Name</label>
		<input class="regular-text" type="text" name="name" id="name" required>
	</div>

	<div class="form-control">
		<label for="address">Location Address</label>
		<input class="regular-text" type="text" name="address" id="address" required>
	</div>

	<div class="form-control">
		<label for="type">Location Type</label>
		<select name="type" id="type" required>
			<option value="-99">-- None Selected --</option>
      <?php foreach ($location_types as $type) : ?>
			<option value="<?php echo $type->id; ?>"><?php echo esc_html($type->name); ?></option>
      <?php endforeach; ?>
		</select>
	</div>

	<button
		class="button button-primary submit-button indented"
		type="button">Add Location</button>

</form>

<br>

<?php
include(plugin_dir_path(dirname(dirname(__FILE__))) . 'shared/partials/nearby-locations-map-display.php');
include(plugin_dir_path(__FILE__) . 'nearby-locations-admin-table.php');

endif;
