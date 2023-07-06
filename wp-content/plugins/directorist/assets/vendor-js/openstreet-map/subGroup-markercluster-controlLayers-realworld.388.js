(function ($) {
    $(document).ready( function() {
        var mapOptions  = JSON.parse( $('#map').attr('data-options') ),
            mapListings = JSON.parse( $('#map').attr('data-card') ),
            queryLatLng = '', queryLat = '', queryLng = '',
            manualLat = '', manualLng = '',
            latlng = '', getLat = '', getLng = '',
            queryLatLng = getOpenStreetLatLng(),
            searchLocation = $('#address').val(),
            searchResult = '',
            customZoomLevel = '';

            if(searchLocation != '') {
                var uri = window.location.toString();
                if (uri.indexOf("?") > 0) {
                    var clean_uri = uri.substring(0, uri.indexOf("?"));
                    window.history.replaceState({}, document.title, clean_uri);
                }
                
                searchResult = direoAjaxSearchLocationMap(searchLocation);

                if(searchResult != false) {                
                    getLat = searchResult[1];
                    getLng = searchResult[2];
                }
            }

        if( ( getLat != '' || getLat != undefined ) && ( getLng != '' && getLng != undefined ) ) {
            manualLat = getLat;
            manualLng = getLng;
        } else {        
            manualLat = queryLatLng.manual_lat;
            manualLng = queryLatLng.manual_lng;
        }

        if( ( manualLat != '' || manualLat != undefined ) && ( manualLng != '' && manualLng != undefined ) ) {
            queryLat = manualLat;
            queryLng = manualLng;
        }  else {
            queryLat = '';
            queryLng = '';
        }
    
        const tiles = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '© <a href="http://osm.org/copyright">OpenStreetMap</a> contributors, Points &copy 2012 LINZ',
        });
        
        if(queryLat != '' && queryLng != '') {
            latlng = L.latLng(queryLat, queryLng);
            customZoomLevel = '13';
        } else {
            let defCordEnabled = mapOptions.use_def_lat_long;
            latlng = defCordEnabled ? L.latLng(mapOptions.default_latitude, mapOptions.default_longitude) : L.latLng(mapOptions.base_latitude, mapOptions.base_longitude);
            customZoomLevel = mapOptions.zoom_level;
        }

        const fullCount = mapListings.length;
        const quarterCount = Math.round(fullCount / 4);
    
        try {
            const map = L.map('map', {
                center: latlng,
                zoom: customZoomLevel,
                scrollWheelZoom: true,
                layers: [tiles],
            });

            // map.once('focus', function() { map.scrollWheelZoom.enable(); });
            const mcg = L.markerClusterGroup();
            const group1 = L.featureGroup.subGroup(mcg);
            const // use `L.featureGroup.subGroup(parentGroup)` instead of `L.featureGroup()` or `L.layerGroup()`!
                group2 = L.featureGroup.subGroup(mcg);
            const group3 = L.featureGroup.subGroup(mcg);
            const group4 = L.featureGroup.subGroup(mcg);
            const control = L.control.layers(null, null, {
                collapsed: false
            });
            let i;
            let a;
            let title;
            let marker;
            mcg.addTo(map);
        
            for (i = 0; i < mapListings.length; i++) {
                const listing = mapListings[i];
                const fontAwesomeIcon = L.divIcon({
                    html: `<div class="atbd_map_shape"><span class="${listing.cat_icon}"></span></div>`,
                    iconSize: [20, 20],
                    className: 'myDivIcon',
                });
        
                title = listing.content;
                marker = L.marker([listing.latitude, listing.longitude], {
                    icon: fontAwesomeIcon
                });
                marker.bindPopup(title);
        
                marker.addTo(
                    i < quarterCount ?
                    group1 :
                    i < quarterCount * 2 ?
                    group2 :
                    i < quarterCount * 3 ?
                    group3 :
                    group4
                );
            }
        
            /* control.addOverlay(group1, 'First quarter');
            control.addOverlay(group2, 'Second quarter');
            control.addOverlay(group3, 'Third quarter');
            control.addOverlay(group4, 'Fourth quarter'); */
            control.addTo(map);
        
            group1.addTo(map); // Adding to map now adds all child layers into the parent group.
            group2.addTo(map);
            group3.addTo(map);
            group4.addTo(map);
        } catch ( _ ) {}
        
        function getOpenStreetLatLng() {
            var vars = [], hash;
            var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
            for(var i = 0; i < hashes.length; i++) {
                hash = hashes[i].split('=');
                vars.push(hash[0]);
                if(hash[1] != '') {
                    vars[hash[0]] = hash[1];
                }
            }
            return vars;
        }

        function direoAjaxSearchLocationMap(location) {
            var displayName = '', lat = '', lon = '', responseArr = [];
            $.ajax({
                type: 'GET',
                dataType: 'json',
                url: 'https://nominatim.openstreetmap.org/',
                async: false,
                data: {
                    'q': location,
                    'format': 'json'
                },
                success: function(response) {
                    if(response != '') {
                        displayName = response[0].display_name;
                        lat = response[0].lat;
                        lon = response[0].lon;

                        if(displayName != '' && lat != '' && lon != '') {
                            responseArr.push(displayName);
                            responseArr.push(lat);
                            responseArr.push(lon);
                        } else {
                            responseArr = false;
                        }
                    } else {
                        responseArr = false;
                    }
                },
                error: function(jqXHR, textStatus, errorThrown){
                    alert("There was an error. We were unable to fetch the location you requested.");
                    console.log(jqXHR + " :: " + textStatus + " :: " + errorThrown);
                }
            });

            return responseArr;
        }
    });
})(jQuery);