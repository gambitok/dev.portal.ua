<?php
error_reporting(1);
@ini_set('display_errors', true);
define('RD', dirname (__FILE__));
date_default_timezone_set("Europe/Kiev");
require_once (RD."/lib/mysql_class.php");
require_once (RD."/lib/DbSingleton.php");
$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();
session_start(); $user_id=$_SESSION["media_user_id"];

if(!empty($_FILES)) {
    $dp_id = $_REQUEST["csv_dp_id"];
    if ($user_id > 0 && $dp_id != "" && $dp_id > 0) {
        $targetDir = RD . "/cdn/dp_files/csv/$dp_id/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777);
        }
        $fileName = $_FILES['file']['name'];
        $real_file_name = iconv("utf-8", "windows-1251", $fileName);
        $fileName = "myparts_dp_" . $dp_id . "_" . time() . "." . pathinfo($fileName, PATHINFO_EXTENSION);
        $targetFile = $targetDir . $fileName;

        if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
            session_start();
            $db->query("INSERT INTO `J_DP_CSV` (`dp_id`,`user_id`,`file_name`,`name`) VALUES ('$dp_id','$user_id','$fileName','$real_file_name');");
        }
    }
}