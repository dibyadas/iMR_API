<?php

/**
 * Created by PhpStorm.
 * User: sahil333
 * Date: 15/1/17
 * Time: 4:39 PM
 */
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH.'libraries/REST_Controller.php');
require_once(APPPATH.'helpers/response.php');
require_once(APPPATH.'helpers/EmptyString.php');
require_once(APPPATH.'helpers/ErrorCollector.php');
require_once(APPPATH.'helpers/authenticate.php');
require_once(APPPATH.'libraries/jwt_helper.php');
class Employee extends REST_Controller{
	protected $token_payload;
	public function __construct(){
		parent::__construct();

		/* PersonModel and UserIdModel both should always be loaded in every Rest_Controller extended class */
		$this->load->model('PersonModel',"Person_");

		// Load EmployeeModel
		$this->load->model('EmployeeModel',"Employee_");
		$this->load->model('PersonHistoryModel',"PH_");
		$this->load->model('HeadHistoryModel',"HH_");
		try{

			// Authenticate User with the token
			$this->token_payload = authenticate($this);

			// Admin Rersources can only be accessed by Admin. So check for token own here itself.
//            if($this->token_payload['own'] != "Admin"){
//                response($this,false,430,"","You are not allowed for this action");
//            }
		}
		catch(Exception $e){
			response($this,false,401,"",$e->getMessage());		// 401 -> invalid token
		}

	}

	protected function isIdUnique($possibleUniqueId){
		if($possibleUniqueId == NULL){return false;}
		return !$this->Employee_->isExist($possibleUniqueId);
	}

	protected function addNewEmployeeProfile($userId,$HQ,$role/*Set in the corresponding controller */,
											 $personId,$headRole,$headId){
		if($this->token_payload["own"] == "Admin" ) {

			if ($personId != "" and $personId != NULL) {
				$active = TRUE;
			} else {
				$active = FALSE;
				$personId = NULL;
			}
			if($HQ == NULL || $HQ == ""){
				response($this,false,496,"","HQ is necessary. Please provide it.");
			}
			try {
				if ($this->Employee_->isPersonAssigned($personId)) {
					response($this, false, 491, "", "PersonId is assigned already");
				}
			}catch (Exception $e){
				response($this,false,493,"",$e->getMessage());
			}
			if(!$this->isIdUnique($userId)){
				response($this,false,490,"","User Id exists already");
			}
			if($headId == NULL)$headId = NULL;
			if($headId != NULL && $headId != "") {
				$headId_Role = $this->Employee_->getEmployee($headId)[EmployeeDb::$ROLE] ;
				if (!$this->isHierarchyRight($role,$headId_Role )) {
					response($this,false,495,"","The provided head with role ".$headId_Role." is not this role : ".$role);
				}
			}
			$error = "";
			$this->db->trans_start();
			$this->Employee_->addEmployee($userId, ucwords($HQ), $role, $personId, $headRole, $headId, $active);
			if($this->db->trans_status() === FALSE){
				$error .= $this->db->error()["message"];
			}
			$this->HH_->addHeadHistory($userId,$headId);
			if($this->db->trans_status() === FALSE){
				$error .= $this->db->error()["message"];
			}
			$this->PH_->addPersonHistory($userId,$personId);
			if($this->db->trans_status() === FALSE){
				$error .= $this->db->error()["message"];
			}
			$this->db->trans_complete();
			if($this->db->trans_status() === FALSE){
				//print $this->db->error_message();
				response($this,false,412,"",$error);
			}else {
				return true;
			}
		}else{
			response($this,false,430,"","Only Admin can access this");
			return false;
		}
	}
	protected function editEmployeeProfile($userId,$HQ,$personId,$headId){
		if($this->token_payload["own"] == "Admin" ) {
			if ($personId != "" and $personId != NULL) {
				$active = TRUE;
			} else {
				$personId = NULL;
				$active = FALSE;
			}
			if($userId == NULL || $userId == "" || $this->isIdUnique($userId)){
				response($this,false,499,"","Please provide existing User ID");
			}
			if($headId == NULL)$headId = NULL;
			if($HQ == NULL || $HQ == ""){
				response($this,false,496,"","HQ is necessary. Please provide it.");
			}
			$currentPersonId = $this->getAssignedPersonId($userId);
//			try {
//				$this->Employee_->isPersonAssigned($personId);
//			}catch (Exception $e){
//				response($this,false,493,"",$e->getMessage());
//			}
			$personChanged = false;
			$headChanged = false;
			if( $currentPersonId != $personId){
				try {
					if ($this->Employee_->isPersonAssigned($personId)) {
						response($this, false, 491, "", "PersonId is assigned already");
					}
				}catch (Exception $e){
					// remember not to remove response .
					response($this,false,493,"",$e->getMessage());
				}
				$personChanged = true;
//				$this->PH_->addPersonHistory($userId,$personId);
//				$timeNow = microtime(true);
//				$this->Person_->setLastCredentialUpdateTime($personId,$timeNow);
//				$this->Person_->setLastCredentialUpdateTime($currentPersonId,$timeNow);
			}else{
				$personId = $currentPersonId;
			}
			$currentHeadId = $this->getAssignedHeadId($userId);
			if( $currentHeadId != $headId ){
				if($headId != NULL && $headId != "") {
					$headId_Role = $this->Employee_->getEmployee($headId)[EmployeeDb::$ROLE];
					$role = $this->Employee_->getEmployee($userId)[EmployeeDb::$ROLE];
					if (!$this->isHierarchyRight($role,$headId_Role)) {
						response($this,false,495,"","This head with role ".$headId_Role." is not appropriate role" );
					}
				}else{
					response($this,false,476,"","The Head is compulsory" );
				}
				$headChanged = true;
//				$this->HH_->addHeadHistory($userId,$headId);
			}else{
				$headId = $currentHeadId;
			}
			try {
				$this->Employee_->editEmployee($userId, ucwords($HQ), $personId, $headId, $active);
				if($personChanged){
					$this->PH_->addPersonHistory($userId,$personId);
					$timeNow = microtime(true);
					$this->Person_->setLastCredentialUpdateTime($personId,$timeNow);
					$this->Person_->setLastCredentialUpdateTime($currentPersonId,$timeNow);
				}
				if($headChanged){
					$this->HH_->addHeadHistory($userId,$headId);
				}
				return true;
			}catch (Exception $e){
				print_r($e->getMessage());
				return false;
			}catch (Error $e){
				print_r($e->getMessage());
				return false;
			}
		}else{
			response($this,false,430,"","Only Admin can access this");
			return false;
		}
	}
	protected function getActiveProfiles($role){
		$query = $this->db->get_where(EmployeeDb::$TABLE,array(EmployeeDb::$ACTIVE=>1,EmployeeDb::$ROLE=>$role));
		return $query->result_array();
	}

	protected function getProfiles($role){
//            $this->db->where(EmployeeDb::$ACTIVE."=",1);
		$query = $this->db->get_where(EmployeeDb::$TABLE,array(EmployeeDb::$ROLE=>$role));
		return $query->result_array();
	}
	protected function getAssignedPersonId($userId){
		return $this->Employee_->getEmployee($userId)[EmployeeDb::$PERSON_ID];
	}
	protected function getAssignedHeadId($userId){
		return $this->Employee_->getEmployee($userId)[EmployeeDb::$HEAD_ID];
	}

	protected function getProfilesWithHead($userId){
		$query = $this->db->get_where(EmployeeDb::$TABLE,array(EmployeeDb::$HEAD_ID=>$userId));
		return $query->result_array();
	}
	protected function activateProfile($userId){
		if($this->token_payload["own"] == "Admin"){
			$personId = $this->Employee_->getEmployee($userId)[EmployeeDb::$PERSON_ID];
			if( $personId == NULL || $personId == ""){
				response($this,false,480,"No Person Id assigned yet. Cannot be activated");
			}
			$timeNow = microtime(true);
			$this->Person_->setLastCredentialUpdateTime($this->getAssignedPersonId($userId),$timeNow);
			$this->Employee_->activateProfile($userId);
		}else{
			response($this,false,430,"","Only Admin can access this");
		}
	}

	protected function deactivateProfile($userId){
		if($this->token_payload["own"] == "Admin"){
			$timeNow = microtime(true);
			$this->Person_->setLastCredentialUpdateTime($this->getAssignedPersonId($userId),$timeNow);
			$this->Employee_->deactivateProfile($userId);
		}else{
			response($this,false,430,"","Only Admin can access this");
		}
	}

	private function isHierarchyRight($role,$headRole){
		if(($role == "MSD" && $headRole == "DUMMY") ||
			($role == "ZM" && $headRole == "MSD")||
			($role == "RM" && $headRole == "ZM")||
			($role == "AM" && $headRole == "RM")||
			($role == "TM" && $headRole == "AM")||
			($role == "MR" && $headRole == "TM")||
			($role == "Admin" && $headRole == "DUMMY")){
			return true;
		}else{
			return false;
		}
	}

	public function getAllAccessibleProfiles($requestUserID){
		if($this->Employee_->getEmployee($requestUserID)[EmployeeDb::$ROLE] == "Admin"){
			$hierarchy["Admin"] = $this->getProfiles("Admin");
//            print_r($hierarchy);
			$hierarchy["children_node"] = array();
			$MSDProfiles = $this->getProfiles("MSD");

//            print_r($MSDProfiles);
//            exit();
			foreach ($MSDProfiles as $MSD){
				$MSD["children_node"] = $this->getChildHierarchy($MSD[EmployeeDb::$USER_ID]);
				array_push($hierarchy["children_node"],$MSD);
			}
			$outerHierarchy["Hierarchy"] = $hierarchy;
			//$outerHierarchy["segmented_hierarchy"] = $this->getSegmentedProfileHierarchy();
			return $outerHierarchy;
		}else{
			$profile = $this->Employee_->getEmployee($requestUserID);
			$profile["children_node"] = $this->getChildHierarchy($requestUserID);
			$outerHierarchy["Hierarchy"] = $profile;
			return $outerHierarchy;
		}
	}
	public function getChildHierarchy($userId){
		$userIdRole = $this->Employee_->getEmployee($userId)[EmployeeDb::$ROLE];
		if( $userIdRole == "MR"){
			return null;
		}elseif($userIdRole == "MSD" || $userIdRole == "ZM" ||$userIdRole == "RM" ||$userIdRole == "AM" ||$userIdRole == "TM"){
			$childrenNode = array();
			$childProfiles = $this->getProfilesWithHead($userId);
			foreach ($childProfiles as $profile){
				$profile["children_node"] = $this->getChildHierarchy($profile[EmployeeDb::$USER_ID]);
				array_push($childrenNode,$profile);
			}
			return $childrenNode;
		}
	}

	protected function getProfile($userId,$role){
		$profile = $this->Employee_->getEmployee($userId);
		if($profile[EmployeeDb::$ROLE] == $role){
			return $profile;
		}else{
			return null;
		}
	}

	public function assignHead($userId, $headId){
		if($this->token_payload["own"] == "Admin"){
			if($this->isHierarchyRight($this->Employee_->getEmployee($userId)[EmployeeDb::$ROLE],
				$this->Employee_->getEmployee($headId)[EmployeeDb::$ROLE])){
				$errorCollector = new ErrorCollector($this);
				$this->db->trans_start();
				$this->Employee_->updateHead($headId,$userId);
				$errorCollector->collect();
				$this->HH_->addHeadHistory($userId,$headId);
				$this->db->trans_complete();
				if($this->db->trans_status == TRUE){
					response($this,true,200,"Head Assigned Successfully");
				}else{
					response($this,false,430,"Database Error : Employee Head assign ".$errorCollector->getError());
				}
			}else{
				response($this,false,497,"","Hierarchy isn't right");
			}
		}else{
			response($this,false,430,"","Only Admin can access this");
		}
	}

	public function myHierarchy_get(){
		// TODO:: 	Add accessible hierarchy code here.
		//$userId = $this->token_payload["user_id"];
		$userId = $this->get('user_id');
		response($this,true,200,$this->getAllAccessibleProfiles($userId));
	}

	public function myHeadHierarchy_get(){
		///$userId = $this->token_payload["user_id"];
		$userId = $this->get('user_id');
		response($this,true,200,$this->Employee_->getHeadHierarchy($userId));
	}

}