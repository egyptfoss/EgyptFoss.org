/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var map, 
    markers = [];

function addLocation() {
  lat = document.getElementById('lat').value;
  lng = document.getElementById('lng').value;
  if(lat == '') lat = 30.046;
  if(lng == '') lng = 31.225;
  var marker = new google.maps.Marker({
    position: new google.maps.LatLng(lat, lng),
    draggable: false
  });

  google.maps.event.addListener(marker, 'dragend', function (evt) {
    document.getElementById('lat').value = evt.latLng.lat();
    document.getElementById('lng').value = evt.latLng.lng();
  });

  map.setCenter(marker.position);
  marker.setMap(map);
  markers.push(marker);
}

function initialize() {
   
  var mapOptions = {
    zoom: 8,
    center: new google.maps.LatLng(30.046, 31.225),
    mapTypeId: google.maps.MapTypeId.ROADMAP
  };
  map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);
    
  addLocation();
}
google.maps.event.addDomListener(window, "load", initialize);

