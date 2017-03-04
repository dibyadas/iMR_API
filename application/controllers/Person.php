<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require(APPPATH.'libraries/REST_Controller.php');
require(APPPATH.'helpers/response.php');
require(APPPATH.'helpers/EmptyString.php');
require(APPPATH.'helpers/authenticate.php');
require_once(APPPATH.'libraries/jwt_helper.php');
require_once (APPPATH.'helpers/passwordChangeEmail.php');

/**
 * Created by PhpStorm.
 * User: sahil333
 * Date: 22/12/16
 * Time: 10:40 PM
 */
class Person extends REST_Controller {
    private $token_payload;
    public function __construct(){
        parent::__construct();
        $this->load->model('PersonModel',"Person_");
        $this->load->model('EmployeeModel',"Employee_");


        if($this->router->method != "passwordForgotRequest" && $this->router->method != "passwordChangeRequestEmail" ){
            try{
                $this->token_payload = authenticate($this);
            }
            catch(Exception $e){
                response($this,false,401,"",$e->getMessage());		// 401 -> invalid token
            }
        }
    }

    public function profiles_get(){
        response($this,true,200,$this->Person_->getAllPersonProfiles());
    }


    public function unassigned_get(){
        response($this,true,200,$this->Person_->getUnassignedPersonProfiles());
    }



    public function add_post(){
        // New person can be added only by admin

        if($this->token_payload["own"] == "Admin"){
            // Collect all details

            $name = $this->post(PersonDb::$NAME);
            $email = $this->post(PersonDb::$EMAIL);
            $phone = $this->post(PersonDb::$PHONE);
            $password = $this->post(PersonDb::$PASSWORD);

            // Create a hashed Password by appending LoginSalt.
            $hashedPassword = md5($password.Constants::$LOGIN_SALT);
            $sex = $this->post(PersonDb::$SEX);
            $DOB = $this->post(PersonDb::$DOB);

            // Profile can only be generated from web  so pic would be uploaded upfront and only link would be send here.
            // So no need to upload here itself
            $profilePic = $this->post(PersonDb::$PROFILE_PIC);

            try{
                //Add new Person Into Database
                $this->Person_->addNewPerson($name,$email,$phone,$hashedPassword,$DOB,$profilePic,$sex);
                response($this,true,200,"New Person Added successfully");
            }catch (Exception $e){
                response($this,false,412,"","Database error : New Person couldn't be added");
            }
        }else{
            response($this,false,430,"","You don't have permission for this action");
        }
    }

    private function getPersonId(){
		$personId = NULL;
    	if($this->token_payload["own"] == "Admin"){
			if($this->post("person_id") == NULL){
				$personId = $this->token_payload["person_id"];
			}else{
				$personId = $this->post("person_id");
			}
		}else{
//			if($this->post("person_id") == NULL){
//				response($this,false,476,"","Please Provide your person ID");
//			}else{
//				$personId = $this->post("person_id");
//			}
		$personId = $this->token_payload["person_id"];
		}
		return $personId;
	}

    public function edit_post(){
		if($this->token_payload["own"] == "Admin") {
			$personId = $this->post(PersonDb::$PERSON_ID);
			/*----------------------*/

			// Ask for the new details

			$newName = $this->post(PersonDb::$NAME);
			$newEmail = $this->post(PersonDb::$EMAIL);
			$newPhone = $this->post(PersonDb::$PHONE);
			$newSex = $this->post(PersonDb::$SEX);
			$newDOB = $this->post(PersonDb::$DOB);
			$newProfilePic = $this->post(PersonDb::$PROFILE_PIC);
			try {
				$this->Person_->editPerson($personId, $newName, $newEmail, $newPhone, $newDOB, $newProfilePic, $newSex);
				response($this, true, 200, "Person data saved successfully");
			} catch (Exception $e) {
				response($this, false, 412, "", "Database error : Person data couldn't be saved");
			}
		}else{
			response($this,false,430,"","You don't have permission for this action");
		}
    }

    public function passwordChange_post(){

        // If admin is asking for changing the password then use posted PersonID

		$personId = $this->getPersonId();
		$newPassword = $this->post("new_password");
		if($this->token_payload["own"] == "Admin"){
			$adminPassword = $this->post("password");
			$currentAdminPassword = $this->Person_->getPassword($this->token_payload["person_id"]);
			if($currentAdminPassword == md5($adminPassword . Constants::$LOGIN_SALT )){
				$this->Person_->updatePassword($personId, md5($newPassword . Constants::$LOGIN_SALT));
				response($this, true, 200, "Password changed successfully");
			}else{
				response($this,false,455,"","Your Admin Password is incorrect.");
			}
		}else{
			$oldPassword = $this->post("password");
			$currentPassword = $this->Person_->getPassword($personId);
			if ($currentPassword == md5($oldPassword . Constants::$LOGIN_SALT)) {
				$this->Person_->updatePassword($personId, md5($newPassword . Constants::$LOGIN_SALT));
				response($this, true, 200, "Password changed successfully");
			} else {
				response($this, false, 401, "", "Password is invalid");
			}
		}
        /*----------------------*/
    }

    public function passwordForgotRequest_post(){
        // Ask The user of his/her userId
        $userId = $this->post("user_id");

        // Get PersonId associated with this user.

        $personId  = $this->Employee_->getActivePersonId($userId);

        // Check if matching person found or not

        if($personId){
            $person = $this->Person_->getPerson($personId);

            //  Check if email exist or not.
            // If not user will have to ask the admin personally to add his email or change password for him.
            if(isset($person[PersonDb::$EMAIL]) && $person[PersonDb::$EMAIL] != ""){
                // Begin building the payload for the key which would be send to his email as a link.
                $payload = $person;
                $payload["iat"] = microtime(true);

                // The user will need to access the link within 24 hours ot it will be expired.

                $payload["exp"] = microtime(true)+3600*24;

                // Build the key using the Password Change Token
                $key = JWT::encode($payload,Constants::$PASSWORD_CHANGE_TOKEN_KEY);
                // Send Email to the user email.

                // TODO : Write the function to send password change request email with link
				print_r($key);
				exit();
                sendPasswordChangeEmail($key,$person[PersonDb::$EMAIL],$person[PersonDb::$NAME]);
                response($this,true,200,"Email sent to your ".$person[PersonDb::$EMAIL]." email. Please click on the link provided within 24 hours");
            }else{
                response($this,false,435,"","Your email is not present. PLease ask an admin to fill your email");
            }
        }else{
            response($this,false,432,"","No User Id matched. It doesn't exist or has been deactivated");
        }
    }

    public function passwordChangeRequestEmail_post(){
        // Ask for the key send to the user email
        $key = $this->get("key");

        // Try decoding the key. If key was issued by us using password_change_token_key then it is great
        // else catch exception
        try{
            $payload = (array)JWT::decode($key,Constants::$PASSWORD_CHANGE_TOKEN_KEY);

            // If Key was not expired then go as it is
            if(microtime(true) > $payload["exp"] ){
                response($this,false,434,"","Your link has expired. Please try again");
            }else{
                // Update new password.

                $newPassword = $this->post("newPassword");
                $this->Person_->updatePassword($payload[PersonDb::$PERSON_ID],md5($newPassword.Constants::$LOGIN_SALT));
                response($this,true,200,"Your Password has been changed successfully.");
            }
        }catch (Exception $e){
            response($this,false,436,"","Please use the sent link in the email to change password");
        }

    }

    public function info_get(){
    	$personId = $this->get("person_id");
    	response($this,true,200,$this->Person_->getPerson($personId));
	}
}