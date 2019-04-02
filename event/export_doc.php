<?php
$access=new access; $mf="export_doc";
list($accss,$acc_lvl)=$access->check_user_access($mf);$alg_u=0;
include_once 'lib/excross.php';

if ($accss=="1"){
	$link=gnLink; if (substr($link,-1)=="/"){$link=substr($link,0,strlen($link)-1);} $links=explode("/", $link);
	$w=$links[1];$client_id=$links[2];$date_start=$links[3];$date_end=$links[4];

	if ($w=="") {
		$form=exportDocsForm();
		$content=str_replace("{work_window}", $form, $content);
	}
	
	if ($w=="download") {
		exportDocs($client_id,$date_start,$date_end);
	}

	if ($alg_u==0){ //не надано права на операціїї з розділом
		$content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
	}
}

if ($accss=="0"){
	$content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
}
