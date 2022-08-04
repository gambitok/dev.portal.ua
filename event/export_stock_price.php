<?php

$access = new access;
$mf = "export_stock_price";
list($accss, $acc_lvl) = $access->check_user_access($mf);
$alg_u = 0;

if ($accss == "1") {
    require_once (RD . "/lib/export_stock_price_class.php");
    $export_stock_price = new export_stock_price;
	$link = gnLink;
	if (substr($link,-1) == "/") {
	    $link = substr($link, 0, strlen($link) - 1);
	}
	$links = explode("/", $link);
	$w = $links[1];
	$form_htm = RD . "/tpl/export_stock_price.htm";
	$form = "";
	if (file_exists("$form_htm")) {
	    $form = file_get_contents($form_htm);
	}
    $form = str_replace("{price_list}", $export_stock_price->getPriceList(), $form);
    $form = str_replace("{brand_list}", $export_stock_price->getBrandList(), $form);

	if ($w == "") {
		$content = str_replace("{work_window}", $form, $content);
	}

	if ($w == "download-price-rating") {
        $brand_select = $links[2];
        $export_stock_price->exportPriceRating($brand_select);
    }

	if ($w == "download-stocks") {
		$export_stock_price->exportStocks();
	}

	if ($w == "download-prices") {
        $price_select = $links[2];
        $export_stock_price->exportPrices($price_select);
	}

    if ($w == "download-clients") {
        $export_stock_price->exportClients();
    }

    if ($w == "download-suppl-clients") {
        $export_stock_price->exportSupplClients();
    }

    if ($w == "download-clients-all") {
        $export_stock_price->exportClientsAll();
    }

    if ($w == "download-array") {
        $list = "<table class='table'>
        <thead><tr>
            <th>#</th><th>ARTICLE_NR_DISPL</th><th>BRAND_NAME</th><th>AMOUNT</th><th>DELIVERY</th><th>PRICE</th><th>TITLE</th><th>DESCRIPTION</th><th>IMAGES</th>    
        </tr></thead><tbody>";
        $arr = $export_stock_price->getArticlesClientsData();
        foreach ($arr as $key => $value) {
            $list .= "<tr><td>$key</td>";
            foreach ($value as $val) {
                $list .= "<td>$val</td>";
            }
            $list .= "</tr>";
        }
        $list .= "</tbody></table>";
        $content = str_replace("{work_window}", $list, $content);
    }

    if ($w == "download-array2") {
        $list = "<table class='table'>
        <thead><tr>
            <th>#</th><th>ARTICLE_NR_DISPL</th><th>BRAND_NAME</th><th>AMOUNT</th><th>DELIVERY</th><th>PRICE</th><th>TITLE</th><th>DESCRIPTION</th><th>IMAGES</th>    
        </tr></thead><tbody>";
        $arr = $export_stock_price->getSupplArticlesClientsData();
        foreach ($arr as $key => $value) {
            $list .= "<tr><td>$key</td>";
            foreach ($value as $val) {
                $list .= "<td>$val</td>";
            }
            $list .= "</tr>";
        }
        $list .= "</tbody></table>";
        $content = str_replace("{work_window}", $list, $content);
    }

	if ($alg_u == 0) { //не надано права на операціїї з розділом
		$content = str_replace("{work_window}", $access->show_access_deny($mf), $content);
	}
}

if ($accss == "0") {
	$content = str_replace("{work_window}", $access->show_access_deny($mf), $content);
}
