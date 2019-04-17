<?php
session_start();$user_id=$_SESSION["user_id"];
if ($user_id!=""){
	define('RD', dirname (__FILE__));$op=$_REQUEST["op"];
	require_once (RD."/lib/mysql_class.php");
	require_once (RD."/lib/DbSingleton.php");$db=DbSingleton::getDb();
	require_once (RD."/lib/slave_class.php");$slave=new slave;
	/*$headers = apache_request_headers();
	foreach ($headers as $name => $value) {
//		if ($name=='Content-Type' and $value=="image/jpeg"){ $filename=time()."-".rand()."-".rand()."-".rand().".jpg"; }
		$filename=time()."-".rand()."-".rand()."-".rand().".png";
	}*/
	$anketa_id=$_REQUEST["anketa_id"];
	if ($anketa_id!=""){$prPath=RD."/uploads/anketa";if(!is_dir($prPath)){mkdir($prPath,0777);}
		$fp = fopen($prPath.'/'.$filename,'a+');fwrite($fp, file_get_contents('php://input'), strlen(file_get_contents('php://input')));fclose($fp);
	}
	if ($op=="save_anketa"){ 
		$r=$db->query("select max(id) as mid from estate_anketa_files;");$photo_id=$db->result($r,0,"mid")+1;
		$db->query("insert into estate_anketa_files (id,estate_anketa_id,file_name,ison) values ('$photo_id','$anketa_id','$filename','1');");
	}
	$bid_id=$_REQUEST["bid_id"];
	if ($bid_id!=""){$prPath=RD."/uploads/bids";if(!is_dir($prPath)){mkdir($prPath,0777);}
		$fp = fopen($prPath.'/'.$filename,'a+');fwrite($fp, file_get_contents('php://input'), strlen(file_get_contents('php://input')));fclose($fp);
	}
	if ($op=="save_bid"){ 
		$r=$db->query("select max(id) as mid from bids_files;");$photo_id=$db->result($r,0,"mid")+1;
		$db->query("insert into bids_files (id,bid_id,file_name,ison) values ('$photo_id','$bid_id','$filename','1');");
	}
	$doc_id=$_REQUEST["doc_id"];
	if ($doc_id!=""){$prPath=RD."/uploads/docs";if(!is_dir($prPath)){mkdir($prPath,0777);}
		$fp = fopen($prPath.'/'.$filename,'a+');fwrite($fp, file_get_contents('php://input'), strlen(file_get_contents('php://input')));fclose($fp);
	}
	if ($op=="save_doc"){ 
		$r=$db->query("select max(id) as mid from docs_files;");$photo_id=$db->result($r,0,"mid")+1;
		$db->query("insert into docs_files (id,doc_id,file_name,ison) values ('$photo_id','$doc_id','$filename','1');");
	}
	$bidId=$_REQUEST["bidId"];$estateId=$_REQUEST["estateId"];$fileId=$_REQUEST["fileId"];$filename=$_REQUEST["file_name"];
	if ($fileId!=""){$prPath=RD."/uploads/bidFiles";
		if(!is_dir($prPath."/$bidId")){mkdir($prPath."/$bidId",0777);}
		if(!is_dir($prPath."/$bidId/$estateId")){mkdir($prPath."/$bidId/$estateId",0777);}$fPath=$prPath."/$bidId/$estateId";
		$fp = fopen($fPath.'/'.$filename,'a+');fwrite($fp, file_get_contents('php://input'), strlen(file_get_contents('php://input')));fclose($fp);
	}
	if ($op=="save_bid_files" && $fileId!="undefined"){ $db->query("update bids_files set file_name='$filename' where id='$fileId' and ison='1';"); }
	if ($op=="save_bid_files" && $fileId=="undefined"){ $db->query("insert into bids_files (`bid_id`,`estate_id`,`file_name`,`ison`) values ('$bidId','$estateId','$filename','1');"); }
}
