<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require(APPPATH.'libraries/REST_Controller.php');
require(APPPATH.'helpers/response.php');
require(APPPATH.'helpers/EmptyString.php');
require(APPPATH.'helpers/authenticate.php');
require_once(APPPATH.'libraries/jwt_helper.php');
require_once (APPPATH.'helpers/PersonHistory.php');
require_once (APPPATH.'controllers/Employee.php');
/**
 * Created by PhpStorm.
 * User: sahil333
 * Date: 22/12/16
 * Time: 10:40 PM
 */
class RM extends Employee{
    public function __construct(){
        parent::__construct();
    }

	public function add_post(){
		// TODO : look into the doctor_gift table. why don't you have an expense table?
		$newRMId = $this->post(EmployeeDb::$USER_ID);
		$HQ = $this->post(EmployeeDb::$HQ);
		$assignedPersonId = $this->post(EmployeeDb::$PERSON_ID);
		// TODO : Ask Khirwal to send me hid role so i can cross-check intented role and shouldberole
		$role = "RM";
		$headRole = "ZM";
		// TODO: in roles which have heads ..headId should be checked if it is appropriate head.
		$headId = $this->post(EmployeeDb::$HEAD_ID);
		$success = $this->addNewEmployeeProfile($newRMId, $HQ, $role, $assignedPersonId, $headRole, $headId);
		if($success == FALSE){
			response($this,false,412,"","Database Error : RM Add");
		}else{
			response($this,true,200,"RM Added successfully");
		}
	}

	public function edit_post(){
		$RMId = $this->post(EmployeeDb::$USER_ID);
		$HQ = $this->post(EmployeeDb::$HQ);
		$personId  = $this->post(EmployeeDb::$PERSON_ID);
//        $personId = $this->getAssignedPersonId($ZMId);
		$headId = $this->post(EmployeeDb::$HEAD_ID);
		$success = $this->editEmployeeProfile($RMId,$HQ,$personId,$headId);
		if($success  == TRUE){
			response($this,true,200,"RM Edited Successfully");
		}else{
			response($this,false,430,"Database Error : RM edit");
		}
	}
	public function activeProfiles_get(){
		$role = "RM";
		$requestUserId = $this->token_payload["user_id"];
		response($this,true,200,$this->getActiveProfiles($role));
	}

	public function profiles_get(){
		$role = "RM";
		$requestUserId = $this->token_payload["user_id"];
		response($this,true,200,$this->getProfiles($role));
	}

	public function profile_get(){
		$RMId = $this->get(EmployeeDb::$USER_ID);
		$role = "RM";
		$profile = $this->getProfile($RMId,$role);
		response($this,true,200,$profile);
	}

	public function activate_post(){
		$this->activateProfile($this->post(EmployeeDb::$USER_ID));
		response($this,true,200,"Profile activated successfully");
	}
	public function deactivate_post(){
		$userId = $this->post(EmployeeDb::$USER_ID);
		$this->deactivateProfile($userId);
		response($this,true,200,"Profile deactivated successfully");
	}

	public function assignHead_post(){
		$headId = $this->post(EmployeeDb::$HEAD_ID);
		$userId = $this->post(EmployeeDb::$USER_ID);
		$this->assignHead($userId,$headId);
		response($this,true,200,"Assigned to new Head");
	}
}
