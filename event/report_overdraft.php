<?php
$access=new access; $mf="report_overdraft";
list($accss,$acc_lvl)=$access->check_user_access($mf);$alg_u=0;
if ($accss=="1"){
	require_once (RD."/lib/report_overdraft_class.php");$report_overdraft=new report_overdraft;$gmanual=new gmanual;
	$form_htm=RD."/tpl/report_overdraft.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$date_cur=date("Y-m-d");
	$content=str_replace("{work_window}", $form, $content);
	$content=str_replace("{date_today}", $date_cur, $content);
	$content=str_replace("{client_select}",$report_overdraft->getClientOverdraftList($date_cur),$content);
	$content=str_replace("{tpoint_select}",$report_overdraft->getTpointList(),$content);
	$tpoint_id=$report_overdraft->getTpointbyUser();
	$content=str_replace("{report_overdraft_range}",$report_overdraft->showReportOverdraftList($date_cur,"0",$tpoint_id)[0],$content);
}
if ($accss=="0"){
	$content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
}


?>
