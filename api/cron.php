<?php
/* delete tmp users (without password and created more one day ago)  */
/*  /var/spool/cron/crontabs/webVPS60  */
/* IN VESTA 0 4 * * * php /home/webVPS60/web/chat.stacksite.ru/public_html/api/cron.php  */

include_once '/home/webVPS60/web/chat.stacksite.ru/public_html/api/config/database.php';

$database = new Database();
$db = $database->getConnection();

$query = "DELETE FROM `users` WHERE `password` = '' AND `created` <= NOW() - INTERVAL 1 DAY";

$stmt = $db->prepare($query);
if ($stmt->execute()) {
    echo 'ok';
}else{
    echo $query;
}
return $stmt->execute();

?>