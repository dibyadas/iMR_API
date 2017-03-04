<?php
require_once "SetDb.php";
class SetModel extends CI_Model{
	
	public function __construct(){   
		parent::__construct();
		$this->load->database();
    }
    public function addNewSet($MRId, $setNo, $station){
		$queryString = "INSERT INTO ".SetDb::$TABLE." (".SetDb::$MR_ID.",".SetDb::$SET_NO.",".SetDb::$STATION.") VALUES (?,?,?)";
		$query = $this->db->query($queryString,array($MRId,$setNo,$station));
	}
	public function getSets($MRId){
		$queryString = "SELECT ".SetDb::$SET_NO.",".SetDb::$STATION." FROM ".SetDb::$TABLE." WHERE ".SetDb::$MR_ID."=?";
		$query = $this->db->query($queryString,array($MRId));
		$result = $query->result_array();
		return $result;
	}
	public function editSet($MRId, $setNo, $station){
	    $queryString = "UPDATE ".SetDb::$TABLE." SET ".SetDb::$STATION."=? WHERE ".SetDb::$MR_ID."=? AND ".SetDb::$SET_NO."=?";
	    $query = $this->db->query($queryString,array($station,$MRId,$setNo));
    }
	public function setExist($MRId, $setNo){
		$queryString = "SELECT * FROM ".SetDb::$TABLE." WHERE ".SetDb::$MR_ID."=? AND ".SetDb::$SET_NO."=?";
		$query = $this->db->query($queryString,array($MRId,$setNo));
		if($query->num_rows()==1)return true;
		else return false;
	}
}
