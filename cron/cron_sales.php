<?php
error_reporting(E_ERROR);
@ini_set('display_errors', true);
@ini_set('html_errors', false);
define('RD', dirname (__FILE__));
date_default_timezone_set("Europe/Kiev");
require_once (RD."/../lib/DbSingleton.php");
$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();

$month_from=date("Y-m-01", strtotime("-1 month"));
$month_to=date("Y-m-31", strtotime("-1 month"));
$month=date("Y-m-00", strtotime("-1 month"));
//$month_from="2018-12-01";$month_to="2018-12-31";$month="2018-12-00";

$r=$db->query("SELECT sis.art_id, SUM(sis.amount) as sales, si.tpoint_id 
FROM `J_SALE_INVOICE_STR` sis 
    INNER JOIN `J_SALE_INVOICE` si ON (si.id=sis.invoice_id) 
WHERE si.data_create>='$month_from' AND si.data_create<='$month_to' GROUP BY sis.art_id, si.tpoint_id;");$n=$db->num_rows($r);
for ($i=1;$i<=$n;$i++){
	$art_id=$db->result($r,$i-1,"art_id");
	$tpoint_id=$db->result($r,$i-1,"tpoint_id");
	$sales=$db->result($r,$i-1,"sales");
	$dbt->query("INSERT INTO `T2_ARTICLES_SALES` (`ART_ID`,`TPOINT_ID`,`MONTH`,`AMOUNT`) VALUES ('$art_id','$tpoint_id','$month','$sales');");
}

$r=$db->query("SELECT bcs.art_id, SUM(bcs.amount) as backs, bc.tpoint_id 
FROM `J_BACK_CLIENTS_STR` bcs 
    INNER JOIN `J_BACK_CLIENTS` bc ON (bc.id=bcs.back_id) 
WHERE bc.data>='$month_from' AND bc.data<='$month_to' AND bc.status='1' AND bc.status_back='103' GROUP BY bcs.art_id, bc.tpoint_id;");$n=$db->num_rows($r);
for ($i=1;$i<=$n;$i++){
	$art_id=$db->result($r,$i-1,"art_id");
	$tpoint_id=$db->result($r,$i-1,"tpoint_id");
	$backs=$db->result($r,$i-1,"backs");
	$r1=$dbt->query("SELECT * FROM `T2_ARTICLES_SALES` WHERE `ART_ID`='$art_id' AND `TPOINT_ID`='$tpoint_id' AND `MONTH`='$month';");$n1=$dbt->num_rows($r1);
	if ($n1==0){
		$dbt->query("INSERT INTO `T2_ARTICLES_SALES` (`ART_ID`,`TPOINT_ID`,`MONTH`,`AMOUNT`) VALUES ('$art_id','$tpoint_id','$month','-$backs');");
	}
	if ($n1>0){
		$dbt->query("UPDATE `T2_ARTICLES_SALES` SET AMOUNT=AMOUNT-$backs WHERE `ART_ID`='$art_id' AND `TPOINT_ID`='$tpoint_id' AND `MONTH`='$month';");
	}
}

