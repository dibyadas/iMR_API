<?php
/**
 * Created by PhpStorm.
 * User: sahil333
 * Date: 28/12/16
 * Time: 7:02 PM
 */
function createNewPriceHistory($price){
    date_default_timezone_set('Asia/Kolkata');
    $newHistory["priceHistory"] = array();
    $newHistory["priceHistory"][0]["date"] = date('m/d/Y H:i:s');
    $newHistory["priceHistory"][0]["price"] = $price;
    return json_encode($newHistory);
}

function addPriceHistory($priceHistory,$newPrice){
    date_default_timezone_set('Asia/Kolkata');
    if($priceHistory != NULL && $priceHistory != "") {
        $History = json_decode($priceHistory, true);
        $entity["date"] = date('m/d/Y H:i:s');
        $entity["price"] = $newPrice;
        array_push($History["priceHistory"], $entity);
        return json_encode($History);
    }else{
        return createNewPriceHistory($newPrice);
    }
}

function getCurrentPrice($priceHistory){
    if($priceHistory != NULL && $priceHistory != "") {
        $History = json_decode($priceHistory, true);
        $entity = end($History["priceHistory"]);
        return $entity["price"];
    }else{
        return 0;
    }
}



