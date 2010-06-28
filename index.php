<?php

require_once('config.php');

?>

<?php
// variable setup. do not edit

// Your KML file from google maps
$kmlLink = 'http://maps.google.com/maps/ms?hl=en&ptab=2&ie=UTF8&oe=UTF8&msa=0&output=nl&output=kml&msid='.$_CONFIG['map_id'];

// Your Map Edit Link
$editMapLink = 'http://maps.google.com/maps/ms?ie=UTF&msa=0&msid='.$_CONFIG['map_id'];

// your google maps key
$GMapKey = $_CONFIG['api_key'];

$centerPoint = $_CONFIG['centerpoint'];
$zoomLevel = $_CONFIG['zoomlevel'];
$mapType = $_CONFIG['maptype'];

if(strstr($_SERVER['HTTP_USER_AGENT'],'iPhone') == false){
	$isIPhone = false;
} else {
	$isIPhone = true;
}

?>
<html>
  <head>
	<base href="http://maps.google.com" />
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title><?php echo $_CONFIG['site_title']; ?></title>
	<!-- need favicon here -->
	<link rel="stylesheet" href="<?php echo $_CONFIG['domain']; ?><?php echo $_CONFIG['stylesheet_url']; ?>" type="text/css" media="all" />

<?php if (($isIPhone)): ?>
	<link rel="stylesheet" type="text/css" href="<?php echo $_CONFIG['domain']; ?><?php echo $_CONFIG['mobile_stylesheet_url']; ?>" media="only screen and (max-device-width: 480px), only screen and (max-device-width: 320px)">
	<link rel="apple-touch-icon" href="<?php echo $_CONFIG['domain']; ?>/apple-touch-icon.png">
	<link rel="apple-touch-startup-image" href="<?php echo $_CONFIG['domain']; ?>/apple-touch-startup-image.png">
	<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=no">
<?php else: ?>

<!-- put any non-iphone stuff here -->

<?php endif; ?>

    <!--[if lt IE 7]>
	
    <![endif]-->

     <!--[if IE ]>
	    <style>
			#placesContainer {
				height: expression(document.body.clientHeight - 100 ); /* make 350 if you want to show ads */
			}
		</style>
	<![endif]-->
    <!--[if lte IE 6]>

    <![endif]-->

<!-- 

oh hi!

original code by:
Brian Suda - http://suda.co.uk | http://twitter.com/biansuda
Josh Russell - http://joshrussell.com | http://twitter.com/joshr

and open-sourced at:
http://github.com/joshr/onePageMapSite

 -->


  </head>
  <body>
	<div id="bar">
		<h1><?php echo $_CONFIG['site_title']; ?></h1>
		<!--<p><a href="<?=str_replace('&','&amp;',$editMapLink)?>">Add / Edit Hotspots</a> - <a href="<?=str_replace('&','&amp;',$kmlLink)?>">Download KML</a></p>-->
		<p><a href="<?=str_replace('&','&amp;',$editMapLink)?>">Add/Edit</a> | <a href="<?=str_replace('&','&amp;',$kmlLink)?>">KML</a> | by <a href="http://twitter.com/<?php echo $_CONFIG['twitter_id']; ?>">@<?php echo $_CONFIG['twitter_id']; ?></a></p>
		<p><!-- AddThis Button BEGIN -->
<a class="addthis_button" href="http://www.addthis.com/bookmark.php?v=250&amp;username=<?php echo $_CONFIG['addthis_id']; ?>"><img src="http://s7.addthis.com/static/btn/v2/lg-share-en.gif" width="125" height="16" alt="Bookmark and Share" style="border:0"/></a><script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#username=<?php echo $_CONFIG['addthis_id']; ?>"></script></p>
<!-- AddThis Button END -->
	<?php
		// fetch the KML file and parse for locations, convert to hCards
		$xml = getKML($kmlLink);
		$hcards = getHCards($xml);
		if($isIPhone){
			// create static map!
			$markers = getMarkers($xml);

			echo '<img id="mapimg" src="http://maps.google.com/staticmap?center='.$centerPoint.'&type='.$mapType.'&zoom='.$zoomLevel.'&size=320x200&maptype=map&key='.$GMapKey.'&markers='.$markers.'" alt="Kort"/>';
			
		}
	?>
		<div id="placesContainer">
			<ul id="places">
				<?php
				foreach($hcards as $hcard){
					echo '<li>'.$hcard.'</li>';
				}
				?>
			</ul> <!-- /places -->
		</div> <!-- /placesContainer -->
		<div id="gads"><script type="text/javascript"><!--
		google_ad_client = "<?php echo $_CONFIG['google_ad_client']; ?>";
		/* 250x250, created 5/26/10 */
		google_ad_slot = "<?php echo $_CONFIG['google_ad_slot']; ?>";
		google_ad_width = 250;
		google_ad_height = 250;
		//-->
		</script>
		<script type="text/javascript"
		src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
		</script></div> <!-- /gads -->
	</div> <!-- /bar -->
	<!--<p><a href="<?=str_replace('&','&amp;',$kmlLink)?>">Download KML</a></p>-->
	
	<?php
	if(!($isIPhone)){
	// Javascript for the progressive enhancement of maps
	?>
	<!--<a href="<?=str_replace('&','&amp;',$editMapLink)?>">Edit Points</a>-->
	<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?=$GMapKey?>" type="text/javascript"></script>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.2.6/jquery.min.js"></script>
	<script type="text/javascript">
	jQuery(function() {
	    // First create a div to host the map
	    $('<div id="map"></div>').css({
	        'width': '100%',
	        'height': '100%'
	    }).insertBefore('#bar');


		var map = new GMap2(document.getElementById("map"));
		map.setCenter(new GLatLng(<?=$centerPoint?>), <?=$zoomLevel?>);
		map.addControl(new GLargeMapControl());
		map.addControl(new GMapTypeControl());
	//	map.removeMapType(G_SATELLITE_MAP);
	//	map.removeMapType(G_HYBRID_MAP);
		
		
	    // Geocode each hcard and add a marker
		$('.vcard').each(function() { 
		  var vcard = $(this); 
		  var geo = vcard.find('.geo[title]'); 
		  if (geo.length && geo.attr('title')) { 
			  var degrees = geo.attr('title').split(';'); 
			  var lat = parseFloat(degrees[0]); 
			  var lon = parseFloat(degrees[1]); vcard.css('cursor', 'pointer');
			  var point = new GLatLng(lat, lon); 

		      var icon = new GIcon();
		      icon.iconAnchor = new GPoint(9, 34);
		      icon.infoWindowAnchor = new GPoint(9, 2);
		      icon.image = "<?php echo $_CONFIG['marker_image']; ?>";
		      icon.shadow = "<?php echo $_CONFIG['marker_shadow_image']; ?>";
		      var marker = new GMarker(point, icon);

			  GEvent.addListener(marker, "click", function() { marker.openInfoWindowHtml(vcard.find('.fn').html()+'<br/>'+vcard.find('.note').html()); });

		      map.addOverlay(marker); vcard.click(function() { $('.current-vcard').removeClass('current-vcard'); vcard.addClass('current-vcard'); map.panTo(point); marker.openInfoWindowHtml(vcard.find('.fn').html()+'<br/>'+vcard.find('.note').html());
		 });  	
		  }
	   });
	});
	</script>
	<?php
	}








	// fetches the KML file and converts it to HTML
	function getKML($kmlLink){
				// fetch the KML file
				$c = curl_init();
				curl_setopt($c, CURLOPT_RETURNTRANSFER,1);
				curl_setopt($c, CURLOPT_URL, $kmlLink);
				curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 2);
				curl_setopt($c, CURLOPT_TIMEOUT, 4);
				curl_setopt($c, CURLOPT_FOLLOWLOCATION,1);
		//		curl_setopt($c, CURLOPT_HEADER,1);
				curl_setopt($c, CURLOPT_USERAGENT, "KML->hCard PHP Class 0.2b /");
				$info = curl_getinfo($c);
				$response = curl_exec($c);
				
				return $response;		
	}
	
	function getMarkers($xml){
		$str = '';
		// extract the points
		$xml = new SimpleXMLElement($xml);

		$str = '';
		foreach($xml->Document->Placemark as $place){
			$coords = explode(',',$place->Point->coordinates);
			$lat    = $coords[1];
			$lon    = $coords[0];
			$str .= $lat.','.$lon.',reda%7C';
		}
		return $str;
	}
	
	function getHCards($xml){
		$tmp = array();

		// extract the points
		$xml = new SimpleXMLElement($xml);

		$str = '';
		foreach($xml->Document->Placemark as $place){
			$coords = explode(',',$place->Point->coordinates);
			$lat    = $coords[1];
			$lon    = $coords[0];
			
			$str = '<div class="vcard"><abbr class="fn geo" title="'.$lat.';'.$lon.'">'.$place->name.'</abbr><div class="note">'.$place->description.'</div></div>';
			$tmp[] = $str;
		}
			// create hCard
			// add to array
			// repeat
		
		return $tmp;
	}
	
	?>


<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', '<?php echo $_CONFIG['analytics_id']; ?>']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>


  </body>
</html>