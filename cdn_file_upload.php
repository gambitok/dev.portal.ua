<?php
error_reporting(1);
@ini_set('display_errors', true);
define('RD', dirname (__FILE__));
date_default_timezone_set("Europe/Kiev");
require_once (RD."/lib/mysql_class.php");
require_once (RD."/lib/DbSingleton.php");
$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();

if(!empty($_FILES)){

	$art_id=$_REQUEST["cdn_art_id"];
	$client_id=$_REQUEST["cdn_client_id"];
	session_start(); $user_id=$_SESSION["media_user_id"];

	if ($user_id>0 && $art_id!="" && $art_id>0){
		$targetDir = RD."/cdn/artfiles/$art_id/"; if (!is_dir($targetDir)){mkdir($targetDir,0777);}
		$fileName = $_FILES['file']['name']; $real_file_name=iconv("utf-8","windows-1251",$fileName);
		$fileName = "myparts_cdn_".$art_id."_".time().".".pathinfo($fileName, PATHINFO_EXTENSION);
		$targetFile = $targetDir.$fileName;
		
		if(move_uploaded_file($_FILES['file']['tmp_name'],$targetFile)){session_start(); 
			$dbt->query("INSERT INTO `T2_ARTICLES_CDN` (`ART_ID`,`USER_ID`,`FILE_NAME`,`NAME`) VALUES ('$art_id','$user_id','$fileName','$real_file_name');");
		}
	}

	if ($user_id>0 && $client_id!="" && $client_id>0){
		$targetDir = RD."/cdn/clfiles/$client_id/"; if (!is_dir($targetDir)){mkdir($targetDir,0777);}
		$fileName = $_FILES['file']['name']; $real_file_name=iconv("utf-8","windows-1251",$fileName);
		$fileName = "myparts_cdn_".$client_id."_".time().".".pathinfo($fileName, PATHINFO_EXTENSION);
		$targetFile = $targetDir.$fileName;
		
		if(move_uploaded_file($_FILES['file']['tmp_name'],$targetFile)){session_start(); 
			$db->query("INSERT INTO `A_CLIENTS_CDN` (`CLIENT_ID`,`USER_ID`,`FILE_NAME`,`NAME`) VALUES ('$client_id','$user_id','$fileName','$real_file_name');");
		}
	}

	$client_id=$_REQUEST["dtls_client_id"];
	$file_type=$_REQUEST["dtls_file_type"];
	if ($user_id>0 && $client_id!="" && $client_id>0 && $file_type!=""){
		$targetDir = RD."/cdn/clfiles/$client_id/"; if (!is_dir($targetDir)){mkdir($targetDir,0777);}
		$fileName = $_FILES['file']['name']; $real_file_name=iconv("utf-8","windows-1251",$fileName);
		$fileName = "myparts_dtls_".$client_id."_".time().".".pathinfo($fileName, PATHINFO_EXTENSION);
		$targetFile = $targetDir.$fileName;
		
		if(move_uploaded_file($_FILES['file']['tmp_name'],$targetFile)){session_start(); 
			$db->query("INSERT INTO `A_CLIENTS_DTLS` (`CLIENT_ID`,`FILE_TYPE`,`USER_ID`,`FILE_NAME`,`NAME`) VALUES ('$client_id','$file_type','$user_id','$fileName','$real_file_name');");
		}
	}
	
	$income_id=$_REQUEST["cdn_income_id"];
	if ($user_id>0 && $income_id!="" && $income_id>0){
		$targetDir = RD."/cdn/income_files/$income_id/"; if (!is_dir($targetDir)){mkdir($targetDir,0777);}
		$fileName = $_FILES['file']['name']; $real_file_name=iconv("utf-8","windows-1251",$fileName);
		$fileName = "myparts_income_".$income_id."_".time().".".pathinfo($fileName, PATHINFO_EXTENSION);
		$targetFile = $targetDir.$fileName;
		
		if(move_uploaded_file($_FILES['file']['tmp_name'],$targetFile)){session_start(); 
			$db->query("INSERT INTO `J_INCOME_CDN` (`income_id`,`user_id`,`file_name`,`name`) VALUES ('$income_id','$user_id','$fileName','$real_file_name');");
		}
	}

	$import_artprice=$_REQUEST["import_artprice"];
	if ($user_id>0 && $import_artprice!="" && $import_artprice==1){
		$targetDir = RD."/cdn/import_artprice_files/$user_id/"; if (!is_dir($targetDir)){mkdir($targetDir,0777);}
		$fileName = $_FILES['file']['name']; $real_file_name=iconv("utf-8","windows-1251",$fileName);
		$fileName = "myparts_artprice_".$user_id."_".time().".".pathinfo($fileName, PATHINFO_EXTENSION);
		$targetFile = $targetDir.$fileName;
		
		if(move_uploaded_file($_FILES['file']['tmp_name'],$targetFile)){session_start(); 
			$db->query("update `J_IMPORT_ARTPRICE_CSV` set `status`=0 WHERE `user_id`='$user_id';");
			$db->query("INSERT INTO `J_IMPORT_ARTPRICE_CSV` (`user_id`,`file_name`,`name`) VALUES ('$user_id','$fileName','$real_file_name');");
		}
	}

	$import_rest=$_REQUEST["import_rest"];
	if ($user_id>0 && $import_rest!="" && $import_rest==1){
		$targetDir = RD."/cdn/import_rest_files/$user_id/"; if (!is_dir($targetDir)){mkdir($targetDir,0777);}
		$fileName = $_FILES['file']['name']; $real_file_name=iconv("utf-8","windows-1251",$fileName);
		$fileName = "myparts_rest_".$user_id."_".time().".".pathinfo($fileName, PATHINFO_EXTENSION);
		$targetFile = $targetDir.$fileName;
		
		if(move_uploaded_file($_FILES['file']['tmp_name'],$targetFile)){session_start(); 
			$db->query("update `J_IMPORT_REST_CSV` set `status`=0 WHERE `user_id`='$user_id';");
			$db->query("INSERT INTO `J_IMPORT_REST_CSV` (`user_id`,`file_name`,`name`) VALUES ('$user_id','$fileName','$real_file_name');");
		}
	}

	$jmoving_id=$_REQUEST["cdn_jmoving_id"];
	if ($user_id>0 && $jmoving_id!="" && $jmoving_id>0){
		$targetDir = RD."/cdn/jmoving_files/$jmoving_id/"; if (!is_dir($targetDir)){mkdir($targetDir,0777);}
		$fileName = $_FILES['file']['name']; $real_file_name=iconv("utf-8","windows-1251",$fileName);
		$fileName = "myparts_jmoving_".$jmoving_id."_".time().".".pathinfo($fileName, PATHINFO_EXTENSION);
		$targetFile = $targetDir.$fileName;
		
		if(move_uploaded_file($_FILES['file']['tmp_name'],$targetFile)){session_start(); 
			$db->query("INSERT INTO `J_MOVING_CDN` (`jmoving_id`,`user_id`,`file_name`,`name`) VALUES ('$jmoving_id','$user_id','$fileName','$real_file_name');");
		}
	}

	$spend_id=$_REQUEST["cdn_spend_id"];
	if ($user_id>0 && $spend_id!="" && $spend_id>0){
		$targetDir = RD."/cdn/money_spend_files/$spend_id/"; if (!is_dir($targetDir)){mkdir($targetDir,0777);}
		$fileName = $_FILES['file']['name']; $real_file_name=iconv("utf-8","windows-1251",$fileName);
		$fileName = "myparts_money_spend_".$spend_id."_".time().".".pathinfo($fileName, PATHINFO_EXTENSION);
		$targetFile = $targetDir.$fileName;
		
		if(move_uploaded_file($_FILES['file']['tmp_name'],$targetFile)){session_start(); 
			$db->query("INSERT INTO `MONEY_SPEND_CDN` (`spend_id`,`user_id`,`file_name`,`name`) VALUES ('$spend_id','$user_id','$fileName','$real_file_name');");
		}
	}
	
	$dp_id=$_REQUEST["cdn_dp_id"];
	if ($user_id>0 && $dp_id!="" && $dp_id>0){
		$targetDir = RD."/cdn/dp_files/$dp_id/"; if (!is_dir($targetDir)){mkdir($targetDir,0777);}
		$fileName = $_FILES['file']['name']; $real_file_name=iconv("utf-8","windows-1251",$fileName);
		$fileName = "myparts_dp_".$income_id."_".time().".".pathinfo($fileName, PATHINFO_EXTENSION);
		$targetFile = $targetDir.$fileName;
		
		if(move_uploaded_file($_FILES['file']['tmp_name'],$targetFile)){session_start(); 
			$db->query("INSERT INTO `J_DP_CDN` (`dp_id`,`user_id`,`file_name`,`name`) VALUES ('$dp_id','$user_id','$fileName','$real_file_name');");
		}
	}
	
	$scheme_template_id=$_REQUEST["scheme_template_id"];
	if ($scheme_template_id!="" && $scheme_template_id>0){
		$targetDir = RD."/cdn/articles_scheme/$scheme_template_id/"; if (!is_dir($targetDir)){mkdir($targetDir,0777);}
		
		$fileName = $_FILES['file']['name']; $real_file_name=iconv("utf-8","windows-1251",$fileName);
		$fileName = "myparts_scheme_".$scheme_template_id."_".time().".".pathinfo($fileName, PATHINFO_EXTENSION);
		$targetFile = $targetDir.$fileName;
		
		if(move_uploaded_file($_FILES['file']['tmp_name'],$targetFile)){session_start(); 
			$dbt->query("INSERT INTO `T2_ARTICLES_SCHEME` (`TEMPLATE_ID`,`USER_ID`,`FILE_NAME`,`NAME`) VALUES ('$scheme_template_id','$user_id','$fileName','$real_file_name');");
		}
	}
	
	$income_str_id=$_REQUEST["cdn_income_str_id"];
	if ($user_id>0 && $income_str_id!="" && $income_str_id>0){
		$targetDir = RD."/cdn/incomeSpendFiles/$income_str_id/"; if (!is_dir($targetDir)){mkdir($targetDir,0777);}
		$fileName = $_FILES['file']['name']; $real_file_name=iconv("utf-8","windows-1251",$fileName);
		$fileName = "myparts_cdn_".$income_str_id."_".time().".".pathinfo($fileName, PATHINFO_EXTENSION);
		$targetFile = $targetDir.$fileName;
		
		if(move_uploaded_file($_FILES['file']['tmp_name'],$targetFile)){session_start(); 
			$db->query("INSERT INTO `J_INCOME_SPEND_FILES` (`str_id`,`user_id`,`file_name`,`name`) VALUES ('$income_str_id','$user_id','$fileName','$real_file_name');");
		}
	}
	
	$csv_income_id=$_REQUEST["csv_income_id"];
	if ($user_id>0 && $csv_income_id!="" && $csv_income_id>0){
		$targetDir = RD."/cdn/income_files/csv/$csv_income_id/"; if (!is_dir($targetDir)){mkdir($targetDir,0777);}
		$fileName = $_FILES['file']['name']; $real_file_name=iconv("utf-8","windows-1251",$fileName);
		$fileName = "myparts_income_cvs_".$csv_income_id."_".time().".".pathinfo($fileName, PATHINFO_EXTENSION);
		$targetFile = $targetDir.$fileName;
		
		if(move_uploaded_file($_FILES['file']['tmp_name'],$targetFile)){session_start(); 
			$r=$db->query("SELECT `file_name` FROM `J_INCOME_CSV` WHERE `income_id`='$csv_income_id' LIMIT 0,1;");$n=$db->num_rows($r);
			if ($n==1){
				$e_file_name=$db->result($r,0,"file_name");
				if (file_exists(RD."/cdn/income_files/csv/$csv_income_id/$e_file_name")){unlink(RD."/cdn/income_files/csv/$csv_income_id/$e_file_name");}
				$db->query("DELETE FROM `J_INCOME_CSV` WHERE `income_id`='$csv_income_id';");
			}
			$db->query("INSERT INTO `J_INCOME_CSV` (`income_id`,`user_id`,`file_name`,`name`) VALUES ('$csv_income_id','$user_id','$fileName','$real_file_name');");
		}
	}
	
	$csv_suppl_id=$_REQUEST["csv_suppl_id"];$csv_suppl_type=$_REQUEST["csv_suppl_type"];
	if ($user_id>0 && $csv_suppl_id!="" && $csv_suppl_id>0 && $csv_suppl_type=="price"){
		$targetDir = RD."/cdn/suppl_files/price/$csv_suppl_id/"; if (!is_dir($targetDir)){mkdir($targetDir,0777);}
		$fileName = $_FILES['file']['name']; $real_file_name=iconv("utf-8","windows-1251",$fileName);
		$fileName = "myparts_suppl_price_".$csv_suppl_id."_".time().".".pathinfo($fileName, PATHINFO_EXTENSION);
		$targetFile = $targetDir.$fileName; 
		
		if(move_uploaded_file($_FILES['file']['tmp_name'],$targetFile)){
			$r=$db->query("SELECT `file_name` FROM `T2_SUPPL_CSV` WHERE `suppl_id`='$csv_suppl_id' and `ftype`='price' LIMIT 0,1;");$n=$db->num_rows($r);
			if ($n==1){
				$e_file_name=$db->result($r,0,"file_name");
				if (file_exists(RD."/cdn/suppl_files/price/$csv_suppl_id/$e_file_name")){unlink(RD."/cdn/suppl_files/price/$csv_suppl_id/$e_file_name");}
				$db->query("DELETE FROM `T2_SUPPL_CSV` WHERE `suppl_id`='$csv_suppl_id' and `ftype`='price';");
			}
			$db->query("INSERT INTO `T2_SUPPL_CSV` (`suppl_id`,`ftype`,`user_id`,`file_name`,`name`) VALUES ('$csv_suppl_id','price','$user_id','$fileName','$real_file_name');");
		}
	}

	if ($user_id>0 && $csv_suppl_id!="" && $csv_suppl_id>0 && $csv_suppl_type=="index"){
		$targetDir = RD."/cdn/suppl_files/index/$csv_suppl_id/"; if (!is_dir($targetDir)){mkdir($targetDir,0777);}
		$fileName = $_FILES['file']['name']; $real_file_name=iconv("utf-8","windows-1251",$fileName);
		$fileName = "myparts_suppl_index_".$csv_suppl_id."_".time().".".pathinfo($fileName, PATHINFO_EXTENSION);
		$targetFile = $targetDir.$fileName; 
		
		if(move_uploaded_file($_FILES['file']['tmp_name'],$targetFile)){session_start(); 
			$r=$db->query("SELECT `file_name` FROM `T2_SUPPL_CSV` WHERE `suppl_id`='$csv_suppl_id' and `ftype`='index' LIMIT 0,1;");$n=$db->num_rows($r);
			if ($n==1){
				$e_file_name=$db->result($r,0,"file_name");
				if (file_exists(RD."/cdn/suppl_files/index/$csv_suppl_id/$e_file_name")){unlink(RD."/cdn/suppl_files/index/$csv_suppl_id/$e_file_name");}
				$db->query("DELETE FROM `T2_SUPPL_CSV` WHERE `suppl_id`='$csv_suppl_id' and `ftype`='index';");
			}
			$db->query("INSERT INTO `T2_SUPPL_CSV` (`suppl_id`,`ftype`,`user_id`,`file_name`,`name`) VALUES ('$csv_suppl_id','index','$user_id','$fileName','$real_file_name');");
		}
	}
	
}
