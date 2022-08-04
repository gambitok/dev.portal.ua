<?php
$access=new access;
$mf="country";
list($accss,$acc_lvl)=$access->check_user_access($mf);
$alg_u=0;

if ($accss=="1"){
    require_once (RD."/lib/clients_class.php");
    $clients=new clients;
	$form_htm=RD."/tpl/country.htm";$form="";
	if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$content=str_replace("{work_window}", $form, $content);

	if ($w==""){
		 $range_list=$clients->show_country_list();
		 $content=str_replace("{country_range}", $range_list, $content);
	}

	if ($alg_u==0){ //не надано права на операціїї з розділом
		$content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
	}
}

if ($accss=="0"){
	$content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
}
