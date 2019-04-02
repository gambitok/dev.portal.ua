<?php
$access=new access; $mf="suppl_orders";
list($accss,$acc_lvl)=$access->check_user_access($mf);$alg_u=0;

if ($accss=="1"){
	require_once (RD."/lib/suppl_orders_class.php");$suppl_orders=new suppl_orders;
	$form_htm=RD."/tpl/suppl_orders.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$content=str_replace("{work_window}", $form, $content);
	$link=gnLink; if (substr($link,-1)=="/"){$link=substr($link,0,strlen($link)-1);} $links=explode("/", $link);$w=$links[1];

	if ($w==""){
		$range_list=$suppl_orders->show_suppl_orders_list();
		$content=str_replace("{suppl_orders_range}", $range_list, $content);
	}
	if ($w=="printSlIv"){ 
		$invoice_id=$links[2];
		$form=$suppl_orders->printDpSaleInvoice($invoice_id);
	}
	if ($w=="printJmS1"){ 
		$suppl_orders_id=$links[2];$select_id=$links[3];
		$form=$suppl_orders->printsuppl_ordersStorageSelect($suppl_orders_id,$select_id);
	}

	if ($alg_u==0){ //не надано права на операціїї з розділом
		$content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
	}
}

if ($accss=="0"){
	$content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
}
