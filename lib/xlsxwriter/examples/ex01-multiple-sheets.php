#<?php
set_include_path( get_include_path().PATH_SEPARATOR."..");
include_once("xlsxwriter.class.php");

header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename("test.xls").'"');
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header('Content-Transfer-Encoding: binary');
header('Cache-Control: must-revalidate');
header('Pragma: public');


$header = array(
    'year'=>'string',
    'month'=>'string',
    'amount'=>'price',
    'first_event'=>'datetime',
    'second_event'=>'date',
);
$data1 = array(
    array('2003','1','-50.5','2010-01-01 23:00:00','2012-12-31 23:00:00'),
    array('2003','=B2', '23.5','2010-01-01 00:00:00','2012-12-31 00:00:00'),
    array('2003',"'=B2", '23.5','2010-01-01 00:00:00','2012-12-31 00:00:00'),
);
$data2 = array(
    array('2003','01','343.12','4000000000'),
    array('2003','02','345.12','2000000000'),
);
$writer = new XLSXWriter();
$writer->writeSheetHeader('Sheet1', $header);
foreach($data1 as $row)
	$writer->writeSheetRow('Sheet11', $row);
foreach($data2 as $row)
	$writer->writeSheetRow('Sheet21', $row);

//$writer->writeToFile('xlsx-sheets.xlsx');
$writer->writeToStdOut();
//echo $writer->writeToString();

exit(0);


#