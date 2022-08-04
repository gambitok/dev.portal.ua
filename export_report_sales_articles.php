<?php
error_reporting(E_ERROR);
@ini_set('display_errors', true);
@ini_set('html_errors', false);
define('RD', dirname (__FILE__));
$content=null;

require_once 'lib/excel/Classes/PHPExcel.php';
$objPHPExcel = new PHPExcel();
$objPHPExcel->getActiveSheet()->setCellValue('A1','ARTICLE_NUMBER');
$objPHPExcel->getActiveSheet()->setCellValue('B1','BRAND_NAME');
$objPHPExcel->getActiveSheet()->setCellValue('C1','DISPLAY_NUMBER');
$objPHPExcel->getActiveSheet()->setCellValue('D1','CROSS_BRAND_NAME');
$objPHPExcel->getActiveSheet()->setTitle('Cross');
$row=2;$ch='A';

$csv_content=[];
$csv_content[0]=array("art","name","displ","cross");
$csv_content[1]=array("art","name","displ","cross");
$csv_content[2]=array("art","name","displ","cross");
$csv_content[3]=array("art","name","displ","cross");

for ($i=1;$i<=count($csv_content);$i++) {
    for ($j=0;$j<4;$j++) {
        $objPHPExcel->getActiveSheet()->setCellValue($ch.$row,$csv_content[$i][$j]);
        $ch++;
    }
    $ch='A';$row++;
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename=export.xlsx');
header('Cache-Control: max-age=0');
$objWritter= PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
$objWritter->save('php://output');
