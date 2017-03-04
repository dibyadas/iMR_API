<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require(APPPATH.'libraries/REST_Controller.php');
require(APPPATH.'helpers/response.php');
require(APPPATH.'helpers/authenticate.php');
require(APPPATH.'helpers/validateDateFormat.php');
require_once(APPPATH.'libraries/jwt_helper.php');
require_once (APPPATH.'helpers/priceHistory.php');
class Gifts extends REST_Controller{
    private $token_payload;
    public function __construct(){
        parent::__construct();
        $this->load->model('PersonModel', "Person_");
	    $this->load->model('EmployeeModel',"Employee_");

        $this->load->model('GiftModel', 'Gift_')  ;
        try{
            $this->token_payload = authenticate($this);
        }
        catch(Exception $e){
            response($this,false,401,"",$e->getMessage());		// 401 -> invalid token
        }
    }

    public function add_post(){
        if($this->token_payload["own"] == "Admin") {
            $name = $this->post(GIftDb::$NAME);
            $description = $this->post(GIftDb::$DESCRIPTION);
            $price = $this->post(GIftDb::$PRICE);
            $active = $this->post(GIftDb::$ACTIVE);
            try {
                $this->Gift_->addGift($name, $description, $price, $active);
                response($this,true,200,"Gift Added Successfully");
            }catch (Exception $e){
                response($this, false, 412, "", "Database Error : Gift Add");
            }
        }else{
            response($this,false,430,"","You don't have permission for this action");
        }
    }

    public function edit_post(){
        if($this->token_payload["own"] == "Admin") {
            $giftId = $this->post(GIftDb::$GIFT_ID);
            $name = $this->post(GIftDb::$NAME);
            $description = $this->post(GIftDb::$DESCRIPTION);
            $price = $this->post(GIftDb::$PRICE);
            $active = $this->post(GIftDb::$ACTIVE);
			$currentPrice = $this->Gift_->getGift($giftId)[GiftDb::$PRICE];
            $priceChanged = false;
			if($currentPrice != $price){
                $priceChanged = true;
            }
            try {
                $this->Gift_->editGift($giftId, $name, $description, $price, $active,$priceChanged);
                response($this,true,200,"Gift edited Successfully");
            }catch (Exception $e){
                response($this, false, 412, "", "Database Error : Gift Add");
            }
        }else{
            response($this,false,430,"","You don't have permission for this action");
        }
    }

    public function activeList_get(){
        response($this,true,200,$this->Gift_->getActiveGiftList());
    }

    public function list_get(){
        response($this,true,200,$this->Gift_->getGiftList());
    }

    public function deactivate_post(){
        if($this->token_payload["own"] == "Admin") {
            $giftId = $this->post(GIftDb::$GIFT_ID);
            try {
                $this->Gift_->deactivateGiftId($giftId);
                response($this, true, 200, "Gift " . $giftId. " has been deactivated successfully");
            }catch (Exception $e) {
                response($this, false, 412, "", "Database Error : Deactivate Gift");
            }
        }else{
            response($this,false,430,"","You don't have permission for this action");
        }
    }

    public function activate_post(){
        if($this->token_payload["own"] == "Admin") {
            $giftId = $this->post(GIftDb::$GIFT_ID);
            try {
                $this->Gift_->activateGiftId($giftId);
                response($this, true, 200, "Gift " . $giftId. " has been activated successfully");
            } catch (Exception $e) {
                response($this, false, 412, "", "Database Error : Activate Gift");
            }
        }else{
            response($this,false,430,"","You don't have permission for this action");
        }
    }
}
