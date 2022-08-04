<?php
error_reporting(E_ERROR);
@ini_set('display_errors', true);
@ini_set('html_errors', false);
define('RD', dirname (__FILE__));
$content = null;
require_once (RD."/lib/DbSingleton.php");
require_once (RD."/lib/mysql_class.php");
require_once (RD."/lib/slave_class.php");
require_once (RD."/lib/manual_class.php");
require_once (RD."/lib/gmanual_class.php");
require_once (RD."/lib/config_class.php");
require_once (RD."/event/get_access.php");
require_once (RD."/lib/module_class.php");
require_once (RD."/lib/access_class.php");
require_once (RD."/lib/catalogue_class.php");
require_once (RD."/lib/brands_class.php");
require_once (RD."/lib/excross.php");
require_once (RD."/lib/dp_class.php");

$dp = new dp;
$dp_id = $_REQUEST["dp_id"];
$type_id = $_REQUEST["type_id"];

$csv_content = $dp->exportDpCard($dp_id, $type_id);
$client_name = $dp->getDpClientName($dp_id);

if ($dp_id > 0) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=export_dp.csv');
    $output = fopen('php://output', 'w');
    fputcsv($output, array("ДП-$dp_id", "$client_name"), $delimiter = ';');
    fputcsv($output, array("Індекс", "Бренд", "Найменування", "Кількість", "Ціна", "Ціна зі знижкою", "Сума"), $delimiter = ';');
    foreach ($csv_content as $fields) {
        fputcsv($output, $fields, $delimiter = ';');
    }
}