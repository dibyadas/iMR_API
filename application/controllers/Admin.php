<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require(APPPATH.'libraries/REST_Controller.php');
require(APPPATH.'helpers/response.php');
require(APPPATH.'helpers/EmptyString.php');
require(APPPATH.'helpers/authenticate.php');
require_once(APPPATH.'libraries/jwt_helper.php');
require_once (APPPATH.'helpers/PersonHistory.php');
require_once (APPPATH."controllers/Employee.php");
class Admin extends Employee {
	public function __construct(){
		parent::__construct();
//		print_r($this->getAllAccessibleProfiles("ADMIN1"));
//		response($this,true,200,$this->Employee_->getHeadHierarchy("ADMIN1"));
//		exit();
//		response($this,true,200,$this->getAllAccessibleProfiles("MSD1"));
		// Load AdminModel
//		$this->load->model('AdminModel',"Admin_");
		if($this->token_payload['own'] != "Admin"){
			response($this,false,430,"","You are not allowed for this action");
		}
	}

	public function add_post(){
		// Get new Admin ID, HQ and assigned person
		$newAdminId = $this->post(EmployeeDb::$USER_ID);
		$HQ = $this->post(EmployeeDb::$HQ);
		$assignedPersonId = $this->post(EmployeeDb::$PERSON_ID);
		// TODO : Ask Khirwal to send me head role so i can cross-check intentRole and shouldBeRole
		$role = "Admin";
		$headRole = "DUMMY";
		// TODO: in roles which have heads ..headId should be checked if it is appropriate head.
		$headId = "DUMMY";
		$success = $this->addNewEmployeeProfile($newAdminId, $HQ, $role, $assignedPersonId, $headRole, $headId);
		if($success == FALSE){
			response($this,false,412,"","Database Error : Admin Add");
		}else{
			response($this,true,200,"Admin Added successfully");
		}
	}
	public function edit_post(){
		// Ask for the Admin Id whose data needs to be changed. It can be tokenholder's own ID or some other Admin ID.

		// TODO: Remember when you will be allowing editing other resources it should be taken care that token
		// TODO: holder should be able to edit only his resources or allowed resources.
		$adminId = $this->post(EmployeeDb::$USER_ID);
		$HQ = $this->post(EmployeeDb::$HQ);
		// TODO: Check With sajal if an admin can edit the profile of an admin to be assigned to another person.
//        $personId  = $this->post(EmployeeDb::$PERSON_ID);
		$personId = $this->getAssignedPersonId($adminId);
		$headId = NULL;
		$success = $this->editEmployeeProfile($adminId,$HQ,$personId,$headId);
		if($success  == TRUE){
			response($this,true,200,"Admin Edited Successfully");
		}else{
			response($this,false,430,"Database Error : Admin edit");
		}

	}
	public function activate_post(){
		$this->activateProfile($this->post(EmployeeDb::$USER_ID));
		response($this,true,200,"Profile activated successfully");
	}
	public function activeProfiles_get(){
		$role = "Admin";
		response($this,true,200,$this->getActiveProfiles($role));
	}
	public function profile_get(){
		$adminId = $this->get(EmployeeDb::$USER_ID);
		$role = "Admin";
		$profile = $this->getProfile($adminId,$role);
		response($this,true,200,$profile);
	}

	public function profiles_get(){
		$role = "Admin";
		response($this,true,200,$this->getProfiles($role));
	}
	public function deactivate_post(){
		$userId = $this->post(EmployeeDb::$USER_ID);
		$this->deactivateProfile($userId);
		response($this,true,200,"Profile deactivated successfully");
	}
}
