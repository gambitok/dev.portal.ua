<?php
$access=new access; $mf="undistribcells";
list($accss,$acc_lvl)=$access->check_user_access($mf);$alg_u=0;

if ($accss=="1"){
	require_once (RD."/lib/undistribcells_class.php");
	$undistribcells=new undistribcells;
	$form_htm=RD."/tpl/undistribcells.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$content=str_replace("{work_window}", $form, $content);

	if ($w==""){
		 $range_list=$undistribcells->show_undistribcells_list();
		 $content=str_replace("{undistribcells_range}", $range_list, $content);
	}

	if ($alg_u==0){ //не надано права на операціїї з розділом
		$content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
	}
}

if ($accss=="0"){
	$content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
}
