<?php

class report_overdraft {
	
	function getTpointbyUser() { $db=DbSingleton::getDb(); $tpoint=1;
		$user_id=$_SESSION["media_user_id"];
		$r=$db->query("select tpoint_id from media_users where id='$user_id';"); $n=$db->num_rows($r);
		if ($n>0) $tpoint=$db->result($r,0,"tpoint_id");
		return $tpoint;
	}
	
	function getClientOverdraftList($date_cur,$tpoint_id = null) { $db=DbSingleton::getDb(); $clients=[]; $list="<option value='0'>пїЅпїЅ пїЅлієпїЅпїЅпїЅ</option>";
		$where=" and sv.data_pay<'$date_cur'";
		if ($tpoint_id!="0" && $tpoint_id!=NULL) $where_tpoint=" and cc.tpoint_id=$tpoint_id "; else $where_tpoint="";
		$r=$db->query("select cc.client_id from J_SALE_INVOICE sv 
		left outer join A_CLIENTS_CONDITIONS cc on cc.client_id=sv.client_id
		where sv.status=1 and sv.summ_debit>0 $where_tpoint $where;"); $n=$db->num_rows($r);
		for ($i=1;$i<=$n;$i++){
			$client_id=$db->result($r,$i-1,"client_id");
			array_push($clients,$client_id);
		}
		$clients=array_unique($clients); $clients=implode(",",$clients);
		$r=$db->query("select * from A_CLIENTS where id in ($clients) order by id asc;"); $n=$db->num_rows($r);
		for ($i=1;$i<=$n;$i++){
			$id=$db->result($r,$i-1,"id");
			$caption=$db->result($r,$i-1,"full_name");
			$list.="<option value='$id'>$id - $caption</option>";
		}										
		return $list;
	}
	
	function getTpointList() { $db=DbSingleton::getDb(); $tpoint=$this->getTpointbyUser(); $list="<option value='0'>Всі торгові точки</option>";
		$r=$db->query("select * from T_POINT where status=1 order by id asc;"); $n=$db->num_rows($r);
		for ($i=1;$i<=$n;$i++){
			$id=$db->result($r,$i-1,"id");
			$name=$db->result($r,$i-1,"name");
			$caption=$db->result($r,$i-1,"full_name");
			if ($id==$tpoint) $selected="selected"; $selected="";
			$list.="<option value='$id' $selected>$caption ($name)</option>";
		}
		return $list;	
	}
	
	function getClientList() { $db=DbSingleton::getDb(); $list="";
		$r=$db->query("select c.id, c.full_name from A_CLIENTS c order by c.id asc;"); $n=$db->num_rows($r);
		for ($i=1;$i<=$n;$i++){
			$id=$db->result($r,$i-1,"id");
			$caption=$db->result($r,$i-1,"full_name");
			$list.="<option value='$id'>$id - $caption</option>";
		}
		return $list;
	}
	
	function getMediaUserName($user_id){$db=DbSingleton::getDb();$name="";
		$r=$db->query("select name from media_users where id='$user_id' limit 0,1;");$n=$db->num_rows($r);
		if ($n==1){$name=$db->result($r,0,"name");}
		return $name;
	}
	
	function showReportOverdraftList($date_cur,$client_id,$tpoint_id) { $db=DbSingleton::getDb();
        $summ_uah=$summ_usd=$summ_eur=0; $list=""; $clients=[];
        $form="";$form_htm=RD."/tpl/report_overdraft_list.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
		if ($tpoint_id!="0" && $tpoint_id!=NULL) $where_tpoint=" and sv.tpoint_id='$tpoint_id' "; else $where_tpoint="";
		if ($client_id!="0" && $client_id!=NULL) $where=" and sv.data_pay<'$date_cur' and sv.client_id=$client_id"; else $where=" and sv.data_pay<'$date_cur'";
		$r=$db->query("select sv.*, dp.prefix as dp_prefix, dp.doc_nom as dp_nom, cl.name as client_name, ch.abr2 as cash_abr from J_SALE_INVOICE sv
			left outer join J_DP dp on dp.id=sv.dp_id
			left outer join CASH ch on ch.id=sv.cash_id
			left outer join A_CLIENTS cl on cl.id=sv.client_conto_id
		where sv.status=1 and sv.summ_debit>0 $where $where_tpoint order by sv.time_stamp desc, sv.status_invoice asc, sv.data_create desc, sv.prefix asc, sv.id desc;");
		$n=$db->num_rows($r);
		for ($i=1;$i<=$n;$i++){
//			$id=$db->result($r,$i-1,"id");
//			$dp_id=$db->result($r,$i-1,"dp_id"); $dp_nom=$db->result($r,$i-1,"dp_prefix").$db->result($r,$i-1,"dp_nom");
//			$prefix=$db->result($r,$i-1,"prefix");
//			$doc_nom=$db->result($r,$i-1,"doc_nom");
			$client_id=$db->result($r,$i-1,"client_id");
			$client_name=$db->result($r,$i-1,"client_name");;
			$summ=$db->result($r,$i-1,"summ_debit");
			$cash_id=$db->result($r,$i-1,"cash_id");
			$cash_abr=$db->result($r,$i-1,"cash_abr");
			$data_pay=$db->result($r,$i-1,"data_pay");
			if ($cash_id==1) $summ_uah+=$summ; if ($cash_id==2) $summ_usd+=$summ; if ($cash_id==3) $summ_eur+=$summ;
			$clients[$client_id]["title"]=$client_name;
			$clients[$client_id]["summ"]+=$summ;
			if (!isset($clients[$client_id]["cash"])) $clients[$client_id]["cash"]=array();
			if (!isset($clients[$client_id]["data_pay"])) $clients[$client_id]["data_pay"]=array();
			array_push($clients[$client_id]["cash"],$cash_abr);
			array_push($clients[$client_id]["data_pay"],$data_pay);
		}
										
		foreach ($clients as $c_id=>$client) {
			$title=$client["title"];
			$summ=$client["summ"];
			$cash=$client["cash"]; $cash=array_unique($cash); $cash=implode(",",$cash);
			$data_pay=$client["data_pay"]; $data_pay=min($data_pay);
			$list.="<tr style='cursor:pointer;background:#ddd;' align='center'>
				<td align='left'><button class='btn btn-xs' title='Взаєморозрахунки з контрагентом' onclick='showClientGeneralSaldoForm($c_id);'><i class='fa fa-th-list'></i></button> $title</td>
				<td align='center' style='min-width:80px;'>$summ</td>
				<td align='center' style='min-width:80px;'>$cash</td>
				<td align='right'>$data_pay</td>
				<td align='right'>&nbsp</td>
				<td align='right'>&nbsp</td>
				<td align='right'>&nbsp</td>
				<td align='right'>&nbsp</td>
			</tr>";
			$list.=$this->getClientReportOverdraftList($c_id,$date_cur,$tpoint_id);
		}
										
		$form=str_replace("{report_overdraft_range}",$list,$form);
		$transform_date=date("d.m.Y", strtotime($date_cur));
		$summ_all="СУМА за $transform_date: $summ_uah UAH / $summ_usd USD / $summ_eur EUR";
		$form=str_replace("{report_overdraft_summ}", $summ_all,$form);
		return array($form,$summ_all);
	}
	
	function getProlognationData($invoice_id,$client_id) { $db=DbSingleton::getDb(); 
		$r=$db->query("select min(date_pay_start) as min_date from J_SALE_INVOICE_PROLONGATION where invoice_id='$invoice_id' and client_id='$client_id';");
		$min_date=$db->result($r,0,"min_date");
		return $min_date;
	}
	
	function getProlognationCount($invoice_id,$client_id) { $db=DbSingleton::getDb(); 
		$r=$db->query("select count(date_pay_start) as amount_date from J_SALE_INVOICE_PROLONGATION where invoice_id='$invoice_id' and client_id='$client_id';");
		$amount_date=$db->result($r,0,"amount_date");
		return $amount_date;
	}
	
	function getClientReportOverdraftList($client_id,$date_cur,$tpoint_id) { $db=DbSingleton::getDb();
        $summ_uah=$summ_usd=$summ_eur=0; $list=""; $docs=[];
		$where=" and sv.data_pay<'$date_cur' and sv.client_id=$client_id"; 
		if ($tpoint_id!="0" && $tpoint_id!=NULL) $where_tpoint=" and sv.tpoint_id=$tpoint_id "; else $where_tpoint="";
		$r=$db->query("select sv.*, dp.prefix as dp_prefix, dp.doc_nom as dp_nom, cl.name as client_name, ch.abr2 as cash_abr from J_SALE_INVOICE sv
			left outer join J_DP dp on dp.id=sv.dp_id
			left outer join CASH ch on ch.id=sv.cash_id
			left outer join A_CLIENTS cl on cl.id=sv.client_conto_id
		where sv.status=1 and sv.summ_debit>0 $where $where_tpoint order by sv.data_pay asc, sv.status_invoice asc, sv.data_create desc, sv.prefix asc, sv.id desc;");
		$n=$db->num_rows($r);
		for ($i=1;$i<=$n;$i++){
			$id=$db->result($r,$i-1,"id");
			//$dp_id=$db->result($r,$i-1,"dp_id"); $dp_nom=$db->result($r,$i-1,"dp_prefix").$db->result($r,$i-1,"dp_nom");
			$prefix=$db->result($r,$i-1,"prefix");
			$doc_nom=$db->result($r,$i-1,"doc_nom");
			$client_id=$db->result($r,$i-1,"client_id");
			//$client_name=$db->result($r,$i-1,"client_name");
			$summ=$db->result($r,$i-1,"summ_debit");
			$cash_id=$db->result($r,$i-1,"cash_id");
			$cash_abr=$db->result($r,$i-1,"cash_abr");
			$data_pay=$db->result($r,$i-1,"data_pay");
			if ($cash_id==1) $summ_uah+=$summ; if ($cash_id==2) $summ_usd+=$summ; if ($cash_id==3) $summ_eur+=$summ;
			$docs[$i]["title"]="$prefix-$doc_nom";
			$docs[$i]["summ"]=$summ;
			$docs[$i]["cash"]=$cash_abr;
			$docs[$i]["data_pay"]=$data_pay;
			$docs[$i]["invoice_id"]=$id;
		}
										
		foreach ($docs as $doc) {
			$title=$doc["title"];
			$summ=$doc["summ"];
			$cash=$doc["cash"]; 
			$data_pay=$doc["data_pay"]; 
			$invoice_id=$doc["invoice_id"];
			$diff=$this->getDiffDate($data_pay,$date_cur);
			
			$prolog_date=$this->getProlognationData($invoice_id,$client_id);
			if ($prolog_date!="") $prolog_diff=$this->getDiffDate($data_pay,$prolog_date); else $prolog_diff="";
			$prolog_amount=$this->getProlognationCount($invoice_id,$client_id); 
			if ($prolog_amount==0) $prolog_amount=""; 
			if ($prolog_amount>0) $js="<button class='btn btn-xs' title='Пролонгація документа' onclick='showDocsProlongationForm($client_id,$invoice_id);'><i class='fa fa-file'></i></button>"; else $js="";
			
			$list.="<tr align='center' style='cursor:pointer'>
				<td align='left'> $js $title</td>
				<td align='center' style='min-width:80px;' onClick='showSaleInvoiceCard(\"$invoice_id\");'>$summ</td>
				<td align='center' style='min-width:80px;' onClick='showSaleInvoiceCard(\"$invoice_id\");'>$cash</td>
				<td align='right' onClick='showSaleInvoiceCard(\"$invoice_id\");'>$data_pay</td>
				<td align='right' onClick='showSaleInvoiceCard(\"$invoice_id\");'>$diff</td>
				<td align='right' onClick='showSaleInvoiceCard(\"$invoice_id\");'>$prolog_date</td>
				<td align='right' onClick='showSaleInvoiceCard(\"$invoice_id\");'>$prolog_diff</td>
				<td align='right' onClick='showSaleInvoiceCard(\"$invoice_id\");'>$prolog_amount</td>
			</tr>";
		}
		return $list;
	}
	
	function getDiffDate($date1,$date2) {
		$earlier=new DateTime($date1); 
		$later=new DateTime($date2); 
		$diff=$later->diff($earlier)->format("%a");
		return $diff;
	}
	
	function showDocsProlongationForm($client_id,$invoice_id) { $db=DbSingleton::getDb(); $list="";
		$r=$db->query("select * from J_SALE_INVOICE_PROLONGATION where invoice_id='$invoice_id' and client_id='$client_id';"); $n=$db->num_rows($r);
		for ($i=1;$i<=$n;$i++){
			$user_id=$db->result($r,$i-1,"user_id"); $user_name=$this->getMediaUserName($user_id);
			$date_pay_new=$db->result($r,$i-1,"date_pay_new");
			$list.="<li>Дата: $date_pay_new; Користувач: $user_name;</li>";
		}
		$list.="</ul>";
		return $list;
	}
	
}