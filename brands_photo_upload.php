<?php
error_reporting(0);
@ini_set('display_errors', false);
define('RD', __DIR__);
date_default_timezone_set("Europe/Kiev");
require_once (RD."/lib/mysql_class.php");
require_once (RD."/lib/DbSingleton.php");
$db = DbSingleton::getTokoDb();
		
$targetDir = RD."/cdn/brands_files/";

if (!empty($_FILES)){
	$brands_id = $_REQUEST["photo_brands_id"];
	$tmpFile = $_FILES['file']['tmp_name']; 
	$filename = $targetDir.'/'.$_FILES['file']['name'];
	move_uploaded_file($tmpFile, $filename);
	$f = $_FILES['file']['name'];
	$db->query("UPDATE `T2_BRAND_LINK` SET `logo_name`='$f' WHERE `brand_id`='$brands_id';");
	
	$r = $db->query("SELECT `brand_id` FROM `T2_BRAND_LINK` WHERE `brand_id`='$brands_id';");
	$n = $db->num_rows($r);

	$r2 = $db->query("SELECT `BRAND_NAME` FROM `T2_BRANDS` WHERE `BRAND_ID`='$brands_id';");
	$name = $db->result($r2,0,"BRAND_NAME");
	
	if ($n == 0) {
	    $db->query("INSERT INTO `T2_BRAND_LINK` (`brand_id`,`name`,`logo_name`) VALUES ('$brands_id','$name','$f');");
    }
}
