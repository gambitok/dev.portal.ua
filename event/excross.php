<?php
$access=new access; $mf="users";
list($accss,$acc_lvl)=$access->check_user_access($mf);$alg_u=0;
include_once 'lib/excross.php';

if ($accss=="1"){
	$form_htm=RD."/tpl/excross.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$content=str_replace("{work_window}", $form, $content);
    $range_list=showBrandsSelect(); 
	$content=str_replace("{brands_list}", $range_list, $content);
	
	if ($alg_u==0){ //не надано права на операціїї з розділом
		$content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
	}
}

if ($accss=="0"){
	$content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
}
