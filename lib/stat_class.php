<?php

class stats {

	function getCeoStat1(){ $db=DbSingleton::getDb();$today=date("Y-m-d");$summ_sale_invoce="";$summ_dp_inwork="";$summ_site_orders="";
		$r=$db->query("select count(id) as kol from J_SALE_INVOICE where status=1 and data_create='$today';");$amount_sale_invoce=$db->result($r,0,"kol");
	    $r=$db->query("select sum(summ) as kol, cash_id from J_SALE_INVOICE where status=1 and data_create='$today' group by cash_id;");$n=$db->num_rows($r);
	    for ($i=1;$i<=$n;$i++){
		    $summ_sale_invoce.=$db->result($r,$i-1,"kol").$this->getCashName($db->result($r,$i-1,"cash_id"))." &nbsp;";
	    }
						   
		$r=$db->query("select count(id) as kol from J_DP where status=1 and data='$today' and status_dp<81 and doc_type_id!=0;");$amount_dp_inwork=$db->result($r,0,"kol");
	    $r=$db->query("select sum(summ) as kol, cash_id from J_DP where status=1 and data='$today' and status_dp<81 and doc_type_id!=0 group by cash_id;");$n=$db->num_rows($r);
	    for ($i=1;$i<=$n;$i++){
		    $summ_dp_inwork.=$db->result($r,$i-1,"kol").$this->getCashName($db->result($r,$i-1,"cash_id"))." &nbsp;";
	    }				   
						   
		$r=$db->query("select count(id) as kol from orders_new where status=1 and data>='$today 00:00:00' and data<='$today 23:59:59';");$amount_site_orders=$db->result($r,0,"kol");
	    $r=$db->query("select sum(price_summ) as kol, cash_id from orders_new where status=1 and data>='$today 00:00:00' and data<='$today 23:59:59' group by cash_id;");$n=$db->num_rows($r);
	    for ($i=1;$i<=$n;$i++){
		    $summ_site_orders.=$db->result($r,$i-1,"kol").$this->getCashName($db->result($r,$i-1,"cash_id"))." &nbsp;";
	    }
						   
		$r=$db->query("select count(id) as kol from J_BACK_CLIENTS where status=1 and data='$today';");$amount_back_clients=$db->result($r,0,"kol");
	    $r=$db->query("select sum(summ) as kol, cash_id from J_BACK_CLIENTS where status=1 and data='$today' group by cash_id;");$n=$db->num_rows($r);$summ_back_clients="&nbsp;";
	    for ($i=1;$i<=$n;$i++){
		    $summ_back_clients.=$db->result($r,$i-1,"kol").$this->getCashName($db->result($r,$i-1,"cash_id"))." &nbsp;";
	    }
		return array($amount_sale_invoce,$summ_sale_invoce,$amount_dp_inwork,$summ_dp_inwork,$amount_site_orders,$summ_site_orders,$amount_back_clients,$summ_back_clients);
	}

	function getCashName($cash_id){$db=DbSingleton::getDb();$name="";
        $r=$db->query("select abr from CASH where id ='$cash_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){$name=$db->result($r,0,"abr");}
        return $name;
    }

}