<?php
$access=new access; $mf="cash_reports";
list($accss,$acc_lvl)=$access->check_user_access($mf);$alg_u=0;

if ($accss=="1"){
	include_once 'lib/cash_reports_class.php';
	$cash_reports=new cash_reports;
	$content=str_replace("{work_window}", $cash_reports->getCashReportsFilters(), $content);
	
	if ($alg_u==0){ //не надано права на операціїї з розділом
		$content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
	}
}

if ($accss=="0"){
	$content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
}
