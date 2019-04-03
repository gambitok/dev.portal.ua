<?php
$access=new access; $mf="export_stock_price";
list($accss,$acc_lvl)=$access->check_user_access($mf);$alg_u=0;

if ($accss=="1"){
    require_once (RD."/lib/export_stock_price_class.php");
    $export_stock_price=new export_stock_price;
	$link=gnLink; if (substr($link,-1)=="/"){$link=substr($link,0,strlen($link)-1);} $links=explode("/", $link); $w=$links[1];
	$form_htm=RD."/tpl/export_stock_price.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	
	if ($w=="") {
		$content=str_replace("{work_window}", $form, $content);
	}
	
	if ($w=="download-stocks") {
		$export_stock_price->exportStocks();
	}
	
	if ($w=="download-prices") {
		$export_stock_price->exportPrices();
	}
	
	if ($alg_u==0){ //не надано права на операціїї з розділом
		$content=str_replace("{work_window}", $access->show_access_deny($mf), $content);	
	}
}

if ($accss=="0"){
	$content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
}
