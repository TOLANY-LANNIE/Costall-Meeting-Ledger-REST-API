<?php
 class OrganisationOperations{

        private $con; 

        function __construct(){
            //require_once dirname(__FILE__) . '/DbConnect.php';
            require_once'../config/DbConnect.php';
            $db = new DbConnect; 
            $this->con = $db->connect(); 
        }

        /*
         METHOD: create a record for the user
         PARAMS: username, password
        */
        public function addOrganization($organizationName) {

            if($this->organisationExist($organizationName)==null){
                $organisationID = uniqid("ORG"); 
                $stmt = $this->con->prepare("INSERT INTO Organisation (Organisation_ID, Organisation_Name) VALUES ( ?, ?)");
                    $stmt->bind_param("ss", $organisationID, $organizationName);
                    if($stmt->execute()) 
                        return $organisationID;  
            } else {
                        return $this->organisationExist($organizationName);
                            
                }
           
         }

         /*
         METHOD: update user's current organisation
         PARAMS: username, password
        */
        public function updateOrganisation(){

        }


        /*
         METHOD: update user's current department
         PARAMS: username, password
        */
        public function updateDepartment(){

        }
     
        /*
         method: check if the participant has already been added to the meeting session.
        */
        public function organisationExist($organisationName){
            $id ='';
            $stmt = $this->con->prepare(" SELECT organisation_id FROM Organisation WHERE  Organisation_Name =?");
            $stmt->bind_param("s",$organisationName);
            $stmt->execute(); 
            $stmt->bind_result($id);
            $stmt->fetch(); 
            return $id; 
            
        }
}