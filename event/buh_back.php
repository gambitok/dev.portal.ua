<?php
$access=new access; $mf="buh_back";
list($accss,$acc_lvl)=$access->check_user_access($mf);$alg_u=0;

if ($accss=="1"){
	$buh_back=new buh_back;
	$form_htm=RD."/tpl/buh_back.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$form=str_replace("{back_clients_range}",$buh_back->show_back_clients_list(),$form);
	$content=str_replace("{work_window}", $form, $content);
	$content=str_replace("{date_today}", date("Y-m-d"), $content);
	$data_cur=date("Y-m-d");
	$data_old = date('Y-m-d', strtotime('-7 day', strtotime($data_cur)));	
	$content=str_replace("{date_old}", $data_old, $content);
	$content=str_replace("{date_today}", $data_cur, $content);
}

if ($accss=="0"){
	$content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
}
