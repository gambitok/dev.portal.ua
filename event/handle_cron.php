<?php

$access = new access;
$mf = "handle_cron";
[$accss, $acc_lvl] = $access->check_user_access($mf);
$alg_u = 0;
require_once RD . '/lib/settings_new_class.php';

if ($accss == "1") {
    $settings_new = new SettingsNewClass;
    $content = str_replace("{work_window}", $settings_new->showHandleCron(), $content);
}

if ($accss == "0") {
    $content = str_replace("{work_window}", $access->show_access_deny($mf), $content);
}
