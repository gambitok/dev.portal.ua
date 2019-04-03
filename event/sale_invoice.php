<?php
$access=new access; $mf="sale_invoice";
list($accss,$acc_lvl)=$access->check_user_access($mf);$alg_u=0;

if ($accss=="1"){
    require_once (RD."/lib/sale_invoice_class.php");
	$sale_invoice=new sale_invoice;
	$form_htm=RD."/tpl/sale_invoice.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$content=str_replace("{work_window}", $form, $content);
	$link=gnLink; if (substr($link,-1)=="/"){$link=substr($link,0,strlen($link)-1);} $links=explode("/", $link);$w=$links[1];

	if ($w=="" || $w=="view"){$invoice_id=$links[2];
		$range_list=$sale_invoice->show_sale_invoice_list();
		$content=str_replace("{sale_invoice_range}", $range_list, $content);
		$content=str_replace("{open_invoice_id}", $invoice_id, $content);
	    $content=str_replace("{date_today}", date("Y-m-d"), $content);
	}

	if ($w=="printSlIv"){
		$invoice_id=$links[2];
		$form=$sale_invoice->printSaleInvoice($invoice_id);
	}

	if ($w=="printDpSlIv"){
		$invoice_id=$links[2];
		$form=$dp->printDpSaleInvoice($invoice_id);
	}

	if ($w=="printSlIvBuh"){
		$invoice_id=$links[2];
		$form=$sale_invoice->printSaleInvoiceBuh($invoice_id);
	}

	if ($w=="exportExcelSlIv"){
		$invoice_id=$links[2];
		$form=$sale_invoice->exportSaleInvoiceExcel($invoice_id);
	}

	if ($alg_u==0){
		$content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
	}
}

if ($accss=="0"){
	$content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
}
