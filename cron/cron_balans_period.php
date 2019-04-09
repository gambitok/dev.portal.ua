<?php
error_reporting(E_ERROR);
@ini_set('display_errors', true);
@ini_set('html_errors', false);
define('RD', dirname (__FILE__));
require_once (RD."/../lib/DbSingleton.php");$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();
require_once (RD."/../lib/slave_class.php");


function getClientBalansPeriodStart($client_id,$cash_id,$data_from){$db=DbSingleton::getDb();$saldo_start=0;$saldo_data_start=$data_from;
	$r=$db->query("select * from B_CLIENT_BALANS_PERIOD where client_id='$client_id' and data_start='".date("Y-m-01",strtotime($data_from))."' limit 0,1;");
	$n=$db->num_rows($r); //print "n=$n<br>";
	if ($n==1){
		$saldo_start=$db->result($r,0,"saldo_start");
		$saldo_data_start=$db->result($r,0,"data_start");
		$saldo_end=$db->result($r,0,"saldo_end");
	}
	if ($n==0){
		$saldo_start=0;
		$saldo_data_start=$data_from;
		$saldo_end=0;
		$db->query("insert into B_CLIENT_BALANS_PERIOD (`client_id`,`cash_id`,`saldo_start`,`data_start`,`active`) values ('$client_id','$cash_id','0','$data_from','1');");
	}
	return array($saldo_start,$saldo_data_start,$saldo_end);
}

function getClientBalansPeriodEnd($client_id,$cash_id,$saldo_start,$data_from,$data_to){$db=DbSingleton::getDb();$slave=new slave;session_start();
	$saldo_data_start=$data_from; $saldo_data_end=$data_to; $saldo_end=0; //default value
	$r=$db->query("select b.*, mc.abr as cash_name, pmc.abr 
	from B_CLIENT_BALANS_JOURNAL b 
		left outer join CASH mc on mc.id=b.cash_id 
		left outer join CASH pmc on pmc.id=b.pay_cash_id 
	where b.client_id='$client_id' and b.data>='$data_from 00:00:00' and b.data<='$data_to 23:59:59' group by b.doc_type_id,b.doc_id order by b.id desc limit 0,1;");
	$n=$db->num_rows($r);$list="";
	if ($n==1){
		$saldo_end=$db->result($r,0,"balans_after");
		/*for ($i=1;$i<=$n;$i++){
			$id=$db->result($r,$i-1,"id");
			$data=$db->result($r,$i-1,"data");
			$cash_id=$db->result($r,$i-1,"cash_id");
			$cash_name=$db->result($r,$i-1,"cash_name");
			$summ=round($db->result($r,$i-1,"summ"),2);
			$deb_kre=$db->result($r,$i-1,"deb_kre");
			$balans_before=$db->result($r,$i-1,"balans_before");
			$balans_after=$db->result($r,$i-1,"balans_after");
			$doc_type_id=$db->result($r,$i-1,"doc_type_id"); // 1-Видаткова (SaleInvoice); 2- Оплата(Pay); 3-Автооплата(PayAuto) 
			$doc_id=$db->result($r,$i-1,"doc_id");
			$pay_cash_name=$db->result($r,$i-1,"pmc.abr");
			$pay_summ=$db->result($r,$i-1,"pay_summ");

			$debit="";$kredit="";
			if ($deb_kre==1){
				$debit=$summ;
				$saldo_end-=$debit;
			}
			if ($deb_kre==2){
				$kredit=$summ;
				$saldo_end+=$kredit;
			}

		}
		$saldo_end=round($saldo_end,2);
		*/
	}
	return $saldo_end;
}	

$data_from=date("Y-m-01",strtotime("-1 month"));
$data_to=date("Y-m-d",strtotime("-1 month"));
$data_to=date("Y-m-t",strtotime($data_to));
//$data_to=date("Y-m-31",strtotime("-1 month"));
//$data_from="2018-12-01";$data_to="2018-12-31";

$r1=$db->query("select c.id,cc.cash_id from A_CLIENTS c 
	inner join A_CLIENTS_CONDITIONS cc on cc.client_id=c.id
	inner join A_CLIENTS_CATEGORY ccat on ccat.client_id=c.id
where c.status=1 and ccat.category_id = 1;");$n1=$db->num_rows($r1);
// print "$data_from, $data_to; n1=$n1";

for ($i1=1;$i1<=$n1;$i1++){
	$client_id=$db->result($r1,$i1-1,"id");
	$cash_id=$db->result($r1,$i1-1,"cash_id");
	list($saldo_start,$saldo_data_start,$saldo_end)=getClientBalansPeriodStart($client_id,$cash_id,$data_from);
	
	if ($saldo_end==0){
		$saldo_end=getClientBalansPeriodEnd($client_id,$cash_id,$saldo_start,$data_from,$data_to);
		$db->query("update B_CLIENT_BALANS_PERIOD set data_end='$data_to', saldo_end='$saldo_end', `active`=0 where `client_id`='$client_id' and `cash_id`='$cash_id' and `data_start`='$data_from' and `active`='1';");
		$db->query("insert into B_CLIENT_BALANS_PERIOD (`client_id`,`cash_id`,`saldo_start`,`data_start`,`active`) values ('$client_id','$cash_id','$saldo_end','".date("Y-m-01")."','1');");
	}
}


?>