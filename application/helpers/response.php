<?php 
function response($context,$status,$status_code,$data="",$error = ""){
	$response["msg"] = "";
	if((gettype($data) == "array" || gettype($data) == "object")){
		$response["data"] = $data;
	}else{
		$response["msg"] = $data;
 	}
	$response["status"] = $status;
	if($status == false){
		$response["msg"] = $error;
	}
	$response["status_code"] = $status_code;
	//$response["http_status"] = $http_status;
	$context->response($response);
	}
?>
