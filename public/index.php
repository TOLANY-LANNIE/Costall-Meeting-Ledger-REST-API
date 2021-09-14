<?php


use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';

require '../routes/RegistrationOperations.php';
require '../routes/OrganisationOperations.php';
require '../routes/UserOperations.php';


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


$app->post('/v1/register', function (Request $request, Response $response) {
    $request_data = $request->getParsedBody();

    $name = $request_data['name'];
    $surname = $request_data['surname'];
    $dob = $request_data['dob'];
    $email = $request_data['email'];
    // validating email address
     //validateEmail($email);
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

    }elseif ($result == RECORD_EXISTS) {
        $message = array();
        $message['error'] = true;
        $message['message'] = "User already exists";

        $response->getBody()->write(json_encode($message));

        return $response
                    ->withHeader('Content-type', 'application/json')
                    ->withStatus(409);

    }elseif ($result == FAILED_TO_CREATE_RECORD) {
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
$app->get('/v1/allusers', function(Request $request, Response $response){

    $db = new UserOperations; 

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
    endpoint: login
    parameters: username and password
    method: post
*/

$app->post('/v1/login', function (Request $request, Response $response) {
    $request_data = $request->getParsedBody();

    $username = $request_data['username'];
    $password = $request_data['password'];

    $db = new UserOperations;
    $message = array();
    if($db->userLogin($username, $password)){
        $user = $db->getUserInfo($username);
        $message['error'] = false;
        $message['Registration_ID'] = $user['Registration_ID'];
        $message['Name'] = $user['Name'];
        $message['Surname'] = $user['Surname'];
        $message['Email'] = $user['Email'];
        $message['UserID'] = $user['User_ID'];
        $message['Username'] = $user['Username'];
        $message['Api_Key'] = $user['Api_key'];

        $response->getBody()->write(json_encode($message));

        return $response
                    ->withHeader('Content-type', 'application/json')
                    ->withStatus(200);

    }else{
        $message = array();
        $message['error'] = true;
        $message['message'] = "Invalid username or password";

        $response->getBody()->write(json_encode($message));

        return $response
                    ->withHeader('Content-type', 'application/json')
                    ->withStatus(422);

    }
});

 
/**
 * Validating email address
 */
function validateEmail($email) {
    $app = \Slim\Slim::getInstance();
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response["error"] = true;
        $response["message"] = 'Email address is not valid';
        echoRespnse(400, $response);
        $app->stop();
    }
}

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

 