<?php
$access=new access; $mf="report_margin";
list($accss,$acc_lvl)=$access->check_user_access($mf);$alg_u=0;
if ($accss=="1"){
	
	require_once (RD."/lib/report_margin_class.php");$report_margin=new report_margin;$gmanual=new gmanual;
	$form_htm=RD."/tpl/report_margin.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
//	$form=str_replace("{report_margin_range}",$report_margin->showSeoReports(),$form);
	
	$link=gnLink; if (substr($link,-1)=="/"){$link=substr($link,0,strlen($link)-1);} $links=explode("/", $link);
	
	$w=$links[1]; $date_from=$links[2]; $date_to=$links[3]; $doc_type_id=$links[4]; $client_status=$links[5];  $doc_status=$links[6]; 

	if ($w=="") {
		$content=str_replace("{work_window}", $form, $content);
		$content=str_replace("{date}", date("Y-m-d"), $content);
		$content=str_replace("{doc_type_select}", $gmanual->showGmanualSelectList('doc_type_id',0), $content);
		$content=str_replace("{cash_select}", $report_margin->getCashList(), $content);
		//$content=str_replace("{managers_list}", $report_margin->getManagersList(), $content);
	}
	
	if ($w=="download") {
//		$report_margin->exportSeoReportsExcel($date_start,$date_end,$managers,$cash_id,$client_status);
	}
	
}
if ($accss=="0"){
	$content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
}
?>
