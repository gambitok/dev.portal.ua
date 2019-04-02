<?php
$main_page_htm=RD."/tpl/main_page.htm";$main_page="";if (file_exists("$main_page_htm")){ $main_page = file_get_contents($main_page_htm);}
$content=str_replace("{work_window}", $main_page, $content);

if ($w==""){
	include_once 'lib/stat_class.php';$stat=new stats;
	$content=str_replace("{today}", date("Y-m-d"), $content);
	$content=str_replace("{summ_sale_today}", "0.00 грн", $content);
	
	$amount_sale_invoce=$amount_dp_inwork=$amount_site_orders=$amount_back_clients=0;
	$summ_sale_invoce=$summ_dp_inwork=$summ_site_orders=$summ_back_clients="0.00 грн";
	list($amount_sale_invoce,$summ_sale_invoce,$amount_dp_inwork,$summ_dp_inwork,$amount_site_orders,$summ_site_orders,$amount_back_clients,$summ_back_clients)=$stat->getCeoStat1();
	
	$content=str_replace("{amount_sale_invoce}", $amount_sale_invoce, $content);
	$content=str_replace("{amount_dp_inwork}", $amount_dp_inwork, $content);
	$content=str_replace("{amount_site_orders}", $amount_site_orders, $content);
	$content=str_replace("{amount_back_clients}", $amount_back_clients, $content);

	$content=str_replace("{summ_sale_invoce}", $summ_sale_invoce, $content);
	$content=str_replace("{summ_dp_inwork}", $summ_dp_inwork, $content);
	$content=str_replace("{summ_site_orders}", $summ_site_orders, $content);
	$content=str_replace("{summ_back_clients}", $summ_back_clients, $content);

/*	$kol_pacient_journal_cur_month=0;$kol_pacient_journal_prev_month=0;$pacient_journal_color="green";$pacient_journal_asc="asc";
	list($kol_pacient_journal_cur_month,$kol_pacient_journal_prev_month,$pacient_journal_color,$pacient_journal_asc)=$stat->getStatPacientJournalInfo();
	
	$content=str_replace("{kol_pacient_journal_cur_month}", $kol_pacient_journal_cur_month, $content);
	$content=str_replace("{kol_pacient_journal_prev_month}", $kol_pacient_journal_prev_month, $content);
	$content=str_replace("{pacient_journal_color}", $pacient_journal_color, $content);
	$content=str_replace("{pacient_journal_asc}", $pacient_journal_asc, $content);
	
	
	$kol_pacient_cur_month=$kol_pacient_diagnostic_cur_month+$kol_pacient_journal_cur_month;
	$kol_pacient_prev_month=$kol_pacient_diagnostic_prev_month+$kol_pacient_journal_prev_month;$pacient_color="green";$pacient_asc="asc";
	if ($kol_pacient_cur_month<$kol_pacient_prev_month){$pacient_color="red";$pacient_asc="desc";}
	
	$content=str_replace("{kol_pacient_cur_month}", $kol_pacient_cur_month, $content);
	$content=str_replace("{kol_pacient_prev_month}", $kol_pacient_prev_month, $content);
	$content=str_replace("{pacient_color}", $pacient_color, $content);
	$content=str_replace("{pacient_asc}", $pacient_asc, $content);

	list($docs_last5_list,$bids_last5_list,$bids_last5_today)=$stat->getDocsLast5List();
	$content=str_replace("{docs_last5_list}", $docs_last5_list, $content);
	$content=str_replace("{bids_last5_list}", $bids_last5_list, $content);
	$content=str_replace("{bids_last5_today}", $bids_last5_today, $content);
	$content=str_replace("{cur_date}", date("Y-m-d"), $content);
	
	$content=str_replace("{servisant_bids_list}", $stat->getServisantBidsList(), $content);

	list($pacient_diagnostic_summ_lat_week,$pacient_diagnostic_summ_cur_month,$pacient_diagnostic_summ_prev_month,$cur_month,$prev_month)=$stat->getBidsSummList();
	$content=str_replace("{pacient_diagnostic_summ_lat_week}", $pacient_diagnostic_summ_lat_week, $content);
	$content=str_replace("{pacient_diagnostic_summ_cur_month}", $pacient_diagnostic_summ_cur_month, $content);
	$content=str_replace("{pacient_diagnostic_summ_prev_month}", $pacient_diagnostic_summ_prev_month, $content);
	$content=str_replace("{cur_month}", $cur_month, $content);
	$content=str_replace("{prev_month}", $prev_month, $content);

	list($list_days_cart,$kols_list)=$stat->getPacientChartInfo();
	$content=str_replace("[LIST_DAYS_CART]", $list_days_cart, $content);
	$content=str_replace("[kols_LIST]", $kols_list, $content);
	
	list($cytology_max_kol,$cytology_rd)=$stat->getPacientCytologyPie();
	$content=str_replace("[cytology_max_kol]", $cytology_max_kol, $content);
	$content=str_replace("[cytology_rd]", $cytology_rd, $content);
	
	list($list_days_cart,$kols_list)=$stat->getPacientJournalChartInfo();
	$content=str_replace("[LIST_DAYS_CART_JOURNAL]", $list_days_cart, $content);
	$content=str_replace("[kols_LIST_JOURNAL]", $kols_list, $content);
	
	*/
}
?>