<?php

$access=new access; $mf="action_clients";
list($accss,$acc_lvl)=$access->check_user_access($mf);$alg_u=0;

if ($accss=="1"){
    require_once (RD."/lib/action_clients_class.php");$action_clients=new action_clients;
    $form_htm=RD."/tpl/action_clients.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}

    $link=gnLink; if (substr($link,-1)=="/"){$link=substr($link,0,strlen($link)-1);} $links=explode("/", $link);$w=$links[1];

    if ($w==""){
        $range_list=$action_clients->showActionClientsList();
        $client_list=$action_clients->getActionClientsList();
        $form=str_replace("{action_clients_range}",$range_list,$form);
        $form=str_replace("{client_list}",$client_list,$form);
    }

    $content=str_replace("{work_window}", $form, $content);

    if ($alg_u==0){ //не надано права на операціїї з розділом
        $content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
    }

}

if ($accss=="0"){
    $content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
}
