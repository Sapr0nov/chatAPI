<?php
// показывать сообщения об ошибках 
error_reporting(E_ALL);
 
// установить часовой пояс по умолчанию 
date_default_timezone_set('Europe/Moscow');
 
// переменные, используемые для JWT 
$key = "your_secret_key";
$iss = "https://stacksite.ru";
$aud = "https://stacksite.ru";
$iat = 1356999524;
$nbf = 1357000000;
?>