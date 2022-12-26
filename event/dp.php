<?php

$access = new access;
$mf = "catalogue";
list($accss, $acc_lvl) = $access->check_user_access($mf);
$alg_u = 0;
session_start();
$media_user_id = (int)$_SESSION["media_user_id"];

if ($accss == "1") {
    require_once (RD."/lib/dp_class.php"); $dp = new dp;
    require_once (RD."/lib/sale_invoice_class.php");
    require_once (RD."/lib/catalogue_class.php");

	$form_htm = RD . "/tpl/dp.htm"; $form = "";
	if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
	$dp_drop_form = ($media_user_id === 7) ? "<input class='form-control' style='width: 100px; display: inline-block;' id='dp_drop_input'><button class='btn btn-danger' onclick='dropFuckingDp();'>Drop</button>" : "";
	$form = str_replace("{dp_drop_form}", $dp_drop_form, $form);
	$content = str_replace("{work_window}", $form, $content);
	$link = gnLink;
	if (substr($link, -1) == "/") {
	    $link = substr($link, 0, strlen($link) - 1);
	}
	$links = explode("/", $link);
	$w = $links[1];
    $invoice_id = $links[2];
	
	if ($w == "") {
		session_start();
		$ses_user = $_SESSION["media_user_id"];
		$uncontrolUserDiscount = 0;
		if ($ses_user == 1 || $ses_user == 2) {
		    $uncontrolUserDiscount = 1;
		}
		$content = str_replace("{uncontrolUserDiscount}", $uncontrolUserDiscount, $content);
		$range_list = $dp->show_dp_list();
		$content = str_replace("{dp_range}", $range_list, $content);
		$dataOrdersSite = $dp->countOrdersSite();
		$content = str_replace("{kilk_orders}", $dataOrdersSite[0], $content);
		$content = str_replace("{kilk_orders_back}", $dataOrdersSite[1], $content);
        $dataCountUsers = $dp->countUsersSite();
		$content = str_replace("{kilk_users}", $dataCountUsers[0], $content);
		$content = str_replace("{kilk_users_back}", $dataCountUsers[1], $content);
		$content = str_replace("{status_main_list}", $dp->getDpListFilter("status"), $content);
		$content = str_replace("{tpoint_main_list}", $dp->getDpListFilter("tpoint"), $content);
		$content = str_replace("{author_main_list}", $dp->getDpListFilter("user"), $content);
		$content = str_replace("{client_type_main_list}", $dp->getDpListFilter("client_type"), $content);
        $dpPauseData = $dp->getDpPauseAccessStatus($media_user_id);
		if ($dpPauseData == 0) {
            $dp_update_class = "<i class='fa fa-stop-circle'></i> Виключити";
        } else {
            $dp_update_class = "<i class='fa fa-play'></i> Включити";
        }
		$content = str_replace("{update_user_status}", $dpPauseData, $content);
		$content = str_replace("{update_user_status_class}", $dp_update_class, $content);
	}
	
	if ($w == "printSlIv") {
		$form = $dp->printSaleInvoice($invoice_id);
	}
	
	if ($w == "printDpSlIv") {
		$form = $dp->printDpSaleInvoice($invoice_id);
	}
	
	if ($w == "printDpJournal") {
        $type_id = $links[3];
		$form = $dp->printDpJournal($invoice_id, $type_id);
	}

	if ($alg_u == 0) {
		$content = str_replace("{work_window}", $access->show_access_deny($mf), $content);
	}
}

if ($accss == "0") {
	$content = str_replace("{work_window}", $access->show_access_deny($mf), $content);
}
