<?php
error_reporting(E_ERROR);
@ini_set('display_errors', true);
@ini_set('html_errors', false);
define('RD', dirname (__FILE__));
$content=null;
require_once (RD."/lib/mysql_class.php");
require_once (RD."/lib/slave_class.php");
require_once (RD."/lib/config_class.php");
require_once (RD."/event/get_access.php");
require_once (RD."/lib/module_class.php");
require_once (RD."/lib/access_class.php");
require_once (RD."/lib/manual_class.php");

$w=$_REQUEST["w"];$id=$_REQUEST["id"];$file=$_REQUEST["file"];$tOp=$_REQUEST["tOp"];if ($tOp==""){$tOp="I";}$tOpPath="";if ($tOp=="F"){$tOpPath=RD."/uploads/print/";}

$html="";


if ($w=="bidEmpty"){ $form_htm=RD."/uploads/blanks/bid_blank.htm";if (file_exists("$form_htm")){ $html = file_get_contents($form_htm);}}
if ($w=="bidForm" && $id!="" && is_numeric($id)){ require_once (RD."/lib/bids_class.php");$bids=new bid; $html=$bids->printBidForm($id);}
if ($w=="bidBill" && $id!="" && is_numeric($id)){ require_once (RD."/lib/bids_class.php");$bids=new bid; $html=$bids->printBidBillForm($id);}
if ($w=="bidDoc" && $id!="" && is_numeric($id)){ require_once (RD."/lib/bids_class.php");$bids=new bid; $html=$bids->printBidDocForm($id,$_REQUEST["sign"]);}
if ($w=="bidAct" && $id!="" && is_numeric($id)){ require_once (RD."/lib/bids_class.php");$bids=new bid; $html=$bids->printBidActForm($id);}
if ($w=="printEstreportFinal" && $id!="" && is_numeric($id)){ require_once (RD."/lib/estreport_class.php");$estreport=new estreport; $html=$estreport->printEstreportFinal($id);}



if ($w=="bidClient"){ $form_htm=RD."/uploads/blanks/bid_blank.htm";if (file_exists("$form_htm")){ $html = file_get_contents($form_htm);} }
if ($w=="bidsBlankPrint"){ $form_htm=RD."/uploads/blanks/bid_blank.htm";if (file_exists("$form_htm")){ $html = file_get_contents($form_htm);} }
if ($w=="docsBlankPrint"){ $form_htm=RD."/uploads/blanks/doc_shablon.htm";if (file_exists("$form_htm")){ $html = file_get_contents($form_htm);} }
if ($w=="fromJPGfile"){ $html = "<img src='uploads/$file' border=0>"; }
if ($w=="clientForm" && $id!="" && is_numeric($id)){
	require_once (RD."/lib/clients_class.php");$cl=new client;
	$html=$cl->printClientCard($id);
}
if ($w=="docDogovor" && $id!="" && is_numeric($id)){
	require_once (RD."/lib/docs_class.php");$docs=new doc;
	$html=$docs->getDocText($id);
}
if ($w=="anketaClient" && $id!="" && is_numeric($id)){ require_once (RD."/lib/anketa_class.php");$anketa=new anketa;$html=$anketa->printAnketaClient($id); }
if ($w=="anketaBlankPrint" && $id!="" && is_numeric($id)){ require_once (RD."/lib/anketa_class.php");$anketa=new anketa;$html=$anketa->printAnketaBlank($id); }

//$html=iconv("utf-8","windows-1251",$html);
include("MPDF56/mpdf.php");
$mpdf = new mPDF('cp1251', 'A4', '14', '', 20, 10, 10, 20, 10, 10); /*задаем формат, отступы и.т.д.*/
$mpdf->charset_in = 'cp1251'; /*не забываем про русский*/

$stylesheet = file_get_contents('style.css'); /*подключаем css*/
$mpdf->WriteHTML($stylesheet, 1);

$mpdf->list_indent_first_level = 0;
$mpdf->setFooter('www.nazaret-ltd.com.ua||{PAGENO}');
$mpdf->WriteHTML($html, 2); /*формируем pdf*/



$mpdf->Output($tOpPath.'mpdf-'.date("Y-m-d-H-i-s").'.pdf', $tOp);


?>