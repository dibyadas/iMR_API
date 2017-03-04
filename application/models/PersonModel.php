<?php

/**
 * Created by PhpStorm.
 * User: sahil333
 * Date: 23/12/16
 * Time: 12:53 AM
 */
require_once "PersonProfileDb.php";
require_once "EmployeeDb.php";
require_once (APPPATH.'helpers/generateUniqueID.php');
class PersonModel extends CI_Model {
    public function __construct(){
        parent::__construct();
        $this->load->database();
    }

    public function getAllPersonProfiles(){
        $queryString = "SELECT * FROM ".PersonDb::$TABLE;
        $query = $this->db->query($queryString);
        return $query->result_array();
    }
    public function getPersonProfile($personId){
        $queryString = "SELECT * FROM ".PersonDb::$TABLE." WHERE ".PersonDb::$PERSON_ID."=?";
        $query = $this->db->query($queryString,array($personId));
        return $query->row_array();
    }
    public function getUnassignedPersonProfiles(){
        $queryString = "SELECT * FROM ".PersonDb::$TABLE." p WHERE NOT EXISTS(SELECT NULL FROM ".EmployeeDb::$TABLE." u WHERE u.".EmployeeDb::$PERSON_ID."=p.".PersonDb::$PERSON_ID." )";
        $query = $this->db->query($queryString);
        return $query->result_array();
    }
    public function addNewPerson($name,$email,$phone,$hashedPassword,$DOB,$profilePic,$sex){
        $personId = $this->createPersonId();
        $timeNow = microtime(true);
        $queryString = "INSERT INTO ".PersonDb::$TABLE." (".PersonDb::$PERSON_ID.",".PersonDb::$NAME.",".PersonDb::$EMAIL.",".PersonDb::$PHONE.
            ",".PersonDb::$PASSWORD.",".PersonDb::$DOB.",".PersonDb::$PROFILE_PIC.",".PersonDb::$SEX.",".PersonDb::$LAST_CREDENTIAL_UPDATE_TIME.") VALUES (?,?,?,?,?,?,?,?,?)";
        $query = $this->db->query($queryString,array($personId,$name,$email,$phone,$hashedPassword,$DOB,$profilePic,$sex,$timeNow));
    }

    public function getPerson($personId){
        $queryString = "SELECT * FROM ".PersonDb::$TABLE." WHERE ".PersonDb::$PERSON_ID."=?";
        $query = $this->db->query($queryString,array($personId));
        return $query->row_array();
    }
    public function editPerson($personId,$newName,$newEmail,$newPhone,$newDOB,$newProfilePic,$newSex){
    	$data = array(
    		PersonDb::$NAME=>$newName,
    		PersonDb::$EMAIL=>$newEmail,
    		PersonDb::$PHONE=>$newPhone,
    		PersonDb::$DOB=>$newDOB,
    		PersonDb::$PROFILE_PIC=>$newProfilePic,
    		PersonDb::$SEX=>$newSex,
		);
    	$this->db->update(PersonDb::$TABLE,$data,array(PersonDb::$PERSON_ID=>$personId));
	}

    public function getPassword($personId){
        $queryString = "SELECT ".PersonDb::$PASSWORD." FROM ".PersonDb::$TABLE." WHERE ".PersonDb::$PERSON_ID."=?";
        $query = $this->db->query($queryString,array($personId));
        return $query->row_array()[PersonDb::$PASSWORD];
    }

    public function updatePassword($personId,$newPassword){
        $queryString = "UPDATE ".PersonDb::$TABLE." SET ".PersonDb::$PASSWORD."=? , ".PersonDb::$LAST_CREDENTIAL_UPDATE_TIME."=? WHERE ".PersonDb::$PERSON_ID."=?";
        $timeNow = microtime(true);
        $query = $this->db->query($queryString,array($newPassword,$timeNow,$personId));
    }

    public function getLastCredentialUpdateTime($personId){
        $queryString = "SELECT ".PersonDb::$LAST_CREDENTIAL_UPDATE_TIME." FROM ".PersonDb::$TABLE." WHERE ".PersonDb::$PERSON_ID."=?";
        $query = $this->db->query($queryString,array($personId));
        return $query->row_array()[PersonDb::$LAST_CREDENTIAL_UPDATE_TIME];
    }
    public function setLastCredentialUpdateTime($personId,$time){
        $queryString = "UPDATE ".PersonDb::$TABLE." SET ".PersonDb::$LAST_CREDENTIAL_UPDATE_TIME."=? WHERE ".PersonDb::$PERSON_ID."=?";
        $this->db->query($queryString,array($time,$personId));
    }
    private function createPersonId(){
        $queryString = "SELECT ".PersonDb::$PERSON_ID." FROM ".PersonDb::$TABLE;
        while(1){
            $flag = 0;
            $query = $this->db->query($queryString);
            $id = generateUniqueID(10);
            $row = $query->row_array();
            while($row){
                if($row[PersonDb::$PERSON_ID] == $id){
                    $flag = 1;
                    break;
                }
                $row = $query->next_row('array');
            }
            if($flag == 1){continue;}
            else{break;}
        }
        return $id;
    }

}