<?php

/**
 * Created by PhpStorm.
 * User: sahil333
 * Date: 14/1/17
 * Time: 8:17 PM
 */
require_once "PriceHistoryDb.php";
class PriceHistoryModel extends CI_Model {
    public function __construct(){
        parent::__construct();
        $this->load->database();
    }
	public function addPriceHistory($commoId, $role, $price){
//		$priceKeyMapModel = new PriceKeyMapModel();
//		$commoKey = $priceKeyMapModel->getCommoKey($commoId,$role);
		$time = Date("m/d/Y H:i:s");
		$data =array(
			PriceHistoryDb::$COMMO_ID=>$commoId,
			PriceHistoryDb::$ROLE=>$role,
			PriceHistoryDb::$PRICE=> $price,
			PriceHistoryDb::$DATE => $time
		);
		$this->db->insert(PersonHistoryDb::$TABLE,$data);
	}
    public function getPriceHistory($commoId,$role){
//		$priceKeyMapModel = new PriceKeyMapModel();
//		$commoKey = $priceKeyMapModel->getCommoKey($commoId,$role);
        $queryString = "SELECT * FROM ".PriceHistoryDb::$TABLE." WHERE ".PriceHistoryDb::$COMMO_ID."=? AND ".PriceHistoryDb::$ROLE."=?";
        $query = $this->db->query($queryString,array($commoId,$role));
        $result = sortByDate($query->result_array());
        return $result;
    }

    public function getLastNPrices($commoId,$role, $n){
//		$priceKeyMapModel = new PriceKeyMapModel();
//		$commoKey = $priceKeyMapModel->getCommoKey($commoId,$role);
//        $queryString = "SELECT * FROM ".PriceHistoryDb::$TABLE." WHERE ".PriceHistoryDb::$COMMO_KEY."=? LIMIT $n";
        $query = $this->db->get_where(PriceHistoryDb::$TABLE,array(PriceHistoryDb::$COMMO_ID=> $commoId,PriceHistoryDb::$ROLE=>$role));
//        $query = $this->db->query($queryString,array($commoKey));
        $priceHistory = sortByDate($query->result_array());
        $lastNHistory = array_slice($priceHistory,0,$n);
        return $lastNHistory;
    }
    public function getPriceHistorySince($commoId,$role,$date){
//		$priceKeyMapModel = new PriceKeyMapModel();
//		$commoKey = $priceKeyMapModel->getCommoKey($commoId,$role);
        $queryString  = "SELECT * FROM ".PriceHistoryDb::$TABLE." WHERE ".PriceHistoryDb::$COMMO_ID."=? AND ".PriceHistoryDb::$ROLE."=?";;
        $query = $this->db->query($queryString,array($commoId,$role));
        $priceHistory = getHistoryAfter($query->result_array(),$date);
        return $priceHistory;
    }
}