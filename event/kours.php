<?php

$access = new access;
$mf = "kours";
list($accss, $acc_lvl) = $access->check_user_access($mf);
$alg_u = 0;

if ($accss == "1") {
    require_once (RD."/lib/kours_class.php");
    $kours = new kours;
	$form_htm = RD . "/tpl/kours.htm"; $form = "";
	if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$content = str_replace("{work_window}", $form, $content);
	$content = str_replace("{kours_range}", $kours->show_kours_list(), $content);

	if ($alg_u == 0) { //не надано права на операціїї з розділом
		$content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
	}
}

if ($accss == "0") {
	$content = str_replace("{work_window}", $access->show_access_deny($mf), $content);
}
