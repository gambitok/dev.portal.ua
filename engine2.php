<?php 

require_once RD.'/lib/brands_class.php'; $brands=new brands;
require_once RD.'/lib/country_class.php'; $country=new country;
require_once RD.'/lib/tpoint_class.php';$tpoint=new tpoint;
require_once RD.'/lib/jmoving_class.php';$jmoving=new jmoving;
require_once RD.'/lib/storsel_class.php';$storsel=new storsel;
require_once RD.'/lib/storage_class.php';$storage=new storage;
require_once RD.'/lib/clients_class.php';$cl=new clients;
require_once RD.'/lib/users_class.php';$users=new users;
require_once RD.'/lib/sale_invoice_class.php';$sale_invoice=new sale_invoice;
require_once RD.'/lib/buh_invoice_class.php';$buh_invoice=new buh_invoice;
require_once RD.'/lib/buh_back_class.php';$buh_back=new buh_back;
require_once RD.'/lib/settings_new_class.php';$settings_new=new SettingsNewClass;
require_once RD.'/lib/catalogue_class.php';$catalogue=new catalogue;
require_once RD.'/lib/dp_class.php';$dp=new dp;
require_once RD.'/lib/income_class.php';$income=new income;
require_once RD.'/lib/back_clients_class.php';$back_clients=new back_clients;
require_once RD.'/lib/cash_reports_class.php';$cash_reports=new cash_reports;
require_once RD.'/lib/jpay_class.php';$jpay=new jpay;
require_once RD.'/lib/unknown_numbers_class.php';$unknown_numbers=new unknown_numbers;
require_once RD.'/lib/seo_reports_class.php';$seo_reports=new seo_reports;
require_once RD.'/lib/report_clients_class.php';$report_clients=new report_clients;
require_once RD.'/lib/report_overdraft_class.php';$report_overdraft=new report_overdraft;
require_once RD.'/lib/report_sales_class.php';$report_sales=new report_sales;
require_once RD.'/lib/group_tree_class.php';$group_tree=new group_tree;

// ---- REPORT CLIENTS ---- //

if ($_REQUEST["w"]=="showReportClients"){ $GLOBALS['_RESULT'] = array("content"=>$report_clients->showReportClients($_REQUEST["date_start"],$_REQUEST["date_end"],$_REQUEST["clients"],$_REQUEST["cash_id"],$_REQUEST["tpoint_id_report"]));}

// ---- UNKNOWN NUMBERS ---- //

if ($_REQUEST["w"]=="showNumbersList"){ $GLOBALS['_RESULT'] = array("content"=>$unknown_numbers->showNumbersList($_REQUEST["suppl_id"]));}

// ---- BUH INVOICES ---- //

if ($_REQUEST["w"]=="filterBuhInvoiceList"){ $GLOBALS['_RESULT'] = array("content"=>$buh_invoice->show_sale_invoice_list_filter($_REQUEST["date_start"],$_REQUEST["date_end"]));}

// ---- JPAY ---- //

if ($_REQUEST["w"]=="filterJPayList"){ $GLOBALS['_RESULT'] = array("content"=>$jpay->filterJPayList($_REQUEST["date_start"],$_REQUEST["date_end"],$_REQUEST["doc_type"],$_REQUEST["jpay_name"]));}

// ---- CASH REPORTS ---- //

if ($_REQUEST["w"]=="showCashReportsList"){ $GLOBALS['_RESULT'] = array("content"=>$cash_reports->showCashReportsList($_REQUEST["date_start"],$_REQUEST["date_end"],$_REQUEST["payboxes"],$_REQUEST["cash_id"]));}

// ---- INCOME ---- //

if ($_REQUEST["w"]=="checkArticleZed"){ $GLOBALS['_RESULT'] = array("content"=>$income->checkArticleZed($_REQUEST["art_id"]));}

if ($_REQUEST["w"]=="saveArticleZed"){ list($answer,$err)=$income->saveArticleZed($_REQUEST["art_id"],$_REQUEST["costums_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="showIncomeStrList"){ $GLOBALS['_RESULT'] = array("content"=>$income->showIncomeStrList($_REQUEST["income_id"],$_REQUEST["type_id"],$_REQUEST["oper_status"]));}

if ($_REQUEST["w"]=="filterIncomeList"){ $GLOBALS['_RESULT'] = array("content"=>$income->show_income_list_filter($_REQUEST["date_start"],$_REQUEST["date_end"]));}

// ---- BUHBACK ---- //

if ($_REQUEST["w"]=="filterBuhBackClientsList"){ $GLOBALS['_RESULT'] = array("content"=>$buh_back->show_back_clients_list_filter($_REQUEST["date_start"],$_REQUEST["date_end"]));}

// ---- DP ---- //

if ($_REQUEST["w"]=="getDpTpointInfo"){ $GLOBALS['_RESULT'] = array("content"=>$dp->getDpTpointInfo($_REQUEST["client_id"]));}

if ($_REQUEST["w"]=="deleteOrderSite"){ list($answer,$err)=$dp->deleteOrderSite($_REQUEST["order_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
	
if ($_REQUEST["w"]=="showOrderSiteRange"){ $GLOBALS['_RESULT'] = array("content"=>$dp->showOrderSiteRange($_REQUEST["status"],$_REQUEST["data_start"],$_REQUEST["data_end"]));}

if ($_REQUEST["w"]=="show_dp_search_filter"){ $GLOBALS['_RESULT'] = array("content"=>$dp->show_dp_list_filter($_REQUEST["status"],$_REQUEST["filStatus"],$_REQUEST["filAuthor"],$_REQUEST["filTpoint"]));}

// ---- New Settings ---- //

// language
if ($_REQUEST["w"]=="loadLanguageList"){ $GLOBALS['_RESULT'] = array("content"=>$settings_new->loadLanguageList());}

if ($_REQUEST["w"]=="showLanguageCard"){ $GLOBALS['_RESULT'] = array("content"=>$settings_new->showLanguageCard($_REQUEST["id"]));}

if ($_REQUEST["w"]=="newLanguageCard"){ $GLOBALS['_RESULT'] = array("id"=>$settings_new->newLanguageCard($_REQUEST["lang_var"]));}

if ($_REQUEST["w"]=="saveLanguage"){ list($answer,$err)=$settings_new->saveLanguage($_REQUEST["lang_id"],$_REQUEST["lang_ru"],$_REQUEST["lang_ua"],$_REQUEST["lang_eng"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="dropLanguage"){ list($answer,$err)=$settings_new->dropLanguage($_REQUEST["id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

// contacts
if ($_REQUEST["w"]=="loadContactsList"){$GLOBALS['_RESULT'] = array("content"=>$settings_new->loadContactsList());}

if ($_REQUEST["w"]=="showContactsCard"){ $GLOBALS['_RESULT'] = array("content"=>$settings_new->showContactsCard($_REQUEST["id"]));}

if ($_REQUEST["w"]=="newContactsCard"){ $GLOBALS['_RESULT'] = array("id"=>$settings_new->newContactsCard($_REQUEST["lang"]));}

if ($_REQUEST["w"]=="saveContacts"){ list($answer,$err)=$settings_new->saveContacts($_REQUEST["id"],$_REQUEST["title"],$_REQUEST["address"],$_REQUEST["schedule"],$_REQUEST["phone"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="dropContacts"){ list($answer,$err)=$settings_new->dropContacts($_REQUEST["id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

// contacts bottom
if ($_REQUEST["w"]=="loadContactsBotList"){ $GLOBALS['_RESULT'] = array("content"=>$settings_new->loadContactsBotList());}

if ($_REQUEST["w"]=="showContactsBotCard"){ $GLOBALS['_RESULT'] = array("content"=>$settings_new->showContactsBotCard($_REQUEST["id"]));}

if ($_REQUEST["w"]=="newContactsBotCard"){ $GLOBALS['_RESULT'] = array("id"=>$settings_new->newContactsBotCard());}

if ($_REQUEST["w"]=="saveContactsBot"){ list($answer,$err)=$settings_new->saveContactsBot($_REQUEST["id"],$_REQUEST["text"],$_REQUEST["icon"],$_REQUEST["link"],$_REQUEST["status"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="dropContactsBot"){ list($answer,$err)=$settings_new->dropContactsBot($_REQUEST["id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

// news
if ($_REQUEST["w"]=="loadNewsList"){ $GLOBALS['_RESULT'] = array("content"=>$settings_new->loadNewsList());}

if ($_REQUEST["w"]=="showNewsCard"){ $GLOBALS['_RESULT'] = array("content"=>$settings_new->showNewsCard($_REQUEST["id"]));}

if ($_REQUEST["w"]=="newNewsCard"){ $GLOBALS['_RESULT'] = array("id"=>$settings_new->newNewsCard($_REQUEST["lang"]));}

if ($_REQUEST["w"]=="saveNews"){ list($answer,$err)=$settings_new->saveNews($_REQUEST["id"],$_REQUEST["caption"],$_REQUEST["data"],$_REQUEST["short"],$_REQUEST["descr"],$_REQUEST["status"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="dropNews"){ list($answer,$err)=$settings_new->dropNews($_REQUEST["id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="loadNewsPhoto"){ $GLOBALS['_RESULT'] = array("content"=>$settings_new->loadNewsPhoto($_REQUEST["news_id"],$_REQUEST["lang_id"]));}

if ($_REQUEST["w"]=="deleteNewsLogo"){list($answer,$err)=$settings_new->deleteNewsLogo($_REQUEST["news_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

// ---- USERS ---- //

if ($_REQUEST["w"]=="newTrustedIPCard")  { $GLOBALS['_RESULT'] = array("trusted_id"=>$users->newTrustedIPCard());}

if ($_REQUEST["w"]=="showTrustedIPList") { $GLOBALS['_RESULT'] = array("content"=>$users->showTrustedIPList());}

if ($_REQUEST["w"]=="showTrustedIPCard") { $GLOBALS['_RESULT'] = array("content"=>$users->showTrustedIPCard($_REQUEST["trusted_id"]));}

if ($_REQUEST["w"]=="saveTrustedIPGeneralInfo"){ list($answer,$err)=$users->saveTrustedIPGeneralInfo($_REQUEST["trusted_id"],$_REQUEST["trusted_ip"],$_REQUEST["trusted_descr"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="dropTrustedIP"){ list($answer,$err)=$users->dropTrustedIP($_REQUEST["trusted_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="loadUsersAccessTime"){ $GLOBALS['_RESULT'] = array("content"=>$users->loadUsersAccessTime($_REQUEST["users_id"]));}

if ($_REQUEST["w"]=="saveUsersAccessTime"){ list($answer,$err)=$users->saveUsersAccessTime($_REQUEST["users_id"],$_REQUEST["access"],$_REQUEST["access_time"],$_REQUEST["time_from"],$_REQUEST["time_to"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

// ---- STORSEL ---- //

if ($_REQUEST["w"]=="updateStorselStatus"){ list($answer,$err)=$storsel->updateStorselStatus($_REQUEST["select_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err); }

if ($_REQUEST["w"]=="calculateStorselParams"){ $GLOBALS['_RESULT'] = array("content"=>$storsel->calculateStorselParams($_REQUEST["select_id"]));}

// ---- BRANDS ---- //

if ($_REQUEST["w"]=="showBrandsList"){ $GLOBALS['_RESULT'] = array("content"=>$brands->show_brands_list());}

if ($_REQUEST["w"]=="newBrandsCard"){ $GLOBALS['_RESULT'] = array("brands_id"=>$brands->newBrandsCard());}

if ($_REQUEST["w"]=="showBrandsCard"){ $GLOBALS['_RESULT'] = array("content"=>$brands->showBrandsCard($_REQUEST["brands_id"]));}

if ($_REQUEST["w"]=="ImportBrands"){ $GLOBALS['_RESULT'] = array("content"=>ImportBrands());}

if ($_REQUEST["w"]=="finishBrandsIndexImport"){ list($answer,$err)=finishBrandsIndexImport($_REQUEST["start_row"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err); }

if ($_REQUEST["w"]=="saveBrandsGeneralInfo") {
	list($answer,$err)=$brands->saveBrandsGeneralInfo(
		$_REQUEST["brands_id"],
		$_REQUEST["brands_name"],
		$_REQUEST["brands_type"],
		$_REQUEST["brands_kind"],
		$_REQUEST["brands_country"],
		$_REQUEST["brands_visible"]);
    $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);
}

if ($_REQUEST["w"]=="loadBrandsDetails"){ $GLOBALS['_RESULT'] = array("content"=>$brands->loadBrandsDetails($_REQUEST["brands_id"]));}

if ($_REQUEST["w"]=="saveBrandsDetails") {
	list($answer,$err)=$brands->saveBrandsDetails(
		$_REQUEST["brands_id"],
		$_REQUEST["descr"],
		$_REQUEST["link"]);
    $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);
}

if ($_REQUEST["w"]=="loadBrandsPhoto"){ $GLOBALS['_RESULT'] = array("content"=>$brands->loadBrandsPhoto($_REQUEST["brands_id"]));}

if ($_REQUEST["w"]=="deleteBrandsLogo"){ list($answer,$err)=$brands->deleteBrandsLogo($_REQUEST["brands_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

// ---- COUNTRIES ---- //

if ($_REQUEST["w"]=="showCountryList"){ $GLOBALS['_RESULT'] = array("content"=>$country->show_country_list());}

if ($_REQUEST["w"]=="newCountryCard"){ $GLOBALS['_RESULT'] = array("country_id"=>$country->newCountryCard());}

if ($_REQUEST["w"]=="showCountryCard"){ $GLOBALS['_RESULT'] = array("content"=>$country->showCountryCard($_REQUEST["country_id"]));}

if ($_REQUEST["w"]=="saveCountryGeneralInfo") {
	list($answer,$err)=$country->saveCountryGeneralInfo(
		$_REQUEST["country_id"],
		$_REQUEST["country_name"],
		$_REQUEST["country_alfa2"],
		$_REQUEST["country_alfa3"],
		$_REQUEST["country_duty"],
		$_REQUEST["country_risk"]);
    $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);
}

if ($_REQUEST["w"]=="DeleteCountry") { list($answer,$err)=$country->DeleteCountry($_REQUEST["country_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

// ---- TPOINT ---- //

if ($_REQUEST["w"]=="cutJmovingStorageAll"){ list($answer,$err,$ids)=$jmoving->cutJmovingStorageAll($_REQUEST["jmoving_id"],$_REQUEST["select_id"],$_REQUEST['comment']); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err,"ids"=>$ids);}

if ($_REQUEST["w"]=="separateJmovingByDefect"){ list($answer,$err)=$jmoving->separateJmovingByDefect($_REQUEST["jmoving_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="checkJmovingBugs"){ $GLOBALS['_RESULT'] = array("content"=>$jmoving->checkJmovingBugs($_REQUEST["jmoving_id"]));}

if ($_REQUEST["w"]=="deleteTpoint"){ list($answer,$err)=$tpoint->deleteTpoint($_REQUEST["tpoint_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="deleteStorage"){ list($answer,$err)=$storage->deleteStorage($_REQUEST["storage_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

// ---- CLIENTS ---- //

if ($_REQUEST["w"]=="checkEmptyClients"){ $GLOBALS['_RESULT'] = array("content"=>$cl->checkEmptyClients());}

if ($_REQUEST["w"]=="showClientConditionsHistory"){ $GLOBALS['_RESULT'] = array("content"=>$cl->showClientConditionsHistory($_REQUEST["client_id"]));}

if ($_REQUEST["w"]=="loadClientDocuments"){ $GLOBALS['_RESULT'] = array("content"=>$cl->loadClientDocuments($_REQUEST["client_id"]));}

if ($_REQUEST["w"]=="loadClientSupplMandate"){ $GLOBALS['_RESULT'] = array("content"=>$cl->loadClientMandate($_REQUEST["client_id"]));}

if ($_REQUEST["w"]=="loadClientSupplBasis"){ $GLOBALS['_RESULT'] = array("content"=>$cl->loadClientBasis($_REQUEST["client_id"]));}

if ($_REQUEST["w"]=="showClientMandateForm"){ list($content,$header)=$cl->showClientMandateForm($_REQUEST["client_id"],$_REQUEST["mandate_id"]); $GLOBALS['_RESULT'] = array("content"=>$content, "header"=>$header);}

if ($_REQUEST["w"]=="saveClientMandateForm"){ list($answer,$err)=$cl->saveClientMandateForm($_REQUEST["client_id"],$_REQUEST["mandate_id"],$_REQUEST["number"],$_REQUEST["seria"],$_REQUEST["receiver"],$_REQUEST["data_from"],$_REQUEST["data_to"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="dropClientMandate"){ list($answer,$err)=$cl->dropClientMandate($_REQUEST["client_id"],$_REQUEST["mandate_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="showClientBasisForm"){ list($content,$header)=$cl->showClientBasisForm($_REQUEST["client_id"],$_REQUEST["basis_id"]); $GLOBALS['_RESULT'] = array("content"=>$content, "header"=>$header);}

if ($_REQUEST["w"]=="saveClientBasisForm"){ list($answer,$err)=$cl->saveClientBasisForm($_REQUEST["client_id"],$_REQUEST["basis_id"],$_REQUEST["number"],$_REQUEST["data_from"],$_REQUEST["data_to"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="dropClientBasis"){ list($answer,$err)=$cl->dropClientBasis($_REQUEST["client_id"],$_REQUEST["basis_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="showClientRetailList"){ $GLOBALS['_RESULT'] = array("content"=>$cl->show_clients_retail_list($_REQUEST["status"]));}

if ($_REQUEST["w"]=="showClientRetailCard"){ $GLOBALS['_RESULT'] = array("content"=>$cl->showClientRetailCard($_REQUEST["user_id"]));}
 
if ($_REQUEST["w"]=="newClientRetailCard"){ $GLOBALS['_RESULT'] = array("user_id"=>$cl->newClientRetailCard());}

if ($_REQUEST["w"]=="saveClientRetailGeneralInfo"){ list($answer,$err)=$cl->saveClientRetailGeneralInfo($_REQUEST["user_id"],$_REQUEST["user_name"],$_REQUEST["country_id"],$_REQUEST["state_id"],$_REQUEST["region_id"],$_REQUEST["city_id"],$_REQUEST["user_category"],$_REQUEST["user_phone"],$_REQUEST["user_email"],$_REQUEST["user_status"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="moveClientsRetail"){ list($answer,$err)=$cl->moveClientsRetail($_REQUEST["user_id"],$_REQUEST["client_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

// ---- SALE INVOICE ---- //

if ($_REQUEST["w"]=="filterInvoiceList"){ $GLOBALS['_RESULT'] = array("content"=>$sale_invoice->show_sale_invoice_list_filter($_REQUEST["date_start"],$_REQUEST["date_end"]));}

if ($_REQUEST["w"]=="exportSaleInvoceExcel")  { $GLOBALS['_RESULT'] = array("content"=>$sale_invoice->exportSaleInvoiceExcel($_REQUEST["invoice_id"]));}

if ($_REQUEST["w"]=="getPartitionsInvoiceAmount"){ $GLOBALS['_RESULT'] = array("content"=>$sale_invoice->getPartitionsInvoiceAmount($_REQUEST["partition_id"]));}

if ($_REQUEST["w"]=="savePartitionsInvoiceAmount"){ $GLOBALS['_RESULT'] = array("content"=>$sale_invoice->savePartitionsInvoiceAmount($_REQUEST["partition_id"],$_REQUEST["invoice_amount"]));}

// ---- CATALOGUE ---- //

if ($_REQUEST["w"]=="showArticleLogs"){ $GLOBALS['_RESULT'] = array("content"=>$catalogue->showArticleLogs($_REQUEST["art_id"]));}

if ($_REQUEST["w"]=="getMaxIndex"){ $GLOBALS['_RESULT'] = array("content"=>$catalogue->getMaxIndex());}

if ($_REQUEST["w"]=="getMaxSupplIndex"){ $GLOBALS['_RESULT'] = array("content"=>$catalogue->getMaxSupplIndex());}

if ($_REQUEST["w"]=="saveIndexArticle"){ $GLOBALS['_RESULT'] = array("content"=>$catalogue->saveIndexArticle($_REQUEST["art_id"],$_REQUEST["article_nr_displ"],$_REQUEST["brand_id"],$_REQUEST["article_name"],$_REQUEST["article_name_ukr"],$_REQUEST["article_info"]));}

if ($_REQUEST["w"]=="showArticleJDocs"){ list($content,$header)=$catalogue->showArticleJDocs($_REQUEST["art_id"]); $GLOBALS['_RESULT'] = array("content"=>$content, "header"=>$header);}

if ($_REQUEST["w"]=="loadArticleInfo"){ $GLOBALS['_RESULT'] = array("content"=>$catalogue->loadArticleInfo($_REQUEST["art_id"]));}

if ($_REQUEST["w"]=="saveArticleInfo"){ list($answer,$err)=$catalogue->saveArticleInfo($_REQUEST["art_id"],$_REQUEST["lang_id"],$_REQUEST["text"],$_REQUEST["value"],$_REQUEST["sort"],$_REQUEST["new_sort"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="dropArticleInfo"){ list($answer,$err)=$catalogue->dropArticleInfo($_REQUEST["art_id"],$_REQUEST["lang_id"],$_REQUEST["sort"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="addArticleInfo"){ list($answer,$err)=$catalogue->addArticleInfo($_REQUEST["art_id"],$_REQUEST["lang_id"],$_REQUEST["text"],$_REQUEST["value"],$_REQUEST["sort"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="loadArticleShortInfo"){ $GLOBALS['_RESULT'] = array("content"=>$catalogue->loadArticleShortInfo($_REQUEST["art_id"]));}

if ($_REQUEST["w"]=="showArticleCross"){ $GLOBALS['_RESULT'] = array("content"=>$catalogue->showArticleCross($_REQUEST["art_id"]));}

if ($_REQUEST["w"]=="saveArticleCross"){ list($answer,$err)=$catalogue->saveArticleCross($_REQUEST["cross"],$_REQUEST["new_cross"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="saveArticleShortInfo"){ list($answer,$err)=$catalogue->saveArticleShortInfo($_REQUEST["art_id"],$_REQUEST["lang_id"],$_REQUEST["text"],$_REQUEST["value"],$_REQUEST["sort"],$_REQUEST["new_sort"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="dropArticleShortInfo"){ list($answer,$err)=$catalogue->dropArticleShortInfo($_REQUEST["art_id"],$_REQUEST["lang_id"],$_REQUEST["sort"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="addArticleShortInfo"){ list($answer,$err)=$catalogue->addArticleShortInfo($_REQUEST["art_id"],$_REQUEST["lang_id"],$_REQUEST["text"],$_REQUEST["value"],$_REQUEST["sort"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

// ---- BACK CLIENTS ---- //

if ($_REQUEST["w"]=="showStorageCellsList"){ $GLOBALS['_RESULT'] = array("content"=>$back_clients->showStorageCellsList($_REQUEST["storage_id"]));}

if ($_REQUEST["w"]=="setBackClientsStorageCell"){ $GLOBALS['_RESULT'] = array("content"=>$back_clients->setBackClientsStorageCell($_REQUEST["back_id"],$_REQUEST["cell_id"]));}

if ($_REQUEST["w"]=="loadBackClientsPartition"){ $GLOBALS['_RESULT'] = array("content"=>$back_clients->loadBackClientsPartition($_REQUEST["back_id"]));}

if ($_REQUEST["w"]=="filterBackClientsList"){ $GLOBALS['_RESULT'] = array("content"=>$back_clients->show_back_clients_list_filter($_REQUEST["date_start"],$_REQUEST["date_end"]));}

// ---- SUPPL ---- //

if ($_REQUEST["w"]=="loadSupplCoopList"){ $GLOBALS['_RESULT'] = array("content"=>$suppl->showSupplCoopList());}

if ($_REQUEST["w"]=="saveSupplCoop"){ list($answer,$err)=$suppl->saveSupplCoop($_REQUEST["suppl_id"],$_REQUEST["company"],$_REQUEST["name"],$_REQUEST["phone"],$_REQUEST["email"],$_REQUEST["city_id"],$_REQUEST["comment"],$_REQUEST["status"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="showSupplCoopCard"){ $GLOBALS['_RESULT'] = array("content"=>$suppl->showSupplCoopCard($_REQUEST["suppl_id"]));}

// ---- Report Overdraft ---- //

if ($_REQUEST["w"]=="filterReportOverdraftList"){ $GLOBALS['_RESULT'] = array("content"=>$report_overdraft->showReportOverdraftList($_REQUEST["data"],$_REQUEST["client_id_cur"],$_REQUEST["tpoint_id_cur"]));}

if ($_REQUEST["w"]=="getClientOverdraftList"){ $GLOBALS['_RESULT'] = array("content"=>$report_overdraft->getClientOverdraftList($_REQUEST["data"],$_REQUEST["tpoint_id"]));}

if ($_REQUEST["w"]=="showDocsProlongationForm"){ $GLOBALS['_RESULT'] = array("content"=>$report_overdraft->showDocsProlongationForm($_REQUEST["client_id"],$_REQUEST["invoice_id"]));}

// ---- Seo Reports ---- //

if ($_REQUEST["w"]=="showSeoReports"){ $GLOBALS['_RESULT'] = array("content"=>$seo_reports->showSeoReports($_REQUEST["date_start"],$_REQUEST["date_end"],$_REQUEST["managers"],$_REQUEST["cash_id"],$_REQUEST["client_status"]));}

if ($_REQUEST["w"]=="getSummUser"){ $GLOBALS['_RESULT'] = array("content"=>$seo_reports->getSummUser($_REQUEST["user_id"],$_REQUEST["date_start"],$_REQUEST["date_end"],$_REQUEST["cash_id"]));}

// ---- Report Sales ---- //

if ($_REQUEST["w"]=="showReportSales"){ $GLOBALS['_RESULT'] = array("content"=>$report_sales->showReportSales($_REQUEST["date_start"],$_REQUEST["tpoint"]));}

// ---- Group Tree ---- //

if ($_REQUEST["w"]=="showGroupTreeCard"){ $GLOBALS['_RESULT'] = array("content"=>$group_tree->showGroupTreeCard($_REQUEST["str_id"]));}

if ($_REQUEST["w"]=="showGroupTreeHeaders"){ $GLOBALS['_RESULT'] = array("content"=>$group_tree->showGroupTreeHeaders());}

if ($_REQUEST["w"]=="saveGroupTreeCard"){ list($answer,$err)=$group_tree->saveGroupTreeCard($_REQUEST["str_id"],$_REQUEST["position"],$_REQUEST["disp_text_ru"],$_REQUEST["disp_text_ua"],$_REQUEST["disp_text_en"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="showGroupTreeHead"){ $GLOBALS['_RESULT'] = array("content"=>$group_tree->showGroupTreeHead($_REQUEST["head_id"]));}

if ($_REQUEST["w"]=="saveGroupTreeHead"){ list($answer,$err)=$group_tree->saveGroupTreeHead($_REQUEST["head_id"],$_REQUEST["disp_text_ru"],$_REQUEST["disp_text_ua"],$_REQUEST["disp_text_en"],$_REQUEST["head_status"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="dropGroupTreeHead"){ list($answer,$err)=$group_tree->dropGroupTreeHead($_REQUEST["head_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

//head str
if ($_REQUEST["w"]=="addGroupTreeHeadStr"){ $GLOBALS['_RESULT'] = array("content"=>$group_tree->addGroupTreeHeadStr($_REQUEST["head_id"]));}

if ($_REQUEST["w"]=="showGroupTreeHeadStr"){ $GLOBALS['_RESULT'] = array("content"=>$group_tree->showGroupTreeHeadStr($_REQUEST["group_id"]));}

if ($_REQUEST["w"]=="saveGroupTreeHeadStrCard"){ list($answer,$err)=$group_tree->saveGroupTreeHeadStrCard($_REQUEST["group_id"],$_REQUEST["head_id"],$_REQUEST["str_id"],$_REQUEST["position"],$_REQUEST["category"],$_REQUEST["disp_text_ru"],$_REQUEST["disp_text_ua"],$_REQUEST["disp_text_en"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="dropGroupTreeHeadStr"){ list($answer,$err)=$group_tree->dropGroupTreeHeadStr($_REQUEST["group_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

//category
if ($_REQUEST["w"]=="showGroupTreeHeadCategory"){ $GLOBALS['_RESULT'] = array("content"=>$group_tree->showGroupTreeHeadCategory($_REQUEST["cat_id"]));}

if ($_REQUEST["w"]=="saveGroupTreeHeadCategoryCard"){ list($answer,$err)=$group_tree->saveGroupTreeHeadCategoryCard($_REQUEST["cat_id"],$_REQUEST["head_id"],$_REQUEST["position"],$_REQUEST["disp_text_ru"],$_REQUEST["disp_text_ua"],$_REQUEST["disp_text_en"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="dropGroupTreeHeadCategory"){ list($answer,$err)=$group_tree->dropGroupTreeHeadCategory($_REQUEST["cat_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

//group tree
if ($_REQUEST["w"]=="dropUploadPhotoForm"){ list($answer,$err)=$group_tree->dropUploadPhotoForm($_REQUEST["type_id"],$_REQUEST["group_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="showUploadDropzone"){ $GLOBALS['_RESULT'] = array("content"=>$group_tree->showUploadDropzone($_REQUEST["type_id"],$_REQUEST["group_id"]));}

?>