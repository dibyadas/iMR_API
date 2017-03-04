<?php

/**
 * Created by PhpStorm.
 * User: sahil333
 * Date: 14/1/17
 * Time: 11:42 PM
 */
require_once "MRHistoryDb.php";
class MRHistoryModel extends CI_Model {
    public function __construct(){
        parent::__construct();
        $this->load->database();
    }
	public function addMRHistory($childId,$role,$userId){
//		$childKeyMapModel = new ChildKeyMapModel();
//		$childKey = $childKeyMapModel->getChildKey($childId,$role);
		$time = Date("m/d/Y H:i:s");
		$data =array(
			MRHistoryDb::$CHILD_ID=>$childId,
			MRHistoryDb::$ROLE=>$role,
			MRHistoryDb::$USER_ID=> $userId,
			MRHistoryDb::$DATE => $time
		);
		$this->db->insert(MRHistoryDb::$TABLE,$data);
	}

    public function getMRHistory($childId,$role){
//		$childKeyMapModel = new ChildKeyMapModel();
//		$childKey = $childKeyMapModel->getChildKey($childId,$role);
		$queryString = "SELECT * FROM ".MRHistoryDb::$TABLE." WHERE ".MRHistoryDb::$CHILD_ID."=? AND ".MRHistoryDb::$ROLE."=?";
        $query = $this->db->query($queryString,array($childId,$role));
        $result = sortByDate($query->result_array());
        return $result;
    }

    public function getLastNMR($childId,$role, $n){
//        $queryString = "SELECT * FROM ".MRHistoryDb::$TABLE." WHERE ".MRHistoryDb::$CHILD_KEY."=? LIMIT $n";
//		$childKeyMapModel = new ChildKeyMapModel();
//		$childKey = $childKeyMapModel->getChildKey($childId,$role);
        $query = $this->db->get_where(MRHistoryDb::$TABLE,array(MRHistoryDb::$CHILD_ID=> $childId, MRHistoryDb::$ROLE=>$role));
//        $query = $this->db->query($queryString,array($childKey));
        $MRHistory = sortByDate($query->result_array());
        $lastNHistory = array_slice($MRHistory,0,$n);
        return $lastNHistory;
    }
    public function getMRHistorySince($childId,$role,$date){
//		$childKeyMapModel = new ChildKeyMapModel();
//		$childKey = $childKeyMapModel->getChildKey($childId,$role);
        $queryString  = "SELECT * FROM ".MRHistoryDb::$TABLE." WHERE ".MRHistoryDb::$CHILD_KEY."=? AND ".MRHistoryDb::$ROLE."=?";
        $query = $this->db->query($queryString,array($childId,$role));
        $MRHistory = getHistoryAfter($query->result_array(),$date);
        return $MRHistory;
    }
}