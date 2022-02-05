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
include_once 'objects/message.php';
 
// получаем соединение с базой данных 
$database = new Database();
$db = $database->getConnection();
 
// создание объекта 'Message' 
$message = new Message($db);
 
// получаем данные 
$data = json_decode(file_get_contents("php://input"));

$jwt=isset($data->jwt) ? $data->jwt : "";

    // если JWT не пуст 
if($jwt) {
 
    // if decode success - 
    try {
        // декодирование jwt 
        $decoded = JWT::decode($jwt, $key, array('HS256'));
        // устанавливаем значения  
        if (isset($data->time) && isset($jwt) ) {
            $message->time = $data->time;
            $message->toId = $decoded->data->id;   
        }

        // чтение сообщений 
        if (
            !empty($message->time) &&
            !empty($message->toId) &&
            is_array($message->read())
        ) {
            http_response_code(200);
            // return aray new messages
            echo json_encode(array("messages" => $message->read() ));
        }
        else {
        http_response_code(400);
        // show error 
        echo json_encode(array("message" => "Невозможно прочитать сообщение."));
        }
    }
    catch (Exception $e){
    
        // код ответа 
        http_response_code(401);
    
        // сообщение об ошибке 
        echo json_encode(array(
            "message" => "Доступ закрыт",
            "error" => $e->getMessage()
        ));
    }
}


?>