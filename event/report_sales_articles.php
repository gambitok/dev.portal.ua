<?php
$access=new access; $mf="report_sales_articles";
list($accss,$acc_lvl)=$access->check_user_access($mf);$alg_u=0;
$link=gnLink; if (substr($link,-1)=="/"){$link=substr($link,0,strlen($link)-1);} $links=explode("/", $link); $w=$links[1];
require_once (RD."/lib/report_clients_class.php"); $report_clients=new report_clients;

if ($accss=="1"){

    if ($w=="") {
        $clients = $_GET["clients"];
        $_GET["date_start"]==NULL ? $date_start=date("Y-m-d") : $date_start=$_GET["date_start"];
        $_GET["date_end"]==NULL ? $date_end=date("Y-m-d") : $date_end=$_GET["date_end"];

        $form_htm=RD."/tpl/report_sales_articles.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $content=str_replace("{work_window}", $form, $content);
        $content=str_replace("{date_start}", $date_start, $content);
        $content=str_replace("{date_end}", $date_end, $content);
        $content=str_replace("{brands_list}", $report_clients->getBrandsList(), $content);
        $content=str_replace("{goods_group_list}", $report_clients->getGoodsGroupList(), $content);
        $content=str_replace("{client_ids_list}", $report_clients->getClientsList($clients), $content);
    }

    if ($w=="export") {
        $_GET["date_start"]==NULL ? $date_start=date("Y-m-d") : $date_start=$_GET["date_start"];
        $_GET["date_end"]==NULL ? $date_end=date("Y-m-d") : $date_end=$_GET["date_end"];

        $params=[
            "availability"  =>$_GET["availability_status"],
            "real_cost"     =>$_GET["real_cost_status"],
            "real_sale"     =>$_GET["real_sale_status"],
            "last_income"   =>$_GET["last_income_status"],
            "storage_rate"  =>$_GET["storage_rate_status"],
            "create_order"  =>$_GET["create_order_status"]
        ];

        $report_clients->exportReportSales($date_start, $date_end, $_GET["brands"], $_GET["goods_group"], $_GET["clients"], $params);
    }
}

if ($accss=="0"){
    $content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
}
