<?php

$access = new access;
$mf = "tpoint";
list($accss, $acc_lvl) = $access->check_user_access($mf);
$alg_u = 0;

if ($accss == "1") {
    require_once (RD . "/lib/tpoint_class.php");
    $tpoint = new tpoint;
	$form_htm = RD . "/tpl/tpoint.htm"; $form = "";
	if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$content = str_replace("{work_window}", $form, $content);
    $content = str_replace("{tpoint_range}", $tpoint->show_tpoint_list(), $content);

	if ($alg_u == 0) { //�� ������ ����� �� ��������� � �������
		$content = str_replace("{work_window}", $access->show_access_deny($mf), $content);
	}
}

if ($accss == "0") {
	$content = str_replace("{work_window}", $access->show_access_deny($mf), $content);
}
