<?php
$access=new access; $mf="clients";
list($accss,$acc_lvl)=$access->check_user_access($mf);$alg_u=0;

if ($accss=="1"){
    $cl=new clients;
    $form_htm=RD."/tpl/clients_retail.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$content=str_replace("{work_window}", $form, $content);
	$link=gnLink; if (substr($link,-1)=="/"){$link=substr($link,0,strlen($link)-1);} $links=explode("/", $link);$w=$links[1];

	if ($w==""){
		$range_list=$cl->show_clients_retail_list();
		$content=str_replace("{clients_range}", $range_list, $content);
	}
}

if ($accss=="0"){
	$content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
}
