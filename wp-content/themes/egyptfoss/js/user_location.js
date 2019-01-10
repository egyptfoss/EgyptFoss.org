var map, 
    markers = [];

function setMapMarkers() {
  for (var i = 0; i < markers.length; i++) {
    markers[i].setMap(map);
  }
}

function clearMapMarkers() {
  for (var i = 0; i < markers.length; i++) {
    markers[i].setMap(null);
  }
}

function addLocation() {
  lat = document.getElementById('lat').value;
  lng = document.getElementById('lng').value;
  if(lat == '') lat = 30.046;
  if(lng == '') lng = 31.225;
  var marker = new google.maps.Marker({
    position: new google.maps.LatLng(lat, lng),
    draggable: true
  });

  google.maps.event.addListener(marker, 'dragend', function (evt) {
    document.getElementById('lat').value = evt.latLng.lat();
    document.getElementById('lng').value = evt.latLng.lng();
  });

  map.setCenter(marker.position);
  marker.setMap(map);
  markers.push(marker);
}

function loadLocations(type, sub_type, theme, technology, interest, badge) {
  if(type == 'undefined') {
    type = 'Event';
  }
  var data = {
    action: 'ef_load_locations',
    type: type,
    sub_type: sub_type,
    theme: theme,
    technology: technology,
    interest: interest,
    badge: badge,
  };
  jQuery.ajax({
    type: 'POST',
    url: ajaxurl,
    data: data,
    success: function (data) {
      viewLocations(type, JSON.parse(data));
    }
  });
}

function viewLocations(type, locations) {
  var bounds = new google.maps.LatLngBounds();
  var infoWindow = new google.maps.InfoWindow(), marker, i;
  for (i in locations) {
    l = locations[i];
    position = new google.maps.LatLng(l.lat, l.lng);
    bounds.extend(position);
    marker = new google.maps.Marker({
      position: position,
      map: map,
      title: l.title
    });
    type = (type == '') ? 'Event' : type;
    if(type == 'Event') {
      google.maps.event.addListener(marker, 'click', (function(marker, i) {
        return function() {
          venue = locations[i];
          content = '<div>'+
                      '<h3 class="events-venue-title">'+venue.title+'</h3>'+
                      '<div class="events-slider owl-carousel">'+venue.events+'</div>'+
                    '</div>';
          infoWindow.setContent(content);
          infoWindow.open(map, marker);
        }
      })(marker, i));
      map.fitBounds(bounds);
      markers.push(marker);
    } else if(type == 'Individual' || type == 'Entity') {
      google.maps.event.addListener(marker, 'click', (function(marker, i) {
        return function() {
          account = locations[i];
          content = '<div>'+
                      '<h4>'+'<a href="'+account.about_link+'">'+account.display_name+'</a>'+'</h4>'+
                      '<div class="">'+account.email+'</div>'+
                    '</div>';
          infoWindow.setContent(content);
          infoWindow.open(map, marker);
        }
      })(marker, i));
      map.fitBounds(bounds);
      markers.push(marker);
    }
  }
  if(jQuery('body').hasClass('rtl')) {
    var slidedir = true;
  } else {
    var slidedir = false;
  }
  if(type == 'Event') {
    google.maps.event.addListener(infoWindow, 'domready', function(){
      jQuery(".events-slider").owlCarousel({
        nav: true, // Show next and prev buttons
        slideSpeed: 300,
        rtl: slidedir,
        lazyLoad : true,
  			smartSpeed:60,
				navContainerClass:'owl-buttons',
        items:1,
        nav: false,
        dots:true
      });
    });
  }
}

function initialize() {
  var mapOptions = {
    zoom: 6,
    center: new google.maps.LatLng(30.046, 31.225),
    mapTypeId: google.maps.MapTypeId.ROADMAP
  };
	map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);
  // Override our map zoom level once our fitBounds function runs (Make sure it only runs once)
  var boundsListener = google.maps.event.addListener((map), 'bounds_changed', function(event) {
    this.setZoom(6);
    google.maps.event.removeListener(boundsListener);
  });
  loadLocations('Event');
}
google.maps.event.addDomListener(window, "load", initialize);


jQuery(document).ready(function($) {

  function filterLocations() {
    var type = $(".location-type").val();
    var sub_type = $(".location-sub-type").val();
    var theme = $(".location-theme").val();
    var technology = $(".location-technology").val();
    var interest = $(".location-interest").val();
    var badge = $("#badges").val();
    loadLocations(type, sub_type, theme, technology, interest, badge);
  }

  function populateSubtypes(sub_types) {
    var emptyOp = $('.location-sub-type :first-child');
    $('.location-sub-type').find('option').remove();
    $('.location-sub-type').append(emptyOp);
    for (var key in sub_types) {
      $('.location-sub-type').append('<option value="'+key+'">'+sub_types[key]+'</option>');
    }
  }
  $(".location-type").val('Event');
  populateSubtypes(events_types);

  $("#add_location_link").on('click', function(){
    clearMapMarkers();
    addLocation();
    $("#add_location").show();
  });
  $("#cancel_add_location").on('click', function(){
    clearMapMarkers();
    loadLocations('Event');
    $("#add_location").hide();
  });
  $(".filter-location").on('change', function(){
    clearMapMarkers();
    filterLocations();
  });
  $(".custom-select2").each(function(){
    $(this).select2({
      placeholder: $.validator.messages[$(this).attr("data-taxonomy")],
      allowClear: true,
      language: {
        noResults: function() {
          return jQuery.validator.messages.select2_no_results;
        }
      }
    });
  });
  $(".location-type").on('change', function(){
    var type = $(this).val();
    if (type == 'Event') {
      sub_types = events_types;
      $("#badges-container").hide();
      $("#badges").val('');
    } else if (type == 'Individual') {
      sub_types = individuals_types;
      $("#badges-container").show();
    } else if (type == 'Entity') {
      sub_types = entities_types;
      $("#badges-container").show();
    }
    populateSubtypes(sub_types);
  });
});
