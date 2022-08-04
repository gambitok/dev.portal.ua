<?php

error_reporting(E_ERROR);
@ini_set('display_errors', true);
@ini_set('html_errors', false);
ini_set('memory_limit', '2048M');

$access = new access;
$mf = "storsel";
list($accss, $acc_lvl) = $access->check_user_access($mf);
$alg_u = 0;

if ($accss == "1") {
    require_once (RD . "/lib/DbSingleton.php");
    require_once (RD . "/lib/sale_invoice_class.php");
	$sale_invoice = new sale_invoice;
    require_once (RD . "/lib/storsel_class.php");
	$storsel = new storsel;
	$form_htm = RD . "/tpl/storsel.htm"; $form = "";
	if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
	$content = str_replace("{work_window}", $form, $content);
	$link = gnLink;
	if (substr($link, -1) == "/") {
	    $link = substr($link, 0, strlen($link) - 1);
	}
	$links = explode("/", $link);
	$w = $links[1];

	if ($w == "") {
		$content = str_replace("{storsel_range}", $storsel->show_storsel_list()[0], $content);
	}

    if ($w == "printBarcode") {
        $select_id = $links[2];
        $db = DbSingleton::getDb();
        $r = $db->query("SELECT `parrent_doc_id` FROM `J_SELECT` WHERE `id` =  $select_id LIMIT 1;");
        $dp_id = $db->result($r, 0, "parrent_doc_id") + 0;
        $content = $sale_invoice->printBarcode($dp_id);
    }

	if ($w == "printStS1"){
	    $select_id = $links[2];
		$form = $storsel->printStorselView($select_id);
	}

	if ($w == "printStS2"){
	    $select_id = $links[2];
		$form = $storsel->printStorselView2($select_id);
	}

	if ($alg_u == 0) { //не надано права на операціїї з розділом
		$content = str_replace("{work_window}", $access->show_access_deny($mf), $content);
	}
}

if ($accss == "0") {
	$content = str_replace("{work_window}", $access->show_access_deny($mf), $content);
}
