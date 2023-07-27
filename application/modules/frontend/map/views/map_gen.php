<script src="<?php echo theme_assets_url(); ?>vendors/bower_components/jquery/dist/jquery.min.js"></script>
<link rel="stylesheet" href="<?php echo theme_assets_url(); ?>js/leaflet-1.3.1/dist/leaflet.css"  crossorigin="" />
    <link rel="stylesheet" href="<?php echo theme_assets_url(); ?>js/leaflet.markercluster-1.3.0/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="<?php echo theme_assets_url(); ?>js/leaflet.markercluster-1.3.0/dist/MarkerCluster.Default.css" />
    <script src="<?php echo theme_assets_url(); ?>js/leaflet-1.3.1/dist/leaflet.js" crossorigin=""></script>
    <script src="<?php echo theme_assets_url(); ?>js/leaflet.markercluster-1.3.0/dist/leaflet.markercluster.js"></script>
<script type="text/javascript" src="<?php echo theme_assets_url(); ?>js/jquery.mousewheel.min.js"></script>
<script type="text/javascript" src="<?php echo theme_assets_url(); ?>js/jquery.jscrollpane.min.js"></script>

<div id="map" style="width:100%;height:500px;"></div>
<script type="text/javascript">
    $(function(){
        var parent = window.parent.document;
        $(parent).find("#icon-loading").hide();
    });
    var MapData     = <?php echo json_encode($mapdata);?>;
    createMarkerMap();

    function createMarkerMap()
{

    var map = L.map( 'map', {
	center: [28.079046 , 17.6034056],
	minZoom: 2,
	zoom: 2,
	noWarp: true
	}).setMaxBounds([[84.67351256610522, -174.0234375], [-58.995311187950925, 223.2421875]]);

	L.tileLayer( 'https://stamen-tiles-{s}.a.ssl.fastly.net/toner/{z}/{x}/{y}.{ext}', {
	subdomains: 'abcd',
	ext: 'png'
	}).addTo( map );

  // ATTR = '&copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, ' +
  // '<a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a> | ' +
  // '&copy; <a href="http://cartodb.com/attributions">CartoDB</a>';

//   CDB_URL = 'https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}.png';

//   // add tiles to map
//   L.tileLayer(CDB_URL).addTo(map);

	var greenIcon = new L.Icon({
	iconUrl: 'https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-green.png',
	shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
	iconSize: [25, 41],
	iconAnchor: [12, 41],
	popupAnchor: [1, -34],
	shadowSize: [41, 41]
	});

	var redIcon = new L.Icon({
	iconUrl: 'https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
	shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
	iconSize: [25, 41],
	iconAnchor: [12, 41],
	popupAnchor: [1, -34],
	shadowSize: [41, 41]
	});

  if(markerCluster != null){
    removeAllMarkers(markerCluster);
  }
    var latlong = MapData;

    var markerCluster = L.markerClusterGroup();
	for ( var i = 0; i<latlong.length;i++)
	{

        if(latlong[i]["map_match_sentiment"] == null){
            latlong[i]["map_match_sentiment"] = "0.00";
        }

		if(latlong[i]["map_match_sentiment"] > 0.00){
			var color = "green";
		}else if(latlong[i]["map_match_sentiment"] < 0.00){
			var color = "red";
		}else{
			var color = "blue";
		}

		if(latlong[i]["map_match_sourceid"] == 1){
			var shortLink = "Facebook";
		}else if(latlong[i]["map_match_sourceid"] == 2){
			var shortLink = "Twitter";
		}
		var popup = '<b>User:</b> '+latlong[i]["map_match_username"] +
					'<br/><b>Content:</b> ' + latlong[i]["map_match_msg_content"].replace(/\?/g,"") +
					'<br/><b>Link:</b> <a href="'+ latlong[i]["map_match_link"] +'" target="_blank"> '+ shortLink + 
					'</a><br/><b>Sentiment: <font style="color:'+color+';">' + latlong[i]["map_match_sentiment"] +
					' %</b></font><br/><b>Timepost:</b> ' + latlong[i]["map_match_timepost"];
		//alert(latlong[i][geo_latitude]);
		if(latlong[i]["map_match_sentiment"] > 0.00){

			var m = L.marker([latlong[i]["map_match_latitude"] , latlong[i]["map_match_longitude"]] , {icon: greenIcon}).bindPopup( popup );
			markerCluster.addLayer( m );
		}else if(latlong[i]["map_match_sentiment"] < 0.00){

			var m = L.marker([latlong[i]["map_match_latitude"] , latlong[i]["map_match_longitude"]] , {icon: redIcon}).bindPopup( popup );
			markerCluster.addLayer( m );
		}else {
    
			var m = L.marker([latlong[i]["map_match_latitude"] , latlong[i]["map_match_longitude"]]).bindPopup( popup );
			markerCluster.addLayer( m );
		}
	}
	map.addLayer(markerCluster);
}

function removeAllMarkers(){
    map.removeLayer(markerCluster);
}
</script>

