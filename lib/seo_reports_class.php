<?php

class seo_reports {
		
	function getCashAbr($cash_id) {$db=DbSingleton::getDb(); 
		$r=$db->query("select abr from CASH where id='$cash_id' limit 1");
		$cash_abr=$db->result($r,0,"abr");
	    return $cash_abr;
	}
	
	function getManuaType($id) { $db=DbSingleton::getDb();
		$r=$db->query("select mcaption from manual where id='$id';");
		$mcaption=$db->result($r,0,"mcaption");
		return $mcaption;
	}
	
	function getMediaUserName($user_id){$db=DbSingleton::getDb();$name="";
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
	
	function getSummReportsSales($date_start,$date_end,$cash_id,$doc_type_id,$user_id,$client_id) { $db=DbSingleton::getDb(); $summary=0;	 
	    $r=$db->query("select j.* from J_SALE_INVOICE j
		where j.time_stamp>='$date_start 00:00:00' and j.time_stamp<='$date_end 23:59:59'
		and j.doc_type_id='$doc_type_id' and j.user_id='$user_id' and j.client_conto_id='$client_id';"); $n=$db->num_rows($r);
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
	
	function getSummReportsBacks($date_start,$date_end,$cash_id,$doc_type_id,$user_id,$client_id) { $db=DbSingleton::getDb(); $summary=0;	
		$r=$db->query("select j.* from J_BACK_CLIENTS j 
			left outer join J_SALE_INVOICE js on js.id=j.sale_invoice_id
		where j.time_stamp>='$date_start 00:00:00' and j.time_stamp<='$date_end 23:59:59'
		and j.doc_type_id='$doc_type_id' and js.user_id='$user_id' and js.client_conto_id='$client_id' and j.status_back=103;"); $n=$db->num_rows($r); 	
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
	
	function exportSeoReports($date_start,$date_end,$managers,$cash_id,$client_status){
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=export_reports.csv'); ob_clean();
		$output = fopen('php://output', 'w');
		fputcsv($output, array("Менежер/Вид документу","Продажі","Повернення","Сума"),$delimiter = ';');
		$reports_array=$this->getSeoReportsData($date_start,$date_end,$managers,$cash_id,$client_status); 
		foreach ($reports_array as $fields) {
			fputcsv($output,$fields,$delimiter = ';');
		}
		exit(0);
	}
	
	function getSeoReportsData($date_start,$date_end,$managers,$cash_id,$client_status) { $db=DbSingleton::getDb(); 
	    $list=[]; $sales=$backs=[]; $clients=new clients; $users=$docs=[]; $summ_list=0; $user_name=$doc_name="";
		if($managers=="" || $managers==0) $where=""; else $where="j.user_id in ($managers) and ";
																																									  
	    $r=$db->query("select j.*, sum(j.summ) as summary from J_SALE_INVOICE j
		where $where 
		j.time_stamp>='$date_start 00:00:00' and j.time_stamp<='$date_end 23:59:59'
		group by j.doc_type_id,j.client_conto_id,j.user_id;"); $n=$db->num_rows($r); 
																					 
		for ($i=1;$i<=$n;$i++){
			$user_id=$db->result($r,$i-1,"user_id"); array_push($users,$user_id);
			$doc_type_id=$db->result($r,$i-1,"doc_type_id"); 
			$client_id=$db->result($r,$i-1,"client_conto_id");
			$cash=$db->result($r,$i-1,"cash_id");
			$summ=$this->getSummReportsSales($date_start,$date_end,$cash_id,$doc_type_id,$user_id,$client_id);
			$sales["$user_id-$doc_type_id-$client_id"]=["user_id"=>$user_id, "doc_type_id"=>$doc_type_id, "client_id"=>$client_id, "summ"=>$summ, "cash"=>$cash];
		}
																					  
	    $r=$db->query("select j.*, sum(j.summ) as summary, js.user_id as sale_user from J_BACK_CLIENTS j
		left outer join J_SALE_INVOICE js on js.id=j.sale_invoice_id
		where $where 
		j.time_stamp>='$date_start 00:00:00' and j.time_stamp<='$date_end 23:59:59' and j.status_back=103
		group by js.doc_type_id,js.client_conto_id,js.user_id;"); $n=$db->num_rows($r); 
																					 
		for ($i=1;$i<=$n;$i++){
			$user_id=$db->result($r,$i-1,"sale_user"); array_push($users,$user_id);
			$doc_type_id=$db->result($r,$i-1,"doc_type_id"); 
			$client_id=$db->result($r,$i-1,"client_id");
			$cash=$db->result($r,$i-1,"cash_id"); 
			$summ=$this->getSummReportsBacks($date_start,$date_end,$cash_id,$doc_type_id,$user_id,$client_id);
			$backs["$user_id-$doc_type_id-$client_id"]=["user_id"=>$user_id, "doc_type_id"=>$doc_type_id, "client_id"=>$client_id, "summ_deb"=>$summ, "cash"=>$cash];
		}	
																					  
		$users=array_unique($users);
																				  
		$result=array_merge_recursive($sales, $backs); $docs = array(); $kilk=0; $usumm=$usumm_deb=$usumm_all=0; $dsumm=$dsumm_deb=$dsumm_all=0;
																						 
		foreach ($users as $user) {
			
			foreach ($result as $key => $value) {
				$user_id=$value["user_id"]; if(is_array($user_id)) $user_id=array_shift($user_id);
				if ($user_id==$user) {
					$doc_type_id=$value["doc_type_id"]; if(is_array($doc_type_id)) $doc_type_id=array_shift($doc_type_id); 
					$cash=$value["cash"]; if(is_array($cash)) $cash=array_shift($cash); 
					$user_name=$this->getMediaUserName($user_id); 
					$summ=$value["summ"]; $usumm+=$summ;
					$summ_deb=$value["summ_deb"]; $usumm_deb+=$summ_deb;
					$summ_all=$summ-$summ_deb; $usumm_all+=$summ_all;
					array_push($docs,$doc_type_id);
				}
			}

			$docs=array_unique($docs);	
			
			$kilk++; $list[$kilk]=array($user_name,$usumm,$usumm_deb,$usumm_all);
			$summ_list+=$usumm_all; $usumm=$usumm_deb=$usumm_all=0; 
		
			foreach ($docs as $doc) {  
				foreach ($result as $key => $value) {
					$user_id=$value["user_id"]; if(is_array($user_id)) $user_id=array_shift($user_id);
					$doc_type_id=$value["doc_type_id"]; if(is_array($doc_type_id)) $doc_type_id=array_shift($doc_type_id); 

					if ($user_id==$user) {
						if ($doc_type_id==$doc) {
							$summ=$value["summ"]; $dsumm+=$summ;
							$summ_deb=$value["summ_deb"]; $dsumm_deb+=$summ_deb;
							$summ_all=$summ-$summ_deb; $dsumm_all+=$summ_all;
							$doc_name=$this->getManuaType($doc_type_id);
							$cash=$value["cash"]; if(is_array($cash)) $cash=array_shift($cash);
						}
					}
				}

				$kilk++; $list[$kilk]=array($doc_name,$dsumm,$dsumm_deb,$dsumm_all);
				$dsumm=$dsumm_deb=$dsumm_all=0;

				foreach ($result as $key => $value) {	
					$user_id=$value["user_id"]; if(is_array($user_id)) $user_id=array_shift($user_id);
					$doc_type_id=$value["doc_type_id"]; if(is_array($doc_type_id)) $doc_type_id=array_shift($doc_type_id);
					$client_id=$value["client_id"]; if(is_array($client_id)) $client_id=array_shift($client_id);
					$client_name=$clients->getClientNameById($client_id,"name");
					$summ=$value["summ"];
					$summ_deb=$value["summ_deb"]; 
					$summ_all=$summ-$summ_deb; 
					if ($user_id==$user) {
						if ($doc_type_id==$doc) {
							if ($client_status) {
								$kilk++; $list[$kilk]=array("Клієнт - ".$client_name,$summ,$summ_deb,$summ_all);
							}
						}
					}
				}
			}
			unset($docs); $docs = array();
		}																					 
		return $list;
	}

	
	function showSeoReports($date_start,$date_end,$managers,$cash_id,$client_status) { $db=DbSingleton::getDb(); $clients=new clients;
        $users=$docs=[]; $summ_list=0; $list=""; $sales=$backs=[]; $user_name=$doc_name="";
        $form="";$form_htm=RD."/tpl/seo_reports_table.htm";if (file_exists("$form_htm")){ $form=file_get_contents($form_htm);}
																					  
		if($managers=="") $where=""; else $where="j.user_id in ($managers) and ";	 			
																					  
	    $r=$db->query("select j.*, sum(j.summ) as summary from J_SALE_INVOICE j
		where $where 
		j.time_stamp>='$date_start 00:00:00' and j.time_stamp<='$date_end 23:59:59'
		group by j.doc_type_id, j.client_conto_id, j.user_id;"); $n=$db->num_rows($r); 
																					 
		for ($i=1;$i<=$n;$i++){
			$user_id=$db->result($r,$i-1,"user_id"); array_push($users,$user_id);
			$doc_type_id=$db->result($r,$i-1,"doc_type_id"); 
			$client_id=$db->result($r,$i-1,"client_conto_id");
			$cash=$db->result($r,$i-1,"cash_id");
			$summ=$this->getSummReportsSales($date_start,$date_end,$cash_id,$doc_type_id,$user_id,$client_id);
			$sales["$user_id-$doc_type_id-$client_id"]=["user_id"=>$user_id, "doc_type_id"=>$doc_type_id, "client_id"=>$client_id, "summ"=>$summ, "cash"=>$cash];
		}
																					  
	    if($managers=="") $where=""; else $where="js.user_id in ($managers) and ";
																					  
	    $r=$db->query("select j.*, sum(j.summ) as summary, js.user_id as sale_user from J_BACK_CLIENTS j
		left outer join J_SALE_INVOICE js on js.id=j.sale_invoice_id
		where $where 
		j.time_stamp>='$date_start 00:00:00' and j.time_stamp<='$date_end 23:59:59' and j.status_back=103
		group by js.doc_type_id, js.client_conto_id, js.user_id;"); $n=$db->num_rows($r); 
																					 
		for ($i=1;$i<=$n;$i++){
			$user_id=$db->result($r,$i-1,"sale_user"); array_push($users,$user_id);
			$doc_type_id=$db->result($r,$i-1,"doc_type_id"); 
			$client_id=$db->result($r,$i-1,"client_id");
			$cash=$db->result($r,$i-1,"cash_id"); 
			$summ=$this->getSummReportsBacks($date_start,$date_end,$cash_id,$doc_type_id,$user_id,$client_id);
			$backs["$user_id-$doc_type_id-$client_id"]=["user_id"=>$user_id, "doc_type_id"=>$doc_type_id, "client_id"=>$client_id, "summ_deb"=>$summ, "cash"=>$cash];
		}	
																					  
		$users=array_unique($users);																		  
		$result=array_merge_recursive($sales, $backs); $docs = array();	$usumm=$usumm_deb=$usumm_all=0; $dsumm=$dsumm_deb=$dsumm_all=0;
					 
		foreach ($users as $user) {
			foreach ($result as $key=>$value) {
				$user_id=$value["user_id"]; if(is_array($user_id)) $user_id=array_shift($user_id);
				if ($user_id==$user) {
					$doc_type_id=$value["doc_type_id"]; if(is_array($doc_type_id)) $doc_type_id=array_shift($doc_type_id); 
					$cash=$value["cash"]; if(is_array($cash)) $cash=array_shift($cash); 
					$user_name=$this->getMediaUserName($user_id); 
					$summ=$value["summ"]; $usumm+=$summ;
					$summ_deb=$value["summ_deb"]; $usumm_deb+=$summ_deb;
					$summ_all=$summ-$summ_deb; $usumm_all+=$summ_all;
					array_push($docs,$doc_type_id);
				}
			}
			
			$docs=array_unique($docs);
			
			$list.="<tr style='background:pink'>
				<td>$user_name</td>
				<td>$usumm</td>
				<td>$usumm_deb</td>
				<td>$usumm_all</td>
			</tr>";
			$summ_list+=$usumm_all; $usumm=$usumm_deb=$usumm_all=0; 
		
				foreach ($docs as $doc) {  
					foreach ($result as $key=>$value) {
						$user_id=$value["user_id"]; if(is_array($user_id)) $user_id=array_shift($user_id);
						$doc_type_id=$value["doc_type_id"]; if(is_array($doc_type_id)) $doc_type_id=array_shift($doc_type_id); 
						 
						if ($user_id==$user) {
							if ($doc_type_id==$doc) {
								$summ=$value["summ"]; $dsumm+=$summ;
								$summ_deb=$value["summ_deb"]; $dsumm_deb+=$summ_deb;
								$summ_all=$summ-$summ_deb; $dsumm_all+=$summ_all;
								$doc_name=$this->getManuaType($doc_type_id);
								$cash=$value["cash"]; if(is_array($cash)) $cash=array_shift($cash);
							}
						}
					}

					$list.="<tr style='background:lightyellow'>
						<td>$doc_name</td>
						<td>$dsumm</td>
						<td>$dsumm_deb</td>
						<td>$dsumm_all</td>
					</tr>";
					$dsumm=$dsumm_deb=$dsumm_all=0;

					foreach ($result as $key => $value) {	
						$user_id=$value["user_id"]; if(is_array($user_id)) $user_id=array_shift($user_id);
						$doc_type_id=$value["doc_type_id"]; if(is_array($doc_type_id)) $doc_type_id=array_shift($doc_type_id);
						$client_id=$value["client_id"]; if(is_array($client_id)) $client_id=array_shift($client_id);
						$client_name=$clients->getClientNameById($client_id,"name");
						$summ=$value["summ"];
						$summ_deb=$value["summ_deb"]; 
						$summ_all=$summ-$summ_deb; 
						if ($user_id==$user) {
							if ($doc_type_id==$doc) {
								if ($client_status) {
									$list.="<tr>
										<td align='right'>$client_name</td>
										<td>$summ</td>
										<td>$summ_deb</td>
										<td>$summ_all</td>
									</tr>";
								}
							}
						}
					}
				}
			unset($docs); $docs = array();
		}																		  
		$form=str_replace("{seo_reports_range}",$list,$form);
		$form=str_replace("{summ_reports}",$summ_list,$form);
		$form=str_replace("{cash_abr}",$this->getCashAbr($cash_id),$form);
		return $form;
	}
		
	function getSeoReportsSumm($date_start,$date_end,$managers,$cash_id,$client_status,$user_id) { $db=DbSingleton::getDb(); $list="";
	    $r=$db->query("select j.id, j.usd_to_uah, j.eur_to_uah, j.user_id, j.doc_type_id, j.summ as prodaga, jb.summ as vosvrat 
	    from J_SALE_INVOICE j
		    cross join J_BACK_CLIENTS jb 
		where j.user_id='$user_id' 
		and (j.time_stamp>='$date_start 00:00:00' and j.time_stamp<='$date_end 23:59:59')
		or (jb.time_stamp>='$date_start 00:00:00' and jb.time_stamp<='$date_end 23:59:59')
		and j.cash_id='$cash_id'
		group by j.doc_type_id;"); $n=$db->num_rows($r);
		for ($i=1;$i<=$n;$i++){
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
	
	function getSeoReportsSummClient($date_start,$date_end,$managers,$cash_id,$client_status,$user_id,$doc_type_id) { $db=DbSingleton::getDb(); $list=""; $clients=new clients;
	    $r=$db->query("select j.id,j.user_id,j.client_id,j.doc_type_id,sum(j.summ) as prodaga 
	    from J_SALE_INVOICE j
		where j.user_id='$user_id' 
		and j.doc_type_id='$doc_type_id'
		and j.time_stamp>='$date_start 00:00:00' and j.time_stamp<='$date_end 23:59:59'
		and j.cash_id='$cash_id'
		group by j.client_id;"); $n=$db->num_rows($r);																	  
		for ($i=1;$i<=$n;$i++){
			$client_id=$db->result($r,$i-1,"client_id"); $client_name=$clients->getClientNameById($client_id,"name");
			//$doc_type_id=$db->result($r,$i-1,"doc_type_id"); $doc_name=$this->getManuaType($doc_type_id);
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
	
	function getCashList() { $db=DbSingleton::getDb();
		$r=$db->query("select * from CASH"); $n=$db->num_rows($r); $list="";				
		for ($i=1;$i<=$n;$i++){
			$id=$db->result($r,$i-1,"id");
			$name=$db->result($r,$i-1,"name"); if ($id==1) $selected="selected"; else $selected="";
			$list.="
			<option value='$id' $selected>$name</option>";
		}
		return $list;
	}
	
	function getManagersList() { $db=DbSingleton::getDb();
		$r=$db->query("select * from media_users where role_id='3'"); $n=$db->num_rows($r); $list="";					
		for ($i=1;$i<=$n;$i++){
			$id=$db->result($r,$i-1,"id");
			$name=$db->result($r,$i-1,"name");
			$list.="
			<option value='$id'>$id - $name</option>";
		}
		return $list;
	}
	
	function getSummUser($user_id,$date_start,$date_end,$cash_id) { $db=DbSingleton::getDb();
		$summ_sales=$summ_backs=0;
		$where="j.user_id in ($user_id) and ";	 			
																					  
	    $r=$db->query("select j.*, sum(j.summ) as summary 
	    from J_SALE_INVOICE j
		where $where 
		j.time_stamp>='$date_start 00:00:00' and j.time_stamp<='$date_end 23:59:59'
		group by j.doc_type_id, j.client_conto_id, j.user_id;"); $n=$db->num_rows($r); 
																					 
		for ($i=1;$i<=$n;$i++){
			$user_id=$db->result($r,$i-1,"user_id"); array_push($users,$user_id);
			$doc_type_id=$db->result($r,$i-1,"doc_type_id"); 
			$client_id=$db->result($r,$i-1,"client_conto_id");
			$summ=$this->getSummReportsSales($date_start,$date_end,$cash_id,$doc_type_id,$user_id,$client_id);
			$summ_sales+=$summ;
		}
																   
	    $where="js.user_id in ($user_id) and ";	
																					  
	    $r=$db->query("select j.*, sum(j.summ) as summary, js.user_id as sale_user 
	    from J_BACK_CLIENTS j
		    left outer join J_SALE_INVOICE js on js.id=j.sale_invoice_id
		where $where 
		j.time_stamp>='$date_start 00:00:00' and j.time_stamp<='$date_end 23:59:59' and j.status_back=103
		group by js.doc_type_id, js.client_conto_id, js.user_id;"); $n=$db->num_rows($r); 													   
																   
		for ($i=1;$i<=$n;$i++){
			$user_id=$db->result($r,$i-1,"sale_user"); array_push($users,$user_id);
			$doc_type_id=$db->result($r,$i-1,"doc_type_id"); 
			$client_id=$db->result($r,$i-1,"client_id");
			$summ=$this->getSummReportsBacks($date_start,$date_end,$cash_id,$doc_type_id,$user_id,$client_id);
			$summ_backs+=$summ;
		}	
		$summ_list=$summ_sales-$summ_backs;
		$summ_list.=" ".$this->getCashAbr($cash_id);
		return $summ_list;
   }

}
