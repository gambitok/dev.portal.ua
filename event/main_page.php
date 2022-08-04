<?php

$main_page_htm = RD . "/tpl/main_page.htm";
$main_page = "";
if (file_exists("$main_page_htm")) {
    $main_page = file_get_contents($main_page_htm);
}

if ($media_user_id == 31) {
    $main_page = "";
}

$content = str_replace("{work_window}", $main_page, $content);

if ($w == "") {
	include_once "lib/stat_class.php";
	$stat = new stats;
	$content = str_replace("{today}", date("Y-m-d"), $content);
	$content = str_replace("{summ_sale_today}", "0.00 грн", $content);
	
	$amount_sale_invoce = $amount_dp_inwork = $amount_site_orders = $amount_back_clients = 0;
	list($amount_sale_invoce, $summ_sale_invoce, $amount_dp_inwork, $summ_dp_inwork, $amount_site_orders, $summ_site_orders, $amount_back_clients, $summ_back_clients) = $stat->getCeoStat1();
	
	$content = str_replace("{amount_sale_invoce}", $amount_sale_invoce, $content);
	$content = str_replace("{amount_dp_inwork}", $amount_dp_inwork, $content);
	$content = str_replace("{amount_site_orders}", $amount_site_orders, $content);
	$content = str_replace("{amount_back_clients}", $amount_back_clients, $content);

	$content = str_replace("{summ_sale_invoce}", $summ_sale_invoce, $content);
	$content = str_replace("{summ_dp_inwork}", $summ_dp_inwork, $content);
	$content = str_replace("{summ_site_orders}", $summ_site_orders, $content);
	$content = str_replace("{summ_back_clients}", $summ_back_clients, $content);
}
