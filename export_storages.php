<?php
error_reporting(E_ERROR);
@ini_set('display_errors', true);
@ini_set('html_errors', false);
define('RD', dirname (__FILE__));
$content=null;
require_once (RD."/lib/mysql_class.php");
require_once (RD."/lib/DbSingleton.php");
require_once (RD."/lib/slave_class.php");
require_once (RD."/lib/manual_class.php");
require_once (RD."/lib/gmanual_class.php");
require_once (RD."/lib/config_class.php");
require_once (RD."/event/get_access.php");
require_once (RD."/lib/module_class.php");
require_once (RD."/lib/access_class.php");
require_once (RD."/lib/catalogue_class.php");
require_once (RD."/lib/clients_class.php");
require_once (RD."/lib/storage_reports_class.php"); $storage_reports=new storage_reports;

$w=$_REQUEST["w"];
$storages=$_REQUEST["storages"];

if ($w=="Export"){
	require_once 'lib/excel/Classes/PHPExcel.php'; $objPHPExcel = new PHPExcel();
    $csv_content=$storage_reports->exportStorageReports($storages);
    $row=1;$ch='A';
	
	for ($i=0;$i<=count($csv_content);$i++) {
		for ($j=0;$j<13;$j++) {
			$objPHPExcel->getActiveSheet()->setCellValue($ch.$row,$csv_content[$i][$j]);
			$ch++;
		}
		$ch='A';$row++;
	}	
	
	header('Content-Disposition: attachment; filename=export.xlsx');
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Cache-Control: max-age=0');
	ob_end_clean();
	$objWritter= PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
	$objWritter->save('php://output');
}

function cellColor($cells,$color){
    global $objPHPExcel;
    $objPHPExcel->getActiveSheet()->getStyle($cells)->getFill()->applyFromArray(array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'startcolor' => array(
             'rgb' => $color
        )
    ));
}

