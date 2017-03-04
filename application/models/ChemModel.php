<?php
require_once "ChemistProfileDb.php";
require_once(APPPATH.'helpers/geotag_match.php');
require_once(APPPATH.'helpers/generateUniqueID.php');
class ChemModel extends CI_Model{
	
	public function __construct(){   
		parent::__construct();
		$this->load->database();
    }
    public function addProfile($name,$phone,$email,$geotag,$active_MR,$chem_image,$station_name,$contact_person,
			$dob,$anniversary,$mr_core,$doc_relation,$probable_stocklist,$shipping_address){
		$chem_id = $this->createChemID();
		// TODO :: When doctor profile is added associated chemist are also send ..take care of it.
		$queryString = "INSERT INTO ".ChemDb::$TABLE." (".ChemDb::$NAME.",".ChemDb::$PHONE.",".ChemDb::$EMAIl.",".ChemDb::$GEOTAG.
			",".ChemDb::$ACTIVE_MR.",".ChemDb::$PHOTO.",".ChemDb::$CHEM_ID.",".ChemDb::$STATION.",".ChemDb::$CONTACT_PERSON.
			",".ChemDb::$DOB.",".ChemDb::$ANNIVERSARY.",".ChemDb::$MR_CORE.",".ChemDb::$DOC_RELATION.
			",".ChemDb::$PROBABLE_STOCKLIST.",".ChemDb::$SHIPPING_ADDRESS.") VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
		$query = $this->db->query($queryString,array($name,$phone,$email,$geotag,$active_MR,$chem_image,$chem_id,$station_name,
			$contact_person,$dob,$anniversary,$mr_core,$doc_relation,$probable_stocklist,$shipping_address));
		return $chem_id;											
	}
	public function editChemProfile($chemId,$name,$phone,$stationName,$email,$geotags,
			$contactPerson,$profilePic,$DOB,$anniversary,$probableStocklist,$MRCore,
			$shippingAddress,$docRelation,$activeMR,$active){
		$data = array(
			ChemDb::$NAME=>$name,
			ChemDb::$PHONE=>$phone,
			ChemDb::$GEOTAG=>$geotags,
			ChemDb::$EMAIl=>$email,
			ChemDb::$ACTIVE_MR=>$activeMR,
			ChemDb::$DOB=>$DOB,
			ChemDb::$ANNIVERSARY=>$anniversary,
			ChemDb::$CONTACT_PERSON=>$contactPerson,
			ChemDb::$PHOTO=>$profilePic,
			ChemDb::$PROBABLE_STOCKLIST=>$probableStocklist,
			ChemDb::$SHIPPING_ADDRESS=>$shippingAddress,
			ChemDb::$MR_CORE=>$MRCore,
			ChemDb::$DOC_RELATION=>$docRelation,
			ChemDb::$STATION=>$stationName,
			ChemDb::$ACTIVE=>$active,
		);
		$query = $this->db->update(ChemDb::$TABLE,$data,array(ChemDb::$CHEM_ID=>$chemId));
    }
    public function isDuplicate($name,$phone/*$geotag,$tolerance*/){
		$queryString = "SELECT * FROM ".ChemDb::$TABLE." WHERE ".ChemDb::$NAME."=? AND ".ChemDb::$PHONE."=?";
		$query = $this->db->query($queryString,array($name,$phone));
		if($query->num_rows() == 0){
			return false;
		}else{
			return true;
		}
    }
	public function isChemID_Active($chem_Id){
		$queryString = "SELECT * FROM ".ChemDb::$TABLE." WHERE ".ChemDb::$CHEM_ID."=? AND ".ChemDb::$ACTIVE."=1";
		$query = $this->db->query($queryString,$chem_Id);
		if($query->num_rows() == 0 ){
			return false;
			} 
		else return true;
		}
	public function isAssoc($chem_id,$MR_id){
		$queryString = "SELECT * FROM ".ChemDb::$TABLE." WHERE ".ChemDb::$CHEM_ID."=? AND ".ChemDb::$ACTIVE_MR."=?";
		$query = $this->db->query($queryString,array($chem_id,$MR_id));
		if($query->num_rows() == 0){
			return false;
			}
		else return true;
		}
	public function getChemist($chem_id){
		$queryString = "SELECT * FROM ".ChemDb::$TABLE." WHERE ".ChemDb::$CHEM_ID."=?";
		$query = $this->db->query($queryString,array($chem_id));
		$result = $query->row_array();
		return $result;
		}
	public function getAllChemist($mr_id){
		$queryString = "SELECT * FROM ".ChemDb::$TABLE." WHERE ".ChemDb::$ACTIVE_MR."=?";
		$query = $this->db->query($queryString,array($mr_id));
		$result = $query->result_array();
		return $result;
		}
	public function assignChemist($chemId,$MRId){
		$chemTableData = array(
			DocDb::$ACTIVE_MR=>$MRId,
		);
		$this->db->update(DocDb::$TABLE,$chemTableData,array(ChemDb::$CHEM_ID=>$chemId));
		$this->db->trans_complete();
		if($this->db->trans_status() == True ){
			$mrHistoryModel = new MRHistoryModel();
			$mrHistoryModel->addMRHistory($chemId,"Chemist",$MRId);
		}else{
			response($this,false,430,"Database Error : MR assign Chemist");
		}
	}
	private function createChemID(){
		$queryString = "SELECT ".ChemDb::$CHEM_ID." FROM ".ChemDb::$TABLE;
		while(1){
			$flag = 0;
			$query = $this->db->query($queryString);
			$id = generateUniqueID(10);
			$row = $query->row_array();
			while($row){
				if($row[ChemDb::$CHEM_ID] == $id){
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
	public function getChemProfiles($mr_id){
		$queryString = "SELECT * FROM ".ChemDb::$TABLE." WHERE ".ChemDb::$ACTIVE_MR."=?";
		$query = $this->db->query($queryString,$mr_id);
		return $result_array = $query->result_array();
	}
	public function activateChem($chemId){
		$data = array(
			ChemDb::$ACTIVE => 1
		);
		$this->db->update(ChemDb::$TABLE,$data,array(ChemDb::$CHEM_ID=>$chemId));
	}
	public function deactivateChem($chemId){
		date_default_timezone_set('Asia/Kolkata');
		$inactiveDate = date('m/d/Y H:i:s');
		$data = array(
			ChemDb::$ACTIVE => 0,
			ChemDb::$INACTIVE_DATE=>$inactiveDate
		);
		$this->db->update(ChemDb::$TABLE,$data,array(ChemDb::$CHEM_ID=>$chemId));
	}
}
