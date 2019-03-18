<?php

class report_margin {
	
	function getClientDocsMarginReportData($type_id,$client_id,$doc_type_id,$date_start,$date_end,$cash_id) { $db=new db; $slave=new slave; 
		$where="";$sales=$backs=[]; 
		if ($type_id==0) {
			if ($client_id!=0) $where=" and j.client_conto_id='$client_id' "; else $where="";
			$r=$db->query("select j.*, jsp.invoice_amount, jsp.oper_price_partition, jsp.price_partition, jsp.price_buh_uah, jsp.price_man_uah,jsp.price_invoice from J_SALE_INVOICE j
			inner join J_SALE_INVOICE_PARTITION_STR jsp on jsp.invoice_id=j.id
			where j.time_stamp>='$date_start 00:00:00' and j.time_stamp<='$date_end 23:59:59' $where and j.doc_type_id='$doc_type_id';"); $n=$db->num_rows($r); $list=""; 
			for ($i=1;$i<=$n;$i++){
				$id=$db->result($r,$i-1,"j.id");
				$prefix=$db->result($r,$i-1,"prefix"); $doc_nom=$db->result($r,$i-1,"doc_nom"); $prefix=$prefix."-".$doc_nom;
				$prefix=$slave->translit($prefix); $prefix = mb_convert_encoding($prefix, "SJIS");
				$data_pay=$db->result($r,$i-1,"data_create");
				$usd_to_uah=$db->result($r,$i-1,"usd_to_uah");
				$eur_to_uah=$db->result($r,$i-1,"eur_to_uah");
				$oper_price_partition=$db->result($r,$i-1,"oper_price_partition");
				$price_partition=$db->result($r,$i-1,"price_partition");
				$price_buh_uah=$db->result($r,$i-1,"price_buh_uah");
				$price_man_uah=$db->result($r,$i-1,"price_man_uah");
				$price_invoice=$db->result($r,$i-1,"price_invoice");
				$partition_amount=$db->result($r,$i-1,"invoice_amount");

				$oper_price_partition=$this->getSummCash($oper_price_partition,2,$usd_to_uah,$eur_to_uah,$cash_id);
				$price_partition=$this->getSummCash($price_partition,2,$usd_to_uah,$eur_to_uah,$cash_id);
				$price_buh_uah=$this->getSummCash($price_buh_uah,1,$usd_to_uah,$eur_to_uah,$cash_id);
				$price_man_uah=$this->getSummCash($price_man_uah,1,$usd_to_uah,$eur_to_uah,$cash_id);
				$price_invoice=$this->getSummCash($price_invoice,2,$usd_to_uah,$eur_to_uah,$cash_id);			

				$salesd[$id]["doc_name"]=$prefix." vid ".$data_pay;
				$salesd[$id]["oper_price_partition"]+=$oper_price_partition*$partition_amount;
				$salesd[$id]["price_partition"]+=$price_partition*$partition_amount;
				$salesd[$id]["price_buh_uah"]+=$price_buh_uah*$partition_amount;
				$salesd[$id]["price_man_uah"]+=$price_man_uah*$partition_amount;
				$salesd[$id]["price_invoice"]+=$price_invoice*$partition_amount;
				$salesd[$id]["type_id"]=0;
			}
			return $salesd;
		} 
		else {
			if ($client_id!=0) $where=" and j.client_id='$client_id' "; else $where="";
			$r=$db->query("select j.*, jsp.partition_amount, jsp.oper_price_partition, jsp.price_partition, jsp.price_buh_uah, jsp.price_man_uah,jsp.price_invoice from J_BACK_CLIENTS j
			inner join J_BACK_CLIENTS_PARTITION_STR jsp on jsp.back_id=j.id
			where j.time_stamp>='$date_start 00:00:00' and j.time_stamp<='$date_end 23:59:59' $where and j.doc_type_id='$doc_type_id';"); $n=$db->num_rows($r); $list=""; 
			for ($i=1;$i<=$n;$i++){
				$id=$db->result($r,$i-1,"j.id");
				$prefix=$db->result($r,$i-1,"prefix"); $doc_nom=$db->result($r,$i-1,"doc_nom"); $prefix=$prefix."-".$doc_nom;
				$prefix=$slave->translit($prefix); $prefix = mb_convert_encoding($prefix, "SJIS");
				$data_pay=$db->result($r,$i-1,"data_create");
				$usd_to_uah=$db->result($r,$i-1,"usd_to_uah");
				$eur_to_uah=$db->result($r,$i-1,"eur_to_uah");
				$oper_price_partition=$db->result($r,$i-1,"oper_price_partition");
				$price_partition=$db->result($r,$i-1,"price_partition");
				$price_buh_uah=$db->result($r,$i-1,"price_buh_uah");
				$price_man_uah=$db->result($r,$i-1,"price_man_uah");
				$price_invoice=$db->result($r,$i-1,"price_invoice");
				$partition_amount=$db->result($r,$i-1,"partition_amount");

				$oper_price_partition=(-1)*$this->getSummCash($oper_price_partition,2,$usd_to_uah,$eur_to_uah,$cash_id);
				$price_partition=(-1)*$this->getSummCash($price_partition,2,$usd_to_uah,$eur_to_uah,$cash_id);
				$price_buh_uah=(-1)*$this->getSummCash($price_buh_uah,1,$usd_to_uah,$eur_to_uah,$cash_id);
				$price_man_uah=(-1)*$this->getSummCash($price_man_uah,1,$usd_to_uah,$eur_to_uah,$cash_id);
				$price_invoice=(-1)*$this->getSummCash($price_invoice,2,$usd_to_uah,$eur_to_uah,$cash_id);			

				$backsd[$id]["doc_name"]=$prefix." vid ".$data_pay;
				$backsd[$id]["oper_price_partition"]+=$oper_price_partition*$partition_amount;
				$backsd[$id]["price_partition"]+=$price_partition*$partition_amount;
				$backsd[$id]["price_buh_uah"]+=$price_buh_uah*$partition_amount;
				$backsd[$id]["price_man_uah"]+=$price_man_uah*$partition_amount;
				$backsd[$id]["price_invoice"]+=$price_invoice*$partition_amount;
				$backsd[$id]["type_id"]=0;
			}
			return $backsd;
		}
	}
	
	function getClientMarginReportData($type_id,$doc_status,$doc_type_id,$date_start,$date_end,$cash_id) { $db=new db; $clients=new clients; $gmanual=new gmanual;
		$where="";$sales=$backs=[]; $slave=new slave;
		if ($type_id==0) {
			$r=$db->query("select j.*, jsp.invoice_amount, jsp.oper_price_partition, jsp.price_partition, jsp.price_buh_uah, jsp.price_man_uah,jsp.price_invoice from J_SALE_INVOICE j
			inner join J_SALE_INVOICE_PARTITION_STR jsp on jsp.invoice_id=j.id
			where j.time_stamp>='$date_start 00:00:00' and j.time_stamp<='$date_end 23:59:59' and j.doc_type_id='$doc_type_id';"); $n=$db->num_rows($r); $list=""; 
			for ($i=1;$i<=$n;$i++){
				$id=$db->result($r,$i-1,"j.id");
				$doc_type_id=$db->result($r,$i-1,"doc_type_id");
				$usd_to_uah=$db->result($r,$i-1,"usd_to_uah");
				$eur_to_uah=$db->result($r,$i-1,"eur_to_uah");
				$client_id=$db->result($r,$i-1,"client_conto_id"); $client_name=$clients->getClientNameById($client_id,"name");
				$client_name=$slave->translit($client_name); $client_name = mb_convert_encoding($client_name, "SJIS");
				$oper_price_partition=$db->result($r,$i-1,"oper_price_partition");
				$price_partition=$db->result($r,$i-1,"price_partition");
				$price_buh_uah=$db->result($r,$i-1,"price_buh_uah");
				$price_man_uah=$db->result($r,$i-1,"price_man_uah");
				$price_invoice=$db->result($r,$i-1,"price_invoice");
				$partition_amount=$db->result($r,$i-1,"invoice_amount");

				$oper_price_partition=$this->getSummCash($oper_price_partition,2,$usd_to_uah,$eur_to_uah,$cash_id);
				$price_partition=$this->getSummCash($price_partition,2,$usd_to_uah,$eur_to_uah,$cash_id);
				$price_buh_uah=$this->getSummCash($price_buh_uah,1,$usd_to_uah,$eur_to_uah,$cash_id);
				$price_man_uah=$this->getSummCash($price_man_uah,1,$usd_to_uah,$eur_to_uah,$cash_id);
				$price_invoice=$this->getSummCash($price_invoice,2,$usd_to_uah,$eur_to_uah,$cash_id);

				$salesc[$client_id]["client_id"]=$client_name; 
				$salesc[$client_id]["oper_price_partition"]+=$oper_price_partition*$partition_amount;
				$salesc[$client_id]["price_partition"]+=$price_partition*$partition_amount;
				$salesc[$client_id]["price_buh_uah"]+=$price_buh_uah*$partition_amount;
				$salesc[$client_id]["price_man_uah"]+=$price_man_uah*$partition_amount;
				$salesc[$client_id]["price_invoice"]+=$price_invoice*$partition_amount;
				$salesc[$client_id]["type_id"]=3;
			}
			
			$sales_doc=[];																							   
			foreach ($salesc as $arr_key=>$arr_val) {
				$sales_doc[$arr_key]=$arr_val;
				$docs=$this->getClientDocsMarginReportData(0,$arr_key,$doc_type_id,$date_start,$date_end,$cash_id);
				foreach ($docs as $doc_key=>$doc_val) {
					$sales_doc[$arr_key.$doc_key]=$doc_val;
				}
			}																									   
			$salesc=$sales_doc;
			
			return $salesc;	
		} 
																										  
		else {																					
			$r=$db->query("select j.*, jsp.partition_amount, jsp.oper_price_partition, jsp.price_partition, jsp.price_buh_uah, jsp.price_man_uah,jsp.price_invoice from J_BACK_CLIENTS j
			inner join J_BACK_CLIENTS_PARTITION_STR jsp on jsp.back_id=j.id
			where j.time_stamp>='$date_start 00:00:00' and j.time_stamp<='$date_end 23:59:59' and j.doc_type_id='$doc_type_id';"); $n=$db->num_rows($r); 
			for ($i=1;$i<=$n;$i++){
				$id=$db->result($r,$i-1,"j.id");
				$doc_type_id=$db->result($r,$i-1,"doc_type_id");
				$usd_to_uah=$db->result($r,$i-1,"usd_to_uah");
				$eur_to_uah=$db->result($r,$i-1,"eur_to_uah");
				$client_id=$db->result($r,$i-1,"client_id"); $client_name=$clients->getClientNameById($client_id,"name");
				$client_name=$slave->translit($client_name); $client_name = mb_convert_encoding($client_name, "SJIS");
				$oper_price_partition=$db->result($r,$i-1,"oper_price_partition");
				$price_partition=$db->result($r,$i-1,"price_partition");
				$price_buh_uah=$db->result($r,$i-1,"price_buh_uah");
				$price_man_uah=$db->result($r,$i-1,"price_man_uah");
				$price_invoice=$db->result($r,$i-1,"price_invoice");
				$partition_amount=$db->result($r,$i-1,"partition_amount");

				$oper_price_partition=(-1)*$this->getSummCash($oper_price_partition,2,$usd_to_uah,$eur_to_uah,$cash_id);
				$price_partition=(-1)*$this->getSummCash($price_partition,2,$usd_to_uah,$eur_to_uah,$cash_id);
				$price_buh_uah=(-1)*$this->getSummCash($price_buh_uah,1,$usd_to_uah,$eur_to_uah,$cash_id);
				$price_man_uah=(-1)*$this->getSummCash($price_man_uah,1,$usd_to_uah,$eur_to_uah,$cash_id);
				$price_invoice=(-1)*$this->getSummCash($price_invoice,2,$usd_to_uah,$eur_to_uah,$cash_id);

				$backsc[$client_id]["client_id"]=$client_name;
				$backsc[$client_id]["oper_price_partition"]+=$oper_price_partition*$partition_amount;
				$backsc[$client_id]["price_partition"]+=$price_partition*$partition_amount;
				$backsc[$client_id]["price_buh_uah"]+=$price_buh_uah*$partition_amount;
				$backsc[$client_id]["price_man_uah"]+=$price_man_uah*$partition_amount;
				$backsc[$client_id]["price_invoice"]+=$price_invoice*$partition_amount;
				$backsc[$client_id]["type_id"]=3;
			}
			
			$backs_doc=[];																							   
			foreach ($backsc as $arr_key=>$arr_val) {
				$backs_doc[$arr_key]=$arr_val;
				$docs=$this->getClientDocsMarginReportData(1,$arr_key,$doc_type_id,$date_start,$date_end,$cash_id);
				foreach ($docs as $doc_key=>$doc_val) {
					$backs_doc[$arr_key.$doc_key]=$doc_val;
				}
			}																									   
			$backsc=$backs_doc;
			
			return $backsc;
		}
	}

	function getReportMarginDataSales($date_start,$date_end,$doc_type_id,$client_status,$doc_status,$cash_id) { $db=new db; $gmanual=new gmanual; $slave=new slave;
		$summ_list=0; $where=$list=""; $sales=$backs=[]; 
		if($doc_type_id>0) {$where.=" and j.doc_type_id='$doc_type_id'";}	
		$r=$db->query("select j.*, jsp.invoice_amount, jsp.oper_price_partition, jsp.price_partition, jsp.price_buh_uah, jsp.price_man_uah, jsp.price_invoice from J_SALE_INVOICE j
		inner join J_SALE_INVOICE_PARTITION_STR jsp on jsp.invoice_id=j.id
		where j.time_stamp>='$date_start 00:00:00' and j.time_stamp<='$date_end 23:59:59' $where;"); $n=$db->num_rows($r);
		
		for ($i=1;$i<=$n;$i++){
			$id=$db->result($r,$i-1,"j.id");					  
			$doc_type_id=$db->result($r,$i-1,"doc_type_id");
			$usd_to_uah=$db->result($r,$i-1,"usd_to_uah");
			$eur_to_uah=$db->result($r,$i-1,"eur_to_uah");
			$oper_price_partition=$db->result($r,$i-1,"oper_price_partition");
			$price_partition=$db->result($r,$i-1,"price_partition");
			$price_buh_uah=$db->result($r,$i-1,"price_buh_uah");
			$price_man_uah=$db->result($r,$i-1,"price_man_uah");
			$price_invoice=$db->result($r,$i-1,"price_invoice");
			$partition_amount=$db->result($r,$i-1,"invoice_amount");
			$client_id=$db->result($r,$i-1,"client_conto_id");
			$doc_type_name=$gmanual->get_gmanual_caption($doc_type_id); $doc_type_name=$slave->translit($doc_type_name); 
			$doc_type_name = mb_convert_encoding($doc_type_name, "SJIS");
			$sales[$doc_type_id]["doc_type_id"]=$doc_type_name;	 
			
			$oper_price_partition=$this->getSummCash($oper_price_partition,2,$usd_to_uah,$eur_to_uah,$cash_id);
			$price_partition=$this->getSummCash($price_partition,2,$usd_to_uah,$eur_to_uah,$cash_id);
			$price_buh_uah=$this->getSummCash($price_buh_uah,1,$usd_to_uah,$eur_to_uah,$cash_id);
			$price_man_uah=$this->getSummCash($price_man_uah,1,$usd_to_uah,$eur_to_uah,$cash_id);
			$price_invoice=$this->getSummCash($price_invoice,2,$usd_to_uah,$eur_to_uah,$cash_id);
			
			$sales[$doc_type_id]["oper_price_partition"]+=$oper_price_partition*$partition_amount;
			$sales[$doc_type_id]["price_partition"]+=$price_partition*$partition_amount;
			$sales[$doc_type_id]["price_buh_uah"]+=$price_buh_uah*$partition_amount;
			$sales[$doc_type_id]["price_man_uah"]+=$price_man_uah*$partition_amount;
			$sales[$doc_type_id]["price_invoice"]+=$price_invoice*$partition_amount;
			$sales[$doc_type_id]["type_id"]=1;
		}	
																											   
		$sales_cl=[];																							   
	   	foreach ($sales as $arr_key=>$arr_val) {
			$sales_cl[$arr_key]=$arr_val;
			$clients=$this->getClientMarginReportData(0,$doc_status,$arr_key,$date_start,$date_end,$cash_id);
			foreach ($clients as $cl_key=>$cl_val) {
				$sales_cl[$arr_key.$cl_key]=$cl_val;
			}
		}																									   
		$sales=$sales_cl;
																											   
		$r=$db->query("select j.*, jsp.partition_amount, jsp.oper_price_partition, jsp.price_partition, jsp.price_buh_uah, jsp.price_man_uah, jsp.price_invoice from J_BACK_CLIENTS j
		inner join J_BACK_CLIENTS_PARTITION_STR jsp on jsp.back_id=j.id
		where j.time_stamp>='$date_start 00:00:00' and j.time_stamp<='$date_end 23:59:59' $where;"); $n=$db->num_rows($r);
		
		for ($i=1;$i<=$n;$i++){
			$id=$db->result($r,$i-1,"j.id");					  
			$doc_type_id=$db->result($r,$i-1,"doc_type_id");
			$usd_to_uah=$db->result($r,$i-1,"usd_to_uah");
			$eur_to_uah=$db->result($r,$i-1,"eur_to_uah");
			$oper_price_partition=$db->result($r,$i-1,"oper_price_partition");
			$price_partition=$db->result($r,$i-1,"price_partition");
			$price_buh_uah=$db->result($r,$i-1,"price_buh_uah");
			$price_man_uah=$db->result($r,$i-1,"price_man_uah");
			$price_invoice=$db->result($r,$i-1,"price_invoice");
			$partition_amount=$db->result($r,$i-1,"partition_amount");
			$client_id=$db->result($r,$i-1,"client_conto_id");
			$doc_type_name=$gmanual->get_gmanual_caption($doc_type_id); $doc_type_name=$slave->translit($doc_type_name);
			$doc_type_name = mb_convert_encoding($doc_type_name, "SJIS");
			$backs[$doc_type_id]["doc_type_id"]=$doc_type_name;
			
			$oper_price_partition=(-1)*$this->getSummCash($oper_price_partition,2,$usd_to_uah,$eur_to_uah,$cash_id);
			$price_partition=(-1)*$this->getSummCash($price_partition,2,$usd_to_uah,$eur_to_uah,$cash_id);
			$price_buh_uah=(-1)*$this->getSummCash($price_buh_uah,1,$usd_to_uah,$eur_to_uah,$cash_id);
			$price_man_uah=(-1)*$this->getSummCash($price_man_uah,1,$usd_to_uah,$eur_to_uah,$cash_id);
			$price_invoice=(-1)*$this->getSummCash($price_invoice,2,$usd_to_uah,$eur_to_uah,$cash_id);
			
			$backs[$doc_type_id]["oper_price_partition"]+=$oper_price_partition*$partition_amount;
			$backs[$doc_type_id]["price_partition"]+=$price_partition*$partition_amount;
			$backs[$doc_type_id]["price_buh_uah"]+=$price_buh_uah*$partition_amount;
			$backs[$doc_type_id]["price_man_uah"]+=$price_man_uah*$partition_amount;
			$backs[$doc_type_id]["price_invoice"]+=$price_invoice*$partition_amount;
			$backs[$doc_type_id]["type_id"]=2;
			
			$backs_cl=[];																							   
			foreach ($backs as $arr_key=>$arr_val) {
				$backs_cl[$arr_key]=$arr_val;
				$clients=$this->getClientMarginReportData(1,$doc_status,$arr_key,$date_start,$date_end,$cash_id);
				foreach ($clients as $cl_key=>$cl_val) {
					$backs_cl[$arr_key.$cl_key]=$cl_val;
				}
			}																									   
			$backs=$backs_cl;
		}
		$array=[];
		$array=array_merge($sales,$backs);
		
		return $array;
	}

	function showReportMargin($date_start,$date_end,$doc_type_id,$client_status,$doc_status,$cash_id) { $db=new db; $gmanual=new gmanual;
		$form_htm=RD."/tpl/report_margin_list.htm";if (file_exists($form_htm)){ $form = file_get_contents($form_htm);}	 
		$summ_list=0; $where=""; $sales=$backs=[]; 
		if($doc_type_id>0) {$where.=" and j.doc_type_id='$doc_type_id'";}																			  
	    $r=$db->query("select j.*, jsp.invoice_amount, jsp.oper_price_partition, jsp.price_partition, jsp.price_buh_uah, jsp.price_man_uah, jsp.price_invoice from J_SALE_INVOICE j
		inner join J_SALE_INVOICE_PARTITION_STR jsp on jsp.invoice_id=j.id
		where j.time_stamp>='$date_start 00:00:00' and j.time_stamp<='$date_end 23:59:59' $where;"); $n=$db->num_rows($r); $list=""; 

		for ($i=1;$i<=$n;$i++){
			$id=$db->result($r,$i-1,"j.id");					  
			$doc_type_id=$db->result($r,$i-1,"doc_type_id");
			$usd_to_uah=$db->result($r,$i-1,"usd_to_uah");
			$eur_to_uah=$db->result($r,$i-1,"eur_to_uah");
			$oper_price_partition=$db->result($r,$i-1,"oper_price_partition");
			$price_partition=$db->result($r,$i-1,"price_partition");
			$price_buh_uah=$db->result($r,$i-1,"price_buh_uah");
			$price_man_uah=$db->result($r,$i-1,"price_man_uah");
			$price_invoice=$db->result($r,$i-1,"price_invoice");
			$partition_amount=$db->result($r,$i-1,"invoice_amount");
			$client_id=$db->result($r,$i-1,"client_conto_id");
			$sales[$doc_type_id]["doc_type_id"]=$doc_type_id;
			
			$oper_price_partition=$this->getSummCash($oper_price_partition,2,$usd_to_uah,$eur_to_uah,$cash_id);
			$price_partition=$this->getSummCash($price_partition,2,$usd_to_uah,$eur_to_uah,$cash_id);
			$price_buh_uah=$this->getSummCash($price_buh_uah,1,$usd_to_uah,$eur_to_uah,$cash_id);
			$price_man_uah=$this->getSummCash($price_man_uah,1,$usd_to_uah,$eur_to_uah,$cash_id);
			$price_invoice=$this->getSummCash($price_invoice,2,$usd_to_uah,$eur_to_uah,$cash_id);
			
			$sales[$doc_type_id]["oper_price_partition"]+=$oper_price_partition*$partition_amount;
			$sales[$doc_type_id]["price_partition"]+=$price_partition*$partition_amount;
			$sales[$doc_type_id]["price_buh_uah"]+=$price_buh_uah*$partition_amount;
			$sales[$doc_type_id]["price_man_uah"]+=$price_man_uah*$partition_amount;
			$sales[$doc_type_id]["price_invoice"]+=$price_invoice*$partition_amount;
		}
																									   
		foreach($sales as $sls){
			$doc_type_id=$sls["doc_type_id"]; $doc_type_name=$gmanual->get_gmanual_caption($doc_type_id);
			$oper_price_partition=$sls["oper_price_partition"];
			$price_partition=$sls["price_partition"];
			$price_buh_uah=$sls["price_buh_uah"];
			$price_man_uah=$sls["price_man_uah"];
			$price_invoice=$sls["price_invoice"];
			$list.="<tr style='background:pink'>
				<td>$doc_type_name</td>
				<td>$oper_price_partition</td>
				<td>$price_partition</td>
				<td>$price_buh_uah</td>
				<td>$price_man_uah</td>
				<td>$price_invoice</td>
			</tr>";
			if ($client_status==1){
				$list.=$this->getClientMarginReport(0,$doc_status,$doc_type_id,$date_start,$date_end,$cash_id);
			}
			if ($doc_status==1){
				$list.=$this->getClientDocsMarginReport(0,$doc_type_id,$date_start,$date_end,$cash_id);
			}
			$summ_list+=$price_invoice;
		}
		
		$r=$db->query("select j.*, jsp.partition_amount, jsp.oper_price_partition, jsp.price_partition, jsp.price_buh_uah, jsp.price_man_uah, jsp.price_invoice from J_BACK_CLIENTS j
		inner join J_BACK_CLIENTS_PARTITION_STR jsp on jsp.back_id=j.id
		where j.time_stamp>='$date_start 00:00:00' and j.time_stamp<='$date_end 23:59:59' $where;"); $n=$db->num_rows($r); 

		for ($i=1;$i<=$n;$i++){
			$id=$db->result($r,$i-1,"j.id");					  
			$doc_type_id=$db->result($r,$i-1,"doc_type_id");
			$usd_to_uah=$db->result($r,$i-1,"usd_to_uah");
			$eur_to_uah=$db->result($r,$i-1,"eur_to_uah");
			$oper_price_partition=$db->result($r,$i-1,"oper_price_partition");
			$price_partition=$db->result($r,$i-1,"price_partition");
			$price_buh_uah=$db->result($r,$i-1,"price_buh_uah");
			$price_man_uah=$db->result($r,$i-1,"price_man_uah");
			$price_invoice=$db->result($r,$i-1,"price_invoice");
			$partition_amount=$db->result($r,$i-1,"partition_amount");
			$client_id=$db->result($r,$i-1,"client_id");
			$backs[$doc_type_id]["doc_type_id"]=$doc_type_id;
			
			$oper_price_partition=$this->getSummCash($oper_price_partition,2,$usd_to_uah,$eur_to_uah,$cash_id);
			$price_partition=$this->getSummCash($price_partition,2,$usd_to_uah,$eur_to_uah,$cash_id);
			$price_buh_uah=$this->getSummCash($price_buh_uah,1,$usd_to_uah,$eur_to_uah,$cash_id);
			$price_man_uah=$this->getSummCash($price_man_uah,1,$usd_to_uah,$eur_to_uah,$cash_id);
			$price_invoice=$this->getSummCash($price_invoice,2,$usd_to_uah,$eur_to_uah,$cash_id);
			
			$backs[$doc_type_id]["oper_price_partition"]+=$oper_price_partition*$partition_amount;
			$backs[$doc_type_id]["price_partition"]+=$price_partition*$partition_amount;
			$backs[$doc_type_id]["price_buh_uah"]+=$price_buh_uah*$partition_amount;
			$backs[$doc_type_id]["price_man_uah"]+=$price_man_uah*$partition_amount;
			$backs[$doc_type_id]["price_invoice"]+=$price_invoice*$partition_amount;	
		}
																									   
		foreach($backs as $sls){
			$doc_type_id=$sls["doc_type_id"]; $doc_type_name=$gmanual->get_gmanual_caption($doc_type_id);
			$oper_price_partition=$sls["oper_price_partition"];
			$price_partition=$sls["price_partition"];
			$price_buh_uah=$sls["price_buh_uah"];
			$price_man_uah=$sls["price_man_uah"];
			$price_invoice=$sls["price_invoice"];
			$list.="<tr style='background:lightblue'>
				<td>$doc_type_name</td>
				<td>-$oper_price_partition</td>
				<td>-$price_partition</td>
				<td>-$price_buh_uah</td>
				<td>-$price_man_uah</td>
				<td>-$price_invoice</td>
			</tr>";
			if ($client_status==1){
				$list.=$this->getClientMarginReport(1,$doc_status,$doc_type_id,$date_start,$date_end,$cash_id);
			}
			if ($doc_status==1){
				$list.=$this->getClientDocsMarginReport(1,$doc_type_id,$date_start,$date_end,$cash_id);
			}
			$summ_list-=$price_invoice;
		}
																									  
		unset($sales);$sales = array(); 
		unset($backs);$backs = array(); 
		$cash_abr=$this->getCashAbr($cash_id);
		$form=str_replace("{report_margin_range}",$list,$form);
		$form=str_replace("{summ_reports}",$summ_list,$form);
		$form=str_replace("{cash_abr}",$cash_abr,$form);
		return $form;
	}
	
	function getClientMarginReport($type_id,$doc_status,$doc_type_id,$date_start,$date_end,$cash_id) {$db=new db; $clients=new clients; $gmanual=new gmanual;
		$where="";$sales=$backs=[];
		if ($type_id==0) {
	    $r=$db->query("select j.*, jsp.invoice_amount, jsp.oper_price_partition, jsp.price_partition, jsp.price_buh_uah, jsp.price_man_uah,jsp.price_invoice from J_SALE_INVOICE j
		inner join J_SALE_INVOICE_PARTITION_STR jsp on jsp.invoice_id=j.id
		where j.time_stamp>='$date_start 00:00:00' and j.time_stamp<='$date_end 23:59:59' and j.doc_type_id='$doc_type_id';"); $n=$db->num_rows($r); $list=""; 
		for ($i=1;$i<=$n;$i++){
			$id=$db->result($r,$i-1,"j.id");
			$doc_type_id=$db->result($r,$i-1,"doc_type_id");
			$usd_to_uah=$db->result($r,$i-1,"usd_to_uah");
			$eur_to_uah=$db->result($r,$i-1,"eur_to_uah");
			$client_id=$db->result($r,$i-1,"client_conto_id");
			$oper_price_partition=$db->result($r,$i-1,"oper_price_partition");
			$price_partition=$db->result($r,$i-1,"price_partition");
			$price_buh_uah=$db->result($r,$i-1,"price_buh_uah");
			$price_man_uah=$db->result($r,$i-1,"price_man_uah");
			$price_invoice=$db->result($r,$i-1,"price_invoice");
			$partition_amount=$db->result($r,$i-1,"invoice_amount");
			
			$oper_price_partition=$this->getSummCash($oper_price_partition,2,$usd_to_uah,$eur_to_uah,$cash_id);
			$price_partition=$this->getSummCash($price_partition,2,$usd_to_uah,$eur_to_uah,$cash_id);
			$price_buh_uah=$this->getSummCash($price_buh_uah,1,$usd_to_uah,$eur_to_uah,$cash_id);
			$price_man_uah=$this->getSummCash($price_man_uah,1,$usd_to_uah,$eur_to_uah,$cash_id);
			$price_invoice=$this->getSummCash($price_invoice,2,$usd_to_uah,$eur_to_uah,$cash_id);
			
			$salesc[$client_id]["client_id"]=$client_id;
			$salesc[$client_id]["oper_price_partition"]+=$oper_price_partition*$partition_amount;
			$salesc[$client_id]["price_partition"]+=$price_partition*$partition_amount;
			$salesc[$client_id]["price_buh_uah"]+=$price_buh_uah*$partition_amount;
			$salesc[$client_id]["price_man_uah"]+=$price_man_uah*$partition_amount;
			$salesc[$client_id]["price_invoice"]+=$price_invoice*$partition_amount;
		}
			
		foreach($salesc as $sls){
			$client_id=$sls["client_id"]; $client_name=$clients->getClientNameById($client_id,"name");
			$oper_price_partition=$sls["oper_price_partition"];
			$price_partition=$sls["price_partition"];
			$price_buh_uah=$sls["price_buh_uah"];
			$price_man_uah=$sls["price_man_uah"];
			$price_invoice=$sls["price_invoice"];
			$list.="<tr style='background:#F9FFD8;'>
				<td align='right'>$client_name</td>
				<td>$oper_price_partition</td>
				<td>$price_partition</td>
				<td>$price_buh_uah</td>
				<td>$price_man_uah</td>
				<td>$price_invoice</td>
			</tr>";
			if ($doc_status==1){
				$list.=$this->getClientDocsMarginReport(0,$client_id,$doc_type_id,$date_start,$date_end,$cash_id);
			}
		}
			
		} else {																					
		$r=$db->query("select j.*, jsp.partition_amount, jsp.oper_price_partition, jsp.price_partition, jsp.price_buh_uah, jsp.price_man_uah,jsp.price_invoice from J_BACK_CLIENTS j
		inner join J_BACK_CLIENTS_PARTITION_STR jsp on jsp.back_id=j.id
		where j.time_stamp>='$date_start 00:00:00' and j.time_stamp<='$date_end 23:59:59' and j.doc_type_id='$doc_type_id';"); $n=$db->num_rows($r); 
		for ($i=1;$i<=$n;$i++){
			$id=$db->result($r,$i-1,"j.id");
			$doc_type_id=$db->result($r,$i-1,"doc_type_id");
			$usd_to_uah=$db->result($r,$i-1,"usd_to_uah");
			$eur_to_uah=$db->result($r,$i-1,"eur_to_uah");
			$client_id=$db->result($r,$i-1,"client_id");
			$oper_price_partition=$db->result($r,$i-1,"oper_price_partition");
			$price_partition=$db->result($r,$i-1,"price_partition");
			$price_buh_uah=$db->result($r,$i-1,"price_buh_uah");
			$price_man_uah=$db->result($r,$i-1,"price_man_uah");
			$price_invoice=$db->result($r,$i-1,"price_invoice");
			$partition_amount=$db->result($r,$i-1,"partition_amount");
			
			$oper_price_partition=$this->getSummCash($oper_price_partition,2,$usd_to_uah,$eur_to_uah,$cash_id);
			$price_partition=$this->getSummCash($price_partition,2,$usd_to_uah,$eur_to_uah,$cash_id);
			$price_buh_uah=$this->getSummCash($price_buh_uah,1,$usd_to_uah,$eur_to_uah,$cash_id);
			$price_man_uah=$this->getSummCash($price_man_uah,1,$usd_to_uah,$eur_to_uah,$cash_id);
			$price_invoice=$this->getSummCash($price_invoice,2,$usd_to_uah,$eur_to_uah,$cash_id);
			
			$backsc[$client_id]["client_id"]=$client_id;
			$backsc[$client_id]["oper_price_partition"]+=$oper_price_partition*$partition_amount;
			$backsc[$client_id]["price_partition"]+=$price_partition*$partition_amount;
			$backsc[$client_id]["price_buh_uah"]+=$price_buh_uah*$partition_amount;
			$backsc[$client_id]["price_man_uah"]+=$price_man_uah*$partition_amount;
			$backsc[$client_id]["price_invoice"]+=$price_invoice*$partition_amount;
		}
			
		foreach($backsc as $sls){
			$client_id=$sls["client_id"]; $client_name=$clients->getClientNameById($client_id,"name");
			$oper_price_partition=$sls["oper_price_partition"];
			$price_partition=$sls["price_partition"];
			$price_buh_uah=$sls["price_buh_uah"];
			$price_man_uah=$sls["price_man_uah"];
			$price_invoice=$sls["price_invoice"];
			$list.="<tr style='background:#F9FFD8;'>
				<td align='right'>$client_name</td>
				<td>-$oper_price_partition</td>
				<td>-$price_partition</td>
				<td>-$price_buh_uah</td>
				<td>-$price_man_uah</td>
				<td>-$price_invoice</td>
			</tr>";
			if ($doc_status==1){
				$list.=$this->getClientDocsMarginReport(1,$client_id,$doc_type_id,$date_start,$date_end,$cash_id);
			}
		}
		}
		unset($salesc);$salesc = array();
		unset($backsc);$backsc = array();
		return $list;
	}
	
	function getClientDocsMarginReport($type_id,$client_id,$doc_type_id,$date_start,$date_end,$cash_id){	$db=new db; $slave=new slave; 
		$where="";$sales=$backs=[]; 
		if ($type_id==0) {
		if ($client_id!=0) $where=" and j.client_conto_id='$client_id' "; else $where="";
	    $r=$db->query("select j.*, jsp.invoice_amount, jsp.oper_price_partition, jsp.price_partition, jsp.price_buh_uah, jsp.price_man_uah,jsp.price_invoice from J_SALE_INVOICE j
		inner join J_SALE_INVOICE_PARTITION_STR jsp on jsp.invoice_id=j.id
		where j.time_stamp>='$date_start 00:00:00' and j.time_stamp<='$date_end 23:59:59' $where and j.doc_type_id='$doc_type_id';"); $n=$db->num_rows($r); $list=""; 
		for ($i=1;$i<=$n;$i++){
			$id=$db->result($r,$i-1,"j.id");
			$prefix=$db->result($r,$i-1,"prefix"); $doc_nom=$db->result($r,$i-1,"doc_nom"); $prefix=$prefix."-".$doc_nom;
			$data_pay=$db->result($r,$i-1,"data_create");
			$usd_to_uah=$db->result($r,$i-1,"usd_to_uah");
			$eur_to_uah=$db->result($r,$i-1,"eur_to_uah");
			$oper_price_partition=$db->result($r,$i-1,"oper_price_partition");
			$price_partition=$db->result($r,$i-1,"price_partition");
			$price_buh_uah=$db->result($r,$i-1,"price_buh_uah");
			$price_man_uah=$db->result($r,$i-1,"price_man_uah");
			$price_invoice=$db->result($r,$i-1,"price_invoice");
			$partition_amount=$db->result($r,$i-1,"invoice_amount");
			
			$oper_price_partition=$this->getSummCash($oper_price_partition,2,$usd_to_uah,$eur_to_uah,$cash_id);
			$price_partition=$this->getSummCash($price_partition,2,$usd_to_uah,$eur_to_uah,$cash_id);
			$price_buh_uah=$this->getSummCash($price_buh_uah,1,$usd_to_uah,$eur_to_uah,$cash_id);
			$price_man_uah=$this->getSummCash($price_man_uah,1,$usd_to_uah,$eur_to_uah,$cash_id);
			$price_invoice=$this->getSummCash($price_invoice,2,$usd_to_uah,$eur_to_uah,$cash_id);			
			
			$salesd[$id]["id"]=$id;
			$salesd[$id]["doc_name"]=$prefix." â³ä ".$data_pay;
			$salesd[$id]["oper_price_partition"]+=$oper_price_partition*$partition_amount;
			$salesd[$id]["price_partition"]+=$price_partition*$partition_amount;
			$salesd[$id]["price_buh_uah"]+=$price_buh_uah*$partition_amount;
			$salesd[$id]["price_man_uah"]+=$price_man_uah*$partition_amount;
			$salesd[$id]["price_invoice"]+=$price_invoice*$partition_amount;
		}
			
		foreach($salesd as $sls){
			$id=$sls["id"]; $doc_name=$sls["doc_name"];
			$oper_price_partition=$sls["oper_price_partition"];
			$price_partition=$sls["price_partition"];
			$price_buh_uah=$sls["price_buh_uah"];
			$price_man_uah=$sls["price_man_uah"];
			$price_invoice=$sls["price_invoice"];
			$list.="<tr style='background:#fff;'>
				<td align='right'>$doc_name</td>
				<td>$oper_price_partition</td>
				<td>$price_partition</td>
				<td>$price_buh_uah</td>
				<td>$price_man_uah</td>
				<td>$price_invoice</td>
			</tr>";
		}
		} else {
		if ($client_id!=0) $where=" and j.client_id='$client_id' "; else $where="";
		$r=$db->query("select j.*, jsp.partition_amount, jsp.oper_price_partition, jsp.price_partition, jsp.price_buh_uah, jsp.price_man_uah,jsp.price_invoice from J_BACK_CLIENTS j
		inner join J_BACK_CLIENTS_PARTITION_STR jsp on jsp.back_id=j.id
		where j.time_stamp>='$date_start 00:00:00' and j.time_stamp<='$date_end 23:59:59' $where and j.doc_type_id='$doc_type_id';"); $n=$db->num_rows($r); $list=""; 
		for ($i=1;$i<=$n;$i++){
			$id=$db->result($r,$i-1,"j.id");
			$prefix=$db->result($r,$i-1,"prefix"); $doc_nom=$db->result($r,$i-1,"doc_nom"); $prefix=$prefix."-".$doc_nom;
			$data_pay=$db->result($r,$i-1,"data_create");
			$usd_to_uah=$db->result($r,$i-1,"usd_to_uah");
			$eur_to_uah=$db->result($r,$i-1,"eur_to_uah");
			$oper_price_partition=$db->result($r,$i-1,"oper_price_partition");
			$price_partition=$db->result($r,$i-1,"price_partition");
			$price_buh_uah=$db->result($r,$i-1,"price_buh_uah");
			$price_man_uah=$db->result($r,$i-1,"price_man_uah");
			$price_invoice=$db->result($r,$i-1,"price_invoice");
			$partition_amount=$db->result($r,$i-1,"partition_amount");
			
			$oper_price_partition=$this->getSummCash($oper_price_partition,2,$usd_to_uah,$eur_to_uah,$cash_id);
			$price_partition=$this->getSummCash($price_partition,2,$usd_to_uah,$eur_to_uah,$cash_id);
			$price_buh_uah=$this->getSummCash($price_buh_uah,1,$usd_to_uah,$eur_to_uah,$cash_id);
			$price_man_uah=$this->getSummCash($price_man_uah,1,$usd_to_uah,$eur_to_uah,$cash_id);
			$price_invoice=$this->getSummCash($price_invoice,2,$usd_to_uah,$eur_to_uah,$cash_id);			
			
			$backsd[$id]["id"]=$id;
			$backsd[$id]["doc_name"]=$prefix." â³ä ".$data_pay;
			$backsd[$id]["oper_price_partition"]+=$oper_price_partition*$partition_amount;
			$backsd[$id]["price_partition"]+=$price_partition*$partition_amount;
			$backsd[$id]["price_buh_uah"]+=$price_buh_uah*$partition_amount;
			$backsd[$id]["price_man_uah"]+=$price_man_uah*$partition_amount;
			$backsd[$id]["price_invoice"]+=$price_invoice*$partition_amount;
		}
			
		foreach($backsd as $sls){
			$id=$sls["id"]; $doc_name=$sls["doc_name"];
			$oper_price_partition=$sls["oper_price_partition"];
			$price_partition=$sls["price_partition"];
			$price_buh_uah=$sls["price_buh_uah"];
			$price_man_uah=$sls["price_man_uah"];
			$price_invoice=$sls["price_invoice"];
			$list.="<tr style='background:#fff;'>
				<td align='right'>$doc_name</td>
				<td>-$oper_price_partition</td>
				<td>-$price_partition</td>
				<td>-$price_buh_uah</td>
				<td>-$price_man_uah</td>
				<td>-$price_invoice</td>
			</tr>";
		}
		}
		unset($salesd);$salesd = array();
		unset($backsd);$backsd = array();
		return $list;
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
	
	function getCashAbr($cash_id) {$db=new db; 
		$r=$db->query("select * from CASH where id='$cash_id' limit 1");
		$cash_abr=$db->result($r,0,"abr");
	    return $cash_abr;
	}
	
}