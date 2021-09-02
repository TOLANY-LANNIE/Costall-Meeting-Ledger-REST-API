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
      
            $stmt = $this->con->prepare("INSERT INTO registration_info (Registration_ID,Name, Surname, DOB, Email, Profession, HourlyRate, Cellphone_Number, Organisation_ID, User_ID) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?);");
            $stmt->bind_param("ssssssssss", $registration_ID, $name, $surname, $dob, $email, $profession, $hourlyrate, $cellNum, $organisation_ID, $userID);
            if ($stmt->execute()) {
               return RECORD_CREATED_SUCCESSFULLY;
               
            } else
               return FAILED_TO_CREATE_RECORD;
               die( "Error preparing: (" .$con->errno . ") " . $con->error);
            
        }
        /*
         METHOD: delete profile
         PARAMS: non
        */
        public function deleteAccount($userID,$currentPassword, $newPassword){
            
        }
}


