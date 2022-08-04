<?php

$access = new access;
$mf = "storage_cells";
list($accss, $acc_lvl) = $access->check_user_access($mf);
$alg_u = 0;

if ($accss == "1"){
    require_once (RD . "/lib/storage_class.php");
    $storage = new storage;

    $link = gnLink;
    if (substr($link, -1) == "/") {
        $link = substr($link, 0, strlen($link) - 1);
    }
    $links = explode("/", $link);
    $w = $links[1];

    if ($w == "") {
        $content = str_replace("{work_window}", $storage->showStorageCostForm(), $content);
    }

    if ($w == "export") {
        $storage_id = $links[2];
        $kours_id = $links[3];
        $brand_id = $links[4];
        $storage->exportStorageCostList($cell_id);
    }

    if ($alg_u == 0) { //не надано права на операціїї з розділом
        $content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
    }
}

if ($accss == "0") {
    $content = str_replace("{work_window}", $access->show_access_deny($mf), $content);
}
