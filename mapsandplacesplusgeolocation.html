<!DOCTYPE HTML>
<html>
<head>
<title>Basic GeoLocation Map</title>
    <!-- Google Maps and Places API -->
	<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?libraries=places&sensor=false"></script>
    
    <!-- jQuery -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
	<script type="text/javascript">
    function initGeolocation()
    {
            if( navigator.geolocation )
            {

              // Call getCurrentPosition with success and failure callbacks
              navigator.geolocation.getCurrentPosition( success, fail );
        }
        else
        {
              alert("Sorry, your browser does not support geolocation services.");
        }
    }

     var map;
     function success(position)
     {
           // Define the coordinates as a Google Maps LatLng Object
           var coords = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);

           // Prepare the map options
           var mapOptions =
          {
                      zoom: 14,
                      center: coords,
                      mapTypeControl: false,
                      navigationControlOptions: {style: google.maps.NavigationControlStyle.SMALL},
                      mapTypeId: google.maps.MapTypeId.ROADMAP
            };

            // Create the map, and place it in the map_canvas div
            map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
			
			//search for schools within 1500 metres of our current location, and as a marker use school.png
			placesRequest('Schools',coords,1500,['school'],'images/school.png');


            // Place the initial marker
            var marker = new google.maps.Marker({
                      position: coords,
                      map: map,
                      title: "Your current location!"
            });
			
        }

        function fail()
        {
              // Could not obtain location
        }
		
		//Request places from Google
		function placesRequest(title,latlng,radius,types,icon)
		{
			//Parameters for our places request
			var request = {
				location: latlng,
				radius: radius,
				types: types
			};
			//Make the service call to google
			var callPlaces = new google.maps.places.PlacesService(map);
			callPlaces.search(request, function(results,status){
				//trace what Google gives us back
				$.each(results, function(i,place){
					var placeLoc = place.geometry.location;
					 var thisplace = new google.maps.Marker({
						 map: map,
						 position: place.geometry.location,
						 icon: icon,
						 title: place.name
					 });
				})
			});
			
		}
		
	
        </script>
</head>
<body onload="initGeolocation();">
    <div style="position:absolute; width:380px; height: 100%; overflow:auto; float:left; padding-left:10px; padding-right:10px;">
        <h1>Google Places API</h1>
        <p>
		We're using both google places (to search for things in a certain category) AND google maps (to put the things on a map)~!	PLUS we're using geolocatoin to automatically centre the map on the user's current location!
		</p>
    </div>
    <!-- map div container -->

<div id="map_canvas" style="height:500px; margin-left:400px; border-right: 1px solid #666666; border-bottom: 1px solid #666666; border-top: 1px solid #AAAAAA; border-left: 1px solid #AAAAAA;"></div>
</body>
</html>
