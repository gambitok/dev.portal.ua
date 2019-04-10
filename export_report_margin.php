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
require_once (RD."/lib/report_margin_class.php"); $report_margin=new report_margin;

$w=$_REQUEST["w"];
$date_start=$_REQUEST["date_start"];
$date_end=$_REQUEST["date_end"];
$doc_type_id=$_REQUEST["doc_type_id"];
$client_status=$_REQUEST["client_status"];
$doc_status=$_REQUEST["doc_status"];
$cash_id=$_REQUEST["cash_id"];

if ($w=="Export"){ 
	require_once 'lib/excel/Classes/PHPExcel.php'; $objPHPExcel = new PHPExcel();
    $arr=$report_margin->getReportMarginDataSales($date_start,$date_end,$doc_type_id,$cash_id);
	$objPHPExcel->getActiveSheet()->setCellValue('A1','Document type'); cellColor('A1', "90ee90");
	$objPHPExcel->getActiveSheet()->setCellValue('B1','OS'); cellColor('B1', "90ee90");
	$objPHPExcel->getActiveSheet()->setCellValue('C1','Cost'); cellColor('C1', "90ee90");
	$objPHPExcel->getActiveSheet()->setCellValue('D1','SB'); cellColor('D1', "90ee90");
	$objPHPExcel->getActiveSheet()->setCellValue('E1','SU'); cellColor('E1', "90ee90");
	$objPHPExcel->getActiveSheet()->setCellValue('F1','Selling'); cellColor('F1', "90ee90");
	$objPHPExcel->getActiveSheet()->setTitle('Mark-up');
	$row=2;$ch='A';  
	
 	foreach ($arr as $arr_key=>$arr_val) {
		 foreach ($arr_val as $key=>$val) {
			if ($ch<'G') {
				if ($arr_val["type_id"]==1) cellColor($ch.$row, "F28A8C");
				if ($arr_val["type_id"]==2) cellColor($ch.$row, "ADD8E6");
				if ($arr_val["type_id"]==3) cellColor($ch.$row, "F9FFD8");
				$objPHPExcel->getActiveSheet()->setCellValue($ch.$row,$val);
				$ch++;
			}
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

