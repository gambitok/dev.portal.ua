<?php

$access = new access;
$mf = "checkbox";
list($accss, $acc_lvl) = $access->check_user_access($mf);
$alg_u = 0;



if ($accss == "1") {
    require_once(RD . "/lib/paybox_class.php");
    $paybox = new paybox;

    $content = str_replace("{work_window}", $paybox->showCheckboxForm(), $content);

//    if ($alg_u == 0) {
//        $content = str_replace("{work_window}", $access->show_access_deny($mf), $content);
//    }
}

if ($accss == "0") {
    $content = str_replace("{work_window}", $access->show_access_deny($mf), $content);
}
