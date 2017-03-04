<?php
/**
 * Created by PhpStorm.
 * User: sahil333
 * Date: 22/12/16
 * Time: 8:08 PM
 */
function createNewPersonHistory($personId){
    date_default_timezone_set('Asia/Kolkata');
    $personHistory["history"] = array();
    $personHistory["history"][0]["date"] = date('m/d/Y H:i:s');
    $personHistory["history"][0]["person_id"] = $personId;
    return json_encode($personHistory);
}
function addPersonToHistory($personHistory,$newPersonId){
    date_default_timezone_set('Asia/Kolkata');
    if($personHistory!= NULL && $personHistory != "") {
        $History = json_decode($personHistory, true);
        $entity["date"] = date('m/d/Y H:i:s');
        $entity["person_id"] = $newPersonId;
        array_push($History["history"], $entity);
        return json_encode($History);
    }else{
        return createNewPersonHistory($newPersonId);
    }
}
function sortByDate($HistoryArray){
    usort($HistoryArray, 'date_compare');
    return $HistoryArray;
    }
function date_compare($a, $b){
    $t1 = strtotime($a['date']);
    $t2 = strtotime($b['date']);
    return -($t1 - $t2);
}
function getHistoryAfter($history,$date){
    usort($history, 'date_compare');
    $newHistory = array();
    $t_cmp = strtotime($date);
    foreach ($history as $entity){
        if(strtotime($entity['date'])>$t_cmp){
            array_push($newHistory,$entity);
        }
    }
    return $newHistory;
}