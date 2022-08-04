<?php

$access = new access;
$mf = "back_suppl";
list($accss, $acc_lvl) = $access->check_user_access($mf);
$alg_u = 0;
$link = gnLink;
if (substr($link,-1) == "/") {
    $link = substr($link, 0, strlen($link) - 1);
}
$links = explode("/", $link);
$w = $links[1];

if ($accss == "1") {
    require_once (RD . "/lib/suppl_class.php");
    $back_suppl = new suppl();

    if ($w == "") {
        $content = str_replace("{work_window}", $back_suppl->showBackSupplForm(), $content);
    }

    if ($alg_u == 0) {
        $content = str_replace("{work_window}", $access->show_access_deny($mf), $content);
    }
}

if ($accss == "0") {
    $content = str_replace("{work_window}", $access->show_access_deny($mf), $content);
}