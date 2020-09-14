<?php
$access=new access; $mf="report_sales";
list($accss,$acc_lvl)=$access->check_user_access($mf);$alg_u=0;

if ($accss=="1"){
	require_once (RD."/lib/catalogue_class.php"); $cat=new catalogue; $date=date("Y-m");
	$form_htm=RD."/tpl/report_sales.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}	
	$form=str_replace("{report_sales_range}",$cat->showReportSales($date,"1"),$form);
	$content=str_replace("{work_window}", $form, $content);
	$content=str_replace("{tpoint_select}", $cat->getTpointList(), $content);
	$content=str_replace("{date_today}", date("Y-m"), $content);
}

if ($accss=="0"){
	$content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
}
