<?php

require_once BASEPATH.'core/Model.php';

class TourPlannerModel extends CI_Model {

	public $query, $query_string;

	public function __construct(){
		parent::__construct();
		$this->load->database();
	}

	public function get_tour_details($user_id){
		$this->query_string  = "SELECT * FROM `Tour_Plan` WHERE `user_id` = ?";
		$this->query = $this->db->query($this->query_string,array($user_id));
		$resp = $this->query->result_array();	
		return $resp;
		
	}


	public function set_tour_details($user_id,$tour_date,$tour_plan,$status,$level){
		$this->query_string = "INSERT INTO `Tour_Plan` (`user_id`,`tour_date`,`tour_plan`,`status`,`level`) 
								VALUES (?,?,?,?,?)";
		$this->query = $this->db->query($this->query_string,array($user_id,$tour_date,$tour_plan,$status,$level));
		return "done";
	}

}

?>