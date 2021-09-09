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
        public function userLogin($username, $pass){
            $password = md5($pass);
            $stmt = $this->con->prepare("SELECT * FROM user_account WHERE username = ? AND password = ?");
            $stmt->bind_param("ss", $username, $password);
            $stmt->execute();
            $stmt->store_result();
            $num_rows= $stmt->num_rows;
            $stmt->close(); 
            return $num_rows; 

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
        public function getUserInfo($username) {
            $stmt = $this->con->prepare("SELECT r.Name, r.Surname, r.Email, r.User_ID, r.Registration_ID, u.Username, u.Api_key  FROM registration_info r INNER JOIN user_account u ON r.user_id = u.user_id WHERE u.Username = ? ;");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $user= $stmt->get_result()->fetch_assoc();
            $stmt->close();
            return $user;          
         }
   


          /*
         METHOD: gets all user in the system 
         PARAMS: non
        */
        public function getAllUsers(){
            $stmt = $this->con->prepare("SELECT r.Name,r.Surname, r.Profession,o.Organisation_Name FROM registration_info r INNER JOIN organisation o ON r.Organisation_ID = o.Organisation_ID ");
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


