<?php

$access = new access;
$mf = "catalog_parts";
list($accss, $acc_lvl) = $access->check_user_access($mf);
$alg_u = 0;

if ($accss == "1") {
    require_once (RD . "/lib/catalog_parts_class.php");
    $catalog_parts = new CatalogParts;

    $content = str_replace("{work_window}", $catalog_parts->getCatalogPartsForm(), $content);

    if ($alg_u == 0) {
        $content = str_replace("{work_window}", $access->show_access_deny($mf), $content);
    }
}

if ($accss == "0") {
    $content = str_replace("{work_window}", $access->show_access_deny($mf), $content);
}
