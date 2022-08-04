<?php

$access=new access; $mf="storage_cells";
list($accss,$acc_lvl)=$access->check_user_access($mf);$alg_u=0;

if ($accss=="1"){
    require_once (RD."/lib/storage_class.php");
    $storage=new storage;
    $link=gnLink; if (substr($link,-1)=="/"){$link=substr($link,0,strlen($link)-1);} $links=explode("/", $link); $w=$links[1];
    $form_htm=RD."/tpl/storage_cells.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
    $content=str_replace("{work_window}", $form, $content);

    session_start(); $user_id=$_SESSION["media_user_id"];
    $tpoind_id=0; $cell_id=1197;
    if ($user_id==11 || $user_id==28) {
        $tpoind_id=2; $cell_id=3;
    }

    if ($w=="") {
        $content=str_replace("{storage_cells_range}", $storage->loadStorageCellsList($cell_id), $content);
        $content=str_replace("{storage_cells_list}", $storage->getStorageCellsList($tpoind_id), $content);
        $content=str_replace("{storage_list}", $storage->getStorageList($tpoind_id), $content);
    }

    if ($w=="export") {
        $cell_id=$links[2];
        $storage->exportStorageCellsList($cell_id);
    }

    if ($w=="export2") {
        $storage_id=$links[2];
        $storage->exportStorageList($storage_id);
    }

    if ($w=="export3") {
        $storage_id=$links[2];
        $storage->exportStorageAllList($storage_id);
    }

    if ($alg_u==0){ //не надано права на операціїї з розділом
        $content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
    }
}

if ($accss=="0"){
    $content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
}
