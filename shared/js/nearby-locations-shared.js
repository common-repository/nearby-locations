(function($) {

  'use strict';

  var activeMarker = null,
    bounds,
    centerLocation,
    geocoder,
    infowindow,
    locations,
    map,
    markerGroups = {},
    markers = [],
    sectionID,
    tempMarkers = [],
    toggleAll = true;

  // return an array key based on value
  var arraySearch = function(array, value) {
    for (var i = 0; i < array.length; i++) {
      if (array[i] === value) return i;
    }
    return false;
  }

  // get locations from db. loop through & populate map with location markers
  var fetchPlaces = function() {

    jQuery.ajax({
      url: myVars.ajaxUrl,
      dataType: 'json',
      type: 'post',
      data: {
        'action': 'nearby_locations_crud',
        'callback': 'read_locations'
      },
      cache: false,
      success: function(response) {

        centerLocation = response.center;
        locations = response.locations;

        // clear markerGroups for a fresh map
        markerGroups = {};

        // create a bounds to contain all markers on the map
        bounds = new google.maps.LatLngBounds();

        // loop through locations and add markers to map
        for (var l in locations) {
          addMarkerToScreen(locations[l].lat, locations[l].lng, '<b>' + locations[l].name + "</b><br>" + locations[l].formatted, locations[l].section_id);
        }
        addCenterMarker();
      }
    })
  };

  var addMarkerToScreen = function(lat, lng, title, section, icon) {
    // make and place map maker
    var marker = new google.maps.Marker({
      map: map,
      position: new google.maps.LatLng(lat, lng),
      icon: icon ? icon : '',
    });

    if (section) {
      if (!(section in markerGroups)) {
        markerGroups[section] = [];
      }
      markerGroups[section].push(marker);
    }

    markers.push(marker);

    // add marker to the contained bounds
    bounds.extend(marker.getPosition());
    map.fitBounds(bounds);

    // bind click event to show the info box
    bindInfoWindow(marker, map, infowindow, title);
  }

  var addCenterMarker = function() {
    if (centerLocation) {
      addMarkerToScreen(centerLocation.coords.lat, centerLocation.coords.lng, '<b>' + centerLocation.address + '</b>', null, {
        url: myVars.pluginsUrl + '/nearby-locations/shared/img/center-marker.svg'
      });
    }
  }

  // binds a map marker and infoWindow together on click
  var bindInfoWindow = function(marker, map, infowindow, html) {
    google.maps.event.addListener(marker, 'click', function() {
      // show the info window
      infowindow.setContent(html);
      infowindow.open(map, marker);
    });
  }

  // hides the markers from the map, but keeps them in the array.
  var hideMarkers = function() {
    for (var i = 0; i < markers.length; i++) {
      markers[i].setVisible(false);
    }
  }

  // showd the markers from the map
  var showMarkers = function() {
    // show a sunset of markers or all markers?
    tempMarkers = sectionID ? markerGroups[sectionID] : markers;
    // create a new bounds
    bounds = new google.maps.LatLngBounds();

    // add selected markers to the contained bounds
    for (var i = 0; i < tempMarkers.length; i++) {
      bounds.extend(tempMarkers[i].getPosition());
      tempMarkers[i].setVisible(true);
    }

    addCenterMarker();

    map.fitBounds(bounds);
  }

  function initialize() {

    // create the geocoder
    geocoder = new google.maps.Geocoder();

    // create the infowindow
    infowindow = new google.maps.InfoWindow({
      content: ''
    });

    if ($('.pl-nearby-locations-container').length) {

      // set some default map details, initial center point, zoom and style
      var mapOptions = {
        scrollwheel: false,
        center: new google.maps.LatLng(39.9523789, -75.1657883),
        zoom: 12,
        mapTypeId: google.maps.MapTypeId.ROADMAP
      };

      // create the map and reference the div#map-canvas container
      map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);

      // fetch the existing places (ajax) and put them on the map
      fetchPlaces();
    }
  }

  // functions run after page content is loaded
  $(function() {

    // if google maps is loaded, initialize the map.
    if (typeof google === 'object' && typeof google.maps === 'object') {
      initialize();
    } else {
      if (!$('body').hasClass('locations_page_nearby-locations-settings')) {
        $('#ajf-nearby-locations-message').html('Please enter a valid Google Maps API key in <a href="?page=nearby-locations-settings" class="button button-primary">Settings</a>');
      } else {
        $('#ajf-nearby-locations-message').html('Please enter a valid Google Maps API key.');
      }
    }

    // validate the location form
    $('.submit-button').on('click', function() {
      // get the form
      var form = $(this).parents('form');

      if (form && $(form).valid()) {
        // if form is valid, submit it
        $(form).submit();
      }
    });

    // add settimgs for validation rules
    $('#settings-form').validate({
      rules: {
        'api-key': 'required',
      }
    });

    // process and submit the settings page form
    $('#settings-form').submit(function(e) {

      e.preventDefault();

      // get the center location
      var centerAddress = $('#center-address').val();

      if (centerAddress) {
        // format the address for saving
        geocoder.geocode({ 'address': centerAddress }, function(results, status) {
          if (status == google.maps.GeocoderStatus.OK) {
            centerAddress = {
              'coords': {
                'lat': results[0].geometry.location.lat(),
                'lng': results[0].geometry.location.lng(),
              },
              'address': results[0].formatted_address,
            };
            submitSettingsForm();
          } else {
            $('#ajf-nearby-locations-message').html('Try again. Geocode was not successful for the following reason: ' + status);
          }
        });
      } else {
        submitSettingsForm();
      }

      function submitSettingsForm() {
        // preparing data for form posting
        var data = {
          'action': 'nearby_locations_crud',
          'callback': 'update_settings',
          'api-key': $('#api-key').val(),
          'center-address': centerAddress,
          'colors': {
            'background': $('#sidebar-background-color').val(),
            'panel': $('#sidebar-panel-color').val(),
            'text': $('#sidebar-text-color').val()
          }
        };

        // save the location type to the database
        $.ajax({
          url: myVars.ajaxUrl,
          type: 'post',
          data: data,
          cache: false,
          success: function(response) {
            $('#ajf-nearby-locations-message').html('Settings saved.');
            location.reload();
          },
          error: function(response) {
            $('#ajf-nearby-locations-message').html('Try again. Settings were not saved.');
          }
        });
      }
    });

    // add validation rules for locatio type form
    $('#location-type-form').validate({
      rules: {
        'type-name': 'required',
        'type-order': {
          required: true,
          digits: true,
        }
      }
    });

    // process and submit the location type page form
    $('#location-type-form').submit(function(e) {

      e.preventDefault();

      // preparing data for form posting
      var data = {
        'action': 'nearby_locations_crud',
        'callback': 'add_new_type',
        'section_id': $('#type-id').val(),
        'name': $('#type-name').val(),
        'order': $('#type-order').val(),
      };

      // save the location type to the database
      $.ajax({
        url: myVars.ajaxUrl,
        type: 'post',
        data: data,
        cache: false,
        success: function(response) {

          var queryParameters = {},
            queryString = location.search.substring(1),
            re = /([^&=]+)=([^&]*)/g,
            m;

          // Creates a map with the query string parameters
          while (m = re.exec(queryString)) {
            queryParameters[decodeURIComponent(m[1])] = decodeURIComponent(m[2]);
          }

          if (data.id !== '') {
            // Add new parameters or update existing ones
            queryParameters['action'] = '';
            queryParameters['location_type'] = '';
          }

          // Causes page to reload
          location.search = $.param(queryParameters);
        },
        error: function(response) {
          $('#ajf-nearby-locations-message').html('Try again. Saving location was not successful.');
        }
      });
    });

    $('#location-form').validate({
      rules: {
        name: 'required',
        address: 'required',
        type: 'required'
      }
    });

    $('#location-form').submit(function(e) {

      e.preventDefault();

      geocoder.geocode({ 'address': $('#address').val() }, function(results, status) {

        if (status == google.maps.GeocoderStatus.OK) {

          // save the location to the database
          $.ajax({
            url: myVars.ajaxUrl,
            type: 'post',
            data: {
              'action': 'nearby_locations_crud',
              'callback': 'add_new_location',
              'section_id': $('#type option:selected').val(),
              'lat': results[0].geometry.location.lat(),
              'lng': results[0].geometry.location.lng(),
              'location_name': $('#name').val(),
              'formatted_name': results[0].formatted_address,
            },
            cache: false,
            success: function(response) {
              location.reload();
            },
            error: function(response) {
              $('#ajf-nearby-locations-message').html('Try again. Saving location was not successful.');
            }
          });
        } else {
          $('#ajf-nearby-locations-message').html('Try again. Geocode was not successful for the following reason: ' + status);
        }
      });
    });

    // show all of the markers at one time
    $('#toggle-all').on('click', function(e) {
      e.preventDefault();
      toggleAll = true;
      $('.ui-accordion-header-active').click();
      showMarkers();
    });

    // initialize the location types accordion 
    $('.accordion').accordion({
      active: false,
      collapsible: true,
      heightStyle: 'content',
      icons: {
        "header": "ui-icon-triangle-1-s",
        "activeHeader": "ui-icon-triangle-1-n"
      },
      beforeActivate: function(event, ui) {
        // prevent current opened accordion from closing on click unless 'all' is clicked
        if (!toggleAll && !ui.newHeader.size()) {
          return false;
        }
        toggleAll = false;

        // clear all markers
        hideMarkers();
        infowindow.close();

        // return only the locations for the current section id
        sectionID = ui.newHeader.data('section-id');
        showMarkers(sectionID);
      }
    });

    // show info box when location is clicked in accordion
    $('.location-link').on('click', function(e) {
      e.preventDefault();
      var index = $(this).data('location-index');
      google.maps.event.trigger(markers[index], 'click');
    });
  });

})(jQuery);