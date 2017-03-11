<?php

/*
* Author @rtyrohit
* 10.3.17
*/

require_once BASEPATH.'core/Model.php';
require_once "UserDb.php";
require_once "ScheduleDb.php";

class UserModel extends CI_Model
{
	protected $query_string, $query;

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
   			$this->query_string = "SELECT * FROM ".ScheduleDb::$TABLE." WHERE ".ScheduleDb::$MEET_SETBY." = ? OR ".ScheduleDb::$MEET_SETWITH." = ?";
   			// return $user[0]['id'];
   			$this->query = $this->db->query($this->query_string, array($user[0]['id'], $user[0]['id']));

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

   	public function set_meeting($meet_setby, $meet_setwith, $meet_date, $starttime, $endtime)
   	{
   		try 
   		{
   			$members_arr = $this->fetch_user_details($meet_setby, [$meet_setwith]);
   			
   			$flag = false; // check for meetsetby existence
   			$meet_setwith_id = null;
   			foreach ($members_arr as $member) {
   				if($meet_setby == $member['username']) {
   					$meet_setby_id = $member['id'];
   					$flag = true;
   				}
   				elseif($meet_setwith == $member['username'])
   				{
   					$meet_setwith_id = $member['id'];
   				}
   			}

   			$this->query_string = "INSERT INTO ".ScheduleDb::$TABLE." (".ScheduleDb::$MEET_SETBY ." , ". ScheduleDb::$MEET_SETWITH ." , ". ScheduleDb::$DATE ." , ".ScheduleDb::$STARTTIME. " , " .ScheduleDb::$ENDTIME .") VALUES (?,?,?,?,?)";
			
			$this->query = $this->db->query($this->query_string, array($meet_setby_id, $meet_setwith_id, $meet_date, $starttime, $endtime));
   			
   			// $meeting = $this->query->result_array();
   			// return array($meet_setby_id, $meet_setwith_id, $meet_date, $starttime, $endtime);
   			return $this->query;
   			// return $meeting;
   		} 
   		catch (Exception $e)
   		{
   			return $e->getMessage();
   		}
   	}

   	public function cancel_meeting($username, $meeting_id)
   	{
   		$user = $this->fetch_user_details($username);

   		if(count($user)>0)
   		{
   			$this->query_string = "UPDATE ".ScheduleDb::$TABLE." SET ".ScheduleDb::$STATUS."=0 WHERE ".ScheduleDb::$MEET_SETBY."=? AND ".ScheduleDb::$_ID."=?";

   			$this->query = $this->db->query($this->query_string, array($user[0]['id'], $meeting_id));

   			return $this->query;
   		}
   		else
   		{
   			return false;
   		}
   	}

   	public function reschedule_meeting($username, $meeting_id, $new_date)
   	{
   		$user = $this->fetch_user_details($username);

   		if(count($user)>0)
   		{
   			$this->query_string = "UPDATE ".ScheduleDb::$TABLE." SET ".ScheduleDb::$DATE."=? WHERE ".ScheduleDb::$MEET_SETBY."=? AND ".ScheduleDb::$_ID."=?";

   			$this->query = $this->db->query($this->query_string, array($new_date,$user[0]['id'], $meeting_id));

   			return $this->query;
   		}
   		else
   		{
   			return false;
   		}
   	}
}
?>