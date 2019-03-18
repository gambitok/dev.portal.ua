<?php

class report_clients {
		
	function getCashAbr($cash_id) {$db=new db; 
		$r=$db->query("select abr from CASH where id='$cash_id' limit 1");
		$cash_abr=$db->result($r,0,"abr");
	    return $cash_abr;
	}
	
	function getManuaType($id) { $db=new db;
		$r=$db->query("select mcaption from manual where id='$id';");
		$mcaption=$db->result($r,0,"mcaption");
		return $mcaption;
	}
	
	function getMediaUserName($user_id){$db=new db;$name="";
		$r=$db->query("select name from media_users where id='$user_id' limit 0,1;");$n=$db->num_rows($r);
		if ($n==1){$name=$db->result($r,0,"name");}
		return $name;
	}	
	
	function getSummCash($summ,$cash_id,$usd_to_uah,$eur_to_uah,$to_cash) {
		$summary=$summ;
		if ($to_cash==1) {
			if ($cash_id==2) $summary=$summ*$usd_to_uah;
			if ($cash_id==3) $summary=$summ*$eur_to_uah;
		}
		if ($to_cash==2) {
			if ($cash_id==1) $summary=$summ/$usd_to_uah;
			if ($cash_id==3) $summary=($summ*$usd_to_uah)/$eur_to_uah;
		}
		if ($to_cash==3) {
			if ($cash_id==1) $summary=$summ/$eur_to_uah;
			if ($cash_id==2) $summary=($summ*$usd_to_uah)/$eur_to_uah;
		}
		return round($summary,2);	
	}
	
	function getTpointName($tpoint_id){$db=new db;
		$r=$db->query("select name from T_POINT where id='$tpoint_id' limit 1;");
		$name=$db->result($r,0,"name");
		return $name;	
	}
	
	function getClientLocation($client_id) {$db=new db;
		$r=$db->query("select c.*, t2st.STATE_NAME, t2rg.REGION_NAME, t2ct.CITY_NAME, cc.tpoint_id from A_CLIENTS c 
			left outer join A_CLIENTS_CONDITIONS cc on cc.client_id=c.id 
			left outer join T2_STATE t2st on t2st.STATE_ID=c.state
			left outer join T2_REGION t2rg on t2rg.REGION_ID=c.region
			left outer join T2_CITY t2ct on t2ct.CITY_ID=c.city
		where c.status=1 and c.id=$client_id;");
		$state=$db->result($r,0,"STATE_NAME");
		$region=$db->result($r,0,"REGION_NAME");
		$city=$db->result($r,0,"CITY_NAME");
		$tpoint_id=$db->result($r,0,"tpoint_id"); $tpoint=$this->getTpointName($tpoint_id);
											
		return array($tpoint,$state,$region,$city);
	}
	
	function getSummReportsSales($date_start,$date_end,$cash_id,$client_id) { $db=new db; $summary=0;	 
	    $r=$db->query("select j.* from J_SALE_INVOICE j
		where j.time_stamp>='$date_start 00:00:00' and j.time_stamp<='$date_end 23:59:59'
		and j.client_conto_id='$client_id';"); $n=$db->num_rows($r);
		for ($i=1;$i<=$n;$i++){
			$usd_to_uah=$db->result($r,$i-1,"usd_to_uah"); 
			$eur_to_uah=$db->result($r,$i-1,"eur_to_uah"); 
			$cash=$db->result($r,$i-1,"cash_id"); 
			$summ=$db->result($r,$i-1,"summ");
			$summ=$this->getSummCash($summ,$cash,$usd_to_uah,$eur_to_uah,$cash_id);
			$summary+=$summ;
		}
	    return $summary;
	}
	
	function getSummReportsBacks($date_start,$date_end,$cash_id,$client_id) { $db=new db; $summary=0;	
		$r=$db->query("select j.* from J_BACK_CLIENTS j 
			left outer join J_SALE_INVOICE js on js.id=j.sale_invoice_id
		where j.time_stamp>='$date_start 00:00:00' and j.time_stamp<='$date_end 23:59:59'
		and js.client_conto_id='$client_id' and j.status_back=103;"); $n=$db->num_rows($r); 	
		for ($i=1;$i<=$n;$i++){
			$id=$db->result($r,$i-1,"id"); 
			$usd_to_uah=$db->result($r,$i-1,"usd_to_uah"); 
			$eur_to_uah=$db->result($r,$i-1,"eur_to_uah"); 
			$cash=$db->result($r,$i-1,"cash_id"); 
			$summ=$db->result($r,$i-1,"summ");
			$summ=$this->getSummCash($summ,$cash,$usd_to_uah,$eur_to_uah,$cash_id);
			$summary+=$summ;
		}
	    return $summary;
	}
	
	function showReportClients($date_start,$date_end,$clients,$cash_id,$tpoint_id) { $db=new db; $list=""; $sales=$backs=[]; $client=new clients;
		$form_htm=RD."/tpl/report_clients_table.htm";if (file_exists("$form_htm")){ $form=file_get_contents($form_htm);} $summ_list=0; 
																					  
		if($clients=="") $where=""; else $where="j.client_conto_id in ($clients) and ";	 			
		if($tpoint_id=="0") $where_tpoint=""; else $where_tpoint="and cc.tpoint_id=$tpoint_id";	
																					
	    $r=$db->query("select j.* from J_SALE_INVOICE j
			left outer join A_CLIENTS_CONDITIONS cc on cc.client_id=j.client_conto_id
		where $where 
		j.time_stamp>='$date_start 00:00:00' and j.time_stamp<='$date_end 23:59:59' $where_tpoint
		group by j.client_conto_id;"); $n=$db->num_rows($r); 

		for ($i=1;$i<=$n;$i++) {
			$id=$db->result($r,$i-1,"id"); 																			  
			$user_id=$db->result($r,$i-1,"user_id"); 
			$doc_type_id=$db->result($r,$i-1,"doc_type_id"); 
			$client_id=$db->result($r,$i-1,"client_conto_id");
			$usd_to_uah=$db->result($r,$i-1,"usd_to_uah"); 
			$eur_to_uah=$db->result($r,$i-1,"eur_to_uah"); 
			$cash=$db->result($r,$i-1,"cash_id");
			$summ=$this->getSummReportsSales($date_start,$date_end,$cash_id,$client_id);
			$sales[$client_id] = ["client_id"=>$client_id, "summ"=>$summ, "cash"=>$cash];
		}																																				
																					  
	    if($clients=="") $where=""; else $where="js.client_conto_id in ($clients) and ";
		if($tpoint_id=="0") $where_tpoint=""; else $where_tpoint="and cc.tpoint_id=$tpoint_id";
																					  
	    $r=$db->query("select j.*, js.user_id as sale_user from J_BACK_CLIENTS j
			left outer join J_SALE_INVOICE js on js.id=j.sale_invoice_id
			left outer join A_CLIENTS_CONDITIONS cc on cc.client_id=js.client_conto_id
		where $where 
		j.time_stamp>='$date_start 00:00:00' and j.time_stamp<='$date_end 23:59:59' and j.status_back=103 $where_tpoint
		group by js.client_conto_id;"); $n=$db->num_rows($r); 
																					 
		for ($i=1;$i<=$n;$i++) {
			$id=$db->result($r,$i-1,"id"); 																			  
			$user_id=$db->result($r,$i-1,"sale_user"); 
			$doc_type_id=$db->result($r,$i-1,"doc_type_id"); 
			$client_id=$db->result($r,$i-1,"client_id"); 
			$usd_to_uah=$db->result($r,$i-1,"usd_to_uah"); 
			$eur_to_uah=$db->result($r,$i-1,"eur_to_uah"); 
			$cash=$db->result($r,$i-1,"cash_id"); 
			$summ=$this->getSummReportsBacks($date_start,$date_end,$cash_id,$client_id);
			$backs[$client_id] = ["client_id"=>$client_id, "summ_deb"=>$summ, "cash"=>$cash];
		}	
					 
		foreach ($sales as $key=>$value) {
			$client_id=$value["client_id"];
			$cash=$value["cash"];
			$summ=$value["summ"];
			$summ_deb=$backs[$key]["summ_deb"]; 
			$client_name=$client->getClientNameById($client_id,"name"); 
			list($tpoint_id,$state,$region,$city)=$this->getClientLocation($client_id);
			$summ_all=$summ-$summ_deb;

			$list.="<tr style='background:pink'>
				<td>$tpoint_id</td>
				<td>$state</td>
				<td>$region</td>
				<td>$city</td>
				<td>$client_name</td>
				<td>$summ</td>
				<td>$summ_deb</td>
				<td>$summ_all</td>
			</tr>";
			$summ_list+=$summ_all;
		}
																					
		$form=str_replace("{report_clients_range}",$list,$form);
		$form=str_replace("{summ_reports}",$summ_list,$form);
		$form=str_replace("{cash_abr}",$this->getCashAbr($cash_id),$form);
		return $form;
	}
		
	function getSeoReportsSumm($date_start,$date_end,$managers,$cash_id,$client_status,$user_id) { $db=new db; $list=""; $clients=new clients;								$r=$db->query("select j.id, j.usd_to_uah, j.eur_to_uah, j.user_id, j.doc_type_id, j.summ as prodaga, jb.summ as vosvrat from J_SALE_INVOICE j
			cross join J_BACK_CLIENTS jb 
		where j.user_id='$user_id' 
		and (j.time_stamp>='$date_start 00:00:00' and j.time_stamp<='$date_end 23:59:59')
		or (jb.time_stamp>='$date_start 00:00:00' and jb.time_stamp<='$date_end 23:59:59')
		and j.cash_id='$cash_id'
		group by j.doc_type_id;"); $n=$db->num_rows($r); $prodaga=$vosvrat=0;			 														  						
		for ($i=1;$i<=$n;$i++){
			$id=$db->result($r,$i-1,"id"); 																										  
			$user_id=$db->result($r,$i-1,"user_id");
			$doc_type_id=$db->result($r,$i-1,"doc_type_id"); $doc_name=$this->getManuaType($doc_type_id);											
			$prodaga=$db->result($r,$i-1,"prodaga");
			$vosvrat=$db->result($r,$i-1,"vosvrat");
			$full_summ=$prodaga-$vosvrat; 
			$list.="<tr style='font-weight:bold;'>
				<td>$doc_name</td>
				<td>$prodaga</td>
				<td>$vosvrat</td>
				<td>$full_summ</td>
			</tr>";
			if ($client_status)
			$list.=$this->getSeoReportsSummClient($date_start,$date_end,$managers,$cash_id,$client_status,$user_id,$doc_type_id);
		}
		return $list;
	}
	
	function getSeoReportsSummClient($date_start,$date_end,$managers,$cash_id,$client_status,$user_id,$doc_type_id) { $db=new db; $list=""; $clients=new clients;			$r=$db->query("select j.id,j.user_id,j.client_id,j.doc_type_id,sum(j.summ) as prodaga from J_SALE_INVOICE j
		where j.user_id='$user_id' 
		and j.doc_type_id='$doc_type_id'
		and j.time_stamp>='$date_start 00:00:00' and j.time_stamp<='$date_end 23:59:59'
		and j.cash_id='$cash_id'
		group by j.client_id;"); $n=$db->num_rows($r);																	  
		for ($i=1;$i<=$n;$i++){
			$id=$db->result($r,$i-1,"id"); 																			  
			$user_id=$db->result($r,$i-1,"user_id");
			$client_id=$db->result($r,$i-1,"client_id"); $client_name=$clients->getClientNameById($client_id,"name");
			$doc_type_id=$db->result($r,$i-1,"doc_type_id"); $doc_name=$this->getManuaType($doc_type_id);											
			$prodaga=$db->result($r,$i-1,"prodaga"); 
			$vosvrat=$db->result($r,$i-1,"vosvrat"); 
			$list.="<tr>
				<td align='right'>$client_name</td>
				<td>$prodaga</td>
				<td>$vosvrat</td>
				<td></td>
			</tr>";
		}
		return $list;
	}
	
	function getCashList() { $db=new db;
		$r=$db->query("select * from CASH"); $n=$db->num_rows($r); $list="";				
		for ($i=1;$i<=$n;$i++){
			$id=$db->result($r,$i-1,"id");
			$name=$db->result($r,$i-1,"name"); if ($id==1) $selected="selected"; else $selected="";
			$list.="
			<option value='$id' $selected>$name</option>";
		}
		return $list;
	}
	
	function getClientsList() { $db=new db;
		$r=$db->query("select * from A_CLIENTS where status='1';"); $n=$db->num_rows($r); $list="";					
		for ($i=1;$i<=$n;$i++){
			$id=$db->result($r,$i-1,"id");
			$name=$db->result($r,$i-1,"name");
			$list.="
			<option value='$id'>$id - $name</option>";
		}
		return $list;
	}
	
	function getTpointList() { $db=new db;
		$r=$db->query("select * from T_POINT where status='1';"); $n=$db->num_rows($r); $list="";					
		for ($i=1;$i<=$n;$i++){
			$id=$db->result($r,$i-1,"id");
			$name=$db->result($r,$i-1,"name"); 
			$full_name=$db->result($r,$i-1,"full_name"); 
			$list.="
			<option value='$id' $selected>$full_name ($name)</option>";
		}
		return $list;
	}
	
}
