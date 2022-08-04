<?php
error_reporting(1);
@ini_set('display_errors', true);
define('RD', dirname (__FILE__));
date_default_timezone_set("Europe/Kiev");
require_once (RD."/lib/mysql_class.php");
require_once (RD."/lib/DbSingleton.php");
$db = DbSingleton::getDb();
$dbt = DbSingleton::getTokoDb();
session_start();

if (!empty($_FILES)) {
    $user_id = $_REQUEST["csv_user_id"];
    if ($user_id > 0) {
        $targetDir = RD . "/cdn/photos_files/photo/$user_id/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777);
        }
        $fileName = $_FILES['file']['name'];
        $targetFile = $targetDir . $fileName;
        if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
            $dbt->query("INSERT INTO `T2_PHOTOS_UPLOAD` (`user_id`, `file_name`) VALUES ('$user_id', '$fileName');");
        }
    }
}