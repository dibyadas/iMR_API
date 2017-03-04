<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require(APPPATH.'libraries/REST_Controller.php');
require(APPPATH.'helpers/response.php');
require(APPPATH.'helpers/upload_pic.php');
require(APPPATH.'helpers/authenticate.php');
require(APPPATH.'helpers/createLink.php');
require(APPPATH.'helpers/EmptyString.php');
require(APPPATH.'helpers/validateDateFormat.php');
require_once(APPPATH.'libraries/jwt_helper.php');
require_once(APPPATH.'models/DoctorProfileDb.php');
class Doctor extends REST_Controller{
	private $token_payload;
	private $geo_distance_tolerance = 2;
	private $name;
	private $email;
	private $phone;
	private $sex;
	private $clinicImage;
	private $profileImage;
	private $padImage;
	private $docId;
	private $monthlyBusiness;
	private $visitDay;
	private $visitFrequency;
	private $geotags;
	private $MRCore;
	private $AMCore;
	private $RMCore;
	private $class;
	private $meetingTime;
	private $active;
	private $specialization;
	private $qualification;
	private $anniversary;
	private $DOB;
	private $assistantPhone;
	private $officePhone;
	private $setNo;
	private $stationName;
	private $patFrequency;
	private $inactiveDate;
	private $activeMR;
	private $assocChemist = array();
	public function __construct(){
		parent::__construct();
		$this->load->model('DocModel', 'doc_');
		$this->load->model('ChemModel', 'Chem_');
		$this->load->model('EmployeeModel', 'Employee_');
		$this->load->model('PersonModel', 'Person_');
		$this->load->model('ProductModel', 'prod_');
		$this->load->model('GiftModel', 'gift_');
		$this->load->model('SetModel', 'Set_');
		$this->load->library('upload');

		// Authenticting the token.
		try{
			$this->token_payload = authenticate($this);
			}
		catch(Exception $e){
			response($this,false,401,"",$e->getMessage());		// 401 -> invalid token
			}
	}

	private function checkPrimaryConditionsForEditOrAdd(){
		/* ----- Checking if token is owned by MR --------*/
		if($this->token_payload['own'] != "MR"){
			response($this,false,480,"","You don't have permissions to add/edit doctor.");
		}
		/* ----- Checking if compulosory variables are posted --------*/
		$this->name = ucwords(strtolower($this->post(DocDb::$NAME)));
		if(isStringEmpty($this->name)){
			response($this,false,481,"","Please fill in the doctor name.");
		}
		$this->visitDay = ucwords($this->post(DocDb::$VISIT_DAY));
		if(isStringEmpty($this->visitDay)){
			response($this,false,482,"","Please fill in the visit days.");
		}
		$this->meetingTime = $this->post(DocDb::$MEETING_TIME);
		if(isStringEmpty($this->meetingTime)){
			response($this,false,483,"","Please fill in the meeting time.");
		}
		$this->geotags = $this->post(DocDb::$GEOTAG);
		if(isStringEmpty($this->geotags)){
			response($this,false,484,"","Please fill in the primary address.");
		}
		$this->setNo = $this->post(DocDb::$SET_NO);
		if(isStringEmpty($this->setNo)){
			response($this,false,485,"","Please fill in the Set number.");
		}else if(!$this->Set_->setExist($this->token_payload["user_id"],$this->setNo)){
			response($this,false,485,"","Please provide a valid Set number");
		}
		$this->phone = $this->post(DocDb::$PHONE);
		if(isStringEmpty($this->phone)){
			response($this,false,485,"","Please fill in the Phone details.");
		}

		/* -----------------------------*/
	}
	private function isDuplicate($name,$phone){
		return $this->doc_->isDuplicate($this->name, $this->phone/*$this->DOB,$geotag,$this->geo_distance_tolerance*/);
	}
	private function getAllOtherInputs(){
		$this->DOB = $this->post(DocDb::$DOB);
		//$phone = !$this->post(DocDb::$PHONE)?"":$this->post(DocDb::$PHONE);
		$this->class = $this->post(DocDb::$CLASS);
		$this->MRCore = $this->post(DocDb::$MR_CORE);
		$this->AMCore = $this->post(DocDb::$AM_CORE);
		$this->RMCore = $this->post(DocDb::$RM_CORE);
		$this->inactiveDate = $this->post(DocDb::$INACTIVE_DATE);
		$this->assistantPhone = $this->post(DocDb::$ASSISTANT_PHONE);
		$this->officePhone = $this->post(DocDb::$OFFICE_PHONE);
		$this->visitFrequency = $this->post(DocDb::$VISIT_FREQ);
		$this->monthlyBusiness = $this->post(DocDb::$MONTHLY_BUSINESS);
		$this->patFrequency = $this->post(DocDb::$PAT_FREQ);
		$this->stationName = ucwords($this->post(DocDb::$STATION));
		$this->specialization = ucwords(strtolower($this->post(DocDb::$SPECIALIZATION)));
		$this->qualification = strtoupper(strtolower($this->post(DocDb::$QUALIFICATION)));
		$this->anniversary = $this->post(DocDb::$ANNIVERSARY);
		$this->email = $this->post(DocDb::$EMAIl);
		$this->sex = $this->post(DocDb::$SEX);
	}
	public function updateProfilePic_post(){
	}

	public function add_post(){

		$this->checkPrimaryConditionsForEditOrAdd();
		/* Checking if doctor iss duplicate or not by name and phone ---------*/
		if($this->isDuplicate($this->name,$this->phone)){
			response($this,true,403,"","Same Doctor Name and Phone already exists.");
		}
		/*---------------*/

		/*------------ Uploading three pics if uploaded ----------------*/
        $this->profileImage="";
        $this->padImage = "";
        $this->clinicImage = "";
		$device = $this->token_payload["device"];

		if($device == "android"){
			$this->profileImage = path2url($this->uploadProfileImage());
			$this->padImage = path2url($this->uploadPadImage());
			$this->clinicImage = path2url($this->uploadClinicImage());
			/*--------------------------------*/
		}else if($device == "web"){
			$this->profileImage = $this->post(DocDb::$PHOTO);
			$this->padImage = $this->post(DocDb::$PRE_PAD);
			$this->clinicImage = $this->post(DocDb::$CLINIC_IMG);
			}

		/*------------------- Taking the input of other variables, if not posted then taking default ---------*/ 
		$this->getAllOtherInputs();
		$this->activeMR = $this->token_payload['user_id'];
		$assocChemist = $this->post(DocDb::$ASS_CHEM_ID);
		if($assocChemist != NULL && $assocChemist != "") {
			try {
				$this->assocChemist = json_decode($assocChemist, true);
			}catch (Exception $e){
				response($this,true,498,"","associated chemist JSONException : ".$e->getMessage());
			}
		}
		/* Trying adding the doctor profile into the database ----------*/
		try{
			// TODO:: Add here the sql to add this doc_id in the MRHistory_KeyMap
			$doc_id = $this->doc_->addProfile($this->name,$this->phone,$this->specialization,$this->geotags,$this->profileImage,
				$this->email,$this->activeMR,$this->padImage,$this->DOB,$this->anniversary,$this->visitDay,$this->meetingTime,
				$this->setNo,$this->clinicImage,$this->class,$this->MRCore,$this->AMCore,$this->RMCore,$this->inactiveDate,
				$this->assistantPhone,$this->officePhone,$this->visitFrequency,$this->monthlyBusiness,$this->patFrequency,
				$this->stationName,$this->qualification,$this->sex,$this->assocChemist);
			$data['doc_id'] = $doc_id;
			$data[DocDb::$PHOTO] = $this->profileImage;
			$data[DocDb::$PRE_PAD] = $this->padImage;
			$data[DocDb::$CLINIC_IMG] = $this->clinicImage;
			$data['msg'] = "Doctor Profile Added Successfully";
			response($this,true,200,$data);
			}
		catch(Exception $e){
			// Delete the already uploaded files, if uploaded.
			unlink($this->padImage);
			unlink($this->profileImage);
			unlink($this->clinicImage);
			response($this,false,412,"",$e->getMessage());
			}
		}
		// TODO :: for pic update from mobile it should be synchronous.
	public function uploadProfileImage(){
		$profileImage = "";
		if(isset($_FILES[DocDb::$PHOTO])){
			if(file_exists($_FILES[DocDb::$PHOTO]['tmp_name']) && is_uploaded_file($_FILES[DocDb::$PHOTO]['tmp_name'])) {
				try{
					$profileImage = upload_pic($this,DocDb::$PHOTO,'./uploads/Doctor/');
				}
				catch(Exception $e){
					//response($this,false,415,"","Doctor Image Upload Error: ".$e->getMessage());  // 415 -> Unsupported Media Type
				}
			}
		}
		return $profileImage;
	}
	public function uploadPadImage(){
		$padImage = "";
		if(isset($_FILES[DocDb::$PRE_PAD])){
			if(file_exists($_FILES[DocDb::$PRE_PAD]['tmp_name']) && is_uploaded_file($_FILES[DocDb::$PRE_PAD]['tmp_name'])) {
				try{
					$padImage = upload_pic($this,DocDb::$PRE_PAD,'./uploads/Doctor/');
				}
				catch(Exception $e){
				}
			}
		}
		return $padImage;
	}
	public function uploadClinicImage(){
		$clinicImage = "";
		if(isset($_FILES[DocDb::$CLINIC_IMG])){
			if(file_exists($_FILES[DocDb::$CLINIC_IMG]['tmp_name']) && is_uploaded_file($_FILES[DocDb::$CLINIC_IMG]['tmp_name'])) {
				try{
					$clinicImage = upload_pic($this,DocDb::$CLINIC_IMG,'./uploads/Doctor/');
				}
				catch(Exception $e){
				}
			}
		}
		return $clinicImage;
	}
	public function edit_post(){
		// TODO: AuthorizationService Implement karni hai for different roles and permission.


		// TODO:: Don't store absolute path of file in the database
		$this->docId = $this->post('doc_id');
		if($this->docId == NULL || $this->docId == "" ){
			response($this,false,409,"","Please provide doctor id to perform the function.");
			}
		if(!$this->doc_->isAssoc($this->docId,$this->token_payload['user_id'])){
			response($this,false,405,"","You are not the Active MR for this doctor profile.");
			}
		$response_data = array();
		$this->checkPrimaryConditionsForEditOrAdd();
		/*if($this->isDuplicate($this->name,$this->phone)){
			response($this,true,403,"","Same Doctor Name and Phone already exists.");
		}*/
		$device = $this->token_payload["device"];
		if($device == "web"){
			// TODO :: Add Doctor and Chemist too to the hierarchy structure. Also edit the schema a little bit.
			$this->profileImage = $this->post(DocDb::$PHOTO);
			$this->padImage = $this->post(DocDb::$PRE_PAD);
			$this->clinicImage = $this->post(DocDb::$CLINIC_IMG);
		}else if($device == "android"){
			$this->profileImage = path2url($this->uploadProfileImage());
			$this->padImage = path2url($this->uploadPadImage());
			$this->clinicImage = path2url($this->uploadClinicImage());
			if($this->profileImage == "" || $this->profileImage == NULL){
				$this->profileImage = $this->doc_->getDoctor($this->docId)[DocDb::$PHOTO];
			}
			if($this->padImage == "" || $this->padImage == NULL){
				$this->padImage = $this->doc_->getDoctor($this->docId)[DocDb::$PRE_PAD];
			}
			if($this->clinicImage == "" || $this->clinicImage == NULL){
				$this->clinicImage = $this->doc_->getDoctor($this->docId)[DocDb::$CLINIC_IMG];
			}
		}else{
			$this->profileImage = $this->doc_->getDoctor($this->docId)[DocDb::$PHOTO];
			$this->padImage = $this->doc_->getDoctor($this->docId)[DocDb::$PRE_PAD];
			$this->clinicImage = $this->doc_->getDoctor($this->docId)[DocDb::$CLINIC_IMG];
		}
		$this->getAllOtherInputs();
		$this->activeMR = $this->token_payload["user_id"];
		$assocChemist = $this->post(DocDb::$ASS_CHEM_ID);
		if($assocChemist != NULL && $assocChemist != "") {
			try {
				$this->assocChemist = json_decode($assocChemist, true);
			}catch (Exception $e){
				response($this,true,498,"","Associated chemist JSONException : ".$e->getMessage());
			}
		}else{
			$this->assocChemist = $this->doc_->getAssociatedChemist($this->docId);
		}
		$this->doc_->editDocProfile($this->docId,$this->name,$this->phone,$this->specialization,$this->geotags,$this->profileImage,
			$this->email,$this->activeMR,$this->padImage,$this->DOB,$this->anniversary,$this->visitDay,$this->meetingTime,
			$this->setNo,$this->clinicImage,$this->class,$this->MRCore,$this->AMCore,$this->RMCore,$this->inactiveDate,
			$this->assistantPhone,$this->officePhone,$this->visitFrequency,$this->monthlyBusiness,$this->patFrequency,
			$this->stationName,$this->qualification,$this->sex,$this->active);
		$response_data['msg'] = "Updates are done successfully";
		$response_data[DocDb::$PHOTO] = $this->profileImage;
		$response_data[DocDb::$PRE_PAD] = $this->padImage;
		$response_data[DocDb::$CLINIC_IMG] = $this->clinicImage;
		response($this,true,200,$response_data);
		}
	public function assign_post(){
		if($this->token_payload["own"] == "Admin"){
			$docId = $this->post(DocDb::$DOC_ID);
			$setNo = $this->post(DocDb::$SET_NO);
			$MRId = $this->post(DocDb::$ACTIVE_MR);
			if($this->Set_->setExist($MRId,$setNo)){
				$this->doc_->assignDoctor($docId,$setNo,$MRId); //  ... MRHistory is also created.
				response($this,true,200,"doctor assigned to new MR");
			}else{
				response($this,false,497,"",$MRId." doesn't have any set no ".$MRId);
			}
		}else{
			response($this,false,430,"","Only Admin can access this");
		}
	}
	public function activate_post(){
		$this->docId = $this->post(DocDb::$DOC_ID);
		if(!$this->doc_->isAssoc($this->docId,$this->token_payload['user_id'])){
			response($this,false,405,"","You are not the Active MR for this doctor profile.");
		}
		$this->doc_->activateDoc($this->docId);
		response($this,true,200,"Doctor is activated.");
	}
	public function deactivate_post(){
		$this->docId = $this->post(DocDb::$DOC_ID);
		if(!$this->doc_->isAssoc($this->docId,$this->token_payload['user_id'])){
			response($this,false,405,"","You are not the Active MR for this doctor profile.");
		}
		$this->doc_->deactivateDoc($this->docId);
		response($this,true,200,"Doctor is deactivated.");
	}

	public function associatedChemist_get(){
		$MRId = $this->token_payload["user_id"];
		$docId = $this->post(DocDb::$DOC_ID);
		response($this,true,200,$this->doc_->getAssociatedChemist($docId));
	}
	public function profile_get(){
		$doc_id = $this->get(DocDb::$DOC_ID);
//		$mr_id = $this->getMRId();
//		if (!$this->doc_->isAssoc($doc_id, $mr_id)){
//			response($this, false, 405, "", "You are not the Active MR for this doctor profile.");
//		}
		if (!isStringEmpty($doc_id)){
			$data = $this->doc_->getDoctor($doc_id);
			response($this, true, 200, $data);
		}else{
			response($this,false,481, "","Please provide a doctor ID to get the regarding info");
		}
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
	public function profiles_get(){
		$mr_id = $this->getMRId();
		response($this,true,200,$this->doc_->getAllDoctor($mr_id));
	}
}
// Relax some conditions in the code. Please no one is gonna build a CURL command to attack you ... All actions will be coming through apps which can be checked on
