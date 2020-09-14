<?php
$access=new access; $mf="storage_duplicates";
list($accss,$acc_lvl)=$access->check_user_access($mf);$alg_u=0;

if ($accss=="1"){
    require_once (RD."/lib/storage_class.php"); $storage=new storage;
	$content=str_replace("{work_window}", $storage->showStorageDuplicates(), $content);
	
	if ($alg_u==0){ //не надано права на операціїї з розділом
		$content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
	}
}

if ($accss=="0"){
	$content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
}
