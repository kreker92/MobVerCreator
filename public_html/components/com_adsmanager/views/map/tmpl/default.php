<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2013 JoomPROD.com. All rights reserved.
 * @license		GNU/GPL
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$baseurl = JURI::base();

?>
<script type="text/javascript"
	src="http://maps.googleapis.com/maps/api/js?key=&sensor=false">
</script>
<script type="text/javascript"
	src="modules/mod_adsmanager_adsmap/js/markerclusterer_compiled.js">
</script>
<script type="text/javascript"> 
    

    function initializemap() {
        
        var map;
        var markers = [];
        var markersCluster = [];
        var markersTitle = [];
        var markerNum = 0;
        var infowindow = new google.maps.InfoWindow();
        var infoWindowsContents = [];
        var geocoder = new google.maps.Geocoder();
        var center = new google.maps.LatLngBounds();
        
        <?php
        $cpt = 0;
        //We load every data of the ads displayed on the map
        foreach ($this->contents as $ads) {
            
            //If the ad had a gmap field...
            if($ads->ad_gmap_lat != null && $ads->ad_gmap_lng != null){
                
                $linkTarget = JRoute::_( "index.php?option=com_adsmanager&view=details&id=".$ads->id."&catid=".$ads->catid);
			
                //We recover 
                if (isset($ads->images[0])) {
                    $image[$cpt] = '<img class="adimage" style="width: 100px;" name="adimage"'.$ads->id.'" src="'.$baseurl.'images/com_adsmanager/ads/'.$ads->images[0]->thumbnail.'" alt="'.htmlspecialchars($ads->ad_headline).'" />';
                } else {
                    $image[$cpt] = '<img class="adimage" style="width: 100px;" src="'.ADSMANAGER_NOPIC_IMG.'" alt="nopic" />';
                }
                
                $ads->ad_text = str_replace ('<br />'," ",$ads->ad_text);
                $af_text = JString::substr($ads->ad_text, 0, 100);
                if (strlen($ads->ad_text)>100) {
                    $af_text .= "[...]";
                }
                ?>
                        
                infoWindowsContents[markerNum] = '<div id="content">'+
                '<div style="float: left; width: 115px;">'+
                '<?php echo $image[$cpt]; ?>'+
                '</div>'+
                '<div style="float: right;">'+
                '<h3 style="margin-top: 0;"><a href="<?php echo $linkTarget ?>"><?php echo addslashes($ads->ad_headline) ?></a>'+
                '<span class="adsmanager_cat"><?php echo "(".addslashes($ads->parent)." / ".addslashes($ads->cat).")"; ?></span></h3>'+
                '<?php echo addslashes($af_text) ?>'+
                '</div>'+
                '</div>';
        
                center.extend(new google.maps.LatLng(<?php echo $ads->ad_gmap_lat ?>, <?php echo $ads->ad_gmap_lng ?> ));
                markers[markerNum] = new google.maps.LatLng(<?php echo $ads->ad_gmap_lat ?>, <?php echo $ads->ad_gmap_lng ?> );
                markersTitle[markerNum] = "<?php echo $ads->ad_headline ?>";
                markerNum++;
                <?php
                $cpt++;
            }
        } 
        ?>
        var myOptions = {
            center: center.getCenter(),
            zoom: 9,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
        var imageMarker = new google.maps.MarkerImage('<?php echo $baseurl?>modules/mod_adsmanager_adsmap/tmpl/marqueur.png', new google.maps.Size(57, 42));
        
        // display the markers
        for(i=0; i<markers.length; i++){
            
            var mark = markers[i];
            var marker = new google.maps.Marker({
                'title': markersTitle[i],
                'map': map,
                'position': mark,
                'icon': imageMarker
                });
            
            marker.html = infoWindowsContents[i];
            
            google.maps.event.addListener(marker, 'click', function() {
                infowindow.setContent(this.html);
                infowindow.open(map,this);
            });
            
            markersCluster[i] = marker;
        }
        
        var markerCluster = new MarkerClusterer(map, markersCluster);
        
        // Zoom for displaying all markers on screen
        map.fitBounds(center)
        
    }
    google.maps.event.addDomListener(window, 'load', initializemap);
</script>
<div class="gmap" id="map_canvas" style="width: auto; height: 300px;"></div>
<script type="text/javascript">initializemap();</script>
<form method="post" action="index.php?option=com_adsmanager&view=map">
    <?php
    foreach($this->searchfields as $fsearch) {
        $title = $this->field->showFieldTitle($this->catid,$fsearch);
        echo htmlspecialchars($title);
        $this->field->showFieldSearch($fsearch,$this->catid,null);
        echo " ";
    }
    ?>
    <input type="submit" value="Rechercher" />
</form>