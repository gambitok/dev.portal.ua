<?php

class cash_reports {
	
	function getCashReportsFilters() {
		$form_htm=RD."/tpl/cash_reports.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
		$form=str_replace("{date}",date("Y-m-d"),$form);
		$form=str_replace("{paybox_list}", $this->showPayBoxSelect(), $form);
		return $form;
	}
	
	function showPayBoxSelect() { $db=DbSingleton::getDb(); $list="";
		$r=$db->query("select * from PAY_BOX where status=1 and in_use=1;"); $n=$db->num_rows($r); 
		for ($i=1;$i<=$n;$i++){
			$id=$db->result($r,$i-1,"id");
			$name=$db->result($r,$i-1,"name");
			$list.="<option value=$id>$name</option>";
		}
		return $list;
	}
	
	function getPayBoxName($id) { $db=DbSingleton::getDb();
		$r=$db->query("select name from PAY_BOX where id='$id';");
		$name=$db->result($r,0,"name");
		return $name;
	}
	
	function showCashReportsList($date_start,$date_end,$payboxes,$cash_id) { $db=DbSingleton::getDb(); $list=""; $summ_kasa=0;													   
		// КАСА														   
		$r=$db->query("select id,paybox_id,pay_type_id from J_PAY 
		where paybox_id in ($payboxes) 
		and pay_type_id in (89,90,91,98)
		and data_time>='$date_start 00:00:00' and data_time<='$date_end 23:59:59'
		and cash_id='$cash_id'
		group by paybox_id;"); $n=$db->num_rows($r); 	
																   
		for ($i=1;$i<=$n;$i++){
			$id=$db->result($r,$i-1,"id"); 
			$paybox_id=$db->result($r,$i-1,"paybox_id"); 
			$pay_type_id=$db->result($r,$i-1,"pay_type_id"); 
			$name=$this->getPayBoxName($paybox_id);
			$summ_1=$this->getSummPayBox($date_start,$date_end,$paybox_id,"89",$cash_id);
			$summ_2=$this->getSummPayBox($date_start,$date_end,$paybox_id,"90",$cash_id);
			$summ_3=$this->getSummPayBox($date_start,$date_end,$paybox_id,"98",$cash_id);
			$summ_4=($this->getSummPayBox($date_start,$date_end,$paybox_id,"91",$cash_id))*(-1);
			$summ=$summ_1+$summ_2+$summ_3+$summ_4;
			$summ_prixod=$summ_1+$summ_2+$summ_3;
			$summ_vidacha=$summ_4;
			$summ_kasa+=$summ;
			$list.="<tr>
				<td>$name</td>
				<td>$summ_prixod</td>
				<td>$summ_vidacha</td>
				<td>$summ</td>
			</tr>";
		}
																   
		$list.="<tr style='background:lightgreen;'>
			<td><b>Каси</b></td>
			<td></td>
			<td></td>
			<td>$summ_kasa</td>
		</tr>";	
																   
																   
		// RASXODI		
		$array_spend=$this->getSpendTypes(); $summ_vidatki=0; 
													   
		$r=$db->query("select id,paybox_id_from from J_MONEY_SPEND 
		where paybox_id_from in ($payboxes) 
		and data>='$date_start 00:00:00' and data<='$date_end 23:59:59'
		and cash_id='$cash_id'
		group by paybox_id_from;"); $n=$db->num_rows($r); 		

		for ($i=1;$i<=$n;$i++){
			$id=$db->result($r,$i-1,"id"); 
			$paybox_id=$db->result($r,$i-1,"paybox_id_from"); 
			$name=$this->getPayBoxName($paybox_id);
			$summ=$this->getSummMoneySpend($date_start,$date_end,$paybox_id,$cash_id);

			$summ_spend=0;
			foreach ($array_spend as $spend_type_id) {
				$caption=$this->getSpendTypesCaption($spend_type_id);	
				$summ_type=$this->getSummMoneySpendType($date_start,$date_end,$paybox_id,$spend_type_id,$cash_id);
				$summ_spend+=$summ_type;
				if ($summ_type>0)
				$list.="<tr>
					<td>$caption</td>
					<td>-</td>
					<td>$summ_type</td>
					<td>$summ_type</td>
				</tr>";	
			}

			$summ_vidatki+=$summ_spend;

			$list.="<tr>
				<td><b>$name</b></td>
				<td>-</td>
				<td>-</td>
				<td>$summ_spend</td>
			</tr>";
		}
																   
		$list.="<tr style='background:pink;'>
			<td><b>Видатки</b></td>
			<td></td>
			<td></td>
			<td>$summ_vidatki</td>
		</tr>";															   
																   
		if ($date_start!=null) {
		$date_start = strtotime($date_start);
		$date_convert_start=date('d.m.Y', $date_start); } else $date_convert_start=$date_start;	
																   
		if ($date_end!=null) {
		$date_end = strtotime($date_end);
		$date_convert_end=date('d.m.Y', $date_end); } else $date_convert_end=$date_end;	
																			
		if ($cash_id==1) {
			$form_htm=RD."/tpl/cash_reports_table.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}	
			$form=str_replace("{cash_abr}","UAH",$form);
		}																			
		if ($cash_id==2) {
			$form_htm=RD."/tpl/cash_reports_table_usd.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}	
			$form=str_replace("{cash_abr}","USD",$form);
		}																	
		if ($cash_id==3) {
			$form_htm=RD."/tpl/cash_reports_table_eur.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}	
			$form=str_replace("{cash_abr}","EUR",$form);
		}	
																			
		$form=str_replace("{date_start}",$date_convert_start,$form);
		$form=str_replace("{date_end}",$date_convert_end,$form);
		$form=str_replace("{reports_range}",$list,$form);
		return $form;
	}	
	
	function getSpendTypes() { $db=DbSingleton::getDb(); $array=[];
		$r=$db->query("select id from manual where `key`='spend_type_id';"); $n=$db->num_rows($r);  
		for ($i=1;$i<=$n;$i++){
			$id=$db->result($r,$i-1,"id"); 
			array_push($array,$id);
		}
		return $array;
	}
	
	function getSpendTypesCaption($id) { $db=DbSingleton::getDb();
		$r=$db->query("select * from manual where `id`='$id';");
		$caption=$db->result($r,0,"mcaption"); 
		return $caption;
	}
	
	function getSummPayBox($date_start,$date_end,$paybox_id,$pay_type_id,$cash_id) { $db=DbSingleton::getDb(); $list=""; $summ=0;	 												   
		$r=$db->query("select sum(summ) as pay_summ from J_PAY 
		where paybox_id=$paybox_id 
		and pay_type_id=$pay_type_id
		and data_time>='$date_start 00:00:00' and data_time<='$date_end 23:59:59'
		and cash_id='$cash_id'
		group by paybox_id;");						
		$summ=$db->result($r,0,"pay_summ");
		return $summ;
	}
	
	function getSummMoneySpend($date_start,$date_end,$paybox_id,$cash_id) { $db=DbSingleton::getDb(); $list=""; $summ=0;	 												   
		$r=$db->query("select id,paybox_id_from,sum(summ) as summa from J_MONEY_SPEND 
		where paybox_id_from=$paybox_id
		and data>='$date_start 00:00:00' and data<='$date_end 23:59:59'
		and cash_id='$cash_id'
		group by paybox_id_from;"); $n=$db->num_rows($r); 				
		$summ=$db->result($r,0,"summa");
		return $summ;
	}
	
	function getSummMoneySpendType($date_start,$date_end,$paybox_id,$spend_type_id,$cash_id) { $db=DbSingleton::getDb(); $list=""; $summ=0;
		$r=$db->query("select id,paybox_id_from,sum(summ) as summa from J_MONEY_SPEND 
		where paybox_id_from=$paybox_id
		and data>='$date_start 00:00:00' and data<='$date_end 23:59:59'
		and spend_type_id='$spend_type_id'
		and cash_id='$cash_id'
		group by paybox_id_from;"); $n=$db->num_rows($r); 						
		$summ=$db->result($r,0,"summa");
		return $summ;
	}
	
}