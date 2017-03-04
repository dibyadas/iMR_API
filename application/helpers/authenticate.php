<?php
require_once "Constants.php";
// Context is the class object which extends to REST_CONTROLLER.
// This object should also have loaded this models already : PersonModel and UserIdModel
function authenticate($context){
    // Collect the token provided in the authorization header
    $token = $context->input->get_request_header('Authorization',TRUE);

    // If token is not provided.
    if(!$token){
		Throw new Exception("The Authorization Header Missing");
		}
    // The field in the authorization is provided with token_type and access_token : "Bearer access_token"
	$temp = explode(" ",$token);

    // Both should be present in the field.
	if(isset($temp[0]) && isset($temp[1])){
		$token_type = strtolower($temp[0]);
		$access_token = $temp[1];
	}
	else{
		Throw new Exception("The access-token is either empty or not complete.");
		}
    // Right now only bearer token is the game.
	if($token_type === "bearer"){

	    // access token shouldn't be null but well chutiyap h ye ..phir se same chweez checkkar rha h sala .
		if($access_token){
			try{
			    // decode to get the payload or else throw exception if couldn't be decoded
				$payload = (array)JWT::decode($access_token,Constants::$TOKEN_KEY);

				// Check if this user is active or not.
				if(!$context->Employee_->isActive($payload['user_id'])){
					Throw new Exception("Your Account is inactive now.");
					}

				// To check if this token was issued after a credential update or not..
                // comparison between lastCredentialUpdateTime and iat of token is important

                $lastCredentialUpdateTime = $context->Person_->getLastCredentialUpdateTime($payload['person_id']);

				// If iat is less than lastCredentialUpdateTime then token was issued before credential update so is invalid

				if($payload['iat'] < $lastCredentialUpdateTime ){
//				    response($context,false,433,"","Your token has expired. Please Login again");
				    Throw new Exception("Your Token has expired because of last credential update.Please login again");
                    }

                // If it is a web token then have a exp as well check for it.
//				$timeNow = microtime(true);
//				if($payload["device"] == "web"){
//                    if($timeNow > $payload["exp"]){
//                        response($context,false,434,"","Your token has expired. Login again");
//                        }
//                    }
				}
			 catch(Exception $e){
				 Throw $e;
				 }
			}
		else{
			 Throw new Exception("Access-token is missing.");
			}
		}
	else{
		Throw new Exception("Incorrect Token-type. We only support Bearer type tokens right now.");
		}
	return $payload;	 
	}
?>
