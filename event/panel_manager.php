<?php
$access=new access; $mf="panel_manager";
list($accss,$acc_lvl)=$access->check_user_access($mf);$alg_u=0;

if ($accss=="1"){	
	require_once (RD."/lib/seo_reports_class.php"); $seo_reports=new seo_reports;
	$form=$seo_reports->showPanelManager();
	$content=str_replace("{work_window}", $form, $content);
	
	if ($alg_u==0){ 
		$content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
	}
}

if ($accss=="0"){
	$content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
}

