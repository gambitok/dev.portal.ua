<?php

$access = new access;
$mf = "auto";
list($accss, $acc_lvl) = $access->check_user_access($mf);
$alg_u = 0;

if ($accss == "1") {
    require_once (RD."/lib/auto_class.php");
    $auto = new auto_class;

    $link = gnLink;
    if (substr($link, -1) == "/") {
        $link = substr($link, 0, strlen($link) - 1);
    }
    $links = explode("/", $link);
    $w = $links[0];

    $content = str_replace("{work_window}", $auto->showManufacturersList(), $content);

    if ($alg_u == 0) { //не надано права на операціїї з розділом
        $content = str_replace("{work_window}", $access->show_access_deny($mf), $content);
    }
}

if ($accss == "0") {
    $content = str_replace("{work_window}", $access->show_access_deny($mf), $content);
}
