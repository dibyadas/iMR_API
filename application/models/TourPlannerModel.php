<?php

require_once BASEPATH.'core/Model.php';

class TourPlannerModel extends CI_Model {

	public $query, $query_string;

	public function __construct(){
		parent::__construct();
		$this->load->database();
	}

	public function get_tour_details($user_id,$tour_month,$tour_year){
		$this->query_string  = "SELECT * FROM `Tour_Plan` WHERE `user_id` = ? AND `tour_month` = ? AND `tour_year` = ?";
		$this->query = $this->db->query($this->query_string,array($user_id,$tour_month,$tour_year));
		$resp = $this->query->result_array();	
		return $resp;
		
	}

	public function _get_profile($user_id){
		$this->query_string = "SELECT * FROM `Employee_profile` WHERE `user_id` = ?";
        $this->query = $this->db->query($this->query_string,array($user_id));
        return $this->query->result_array();
    }

	public function check_hierarchy($user_id,$target_user_id){  // returns true if user_id is head of target_user_id

		if((string)array_search($user_id,$this->get_hierarchy($target_user_id)) != (string)false)
		{
			return true;
		}
		else{
			return false;
		}
	}

	public function get_hierarchy($user_id,$hierarchy = null){  // this func returns the array containing ids of the heads
		if($hierarchy == null){
			$hierarchy = array();
		}
		$temp = $this->_get_profile($user_id)[0]['head_id'];
		if($temp == "DUMMY")
		{
			return $hierarchy;
		}
		else{
			array_push($hierarchy,$temp);
			return $this->get_hierarchy($temp,$hierarchy);
		}

	}


	public function update_status($tour_month,$tour_year,$status,$target_user_id){
		$this->query_string = "UPDATE `Tour_Plan` SET `approval_status` = ? WHERE `user_id` = ? AND `tour_month` = ? AND `tour_year` = ?";
		$this->query = $this->db->query($this->query_string,array($status,$target_user_id,$tour_month,$tour_year));
		return (bool)($this->db->affected_rows() > 0); // check if the number of rows affected is atleast 1 so that the query succeeded
	}

	public function fetch_edit_access($user_id,$tour_month,$tour_year){
		$this->query_string = "SELECT `edit_access` FROM `Tour_Plan` WHERE `user_id` = ? AND `tour_month` = ? AND `tour_year` = ?";
		$this->query = $this->db->query($this->query_string,array($user_id,$tour_month,$tour_year));
		$result = $this->query->result_array();
		if(isset($result[0])){ 
			if($result[0]['edit_access'] == '0'){
				return false;
			}
			elseif($this->query->result_array()[0]['edit_access'] == '1'){
				return true;
			}
		}else{   // if no such plan exists
			return false;
		}
	}

	public function change_edit_access($user_id,$tour_month,$tour_year,$access){
		if($access == 1 || $access == 0){
			$this->query_string = "UPDATE `Tour_Plan` SET `edit_access` = ? WHERE `user_id` = ? AND `tour_month` = ? AND `tour_year` = ?";
			$this->query = $this->db->query($this->query_string,array((string)$access,$user_id,$tour_month,$tour_year));
			return (bool)($this->db->affected_rows() > 0);
		}
		else{
			return false;
		}
	}

	public function change_tour_plan($tour_month,$tour_year,$tour_plan,$target_user_id){
			$this->query_string = "UPDATE `Tour_Plan` SET `tour_plan` = ? WHERE `user_id` = ? AND `tour_month` = ? AND `tour_year` = ?";
			$this->query  = $this->db->query($this->query_string,array($tour_plan,$target_user_id,$tour_month,$tour_year));
			return (bool)($this->db->affected_rows() > 0);
	}


	public function set_tour_details($user_id,$tour_month,$tour_year,$tour_plan,$status){

			$this->query_string = "INSERT INTO `Tour_Plan` (`user_id`,`tour_month`,`tour_year`,`tour_plan`,`approval_status`)
									VALUES (?,?,?,?,?)";
			$this->query = $this->db->query($this->query_string,array($user_id,$tour_month,$tour_year,$tour_plan,$status));
			return (bool)($this->db->affected_rows() > 0);
	}

}

?>