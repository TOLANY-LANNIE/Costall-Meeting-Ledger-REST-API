<?php
 class MinutesOperations{

        private $con; 

        function __construct(){
            require_once dirname(__FILE__) . '/DbConnect.php';
            $db = new DbConnect; 
            $this->con = $db->connect(); 
        }

        /*
         METHOD: create a record for the user
         PARAMS: username, password
        */
       
}