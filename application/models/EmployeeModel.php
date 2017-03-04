<?php

/**
 * Created by PhpStorm.
 * User: sahil333
 * Date: 15/1/17
 * Time: 5:30 PM
 */
require_once "EmployeeDb.php";
class EmployeeModel extends CI_Model {
    public function __construct(){
        parent::__construct();
        $this->load->database();
    }
    public function addEmployee($userId,$HQ,$role,$personId,$headRole,$headId,$active){
        $createDate = date('m/d/Y H:i:s');
    	if($active == 0 || $active == false)
    		$inactiveDate = $createDate;
    	else $inactiveDate = NULL;
        $data = array(
            EmployeeDb::$USER_ID => $userId ,
            EmployeeDb::$HQ => $HQ ,
            EmployeeDb::$ROLE => $role ,
            EmployeeDb::$PERSON_ID => $personId,
            EmployeeDb::$HEAD_ROLE => $headRole ,
            EmployeeDb::$HEAD_ID => $headId ,
			EmployeeDb::$CREATE_DATE => $createDate,
			EmployeeDb::$INACTIVE_DATE=>$inactiveDate,
            EmployeeDb::$ACTIVE => $active
        );
//		$this->db->trans_start();
        $this->db->insert(EmployeeDb::$TABLE,$data);
//		$this->db->trans_complete();
//		if($this->db->trans_status() == false){
//			//$this->db->trans_rollback();
////			print_r($this->db->_error_message());
//			print_r($this->db->_error_message());
//		}
    }

    public function editEmployee($userId, $HQ ,$personId , $headId , $active){
		if($active == 0 || $active == false)
			$inactiveDate = date('m/d/Y H:i:s');
		else $inactiveDate = NULL;
    	$data = array(
            EmployeeDb::$PERSON_ID => $personId,
            EmployeeDb::$HEAD_ID => $headId,
            EmployeeDb::$ACTIVE => $active,
			EmployeeDb::$INACTIVE_DATE=>$inactiveDate,
            EmployeeDb::$HQ => $HQ
        );
        $this->db->update(EmployeeDb::$TABLE,$data,array(EmployeeDb::$USER_ID=>$userId));
    }
    public function isExist($possibleUniqueId){
        $query = $this->db->get_where(EmployeeDb::$TABLE,array(EmployeeDb::$USER_ID=>$possibleUniqueId));
        if($query->num_rows() == 0){
            return false;
        }else{
            return true;
        }
    }

    public function getEmployee($userId){
        $query = $this->db->get_where(EmployeeDb::$TABLE,array(EmployeeDb::$USER_ID => $userId));
        return $query->row_array();
    }

    public function activateProfile($userId){
        $data = array(
            EmployeeDb::$ACTIVE=>1
        );
        $this->db->update(EmployeeDb::$TABLE,$data,array(EmployeeDb::$USER_ID=>$userId));
    }

    public function deactivateProfile($userId){
        date_default_timezone_set("Asia/Kolkata");
        $inactiveDate = date("m/d/Y H:i:s");
    	$data = array(
            EmployeeDb::$ACTIVE=>0,
			EmployeeDb::$INACTIVE_DATE=>$inactiveDate
        );
        $this->db->update(EmployeeDb::$TABLE,$data,array(EmployeeDb::$USER_ID=>$userId));
    }

    public function isPersonAssigned($personId){
        if($personId == NULL ) return false;
    	$query = $this->db->get_where(EmployeeDb::$TABLE,array(EmployeeDb::$PERSON_ID=>$personId));
        if($query->num_rows() == 0){
            return false;
        }else if($query->num_rows() == 1){
            return true;
        }else{
            throw new Exception("Same Person assigned ".$query->num_rows()." company profiles");
        }
    }

	public function getUserByPersonId($personId){
    	if($this->isPersonAssigned($personId)) {
			$query = $this->db->get_where(EmployeeDb::$TABLE, array(EmployeeDb::$PERSON_ID => $personId));
			return $query->row_array();
    		}
	}

	public function getActivePersonId($userId){
		$query = $this->db->get_where(EmployeeDb::$TABLE,array(EmployeeDb::$USER_ID=>$userId,EmployeeDb::$ACTIVE=>1));
		if($query->num_rows() == 0) {
			return true;
		}else{
			return $query->row_array()[EmployeeDb::$PERSON_ID];
		}
	}
	public function isActive($userId){
		$query = $this->db->get_where(EmployeeDb::$TABLE,array(EmployeeDb::$USER_ID=>$userId,EmployeeDb::$ACTIVE=>1));
		if($query->num_rows() == 0){
			return false;
		}else{
			return true;
		}
	}

	public function getHeadHierarchy($userId){
		$userIdRole = $this->getEmployee($userId)[EmployeeDb::$ROLE];
		if($userIdRole == "Admin"){
			$userProfile = $this->getEmployee($userId);
			$userProfile["head_node"] = array();
//			array_push($headNode,$userProfile);
			return $userProfile;
		}
		elseif($userIdRole == "MSD" ||  $userIdRole == "ZM" ||$userIdRole == "RM" ||$userIdRole == "AM" ||$userIdRole == "TM" || $userIdRole == "MR"){
//			$headNode = array();
			$userProfile = $this->getEmployee($userId);
			$userProfile["head_node"] = $this->getHeadHierarchy($userProfile[EmployeeDb::$HEAD_ID]);
//			array_push($headNode,$userProfile);
			return $userProfile;
		}
	}

	public function updateHead($headId,$userId){
		$employeeData = array(
			EmployeeDb::$HEAD_ID => $headId
		);
		$this->db->update(EmployeeDb::$TABLE,$employeeData,array(EmployeeDb::$USER_ID=>$userId));
	}
}