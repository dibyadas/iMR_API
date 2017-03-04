<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require(APPPATH.'libraries/REST_Controller.php');
require(APPPATH.'helpers/response.php');
require(APPPATH.'helpers/authenticate.php');
require_once(APPPATH.'libraries/jwt_helper.php');
require_once (APPPATH.'helpers/priceHistory.php');
class Products extends REST_Controller{
	private $token_payload;
	public function __construct(){   
		parent::__construct();
		$this->load->model('PersonModel',"Person_");
		$this->load->model('EmployeeModel',"Employee_");

		$this->load->model('ProductModel', 'Product_')  ;
		try{
			$this->token_payload = authenticate($this);
		}
		catch(Exception $e){
			response($this,false,401,"",$e->getMessage());		// 401 -> invalid token
		}
	}

	public function add_post(){
        if($this->token_payload["own"] == "Admin") {
            $name = $this->post(ProductDb::$NAME);
            $group = $this->post(ProductDb::$GROUP);
            $pack = $this->post(ProductDb::$PACK);
            $price = $this->post(ProductDb::$PRICE);
            $PTS = $this->post(ProductDb::$PTS);
            $scheme = $this->post(ProductDb::$SCHEME);
            $remarks = $this->post(ProductDb::$REMARKS);
            $active = $this->post(ProductDb::$IN_PRACTICE);
//            $priceHistory = createNewPriceHistory($price);
            try {
                $this->Product_->addProduct($name, $group, $pack, $price, $PTS, $scheme, $remarks,$active);
                response($this,true,200,"Product Added Successfully");
            }catch (Exception $e){
                response($this, false, 412, "", "Database Error : Product Add");
            }
        }else{
            response($this,false,430,"","You don't have permission for this action");
        }
    }

    public function edit_post(){
        if($this->token_payload["own"] == "Admin") {
            $productId = $this->post(ProductDb::$PRO_ID);
            $name = $this->post(ProductDb::$NAME);
            $group = $this->post(ProductDb::$GROUP);
            $pack = $this->post(ProductDb::$PACK);
            $price = $this->post(ProductDb::$PRICE);
            $PTS = $this->post(ProductDb::$PTS);
            $scheme = $this->post(ProductDb::$SCHEME);
            $remarks = $this->post(ProductDb::$REMARKS);
            $active = $this->post(ProductDb::$IN_PRACTICE);

            $currentPrice = $this->Product_->getProduct($productId)[ProductDb::$PRICE];
            $priceChanged = false;
            if($currentPrice != $price){
                $priceChanged = true;
            }
            try {
                $this->Product_->editProduct($productId, $name, $group, $pack, $price, $PTS, $scheme, $remarks, $active,$priceChanged);
                response($this,true,200,"Product edited Successfully");
            }catch (Exception $e){
                response($this, false, 412, "", "Database Error : Product Add");
            }
        }else{
            response($this,false,430,"","You don't have permission for this action");
        }
    }

    public function value_get(){
    	$productId = $this->get(ProductDb::$PRO_ID);
    	$units = $this->get("units");
    	$data["value"] = $this->Product_->getProductValue($productId,$units);
		response($this,true,200,$data);
	}

    public function activeList_get(){
        response($this,true,200,$this->Product_->getActiveProductList());
    }

    public function list_get(){
        response($this,true,200,$this->Product_->getProductList());
    }

    public function deactivate_post(){
       // TODO: note : let say the admin has deactivated a product which is in WCP then it would still be available or option for
       // the mr to continue with that product for current WCP but won't be an option in the next WCP. This way i won't have to keep a condition here.
        if($this->token_payload["own"] == "Admin") {
            $productId = $this->post(ProductDb::$PRO_ID);
            try {
                $this->Product_->deactivateProductId($productId);
                response($this, true, 200, "Product " . $productId. " has been deactivated successfully");
            } catch (Exception $e) {
                response($this, false, 412, "", "Database Error : Deactivate Product");
            }
        }else{
            response($this,false,430,"","You don't have permission for this action");
        }
    }

    public function activate_post(){
        if($this->token_payload["own"] == "Admin") {
            $productId = $this->post(ProductDb::$PRO_ID);
            try {
                $this->Product_->activateProductId($productId);
                response($this, true, 200, "Product " . $productId. " has been activated successfully");
            } catch (Exception $e) {
                response($this, false, 412, "", "Database Error : Activate Product");
            }
        }else{
            response($this,false,430,"","You don't have permission for this action");
        }
    }
}
