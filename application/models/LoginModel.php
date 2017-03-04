<?php
require_once "EmployeeDb.php";
require_once "PersonProfileDb.php";
class LoginModel extends CI_Model{
	
	public function __construct(){   
		parent::__construct();
		$this->load->database();
    }
    public function index(){
		
		} 
    public function getProfile($user_id,$password){

        // Find Profile in which both person and employee profile is active
			// TODO: Send the personal profile info too with Employee Profile info
        $queryString = "SELECT * FROM ".EmployeeDb::$TABLE.",".PersonDb::$TABLE.
		" WHERE ".EmployeeDb::$TABLE.".".EmployeeDb::$USER_ID."=? ".
		" AND ".EmployeeDb::$TABLE.".".EmployeeDb::$PERSON_ID."=".PersonDb::$TABLE.".".PersonDb::$PERSON_ID.
		" AND ".EmployeeDb::$TABLE.".".EmployeeDb::$ACTIVE."=1 ";
		$query = $this->db->query($queryString,array($user_id));

		//  Check if Profile exists and check password.

		if($query->num_rows() == 0){
			return null;
			}
		else{
            $row = $query->row_array();
			if($password == $row['password']){
				unset($row[PersonDb::$PASSWORD]);
				unset($row[PersonDb::$LAST_CREDENTIAL_UPDATE_TIME]);
				//unset($row['person_id']);
				return $row;
				}
			else{
				return null;
				}
			}
		}
}
