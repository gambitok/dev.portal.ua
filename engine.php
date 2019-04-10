<?php

$db = DbSingleton::getDb();$dbp = DbSingleton::getTokoDb();$slave = new slave;
require_once RD.'/lib/manual_class.php';$manual=new manual;$manualD=new manualD;$manualK=new manualK;$manualP=new manualP;$manualManager=new manualManager;
require_once RD.'/lib/gmanual_class.php';$gmanual=new gmanual;
require_once RD.'/lib/access_class.php';$access=new access;
require_once RD.'/lib/media_users_class.php';$media_users=new media_users;
require_once RD.'/lib/users_class.php';$users=new users;
require_once RD.'/lib/catalogue_class.php';$cat=new catalogue;
require_once RD.'/lib/clients_class.php';$cl=new clients;
require_once RD.'/lib/income_class.php';$income=new income;
require_once RD.'/lib/storage_class.php';$storage=new storage;
require_once RD.'/lib/undistribcells_class.php';$undistribcells=new undistribcells;
require_once RD.'/lib/tpoint_class.php';$tpoint=new tpoint;
require_once RD.'/lib/paybox_class.php';$paybox=new paybox;
require_once RD.'/lib/jmoving_class.php';$jmoving=new jmoving;
require_once RD.'/lib/kours_class.php';$kours=new kours;
require_once RD.'/lib/suppl_class.php';$suppl=new suppl;
require_once RD.'/lib/dp_class.php';$dp=new dp;
require_once RD.'/lib/storsel_class.php';$storsel=new storsel;
require_once RD.'/lib/sale_invoice_class.php';$sale_invoice=new sale_invoice;
require_once RD.'/lib/tax_invoice_class.php';$tax_invoice=new tax_invoice;
require_once RD.'/lib/jpay_class.php';$jpay=new jpay;
require_once RD.'/lib/back_clients_class.php';$back_clients=new back_clients;
require_once RD.'/lib/claim_class.php';$claim=new claim;
require_once RD.'/lib/import_rest_class.php';$import_rest=new import_rest;
require_once RD.'/lib/money_move_class.php';$money_move=new money_move;
require_once RD.'/lib/money_spend_class.php';$money_spend=new money_spend;
require_once RD.'/lib/suppl_orders_class.php';$suppl_orders=new suppl_orders;
require_once RD.'/lib/report_margin_class.php';$report_margin=new report_margin;
require_once (RD."/js/JsHttpRequest/JsHttpRequest.php");

$JsHttpRequest = new JsHttpRequest("windows-1251");
session_start();

$media_user_id=$media_users->get_media_user();

if ($_REQUEST["w"]=="setWindowSizeState"){ $GLOBALS['_RESULT'] = array("answer"=>$media_users->setWindowSizeState($_REQUEST["state"]));}
if ($_REQUEST["w"]=="authUser"){ $GLOBALS['_RESULT'] = array("answer"=>$media_users->authUserMedia($_REQUEST["phone"],$_REQUEST["pass"],$_REQUEST["remember"]));}
if ($_REQUEST["w"]=="logOutUser"){ $GLOBALS['_RESULT'] = array("answer"=>$media_users->logOutUser());}
if ($_REQUEST["w"]=="setCookieNavBarMini"){ $GLOBALS['_RESULT'] = array("answer"=>$media_users->setCookieNavBarMini());}
if ($_REQUEST["w"]=="getNBUKours"){ $GLOBALS['_RESULT'] = array("content"=>$income->getNBUKours($_REQUEST["data"],$_REQUEST["valuta"]));}

if ($media_user_id>0 && $media_user_id!=""){


if ($_REQUEST["w"]=="resetDbZero"){ list($answer,$err)=$users->resetDbZero(); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="showCatNewArticle"){ list($content,$header)=$cat->showCatNewArticle();$GLOBALS['_RESULT'] = array("content"=>$content,"header"=>$header);}
if ($_REQUEST["w"]=="showGoodsGroupLetterListSelect"){ $content=$cat->showGoodsGroupLetterListSelect($_REQUEST["prnt_id"]);$GLOBALS['_RESULT'] = array("content"=>$content);}
if ($_REQUEST["w"]=="loadRefinementList"){ $content=$cat->loadRefinementListSelect($_REQUEST["subgoods_group_id"]);$GLOBALS['_RESULT'] = array("content"=>$content);}
if ($_REQUEST["w"]=="findNewArtNextNum"){ $content=$cat->findNewArtNextNum($_REQUEST["brand"],$_REQUEST["group"],$_REQUEST["sub_group"],$_REQUEST["manuf"]);$GLOBALS['_RESULT'] = array("content"=>$content);}
if ($_REQUEST["w"]=="findNewArtID"){ $content=$cat->findNewArtID($_REQUEST["brand"]);$GLOBALS['_RESULT'] = array("content"=>$content);}
if ($_REQUEST["w"]=="checkCatalogueNewArt"){ list($answer,$err)=$cat->checkCatalogueNewArt($_REQUEST["num"],$_REQUEST["art_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="saveCatalogueNewArt"){ list($answer,$err,$art_id)=$cat->saveCatalogueNewArt($_REQUEST["num"],$_REQUEST["art_id"],$_REQUEST["brand"],$_REQUEST["sub_group"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err,"art_id"=>$art_id);}

if ($_REQUEST["w"]=="catalogue_article_search"){ list($header_list,$range_list,$brand_list)=$cat->showArticlesSearchList($_REQUEST["art"],"",$_REQUEST["search_type"]);$GLOBALS['_RESULT']=array("content"=>$range_list,"header"=>$header_list,"brand_list"=>$brand_list);}
if ($_REQUEST["w"]=="catalogue_article_search_doc"){list($header_list,$range_list,$brand_list)=$cat->showArticlesSearchListDoc($_REQUEST["art"],$_REQUEST["brand_id"],"",$_REQUEST["search_type"],$_REQUEST["doc_type"],$_REQUEST["doc_id"]);$GLOBALS['_RESULT']=array("content"=>$range_list,"header"=>$header_list,"brand_list"=>$brand_list);}
if ($_REQUEST["w"]=="doc_catalogue_article_search"){list($header_list,$range_list,$brand_list)=$cat->showArticlesSearchDocumentList($_REQUEST["art"],$_REQUEST["brand_id"],"",$_REQUEST["search_type"]);$GLOBALS['_RESULT']=array("content"=>$range_list,"header"=>$header_list,"brand_list"=>$brand_list);}


if ($_REQUEST["w"]=="catalogue_fil4_search"){ list($header_list,$range_list,$brand_list)=$cat->showArticlesFil4SearchList($_REQUEST["brand_id"],$_REQUEST["goods_group_id"]);$GLOBALS['_RESULT'] = array("content"=>$range_list,"header"=>$header_list);}
if ($_REQUEST["w"]=="catalogue_fil2_search"){ list($header_list,$range_list)=$cat->showArticlesFil2SearchList($_REQUEST["art_ids"]);$GLOBALS['_RESULT'] = array("content"=>$range_list,"header"=>$header_list);}

if ($_REQUEST["w"]=="loadModelSelectList"){ $GLOBALS['_RESULT'] = array("content"=>$cat->showModelSelectList($_REQUEST["mfa_id"],0));}
if ($_REQUEST["w"]=="loadFilterModificationSelectList"){ $GLOBALS['_RESULT'] = array("content"=>$cat->showFilterModificationSelectList($_REQUEST["mod_id"],0));}
if ($_REQUEST["w"]=="loadFilterGroupTreeList"){ list($content,$header)=$cat->loadFilterGroupTreeList($_REQUEST["typ_id"]);$GLOBALS['_RESULT'] = array("content"=>$content, "header"=>$header);}
if ($_REQUEST["w"]=="loadFilterGroupTreeListSide"){ list($content,$tree,$result_table)=$cat->loadFilterGroupTreeListSide($_REQUEST["typ_id"]);$GLOBALS['_RESULT'] = array("content"=>$content, "tree"=>$tree,"result_table"=>$result_table);}

if ($_REQUEST["w"]=="showCatFieldsViewForm"){ list($content,$header)=$cat->showCatFieldsViewForm(); $GLOBALS['_RESULT'] = array("content"=>$content,"header"=>$header);}
if ($_REQUEST["w"]=="saveCatalogueFieldsViewForm"){ list($answer,$err)=$cat->saveCatalogueFieldsViewForm($_REQUEST["kol_fields"],$_REQUEST["fl_id"],$_REQUEST["fl_ch"],$_REQUEST["table_key"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="showCatFieldsViewDocForm"){ list($content,$header)=$cat->showCatFieldsViewDocForm(); $GLOBALS['_RESULT'] = array("content"=>$content,"header"=>$header);}
if ($_REQUEST["w"]=="showCatalogueCard"){ list($content,$nr_display)=$cat->showCatalogueCard($_REQUEST["art_id"]); $GLOBALS['_RESULT'] = array("content"=>$content,"nr_display"=>$nr_display);}
if ($_REQUEST["w"]=="showArticleStorageCellsRestForm"){ list($content,$header)=$cat->showArticleStorageCellsRestForm($_REQUEST["art_id"]);$GLOBALS['_RESULT'] = array("content"=>$content, "header"=>$header);}
if ($_REQUEST["w"]=="showArticlePartitionsRestForm"){ list($content,$header)=$cat->showArticlePartitionsRestForm($_REQUEST["art_id"]);$GLOBALS['_RESULT'] = array("content"=>$content, "header"=>$header);}


if ($_REQUEST["w"]=="loadArticleCommets"){ $GLOBALS['_RESULT'] = array("content"=>$cat->loadArticleCommets($_REQUEST["art_id"]));}
if ($_REQUEST["w"]=="saveArticleComment"){ list($answer,$err)=$cat->saveArticleComment($_REQUEST["art_id"],$_REQUEST["comment"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="dropArticleComment"){ list($answer,$err)=$cat->dropArticleComment($_REQUEST["art_id"],$_REQUEST["cmt_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="loadArticleCDN"){ $GLOBALS['_RESULT'] = array("content"=>$cat->loadArticleCDN($_REQUEST["art_id"]));}
if ($_REQUEST["w"]=="articlesCDNDropFile"){ list($answer,$err)=$cat->articlesCDNDropFile($_REQUEST["art_id"],$_REQUEST["file_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="showArtilceGallery"){ list($content,$header)=$cat->showArtilceGallery($_REQUEST["art_id"],$_REQUEST["article_nr_displ"]); $GLOBALS['_RESULT'] = array("content"=>$content, "header"=>$header);}
if ($_REQUEST["w"]=="loadArticleFoto"){ $GLOBALS['_RESULT'] = array("content"=>$cat->loadArticleFoto($_REQUEST["art_id"]));}
if ($_REQUEST["w"]=="setArticlesFotoMain"){ list($answer,$err)=$cat->setArticlesFotoMain($_REQUEST["art_id"],$_REQUEST["file_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="articlesFotoDropFile"){ list($answer,$err)=$cat->articlesFotoDropFile($_REQUEST["art_id"],$_REQUEST["file_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="showGoodGroupTree"){ $GLOBALS['_RESULT'] = array("content"=>$cat->showGoodGroupTree($_REQUEST["goods_group_id"]));}
if ($_REQUEST["w"]=="unlinkCatalogueGoodGroup"){ list($answer,$error)=$cat->unlinkCatalogueGoodGroup($_REQUEST["art_id"],$_REQUEST["goods_group_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$error);}
if ($_REQUEST["w"]=="saveCatalogueGeneralInfo"){ list($answer,$err)=$cat->saveCatalogueGeneralInfo($_REQUEST["art_id"],$_REQUEST["article_nr_displ"],$_REQUEST["barcode"],$_REQUEST["inner_cross"],$_REQUEST["brand_id"],$_REQUEST["goods_group_id"],$_REQUEST["article_name"],$_REQUEST["article_info"],$_REQUEST["article_name_ukr"],$_REQUEST["unique_number"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}



if ($_REQUEST["w"]=="loadArticleParams"){ $GLOBALS['_RESULT'] = array("content"=>$cat->loadArticleParams($_REQUEST["art_id"]));}
if ($_REQUEST["w"]=="saveCatalogueParams"){ list($answer,$err)=$cat->saveCatalogueParams($_REQUEST["art_id"],$_REQUEST["goods_group_id"],$_REQUEST["template_id"],$_REQUEST["fields_type"],$_REQUEST["params_value"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="showCatalogueGoodGroupTemplateForm"){ list($content,$header)=$cat->showCatalogueGoodGroupTemplateForm($_REQUEST["art_id"],$_REQUEST["template_id"]); $GLOBALS['_RESULT'] = array("content"=>$content,"header"=>$header);}
	
if ($_REQUEST["w"]=="saveCatalogueParamsTemplate"){ list($answer,$err)=$cat->saveCatalogueParamsTemplate($_REQUEST["art_id"],$_REQUEST["goods_group_id"],$_REQUEST["template_id"],$_REQUEST["template_name"],$_REQUEST["template_caption"],$_REQUEST["template_descr"],$_REQUEST["cn"],$_REQUEST["params_id"],$_REQUEST["fields_type"],$_REQUEST["params_name"],$_REQUEST["params_type"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
	
if ($_REQUEST["w"]=="loadCatalogueGoodGroupTemplateParams"){ list($content,$scheme_list)=$cat->loadCatalogueGoodGroupTemplateParams($_REQUEST["art_id"],$_REQUEST["template_id"]); $GLOBALS['_RESULT'] = array("content"=>$content,"scheme_list"=>$scheme_list);}

if ($_REQUEST["w"]=="loadArticleScheme"){ $GLOBALS['_RESULT'] = array("content"=>$cat->loadArticleScheme($_REQUEST["template_id"]));}
if ($_REQUEST["w"]=="articlesSchemeDropFile"){ list($answer,$err)=$cat->articlesSchemeDropFile($_REQUEST["file_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}


if ($_REQUEST["w"]=="loadArticleLogistic"){ $GLOBALS['_RESULT'] = array("content"=>$cat->loadArticleLogistic($_REQUEST["art_id"]));}
if ($_REQUEST["w"]=="saveCatalogueLogistic"){ list($answer,$err)=$cat->saveCatalogueLogistic($_REQUEST["art_id"],$_REQUEST["index_pack"],$_REQUEST["height"],$_REQUEST["length"],$_REQUEST["width"],$_REQUEST["volume"],$_REQUEST["weight_netto"],$_REQUEST["weight_brutto"],$_REQUEST["necessary_amount_car"],$_REQUEST["units_id"], $_REQUEST["multiplicity_package"],$_REQUEST["shoulder_delivery"],$_REQUEST["general_quant"],$_REQUEST["work_pair"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}


if ($_REQUEST["w"]=="loadArticleZED"){ $GLOBALS['_RESULT'] = array("content"=>$cat->loadArticleZED($_REQUEST["art_id"]));}
if ($_REQUEST["w"]=="saveCatalogueZED"){ list($answer,$err)=$cat->saveCatalogueZED($_REQUEST["art_id"],$_REQUEST["country_id"],$_REQUEST["costums_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="loadArticlePricing"){ $GLOBALS['_RESULT'] = array("content"=>$cat->loadArticlePricing($_REQUEST["art_id"]));}
if ($_REQUEST["w"]=="loadPriceRatingTemplate"){ list($answer,$err,$min_markup,$kol_val,$rating)=$cat->loadPriceRatingTemplateStr($_REQUEST["template_id"]);$GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err,"min_markup"=>$min_markup,"kol_val"=>$kol_val,"rating"=>$rating);}
if ($_REQUEST["w"]=="saveArticlePriceRating"){ list($answer,$err,$user_name,$date)=$cat->saveArticlePriceRating($_REQUEST["art_id"],$_REQUEST["kol_elm"],$_REQUEST["template_id"],$_REQUEST["minMarkup"],$_REQUEST["prc"],$_REQUEST["prs"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err,"user_name"=>$user_name,"date"=>$date);}
if ($_REQUEST["w"]=="showArticlePriceRatingHistory"){ list($answer,$err,$content,$header)=$cat->showArticlePriceRatingHistory($_REQUEST["art_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err,"content"=>$content,"header"=>$header);}
if ($_REQUEST["w"]=="showArticleSales"){ list($answer,$err,$content,$header)=$cat->showArticleSales($_REQUEST["art_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err,"content"=>$content,"header"=>$header);}



if ($_REQUEST["w"]=="showCountryManual"){ $GLOBALS['_RESULT'] = array("content"=>$cat->showCountryManual($_REQUEST["country_id"]));}
if ($_REQUEST["w"]=="showCountryForm"){ list($content,$header)=$cat->showCountryForm($_REQUEST["country_id"]); $GLOBALS['_RESULT'] = array("content"=>$content,"header"=>$header);}
if ($_REQUEST["w"]=="saveCatalogueCountryForm"){ list($answer,$err)=$cat->saveCatalogueCountryForm($_REQUEST["id"],$_REQUEST["name"],$_REQUEST["alfa2"],$_REQUEST["alfa3"],$_REQUEST["duty"],$_REQUEST["risk"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="dropCountry"){ list($answer,$err)=$cat->dropCountry($_REQUEST["country_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}


if ($_REQUEST["w"]=="showCostumsManual"){ $GLOBALS['_RESULT'] = array("content"=>$cat->showCostumsManual($_REQUEST["costums_id"]));}
if ($_REQUEST["w"]=="showCostumsForm"){ list($content,$header)=$cat->showCostumsForm($_REQUEST["costums_id"]); $GLOBALS['_RESULT'] = array("content"=>$content,"header"=>$header);}
if ($_REQUEST["w"]=="saveCatalogueCostumsForm"){ list($answer,$err)=$cat->saveCatalogueCostumsForm($_REQUEST["id"],$_REQUEST["code"],$_REQUEST["name"],$_REQUEST["preferential_rate"],$_REQUEST["full_rate"],$_REQUEST["type_declaration"],$_REQUEST["sertification"],$_REQUEST["gos_standart"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="dropCostums"){ list($answer,$err)=$cat->dropCostums($_REQUEST["costums_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}



if ($_REQUEST["w"]=="loadArticleAnalogs"){ $GLOBALS['_RESULT'] = array("content"=>$cat->loadArticleAnalogs($_REQUEST["art_id"]));}
if ($_REQUEST["w"]=="showCatalogueAnalogForm"){ list($content,$header)=$cat->showCatalogueAnalogForm($_REQUEST["art_id"],$_REQUEST["kind"],$_REQUEST["relation"],$_REQUEST["search_number"]); $GLOBALS['_RESULT'] = array("content"=>$content,"header"=>$header);}
if ($_REQUEST["w"]=="saveCatalogueAnalogForm"){ list($answer,$err)=$cat->saveCatalogueAnalogForm($_REQUEST["art_id"],$_REQUEST["kind"],$_REQUEST["relation"],$_REQUEST["search_number"],$_REQUEST["display_nr"],$_REQUEST["brand_id"],$_REQUEST["art_id2"],$_REQUEST["index2"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="dropCatalogueAnalog"){ list($answer,$err)=$cat->dropCatalogueAnalog($_REQUEST["art_id"],$_REQUEST["kind"],$_REQUEST["relation"],$_REQUEST["search_number"],$_REQUEST["brand_id"],$_REQUEST["display_nr"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="clearCatalogueAnalogArticle"){ list($answer,$err)=$cat->clearCatalogueAnalogArticle($_REQUEST["art_id"],$_REQUEST["kind"],$_REQUEST["relation"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}


if ($_REQUEST["w"]=="viewArticleReservDocs"){ list($answer,$error,$content,$header)=$cat->viewArticleReservDocs($_REQUEST["art_id"],$_REQUEST["storage_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$error,"content"=>$content,"header"=>$header);}
if ($_REQUEST["w"]=="viewArticleCellsRest"){ list($answer,$error,$content,$header)=$cat->viewArticleCellsRest($_REQUEST["art_id"],$_REQUEST["storage_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$error,"content"=>$content,"header"=>$header);}


if ($_REQUEST["w"]=="showCatalogueAnalogIndexSearch"){ list($content,$header)=$cat->showCatalogueAnalogIndexSearch(); $GLOBALS['_RESULT'] = array("content"=>$content,"header"=>$header);}
if ($_REQUEST["w"]=="findCatalogueAnalogIndexSearch"){ $content=$cat->findCatalogueAnalogIndexSearch($_REQUEST["index"]); $GLOBALS['_RESULT'] = array("content"=>$content);}


if ($_REQUEST["w"]=="showCatalogueDonorForm"){ list($content,$header)=$cat->showCatalogueDonorForm($_REQUEST["art_id"]); $GLOBALS['_RESULT'] = array("content"=>$content,"header"=>$header);}
if ($_REQUEST["w"]=="showCatalogueDonorIndexSearch"){ list($content,$header)=$cat->showCatalogueDonorIndexSearch(); $GLOBALS['_RESULT'] = array("content"=>$content,"header"=>$header);}
if ($_REQUEST["w"]=="findCatalogueDonorIndexSearch"){ $content=$cat->findCatalogueDonorIndexSearch($_REQUEST["index"]); $GLOBALS['_RESULT'] = array("content"=>$content);}
if ($_REQUEST["w"]=="saveCatalogueDonorForm"){ list($answer,$err)=$cat->saveCatalogueDonorForm($_REQUEST["art_id"],$_REQUEST["display_nr"],$_REQUEST["art_id2"],$_REQUEST["ch"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}


if ($_REQUEST["w"]=="loadArticleAplicability"){ $GLOBALS['_RESULT'] = array("content"=>$cat->loadArticleAplicability($_REQUEST["art_id"]));}
if ($_REQUEST["w"]=="loadArticleAplicabilityModels"){ $GLOBALS['_RESULT'] = array("content"=>$cat->loadArticleAplicabilityModels($_REQUEST["art_id"],$_REQUEST["mfa_id"]));}
if ($_REQUEST["w"]=="unlinkArticleAplicabilityModel"){ $GLOBALS['_RESULT'] = array("answer"=>$cat->unlinkArticleAplicabilityModel($_REQUEST["art_id"],$_REQUEST["typ_id"]));}
if ($_REQUEST["w"]=="clearActicleAplicabilityManuf"){ $GLOBALS['_RESULT'] = array("answer"=>$cat->clearActicleAplicabilityManuf($_REQUEST["art_id"],$_REQUEST["mfa_id"]));}
if ($_REQUEST["w"]=="loadArticleAplicabilityNew"){list($content,$header)=$cat->loadArticleAplicabilityNew($_REQUEST["art_id"],$_REQUEST["index"]); $GLOBALS['_RESULT'] = array("content"=>$content,"header"=>$header); }
if ($_REQUEST["w"]=="loadAplicabilityModelList"){ $GLOBALS['_RESULT'] = array("content"=>$cat->loadAplicabilityModelList($_REQUEST["mfa_id"]));}
if ($_REQUEST["w"]=="loadAplicabilityModificationList"){ $GLOBALS['_RESULT'] = array("content"=>$cat->loadAplicabilityModificationList($_REQUEST["mod_id"]));}
if ($_REQUEST["w"]=="saveCatalogueAplicabilityForm"){ list($answer,$err)=$cat->saveCatalogueAplicabilityForm($_REQUEST["art_id"],$_REQUEST["comment"],$_REQUEST["typ_array"],$_REQUEST["str_array"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="showLaIdCommentForm"){list($content,$header)=$cat->showLaIdCommentForm($_REQUEST["art_id"],$_REQUEST["type_id"]); $GLOBALS['_RESULT'] = array("content"=>$content,"header"=>$header); }
if ($_REQUEST["w"]=="saveLaIdCommentForm"){ list($answer,$err)=$cat->saveLaIdCommentForm($_REQUEST["art_id"],$_REQUEST["type_id"],$_REQUEST["kol"],$_REQUEST["la_ids"],$_REQUEST["sorts"],$_REQUEST["types"],$_REQUEST["text_names"],$_REQUEST["texts"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="dropLaIdComment"){ list($answer,$err)=$cat->dropLaIdComment($_REQUEST["art_id"],$_REQUEST["type_id"],$_REQUEST["la_id"],$_REQUEST["sortf"],$_REQUEST["typef"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}



if ($_REQUEST["w"]=="newKoursCard"){ $GLOBALS['_RESULT'] = array("kours_id"=>$kours->newKoursCard());}
if ($_REQUEST["w"]=="showKoursCard"){ $content=$kours->showKoursCard($_REQUEST["kours_id"]); $GLOBALS['_RESULT'] = array("content"=>$content);}
if ($_REQUEST["w"]=="saveKoursForm"){ list($answer,$err)=$kours->saveKoursForm($_REQUEST["kours_id"],$_REQUEST["kours_value"],$_REQUEST["cash_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="showKoursList"){ $content=$kours->show_kours_list(); $GLOBALS['_RESULT'] = array("content"=>$content);}


//------------              J_SELECT     --------------------------


if ($_REQUEST["w"]=="show_storsel_search"){ $content=$storsel->show_storsel_list($_REQUEST["status"]);$GLOBALS['_RESULT']=array("content"=>$content);}
if ($_REQUEST["w"]=="showStorselCard"){ list($content,$doc_prefix_nom)=$storsel->showStorselCard($_REQUEST["select_id"]);  $GLOBALS['_RESULT'] = array("content"=>$content,"doc_prefix_nom"=>$doc_prefix_nom);}
if ($_REQUEST["w"]=="unlockStorselCard"){ $answer=$storsel->unlockStorselCard($_REQUEST["select_id"]);  $GLOBALS['_RESULT'] = array("answer"=>$answer);}

if ($_REQUEST["w"]=="closeStorselCard"){ $answer=$storsel->closeStorselCard($_REQUEST["select_id"]);  $GLOBALS['_RESULT'] = array("answer"=>$answer);}
if ($_REQUEST["w"]=="loadStorselCommetsLabel"){ list($kol,$label)=$storsel->labelCommentsCount($_REQUEST["select_id"]); $GLOBALS['_RESULT'] = array("label"=>$label);}
if ($_REQUEST["w"]=="loadStorselCommets"){ $GLOBALS['_RESULT'] = array("content"=>$storsel->loadStorselCommets($_REQUEST["select_id"]));}
if ($_REQUEST["w"]=="saveStorselComment"){ list($answer,$err)=$storsel->saveStorselComment($_REQUEST["select_id"],$_REQUEST["comment"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="dropStorselComment"){ list($answer,$err)=$storsel->dropStorselComment($_REQUEST["select_id"],$_REQUEST["cmt_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="collectStorsel"){ list($answer,$err)=$storsel->collectStorsel($_REQUEST["select_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="showStorselBarcodeForm"){ list($answer,$error,$content,$header)=$storsel->showStorselBarcodeForm($_REQUEST["select_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$error,"content"=>$content,"header"=>$header);}
if ($_REQUEST["w"]=="showStorselBugForm"){ list($answer,$error,$content,$header)=$storsel->showStorselBugForm($_REQUEST["select_id"],$_REQUEST["str_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$error,"content"=>$content,"header"=>$header);}
if ($_REQUEST["w"]=="saveStorselBugForm"){ 
	list($answer,$error,$row_id,$storage_select_bug_name,$dif_amount_barcode,$amount_bug,$amount_barcodes,$amount_barcodes_noscan)=$storsel->saveStorselBugForm($_REQUEST["select_id"],$_REQUEST["str_id"],$_REQUEST["storage_select_bug"],$_REQUEST["dif_amount_barcode"]); 
	$GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$error,"row_id"=>$row_id,"storage_select_bug_name"=>$storage_select_bug_name,"dif_amount_barcode"=>$dif_amount_barcode,"amount_bug"=>$amount_bug,"amount_barcode"=>$amount_barcodes,"amount_barcode_noscan"=>$amount_barcodes_noscan);}
if ($_REQUEST["w"]=="showStorselNoscanForm"){ list($answer,$error,$content,$header)=$storsel->showStorselNoscanForm($_REQUEST["select_id"],$_REQUEST["str_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$error,"content"=>$content,"header"=>$header);}
if ($_REQUEST["w"]=="saveStorselNoscanForm"){ 
	list($answer,$error,$row_id,$dif_amount_barcode,$amount_barcode_noscan)=$storsel->saveStorselNoscanForm($_REQUEST["select_id"],$_REQUEST["art_id"],$_REQUEST["str_id"],$_REQUEST["amount_barcode_noscan"]); 
	$GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$error,"row_id"=>$row_id,"dif_amount_barcode"=>$dif_amount_barcode,"amount_barcode_noscan"=>$amount_barcode_noscan);
}

if ($_REQUEST["w"]=="saveStorselBarcodeForm"){ list($answer,$error,$row_id,$amount_barcode,$dif_amount_barcode)=$storsel->saveStorselBarcodeForm($_REQUEST["select_id"],$_REQUEST["barcode"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$error,"row_id"=>$row_id,"amount_barcode"=>$amount_barcode,"dif_amount_barcode"=>$dif_amount_barcode);}
if ($_REQUEST["w"]=="finishStorselBarcodeForm"){ list($answer,$error,$id,$status_select)=$storsel->finishStorselBarcodeForm($_REQUEST["select_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$error,"status_select"=>$status_select);}


//--------------- CLAIM CLASS -----------------------------
if ($_REQUEST["w"]=="showClaimCard"){ $content=$claim->showClaimCard($_REQUEST["claim_id"]); $GLOBALS['_RESULT'] = array("content"=>$content);}
if ($_REQUEST["w"]=="closeClaimCard"){ $answer=$claim->closeClaimCard($_REQUEST["claim_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer);}
if ($_REQUEST["w"]=="loadClaimAct"){ $content=$claim->loadClaimAct($_REQUEST["claim_id"]); $GLOBALS['_RESULT'] = array("content"=>$content);}

//------------              J_MOVING     --------------------------


if ($_REQUEST["w"]=="show_jmoving_search"){ $content=$jmoving->show_jmoving_list($_REQUEST["status"]);$GLOBALS['_RESULT']=array("content"=>$content);}
	
if ($_REQUEST["w"]=="filterJmovingList"){ $content=$jmoving->filterJmovingList($_REQUEST["name"],$_REQUEST["data_from"],$_REQUEST["data_to"],$_REQUEST["status"]);$GLOBALS['_RESULT']=array("content"=>$content);}
	
	

if ($_REQUEST["w"]=="cancelJmoving"){ list($answer,$err)=$jmoving->cancelJmoving($_REQUEST["jmoving_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
	

if ($_REQUEST["w"]=="preNewJmovingCard"){ $GLOBALS['_RESULT'] = array("content"=>$jmoving->preNewJmovingCard());}
if ($_REQUEST["w"]=="newJmovingCard"){ $GLOBALS['_RESULT'] = array("jmoving_id"=>$jmoving->newJmovingCard($_REQUEST["type_id"]));}
if ($_REQUEST["w"]=="showJmovingCard"){ list($content,$doc_prefix_nom)=$jmoving->showJmovingCard($_REQUEST["jmoving_id"]);  $GLOBALS['_RESULT'] = array("content"=>$content,"doc_prefix_nom"=>$doc_prefix_nom);}
if ($_REQUEST["w"]=="showJmovingCardLocal"){ list($content,$doc_prefix_nom)=$jmoving->showJmovingCardLocal($_REQUEST["jmoving_id"]);  $GLOBALS['_RESULT'] = array("content"=>$content,"doc_prefix_nom"=>$doc_prefix_nom);}
if ($_REQUEST["w"]=="closeJmovingCard"){ $answer=$jmoving->closeJmovingCard($_REQUEST["jmoving_id"]);  $GLOBALS['_RESULT'] = array("answer"=>$answer);}

if ($_REQUEST["w"]=="showJmovingCardStr"){ list($content,$kol_art)=$jmoving->showJmovingStrList($_REQUEST["jmoving_id"],"",$_REQUEST["storage_id_to"]); $GLOBALS['_RESULT'] = array("content"=>$content);}
if ($_REQUEST["w"]=="showJmovingLocalCardStr"){ list($content,$kol_art)=$jmoving->showJmovingStrLocalList($_REQUEST["jmoving_id"],""); $GLOBALS['_RESULT'] = array("content"=>$content);}

if ($_REQUEST["w"]=="showJmovingDocumentList"){list($content,$header)=$jmoving->showJmovingDocumentList($_REQUEST["jmoving_id"],$_REQUEST["jmoving_op_id"],$_REQUEST["document_id"]); $GLOBALS['_RESULT'] = array("content"=>$content,"header"=>$header); }
if ($_REQUEST["w"]=="findJmovingDocumentsSearch"){$content=$jmoving->findJmovingDocumentsSearch($_REQUEST["jmoving_id"],$_REQUEST["jmoving_op_id"],$_REQUEST["s_nom"]); $GLOBALS['_RESULT'] = array("content"=>$content); }
if ($_REQUEST["w"]=="saveJmovingCard"){ list($answer,$err)=$jmoving->saveJmovingCard($_REQUEST["jmoving_id"],$_REQUEST["jmoving_op_id"],$_REQUEST["data"],$_REQUEST["storage_id_to"],$_REQUEST["cell_id_to"],$_REQUEST["comment"],$_REQUEST["kol_row"],$_REQUEST["idStr"],$_REQUEST["artIdStr"],$_REQUEST["article_nr_displStr"],$_REQUEST["brandIdStr"],$_REQUEST["storageIdFromStr"],$_REQUEST["cellIdFromStr"],$_REQUEST["amountStr"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="saveJmovingCardLocal"){ list($answer,$err)=$jmoving->saveJmovingCardLocal($_REQUEST["jmoving_id"],$_REQUEST["jmoving_op_id"],$_REQUEST["data"],$_REQUEST["storage_id_to"],$_REQUEST["comment"],$_REQUEST["kol_row"],$_REQUEST["idStr"],$_REQUEST["artIdStr"],$_REQUEST["cellIdToStr"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="loadJmovingStorageCellsSelectList"){ list($content,$cells_show)=$jmoving->showStorageCellsSelectList($_REQUEST["storage_id"],0); $GLOBALS['_RESULT'] = array("content"=>$content,"cells_show"=>$cells_show);}


if ($_REQUEST["w"]=="showJmovingLocalAutoCellForm"){ $GLOBALS['_RESULT'] = array("content"=>$jmoving->showJmovingLocalAutoCellForm($_REQUEST["jmoving_id"],$_REQUEST["storage_id_to"]));}
if ($_REQUEST["w"]=="saveJmovingLocalAutoCell"){ list($answer,$err,$no_row)=$jmoving->saveJmovingLocalAutoCell($_REQUEST["jmoving_id"],$_REQUEST["storage_id_to"],$_REQUEST["cell_id_from"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err,"no_row"=>$no_row);}

if ($_REQUEST["w"]=="showJmovingArticleSearchForm"){ $GLOBALS['_RESULT'] = array("content"=>$jmoving->showJmovingArticleSearchForm($_REQUEST["brand_id"],$_REQUEST["article_nr_displ"],$_REQUEST["jmoving_id"]));}
if ($_REQUEST["w"]=="showJmovingArticleLocalSearchForm"){ $GLOBALS['_RESULT'] = array("content"=>$jmoving->showJmovingArticleLocalSearchForm($_REQUEST["brand_id"],$_REQUEST["article_nr_displ"],$_REQUEST["jmoving_id"],$_REQUEST["storage_id_from"]));}

if ($_REQUEST["w"]=="catalogue_article_storage_rest_search"){list($range_list,$brand_list)=$jmoving->showArticlesSearchDocumentList($_REQUEST["art"],$_REQUEST["brand_id"],$_REQUEST["search_type"],$_REQUEST["jmoving_id"]);$GLOBALS['_RESULT']=array("content"=>$range_list,"brand_list"=>$brand_list);}
if ($_REQUEST["w"]=="catalogue_article_storage_rest_search_local"){list($range_list,$brand_list)=$jmoving->showArticlesLocalSearchDocumentList($_REQUEST["art"],$_REQUEST["brand_id"],$_REQUEST["search_type"],$_REQUEST["jmoving_id"],$_REQUEST["storage_id"]);$GLOBALS['_RESULT']=array("content"=>$range_list,"brand_list"=>$brand_list);}

if ($_REQUEST["w"]=="setArticleToSelectAmountJmoving"){ $GLOBALS['_RESULT'] = array("content"=>$jmoving->setArticleToSelectAmountJmoving($_REQUEST["art_id"],$_REQUEST["j_storage"]));}
if ($_REQUEST["w"]=="setArticleToSelectAmountJmovingLocal"){ $GLOBALS['_RESULT'] = array("content"=>$jmoving->setArticleToSelectAmountJmovingLocal($_REQUEST["art_id"],$_REQUEST["storage_id"]));}

if ($_REQUEST["w"]=="showJmovingArticleAmountChange"){ list($content,$article_nr_displ,$brand_name)=$jmoving->showJmovingArticleAmountChange($_REQUEST["art_id"],$_REQUEST["str_id"]);$GLOBALS['_RESULT'] = array("content"=>$content,"article_nr_displ"=>$article_nr_displ,"brand_name"=>$brand_name);}
if ($_REQUEST["w"]=="showJmovingArticleAmountLocalChange"){ list($content,$article_nr_displ,$brand_name)=$jmoving->showJmovingArticleAmountLocalChange($_REQUEST["art_id"],$_REQUEST["str_id"]);$GLOBALS['_RESULT'] = array("content"=>$content,"article_nr_displ"=>$article_nr_displ,"brand_name"=>$brand_name);}
if ($_REQUEST["w"]=="dropJmovingStr"){ list($answer,$err)=$jmoving->dropJmovingStr($_REQUEST["jmoving_id"],$_REQUEST["jmoving_str_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="dropJmovingLocalStr"){ list($answer,$err)=$jmoving->dropJmovingLocalStr($_REQUEST["jmoving_id"],$_REQUEST["jmoving_str_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="clearJmovingLocalAutoCellForm"){ list($answer,$err)=$jmoving->clearJmovingLocalAutoCellForm($_REQUEST["jmoving_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}



if ($_REQUEST["w"]=="setArticleToJmoving"){  
	list($answer,$err,$idStr,$amountRest,$weight,$volume,$empty_kol,$label_empty)=$jmoving->setArticleToJmoving($_REQUEST["jmoving_id"],$_REQUEST["idStr"],$_REQUEST["artIdStr"],$_REQUEST["article_nr_displStr"],$_REQUEST["brandIdStr"],$_REQUEST["storageIdFromStr"],$_REQUEST["cellIdFromStr"],$_REQUEST["amountStr"]); 
	$GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err,"idStr"=>$idStr,"amountRest"=>$amountRest,"weight"=>$weight,"volume"=>$volume,"empty_kol"=>$empty_kol,"label_empty"=>$label_empty);
}
if ($_REQUEST["w"]=="setArticleToJmovingLocal"){  
	list($answer,$err,$idStr,$amountRest,$weight,$volume,$empty_kol,$label_empty)=$jmoving->setArticleToJmovingLocal($_REQUEST["jmoving_id"],$_REQUEST["idStr"],$_REQUEST["artIdStr"],$_REQUEST["article_nr_displStr"],$_REQUEST["brandIdStr"],$_REQUEST["storageId"],$_REQUEST["cell_from_move"],$_REQUEST["cell_to_move"],$_REQUEST["amountStr"]); 
	$GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err,"idStr"=>$idStr,"amountRest"=>$amountRest,"weight"=>$weight,"volume"=>$volume,"empty_kol"=>$empty_kol,"label_empty"=>$label_empty);
}

if ($_REQUEST["w"]=="changeArticleToJmoving"){  
	list($answer,$err,$weight,$volume)=$jmoving->changeArticleToJmoving($_REQUEST["jmoving_id"],$_REQUEST["jmoving_str_id"],$_REQUEST["amount_change"]); 
	$GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err,"weight"=>$weight,"volume"=>$volume);
}
if ($_REQUEST["w"]=="changeArticleToJmovingLocal"){  
	list($answer,$err,$weight,$volume)=$jmoving->changeArticleToJmovingLocal($_REQUEST["jmoving_id"],$_REQUEST["jmoving_str_id"],$_REQUEST["amount_change"]); 
	$GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err,"weight"=>$weight,"volume"=>$volume);
}

if ($_REQUEST["w"]=="loadJmovingUnknownArticles"){ $GLOBALS['_RESULT'] = array("content"=>$jmoving->loadJmovingUnknownArticles($_REQUEST["jmoving_id"]));}
if ($_REQUEST["w"]=="checkJmovingUnStr"){ list($answer,$err)=$jmoving->checkJmovingUnStr($_REQUEST["jmoving_id"],$_REQUEST["art_id"],$_REQUEST["volume"],$_REQUEST["weight"],$_REQUEST["weight2"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="updateInformerJmovingUnknownArticles"){ list($kol_art_unknown,$label_art_unknown)=$jmoving->labelArtEmptyCount($_REQUEST["jmoving_id"],0); $GLOBALS['_RESULT'] = array("content"=>$label_art_unknown);}

if ($_REQUEST["w"]=="loadJmovingCDN"){ $GLOBALS['_RESULT'] = array("content"=>$jmoving->loadJmovingCDN($_REQUEST["jmoving_id"]));}
if ($_REQUEST["w"]=="jmovingCDNDropFile"){ list($answer,$err)=$jmoving->jmovingCDNDropFile($_REQUEST["jmoving_id"],$_REQUEST["file_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}


if ($_REQUEST["w"]=="loadJmovingCommetsLabel"){ list($kol,$label)=$jmoving->labelCommentsCount($_REQUEST["jmoving_id"]); $GLOBALS['_RESULT'] = array("label"=>$label);}
if ($_REQUEST["w"]=="loadJmovingCommets"){ $GLOBALS['_RESULT'] = array("content"=>$jmoving->loadJmovingCommets($_REQUEST["jmoving_id"]));}
if ($_REQUEST["w"]=="saveJmovingComment"){ list($answer,$err)=$jmoving->saveJmovingComment($_REQUEST["jmoving_id"],$_REQUEST["comment"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="dropJmovingComment"){ list($answer,$err)=$jmoving->dropJmovingComment($_REQUEST["jmoving_id"],$_REQUEST["cmt_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}



if ($_REQUEST["w"]=="startJmovingStorageSelect"){ list($answer,$err)=$jmoving->startJmovingStorageSelect($_REQUEST["jmoving_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="startJmovingStorageSelectLocal"){ list($answer,$err)=$jmoving->startJmovingStorageSelectLocal($_REQUEST["jmoving_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="makesJmovingStorageSelect"){ list($answer,$err)=$jmoving->makesJmovingStorageSelect($_REQUEST["jmoving_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="makesJmovingStorageSelectLocal"){ list($answer,$err)=$jmoving->makesJmovingStorageSelectLocal($_REQUEST["jmoving_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="clearJmovingStorageSelect"){ list($answer,$err)=$jmoving->clearJmovingStorageSelect($_REQUEST["jmoving_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="clearJmovingStorageSelectLocal"){ list($answer,$err)=$jmoving->clearJmovingStorageSelectLocal($_REQUEST["jmoving_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}


if ($_REQUEST["w"]=="loadJmovingStorageSelect"){ $GLOBALS['_RESULT'] = array("content"=>$jmoving->loadJmovingStorageSelect($_REQUEST["jmoving_id"],$_REQUEST["jmoving_status"]));}
if ($_REQUEST["w"]=="loadJmovingStorageSelectLocal"){ $GLOBALS['_RESULT'] = array("content"=>$jmoving->loadJmovingStorageSelectLocal($_REQUEST["jmoving_id"],$_REQUEST["jmoving_status"]));}
if ($_REQUEST["w"]=="viewJmovingStorageSelect"){ list($content,$header)=$jmoving->viewJmovingStorageSelect($_REQUEST["jmoving_id"],$_REQUEST["select_id"],$_REQUEST["jmoving_status"]); $GLOBALS['_RESULT'] = array("content"=>$content,"header"=>$header);}
if ($_REQUEST["w"]=="viewJmovingStorageSelectLocal"){ list($content,$header)=$jmoving->viewJmovingStorageSelectLocal($_REQUEST["jmoving_id"],$_REQUEST["select_id"],$_REQUEST["jmoving_status"]); $GLOBALS['_RESULT'] = array("content"=>$content,"header"=>$header);}
if ($_REQUEST["w"]=="dropJmovingStorageSelect"){ list($answer,$err)=$jmoving->dropJmovingStorageSelect($_REQUEST["jmoving_id"],$_REQUEST["select_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="dropJmovingStorageSelectLocal"){ list($answer,$err)=$jmoving->dropJmovingStorageSelectLocal($_REQUEST["jmoving_id"],$_REQUEST["select_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="cutJmovingStorage"){ list($answer,$err)=$jmoving->cutJmovingStorage($_REQUEST["jmoving_id"],$_REQUEST["select_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="collectJmovingStorageSelect"){ list($answer,$err)=$jmoving->collectJmovingStorageSelect($_REQUEST["jmoving_id"],$_REQUEST["select_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="collectJmovingStorageSelectLocal"){ list($answer,$err)=$jmoving->collectJmovingStorageSelectLocal($_REQUEST["jmoving_id"],$_REQUEST["select_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}


if ($_REQUEST["w"]=="showJmovingStorageSelectBarcodeForm"){ list($answer,$error,$content,$header)=$jmoving->showJmovingStorageSelectBarcodeForm($_REQUEST["jmoving_id"],$_REQUEST["select_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$error,"content"=>$content,"header"=>$header);}
if ($_REQUEST["w"]=="saveJmovingStorageSelectBarcodeForm"){ list($answer,$error,$row_id,$amount_barcode,$dif_amount_barcode)=$jmoving->saveJmovingStorageSelectBarcodeForm($_REQUEST["jmoving_id"],$_REQUEST["select_id"],$_REQUEST["barcode"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$error,"row_id"=>$row_id,"amount_barcode"=>$amount_barcode,"dif_amount_barcode"=>$dif_amount_barcode);}
if ($_REQUEST["w"]=="finishJmovingStorageSelectBarcodeForm"){ list($answer,$error,$id,$status_jmoving)=$jmoving->finishJmovingStorageSelectBarcodeForm($_REQUEST["jmoving_id"],$_REQUEST["select_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$error,"status_jmoving"=>$status_jmoving);}
if ($_REQUEST["w"]=="finishJmovingLocalStorageSelect"){ list($answer,$error,$id,$status_jmoving)=$jmoving->finishJmovingLocalStorageSelect($_REQUEST["jmoving_id"],$_REQUEST["select_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$error,"status_jmoving"=>$status_jmoving);}

if ($_REQUEST["w"]=="setJmovingSendTruck"){ list($answer,$error)=$jmoving->setJmovingSendTruck($_REQUEST["jmoving_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$error);}
if ($_REQUEST["w"]=="showJmovingStorageSelectBugForm"){ list($answer,$error,$content,$header)=$jmoving->showJmovingStorageSelectBugForm($_REQUEST["jmoving_id"],$_REQUEST["select_id"],$_REQUEST["str_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$error,"content"=>$content,"header"=>$header);}
if ($_REQUEST["w"]=="saveJmovingStorageSelectBugForm"){ 
	list($answer,$error,$row_id,$storage_select_bug_name,$dif_amount_barcode,$amount_bug,$amount_barcodes,$amount_barcodes_noscan)=$jmoving->saveJmovingStorageSelectBugForm($_REQUEST["jmoving_id"],$_REQUEST["select_id"],$_REQUEST["str_id"],$_REQUEST["storage_select_bug"],$_REQUEST["dif_amount_barcode"]); 
	$GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$error,"row_id"=>$row_id,"storage_select_bug_name"=>$storage_select_bug_name,"dif_amount_barcode"=>$dif_amount_barcode,"amount_bug"=>$amount_bug,"amount_barcode"=>$amount_barcodes,"amount_barcode_noscan"=>$amount_barcodes_noscan);}

if ($_REQUEST["w"]=="showJmovingStorageAcceptForm"){ list($answer,$error,$content,$header)=$jmoving->showJmovingStorageAcceptForm($_REQUEST["jmoving_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$error,"content"=>$content,"header"=>$header);}

if ($_REQUEST["w"]=="saveJmovingAcceptBarcodeForm"){ list($answer,$error,$row_id,$amount_barcode,$dif_amount_barcode)=$jmoving->saveJmovingAcceptBarcodeForm($_REQUEST["jmoving_id"],$_REQUEST["barcode"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$error,"row_id"=>$row_id,"amount_barcode"=>$amount_barcode,"dif_amount_barcode"=>$dif_amount_barcode);}

if ($_REQUEST["w"]=="finishJmovingAcceptForm"){ list($answer,$error)=$jmoving->finishJmovingAcceptForm($_REQUEST["jmoving_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$error);}

if ($_REQUEST["w"]=="finishJmovingLocalAcceptForm"){ list($answer,$error)=$jmoving->finishJmovingLocalAcceptForm($_REQUEST["jmoving_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$error);}

if ($_REQUEST["w"]=="showJmovingAcceptBugForm"){ list($answer,$error,$content,$header)=$jmoving->showJmovingAcceptBugForm($_REQUEST["jmoving_id"],$_REQUEST["str_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$error,"content"=>$content,"header"=>$header);}
if ($_REQUEST["w"]=="saveJmovingAcceptBugForm"){ 
	list($answer,$error,$row_id,$storage_bug_name,$dif_amount_barcode,$amount_bug)=$jmoving->saveJmovingAcceptBugForm($_REQUEST["jmoving_id"],$_REQUEST["str_id"],$_REQUEST["storage_select_bug"],$_REQUEST["dif_amount_barcode"]); 
	$GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$error,"row_id"=>$row_id,"storage_bug_name"=>$storage_bug_name,"dif_amount_barcode"=>$dif_amount_barcode,"amount_bug"=>$amount_bug);}

if ($_REQUEST["w"]=="showJmovingAcceptNoscanForm"){ list($answer,$error,$content,$header)=$jmoving->showJmovingAcceptNoscanForm($_REQUEST["jmoving_id"],$_REQUEST["str_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$error,"content"=>$content,"header"=>$header);}
if ($_REQUEST["w"]=="saveJmovingAcceptNoscanForm"){ 
	list($answer,$error,$row_id,$dif_amount_barcode,$amount_barcode_noscan)=$jmoving->saveJmovingAcceptNoscanForm($_REQUEST["jmoving_id"],$_REQUEST["art_id"],$_REQUEST["str_id"],$_REQUEST["amount_barcode_noscan"]); 
	$GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$error,"row_id"=>$row_id,"dif_amount_barcode"=>$dif_amount_barcode,"amount_barcode_noscan"=>$amount_barcode_noscan);
}
	
if ($_REQUEST["w"]=="showJmovingStorageSelectNoscanForm"){ list($answer,$error,$content,$header)=$jmoving->showJmovingStorageSelectNoscanForm($_REQUEST["jmoving_id"],$_REQUEST["select_id"],$_REQUEST["str_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$error,"content"=>$content,"header"=>$header);}
if ($_REQUEST["w"]=="saveJmovingStorageSelectNoscanForm"){ 
	list($answer,$error,$row_id,$dif_amount_barcode,$amount_barcode_noscan)=$jmoving->saveJmovingStorageSelectNoscanForm($_REQUEST["jmoving_id"],$_REQUEST["select_id"],$_REQUEST["art_id"],$_REQUEST["str_id"],$_REQUEST["amount_barcode_noscan"]); 
	$GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$error,"row_id"=>$row_id,"dif_amount_barcode"=>$dif_amount_barcode,"amount_barcode_noscan"=>$amount_barcode_noscan);
}

if ($_REQUEST["w"]=="printJmovingStorageSelect"){ 
	$content=$jmoving->printJmovingStorageSelect($_REQUEST["jmoving_id"],$_REQUEST["select_id"],$_REQUEST["jmoving_status"]); 
	$GLOBALS['_RESULT'] = array("content"=>$content,"header"=>$header);
}




//------------              MANUAL NEW ITEMS     --------------------------



if ($_REQUEST["w"]=="addNewCity"){ $GLOBALS['_RESULT'] = array("content"=>$manual->addNewCity($_REQUEST["region_id"],$_REQUEST["name"]));}


//------------              CLIENTS              --------------------------
if ($_REQUEST["w"]=="filterClientsList"){ $GLOBALS['_RESULT'] = array("content"=>$cl->show_clients_list($_REQUEST["client_id"],$_REQUEST["client_name"],$_REQUEST["phone"],$_REQUEST["email"],$_REQUEST["state_id"]));}
if ($_REQUEST["w"]=="newClientCard"){ $GLOBALS['_RESULT'] = array("client_id"=>$cl->newClientCard());}
if ($_REQUEST["w"]=="showClientCard"){ $GLOBALS['_RESULT'] = array("content"=>$cl->showClientCard($_REQUEST["client_id"]));}
if ($_REQUEST["w"]=="loadClientStateSelectList"){ $GLOBALS['_RESULT'] = array("content"=>$cl->loadStateSelectList($_REQUEST["country_id"],0));}
if ($_REQUEST["w"]=="loadStorageStateSelectList"){ $GLOBALS['_RESULT'] = array("content"=>$cl->loadStateSelectList($_REQUEST["country_id"],0));}
if ($_REQUEST["w"]=="loadClientRegionSelectList"){ $GLOBALS['_RESULT'] = array("content"=>$cl->loadRegionSelectList($_REQUEST["state_id"],0));}
if ($_REQUEST["w"]=="loadStorageRegionSelectList"){ $GLOBALS['_RESULT'] = array("content"=>$cl->loadRegionSelectList($_REQUEST["state_id"],0));}
if ($_REQUEST["w"]=="loadClientCitySelectList"){ $GLOBALS['_RESULT'] = array("content"=>$cl->loadCitySelectList($_REQUEST["region_id"],0));}
if ($_REQUEST["w"]=="loadStorageCitySelectList"){ $GLOBALS['_RESULT'] = array("content"=>$cl->loadCitySelectList($_REQUEST["region_id"],0));}

if ($_REQUEST["w"]=="saveClientGeneralInfo"){ list($answer,$err,$client_id)=$cl->saveClientGeneralInfo($_REQUEST["client_id"],$_REQUEST["org_type"],$_REQUEST["name"],$_REQUEST["full_name"],$_REQUEST["phone"],$_REQUEST["email"],$_REQUEST["parrent_id"],$_REQUEST["country_id"],$_REQUEST["state_id"],$_REQUEST["region_id"],$_REQUEST["city_id"],$_REQUEST["c_category_kol"],$_REQUEST["c_category"],$_REQUEST["user_category"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err,"client_id"=>$client_id);}

if ($_REQUEST["w"]=="loadClientConditions"){ $GLOBALS['_RESULT'] = array("content"=>$cl->loadClientConditions($_REQUEST["client_id"]));}
if ($_REQUEST["w"]=="saveClientConditions"){ list($answer,$err)=$cl->saveClientConditions($_REQUEST["client_id"],$_REQUEST["cash_id"],$_REQUEST["country_cash_id"],$_REQUEST["price_lvl"],$_REQUEST["margin_price_lvl"],$_REQUEST["price_suppl_lvl"],$_REQUEST["margin_price_suppl_lvl"],$_REQUEST["tpoint_id"],$_REQUEST["client_vat"],$_REQUEST["payment_delay"],$_REQUEST["payment_delay"],$_REQUEST["credit_limit"],$_REQUEST["credit_cash_id"],$_REQUEST["credit_return"],$_REQUEST["doc_type_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="loadClientSupplConditions"){ $GLOBALS['_RESULT'] = array("content"=>$cl->loadClientSupplConditions($_REQUEST["client_id"]));}
if ($_REQUEST["w"]=="saveClientSupplConditions"){ list($answer,$err)=$cl->saveClientSupplConditions($_REQUEST["client_id"],$_REQUEST["prepayment"],$_REQUEST["prepay_all"],$_REQUEST["prepay_summ"],$_REQUEST["prepay_type"],$_REQUEST["prepay_persent"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}


if ($_REQUEST["w"]=="loadClientDetails"){ $GLOBALS['_RESULT'] = array("content"=>$cl->loadClientDetails($_REQUEST["client_id"]));}
if ($_REQUEST["w"]=="saveClientDetails"){ list($answer,$err)=$cl->saveClientDetails($_REQUEST["client_id"],$_REQUEST["address_jur"],$_REQUEST["address_fakt"],$_REQUEST["edrpou"],$_REQUEST["svidotctvo"],$_REQUEST["vytjag"],$_REQUEST["vat"],$_REQUEST["mfo"],$_REQUEST["bank"],$_REQUEST["account"],$_REQUEST["not_resident"],$_REQUEST["nr_details"],$_REQUEST["buh_name"],$_REQUEST["buh_edrpou"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}


if ($_REQUEST["w"]=="loadClientCDN"){ $GLOBALS['_RESULT'] = array("content"=>$cl->loadClientCDN($_REQUEST["client_id"]));}
if ($_REQUEST["w"]=="clientsCDNDropFile"){ list($answer,$err)=$cl->clientsCDNDropFile($_REQUEST["client_id"],$_REQUEST["file_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}


if ($_REQUEST["w"]=="loadClientCommets"){ $GLOBALS['_RESULT'] = array("content"=>$cl->loadClientCommets($_REQUEST["client_id"]));}
if ($_REQUEST["w"]=="saveClientComment"){ list($answer,$err)=$cl->saveClientComment($_REQUEST["client_id"],$_REQUEST["comment"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="dropClientComment"){ list($answer,$err)=$cl->dropClientComment($_REQUEST["client_id"],$_REQUEST["cmt_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="loadClientsDetailsFile"){ $GLOBALS['_RESULT'] = array("content"=>$cl->loadClientsDetailsFile($_REQUEST["client_id"],$_REQUEST["file_type"]));}
if ($_REQUEST["w"]=="clientsDetailsDropFile"){ list($answer,$err)=$cl->clientsDetailsDropFile($_REQUEST["client_id"],$_REQUEST["file_type"],$_REQUEST["file_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="showClientsParrentTree"){ $GLOBALS['_RESULT'] = array("content"=>$cl->showClientsParrentTree($_REQUEST["client_id"],$_REQUEST["parrent_id"]));}
if ($_REQUEST["w"]=="unlinkClientsParrent"){ list($answer,$err)=$cl->unlinkClientsParrent($_REQUEST["client_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="unlinkClientsSubclient"){ list($answer,$err)=$cl->unlinkClientsSubclient($_REQUEST["client_id"],$_REQUEST["subclient_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="loadClientSubclients"){ list($x,$x,$list)=$cl->checkClientSubclients($_REQUEST["client_id"]); $GLOBALS['_RESULT'] = array("content"=>$list);}

if ($_REQUEST["w"]=="loadClientContacts"){ $GLOBALS['_RESULT'] = array("content"=>$cl->loadClientContacts($_REQUEST["client_id"]));}
if ($_REQUEST["w"]=="showClientContactForm"){ $GLOBALS['_RESULT'] = array("content"=>$cl->showClientContactForm($_REQUEST["client_id"],$_REQUEST["contact_id"]));}
if ($_REQUEST["w"]=="saveClientContactForm"){ list($answer,$err)=$cl->saveClientContactForm($_REQUEST["client_id"],$_REQUEST["contact_id"],$_REQUEST["contact_name"],$_REQUEST["contact_post"],$_REQUEST["contact_con_kol"],$_REQUEST["con_id"],$_REQUEST["sotc_cont"],$_REQUEST["contact_value"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="dropClientContact"){ list($answer,$err)=$cl->dropClientContact($_REQUEST["client_id"],$_REQUEST["contact_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}



if ($_REQUEST["w"]=="loadClientUsers"){ $GLOBALS['_RESULT'] = array("content"=>$cl->loadClientUsers($_REQUEST["client_id"]));}
if ($_REQUEST["w"]=="showClientUserForm"){ $GLOBALS['_RESULT'] = array("content"=>$cl->showClientUserForm($_REQUEST["client_id"],$_REQUEST["user_id"]));}
if ($_REQUEST["w"]=="saveClientUserForm"){ list($answer,$err)=$cl->saveClientUserForm($_REQUEST["client_id"],$_REQUEST["user_id"],$_REQUEST["user_name"],$_REQUEST["user_email"],$_REQUEST["user_phone"],$_REQUEST["user_pass"],$_REQUEST["user_main"],$_REQUEST["price_main"],$_REQUEST["export_main"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="dropClientUser"){ list($answer,$err)=$cl->dropClientUser($_REQUEST["client_id"],$_REQUEST["user_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}


if ($_REQUEST["w"]=="loadClientDocumentPrefix"){ $GLOBALS['_RESULT'] = array("content"=>$cl->loadClientDocumentPrefix($_REQUEST["client_id"]));}
if ($_REQUEST["w"]=="showClientDocumentPrefixForm"){ $GLOBALS['_RESULT'] = array("content"=>$cl->showClientDocumentPrefixForm($_REQUEST["client_id"],$_REQUEST["prefix_id"]));}
if ($_REQUEST["w"]=="saveClientDocumentPrefixForm"){ list($answer,$err)=$cl->saveClientDocumentPrefixForm($_REQUEST["client_id"],$_REQUEST["prefix_id"],$_REQUEST["doc_type_id"],$_REQUEST["prefix"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="dropClientDocumentPrefix"){ list($answer,$err)=$cl->dropClientDocumentPrefix($_REQUEST["client_id"],$_REQUEST["prefix_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}


if ($_REQUEST["w"]=="loadClientStorage"){ $GLOBALS['_RESULT'] = array("content"=>$cl->loadClientStorage($_REQUEST["client_id"]));}
if ($_REQUEST["w"]=="showClientStorageForm"){ list($content,$header)=$cl->showClientStorageForm($_REQUEST["client_id"],$_REQUEST["storage_id"]); $GLOBALS['_RESULT'] = array("content"=>$content, "header"=>$headrer);}
if ($_REQUEST["w"]=="saveClientStorageForm"){ list($answer,$err)=$cl->saveClientStorageForm($_REQUEST["client_id"],$_REQUEST["storage_id"],$_REQUEST["name"],$_REQUEST["email"],$_REQUEST["phone"],$_REQUEST["contact_person"],$_REQUEST["country"],$_REQUEST["state"],$_REQUEST["region"],$_REQUEST["city"],$_REQUEST["client_visible"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="dropClientStorage"){ list($answer,$err)=$cl->dropClientStorage($_REQUEST["client_id"],$_REQUEST["storage_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="showClientGeneralSaldoForm"){ list($content,$header)=$cl->showClientGeneralSaldoForm($_REQUEST["client_id"]);$GLOBALS['_RESULT'] = array("content"=>$content, "header"=>$header);}
if ($_REQUEST["w"]=="filterClientGeneralSaldoForm"){ list($range,$saldo_start,$saldo_end,$saldo_data_start,$saldo_data_end)=$cl->filterClientGeneralSaldoForm($_REQUEST["client_id"],$_REQUEST["from"],$_REQUEST["to"]);$GLOBALS['_RESULT'] = array("range"=>$range, "saldo_start"=>$saldo_start, "saldo_end"=>$saldo_end, "saldo_data_start"=>$saldo_data_start, "saldo_data_end"=>$saldo_data_end);}


//------------              END CLIENTS          --------------------------



if ($_REQUEST["w"]=="showSupplCard"){ $GLOBALS['_RESULT'] = array("content"=>$suppl->showSupplCard($_REQUEST["suppl_id"]));}
if ($_REQUEST["w"]=="loadSupplPrice"){ $GLOBALS['_RESULT'] = array("content"=>$suppl->loadSupplPrice($_REQUEST["suppl_id"]));}
if ($_REQUEST["w"]=="finishSupplPriceImport"){list($answer,$err)=$suppl->finishSupplPriceImport($_REQUEST["suppl_id"],$_REQUEST["start_row"],$_REQUEST["kol_cols"],$_REQUEST["cash"],$_REQUEST["kours_usd"],$_REQUEST["kours_eur"],$_REQUEST["cols"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err); }
if ($_REQUEST["w"]=="loadSupplIndex"){ $GLOBALS['_RESULT'] = array("content"=>$suppl->loadSupplIndex($_REQUEST["suppl_id"]));}
if ($_REQUEST["w"]=="finishSupplIndexImport"){list($answer,$err)=$suppl->finishSupplIndexImport($_REQUEST["suppl_id"],$_REQUEST["start_row"],$_REQUEST["kol_cols"],$_REQUEST["cols"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err); }
if ($_REQUEST["w"]=="loadSupplVat"){ $GLOBALS['_RESULT'] = array("content"=>$suppl->loadSupplVat($_REQUEST["suppl_id"]));}
if ($_REQUEST["w"]=="saveSupplVat"){list($answer,$err)=$suppl->saveSupplVat($_REQUEST["suppl_id"],$_REQUEST["price_in_vat"],$_REQUEST["show_in_vat"],$_REQUEST["price_add_vat"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err); }
if ($_REQUEST["w"]=="loadSupplOrderInfo"){ $GLOBALS['_RESULT'] = array("content"=>$suppl->loadSupplOrderInfo($_REQUEST["suppl_id"]));}
if ($_REQUEST["w"]=="saveSupplOrderInfo"){list($answer,$err)=$suppl->saveSupplOrderInfo($_REQUEST["suppl_id"],$_REQUEST["info"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err); }

//------------              STORAGE              --------------------------

if ($_REQUEST["w"]=="loadStorageList"){ $GLOBALS['_RESULT'] = array("content"=>$storage->show_storage_list());}
if ($_REQUEST["w"]=="newStorageCard"){ $GLOBALS['_RESULT'] = array("storage_id"=>$storage->newStorageCard());}
if ($_REQUEST["w"]=="showStorageCard"){ $GLOBALS['_RESULT'] = array("content"=>$storage->showStorageCard($_REQUEST["storage_id"]));}
if ($_REQUEST["w"]=="saveStorageGeneralInfo"){ list($answer,$err)=$storage->saveStorageGeneralInfo($_REQUEST["storage_id"],$_REQUEST["name"],$_REQUEST["full_name"],$_REQUEST["address"],$_REQUEST["storekeeper"],$_REQUEST["country_id"],$_REQUEST["state_id"],$_REQUEST["region_id"],$_REQUEST["city_id"],$_REQUEST["order_by"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="loadStorageDetails"){ $GLOBALS['_RESULT'] = array("content"=>$storage->loadStorageDetails($_REQUEST["storage_id"]));}
if ($_REQUEST["w"]=="showStorageDetailsForm"){ $GLOBALS['_RESULT'] = array("content"=>$storage->showStorageDetailsForm($_REQUEST["storage_id"],$_REQUEST["param_id"]));}
if ($_REQUEST["w"]=="saveStorageDetailsForm"){ list($answer,$err)=$storage->saveStorageDetailsForm($_REQUEST["storage_id"],$_REQUEST["storage_str_id"],$_REQUEST["param_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="dropStorageDetails"){ list($answer,$err)=$storage->dropStorageDetails($_REQUEST["storage_id"],$_REQUEST["storage_str_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="loadStorageCells"){ $GLOBALS['_RESULT'] = array("content"=>$storage->loadStorageCells($_REQUEST["storage_id"]));}
if ($_REQUEST["w"]=="loadStorageUsers"){ $GLOBALS['_RESULT'] = array("content"=>$storage->loadStorageUsers($_REQUEST["storage_id"]));}
if ($_REQUEST["w"]=="setUserStorage"){ list($answer,$err)=$storage->setUserStorage($_REQUEST["user_id"],$_REQUEST["storage_id"],$_REQUEST["status"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
	
if ($_REQUEST["w"]=="showStorageCellsForm"){ $GLOBALS['_RESULT'] = array("content"=>$storage->showStorageCellsForm($_REQUEST["storage_id"],$_REQUEST["cells_id"]));}
if ($_REQUEST["w"]=="saveStorageCellsForm"){ list($answer,$err)=$storage->saveStorageCellsForm($_REQUEST["storage_id"],$_REQUEST["cells_id"],$_REQUEST["str_kol"],$_REQUEST["cell_param_ids"],$_REQUEST["cell_vls"],$_REQUEST["def_ch"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="dropStorageCells"){ list($answer,$err)=$storage->dropStorageCells($_REQUEST["storage_id"],$_REQUEST["cells_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

//------------              END STORAGE          --------------------------


//------------				USERS				---------------------------


if ($_REQUEST["w"]=="newUsersCard"){ $GLOBALS['_RESULT'] = array("users_id"=>$users->newUsersCard());}
if ($_REQUEST["w"]=="showUsersList"){ $GLOBALS['_RESULT'] = array("content"=>$users->show_users_list());}
if ($_REQUEST["w"]=="showUsersCard"){ $GLOBALS['_RESULT'] = array("content"=>$users->showUsersCard($_REQUEST["users_id"]));}
if ($_REQUEST["w"]=="saveUsersGeneralInfo"){ list($answer,$err)=$users->saveUsersGeneralInfo($_REQUEST["users_id"],$_REQUEST["name"],$_REQUEST["post"],$_REQUEST["tpoint_id"],$_REQUEST["role_id"],$_REQUEST["phone2"],$_REQUEST["login"],$_REQUEST["pass"],$_REQUEST["status"],$_REQUEST["email"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="loadUsersAccess"){ $GLOBALS['_RESULT'] = array("content"=>$users->loadUsersAccess($_REQUEST["users_id"]));}
if ($_REQUEST["w"]=="loadUsersAccessCredit"){ $GLOBALS['_RESULT'] = array("content"=>$users->loadUsersAccessCredit($_REQUEST["users_id"]));}
if ($_REQUEST["w"]=="clearUsersAc�ess"){ list($answer,$err)=$users->clearUsersAc�ess($_REQUEST["users_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="showUsersAccessItemForm"){ $GLOBALS['_RESULT'] = array("content"=>$users->showUsersAccessItemForm($_REQUEST["users_id"],$_REQUEST["mf_id"]));}
if ($_REQUEST["w"]=="saveUsersAccessItemForm"){ list($answer,$err)=$users->saveUsersAccessItemForm($_REQUEST["users_id"],$_REQUEST["mf_id"],$_REQUEST["lvl_id"],$_REQUEST["file_access"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
	
if ($_REQUEST["w"]=="saveUsersAccessCredit"){ list($answer,$err)=$users->saveUsersAccessCredit($_REQUEST["users_id"],$_REQUEST["credit"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

//------------              PAY BOX          --------------------------
	
if ($_REQUEST["w"]=="newPayboxCard"){ $GLOBALS['_RESULT'] = array("paybox_id"=>$paybox->newPayboxCard());}
if ($_REQUEST["w"]=="loadPayboxList"){ $GLOBALS['_RESULT'] = array("content"=>$paybox->show_paybox_list());}
if ($_REQUEST["w"]=="showPayboxCard"){ $GLOBALS['_RESULT'] = array("content"=>$paybox->showPayboxCard($_REQUEST["paybox_id"]));}
if ($_REQUEST["w"]=="savePayboxGeneralInfo"){ list($answer,$err)=$paybox->savePayboxGeneralInfo($_REQUEST["paybox_id"],$_REQUEST["name"],$_REQUEST["full_name"],$_REQUEST["firm_id"],$_REQUEST["doc_type_id"],$_REQUEST["in_use"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="showPayboxClientList"){ $GLOBALS['_RESULT'] = array("content"=>$paybox->showPayboxClientList($_REQUEST["client_id"]));}


if ($_REQUEST["w"]=="loadPayboxWorkersSaldo"){ $GLOBALS['_RESULT'] = array("content"=>$paybox->loadPayboxWorkersSaldo($_REQUEST["paybox_id"]));}
if ($_REQUEST["w"]=="showPayboxWorkerSaldoJournal"){ $GLOBALS['_RESULT'] = array("content"=>$paybox->showPayboxWorkerSaldoJournal($_REQUEST["paybox_id"],$_REQUEST["user_id"],$_REQUEST["cash_id"]));}

if ($_REQUEST["w"]=="loadPayboxWorkers"){ $GLOBALS['_RESULT'] = array("content"=>$paybox->loadPayboxWorkers($_REQUEST["paybox_id"]));}
if ($_REQUEST["w"]=="showPayboxWorkerForm"){ $GLOBALS['_RESULT'] = array("content"=>$paybox->showPayboxWorkerForm($_REQUEST["paybox_id"],$_REQUEST["s_id"]));}
if ($_REQUEST["w"]=="savePayboxWorkerForm"){ list($answer,$err)=$paybox->savePayboxWorkerForm($_REQUEST["paybox_id"], $_REQUEST["s_id"], $_REQUEST["worker_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="dropPayboxWorker"){ list($answer,$err)=$paybox->dropPayboxWorker($_REQUEST["paybox_id"],$_REQUEST["s_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="dropPaybox"){ list($answer,$err)=$paybox->dropPaybox($_REQUEST["paybox_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
//------------              TPOINT          --------------------------

if ($_REQUEST["w"]=="newTpointCard"){ $GLOBALS['_RESULT'] = array("tpoint_id"=>$tpoint->newTpointCard());}
if ($_REQUEST["w"]=="showTpointList"){ $GLOBALS['_RESULT'] = array("content"=>$tpoint->show_tpoint_list());}
if ($_REQUEST["w"]=="showTpointCard"){ $GLOBALS['_RESULT'] = array("content"=>$tpoint->showTpointCard($_REQUEST["tpoint_id"]));}
if ($_REQUEST["w"]=="saveTpointGeneralInfo"){ list($answer,$err)=$tpoint->saveTpointGeneralInfo($_REQUEST["tpoint_id"],$_REQUEST["name"],$_REQUEST["full_name"],$_REQUEST["address"],$_REQUEST["chief"],$_REQUEST["country_id"],$_REQUEST["state_id"],$_REQUEST["region_id"],$_REQUEST["city_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="loadTpointStorage"){ $GLOBALS['_RESULT'] = array("content"=>$tpoint->loadTpointStorage($_REQUEST["tpoint_id"]));}
if ($_REQUEST["w"]=="showTpointStorageForm"){ $GLOBALS['_RESULT'] = array("content"=>$tpoint->showTpointStorageForm($_REQUEST["tpoint_id"],$_REQUEST["s_id"]));}
if ($_REQUEST["w"]=="saveTpointStorageForm"){ list($answer,$err)=$tpoint->saveTpointStorageForm($_REQUEST["tpoint_id"],$_REQUEST["s_id"],$_REQUEST["storage_id"],$_REQUEST["local"],$_REQUEST["default"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="dropTpointStorage"){ list($answer,$err)=$tpoint->dropTpointStorage($_REQUEST["tpoint_id"],$_REQUEST["s_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="loadTpointClients"){ $GLOBALS['_RESULT'] = array("content"=>$tpoint->loadTpointClients($_REQUEST["tpoint_id"]));}
if ($_REQUEST["w"]=="showTpointClientsForm"){ $GLOBALS['_RESULT'] = array("content"=>$tpoint->showTpointClientsForm($_REQUEST["tpoint_id"],$_REQUEST["s_id"]));}
if ($_REQUEST["w"]=="showTpointClientList"){ $GLOBALS['_RESULT'] = array("content"=>$tpoint->showTpointClientList($_REQUEST["client_id"]));}
if ($_REQUEST["w"]=="saveTpointClientsForm"){ list($answer,$err)=$tpoint->saveTpointClientsForm($_REQUEST["tpoint_id"],$_REQUEST["s_id"],$_REQUEST["client_id"],$_REQUEST["sale_type"],$_REQUEST["tax_credit"],$_REQUEST["tax_inform"],$_REQUEST["in_use"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="dropTpointClients"){ list($answer,$err)=$tpoint->dropTpointClients($_REQUEST["tpoint_id"],$_REQUEST["s_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="loadTpointWorkers"){ $GLOBALS['_RESULT'] = array("content"=>$tpoint->loadTpointWorkers($_REQUEST["tpoint_id"]));}
if ($_REQUEST["w"]=="showTpointWorkersForm"){ $GLOBALS['_RESULT'] = array("content"=>$tpoint->showTpointWorkersForm($_REQUEST["tpoint_id"],$_REQUEST["s_id"]));}
if ($_REQUEST["w"]=="saveTpointWorkersForm"){ list($answer,$err)=$tpoint->saveTpointWorkersForm($_REQUEST["tpoint_id"],$_REQUEST["s_id"],$_REQUEST["worker_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="dropTpointWorkers"){ list($answer,$err)=$tpoint->dropTpointWorkers($_REQUEST["tpoint_id"],$_REQUEST["s_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}


if ($_REQUEST["w"]=="loadTpointDeliveryTime"){ $GLOBALS['_RESULT'] = array("content"=>$tpoint->loadTpointDeliveryTime($_REQUEST["tpoint_id"]));}
if ($_REQUEST["w"]=="showTpointDeliveryForm"){ $GLOBALS['_RESULT'] = array("content"=>$tpoint->showTpointDeliveryForm($_REQUEST["tpoint_id"],$_REQUEST["s_id"]));}
if ($_REQUEST["w"]=="saveTpointDeliveryForm"){ list($answer,$err)=$tpoint->saveTpointDeliveryForm($_REQUEST["tpoint_id"],$_REQUEST["s_id"],$_REQUEST["storage_id"],$_REQUEST["week_day"],$_REQUEST["time_from"],$_REQUEST["time_to"],$_REQUEST["delivery_days"],$_REQUEST["giveout_time"],$_REQUEST["time_from_del"],$_REQUEST["time_to_del"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="dropTpointDelivery"){ list($answer,$err)=$tpoint->dropTpointDelivery($_REQUEST["tpoint_id"],$_REQUEST["s_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="loadTpointSupplDeliveryTime"){ $GLOBALS['_RESULT'] = array("content"=>$tpoint->loadTpointSupplDeliveryTime($_REQUEST["tpoint_id"]));}
if ($_REQUEST["w"]=="showTpointSupplDeliveryForm"){ $GLOBALS['_RESULT'] = array("content"=>$tpoint->showTpointSupplDeliveryForm($_REQUEST["tpoint_id"],$_REQUEST["s_id"]));}
if ($_REQUEST["w"]=="loadTpointSupplStorageSelectList"){ $GLOBALS['_RESULT'] = array("content"=>$tpoint->showTpointSupplStorageSelectList($_REQUEST["suppl_id"]));}
if ($_REQUEST["w"]=="saveTpointSupplDeliveryForm"){ list($answer,$err)=$tpoint->saveTpointSupplDeliveryForm($_REQUEST["tpoint_id"],$_REQUEST["s_id"],$_REQUEST["suppl_id"],$_REQUEST["suppl_storage_id"],$_REQUEST["week_day"],$_REQUEST["time_from"],$_REQUEST["time_to"],$_REQUEST["delivery_days"],$_REQUEST["giveout_time"],$_REQUEST["time_from_del"],$_REQUEST["time_to_del"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="dropTpointSupplDelivery"){ list($answer,$err)=$tpoint->dropTpointSupplDelivery($_REQUEST["tpoint_id"],$_REQUEST["s_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="loadTpointSupplFm"){ $GLOBALS['_RESULT'] = array("content"=>$tpoint->loadTpointSupplFm($_REQUEST["tpoint_id"]));}
if ($_REQUEST["w"]=="showTpointSupplFmForm"){ $GLOBALS['_RESULT'] = array("content"=>$tpoint->showTpointSupplFmForm($_REQUEST["tpoint_id"],$_REQUEST["s_id"]));}

if ($_REQUEST["w"]=="saveTpointSupplFmForm"){ list($answer,$err)=$tpoint->saveTpointSupplFmForm($_REQUEST["tpoint_id"],$_REQUEST["s_id"],$_REQUEST["suppl_id"],$_REQUEST["suppl_storage_id"],$_REQUEST["price_rating_id"],$_REQUEST["price_from"],$_REQUEST["price_to"],$_REQUEST["margin"],$_REQUEST["delivery"],$_REQUEST["margin2"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}


if ($_REQUEST["w"]=="loadTpointSupplStorage"){ $GLOBALS['_RESULT'] = array("content"=>$tpoint->loadTpointSupplStorage($_REQUEST["tpoint_id"]));}
if ($_REQUEST["w"]=="showTpointSupplStorageForm"){ $GLOBALS['_RESULT'] = array("content"=>$tpoint->showTpointSupplStorageForm($_REQUEST["tpoint_id"],$_REQUEST["s_id"]));}
if ($_REQUEST["w"]=="loadSupplStorageList"){ $GLOBALS['_RESULT'] = array("content"=>$tpoint->loadSupplStorageList($_REQUEST["suppl_id"],"0"));}
if ($_REQUEST["w"]=="saveTpointSupplStorageForm"){ list($answer,$err)=$tpoint->saveTpointSupplStorageForm($_REQUEST["tpoint_id"],$_REQUEST["s_id"],$_REQUEST["storage_id"],$_REQUEST["suppl_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="dropTpointSupplStorageForm"){ list($answer,$err)=$tpoint->dropTpointSupplStorageForm($_REQUEST["tpoint_id"],$_REQUEST["s_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}


if ($_REQUEST["w"]=="loadTpointPayBox"){ $GLOBALS['_RESULT'] = array("content"=>$tpoint->loadTpointPayBox($_REQUEST["tpoint_id"]));}
if ($_REQUEST["w"]=="showTpointPayBoxForm"){ $GLOBALS['_RESULT'] = array("content"=>$tpoint->showTpointPayBoxForm($_REQUEST["tpoint_id"],$_REQUEST["s_id"]));}
if ($_REQUEST["w"]=="showTpointPayBoxClientList"){ $GLOBALS['_RESULT'] = array("content"=>$tpoint->showTpointPayBoxClientList($_REQUEST["tpoint_id"],$_REQUEST["client_id"]));}
if ($_REQUEST["w"]=="saveTpointPayBoxForm"){ list($answer,$err)=$tpoint->saveTpointPayBoxForm($_REQUEST["tpoint_id"],$_REQUEST["s_id"],$_REQUEST["client_id"],$_REQUEST["name"],$_REQUEST["in_use"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="dropTpointPayBox"){ list($answer,$err)=$tpoint->dropTpointPayBox($_REQUEST["tpoint_id"],$_REQUEST["s_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

//------------              END TPOINT          --------------------------



//------------              DP           	    --------------------------
if ($_REQUEST["w"]=="show_dp_search"){ $GLOBALS['_RESULT'] = array("content"=>$dp->show_dp_list($_REQUEST["status"]));}
if ($_REQUEST["w"]=="newDpCard"){ $GLOBALS['_RESULT'] = array("dp_id"=>$dp->newDpCard());}
if ($_REQUEST["w"]=="showDpCard"){ list($content,$doc_prefix_nom)=$dp->showDpCard($_REQUEST["dp_id"]); $GLOBALS['_RESULT'] = array("content"=>$content,"doc_prefix_nom"=>$doc_prefix_nom);}
if ($_REQUEST["w"]=="showDpCardStr"){ list($content,$kol_rw)=$dp->showDpStrList($_REQUEST["dp_id"],""); $GLOBALS['_RESULT'] = array("content"=>$content,"kol_rw"=>$kol_rw);}

if ($_REQUEST["w"]=="closeDpCard"){ $answer=$dp->closeDpCard($_REQUEST["dp_id"]);  $GLOBALS['_RESULT'] = array("answer"=>$answer);}
if ($_REQUEST["w"]=="unlockDpCard"){ $answer=$dp->unlockDpCard($_REQUEST["dp_id"]);  $GLOBALS['_RESULT'] = array("answer"=>$answer);}
if ($_REQUEST["w"]=="clearDpStr"){ list($answer,$err)=$dp->clearDpStr($_REQUEST["dp_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="dropDpStr"){ list($answer,$err,$dp_summ)=$dp->dropDpStr($_REQUEST["dp_id"],$_REQUEST["dp_str_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err,"dp_summ"=>$dp_summ);}
if ($_REQUEST["w"]=="saveDpCard"){ list($answer,$err)=$dp->saveDpCard($_REQUEST["dp_id"],$_REQUEST["data_pay"],$_REQUEST["cash_id"],$_REQUEST["dp_summ"],$_REQUEST["doc_type_id"],$_REQUEST["tpoint_id"],$_REQUEST["client_id"],$_REQUEST["client_conto_id"],$_REQUEST["delivery_type_id"],$_REQUEST["carrier_id"],$_REQUEST["delivery_address"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="saveDpCardData"){ list($answer,$err)=$dp->saveDpCardData($_REQUEST["dp_id"],$_REQUEST["cash_id"],$_REQUEST["frm"],$_REQUEST["tto"],$_REQUEST["idStr"],$_REQUEST["artIdStr"],$_REQUEST["article_nr_displStr"],$_REQUEST["brandIdStr"],$_REQUEST["amountStr"],$_REQUEST["priceStr"],$_REQUEST["priceEndStr"],$_REQUEST["discountStr"],$_REQUEST["summStr"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="setDpClient"){ list($answer,$err)=$dp->setDpClient($_REQUEST["dp_id"],$_REQUEST["client_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}



if ($_REQUEST["w"]=="showSupplToLocalChangeForm"){ list($content,$header)=$dp->showSupplToLocalChangeForm($_REQUEST["art_id"],$_REQUEST["brand_id"],$_REQUEST["dp_id"],$_REQUEST["dp_str_id"]); $GLOBALS['_RESULT'] = array("content"=>$content,"header"=>$header);}

if ($_REQUEST["w"]=="saveDpSupplToLocalChangeForm"){ list($answer,$err)=$dp->saveDpSupplToLocalChangeForm($_REQUEST["dp_id"],$_REQUEST["dp_str_id"],$_REQUEST["art_id"],$_REQUEST["amount"],$_REQUEST["price"],$_REQUEST["storage_id"],$_REQUEST["cell_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

	
if ($_REQUEST["w"]=="updateDpStrPrice"){ list($answer,$err)=$dp->updateDpStrPrice($_REQUEST["dp_id"],$_REQUEST["str_id"],$_REQUEST["discount"],$_REQUEST["cash_id"],$_REQUEST["price_end"],$_REQUEST["summ"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="changeDpCash"){ list($answer,$err)=$dp->changeDpCash($_REQUEST["dp_id"],$_REQUEST["cash_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}


if ($_REQUEST["w"]=="loadDpCDN"){ $GLOBALS['_RESULT'] = array("content"=>$dp->loadDpCDN($_REQUEST["dp_id"]));}
if ($_REQUEST["w"]=="dpCDNDropFile"){ list($answer,$err)=$dp->dpCDNDropFile($_REQUEST["dp_id"],$_REQUEST["file_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="loadDpCommetsLabel"){ list($kol,$label)=$dp->labelCommentsCount($_REQUEST["dp_id"]); $GLOBALS['_RESULT'] = array("label"=>$label);}
if ($_REQUEST["w"]=="loadDpCommets"){ $GLOBALS['_RESULT'] = array("content"=>$dp->loadDpCommets($_REQUEST["dp_id"]));}
if ($_REQUEST["w"]=="saveDpComment"){ list($answer,$err)=$dp->saveDpComment($_REQUEST["dp_id"],$_REQUEST["comment"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="dropDpComment"){ list($answer,$err)=$dp->dropDpComment($_REQUEST["dp_id"],$_REQUEST["cmt_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="loadDpClientContoList"){ $GLOBALS['_RESULT'] = array("content"=>$dp->getClientContoSelectList($_REQUEST["client_id"],0));}
if ($_REQUEST["w"]=="showDpClientList"){ $GLOBALS['_RESULT'] = array("content"=>$dp->showDpClientList($_REQUEST["client_id"]));}
if ($_REQUEST["w"]=="filterDpClientsList"){ $content=$dp->filterDpClientsList($_REQUEST["sel_id"],$_REQUEST["client_id"],$_REQUEST["client_name"],$_REQUEST["phone"],$_REQUEST["email"],$_REQUEST["state_id"]); $GLOBALS['_RESULT'] = array("content"=>$content);}
if ($_REQUEST["w"]=="unlinkDpClient"){ list($answer,$err)=$dp->unlinkDpClient($_REQUEST["dp_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="showDpTpointList"){ $GLOBALS['_RESULT'] = array("content"=>$dp->showDpTpointList($_REQUEST["tpoint_id"]));}
if ($_REQUEST["w"]=="unlinkDpTpoint"){ list($answer,$err)=$dp->unlinkDpTpoint($_REQUEST["dp_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="showDpArticleSearchForm"){ $GLOBALS['_RESULT'] = array("content"=>$dp->showDpArticleSearchForm($_REQUEST["art_id"],$_REQUEST["brand_id"],$_REQUEST["article_nr_displ"],$_REQUEST["dp_id"],$_REQUEST["tpoint_id"]));}
if ($_REQUEST["w"]=="catalogue_article_storage_rest_search_dp"){list($range_list,$brand_list)=$dp->showArticlesSearchDocumentList($_REQUEST["art"],$_REQUEST["brand_id"],$_REQUEST["search_type"],$_REQUEST["dp_id"],$_REQUEST["tpoint_id"]);$GLOBALS['_RESULT']=array("content"=>$range_list,"brand_list"=>$brand_list);}
if ($_REQUEST["w"]=="setArticleToSelectAmountDp"){ $GLOBALS['_RESULT'] = array("content"=>$dp->setArticleToSelectAmountDp($_REQUEST["art_id"],$_REQUEST["dp_id"]));}
if ($_REQUEST["w"]=="showDpAmountInputWindow"){ $GLOBALS['_RESULT'] = array("content"=>$dp->showDpAmountInputWindow($_REQUEST["art_id"],$_REQUEST["dp_id"],$_REQUEST["storage_id"]));}
if ($_REQUEST["w"]=="setArticleToDp"){ list($answer,$err,$idS,$weight,$volume,$empty_kol,$label_empty,$dp_summ)=$dp->setArticleToDp($_REQUEST["dp_id"],$_REQUEST["tpoint_id"],$_REQUEST["artIdStr"],$_REQUEST["article_nr_displStr"],$_REQUEST["brandIdStr"],$_REQUEST["storageIdStr"],$_REQUEST["amountStr"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err,"weight"=>$weight,"volume"=>$volume,"empty_kol"=>$empty_kol,"label_empty"=>$label_empty,"dp_summ"=>$dp_summ); }

if ($_REQUEST["w"]=="showDpSupplAmountInputWindow"){ $GLOBALS['_RESULT'] = array("content"=>$dp->showDpSupplAmountInputWindow($_REQUEST["art_id"],$_REQUEST["article_nr_displ"],$_REQUEST["brand_id"],$_REQUEST["dp_id"],$_REQUEST["suppl_id"],$_REQUEST["suppl_storage_id"],$_REQUEST["price"]));}
if ($_REQUEST["w"]=="setArticleSupplToDp"){ list($answer,$err,$idS,$weight,$volume,$empty_kol,$label_empty,$dp_summ)=$dp->setArticleSupplToDp($_REQUEST["dp_id"],$_REQUEST["tpoint_id"],$_REQUEST["art_id"],$_REQUEST["article_nr_displ"],$_REQUEST["brandId"],$_REQUEST["supplId"],$_REQUEST["supplStorageId"],$_REQUEST["amountStr"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err,"weight"=>$weight,"volume"=>$volume,"empty_kol"=>$empty_kol,"label_empty"=>$label_empty,"dp_summ"=>$dp_summ); }

if ($_REQUEST["w"]=="showArticleSearchDocumentForm"){ $GLOBALS['_RESULT'] = array("content"=>$cat->showArticleSearchDocumentForm($_REQUEST["brand_id"],$_REQUEST["article_nr_displ"],$_REQUEST["doc_type"], $_REQUEST["doc_id"]));}
if ($_REQUEST["w"]=="showSupplStorageSelectWindow"){ $GLOBALS['_RESULT'] = array("content"=>$cat->showSupplStorageSelectWindow($_REQUEST["art_id"],$_REQUEST["article_nr_displ"],$_REQUEST["doc_type"],$_REQUEST["doc_id"]));}

if ($_REQUEST["w"]=="startDpExecute"){ list($answer,$err,$suppl_ex)=$dp->startDpExecute($_REQUEST["dp_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err,"suppl_ex"=>$suppl_ex); }
if ($_REQUEST["w"]=="makeDpJmovingStorselPreorder"){ list($answer,$err)=$dp->makeDpJmovingStorselPreorder($_REQUEST["dp_id"],$_REQUEST["local"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err); }
if ($_REQUEST["w"]=="makeDpJmovingStorselPreorder2"){ list($answer,$err)=$dp->makeDpJmovingStorselPreorder2($_REQUEST["dp_id"],$_REQUEST["local"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err); }
if ($_REQUEST["w"]=="loadDpStorsel"){ $GLOBALS['_RESULT'] = array("content"=>$dp->loadDpStorsel($_REQUEST["dp_id"]));}
if ($_REQUEST["w"]=="viewDpStorageSelect"){ list($content,$header)=$dp->viewDpStorageSelect($_REQUEST["dp_id"],$_REQUEST["select_id"],$_REQUEST["select_status"]); $GLOBALS['_RESULT'] = array("content"=>$content,"header"=>$header);}

if ($_REQUEST["w"]=="loadDpJmoving"){ $GLOBALS['_RESULT'] = array("content"=>$dp->loadDpJmoving($_REQUEST["dp_id"]));}



if ($_REQUEST["w"]=="loadDpSaleInvoice"){ $GLOBALS['_RESULT'] = array("content"=>$dp->loadDpSaleInvoice($_REQUEST["dp_id"]));}
if ($_REQUEST["w"]=="showDpStorselForSaleInvoice"){ $content=$dp->showDpStorselForSaleInvoice($_REQUEST["dp_id"]); $GLOBALS['_RESULT'] = array("content"=>$content); }

if ($_REQUEST["w"]=="sendDpStorselToSaleInvoice"){ list($answer,$err,$sale_invoice_nom,$sale_invoice_prefix)=$dp->sendDpStorselToSaleInvoice($_REQUEST["dp_id"],$_REQUEST["kol_storsel"],$_REQUEST["ar_storsel"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err,"sale_invoice_nom"=>$sale_invoice_nom,"sale_invoice_prefix"=>$sale_invoice_prefix); }
	
if ($_REQUEST["w"]=="viewDpSaleInvoice"){ list($content,$header)=$dp->viewDpSaleInvoice($_REQUEST["dp_id"],$_REQUEST["invoice_id"]); $GLOBALS['_RESULT'] = array("content"=>$content,"header"=>$header);}

if ($_REQUEST["w"]=="loadDpMoneyPay"){ $GLOBALS['_RESULT'] = array("content"=>$dp->loadDpMoneyPay($_REQUEST["dp_id"]));}
if ($_REQUEST["w"]=="getDpClientContoCash"){ list($answer,$err,$cash_id)=$dp->getDpClientContoCash($_REQUEST["client_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err,"cash_id"=>$cash_id);}
if ($_REQUEST["w"]=="getDpClientDocType"){ list($answer,$err,$doc_type_id)=$dp->getDpClientDocType($_REQUEST["client_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err,"doc_type_id"=>$doc_type_id);}
if ($_REQUEST["w"]=="getClientPaymentDelay"){ list($answer,$err,$data_pay)=$dp->getClientPaymentDelay($_REQUEST["client_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err,"data_pay"=>$data_pay);}

if ($_REQUEST["w"]=="viewDpDatapayLimitSaleInvoice"){ list($content,$header)=$dp->viewDpDatapayLimitSaleInvoice($_REQUEST["dp_id"]); $GLOBALS['_RESULT'] = array("content"=>$content,"header"=>$header);}
	
if ($_REQUEST["w"]=="getDpNote"){ $content=$dp->getDpNote($_REQUEST["dp_id"]); $GLOBALS['_RESULT'] = array("content"=>$content);}
if ($_REQUEST["w"]=="setDpNote"){ list($answer,$err)=$dp->setDpNote($_REQUEST["dp_id"],$_REQUEST["text"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="dropDpNote"){ list($answer,$err)=$dp->dropDpNote($_REQUEST["dp_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

 
	
//------------				ORDERS SITE				  --------------------------
	
if ($_REQUEST["w"]=="showOrdersSite"){ $GLOBALS['_RESULT'] = array("content"=>$dp->showOrdersSite());}
if ($_REQUEST["w"]=="showOrdersSiteCard"){ list($content,$header)=$dp->showOrdersSiteCard($_REQUEST["order_id"]); $GLOBALS['_RESULT'] = array("content"=>$content,"header"=>$header);}
if ($_REQUEST["w"]=="createDpFromOrder"){ list($answer,$err,$dp_id)=$dp->createDpFromOrder($_REQUEST["order_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err,"dp_id"=>$dp_id);}	
if ($_REQUEST["w"]=="loadDpSiteOrder"){ $GLOBALS['_RESULT'] = array("content"=>$dp->loadDpSiteOrder($_REQUEST["dp_id"]));}

//------------              SALE INVOICE              --------------------------

if ($_REQUEST["w"]=="show_sale_invoice_search"){ $GLOBALS['_RESULT'] = array("content"=>$sale_invoice->show_sale_invoice_list());}
if ($_REQUEST["w"]=="showSaleInvoiceCard"){ list($content,$doc_prefix_nom)=$sale_invoice->showSaleInvoiceCard($_REQUEST["invoice_id"]); $GLOBALS['_RESULT'] = array("content"=>$content,"doc_prefix_nom"=>$doc_prefix_nom);}
if ($_REQUEST["w"]=="closeSaleInvoiceCard"){ $answer=$sale_invoice->closeSaleInvoiceCard($_REQUEST["invoice_id"]);  $GLOBALS['_RESULT'] = array("answer"=>$answer);}
if ($_REQUEST["w"]=="unlockSaleInvoiceCard"){ $answer=$sale_invoice->unlockSaleInvoiceCard($_REQUEST["invoice_id"]);  $GLOBALS['_RESULT'] = array("answer"=>$answer);}
if ($_REQUEST["w"]=="loadSaleInvoiceMoneyPay"){ $GLOBALS['_RESULT'] = array("content"=>$sale_invoice->loadSaleInvoiceMoneyPay($_REQUEST["invoice_id"]));}
if ($_REQUEST["w"]=="showSaleInvoceMoneyPayForm"){ $content=$sale_invoice->showSaleInvoceMoneyPayForm($_REQUEST["invoice_id"],$_REQUEST["pay_id"]); $GLOBALS['_RESULT'] = array("content"=>$content);}
if ($_REQUEST["w"]=="saveSaleInvoiceMoneyPay"){ list($answer,$err,$pay_id)=$sale_invoice->saveSaleInvoiceMoneyPay($_REQUEST["invoice_id"],$_REQUEST["pay_id"],$_REQUEST["kredit"],$_REQUEST["pay_type_id"],$_REQUEST["paybox_id"],$_REQUEST["doc_cash_id"],$_REQUEST["cash_id"],$_REQUEST["cash_kours"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err,"pay_id"=>$pay_id); }
if ($_REQUEST["w"]=="unlockSaleInvoiceMoneyPayKours"){ list($answer,$err)=$sale_invoice->unlockSaleInvoiceMoneyPayKours($_REQUEST["invoice_id"],$_REQUEST["pay_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="getCashKoursSaleInvoiceMoneyPay"){ list($answer,$err,$cash_kours)=$sale_invoice->getCashKoursSaleInvoiceMoneyPay($_REQUEST["doc_cash_id"],$_REQUEST["cash_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err,"cash_kours"=>$cash_kours);}
if ($_REQUEST["w"]=="loadSaleInvoicePartitions"){ $GLOBALS['_RESULT'] = array("content"=>$sale_invoice->loadSaleInvoicePartitions($_REQUEST["invoice_id"]));}
if ($_REQUEST["w"]=="createTaxInvoice"){ list($answer,$err,$tax_id)=$sale_invoice->createTaxInvoice($_REQUEST["invoice_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err,"tax_id"=>$tax_id);}



//------------				SUPPL ORDERS	  --------------------------
	
if ($_REQUEST["w"]=="showSupplOrder"){ $content=$suppl_orders->showSupplOrder($_REQUEST["so_id"]); $GLOBALS['_RESULT'] = array("content"=>$content);}
if ($_REQUEST["w"]=="saveSupplOrder"){ list($answer,$err)=$suppl_orders->saveSupplOrder($_REQUEST["so_id"],$_REQUEST["amount_order"],$_REQUEST["delivery_data_finish"],$_REQUEST["delivery_time_finish"],$_REQUEST["delivery_type_id"],$_REQUEST["suppl_order_status_id"],$_REQUEST["suppl_order_doc"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err); }
	
//------------				MONEY MOVE		  --------------------------
	
if ($_REQUEST["w"]=="showMoneyMoveForm"){ $content=$money_move->showMoneyMoveForm(); $GLOBALS['_RESULT'] = array("content"=>$content);}
if ($_REQUEST["w"]=="viewMoneyMove"){ $content=$money_move->viewMoneyMove($_REQUEST["move_id"]); $GLOBALS['_RESULT'] = array("content"=>$content);}	
if ($_REQUEST["w"]=="getPayboxUserCashSaldoList"){ $GLOBALS['_RESULT'] = array("content"=>$money_move->getPayboxUserCashSaldoList($_REQUEST["paybox_id"],$_REQUEST["user_id"]));}
if ($_REQUEST["w"]=="getPayboxResiverList"){ $GLOBALS['_RESULT'] = array("content"=>$money_move->getPayboxResiverList($_REQUEST["paybox_id"],$_REQUEST["balans_id_from"],$_REQUEST["user_id"]));}
if ($_REQUEST["w"]=="getPayboxManagerList"){ $GLOBALS['_RESULT'] = array("content"=>$money_move->getPayboxManagerList($_REQUEST["paybox_id"],$_REQUEST["balans_id_from"]));}

if ($_REQUEST["w"]=="saveMoneyMove"){ list($answer,$err,$move_id)=$money_move->saveMoneyMove($_REQUEST["paybox_id_from"],$_REQUEST["paybox_id_to"],$_REQUEST["user_id_to"],$_REQUEST["balans_id_from"],$_REQUEST["summ"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err,"move_id"=>$move_id); }
if ($_REQUEST["w"]=="acceptMoneyMove"){ list($answer,$err)=$money_move->acceptMoneyMove($_REQUEST["move_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err); }


//------------				MONEY SPEND		  --------------------------
	
if ($_REQUEST["w"]=="showMoneySpendForm"){ $content=$money_spend->showMoneySpendForm(); $GLOBALS['_RESULT'] = array("content"=>$content);}
if ($_REQUEST["w"]=="viewMoneySpend"){ $content=$money_spend->viewMoneySpend($_REQUEST["spend_id"]); $GLOBALS['_RESULT'] = array("content"=>$content);}	
if ($_REQUEST["w"]=="saveMoneySpend"){ list($answer,$err,$move_id)=$money_spend->saveMoneySpend($_REQUEST["paybox_id_from"],$_REQUEST["balans_id_from"],$_REQUEST["spend_type_id"],$_REQUEST["summ"],$_REQUEST["comment"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err,"spend_id"=>$spend_id); }

if ($_REQUEST["w"]=="loadMoneySpendCDN"){ $GLOBALS['_RESULT'] = array("content"=>$money_spend->loadMoneySpendCDN($_REQUEST["spend_id"]));}
if ($_REQUEST["w"]=="moneySpendCDNDropFile"){ list($answer,$err)=$money_spend->moneySpendCDNDropFile($_REQUEST["spend_id"],$_REQUEST["file_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
	
//------------				TAX INVOICE		  --------------------------
	
if ($_REQUEST["w"]=="showTaxInvoiceCard"){ list($content,$doc_nom)=$tax_invoice->showTaxInvoiceCard($_REQUEST["tax_id"]); $GLOBALS['_RESULT'] = array("content"=>$content,"doc_nom"=>$doc_nom);}
if ($_REQUEST["w"]=="saveTaxCard"){ list($answer,$err,$tax_id)=$tax_invoice->saveTaxCard($_REQUEST["tax_id"],$_REQUEST["data_create"],$_REQUEST["data_send"],$_REQUEST["cash_id"],$_REQUEST["tax_summ"],$_REQUEST["tax_type_id"],$_REQUEST["tpoint_id"],$_REQUEST["seller_id"],$_REQUEST["client_id"],$_REQUEST["status_tax"],$_REQUEST["doc_xml_nom"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err,"tax_id"=>$tax_id);}
if ($_REQUEST["w"]=="saveTaxCardData"){ list($answer,$err)=$tax_invoice->saveTaxCardData($_REQUEST["tax_id"],$_REQUEST["frm"],$_REQUEST["tto"],$_REQUEST["idStr"],$_REQUEST["zedStr"],$_REQUEST["goods_nameStr"],$_REQUEST["amountStr"],$_REQUEST["priceStr"],$_REQUEST["summStr"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="dropTaxStr"){ list($answer,$err,$tax_summ)=$tax_invoice->dropTaxStr($_REQUEST["tax_id"],$_REQUEST["tax_str_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err,"tax_summ"=>$tax_summ);}
if ($_REQUEST["w"]=="loadTaxInvoiceCDN"){ $GLOBALS['_RESULT'] = array("content"=>$tax_invoice->loadTaxInvoiceCDN($_REQUEST["tax_id"]));}
if ($_REQUEST["w"]=="taxInvoiceCDNDropFile"){ list($answer,$err)=$tax_invoice->taxInvoiceCDNDropFile($_REQUEST["tax_id"],$_REQUEST["file_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

	

if ($_REQUEST["w"]=="showTaxInvoiceBackCard"){ list($content,$doc_nom)=$tax_invoice->showTaxInvoiceBackCard($_REQUEST["tax_id"]); $GLOBALS['_RESULT'] = array("content"=>$content,"doc_nom"=>$doc_nom);}
if ($_REQUEST["w"]=="showTaxSelectList"){ $GLOBALS['_RESULT'] = array("content"=>$tax_invoice->showTaxSelectList($_REQUEST["tax_to_back_id"]));}
if ($_REQUEST["w"]=="unlinkTaxBack"){ list($answer,$err)=$tax_invoice->unlinkTaxBack($_REQUEST["tax_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="loadTaxBackSellerClient"){ list($answer,$err,$client_id,$client_name,$seller_id,$seller_name)=$tax_invoice->loadTaxBackSellerClient($_REQUEST["tax_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err,"client_id"=>$client_id,"client_name"=>$client_name,"seller_id"=>$seller_id,"seller_name"=>$seller_name);}
if ($_REQUEST["w"]=="findTaxStr"){ $GLOBALS['_RESULT'] = array("content"=>$tax_invoice->findTaxStr($_REQUEST["tax_to_back_id"]));}
if ($_REQUEST["w"]=="dropTaxBackStr"){ list($answer,$err,$tax_summ)=$tax_invoice->dropTaxBackStr($_REQUEST["tax_id"],$_REQUEST["tax_str_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err,"tax_summ"=>$tax_summ);}
if ($_REQUEST["w"]=="saveTaxBackCard"){ list($answer,$err,$tax_id)=$tax_invoice->saveTaxBackCard($_REQUEST["tax_id"],$_REQUEST["tax_to_back_id"],$_REQUEST["data_create"],$_REQUEST["data_send"],$_REQUEST["cash_id"],$_REQUEST["tax_summ"],$_REQUEST["tax_type_id"],$_REQUEST["tpoint_id"],$_REQUEST["seller_id"],$_REQUEST["client_id"],$_REQUEST["status_tax"],$_REQUEST["doc_xml_nom"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err,"tax_id"=>$tax_id);}
if ($_REQUEST["w"]=="saveTaxBackCardData"){ list($answer,$err)=$tax_invoice->saveTaxBackCardData($_REQUEST["tax_id"],$_REQUEST["frm"],$_REQUEST["tto"],$_REQUEST["idStr"],$_REQUEST["tsidStr"],$_REQUEST["nomStr"],$_REQUEST["zedStr"],$_REQUEST["goods_nameStr"],$_REQUEST["amountStr"],$_REQUEST["priceStr"],$_REQUEST["summStr"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
	
//------------              JPAY              --------------------------
if ($_REQUEST["w"]=="showJpayMoneyPayForm"){ $content=$jpay->showJpayMoneyPayForm(); $GLOBALS['_RESULT'] = array("content"=>$content);}
if ($_REQUEST["w"]=="viewJpayMoneyPay"){ $content=$jpay->viewJpayMoneyPay($_REQUEST["pay_id"]); $GLOBALS['_RESULT'] = array("content"=>$content);}

if ($_REQUEST["w"]=="unlinkJpayClient"){ list($answer,$err)=$jpay->unlinkJpayClient($_REQUEST["pay_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="showJpayClientList"){ $GLOBALS['_RESULT'] = array("content"=>$jpay->showJpayClientList($_REQUEST["client_id"]));}
if ($_REQUEST["w"]=="showJpayClientSaleInvoiceList"){ $GLOBALS['_RESULT'] = array("content"=>$jpay->showJpayClientSaleInvoiceList($_REQUEST["client_id"]));}
if ($_REQUEST["w"]=="loadJpayCashBoxList"){ $GLOBALS['_RESULT'] = array("content"=>$jpay->loadJpayCashBoxList($_REQUEST["client_id"],$_REQUEST["document_type_id"],$_REQUEST["seller_id"]));}
if ($_REQUEST["w"]=="saveJpayMoneyPay"){ list($answer,$err,$pay_id)=$jpay->saveJpayMoneyPay($_REQUEST["invoice_id"],$_REQUEST["kredit"],$_REQUEST["pay_type_id"],$_REQUEST["paybox_id"],$_REQUEST["doc_cash_id"],$_REQUEST["cash_id"],$_REQUEST["cash_kours"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err,"pay_id"=>$pay_id); }

if ($_REQUEST["w"]=="getPayBoxBalans"){ $content=$jpay->getPayBoxBalans($_REQUEST["paybox_id"]); $GLOBALS['_RESULT'] = array("content"=>$content); }

if ($_REQUEST["w"]=="showJpayAutoMoneyPayForm"){ $content=$jpay->showJpayAutoMoneyPayForm(); $GLOBALS['_RESULT'] = array("content"=>$content);}
if ($_REQUEST["w"]=="loadJpayClientSaleInvoiceUnpayedList"){ list($content,$summ_balans,$cash_id,$cash_name,$tpoint_id,$document_type_id,$client_balans_avans,$seller_id)=$jpay->loadJpayClientSaleInvoiceUnpayedList($_REQUEST["client_id"]); $GLOBALS['_RESULT'] = array("content"=>$content,"summ_balans"=>$summ_balans,"cash_id"=>$cash_id,"cash_name"=>$cash_name,"tpoint_id"=>$tpoint_id,"document_type_id"=>$document_type_id, "client_balans_avans"=>$client_balans_avans,"seller_id"=>$seller_id);}
if ($_REQUEST["w"]=="saveJpayAutoMoneyPay"){ list($answer,$err,$pay_id)=$jpay->saveJpayAutoMoneyPay($_REQUEST["client_id"],$_REQUEST["kredit"],$_REQUEST["pay_type_id"],$_REQUEST["paybox_id"],$_REQUEST["cash_id"],$_REQUEST["cash_kours"],$_REQUEST["doc_cash_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err,"pay_id"=>$pay_id); }
if ($_REQUEST["w"]=="showJpayMoneyBackForm"){ $content=$jpay->showJpayMoneyBackForm(); $GLOBALS['_RESULT'] = array("content"=>$content);}
if ($_REQUEST["w"]=="saveJpayMoneyBackPay"){ list($answer,$err,$pay_id)=$jpay->saveJpayMoneyBackPay($_REQUEST["client_id"],$_REQUEST["avans_debit"],$_REQUEST["pay_type_id"],$_REQUEST["paybox_id"],$_REQUEST["cash_id"],$_REQUEST["cash_kours"],$_REQUEST["doc_cash_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err,"pay_id"=>$pay_id); }
	
if ($_REQUEST["w"]=="showJpayAvansForm"){ $content=$jpay->showJpayAvansForm(); $GLOBALS['_RESULT'] = array("content"=>$content);}
if ($_REQUEST["w"]=="saveJpayAvansPay"){ list($answer,$err,$pay_id)=$jpay->saveJpayAvansPay($_REQUEST["client_id"],$_REQUEST["kredit"],$_REQUEST["pay_type_id"],$_REQUEST["paybox_id"],$_REQUEST["cash_id"],$_REQUEST["cash_kours"],$_REQUEST["doc_cash_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err,"pay_id"=>$pay_id); }
if ($_REQUEST["w"]=="getJpayClientDocCashId"){ list($cash_id,$cash_name,$doc_type_id)=$jpay->getJpayClientDocCashId($_REQUEST["client_id"]); $GLOBALS['_RESULT'] = array("cash_id"=>$cash_id,"cash_name"=>$cash_name,"doc_type_id"=>$doc_type_id);}
if ($_REQUEST["w"]=="showJpayAvansMoneyPayForm"){ $content=$jpay->showJpayAvansMoneyPayForm(); $GLOBALS['_RESULT'] = array("content"=>$content);}
if ($_REQUEST["w"]=="saveJpayAvansMoneyPay"){ list($answer,$err,$pay_id)=$jpay->saveJpayAvansMoneyPay($_REQUEST["client_id"],$_REQUEST["kredit"],$_REQUEST["pay_type_id"],$_REQUEST["doc_cash_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err,"pay_id"=>$pay_id); }


//------------              BACK CLIENTS              --------------------------
if ($_REQUEST["w"]=="show_back_clients_search"){ $GLOBALS['_RESULT'] = array("content"=>$back_clients->show_back_clients_list());}
if ($_REQUEST["w"]=="newBackClientsCard"){ $GLOBALS['_RESULT'] = array("back_id"=>$back_clients->newBackClientsCard());}
if ($_REQUEST["w"]=="showBackClientsCard"){ list($content,$doc_prefix_nom)=$back_clients->showBackClientsCard($_REQUEST["back_id"]); $GLOBALS['_RESULT'] = array("content"=>$content,"doc_prefix_nom"=>$doc_prefix_nom);}
if ($_REQUEST["w"]=="showBackClientsStrList"){ list($content,$kol_rw)=$back_clients->showBackClientsStrList($_REQUEST["back_id"],"",$_REQUEST["si_id"]); $GLOBALS['_RESULT'] = array("content"=>$content,"kol_rw"=>$kol_rw);}
if ($_REQUEST["w"]=="closeBackClientsCard"){ $answer=$back_clients->closeBackClientsCard($_REQUEST["back_id"]);  $GLOBALS['_RESULT'] = array("answer"=>$answer);}
if ($_REQUEST["w"]=="unlockBackClientsCard"){ $answer=$back_clients->unlockBackClientsCard($_REQUEST["back_id"]);  $GLOBALS['_RESULT'] = array("answer"=>$answer);}
if ($_REQUEST["w"]=="clearBackClientsStr"){ list($answer,$err)=$back_clients->clearBackClientsStr($_REQUEST["back_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="dropBackClientsStr"){ list($answer,$err,$back_clients_summ)=$back_clients->dropBackClientsStr($_REQUEST["back_id"],$_REQUEST["back_str_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err,"back_summ"=>$back_summ);}
if ($_REQUEST["w"]=="saveBackClientsCard"){ list($answer,$err)=$back_clients->saveBackClientsCard($_REQUEST["back_id"],$_REQUEST["data_pay"],$_REQUEST["cash_id"],$_REQUEST["back_summ"],$_REQUEST["doc_type_id"],$_REQUEST["tpoint_id"],$_REQUEST["client_id"],$_REQUEST["client_conto_id"],$_REQUEST["delivery_type_id"],$_REQUEST["carrier_id"],$_REQUEST["delivery_address"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="saveBackClientsCardData"){ list($answer,$err)=$back_clients->saveBackClientsCardData($_REQUEST["back_id"],$_REQUEST["cash_id"],$_REQUEST["frm"],$_REQUEST["tto"],$_REQUEST["idStr"],$_REQUEST["artIdStr"],$_REQUEST["article_nr_displStr"],$_REQUEST["brandIdStr"],$_REQUEST["amountStr"],$_REQUEST["priceStr"],$_REQUEST["priceEndStr"],$_REQUEST["discountStr"],$_REQUEST["summStr"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
	
if ($_REQUEST["w"]=="setBackClientsClient"){ list($answer,$err,$client_name)=$back_clients->setBackClientsClient($_REQUEST["back_id"],$_REQUEST["client_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err,"client_name"=>$client_name);}


if ($_REQUEST["w"]=="loadBackClientsTpointStorage"){ $content=$back_clients->showStorageSelectListByTpoint($_REQUEST["tpoint_id"],0); $GLOBALS['_RESULT'] = array("content"=>$content);}
if ($_REQUEST["w"]=="setBackClientsTpointStorage"){ $$answer=$back_clients->setBackClientsTpointStorage($_REQUEST["back_id"],$_REQUEST["tpoint_id"],$_REQUEST["storage_id"]); $GLOBALS['_RESULT'] = array("$answer"=>$answer);}
	

if ($_REQUEST["w"]=="loadBackClientsCDN"){ $GLOBALS['_RESULT'] = array("content"=>$back_clients->loadBackClientsCDN($_REQUEST["back_id"]));}
if ($_REQUEST["w"]=="dpCDNDropFile"){ list($answer,$err)=$back_clients->back_clientsCDNDropFile($_REQUEST["back_id"],$_REQUEST["file_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="loadBackClientsCommetsLabel"){ list($kol,$label)=$back_clients->labelCommentsCount($_REQUEST["back_id"]); $GLOBALS['_RESULT'] = array("label"=>$label);}
if ($_REQUEST["w"]=="loadBackClientsCommets"){ $GLOBALS['_RESULT'] = array("content"=>$back_clients->loadBackClientsCommets($_REQUEST["back_id"]));}
if ($_REQUEST["w"]=="saveBackClientsComment"){ list($answer,$err)=$back_clients->saveBackClientsComment($_REQUEST["back_id"],$_REQUEST["comment"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="dropBackClientsComment"){ list($answer,$err)=$back_clients->dropBackClientsComment($_REQUEST["back_id"],$_REQUEST["cmt_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="loadBackClientsClientContoList"){ $GLOBALS['_RESULT'] = array("content"=>$back_clients->getClientContoSelectList($_REQUEST["client_id"],0));}
if ($_REQUEST["w"]=="showBackClientsClientList"){ $GLOBALS['_RESULT'] = array("content"=>$back_clients->showBackClientsClientList($_REQUEST["client_id"]));}
if ($_REQUEST["w"]=="unlinkBackClientsClient"){ list($answer,$err)=$back_clients->unlinkBackClientsClient($_REQUEST["back_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="showBackClientsTpointList"){ $GLOBALS['_RESULT'] = array("content"=>$back_clients->showBackClientsTpointList($_REQUEST["tpoint_id"]));}
if ($_REQUEST["w"]=="unlinkBackClientsTpoint"){ list($answer,$err)=$back_clients->unlinkBackClientsTpoint($_REQUEST["back_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="showBackClientsSaleInvoiceList"){ $GLOBALS['_RESULT'] = array("content"=>$back_clients->showBackClientsSaleInvoiceList($_REQUEST["client_id"],$_REQUEST["sale_invoice_id"]));}
if ($_REQUEST["w"]=="setBackClientsSaleInvoice"){ list($answer,$err)=$back_clients->setBackClientsSaleInvoice($_REQUEST["back_id"],$_REQUEST["sale_invoice_id"],$_REQUEST["cash_id"],$_REQUEST["seller_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}	

if ($_REQUEST["w"]=="showSaleInvoiceArticleSearchForm"){ $GLOBALS['_RESULT'] = array("content"=>$back_clients->showSaleInvoiceArticleSearchForm($_REQUEST["si_id"],$_REQUEST["si_str_id"]));}

if ($_REQUEST["w"]=="showBackClientsArticleAmountWindow"){ $GLOBALS['_RESULT'] = array("content"=>$back_clients->showBackClientsArticleAmountWindow($_REQUEST["art_id"],$_REQUEST["back_id"]));}
if ($_REQUEST["w"]=="setArticleToBackClients"){ list($answer,$err,$summ)=$back_clients->setArticleToBackClients($_REQUEST["back_id"],$_REQUEST["si_id"],$_REQUEST["sis_id"],$_REQUEST["art_id"],$_REQUEST["article_nr_displ"],$_REQUEST["amount_back"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err,"back_clients_summ"=>$summ);}

if ($_REQUEST["w"]=="acceptBackClients"){ list($answer,$err)=$back_clients->acceptBackClients($_REQUEST["back_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="createBackClientsTax"){ list($answer,$err,$tax_nom)=$back_clients->createBackClientsTax($_REQUEST["back_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err,"tax_nom"=>$tax_nom);}

	
	
	
if ($_REQUEST["w"]=="getSaleInvoceProlog"){ $content=$cl->getSaleInvoceProlog($_REQUEST["client_id"],$_REQUEST["date_search"]); $GLOBALS['_RESULT'] = array("content"=>$content);}
if ($_REQUEST["w"]=="checkSaleInvoceProlog"){ $content=$cl->checkSaleInvoceProlog($_REQUEST["client_id"],$_REQUEST["date_start"],$_REQUEST["date_new"]); $GLOBALS['_RESULT'] = array("content"=>$content);}
if ($_REQUEST["w"]=="getSaleInvocePrologHistory"){ $content=$cl->getSaleInvocePrologHistory($_REQUEST["client_id"]); $GLOBALS['_RESULT'] = array("content"=>$content);}
if ($_REQUEST["w"]=="editSaleInvoceProlog"){ list($answer,$err)=$cl->editSaleInvoceProlog($_REQUEST["client_id"],$_REQUEST["date_start"],$_REQUEST["date_new"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
	
	
	
//------------              INCOME              --------------------------

if ($_REQUEST["w"]=="filterIncomesList"){ $GLOBALS['_RESULT'] = array("content"=>$income->show_income_list());}
if ($_REQUEST["w"]=="preNewIncomeCard"){ $GLOBALS['_RESULT'] = array("content"=>$income->preNewIncomeCard());}
if ($_REQUEST["w"]=="newIncomeCard"){ $GLOBALS['_RESULT'] = array("income_id"=>$income->newIncomeCard($_REQUEST["type_id"]));}
if ($_REQUEST["w"]=="showIncomeCard"){ list($content,$doc_prefix_nom)=$income->showIncomeCard($_REQUEST["income_id"]); $GLOBALS['_RESULT'] = array("content"=>$content,"doc_prefix_nom"=>$doc_prefix_nom);}
if ($_REQUEST["w"]=="showIncomeClientList"){ $GLOBALS['_RESULT'] = array("content"=>$income->showIncomeClientList($_REQUEST["client_id"]));}
if ($_REQUEST["w"]=="unlinkIncomeClient"){ list($answer,$err)=$income->unlinkIncomeClient($_REQUEST["income_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="showIncomeClientSellerList"){ $GLOBALS['_RESULT'] = array("content"=>$income->showIncomeClientSellerList($_REQUEST["client_id"]));}
if ($_REQUEST["w"]=="unlinkIncomeClientSeller"){ list($answer,$err)=$income->unlinkIncomeClientSeller($_REQUEST["income_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="showIncomeArticleSearchForm"){ $GLOBALS['_RESULT'] = array("content"=>$income->showIncomeArticleSearchForm($_REQUEST["brand_id"],$_REQUEST["article_nr_displ"]));}
if ($_REQUEST["w"]=="showArticleSearchForm"){ $GLOBALS['_RESULT'] = array("content"=>$income->showIncomeArticleSearchForm($_REQUEST["brand_id"],$_REQUEST["article_nr_displ"]));}

if ($_REQUEST["w"]=="loadIncomeKours"){ list($usd_to_uah,$eur_to_uah)=$income->loadIncomeKours($_REQUEST["data"]); $GLOBALS['_RESULT'] = array("usd_to_uah"=>$usd_to_uah,"eur_to_uah"=>$eur_to_uah); }

if ($_REQUEST["w"]=="getIncomeClientPrefixDocument"){ $doc_prefix=$income->getIncomeClientPrefixDocument($_REQUEST["client_id"]); $GLOBALS['_RESULT'] = array("doc_prefix"=>$doc_prefix);}



if ($_REQUEST["w"]=="loadIncomeCDN"){ $GLOBALS['_RESULT'] = array("content"=>$income->loadIncomeCDN($_REQUEST["income_id"]));}
if ($_REQUEST["w"]=="incomeCDNDropFile"){ list($answer,$err)=$income->incomeCDNDropFile($_REQUEST["income_id"],$_REQUEST["file_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}


if ($_REQUEST["w"]=="loadIncomeCommetsLabel"){ list($kol,$label)=$income->labelCommentsCount($_REQUEST["income_id"]); $GLOBALS['_RESULT'] = array("label"=>$label);}
if ($_REQUEST["w"]=="loadIncomeCommets"){ $GLOBALS['_RESULT'] = array("content"=>$income->loadIncomeCommets($_REQUEST["income_id"]));}
if ($_REQUEST["w"]=="saveIncomeComment"){ list($answer,$err)=$income->saveIncomeComment($_REQUEST["income_id"],$_REQUEST["comment"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="dropIncomeComment"){ list($answer,$err)=$income->dropIncomeComment($_REQUEST["income_id"],$_REQUEST["cmt_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}




if ($_REQUEST["w"]=="loadIncomeStorage"){ $GLOBALS['_RESULT'] = array("content"=>$income->loadIncomeStorage($_REQUEST["income_id"]));}
if ($_REQUEST["w"]=="loadStorageCellsSelectList"){ $GLOBALS['_RESULT'] = array("content"=>$income->showStorageCellsSelectList($_REQUEST["storage_id"],0));}
if ($_REQUEST["w"]=="loadIncomeStorageCellsSelectList"){ $GLOBALS['_RESULT'] = array("content"=>$income->showStorageCellsSelectList($_REQUEST["storage_id"],0));}
if ($_REQUEST["w"]=="saveIncomeStorage"){ list($answer,$err)=$income->saveIncomeStorage($_REQUEST["income_id"],$_REQUEST["storage_id"],$_REQUEST["storage_cells_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}


if ($_REQUEST["w"]=="loadIncomeSpend"){ $GLOBALS['_RESULT'] = array("content"=>$income->loadIncomeSpend($_REQUEST["income_id"]));}
if ($_REQUEST["w"]=="showIncomeSpendItemRow"){list($content,$header)=$income->showIncomeSpendItemRow($_REQUEST["income_id"],$_REQUEST["spend_item_id"],$_REQUEST["str_id"]); $GLOBALS['_RESULT'] = array("content"=>$content,"header"=>$header); }
if ($_REQUEST["w"]=="saveIncomeSpendStrForm"){ list($answer,$err)=$income->saveIncomeSpendStrForm($_REQUEST["income_id"],$_REQUEST["spend_item_id"],$_REQUEST["str_id"],$_REQUEST["caption"],$_REQUEST["data"],$_REQUEST["cash_id"],$_REQUEST["summ_cash"],$_REQUEST["kours"],$_REQUEST["summ_uah"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="dropIncomeSpendItemRow"){ list($answer,$err)=$income->dropIncomeSpendItemRow($_REQUEST["income_id"],$_REQUEST["str_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="showCountrySearchForm"){$content=$cat->showDocumentCountryManual($_REQUEST["country_id"],$_REQUEST["pos"]); $GLOBALS['_RESULT'] = array("content"=>$content); }
if ($_REQUEST["w"]=="showCostumsSearchForm"){$content=$cat->showDocumentCostumsManual($_REQUEST["costums_id"],$_REQUEST["pos"]); $GLOBALS['_RESULT'] = array("content"=>$content); }

if ($_REQUEST["w"]=="showIncomeCountrySearchForm"){$content=$cat->showDocumentCountryManual($_REQUEST["country_id"],$_REQUEST["pos"]); $GLOBALS['_RESULT'] = array("content"=>$content); }
if ($_REQUEST["w"]=="showIncomeCostumsSearchForm"){$content=$cat->showDocumentCostumsManual($_REQUEST["costums_id"],$_REQUEST["pos"]); $GLOBALS['_RESULT'] = array("content"=>$content); }

if ($_REQUEST["w"]=="getRateTypeDeclarationdocumentPos"){ list($rate,$type_declaration,$type_declaration_id)=$income->getRateTypeDeclarationdocumentPos($_REQUEST["costums_id"],$_REQUEST["country_id"]); $GLOBALS['_RESULT'] = array("rate"=>$rate,"type_declaration"=>$type_declaration,"type_declaration_id"=>$type_declaration_id);}

if ($_REQUEST["w"]=="saveIncomeCard"){ list($answer,$err)=$income->saveIncomeCard($_REQUEST["income_id"],$_REQUEST["data"],$_REQUEST["client_seller"],$_REQUEST["invoice_income"],$_REQUEST["cash_id"],$_REQUEST["client_id"],$_REQUEST["invoice_data"],$_REQUEST["cours_to_uah"],$_REQUEST["cours_to_uah_nbu"],$_REQUEST["invoice_summ"],$_REQUEST["usd_to_uah"],$_REQUEST["eur_to_uah"],$_REQUEST["costums_pd_uah"],$_REQUEST["costums_pp_uah"],$_REQUEST["costums_summ_uah"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="saveIncomeCardData"){ list($answer,$err)=$income->saveIncomeCardData($_REQUEST["income_id"],$_REQUEST["frm"],$_REQUEST["tto"],$_REQUEST["idStr"],$_REQUEST["artIdStr"],$_REQUEST["article_nr_displStr"],$_REQUEST["brandIdStr"],$_REQUEST["countryIdStr"],$_REQUEST["costumsIdStr"],$_REQUEST["amountStr"],$_REQUEST["price_buh_cashinStr"],$_REQUEST["weightNettoStr"],$_REQUEST["rateStr"],$_REQUEST["typeDeclarationIdStr"],$_REQUEST["price_man_cashinStr"],$_REQUEST["price_man_usdStr"],$_REQUEST["price_buh_uahStr"],$_REQUEST["price_man_uahStr"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}


if ($_REQUEST["w"]=="setIncomeImport"){ list($answer,$err)=$income->setIncomeImport($_REQUEST["income_id"],$_REQUEST["import_use"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="setIncomeVat"){ list($answer,$err)=$income->setIncomeVat($_REQUEST["income_id"],$_REQUEST["vat_use"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}

if ($_REQUEST["w"]=="makeIncomeCardFinish"){ list($answer,$err)=$income->makeIncomeCardFinish($_REQUEST["income_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
	
if ($_REQUEST["w"]=="checkInvoiceIncome"){ $content=$income->checkInvoiceIncome($_REQUEST["invoice_income"],$_REQUEST["seller"],$_REQUEST["date"]); $GLOBALS['_RESULT'] = array("content"=>$content);}

if ($_REQUEST["w"]=="showImportIncomeStrCSVform"){list($content,$header)=$income->showImportIncomeStrCSVform($_REQUEST["income_id"]); $GLOBALS['_RESULT'] = array("content"=>$content,"header"=>$header); }
if ($_REQUEST["w"]=="finishCsvImport"){list($answer,$err)=$income->finishCsvImport($_REQUEST["income_id"],$_REQUEST["start_row"],$_REQUEST["kol_cols"],$_REQUEST["cols"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err); }

if ($_REQUEST["w"]=="loadIncomeUnknownArticles"){ $GLOBALS['_RESULT'] = array("content"=>$income->loadIncomeUnknownArticles($_REQUEST["income_id"]));}
if ($_REQUEST["w"]=="checkIncomUnStr"){ list($answer,$err)=$income->checkIncomUnStr($_REQUEST["income_id"],$_REQUEST["unknown_id"],$_REQUEST["art_id"],$_REQUEST["article_nr_displ"],$_REQUEST["brand_id"],$_REQUEST["country_id"],$_REQUEST["costums_id"],$_REQUEST["amount"],$_REQUEST["price"],$_REQUEST["weight"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
if ($_REQUEST["w"]=="dropIncomUnStr"){ list($answer,$err)=$income->dropIncomUnStr($_REQUEST["income_id"],$_REQUEST["unknown_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
	
if ($_REQUEST["w"]=="dropIncomeStr"){ list($answer,$err)=$income->dropIncomeStr($_REQUEST["income_id"],$_REQUEST["art_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}
	
if ($_REQUEST["w"]=="updateInformerUnknownArticles"){ list($kol_art_unknown,$label_art_unknown)=$income->labelArtUnknownCount($_REQUEST["income_id"]); $GLOBALS['_RESULT'] = array("content"=>$label_art_unknown);}
if ($_REQUEST["w"]=="clearIncomeStr"){ list($answer,$err)=$income->clearIncomeStr($_REQUEST["income_id"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}



//------------              END INCOME         		--------------------------
	
//------------              IMPORT_ARTPRICE    		--------------------------
if ($_REQUEST["w"]=="showImportArtpriceStrCSVform"){require_once RD.'/lib/import_artprice_class.php'; $import_artprice=new import_artprice; $content=$import_artprice->show_import_artprice_form(); $GLOBALS['_RESULT'] = array("content"=>$content); }
if ($_REQUEST["w"]=="finishArtpriceCsvImport"){require_once RD.'/lib/import_artprice_class.php'; $import_artprice=new import_artprice; list($answer,$err)=$import_artprice->finishArtpriceCsvImport($_REQUEST["start_row"],$_REQUEST["kol_cols"],$_REQUEST["cols"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err); }
//------------              END IMPORT_ARTPRICE		--------------------------
	
	
//------------              IMPORT_REST_FROM_1C    		--------------------------
if ($_REQUEST["w"]=="showImportRestCSVform"){$content=$import_rest->show_import_rest_form(); $GLOBALS['_RESULT'] = array("content"=>$content); }
if ($_REQUEST["w"]=="finishRestCsvImport"){list($answer,$err)=$import_rest->finishRestCsvImport(); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err); }


//------------              UNDISTRIBCELLS          --------------------------


if ($_REQUEST["w"]=="showUndistribCellsCard"){ list($content)=$undistribcells->showUndistribCellsCard($_REQUEST["storage_cells_id"],$_REQUEST["income_id"]); $GLOBALS['_RESULT'] = array("content"=>$content);}
if ($_REQUEST["w"]=="showStorageCellSelectForm"){ list($content)=$undistribcells->showStorageCellSelectForm($_REQUEST["art_id"],$_REQUEST["income_id"],$_REQUEST["storage_id"],$_REQUEST["amount"]); $GLOBALS['_RESULT'] = array("content"=>$content);}
if ($_REQUEST["w"]=="saveUndistribCellsStorageCellForm"){ list($answer,$err)=$undistribcells->saveUndistribCellsStorageCellForm($_REQUEST["art_id"],$_REQUEST["income_id"],$_REQUEST["storage_id"],$_REQUEST["storage_cells_id"],$_REQUEST["amount"]); $GLOBALS['_RESULT'] = array("answer"=>$answer,"error"=>$err);}



//------------              END UNDISTRIBCELLS      --------------------------

if ($_REQUEST["w"]=="filterOrgEventList"){ $GLOBALS['_RESULT'] = array("content"=>$orgs_journal->filterOrgEventList($_REQUEST["data_from"],$_REQUEST["data_to"],$_REQUEST["type_event"],$_REQUEST["event_operation"]));}
if ($_REQUEST["w"]=="sendModerationRequest"){ list($content,$label)=$orgs_journal->sendModerationRequest($_REQUEST["event_id"]); $GLOBALS['_RESULT'] = array("content"=>$content,"label"=>$label);}
if ($_REQUEST["w"]=="sendDeleteRequest"){ list($content,$label)=$orgs_journal->sendDeleteRequest($_REQUEST["event_id"]); $GLOBALS['_RESULT'] = array("content"=>$content,"label"=>$label);}

if ($_REQUEST["w"]=="sendDeleteOrgRequest"){ list($content,$label)=$orgs_info->sendDeleteRequest($_REQUEST["org_id"]); $GLOBALS['_RESULT'] = array("content"=>$content,"label"=>$label);}
if ($_REQUEST["w"]=="sendDeleteUserRequest"){ list($content,$label)=$orgs_users->sendDeleteRequest($_REQUEST["user_id"]); $GLOBALS['_RESULT'] = array("content"=>$content,"label"=>$label);}



if ($_REQUEST["w"]=="loadManualData"){ $GLOBALS['_RESULT'] = array("content"=>$manual->loadManualData($_REQUEST["key"],$_REQUEST["manValue"],$_REQUEST["manText"]));}
if ($_REQUEST["w"]=="showManualList"){ $GLOBALS['_RESULT'] = array("content"=>$manual->showManualList($_REQUEST["key"],$_REQUEST["filter"]));}
if ($_REQUEST["w"]=="AddManualValue"){ list($manValue,$manText)=$manual->AddManualValue($_REQUEST["key"],$_REQUEST["manText"]); $GLOBALS['_RESULT'] = array("manValue"=>$manValue,"manText"=>$manText);}

if ($_REQUEST["w"]=="loadManualDData"){ $GLOBALS['_RESULT'] = array("content"=>$manualD->loadManualDData($_REQUEST["key"],$_REQUEST["manValue"],$_REQUEST["manText"]));}
if ($_REQUEST["w"]=="setValueD"){  list($caption,$desc)=$manualD->setValueD($_REQUEST["id"]);$GLOBALS['_RESULT'] = array("caption"=>$caption,"desc"=>$desc);}
if ($_REQUEST["w"]=="showManualDList"){ $GLOBALS['_RESULT'] = array("content"=>$manualD->showManualDList($_REQUEST["key"],$_REQUEST["filter"]));}
if ($_REQUEST["w"]=="AddManualD"){ $GLOBALS['_RESULT'] = array("answer"=>$manualD->AddManualD($_REQUEST["key"],$_REQUEST["caption"],$_REQUEST["desc"]));}
if ($_REQUEST["w"]=="saveManualD"){ $GLOBALS['_RESULT'] = array("answer"=>$manualD->saveManualD($_REQUEST["id"],$_REQUEST["key"],$_REQUEST["caption"],$_REQUEST["desc"]));}
if ($_REQUEST["w"]=="getManualDInfo"){  list($caption,$desc)=$manualD->setValueD($_REQUEST["id"]);$GLOBALS['_RESULT'] = array("caption"=>$caption,"desc"=>$desc);}


if ($_REQUEST["w"]=="loadManualKData"){ $GLOBALS['_RESULT'] = array("content"=>$manualK->loadManualKData($_REQUEST["key"],$_REQUEST["manValue"],$_REQUEST["manText"],$_REQUEST["filter"]));}


if ($_REQUEST["w"]=="loadManualPData"){ $GLOBALS['_RESULT'] = array("content"=>$manualP->loadManualPData($_REQUEST["key"],$_REQUEST["manValue"],$_REQUEST["manText"],$_REQUEST["parrentKey"],$_REQUEST["parrentKeyId"]));}
if ($_REQUEST["w"]=="showManualPList"){ $GLOBALS['_RESULT'] = array("content"=>$manualP->showManualPList($_REQUEST["key"],$_REQUEST["filter"],$_REQUEST["parrentKey"],$_REQUEST["parrentKeyId"]));}
if ($_REQUEST["w"]=="AddManualPValue"){ list($manValue,$manText)=$manualP->AddManualPValue($_REQUEST["key"],$_REQUEST["manText"],$_REQUEST["parrentKey"],$_REQUEST["parrentKeyId"]); $GLOBALS['_RESULT'] = array("manValue"=>$manValue,"manText"=>$manText);}


if ($_REQUEST["w"]=="loadManualManagerData"){ $GLOBALS['_RESULT'] = array("content"=>$manualManager->loadManualManagerData($_REQUEST["manValue"],$_REQUEST["manText"],$_REQUEST["bankOc"]));}
if ($_REQUEST["w"]=="showManualManagerList"){ $GLOBALS['_RESULT'] = array("content"=>$manualManager->showManualManagerList($_REQUEST["filter"],$_REQUEST["bankOc"]));}
if ($_REQUEST["w"]=="AddManualManagerValue"){ list($manValue,$manText)=$manualManager->AddManualManagerValue($_REQUEST["name"],$_REQUEST["city"],$_REQUEST["phone"],$_REQUEST["email"],$_REQUEST["persent"],$_REQUEST["bankOc"]); $GLOBALS['_RESULT'] = array("manValue"=>$manValue,"manText"=>$manText);}



if ($_REQUEST["w"]=="sendGmanualRequest1"){ list($content,$label)=$gmanual->sendGmanualRequest1($_REQUEST["gkey"],$_REQUEST["id"],$_REQUEST["caption"]); $GLOBALS['_RESULT'] = array("content"=>$content,"label"=>$label);}
if ($_REQUEST["w"]=="checkGkey"){ list($content,$answer)=$gmanual->checkGkey($_REQUEST["gkey"],$_REQUEST["id"]); $GLOBALS['_RESULT'] = array("content"=>$content,"answer"=>$answer);}
	
	
if ($_REQUEST["w"]=="showReportMargin"){ $GLOBALS['_RESULT'] = array("content"=>$report_margin->showReportMargin($_REQUEST["date_start"],$_REQUEST["date_end"],$_REQUEST["doc_type_id"],$_REQUEST["client_status"],$_REQUEST["doc_status"],$_REQUEST["cash_id"]));}

include_once require_once RD.'/engine2.php';
}
?>
