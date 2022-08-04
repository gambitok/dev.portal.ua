<?php
date_default_timezone_set("Europe/Kiev");
$access=new access; $mf="buh_income";
list($accss,$acc_lvl)=$access->check_user_access($mf);$alg_u=0;

if ($accss=="1"){
    require_once (RD."/lib/buh_invoice_class.php"); $back_clients=new buh_invoice();
    $form_htm=RD."/tpl/buh_income.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
    $buh_income_range=$back_clients->getBuhIncomeList();
    $buh_convert_range=$back_clients->getBuhConvertList();
    $form=str_replace("{buh_income_range}", $buh_income_range, $form);
    $form=str_replace("{buh_convert_range}", $buh_convert_range, $form);
    $content=str_replace("{work_window}", $form, $content);
}

if ($accss=="0"){
    $content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
}

