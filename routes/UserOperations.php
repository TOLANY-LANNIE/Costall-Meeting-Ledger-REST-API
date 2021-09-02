<?php
 class UserOperations{

        private $con; 

        function __construct(){
            //require_once dirname(__FILE__) . '/DbConnect.php';
            require_once '../config/DbConnect.php';
            $db = new DbConnect; 
            $this->con = $db->connect(); 
        }
        
     
        /*
         METHOD: creates a new user account
         PARAMS: username, password
        */
        public function createUser($username, $password) {
             $userID = uniqid("USR");
             $passHash = md5($password);
 
            $stmt=$this->con->prepare("INSERT INTO user_account (User_ID, Username, Password) 
            SELECT ?, ?, ?  WHERE NOT EXISTS (
            SELECT username FROM user_account WHERE username LIKE ?
          );");
          $stmt->bind_param("ssss", $userID, $username, $passHash, $username);
             if ($stmt->execute()) {
                return $userID;
                $stmt->bind_result($userID);
                $stmt->fetch();
             } else {
                return RECORD_EXISTS;
            }
    }
        /*
         METHOD: update password 
         PARAMS: non
        */
        public function updateUserPassword($userID,$currentPassword, $newPassword){

        }

        /*
         METHOD: update password 
         PARAMS: non
        */
        public function updateUserName($userID,$currentUsername, $newUsername){
            
        }

        /*
         METHOD: gets all user in the system 
         PARAMS: non
        */
        public function getAllUsers(){
         
            $stmt = $this->con->prepare("SELECT Registration_ID,Name,Surname,Profession,o.Organisation_Name,o.Department FROM registration_info r INNER JOIN organisation o ON r.Organisation_ID = o.Organisation_ID");
            $stmt->execute();
            $result = $stmt->get_result(); // get the mysqli result
            $rows = array();
            while ($row =$result->fetch_assoc()) {
               array_push($rows, $row);
            }
            return $rows;
        }


        /*
        METHOD: get the id of the user based in their email address
        PARAMS:email
        */
        private function getCurrentUserID($email){
            $stmt = $this->con->prepare("SELECT registration_id FROM  registration_info WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute(); 
            $stmt->bind_result($id);
            $stmt->fetch(); 
            return $id; 

        }
}


