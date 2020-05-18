		function b64DecodeUnicode(str) {
			return decodeURIComponent(Array.prototype.map.call(atob(str), function(c) {
				return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
			}).join(''));
		}

		// map
		function render_map($el) {

			var $markers = $el.find('.marker');
			var mapData = $el.data();

			var args = {
				zoom				: 14,
				center				: new google.maps.LatLng(0, 0),
				mapTypeId			: google.maps.MapTypeId.ROADMAP,
				scrollwheel			: true,
				navigationControl	: false,
				mapTypeControl		: false,
				scaleControl		: false,
				styles: [{
					"stylers":[{"hue":"#aabfd6"},{"saturation":40}]
				},{
					"featureType":"administrative.country",
					"elementType":"labels",
					"stylers":[{"visibility":"off"}]
	            },{
	                "featureType": "water",
	                "elementType": "geometry.fill",
	                "stylers": [{"visibility": "on"},{"color": "#10B9CA"}]
	            },{
	                "featureType": "poi",
	                "elementType": "labels.icon",
	                "stylers": [{"visibility": "on"}]
	            },{
	                "featureType": "road",
	                "elementType": "geometry",
	                "stylers": [{"color": "#ffffff"}]
	            },{
	                "featureType": "road.arterial",
	                "elementType": "geometry",
	                "stylers": [{"visibility": "on"},{"color": "#B5CCD6"}]
	            },{
	                "featureType": "road.arterial",
	                "elementType": "labels.text",
	                "stylers": [{"visibility": "simplified"},{"color": "#556D79"},{"hue": "#000000"}]
	            },{
	                "featureType": "poi",
	                "elementType": "geometry.fill",
	                "stylers": [{"color": "#D8E4E7"}]
	            }]
			};

			if( typeof mapData.scrollwheel !== 'undefined' && mapData.scrollwheel == false ) 
				args.scrollwheel = false;

			if( typeof mapData.mapStyle !== 'undefined' && mapData.mapStyle == false ) 
				args.styles = false;

			if( typeof mapData.mapStyle !== 'undefined' && mapData.mapStyle != '' )
				args.styles = b64DecodeUnicode(mapData.mapStyle);

			var map = new google.maps.Map( $el[0], args);

			map.markers = [];

			$markers.each(function(){
				add_marker( $(this), map );
			});

			center_map( map );

			// no IE8 center map é executado antes da finalização do Mapa, por isso o timeout.
			if( $('html').hasClass('ie8') ){
				setTimeout(function() {
					center_map( map );
				}, 2000);
			}

		}

		function add_marker( $marker, map ) {

			var latlng  = new google.maps.LatLng( $marker.attr('data-lat'), $marker.attr('data-lng') );
			var icon = {
				url: 	$marker.data('icon'),
				//size: 	new google.maps.Size(45, 61), // size width x height
				origin: new google.maps.Point(0, 0),
    			//anchor: new google.maps.Point(0, 50) // the anchor for this image is the base of the flagpole at (0, 32).
    		};

    		var marker = new google.maps.Marker({
    			position	: latlng,
    			map			: map,
    			icon		: icon
    		});

    		map.markers.push( marker );

			// if marker contains HTML, add it to an infoWindow
			if( $marker.html() ){
				// create info window
				var infowindow = new google.maps.InfoWindow({
					content		: $marker.html()
				});

				// show info window when marker is clicked
				google.maps.event.addListener(marker, 'click', function() {
					infowindow.open( map, marker );

					var dt = $marker.data();
					var ct;

					if( dt.address && dt.address != '' )
						ct = dt.address;
					else
						ct = $marker.text();

				});
			}

		}

		function center_map( map ) {

			var bounds = new google.maps.LatLngBounds();

			// loop through all markers and create bounds
			$.each( map.markers, function( i, marker ){
				var latlng = new google.maps.LatLng( marker.position.lat(), marker.position.lng() );
				bounds.extend( latlng );
			});

			// only 1 marker?
			if( map.markers.length == 1 ){
				// set center of map
				map.setCenter( bounds.getCenter() );
				map.setZoom( 18 );
			} else {
				// fit to bounds
				map.fitBounds( bounds );
			}

		}

		( function( $ ) {
			$(function() {

		// create map
		var $map = $('.gmap');
		$map.each(function(){
			render_map( $(this) );
		});
		
	});
		} )( jQuery );