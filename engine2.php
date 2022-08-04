<?php
date_default_timezone_set("Europe/Kiev");
error_reporting(E_ERROR);
ini_set('memory_limit', '1024M');
error_reporting(E_ERROR);
@ini_set('display_errors', true);
@ini_set('html_errors', false);

require_once RD.'/lib/brands_class.php'; $brands=new brands;
require_once RD.'/lib/tpoint_class.php';$tpoint=new tpoint;
require_once RD.'/lib/suppl_class.php';$suppl=new suppl;
require_once RD.'/lib/jmoving_class.php';$jmoving=new jmoving;
require_once RD.'/lib/storsel_class.php';$storsel=new storsel;
require_once RD.'/lib/storage_class.php';$storage=new storage;
require_once RD.'/lib/clients_class.php';$cl=new clients;
require_once RD.'/lib/users_class.php';$users=new users;
require_once RD.'/lib/sale_invoice_class.php';$sale_invoice=new sale_invoice;
require_once RD.'/lib/buh_invoice_class.php';$buh_invoice=new buh_invoice;
require_once RD.'/lib/settings_new_class.php';$settings_new=new SettingsNewClass;
require_once RD.'/lib/catalogue_class.php';$catalogue=new catalogue;
require_once RD.'/lib/dp_class.php';$dp=new dp;
require_once RD.'/lib/income_class.php';$income=new income;
require_once RD.'/lib/back_clients_class.php';$back_clients=new back_clients;
require_once RD.'/lib/paybox_class.php';$paybox=new paybox;
require_once RD.'/lib/jpay_class.php';$jpay=new jpay;
require_once RD.'/lib/seo_reports_class.php';$seo_reports=new seo_reports;
require_once RD.'/lib/report_clients_class.php';$report_clients=new report_clients;
require_once RD.'/lib/report_overdraft_class.php';$report_overdraft=new report_overdraft;
require_once RD.'/lib/report_margin_class.php';$report_margin=new report_margin;
require_once RD.'/lib/group_tree_class.php';$group_tree=new group_tree;
require_once RD.'/lib/claim_class.php';$claim=new claim;
require_once RD.'/lib/write_off_class.php';$write_off=new write_off;
require_once RD.'/lib/action_clients_class.php';$action_clients=new action_clients;
require_once RD.'/lib/auto_class.php';$auto=new auto_class;
require_once RD.'/lib/smartkidbelt_class.php';$smart=new smartkidbelt;
require_once RD.'/lib/configs_class.php';$configs=new configs;
require_once RD.'/lib/catalog_parts_class.php';$catalog_parts=new CatalogParts;
require_once (RD . "/checkbox-in-ua-php-sdk/vendor/autoload.php");

if ($_REQUEST["w"]=="loadTopNavigation"){ $GLOBALS['_RESULT'] = array("content"=>$users->loadTopNavigation());}

/*smartkidbelt*/

if ($_REQUEST["w"]=="showBrandCard"){ $GLOBALS['_RESULT'] = array("content"=>$smart->showBrandCard($_REQUEST["brand_id"]));}

if ($_REQUEST["w"]=="getSmartBrandImage"){ $GLOBALS['_RESULT'] = array("content"=>$smart->getSmartBrandImage($_REQUEST["brand_id"]));}
if ($_REQUEST["w"]=="deleteSmartPhoto"){ list($answer,$err)=$smart->deleteSmartPhoto($_REQUEST["brand_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="saveBrandCard"){ list($answer,$err,$brand_id)=$smart->saveBrandCard($_REQUEST["brand_id"], $_REQUEST["brand_name"], $_REQUEST["brand_text"], $_REQUEST["brand_pos"], $_REQUEST["brand_status"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err, "brand_id"=>$brand_id);}

if ($_REQUEST["w"]=="showStoreCard"){ $GLOBALS['_RESULT'] = array("content"=>$smart->showStoreCard($_REQUEST["store_id"], $_REQUEST["brand_id"]));}
if ($_REQUEST["w"]=="saveStoreCard"){ list($answer,$err)=$smart->saveStoreCard($_REQUEST["store_id"], $_REQUEST["brand_id"], $_REQUEST["address"], $_REQUEST["store_pos"], $_REQUEST["store_status"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="showNavCard"){ $GLOBALS['_RESULT'] = array("content"=>$smart->showNavCard($_REQUEST["nav_id"]));}
if ($_REQUEST["w"]=="saveNavCard"){ list($answer,$err)=$smart->saveNavCard($_REQUEST["nav_id"], $_REQUEST["nav_text"], $_REQUEST["nav_text_ru"], $_REQUEST["nav_link"], $_REQUEST["nav_pos"], $_REQUEST["nav_status"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="showFaqCard"){ $GLOBALS['_RESULT'] = array("content"=>$smart->showFaqCard($_REQUEST["faq_id"]));}
if ($_REQUEST["w"]=="saveFaqCard"){ list($answer,$err)=$smart->saveFaqCard($_REQUEST["faq_id"], $_REQUEST["faq_question"], $_REQUEST["faq_answer"], $_REQUEST["faq_question_ru"], $_REQUEST["faq_answer_ru"], $_REQUEST["faq_pos"], $_REQUEST["faq_status"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="showSmartNewsCard"){ $GLOBALS['_RESULT'] = array("content"=>$smart->showSmartNewsCard($_REQUEST["news_id"]));}
if ($_REQUEST["w"]=="saveSmartNewsCard"){ list($answer,$err)=$smart->saveSmartNewsCard($_REQUEST["news_id"], $_REQUEST["news_title"], $_REQUEST["news_text"], $_REQUEST["news_title_ru"], $_REQUEST["news_text_ru"], $_REQUEST["news_pos"], $_REQUEST["news_status"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="getSmartNewsImage"){ $GLOBALS['_RESULT'] = array("content"=>$smart->getSmartNewsImage($_REQUEST["news_id"]));}
if ($_REQUEST["w"]=="deleteSmartNewsPhoto"){ list($answer,$err)=$smart->deleteSmartNewsPhoto($_REQUEST["news_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

// ----- AUTO ----- //

if ($_REQUEST["w"]=="showManufacturersCard"){ list($content,$mfa_brand)=$auto->showManufacturersCard($_REQUEST["mfa_id"]); $GLOBALS['_RESULT'] = array("content"=>$content,"mfa_brand"=>$mfa_brand);}
if ($_REQUEST["w"]=="saveManufacturersCard"){ list($answer,$err)=$auto->saveManufacturersCard($_REQUEST["mfa_id"],$_REQUEST["mfa_brand"],$_REQUEST["mfa_logo"],$_REQUEST["mfa_position"],$_REQUEST["mfa_active"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="showModelsCard"){ list($content,$mod_tex_text)=$auto->showModelsCard($_REQUEST["mod_id"]); $GLOBALS['_RESULT'] = array("content"=>$content,"mod_tex_text"=>$mod_tex_text);}
if ($_REQUEST["w"]=="saveModelsCard"){ list($answer,$err)=$auto->saveModelsCard($_REQUEST["mod_id"],$_REQUEST["mod_mfa_id"],$_REQUEST["mod_model"],$_REQUEST["mod_tex_text"],$_REQUEST["mod_date_start"],$_REQUEST["mod_date_end"],$_REQUEST["mod_img"],$_REQUEST["mod_img_status"],$_REQUEST["mod_active"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="showTypesCard"){ list($content,$typ_mmt)=$auto->showTypesCard($_REQUEST["typ_id"]); $GLOBALS['_RESULT'] = array("content"=>$content,"typ_mmt"=>$typ_mmt);}
if ($_REQUEST["w"]=="saveTypesCard"){ list($answer,$err)=$auto->saveTypesCard($_REQUEST["typ_id"], $_REQUEST["typ_text"], $_REQUEST["typ_mmt"], $_REQUEST["typ_mod"], $_REQUEST["typ_sort"], $_REQUEST["typ_pcon_start"], $_REQUEST["typ_pcon_end"], $_REQUEST["typ_kw_from"], $_REQUEST["typ_hp_from"], $_REQUEST["typ_ccm"], $_REQUEST["fuel_id"], $_REQUEST["body_id"], $_REQUEST["eng_cod"], $_REQUEST["typ_active"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

// ---- REPORT CLIENTS ---- //

if ($_REQUEST["w"]=="showReportClients"){ $GLOBALS['_RESULT'] = array("content"=>$report_clients->showReportClients($_REQUEST["date_start"],$_REQUEST["date_end"],$_REQUEST["clients"],$_REQUEST["cash_id"],$_REQUEST["tpoint_id_report"],$_REQUEST["client_category"]));}

if ($_REQUEST["w"]=="showAnalyticsClients"){ $GLOBALS['_RESULT'] = array("content"=>$report_clients->showAnalyticsClients($_REQUEST["date_start"], $_REQUEST["date_end"], $_REQUEST["clients"], $_REQUEST["cash_id"], $_REQUEST["tpoint_id_report"], $_REQUEST["price_id"], $_REQUEST["margin_status"], $_REQUEST["states"], $_REQUEST["regions"], $_REQUEST["citys"]));}

if ($_REQUEST["w"]=="showReportSalesArticles"){ $GLOBALS['_RESULT'] = array("content"=>$report_clients->showReportSalesArticles($_REQUEST["date_start"], $_REQUEST["date_end"], $_REQUEST["brands"], $_REQUEST["goods_group"], $_REQUEST["client_ids"], $_REQUEST["params"]));}

if ($_REQUEST["w"]=="updateCitysRange"){ $GLOBALS['_RESULT'] = array("content"=>$report_clients->updateCitysRange($_REQUEST["text"], $_REQUEST["citys_selected"]));}

if ($_REQUEST["w"]=="loadClientDetailsList"){ $GLOBALS['_RESULT'] = array("content"=>$cl->loadClientDetailsList($_REQUEST["client_id"]));}
if ($_REQUEST["w"]=="loadClientDetailsCard"){ $GLOBALS['_RESULT'] = array("content"=>$cl->loadClientDetailsCard($_REQUEST["detail_id"], $_REQUEST["client_id"]));}
if ($_REQUEST["w"]=="saveClientDetailsCard"){ list($answer,$err)=$cl->saveClientDetailsCard($_REQUEST["detail_id"],$_REQUEST["client_id"],$_REQUEST["address_jur"],$_REQUEST["address_fakt"],$_REQUEST["edrpou"],$_REQUEST["svidotctvo"],$_REQUEST["vytjag"],$_REQUEST["vat"],$_REQUEST["mfo"],$_REQUEST["bank"],$_REQUEST["account"],$_REQUEST["not_resident"],$_REQUEST["nr_details"],$_REQUEST["buh_name"],$_REQUEST["buh_edrpou"],$_REQUEST["main_details"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="dropClientDetailsCard"){ list($answer,$err)=$cl->dropClientDetailsCard($_REQUEST["detail_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

// ---- BUH INVOICES ---- //

if ($_REQUEST["w"]=="filterBuhInvoiceList"){ $GLOBALS['_RESULT'] = array("content"=>$buh_invoice->show_sale_invoice_list_filter($_REQUEST["date_start"], $_REQUEST["date_end"]));}

// ---- JPAY ---- //

if ($_REQUEST["w"]=="filterJPayList"){ $GLOBALS['_RESULT'] = array("content"=>$jpay->filterJPayList($_REQUEST["date_start"],$_REQUEST["date_end"],$_REQUEST["doc_type"],$_REQUEST["jpay_name"]));}

// ---- CASH REPORTS ---- //

if ($_REQUEST["w"] == "loadPayboxConvertRange") {
    $GLOBALS['_RESULT'] = array("content" => $paybox->loadPayboxConvertRange($_REQUEST["paybox_id_select"], $_REQUEST["date_start_select"], $_REQUEST["date_end_select"]));
}
if ($_REQUEST["w"] == "showCashReportsList") {
    $GLOBALS['_RESULT'] = array("content" => $paybox->showCashReportsList($_REQUEST["date_start"], $_REQUEST["date_end"], $_REQUEST["payboxes"], $_REQUEST["cash_id"]));
}
if ($_REQUEST["w"] == "showCashListSelected") {
    $GLOBALS['_RESULT'] = array("content" => $paybox->showCashListSelected($_REQUEST["sel"]));
}
if ($_REQUEST["w"] == "changePayboxConvertSumm") {
    list($price_from_max, $kours_from, $price_to, $kours_to_err) = $paybox->changePayboxConvertSumm($_REQUEST["paybox_id"], $_REQUEST["cash_id_from"], $_REQUEST["price_from"], $_REQUEST["cash_id_to"], $_REQUEST["kours_to"]);
    $GLOBALS['_RESULT'] = array("price_from_max"=>$price_from_max, "kours_from"=>$kours_from, "price_to"=>$price_to, "kours_to_err"=>$kours_to_err);
}
if ($_REQUEST["w"] == "savePayboxConvert") {
    list($answer, $err, $output) = $paybox->savePayboxConvert($_REQUEST["paybox_id"], $_REQUEST["cash_id_from"], $_REQUEST["cash_id_to"], $_REQUEST["kours_to"], $_REQUEST["price_from"], $_REQUEST["price_to"], $_REQUEST["note"]);
    $GLOBALS['_RESULT'] = array("answer"=>$answer, "error"=>$err, "output"=>$output);
}

if ($_REQUEST["w"] == "showCbCheckForm") {
    $GLOBALS['_RESULT'] = array("content" => $sale_invoice->showCbCheckForm($_REQUEST["invoice_id"], $_REQUEST["type_id"]));
}

if ($_REQUEST["w"] == "saveCbTpoint") {
    $GLOBALS['_RESULT'] = array("content" => $paybox->saveCbTpoint($_REQUEST["prro_id"]));
}

if ($_REQUEST["w"] == "createXReport") {
    list($answer, $err) = $paybox->createXReport($_REQUEST["prro_id"]);
    $GLOBALS['_RESULT'] = array("answer" => $answer, "error" => $err);
}

if ($_REQUEST["w"] == "showCheckReport") {
    list($answer, $err) = $paybox->showCheckReport($_REQUEST["prro_id"], $_REQUEST["check_id"]);
    $GLOBALS['_RESULT'] = array("answer" => $answer, "error" => $err);
}

if ($_REQUEST["w"] == "createServiceReceipt") {
    list($answer, $err) = $paybox->createServiceReceipt($_REQUEST["prro_id"], $_REQUEST["sum"], $_REQUEST["doc_type_id"], $_REQUEST["payment_type_id"]);
    $GLOBALS['_RESULT'] = array("answer" => $answer, "error" => $err);
}

if ($_REQUEST["w"] == "addCbCheck") {
    list($answer, $err) = $sale_invoice->addCbCheck($_REQUEST["invoice_id"], $_REQUEST["type_id"], $_REQUEST["payment_id"], $_REQUEST["email"]);
    $GLOBALS['_RESULT'] = array("answer" => $answer, "error" => $err);
}

if ($_REQUEST["w"] == "showCbCheck") {
    list($answer, $err, $check_id) = $sale_invoice->showCbCheck($_REQUEST["invoice_id"], $_REQUEST["type_id"]);
    $GLOBALS['_RESULT'] = array("answer" => $answer, "error" => $err, "check_id" => $check_id);
}

// ---- INCOME ---- //

if ($_REQUEST["w"]=="checkArticleZed"){ $GLOBALS['_RESULT'] = array("content"=>$income->checkArticleZed($_REQUEST["art_id"]));}

if ($_REQUEST["w"]=="saveArticleZed"){ list($answer,$err)=$income->saveArticleZed($_REQUEST["art_id"],$_REQUEST["costums_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="showIncomeStrList"){ $GLOBALS['_RESULT'] = array("content"=>$income->showIncomeStrList($_REQUEST["income_id"],$_REQUEST["type_id"],$_REQUEST["oper_status"]));}

if ($_REQUEST["w"]=="filterIncomeList"){ $GLOBALS['_RESULT'] = array("content"=>$income->show_income_list_filter($_REQUEST["date_start"],$_REQUEST["date_end"]));}

if ($_REQUEST["w"]=="setIncomeBarcodes"){ $GLOBALS['_RESULT'] = array("content"=>$income->setIncomeBarcodes($_REQUEST["income_id"]));}

if ($_REQUEST["w"]=="setIncomePrices"){ $GLOBALS['_RESULT'] = array("content"=>$income->setIncomePrices($_REQUEST["income_id"]));}

if ($_REQUEST["w"]=="saveIncomeArticlePriceRating"){ $GLOBALS['_RESULT'] = array("content"=>$income->saveIncomeArticlePriceRating($_REQUEST["income_id"],$_REQUEST["price_cash_id"],$_REQUEST["minMarkup"],$_REQUEST["prs"]));}

if ($_REQUEST["w"]=="showPreArticlePriceRating"){ $GLOBALS['_RESULT'] = array("content"=>$income->showPreArticlePriceRating($_REQUEST["income_id"],$_REQUEST["price_cash_id"],$_REQUEST["prs"]));}

if ($_REQUEST["w"]=="recalculateMadeIncome"){ $GLOBALS['_RESULT'] = array("content"=>$income->recalculateMadeIncome($_REQUEST["income_id"]));}

// ---- BUHBACK ---- //

if ($_REQUEST["w"]=="filterBuhBackClientsList"){ $GLOBALS['_RESULT'] = array("content"=>$back_clients->show_buh_back_clients_list_filter($_REQUEST["date_start"], $_REQUEST["date_end"]));}
if ($_REQUEST["w"]=="loadBackImport"){ $GLOBALS['_RESULT'] = array("content"=>$back_clients->loadBackImport());}
if ($_REQUEST["w"]=="saveCsvBackImport"){list($answer,$err)=$back_clients->saveCsvBackImport($_REQUEST["start_row"],$_REQUEST["kol_cols"],$_REQUEST["cols"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err); }
if ($_REQUEST["w"]=="saveTablePreviewBack"){list($answer,$err)=$back_clients->saveTablePreviewBack($_REQUEST["brands"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err); }
if ($_REQUEST["w"]=="loadTablePreviewBack"){$GLOBALS['_RESULT'] = array("content"=>$back_clients->loadTablePreviewBack($_REQUEST["brands"]));}
if ($_REQUEST["w"]=="getCellsList"){$GLOBALS['_RESULT'] = array("content"=>$back_clients->getCellsList($_REQUEST["storage_id"]));}

if ($_REQUEST["w"]=="checkBackArticles"){ $GLOBALS['_RESULT'] = array("content"=>$back_clients->checkBackArticles($_REQUEST["client_id"],$_REQUEST["storage_id"],$_REQUEST["cell_id"]));}
if ($_REQUEST["w"]=="finishBackImport"){list($answer,$err,$invoice_count)=$back_clients->finishBackImport($_REQUEST["client_id_sel"],$_REQUEST["storage_id_sel"],$_REQUEST["cell_id_sel"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err,"invoice_count"=>$invoice_count); }
if ($_REQUEST["w"]=="clearBackImport"){list($answer,$err)=$back_clients->clearBackImport(); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err); }

// ---- DP ---- //

if ($_REQUEST["w"]=="setDpPauseAccessStatus"){ $GLOBALS['_RESULT'] = array("content"=>$dp->setDpPauseAccessStatus($_REQUEST["media_user_id"],$_REQUEST["access_dp_pause"]));}

if ($_REQUEST["w"]=="showDPDocErrorForm"){ $GLOBALS['_RESULT'] = array("content"=>$dp->showDPDocErrorForm($_REQUEST["dp_id"]));}

if ($_REQUEST["w"]=="getDpTpointInfo"){ $GLOBALS['_RESULT'] = array("content"=>$dp->getDpTpointInfo($_REQUEST["client_id"]));}

if ($_REQUEST["w"]=="deleteOrderSite"){ list($answer,$err)=$dp->deleteOrderSite($_REQUEST["order_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
	
if ($_REQUEST["w"]=="showOrderSiteRange"){ $GLOBALS['_RESULT'] = array("content"=>$dp->showOrderSiteRange($_REQUEST["status"],$_REQUEST["data_start"],$_REQUEST["data_end"]));}

if ($_REQUEST["w"]=="show_dp_search_filter"){ $GLOBALS['_RESULT'] = array("content"=>$dp->show_dp_list_filter($_REQUEST["status"],$_REQUEST["filStatus"],$_REQUEST["filAuthor"],$_REQUEST["filTpoint"],$_REQUEST["filClientTypeMain"],$_REQUEST["filDpName"]));}

if ($_REQUEST["w"]=="saveDpOrderInfo"){ $GLOBALS['_RESULT'] = array("content"=>$dp->saveDpOrderInfo($_REQUEST["dp_id"],$_REQUEST["order_info_id"],$_REQUEST["client_id"],$_REQUEST["user_id"],$_REQUEST["city_id"],$_REQUEST["delivery_id"],$_REQUEST["payment_id"],$_REQUEST["delivery_charge_id"],$_REQUEST["del_name"],$_REQUEST["del_phone"],$_REQUEST["del_street"],$_REQUEST["del_house"],$_REQUEST["del_porch"],$_REQUEST["del_department"],$_REQUEST["del_department_text"],$_REQUEST["del_express"],$_REQUEST["del_express_info"],$_REQUEST["del_express_payment"]));}
if ($_REQUEST["w"]=="getCityVal"){$GLOBALS['_RESULT'] = array("content"=>$dp->getCityVal($_REQUEST["search_text"]));}
if ($_REQUEST["w"]=="setCityNPVal"){$GLOBALS['_RESULT'] = array("content"=>$dp->setCityNPVal($_REQUEST["city_id"]));}
if ($_REQUEST["w"]=="setCityDepartments"){$GLOBALS['_RESULT'] = array("content"=>$dp->setCityDepartments($_REQUEST["city_ref"],$_REQUEST["department_ref"]));}
if ($_REQUEST["w"]=="setClientOrderInfo"){$GLOBALS['_RESULT'] = array("content"=>$dp->setClientOrderInfo($_REQUEST["order_info_id"],$_REQUEST["dp_id"]));}
if ($_REQUEST["w"]=="dropClientOrderInfo") {
    list($answer, $err) = $dp->dropClientOrderInfo($_REQUEST["order_info_id"],$_REQUEST["dp_id"]);
    $GLOBALS['_RESULT'] = array("answer" => $answer, "error" => $err);
}

if ($_REQUEST["w"] == "showCombineDpForm") {
    $GLOBALS['_RESULT'] = array("content" => $dp->showCombineDpForm());
}
if ($_REQUEST["w"] == "getCombineDpCrossList") {
    $GLOBALS['_RESULT'] = array("content" => $dp->getCombineDpCrossList($_REQUEST["main_dp_id"]));
}
if ($_REQUEST["w"]=="saveCombineDpCross") {
    list($answer, $err) = $dp->saveCombineDpCross($_REQUEST["main_dp_id"], $_REQUEST["cross_dp_ids"]);
    $GLOBALS['_RESULT'] = array("answer" => $answer, "error" => $err);
}

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

// REVIEWS

if ($_REQUEST["w"]=="loadReviewsList"){ $GLOBALS['_RESULT'] = array("content"=>$settings_new->loadReviewsList());}

if ($_REQUEST["w"]=="showReviewCard"){ $GLOBALS['_RESULT'] = array("content"=>$settings_new->showReviewCard($_REQUEST["id"]));}

if ($_REQUEST["w"]=="saveReview"){ list($answer,$err)=$settings_new->saveReview($_REQUEST["id"],$_REQUEST["t_ru"],$_REQUEST["t_ua"],$_REQUEST["t_en"],$_REQUEST["d_ru"],$_REQUEST["d_ua"],$_REQUEST["d_en"],$_REQUEST["title"],$_REQUEST["title_ua"],$_REQUEST["title_en"],$_REQUEST["text"],$_REQUEST["text_ua"],$_REQUEST["text_en"],$_REQUEST["data"],$_REQUEST["status"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="dropReview"){ list($answer,$err)=$settings_new->dropReview($_REQUEST["id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

// REQUESTS

if ($_REQUEST["w"]=="loadRequestsList"){ $GLOBALS['_RESULT'] = array("content"=>$settings_new->loadRequestsList());}

if ($_REQUEST["w"]=="showRequestCard"){ $GLOBALS['_RESULT'] = array("content"=>$settings_new->showRequestCard($_REQUEST["id"]));}

if ($_REQUEST["w"]=="closeRequestCard"){ $GLOBALS['_RESULT'] = array("content"=>$settings_new->closeRequestCard($_REQUEST["id"]));}
if ($_REQUEST["w"]=="unlockRequestCard"){ $GLOBALS['_RESULT'] = array("content"=>$settings_new->unlockRequestCard($_REQUEST["id"]));}

if ($_REQUEST["w"]=="saveRequest"){ list($answer,$err)=$settings_new->saveRequest($_REQUEST["id"],$_REQUEST["vin"],$_REQUEST["phone"],$_REQUEST["text"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="dropRequest"){ list($answer,$err)=$settings_new->dropRequest($_REQUEST["id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="showSeoTextCard"){ $GLOBALS['_RESULT'] = array("content"=>$settings_new->showSeoTextCard($_REQUEST["id"]));}
if ($_REQUEST["w"]=="saveSeoText"){ list($answer,$err)=$settings_new->saveSeoText($_REQUEST["id"],$_REQUEST["router"],$_REQUEST["link"],$_REQUEST["text_ru"],$_REQUEST["text_ua"],$_REQUEST["text_en"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="dropSeoText"){ list($answer,$err)=$settings_new->dropSeoText($_REQUEST["id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="showSeoFooterCard"){ $GLOBALS['_RESULT'] = array("content"=>$settings_new->showSeoFooterCard($_REQUEST["id"]));}
if ($_REQUEST["w"]=="saveSeoFooter"){ list($answer,$err)=$settings_new->saveSeoFooter($_REQUEST["id"],$_REQUEST["router"],$_REQUEST["link"],$_REQUEST["text_ru"],$_REQUEST["text_ua"],$_REQUEST["text_en"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="dropSeoFooter"){ list($answer,$err)=$settings_new->dropSeoFooter($_REQUEST["id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="showSeoGenerateCard"){ $GLOBALS['_RESULT'] = array("content"=>$settings_new->showSeoGenerateCard($_REQUEST["id"]));}
if ($_REQUEST["w"]=="saveSeoGenerate"){ list($answer,$err)=$settings_new->saveSeoGenerate($_REQUEST["id"],$_REQUEST["router"],$_REQUEST["link"],$_REQUEST["text_ru"],$_REQUEST["text_ua"],$_REQUEST["text_en"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="dropSeoGenerate"){ list($answer,$err)=$settings_new->dropSeoGenerate($_REQUEST["id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="showSeoTitleCard"){ $GLOBALS['_RESULT'] = array("content"=>$settings_new->showSeoTitleCard($_REQUEST["id"]));}
if ($_REQUEST["w"]=="saveSeoTitle"){ list($answer,$err)=$settings_new->saveSeoTitle($_REQUEST["id"],$_REQUEST["router"],$_REQUEST["link"],$_REQUEST["text_ru"],$_REQUEST["text_ua"],$_REQUEST["text_en"],$_REQUEST["descr_ru"],$_REQUEST["descr_ua"],$_REQUEST["descr_en"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="dropSeoTitle"){ list($answer,$err)=$settings_new->dropSeoTitle($_REQUEST["id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

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

if ($_REQUEST["w"]=="cancelStorselScan"){ list($answer,$err)=$storsel->cancelStorselScan($_REQUEST["select_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="scanStorselBarcodeForm"){ list($answer,$err)=$storsel->scanStorselBarcodeForm($_REQUEST["select_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

// ---- BRANDS ---- //

if ($_REQUEST["w"]=="showBrandsList"){ $GLOBALS['_RESULT'] = array("content"=>$brands->show_brands_list());}

if ($_REQUEST["w"]=="newBrandsCard"){ $GLOBALS['_RESULT'] = array("brands_id"=>$brands->newBrandsCard());}

if ($_REQUEST["w"]=="showBrandsCard"){ $GLOBALS['_RESULT'] = array("content"=>$brands->showBrandsCard($_REQUEST["brands_id"]));}

if ($_REQUEST["w"]=="ImportBrands"){ $GLOBALS['_RESULT'] = array("content"=>ImportBrands());}

if ($_REQUEST["w"]=="finishBrandsIndexImport"){ list($answer,$err)=finishBrandsIndexImport($_REQUEST["start_row"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err); }

if ($_REQUEST["w"]=="saveBrandsGeneralInfo") { list($answer,$err)=$brands->saveBrandsGeneralInfo($_REQUEST["brands_id"], $_REQUEST["brands_name"], $_REQUEST["brands_type"], $_REQUEST["brands_kind"], $_REQUEST["brands_country"], $_REQUEST["brands_visible"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err); }

if ($_REQUEST["w"]=="loadBrandsDetails"){ $GLOBALS['_RESULT'] = array("content"=>$brands->loadBrandsDetails($_REQUEST["brands_id"]));}

if ($_REQUEST["w"]=="saveBrandsDetails") { list($answer,$err)=$brands->saveBrandsDetails($_REQUEST["brands_id"], $_REQUEST["descr"], $_REQUEST["descr_ua"], $_REQUEST["descr_en"], $_REQUEST["link"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="loadBrandsPhoto"){ $GLOBALS['_RESULT'] = array("content"=>$brands->loadBrandsPhoto($_REQUEST["brands_id"]));}

if ($_REQUEST["w"]=="deleteBrandsLogo"){ list($answer,$err)=$brands->deleteBrandsLogo($_REQUEST["brands_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="getCertificatesList"){ $GLOBALS['_RESULT'] = array("content"=>$brands->getCertificatesList());}

if ($_REQUEST["w"]=="dropCertificatePhoto"){ $GLOBALS['_RESULT'] = array("content"=>$brands->dropCertificatePhoto($_REQUEST["certificate_id"]));}

if ($_REQUEST["w"]=="showCertificateCard"){ $GLOBALS['_RESULT'] = array("content"=>$brands->showCertificateCard($_REQUEST["certificate_id"]));}

if ($_REQUEST["w"]=="saveCertificateCard"){ list($answer, $err, $certificate_id) = $brands->saveCertificateCard($_REQUEST["certificate_id"], $_REQUEST["brand_id"], $_REQUEST["suppl_id"], $_REQUEST["date_from"], $_REQUEST["date_to"], $_REQUEST["status"]); $GLOBALS['_RESULT'] = array("answer"=>$answer, "error"=>$err, "certificate_id"=>$certificate_id);}

if ($_REQUEST["w"]=="dropCertificateCard"){ list($answer, $err) = $brands->dropCertificateCard($_REQUEST["certificate_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

// ---- COUNTRIES ---- //

if ($_REQUEST["w"]=="showCountryList"){ $GLOBALS['_RESULT'] = array("content"=>$cl->show_country_list());}

if ($_REQUEST["w"]=="newCountryCard"){ $GLOBALS['_RESULT'] = array("country_id"=>$cl->newCountryCard());}

if ($_REQUEST["w"]=="showCountryCard"){ $GLOBALS['_RESULT'] = array("content"=>$cl->showCountryCard($_REQUEST["country_id"]));}

if ($_REQUEST["w"]=="saveCountryGeneralInfo") { list($answer,$err)=$cl->saveCountryGeneralInfo($_REQUEST["country_id"], $_REQUEST["country_name"], $_REQUEST["country_alfa2"], $_REQUEST["country_alfa3"], $_REQUEST["country_duty"], $_REQUEST["country_risk"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="dropCountry") { list($answer,$err)=$cl->dropCountry($_REQUEST["country_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

// ---- JMOVING ---- //

if ($_REQUEST["w"]=="cutJmovingStorageAll"){ list($answer,$err,$ids)=$jmoving->cutJmovingStorageAll($_REQUEST["jmoving_id"],$_REQUEST["select_id"],$_REQUEST['comment']); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err,"ids"=>$ids);}

if ($_REQUEST["w"]=="separateJmovingByDefect"){ list($answer,$err)=$jmoving->separateJmovingByDefect($_REQUEST["jmoving_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="checkJmovingBugs"){ $GLOBALS['_RESULT'] = array("content"=>$jmoving->checkJmovingBugs($_REQUEST["jmoving_id"]));}

/*
 * consolidate jmoving form
 * */
if ($_REQUEST["w"]=="consolidateJmoving"){ $GLOBALS['_RESULT'] = array("content"=>$jmoving->consolidateJmoving($_REQUEST["storage_id"]));}
if ($_REQUEST["w"]=="saveUserJmoving"){ list($answer,$err)=$jmoving->saveUserJmoving($_REQUEST["user_id"], $_REQUEST["jmoving_arr"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="clearJmovingUse"){ $GLOBALS['_RESULT'] = array("content"=>$jmoving->clearJmovingUse($_REQUEST["user_id"]));}
if ($_REQUEST["w"]=="saveConsolidateJmovingAcceptBarcodeForm"){ list($answer,$err)=$jmoving->saveConsolidateJmovingAcceptBarcodeForm($_REQUEST["barcode"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

/*Jmoving Import*/
if ($_REQUEST["w"]=="loadJmovingImport"){ $GLOBALS['_RESULT'] = array("content"=>$jmoving->loadJmovingImport($_REQUEST["jmoving_id"]));}
if ($_REQUEST["w"]=="loadJmovingTablePreview"){$GLOBALS['_RESULT'] = array("content"=>$jmoving->loadTablePreview($_REQUEST["jmoving_id"],$_REQUEST["brands"]));}

if ($_REQUEST["w"]=="saveJmovingTablePreview"){list($answer,$err)=$jmoving->saveTablePreview($_REQUEST["jmoving_id"],$_REQUEST["brands"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err); }

if ($_REQUEST["w"]=="finishJmovingImport"){list($answer,$err)=$jmoving->finishJmovingImport($_REQUEST["jmoving_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err); }
if ($_REQUEST["w"]=="clearJmovingImport"){list($answer,$err)=$jmoving->clearJmovingImport($_REQUEST["jmoving_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err); }
if ($_REQUEST["w"]=="saveCsvJmovingImport"){list($answer,$err)=$jmoving->saveCsvJmovingImport($_REQUEST["jmoving_id"],$_REQUEST["start_row"],$_REQUEST["kol_cols"],$_REQUEST["cols"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err); }

/*Jmoving Unknown*/
if ($_REQUEST["w"]=="loadJmovingUnknown"){ $GLOBALS['_RESULT'] = array("content"=>$jmoving->loadJmovingUnknown($_REQUEST["jmoving_id"]));}
if ($_REQUEST["w"]=="clearJmovingUnknown"){list($answer,$err)=$jmoving->clearJmovingUnknown($_REQUEST["jmoving_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err); }


if ($_REQUEST["w"]=="saveJmovingStorageFieldsViewForm"){ list($answer,$err)=$jmoving->saveJmovingStorageFieldsViewForm($_REQUEST["kol_fields"],$_REQUEST["fl_id"],$_REQUEST["fl_ch"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="scanJmovingAcceptForm"){ list($answer,$err)=$jmoving->scanJmovingAcceptForm($_REQUEST["jmoving_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="dropJmovingCard"){$GLOBALS['_RESULT'] = array("content"=>$jmoving->dropJmovingCard($_REQUEST["jmoving_id"]));}

// ---- TPOINT ---- //

if ($_REQUEST["w"]=="deleteTpoint"){ list($answer,$err)=$tpoint->deleteTpoint($_REQUEST["tpoint_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="deleteStorage"){ list($answer,$err)=$storage->deleteStorage($_REQUEST["storage_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="loadStorageCellsList"){ $GLOBALS['_RESULT'] = array("content"=>$storage->loadStorageCellsList($_REQUEST["cell_id"]));}
if ($_REQUEST["w"]=="loadStorageAllCellList"){ $GLOBALS['_RESULT'] = array("content"=>$storage->loadStorageAllCellList($_REQUEST["storage_id"]));}
if ($_REQUEST["w"]=="loadStorageAllList"){ $GLOBALS['_RESULT'] = array("content"=>$storage->loadStorageAllList($_REQUEST["storage_id"]));}

if ($_REQUEST["w"]=="getStorageReservDuplicates"){ $GLOBALS['_RESULT'] = array("content"=>$storage->getStorageReservDuplicates($_REQUEST["storage_id"]));}

if ($_REQUEST["w"]=="showJmovingStorageFieldsViewForm"){ $GLOBALS['_RESULT'] = array("content"=>$jmoving->showJmovingStorageFieldsViewForm($_REQUEST["jmoving_id"]));}

// ---- CLIENTS ---- //

if ($_REQUEST["w"]=="dropClient"){ list($answer,$err)=$cl->dropClient($_REQUEST["client_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

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

if ($_REQUEST["w"]=="loadClientAddresses"){ $GLOBALS['_RESULT'] = array("content"=>$cl->loadClientAddresses($_REQUEST["client_id"]));}

if ($_REQUEST["w"]=="addClientAddress"){ $GLOBALS['_RESULT'] = array("content"=>$cl->addClientAddress($_REQUEST["client_id"],$_REQUEST["address"]));}

if ($_REQUEST["w"]=="saveClientAddresss"){ list($answer,$err)=$cl->saveClientAddresss($_REQUEST["address_id"],$_REQUEST["address"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="dropClientAddresss"){ list($answer,$err)=$cl->dropClientAddresss($_REQUEST["address_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

// ---- SALE INVOICE ---- //

if ($_REQUEST["w"]=="filterInvoiceList"){ $GLOBALS['_RESULT'] = array("content"=>$sale_invoice->show_sale_invoice_list_filter($_REQUEST["date_start"],$_REQUEST["date_end"],$_REQUEST["prefix"],$_REQUEST["doc_nom"]));}

if ($_REQUEST["w"]=="exportSaleInvoceExcel")  { $GLOBALS['_RESULT'] = array("content"=>$sale_invoice->exportSaleInvoiceExcel($_REQUEST["invoice_id"]));}

if ($_REQUEST["w"]=="getPartitionsInvoiceAmount"){ $GLOBALS['_RESULT'] = array("content"=>$sale_invoice->getPartitionsInvoiceAmount($_REQUEST["partition_id"]));}

if ($_REQUEST["w"]=="savePartitionsInvoiceAmount"){ $GLOBALS['_RESULT'] = array("content"=>$sale_invoice->savePartitionsInvoiceAmount($_REQUEST["partition_id"],$_REQUEST["invoice_amount"]));}

//if ($_REQUEST["w"]=="addClientInvoiceCron"){ $GLOBALS['_RESULT'] = array("content"=>$sale_invoice->addClientInvoiceCron($_REQUEST["invoice_id"]));}
//if ($_REQUEST["w"]=="sendSaleInvoceMail"){ list($answer,$err)=$sale_invoice->sendSaleInvoceMail($_REQUEST["invoice_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

// ---- CATALOGUE ---- //

if ($_REQUEST["w"]=="generateBarcode"){ $GLOBALS['_RESULT'] = array("content"=>$catalogue->generateBarcode());}

if ($_REQUEST["w"]=="saveBarcode"){ list($answer,$err)=$catalogue->saveBarcode($_REQUEST["art_id"],$_REQUEST["barcode"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="getCatalogueParamsList"){ $GLOBALS['_RESULT'] = array("content"=>$catalogue->getParamsList($_REQUEST["template_id"]));}

if ($_REQUEST["w"]=="getCatalogueValuesList"){ $GLOBALS['_RESULT'] = array("content"=>$catalogue->getValuesList($_REQUEST["template_id"],$_REQUEST["param_id"]));}

//TEMPLATE
if ($_REQUEST["w"]=="showCatalogueTemplateForm"){ $GLOBALS['_RESULT'] = array("content"=>$catalogue->showCatalogueTemplateForm($_REQUEST["template_id"]));}

if ($_REQUEST["w"]=="saveCatalogueTemplateForm"){ list($answer,$err)=$catalogue->saveCatalogueTemplateForm($_REQUEST["template_id"],$_REQUEST["template_name"],$_REQUEST["child_status"],$_REQUEST["parent_id"],$_REQUEST["template_status"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="dropCatalogueTemplate"){ list($answer,$err)=$catalogue->dropCatalogueTemplate($_REQUEST["template_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="dropCatalogueTemplateArticle"){ list($answer,$err)=$catalogue->dropCatalogueTemplateArticle($_REQUEST["template_id"],$_REQUEST["art_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

//PARAMS
if ($_REQUEST["w"]=="showCatalogueTemplateParamsForm"){ $GLOBALS['_RESULT'] = array("content"=>$catalogue->showCatalogueTemplateParamsForm($_REQUEST["param_id"],$_REQUEST["template_id"]));}

if ($_REQUEST["w"]=="saveCatalogueTemplateParamsForm"){ list($answer,$err)=$catalogue->saveCatalogueTemplateParamsForm($_REQUEST["param_id"],$_REQUEST["template_id"],$_REQUEST["param_name"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="dropCatalogueTemplateParams"){ list($answer,$err)=$catalogue->dropCatalogueTemplateParams($_REQUEST["param_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

//VALUE
if ($_REQUEST["w"]=="showCatalogueParamValueForm"){ $GLOBALS['_RESULT'] = array("content"=>$catalogue->showCatalogueParamValueForm($_REQUEST["template_id"],$_REQUEST["param_id"]));}

if ($_REQUEST["w"]=="showCatalogueTemplateParamsValueForm"){ $GLOBALS['_RESULT'] = array("content"=>$catalogue->showCatalogueTemplateParamsValueForm($_REQUEST["template_id"]));}

if ($_REQUEST["w"]=="saveCatalogueParamValueForm"){ list($answer,$err)=$catalogue->saveCatalogueParamValueForm($_REQUEST["art_id"],$_REQUEST["template_id"],$_REQUEST["param_id"],$_REQUEST["value_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="saveCatalogueTemplateParamsValue"){ list($answer,$err)=$catalogue->saveCatalogueTemplateParamsValue($_REQUEST["id"],$_REQUEST["value_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="saveCatalogueTemplateParamsValueForm"){ list($answer,$err)=$catalogue->saveCatalogueTemplateParamsValueForm($_REQUEST["template_id"],$_REQUEST["param_id"],$_REQUEST["param_value"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="dropCatalogueTemplateParamsValue"){ list($answer,$err)=$catalogue->dropCatalogueTemplateParamsValue($_REQUEST["id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

//get CATALOGUE variables

if ($_REQUEST["w"]=="loadArticleCatalogue"){ $GLOBALS['_RESULT'] = array("content"=>$catalogue->loadArticleCatalogue($_REQUEST["art_id"]));}

if ($_REQUEST["w"]=="loadTemplateList"){ $GLOBALS['_RESULT'] = array("content"=>$catalogue->loadTemplateList($_REQUEST["art_id"],$_REQUEST["template_id"]));}

if ($_REQUEST["w"]=="showArticleLogs"){ $GLOBALS['_RESULT'] = array("content"=>$catalogue->showArticleLogs($_REQUEST["art_id"]));}

if ($_REQUEST["w"]=="getMaxIndex"){ $GLOBALS['_RESULT'] = array("content"=>$catalogue->getMaxIndex());}

if ($_REQUEST["w"]=="getMaxSupplIndex"){ $GLOBALS['_RESULT'] = array("content"=>$catalogue->getMaxSupplIndex());}

if ($_REQUEST["w"]=="saveIndexArticle"){ list($answer,$err)=$catalogue->saveIndexArticle($_REQUEST["art_id"],$_REQUEST["article_nr_displ"],$_REQUEST["brand_id"],$_REQUEST["article_name"],$_REQUEST["article_name_ukr"],$_REQUEST["article_info"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="showArticleJDocs"){ list($content,$header)=$catalogue->showArticleJDocs($_REQUEST["art_id"]); $GLOBALS['_RESULT'] = array("content"=>$content, "header"=>$header);}

if ($_REQUEST["w"]=="addArticleInfo"){ list($answer,$err)=$catalogue->addArticleInfo($_REQUEST["art_id"],$_REQUEST["lang_id"],$_REQUEST["text"],$_REQUEST["value"],$_REQUEST["sort"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="loadArticleInfo"){ $GLOBALS['_RESULT'] = array("content"=>$catalogue->loadArticleInfo($_REQUEST["art_id"]));}
if ($_REQUEST["w"]=="saveArticleInfo"){ list($answer,$err)=$catalogue->saveArticleInfo($_REQUEST["id"],$_REQUEST["text"],$_REQUEST["value"],$_REQUEST["sort"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="dropArticleInfo"){ list($answer,$err)=$catalogue->dropArticleInfo($_REQUEST["id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="addArticleShortInfo"){ list($answer,$err)=$catalogue->addArticleShortInfo($_REQUEST["art_id"],$_REQUEST["lang_id"],$_REQUEST["text"],$_REQUEST["value"],$_REQUEST["sort"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="loadArticleShortInfo"){ $GLOBALS['_RESULT'] = array("content"=>$catalogue->loadArticleShortInfo($_REQUEST["art_id"]));}
if ($_REQUEST["w"]=="saveArticleShortInfo"){ list($answer,$err)=$catalogue->saveArticleShortInfo($_REQUEST["id"],$_REQUEST["text"],$_REQUEST["value"],$_REQUEST["sort"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="dropArticleShortInfo"){ list($answer,$err)=$catalogue->dropArticleShortInfo($_REQUEST["id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="showArticleCross"){ $GLOBALS['_RESULT'] = array("content"=>$catalogue->showArticleCross($_REQUEST["art_id"]));}

if ($_REQUEST["w"]=="saveArticleCross"){ list($answer,$err)=$catalogue->saveArticleCross($_REQUEST["cross"],$_REQUEST["new_cross"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

// ---- BACK CLIENTS ---- //

if ($_REQUEST["w"]=="showStorageCellsList") { $GLOBALS['_RESULT'] = array("content"=>$back_clients->showStorageCellsList($_REQUEST["storage_id"]));}

if ($_REQUEST["w"]=="setBackClientsStorageCell") { $GLOBALS['_RESULT'] = array("content"=>$back_clients->setBackClientsStorageCell($_REQUEST["back_id"],$_REQUEST["cell_id"]));}

if ($_REQUEST["w"]=="loadBackClientsPartition") { $GLOBALS['_RESULT'] = array("content"=>$back_clients->loadBackClientsPartition($_REQUEST["back_id"]));}

if ($_REQUEST["w"]=="filterBackClientsList") { $GLOBALS['_RESULT'] = array("content"=>$back_clients->show_back_clients_list_filter($_REQUEST["date_start"],$_REQUEST["date_end"]));}

if ($_REQUEST["w"]=="exportBackClientsExcel") { $GLOBALS['_RESULT'] = array("content"=>$back_clients->exportBackClientsExcel($_REQUEST["back_id"]));}

// ---- SUPPL ---- //

if ($_REQUEST["w"]=="showDpSupplInfo"){ $content=$suppl->showDpSupplInfo($_REQUEST["suppl_id"], $_REQUEST["suppl_storage_id"]); $GLOBALS['_RESULT'] = array("content"=>$content);}

if ($_REQUEST["w"]=="loadSupplCoopList"){ $GLOBALS['_RESULT'] = array("content"=>$suppl->showSupplCoopList());}

if ($_REQUEST["w"]=="saveSupplCoop"){ list($answer,$err)=$suppl->saveSupplCoop($_REQUEST["suppl_id"],$_REQUEST["company"],$_REQUEST["name"],$_REQUEST["phone"],$_REQUEST["email"],$_REQUEST["city_id"],$_REQUEST["comment"],$_REQUEST["status"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="showSupplCoopCard"){ $GLOBALS['_RESULT'] = array("content"=>$suppl->showSupplCoopCard($_REQUEST["suppl_id"]));}

if ($_REQUEST["w"]=="saveSupplImportGeneralInfo"){ list($answer,$err)=$suppl->saveSupplImportGeneralInfo($_REQUEST["suppl_id"],$_REQUEST["client_id"],$_REQUEST["email"],$_REQUEST["file_format"],$_REQUEST["cash_id"],$_REQUEST["start_row"],$_REQUEST["delimiter"],$_REQUEST["column_article"],$_REQUEST["column_brand"],$_REQUEST["column_price"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="showSupplImportCard"){ $GLOBALS['_RESULT'] = array("content"=>$suppl->showSupplImportCard($_REQUEST["suppl_id"]));}

if ($_REQUEST["w"]=="previewSupplImport"){ $GLOBALS['_RESULT'] = array("content"=>$suppl->previewSupplImport($_REQUEST["cash_id"],$_REQUEST["start_row"],$_REQUEST["column_article"],$_REQUEST["column_brand"],$_REQUEST["column_price"]));}

if ($_REQUEST["w"]=="changeSupplSelect"){ $GLOBALS['_RESULT'] = array("return_delay"=>$suppl->changeSupplSelect($_REQUEST["suppl_id"]));}

//UNKNOWN NUMBERS
if ($_REQUEST["w"]=="showNumbersList"){ list($content,$select)=$suppl->showNumbersList($_REQUEST["suppl_id"]); $GLOBALS['_RESULT'] = array("content"=>$content,"select"=>$select);}
if ($_REQUEST["w"]=="showNumbersBrandList"){ $GLOBALS['_RESULT'] = array("content"=>$suppl->showNumbersBrandList($_REQUEST["suppl_id"]));}

//if ($_REQUEST["w"]=="showNumbersList"){ $GLOBALS['_RESULT'] = array("content"=>$suppl->showNumbersList($_REQUEST["suppl_id"]));}
if ($_REQUEST["w"]=="showArticlesNumbersList"){ $GLOBALS['_RESULT'] = array("content"=>$suppl->showArticlesNumbersList($_REQUEST["suppl_id"],$_REQUEST["suppl_brand"],$_REQUEST["brand_id"],$_REQUEST["prefix"],$_REQUEST["limit"]));}
if ($_REQUEST["w"]=="showAllBrandIds"){ $GLOBALS['_RESULT'] = array("content"=>$suppl->showAllBrandIds());}

if ($_REQUEST["w"]=="showUnknownBrandIds"){ $GLOBALS['_RESULT'] = array("content"=>$suppl->showUnknownBrandIds($_REQUEST["suppl_id"],$_REQUEST["brand"]));}
if ($_REQUEST["w"]=="saveArticlesNumbersList"){ list($answer,$err)=$suppl->saveArticlesNumbersList($_REQUEST["suppl_id"],$_REQUEST["suppl_brand"],$_REQUEST["return_delay"],$_REQUEST["warranty_info"],$_REQUEST["prefix"],$_REQUEST["limit"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="showArticlesUnknown"){ list($content,$header)=$suppl->showArticlesUnknown($_REQUEST["suppl_id"],$_REQUEST["suppl_index"],$_REQUEST["suppl_brand"],$_REQUEST["prefix"]); $GLOBALS['_RESULT'] = array("content"=>$content,"header"=>$header);}

if ($_REQUEST["w"]=="saveArticlesUnknown"){ list($answer,$err)=$suppl->saveArticlesUnknown($_REQUEST["suppl_id"],$_REQUEST["suppl_index"],$_REQUEST["suppl_brand"],$_REQUEST["art_id"],$_REQUEST["return_delay"],$_REQUEST["warranty_info"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="saveSupplPrefix"){list($answer,$err)=$suppl->saveSupplPrefix($_REQUEST["suppl_id"],$_REQUEST["suppl_brand"],$_REQUEST["brand_id"],$_REQUEST["prefix"],$_REQUEST["return_delay"],$_REQUEST["warranty_info"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err); }


if ($_REQUEST["w"]=="autoSetNumbersList"){list($answer,$err,$numbers_count,$numbers_list)=$suppl->autoSetNumbersList($_REQUEST["suppl_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err,"numbers_count"=>$numbers_count,"numbers_list"=>$numbers_list); }


if ($_REQUEST["w"]=="showUnknownBrandPrefix"){list($prefix,$return_delay,$warranty_info)=$suppl->showUnknownBrandPrefix($_REQUEST["suppl_id"],$_REQUEST["suppl_brand"],$_REQUEST["brand_id"]); $GLOBALS['_RESULT'] = array("prefix"=>$prefix,"return_delay"=>$return_delay,"warranty_info"=>$warranty_info); }

// ---- Report Overdraft ---- //

if ($_REQUEST["w"]=="filterReportOverdraftList"){ $GLOBALS['_RESULT'] = array("content"=>$report_overdraft->showReportOverdraftList($_REQUEST["data"],$_REQUEST["client_id_cur"],$_REQUEST["tpoint_id_cur"]));}

if ($_REQUEST["w"]=="getClientOverdraftList"){ $GLOBALS['_RESULT'] = array("content"=>$report_overdraft->getClientOverdraftList($_REQUEST["data"],$_REQUEST["tpoint_id"]));}

if ($_REQUEST["w"]=="showDocsProlongationForm"){ $GLOBALS['_RESULT'] = array("content"=>$report_overdraft->showDocsProlongationForm($_REQUEST["client_id"],$_REQUEST["invoice_id"]));}

// ---- Seo Reports ---- //

if ($_REQUEST["w"]=="showSeoReports"){ $GLOBALS['_RESULT'] = array("content"=>$seo_reports->showSeoReports($_REQUEST["date_start"],$_REQUEST["date_end"],$_REQUEST["managers"],$_REQUEST["cash_id"],$_REQUEST["client_status"]));}

if ($_REQUEST["w"]=="getSummUser"){ $GLOBALS['_RESULT'] = array("content"=>$seo_reports->getSummUser($_REQUEST["user_id"],$_REQUEST["date_start"],$_REQUEST["date_end"],$_REQUEST["cash_id"]));}

// ---- Report Sales ---- //

if ($_REQUEST["w"]=="showReportSales"){ $GLOBALS['_RESULT'] = array("content"=>$catalogue->showReportSales($_REQUEST["date_start"],$_REQUEST["tpoint"]));}

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

if ($_REQUEST["w"]=="saveGroupTreeHeadStrCard"){ list($answer,$err)=$group_tree->saveGroupTreeHeadStrCard($_REQUEST["group_id"],$_REQUEST["head_id"],$_REQUEST["str_id"],$_REQUEST["position"],$_REQUEST["category"],$_REQUEST["disp_text_ru"],$_REQUEST["disp_text_ua"],$_REQUEST["disp_text_en"],$_REQUEST["disp_text_link"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="dropGroupTreeHeadStr"){ list($answer,$err)=$group_tree->dropGroupTreeHeadStr($_REQUEST["group_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

//category
if ($_REQUEST["w"]=="showGroupTreeHeadCategory"){ $GLOBALS['_RESULT'] = array("content"=>$group_tree->showGroupTreeHeadCategory($_REQUEST["cat_id"]));}

if ($_REQUEST["w"]=="saveGroupTreeHeadCategoryCard"){ list($answer,$err)=$group_tree->saveGroupTreeHeadCategoryCard($_REQUEST["cat_id"],$_REQUEST["head_id"],$_REQUEST["position"],$_REQUEST["disp_text_ru"],$_REQUEST["disp_text_ua"],$_REQUEST["disp_text_en"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="dropGroupTreeHeadCategory"){ list($answer,$err)=$group_tree->dropGroupTreeHeadCategory($_REQUEST["cat_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

//group tree
if ($_REQUEST["w"]=="dropUploadPhotoForm"){ list($answer,$err)=$group_tree->dropUploadPhotoForm($_REQUEST["type_id"],$_REQUEST["group_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="showUploadDropzone"){ $GLOBALS['_RESULT'] = array("content"=>$group_tree->showUploadDropzone($_REQUEST["type_id"],$_REQUEST["group_id"]));}

if ($_REQUEST["w"]=="loadTreeCons"){ $GLOBALS['_RESULT'] = array("content"=>$group_tree->loadTreeCons());}
if ($_REQUEST["w"]=="loadTreeConsHeader"){ $GLOBALS['_RESULT'] = array("content"=>$group_tree->loadTreeConsHeader());}
if ($_REQUEST["w"]=="loadTreeConsView"){ $GLOBALS['_RESULT'] = array("content"=>$group_tree->loadTreeConsView());}
if ($_REQUEST["w"]=="addHeadPopular"){ $GLOBALS['_RESULT'] = array("content"=>$group_tree->addHeadPopular($_REQUEST["head_id"]));}
if ($_REQUEST["w"]=="moveTreeConsCat"){ $GLOBALS['_RESULT'] = array("content"=>$group_tree->moveTreeConsCat($_REQUEST["head_id"],$_REQUEST["cat_id"],$_REQUEST["status"]));}
if ($_REQUEST["w"]=="moveTreeConsColumn"){ $GLOBALS['_RESULT'] = array("content"=>$group_tree->moveTreeConsColumn($_REQUEST["head_id"],$_REQUEST["status"]));}
if ($_REQUEST["w"]=="loadTreeConsViewList"){ $GLOBALS['_RESULT'] = array("content"=>$group_tree->loadTreeConsViewList($_REQUEST["head_id"]));}
if ($_REQUEST["w"]=="addTreeConsColumn"){ list($answer,$err)=$group_tree->addTreeConsColumn($_REQUEST["head_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="dropTreeConsColumn"){ list($answer,$err)=$group_tree->dropTreeConsColumn($_REQUEST["head_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="addTreeConsCatForm"){ $GLOBALS['_RESULT'] = array("content"=>$group_tree->addTreeConsCatForm($_REQUEST["head_id"]));}
if ($_REQUEST["w"]=="addTreeConsCat"){ list($answer,$err)=$group_tree->addTreeConsCat($_REQUEST["head_id"],$_REQUEST["cat_id"],$_REQUEST["cat_col"],$_REQUEST["cat_row"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="dropTreeConsCat"){ list($answer,$err)=$group_tree->dropTreeConsCat($_REQUEST["head_id"],$_REQUEST["cat_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="saveTreeConsCatPos"){ list($answer,$err)=$group_tree->saveTreeConsCatPos($_REQUEST["head_id"],$_REQUEST["cat_id"],$_REQUEST["cat_col"],$_REQUEST["cat_row"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="loadCatalogExist"){ $GLOBALS['_RESULT'] = array("content"=>$group_tree->loadCatalogExist($_REQUEST["status"]));}
if ($_REQUEST["w"]=="saveGroupStatus"){ $GLOBALS['_RESULT'] = array("content"=>$group_tree->saveGroupStatus($_REQUEST["type"],$_REQUEST["group_id"],$_REQUEST["status"]));}

if ($_REQUEST["w"]=="showGroupExistCard"){ $GLOBALS['_RESULT'] = array("content"=>$group_tree->showGroupExistCard($_REQUEST["group_id"]));}
if ($_REQUEST["w"]=="saveGroupExistCard"){ list($answer,$err)=$group_tree->saveGroupExistCard($_REQUEST["group_id"],$_REQUEST["text_ru"],$_REQUEST["text_ua"],$_REQUEST["text_en"],$_REQUEST["one_ru"],$_REQUEST["one_ua"],$_REQUEST["one_en"],$_REQUEST["h1_ru"],$_REQUEST["h1_ua"],$_REQUEST["h1_en"],$_REQUEST["descr_ru"],$_REQUEST["descr_ua"],$_REQUEST["descr_en"],$_REQUEST["text_link"],$_REQUEST["status"],$_REQUEST["status_auto"],$_REQUEST["reviews"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

//---- CLAIM ----//

if ($_REQUEST["w"]=="loadClaimList"){ $content=$claim->showClaimList(); $GLOBALS['_RESULT'] = array("content"=>$content);}

if ($_REQUEST["w"]=="showClaimCard"){ $content=$claim->showClaimCard($_REQUEST["claim_id"]); $GLOBALS['_RESULT'] = array("content"=>$content);}

if ($_REQUEST["w"]=="loadClaimAct"){ $content=$claim->loadClaimAct($_REQUEST["claim_id"]); $GLOBALS['_RESULT'] = array("content"=>$content);}

if ($_REQUEST["w"]=="saveClaimCard"){ list($answer,$err)=$claim->saveClaimCard($_REQUEST["claim_id"],$_REQUEST["art_id"],$_REQUEST["brand_id"],$_REQUEST["amount"],$_REQUEST["data"],$_REQUEST["supplier"],$_REQUEST["manufacturer"],$_REQUEST["client_id"],$_REQUEST["client_invoice"],$_REQUEST["comment"],$_REQUEST["receipt_doc"],$_REQUEST["kilometers"],$_REQUEST["state"],$_REQUEST["text_ru"],$_REQUEST["text_ua"],$_REQUEST["text_en"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

//---- WRITE OFF ----//

if ($_REQUEST["w"]=="showWriteOffCard"){ $content=$write_off->showWriteOffCard($_REQUEST["write_off_id"]); $GLOBALS['_RESULT'] = array("content"=>$content);}

if ($_REQUEST["w"]=="filterWriteOffList"){ $GLOBALS['_RESULT'] = array("content"=>$write_off->filterWriteOffList($_REQUEST["date_start"],$_REQUEST["date_end"]));}

if ($_REQUEST["w"]=="loadWriteOffPartitions"){ $GLOBALS['_RESULT'] = array("content"=>$write_off->loadWriteOffPartitions($_REQUEST["write_off_id"]));}

//---- ACTION CLIENTS ----//

if ($_REQUEST["w"]=="loadActionClientsList"){ $content=$action_clients->showActionClientsList(); $GLOBALS['_RESULT'] = array("content"=>$content);}

if ($_REQUEST["w"]=="showActionClientsCard"){ list($content,$clients,$categories,$action_id)=$action_clients->showActionClientsCard($_REQUEST["action_id"],$_REQUEST["sel_art_id"]); $GLOBALS['_RESULT'] = array("content"=>$content,"clients"=>$clients,"categories"=>$categories,"action_id"=>$action_id);}

if ($_REQUEST["w"]=="showSearchIndexForm"){ $content=$action_clients->showSearchIndexForm(); $GLOBALS['_RESULT'] = array("content"=>$content);}

if ($_REQUEST["w"]=="searchArticleDispl"){ list($content,$type_search,$brands)=$action_clients->searchArticleDispl($_REQUEST["article_nr_displ"],$_REQUEST["brand_id"]); $GLOBALS['_RESULT'] = array("content"=>$content,"type_search"=>$type_search,"brands"=>$brands);}

if ($_REQUEST["w"]=="saveActionClients"){ list($answer,$err)=$action_clients->saveActionClients($_REQUEST["action_id"],$_REQUEST["art_id"],$_REQUEST["client_list"],$_REQUEST["amount"],$_REQUEST["max_amount"],$_REQUEST["price"],$_REQUEST["action_data"],$_REQUEST["category_list"],$_REQUEST["return_delay"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="dropActionClients"){ list($answer,$err)=$action_clients->dropActionClients($_REQUEST["action_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="disableActionClients"){ list($answer,$err)=$action_clients->disableActionClients($_REQUEST["action_id"],$_REQUEST["status"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

/*==========================================*/

if ($_REQUEST["w"]=="showModuleList"){ $GLOBALS['_RESULT'] = array("content"=>$configs->showModuleList());}
if ($_REQUEST["w"]=="showModuleCard"){ $GLOBALS['_RESULT'] = array("content"=>$configs->showModuleCard($_REQUEST["module_id"]));}
if ($_REQUEST["w"]=="saveModuleCard"){ list($answer,$err)=$configs->saveModuleCard($_REQUEST["module_id"], $_REQUEST["module_caption"], $_REQUEST["module_link"], $_REQUEST["module_icon"], $_REQUEST["module_file"], $_REQUEST["module_lenta"], $_REQUEST["module_ison"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="dropModuleCard"){ list($answer,$err)=$configs->dropModuleCard($_REQUEST["module_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="showModulePagesList"){ $GLOBALS['_RESULT'] = array("content"=>$configs->showModulePagesList());}
if ($_REQUEST["w"]=="showModulePageCard"){ $GLOBALS['_RESULT'] = array("content"=>$configs->showModulePageCard($_REQUEST["page_id"]));}
if ($_REQUEST["w"]=="saveModulePageCard"){ list($answer,$err)=$configs->saveModulePageCard($_REQUEST["page_id"], $_REQUEST["page_mid"], $_REQUEST["page_module"], $_REQUEST["page_caption"], $_REQUEST["page_file"], $_REQUEST["page_link"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="dropModulePageCard"){ list($answer,$err)=$configs->dropModulePageCard($_REQUEST["page_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="showModuleFilesList"){ $GLOBALS['_RESULT'] = array("content"=>$configs->showModuleFilesList());}
if ($_REQUEST["w"]=="showModuleFileCard"){ $GLOBALS['_RESULT'] = array("content"=>$configs->showModuleFileCard($_REQUEST["file_id"]));}
if ($_REQUEST["w"]=="saveModuleFileCard"){ list($answer,$err)=$configs->saveModuleFileCard($_REQUEST["file_id"], $_REQUEST["file_caption"], $_REQUEST["file_file"], $_REQUEST["file_system"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="dropModuleFileCard"){ list($answer,$err)=$configs->dropModuleFileCard($_REQUEST["file_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

/*==========================================*/

if ($_REQUEST["w"]=="backIncome"){ $GLOBALS['_RESULT'] = array("content"=>$income->backIncome($_REQUEST["income_id"]));}

if ($_REQUEST["w"]=="showReportMargin"){ $GLOBALS['_RESULT'] = array("content"=>$report_margin->showReportMargin($_REQUEST["date_start"],$_REQUEST["date_end"],$_REQUEST["doc_type_id"],$_REQUEST["client_status"],$_REQUEST["doc_status"],$_REQUEST["cash_id"]));}

if ($_REQUEST["w"]=="showBuhIncomeCard"){ $GLOBALS['_RESULT'] = array("content"=>$buh_invoice->showBuhIncomeCard($_REQUEST["buh_income_id"]));}
if ($_REQUEST["w"]=="saveBuhIncomeCard"){ list($answer,$err)=$buh_invoice->saveBuhIncomeCard($_REQUEST["buh_income_id"], $_REQUEST["buh_income_text"], $_REQUEST["buh_income_pay_id"], $_REQUEST["buh_income_cash_id"], $_REQUEST["buh_income_state_id"], $_REQUEST["buh_income_summ"], $_REQUEST["buh_income_user_id"], $_REQUEST["buh_income_data"], $_REQUEST["buh_income_status"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
//if ($_REQUEST["w"]=="dropBuhIncomeCard"){ list($answer,$err)=$buh_invoice->dropBuhIncomeCard($_REQUEST["buh_income_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="showBuhConvertCard"){ $GLOBALS['_RESULT'] = array("content"=>$buh_invoice->showBuhConvertCard($_REQUEST["buh_convert_id"]));}
if ($_REQUEST["w"]=="getPayCashList"){ $GLOBALS['_RESULT'] = array("content"=>$buh_invoice->getPayCashList($_REQUEST["buh_convert_cash_id_pay"],$_REQUEST["buh_convert_user_id"]));}
if ($_REQUEST["w"]=="saveBuhConvertCard"){ list($answer,$err)=$buh_invoice->saveBuhConvertCard($_REQUEST["buh_convert_id"], $_REQUEST["buh_convert_text"], $_REQUEST["buh_convert_pay_id"], $_REQUEST["buh_convert_cash_id_pay"], $_REQUEST["buh_convert_cash_id_to"], $_REQUEST["buh_convert_kours_usd"], $_REQUEST["buh_convert_kours_eur"], $_REQUEST["buh_convert_summ"], $_REQUEST["buh_convert_user_id"], $_REQUEST["buh_convert_data"], $_REQUEST["buh_convert_status"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

//if ($_REQUEST["w"]=="filterBackSupplList"){ $GLOBALS['_RESULT'] = array("content"=>$suppl->showBackSupplList($_REQUEST["data_start"], $_REQUEST["data_end"], $_REQUEST["suppl_id"]));}

//clients

if ($_REQUEST["w"]=="showClientSupplGeneralSaldoForm"){ list($content,$header)=$cl->showClientSupplGeneralSaldoForm($_REQUEST["client_id"]);$GLOBALS['_RESULT'] = array("content"=>$content, "header"=>$header);}
//if ($_REQUEST["w"]=="filterClientGeneralSaldoForm"){ list($range,$saldo_start,$saldo_end,$saldo_data_start,$saldo_data_end)=$cl->filterClientGeneralSaldoForm($_REQUEST["client_id"],$_REQUEST["from"],$_REQUEST["to"]);$GLOBALS['_RESULT'] = array("range"=>$range, "saldo_start"=>$saldo_start, "saldo_end"=>$saldo_end, "saldo_data_start"=>$saldo_data_start, "saldo_data_end"=>$saldo_data_end);}

if ($_REQUEST["w"]=="showCatalogList"){ $GLOBALS['_RESULT'] = array("content"=>$catalog_parts->showCatalogList($_REQUEST["str_id"],$_REQUEST["brand_id"],$_REQUEST["type_id"],$_REQUEST["text"],$_REQUEST["name"],$_REQUEST["name_exist"],$_REQUEST["name_select"],$_REQUEST["name_exist_select"],$_REQUEST["check_auto"]));}
if ($_REQUEST["w"]=="showCatalogPartsCard"){ $GLOBALS['_RESULT'] = array("content"=>$catalog_parts->showCatalogPartsCard($_REQUEST["art_ids_str"]));}
if ($_REQUEST["w"]=="showCatalogPartsCard2"){ $GLOBALS['_RESULT'] = array("content"=>$catalog_parts->showCatalogPartsCard2());}
if ($_REQUEST["w"]=="setCatalogPartsBrands"){ list($list_brand, $list_name, $arr_name, $list_name_exist, $arr_name_exist)=$catalog_parts->setCatalogPartsBrands($_REQUEST["str_id"],$_REQUEST["type_id"]);$GLOBALS['_RESULT'] = array("list_brand"=>$list_brand, "list_name"=>$list_name, "arr_name"=>$arr_name, "list_name_exist"=>$list_name_exist, "arr_name_exist"=>$arr_name_exist);}
if ($_REQUEST["w"]=="setCatalogPartsBrandsName"){ $GLOBALS['_RESULT'] = array("content"=>$catalog_parts->setCatalogPartsBrandsName($_REQUEST["str_id"], $_REQUEST["brand_id"], $_REQUEST["type_id"]));}

if ($_REQUEST["w"]=="getGroupName"){ $GLOBALS['_RESULT'] = array("content"=>$catalog_parts->getGroupName($_REQUEST["group_id"]));}
//
if ($_REQUEST["w"]=="saveCatalogParts"){ $GLOBALS['_RESULT'] = array("content"=>$catalog_parts->saveCatalogParts($_REQUEST["group_id"], $_REQUEST["arts"]));}
if ($_REQUEST["w"]=="saveCatalogParts2"){ $GLOBALS['_RESULT'] = array("content"=>$catalog_parts->saveCatalogParts2($_REQUEST["group_id"],$_REQUEST["str_id"],$_REQUEST["brand_id"],$_REQUEST["type_id"],$_REQUEST["text"],$_REQUEST["name"],$_REQUEST["name_exist"],$_REQUEST["name_select"],$_REQUEST["name_exist_select"],$_REQUEST["check_auto"]));}

if ($_REQUEST["w"]=="getArticleNameCount"){ $GLOBALS['_RESULT'] = array("content"=>$catalog_parts->getArticleNameCount($_REQUEST["name_select"]));}

if ($_REQUEST["w"]=="showCatalogPartsEditCard"){ $GLOBALS['_RESULT'] = array("content"=>$catalog_parts->showCatalogPartsEditCard());}

if ($_REQUEST["w"]=="showCatalogLogs"){ $GLOBALS['_RESULT'] = array("content"=>$catalog_parts->showCatalogLogs($_REQUEST["group_id"]));}
if ($_REQUEST["w"]=="untieCatalogGroup"){ $GLOBALS['_RESULT'] = array("content"=>$catalog_parts->untieCatalogGroup($_REQUEST["head_id"], $_REQUEST["cat_id"], $_REQUEST["group_id"]));}
if ($_REQUEST["w"]=="showCatalogLogsCard"){ $GLOBALS['_RESULT'] = array("content"=>$catalog_parts->showCatalogLogsCard($_REQUEST["group_id"], $_REQUEST["date"], $_REQUEST["user_id"]));}
if ($_REQUEST["w"]=="showCatalogLogsArt"){ $GLOBALS['_RESULT'] = array("content"=>$catalog_parts->showCatalogLogsArt($_REQUEST["group_id"], $_REQUEST["art_id"]));}

if ($_REQUEST["w"]=="dropCatalogPartsArtsGroup"){ $GLOBALS['_RESULT'] = array("content"=>$catalog_parts->dropCatalogPartsArtsGroup($_REQUEST["group_id"], $_REQUEST["date"], $_REQUEST["user_id"]));}
if ($_REQUEST["w"]=="dropCatalogPartsArts"){ $GLOBALS['_RESULT'] = array("content"=>$catalog_parts->dropCatalogPartsArts($_REQUEST["group_id"], $_REQUEST["art_id"]));}

if ($_REQUEST["w"]=="showCatalogPartsAddCard"){ $GLOBALS['_RESULT'] = array("content"=>$catalog_parts->showCatalogPartsAddCard($_REQUEST["head_id"], $_REQUEST["cat_id"], $_REQUEST["group_id"]));}

if ($_REQUEST["w"]=="getCatalogHeadList"){ $GLOBALS['_RESULT'] = array("content"=>$catalog_parts->getCatalogHeadList());}
if ($_REQUEST["w"]=="getCatalogCatList"){ $GLOBALS['_RESULT'] = array("content"=>$catalog_parts->getCatalogCatList());}
if ($_REQUEST["w"]=="getCatalogGroupList"){ $GLOBALS['_RESULT'] = array("content"=>$catalog_parts->getCatalogGroupList());}

if ($_REQUEST["w"]=="saveCatalogPartsAddCard"){ $GLOBALS['_RESULT'] = array("content"=>$catalog_parts->saveCatalogPartsAddCard($_REQUEST["head_id"], $_REQUEST["cat_id"], $_REQUEST["group_id"]));}

if ($_REQUEST["w"]=="showCatalogItem"){ $GLOBALS['_RESULT'] = array("content"=>$catalog_parts->showCatalogItem($_REQUEST["type"], $_REQUEST["item_id"]));}
if ($_REQUEST["w"]=="addCatalogItem"){ $GLOBALS['_RESULT'] = array("content"=>$catalog_parts->addCatalogItem($_REQUEST["type"],$_REQUEST["text_ru"],$_REQUEST["text_ua"],$_REQUEST["text_en"],$_REQUEST["text_link"],$_REQUEST["status"],$_REQUEST["status_auto"],$_REQUEST["reviewed"],$_REQUEST["description_ru"],$_REQUEST["description_ua"],$_REQUEST["description_en"]));}
if ($_REQUEST["w"]=="editCatalogItem"){ $GLOBALS['_RESULT'] = array("content"=>$catalog_parts->editCatalogItem($_REQUEST["type"],$_REQUEST["item_id"],$_REQUEST["text_ru"],$_REQUEST["text_ua"],$_REQUEST["text_en"],$_REQUEST["text_link"],$_REQUEST["status"],$_REQUEST["status_auto"],$_REQUEST["reviewed"],$_REQUEST["description_ru"],$_REQUEST["description_ua"],$_REQUEST["description_en"]));}
if ($_REQUEST["w"]=="dropCatalogItem"){ $GLOBALS['_RESULT'] = array("content"=>$catalog_parts->dropCatalogItem($_REQUEST["type"], $_REQUEST["item_id"]));}

if ($_REQUEST["w"]=="showCatalogTree"){ $GLOBALS['_RESULT'] = array("content"=>$catalog_parts->showCatalogTree());}

if ($_REQUEST["w"]=="loadArticleTree"){ $GLOBALS['_RESULT'] = array("content"=>$catalogue->loadArticleTree($_REQUEST["art_id"]));}

if ($_REQUEST["w"]=="dropArticleTreeTecdoc"){ $GLOBALS['_RESULT'] = array("content"=>$catalogue->dropArticleTreeTecdoc($_REQUEST["art_id"], $_REQUEST["str_id"]));}
if ($_REQUEST["w"]=="dropArticleTreeNew"){ $GLOBALS['_RESULT'] = array("content"=>$catalogue->dropArticleTreeNew($_REQUEST["art_id"], $_REQUEST["group_id"]));}

if ($_REQUEST["w"]=="showArticleTreeTecdoc"){ $GLOBALS['_RESULT'] = array("content"=>$catalogue->showArticleTreeTecdoc());}
if ($_REQUEST["w"]=="showArticleTreeNew"){ $GLOBALS['_RESULT'] = array("content"=>$catalogue->showArticleTreeNew());}

if ($_REQUEST["w"]=="saveArticleTreeTecdoc"){ $GLOBALS['_RESULT'] = array("content"=>$catalogue->saveArticleTreeTecdoc($_REQUEST["art_id"], $_REQUEST["str_id"]));}
if ($_REQUEST["w"]=="saveArticleTreeNew"){ $GLOBALS['_RESULT'] = array("content"=>$catalogue->saveArticleTreeNew($_REQUEST["art_id"], $_REQUEST["group_id"]));}

if ($_REQUEST["w"]=="showArticleTreeExist"){ $GLOBALS['_RESULT'] = array("content"=>$catalogue->showArticleTreeExist());}
if ($_REQUEST["w"]=="saveArticleTreeExist"){ $GLOBALS['_RESULT'] = array("content"=>$catalogue->saveArticleTreeExist($_REQUEST["art_id"], $_REQUEST["brand_id"], $_REQUEST["group_id"]));}
if ($_REQUEST["w"]=="dropArticleTreeExist"){ $GLOBALS['_RESULT'] = array("content"=>$catalogue->dropArticleTreeExist($_REQUEST["art_id"], $_REQUEST["group_id"]));}

if ($_REQUEST["w"]=="loadImportIndex"){ $GLOBALS['_RESULT'] = array("content"=>$catalogue->loadImportIndex($_REQUEST["user_id"]));}
if ($_REQUEST["w"]=="saveCsvImportIndex"){list($answer,$err)=$catalogue->saveCsvImportIndex($_REQUEST["user_id"],$_REQUEST["start_row"],$_REQUEST["kol_cols"],$_REQUEST["cols"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err); }
if ($_REQUEST["w"]=="clearImportIndex"){list($answer,$err)=$catalogue->clearImportIndex($_REQUEST["user_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err); }
if ($_REQUEST["w"]=="finishImportIndex"){list($answer,$err)=$catalogue->finishImportIndex($_REQUEST["user_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err); }

if ($_REQUEST["w"]=="loadImportPhoto"){ $GLOBALS['_RESULT'] = array("content"=>$catalogue->loadImportPhoto($_REQUEST["user_id"]));}
if ($_REQUEST["w"]=="dropImportPhoto"){ $GLOBALS['_RESULT'] = array("content"=>$catalogue->dropImportPhoto($_REQUEST["user_id"]));}

if ($_REQUEST["w"]=="loadImportPhotoCsv"){ $GLOBALS['_RESULT'] = array("content"=>$catalogue->loadImportPhotoCsv($_REQUEST["user_id"]));}
if ($_REQUEST["w"]=="clearImportPhoto"){list($answer,$err)=$catalogue->clearImportPhoto($_REQUEST["user_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err); }
if ($_REQUEST["w"]=="saveImportPhotoCsv"){list($answer,$err)=$catalogue->saveImportPhotoCsv($_REQUEST["user_id"],$_REQUEST["start_row"],$_REQUEST["kol_cols"],$_REQUEST["cols"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err); }
if ($_REQUEST["w"]=="finishImportPhoto"){list($answer,$err)=$catalogue->finishImportPhoto($_REQUEST["user_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err); }
if ($_REQUEST["w"]=="finishImportExistPhoto"){list($answer,$err)=$catalogue->finishImportExistPhoto($_REQUEST["user_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err); }

if ($_REQUEST["w"]=="loadImportCross"){ $GLOBALS['_RESULT'] = array("content"=>$catalogue->loadImportCross($_REQUEST["user_id"]));}
if ($_REQUEST["w"]=="saveImportCross"){list($answer,$err)=$catalogue->saveImportCross($_REQUEST["user_id"],$_REQUEST["start_row"],$_REQUEST["kol_cols"],$_REQUEST["cols"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err); }
if ($_REQUEST["w"]=="clearImportCross"){list($answer,$err)=$catalogue->clearImportCross($_REQUEST["user_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err); }
if ($_REQUEST["w"]=="finishImportCross"){list($answer,$err)=$catalogue->finishImportCross($_REQUEST["user_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err); }
if ($_REQUEST["w"]=="saveImportCrossUnknown"){list($answer,$err)=$catalogue->saveImportCrossUnknown($_REQUEST["user_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err); }
if ($_REQUEST["w"]=="setUnknownBrands"){list($answer,$err)=$catalogue->setUnknownBrands($_REQUEST["user_id"],$_REQUEST["type"],$_REQUEST["brand_id_from"],$_REQUEST["brand_id_to"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err); }
if ($_REQUEST["w"]=="getUnknownBrandsCatalog"){ $GLOBALS['_RESULT'] = array("content"=>$catalogue->getUnknownBrandsCatalog($_REQUEST["brand_id_select"]));}
if ($_REQUEST["w"]=="getUnknownBrandsAll"){ $GLOBALS['_RESULT'] = array("content"=>$catalogue->getUnknownBrandsAll());}

if ($_REQUEST["w"]=="showClarifyForm"){ $GLOBALS['_RESULT'] = array("content"=>$catalogue->showClarifyForm($_REQUEST["import_id"]));}
if ($_REQUEST["w"]=="saveClarifyForm"){list($answer,$err)=$catalogue->saveClarifyForm($_REQUEST["import_id"],$_REQUEST["art_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err); }

//if ($_REQUEST["w"]=="loadCrossTablePreview"){ $GLOBALS['_RESULT'] = array("content"=>$catalogue->loadCrossTablePreview($_REQUEST["user_id"],$_REQUEST["status_check"]));}

if ($_REQUEST["w"]=="loadCrossTablePreview"){ $GLOBALS['_RESULT'] = array("content"=>$catalogue->loadCrossTablePreview($_REQUEST["user_id"],$_REQUEST["status_check"])["list"]);}

/*
 * BACK SUPPL
 * */

if ($_REQUEST["w"]=="loadBackSupplList"){ $GLOBALS['_RESULT'] = array("content"=>$suppl->loadBackSupplList());}
if ($_REQUEST["w"]=="showBackSupplCard"){ $GLOBALS['_RESULT'] = array("content"=>$suppl->showBackSupplCard($_REQUEST["back_suppl_id"]));}
if ($_REQUEST["w"]=="saveBackSuppl"){list($answer,$err,$back_suppl_id)=$suppl->saveBackSuppl($_REQUEST["back_suppl_id"],$_REQUEST["client_id"],$_REQUEST["client_seller"],$_REQUEST["income_id"],$_REQUEST["storage_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err,"back_suppl_id"=>$back_suppl_id); }

if ($_REQUEST["w"]=="addBackSupplStr"){ $GLOBALS['_RESULT'] = array("content"=>$suppl->addBackSupplStr($_REQUEST["back_suppl_id"], $_REQUEST["income_id"], $_REQUEST["client_id"], $_REQUEST["client_seller"]));}
if ($_REQUEST["w"]=="addBackSupplStrAmount"){ $GLOBALS['_RESULT'] = array("content"=>$suppl->addBackSupplStrAmount($_REQUEST["back_suppl_id"], $_REQUEST["income_id"], $_REQUEST["art_id"]));}
if ($_REQUEST["w"]=="saveBackSupplStr"){list($answer,$err)=$suppl->saveBackSupplStr($_REQUEST["back_suppl_id"],$_REQUEST["income_id"],$_REQUEST["art_id"],$_REQUEST["amount"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err); }
if ($_REQUEST["w"]=="clearBackSupplStr"){list($answer,$err)=$suppl->clearBackSupplStr($_REQUEST["back_suppl_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err); }
if ($_REQUEST["w"]=="dropBackSupplStr"){ $GLOBALS['_RESULT'] = array("content"=>$suppl->dropBackSupplStr($_REQUEST["back_suppl_id"],$_REQUEST["back_suppl_str_id"]));}

if ($_REQUEST["w"]=="showBackSupplClientList"){ $GLOBALS['_RESULT'] = array("content"=>$suppl->showBackSupplClientList($_REQUEST["back_suppl_id"]));}
if ($_REQUEST["w"]=="setBackSupplClient"){list($answer,$err,$client_name)=$suppl->setBackSupplClient($_REQUEST["back_suppl_id"],$_REQUEST["client_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err,"client_name"=>$client_name); }
if ($_REQUEST["w"]=="unlinkBackSupplClient"){list($answer,$err)=$suppl->unlinkBackSupplClient($_REQUEST["back_suppl_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err); }

if ($_REQUEST["w"]=="showBackSupplClientSellerList"){ $GLOBALS['_RESULT'] = array("content"=>$suppl->showBackSupplClientSellerList($_REQUEST["back_suppl_id"]));}
if ($_REQUEST["w"]=="setBackSupplClientSeller"){list($answer,$err,$client_seller_name)=$suppl->setBackSupplClientSeller($_REQUEST["back_suppl_id"],$_REQUEST["client_seller"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err,"client_seller_name"=>$client_seller_name); }
if ($_REQUEST["w"]=="unlinkBackSupplClientSeller"){list($answer,$err)=$suppl->unlinkBackSupplClientSeller($_REQUEST["back_suppl_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err); }

if ($_REQUEST["w"]=="findIncomeSuppl"){ $GLOBALS['_RESULT'] = array("content"=>$suppl->findIncomeSuppl($_REQUEST["art"]));}
if ($_REQUEST["w"]=="saveIncomeSuppl"){list($answer,$err,$income_name,$client_name,$client_seller_name)=$suppl->saveIncomeSuppl($_REQUEST["back_suppl_id"],$_REQUEST["income_id"],$_REQUEST["client_id"],$_REQUEST["client_seller"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err,"income_name"=>$income_name,"client_name"=>$client_name,"client_seller_name"=>$client_seller_name); }

if ($_REQUEST["w"]=="makeBackSupplStorsel"){list($answer,$err,$select_id)=$suppl->makeBackSupplStorsel($_REQUEST["back_suppl_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err,"select_id"=>$select_id); }

if ($_REQUEST["w"]=="saveFinishBackSuppl"){list($answer,$err)=$suppl->saveFinishBackSuppl($_REQUEST["back_suppl_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err); }

//if ($_REQUEST["w"]=="updateGroupCacheArts"){list($answer,$err)=$suppl->updateGroupCacheArts(); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err); }




//require_once RD.'/lib/example_class.php';$example=new example;
//
//if ($_REQUEST["w"]=="showModuleList"){ $GLOBALS['_RESULT'] = array("content"=>$example->showModuleList());}
//if ($_REQUEST["w"]=="showModuleCard"){ $GLOBALS['_RESULT'] = array("content"=>$example->showModuleCard($_REQUEST["module_id"]));}
//
//if ($_REQUEST["w"]=="saveModuleCard"){ list($answer,$err)=$example->saveModuleCard($_REQUEST["module_id"], $_REQUEST["module_name"], $_REQUEST["module_status"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
//if ($_REQUEST["w"]=="dropModuleCard"){ list($answer,$err)=$example->dropModuleCard($_REQUEST["module_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

