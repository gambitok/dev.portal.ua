<?php 

function exportDocs($client_id,$date_start,$date_end) {
	header('Content-Type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename=export_documents.csv'); ob_clean();
	$output = fopen('php://output', 'w');
	fputcsv($output, array("Вид документа","Номер нашего документа","Дата нашего документа","Номер расходной поставщика","Дата расходной поставщика","Номер налоговой","Номер корректировки","Номер налоговой которую корректируем","Дата налоговой","Назва повна контрагента","ІПН","ЄДРПОУ","К-сть, шт.","Собівартість СБ","Сумма с ПДВ"),$delimiter = ';');
	$income_array=exportIncome($client_id,$date_start,$date_end);
	$sale_array=exportSaleInvoice($client_id,$date_start,$date_end);
	$back_array=exportBackClients($client_id,$date_start,$date_end);
	foreach ($income_array as $fields) {
		fputcsv($output,$fields,$delimiter=';');
	}
	foreach ($sale_array as $fields) {
		fputcsv($output,$fields,$delimiter=';');
	}
	foreach ($back_array as $fields) {
		fputcsv($output,$fields,$delimiter=';');
	}
	exit(0);
}

function getBackClientsStrSumm($back_id) { $db=DbSingleton::getDb();
	$r=$db->query("SELECT `price_buh_uah`, `partition_amount` FROM `J_BACK_CLIENTS_PARTITION_STR` WHERE `back_id`='$back_id';"); $n=$db->num_rows($r); $summ=$col=0;
	for ($i=1;$i<=$n;$i++){
		$price=$db->result($r,$i-1,"price_buh_uah");
		$amount=$db->result($r,$i-1,"partition_amount");
		$col+=$amount;
		$summ+=($price*$amount);
	}
    return array($summ,$col);
}

function getTaxInvo($tax_id) { $db=DbSingleton::getDb();
	$r=$db->query("SELECT `doc_nom` FROM `J_TAX_INVOICE` WHERE `id`='$tax_id';");
	$tax_type_id=$db->result($r,0,"doc_nom");
	return $tax_type_id;
}

function getTaxInvoData($tax_id) { $db=DbSingleton::getDb();
	$r=$db->query("SELECT `data_create` FROM `J_TAX_INVOICE` WHERE `id`='$tax_id';");
	$tax_type_id=$db->result($r,0,"data_create");
	return $tax_type_id;
}

function exportBackClients($client_id,$date_start,$date_end) { $db=DbSingleton::getDb();$list=[];
	$where=" AND j.seller_id='$client_id' AND j.time_stamp>='$date_start 00:00:00' AND j.time_stamp<='$date_end 23:59:59' ";
	$r=$db->query("SELECT j.*, c.full_name as client_seller_name, cd.edrpou, cd.vytjag, tx.doc_nom as tax_doc, tx.tax_type_id, tx.tax_to_back_id
	FROM `J_BACK_CLIENTS` j
		LEFT OUTER JOIN `A_CLIENTS` c on c.id=j.client_id
		LEFT OUTER JOIN `A_CLIENT_DETAILS` cd on cd.client_id=j.client_id
		LEFT OUTER JOIN `J_TAX_INVOICE` tx on tx.back_id=j.id
	WHERE j.status=1 AND j.doc_type_id=61 AND tx.status=1 $where;"); $n=$db->num_rows($r);
	for ($i=1;$i<=$n;$i++){
		$id=$db->result($r,$i-1,"id");
		$prefix=$db->result($r,$i-1,"prefix"); $doc_nom=$db->result($r,$i-1,"doc_nom"); if ($doc_nom==0){$doc_nom="-";} $name="$prefix-$doc_nom";
		$data=$db->result($r,$i-1,"data");
		$client_seller_name=$db->result($r,$i-1,"client_seller_name");
		$client_seller_name=str_replace("&rsquo;",'"',$client_seller_name);
		$client_edrpou=$db->result($r,$i-1,"edrpou");
		$client_vat=$db->result($r,$i-1,"vytjag");
		$tax_doc=$db->result($r,$i-1,"tax_doc");
		$tax_type_id=$db->result($r,$i-1,"tax_type_id");
		$tax_to_back_id=$db->result($r,$i-1,"tax_to_back_id");
		$true_tax_to_back_id=$tax_to_back_id;
		$tax_to_back_id=getTaxInvo($tax_to_back_id);
	    $tax_data_create=getTaxInvoData($true_tax_to_back_id);
		$pre="НН"; if ($tax_type_id==161) $pre="КНН";
		$tax_doc=$pre."-".$tax_doc;
		$tax_to_back_id="НН-".$tax_to_back_id;
		$summ_pdv=$db->result($r,$i-1,"summ");
		list($summ,$amount)=getBackClientsStrSumm($id);
	    $summ=number_format($summ, 2, '.', '');
		$list[$i]=array("back_from_client",$name,$data,"","","",$tax_doc,$tax_to_back_id,$tax_data_create,$client_seller_name,$client_vat,$client_edrpou,$amount,$summ,$summ_pdv);
	}
	return $list;
}

function getSaleInvoiceStrSumm($invoice_id) { $db=DbSingleton::getDb();
	$r=$db->query("SELECT price_buh_uah, invoice_amount FROM `J_SALE_INVOICE_PARTITION_STR` WHERE `invoice_id`='$invoice_id';"); $n=$db->num_rows($r); $summ=$col=0;
	for ($i=1;$i<=$n;$i++){
		$price=$db->result($r,$i-1,"price_buh_uah");
		$amount=$db->result($r,$i-1,"invoice_amount");
		$col+=$amount;
		$summ+=($price*$amount);
	}
    return array($summ,$col);
}

function exportSaleInvoice($client_id,$date_start,$date_end) { $db=DbSingleton::getDb();
	$where=" AND j.seller_id='$client_id' AND j.data_create>='$date_start 00:00:00' AND j.data_create<='$date_end 23:59:59' ";
	$r=$db->query("SELECT j.*, c.full_name as client_seller_name, cd.edrpou, cd.vytjag, tx.doc_nom as tax_doc 
	FROM `J_SALE_INVOICE` j
		LEFT OUTER JOIN A_CLIENTS c on c.id=j.client_conto_id
		LEFT OUTER JOIN A_CLIENT_DETAILS cd on cd.client_id=j.client_conto_id
		LEFT OUTER JOIN J_TAX_INVOICE tx on tx.sale_invoice_id=j.id
	WHERE j.status=1 $where AND tx.back_id=0;");$n=$db->num_rows($r);$list=[];
	for ($i=1;$i<=$n;$i++){
		$id=$db->result($r,$i-1,"id");
		$prefix=$db->result($r,$i-1,"prefix"); $doc_nom=$db->result($r,$i-1,"doc_nom");if ($doc_nom==0){$doc_nom="-";} $name="$prefix-$doc_nom";
		$data=$db->result($r,$i-1,"data_create");
		$client_seller_name=$db->result($r,$i-1,"client_seller_name");
		$client_seller_name=str_replace("&rsquo;",'"',$client_seller_name);
		$client_edrpou=$db->result($r,$i-1,"edrpou");
		$client_vat=$db->result($r,$i-1,"vytjag");
		$tax_doc=$db->result($r,$i-1,"tax_doc");
		$summ_pdv=$db->result($r,$i-1,"summ");
		list($summ,$amount)=getSaleInvoiceStrSumm($id);
	    $summ=number_format($summ, 2, '.', '');
		$list[$i]=array('sale',$name,$data,"","",$tax_doc,"","","",$client_seller_name,$client_vat,$client_edrpou,$amount,$summ,$summ_pdv);
	}
	return $list;
}

function getIncomeStrSumm($income_id) { $db=DbSingleton::getDb();
    $summ=0; $col=0;
	$r=$db->query("SELECT price_buh_uah, amount FROM `J_INCOME_STR` WHERE `income_id`='$income_id';"); $n=$db->num_rows($r);
	for ($i=1;$i<=$n;$i++){
		$price=$db->result($r,$i-1,"price_buh_uah");
		$amount=$db->result($r,$i-1,"amount");
		$col+=$amount;
		$summ+=($price*$amount);
	}
    return array($summ,$col);
}

function exportIncome($client_id,$date_start,$date_end) { $db=DbSingleton::getDb();
	$where=" AND j.client_id='$client_id' AND j.data>='$date_start 00:00:00' AND j.data<='$date_end 23:59:59' ";$list=[];
	$r=$db->query("SELECT j.*, c.full_name as client_seller_name, cd.edrpou, cd.vytjag 
	FROM `J_INCOME` j
		LEFT OUTER JOIN `A_CLIENTS` c on c.id=j.client_seller
		LEFT OUTER JOIN `A_CLIENT_DETAILS` cd on cd.client_id=j.client_seller
	WHERE j.status=1 $where ;");$n=$db->num_rows($r);
	for ($i=1;$i<=$n;$i++){
		$id=$db->result($r,$i-1,"id");
		$prefix=$db->result($r,$i-1,"prefix"); $doc_nom=$db->result($r,$i-1,"doc_nom");if ($doc_nom==0){$doc_nom="-";} $name="$prefix-$doc_nom";
		$data=$db->result($r,$i-1,"data");
		$invoice_income=$db->result($r,$i-1,"invoice_income");
		$invoice_data=$db->result($r,$i-1,"invoice_data");
		$client_seller_name=$db->result($r,$i-1,"client_seller_name");
		$client_seller_name=str_replace("&rsquo;",'"',$client_seller_name);
		$client_edrpou=$db->result($r,$i-1,"edrpou");
		$client_vat=$db->result($r,$i-1,"vytjag");
		list($invoice_summ,$amount)=getIncomeStrSumm($id);
	    $invoice_summ=number_format($invoice_summ, 2, '.', '');
		$list[$i]=array('income',$name,$data,$invoice_income,$invoice_data,"","","","",$client_seller_name,$client_vat,$client_edrpou,$amount,$invoice_summ,'');
	}
	return $list;
}

function showSelectList() { $db=DbSingleton::getDb();
	$r=$db->query("SELECT c.id, c.full_name, cc.category_id 
	FROM `A_CLIENTS` c 
		LEFT OUTER JOIN `A_CLIENTS_CATEGORY` cc on cc.client_id=c.id
	WHERE cc.category_id=3 ORDER BY c.id ASC;");$n=$db->num_rows($r);$list="";
	for ($i=1;$i<=$n;$i++){
		$id=$db->result($r,$i-1,"id");
		$caption=$db->result($r,$i-1,"full_name");
		$list.="<option value='$id'>$id - $caption</option>";
	}
	return $list;
}

function exportDocsForm() {
	$form_htm=RD."/tpl/export_doc.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$form=str_replace("{date}",date("Y-m-d"),$form);
	$form=str_replace("{client_list}",showSelectList(),$form);
	return $form;
}

// export Cross -----------------------------------------------------------------------------------------------------------------------------------

function ExportCross($br) { $db=DbSingleton::getTokoDb();
	$br2="'".implode("','", $br)."'"; $list=[];
	$r=$db->query("
	SELECT b3.BRAND_NAME, tarez.ARTICLE_NR_DISPL, t2c.DISPLAY_NR, b2.BRAND_NAME
	FROM (
	SELECT t2a.ART_ID, t2a.ARTICLE_NR_DISPL, t2a.BRAND_ID
	FROM T2_ARTICLES t2a
	WHERE t2a.BRAND_ID
	IN (
	SELECT BRAND_ID
	FROM T2_BRANDS
	WHERE BRAND_NAME
	IN ($br2)
	)
	)tarez
	INNER JOIN T2_CROSS t2c ON t2c.ART_ID = tarez.ART_ID
	INNER JOIN T2_BRANDS b2 ON b2.BRAND_ID = t2c.BRAND_ID
	LEFT JOIN T2_BRANDS b3 ON b3.BRAND_ID = tarez.BRAND_ID
	");
	$n=$db->num_rows($r);
	for ($i=1;$i<=$n;$i++){
		$article=$db->result($r,$i-1,"tarez.ARTICLE_NR_DISPL");
		$name=$db->result($r,$i-1,"b3.BRAND_NAME");
		$display=$db->result($r,$i-1,"t2c.DISPLAY_NR");
		$crossbname=$db->result($r,$i-1,"b2.BRAND_NAME");
		$list[$i]=array($article,$name,$display,$crossbname);
	}
	return $list;
}

function showBrandsSelect() { $db=DbSingleton::getTokoDb();
	$r=$db->query("SELECT * FROM `T2_BRANDS` ORDER BY `BRAND_NAME` ASC;"); $n=$db->num_rows($r); $list="";
	for ($i=1;$i<=$n;$i++){
		$name=$db->result($r,$i-1,"BRAND_NAME");
		$list.="<option value=".$i.">".$name."</option>";
	}
	return $list;
}

