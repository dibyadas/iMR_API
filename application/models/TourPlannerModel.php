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

	public function _get_profile($user_id){
		$this->query_string = "SELECT * FROM `Employee_profile` WHERE `user_id` = ?";
        $this->query = $this->db->query($this->query_string,array($user_id));
        return $this->query->result_array();
    }

	public function check_heirarchy($user_id,$target_user_id){  // returns true if user_id is head of target_user_id
		if($this->_get_profile($target_user_id)[0]['head_id'] == $user_id)
		{
			return true;

		}
		else{
			return false;
		}
	}	


	public function update_status($user_id,$status,$target_user_id){
		if($this->check_heirarchy($user_id,$target_user_id)){
			$this->query_string = "UPDATE `Tour_Plan` SET `status` = ? WHERE `user_id` = ?";
			$this->query = $this->db->query($this->query_string,array($status,$target_user_id));
			return true;
		}
		else{
			return false;
		}
	}

	public function change_tour_plan($user_id,$tour_month,$tour_plan,$target_user_id){
		if($this->check_heirarchy($user_id,$target_user_id)){
			$this->query_string = "UPDATE `Tour_Plan` SET `tour_plan` = ? WHERE `user_id` = ? AND `tour_month` = ?";
			$this->query  = $this->db->query($this->query_string,array($tour_plan,$target_user_id,$tour_month));
			return true;
		}
		else{
			return false;
		}
	}

	public function set_tour_details($user_id,$tour_month,$tour_plan,$status,$level){
			$this->query_string = "INSERT INTO `Tour_Plan` (`user_id`,`tour_month`,`tour_plan`,`status`,`level`)
									VALUES (?,?,?,?,?)";
			$this->query = $this->db->query($this->query_string,array($user_id,$tour_month,$tour_plan,$status,$level));
			return true;
	}

}

?>