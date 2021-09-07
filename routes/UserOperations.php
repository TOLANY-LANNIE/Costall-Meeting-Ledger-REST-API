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
             $api_key = $this->generateApiKey();
 
            $stmt=$this->con->prepare("INSERT INTO user_account (User_ID, Username, Password,Api_key) 
            SELECT ?, ?, ?,? WHERE NOT EXISTS (
            SELECT username FROM user_account WHERE username LIKE ?);");
          $stmt->bind_param("sssss", $userID, $username, $passHash, $api_key,$username);
             if ($stmt->execute()) {
                return $userID;
                
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
         METHOD: 
         PARAMS: non
        */
        private function deleteUserAccount($userID,$currentUsername, $newUsername){
            
        }


        /*
        METHOD: 
        PARAMS:
        */
        public function getUserInfo($username, $password) {
            $userID = $this->userLogin($username, $password);
            $stmt = $this->con->prepare("SELECT r.Name, r.Surname, r.Email, r.User_ID, r.registration_id FROM registration_info r INNER JOIN user_account u ON r.user_id = u.user_id WHERE u.User_ID = ? ;");
            $stmt->bind_param("s", $userID);
            if ($stmt->execute()) {
               $result = $stmt->get_result();
               $userInfo = array();
               while ($row = $result->fetch_assoc()) {
                array_push($userInfo, $row);
               }
               return $userInfo;
               return USER_DETAILS_RETRIEVED;
            }else {
   
            }
            
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


         /**
     * Generating random Unique MD5 String for user Api key
     */
    private function generateApiKey() {
        return md5(uniqid(rand(), true));
    }
}


