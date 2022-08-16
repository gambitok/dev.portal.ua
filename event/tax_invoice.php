<?php

$access = new access;
$mf = "tax_invoice";
list($accss, $acc_lvl) = $access->check_user_access($mf);
$alg_u = 0;

if ($accss == "1") {
    require_once (RD . "/lib/tax_invoice_class.php");
	$tax_invoice = new tax_invoice;

    $form = ""; $form_htm = RD . "/tpl/tax_invoice.htm";
	if (file_exists($form_htm)){ $form = file_get_contents($form_htm);}
	$content = str_replace("{work_window}", $form, $content);

	$link = gnLink;
	if (substr($link, -1) == "/") {
	    $link = substr($link, 0, strlen($link) - 1);
	}
	$links = explode("/", $link);
	$w = $links[1];

	if ($w == "") {

        $date_start = $date_end = "";
	    if (empty($_GET["date_start"])) {
            $date_start = date("Y-m-d", strtotime("-1 week"));
        } else {
            $date_start = $_GET["date_start"];
        }
        if (empty($_GET["date_end"])) {
            $date_end   = date("Y-m-d");
        } else {
            $date_end = $_GET["date_end"];
        }

		$range_list = $tax_invoice->show_tax_invoice_list($date_start, $date_end);

		$content = str_replace("{tax_invoice_range}", $range_list, $content);
	}

	if ($w == "exportTIvXML") {
		$tax_id = $links[2];
		$form = $tax_invoice->exportTaxInvoiceXML($tax_id);
	}

	if ($w == "exportTBIvXML") {
		$tax_id = $links[2];
		$form = $tax_invoice->exportTaxBackInvoiceXML($tax_id);
	}

	if ($alg_u == 0) { //не надано права на операціїї з розділом
		$content = str_replace("{work_window}", $access->show_access_deny($mf), $content);
	}
}

if ($accss == "0") {
	$content = str_replace("{work_window}", $access->show_access_deny($mf), $content);
}
