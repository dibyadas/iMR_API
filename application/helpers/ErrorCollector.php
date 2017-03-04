<?php
/**
 * Created by PhpStorm.
 * User: sahil333
 * Date: 30/1/17
 * Time: 7:21 PM
 */
class ErrorCollector{
//	private $error = "";
	private $errorArray = array();
	private $context;

	public function __construct($context){
		$this->context = $context;
	}

	public function collect(){
		if($this->context->db->trans_status() == False){
//			$this->error .=  $this->context->db->error()["message"]."\n";
			array_push($this->errorArray,$this->context->db->error()["message"]);
		}
	}
	public function getError(){
//		return implode("\n",$this->errorArray);
		return $this->errorArray[0];
	}
}