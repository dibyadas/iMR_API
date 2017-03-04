<?php
require_once "DoctorProfileDb.php";
require_once(APPPATH.'helpers/geotag_match.php');
require_once(APPPATH.'helpers/createLink.php');
require_once(APPPATH.'helpers/generateUniqueID.php');
require_once "MRHistoryModel.php";
require_once "DocChemDb.php";
class DocModel extends CI_Model{
	
	public function __construct(){   
		parent::__construct();
		$this->load->database();
    }
	
	public function addProfile($name,$phone,$specialization,$geotag,$photo,$email,$active_MR,$pad_image,$dob,
			$anniversary,$visit_day,$meeting_time,$set_no,$clinic_image,$class,$mr_core,$am_core,$rm_core,$inactive_date,
			$assistant_phone,$office_phone,$visit_freq,$monthly_business,$pat_freq,$station_name,$qualification,$sex,$assocChemist){
		$doc_id = $this->createDocID();
		$queryString = "INSERT INTO ".DocDb::$TABLE." (".DocDb::$NAME.",".DocDb::$EMAIl.",".DocDb::$PHONE.",".DocDb::$DOB.
			",".DocDb::$GEOTAG.",".DocDb::$SPECIALIZATION.",".DocDb::$ANNIVERSARY.",".DocDb::$PHOTO.
			",".DocDb::$PRE_PAD.",".DocDb::$ACTIVE_MR.",".DocDb::$DOC_ID.",".DocDb::$VISIT_DAY.",".DocDb::$MEETING_TIME.
			",".DocDb::$SET_NO.",".DocDb::$CLINIC_IMG.",".DocDb::$CLASS.",".DocDb::$MR_CORE.",".DocDb::$AM_CORE.
			",".DocDb::$RM_CORE.",".DocDb::$INACTIVE_DATE.",".DocDb::$ASSISTANT_PHONE.",".DocDb::$OFFICE_PHONE.
			",".DocDb::$VISIT_FREQ.",".DocDb::$MONTHLY_BUSINESS.",".DocDb::$PAT_FREQ.",".DocDb::$STATION.
			",".DocDb::$QUALIFICATION.",".DocDb::$SEX.
			") VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
		$query = $this->db->query($queryString,array($name,$email,$phone,$dob,$geotag,$specialization,$anniversary,$photo,
			$pad_image,$active_MR,$doc_id,$visit_day,$meeting_time,$set_no,$clinic_image,$class,$mr_core,$am_core,$rm_core,
			$inactive_date,$assistant_phone,$office_phone,$visit_freq,$monthly_business,$pat_freq,$station_name,$qualification,
			$sex));
		$this->addAssociatedChemist($doc_id,$assocChemist);
		$MRHistoryModel = new MRHistoryModel();
		$MRHistoryModel->addMRHistory($doc_id,"Doctor",$active_MR);
		return $doc_id;
	}

	public function addAssociatedChemist($docId,$assocChemist){
		$data = array(
			DocChemDb::$DOC_ID=>$docId,
			DocChemDb::$ASS_CHEM_ID_1=>$assocChemist[DocChemDb::$ASS_CHEM_ID_1],
			DocChemDb::$ASS_CHEM_ID_2=>$assocChemist[DocChemDb::$ASS_CHEM_ID_2],
			DocChemDb::$ASS_CHEM_ID_3=>$assocChemist[DocChemDb::$ASS_CHEM_ID_3]
		);
		$this->db->insert(DocChemDb::$TABLE,$data,array(DocChemDb::$DOC_ID=>$docId));
	}
	public function getAssociatedChemist($docId){
		$query = $this->db->get_where(DocChemDb::$TABLE,array(DocChemDb::$DOC_ID=>$docId));
		return $query->row_array();
	}
	private function createDocID(){
		$queryString = "SELECT ".DocDb::$DOC_ID." FROM ".DocDb::$TABLE;
		while(1){
			$flag = 0;
			$query = $this->db->query($queryString);
			$id = generateUniqueID(10);
			$row = $query->row_array();
			while($row){
				if($row[DocDb::$DOC_ID] == $id){
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
	public function isDuplicate($name,$phone/*$dob,$geotag,$tolerance*/){
		$queryString = "SELECT * FROM ".DocDb::$TABLE." WHERE ".DocDb::$NAME."=? AND ".DocDb::$PHONE."=?";
		$query = $this->db->query($queryString,array($name,$phone));
		if($query->num_rows() == 0){
			return False;
			}
		else{
			//~ $row = $query->row_array();
			//~ while($row){
				//~ if(geoMatch($geotag,$row[DocDb::$GEOTAG],$tolerance)){
					//~ return true;
					//~ }
				//~ $row = $query->next_row('array');
				//~ }
			//~ return false;
			return true;
			}
		}
	public function isDocID_Active($doc_Id){
		$queryString = "SELECT * FROM ".DocDb::$TABLE." WHERE ".DocDb::$DOC_ID."=? AND ".DocDb::$ACTIVE."=1";
		$query = $this->db->query($queryString,$doc_Id);
		if($query->num_rows() == 0 ){
			return false;
			} 
		else return true;
		}
	public function isAssoc($doc_id,$MR_id){
		$queryString = "SELECT * FROM ".DocDb::$TABLE." WHERE ".DocDb::$DOC_ID."=? AND ".DocDb::$ACTIVE_MR."=?";
		$query = $this->db->query($queryString,array($doc_id,$MR_id));
		if($query->num_rows() == 0){
			return false;
			}
		else return true;
		}
	public function getDocProfiles($mr_id){
		$queryString = "SELECT * FROM ".DocDb::$TABLE." WHERE ".DocDb::$ACTIVE_MR."=?";
		$query = $this->db->query($queryString,$mr_id);
		return $result_array = $query->result_array();
	}
	public function getDoctor($doc_id){
		$queryString = "SELECT * FROM ".DocDb::$TABLE." WHERE ".DocDb::$DOC_ID."=?";
		$query = $this->db->query($queryString,array($doc_id));
		$result = $query->row_array();
		return $result;
		}
	public function getAllDoctor($mr_id){
		$queryString = "SELECT * FROM ".DocDb::$TABLE." WHERE ".DocDb::$ACTIVE_MR."=?";
		$query = $this->db->query($queryString,array($mr_id));
		$result = $query->result_array();
		return $result;
		}
	public function activateDoc($docId){
		$data = array(
			DocDb::$ACTIVE => 1
		);
		$this->db->update(DocDb::$TABLE,$data,array(DocDb::$DOC_ID=>$docId));
	}
	public function deactivateDoc($docId){
		date_default_timezone_set('Asia/Kolkata');
		$inactiveDate = date('m/d/Y H:i:s');
		$data = array(
			DocDb::$ACTIVE => 0,
			DocDb::$INACTIVE_DATE=>$inactiveDate
		);
		$this->db->update(DocDb::$TABLE,$data,array(DocDb::$DOC_ID=>$docId));
	}

	public function editDocProfile($docId,$name,$phone,$specialization,$geotag,$profileImage,$email,$activeMR,$padImage,$DOB,
	$anniversary,$visitDay,$meetingTime,$setNo,$clinicImage,$class,$MRCore,$AMCore,$RMCore,$inactiveDate,
	$assistantPhone,$officePhone,$visitFreq,$monthlyBusiness,$patFreq,$stationName,$qualification,$sex,$active){
		$data = array(
			DocDb::$NAME=>$name,
			DocDb::$PHONE=>$phone,
			DocDb::$SPECIALIZATION=>$specialization,
			DocDb::$GEOTAG=>$geotag,
			DocDb::$EMAIl=>$email,
			DocDb::$ACTIVE_MR=>$activeMR,
			DocDb::$DOB=>$DOB,
			DocDb::$ANNIVERSARY=>$anniversary,
			DocDb::$VISIT_DAY=>$visitDay,
			DocDb::$MEETING_TIME=>$meetingTime,
			DocDb::$SET_NO=>$setNo,
			DocDb::$PHOTO=>$profileImage,
			DocDb::$CLINIC_IMG=>$clinicImage,
			DocDb::$PRE_PAD=>$padImage,
			DocDb::$CLASS=>$class,
			DocDb::$MR_CORE=>$MRCore,
			DocDb::$AM_CORE=>$AMCore,
			DocDb::$RM_CORE=>$RMCore,
			DocDb::$INACTIVE_DATE=>$inactiveDate,
			DocDb::$ASSISTANT_PHONE=>$assistantPhone,
			DocDb::$OFFICE_PHONE=>$officePhone,
			DocDb::$VISIT_FREQ=>$visitFreq,
			DocDb::$MONTHLY_BUSINESS=>$monthlyBusiness,
			DocDb::$PAT_FREQ=>$patFreq,
			DocDb::$STATION=>$stationName,
			DocDb::$QUALIFICATION=>$qualification,
			DocDb::$SEX=>$sex,
			DocDb::$ACTIVE=>$active,
		);
		$query = $this->db->update(DocDb::$TABLE,$data,array(DocDb::$DOC_ID=>$docId));
	}
	public function assignDoctor($docId,$setNo,$MRId){
		$docTableData = array(
			DocDb::$ACTIVE_MR=>$MRId,
			DocDb::$SET_NO=>$setNo,
		);
		$this->db->update(DocDb::$TABLE,$docTableData,array(DocDb::$DOC_ID=>$docId));
		$this->db->trans_complete();
		if($this->db->trans_status() == True ){
			$mrHistoryModel = new MRHistoryModel();
			$mrHistoryModel->addMRHistory($docId,"Doctor",$MRId);
		}else{
			response($this,false,430,"Database Error : MR assign Doctor");
		}
	}

}


