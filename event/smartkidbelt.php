<?php

$access = new access;
$mf = "smartkidbelt";
list($accss, $acc_lvl) = $access->check_user_access($mf);
$alg_u = 0;

if ($accss == "1") {
    require_once (RD . "/lib/smartkidbelt_class.php");
    $smart = new smartkidbelt;
    $form_htm = RD . "/tpl/smartkidbelt.htm";
    $form = "";
    if (file_exists("$form_htm")) {
        $form = file_get_contents($form_htm);
    }
    $content = str_replace("{work_window}", $form, $content);
    $content = str_replace("{brands_range}", $smart->showBrandsList(), $content);
    $content = str_replace("{nav_range}", $smart->showNavList(), $content);
    $content = str_replace("{faq_range}", $smart->showFaqList(), $content);
    $content = str_replace("{news_range}", $smart->showNewsList(), $content);

    if ($alg_u == 0) { //не надано права на операціїї з розділом
        $content = str_replace("{work_window}", $access->show_access_deny($mf), $content);
    }
}

if ($accss == "0") {
    $content = str_replace("{work_window}", $access->show_access_deny($mf), $content);
}
