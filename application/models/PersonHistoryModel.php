<?php
require_once "PersonHistoryDb.php";
require_once APPPATH."helpers/PersonHistory.php";
class PersonHistoryModel extends CI_Model{
    public function __construct(){
        parent::__construct();
        $this->load->database();
    }
    public function addPersonHistory($userId,$personId){
        $time = Date("m/d/Y H:i:s");
        $data =array(
            PersonHistoryDb::$USER_ID => $userId,
            PersonHistoryDb::$PERSON_ID=> $personId,
            PersonHistoryDb::$DATE => $time
        );
        $this->db->insert(PersonHistoryDb::$TABLE,$data);
    }

    public function getPersonHistory($userId){
        $queryString = "SELECT * FROM ".PersonHistoryDb::$TABLE." WHERE ".PersonHistoryDb::$USER_ID."=?";
        $query = $this->db->query($queryString,array($userId));
        $result = sortByDate($query->result_array());
        return $result;
    }

    public function getLastNPersons($userId,$n){
        $queryString = "SELECT * FROM ".PersonHistoryDb::$TABLE." WHERE ".PersonHistoryDb::$USER_ID."=?";
        $query = $this->db->query($queryString,array($userId));
        $personHistory = sortByDate($query->result_array());
        $lastNHistory = array_slice($personHistory,0,$n);
        return $lastNHistory;
    }
    public function getPersonHistorySince($userId,$date){
        $queryString  = "SELECT * FROM ".PersonHistoryDb::$TABLE." WHERE ".PersonHistoryDb::$USER_ID."=?";
        $query = $this->db->query($queryString,array($userId));
        $personHistory = getHistoryAfter($query->result_array(),$date);
        return $personHistory;
    }
}