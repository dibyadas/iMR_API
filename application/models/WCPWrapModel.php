<?php

/**
 * Created by PhpStorm.
 * User: sahil333
 * Date: 30/1/17
 * Time: 7:04 PM
 */
require_once "WCPWrapDb.php";
class WCPWrapModel extends CI_Model {
	public function __construct(){
		parent::__construct();
		$this->load->database();
	}

	public function addWCPWrap($MRId,$month,$year){
		$data = array(
			WCPWrapDb::$MR_ID=>$MRId,
			WCPWrapDb::$MONTH=>$month,
			WCPWrapDb::$YEAR=>$year
		);
		$this->db->insert(WCPWrapDb::$TABLE,$data);
		return $this->db->insert_id();
	}

	public function getWCPWrap($WCPWrapId){
		$query = $this->db->get_where(WCPWrapDb::$TABLE,array(WCPWrapDb::$WCP_WRAP_ID=>$WCPWrapId));
		return $query->row_array();
	}


	public function getWCPWrapOfMR($MRId){
		$query = $this->db->get_where(WCPWrapDb::$TABLE,array(WCPWrapDb::$MR_ID=>$MRId));
		return $query->result_array();
	}

	public function approveWCP($WCPWrapId,$approvedBy){
		$data = array(
			WCPWrapDb::$APPROVAL_STATUS=>1,
			WCPWrapDb::$SUBMIT_STATUS=>1,
			WCPWrapDb::$IS_EXCEPTED=>0,
			WCPWrapDb::$APPROVED_BY=>$approvedBy
		);
		$this->db->update(WCPWrapDb::$TABLE,$data,array(WCPWrapDb::$WCP_WRAP_ID=>$WCPWrapId));
	}

	public function submitWCP($WCPWrapId){
		$data = array(
			WCPWrapDb::$APPROVAL_STATUS=>0,
			WCPWrapDb::$SUBMIT_STATUS=>1,
		);
		$this->db->update(WCPWrapDb::$TABLE,$data,array(WCPWrapDb::$WCP_WRAP_ID=>$WCPWrapId));
	}

	public function unsubmitWCP($WCPWrapId){
		$data = array(
			WCPWrapDb::$APPROVAL_STATUS=>0,
			WCPWrapDb::$SUBMIT_STATUS=>0,
		);
		$this->db->update(WCPWrapDb::$TABLE,$data,array(WCPWrapDb::$WCP_WRAP_ID=>$WCPWrapId));
	}

	public function exceptionEditWCP($WCPWrapId){
		$data = array(
			WCPWrapDb::$IS_EXCEPTED=>1,
			WCPWrapDb::$APPROVAL_STATUS=>0,
			WCPWrapDb::$SUBMIT_STATUS=>0,
		);
		$this->db->update(WCPWrapDb::$TABLE,$data,array(WCPWrapDb::$WCP_WRAP_ID=>$WCPWrapId));
	}
}