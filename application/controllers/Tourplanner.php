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
		try{
			$this->token_payload = authenticate($this);
		}
		catch(Exception $e){
			response($this,false,401,"",$e->getMessage());
		}
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

		if(!($this->token_payload["own"] == "Admin" || $this->token_payload["user_id"] == $user_id)){
			$this->response("Action Forbidden");
		}else{
			try {
				if($this->tp_->set_tour_details($user_id,$tour_month,$tour_plan,$status,$level)){
					$this->response("Tour Plan successfully added");
				}
				else{
					$this->response("Action Forbidden");
				}			
			} catch (Exception $e) {
				$this->response("Unsuccessful");
			}
 		}
	}

	public function update_submission_status_post(){
		if(!($this->token_payload["own"] == "Admin" || $this->token_payload["user_id"] == $user_id)){
			$this->response("Action Forbidden");
		}
	}

	public function update_status_post(){

		$user_id = $this->post('user_id');
		$status = $this->post('status');
		$target_user_id = $this->post('target_user_id');

		if(!($this->token_payload["own"] == "Admin" || $this->token_payload["user_id"] == $user_id)){
			$this->response("Action Forbidden");		
		}
		elseif($this->tp_->check_hierarchy($user_id,$target_user_id)){
				if($this->tp_->update_status($user_id,$status,$target_user_id)){
				$this->response('Tour Plan status successfully updated');
			}
			else{
				$this->response('Action Forbidden');
			}
		}else{
			$this->response('Action Forbidden');
		}
	}

	public function change_edit_access_post(){  // available only for admin
		if($this->token_payload['own'] == "Admin"){
			$user_id = $this->post('user_id');
			$access = $this->post('access');
			if($this->tp_->change_edit_access($user_id,$access)){
				$this->response('Edit access successfully changed');
			}else{
				$this->response('Edit access couldn\'t be changed');
			}
		}else{
			$this->response('Action Forbidden');	
		}
	}

	public function change_tour_plan_post(){
		$user_id = $this->post('user_id');
		$target_user_id = $this->post('target_user_id');
		$tour_month = $this->post('tour_month');
		$tour_plan = $this->post('tour_plan');

		if(!($this->token_payload["own"] == "Admin" || $this->token_payload["user_id"] == $user_id)){
			$this->response("Action Forbidden");
		}
		elseif($user_id == $target_user_id) { // if user trying to change his own tour plan
			if(20<=getdate()['mday'] && getdate()['mday']<=31 || $this->tp_->fetch_edit_access($user_id)){
				if($this->tp_->change_tour_plan($user_id,$tour_month,$tour_plan,$target_user_id)){
						$this->response('Tour Plan Successfully updated');
					}
					else{
						$this->response('Action Forbidden');
				}
			}
		}elseif($user_id != $target_user_id){  // if his head his trying to change the tour plan
			if($this->tp_->check_hierarchy($user_id,$target_user_id)){
				if($this->tp_->change_tour_plan($user_id,$tour_month,$tour_plan,$target_user_id)){
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

}

?>