<?php
error_reporting(E_ERROR);
@ini_set('display_errors', true);
@ini_set('html_errors', false);
define('RD', dirname (__FILE__));
date_default_timezone_set("Europe/Kiev");
require_once (RD."/../lib/DbSingleton.php");
$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();
require_once (RD."/../lib/slave_class.php");

function getClientBalansPeriodStart($client_id,$cash_id,$data_from){$db=DbSingleton::getDb();
    $saldo_start=0;$saldo_data_start=$data_from;$saldo_end=0;
	$r=$db->query("SELECT * FROM `B_CLIENT_BALANS_PERIOD` WHERE `client_id`='$client_id' AND `data_start`='".date("Y-m-01",strtotime($data_from))."' LIMIT 1;");
	$n=$db->num_rows($r);
	if ($n==1){
		$saldo_start=$db->result($r,0,"saldo_start");
		$saldo_data_start=$db->result($r,0,"data_start");
		$saldo_end=$db->result($r,0,"saldo_end");
	}
	if ($n==0){
		$saldo_start=0;
		$saldo_data_start=$data_from;
		$saldo_end=0;
		$db->query("INSERT INTO `B_CLIENT_BALANS_PERIOD` (`client_id`,`cash_id`,`saldo_start`,`data_start`,`active`) VALUES ('$client_id','$cash_id','0','$data_from','1');");
	}
	return array($saldo_start,$saldo_data_start,$saldo_end);
}

function getClientBalansPeriodEnd($client_id,$data_from,$data_to){$db=DbSingleton::getDb();
	$saldo_end=0;
	$r=$db->query("SELECT b.*, mc.abr as cash_name, pmc.abr 
	FROM `B_CLIENT_BALANS_JOURNAL` b 
		LEFT OUTER JOIN `CASH` mc on mc.id=b.cash_id 
		LEFT OUTER JOIN `CASH` pmc on pmc.id=b.pay_cash_id 
	WHERE b.client_id='$client_id' AND b.data>='$data_from 00:00:00' AND b.data<='$data_to 23:59:59' GROUP BY b.doc_type_id, b.doc_id ORDER BY b.id DESC LIMIT 1;");
	$n=$db->num_rows($r);
	if ($n==1){
		$saldo_end=$db->result($r,0,"balans_after");
	}
	return $saldo_end;
}	

$data_from=date("Y-m-01",strtotime("-1 month"));
$data_to=date("Y-m-d",strtotime("-1 month"));
$data_to=date("Y-m-t",strtotime($data_to));

$r1=$db->query("SELECT c.id, cc.cash_id FROM `A_CLIENTS` c 
    INNER JOIN `A_CLIENTS_CONDITIONS` cc on cc.client_id=c.id
WHERE c.status=1;");$n1=$db->num_rows($r1);

for ($i1=1;$i1<=$n1;$i1++){
	$client_id=$db->result($r1,$i1-1,"id");
	$cash_id=$db->result($r1,$i1-1,"cash_id");
	list($saldo_start,$saldo_data_start,$saldo_end)=getClientBalansPeriodStart($client_id,$cash_id,$data_from);
	if ($saldo_end==0){
		$saldo_end=getClientBalansPeriodEnd($client_id,$data_from,$data_to);
		$db->query("UPDATE `B_CLIENT_BALANS_PERIOD` SET `data_end`='$data_to', `saldo_end`='$saldo_end', `active`=0 
		WHERE `client_id`='$client_id' AND `cash_id`='$cash_id' AND `data_start`='$data_from' AND `active`='1';");
		$db->query("INSERT INTO `B_CLIENT_BALANS_PERIOD` (`client_id`,`cash_id`,`saldo_start`,`data_start`,`active`) 
		VALUES ('$client_id','$cash_id','$saldo_end','".date("Y-m-01")."','1');");
	}
}

