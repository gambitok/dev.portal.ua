<?php
$access=new access; $mf="money_move";
list($accss,$acc_lvl)=$access->check_user_access($mf);$alg_u=0;
if ($accss=="1"){
	
	require_once (RD."/lib/money_move_class.php");$money_move=new money_move;
	$form_htm=RD."/tpl/money_move.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$content=str_replace("{work_window}", $form, $content);
	$link=gnLink; if (substr($link,-1)=="/"){$link=substr($link,0,strlen($link)-1);} $links=explode("/", $link);$w=$links[1]; 
	if ($w==""){
		$range_list=$money_move->show_money_move_list();
		$content=str_replace("{money_move_range}", $range_list, $content);
	}
	if ($w=="printMMv"){ 
		$invoice_id=$links[2];
		$form=$money_move->printDpSaleInvoice($invoice_id);
	}
	

	if ($alg_u==0){ //не надано права на операціїї з розділом
		$content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
	}
	
}
if ($accss=="0"){
	$content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
}
?>