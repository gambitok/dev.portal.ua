<?php

$access = new access;
$mf = "export_doc";
list($accss, $acc_lvl) = $access->check_user_access($mf);
$alg_u = 0;
require_once (RD . "/lib/excross.php");

if ($accss == "1") {
	$link = gnLink;
	if (substr($link, -1) == "/") {
	    $link = substr($link, 0, strlen($link) - 1);
	}
	$links = explode("/", $link);
	$w = $links[1];

	if ($w == "") {
		$content = str_replace("{work_window}", exportDocsForm(), $content);
	}
	
	if ($w == "download") {
        $client_id = $links[2];
        $date_start = $links[3];
        $date_end = $links[4];
		exportDocs($client_id, $date_start, $date_end);
	}

	if ($alg_u == 0) { //�� ������ ����� �� ��������� � �������
		$content = str_replace("{work_window}", $access->show_access_deny($mf), $content);
	}
}

if ($accss == "0") {
	$content = str_replace("{work_window}", $access->show_access_deny($mf), $content);
}
