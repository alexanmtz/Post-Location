/**
 * @author Andrew Rickmann
 */
var map;
var geocoder;
function addAddressToMap(response){
    map.clearOverlays();
	console.info(reponse);
    if (!response || response.Status.code != 200) {
        alert("Sorry, we were unable to geocode that address");
    }
    else {
        place = response.Placemark[0];
        point = new GLatLng(place.Point.coordinates[1], place.Point.coordinates[0]);
        marker = new GMarker(point);
        map.addOverlay(marker);
        marker.openInfoWindowHtml(place.address + '<br>' +
        '<b>Country code:</b> ' +
        place.AddressDetails.Country.CountryNameCode);
    }
}

jQuery(document).ready( function($){
	map = new GMap2(document.getElementById("location-map"));
	map.setCenter(new GLatLng(37.4419, -122.1419), 13);
  	map.setUIToDefault();
	geocoder = new GClientGeocoder();

	$('#location-find').bind('click',function(){
		var address = $('#location_address').val();
		geocoder.getLocations(address, addAddressToMap);
		return false;
	});

});