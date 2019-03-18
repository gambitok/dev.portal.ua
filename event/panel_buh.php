<?php

$access=new access; $mf="panel_buh";
list($accss,$acc_lvl)=$access->check_user_access($mf);$alg_u=0;

if ($accss=="1"){	
	require_once (RD."/lib/panel_buh_class.php"); //$panel_buh=new panel_buh;
	$form_htm=RD."/tpl/panel_buh.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$content=str_replace("{work_window}", $form, $content);
	if ($alg_u==0){ 
		$content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
	}
}
if ($accss=="0"){
	$content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
}

?>