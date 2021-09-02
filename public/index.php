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

 