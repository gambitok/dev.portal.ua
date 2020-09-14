<?php
$access=new access; $mf="back_clients";
list($accss,$acc_lvl)=$access->check_user_access($mf);$alg_u=0;

if ($accss=="1"){
    require_once (RD."/lib/back_clients_class.php");$back_clients=new back_clients;
	$form_htm=RD."/tpl/back_clients.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$content=str_replace("{work_window}", $form, $content);
	$link=gnLink; if (substr($link,-1)=="/"){$link=substr($link,0,strlen($link)-1);} $links=explode("/", $link);$w=$links[1];

	if ($w==""){
		$range_list=$back_clients->show_back_clients_list();
		$content=str_replace("{back_clients_range}", $range_list, $content); $data_cur=date("Y-m-d");
		$data_old = date('Y-m-d', strtotime('-7 day', strtotime($data_cur)));	
	    $content=str_replace("{date_old}", $data_old, $content);
	    $content=str_replace("{date_today}", $data_cur, $content);
	}

    if ($w=="exportExcelSlIv"){
        $back_id=$links[2];$separator=$links[3];
        $form=$back_clients->exportBackClientsExcel($back_id,$separator);
    }

	if ($w=="printBCn1"){ 
		$back_id=$links[2];
		$form=$back_clients->printBackClientsN1($back_id);
	}

	if ($alg_u==0){ //не надано права на операціїї з розділом
		$content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
	}
}

if ($accss=="0"){
	$content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
}
