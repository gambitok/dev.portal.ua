<?php
$access=new access; $mf="suppliers_cooperation";
list($accss,$acc_lvl)=$access->check_user_access($mf);$alg_u=0;
if ($accss=="1"){
		
	$form_htm=RD."/tpl/suppliers_cooperation.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$content=str_replace("{work_window}", $form, $content);
	include_once 'lib/suppl_class.php';$suppl=new suppl;
	if ($w==""){
		 $range_list=$suppl->showSupplCoopList();
		 $content=str_replace("{suppl_range}", $range_list, $content);
	}
	if ($alg_u==0){ //не надано права на операціїї з розділом
		$content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
	}
	
}
if ($accss=="0"){
	$content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
}
