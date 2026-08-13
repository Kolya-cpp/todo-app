<?php
session_start();

$host="127.0.0.1";
$user="root";
$password="root";
$database="todoapp";

$date=date("Y-m-d_H-i-s");

$filename="backup_".$date.".sql";

header('Content-Type: application/octet-stream');
header("Content-Disposition: attachment; filename=$filename");

system(
"mysqldump --user=$user --password=$password $database"
);

exit;

?>