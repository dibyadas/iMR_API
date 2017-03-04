<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require(APPPATH.'libraries/REST_Controller.php');
require(APPPATH.'helpers/response.php');
require(APPPATH.'helpers/upload_pic.php');
require(APPPATH.'helpers/EmptyString.php');
require(APPPATH.'helpers/authenticate.php');
require(APPPATH.'helpers/createLink.php');
require(APPPATH.'helpers/validateDateFormat.php');
require_once(APPPATH.'libraries/jwt_helper.php');
require_once(APPPATH.'models/ChemistProfileDb.php');
require_once(APPPATH.'models/TBDChemDb.php');

class Chemist extends REST_Controller{
	private $token_payload;
	private $name;
	private $email;
	private $phone;
	private $chemId;
	private $probableStocklist;
	private $contactPerson;
	private $profilePic;
	private $DOB;
	private $anniversary;
	private $MRCore;
	private $geotags;
	private $stationName;
	private $docRelation;
	private $shippingAddress;
	private $activeMR;
	private $active;

	public function __construct(){   
		parent::__construct();
		$this->load->model('DocModel', 'doc_');
		$this->load->model('MRModel', 'MR_');		
		$this->load->model('SetModel', 'Set_');
		$this->load->library('upload');
		try{
			$this->token_payload = authenticate($this);
			}
		catch(Exception $e){
			response($this,false,401,"",$e->getMessage());		// 401 -> invalid token
			}
	}

	private function checkPrimaryConditionsForEditOrAdd(){
		if($this->token_payload['own'] != "MR"){
			response($this,false,480,"","You don't have permissions to add chemist.");
		}
		$this->name = ucwords(strtolower($this->post(ChemDb::$NAME)));
		if(isStringEmpty($this->name)){
			response($this,false,491,"","Please fill in the medical name.");
		}
		$this->phone = $this->post(ChemDb::$PHONE);
		if(isStringEmpty($this->phone)){
			response($this,false,492,"","Please fill in the Mobile number.");
		}
		$this->stationName = $this->post(ChemDb::$STATION);
		if(isStringEmpty($this->stationName)){
			response($this,false,493,"","Please fill in the Station name.");
		}
		if($this->Chem_->isDuplicate($this->name,$this->phone/*$this->geotags,$this->geo_distance_tolerance*/)){
			response($this,false,400,"","Chemist Profile : Already Exists , Two Chemist have same phone numbers.");
		}
	}
	private function getAllOtherInputs(){
		$this->email = $this->post(ChemDb::$EMAIl);
		$this->geotags = $this->post(ChemDb::$GEOTAG);
		$this->contactPerson = $this->post(ChemDb::$CONTACT_PERSON);
		$this->DOB = $this->post(ChemDb::$DOB);
		$this->anniversary = $this->post(ChemDb::$ANNIVERSARY);
		$this->MRCore = $this->post(ChemDb::$MR_CORE);
		$this->docRelation = $this->post(ChemDb::$DOC_RELATION);
		$this->probableStocklist = $this->post(ChemDb::$PROBABLE_STOCKLIST);
		$this->shippingAddress = $this->post(ChemDb::$SHIPPING_ADDRESS);
	}
	public function uploadProfileImage(){
		$profilePic = "";
		if(isset($_FILES[ChemDb::$PHOTO])){
			if(file_exists($_FILES[ChemDb::$PHOTO]['tmp_name']) && is_uploaded_file($_FILES[ChemDb::$PHOTO]['tmp_name'])) {
				try{
					$profilePic = upload_pic($this,ChemDb::$PHOTO,'./uploads/Doctor/');
				}
				catch(Exception $e){
					//response($this,false,415,"","Doctor Image Upload Error: ".$e->getMessage());  // 415 -> Unsupported Media Type
				}
			}
		}
		return $profilePic;
	}
	public function add_post(){
		$this->checkPrimaryConditionsForEditOrAdd();
		$device = $this->token_payload["device"];
		$this->profilePic = "";
		if($device == "android"){
			$this->profilePic = path2url($this->uploadProfileImage());
		}else if($device == "web"){
			$this->profilePic = $this->post(ChemDb::$PHOTO);
			}
		$this->getAllOtherInputs();
		$this->activeMR = $this->token_payload['user_id'];
		try{
            // TODO:: Add here the sql to add this chemist_id in the MRHistory_KeyMap
			$data['chem_id'] = $this->Chem_->addProfile($this->name,$this->phone,$this->email,$this->geotags,$this->activeMR,
				$this->profilePic,$this->stationName,$this->contactPerson,$this->DOB,$this->anniversary,$this->MRCore,
				$this->docRelation,$this->probableStocklist,$this->shippingAddress); // <--- Here
			$data[ChemDb::$PHOTO] = $this->profilePic;
			$data['msg'] = "Chemist Profile Added Successfully";
			response($this,true,200,$data);
			}
		catch(Exception $e){
			unlink($this->profilePic);
			response($this,false,412,"",$e->getMessage());
			}
		}
	public function edit_post(){
		$this->chemId = $this->post('chem_id');
		if($this->chemId == NULL || $this->chemId == "" ){
			response($this,false,408,"","Please provide chemist id to perform function.");
			}
		if(!$this->Chem_->isAssoc($this->chemId,$this->token_payload['user_id'])){
			response($this,false,405,"","You are not the Active MR for this chemist profile.");
			}
		$response_data = array();
		$this->checkPrimaryConditionsForEditOrAdd();
		$device = $this->token_payload["device"];
		if($device == "web"){
			// TODO :: Add Doctor and Chemist too to the hierarchy structure. Also edit the schema a little bit.
			$this->profilePic= $this->post(ChemDb::$PHOTO);
			
		}else if($device == "android"){
			$this->profilePic = path2url($this->uploadProfileImage());
			if($this->profilePic == "" || $this->profilePic == NULL){
				$this->profilePic = $this->Chem_->getChemist($this->chemId)[ChemDb::$PHOTO]; // <--- Here
			}
		}else{
			$this->profilePic = $this->Chem_->getChemist($this->chemId)[ChemDb::$PHOTO];
		}
		$this->getAllOtherInputs();
		$this->activeMR = $this->token_payload["user_id"];
		$this->active = ($this->post(ChemDb::$ACTIVE) === NULL)?1:$this->post(ChemDb::$ACTIVE);

		$this->Chem_->editChemProfile($this->chemId,$this->name,$this->phone,$this->stationName,$this->email,$this->geotags,
			$this->contactPerson,$this->profilePic,$this->DOB,$this->anniversary,$this->probableStocklist,$this->MRCore,
			$this->shippingAddress,$this->docRelation,$this->activeMR,$this->active);// <--- Here
		$response_data[ChemDb::$PHOTO] = $this->profilePic;
		$response_data['msg'] = "Updates are done successfully";
		response($this,true,200,$response_data);
		}
	public function assign_post(){
		if($this->token_payload["own"] == "Admin"){
			$chemId = $this->post(ChemDb::$CHEM_ID);
			$MRId = $this->post(ChemDb::$ACTIVE_MR);
			$this->Chem_->assignChemist($chemId,$MRId); // <--- Here  ... MRHistory is also created.
			response($this,true,200,"chemist assigned to new MR");
		}else{
			response($this,false,430,"","Only Admin can access this");
		}
	}

	public function activate_post(){
		$this->chemId = $this->post(ChemDb::$CHEM_ID);
		if(!$this->Chem_->isAssoc($this->chemId,$this->token_payload['user_id'])){ // <--- Here
			response($this,false,405,"","You are not the Active MR for this chemist profile.");
		}
		$this->Chem_->activateChem($this->chemId);
		response($this,true,200,"Chemist is activated.");
	}
	public function deactivate_post(){
		$this->chemId= $this->post(ChemDb::$CHEM_ID);
		if(!$this->Chem_->isAssoc($this->chemId,$this->token_payload['user_id'])){ // <--- Here
			response($this,false,405,"","You are not the Active MR for this chemist profile.");
		}
		$this->Chem_->deactivateChem($this->chemId);
		response($this,true,200,"Chemist is deactivated.");
	}
	public function profile_get(){
		$chemId = $this->get(ChemDb::$CHEM_ID);
//		$MRId = $this->getMRId();
//		if (!$this->Chem_->isAssoc($chemId, $MRId)){
//			response($this, false, 405, "", "You are not the Active MR for this Chemist profile.");
//		}
		/*else */if (!isStringEmpty($chemId)){
			$data = $this->Chem_->getChemist($chemId);
			response($this, true, 200, $data);
		}else{
			response($this,false,481, "","Please provide a Chemist ID to get the regarding info");
		}
	}
	public function profiles_get(){
		$mr_id = $this->getMRId();
		response($this,true,200,$this->Chem_->getAllChemist($mr_id));
	}

	private function getMRId(){
		$MRId = $this->get("mr_id");
		if($MRId == NULL || $MRId == ""){
			if($this->token_payload["own"] == "MR"){
				$MRId = $this->token_payload["user_id"];
			}else{
				$MRId = NULL;
			}
		}
		return $MRId;
	}
}
