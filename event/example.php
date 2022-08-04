<?php
$access=new access; $mf="configs";
list($accss,$acc_lvl)=$access->check_user_access($mf);$alg_u=0;

if ($accss=="1"){
    require_once (RD."/lib/configs_class.php"); $configs=new configs;
    $form_htm=RD."/tpl/example.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
    $content=str_replace("{work_window}", $form, $content);

    $range_list=$configs->showModuleList();
    $content=str_replace("{module_range}", $range_list, $content);

    if ($alg_u==0){ //не надано права на операціїї з розділом
        $content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
    }
}

if ($accss=="0"){
    $content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
}
