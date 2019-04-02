<?php
$access=new access; $mf="seo_reports";
list($accss,$acc_lvl)=$access->check_user_access($mf);$alg_u=0;

if ($accss=="1"){
	require_once (RD."/lib/seo_reports_class.php");$seo_reports=new seo_reports;
	$form_htm=RD."/tpl/seo_reports.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$link=gnLink; if (substr($link,-1)=="/"){$link=substr($link,0,strlen($link)-1);} $links=explode("/", $link);
	$w=$links[1];$date_start=$links[2];$date_end=$links[3];$managers=$links[4];$cash_id=$links[5];$client_status=$links[6];

	if ($w=="") {
		$content=str_replace("{work_window}", $form, $content);
		$content=str_replace("{date}", date("Y-m-d"), $content);
		$content=str_replace("{cash_select}", $seo_reports->getCashList(), $content);
		$content=str_replace("{managers_list}", $seo_reports->getManagersList(), $content);
	}
}

if ($accss=="0"){
	$content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
}

