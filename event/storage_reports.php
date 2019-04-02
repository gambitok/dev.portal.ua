<?php
$access=new access; $mf="storage_reports";
list($accss,$acc_lvl)=$access->check_user_access($mf);$alg_u=0;

if ($accss=="1"){
	require_once (RD."/lib/storage_reports_class.php");$storage_reports=new storage_reports;
	$form_htm=RD."/tpl/storage_reports.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}

	if ($w=="") {
		$content=str_replace("{work_window}", $form, $content);
		$content=str_replace("{date}", date("Y-m-d"), $content);
		$content=str_replace("{storages_list}", $storage_reports->getStorages(), $content);
	}
}

if ($accss=="0"){
	$content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
}
