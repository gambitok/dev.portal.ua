<?php
$access = new access;
$mf = "unknown_numbers";
list($accss, $acc_lvl) = $access->check_user_access($mf);
$alg_u = 0;

if ($accss == "1") {
	require_once (RD . "/lib/suppl_class.php");
	$suppl = new suppl;

    $link = gnLink;
    if (substr($link,-1) == "/") {
        $link = substr($link, 0, strlen($link) - 1);
    }
    $links = explode("/", $link);
    $w = $links[1];

	if ($w == "") {
        $form_htm = RD . "/tpl/unknown_numbers.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $form = str_replace("{numbers_select}",$suppl->showSupplList(),$form);
        $content = str_replace("{work_window}", $form, $content);
    }

    if ($w == "download-brands") {
        $suppl_id = $links[2];
        $suppl_brand = $links[3];
        $suppl->exportNumbersBrandList($suppl_id, $suppl_brand);
    }

}

if ($accss == "0") {
	$content = str_replace("{work_window}", $access->show_access_deny($mf), $content);
}
