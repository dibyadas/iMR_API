<?php
require_once "GiftDb.php";
require_once "PriceHistoryModel.php";
class GiftModel extends CI_Model{

    public function __construct(){
        parent::__construct();
        $this->load->database();
    }
    public function addGift($name, $description, $price, $active){
        $giftId = $this->createGiftId();
        if($active == NULL){$active = 1;}
        $queryString = "INSERT INTO ".GiftDb::$TABLE." (".GiftDb::$GIFT_ID.",".GiftDb::$NAME.",".
            GiftDb::$DESCRIPTION.",".GiftDb::$PRICE.",".GiftDb::$ACTIVE.") VALUES (?,?,?,?,?)";
        $query = $this->db->query($queryString,array($giftId,$name, $description,  $price, $active));
		$priceHistoryModel = new PriceHistoryModel();
		$priceHistoryModel->addPriceHistory($giftId,"Gift",$price);
    }

    public function editGift($giftId, $name, $description, $price, $active,$priceChanged){
        $queryString = "UPDATE ".GiftDb::$TABLE." SET ".GiftDb::$NAME."=? ,".GiftDb::$DESCRIPTION."=? ,".GiftDb::$PRICE."=? ,".
            GiftDb::$ACTIVE."=? WHERE ".
            GiftDb::$GIFT_ID."=?";
        $query = $this->db->query($queryString,array($name, $description, $price,  $active,$giftId));
		if($priceChanged){
			$priceHistoryModel = new PriceHistoryModel();
			$priceHistoryModel->addPriceHistory($giftId,"Gift",$price);
		}
    }

    public function getGift($giftId){
    	$query = $this->db->get_where(GiftDb::$TABLE,array(GiftDb::$GIFT_ID=>$giftId));
    	return $query->row_array();
	}
    public function getPriceHistory($giftId){
//        $queryString = "SELECT ".GiftDb::$PRICE_HISTORY." FROM ".GiftDb::$TABLE." WHERE ".GiftDb::$GIFT_ID."=?";
//        $query = $this->db->query($queryString,array($giftId));
//        $row = $query->row_array();
//        return $row[GiftDb::$PRICE_HISTORY];
		return null;
    }

    public function getActiveGiftList(){
        $queryString = "SELECT * FROM ".GiftDb::$TABLE." WHERE ".GiftDb::$ACTIVE."=1";
        $query = $this->db->query($queryString);
        return $query->result_array();
    }
    public function getGiftList(){
        $queryString = "SELECT * FROM ".GiftDb::$TABLE;
        $query = $this->db->query($queryString);
        return $query->result_array();
    }
    public function deactivateGiftId($giftId){
        $queryString  = "UPDATE ".GiftDb::$TABLE." SET ".GiftDb::$ACTIVE."=0 WHERE ".GiftDb::$GIFT_ID."=?";
        $query = $this->db->query($queryString,array($giftId));
    }
    public function activateGiftId($giftId){
        $queryString  = "UPDATE ".GiftDb::$TABLE." SET ".GiftDb::$ACTIVE."=1 WHERE ".GiftDb::$GIFT_ID."=?";
        $query = $this->db->query($queryString,array($giftId));
    }
    private function createGiftId(){
        $queryString = "SELECT ".GiftDb::$GIFT_ID." FROM ".GiftDb::$TABLE;
        while(1){
            $flag = 0;
            $query = $this->db->query($queryString);
            $id = generateUniqueID(10);
            $row = $query->row_array();
            while($row){
                if($row[GiftDb::$GIFT_ID] == $id){
                    $flag = 1;
                    break;
                }
                $row = $query->next_row('array');
            }
            if($flag == 1){continue;}
            else{break;}
        }
        return $id;
    }
}
