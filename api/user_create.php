<?php
// required headers
//header("Access-Control-Allow-Origin: http://authentication-jwt/");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: content-type, access-control-allow-headers, access-control-allow-origin, authorization, x-Requested-With");

// required for web token JSON 
include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;

// файлы, необходимые для подключения к базе данных 
include_once 'config/database.php';
include_once 'objects/user.php';
 
// получаем соединение с базой данных 
$database = new Database();
$db = $database->getConnection();
 
// создание объекта 'User' 
$user = new User($db);
 
// получаем данные 
$data = json_decode(file_get_contents("php://input"));
 
// устанавливаем значения 
$user->firstname = $data->firstname;
$user->email = $data->email;
$user->password = $data->password;
 
// создание пользователя 
if (
    !empty($user->firstname) &&
    !empty($user->email) 
//    !empty($user->password) &&
) {
    $res = $user->create();
    $token = array(
        "iss" => $iss,
        "aud" => $aud,
        "iat" => $iat,
        "nbf" => $nbf,
        "data" => array(
            "id" => $res->uid,
            "firstname" => $res->name
        )
    );
    $jwt = JWT::encode($token, $key);
    $res->jwt = $jwt;

    // устанавливаем код ответа 
    http_response_code(200);
 
    // покажем сообщение о том, что пользователь был создан 
    echo json_encode(array("chat" => $res));
}
 
// сообщение, если не удаётся создать пользователя 
else {
 
    // устанавливаем код ответа 
    http_response_code(400);
 
    // покажем сообщение о том, что создать пользователя не удалось 
    echo json_encode(array("message" => "Невозможно создать пользователя."));
}
?>