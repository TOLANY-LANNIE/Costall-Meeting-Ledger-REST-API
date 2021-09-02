<?php


use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';

require '../routes/RegistrationOperations.php';
require '../routes/OrganisationOperations.php';


$app = new \Slim\App([
    'settings'=>[
        'displayErrorDetails'=>true
    ]
]);

/*$app->add(new Tuupola\Middleware\HttpBasicAuthentication([
    "secure"=>false,
    "users" => [
        "thulani" => "123456",
    ]
]));*/


$app->post('/api/v1/users/registerUser', function (Request $request, Response $response) {
    $request_data = $request->getParsedBody();

    $name = $request_data['name'];
    $surname = $request_data['surname'];
    $dob = $request_data['dob'];
    $email = $request_data['email'];
    $profession = $request_data['profession'];
    $hourlyrate= $request_data['hourlyrate'];
    $cellNumber = $request_data['cellnumber'];
    $organization = $request_data['organization'];
    $username = $request_data['username'];
    $password = $request_data['password'];

    $db = new RegistrationOperations();
    $result = $db->registerUser($name, $surname, $dob, $email, $profession, $hourlyrate, $cellNumber, $organization, $username, $password);
    if ($result == RECORD_CREATED_SUCCESSFULLY) {
        $message = array();
        $message['error'] = false;
        $message['message'] = "User register successfully";

        $response->getBody()->write(json_encode($message));

        return $response
                    ->withHeader('Content-type', 'application/json')
                    ->withStatus(201);

    }  else if ($result == FAILED_TO_CREATE_RECORD) {
        $message = array();
        $message['error'] = true;
        $message['message'] = "Failed to register user";

        $response->getBody()->write(json_encode($message));

        return $response
                    ->withHeader('Content-type', 'application/json')
                    ->withStatus(422);
    }

});


    
 






































/* 
    endpoint: createMeetingSession
    parameters: id,meeeting_title, meeting_goal, organisedBy,date,start_time, duration,type_ID,totalcost
    method: POST
*/
$app->post('/createMeetingSession', function(Request $request, Response $response){
    if(!hasEmptyParameters(array('meetingTitle', 'meetingGoal','organizerEmail', 'date', 'startTime','endTime','meetingType'), $request, $response)){
        
        $request_data = $request->getParsedBody(); 

        $meetingTitle= $request_data['meetingTitle'];
        $meetingGoal= $request_data['meetingGoal'];
        $organizerEmail = $request_data['organizerEmail'];
        $date= $request_data['date'];
        $startTime = $request_data['startTime'];
        $meetingDuration= $request_data['endTime'];
        $meetingType = $request_data['meetingType'];

        $db = new DbOperations; 
        
        $result = $db->createMeetingSession($meetingTitle, $meetingGoal,$organizerEmail, $date, $startTime,$meetingDuration,$meetingType);
        
        if($result == MEETING_SESSION_CREATED){

            $message = array(); 
            $message['error'] = false; 
            $message['message'] = 'Session created successfully';

            $response->write(json_encode($message));

            return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(201);

        }else if($result == MEETING_SESSION_FAILED){

            $message = array(); 
            $message['error'] = true; 
            $message['message'] = 'Some error occurred';

            $response->write(json_encode($message));

            return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(422);    

        }
    }
     return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(422);
});

$app->post('/createMeetingSession4', function(Request $request, Response $response){
    if(!hasEmptyParameters(array('meetingTitle', 'meetingGoal','organizerEmail', 'date', 'startTime','endTime','meetingType'), $request, $response)){
        
        $request_data = $request->getParsedBody(); 

        $meetingTitle= $request_data['meetingTitle'];
        $meetingGoal= $request_data['meetingGoal'];
        $organizerEmail = $request_data['organizerEmail'];
        $date= $request_data['date'];
        $startTime = $request_data['startTime'];
        $meetingDuration= $request_data['endTime'];
        $meetingType = $request_data['meetingType'];

        $db = new DbOperations; 
        
        $result = $db->createMeetingSession($meetingTitle, $meetingGoal,$organizerEmail, $date, $startTime,$meetingDuration,$meetingType);
        
        if(!empty($result)){

            $message = array(); 
            $message['error'] = false; 
            $message['message'] = 'Session created successfully';
            $message['meeting session'] = $result;

            $response->write(json_encode($message));

            return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(201);

        }else if($result == MEETING_SESSION_FAILED){

            $message = array(); 
            $message['error'] = true; 
            $message['message'] = 'Some error occurred';

            $response->write(json_encode($message));

            return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(422);    

        }
    }
     return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(422);
});

/* 
    endpoint: addParticipant
    parameters: 
    method: POST
*/
$app->post('/addParticipants', function(Request $request, Response $response){
    if(!hasEmptyParameters(array('registrationID','roleName', 'session', 'attending_meeting'), $request, $response)){
        
        $request_data = $request->getParsedBody(); 

        $registrationID =$request_data['registrationID'];;
		$roleName=$request_data['roleName'];;
		$session=$request_data['session'];;
		$attending_meeting=$request_data['attending_meeting'];;
      
        $db = new DbOperations; 
        
        $result = $db->addParticipants($registrationID,$roleName, $session, $attending_meeting);
        
        if($result == PARTICIPANT_CREATED){

            $message = array(); 
            $message['error'] = false; 
            $message['message'] = 'Participant added successfully';

            $response->write(json_encode($message));

            return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(201);

        }else if($result == FAILED_TO_CREATE_PARTICIPANT){

            $message = array(); 
            $message['error'] = true; 
            $message['message'] = 'Some error occurred';

            $response->write(json_encode($message));

            return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(422);    

        }else if($result == PARTICIPANT_EXISTS){
            $message = array(); 
            $message['error'] = true; 
            $message['message'] = 'Participant Already Exists';

            $response->write(json_encode($message));

            return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(422);    
        }
    }
     return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(422);
});

/* 
    endpoint: addVenue
    parameters: 
    method: POST
*/
$app->post('/addVenue', function(Request $request, Response $response){
    if(!hasEmptyParameters(array('name', 'location','cost', 'session'), $request, $response)){
        
        $request_data = $request->getParsedBody(); 

        $name= $request_data['name'];
        $location= $request_data['location'];
        $cost = $request_data['cost'];
        $session= $request_data['session'];
    
        $db = new DbOperations; 
        
        $result = $db->addVenue($name, $location, $cost, $session);
        
        if($result == VENUE_CREATED){

            $message = array(); 
            $message['error'] = false; 
            $message['message'] = 'Venue added successfully';

            $response->write(json_encode($message));

            return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(201);

        }else if($result == FAILED_TO_CREATE_VENUE){

            $message = array(); 
            $message['error'] = true; 
            $message['message'] = 'Some error occurred';

            $response->write(json_encode($message));

            return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(422);    

        }
    }
     return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(422);
});

/* 
    endpoint: addResource
    parameters: 
    method: POST
*/
$app->post('/addResource', function(Request $request, Response $response){
    if(!hasEmptyParameters(array('resourceName','purpose', 'cost', 'session'), $request, $response)){
        
        $request_data = $request->getParsedBody(); 

        $resourceName =$request_data['resourceName'];;
		$purpose=$request_data['purpose'];;
		$cost=$request_data['cost'];;
		$session=$request_data['session'];;
      
        $db = new DbOperations; 
        
        $result = $db->addResources($resourceName, $purpose, $cost, $session);
        
        if($result == RESOURCE_CREATED){

            $message = array(); 
            $message['error'] = false; 
            $message['message'] = 'Resource added successfully';

            $response->write(json_encode($message));

            return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(201);

        }else if($result == FAILED_TO_CREATE_RESOURCE){

            $message = array(); 
            $message['error'] = true; 
            $message['message'] = 'Some error occurred';

            $response->write(json_encode($message));

            return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(422);    

        }
    }
     return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(422);
});

/* 
    endpoint: addAgenda
    parameters: 
    method: POST
*/
$app->post('/addAgenda', function(Request $request, Response $response){
    
    if(!hasEmptyParameters(array('sessionID', 'agendaDescription','presenter', 'agendaDuration'), $request, $response)){
        
        $request_data = $request->getParsedBody(); 

        $sessionID = $request_data['sessionID'];
        $agendaDescription= $request_data['agendaDescription'];
        $presenter = $request_data['presenter'];
        $agendaDuration= $request_data['agendaDuration'];
        
        $db = new DbOperations; 
        
        $result = $db->addAgenda($sessionID, $agendaDescription,$presenter, $agendaDuration);
        
        if($result == AGENDA_CREATED){

            $message = array(); 
            $message['error'] = false; 
            $message['message'] = 'Agenda created successfully';

            $response->write(json_encode($message));

            return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(201);

        }else if($result == FAILED_TO_CREATE_AGENDA){

            $message = array(); 
            $message['error'] = true; 
            $message['message'] = 'Some error occurred';

            $response->write(json_encode($message));

            return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(422);    

        }else if($result == AGENDA_EXISTS){
            $message = array(); 
            $message['error'] = true; 
            $message['message'] = 'AGENDA Already Exists';

            $response->write(json_encode($message));

            return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(422);    
        }
    }
     return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(422);
});

/* 
    endpoint: createMinutes
    parameters: 
    method: POST
*/
$app->post('/createMinutes', function(Request $request, Response $response){
    
    if(!hasEmptyParameters(array('session', 'takenBy','approvedBy'), $request, $response)){
        
        $request_data = $request->getParsedBody(); 

        $session= $request_data['session'];
        $takenBy= $request_data['takenBy'];
        $approvedBy = $request_data['approvedBy'];
        
        $db = new DbOperations; 
        
        $result = $db->createMinutes($session,$takenBy,$approvedBy);
        
        if($result == MINUTES_CREATED){

            $message = array(); 
            $message['error'] = false; 
            $message['message'] = 'Minutes created successfully';

            $response->write(json_encode($message));

            return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(201);

        }else if($result == FAILED_TO_CREATE_MINUTES){

            $message = array(); 
            $message['error'] = true; 
            $message['message'] = 'Some error occurred';

            $response->write(json_encode($message));

            return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(422);    

        }
    }
     return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(422);
});

/* 
    endpoint: createMinutes
    parameters: 
    method: POST
*/
$app->post('/addActionItem', function(Request $request, Response $response){
    
    if(!hasEmptyParameters(array('agenda', 'actionDescription','participant','deadline'), $request, $response)){
        
        $request_data = $request->getParsedBody(); 

        $agenda= $request_data['agenda'];
        $actionDescription= $request_data['actionDescription'];
        $participant = $request_data['participant'];
        $deadline= $request_data['deadline'];
        
        $db = new DbOperations; 
        
        $result = $db->addActionItem($agenda,$actionDescription,$participant,$deadline);
    
        
        if($result == ACTION_ITEM_CREATED){

            $message = array(); 
            $message['error'] = false; 
            $message['message'] = 'Action item created successfully';

            $response->write(json_encode($message));

            return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(201);

        }else if($result == FAILED_TO_CREATE_ACTION_ITEM){

            $message = array(); 
            $message['error'] = true; 
            $message['message'] = 'Some error occurred';

            $response->write(json_encode($message));

            return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(422);    

        }
    }
     return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(422);
});

/* 
    endpoint: allusers
    parameters: 
    method: get
*/
$app->get('/allusers', function(Request $request, Response $response){

    $db = new DbOperations; 

    $users = $db->getAllUsers();

    $response_data = array();

    $response_data['error'] = false; 
    $response_data['users'] = $users; 

    $response->write(json_encode($response_data));

    return $response
    ->withHeader('Content-type', 'application/json')
    ->withStatus(200);  

});

/* 
    endpoint: getMeetingSessions
    parameters: 
    method: post
*/
$app->post('/getMeetingSessions', function(Request $request, Response $response){
    if(!hasEmptyParameters(array('regID'), $request, $response)){
        
        $request_data = $request->getParsedBody(); 
        $regID= $request_data['regID'];
        
         $db = new DbOperations; 

         $rows = $db->getMeetingSession($regID);

         $response_data = array();

    $response_data['error'] = false; 
    $response_data['rows'] = $rows; 

    $response->write(json_encode($response_data));

    return $response
    ->withHeader('Content-type', 'application/json')
    ->withStatus(200);  
        
     }
    return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(422);
});

/* 
    endpoint: getParticipants
    parameters: 
    method: post
*/
$app->post('/getParticipants', function(Request $request, Response $response){
    if(!hasEmptyParameters(array('sessionID'), $request, $response)){
        
        $request_data = $request->getParsedBody(); 
        $sessionID = $request_data['sessionID'];
        
         $db = new DbOperations; 

         $rows = $db->getAllParticipants($sessionID);

         $response_data = array();

    $response_data['error'] = false; 
    $response_data['participants'] = $rows; 

    $response->write(json_encode($response_data));

    return $response
    ->withHeader('Content-type', 'application/json')
    ->withStatus(200);  
        
     }
    return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(422);
});

/* 
    endpoint: getAgendaItems
    parameters: 
    method: post
*/
$app->post('/getAgendaItems', function(Request $request, Response $response){
    if(!hasEmptyParameters(array('sessionID'), $request, $response)){
        
        $request_data = $request->getParsedBody(); 
        $sessionID = $request_data['sessionID'];
        
         $db = new DbOperations; 

         $rows = $db->getMeetingAgenda($sessionID);

         $response_data = array();

    $response_data['error'] = false; 
    $response_data['agendaItems'] = $rows; 

    $response->write(json_encode($response_data));

    return $response
    ->withHeader('Content-type', 'application/json')
    ->withStatus(200);  
        
     }
    return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(422);
});

/* 
    endpoint: getResource
    parameters: 
    method: post
*/
$app->post('/getResources', function(Request $request, Response $response){
    if(!hasEmptyParameters(array('sessionID'), $request, $response)){
        
        $request_data = $request->getParsedBody(); 
        $sessionID = $request_data['sessionID'];
        
         $db = new DbOperations; 

         $rows = $db->getMeetingResources($sessionID);

         $response_data = array();

    $response_data['error'] = false; 
    $response_data['resources'] = $rows; 

    $response->write(json_encode($response_data));

    return $response
    ->withHeader('Content-type', 'application/json')
    ->withStatus(200);  
        
     }
    return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(422);
});


//checks if all parameter are not empty
function hasEmptyParameters($required_params, $request, $response){
    $error = false; 
    $error_params = '';
    $request_params = $request->getParsedBody(); 

    foreach($required_params as $param){
        if(!isset($request_params[$param]) || strlen($request_params[$param])<=0){
            $error = true; 
            $error_params .= $param . ', ';
        }
    }

    if($error){
        $error_detail = array();
        $error_detail['error'] = true; 
        $error_detail['message'] = 'Required parameters ' . substr($error_params, 0, -2) . ' are missing or empty';
        $response->write(json_encode($error_detail));
    }
    return $error; 
}
$app->run();

 