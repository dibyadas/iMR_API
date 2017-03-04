<?php

/**
 * Created by PhpStorm.
 * User: sahil333
 * Date: 23/2/17
 * Time: 5:45 PM
 */
require_once ("ChemVisitDb.php");
class ChemVisitModel extends CI_Model {
	public function __construct(){
		parent::__construct();
		$this->load->database();
	}

	public function getVisitsOfPID($pId){
		$query = $this->db->get_where(ChemVisitDb::$TABLE,array(ChemVisitDb::$P_ID=>$pId));
		return $query->result_array();
	}
}