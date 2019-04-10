<?php
error_reporting(E_ERROR);
@ini_set('display_errors', true);
@ini_set('html_errors', false);
define('RD', dirname (__FILE__));
require_once (RD."/../lib/DbSingleton.php");$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();

$month_from=date("Y-m-01", strtotime("-1 month"));$month_to=date("Y-m-31", strtotime("-1 month"));
$month=date("Y-m-00", strtotime("-1 month"));

//$month_from="2018-12-01";$month_to="2018-12-31";$month="2018-12-00";

$r=$db->query("select sis.art_id, sum(sis.amount) as sales, si.tpoint_id from J_SALE_INVOICE_STR sis inner join J_SALE_INVOICE si on si.id=sis.invoice_id where si.data_create>='$month_from' and si.data_create<='$month_to' GROUP by sis.art_id,si.tpoint_id;");$n=$db->num_rows($r); 
for ($i=1;$i<=$n;$i++){
	$art_id=$db->result($r,$i-1,"art_id");
	$tpoint_id=$db->result($r,$i-1,"tpoint_id");
	$sales=$db->result($r,$i-1,"sales");
	//print "<br>$art_id - $sales";
	$dbt->query("insert into T2_ARTICLES_SALES (`ART_ID`,`TPOINT_ID`,`MONTH`,`AMOUNT`) value ('$art_id','$tpoint_id','$month','$sales');");
}

$r=$db->query("select bcs.art_id, sum(bcs.amount) as backs, bc.tpoint_id from J_BACK_CLIENTS_STR bcs inner join J_BACK_CLIENTS bc on bc.id=bcs.back_id where bc.data>='$month_from' and bc.data<='$month_to' and bc.status='1' and bc.status_back='103' GROUP by bcs.art_id,bc.tpoint_id;");$n=$db->num_rows($r);
for ($i=1;$i<=$n;$i++){
	$art_id=$db->result($r,$i-1,"art_id");
	$tpoint_id=$db->result($r,$i-1,"tpoint_id");
	$backs=$db->result($r,$i-1,"backs");
	//print "<br>$art_id - $backs";
	$r1=$dbt->query("select * from T2_ARTICLES_SALES where `ART_ID`='$art_id' and `TPOINT_ID`='$tpoint_id' and `MONTH`='$month';");$n1=$dbt->num_rows($r1);
	if ($n1==0){
		$dbt->query("insert into T2_ARTICLES_SALES (`ART_ID`,`TPOINT_ID`,`MONTH`,`AMOUNT`) value ('$art_id','$tpoint_id','$month','-$backs');");
	}
	if ($n1>0){
		$dbt->query("update T2_ARTICLES_SALES set AMOUNT=AMOUNT-$backs where `ART_ID`='$art_id' and `TPOINT_ID`='$tpoint_id' and `MONTH`='$month';");
	}
	
}

?>