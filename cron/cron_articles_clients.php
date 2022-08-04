<?php
$start = microtime(true);
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=export_reports.csv');
ini_set('memory_limit', '1024M');
error_reporting(E_ERROR);
@ini_set('display_errors', true);
@ini_set('html_errors', false);
define('RD', dirname (__FILE__));
date_default_timezone_set("Europe/Kiev");
require_once (RD."/../lib/DbSingleton.php"); $db=DbSingleton::getDb(); $dbt=DbSingleton::getTokoDb();
require_once (RD."/../lib/slave_class.php");
require_once (RD."/../lib/export_stock_price_class.php"); $export=new export_stock_price;

$list = $export->getArticlesClientsAll();

$fp = fopen(RD."/export_clients.csv", "w");

fwrite($fp,'"ArticleID","Brand","Quantity","ShipmentDay","PriceAmount","Title","Description","UrlImages"'. PHP_EOL);

foreach ($list as $fields) {
    $str="";
    for ($i=0;$i<8;$i++) {
        $str.='"'.$fields[$i].'"'.",";
    }
    $str=trim($str,",");
    fwrite($fp, $str. PHP_EOL);
}

fclose($fp);

$time = microtime(true) - $start;
print "Created file: export_clients.csv \n 
       Access by link: https://portal.myparts.pro/cron/export_clients.csv \n 
       Run time: $time \n";

