
<?php

// Database Connection
$host = "localhost";
$uname = "root";
$pass = "";
$database = "phpmyadmin";

$connection = mysql_connect ( $host, $uname, $pass ) or die ( "Database Connection Failed" );

$selectdb = mysql_select_db ( $database ) or die ( "Database could not be selected" );
$result = mysql_select_db ( $database ) or die ( "database cannot be selected <br>" );

$table = "markers";


if (isset ( $_GET ["beginLat"] ) && isset ( $_GET ["beginLng"] ) && isset ( $_GET ["endLat"] ) && isset ( $_GET ["endLng"] )) {
	
	$beginLat = $_GET ["beginLat"];
	$endLat = $_GET ["endLat"];
	$beginLng = $_GET ["beginLng"];
	$endLng = $_GET ["endLng"];
	
	$row = null;
	$result = null;
	
	if ($beginLat == 'null') {
		$result = mysql_query ( "SELECT * FROM markers" );
	} else {
		$result = mysql_query ( "SELECT * FROM markers WHERE lat>='" . $beginLat . "' AND lat<='" . $endLat . "' AND lng>='" . $beginLng . "' AND lng<='" . $endLng . "'" );
	}
	
	$row = @mysql_fetch_assoc ( $result );
	
	$latitude = $row ['lat'];
	$longitude = $row ['lng'];
	$description = $row ['address'] . '<h1>Bar</h1> <img src="' . $row ['imageLink'] . '" alt="branch office picture"> <br> <a class="directions" href="https://www.google.com/maps/dir/_lati__long_/' . $row ['lat'] . ',' . $row ['lng'] . '"><img src="https://lh6.googleusercontent.com/-uuy_Ayk3IO4/U9Y_2fK6pPI/AAAAAAAAADY/zres-mhI4PM/s20-no/direction_icon.png" alt="Navigate here"></a>';
	
	$output = $latitude . ";" . $longitude . ";" . $description;
	
	echo $description;
	
	while ( $row = @mysql_fetch_assoc ( $result ) ) {
		
		$latitude = $row ['lat'];
		$longitude = $row ['lng'];
		$description = $row ['address'] . '<h1>Bar</h1> <img src="' . $row ['imageLink'] . '" alt="branch office picture"> <br> <a class="directions" href="https://www.google.com/maps/dir/_lati__long_/' . $row ['lat'] . ',' . $row ['lng'] . '"><img src="https://lh6.googleusercontent.com/-uuy_Ayk3IO4/U9Y_2fK6pPI/AAAAAAAAADY/zres-mhI4PM/s20-no/direction_icon.png" alt="Navigate here"></a>';
		
		$output = $output . "{placemark}" . $latitude . ";" . $longitude . ";" . $description;
		
		if ($row ['type'] == 'bars') {
			$output = $output . ";https://maps.gstatic.com/mapfiles/ms2/micons/bar.png";
		}
		if ($row ['type'] == 'dining') {
			$output = $output . ";https://maps.gstatic.com/mapfiles/ms2/micons/restaurant.png";
		}
	}
	
	echo $output;
	return;
}

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport"></meta>
<title>Goomaxx Maps</title>
<style type="text/css">
html,body,#googft-mapCanvas {
	height: 100%;
	margin: 0;
	padding: 0;
	width: 100%;
}
</style>

<script type="text/javascript"
	src="https://maps.google.com/maps/api/js?sensor=false&key=AIzaSyAk9XHSmk_a3AJ5qf4ryv4uFJn3iBoeIpI&libraries=visualization,geometry&amp;v=3"></script>

<script src="jquery.js"></script>

<script type="text/javascript">
	var map;

	var myLocation = null;
	var myLocationMarker = null;
	var previousLocation = null;
	var watchId = null;
	var map_id;

	var lastError = null;

	var displayed_placemarks = new Array();

	function initialize() {
		
		var isMobile = (navigator.userAgent.toLowerCase().indexOf('android') > -1) ||
	      (navigator.userAgent.match(/(iPod|iPhone|iPad|BlackBerry|Windows Phone|iemobile)/));
	    if (isMobile) {
	      var viewport = document.querySelector("meta[name=viewport]");
	      viewport.setAttribute('content', 'initial-scale=1.0, user-scalable=no');
	    }

		var mapDiv = document.getElementById('googft-mapCanvas');

		map = new google.maps.Map(mapDiv, {
			center : new google.maps.LatLng(47,
					17),
			zoom : 5,
			mapTypeId : google.maps.MapTypeId.ROADMAP
		});
		
		
		localizeMap();
		
		initializeGetDirections();

		getLocation();

	}

	function getLocation() {

		startWatch();

	}

	function startWatch() {
		if (navigator.geolocation) {
			var optn = {
				enableHighAccuracy : true,
				timeout : 10000,
				maximumAge : 3000
			};
			watchId = navigator.geolocation.watchPosition(showPosition,
					showError, optn);
		} else {
			queryPointsWithinDistance(null, null, 50);
			if(lastError != 1){
				alert('Geolocation is not supported in your browser');	
			}
			lastError = 1;
		}
	}
	function stopWatch() {
		if (watchId) {
			navigator.geolocation.clearWatch(watchId);
			watchId = null;
		}
	}

	function showPosition(position) {
		
		lastError = null;
		
		myLocation = new google.maps.LatLng(position.coords.latitude,
				position.coords.longitude);
		

		var latitude = position.coords.latitude;
		var longitude = position.coords.longitude;

		if (myLocationMarker == null) {
			map.setCenter(myLocation);
			map.setZoom(17);
		}
		
		

		hideLocationMarker();
		
		displayMyLocationMarker();
		
		console.log('my location '+myLocation);

		queryPointsWithinDistance(latitude, longitude, 50);

	}
	
	function displayMyLocationMarker() {
		
		myLocationMarker = new google.maps.Marker({
			position : myLocation,
			map : map,
			icon : 'mylocation_icon.png'
		});
		
	}
	
	function hideLocationMarker() {
		if (myLocationMarker != null) {
			myLocationMarker.setMap(null);
		}
	}
	
	function displayPlacemark(placemark) {

		var marker = placemark.marker;
		var infowindow = placemark.infowindow;

		marker.setMap(map);

		google.maps.event.addListener(marker, 'click', function() {
		    infowindow.open(map, marker);
		});

	}

	function hideAllPlacemarks() {
		for(var i = 0; i < displayed_placemarks.length; i++){
			displayed_placemarks[i].marker.setMap(null);
		}
		displayed_placemarks = new Array();
	}
	
	function calcDistance(p1, p2) {//in km
		  return (google.maps.geometry.spherical.computeDistanceBetween(p1, p2) / 1000).toFixed(2);
	}
	
	
	function queryPointsWithinDistance(myLat, myLng, radius) {

		if(myLat == null){
			callPage('map_phpmysql.php?beginLat=null&beginLng=null&endLat=null&endLng=null');
			return;
		}
		
		if(myLat != null) {

			var location = new google.maps.LatLng(myLat, myLng);
			
			if(previousLocation != null) {
				console.log(calcDistance(previousLocation, location) + ' <=> ' + radius/5 );
				
				if(calcDistance(previousLocation, location) < radius/5) {
					return;
				}
			}
			
			previousLocation = location;
		}
		
		var circuit = 6371 * 2 * Math.PI;
		
		var geoToKm = circuit / 360;
		
		var dx_y = radius/geoToKm;
		
		var beginLat = myLat - dx_y;
		var endLat = myLat + dx_y;
		
		var beginLng = myLng - dx_y;
		var endLng = myLng + dx_y;

		console.log('top left [ ' + beginLat + ', ' + beginLng + ' ]');
		console.log('bottom right [ ' + endLat + ', ' + endLng + ' ]');

		hideAllPlacemarks();		

		callPage('map_phpmysql.php?beginLat='+beginLat+'&beginLng='+beginLng+'&endLat='+endLat+'&endLng='+endLng);

	}
	
	function initializeGetDirections() {
	
		var timer = setInterval(function(){
			$( ".directions" ).click(function() {
				
				var lat = '';
				var lng = '';
				if(myLocation != null) {
					lat = myLocation.lat() + ',';
					lng = myLocation.lng();
				}
				
				var href = $(this).attr('href').replace('_lati_', lat).replace('_long_', lng);
				$(this).attr('href', href);
			});
			
		}, 300);
		
		
		
// 		var timer = setInterval(function(){
			
// 			jQuery('a[href*="_lati_"]').each(function(){
// 				if (this._repl) return false;
// 				var href = $(this).attr('href').replace('_lati_', lat).replace('_long_', lng);
// 				$(this).attr('href', href);
// 				this._repl = true;
// 			});
// 		}, 300);

	}

	function showError(error) {
		
		switch (error.code) {
		case error.PERMISSION_DENIED:
			if(lastError != 2) {
				alert("User denied the request for Geolocation.");	
			}
			lastError = 2;
			break;
		case error.POSITION_UNAVAILABLE:
			if(lastError != 3) {
				alert("Location information is unavailable.");	
			}
			lastError = 3;
			break;
		case error.TIMEOUT:
			if(lastError != 4) {
				alert("The request to get user location timed out.");	
			}
			lastError = 4;
			break;
		case error.UNKNOWN_ERROR:
			if(lastError != 5) {
				alert("An unknown error occurred.");	
			}
			lastError = 5;
			break;
		}
		
	}
	
function localizeMap(){
		
		
		var userLang = navigator.language || navigator.userLanguage;
		
		var shortUserLang = userLang.substring(0,2); 
		
		
		if(userLang.localeCompare('en-GB') == 0){
			<!-- english --> 
			map_id = '1FDx5lyOpGDMNub8oR-KTbMxLF2YZya-VJZHFFAx3';
		}
		
		else if(userLang.localeCompare('de-DE') == 0){
			<!-- german -->
			map_id = '1GV8RWES7Fr9VeHuYLI5_L3gxhWcpwCkqOnHfK-Mh';
		}

		else if(userLang.localeCompare('es-ES') == 0){
			<!-- spanish -->
			map_id = '1D7GeeGvXjJIiTpbTOenudEzDXmIUUYBRCfA3d_m8';

		}
		else if(shortUserLang.localeCompare('en') == 0){
			<!-- english -->
			map_id = '1FDx5lyOpGDMNub8oR-KTbMxLF2YZya-VJZHFFAx3';
		}
		else if(shortUserLang.localeCompare('de') == 0){
			<!-- german -->
			map_id = '1GV8RWES7Fr9VeHuYLI5_L3gxhWcpwCkqOnHfK-Mh';
		}
		else if(shortUserLang.localeCompare('es') == 0){
			<!-- spanish -->
			map_id = '1D7GeeGvXjJIiTpbTOenudEzDXmIUUYBRCfA3d_m8';
		}
		else{
			<!-- default language = english-->
			map_id = '1FDx5lyOpGDMNub8oR-KTbMxLF2YZya-VJZHFFAx3';
		}
		
		
	}



function AjaxCaller(){
    var xmlhttp=false;
    try{
        xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
    }catch(e){
        try{
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }catch(E){
            xmlhttp = false;
        }
    }

    if(!xmlhttp && typeof XMLHttpRequest!='undefined'){
        xmlhttp = new XMLHttpRequest();
    }
    return xmlhttp;
}

function callPage(url) {
    ajax=AjaxCaller(); 
    ajax.open("GET", url, true); 
    ajax.onreadystatechange=function(){
        if(ajax.readyState==4){
            if(ajax.status==200) {

                var response = ajax.responseText;
                console.log(response);

                var placemarks = response.split("{placemark}");

                for(var i = 0; i < placemarks.length; i++) {
                    
                	var elems = placemarks[i].split(";");

                    marker_lat = elems[0];
                    marker_lng = elems[1];
				
                    var pm_location = new google.maps.LatLng(marker_lat, marker_lng);

                    var infowindow = new google.maps.InfoWindow({
          		      content: elems[2]
          		  	});

	          		  var marker = new google.maps.Marker({
	          		      position: pm_location,
	          		      icon: elems[3],
	          		      title: 'Meno'
	          		  });
	          		  
	          		var placemark = {marker: marker, infowindow: infowindow};
	          		displayed_placemarks.push(placemark);

	          		displayPlacemark(placemark);
                }

                
            }
        }
    }
    ajax.send(null);
}
	

	google.maps.event.addDomListener(window, 'load', initialize);
</script>
</head>

<body>
	<div id="googft-mapCanvas"></div>
</body>
</html>
