<?php

$access = new access;
$mf = "import_photo";
list($accss, $acc_lvl) = $access->check_user_access($mf);
$alg_u = 0;

if ($accss == "1") {
    require_once (RD."/lib/catalogue_class.php");
    $catalogue = new catalogue();

    $content = str_replace("{work_window}", $catalogue->showImportPhotoForm(), $content);

    if ($alg_u == 0) { //не надано права на операціїї з розділом
        $content = str_replace("{work_window}", $access->show_access_deny($mf), $content);
    }
}

if ($accss == "0") {
    $content = str_replace("{work_window}", $access->show_access_deny($mf), $content);
}
