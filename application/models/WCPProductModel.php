<?php

/**
 * Created by PhpStorm.
 * User: sahil333
 * Date: 25/1/17
 * Time: 1:42 AM
 */
require_once "WCPProductDb.php";
class WCPProductModel extends  CI_Model {
	public function __construct(){
		parent::__construct();
		$this->load->database();
	}

	public function addWCPProduct($WCPMainId,$productId,$samplePlanUnit,$samplePlanValue,$salePlanUnit,$salePlanValue){
		$data = array(
			WCPProduct::$WCP_MAIN_ID=>$WCPMainId,
			WCPProduct::$PRO_ID=>$productId,
			WCPProduct::$SAMPLE_PLAN_UNIT=>$samplePlanUnit,
			WCPProduct::$SAMPLE_PLAN_VALUE=>$samplePlanValue,
			WCPProduct::$SALE_PLAN_UNIT=>$salePlanUnit,
			WCPProduct::$SALE_PLAN_VALUE=>$salePlanValue,
		);
		$this->db->insert(WCPProduct::$TABLE,$data);
	}

	public function editWCPProduct($pId,$productId,$samplePlanUnit,$samplePlanValue,$salePlanUnit,$salePlanValue){
		$data = array(
			WCPProduct::$PRO_ID=>$productId,
			WCPProduct::$SAMPLE_PLAN_UNIT=>$samplePlanUnit,
			WCPProduct::$SAMPLE_PLAN_VALUE=>$samplePlanValue,
			WCPProduct::$SALE_PLAN_UNIT=>$salePlanUnit,
			WCPProduct::$SALE_PLAN_VALUE=>$salePlanValue,
		);
		$this->db->update(WCPProduct::$TABLE,$data,array(WCPProduct::$WCP_PRODUCT_ID=>$pId));
	}

	public function getWCPProductOfDoctor($WCPMainId){
		$query = $this->db->get_where(WCPProduct::$TABLE,array(WCPProduct::$WCP_MAIN_ID=>$WCPMainId));
		return $query->result_array();
	}
}