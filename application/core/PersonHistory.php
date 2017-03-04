<?php

/**
 * Created by PhpStorm.
 * User: sahil333
 * Date: 14/1/17
 * Time: 1:42 AM
 */
require_once APPPATH."models/PersonHistoryModel.php";
class PersonHistory{
    public static function getPersonHistory($userId){
        $ph = new PersonHistoryModel();
        return json_encode($ph->getPersonHistory($userId));
    }

    public static function getLastNPersons($userId){

    }

    public static function getPersonHistorySince($userId,$date){

    }

    public static function getAssignedIdHistory($personId){

    }

    public static function getLastNAssignedId($personId){

    }

    public static function getAssignedIdHistorySince($personId,$date){

    }
}