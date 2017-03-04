<?php
/**
 * Created by PhpStorm.
 * User: sahil333
 * Date: 26/12/16
 * Time: 11:51 PM
 */
function createNewHeadHistory($headId,$headtype){
    date_default_timezone_set('Asia/Kolkata');
    $headHistory["history"] = array();
    $headHistory["history"][0]["date"] = date('m/d/Y H:i:s');
    $headHistory["history"][0][$headtype] = $headId;
    return json_encode($headHistory);

    // TODO : Doctor Profile me sab VarChar kyu hai ?
}

function addHeadToHistory($headHistory,$newHeadId,$headType){
    date_default_timezone_set('Asia/Kolkata');
    $History = json_decode($headHistory,true);
    $entity["date"] = date('m/d/Y H:i:s');
    $entity[$headType] = $newHeadId;
    array_push($History["history"],$entity);
    return json_encode($History);
}