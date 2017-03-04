<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require(APPPATH . 'libraries/REST_Controller.php');
require(APPPATH . 'helpers/response.php');
require(APPPATH . 'helpers/authenticate.php');
require(APPPATH . 'helpers/ErrorCollector.php');
require_once(APPPATH . 'libraries/jwt_helper.php');
require_once(APPPATH . 'controllers/AuthService.php');
//set_include_path(APPPATH.'third_party/JsonSchema');
//set_include_path(get_include_path() . PATH_SEPARATOR .__DIR__."/JsonSchema");
//require_once "JsonSchema";
//require_once "WCPProductListSchema.json";
class WCP extends REST_Controller
{
	public $token_payload;

	public function __construct(){
		parent::__construct();
		$this->load->model('PersonModel', "Person_");
		$this->load->model('EmployeeModel', "Employee_");
		$this->load->model('WCPModel', 'WCP_');
		$this->load->model('ProductModel', 'Product_');
		$this->load->model('WCPProductModel', 'WCPP_');
		$this->load->model('WCPWrapModel', 'WCPW_');
		$this->load->model('DocModel', 'Doc_');
		$this->load->model('ChemVisitModel', 'ChemVisit_');
		try {
			$this->token_payload = authenticate($this);
		} catch (Exception $e) {
			response($this, false, 401, "", $e->getMessage());        // 401 -> invalid token
		}

//        $this->load->library('JsonSchema');
	}


	public function permissionadd_post(){
		$month = $this->post("month");
		$year = $this->post("year");
		$isAuthorized = AuthService::WCPAdd($this,$month,$year);
		if ($isAuthorized) {
			response($this, true, 200, "You have permission to add WCP");
		} else {
			response($this, false, 582,"", "You can not add WCP right now");
		}
	}

	public function permissionedit_post(){
		$WCPWrapId = $this->post(WCPWrapDb::$WCP_WRAP_ID);
		$isAuthorized = AuthService::WCPEdit($this, $WCPWrapId);
		if ($isAuthorized) {
			response($this, true, 200, "You have permission to edit WCP");
		} else {
			response($this, false, 582, "","You can not edit WCP right now");
		}
	}

	public function approve_post(){
		$WCPWrapId = $this->post(WCPWrapDb::$WCP_WRAP_ID);
		$isAuthorized = AuthService::WCPEdit($this, $WCPWrapId);
		if ($isAuthorized) {
			$errorCollector = new ErrorCollector($this);
			$this->db->trans_start();
			$this->WCPW_->approveWCP($WCPWrapId, $this->token_payload["user_id"]);
			$errorCollector->collect();
			$this->db->trans_complete();
			if ($this->db->trans_status() == FALSE) {
				response($this, false, 412, "", "An error occurred : " . $errorCollector->getError());
			} else {
				response($this, true, 200, "WCP approved");
			}
		} else {
			response($this, false, 582, "","You can not approve WCP, wait or you don't have permission");
		}
	}

	public function submit_post(){
		$WCPWrapId = $this->post(WCPWrapDb::$WCP_WRAP_ID);
		$isAuthorized = AuthService::WCPEdit($this, $WCPWrapId);
		if ($isAuthorized) {
			$errorCollector = new ErrorCollector($this);
			$this->db->trans_start();
			$this->WCPW_->submitWCP($WCPWrapId, $this->token_payload["user_id"]);
			$errorCollector->collect();
			$this->db->trans_complete();
			if ($this->db->trans_status() == FALSE) {
				response($this, false, 412, "", "An error occurred : " . $errorCollector->getError());
			} else {
				response($this, true, 200, "WCP submitted");
			}
		} else {
			response($this, false, 582,"", "You can not submit right now, or you don't have permission");
		}
	}

	public function unSubmit_post(){
		$WCPWrapId = $this->post(WCPWrapDb::$WCP_WRAP_ID);
		$isAuthorized = AuthService::WCPEdit($this, $WCPWrapId);
		if ($isAuthorized) {
			$errorCollector = new ErrorCollector($this);
			$this->db->trans_start();
			$this->WCPW_->unsubmitWCP($WCPWrapId);
			$errorCollector->collect();
			$this->db->trans_complete();
			if ($this->db->trans_status() == FALSE) {
				response($this, false, 412, "", "An error occurred : " . $errorCollector->getError());
			} else {
				response($this, true, 200, "WCP unsubmitted.");
			}
		} else {
			response($this, false, 582,"", "You can not unsubmit right now, or you don't have permission");
		}
	}

	public function ExceptionForWCPAdd_post(){

	}

	public function ExceptionForWCPEdit_post(){
		$WCPWrapId = $this->post(WCPWrapDb::$WCP_WRAP_ID);
		$isAuthorized = AuthService::WCPEditException($this, $WCPWrapId);
		if ($isAuthorized) {
			$errorCollector = new ErrorCollector($this);
			$this->db->trans_start();
			$this->WCPW_->exceptionEditWCP($WCPWrapId);
			$errorCollector->collect();
			$this->db->trans_complete();
			if ($this->db->trans_status() == FALSE) {
				response($this, false, 412, "", "An error occurred : " . $errorCollector->getError());
			} else {
				response($this, true, 200, "WCP edit allowed for MR, MR now should edit WCP and get approved");
			}
		}else{
			response($this, false, 402, "","You can't add Exception.");
		}
	}

	public function addWCP_post(){
		$WCPData = json_decode(file_get_contents("php://input"), true);
		$month = $WCPData[WCPWrapDb::$MONTH];
		$year = $WCPData[WCPWrapDb::$YEAR];
		$isAuthorized = AuthService::WCPAdd($this,$month,$year);
		if ($isAuthorized) {
			$WCPData = json_decode(file_get_contents("php://input"), true);
			// Validate JSON Schema
			$MRId = $this->token_payload["user_id"];
			$errorCollector = new ErrorCollector($this);
			$this->db->trans_start();
			$WCPWrapId = $this->WCPW_->addWCPWrap($MRId, $month, $year);
			$errorCollector->collect();
			$WCPList = $WCPData["WCPList"];
			foreach ($WCPList as $WCPdoc) {
				$type = $WCPdoc[WCPDb::$TYPE];
				$dot = $WCPdoc[WCPDb::$DOT];
				$docId = $WCPdoc[WCPDb::$DOC_ID];
				if (!$this->Doc_->isAssoc($docId, $MRId)) {
					$this->db->trans_rollback();
					response($this, false, 445, "","You are not assigned to " . $docId . " doctor");
				}
				$productList = $WCPdoc["productList"];
				$WCPMainId = $this->WCP_->addWCPMain($WCPWrapId, $type, $dot, $docId);
				$errorCollector->collect();
				foreach ($productList as $product) {
					$productId = $product[WCPProduct::$PRO_ID];
					$samplePlanUnit = $product[WCPProduct::$SAMPLE_PLAN_UNIT];
					$salePlanUnit = $product[WCPProduct::$SALE_PLAN_UNIT];
					$samplePlanValue = $this->Product_->getSampleProductValue($productId, $samplePlanUnit);
					$salePlanValue = $this->Product_->getSaleProductValue($productId, $salePlanUnit);
					$this->WCPP_->addWCPProduct($WCPMainId, $productId, $samplePlanUnit, $samplePlanValue,
						$salePlanUnit, $salePlanValue);
					$errorCollector->collect();
				}
			}
			$this->db->trans_complete();
			if ($this->db->trans_status() == FALSE) {
				response($this, false, 412, "", $errorCollector->getError());
			} else {
				response($this, true, 200, "WCP added successfully");
			}
		} else {
			response($this, false, 480, "", "You don't have permissions to add WCP.");
		}
	}

	public function WCPWrap_get(){
		$MRId = $this->get("mr_id");
		response($this,true,200,$this->WCPW_->getWCPWrapOfMR($MRId));
	}

	public function WCP_get(){
		$WCPWrapId = $this->get("wcp_wrap_id");
		$WCPResult = array();
		$WCPResult["WCP_Wrap"] = $this->WCPW_->getWCPWrap($WCPWrapId);
		$WCPArray = $this->WCP_->getWCPOfWrap($WCPWrapId);
//		print_r($WCPArray);
//		exit();
		$WCPResult["WCP_Wrap"]["WCP_list"] = array();
		foreach($WCPArray as $WCP){
			$productArray = $this->WCPP_->getWCPProductOfDoctor($WCP[WCPDb::$WCP_ID]);
			$finalProductArray = array();
			foreach ($productArray as $product){
				$VisitArray = $this->ChemVisit_->getVisitsOfPID($product[WCPProduct::$WCP_PRODUCT_ID]);
				$achievedUnit = "";
				$achievedSale = "";
				foreach ($VisitArray as $visit){
					$achievedUnit .= $visit[ChemVisitDb::$SALE_ACHIEVED_UNIT].",";
					$achievedSale .= $visit[ChemVisitDb::$SALE_ACHIEVED_VALUE].",";
				}
				$product["achieved_unit"] = $achievedUnit;
				$product["achieved_sale"] = $achievedSale;
				array_push($finalProductArray,$product);
			}
			$WCP["product_list"] = $finalProductArray;
			array_push($WCPResult["WCP_Wrap"]["WCP_list"],$WCP);
		}
		response($this,true,200,$WCPResult);
	}

	public function editWCP_post(){
		$WCPData = json_decode(file_get_contents("php://input"), true);
		// Validate JSON Schema
		$WCPWrapId = $WCPData[WCPWrapDb::$WCP_WRAP_ID];
		$isAuthorized = AuthService::WCPEdit($this, $WCPWrapId);
		if ($isAuthorized) {
			$WCPArray =  $WCPData["WCPList"];
			$errorCollector = new ErrorCollector($this);
			$this->db->trans_start();
			foreach ($WCPArray as $WCP){
				$this->WCP_->editWCPMain($WCP[WCPDb::$WCP_ID],$WCP[WCPDb::$TYPE],$WCP[WCPDb::$DOT]);
				$errorCollector->collect();
				$productList = $WCP["productList"];
				foreach($productList as $product){
					$pId = $product[WCPProduct::$WCP_PRODUCT_ID];
					$productId = $product[WCPProduct::$PRO_ID];
					$samplePlanUnit = $product[WCPProduct::$SAMPLE_PLAN_UNIT];
					$salePlanUnit = $product[WCPProduct::$SALE_PLAN_UNIT];
					$samplePlanValue = $this->Product_->getSampleProductValue($productId, $samplePlanUnit);
					$salePlanValue = $this->Product_->getSaleProductValue($productId, $salePlanUnit);
					$this->WCPP_->editWCPProduct($pId,$productId, $samplePlanUnit, $samplePlanValue,
						$salePlanUnit, $salePlanValue);
					$errorCollector->collect();
				}
			}
			$this->db->trans_complete();
			if($this->db->trans_status() == FALSE){
				response($this,false,400,"","Database Error : ".$errorCollector->getError());
			}else{
				response($this,true,200,"WCP edited.");
			}
		} else {
			response($this,false,403,"","You cannot edit this WCP");
		}
	}


	private function calculateCumulativeVisits($startDate, $endDate, $docId){

	}

	private function calculateCumulativeBusiness($startDate, $endDate, $docId){

	}

}