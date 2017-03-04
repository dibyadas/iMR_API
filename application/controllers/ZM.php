<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require(APPPATH.'libraries/REST_Controller.php');
require(APPPATH.'helpers/response.php');
require(APPPATH.'helpers/EmptyString.php');
require(APPPATH.'helpers/authenticate.php');
require_once(APPPATH.'libraries/jwt_helper.php');
require_once (APPPATH.'helpers/PersonHistory.php');
require_once (APPPATH.'helpers/headHistory.php');
require_once (APPPATH.'controllers/Employee.php');
class ZM extends Employee {
    public function __construct(){
        parent::__construct();
    }
    public function add_post(){
        $newZMId = $this->post(EmployeeDb::$USER_ID);
        $HQ = $this->post(EmployeeDb::$HQ);
        $assignedPersonId = $this->post(EmployeeDb::$PERSON_ID);
        // TODO : Ask Khirwal to send me hid role so i can cross-check intented role and shouldberole
        $role = "ZM";
        $headRole = "MSD";
        // TODO: in roles which have heads ..headId should be checked if it is appropriate head.
        $headId = $this->post(EmployeeDb::$HEAD_ID);
        $success = $this->addNewEmployeeProfile($newZMId, $HQ, $role, $assignedPersonId, $headRole, $headId);
        if($success == FALSE){
            response($this,false,412,"","Database Error : ZM Add");
        }else{
            response($this,true,200,"ZM Added successfully");
        }
    }

    public function edit_post(){
        // TODO: Remember when you will be allowing editing other resources it should be taken care that token
        // TODO: holder should be able to edit only his resources or allowed resources.
        $ZMId = $this->post(EmployeeDb::$USER_ID);
        $HQ = $this->post(EmployeeDb::$HQ);
        $personId  = $this->post(EmployeeDb::$PERSON_ID);
        $headId = $this->post(EmployeeDb::$HEAD_ID);
        $success = $this->editEmployeeProfile($ZMId,$HQ,$personId,$headId);
        if($success  == TRUE){
            response($this,true,200,"ZM Edited Successfully");
        }else{
            response($this,false,430,"Database Error : ZM edit");
        }
    }

    public function activeProfiles_get(){
		$role = "ZM";
		response($this,true,200,$this->getActiveProfiles($role));
    }

    public function profiles_get(){
		$role = "ZM";
		response($this,true,200,$this->getProfiles($role));
    }

	public function profile_get(){
		$ZMId = $this->get(EmployeeDb::$USER_ID);
		$role = "ZM";
		$profile = $this->getProfile($ZMId,$role);
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