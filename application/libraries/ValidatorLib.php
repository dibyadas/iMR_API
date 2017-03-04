<?php

/**
 * Created by PhpStorm.
 * User: sahil333
 * Date: 26/1/17
 * Time: 3:50 PM
*/

if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include APPPATH.'thirdparty/JsonSchema/Validator.php';
class ValidatorLib extends Validator {
	function __construct() {
		parent::__construct();
	}
}