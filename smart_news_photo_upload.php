<?php
error_reporting(1);
@ini_set('display_errors', true);
define('RD', dirname (__FILE__));
date_default_timezone_set("Europe/Kiev");
require_once (RD."/lib/mysql_class.php");
require_once (RD."/lib/DbSingleton.php");
$db=DbSingleton::getTokoDb();

if(!empty($_FILES)){
    $smart_news_id=$_REQUEST["photo_smart_news_id"];

    $targetDir = RD."/uploads/images/smart_news/";
    $fileName = $_FILES["file"]["name"];
    $fileName = $smart_news_id.".".pathinfo($fileName, PATHINFO_EXTENSION);
    $targetFile = $targetDir.$fileName;

    if (move_uploaded_file($_FILES["file"]["tmp_name"],$targetFile)){
        $db->query("INSERT INTO `A_SMART_UPLOAD` (`smart_brand_id`,`target_dir`,`tmpFile`,`filename`,`status`) VALUES ('$smart_news_id','$targetDir','$targetFile','$fileName',1);");
        $db->query("UPDATE `SMART_NEWS` SET `IMAGE`='$fileName' WHERE `ID`='$smart_news_id' LIMIT 1;");
    }
}
