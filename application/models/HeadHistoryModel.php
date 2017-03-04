<?php

/**
 * Created by PhpStorm.
 * User: sahil333
 * Date: 14/1/17
 * Time: 10:48 PM
 */
require_once "HeadHistoryDb.php";
class HeadHistoryModel extends CI_Model {
    public function __construct(){
        parent::__construct();
        $this->load->database();
    }

    public function addHeadHistory($userId,$headId){
        $time = Date("m/d/Y H:i:s");
        $data =array(
            HeadHistoryDb::$USER_ID => $userId,
            HeadHistoryDb::$HEAD_ID => $headId,
            HeadHistoryDb::$DATE => $time
        );
        $this->db->insert(HeadHistoryDb::$TABLE,$data);
    }

    public function getHeadHistory($userId){
        $queryString = "SELECT * FROM ".HeadHistoryDb::$TABLE." WHERE ".HeadHistoryDb::$USER_ID."=?";
        $query = $this->db->query($queryString,array($userId));
        $result = sortByDate($query->result_array());
        return $result;
    }

    public function getLastNHeads($userId,$n){
        $queryString = "SELECT * FROM ".HeadHistoryDb::$TABLE." WHERE ".HeadHistoryDb::$USER_ID."=?";
        $query = $this->db->query($queryString,array($userId));
        $headHistory = sortByDate($query->result_array());
        $lastNHistory = array_slice($headHistory,0,$n);
        return $lastNHistory;
    }
    public function getHeadHistorySince($userId,$date){
        $queryString  = "SELECT * FROM ".HeadHistoryDb::$TABLE." WHERE ".HeadHistoryDb::$USER_ID."=?";
        $query = $this->db->query($queryString,array($userId));
        $headHistory = getHistoryAfter($query->result_array(),$date);
        return $headHistory;
    }
}