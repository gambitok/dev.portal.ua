<?php

class report_clients {

    function getTpointName($tpoint_id) { $db=DbSingleton::getDb();
        $r=$db->query("SELECT `name` FROM `T_POINT` WHERE `id`='$tpoint_id' LIMIT 1;");
        $name=$db->result($r,0,"name");
        return $name;
    }
		
	function getCashAbr($cash_id) { $db=DbSingleton::getDb();
		$r=$db->query("SELECT `abr` FROM `CASH` WHERE `id`='$cash_id' LIMIT 1;");
		$cash_abr=$db->result($r,0,"abr");
	    return $cash_abr;
	}
	
	function getManuaType($id) { $db=DbSingleton::getDb();
		$r=$db->query("SELECT `mcaption` FROM `manual` WHERE `id`='$id';");
		$mcaption=$db->result($r,0,"mcaption");
		return $mcaption;
	}

    function getCashList() { $db=DbSingleton::getDb();
        $r=$db->query("SELECT * FROM `CASH`;"); $n=$db->num_rows($r); $list="";
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $name=$db->result($r,$i-1,"name"); if ($id==1) $selected="selected"; else $selected="";
            $list.="<option value='$id' $selected>$name</option>";
        }
        return $list;
    }

    function getClientsList($clients=NULL) { $db=DbSingleton::getDb();
        $r=$db->query("SELECT * FROM `A_CLIENTS` WHERE `status`='1';"); $n=$db->num_rows($r); $list="";
        for ($i=1;$i<=$n;$i++){
            $client_id=$db->result($r,$i-1,"id");
            $name=$db->result($r,$i-1,"name");
            $sel="";
            if ($clients!=null) {
                $arr_clients=explode(",", $clients);
                if (in_array($client_id, $arr_clients)) $sel="selected";
            }
            $list.="<option value='$client_id' $sel>$client_id - $name</option>";
        }
        return $list;
    }

    function getTpointList() { $db = DbSingleton::getDb();
        $r=$db->query("SELECT * FROM `T_POINT` WHERE `status`='1';"); $n=$db->num_rows($r); $list="";
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $name=$db->result($r,$i-1,"name");
            $full_name=$db->result($r,$i-1,"full_name");
            $list.="<option value='$id'>$full_name ($name)</option>";
        }
        return $list;
    }

    function getClientCategoryList() { $db = DbSingleton::getDb();
        $r = $db->query("SELECT * FROM `manual` WHERE `key`='customers_categories' ORDER BY mid ASC;"); $n = $db->num_rows($r); $list = "";
        for ($i=1;$i<=$n;$i++){
            $id = $db->result($r, $i - 1, "id");
            $rs = $db->query("SELECT * FROM `A_CUSTOMERS_CATEGORIES` WHERE `manual_id`='$id' AND `lang_id`='1' LIMIT 1;");
            $caption = $db->result($rs, 0, "caption");
            if ($caption=="") $caption = $db->result($r, $i-1, "mcaption");
            $list.="<option value=\"$id\">$caption</option>";
        }
        return $list;
    }

    function getBrandsList() { $db = DbSingleton::getTokoDb();
        $r=$db->query("SELECT * FROM `T2_BRANDS`;"); $n=$db->num_rows($r); $list="";
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"BRAND_ID");
            $name=$db->result($r,$i-1,"BRAND_NAME");
            $list.="<option value='$id'>$id - $name</option>";
        }
        return $list;
    }

    function getGoodsGroupList() { $db = DbSingleton::getTokoDb();
        $r=$db->query("SELECT * FROM `GOODS_GROUP`;"); $n=$db->num_rows($r); $list="";
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"ID");
            $parrent_id=$db->result($r,$i-1,"PARRENT_ID"); $parrent_id ? $pr="" : $pr="$";
            $name=$db->result($r,$i-1,"NAME");
            $list.="<option value='$id'>$pr $name</option>";
        }
        return $list;
    }
	
	function getSummCash($summ, $cash_id, $usd_to_uah, $eur_to_uah, $to_cash) {
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
		return round($summary, 2);
	}
	
	function getClientLocation($client_id) { $db = DbSingleton::getDb();
		$r=$db->query("SELECT c.*, t2st.STATE_NAME, t2rg.REGION_NAME, t2ct.CITY_NAME, cc.tpoint_id 
		FROM `A_CLIENTS` c 
			LEFT OUTER JOIN `A_CLIENTS_CONDITIONS` cc on cc.client_id=c.id 
			LEFT OUTER JOIN `T2_STATE` t2st on t2st.STATE_ID=c.state
			LEFT OUTER JOIN `T2_REGION` t2rg on t2rg.REGION_ID=c.region
			LEFT OUTER JOIN `T2_CITY` t2ct on t2ct.CITY_ID=c.city
		WHERE c.status=1 AND c.id=$client_id;");
		$state=$db->result($r,0,"STATE_NAME");
		$region=$db->result($r,0,"REGION_NAME");
		$city=$db->result($r,0,"CITY_NAME");
		$tpoint_id=$db->result($r,0,"tpoint_id"); $tpoint=$this->getTpointName($tpoint_id);
		return array($tpoint, $state, $region, $city);
	}
	
	function getSummReportsSales($date_start, $date_end, $cash_id, $client_id) { $db = DbSingleton::getDb();
	    $r=$db->query("SELECT * FROM `J_SALE_INVOICE`
		WHERE `time_stamp`>='$date_start 00:00:00' AND `time_stamp`<='$date_end 23:59:59' AND `client_conto_id`='$client_id';"); $n=$db->num_rows($r); $summary=0;
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
	
	function getSummReportsBacks($date_start, $date_end, $cash_id, $client_id) { $db = DbSingleton::getDb();
		$r=$db->query("SELECT j.* FROM `J_BACK_CLIENTS` j 
			LEFT OUTER JOIN `J_SALE_INVOICE` js on js.id=j.sale_invoice_id
		WHERE j.time_stamp>='$date_start 00:00:00' AND j.time_stamp<='$date_end 23:59:59' AND js.client_conto_id='$client_id' AND j.status_back=103;"); $n=$db->num_rows($r); $summary=0;
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

    function getSummReportsSalesAll($date_start, $date_end, $client_id) { $db = DbSingleton::getDb();
        $sum_uah=$sum_usd=$sum_eur=0;
        $r=$db->query("SELECT * FROM `J_SALE_INVOICE`
		WHERE `time_stamp`>='$date_start 00:00:00' AND `time_stamp`<='$date_end 23:59:59' AND `client_conto_id`='$client_id';"); $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $usd_to_uah=$db->result($r,$i-1,"usd_to_uah");
            $eur_to_uah=$db->result($r,$i-1,"eur_to_uah");
            $cash=$db->result($r,$i-1,"cash_id");
            $summ=$db->result($r,$i-1,"summ");
            $s_uah=$this->getSummCash($summ,$cash,$usd_to_uah,$eur_to_uah,1);
            $s_usd=$this->getSummCash($summ,$cash,$usd_to_uah,$eur_to_uah,2);
            $s_eur=$this->getSummCash($summ,$cash,$usd_to_uah,$eur_to_uah,3);
            $sum_uah+=$s_uah;
            $sum_usd+=$s_usd;
            $sum_eur+=$s_eur;
        }
        return array($sum_uah, $sum_usd, $sum_eur);
    }

    function getSummReportsBacksAll($date_start, $date_end, $client_id) { $db = DbSingleton::getDb();
        $sum_uah=$sum_usd=$sum_eur=0;
        $r=$db->query("SELECT j.* FROM `J_BACK_CLIENTS` j 
			LEFT OUTER JOIN `J_SALE_INVOICE` js on js.id=j.sale_invoice_id
		WHERE j.time_stamp>='$date_start 00:00:00' AND j.time_stamp<='$date_end 23:59:59' AND js.client_conto_id='$client_id' AND j.status_back=103;"); $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $usd_to_uah=$db->result($r,$i-1,"usd_to_uah");
            $eur_to_uah=$db->result($r,$i-1,"eur_to_uah");
            $cash=$db->result($r,$i-1,"cash_id");
            $summ=$db->result($r,$i-1,"summ");
            $s_uah=$this->getSummCash($summ,$cash,$usd_to_uah,$eur_to_uah,1);
            $s_usd=$this->getSummCash($summ,$cash,$usd_to_uah,$eur_to_uah,2);
            $s_eur=$this->getSummCash($summ,$cash,$usd_to_uah,$eur_to_uah,3);
            $sum_uah+=$s_uah;
            $sum_usd+=$s_usd;
            $sum_eur+=$s_eur;
        }
        return array($sum_uah, $sum_usd, $sum_eur);
    }

    function getClientBalans($client_id, $cash_id) { $db = DbSingleton::getDb();
        $income = new income;
        $r = $db->query("SELECT * FROM `B_CLIENT_BALANS` WHERE `client_id`='$client_id' ORDER BY `last_update` DESC LIMIT 1;");
        $saldo = $db->result($r, 0, "saldo");
        $cash_from = $db->result($r, 0, "cash_id");
        $saldo = $income->getSummCash($saldo, $cash_from, $cash_id);
        return $saldo;
    }

    function getClientJpay($client_id, $cash_id, $date_start, $date_end) { $db = DbSingleton::getDb();
        $income = new income;
        $data_cur = date("Y-m-d"); $summary = 0;
        if ($date_start!="" && $date_end!="") $where_date = "AND j.data_time>='$date_start 00:00:00' AND j.data_time<='$date_end 23:59:59'";
        else $where_date = " AND j.data_time>='$data_cur 00:00:00' AND j.data_time<='$data_cur 23:59:59'";

        $r = $db->query("SELECT j.* FROM `J_PAY` j WHERE j.`client_id`='$client_id' $where_date"); $n = $db->num_rows($r);
        for ($i=1; $i<=$n; $i++) {
            $summ = 0 + $db->result($r, $i-1, "summ");
            $cash_from = 0 + $db->result($r, $i-1, "cash_id");
            $summ = $income->getSummCash($summ, $cash_from, $cash_id);

            $summary+=$summ;
        }
        return $summary;
    }

    function getClientSumInvoice($client_id, $cash_id) { $db = DbSingleton::getDb();
        $income = new income;
        $summary = 0;
        $r = $db->query("SELECT `invoice_id` FROM `J_SALE_INVOICE_PROLONGATION` WHERE `client_id`='$client_id';"); $n = $db->num_rows($r);
        if ($n>0) {
            for ($i=1; $i<=$n; $i++) {
                $invoice_id = $db->result($r, $i-1, "invoice_id");
                $r2 = $db->query("SELECT `summ_debit`, `cash_id` FROM `J_SALE_INVOICE` WHERE `id`='$invoice_id' LIMIT 1;");
                $summ_debit = $db->result($r2, 0, "summ_debit");
                $cash_from = $db->result($r2, 0, "cash_id");
                $summ_debit = $income->getSummCash($summ_debit, $cash_from, $cash_id);
                $summary+=$summ_debit;
            }
            $summary = round($summary, 2);
        }
        return $summary;
    }
	
	function showReportClients($date_start, $date_end, $clients, $cash_id, $tpoint_id, $client_category) { $db = DbSingleton::getDb();
	    $client=new clients; $income = new income;
	    $summ_list=0; $list=""; $sales=$backs=[];
		$form=""; $form_htm=RD."/tpl/report_clients_table.htm"; if (file_exists("$form_htm")){ $form=file_get_contents($form_htm);}

		if($clients=="") $where=""; else $where="j.client_conto_id IN ($clients) AND ";
		if($tpoint_id=="0") $where_tpoint=""; else $where_tpoint="AND cc.tpoint_id=$tpoint_id";
		if($client_category=="0") $where_category=""; else $where_category="AND c.client_category=$client_category";

	    $r = $db->query("SELECT j.* 
	    FROM `J_SALE_INVOICE` j
			LEFT OUTER JOIN `A_CLIENTS` c ON c.id=j.client_conto_id
			LEFT OUTER JOIN `A_CLIENTS_CONDITIONS` cc ON cc.client_id=j.client_conto_id
		WHERE $where j.time_stamp>='$date_start 00:00:00' AND j.time_stamp<='$date_end 23:59:59' $where_tpoint $where_category
		GROUP BY j.client_conto_id;"); $n = $db->num_rows($r);
		for ($i=1; $i<=$n; $i++) {
			$client_id = $db->result($r,$i-1,"client_conto_id");
			$cash = $db->result($r,$i-1,"cash_id");
			$summ_debit = $db->result($r,$i-1,"summ_debit");
			$summ = $this->getSummReportsSales($date_start, $date_end, $cash_id, $client_id);
			$sales[$client_id] = ["client_id"=>$client_id, "summ"=>$summ, "cash"=>$cash, "summ_debit"=>$summ_debit];
		}																																				
																					  
	    if ($clients=="") $where=""; else $where="js.client_conto_id IN ($clients) AND ";
		if ($tpoint_id=="0") $where_tpoint=""; else $where_tpoint="AND cc.tpoint_id=$tpoint_id";
        if ($client_category=="0") $where_category=""; else $where_category="AND c.client_category=$client_category";
																					  
	    $r = $db->query("SELECT j.*, js.user_id as sale_user 
	    FROM `J_BACK_CLIENTS` j
			LEFT OUTER JOIN `J_SALE_INVOICE` js on js.id=j.sale_invoice_id
			LEFT OUTER JOIN `A_CLIENTS` c on c.id=js.client_conto_id
			LEFT OUTER JOIN `A_CLIENTS_CONDITIONS` cc on cc.client_id=js.client_conto_id
		WHERE $where j.time_stamp>='$date_start 00:00:00' AND j.time_stamp<='$date_end 23:59:59' AND j.status_back=103 $where_tpoint $where_category
		GROUP BY js.client_conto_id;"); $n = $db->num_rows($r);
		for ($i=1; $i<=$n; $i++) {
			$client_id = $db->result($r,$i-1,"client_id");
			$cash = $db->result($r,$i-1,"cash_id");
			$summ = $this->getSummReportsBacks($date_start, $date_end, $cash_id, $client_id);
            if ($sales[$client_id]==null) $sales[$client_id] = ["client_id"=>$client_id, "summ"=>0, "cash"=>$cash];
			$backs[$client_id] = ["client_id"=>$client_id, "summ_deb"=>$summ, "cash"=>$cash];
		}	
					 
		foreach ($sales as $key=>$value) {
			$client_id = $value["client_id"];
			$summ = $value["summ"];
            $summ_deb = $backs[$key]["summ_deb"];
            $cash_from = $backs[$key]["cash"];
			$summ_debit = $value["summ_debit"]; $summ_debit = $income->getSummCash($summ_debit, $cash_from, $cash_id);
			$client_name = $client->getClientNameById($client_id, "name");
			list($tpoint_id, $state, $region, $city) = $this->getClientLocation($client_id);
			$summ_all = $summ - $summ_deb;
            $client_payment = $this->getClientJpay($client_id, $cash_id, $date_start, $date_end);
            $client_saldo = $this->getClientBalans($client_id, $cash_id);
            $client_sum_invoice = $this->getClientSumInvoice($client_id, $cash_id);

			$list.="<tr style='background:pink'>
				<td>$tpoint_id</td>
				<td>$state</td>
				<td>$region</td>
				<td>$city</td>
				<td>$client_id. $client_name</td>
				<td>$summ</td>
				<td>$summ_deb</td>
				<td>$summ_all</td>
				<td>$summ_debit</td>
				<td>$client_payment</td>
				<td>$client_saldo</td>
				<td>$client_sum_invoice</td>
			</tr>";
			$summ_list+=$summ_all;
		}

		$form=str_replace("{report_clients_range}",$list,$form);
		$form=str_replace("{summ_reports}",$summ_list,$form);
		$form=str_replace("{cash_abr}",$this->getCashAbr($cash_id),$form);
		return $form;
	}
		
	function getSeoReportsSumm($date_start, $date_end, $cash_id, $client_status, $user_id) { $db = DbSingleton::getDb();
	    $r = $db->query("SELECT j.id, j.usd_to_uah, j.eur_to_uah, j.user_id, j.doc_type_id, j.summ as prodaga, jb.summ as vosvrat 
	    FROM `J_SALE_INVOICE` j
			CROSS JOIN `J_BACK_CLIENTS` jb 
		WHERE j.user_id='$user_id' 
            AND (j.time_stamp>='$date_start 00:00:00' AND j.time_stamp<='$date_end 23:59:59')
            OR (jb.time_stamp>='$date_start 00:00:00' AND jb.time_stamp<='$date_end 23:59:59')
            AND j.cash_id='$cash_id'
		GROUP BY j.doc_type_id;"); $n = $db->num_rows($r); $list = "";
		for ($i=1; $i<=$n; $i++) {
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
			if ($client_status) $list.=$this->getSeoReportsSummClient($date_start,$date_end,$cash_id,$user_id,$doc_type_id);
		}
		return $list;
	}
	
	function getSeoReportsSummClient($date_start, $date_end, $cash_id, $user_id, $doc_type_id) { $db = DbSingleton::getDb();
	    $list=""; $clients=new clients;
	    $r = $db->query("SELECT j.id, j.user_id, j.client_id, j.doc_type_id, SUM(j.summ) as prodaga 
	    FROM `J_SALE_INVOICE` j
		WHERE j.user_id='$user_id' 
            AND j.doc_type_id='$doc_type_id'
            AND j.time_stamp>='$date_start 00:00:00' AND j.time_stamp<='$date_end 23:59:59'
            AND j.cash_id='$cash_id'
		GROUP BY j.client_id;"); $n = $db->num_rows($r);
		for ($i=1; $i<=$n; $i++) {
			$client_id=$db->result($r,$i-1,"client_id");
			$client_name=$clients->getClientNameById($client_id,"name");
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

	/*
	 * ANALYTICS
	 *
	 * */
    function showAnalyticsClients($date_start, $date_end, $clients, $cash_id, $tpoint_id, $price_id, $margin_status, $states, $regions, $citys) { $db = DbSingleton::getDb();
        $slave=new slave; $client=new clients;
        $summ_list=0; $list=""; $sales=$backs=[];
        $form="";$form_htm=RD."/tpl/analytics_clients_table.htm";if (file_exists("$form_htm")){ $form=file_get_contents($form_htm);}
        if($clients=="") $where=""; else $where="j.client_conto_id IN ($clients) AND ";
        if($tpoint_id=="0") $where_tpoint=""; else $where_tpoint="AND cc.tpoint_id=$tpoint_id";

        $loc_clients_array = $this->getLocationClientsList($states,$regions,$citys);
        $price_clients_array = $this->getPriceClientsList($price_id);
        $clients_array = array_intersect($loc_clients_array, $price_clients_array);

        $r = $db->query("SELECT j.* FROM `J_SALE_INVOICE` j
			LEFT OUTER JOIN `A_CLIENTS_CONDITIONS` cc on cc.client_id=j.client_conto_id
		WHERE $where j.time_stamp>='$date_start 00:00:00' AND j.time_stamp<='$date_end 23:59:59' $where_tpoint
		GROUP BY j.client_conto_id;"); $n = $db->num_rows($r);
        for ($i=1; $i<=$n; $i++) {
            $client_id=$db->result($r,$i-1,"client_conto_id");
            $cash=$db->result($r,$i-1,"cash_id");
            $summ=$this->getSummReportsSales($date_start,$date_end,$cash_id,$client_id);
            $sales[$client_id] = ["client_id"=>$client_id, "summ"=>$summ, "cash"=>$cash];
        }

        if($clients=="") $where=""; else $where="js.client_conto_id IN ($clients) AND ";
        if($tpoint_id=="0") $where_tpoint=""; else $where_tpoint="AND cc.tpoint_id=$tpoint_id";

        $r = $db->query("SELECT j.*, js.user_id as sale_user 
	    FROM `J_BACK_CLIENTS` j
			LEFT OUTER JOIN `J_SALE_INVOICE` js on js.id=j.sale_invoice_id
			LEFT OUTER JOIN `A_CLIENTS_CONDITIONS` cc on cc.client_id=js.client_conto_id
		WHERE $where j.time_stamp>='$date_start 00:00:00' AND j.time_stamp<='$date_end 23:59:59' AND j.status_back=103 $where_tpoint
		GROUP BY js.client_conto_id;"); $n = $db->num_rows($r);
        for ($i=1; $i<=$n; $i++) {
            $client_id=$db->result($r,$i-1,"client_id");
            $cash=$db->result($r,$i-1,"cash_id");
            $summ=$this->getSummReportsBacks($date_start,$date_end,$cash_id,$client_id);
            if ($sales[$client_id]==null) $sales[$client_id] = ["client_id"=>$client_id, "summ"=>0, "cash"=>$cash];
            $backs[$client_id] = ["client_id"=>$client_id, "summ_deb"=>$summ, "cash"=>$cash];
        }

        $dates = $this->getMonthList($date_start, $date_end);
        $month_table="";

        foreach ($dates as $date) {
            $month_id=date("m",strtotime($date[0]));
            $month_name=$slave->get_month_name($month_id);
            $month_table.="<th>$month_name ($month_id)</th>";
        }

        foreach ($sales as $key=>$value) {
            $client_id=$value["client_id"];
            $summ=$value["summ"];
            $summ_deb=$backs[$key]["summ_deb"];
            $client_name=$client->getClientNameById($client_id,"name");
            list($tpoint_id, $state, $region, $city) = $this->getClientLocation($client_id);
            $summ_all=round(floatval($summ)-floatval($summ_deb),2);

            $month_body=$this->calculateSummSales($date_start,$date_end,$client_id,$cash_id,$margin_status);

            if (in_array($client_id, $clients_array)) {
                $list.="<tr>
                    <td>$client_id</td>
                    <td>$tpoint_id</td>
                    <td>$state</td>
                    <td>$region</td>
                    <td>$city</td>
     				<td style='cursor: pointer'>
                        <a class='btn btn-primary' onclick=\"showClientCard('$client_id')\" title='Карточка клієнта'><i class='fa fa-user'></i></a>
                        <a class='btn btn-warning' href='https://portal.myparts.pro/ReportSalesArticles?clients=$client_id&date_start=$date_start&date_end=$date_end' target='_blank' title='Продажі за артикулами по клієнту'><i class='fa fa-money'></i></a>
     				    $client_name
     				</td>
                    <td style='background:#f0ad4e; color:white; font-weight: bold;'>$summ_all</td>
                    $month_body
                </tr>";
                $summ_list+=$summ_all;
            }
        }

        $form=str_replace("{month_table}",$month_table,$form);
        $form=str_replace("{report_clients_range}",$list,$form);
        $form=str_replace("{summ_reports}",$summ_list,$form);
        $form=str_replace("{cash_abr}",$this->getCashAbr($cash_id),$form);
        return $form;
    }

    function calculateSummSales($date_start, $date_end, $client_id, $cash_id, $margin_status) {
        $dates = $this->getMonthList($date_start, $date_end);
        $month_table=""; $price_lvl=""; $st=""; $ttl=""; $summ_all=0;
        $month_start = date("Y-m",strtotime($date_start));
        $month_end = date("Y-m",strtotime($date_end));
        foreach ($dates as $date) {
            $month = $date[2];
            if ($month!=$month_start && $month!=$month_end) {
                list($status_cash,$sales_cash) = $this->getClientsSales($client_id,$month,$cash_id);
                if ($status_cash) {
                    $summ_all = $sales_cash;
                } else {
                    list($sales_uah,$sales_usd,$sales_eur) = $this->getSummReportsSalesAll($date[0],$date[1],$client_id);
                    list($backs_uah,$backs_usd,$backs_eur) = $this->getSummReportsBacksAll($date[0],$date[1],$client_id);
                    $s_uah = round(floatval($sales_uah)-floatval($backs_uah),2);
                    $s_usd = round(floatval($sales_usd)-floatval($backs_usd),2);
                    $s_eur = round(floatval($sales_eur)-floatval($backs_eur),2);
                    if ($cash_id==1) $summ_all=$s_uah;
                    if ($cash_id==2) $summ_all=$s_usd;
                    if ($cash_id==3) $summ_all=$s_eur;
                    $this->addClientSales($client_id,$month,$s_uah,$s_usd,$s_eur);
                }
            } else {
                list($sales_uah,$sales_usd,$sales_eur) = $this->getSummReportsSalesAll($date[0],$date[1],$client_id);
                list($backs_uah,$backs_usd,$backs_eur) = $this->getSummReportsBacksAll($date[0],$date[1],$client_id);
                $s_uah = round(floatval($sales_uah)-floatval($backs_uah),2);
                $s_usd = round(floatval($sales_usd)-floatval($backs_usd),2);
                $s_eur = round(floatval($sales_eur)-floatval($backs_eur),2);
                if ($cash_id==1) $summ_all=$s_uah;
                if ($cash_id==2) $summ_all=$s_usd;
                if ($cash_id==3) $summ_all=$s_eur;
                if ($date_start==date("$month_start-01",strtotime($date_start)) && $date_end==date("$month_end-t",strtotime($date_end)))
                    $this->addClientSales($client_id,$month,$s_uah,$s_usd,$s_eur);
            }

            if ($margin_status) {
                list($price_lvl,$price_status) = $this->getClientPrice($client_id,$date[0],$date[1]);
                if ($price_status) {$st="style='background:pink;cursor:help;'";$ttl="title='[Прайс(Націнка на прайс, %)]'";} else {$st="";$ttl="";}
            }
            $month_table.="<td $st $ttl>$summ_all <span style='color: #bbbbbb'>$price_lvl</span></td>";
        }
        return $month_table;
    }

    function getClientsSales($client_id, $month, $cash_id) { $db = DbSingleton::getDb();
        $sales = 0; $cash_cap = "";
        if ($cash_id==1) $cash_cap="s_uah";
        if ($cash_id==2) $cash_cap="s_usd";
        if ($cash_id==3) $cash_cap="s_eur";
        $r = $db->query("SELECT * FROM `J_CLIENTS_SALE` WHERE `client_id`='$client_id' AND `month`='$month' AND `status`=1;"); $n = $db->num_rows($r);
        if ($n>0) {
            $sales = $db->result($r,0,"$cash_cap");
            $status = true;
        } else {
            $status = false;
        }
        return array($status, $sales);
    }

    function addClientSales($client_id, $month, $s_uah, $s_usd, $s_eur) { $db = DbSingleton::getDb();
        $r = $db->query("SELECT * FROM `J_CLIENTS_SALE` WHERE `client_id`='$client_id' AND `month`='$month' AND `status`=1 LIMIT 1;"); $n = $db->num_rows($r);
        if ($n==0) $db->query("INSERT INTO `J_CLIENTS_SALE` (`client_id`, `month`, `s_uah`, `s_usd`, `s_eur`, `status`) 
        VALUES ('$client_id', '$month', '$s_uah', '$s_usd', '$s_eur', '1');");
        return true;
    }

    function getMonthList($date_start, $date_end) {
        $dates = [];
        $start = strtotime($date_start);
        $month = strtotime(date("Y-m-01", strtotime($date_start)));
        $end = strtotime($date_end);
        $i = 0;
        while($month <= $end) {
            $date1 = date("Y-m-01", $month);
            $date2 = date("Y-m-t", $month);
            $cur_month = date("Y-m",$month);
            $sel_month = date("Y-m",$month);
            $str_month = date("Y-m",$start); $str_day = date("d",$start);
            $end_month = date("Y-m",$end); $end_day = date("d",$end);
            if ($cur_month==$str_month) {$date1 = date("Y-m-$str_day", $month);}
            if ($cur_month==$end_month) {$date2 = date("Y-m-$end_day", $month);}
            $dates[$i] = [$date1, $date2, $sel_month];
            $month = strtotime("+1 month", $month);
            $i++;
        }
        return $dates;
    }

    function getClientPrice($client_id, $date_start, $date_end) { $db = DbSingleton::getDb();
        $price_status=0; $list=""; $list.="<br>[";
        $old_price_lvl=$old_margin_price_lvl=99999;
        $r = $db->query("SELECT * FROM `A_CLIENTS_CONDITIONS_HISTORY` WHERE `client_id`='$client_id' AND `data`>='$date_start 00:00:00' AND `data`<='$date_end 23:59:59';"); $n = $db->num_rows($r);
        if ($n>0) {
            $tn = 0;
            for ($i=1; $i<=$n; $i++) {
                $price_lvl = $db->result($r,$i-1,"price_lvl");
                $margin_price_lvl = $db->result($r,$i-1,"margin_price_lvl");
                if (!($old_price_lvl==$price_lvl && $old_margin_price_lvl==$margin_price_lvl)) {
                    $tn++;
                    $list.="$price_lvl($margin_price_lvl)=>";
                }
                $old_price_lvl = $price_lvl;
                $old_margin_price_lvl = $margin_price_lvl;
            }
            if ($tn>1) $price_status = 1;
        } else {
            $price_status = 0;
            $r = $db->query("SELECT * FROM `A_CLIENTS_CONDITIONS` WHERE `client_id`='$client_id' LIMIT 1;");
            $price_lvl = $db->result($r,0,"price_lvl");
            $margin_price_lvl = $db->result($r,0,"margin_price_lvl");
            $list.="$price_lvl($margin_price_lvl)=>";
        }
        $list = trim($list, "=>");
        $list.="]";
        return array($list, $price_status);
    }

    function getLocationList($key) { $db = DbSingleton::getDb();
        $r = $db->query("SELECT * FROM `T2_$key` ORDER BY `".$key."_NAME` ASC;"); $n = $db->num_rows($r); $list = "";
        for ($i=1; $i<=$n; $i++) {
            $location_id = $db->result($r,$i-1,$key."_ID");
            $location_name = $db->result($r,$i-1,$key."_NAME");
            $list.="<option value='$location_id'>$location_name</option>";
        }
        return $list;
    }

    function getLocationClientsList($states, $regions, $citys) { $db = DbSingleton::getDb();
        $where_states = $where_regions = $where_citys = $where = ""; $clients = [];
        if ($states!="") $where_states = "`state` IN ($states)";
        if ($regions!="") $where_regions = "`region` IN ($regions)";
        if ($citys!="") $where_citys = "`city` IN ($citys)";
        if ($where_states!="" && $where_regions!="" && $where_citys!="") $where = "$where_states OR $where_regions OR $where_citys";
        if ($where_states!="" && $where_regions=="" && $where_citys!="") $where = "$where_states OR $where_citys";
        if ($where_states!="" && $where_regions=="" && $where_citys=="") $where = "$where_states";
        if ($where_states=="" && $where_regions!="" && $where_citys!="") $where = "$where_regions OR $where_citys";
        if ($where_states=="" && $where_regions!="" && $where_citys=="") $where = "$where_regions";
        if ($where_states!="" && $where_regions!="" && $where_citys=="") $where = "$where_states OR $where_regions";
        if ($where_states=="" && $where_regions=="" && $where_citys!="") $where = "$where_citys";
        if ($where!="") $where="AND ($where)";
        $r = $db->query("SELECT * FROM `A_CLIENTS` WHERE `status`=1 $where;"); $n = $db->num_rows($r);
        for ($i=1; $i<=$n; $i++) {
            $client_id = $db->result($r,$i-1,"id");
            array_push($clients, $client_id);
        }
        return $clients;
    }

    function getPriceClientsList($price_id) { $db = DbSingleton::getDb();
        $clients = [];
        if ($price_id==0) {
            $r = $db->query("SELECT * FROM `A_CLIENTS` WHERE `status`=1;"); $n = $db->num_rows($r);
            for ($i=1; $i<=$n; $i++) {
                $client_id = $db->result($r,$i-1,"id");
                array_push($clients, $client_id);
            }
        } else {
            $price_id = $price_id - 1;
            $r = $db->query("SELECT * FROM `A_CLIENTS_CONDITIONS` WHERE `price_lvl`='$price_id';"); $n = $db->num_rows($r);
            for ($i=1; $i<=$n; $i++) {
                $client_id = $db->result($r,$i-1,"client_id");
                array_push($clients, $client_id);
            }
        }
        return $clients;
    }

    function updateCitysRange($text, $citys_selected) { $db = DbSingleton::getDb();
        $citys_range = [];
        $citys_selected = implode(",", $citys_selected);
        $citys_selected != "" ? $where = "AND `CITY_ID` NOT IN ($citys_selected)" : $where = "";
        $r = $db->query("SELECT *  FROM `T2_CITY` WHERE `CITY_NAME` LIKE '%$text%' $where;"); $n = $db->num_rows($r);
        for ($i=1; $i<=$n; $i++) {
            $city_id = $db->result($r,$i-1,"CITY_ID");
            $city_name = $db->result($r,$i-1,"CITY_NAME");
            $citys_range[$city_id] = $city_name;
        }
        return $citys_range;
    }

    /*
     * REPORT SALES ARTILCES
     *
     * */
    function getParrentGoodsGroup($goods_group) { $db=DbSingleton::getTokoDb();
        $parrent_ids = [];
        $r = $db->query("SELECT * FROM `GOODS_GROUP` WHERE `PARRENT_ID` IN ($goods_group);"); $n = $db->num_rows($r);
        if ($n>0) {
            for ($i=1;$i<=$n;$i++) {
                $id = $db->result($r, $i - 1, "ID");
                array_push($parrent_ids, $id);
            }
        }
        $goods_group = explode(",", $goods_group);
        $arr = array_merge($goods_group, $parrent_ids);
        $arr = implode(",", $arr);
        return $arr;
    }

    function getArticleMainInfo($art_id) { $db = DbSingleton::getTokoDb();
        $r=$db->query("SELECT t2a.ARTICLE_NR_DISPL, t2b.BRAND_NAME, gg.NAME as GOODS_GROUP_NAME, t2p.GENERAL_STOCK, t2pck.GENERAL_QUANT as gq 
        FROM  `T2_ARTICLES` t2a
            LEFT JOIN `T2_ARTICLES_PRICE_STOCK` t2p ON (t2p.ART_ID=t2a.ART_ID)
            LEFT JOIN `T2_GOODS_GROUP` t2g ON (t2g.ART_ID=t2a.ART_ID)  
            LEFT JOIN `GOODS_GROUP` gg ON (gg.ID=t2g.GOODS_GROUP_ID)
            LEFT JOIN `T2_BRANDS` t2b ON (t2b.BRAND_ID=t2a.BRAND_ID)
            LEFT JOIN `T2_PACKAGING` t2pck ON (t2pck.ART_ID=t2a.ART_ID)
        WHERE t2a.ART_ID='$art_id' LIMIT 1;");
        $article_nr_displ = $db->result($r, 0, "ARTICLE_NR_DISPL");
        $brand_name = $db->result($r, 0, "BRAND_NAME");
        $goods_group_name = $db->result($r, 0, "GOODS_GROUP_NAME");
        $general_stock = $db->result($r, 0, "GENERAL_STOCK");
        $general_quant = $db->result($r, 0, "gq"); // NORMA SKLADA

        return array($article_nr_displ, $brand_name, $goods_group_name, $general_stock, $general_quant);
    }

    function getArticleIdInfo($art_id) { $db = DbSingleton::getTokoDb();
        $r = $db->query("SELECT t2a.BRAND_ID, t2g.GOODS_GROUP_ID 
        FROM  `T2_ARTICLES` t2a
            LEFT JOIN `T2_GOODS_GROUP` t2g ON (t2g.ART_ID=t2a.ART_ID)  
        WHERE t2a.ART_ID='$art_id' LIMIT 1;");
        $brand_id = $db->result($r, 0, "BRAND_ID");
        $goods_good_id = $db->result($r, 0, "GOODS_GROUP_ID");
        return array($brand_id, $goods_good_id);
    }

    function getReportArray($table, $table_nom, $where_date, $where_client_ids, $brands, $goods_group, $order="") { $db = DbSingleton::getTokoDb();
        $array = [];

        $table_str = "$table"."_STR";
        $table_id = "sis.".$table_nom;
        if ($brands==0) $brands="";
        if ($goods_group==0) $goods_group="";

        $brands_arr = explode(",", $brands);
        $goods_arr = explode(",", $goods_group);

        $r = $db->query("SELECT sis.art_id, SUM(sis.amount) as art_amount
        FROM myparts_dba.`$table` si
            LEFT OUTER JOIN myparts_dba.`$table_str` sis ON ($table_id = si.id) 
        WHERE 1 $where_date $where_client_ids
        GROUP BY sis.art_id
        $order;"); $n = $db->num_rows($r);

        for ($i=1; $i<=$n; $i++) {
            $art_id = $db->result($r,$i-1,"art_id");
            list($brand_id, $goods_group_id) = $this->getArticleIdInfo($art_id);
            if ($brands=="" && $goods_group=="") {
                array_push($array, $art_id);
            } elseif ($brands!="" && $goods_group!="") {
                if (in_array($brand_id, $brands_arr) && in_array($goods_group_id, $goods_arr)) {
                    array_push($array, $art_id);
                }
            } elseif ($brands!="" || $goods_group!="") {
                if ($brands!="") {
                    if (in_array($brand_id, $brands_arr)) {
                        array_push($array, $art_id);
                    }
                }
                if ($goods_group!="") {
                    if (in_array($goods_group_id, $goods_arr)) {
                        array_push($array, $art_id);
                    }
                }
            }
        }

        return $array;
    }

    function getArticlesStorageArray($brands, $goods_group) { $db = DbSingleton::getTokoDb();
        $array = [];
        if ($brands==0) $brands="";
        if ($goods_group==0) $goods_group="";

        $brands_arr = explode(",", $brands);
        $goods_arr = explode(",", $goods_group);

        $r = $db->query("SELECT `ART_ID`, (`AMOUNT`+`RESERV_AMOUNT`) as total FROM `T2_ARTICLES_STRORAGE` HAVING total > 0;");  $n = $db->num_rows($r);
        for ($i=1;$i<=$n;$i++) {
            $art_id = $db->result($r,$i-1,"ART_ID");
            list($brand_id, $goods_group_id) = $this->getArticleIdInfo($art_id);
            if ($brands=="" && $goods_group=="") {
                array_push($array, $art_id);
            } elseif ($brands!="" && $goods_group!="") {
                if (in_array($brand_id, $brands_arr) && in_array($goods_group_id, $goods_arr)) {
                    array_push($array, $art_id);
                }
            } elseif ($brands!="" || $goods_group!="") {
                if ($brands!="") {
                    if (in_array($brand_id, $brands_arr)) {
                        array_push($array, $art_id);
                    }
                }
                if ($goods_group!="") {
                    if (in_array($goods_group_id, $goods_arr)) {
                        array_push($array, $art_id);
                    }
                }
            }
        }
        return $array;
    }

    function getArticlesTableArray($brands, $goods_group) { $db = DbSingleton::getTokoDb();
        $array = [];
        if ($brands==0) $brands="";
        if ($goods_group==0) $goods_group="";

        $brands_arr = explode(",", $brands);
        $goods_arr = explode(",", $goods_group);

        $r = $db->query("SELECT t2a.`ART_ID`, t2pck.GENERAL_QUANT
        FROM `T2_ARTICLES` t2a
             LEFT JOIN `T2_PACKAGING` t2pck ON (t2pck.ART_ID=t2a.ART_ID)
        WHERE t2pck.GENERAL_QUANT>0;"); $n = $db->num_rows($r);

        for ($i=1; $i<=$n; $i++) {
            $art_id = $db->result($r,$i-1,"ART_ID");
            list($brand_id, $goods_group_id) = $this->getArticleIdInfo($art_id);
            if ($brands=="" && $goods_group=="") {
                array_push($array, $art_id);
            } elseif ($brands!="" && $goods_group!="") {
                if (in_array($brand_id, $brands_arr) && in_array($goods_group_id, $goods_arr)) {
                    array_push($array, $art_id);
                }
            } elseif ($brands!="" || $goods_group!="") {
                if ($brands!="") {
                    if (in_array($brand_id, $brands_arr)) {
                        array_push($array, $art_id);
                    }
                }
                if ($goods_group!="") {
                    if (in_array($goods_group_id, $goods_arr)) {
                        array_push($array, $art_id);
                    }
                }
            }
        }
        return $array;
    }

    function showReportSalesArticles($date_start, $date_end, $brands, $goods_group, $client_ids, $params) {
        $list=""; $form=""; $form_htm=RD."/tpl/report_sales_articles_table.htm"; if (file_exists("$form_htm")){ $form=file_get_contents($form_htm);}
        $summ_sales=0;  $avables=[];

        list(
            "availability"  =>$availability,
            "real_cost"     =>$real_cost,
            "real_sale"     =>$real_sale,
            "last_income"   =>$last_income,
            "storage_rate"  =>$storage_rate,
            "create_order"  =>$create_order
        ) = $params;

        if ($goods_group!="") $goods_group = $this->getParrentGoodsGroup($goods_group);

        $client_ids=="" ? $where_client_ids="" : $where_client_ids="AND si.client_conto_id IN ($client_ids)";
        $client_ids=="" ? $where_client_back_ids="" : $where_client_back_ids="AND si.client_id IN ($client_ids)";

        $where_date="AND si.data_create>='$date_start' AND si.data_create<='$date_end' ";
        $where_date_back="AND si.data>='$date_start' AND si.data<='$date_end' ";

        $dates = $this->getMonthList($date_start, $date_end);
        $month_table=""; $month_td="";
        foreach ($dates as $date) {
            $month_id=date("m",strtotime($date[0]));
            $year_id=date("y",strtotime($date[0]));
            $month_table.="<th>$month_id/$year_id</th>";
            $month_td.="<td>-</td>";
        }

        $sales = $this->getReportArray("J_SALE_INVOICE", "invoice_id", $where_date, $where_client_ids, $brands, $goods_group);
        $backs = $this->getReportArray("J_BACK_CLIENTS", "back_id", $where_date_back, $where_client_back_ids, $brands, $goods_group);
        $arts = array_merge($sales, $backs); $arts = array_unique($arts);

        foreach ($arts as $art_id) {
            list($article_nr_displ, $brand_name, $goods_group_name, $general_stock, $general_quant) = $this->getArticleMainInfo($art_id);
            list($income_date, $income_amount)=$this->getIncomeArticleInfo($art_id);
            list($month_body, $summ_amount)=$this->calculateSummSalesArticleMonthly($date_start, $date_end, $art_id, $client_ids);
            list($price_partition, $oper_price_partition)=$this->getSummPartitionSalesArticle($date_start, $date_end, $art_id);
            $order = $general_quant - $general_stock; if ($order<0) $style_order = "style='background:pink;'"; else $style_order = "";

            $real_cost ? $real_cost_list="<td>$price_partition</td>" : $real_cost_list="";
            $real_sale ? $real_sale_list="<td>$oper_price_partition</td>" : $real_sale_list="";
            $last_income ? $last_income_list="<td>$income_date</td><td>$income_amount</td>" : $last_income_list="";
            $storage_rate ? $storage_rate_list="<td>$general_quant</td>" : $storage_rate_list="";
            $create_order ? $create_order_list="<td $style_order>$order</td>" : $create_order_list="";

            $list.="<tr>
                <td>
                    <a class='btn-xs btn-primary' onclick=\"showCatalogueCard('$art_id')\" title='Карта товара'><i class='fa fa-car'></i></a>
                    <a class='btn-xs btn-warning' onclick=\"showArticleJDocs('$art_id')\" title='Історія товара'><i class='fa fa-history'></i></a>
                    $article_nr_displ
                </td>
                <td>$brand_name</td>
                <td>$goods_group_name</td>
                <td>$general_stock</td>
                $last_income_list
                $storage_rate_list
                $create_order_list
                <td>$summ_amount</td>
                $real_cost_list
                $real_sale_list
                $month_body
            </tr>";
            $summ_sales+=$summ_amount;
        }

        if ($availability) {
            $avables = $this->getArticlesStorageArray($brands, $goods_group);
            foreach ($avables as $art_id) {
                list($article_nr_displ, $brand_name, $goods_group_name, $general_stock, $general_quant) = $this->getArticleMainInfo($art_id);
                $order = $general_quant - $general_stock; if ($order<0) $style_order="style='background:pink;'"; else $style_order="";
                $real_cost ? $real_cost_list="<td>0</td>" : $real_cost_list="";
                $real_sale ? $real_sale_list="<td>0</td>" : $real_sale_list="";
                $last_income ? $last_income_list="<td>0</td><td>0</td>" : $last_income_list="";
                $storage_rate ? $storage_rate_list="<td>$general_quant</td>" : $storage_rate_list="";
                $create_order ? $create_order_list="<td $style_order>$order</td>" : $create_order_list="";

                if (!(in_array($art_id, $arts)))
                    $list.="<tr style='background: lightyellow'>
                    <td>
                        <a class='btn-xs btn-primary' onclick=\"showCatalogueCard('$art_id')\" title='Карта товара'><i class='fa fa-car'></i></a>
                        <a class='btn-xs btn-warning' onclick=\"showArticleJDocs('$art_id')\" title='Історія товара'><i class='fa fa-history'></i></a>
                        $article_nr_displ
                    </td>
                    <td>$brand_name</td>
                    <td>$goods_group_name</td>
                    <td>$general_stock</td>
                    $last_income_list
                    $storage_rate_list
                    $create_order_list
                    <td>0</td>
                    $real_cost_list
                    $real_sale_list
                    $month_td
                </tr>";
            }
        }

        if ($storage_rate || $create_order) {
            $quants = $this->getArticlesTableArray($brands, $goods_group);
            foreach ($quants as $art_id) {
                list($article_nr_displ, $brand_name, $goods_group_name, $general_stock, $general_quant) = $this->getArticleMainInfo($art_id);
                $order = $general_quant - $general_stock; if ($order<0) $style_order="style='background:pink;'"; else $style_order="";
                $real_cost ? $real_cost_list="<td>0</td>" : $real_cost_list="";
                $real_sale ? $real_sale_list="<td>0</td>" : $real_sale_list="";
                $last_income ? $last_income_list="<td>0</td><td>0</td>" : $last_income_list="";
                $storage_rate ? $storage_rate_list="<td>$general_quant</td>" : $storage_rate_list="";
                $create_order ? $create_order_list="<td $style_order>$order</td>" : $create_order_list="";

                if (!(in_array($art_id, $avables)) && !(in_array($art_id, $arts)))
                $list.="<tr style='background: lightskyblue'>
                    <td>
                        <a class='btn-xs btn-primary' onclick=\"showCatalogueCard('$art_id')\" title='Карта товара'><i class='fa fa-car'></i></a>
                        <a class='btn-xs btn-warning' onclick=\"showArticleJDocs('$art_id')\" title='Історія товара'><i class='fa fa-history'></i></a>
                        $article_nr_displ
                    </td>
                    <td>$brand_name</td>
                    <td>$goods_group_name</td>
                    <td>$general_stock</td>
                    $last_income_list
                    $storage_rate_list
                    $create_order_list
                    <td>0</td>
                    $real_cost_list
                    $real_sale_list
                    $month_td
                </tr>";
            }
        }

        $form=str_replace("{report_sales_articles_range}",$list,$form);
        $form=str_replace("{summ_reports}",$summ_sales,$form);
        $form=str_replace("{month_table}",$month_table,$form);
        $form=str_replace("{last_income_status}",$last_income ? "<th>Дата останнього надходження</th><th>К-сть останнього надходження</th>" : "",$form);
        $form=str_replace("{storage_rate_status}",$storage_rate ? "<th>Общая норма, шт.</th>" : "",$form);
        $form=str_replace("{create_order_status}",$create_order ? "<th>Замовлення</th>" : "",$form);
        $form=str_replace("{real_cost_status}",$real_cost ? "<th>Собівартість, $</th>" : "",$form);
        $form=str_replace("{real_sale_status}",$real_sale ? "<th>Продаж, $</th>" : "",$form);
        return $form;
    }

    function calculateSummSalesArticleMonthly($date_start, $date_end, $art_id, $clients) {
        $dates = $this->getMonthList($date_start, $date_end);
        $month_table=""; $all_summ=0; $abs_all_summ=0; $month_arr=[];
        foreach ($dates as $date) {
            $summ_amount=$this->getAmountSalesArticle($date[0], $date[1], $art_id, $clients);
            if ($summ_amount==0) $month_table.="<td style='color: #dadada;'>$summ_amount</td>";
            else $month_table.="<td style='font-weight: bold'>$summ_amount</td>";
            array_push($month_arr, $summ_amount);
            $all_summ+=$summ_amount;
            $abs_all_summ+=abs($summ_amount);
        }
        return array($month_table, $all_summ, $abs_all_summ, $month_arr);
    }

    function getIncomeArticleInfo($art_id) { $db = DbSingleton::getDb();
        $date="0000-00-00 00:00:00"; $amount=0;
        $r = $db->query("SELECT * FROM `J_ART_DOCS` WHERE `art_id`='$art_id' AND `doc_type`=1 ORDER BY `id` DESC LIMIT 1;"); $n = $db->num_rows($r);
        if ($n>0) {
            $date = $db->result($r,0,"data");
            $amount = intval($db->result($r,0,"amount"));
        }
        return array($date, $amount);
    }

    function getAmountSalesArticle($data_start, $data_end, $art_id, $clients) { $db = DbSingleton::getDb();
        $clients=="" ? $where_clients="" : $where_clients="AND si.client_conto_id IN ($clients)";
        $clients=="" ? $where_clients_backs="" : $where_clients_backs="AND si.client_id IN ($clients)";

        $r=$db->query("SELECT SUM(sis.amount) as amount_sales 
        FROM `J_SALE_INVOICE_STR` sis 
            INNER JOIN `J_SALE_INVOICE` si ON (si.id=sis.invoice_id) 
        WHERE si.data_create>='$data_start' AND si.data_create<='$data_end' AND sis.art_id='$art_id' $where_clients GROUP BY sis.art_id LIMIT 1;");
        $n=$db->num_rows($r); $n>0 ? $amount_sales=$db->result($r,0,"amount_sales") : $amount_sales=0;

        $r=$db->query("SELECT SUM(sis.amount) as amount_backs 
        FROM `J_BACK_CLIENTS_STR` sis 
            INNER JOIN `J_BACK_CLIENTS` si ON (si.id=sis.back_id) 
        WHERE si.data>='$data_start' AND si.data<='$data_end' AND sis.art_id='$art_id' $where_clients_backs GROUP BY sis.art_id LIMIT 1;");
        $n=$db->num_rows($r); $n>0 ? $amount_backs=$db->result($r,0,"amount_backs") : $amount_backs=0;

        $amount = $amount_sales - $amount_backs;

        return $amount;
    }

    function getSummPartitionSalesArticle($date_start, $date_end, $art_id) { $db = DbSingleton::getDb();
        $sales_price_partition=$sales_oper_price_partition=$backs_price_partition=$backs_oper_price_partition=0;

        $r=$db->query("SELECT SUM(jsp.price_invoice) as sum_price_invoice, SUM(jsp.oper_price_partition) as sum_oper_price_partition 
        FROM `J_SALE_INVOICE` js
            LEFT OUTER JOIN `J_SALE_INVOICE_PARTITION_STR` jsp ON (jsp.invoice_id=js.id)
		WHERE js.time_stamp>='$date_start 00:00:00' AND js.time_stamp<='$date_end 23:59:59' AND jsp.art_id='$art_id' GROUP BY jsp.art_id LIMIT 1;"); $n=$db->num_rows($r);
        if ($n>0) {
            $sales_price_partition=$db->result($r,0,"sum_price_invoice");
            $sales_oper_price_partition=$db->result($r,0,"sum_oper_price_partition");
        }

        $r=$db->query("SELECT SUM(jsp.price_invoice) as sum_price_invoice, SUM(jsp.oper_price_partition) as sum_oper_price_partition 
        FROM `J_BACK_CLIENTS` js
            LEFT OUTER JOIN `J_BACK_CLIENTS_PARTITION_STR` jsp ON (jsp.back_id=js.id)
		WHERE js.time_stamp>='$date_start 00:00:00' AND js.time_stamp<='$date_end 23:59:59' AND jsp.art_id='$art_id' GROUP BY jsp.art_id LIMIT 1;"); $n=$db->num_rows($r);
        if ($n>0) {
            $backs_price_partition=$db->result($r,0,"sum_price_invoice");
            $backs_oper_price_partition=$db->result($r,0,"sum_oper_price_partition");
        }

        $price_partition = $sales_price_partition-$backs_price_partition;
        $oper_price_partition = $sales_oper_price_partition-$backs_oper_price_partition;

        return array($oper_price_partition, $price_partition);
    }

    function exportReportSales($date_start, $date_end, $brands, $goods_group, $client_ids, $params) {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=export.csv'); ob_clean();
        $output = fopen('php://output', 'w');
        list($title, $array) = $this->getReportSalesData($date_start, $date_end, $brands, $goods_group, $client_ids, $params);
        foreach ($title as $fields) {
            fputcsv($output, $fields, $delimiter = ';');
        }
        foreach ($array as $fields) {
            fputcsv($output, $fields, $delimiter = ';');
        }
        exit(0);
    }

    function getReportSalesData($date_start, $date_end, $brands, $goods_group, $client_ids, $params) {
        $array = []; $title = []; $avables = []; $i = 0;

        list(
            "availability"  =>$availability,
            "real_cost"     =>$real_cost,
            "real_sale"     =>$real_sale,
            "last_income"   =>$last_income,
            "storage_rate"  =>$storage_rate,
            "create_order"  =>$create_order
        ) = $params;

        $title[0] = array("Артикул", "Бренд", "Група товарів", "Залишок", "Загальний продаж");
        if ($real_cost)     { array_push($title[0], "Собівартість"); }
        if ($real_sale)     { array_push($title[0], "Ціна накладної"); }
        if ($last_income)   { array_push($title[0], "Останнє надходження"); }
        if ($storage_rate)  { array_push($title[0], "Загальна норма"); }
        if ($create_order)  { array_push($title[0], "Замовлення"); }

        $month_table=""; $month_td="";
        foreach ($this->getMonthList($date_start, $date_end) as $date) {
            $month_id=date("m",strtotime($date[0]));
            $year_id=date("y",strtotime($date[0]));
            array_push($title[0], "[$month_id / $year_id]");
            $month_table.="<th>$month_id/$year_id</th>";
            $month_td.="<td>-</td>";
        }

        if ($goods_group!="") $goods_group = $this->getParrentGoodsGroup($goods_group);
        $client_ids=="" ? $where_client_ids="" : $where_client_ids="AND si.client_conto_id IN ($client_ids)";
        $client_ids=="" ? $where_client_back_ids="" : $where_client_back_ids="AND si.client_id IN ($client_ids)";

        $where_date="AND si.data_create>='$date_start' AND si.data_create<='$date_end' ";
        $where_date_back="AND si.data>='$date_start' AND si.data<='$date_end' ";

        $sales = $this->getReportArray("J_SALE_INVOICE", "invoice_id", $where_date, $where_client_ids, $brands, $goods_group);
        $backs = $this->getReportArray("J_BACK_CLIENTS", "back_id", $where_date_back, $where_client_back_ids, $brands, $goods_group);
        $arts = array_merge($sales, $backs); $arts = array_unique($arts);

        foreach ($arts as $art_id) { $i++;
            list($article_nr_displ, $brand_name, $goods_group_name, $general_stock, $general_quant) = $this->getArticleMainInfo($art_id);
            list($income_date, $income_amount)=$this->getIncomeArticleInfo($art_id);
            list(, $summ_amount, , $month_arr)=$this->calculateSummSalesArticleMonthly($date_start, $date_end, $art_id, $client_ids);
            list($price_partition, $oper_price_partition)=$this->getSummPartitionSalesArticle($date_start, $date_end, $art_id);
            $order = $general_quant - $general_stock;

            $array[$i] = array("$article_nr_displ", "$brand_name", "$goods_group_name", $general_stock, $summ_amount);
            if ($real_cost)             { array_push($array[$i], $price_partition); }
            if ($real_sale)             { array_push($array[$i], $oper_price_partition); }
            if ($last_income)           { array_push($array[$i], $income_date); array_push($array[$i], $income_amount); }
            if ($storage_rate)          { array_push($array[$i], $general_quant); }
            if ($create_order)          { array_push($array[$i], $order); }
            if (!empty($month_arr))     { foreach ($month_arr as $month) {array_push($array[$i], $month);} }
        }

        if ($availability) {
            $avables = $this->getArticlesStorageArray($brands, $goods_group);
            foreach ($avables as $art_id) {
                list($article_nr_displ, $brand_name, $goods_group_name, $general_stock, $general_quant) = $this->getArticleMainInfo($art_id);
                $order = $general_quant - $general_stock;

                if (!(in_array($art_id, $arts))) { $i++;
                    $array[$i] = array("$article_nr_displ", "$brand_name", "$goods_group_name", $general_stock, 0);
                    if ($real_cost)             { array_push($array[$i], 0); }
                    if ($real_sale)             { array_push($array[$i], 0); }
                    if ($last_income)           { array_push($array[$i], 0); array_push($array[$i], 0); }
                    if ($storage_rate)          { array_push($array[$i], $general_quant); }
                    if ($create_order)          { array_push($array[$i], $order); }
                    if (!empty($month_arr))     { foreach ($month_arr as $month) {array_push($array[$i], $month);} }
                }
            }
        }

        if ($storage_rate || $create_order) {
            $quants = $this->getArticlesTableArray($brands, $goods_group);
            foreach ($quants as $art_id) {
                list($article_nr_displ, $brand_name, $goods_group_name, $general_stock, $general_quant) = $this->getArticleMainInfo($art_id);
                $order = $general_quant - $general_stock;

                if (!(in_array($art_id, $avables)) && !(in_array($art_id, $arts))) { $i++;
                    $array[$i] = array("$article_nr_displ", "$brand_name", "$goods_group_name", $general_stock, 0);
                    if ($real_cost)             { array_push($array[$i], 0); }
                    if ($real_sale)             { array_push($array[$i], 0); }
                    if ($last_income)           { array_push($array[$i], 0); array_push($array[$i], 0); }
                    if ($storage_rate)          { array_push($array[$i], $general_quant); }
                    if ($create_order)          { array_push($array[$i], $order); }
                    if (!empty($month_arr))     { foreach ($month_arr as $month) {array_push($array[$i], $month);} }
                }
            }
        }

        return array($title, $array);
    }

}
