<?php

/**
 * @Author: rtyrohit
 * @Date:   2017-03-09 16:15:12
 * @Last Modified by:   rtyrohit
 * @Last Modified time: 2017-04-15 21:12:32
 */

require_once BASEPATH.'core/Model.php';
require_once "UserDb.php";
require_once "TourDetailsDb.php";
require_once "ScheduleDb.php";

class UserModel extends CI_Model
{
	protected $query_string, $query;
  // private $_role_hirerarchy = ["MR", "TM", "AM", "RM", "ZM", "MSD"];


	public function __construct(){   
		parent::__construct();
		$this->load->database();
    }

    protected function fetch_user_details($username, $username_arr=null)
    {
    	$this->query_string = "SELECT * FROM ".UserDb::$TABLE." WHERE ".UserDb::$USERNAME." = ?";
    	
    	$query_run_arr = [$username];
    	if($username_arr!=null)
    	{
    		for($i=0;$i<count($username_arr);$i++)
    		{
    			$this->query_string .= " OR ".UserDb::$USERNAME." = ?";
    			array_push($query_run_arr, $username_arr[$i]);
    		}
    	}
    	$this->query = $this->db->query($this->query_string, $query_run_arr);

    	$user = $this->query->result_array();

    	return $user;
    }	

    public function check_access_level($user_username, $target_username) 
    {
    	$this->query_string = "SELECT ".UserDb::$USERNAME.", ".UserDb::$STATUS.", ".UserDb::$LEVEL." FROM ".UserDb::$TABLE." WHERE ".UserDb::$USERNAME." IN (?,?)";
    	$this->query = $this->db->query($this->query_string, array($user_username, $target_username));

    	$users = $this->query->result_array();
    	
    	if(count($users) == 2) 
    	{
    		foreach ($users as $u) 
    		{
    			if($u['username'] == $user_username)
    				$i = $u['level'];
    			else
    				$j = $u['level'];
    		}

    		if($i == $j+1)
    			return true;
    		else
    			return false;
    	}
    	elseif(count($users) == 1 && $user_username == $target_username)
    		return true;
    	else
    		return false;
    }

    public function fetch_schedule($username)
   	{
   		$user = $this->fetch_user_details($username);
   		if(count($user)>0)
   		{
   			$this->query_string = "SELECT * FROM ".ScheduleDb::$TABLE." WHERE ".ScheduleDb::$CREATED_BY." = ?";
   			// return $user[0]['id'];
   			$this->query = $this->db->query($this->query_string, array($user[0]['id']));

   			$schedule = $this->query->result_array();
   			
   			$response = [
   				"code" => 0,
   				"data" => $schedule
   			];
   		}
   		else
   		{
   			$response = [
   				"code" => -1,
   				"data" => [],
   				"msg"  => "User not found."
   			];
   		}

   		return $response;
   	}

   	public function call_meeting($username, $members, $meet_date)
   	{
   		$user = $this->fetch_user_details($username);
   	}

   	public function create_plan($username, $complete_plan, $plan_date) 
    {
         /*example input ($complete_plan)
            [
                "month"=> M(int),
                "year"=> Y(int),

                "plan" => [
                    [
                        "date"=> date yyyy-mm-dd,
                        "plan"=> "text (comma seperated values)",
                        "recommendation" => "text",
                        "accompany_by" => "text (comma seperated values)"
                    ]
                ]
            ]
        */

        $this->query_string = "INSERT INTO ".TourDetailsDb::$TABLE." (".TourDetailsDb::$MONTH.", ".TourDetailsDb::$YEAR.", ".TourDetailsDb::$PROGRAMME_STATUS.", ".TourDetailsDb::$USER_DESIGNATION.", ".TourDetailsDb::$DATE_CREATED.", ".TourDetailsDb::$DATE_MODIFIED.", ".TourDetailsDb::$ASSIGNED_TO.", ".TourDetailsDb::$TEAM.", ".TourDetailsDb::$APPROVED_AT.") VALUES (?,?,?,?,?,?,?,?,?)";
        $this->query = $this->db->query($this->query_string, array($input['month'], $input['year'], $input['plan']['programme_status'], $input['plan']['assigned_user_designation'], $input['plan']['date_created'], $input['plan']['date_modified'], $input['plan']['assigned_to'], $input['plan']['team'], $input['plan']['approved_at']));

        $tour_id = $this->db->insert_id();

        $this->query_string = "INSERT INTO ".ScheduleDb::$TABLE." (".ScheduleDb::$CREATED_BY.", ".ScheduleDb::$PLAN.", ".ScheduleDb::$ACCOMPANY_BY.", ".ScheduleDb::$DATE.", ".ScheduleDb::$RECOMMENDATION.") VALUES ";
        
        $total_days = count($complete_plan['plan']);
        for($i=0;$i<$total_days;$i++) {
            $plan = $complete_plan['plan'][$i];

            $this->query_string += "(".$tour_id.", '".$plan['plan']."', '".$plan['accompany_by']."', '".$plan['date']."', '".$plan['recommendation']."')";

            if ($i != $total_days-2) {  
                $this->query_string += ", ";
            }
        }
        $this->query = $this->db->query($this->query_string);
    
        return true;
    }

    public function approve_tour($tour_id) {
      $datetime = new DateTime('now', new DateTimeZone($timezone));
      $timestamp = $datetime->format('j-n-Y G:i:s');

      $this->query_string = "UPDATE ".TourDetailsDb::$TABLE." SET ".TourDetailsDb::$APPROVED_AT."=? WHERE ".TourDetailsDb::$_ID."=?";
      $this->query_string = $this->db->query($this->query_string, array($timestamp, $tour_id));
    }
}
?>