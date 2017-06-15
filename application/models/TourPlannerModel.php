<?php

require_once BASEPATH.'core/Model.php';

class TourPlannerModel extends CI_Model {

	public $query, $query_string;
	public $heirarchy = ['MR','AM','RM','ZM','MSD'];

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

	public function _fetch_level($user_id){  
		$this->query_string = "SELECT `level` FROM `Tour_Plan` WHERE `user_id` = ?";
		$this->query = $this->db->query($this->query_string,array($user_id));
		return $this->query->result_array()[0]['level'];
	}
	public function check_heirarchy($user_id,$target_user_id){  // returns true if user_id higher that target_user_id else false
		return array_search($this->_fetch_level($user_id),$this->heirarchy) > array_search($this->_fetch_level($target_user_id), $this->heirarchy);
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
		if(true){//if(20<=getdate()['mday'] && getdate()['mday']<=31){  // if(true){ //
			$this->query_string = "INSERT INTO `Tour_Plan` (`user_id`,`tour_month`,`tour_plan`,`status`,`level`)
									VALUES (?,?,?,?,?)";
			$this->query = $this->db->query($this->query_string,array($user_id,$tour_month,$tour_plan,$status,$level));
			return true;
			}
		else{
			return false;
		}
	}

}

?>