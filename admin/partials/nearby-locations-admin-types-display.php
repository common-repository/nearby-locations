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

global $wpdb;

$btn_text = 'Add Location Type';

$location_type = [
	'id'		=> '',
	'name' 	=> '',
	'order'	=> ''
];

if (isset($_GET['action']) && $_GET['action'] === 'edit') {
	// get the location type
	$location_type_id = isset($_GET['location_type']) ? $_GET['location_type'] : '';
	if ($location_type_id) {
		$table_name = $wpdb->prefix . "ajf_nl_sections"; 
		$location_type = $wpdb->get_row("SELECT * FROM $table_name WHERE id = $location_type_id", "ARRAY_A");
		$btn_text = 'Edit Location Type';
	}
}

$api_key = get_option("ajf-nl-google-api-key");

?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<h1>Add Location Type</h1>

<div id="ajf-nearby-locations-message"></div>

<br>

<?php if ($api_key) : ?>

<form id="location-type-form">

	<input type="hidden" name="type-id" id="type-id" value="<?php echo esc_attr($location_type_id); ?>">

	<div class="form-control">
		<label for="type-name">Location Type Name</label>
		<input class="regular-text" type="text" name="type-name" id="type-name" value="<?php echo esc_attr($location_type['name']); ?>" required>
	</div>

	<div class="form-control">
		<label for="type-order">Location Type Order</label>
		<input class="regular-text" type="text" name="type-order" id="type-order" value="<?php echo esc_attr($location_type['order']); ?>" required>
	</div>

	<button
		class="button button-primary submit-button indented"
		type="button"><?php echo $btn_text; ?></button>

</form>

<?php

include(plugin_dir_path(__FILE__) . 'nearby-locations-admin-types-table.php');

endif;