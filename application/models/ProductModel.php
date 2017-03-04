<?php
require_once "ProductDb.php";
require_once "MRHistoryModel.php";
class ProductModel extends CI_Model{
	
	public function __construct(){   
		parent::__construct();
		$this->load->database();
    }
    public function addProduct($name, $group, $pack, $price, $PTS, $scheme, $remarks, $active){
	    $productId = $this->createProductId();
	    if($active == NULL){$active = 1;}
	    $queryString = "INSERT INTO ".ProductDb::$TABLE." (".ProductDb::$PRO_ID.",".ProductDb::$NAME.",".
            ProductDb::$GROUP.",".ProductDb::$PACK.",".ProductDb::$PRICE.",".ProductDb::$PTS.",".ProductDb::$SCHEME.
            ",".ProductDb::$REMARKS.",".ProductDb::$IN_PRACTICE.") VALUES (?,?,?,?,?,?,?,?,?)";
	    $query = $this->db->query($queryString,array($productId,$name, $group, $pack, $price, $PTS, $scheme, $remarks, $active));
	    $priceHistoryModel = new PriceHistoryModel();
	    $priceHistoryModel->addPriceHistory($productId,"Product",$price);
    }

    public function editProduct($productId, $name, $group, $pack, $price, $PTS, $scheme, $remarks, $active,$priceChanged){
        $queryString = "UPDATE ".ProductDb::$TABLE." SET ".ProductDb::$NAME."=? ,".ProductDb::$GROUP."=? ,".ProductDb::$PACK."=? ,".ProductDb::$PRICE."=? ,".
            ProductDb::$PTS."=? ,".ProductDb::$SCHEME."=? ,".ProductDb::$REMARKS."=? ,".ProductDb::$IN_PRACTICE."=? WHERE ".
            ProductDb::$PRO_ID."=?";
        $query = $this->db->query($queryString,array($name, $group, $pack, $price, $PTS, $scheme, $remarks,$active,$productId));
		if($priceChanged){
			$priceHistoryModel = new PriceHistoryModel();
			$priceHistoryModel->addPriceHistory($productId,"Product",$price);
		}
    }
	public function getProduct($productId){
    	$query = $this->db->get_where(ProductDb::$TABLE,array(ProductDb::$PRO_ID=>$productId));
    	return $query->row_array();
	}
	public function getPriceHistory($productId){
//		$queryString = "SELECT ".ProductDb::$PRICE_HISTORY." FROM ".ProductDb::$TABLE." WHERE ".ProductDb::$PRO_ID."=?";
//		$query = $this->db->query($queryString,array($productId));
//		$row = $query->row_array();
//		return $row[ProductDb::$PRICE_HISTORY];
		return null;
	}

	public function getProductValue($productId,$units){
		$product = $this->getProduct($productId);
		$value = $product[ProductDb::$PRICE]*$units;
		return $value;
	}


	public function getSampleProductValue($productId,$units){
		$product = $this->getProduct($productId);
		$value = $product[ProductDb::$PRICE]*$units;
		return $value;
	}

	public function getSaleProductValue($productId,$units){
		$product = $this->getProduct($productId);
		$value = $product[ProductDb::$PTS]*$units;
		return $value;
	}

    public function getActiveProductList(){
        $queryString = "SELECT * FROM ".ProductDb::$TABLE." WHERE ".ProductDb::$IN_PRACTICE."=1";
        $query = $this->db->query($queryString);
        return $query->result_array();
    }
    public function getProductList(){
        $queryString = "SELECT * FROM ".ProductDb::$TABLE;
        $query = $this->db->query($queryString);
        return $query->result_array();
    }
    public function deactivateProductId($productId){
        $queryString  = "UPDATE ".ProductDb::$TABLE." SET ".ProductDb::$IN_PRACTICE."=0 WHERE ".ProductDb::$PRO_ID."=?";
        $query = $this->db->query($queryString,array($productId));
    }
    public function activateProductId($productId){
        $queryString  = "UPDATE ".ProductDb::$TABLE." SET ".ProductDb::$IN_PRACTICE."=1 WHERE ".ProductDb::$PRO_ID."=?";
        $query = $this->db->query($queryString,array($productId));
    }
    private function createProductId(){
        $queryString = "SELECT ".ProductDb::$PRO_ID." FROM ".ProductDb::$TABLE;
        while(1){
            $flag = 0;
            $query = $this->db->query($queryString);
            $id = generateUniqueID(10);
            $row = $query->row_array();
            while($row){
                if($row[ProductDb::$PRO_ID] == $id){
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
