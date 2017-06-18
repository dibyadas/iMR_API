<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require(APPPATH.'libraries/REST_Controller.php');
require(APPPATH.'helpers/response.php');
require_once(APPPATH.'libraries/jwt_helper.php');
require_once(APPPATH.'helpers/createLink.php');
require_once(APPPATH.'models/PersonProfileDb.php');
require_once (APPPATH.'helpers/Constants.php');
class Login extends REST_Controller{
	private $token_payload;
	private $exp;
	public function __construct(){   
		parent::__construct();
		$this->load->model('LoginModel','login_');
		$this->exp = microtime(true)+3600*2;
	}
	
	public function login_post(){

	    //  get Post Variables
		$user_id = $this->post('user_id');
		$pass = $this->post('password');
		$device = $this->post('device');  // web or Android .. 
		$password = md5($pass.Constants::$LOGIN_SALT);
		
		// Get Profile if it exits, else will return null.

		$profile = $this->login_->getProfile($user_id,$password);

		response($this,true,200,$profile);
		// if profile exists return the response with the access token
		if($profile){
			$data = $profile;
			$data[PersonDb::$PROFILE_PIC] = path2url($data[PersonDb::$PROFILE_PIC]);

			// if request is from android don't use any expiry time for access token.
			if($device == "android"){
					$access_token = $this->createAccessToken($data,$device,false);
				}
            // else use expiry time.

			else{
					$access_token = $this->createAccessToken($data,$device,true);
					$data['exp'] = $this->exp;
				}
			$data['access_token'] = $access_token;
			$data['token_type'] = "Bearer";
			response($this,true,200,$data);
			}
		else{
			response($this,false,401,"","Authorization failed. Either UserID or Password is invalid or Account is inactive");
			}

	}
	private function createAccessToken($data,$device,$expiry){

	    $payload = $data;
		$iat =  microtime(true);
		$payload['iat'] = $iat; 
		$payload['own'] = $data[EmployeeDb::$ROLE];
		$payoad['iss'] = "www.Pharma.com";
		$payload['device'] = $device;
		if($expiry){
			$payload['exp'] =$this->exp;
			}
		$payload['token_type'] = "Bearer";
		return JWT::encode($payload,Constants::$TOKEN_KEY);
		}
}

?>
