<?php

error_reporting(1);
@ini_set('display_errors', true);
define('RD', __DIR__);
date_default_timezone_set("Europe/Kiev");

require_once (RD."/lib/mysql_class.php");
require_once (RD."/lib/DbSingleton.php");
$db = DbSingleton::getTokoDb();

$review_id = (int)$_REQUEST["photo_review_id"];

//$db->query("UPDATE `T2_REVIEWS` SET `IMG` = 'test' WHERE `ID` = $review_id LIMIT 1;");

if ($_FILES['file']['name']) {
    if (!$_FILES['file']['error']) {
        $name           = date('m-d-Y_his') . md5(rand(100, 200));
        $ext            = explode('.', $_FILES['file']['name']);
        $filename       = $name . '.' . $ext[1];
        $destination    = 'uploads/images/saved/' . $filename; // change this directory
        $location       = $_FILES["file"]["tmp_name"];

        move_uploaded_file($location, $destination);
        echo 'https://portal.myparts.pro/uploads/images/saved/' . $filename; // change this URL

        $db->query("INSERT INTO `test_table` (`name`, `descr`) VALUES ('$filename', '$location');");

//        $db->query("UPDATE `T2_REVIEWS` SET `IMG` = '$filename' WHERE `ID` = $review_id LIMIT 1;");
    } else {
        echo  $message = 'Ooops!  Your upload triggered the following error:  ' . $_FILES['file']['error'];
    }
}