<?php

/**
 * @link       http://www.aaronjfrey.com/
 * @since      1.0.0
 *
 * @package    Nearby_Locations
 * @subpackage Nearby_Locations/admin/partials
 */

// Get all options
$api_key = get_option("ajf-nl-google-api-key");
$center_address = get_option("ajf-nl-center-address");
$background_color = esc_html(get_option('ajf-nl-color-background'));
$panel_color = esc_html(get_option('ajf-nl-color-panel'));
$text_color = esc_html(get_option('ajf-nl-color-text'));

?>

<h1>Settings</h1>

<div id="ajf-nearby-locations-message"></div>

<br>

<form id="settings-form">

	<div class="form-control">
		<label for="api-key">Google Maps API Key</label>
		<input
			class="regular-text"
			type="text"
			name="api-key"
			id="api-key"
			value="<?php echo $api_key ? esc_attr($api_key) : ''; ?>"
			required>
	</div>

	<?php if ($api_key && !$center_address) : ?>
	<div class="form-control">
		<label for="center-address">Featured Address</label>
		<input
			class="regular-text"
			type="text"
			name="center-address"
			id="center-address"
			<?php echo !$api_key ? 'disabled' : ''; ?>>
	</div>
	<div class="indented">Enter an address that will be the focal point of the map.</div>
	<?php endif; ?>

	<?php if ($center_address) : ?>
	<div class="form-control">
		<label for="center-address">Featured Address</label>
		<div class="regular-text" style="float: left;">
			<?php echo esc_textarea($center_address['address']); ?>
			<a href="#" id="remove-location">Remove</a>
		</div>
	</div>
	<?php endif; ?>

	<fieldset>

		<h2>Custom Colors</h2>

		<div class="form-control">
			<label for="sidebar-color">Background Color</label>
			<input
				type="text"
				name="sidebar-background-color"
				id="sidebar-background-color"
				class="color-field"
				value="<?php echo $background_color; ?>">
		</div>

		<div class="form-control">
			<label for="sidebar-color">Active Panel Color</label>
			<input
				type="text"
				name="sidebar-panel-color"
				id="sidebar-panel-color"
				class="color-field"
				value="<?php echo $panel_color; ?>">
		</div>

		<div class="form-control">
			<label for="sidebar-color">Text Color</label>
			<input
				type="text"
				name="sidebar-text-color"
				id="sidebar-text-color"
				class="color-field"
				value="<?php echo $text_color; ?>">
		</div>

	</fieldset>

	<button class="button button-primary submit-button indented" type="button">Save Settings</button>

</form>