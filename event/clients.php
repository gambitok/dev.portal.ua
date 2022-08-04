<?php
error_reporting(E_ERROR);
@ini_set('display_errors', true);
@ini_set('html_errors', false);
ini_set('memory_limit', '2048M');

$access = new access;
$mf = "clients";
list($accss, $acc_lvl) = $access->check_user_access($mf);
$alg_u = 0;

if ($accss == "1") {
    require_once (RD . "/lib/clients_class.php");
    require_once (RD . "/lib/sale_invoice_class.php");
    require_once (RD . "/lib/back_clients_class.php");
    $cl = new clients;
    $form_htm = RD . "/tpl/clients.htm"; $form = "";
    if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
	$content = str_replace("{work_window}", $form, $content);
	$link = gnLink;
	if (substr($link,-1) == "/") {
	    $link = substr($link, 0, strlen($link) - 1);
	}
	$links = explode("/", $link);
	$w = $links[1];

	if ($w == "") {
		$range_list = $cl->show_clients_list();
		$content = str_replace("{clients_range}", $range_list, $content);
		$content = str_replace("{client_category_tree}", $cl->showCategoryTree(), $content);
		$content = str_replace("{state_list}", $cl->loadStateSelectList(0, 0), $content);
		$content = str_replace("{filPhone}", "", $content);
		$content = str_replace("{filEmail}", "", $content);
		$content = str_replace("{filClientName}", "", $content);
		$content = str_replace("{filClientId}", "", $content);
	}

	if ($w == "printCl1") {
		$client_id = $links[2];
		$date_start = $links[3];
		$date_end = $links[4];
		$saldo_articles = $links[5];
		if ($saldo_articles == "true") $saldo_articles = 1;
		if ($saldo_articles == "false") $saldo_articles = 0;
		$form = $cl->printGeneralSaldoList($client_id, $date_start, $date_end, $saldo_articles);
	}

	if ($alg_u == 0) {
	    // не надано права на операціїї з розділом
		$content = str_replace("{work_window}", $access->show_access_deny($mf), $content);
	}
}

if ($accss == "0") {
	$content = str_replace("{work_window}", $access->show_access_deny($mf), $content);
}

