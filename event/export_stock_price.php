<?php
$access=new access; $mf="export_stock_price";
list($accss,$acc_lvl)=$access->check_user_access($mf);$alg_u=0;

if ($accss=="1"){
    require_once (RD."/lib/export_stock_price_class.php");
    $export_stock_price=new export_stock_price;
	$link=gnLink; if (substr($link,-1)=="/"){$link=substr($link,0,strlen($link)-1);} $links=explode("/", $link); $w=$links[1];
	$form_htm=RD."/tpl/export_stock_price.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
    $form=str_replace("{price_list}",$export_stock_price->getPriceList(),$form);
	
	if ($w=="") {
		$content=str_replace("{work_window}", $form, $content);
	}
	
	if ($w=="download-stocks") {
		$export_stock_price->exportStocks();
	}
	
	if ($w=="download-prices") {
        $price_select=$links[2];
        $export_stock_price->exportPrices($price_select);
	}
	
	if ($alg_u==0){ //�� ������ ����� �� ��������� � �������
		$content=str_replace("{work_window}", $access->show_access_deny($mf), $content);	
	}
}

if ($accss=="0"){
	$content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
}
