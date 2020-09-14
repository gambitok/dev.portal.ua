<?php
$access=new access; $mf="jpay";
list($accss,$acc_lvl)=$access->check_user_access($mf);$alg_u=0;

if ($accss=="1"){
    require_once (RD."/lib/jpay_class.php"); $jpay=new jpay;
	$form_htm=RD."/tpl/jpay.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$content=str_replace("{work_window}", $form, $content);
	$link=gnLink; if (substr($link,-1)=="/"){$link=substr($link,0,strlen($link)-1);} $links=explode("/", $link);$w=$links[1];

	if ($w==""){
		$range_list=$jpay->show_jpay_list();
		$content=str_replace("{jpay_range}", $range_list, $content);
		$content=str_replace("{date_today}", date("Y-m-d"), $content);
		$content=str_replace("{select_doc_type}", $jpay->getJpayDocTypeSelect(), $content);
		$content=str_replace("{select_jpay_name}", $jpay->getJpayNameSelect(), $content);
	}

//	if ($w=="printSlIv"){
//		$invoice_id=$links[2];
//		$form=$jpay->printDpSaleInvoice($invoice_id);
//	}
//
//	if ($w=="printJmS1"){
//		$jpay_id=$links[2];$select_id=$links[3];
//		$form=$jpay->printJpayStorageSelect($jpay_id,$select_id);
//	}

	if ($alg_u==0){ //не надано права на операціїї з розділом
		$content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
	}
}

if ($accss=="0"){
	$content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
}
