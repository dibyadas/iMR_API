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
		$tour_month = $this->post("tour_month");
		$tour_plan = $this->post("tour_plan");
		$status = $this->post("status");
		$level = $this->post("level");
		try {
			if($this->tp_->set_tour_details($user_id,$tour_month,$tour_plan,$status,$level)){
				$this->response("Tour Plan Successfully updated");	
			}
			else{
				$this->response("Action Forbidden");		
			}			
		} catch (Exception $e) {
			$this->response("Unsuccessful");
		}

	}

	public function update_status_post(){
		$user_id = $this->post('user_id');
		$status = $this->post('status');
		$target_user_id = $this->post('target_user_id');
		if($this->tp_->update_status($user_id,$status,$target_user_id)){
			$this->response('Tour Plan status Successfully updated');
		}
		else{
			$this->response('Action Forbidden');
		}
	}

	public function change_tour_plan_post(){
		$user_id = $this->post('user_id');
		$target_user_id = $this->post('target_user_id');
		$tour_month = $this->post('tour_month');
		$tour_plan = $this->post('tour_plan');
		if($this->tp_->change_tour_plan($user_id,$tour_month,$tour_plan,$target_user_id)){
			$this->response('Tour Plan Successfully updated');
		}
		else{
			$this->response('Action Forbidden');
		}
	}

	public function test_get(){
		$user_id = $this->get('user_id');
		$this->response($this->tp_->_get_profile($user_id));
	}


}

?>