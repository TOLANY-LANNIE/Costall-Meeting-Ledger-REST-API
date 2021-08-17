<?php
 class DbOperations{

        private $con; 

        function __construct(){
            require_once dirname(__FILE__) . '/DbConnect.php';
            $db = new DbConnect; 
            $this->con = $db->connect(); 
        }
     
     /*
     METHOD: get the id of the user based in their email address
     PARAMS:
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
     METHOD: CREATE MEETING SESSION
     PARAMS: EMAIL
     RETURNS: ID
     */
     public function createMeetingSession($meetingTitle, $meetingGoal,$organizerEmail, $date, $startTime,$meetingDuration,$meetingType){
        $user_id = $this->getCurrentUserID($organizerEmail);
        $meeting_id =uniqid('MS');
         $init_cost =0;
         
         $stmt = $this->con->prepare("INSERT INTO meeting_session(Session_ID, Meeting_Title, Meeting_Goal, Organizer, Date, StartTime, EndTime, Meeting_Type, Total_Cost) VALUES (?,?,?,?,?,?,?,?,?)");
                $stmt->bind_param("ssssssssd", $meeting_id, $meetingTitle, $meetingGoal,$user_id, $date, $startTime,$meetingDuration,$meetingType,$init_cost);
                if($stmt->execute()){
                   // return MEETING_SESSION_CREATED; 
                    $result = $this->getSessionID($meetingTitle);
                    return $result;
                }else{
                    return MEETING_SESSION_FAILED;
                }
     }
     /*
     METHOD: get the id of the user based in their email address
     PARAMS:
     */
     private function getUserID($email){
         $stmt = $this->con->prepare("SELECT registration_id FROM  registration_info WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute(); 
            $stmt->bind_result($id);
            $stmt->fetch(); 
            return $id; 

     }
     /*
     METHOD: get the id of a messing session based on its title 
     PARAMS:
     */
     public function getSessionID($title){
         $stmt = $this->con->prepare("SELECT Session_id FROM  meeting_session WHERE meeting_title = ?");
            $stmt->bind_param("s", $title);
            $stmt->execute(); 
            $stmt->bind_result($id);
            $stmt->fetch(); 
            return $id; 

     }
     /*
     METHOD: get the id of the user's role based on their title of the rolee
     PARAMS:
     */
     private function getRoleID($roleTitle){
         $stmt = $this->con->prepare("SELECT role_id FROM participant_role WHERE role_name = ?");
            $stmt->bind_param("s", $roleTitle);
            $stmt->execute(); 
            $stmt->bind_result($id);
            $stmt->fetch(); 
            return $id; 

     }
     
     /*
     METHOD: ADDS PARTICIPANT TO A MEETING SESSION
     PARAMS:
     */
     public function addParticipants($registrationID,$roleName, $session,$attending_meeting){
         
         $roleID = $this-> getRoleId($roleName);
         $cost =0.00;
         $part_id = uniqid('MP');
             if(!$this->participantExist($registrationID)){
                 $stmt = $this->con->prepare("INSERT INTO meeting_participant(Participation_ID, Registration_ID, Role_ID, Session_ID, attending_meeting, Total_Cost) VALUES (?,?,?,?,?,?)");
                 $stmt->bind_param("ssssid", $part_id, $registrationID, $roleID, $session, $attending_meeting,$cost);
                 if($stmt->execute()){
                    return PARTICIPANT_CREATED; 
                 }else{
                    return FAILED_TO_CREATE_PARTICIPANT;
                 }
             }
         return PARTICIPANT_EXISTS;
     }
     
     /*
     METHOD: creates meeting session minutes
     */
     public function createMinutes($session,$takenBy,$approvedBY){
         $minutesID = uniqid('MN');
         $status ="pending";
         $stmt = $this->con->prepare("INSERT INTO meeting_minutes(Minutes_ID, TakenBy, ApprovedBy, Session_ID, status) VALUES (?,?,?,?,?)");
                 $stmt->bind_param("sssss", $minutesID, $takenBy,$approvedBY,$session,$status);
                 if($stmt->execute()){
                    return MINUTES_CREATED; 
                 }else{
                    return FAILED_TO_CREATE_MINUTES;
                 }   
     }
      /*
     METHOD: ADDS AGENDA ITEMS TO A MEETING SESSION
     PARAMS:
     */
     public function addAgenda($sessionID, $agendaDescription,$presenter, $agendaDuration){
         $agendaID = uniqid('AG');
         $discussion="--";
         $conclusion ="--";
         
         if(!$this->agendaExists($sessionID,$presenter,$agendaDescription)){
              $stmt = $this->con->prepare("INSERT INTO agendaitem(Agenda_Item_ID, SessionID, Agenda_Description, Presenter, AgendaDuration, Discussion, Conclusion) VALUES (?,?,?,?,?,?,?)");
             $stmt->bind_param("ssssiss", $agendaID, $sessionID,$agendaDescription,$presenter,$agendaDuration,$discussion,$conclusion);
                 if($stmt->execute()){
                    return AGENDA_CREATED; 
                 }else{
                    return FAILED_TO_CREATE_AGENDA;
                 } 
         }
         return AGENDA_EXISTS;
     }
     
    /*
     METHOD: ADDS RESOURCE TO BE USED IN A MEETING SESSION
     PARAMS:
     */
     public function addResources($resourceName, $purpose, $cost, $session){
         $resourceId =uniqid('RS');
         $stmt = $this->con->prepare("INSERT INTO resource(Resource_ID, Resource_Name, Purpose, Cost, Session_ID) VALUES(?,?,?,?,?)");
                 $stmt->bind_param("sssds", $resourceId, $resourceName,$purpose,$cost,$session);
                 if($stmt->execute()){
                    return RESOURCE_CREATED; 
                 }else{
                    return FAILED_TO_CREATE_RESOURCE;
                 }   
     }
     /*
     METHOD: ADDS  ACTION FOR EACH AGENDA ITEM IN A MEETING SESSION
     PARAMS:
     */
     public function addActionItem($agenda,$actionDescription,$participant,$deadline){
         $actionID =uniqid('AC');
         
         $stmt = $this->con->prepare("INSERT INTO action_item(Action_Item_ID, Agenda_Item_ID, Action_Description, Participation_ID, Deadline) VALUES (?,?,?,?,?)");
            echo $actionID." ".$agenda." ". $actionDescription." ".$participant." ".$deadline;
             $stmt->bind_param("sssss", $actionID, $agenda,$actionDescription,$participant,$deadline);
                 if($stmt->execute()){
                    return ACTION_ITEM_CREATED; 
                 }else{
                    return FAILED_TO_CREATE_ACTION_ITEM;
                 } 
     }
     
     /*
     METHOD: ADDS VENUE DETAILS FOR A MEETING SESSION
     PARAMS:
     */
     public function addVenue($name, $location, $cost, $session){
         $venueID = uniqid('VN');
         $stmt = $this->con->prepare("INSERT INTO venue(Venue_ID, Name, Location, Cost, Session_ID) VALUES (?,?,?,?,?)");
                 $stmt->bind_param("sssds", $venueID, $name,$location,$cost,$session);
                 if($stmt->execute()){
                    return VENUE_CREATED; 
                 }else{
                    return FAILED_TO_CREATE_VENUE;
                 } 
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
     METHOD: gets all meeting sessions the user is part of
     PARAMS: reg_id, date
     */
     public function getMeetingSession($regID){
         $stmt = $this->con->prepare("SELECT ms.Session_ID,Meeting_Title,Meeting_Goal,ms.Date,StartTime,EndTime,Meeting_Type,ms.Total_Cost, Name,Surname FROM meeting_session ms INNER JOIN meeting_participant mp on ms.Session_ID = mp.Session_ID INNER JOIN registration_info r on ms.Organizer = r.Registration_ID WHERE mp.Registration_ID =?");
          $stmt->bind_param("s", $regID);
          $stmt->execute();
          $result = $stmt->get_result(); // get the mysqli result
          $rows = array();
         while ($row =$result->fetch_assoc()) {
            array_push($rows, $row);
         }
         return $rows;
     }
      /*
     METHOD: gets all participants of a meeting session
     PARAMS: session_id
     */
     public function getAllParticipants($sessionID){
         
         $stmt = $this->con->prepare("SELECT mp.Participation_ID,Name,Surname,Role_Name from meeting_participant mp inner JOIN registration_info r on mp.Registration_ID = r.Registration_ID inner JOIN participant_role pr on pr.Role_ID = mp.Role_ID WHERE Session_ID = ?");
         $stmt->bind_param("s", $sessionID);
         $stmt->execute();
         $result = $stmt->get_result(); // get the mysqli result
         $rows = array();
         while ($row =$result->fetch_assoc()) {
            array_push($rows, $row);
         }
         return $rows;
     }
      /*
     METHOD: gets all resources' details for eash meeting session
     PARAMS: sessionid
     */
     public function getMeetingResources($sessionID){
         
         $stmt = $this->con->prepare("SELECT * FROM resource");
         $stmt->execute();
         $result = $stmt->get_result(); // get the mysqli result
         $rows = array();
         while ($row =$result->fetch_assoc()) {
            array_push($rows, $row);
         }
         return $rows;
     }
      /*
     METHOD: gets agenda items based on the meeting event
     PARAMS: session_id
     */
     public function getMeetingAgenda($session_id){
         
         $stmt = $this->con->prepare("SELECT ag.Agenda_Item_ID, ag.Agenda_Description, mp.Participation_ID,ag.AgendaDuration,ag.Discussion,ag.Conclusion, r.Name, r.Surname FROM agendaitem ag inner JOIN meeting_participant mp on ag.Presenter=mp.Participation_ID inner JOIN registration_info r on mp.Registration_ID=r.Registration_ID WHERE ag.SessionID =?");
         $stmt->bind_param("s", $session_id);
         $stmt->execute();
         $result = $stmt->get_result(); // get the mysqli result
         $rows = array();
         while ($row =$result->fetch_assoc()) {
            array_push($rows, $row);
         }
         return $rows;
     }
      /*
     METHOD: gets the venue were the meeting session is held
     PARAMS: sessionID
     */
     public function getVenue($sessionID){
         
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
     METHOD: gets all action items base on the agenda 
     PARAMS: agenda
     */
     public function getActionItem($agenda){
         
         $stmt = $this->con->prepare("");
         $stmt->execute();
         $result = $stmt->get_result(); // get the mysqli result
         $rows = array();
         while ($row =$result->fetch_assoc()) {
            array_push($rows, $row);
         }
         return $rows;
     }
      /*
     METHOD: gets all action item based on the user and the action item status 
     PARAMS: $registration_ID,$status
     */
     public function getUserActionitems($registration_ID,$status){
         
         $stmt = $this->con->prepare("");
         $stmt->execute();
         $result = $stmt->get_result(); // get the mysqli result
         $rows = array();
         while ($row =$result->fetch_assoc()) {
            array_push($rows, $row);
         }
         return $rows;
     }
     
     
     /*
     method: check if the participant has already been added to the meeting session.
     */
     private function participantExist($registrationID){
          $stmt = $this->con->prepare("SELECT participation_id FROM meeting_participant WHERE registration_id = ?");
            $stmt->bind_param("s", $registrationID);
            $stmt->execute(); 
            $stmt->store_result(); 
            return $stmt->num_rows > 0;  
     }
     
     /*
     method: check if the participant has already been added to the meeting session.
     */
     private function agendaExists($sessionID,$presenter,$agendaDescription){
          $stmt = $this->con->prepare("SELECT agenda_item_id FROM agendaitem WHERE sessionid = ? AND presenter =? AND Agenda_Description =?");
            $stmt->bind_param("sss", $sessionID,$presenter,$agendaDescription);
            $stmt->execute(); 
            $stmt->store_result(); 
            return $stmt->num_rows > 0;  
     }
 
 }