(function($) {

  'use strict';

  $(function() {

    // Add Color Picker to all inputs that have 'color-field' class
    $('.color-field').wpColorPicker();

    // Delete center address from the database when remove is clicked
    $('#remove-location').on('click', function(e) {

      e.preventDefault();

      // preparing data for form posting
      var data = {
        'action': 'nearby_locations_crud',
        'callback': 'remove_center_location',
      };

      // save the location type to the database
      $.ajax({
        url: myVars.ajaxUrl,
        type: 'post',
        data: data,
        cache: false,
        success: function(response) {
          $('#ajf-nearby-locations-message').html('Featured Location removed.');
          location.reload();
        },
        error: function(response) {
          $('#ajf-nearby-locations-message').html('Try again. The Featured Location was not removed.');
        }
      });
    });
  });
})(jQuery);