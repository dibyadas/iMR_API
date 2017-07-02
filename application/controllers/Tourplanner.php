<?php
require(APPPATH.'/libraries/REST_Controller.php');
require_once(APPPATH.'models/TourPlannerModel.php');
require_once(APPPATH.'helpers/authenticate.php');
require(APPPATH.'helpers/response.php');


class Tourplanner extends REST_Controller{
	protected $token_payload;
	public function __construct(){
		parent::__construct();
		$this->load->model('TourPlannerModel','tp_');
		$this->load->model('EmployeeModel','Employee_');
		$this->load->model('PersonModel','Person_');
		try{
			$this->token_payload = authenticate($this);
		}
		catch(Exception $e){
			response($this,false,401,"",$e->getMessage());
		}
	}

	public function get_tour_details_get(){
		$user_id = $this->get('user_id');
		$tour_month = $this->get('tour_month');
		$tour_year = $this->get('tour_year');

		$this->response($this->tp_->get_tour_details($user_id,$tour_month,$tour_year));
	}

	public function set_tour_details_post(){
		$user_id = $this->post("user_id");
		$tour_month = $this->post("tour_month");
		$tour_year = $this->post("tour_year");
		$tour_plan = $this->post("tour_plan");
		$status = "0";

		if(!($this->token_payload["own"] == "MR" && $this->token_payload["user_id"] == $user_id)){ // only MRs allowed to create tour plans
			response($this,false,401,"","Action Forbidden");
		}else{
			if($this->tp_->set_tour_details($user_id,$tour_month,$tour_year,$tour_plan,$status)){
				response($this,true,200,"Tour Plan successfully added");
			}
			else{
				response($this,false,401,"","Action Forbidden");
			}
 		}

 	}

	public function update_status_post(){

		$user_id = $this->post('user_id');
		$status = $this->post('status');
		$target_user_id = $this->post('target_user_id');
		$tour_month = $this->post('tour_month');
		$tour_year = $this->post('tour_year');

		if(!($this->token_payload["own"] == "Admin" || $this->token_payload["user_id"] == $user_id)){
			response($this,false,401,"","Action Forbidden");
		}
		elseif($this->tp_->check_hierarchy($user_id,$target_user_id)) {
			if(20<=getdate()['mday'] && getdate()['mday']<=31 || $this->tp_->fetch_edit_access($target_user_id,$tour_month,$tour_year)){
				if($this->tp_->update_status($tour_month,$tour_year,$status,$target_user_id)){
					response($this,true,200,"Tour Plan successfully added");
				}
				else{
					response($this,false,401,"","Action Forbidden");
				}
			}else{
				response($this,false,401,"","Action Forbidden");
			}
		}else{
			response($this,false,401,"","Action Forbidden");
		}
	}

	public function change_edit_access_post(){  // available only for admin
		if($this->token_payload['own'] == "Admin"){
			$user_id = $this->post('user_id');
			$tour_month = $this->post('tour_month');
			$tour_year = $this->post('tour_year');
			$access = $this->post('access');

			if($this->tp_->change_edit_access($user_id,$tour_month,$tour_year,$access)){
				response($this,true,200,"Tour Plan successfully added");
			}else{
				response($this,false,401,"","Action Forbidden");
			}
		}else{
			response($this,false,401,"","Action Forbidden");
		}
	}

	public function change_tour_plan_post(){
		$user_id = $this->post('user_id');
		$target_user_id = $this->post('target_user_id');
		$tour_month = $this->post('tour_month');
		$tour_year = $this->post('tour_year');
		$tour_plan = $this->post('tour_plan');
		$tour_month_year = $tour_year."-".$tour_month;
	
		if(!($this->token_payload["own"] == "Admin" || $this->token_payload["user_id"] == $user_id)){
			$this->response("Action Forbidden");
		}
		elseif($user_id == $target_user_id) { // if user trying to change his own tour plan
			if(20<=getdate()['mday'] && getdate()['mday']<=31){
				if(date('Y-m') < $tour_month_year){
					if($this->tp_->change_tour_plan($tour_month,$tour_year,$tour_plan,$target_user_id)){
							$this->response('Tour Plan Successfully updated');
						}
						else{
							$this->response('Action Forbidden');
					}
				}else{
					$this->response('Action Forbidden');
				}
			}
			elseif($this->tp_->fetch_edit_access($user_id,$tour_month,$tour_year)){
				if(date('Y-m') == $tour_month_year){
					if($this->tp_->change_tour_plan($tour_month,$tour_year,$tour_plan,$target_user_id)){
									$this->response('Tour Plan Successfully updated');
								}
								else{
									$this->response('Action Forbidden');
							}
				}
			}
			else{
			 	$this->response('Action Forbidden');
			}
		}
		elseif($user_id != $target_user_id){  // if his head his trying to change the tour plan
			if(20<=getdate()['mday'] && getdate()['mday']<=31){
				if(date('Y-m') < $tour_month_year){
						if($this->tp_->check_hierarchy($user_id,$target_user_id)){
							if($this->tp_->change_tour_plan($tour_month,$tour_year,$tour_plan,$target_user_id)){
									$this->response('Tour Plan Successfully updated');
								}
								else{
									$this->response('Action Forbidden');
							}
						}
						else{
							$this->response('Action Forbidden');
						}
				}else{
					$this->response('Action Forbidden');
				}
			}
			elseif($this->tp_->fetch_edit_access($user_id,$tour_month,$tour_year)){
				if(date('Y-m') == $tour_month_year){
					if($this->tp_->check_hierarchy($user_id,$target_user_id)){
							if($this->tp_->change_tour_plan($tour_month,$tour_year,$tour_plan,$target_user_id)){
								$this->response('Tour Plan Successfully updated');
							}
							else{
								$this->response('Action Forbidden');
							}
						}
						else{
							$this->response('Action Forbidden');
						}
					}else{
					 	$this->response('Action Forbidden');
					}
				}else{
						$this->response('Action Forbidden');
					}
			}else{
				$this->response('Action Forbidden');
			}
	}

	// public function test_get(){
	// 	$user_id = $this->get('user_id');
	// 	$this->response($this->tp_->check_heirarchy($user_id,"MR1_INDORE"));
	// }

}

?>