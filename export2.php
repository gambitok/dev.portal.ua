<?php
error_reporting(E_ERROR);
@ini_set('display_errors', true);
@ini_set('html_errors', false);
define('RD', dirname (__FILE__));
$content=null;
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

$w=$_REQUEST["w"];$html="";

if ($w=="ExportBrands"){
		require_once (RD."/lib/brands_class.php");
		$exb = ExportBrands();
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=t2brands.csv');
		$output = fopen('php://output', 'w');
		fputcsv($output, array("���","�����","���","�����","��������"),$delimiter = ';');
		foreach ($exb as $fields) {
			fputcsv($output,$fields,$delimiter = ';');
		}
		fclose($output);
}

if ($w=="ExportCross"){ 
		$expcross_brands=$_REQUEST["expcross_brands"];
		$expcross_type=$_REQUEST["expcross_type"];
		$expcross_all=$_REQUEST["expcross_all"];
		$expcross_brands=explode(',', $expcross_brands);
		//var_dump($expcross_brands);
		require_once (RD."/lib/excross.php");
		//if($expcross_all==1) $csv_content = ExportCross("ALL"); else
		$csv_content = ExportCross($expcross_brands);
		
		if ($expcross_type==1) {
			require_once 'lib/excel/Classes/PHPExcel.php';
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->getActiveSheet()->setCellValue('A1','ARTICLE_NUMBER');
			$objPHPExcel->getActiveSheet()->setCellValue('B1','BRAND_NAME');
			$objPHPExcel->getActiveSheet()->setCellValue('C1','DISPLAY_NUMBER');
			$objPHPExcel->getActiveSheet()->setCellValue('D1','CROSS_BRAND_NAME');
			$objPHPExcel->getActiveSheet()->setTitle('Cross');
			$row=2;$ch='A';
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
			
		} else 
		if ($expcross_type==2) {
			header('Content-Type: text/csv; charset=utf-8');
			header('Content-Disposition: attachment; filename=t2cross.csv');
			$output = fopen('php://output', 'w');
			fputcsv($output, array("ARTICLE_NUMBER","BRAND_NAME","DISPLAY_NUMBER","CROSS_BRAND_NAME"),$delimiter = ';');
			foreach ($csv_content as $fields) {
				fputcsv($output,$fields,$delimiter = ';');
			}
		}
}

?>