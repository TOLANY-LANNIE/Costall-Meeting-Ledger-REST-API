<?php
 class LoginOpereation{

        private $con; 

        function __construct(){
            require_once '../config/DbConnect.php';
            $db = new DbConnect; 
            $this->con = $db->connect(); 
        }

        public function userLogin($username, $pass){
			$password = md5($pass);
			$stmt = $this->con->prepare("SELECT User_ID FROM user_account WHERE username = ? AND password = ?");
			$stmt->bind_param("ss", $username, $password);
			$stmt->execute();
			$stmt->bind_result($userID);
            $stmt->fetch(); 
            return $userID; 

      }
      
      public function getUserInfo($username, $password) {
         $userID = $this->userLogin($username, $password);
         $stmt = $this->con->prepare("SELECT r.Name, r.Surname, r.Email, r.User_ID, r.registration_id FROM registration r INNER JOIN user_account u ON r.user_id = u.user_id WHERE u.User_ID = ? ;");
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
     
}