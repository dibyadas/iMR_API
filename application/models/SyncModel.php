<?php

/**
 * Created by PhpStorm.
 * User: sahil333
 * Date: 9/1/17
 * Time: 3:54 PM
 */
class SyncModel extends CI_Model {
    public function __construct(){
        parent::__construct();
        $this->load->database();
    }

    public function addNewSyncable($rowId,$tableName,$changedBy,$changeType,$editorRole){
        $queryString = "INSERT INTO ".SyncDB::$TABLE." (".SyncDB::$ROW_ID.",".SyncDB::$TABLE_NAME.",".SyncDB::$EDITOR.",".SyncDB::$CHANGE_TYPE.",".SyncDB::$EDITOR_ROLE.") VALUES (?,?,?,?,?)";
        $query = $this->db->query($queryString,array($rowId,$tableName,$changedBy,$changeType,$editorRole));
    }

    public function getSyncableAfter($time){

    }

    public function getSyncableByTableAndAfter($tableName,$time){

    }

    public function getSyncableByTable($tableName){

    }
}