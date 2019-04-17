<?php
error_reporting(0);
@ini_set('display_errors', false);
define('RD', dirname (__FILE__));
date_default_timezone_set("Europe/Kiev");
require_once (RD."/lib/mysql_class.php");
require_once (RD."/lib/DbSingleton.php");$db=DbSingleton::getTokoDb();
		
$targetDir = RD."/cdn/brands_files/";

if(!empty($_FILES)){
	$brands_id=$_REQUEST["photo_brands_id"];
	$tmpFile = $_FILES['file']['tmp_name']; 
	$filename = $targetDir.'/'.$_FILES['file']['name'];
	move_uploaded_file($tmpFile,$filename);
	$f=$_FILES['file']['name'];
	$db->query("update T2_BRAND_LINK set logo_name='$f' where brand_id='$brands_id';");
	
	$r=$db->query("select brand_id from T2_BRAND_LINK where brand_id='$brands_id';"); $n=$db->num_rows($r);

	$r2=$db->query("select BRAND_NAME from T2_BRANDS where BRAND_ID='$brands_id';");
	$name=$db->result($r2,0,"BRAND_NAME");
	
	if($n==0) $db->query("insert into T2_BRAND_LINK (`brand_id`,`name`,`logo_name`) values ('$brands_id','$name','$f');");
}
