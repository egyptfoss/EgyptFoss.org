jQuery(document).ready(function($) {
  var map_loaded = false;
  
  function addLocation(map) {
    lat = document.getElementById('lat').value;
    lng = document.getElementById('lng').value;
    if(lat == '') lat = 30.046;
    if(lng == '') lng = 31.225;
    var event_marker = new google.maps.Marker({
      position: new google.maps.LatLng(lat, lng),
      draggable: true
    });
    google.maps.event.addListener(event_marker, 'dragend', function (evt) {
      document.getElementById('lat').value = evt.latLng.lat();
      document.getElementById('lng').value = evt.latLng.lng();
    });
    map.setCenter(event_marker.position);
    event_marker.setMap(map);
  }

  function displayMap() {
    var mapOptions = {
      zoom: 6,
      center: new google.maps.LatLng(30.046, 31.225),
      mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);
    addLocation(map);
  }

  function toggleForm(formId) {
    if($("#new-"+formId).is(':visible')) {
      $("#add-"+formId).show();
      $("#cancel-"+formId).hide();
    } else {
      $("#add-"+formId).hide();
      $("#cancel-"+formId).show();
      if(!map_loaded && formId=="venue"){
        displayMap();
        map_loaded = true;
      }
    }
  }

  $("#new-venue-link").on('click', function(){
    toggleForm('venue');
  });

  $("#new-organizer-link").on('click', function(){
    toggleForm('organizer');
  });

});