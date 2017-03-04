<?php
function isStringEmpty($string){
	if($string == NULL || $string == '' || !isset($string)){
		return true;
		}
	else{
		return false;
		}
	}
?>
