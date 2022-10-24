<?php

error_reporting(1);
@ini_set('display_errors', true);
define('RD', __DIR__);
date_default_timezone_set("Europe/Kiev");

require_once (RD."/lib/mysql_class.php");
require_once (RD."/lib/DbSingleton.php");
$db = DbSingleton::getTokoDb();

$review_id = (int)$_REQUEST["photo_review_id"];

$db->query("UPDATE `T2_REVIEWS` SET `IMG` = 'test' WHERE `ID` = $review_id LIMIT 1;");

if ($_FILES['file']['name']) {
    if (!$_FILES['file']['error']) {
        $name           = md5(rand(100, 200));
        $ext            = explode('.', $_FILES['file']['name']);
        $filename       = $name . '.' . $ext[1];
        $destination    = 'uploads/images/saved/' . $filename; // change this directory
        $location       = $_FILES["file"]["tmp_name"];

        move_uploaded_file($location, $destination);
        echo 'https://portal.myparts.pro/uploads/images/saved/' . $filename; // change this URL

        $db->query("UPDATE `T2_REVIEWS` SET `IMG` = '$filename' WHERE `ID` = $review_id LIMIT 1;");
    } else {
        echo  $message = 'Ooops!  Your upload triggered the following error:  ' . $_FILES['file']['error'];
    }
}

$file_list = [];
$target_dir = "uploads/images/saved/";
$dir = $target_dir;
if(is_dir($dir) && $dh = opendir($dir)) {

    //read file
    while( ($file = readdir($dh)) !== false){
        if($file !== '' && $file !== '.' && $file !== '..'){

            //file path
            $file_path = $target_dir.$file;

            //check its not directory
            if(!is_dir($file_path)){
                $size = filesize($file_path);

                $file_list[] = array('name'=>$file, 'size'=>$size, 'path'=>$file_path);

            }
        }
    }
    closedir($dh);
}

echo json_encode($file_list);
exit;