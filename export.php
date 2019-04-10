<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_WARNING);
@ini_set('display_errors', true);
set_include_path( get_include_path().PATH_SEPARATOR."..");
define('RD', dirname (__FILE__));
$content=null;
require_once (RD."/lib/mysql_class.php");
require_once (RD."/lib/slave_class.php");
require_once (RD."/lib/manual_class.php");
require_once (RD."/lib/gmanual_class.php");
require_once (RD."/lib/config_class.php");
require_once (RD."/event/get_access.php");
require_once (RD."/lib/module_class.php");
require_once (RD."/lib/access_class.php");
require_once (RD."/lib/catalogue_class.php");
require_once (RD."/lib/sale_invoice_class.php");$sale_invoice=new sale_invoice;
require_once (RD."/lib/income_class.php");$income=new income;

$w=$_REQUEST["w"];

if ($w=="incomeUnStr"){
	$income_id=$_REQUEST["income_id"];
	if ($income_id!="" && is_numeric($income_id)){
		$csv_content=$income->exportIncomeUnStr($income_id);
		// output headers so that the file is downloaded rather than displayed
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=data.csv');
		$output = fopen('php://output', 'w');
		fputcsv($output, array('#', 'ART_ID', 'Індекс товару', 'Бренд', 'Країна Абр', 'Код УКТЗЕД', 'Кількість', 'Ціна', 'Вага Нетто', 'Коментар'));
		foreach ($csv_content as $fields) {
			fputcsv($output, $fields);
		}
	}
}

if ($w=="SIRP"){
	$si_id=$_REQUEST["sid"];
	$sale_invoice->exportSaleInvoiceExcel($si_id);
	//exit(0);
}
