<?php

$access = new access;
$mf = "write_off";
list($accss, $acc_lvl) = $access->check_user_access($mf);
$alg_u = 0;

if ($accss == "1") {
    require_once (RD."/lib/write_off_class.php");
    $write_off = new write_off;
    require_once (RD."/lib/sale_invoice_class.php");
    require_once (RD."/lib/dp_class.php");
    $dp = new dp;
    require_once (RD."/lib/catalogue_class.php");
    require_once (RD."/lib/media_users_class.php");

    $form_htm = RD."/tpl/write_off.htm";$form="";
    if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
    $content = str_replace("{work_window}", $form, $content);
    $link = gnLink;
    if (substr($link, -1) == "/") {
        $link = substr($link, 0, strlen($link) - 1);
    }
    $links = explode("/", $link);
    $w = $links[1];

    if ($w == "" || $w == "view") {
        $invoice_id = $links[2];
        $range_list = $write_off->show_write_off_list();
        $content = str_replace("{write_off_range}", $range_list, $content);
        $content = str_replace("{date_today}", date("Y-m-d"), $content);
    }

    if ($w == "printWriteOff") {
        $write_off_id = $links[2];
        $form = $dp->printWriteOff($write_off_id);
    }

    if ($alg_u == 0) {
        $content = str_replace("{work_window}", $access->show_access_deny($mf), $content);
    }
}

if ($accss == "0") {
    $content = str_replace("{work_window}", $access->show_access_deny($mf), $content);
}
