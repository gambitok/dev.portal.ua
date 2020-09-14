<?php
$access=new access; $mf="suppl";
list($accss,$acc_lvl)=$access->check_user_access($mf);$alg_u=0;

if ($accss=="1"){
    require_once (RD."/lib/suppl_class.php"); $suppl=new suppl;
	$form_htm=RD."/tpl/suppl.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$content=str_replace("{work_window}", $form, $content);

	if ($w==""){
		 $range_list=$suppl->show_suppl_list();
		 $content=str_replace("{suppl_range}", $range_list, $content);
	}

	if ($alg_u==0){ //не надано права на операціїї з розділом
		$content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
	}
}

if ($accss=="0"){
	$content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
}
