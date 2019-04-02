<?php
$access=new access; $mf="storage_duplicates";
list($accss,$acc_lvl)=$access->check_user_access($mf);$alg_u=0;

if ($accss=="1"){
	include_once 'lib/storage_duplicates_class.php'; $storage_duplicates=new storage_duplicates;
	$content=str_replace("{work_window}", $storage_duplicates->showStorageDuplicates(), $content);
	
	if ($alg_u==0){ //не надано права на операціїї з розділом
		$content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
	}
}

if ($accss=="0"){
	$content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
}
