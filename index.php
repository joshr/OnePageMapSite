<?php
  require_once('config.php');

  // variable setup. do not edit

  // Your KML file from google maps
  $kmlLink = 'http://maps.google.com/maps/ms?hl=en&ptab=2&ie=UTF8&oe=UTF8&msa=0&output=nl&output=kml&msid='.$_CONFIG['map_id'];

  // Your Map Edit Link
  $editMapLink = 'http://maps.google.com/maps/ms?ie=UTF&msa=0&msid='.$_CONFIG['map_id'];

  $centerPoint = $_CONFIG['centerpoint'];
  $zoomLevel = $_CONFIG['zoomlevel'];
  $mapType = $_CONFIG['maptype'];

  if(strstr($_SERVER['HTTP_USER_AGENT'],'iPhone') == false){
  	$isIPhone = false;
  } else {
  	$isIPhone = true;
  }
  
  // fetch the KML file and parse for locations, convert to hCards
  $xml = getKML($kmlLink);
  $hcards = getHCards($xml);

?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8" />
  <title><?php echo $_CONFIG['site_title']; ?></title>
  <base href="http://maps.google.com">

  <!-- 

    oh hi!

    original code by:
    Brian Suda - http://suda.co.uk | http://twitter.com/biansuda
    Josh Russell - http://joshrussell.com | http://twitter.com/joshr

    and open-sourced at:
    http://github.com/joshr/onePageMapSite

   -->

<?php if (($isIPhone)): ?>
	<link rel="stylesheet" href="<?php echo $_CONFIG['domain']; ?><?php echo $_CONFIG['mobile_stylesheet_url']; ?>">
	<link rel="apple-touch-icon" href="<?php echo $_CONFIG['domain']; ?>/apple-touch-icon.png">
	<link rel="apple-touch-startup-image" href="<?php echo $_CONFIG['domain']; ?>/apple-touch-startup-image.png">
	<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=no">
<?php else: ?>
	<link rel="stylesheet" href="<?php echo $_CONFIG['domain']; ?><?php echo $_CONFIG['stylesheet_url']; ?>" type="text/css" media="all" />
  <!-- put any non-iphone stuff here -->

<?php endif; ?>
  
  <!--[if IE ]>
    <style>
      #placesContainer {
			  height: expression(document.body.clientHeight - 100 ); /* make 350 if you want to show ads */
			}
		</style>
	<![endif]-->
</head>
<body>
	<div id="bar">
		<h1><?php echo $_CONFIG['site_title']; ?></h1>
		
		<!--
		  <p>
		    <a href="<?=str_replace('&','&amp;',$editMapLink)?>">Add / Edit Hotspots</a> - 
		    <a href="<?=str_replace('&','&amp;',$kmlLink)?>">Download KML</a>
		  </p>
		-->
		<p>
		  <a href="<?=str_replace('&','&amp;',$editMapLink)?>">Add/Edit</a> |
		  <a href="<?=str_replace('&','&amp;',$kmlLink)?>">KML</a> |
		  by <a href="http://twitter.com/<?php echo $_CONFIG['twitter_id']; ?>">@<?php echo $_CONFIG['twitter_id']; ?></a>
		</p>
		<p>
		  <!-- AddThis Button BEGIN -->
      <a class="addthis_button" href="http://www.addthis.com/bookmark.php?v=250&amp;username=<?php echo $_CONFIG['addthis_id']; ?>">
        <img src="http://s7.addthis.com/static/btn/v2/lg-share-en.gif" width="125" height="16" alt="Bookmark and Share" style="border:0"/>
      </a>
      <script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#username=<?php echo $_CONFIG['addthis_id']; ?>"></script>
      <!-- AddThis Button END -->
    </p>

	  <?php
	    if($isIPhone){
			  // create static map!
			  $markers = getMarkers($xml);

        echo '<a href="'.$editMapLink.'"><img id="mapimg" src="http://maps.google.com/staticmap?center='.$centerPoint.'&type='.$mapType.'&zoom='.$mobileZoomLevel.'&size=640x400&maptype=map&key='.$GMapKey.'&markers='.$markers.'" alt="Kort"/></a>';
		  }
	  ?>
		<div id="placesContainer">
			<ul id="places">
				<?php
  				foreach($hcards as $hcard){
  					echo '<li>'.$hcard.'</li>';
  				}
				?>
			</ul>
		</div>
		
		<?php if (($_CONFIG['google_ad_client']) && ($_CONFIG['google_ad_slot'])): ?>
		<?php if (($isIPhone)): ?>
		
  		<!-- mobile ads, eventually -->
  		<div id="gads">
  		  <script>
    		  window.googleAfmcRequest = {
      		  client: 'ca-mb-<?php echo $_CONFIG['google_ad_client']; ?>',
      			ad_type: 'text_image',
      			output: 'html',
      			channel: '',
      			format: '320x50_mb',
      			oe: 'utf8',
      			color_border: '336699',
      			color_bg: 'FFFFFF',
      			color_link: '0000FF',
      			color_text: '000000',
      			color_url: '008000',
  			  };
  			</script>
  			<script src="http://pagead2.googlesyndication.com/pagead/show_afmc_ads.js"></script>
  		</div>

		<?php else: ?>
		
  		<div id="gads">
  		  <script>
  		    google_ad_client = "<?php echo $_CONFIG['google_ad_client']; ?>";
  	      /* 250x250, created 5/26/10 */
  		    google_ad_slot = "<?php echo $_CONFIG['google_ad_slot']; ?>";
  		    google_ad_width = 250;
  		    google_ad_height = 250;
  		</script>
  		<script src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script></div>
		
		<?php endif; ?>
		<?php endif; ?>

	</div> <!-- /bar -->
	<!--<p><a href="<?=str_replace('&','&amp;',$kmlLink)?>">Download KML</a></p>-->
	
	<?php
	  if(!($isIPhone)){
	    // Javascript for the progressive enhancement of maps
	?>
	<!--<a href="<?=str_replace('&','&amp;',$editMapLink)?>">Edit Points</a>-->
	
	<script src="http://maps.google.com/maps/api/js?sensor=false"></script>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.2.6/jquery.min.js"></script>
	<script>
	jQuery(function () {
	    // First create a div to host the map
	    $('<div id="map"></div>').css({
        'position': 'absolute',
        'top': '0',
        'bottom': '0',
        'left': '0',
        'right': '0'
	    }).insertBefore('#bar');

		var map = new google.maps.Map(document.getElementById("map"), {
		  center: new google.maps.LatLng(<?=$centerPoint['lat']?>, <?=$centerPoint['lng']?>),
		  zoom: <?=$zoomLevel?>,
		  mapTypeId: google.maps.MapTypeId.ROADMAP
		});
		
		var icon = new google.maps.MarkerImage(
      // URL of the image
      '<?php echo $_CONFIG['marker_image']; ?>',
      new google.maps.Size(20, 34),   // Size of the image
      new google.maps.Point(0, 0),    // Orgin of the image sprite
      new google.maps.Point(10, 34)  // Anchor/Point
    );
    
    var iconShadow = new google.maps.MarkerImage(
      // URL of the shadow
      '<?php echo $_CONFIG['marker_shadow_image']; ?>',
      new google.maps.Size(37, 34),   // Size of the shadow
      new google.maps.Point(0, 0),    // Orgin of the shadow sprite
      new google.maps.Point(10, 34)  // Anchour/Point
    );
    
    var prevInfoBox;
				
	    // Geocode each hcard and add a marker
		$('.vcard').each(function() { 
		  var vcard = $(this).css('cursor', 'pointer'),
          geo = vcard.find('.geo[title]'),
          title = geo.attr('title');
		  
		  if (geo.length && title) { 
			  var degrees = title.split(';'),
			      lat = parseFloat(degrees[0]),
			      lng = parseFloat(degrees[1]); 
			      point = new google.maps.LatLng(lat, lng);

        var marker = new google.maps.Marker({
            position: point,
            map: map,
            icon: icon,
            shadow: iconShadow,
            title: title
        });
        
        var infoBox = new google.maps.InfoWindow({
            content: $('<div/>').html('<strong>' + vcard.find('.fn').html() + '</strong><br>' + vcard.find('.note').html())[0]
        });
        
        google.maps.event.addListener(marker, 'click', function() {
          prevInfoBox ? prevInfoBox.close() : null ;
          infoBox.open(map, marker);
          prevInfoBox = infoBox;
        });

        vcard.click(function () { 
          $('.current-vcard').removeClass('current-vcard'); 
          vcard.addClass('current-vcard');
          map.panTo(marker.getPosition());
          prevInfoBox ? prevInfoBox.close() : null ;
          infoBox.open(map, marker);
          prevInfoBox = infoBox;
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

	<?php if (($_CONFIG['analytics_id'])): ?>
    <script>
      var _gaq = _gaq || [];
      _gaq.push(['_setAccount', '<?php echo $_CONFIG['analytics_id']; ?>']);
      _gaq.push(['_trackPageview']);
      (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
      })();
    </script>
  <?php endif; ?>
  
  </body>
</html>