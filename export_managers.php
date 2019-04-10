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
require_once (RD."/lib/seo_reports_class.php"); $seo_reports=new seo_reports;

$w=$_REQUEST["w"];
$date_start=$_REQUEST["date_start"];
$date_end=$_REQUEST["date_end"];
$managers=$_REQUEST["managers"];
$cash_id=$_REQUEST["cash_id"];
$client_status=$_REQUEST["client_status"];

if ($w=="Export"){
	require_once 'lib/excel/Classes/PHPExcel.php'; $objPHPExcel = new PHPExcel();
    $managers_cap="Менеджери";
	$objPHPExcel->getActiveSheet()->setCellValue('A1',$managers_cap);
	$objPHPExcel->getActiveSheet()->setCellValue('B1','Sales');
	$objPHPExcel->getActiveSheet()->setCellValue('C1','Backs');
	$objPHPExcel->getActiveSheet()->setCellValue('D1','Summ');
	$objPHPExcel->getActiveSheet()->setTitle('Sales ');
	$row=2;$ch='A';

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


