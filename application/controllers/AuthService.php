<?php

/**
 * Created by PhpStorm.
 * User: sahil333
 * Date: 30/1/17
 * Time: 3:58 PM
 */
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH."controllers/Employee.php");
class AuthService{
	/*  TODO : Issue - Assumption: one Chemist can be associated with multiple doctors. Generally they will be associated
		with doctors with same set_no , if not kept in check and Chemist associated in cross sets then, propagation
		of doctor_profile to some other MR will be difficult or might produce unexpected inconsistency.

		Possible Solution : Chemist should be in a common pool for every ID. From where doctor can be associated with
		any chemist. Since, it is a common pool, Doctor profile migration will not effect anyone. We can ofCourse group
		Chemist based on some independent set or HQ. Some Sensitive data can be handled by Admin only.

	*/

	// TODO :: Trim all inputs

	public static function WCPAdd($context,$month,$year){
		// TODO : What to do if MR didn't made the WCP on time and need to make it after deadline.
		$month = DateTime::createFromFormat('F', $month);
		$year = DateTime::createFromFormat('Y', $year);

		$WCPAddStartDate = new DateTime(date('m/d/Y H:i:s', strtotime(date(($month->format('m')-1).'/20/'.$year->format('Y').' 00:00:00'))));
		$todayDate = new DateTime(date('m/d/Y H:i:s'));
 		$WCPAddEndDate = new DateTime(date('m/d/Y H:i:s', strtotime(date(($month->format('m')-1).'/26/'.$year->format('Y').' 12:59:59'))));
 		$withinAddDeadline = $todayDate>$WCPAddStartDate && $todayDate<$WCPAddEndDate;
 		if(!$withinAddDeadline){
 			return false;
		}
 		elseif($context->token_payload["own"] == "MR"){
 			return true;
		}else{
			return false;
		}
	}

	public static function WCPEditException($context,$WCPWrapId){
		$requesterRole = $context->token_payload["own"];
		if($requesterRole == "Admin"){
			return true;
		}else{
			return false;
		}
	}


	public static function WCPEdit($context,$WCPWrapId){
		$requesterUserId = $context->token_payload["user_id"];
		$requesterRole = $context->token_payload["own"];


		$WCPWrap = $context->WCPW_->getWCPWrap($WCPWrapId);
		$submitted = $WCPWrap[WCPWrapDb::$SUBMIT_STATUS];
		$approved = $WCPWrap[WCPWrapDb::$APPROVAL_STATUS];
		$exception = $WCPWrap[WCPWrapDb::$IS_EXCEPTED];

		$month = DateTime::createFromFormat('F', $WCPWrap[WCPWrapDb::$MONTH]);
		$year = DateTime::createFromFormat('Y', $WCPWrap[WCPWrapDb::$YEAR]);

		$MRId = $WCPWrap[WCPWrapDb::$MR_ID];
		if($requesterRole == "Admin"){
			if($submitted)return true;
			else return false;
		}
		if(self::inHeadHierarchy($context,$requesterUserId,$MRId)){
			$todayDate = new DateTime(date('m/d/Y H:i:s'));

			$MRWCPEditStartDate = new DateTime(date('m/d/Y H:i:s', strtotime(date(($month->format('m')-1).'/20/'.$year->format('Y').' 00:00:00'))));
			$MRWCPEditEndDate = new DateTime(date('m/d/Y H:i:s', strtotime(date(($month->format('m')).'/1/'.$year->format('Y').' 12:59:59'))));

			$headWCPEditStartDate = new DateTime(date('m/d/Y H:i:s', strtotime(date(($month->format('m')-1).'/26/'.$year->format('Y').' 00:00:00'))));
			$headWCPEditEndDate = new DateTime(date('m/d/Y H:i:s', strtotime(date(($month->format('m')-1).'/1/'.$year->format('Y').' 12:59:59'))));

			$withinDeadlineMR = $todayDate>$MRWCPEditStartDate && $todayDate<$MRWCPEditEndDate;
			$withinDeadlineHead = $todayDate>$headWCPEditStartDate && $todayDate<$headWCPEditEndDate;

			// TODO :: Critical point is if all three are 1 and this shouldn't happen ever. if exception id 1 then submitted
			// and approved can't be both 1. restricted states are 000,100,110,001,101. approval authorization is with heads
			// and submission authorization is with MR. approval and submission should have separate APIs not within th edit.


			if($requesterRole == "MR"){
//				print("iMR\n");
				if(!$submitted && !$approved){
					if($withinDeadlineMR){
						return true;
					}elseif($exception){
						return true;
					}else{
						return false;
					}
				}else{
					return false;
				}
			}else{
//				print("ahead\n");
				if($submitted && !$approved){
					if($withinDeadlineHead){
						return true;
					}elseif ($exception){
						return true;
					}else{
						return false;
					}
				}else{
					return false;
				}
			}
		}else{
			return false;
		}
	}

	public static function inHeadHierarchy($context,$headId, $userId){
		$headHierarchy = $context->Employee_->getHeadHierarchy($userId);
		$headNode = $headHierarchy;
		do{
			if($headNode["user_id"] == $headId){
				return true;
			}else{
				$headNode = $headNode["head_node"];
			}
		}while($headNode != NULL || count($headNode) != 0);
		return false;
	}
}