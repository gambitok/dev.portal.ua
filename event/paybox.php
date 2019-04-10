<?php
$access=new access; $mf="paybox";
list($accss,$acc_lvl)=$access->check_user_access($mf);$alg_u=0;

if ($accss=="1"){
    require_once (RD."/lib/paybox_class.php");
	$paybox=new paybox;
	$form_htm=RD."/tpl/paybox.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$content=str_replace("{work_window}", $form, $content);
	$link=gnLink; if (substr($link,-1)=="/"){$link=substr($link,0,strlen($link)-1);} $links=explode("/", $link);$w=$links[1];

	if ($w==""){
		$range_list=$paybox->show_paybox_list();
		$content=str_replace("{paybox_range}", $range_list, $content);
	}

	if ($w=="printSlIv"){ 
		$invoice_id=$links[2];
		$form=$paybox->printDpSaleInvoice($invoice_id);
	}

	if ($w=="printJmS1"){ 
		$paybox_id=$links[2];$select_id=$links[3];
		$form=$paybox->printpayboxStorageSelect($paybox_id,$select_id);
	}

	if ($alg_u==0){ //не надано права на операціїї з розділом
		$content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
	}
}

if ($accss=="0"){
	$content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
}
