<?php
require(APPPATH.'/libraries/REST_Controller.php');
require_once(APPPATH.'models/TourPlannerModel.php');

class Tourplanner extends REST_Controller{
	public function __construct(){
		parent::__construct();
		$this->load->model('TourPlannerModel','tp_');
	}

	public function get_tour_details_get(){
		$this->response($this->tp_->get_tour_details($this->get('user_id')));
	}

	public function set_tour_details_post(){
		$user_id = $this->post("user_id");
		$tour_date = $this->post("tour_date");
		$tour_plan = $this->post("tour_plan");
		$status = $this->post("status");
		$level = $this->post("level");
		try {
			$this->tp_->set_tour_details($user_id,$tour_date,$tour_plan,$status,$level);	
			$this->response("Successful");
		} catch (Exception $e) {
			$this->response("Unsuccessful");
		}

	}
	
}

?>