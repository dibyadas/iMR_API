<?php
function validateDateFormat($dateString,$format){
	$d = DateTime::createFromFormat($format, $dateString);
    return $d && $d->format($format) === $dateString;
	}
?>
