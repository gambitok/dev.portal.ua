<?php
$access=new access; $mf="money_spend";
list($accss,$acc_lvl)=$access->check_user_access($mf);$alg_u=0;
if ($accss=="1"){
	
	require_once (RD."/lib/money_spend_class.php");$money_spend=new money_spend;
	$form_htm=RD."/tpl/money_spend.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$content=str_replace("{work_window}", $form, $content);
	$link=gnLink; if (substr($link,-1)=="/"){$link=substr($link,0,strlen($link)-1);} $links=explode("/", $link);$w=$links[1]; 
	if ($w==""){
		$range_list=$money_spend->show_money_spend_list();
		$content=str_replace("{money_spend_range}", $range_list, $content);
	}
	if ($w=="printMMv"){ 
		$invoice_id=$links[2];
		$form=$money_spend->printDpSaleInvoice($invoice_id);
	}
	

	if ($alg_u==0){ //не надано права на операціїї з розділом
		$content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
	}
	
}
if ($accss=="0"){
	$content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
}
?>