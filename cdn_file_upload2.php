<?php
error_reporting(1);
@ini_set('display_errors', true);
define('RD', dirname (__FILE__));
date_default_timezone_set("Europe/Kiev");
require_once (RD."/lib/mysql_class.php");$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();

if(!empty($_FILES)){

	session_start(); $user_id=$_SESSION["media_user_id"];
	
	$targetDir = RD."/cdn/brands_files/index/"; if (!is_dir($targetDir)){mkdir($targetDir,0777);}
	$fileName = $_FILES['file']['name']; $real_file_name=iconv("utf-8","windows-1251",$fileName);
	$fileName = "myparts_brands_index_".time().".".pathinfo($fileName, PATHINFO_EXTENSION);
	$targetFile = $targetDir.$fileName; 
		
		if(move_uploaded_file($_FILES['file']['tmp_name'],$targetFile)){session_start(); 
			$r=$db->query("select FILE_NAME from T2_BRANDS_CSV limit 0,1;");$n=$db->num_rows($r);
			if ($n==1){
				$e_file_name=$db->result($r,0,"FILE_NAME");
				if (file_exists(RD."/cdn/brands_files/index/$e_file_name")){unlink(RD."/cdn/brands_files/index/$e_file_name");}
				$db->query("delete from T2_BRANDS_CSV where ID = 0;");
			}
			$db->query("insert into T2_BRANDS_CSV (`USER_ID`,`FILE_NAME`,`NAME`) values ('$user_id','$fileName','$real_file_name');");
		}
	
}
?>