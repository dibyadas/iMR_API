<?php

/**
 * Created by PhpStorm.
 * User: sahil333
 * Date: 9/1/17
 * Time: 2:29 PM
 */
defined('BASEPATH') OR exit('No direct script access allowed');
require(APPPATH.'libraries/REST_Controller.php');
require(APPPATH.'helpers/response.php');
//require(APPPATH.'helpers/authenticate.php');
require(APPPATH.'helpers/PersonHistory.php');
require_once(APPPATH.'libraries/jwt_helper.php');
class Sets extends REST_Controller {
    private $token_payload;
    public function __construct(){
        parent::__construct();
        //$this->load->model('UserIdModel', "UserId_"); module not present
        $this->load->model('PersonModel', "Person_");

        $this->load->model('SetModel', 'Set_');
        $this->load->model('PersonHistoryModel', 'PEH_');
        $this->load->model('PriceHistoryModel', 'PRH_');
        //$this->load->model('PriceKeyMapModel', 'PR_KeyMap');  module not present
        $this->load->model('HeadHistoryModel', 'HH_');
        $this->load->model('MRHistoryModel', 'MRH_');
       // $this->load->model('ChildKeyMapModel', 'C_KMP');   module not present\
        try{
            //$this->token_payload = authenticate($this);
        }
        catch(Exception $e){
            response($this,false,401,"",$e->getMessage());		// 401 -> invalid token
        }
    }  // there seems to be a problem of authentication with this module

    public function list_get(){
        if(1 || $this->token_payload["own"] == "MR") {
			//$MRId = $this->token_payload["user_id"];
            $MRId = $this->get('user_id');
			response($this,true,200,$this->Set_->getSets($MRId));
        }else{
			response($this,false,430,"","Only MR have permission for this action");
		}

    }


    public function add_post(){
        if($this->token_payload["own"] == "MR"){
            $MRId = $this->token_payload["user_id"];
            $setNo =  $this->post(SetDb::$SET_NO);
            $station = $this->post(SetDb::$STATION);
            if(!$this->Set_->setExist($MRId,$setNo)){
                $this->Set_->addNewSet($MRId, $setNo, $station);
                response($this,true,200,"Set Added Successfully");
            }else{
                response($this,false,437,"","Set Already Exist");
            }
        }else{
            response($this,false,430,"","Only MR have permission for this action");
        }
    }

    public function edit_post(){
        if($this->token_payload["own"] == "MR"){
            $MRId = $this->token_payload["user_id"];
            $setNo =  $this->post(SetDb::$SET_NO);
            $station = $this->post(SetDb::$STATION);
            if($this->Set_->setExist($MRId,$setNo)){
                $this->Set_->editSet($MRId, $setNo, $station);
                response($this,true,200,"Set edited Successfully");
            }else{
                response($this,false,437,"","Set Doesn't Exist");
            }
        }else{
            response($this,false,430,"","Only MR have permission for this action");
        }
    }

    // TODO:: LastNHeadsHistory jyada use hoga bakeda ki Since wala
    public function history_get(){
//        $time = "14/02/2016 9:25:59 am";
//        print_r(strtotime($time));
//        exit();
//        $commoId = $this->get("CommoId");
//
//        $n = $this->get("n");
//        $role = "Product";
//        $commoKey = $this->PR_KeyMap->getCommoKey($commoId, $role);
//        $History = $this->PRH_->getLastNPrices($commoKey,$n);
//        print_r($History);

//        $userId = $this->get("user_id");
//
//        $n = $this->get("n");
//        $History = $this->PEH_->getLastNPersons($userId,$n);
//        print_r($History);

//        $userId = $this->get("user_id");
//        $n = $this->get("n");
//        $History = $this->HH_->getLastNHeads($userId,$n);
//        print_r($History);

//        $userId = $this->get("user_id");
//        $date = $this->get("date");
//        $History = $this->HH_->getHeadHistorySince($userId,$date);
//        print_r($History);

//        $childId = $this->get("child_id");
//        $n = $this->get("n");
//        $role = "Doctor";
//        $childKey = $this->C_KMP->getChildKey($childId, $role);
//        $History = $this->MRH_->getLastNMR($childKey,$n);
//        print_r($History);
    }
}