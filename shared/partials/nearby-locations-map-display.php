<?php

/**
 * @link       http://www.aaronjfrey.com/
 * @since      1.0.0
 *
 * @package    Nearby_Locations
 * @subpackage Nearby_Locations/shared/partials
 */

// Get all of the location types
global $wpdb;
$table_name = $wpdb->prefix . 'ajf_nl_sections'; 
$join_table_name = $wpdb->prefix . 'ajf_nl_locations'; 
$locations = $wpdb->get_results("
  SELECT `locations`.*, `sections`.name `section_name`
  FROM $join_table_name `locations`
  LEFT JOIN $table_name `sections` ON `sections`.id = `locations`.section_id
  ORDER BY `sections`.`order` ASC, `locations`.name
", OBJECT);

if (get_option('ajf-nl-google-api-key')) :
  $background_color = get_option('ajf-nl-color-background');
  $panel_color = get_option('ajf-nl-color-panel');
  $text_color = get_option('ajf-nl-color-text');
?>

<style type="text/css">
  .ui-widget-content a {
    color: <?php echo $text_color; ?>;
  }
  .ui-accordion .ui-accordion-content {
    background-color: <?php echo $panel_color; ?>;
  }
  .ui-accordion .ui-accordion-content ul {
    color: <?php echo $text_color; ?>;
  }
</style>

<div class="pl-nearby-locations-container" style="background-color:<?php echo $background_color; ?>;">

  <div class="accordion-container">

    <a href="#" id="toggle-all" class="toggle-all" style="color: <?php echo $text_color; ?>;">ALL</a>

    <?php if ($locations) : ?>

    <div class="accordion">

      <?php

      $current_location_type = null;

      foreach ($locations as $idx => $location) :

        if ($location->section_id !== "-99") :

          if ($location->section_id !== $current_location_type) :

            if ($current_location_type) : ?>
              </ul></div>
            <?php endif;

            $current_location_type = $location->section_id; ?>

            <h3 data-section-id="<?php echo esc_attr($location->section_id); ?>"
              style="background: <?php echo $background_color; ?>; color: <?php echo $text_color; ?>">
              <?php echo esc_html($location->section_name); ?>
            </h3>
            <div>
              <ul>

          <?php endif; ?>

          <li>
            <a href="#" class="location-link" data-location-index="<?php echo esc_attr($idx); ?>">
              <?php echo esc_html($location->name); ?>
            </a>
          </li>

        <?php endif;

      endforeach;

      if ($current_location_type) : ?>
      </ul></div>
      <?php endif; ?>

    </div><!-- .accordion -->

    <?php endif; ?>

  </div><!-- .accordion-container -->

  <div id="map-canvas" class="map"></div>

</div><!-- .pl-nearby-locations-container -->

<?php endif; ?>