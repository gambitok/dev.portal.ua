<?php
error_reporting(E_ERROR);
@ini_set('display_errors', true);
@ini_set('html_errors', false);
define('RD', dirname (__FILE__));
date_default_timezone_set("Europe/Kiev");
require_once (RD . "/../lib/DbSingleton.php");
$db = DbSingleton::getDb();
$dbt = DbSingleton::getTokoDb();

$month = date("Y-m-01");

$r = $db->query("SELECT * FROM `T2_ARTICLES_PARTITIONS` WHERE `status`=1;");
$n = $db->num_rows($r);
for ($i = 1; $i <= $n; $i++) {
	$art_id = $db->result($r,$i-1,"art_id");
	$op_type = $db->result($r,$i-1,"op_type");
	$parrent_type_id = $db->result($r,$i-1,"parrent_type_id");
	$parrent_doc_id = $db->result($r,$i-1,"parrent_doc_id");
	$amount = $db->result($r,$i-1,"amount");
	$rest = $db->result($r,$i-1,"rest");
	$reserv = $db->result($r,$i-1,"reserv");
	$price = $db->result($r,$i-1,"price");
	$oper_price = $db->result($r,$i-1,"oper_price");
	$price_buh_uah = $db->result($r,$i-1,"price_buh_uah");
	$price_man_uah = $db->result($r,$i-1,"price_man_uah");
	$db->query("INSERT INTO `T2_ARTICLES_PARTITIONS_PERIOD` (`month`,`art_id`,`op_type`,`parrent_type_id`,`parrent_doc_id`,`amount`,`rest`,`reserv`,`price`,`oper_price`,`price_buh_uah`,`price_man_uah`) 
	VALUES ('$month','$art_id','$op_type','$parrent_type_id','$parrent_doc_id','$amount','$rest','$reserv','$price','$oper_price','$price_buh_uah','$price_man_uah');");
}

$r = $dbt->query("SELECT * FROM `T2_ARTICLES_STRORAGE`;");
$n = $dbt->num_rows($r);
for ($i = 1; $i <= $n; $i++) {
	$art_id = $dbt->result($r,$i-1,"ART_ID");
	$amount = $dbt->result($r,$i-1,"AMOUNT");
	$reserv_amount = $dbt->result($r,$i-1,"RESERV_AMOUNT");
	$storage_id = $dbt->result($r,$i-1,"STORAGE_ID");
	$dbt->query("INSERT INTO `T2_ARTICLES_STRORAGE_PERIOD` (`MONTH`,`ART_ID`,`AMOUNT`,`RESERV_AMOUNT`,`STORAGE_ID`) 
	VALUES ('$month','$art_id','$amount','$reserv_amount','$storage_id');");
}

$r = $dbt->query("SELECT * FROM `T2_ARTICLES_STRORAGE_CELLS`;");
$n = $dbt->num_rows($r);
for ($i = 1; $i <= $n; $i++) {
	$art_id = $dbt->result($r,$i-1,"ART_ID");
	$amount = $dbt->result($r,$i-1,"AMOUNT");
	$reserv_amount = $dbt->result($r,$i-1,"RESERV_AMOUNT");
	$storage_id = $dbt->result($r,$i-1,"STORAGE_ID");
	$storage_cells_id = $dbt->result($r,$i-1,"STORAGE_CELLS_ID");
	$dbt->query("INSERT INTO `T2_ARTICLES_STRORAGE_CELLS_PERIOD` (`MONTH`,`ART_ID`,`AMOUNT`,`RESERV_AMOUNT`,`STORAGE_ID`,`STORAGE_CELLS_ID`) 
	VALUES ('$month','$art_id','$amount','$reserv_amount','$storage_id','$storage_cells_id');");
}

