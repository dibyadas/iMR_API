<?php
function geoMatch($geotag1,$geotag2,$tolerance){
	$geoTag1 = json_decode($geotag1,true);
	$geoTag2 = json_decode($geotag2,true);
	$lat1 = $geoTag1['loc_1']['lat'];
	$lat2 = $geoTag2['loc_1']['lat'];
	$lon1 = $geoTag1['loc_1']['lon'];
	$lon2 = $geoTag2['loc_1']['lon'];
	$distance = getDistanceFromLatLonInKm($lat1,$lon1,$lat2,$lon2);
	if($distance>$tolerance){
		return false;
		}
	else return true;
	}
function getDistanceFromLatLonInKm($lat1,$lon1,$lat2,$lon2) {
  $R = 6371; // Radius of the earth in km
  $dLat = deg2rad($lat2-$lat1);  // deg2rad below
  $dLon = deg2rad($lon2-$lon1); 
  $a = 
    sin($dLat/2) * sin($dLat/2) +
    cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * 
    sin($dLon/2) * sin($dLon/2)
    ; 
  $c = 2 * atan2(sqrt($a),sqrt(1-$a)); 
  $d = $R * $c; // Distance in km
  return $d;
}

?>
