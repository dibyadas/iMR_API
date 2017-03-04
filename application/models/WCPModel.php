<?php

/**
 * Created by PhpStorm.
 * User: sahil333
 * Date: 9/1/17
 * Time: 2:15 PM
 */
require_once "WCPDb.php";
class WCPModel extends CI_Model{
    public function __construct(){
        parent::__construct();
        $this->load->database();
    }
	public function addWCPMain($WCPWrapId,$type,$dot,$docId){
    	$data = array(
    		WCPDb::$TYPE=>$type,
    		WCPDb::$DOT=>$dot,
    		WCPDb::$DOC_ID=>$docId,
    		WCPDb::$WCP_WRAP_ID=>$WCPWrapId,
		);
    	$this->db->insert(WCPDb::$TABLE,$data);
    	return $this->db->insert_id();
	}

	public function editWCPMain($WCPId,$type,$dot){
		$data = array(
			WCPDb::$TYPE=>$type,
			WCPDb::$DOT=>$dot,
			);
		$this->db->update(WCPDb::$TABLE,$data,array(WCPDb::$WCP_ID=>$WCPId));
//		return $this->db->insert_id();
	}

	public function getWCPOfWrap($WCPWrapId){
		$query = $this->db->get_where(WCPDb::$TABLE,array(WCPDb::$WCP_WRAP_ID=>$WCPWrapId));
		return $query->result_array();
	}
}