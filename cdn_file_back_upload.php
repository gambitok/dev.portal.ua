<?php
error_reporting(1);
@ini_set('display_errors', true);
define('RD', dirname (__FILE__));
date_default_timezone_set("Europe/Kiev");
require_once (RD . "/lib/mysql_class.php");
require_once (RD . "/lib/DbSingleton.php");
$db = DbSingleton::getDb();
$dbt = DbSingleton::getTokoDb();
session_start();
$user_id = $_SESSION["media_user_id"];

if(!empty($_FILES)) {
    if ($user_id > 0) {
        $targetDir = RD . "/cdn/back_files/csv/$user_id/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777);
        }
        $fileName = $_FILES['file']['name'];
        $real_file_name = iconv("utf-8", "windows-1251", $fileName);
        $fileName = "myparts_back_" . $user_id . "_" . time() . "." . pathinfo($fileName, PATHINFO_EXTENSION);
        $targetFile = $targetDir . $fileName;

        /*
         * drop pred files
         * */
        $files = glob(RD . "/cdn/back_files/csv/$user_id/*");
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        $db->query("DELETE FROM `J_BACK_CSV` WHERE `user_id`='$user_id';");

        /*
         * create new file
         * */
        if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
            session_start();
            $db->query("INSERT INTO `J_BACK_CSV` (`user_id`, `file_name`, `name`) VALUES ('$user_id', '$fileName', '$real_file_name');");
        }
    }
}