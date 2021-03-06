<?php

/**
 * @Author: rtyrohit
 * @Date:   2017-03-09 16:59:08
 * @Last Modified by:   rtyrohit
 * @Last Modified time: 2017-03-28 19:26:16
 */

defined('BASEPATH') OR exit('No direct script access allowed');
require(APPPATH.'libraries/REST_Controller.php');
require(APPPATH.'helpers/response.php');
require(APPPATH.'helpers/EmptyString.php');
require(APPPATH.'helpers/authenticate.php');
require_once(APPPATH.'libraries/jwt_helper.php');
require_once(APPPATH.'models/UserDb.php');
require_once(APPPATH.'models/UserModel.php');

class User extends REST_Controller
{
	private $_id;
	private $_level;
	private $status;
	private $username;
	private $meet_date;
	private $starttime = "00:00:00";
	private $endtime = "00:00:00";

	private $_role_hirerarchy = ["MR", "TM", "AM", "RM", "ZM", "MSD"];

	public function __construct()
	{
		parent::__construct();
		/*try
		{
			$this->token_payload = authenticate($this);
		}
		catch(Exception $e)
		{
			response($this,false,401,"",$e->getMessage());		// 401 -> invalid token
		}*/
		$this->load->model('UserModel', 'user_');
	}


	public function call_meeting_get()
	{
		// $this->username = $this->get('username');
		// $this->members = $this->get('members'); // array of member usernames
		// $this->meet_date = $this->get('meet_date');

		$this->username = "rtyrohit";
		$this->mem_usernames = ["rtyrt", "rohitkr"];
		$this->meet_date = "1996-07-15";

		$this->_level = "MR";

		// $this->_level = array_search($this->_level, $_role_hirerarchy)
		// if($this->_level != false) {

		$members = [];
		if(!empty($username))
		{
			foreach ($this->mem_usernames as $mem) 
			{
				if(!empty($mem))
				{
					if(!in_array($mem, $members))
					{
						array_push($members, $mem);
					}
				}
				else
				{
					response($this,true,200,"Invalid Input");
				}
			}

			if(count($members)>0)
			{
				$this->_level = array_search($this->_level, $_role_hirerarchy);
				if($this->_level != false) {
					$call_meeting = $this->user_->call_meeting($this->_level, $this->username, $members, $this->meet_date);

					response($this,true,200,$call_meeting);
				} else {
					response($this,true,200,"Access Forbidden");
				}
			}
			else
			{
				response($this,true,200,"Invalid Input");
			}
		}
		else
		{
			response($this,true,200,"Invalid Input");
		}
	}

	public function set_meeting_get()
	{
		$this->username = $this->get('username');
		$this->meet_setwith = $this->get('meet_setwith');
		$this->meet_date = $this->get('meet_date');

		if(!empty($this->get('starttime')))
			$this->starttime = $this->get('starttime');
		if(!empty($this->get('endtime')))
			$this->endtime = $this->get('endtime');

		if(!empty($this->username) && !empty($this->meet_setwith) && !empty($this->meet_date))
		{
			$fix_meeting = $this->user_->set_meeting($this->username, $this->meet_setwith, $this->meet_date, $this->starttime, $this->endtime);

			// $fix_meeting = ['fixed'];
			response($this,true,200,$fix_meeting);
		}
		else
		{
			response($this,true,200,['invalid input']);
		}

	}

	public function get_schedule_get()
	{
		$has_access = $this->user_->check_access_level('rtyrohit', 'rtyrt');
		// response($this,true,200,$has_access);
		if($has_access)
		{
			$schedule = $this->user_->fetch_schedule('rtyrohit');
			if($schedule['code'] == 0)
			{
				$result = $schedule['data'];
			}
			else
			{
				$result = $schedule['msg'];
			}
		} 
		else
		{
			$result = 'Access Forbidden';
		}
		response($this,true,200,$result);
	}

	public function edit_meeting_get()
	{
		$username = $this->get('username');
		$meeting_id = $this->get('meeting_id');
		$action = $this->get('action');

		if($action == 'cancel')
		{
			$cancel_meeting = $this->user_->cancel_meeting($username, $meeting_id);

			response($this,true,200,$cancel_meeting);
		}
		elseif($action == 'edit') // change date
		{
			$new_date = $this->get('new_date');
			$reschedule_meeting = $this->user_->reschedule_meeting($username, $meeting_id, $new_date);

			response($this,true,200,$reschedule_meeting);
		}
		else
		{
			response($this,true,200,"Invalid Input");
		}
	}
}

?>