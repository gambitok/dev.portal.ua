<?php
error_reporting(0);
@ini_set('display_errors', false);
define('RD', dirname (__FILE__));
date_default_timezone_set("Europe/Kiev");
require_once (RD."/lib/mysql_class.php");
require_once (RD."/lib/DbSingleton.php");$db=DbSingleton::getTokoDb();

if(!empty($_FILES)) {
	$art_id=$_REQUEST["foto_art_id"];
	session_start(); $user_id=$_SESSION["media_user_id"];
	if ($user_id>0 && $art_id!="" && $art_id>0){
		$targetDir = RD."/uploads/images/catalogue/";
		$fileName = $_FILES["file"]["name"]; $real_file_name=iconv("utf-8","windows-1251",$fileName);
		$fileName = "" . $art_id . "_" . time() . "." . pathinfo($fileName, PATHINFO_EXTENSION);
		$targetFile = $targetDir.$real_file_name;
		if (move_uploaded_file($_FILES["file"]["tmp_name"],$targetFile)){
			$db->query("INSERT INTO `T2_PHOTOS` (`ART_ID`, `USER_ID`, `PHOTO_NAME`, `ACTIVE`) VALUES ('$art_id', '$user_id', '$real_file_name', 1);");
		}
	}
}
