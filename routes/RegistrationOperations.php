<?php
 class RegistrationOperations{

        private $con; 

        function __construct(){
            require_once '../config/DbConnect.php';
            //require_once dirname(__FILE__) . '';
            require_once dirname(__FILE__) .'/UserOperations.php';
            require_once dirname(__FILE__) .'/OrganisationOperations.php';
            
            $db = new DbConnect; 
            $this->con = $db->connect(); 
        }
        
        /*
         METHOD: registers new user
         PARAMS: $name, $surname, $dob, $email, $profession, $hourlyrate, $cellNum, $organization, $username, $password
        */
        public function registerUser($name, $surname, $dob, $email, $profession, $hourlyrate, $cellNum, $organization, $username, $password) {
            $organisationClass = new OrganisationOperations();
            $userClass  = new UserOperations();
            
            
            $registration_ID = uniqid('REG');
            $organisation_ID = $organisationClass->addOrganization($organization);
            $userID = $userClass->createUser($username, $password);
            if($userID == RECORD_EXISTS){
                return RECORD_EXISTS;
            }
            if($this->emailExist($email) >0){
                return RECORD_EXISTS;
            }

            $stmt = $this->con->prepare("INSERT INTO registration_info (Registration_ID,Name, Surname, DOB, Email, Profession, HourlyRate, Cellphone_Number, Organisation_ID, User_ID) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?);");
                $stmt->bind_param("ssssssssss", $registration_ID, $name, $surname, $dob, $email, $profession, $hourlyrate, $cellNum, $organisation_ID, $userID);
                if ($stmt->execute()) {
                    return RECORD_CREATED_SUCCESSFULLY;
               
                } else
                return FAILED_TO_CREATE_RECORD;
        }
        /*
         METHOD: delete profile
         PARAMS: non
        */
        public function deleteAccount($userID,$currentPassword, $newPassword){
            
        }
        //check if email address exists
        private function emailExist($email){
            $stmt = $this->con->prepare("SELECT Email FROM registration_info WHERE Email = ?");
              $stmt->bind_param("s", $email);
              $stmt->execute(); 
              $stmt->store_result(); 
              return $stmt->num_rows > 0;  
       }
}


