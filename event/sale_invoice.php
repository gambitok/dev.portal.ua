<?php

$access = new access;
$mf = "sale_invoice";
list($accss, $acc_lvl) = $access->check_user_access($mf);
$alg_u = 0;

if ($accss == "1") {
    require_once (RD . "/lib/DbSingleton.php");
    require_once (RD . "/lib/sale_invoice_class.php");
	$sale_invoice = new sale_invoice;
	$form_htm = RD . "/tpl/sale_invoice.htm"; $form = "";
	if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
	$content = str_replace("{work_window}", $form, $content);
	$link = gnLink;
	if (substr($link, -1) == "/") {
	    $link = substr($link, 0, strlen($link) - 1);
	}
	$links = explode("/", $link);
	$w = $links[1];

	if ($w == "" || $w == "view") {
	    $invoice_id = $links[2];
		$content = str_replace("{sale_invoice_range}", $sale_invoice->show_sale_invoice_list(), $content);
	    $content = str_replace("{date_today}", date("Y-m-d"), $content);
	}

	if ($w == "printSlIv") {
		$invoice_id = $links[2];
		$type = $links[3];
        $content = $sale_invoice->printSaleInvoice($invoice_id, $type);
	}

	if ($w == "printBarcode") {
        $invoice_id = $links[2];
        $db = DbSingleton::getDb();
        $r = $db->query("SELECT `dp_id` FROM `J_SALE_INVOICE` WHERE `id` =  $invoice_id LIMIT 1;");
        $dp_id = $db->result($r, 0, "dp_id") + 0;
	    $content = $sale_invoice->printBarcode($dp_id);
    }

	if ($w == "printDpSlIv") {
		$invoice_id = $links[2];
        $content = $dp->printDpSaleInvoice($invoice_id);
	}

	if ($w == "printSlIvBuh") {
		$invoice_id = $links[2];
        $content = $sale_invoice->printSaleInvoiceBuh($invoice_id);
	}

	if ($w == "exportExcelSlIv") {
		$invoice_id = $links[2];
		$separator = $links[3];
        $content = $sale_invoice->exportSaleInvoiceExcel($invoice_id, $separator);
	}

	if ($alg_u == 0) {
		$content = str_replace("{work_window}", $access->show_access_deny($mf), $content);
	}
}

if ($accss == "0") {
	$content = str_replace("{work_window}", $access->show_access_deny($mf), $content);
}
