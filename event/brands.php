<?php

$access = new access;
$mf = "brands";
list($accss, $acc_lvl) = $access->check_user_access($mf);
$alg_u = 0;

if ($accss == "1") {
    require_once (RD . "/lib/brands_class.php");
    $brands = new brands;
	$form_htm = RD . "/tpl/brands.htm"; $form = "";
	if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$content = str_replace("{work_window}", $form, $content);
    $content = str_replace("{brands_range}", $brands->show_brands_list(), $content);

	if ($alg_u == 0) { //�� ������ ����� �� ��������� � �������
		$content = str_replace("{work_window}", $access->show_access_deny($mf), $content);
	}
}

if ($accss == "0") {
	$content = str_replace("{work_window}", $access->show_access_deny($mf), $content);
}
