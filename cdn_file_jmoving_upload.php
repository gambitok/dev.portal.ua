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
    $jmoving_id = $_REQUEST["csv_jmoving_id"];
    if ($user_id > 0 && $jmoving_id != "" && $jmoving_id > 0) {
        $targetDir = RD . "/cdn/jmoving_files/csv/$jmoving_id/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777);
        }
        $fileName = $_FILES['file']['name'];
        $real_file_name = iconv("utf-8", "windows-1251", $fileName);
        $fileName = "myparts_jmoving_" . $jmoving_id . "_" . time() . "." . pathinfo($fileName, PATHINFO_EXTENSION);
        $targetFile = $targetDir . $fileName;
        if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
            session_start();
            $db->query("INSERT INTO `J_MOVING_CSV` (`jmoving_id`,`user_id`,`file_name`,`name`) VALUES ('$jmoving_id','$user_id','$fileName','$real_file_name');");
        }
    }
}