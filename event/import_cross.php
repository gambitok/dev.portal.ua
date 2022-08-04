<?php
error_reporting(E_ERROR);
@ini_set('display_errors', true);
@ini_set('html_errors', false);
ini_set('memory_limit', '2048M');

$access = new access;
$mf = "import_cross";
list($accss, $acc_lvl) = $access->check_user_access($mf);
$alg_u = 0;

if ($accss == "1") {
    require_once (RD."/lib/catalogue_class.php");
    $catalogue = new catalogue();

    $content = str_replace("{work_window}", $catalogue->showImportCrossForm(), $content);

    if ($alg_u == 0) { //не надано права на операціїї з розділом
        $content = str_replace("{work_window}", $access->show_access_deny($mf), $content);
    }
}

if ($accss == "0") {
    $content = str_replace("{work_window}", $access->show_access_deny($mf), $content);
}
