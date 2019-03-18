<?php
class tax_invoice{

function getMediaUserName($user_id){$db=DbSingleton::getDb();$name="";
	$r=$db->query("select name from media_users where id='$user_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){$name=$db->result($r,0,"name");}
	return $name;
}
function show_tax_invoice_list(){$db=DbSingleton::getDb();$slave=new slave;$gmanual=new gmanual;$dp=new dp; $where="";$limit ="limit 0,300"; if ($where!=""){$limit="";}
	$r=$db->query("select ti.*, si.prefix as si_prefix, si.doc_nom as si_nom, t.name as tpoint_name, sl.name as seller_name, cl.name as client_name, ch.abr2 as cash_abr from J_TAX_INVOICE ti
	left outer join J_SALE_INVOICE si on si.id=ti.sale_invoice_id
	left outer join CASH ch on ch.id=ti.cash_id
	left outer join T_POINT t on t.id=ti.tpoint_id
	left outer join A_CLIENTS sl on sl.id=ti.seller_id
	left outer join A_CLIENTS cl on cl.id=ti.client_id
	where ti.status=1 order by ti.status_tax asc, ti.data_create desc, si.id desc;");$n=$db->num_rows($r);$list="";
	for ($i=1;$i<=$n;$i++){
		$id=$db->result($r,$i-1,"id");
		$sale_invoice_id=$db->result($r,$i-1,"sale_invoice_id"); 
		$si_nom=$db->result($r,$i-1,"si_prefix")."-".$db->result($r,$i-1,"si_nom");
		$doc_nom=$db->result($r,$i-1,"doc_nom");
		$data_create=$db->result($r,$i-1,"data_create");
		$tax_type_id=$db->result($r,$i-1,"tax_type_id");
		$tpoint_id=$db->result($r,$i-1,"tpoint_id");
		$tpoint_name=$db->result($r,$i-1,"tpoint_name");
		$seller_id=$db->result($r,$i-1,"seller_id");
		$seller_name=$db->result($r,$i-1,"seller_name");
		$client_id=$db->result($r,$i-1,"client_id");
		$client_name=$db->result($r,$i-1,"client_name");
		$summ=$db->result($r,$i-1,"summ");
		$cash_id=$db->result($r,$i-1,"cash_id");
		$cash_abr=$db->result($r,$i-1,"cash_abr");
		$data_send=$db->result($r,$i-1,"data_send");
		$user_name=$this->getMediaUserName($db->result($r,$i-1,"user_id"));
		$status_tax=$db->result($r,$i-1,"status_tax");
		$status_tax_cap=$gmanual->get_gmanual_caption($status_tax);
		$function="showTaxInvoiceCard";$prefix="��"; 
		if ($tax_type_id==161){$function="showTaxInvoiceBackCard";$prefix="���";}
		
		$list.="<tr id='strStsRow_$i' style='cursor:pointer' align='center' onClick='$function(\"$id\");'>
			<td>$i</td>
			<td>$prefix-$doc_nom</td>
			<td align='center'>$data_create</td>
			<td>$si_nom</td>
			<td>$tpoint_name</td>
			<td align='left'>$seller_name</td>
			<td align='left'>$client_name</td>
			<td align='center' style='min-width:80px;'>$summ$cash_abr</td>
			<td align='right'>$data_send</td>
			<td align='left'>$user_name</td>
			<td align='center'>$status_tax_cap</td>
		</tr>";
	}
	return $list;
}

function getTpointName($id){$db=DbSingleton::getDb();$slave=new slave;$name=""; 
	$r=$db->query("select name from T_POINT where id='$id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){	$name=$db->result($r,0,"name");	}
	return $name;	
}
function getClientName($id){$db=DbSingleton::getDb();$slave=new slave;$name=""; 
	$r=$db->query("select name from A_CLIENTS where id='$id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){	$name=$db->result($r,0,"name");	}
	return $name;	
}
function getClientContoSelectList($client_id,$sel_id){$db=DbSingleton::getDb();$list="";
	if ($client_id>0){
		$r=$db->query("select id,name from `A_CLIENTS` where status='1' and (parrent_id='$client_id' or id='$client_id' or id='$sel_id') order by name,id asc;");$n=$db->num_rows($r);
		for ($i=1;$i<=$n;$i++){
			$id=$db->result($r,$i-1,"id");
			$name=$db->result($r,$i-1,"name");
			$sel="";if ($sel_id==$id || $client_id==$id){$sel="selected='selected'";}
			$list.="<option value='$id' $sel>$name</option>";
		}
	}
	return $list;	
}
function getSellerSelectList($sel_id){$db=DbSingleton::getDb();$list="";
	
	$r=$db->query("select c.id,c.name from `A_CLIENTS` c join A_CLIENTS_CATEGORY cc on cc.client_id=c.id where c.status='1' and cc.category_id='3' and c.org_type=2 order by c.id asc;");$n=$db->num_rows($r);
	for ($i=1;$i<=$n;$i++){
		$id=$db->result($r,$i-1,"id");
		$name=$db->result($r,$i-1,"name");
		$sel="";if ($sel_id==$id){$sel="selected='selected'";}
		$list.="<option value='$id' $sel>$name</option>";
	}
	return $list;	
}
function getTpointSelectList($sel_id){$db=DbSingleton::getDb();$list="";
	$r=$db->query("select id,name from `T_POINT` where status='1' order by position asc,id asc;");$n=$db->num_rows($r);
	for ($i=1;$i<=$n;$i++){
		$id=$db->result($r,$i-1,"id");
		$name=$db->result($r,$i-1,"name");
		$sel="";if ($sel_id==$id){$sel="selected='selected'";}
		$list.="<option value='$id' $sel>$name</option>";
	}
	return $list;	
}
	
function showTaxInvoiceCard($tax_id){$db=DbSingleton::getDb();$cat=new catalogue;$slave=new slave;$manual=new manual;$gmanual=new gmanual; session_start(); $user_id=$_SESSION["media_user_id"]; $user_name=$_SESSION["user_name"];  
	$form_htm=RD."/tpl/tax_invoice_card.htm";	if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$cash_id=1;$cash_name=$this->getCashName($cash_id); 
	$r=$db->query("select * from J_TAX_INVOICE where status=1 and id='$tax_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==0){$id=$tax_id;$summ=0;$doc_xml_nom="";$status_tax=155;$tax_type_id=160;$data_create=date("Y-m-d");$doc_nom=0;}
	if ($n==1){
		$id=$tax_id;
		$tax_type_id=$db->result($r,0,"tax_type_id");
		$doc_xml_nom=$db->result($r,0,"doc_xml_nom");
		$doc_nom=$db->result($r,0,"doc_nom");
		$tpoint_id=$db->result($r,0,"tpoint_id");
		$seller_id=$db->result($r,0,"seller_id");
		$client_id=$db->result($r,0,"client_id");$client_name=$this->getClientName($client_id);
		$client_conto_id=$db->result($r,0,"client_conto_id");$client_conto_name=$this->getClientName($client_conto_id);
		$data_create=$db->result($r,0,"data_create");
		$data_send=$db->result($r,0,"data_send");
		$cash_id=$db->result($r,0,"cash_id");$cash_name=$this->getCashName($cash_id); 
		$summ=$db->result($r,0,"summ");
		$user_id=$db->result($r,0,"user_id");$user_name=$this->getMediaUserName($user_id);
		$status_xml_tax=$db->result($r,0,"status_xml_tax");
		$status_tax=$db->result($r,0,"status_tax");
	}
	$form=str_replace("{tax_id}",$tax_id,$form);								 
	$form=str_replace("{user_id}",$user_id,$form);
	$form=str_replace("{cash_name}",$cash_name,$form);
	$form=str_replace("{cash_id}",$cash_id,$form);
	$form=str_replace("{doc_nom}",$doc_nom,$form);
	$form=str_replace("{client_id}",$client_id,$form);
	$form=str_replace("{client_name}",$client_name,$form);
	$form=str_replace("{seller_list}",$this->getSellerSelectList($seller_id),$form);
	$form=str_replace("{data_send}",$data_send,$form);
	$form=str_replace("{data_create}",$data_create,$form);
	$form=str_replace("{tax_type_list}",$gmanual->showGmanualSelectList("tax_type_id",$tax_type_id),$form);
	$form=str_replace("{tpoint_list}",$this->getTpointSelectList($tpoint_id),$form);
	$form=str_replace("{status_tax_list}",$gmanual->showGmanualSelectList("status_tax",$status_tax),$form);
	$form=str_replace("{doc_xml_nom}",$doc_xml_nom,$form);
	$form=str_replace("{tax_summ}",$summ,$form);
									 
	$tax_str_list="";
	list($tax_str_list,$kol_str_row)=$this->showTaxStrList($tax_id,$status_tax);
	$form=str_replace("{tax_invoice_range}",$tax_str_list,$form);
	$form=str_replace("{kol_str_row}",$kol_str_row,$form);
									 
	$form=str_replace("{disabled}","",$form);
									 
	return array($form,$doc_nom);
}

function showTaxInvoiceBackCard($tax_id){$db=DbSingleton::getDb();$cat=new catalogue;$slave=new slave;$manual=new manual;$gmanual=new gmanual; session_start(); $user_id=$_SESSION["media_user_id"]; $user_name=$_SESSION["user_name"];  
	$form_htm=RD."/tpl/tax_invoice_back_card.htm";	if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$cash_id=1;$cash_name=$this->getCashName($cash_id); 
	$r=$db->query("select * from J_TAX_INVOICE where status=1 and id='$tax_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==0){$id=$tax_id;$summ=0;$doc_xml_nom="";$status_tax=155;$tax_type_id=161;$data_create=date("Y-m-d");$doc_nom=0;}
	if ($n==1){
		$id=$tax_id;
		$tax_type_id=$db->result($r,0,"tax_type_id");
		$doc_xml_nom=$db->result($r,0,"doc_xml_nom");
		$doc_nom=$db->result($r,0,"doc_nom");
		$tax_to_back_id=$db->result($r,0,"tax_to_back_id");
		$sale_invoice_id=$db->result($r,0,"sale_invoice_id");
		$tpoint_id=$db->result($r,0,"tpoint_id");
		$seller_id=$db->result($r,0,"seller_id");
		$client_id=$db->result($r,0,"client_id");$client_name=$this->getClientName($client_id);
		$client_conto_id=$db->result($r,0,"client_conto_id");$client_conto_name=$this->getClientName($client_conto_id);
		$data_create=$db->result($r,0,"data_create");
		$data_send=$db->result($r,0,"data_send");
		$cash_id=$db->result($r,0,"cash_id");$cash_name=$this->getCashName($cash_id); 
		$summ=$db->result($r,0,"summ");
		$user_id=$db->result($r,0,"user_id");$user_name=$this->getMediaUserName($user_id);
		$status_xml_tax=$db->result($r,0,"status_xml_tax");
		$status_tax=$db->result($r,0,"status_tax");
		list($tb_doc_nom,$tb_seller_id,$tb_seller_name,$tb_tax_type_id,$tb_cash_id,$tb_cash_name,$tb_summ,$tb_data_create)=$this->getTaxInvoceHeader($tax_to_back_id);
		$tax_to_back_document="��-$tb_doc_nom $tb_data_create";
	}
	$form=str_replace("{tax_id}",$tax_id,$form);								 
	$form=str_replace("{user_id}",$user_id,$form);
	$form=str_replace("{cash_name}",$cash_name,$form);
	$form=str_replace("{cash_id}",$cash_id,$form);
	$form=str_replace("{tax_to_back_id}",$tax_to_back_id,$form);
	$form=str_replace("{tax_to_back_name}",$tax_to_back_document,$form);
	$form=str_replace("{sale_invoice_id}",$sale_invoice_id,$form);
	$form=str_replace("{doc_nom}",$doc_nom,$form);
	$form=str_replace("{client_id}",$client_id,$form);
	$form=str_replace("{client_name}",$client_name,$form);
	$form=str_replace("{seller_list}",$this->getSellerSelectList($seller_id),$form);
	$form=str_replace("{data_send}",$data_send,$form);
	$form=str_replace("{data_create}",$data_create,$form);
	$form=str_replace("{tax_type_list}",$gmanual->showGmanualSelectList("tax_type_id",$tax_type_id),$form);
	$form=str_replace("{tpoint_list}",$this->getTpointSelectList($tpoint_id),$form);
	$form=str_replace("{status_tax_list}",$gmanual->showGmanualSelectList("status_tax",$status_tax),$form);
	$form=str_replace("{doc_xml_nom}",$doc_xml_nom,$form);
	$form=str_replace("{tax_summ}",$summ,$form);
									 
	$tax_str_list="";
	list($tax_str_list,$kol_str_row)=$this->showTaxStrBackList($tax_id,$status_tax);
	$form=str_replace("{tax_invoice_range}",$tax_str_list,$form);
	$form=str_replace("{kol_str_row}",$kol_str_row,$form);
									 
	$form=str_replace("{disabled}","",$form);
									 
	return array($form,$doc_nom);
}

function getTaxInvoceHeader($tax_id){$db=DbSingleton::getDb();
	$r=$db->query("select * from J_TAX_INVOICE where status=1 and id='$tax_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){
		$tax_type_id=$db->result($r,0,"tax_type_id");
		$doc_nom=$db->result($r,0,"doc_nom");
		$seller_id=$db->result($r,0,"seller_id");$seller_name=$this->getClientName($seller_id);
		$data_create=$db->result($r,0,"data_create");
		$cash_id=$db->result($r,0,"cash_id");$cash_name=$this->getCashName($cash_id); 
		$summ=$db->result($r,0,"summ");
	}
	return array($doc_nom,$seller_id,$seller_name,$tax_type_id,$cash_id,$cash_name,$summ,$data_create);
}
	
function showTaxStrList($tax_id,$status_tax){$db=DbSingleton::getDb();$slave=new slave;$manual=new manual;$list="";$kol=0;
	if ($status_tax==155){$opr="";}else{$opr=" readonly disabled";}
	$r=$db->query("select * from J_TAX_INVOICE_STR where status=1 and tax_id='$tax_id' order by id asc;");$n=$db->num_rows($r);
	for ($i=1;$i<=$n;$i++){
		$str_id=$db->result($r,$i-1,"id");
		$zed=$db->result($r,$i-1,"zed");
		$goods_name=$db->result($r,$i-1,"goods_name");
		$amount=$db->result($r,$i-1,"amount");
		$price=$db->result($r,$i-1,"price");
		$summ=$db->result($r,$i-1,"summ");
		
		$list.="<tr id='strRow_$i'>
			<td>$i<input type='hidden' id='idStr_$i' value='$str_id'></td>
			<td><input class='form-control input-xs' type='text numberOnly' id='zedStr_$i' $opr value='$zed'></td>
			<td><input class='form-control input-xs' type='text' id='goods_nameStr_$i' $opr value='$goods_name'></td>
			<td><input class='form-control input-xs numberOnly' type='text' id='amountStr_$i' $opr value='$amount' autocomplete='off'></td>
			<td><input class='form-control input-xs numberOnly' type='text' id='priceStr_$i' $opr value='$price' autocomplete='off'></td>
			<td><input class='form-control input-xs numberOnly' type='text' id='summStr_$i' $opr value='$summ' autocomplete='off'></td>
			<td><button class='btn btn-xs dtn-default' id='dropStr_$i' $opr onClick='dropTaxStr(\"$i\",\"$tax_id\",\"$str_id\")'><i class='fa fa-times'></i></button></td>
		</tr>";
		
	}$kol=$n;
	if ($status_tax==155){
		$list="
			<tr id='taxStrNewRow' class='hidden'>
				<td>nom_i<input type='hidden' id='idStr_0' value=''></td>
				<td style='min-width:90px;'><input class='form-control input-xs numberOnly' type='text' id='zedStr_0' value='' placeholder='���'></td>
				<td style='min-width:160px;'><input class='form-control input-xs' type='text' id='goods_nameStr_0' value='' placeholder='�����'></td>
				<td><input type='text' id='amountStr_0' value='0' class='form-control input-xs numberOnly' autocomplete='off' maxlength=''  min='1' max=''></td>
				<td><input type='text' id='priceStr_0' value='0' class='form-control input-xs numberOnly' autocomplete='off'></td>
				<td><input type='text' id='summStr_0' value='0' class='form-control input-xs numberOnly' autocomplete='off'></td>
				<td><button class='btn btn-xs btn-default' onClick='dropTaxStr(\"i_0\",\"0\",\"0\");'><i class='fa fa-times'></i></button></td>
			</tr>".$list;
	}
											 
	return array($list,$kol);
}
function showTaxStrBackList($tax_id,$status_tax){$db=DbSingleton::getDb();$slave=new slave;$manual=new manual;$list="";$kol=0;
	if ($status_tax==155){$opr="";}else{$opr=" readonly disabled";}
	$r=$db->queryP("select * from J_TAX_INVOICE_STR where status=1 and tax_id='$tax_id' order by id asc;");$n=$db->num_rows($r);
	for ($i=1;$i<=$n;$i++){
		$str_id=$db->result($r,$i-1,"id");
		$tax_str_id=$db->result($r,$i-1,"tax_str_id");
		$tax_str_nom=$db->result($r,$i-1,"tax_str_nom");
		$zed=$db->result($r,$i-1,"zed");
		$goods_name=$db->result($r,$i-1,"goods_name");
		$amount=$db->result($r,$i-1,"amount");
		$price=$db->result($r,$i-1,"price");
		$summ=$db->result($r,$i-1,"summ");
		
		$list.="<tr id='strRow_$i'>
			<td>$i<input type='hidden' id='idStr_$i' value='$str_id'></td>
			<td>
				<input class='form-control input-xs' type='text numberOnly' id='nomStr_$i' $opr value='$tax_str_nom'>
				<input type='hidden' id='tax_str_idStr_$i' value='$tax_str_id'>
			</td>
			<td><input class='form-control input-xs' type='text numberOnly' id='zedStr_$i' $opr value='$zed'></td>
			<td><input class='form-control input-xs' type='text' id='goods_nameStr_$i' $opr value='$goods_name'></td>
			<td><input class='form-control input-xs numberOnly' type='text' id='amountStr_$i' $opr value='$amount' autocomplete='off'></td>
			<td><input class='form-control input-xs numberOnly' type='text' id='priceStr_$i' $opr value='$price' autocomplete='off'></td>
			<td><input class='form-control input-xs numberOnly' type='text' id='summStr_$i' $opr value='$summ' autocomplete='off'></td>
			<td><button class='btn btn-xs dtn-default' id='dropStr_$i' $opr onClick='dropTaxStr(\"$i\",\"$tax_id\",\"$str_id\")'><i class='fa fa-times'></i></button></td>
			<td><button class='btn btn-xs dtn-default' id='findStr_$i' $opr onClick='findTaxStr(\"$i\",\"$tax_id\",\"$str_id\",\"$tax_str_id\")'><i class='fa fa-search'></i></button></td>
		</tr>";
		
	}$kol=$n;
	if ($status_tax==155){
		$list="
			<tr id='taxStrNewRow' class='hidden'>
				<td>nom_i<input type='hidden' id='idStr_0' value=''></td>
				<td style='min-width:40px;'>
					<input class='form-control input-xs' type='text numberOnly' id='nomStr_0'  value='' placeholder='�����'>
					<input type='hidden' id='tax_str_idStr_0' value='0'>
				</td>
				<td style='min-width:90px;'><input class='form-control input-xs numberOnly' type='text' id='zedStr_0' value='' placeholder='���'></td>
				<td style='min-width:160px;'><input class='form-control input-xs' type='text' id='goods_nameStr_0' value='' placeholder='�����'></td>
				<td><input type='text' id='amountStr_0' value='0' class='form-control input-xs numberOnly' autocomplete='off' maxlength=''  min='1' max=''></td>
				<td><input type='text' id='priceStr_0' value='0' class='form-control input-xs numberOnly' autocomplete='off'></td>
				<td><input type='text' id='summStr_0' value='0' class='form-control input-xs numberOnly' autocomplete='off'></td>
				<td><button class='btn btn-xs btn-default' onClick='dropTaxStr(\"i_0\",\"0\",\"0\");'><i class='fa fa-times'></i></button></td>
				<td><button class='btn btn-xs dtn-default' id='findStr_0' onClick='findTaxStr(\"i_0\",\"$tax_id\",\"0\",\"0\")'><i class='fa fa-search'></i></button></td>
			</tr>".$list;
	}
											 
	return array($list,$kol);
}
	
	

	
	
function saveTaxCard($tax_id,$data_create,$data_send,$cash_id,$tax_summ,$tax_type_id,$tpoint_id,$seller_id,$client_id,$status_tax,$doc_xml_nom){$db=DbSingleton::getDb();$slave=new slave;session_start();$user_id=$_SESSION["media_user_id"];$user_name=$_SESSION["user_name"];$answer=0;$err="������� ���������� �����!";
	$tax_id=$slave->qq($tax_id);$data_create=$slave->qq($data_create);$data_send=$slave->qq($data_send);$cash_id=$slave->qq($cash_id);$tax_summ=$slave->qq($tax_summ);$tax_type_id=$slave->qq($tax_type_id);$tpoint_id=$slave->qq($tpoint_id);$client_id=$slave->qq($client_id);$seller_id=$slave->qq($seller_id);$status_tax=$slave->qq($status_tax);$doc_xml_nom=$slave->qq($doc_xml_nom);
	if ($tax_id==0 || $tax_id==""){
		$r=$db->query("select max(id) as mid from J_TAX_INVOICE;");$tax_id=$db->result($r,0,"mid")+1;
		$r=$db->query("select max(doc_nom) as doc_nom from J_TAX_INVOICE where seller_id='$seller_id' and tax_type_id=160;");$doc_nom=$db->result($r,0,"doc_nom")+1;
		$db->query("insert into J_TAX_INVOICE (id,doc_nom,status) values ('$tax_id','$doc_nom','1');");
	}
	if ($tax_id>0){
		//$this->check_doc_prefix_nom($income_id,$client_id);
		$db->query("update J_TAX_INVOICE set `tax_type_id`='$tax_type_id', `tpoint_id`='$tpoint_id', `client_id`='$client_id', `seller_id`='$seller_id', `data_create`='$data_create', `data_send`='$data_send', `cash_id`='$cash_id', `summ`='$tax_summ', `status_tax`='$status_tax', `doc_xml_nom`='$doc_xml_nom',user_id='$user_id' where `id`='$tax_id';");		
		$answer=1;$err="";
	}
	return array($answer,$err,$tax_id);
}

function saveTaxCardData($tax_id,$frm,$tto,$idStr,$zedStr,$goods_nameStr,$amountStr,$priceStr,$summStr){$db=DbSingleton::getDb();$slave=new slave;session_start();$user_id=$_SESSION["media_user_id"];$user_name=$_SESSION["user_name"];$answer=0;$err="������� ���������� �����!";
	$tax_id=$slave->qq($tax_id);$frm=$slave->qq($frm);$tto=$slave->qq($tto);
	if ($tax_id>0){
		
		$idStr=$slave->qq($idStr);$zedStr=$slave->qq($zedStr);$goods_nameStr=$slave->qq($goods_nameStr); $amountStr=$slave->qq($amountStr);$priceStr=$slave->qq($priceStr);$summStr=$slave->qq($summStr);
		for($i=$frm;$i<=$tto;$i++){
			$idS=$idStr[$i]; $zedS=$zedStr[$i]; $goods_nameS=$goods_nameStr[$i]; $amountS=$amountStr[$i]; $priceS=$priceStr[$i]; $summS=$summStr[$i]; if ($zedS=="undefined"){$zedS="";}
		
			if ($goods_nameS!="" && $goods_nameS!="undefined"){
				if ($idS=="" || $idS==0){
					$r=$db->queryP("select max(id) as mid from J_TAX_INVOICE_STR;");$idS=0+$db->result($r,0,"mid")+1;
					$db->query("insert into J_TAX_INVOICE_STR (`id`,`tax_id`) values ('$idS','$tax_id');");
				}
				if ($idS>0){
					$db->queryP("update J_TAX_INVOICE_STR set `zed`='$zedS', `goods_name`='$goods_nameS', `amount`='$amountS', `price`='$priceS', `summ`='$summS' where id='$idS' and tax_id='$tax_id';");
				}
			}
		}
		$answer=1;$err="";
	}
	return array($answer,$err);
}

function saveTaxBackCard($tax_id,$tax_to_back_id,$data_create,$data_send,$cash_id,$tax_summ,$tax_type_id,$tpoint_id,$seller_id,$client_id,$status_tax,$doc_xml_nom){$db=DbSingleton::getDb();$slave=new slave;session_start();$user_id=$_SESSION["media_user_id"];$user_name=$_SESSION["user_name"];$answer=0;$err="������� ���������� �����!";
	$tax_id=$slave->qq($tax_id);$tax_to_back_id=$slave->qq($tax_to_back_id);$data_create=$slave->qq($data_create);$data_send=$slave->qq($data_send);$cash_id=$slave->qq($cash_id);$tax_summ=$slave->qq($tax_summ);$tax_type_id=$slave->qq($tax_type_id);$tpoint_id=$slave->qq($tpoint_id);$client_id=$slave->qq($client_id);$seller_id=$slave->qq($seller_id);$status_tax=$slave->qq($status_tax);$doc_xml_nom=$slave->qq($doc_xml_nom);
	if ($tax_id==0 || $tax_id==""){
		$r=$db->query("select max(id) as mid from J_TAX_INVOICE;");$tax_id=$db->result($r,0,"mid")+1;
		$r=$db->query("select max(doc_nom) as doc_nom from J_TAX_INVOICE where seller_id='$seller_id' and tax_type_id=161;");$doc_nom=$db->result($r,0,"doc_nom")+1;
		$db->query("insert into J_TAX_INVOICE (id,doc_nom,status) values ('$tax_id','$doc_nom','1');");
	}
	if ($tax_id>0){
		//$this->check_doc_prefix_nom($income_id,$client_id);
		$db->query("update J_TAX_INVOICE set `tax_to_back_id`='$tax_to_back_id', `tax_type_id`='$tax_type_id', `tpoint_id`='$tpoint_id', `client_id`='$client_id', `seller_id`='$seller_id', `data_create`='$data_create', `data_send`='$data_send', `cash_id`='$cash_id', `summ`='$tax_summ', `status_tax`='$status_tax', `doc_xml_nom`='$doc_xml_nom',user_id='$user_id' where `id`='$tax_id';");		
		$answer=1;$err="";
	}
	return array($answer,$err,$tax_id);
}

function saveTaxBackCardData($tax_id,$frm,$tto,$idStr,$tsidStr,$nomStr,$zedStr,$goods_nameStr,$amountStr,$priceStr,$summStr){$db=DbSingleton::getDb();$slave=new slave;session_start();$user_id=$_SESSION["media_user_id"];$user_name=$_SESSION["user_name"];$answer=0;$err="������� ���������� �����!";
	$tax_id=$slave->qq($tax_id);$frm=$slave->qq($frm);$tto=$slave->qq($tto);
	if ($tax_id>0){
		
		$idStr=$slave->qq($idStr);$tsidStr=$slave->qq($tsidStr);$nomStr=$slave->qq($nomStr);$zedStr=$slave->qq($zedStr);$goods_nameStr=$slave->qq($goods_nameStr); $amountStr=$slave->qq($amountStr);$priceStr=$slave->qq($priceStr);$summStr=$slave->qq($summStr);
		for($i=$frm;$i<=$tto;$i++){
			$idS=$idStr[$i]; $tsidS=$tsidStr[$i];  $nomS=$nomStr[$i]; $zedS=$zedStr[$i]; $goods_nameS=$goods_nameStr[$i]; $amountS=$amountStr[$i]; $priceS=$priceStr[$i]; $summS=$summStr[$i]; if ($zedS=="undefined"){$zedS="";}
		
			if ($goods_nameS!="" && $goods_nameS!="undefined"){
				if ($idS=="" || $idS==0){
					$r=$db->queryP("select max(id) as mid from J_TAX_INVOICE_STR;");$idS=0+$db->result($r,0,"mid")+1;
					$db->query("insert into J_TAX_INVOICE_STR (`id`,`tax_id`) values ('$idS','$tax_id');");
				}
				if ($idS>0){
					$db->queryP("update J_TAX_INVOICE_STR set `zed`='$zedS',`tax_str_nom`='$nomS',`tax_str_id`='$tsidS', `goods_name`='$goods_nameS', `amount`='$amountS', `price`='$priceS', `summ`='$summS' where id='$idS' and tax_id='$tax_id';");
				}
			}
		}
		$answer=1;$err="";
	}
	return array($answer,$err);
}
	
function exportTaxInvoiceXML($tax_id){$db=DbSingleton::getDb();$cat=new catalogue;$slave=new slave;$manual=new manual;session_start();$user_id=$_SESSION["media_user_id"];$user_name=$_SESSION["user_name"]; $gmanual=new gmanual; $invoice_summ=0;
											 
	$r=$db->query("select ti.*, sl.full_name as seller_name, sld.edrpou as seller_edrpou, sld.vytjag as seller_vytjag, sld.buh_name, sld.buh_edrpou, cl.full_name as client_name, cld.edrpou as client_edrpou, cld.vytjag as client_vytjag, ch.abr2 as cash_abr from J_TAX_INVOICE ti
					left outer join CASH ch on ch.id=ti.cash_id
					left outer join A_CLIENTS sl on sl.id=ti.seller_id
					left outer join A_CLIENT_DETAILS sld on sld.client_id=ti.seller_id
					left outer join A_CLIENTS cl on cl.id=ti.client_id
					left outer join A_CLIENT_DETAILS cld on cld.client_id=ti.client_id
					where ti.status=1 and ti.id='$tax_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){
		$tax_type_id=$db->result($r,0,"tax_type_id");
		$doc_xml_nom=$db->result($r,0,"doc_xml_nom");
		$doc_nom=$db->result($r,0,"doc_nom");
		$data_create=$db->result($r,0,"data_create");
		$seller_name=$db->result($r,0,"seller_name");
		$seller_edrpou=$db->result($r,0,"seller_edrpou");
		$seller_vytjag=$db->result($r,0,"seller_vytjag");
		$buh_name=$db->result($r,0,"buh_name");
		$buh_edrpou=$db->result($r,0,"buh_edrpou");
		$client_name=$db->result($r,0,"client_name");
		$client_edrpou=$db->result($r,0,"client_edrpou");
		$client_vytjag=$db->result($r,0,"client_vytjag");
		$summ=$db->result($r,0,"summ");
		$cash_id=$db->result($r,0,"cash_id");
		$cash_abr=$db->result($r,0,"cash_abr");
		$user_name=$this->getMediaUserName($db->result($r,0,"user_id"));
	
		$list="";
		$r=$db->query("select * from J_TAX_INVOICE_STR where tax_id='$tax_id' order by id asc;");$n=$db->num_rows($r);
		for ($i=1;$i<=$n;$i++){
			$id=$db->result($r,$i-1,"id");
			$zed=$db->result($r,$i-1,"zed");
			$goods_name=$db->result($r,$i-1,"goods_name");
			$amount=$db->result($r,$i-1,"amount");
			$price=$db->result($r,$i-1,"price");
			$summ=$db->result($r,$i-1,"summ");
			$invoice_summ+=$summ;
			$list.="<RXXXXG3S ROWNUM=\"$i\">$goods_name</RXXXXG3S>
					<RXXXXG32 ROWNUM=\"$i\">1</RXXXXG32>
					<RXXXXG33 ROWNUM=\"$i\"></RXXXXG33>
					<RXXXXG4 ROWNUM=\"$i\">$zed</RXXXXG4>
					<RXXXXG011 ROWNUM=\"$i\"></RXXXXG011>
					<RXXXXG4S ROWNUM=\"$i\">��</RXXXXG4S>
					<RXXXXG105_2S ROWNUM=\"$i\">2009</RXXXXG105_2S>
					<RXXXXG5 ROWNUM=\"$i\">$amount</RXXXXG5>
					<RXXXXG6 ROWNUM=\"$i\">".round($price/1.2,6)."</RXXXXG6>
					<RXXXXG008 ROWNUM=\"$i\">20</RXXXXG008>
					<RXXXXG009 ROWNUM=\"$i\" xsi:nil=\"true\" />
					<RXXXXG010 ROWNUM=\"$i\">".round($summ/1.2,2)."</RXXXXG010>
					<RXXXXG11_10 ROWNUM=\"$i\">".round((round($summ/1.2,2))*0.2,6)."</RXXXXG11_10>
					";
		}
		$doc_stan=1;$dfill=date("dmY",strtotime($data_create));
		$sum_vat=round($invoice_summ/6,2);$sum_non_vat=$invoice_summ-$sum_vat;
		$period_month=substr($data_create,5,2);$period_year=substr($data_create,0,4);
		
		//22250035541076J1201009100000032311120182225.xml	
		$filename="2225".$this->addZero($seller_edrpou,10)."J12"."010"."08"."1".$this->addZero($doc_nom,7)."1"."$period_month"."$period_year"."2225.xml";
		header("Content-Type:text/xml;charset=windows-1251");
		header("Content-Disposition:attachment;filename=$filename");
		
		ob_clean();
		
		$form="<?xml version=\"1.0\" encoding=\"WINDOWS-1251\"?>
		<DECLAR xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:noNamespaceSchemaLocation=\"J1201010.XSD\">
		<DECLARHEAD>
		<TIN>$seller_edrpou</TIN>
		<C_DOC>J12</C_DOC>
		<C_DOC_SUB>010</C_DOC_SUB>
		<C_DOC_VER>10</C_DOC_VER>
		<C_DOC_TYPE>0</C_DOC_TYPE>
		<C_DOC_CNT>$doc_nom</C_DOC_CNT>
		<C_REG>22</C_REG>
		<C_RAJ>25</C_RAJ>
		<PERIOD_MONTH>$period_month</PERIOD_MONTH>
		<PERIOD_TYPE>1</PERIOD_TYPE>
		<PERIOD_YEAR>$period_year</PERIOD_YEAR>
		<C_STI_ORIG>2225</C_STI_ORIG>
		<C_DOC_STAN>$doc_stan</C_DOC_STAN>
		<LINKED_DOCS xsi:nil=\"true\" />
		<D_FILL>$dfill</D_FILL>
		</DECLARHEAD>
		<DECLARBODY>
		<H03 xsi:nil=\"true\" />
		<R03G10S xsi:nil=\"true\" />
		<HORIG1 xsi:nil=\"true\" />
		<HTYPR xsi:nil=\"true\" />
		<HFILL>$dfill</HFILL>
		<HNUM>$doc_nom</HNUM>
		<HNUM1 xsi:nil=\"true\" />
		<HNAMESEL>$seller_name</HNAMESEL>
		<HNAMEBUY>$client_name</HNAMEBUY>
		<HTINSEL>$seller_edrpou</HTINSEL>
		<HTINBUY>$client_edrpou</HTINBUY>
		<HKSEL>$seller_vytjag</HKSEL>
		<HKBUY>$client_vytjag</HKBUY>
		<R04G11>$invoice_summ</R04G11>
		<R03G11>".round($invoice_summ/6,2)."</R03G11>
		<R03G7>".round($invoice_summ/6,2)."</R03G7>
		<R03G109 xsi:nil=\"true\" />
		<R01G7>$sum_non_vat</R01G7>
		<R01G109 xsi:nil=\"true\" />
		<R01G9 xsi:nil=\"true\" />
		<R01G8 xsi:nil=\"true\" />
		<R01G10 xsi:nil=\"true\" />
		<R02G11 xsi:nil=\"true\"/>$list
		<HBOS>$buh_name</HBOS>
		<HKBOS>$buh_edrpou</HKBOS>
		<R003G10S xsi:nil=\"true\" />
		</DECLARBODY>
		</DECLAR>";
		$output=fopen('php://output','w');
		fputs($output, $form);
		exit(0);
	}
	return;
}
function exportTaxBackInvoiceXML($tax_id){$db=DbSingleton::getDb();$cat=new catalogue;$slave=new slave;$manual=new manual;session_start();$user_id=$_SESSION["media_user_id"];$user_name=$_SESSION["user_name"]; $gmanual=new gmanual; $invoice_summ=0;
											 
	$r=$db->query("select ti.*, sl.full_name as seller_name, sld.edrpou as seller_edrpou, sld.vytjag as seller_vytjag, sld.buh_name, sld.buh_edrpou, cl.full_name as client_name, cld.edrpou as client_edrpou, cld.vytjag as client_vytjag, ch.abr2 as cash_abr from J_TAX_INVOICE ti
					left outer join CASH ch on ch.id=ti.cash_id
					left outer join A_CLIENTS sl on sl.id=ti.seller_id
					left outer join A_CLIENT_DETAILS sld on sld.client_id=ti.seller_id
					left outer join A_CLIENTS cl on cl.id=ti.client_id
					left outer join A_CLIENT_DETAILS cld on cld.client_id=ti.client_id
					where ti.status=1 and ti.id='$tax_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){
		$tax_type_id=$db->result($r,0,"tax_type_id");
		$tax_to_back_id=$db->result($r,0,"tax_to_back_id");
		$doc_xml_nom=$db->result($r,0,"doc_xml_nom");
		$doc_nom=$db->result($r,0,"doc_nom");
		$data_create=$db->result($r,0,"data_create");
		$seller_name=$db->result($r,0,"seller_name");
		$seller_edrpou=$db->result($r,0,"seller_edrpou");
		$seller_vytjag=$db->result($r,0,"seller_vytjag");
		$buh_name=$db->result($r,0,"buh_name");
		$buh_edrpou=$db->result($r,0,"buh_edrpou");
		$client_name=$db->result($r,0,"client_name");
		$client_edrpou=$db->result($r,0,"client_edrpou");
		$client_vytjag=$db->result($r,0,"client_vytjag");
		$summ=$db->result($r,0,"summ");
		$cash_id=$db->result($r,0,"cash_id");
		$cash_abr=$db->result($r,0,"cash_abr");
		$user_name=$this->getMediaUserName($db->result($r,0,"user_id"));
		list($tb_doc_nom,$tb_seller_id,$tb_seller_name,$tb_tax_type_id,$tb_cash_id,$tb_cash_name,$tb_summ,$tb_data_create)=$this->getTaxInvoceHeader($tax_to_back_id);
	
		$list="";
		$r=$db->query("select * from J_TAX_INVOICE_STR where tax_id='$tax_id' order by id asc;");$n=$db->num_rows($r);
		for ($i=1;$i<=$n;$i++){
			$id=$db->result($r,$i-1,"id");
			$nom=$db->result($r,$i-1,"tax_str_nom");
			$zed=$db->result($r,$i-1,"zed");
			$goods_name=$db->result($r,$i-1,"goods_name");
			$amount=$db->result($r,$i-1,"amount");
			$price=$db->result($r,$i-1,"price");
			$summ=$db->result($r,$i-1,"summ");
			$invoice_summ+=$summ;
			$list.="
					<RXXXXG001 ROWNUM=\"1\">$nom</RXXXXG001>
					<RXXXXG2S ROWNUM=\"1\">���������� ������</RXXXXG2S>
					<RXXXXG3S ROWNUM=\"$i\">$goods_name</RXXXXG3S>
					<RXXXXG21 ROWNUM=\"$i\">103</RXXXXG21>
					<RXXXXG22 ROWNUM=\"$i\">1</RXXXXG22>
					<RXXXXG32 ROWNUM=\"$i\">1</RXXXXG32>
					<RXXXXG33 ROWNUM=\"$i\"></RXXXXG33>
					<RXXXXG4 ROWNUM=\"$i\">$zed</RXXXXG4>
					<RXXXXG011 ROWNUM=\"$i\"></RXXXXG011>
					<RXXXXG4S ROWNUM=\"$i\">��</RXXXXG4S>
					<RXXXXG105_2S ROWNUM=\"$i\">2009</RXXXXG105_2S>
					<RXXXXG5 ROWNUM=\"$i\">$amount</RXXXXG5>
					<RXXXXG6 ROWNUM=\"$i\">".round($price/1.2,6)."</RXXXXG6>
					<RXXXXG008 ROWNUM=\"$i\">20</RXXXXG008>
					<RXXXXG010 ROWNUM=\"$i\">".round($summ/1.2,2)."</RXXXXG010>
					<RXXXXG11_10 ROWNUM=\"$i\">".round($summ*0.2,3)."</RXXXXG11_10>";
		}
		$doc_stan=1;$dfill=date("dmY",strtotime($data_create));
		$sum_vat=round($invoice_summ*0.2);$sum_non_vat=$invoice_summ-$sum_vat;
		$period_month=substr($data_create,5,2);$period_year=substr($data_create,0,4);
		
		//22250035541076J1201009100000032311120182225.xml	
		$filename="2225".$this->addZero($seller_edrpou,10)."J12"."010"."08"."1".$this->addZero($doc_nom,7)."1"."$period_month"."$period_year"."2225.xml";
		header("Content-Type:text/xml;charset=windows-1251");
		header("Content-Disposition:attachment;filename=$filename");
		
		ob_clean();
		
		$form="<?xml version=\"1.0\" encoding=\"WINDOWS-1251\"?>
		<DECLAR xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:noNamespaceSchemaLocation=\"J1201009.XSD\">
		<DECLARHEAD>
		<TIN>$seller_edrpou</TIN>
		<C_DOC>J12</C_DOC>
		<C_DOC_SUB>012</C_DOC_SUB>
		<C_DOC_VER>8</C_DOC_VER>
		<C_DOC_TYPE>0</C_DOC_TYPE>
		<C_DOC_CNT>$doc_nom</C_DOC_CNT>
		<C_REG>25</C_REG>
		<C_RAJ>22</C_RAJ>
		<PERIOD_MONTH>$period_month</PERIOD_MONTH>
		<PERIOD_TYPE>1</PERIOD_TYPE>
		<PERIOD_YEAR>$period_year</PERIOD_YEAR>
		<C_STI_ORIG>2225</C_STI_ORIG>
		<C_DOC_STAN>$doc_stan</C_DOC_STAN>
		<LINKED_DOCS xsi:nil=\"true\" />
		<D_FILL>$dfill</D_FILL>
		</DECLARHEAD>
		<DECLARBODY>
		<HERPN>1</HERPN>
		<H03 xsi:nil=\"true\" />
		<R03G10S xsi:nil=\"true\" />
		<HORIG1 xsi:nil=\"true\" />
		<HTYPR xsi:nil=\"true\" />
		<HFILL>$dfill</HFILL>
		<HNUM>$doc_nom</HNUM>
		<HNUM1 xsi:nil=\"true\" />
		<HPODFILL>$tb_data_create</HPODFILL>
		<HPODNUM>$tb_doc_nom</HPODNUM>
		<HNAMESEL>$seller_name</HNAMESEL>
		<HNAMEBUY>$client_name</HNAMEBUY>
		<HTINSEL>$seller_edrpou</HTINSEL>
		<HTINBUY>$client_edrpou</HTINBUY>
		<HKSEL>$seller_vytjag</HKSEL>
		<HKBUY>$client_vytjag</HKBUY>
		<R03G109 xsi:nil=\"true\" />
		<R01G109 xsi:nil=\"true\" />
		<R01G8 xsi:nil=\"true\" />
		<R01G10 xsi:nil=\"true\" />
		<R02G11 xsi:nil=\"true\"/>$list
		<R01G9>".round(($invoice_summ-($invoice_summ/6)),2)."</R01G9>
		<R02G9>".round($invoice_summ/6,2)."</R02G9>
		<R001G03>".round($invoice_summ/6,2)."</R001G03> 
		<R001G3>".round($invoice_summ/6,2)."</R001G3>
		<HBOS>$buh_name</HBOS>
		<HKBOS>$buh_edrpou</HKBOS>
		<R003G10S xsi:nil=\"true\" />
		</DECLARBODY>
		</DECLAR>";

		$output=fopen('php://output','w');
		fputs($output, $form);
		
//		<R04G11>$invoice_summ</R04G11>
//		<R03G11>".round($invoice_summ/6,2)."</R03G11>
//		<R03G7>".round($invoice_summ/6,2)."</R03G7>
//		<R01G7>$sum_non_vat</R01G7>
		
		exit(0);
	}
	return;
}
function addZero($str,$num_str){
	$str_len=strlen($str);$toAdd=$num_str-$str_len;
	for ($i=1;$i<=$toAdd;$i++){$str="0".$str;}
	return $str;
}
function getCashAbr($cash_id){$db=DbSingleton::getDb();$name="";
	$r=$db->query("select abr from CASH where id ='$cash_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){$name=$db->result($r,0,"abr");}
	return $name;
}
function getCashName($cash_id){$db=DbSingleton::getDb();$name="";
	$r=$db->query("select name from CASH where id ='$cash_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){$name=$db->result($r,0,"name");}
	return $name;
}
function dropTaxStr($tax_id,$tax_str_id){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$slave=new slave;$answer=0;$err="������� �������";
	$tax_id=$slave->qq($tax_id);
	$r=$db->query("select status_tax,status from J_TAX_INVOICE where id='$tax_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){
		$status=$db->result($r,0,"status");
		$status_tax=$db->result($r,0,"status_tax");
		if ($status_tax==155 && $status==1){
			$r1=$db->query("select * from J_TAX_INVOICE_STR where id='$tax_str_id' and status=1 limit 0,1;");$n1=$db->num_rows($r1);
			if ($n1==1){
				$db->query("delete from J_TAX_INVOICE_STR where id='$tax_str_id' and tax_id='$tax_id' limit 1;");
				$answer=1;$err="";
//				$tax_summ=$this->updateTaxSumm($tax_id);
			}
		}else {$answer=0;$err="��������� �����������. �������� �������� � �ϲ.";}
	}
	return array($answer,$err,$tax_summ);
}



function loadTaxInvoiceCDN($tax_id){$db=DbSingleton::getDb();$slave=new slave;
	$form_htm=RD."/tpl/tax_invoice_cdn_block.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$r=$db->query("select cc.*,u.name as user_name from MONEY_SPEND_CDN cc 
		left outer join media_users u on u.id=cc.USER_ID 
		where cc.tax_id='$tax_id' and cc.status='1' order by cc.file_name asc;");$n=$db->num_rows($r);$list="";
		for ($i=1;$i<=$n;$i++){
			$file_id=$db->result($r,$i-1,"id");
			$user_id=$db->result($r,$i-1,"user_id");
			$file_key=$db->result($r,$i-1,"file_key");
			$file_name=$db->result($r,$i-1,"file_name");
			$name=$db->result($r,$i-1,"name");
			$data=$db->result($r,$i-1,"data");
			$comment=$db->result($r,$i-1,"comment");
			$user_name=$db->result($r,$i-1,"user_name");
			
			$link="http://portal.myparts.pro/cdn/tax_invoice_files/$tax_id/$file_name";
			
			$file_view="<div class=\"icon\"><i class=\"fa fa-file\"></i></div>";
			$exten=pathinfo($file_name, PATHINFO_EXTENSION);
			if ($exten=="jpg" || $exten=="jpeg" || $exten=="png" || $exten=="gif" || $exten=="bmp" || $exten=="svg"){
	            $file_view="<div class=\"image\"><img alt=\"image\" class=\"img-responsive\" src=\"$link\"></div>";
			}
			
			$block=$form;
			$block=str_replace("{file_id}",$file_id,$block);
			$block=str_replace("{file_name}",$name,$block);
			$block=str_replace("{user_name}",$user_name,$block);
			$block=str_replace("{data}",$data,$block);
			$block=str_replace("{tax_id}",$tax_id,$block);
			$block=str_replace("{link}",$link,$block);
			$block=str_replace("{file_view}",$file_view,$block);
			
			$list.=$block;
			
		}
		if ($n==0){$list="<h3 class='text-center'>����� ������</h3>";}
		return $list;
}

function findTaxStr($tax_id,$tax_to_back_id){$db=DbSingleton::getDb();$slave=new slave;$gmanual=new gmanual;
	$form_htm=RD."/tpl/tax_back_article_tree.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$r=$db->queryP("select * from J_TAX_INVOICE_STR where status=1 and tax_id='$tax_to_back_id' order by id asc;");$n=$db->num_rows($r);$list="";
	for ($i=1;$i<=$n;$i++){
		$id=$db->result($r,$i-1,"id");
		$zed=$db->result($r,$i-1,"zed");
		$tax_str_id=$db->result($r,$i-1,"tax_str_id");
		$tax_str_nom=$db->result($r,$i-1,"tax_str_nom");
		$art_id=$db->result($r,$i-1,"art_id");
		$goods_name=$db->result($r,$i-1,"goods_name");
		$amount=$db->result($r,$i-1,"amount");
		$price=$db->result($r,$i-1,"price");
		$summ=$db->result($r,$i-1,"summ");
		
		$cur="";$fn=" onClick='setTaxBackArticle(\"$id\", \"$i\", \"$zed\", \"".base64_encode(iconv("windows-1251","utf-8","$goods_name"))."\",\"$amount\",\"$price\",\"$summ\")'";
		if ($id==$prnt_id){$cur="background-color:#FFFF00;";}if ($id==$sel_id){$cur="background-color:#0CF;";}
		$list.="<tr style='$cur cursor:pointer;' $fn>
			<td>$i</td>
			<td align='center'>$zed</td>
			<td>$goods_name</td>
			<td>$amount</td>
			<td align='right'>$price</td>
			<td align='right'>$summ</td>
		</tr>";
		
	}
	$form=str_replace("{list}",$list,$form);
	return $form;
}
	
function showTaxSelectList($sel_id){$db=DbSingleton::getDb();$slave=new slave;$gmanual=new gmanual;
	$form_htm=RD."/tpl/tax_select_tree.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$r=$db->query("select ti.*, si.prefix as si_prefix, si.doc_nom as si_nom, t.name as tpoint_name, sl.name as seller_name, cl.name as client_name, ch.abr2 as cash_abr from J_TAX_INVOICE ti
	left outer join J_SALE_INVOICE si on si.id=ti.sale_invoice_id
	left outer join CASH ch on ch.id=ti.cash_id
	left outer join T_POINT t on t.id=ti.tpoint_id
	left outer join A_CLIENTS sl on sl.id=ti.seller_id
	left outer join A_CLIENTS cl on cl.id=ti.client_id
	where ti.status=1 and ti.tax_type_id=160 order by ti.status_tax asc, ti.data_create desc, si.id desc limit 0,100;");$n=$db->num_rows($r);$list="";
	for ($i=1;$i<=$n;$i++){
		$id=$db->result($r,$i-1,"id");
		$sale_invoice_id=$db->result($r,$i-1,"sale_invoice_id"); 
		$si_nom=$db->result($r,$i-1,"si_prefix").$db->result($r,$i-1,"si_nom");
		$doc_nom=$db->result($r,$i-1,"doc_nom");
		$data_create=$db->result($r,$i-1,"data_create");
		$tpoint_id=$db->result($r,$i-1,"tpoint_id");
		$tpoint_name=$db->result($r,$i-1,"tpoint_name");
		$seller_id=$db->result($r,$i-1,"seller_id");
		$seller_name=$db->result($r,$i-1,"seller_name");
		$client_id=$db->result($r,$i-1,"client_id");
		$client_name=$db->result($r,$i-1,"client_name");
		$summ=$db->result($r,$i-1,"summ");
		$cash_id=$db->result($r,$i-1,"cash_id");
		$cash_abr=$db->result($r,$i-1,"cash_abr");
		$data_send=$db->result($r,$i-1,"data_send");
		$user_name=$this->getMediaUserName($db->result($r,$i-1,"user_id"));
		$cur="";$fn=" onClick='setTaxBackSelect(\"$id\", \"".base64_encode(iconv("windows-1251","utf-8","��-$doc_nom $data_create"))."\")'";
		if ($id==$prnt_id){$cur="background-color:#FFFF00;";}if ($id==$sel_id){$cur="background-color:#0CF;";}
		$list.="<tr style='$cur cursor:pointer;' $fn>
			<td>$i</td>
			<td>$doc_nom</td>
			<td align='center'>$data_create</td>
			<td>$si_nom</td>
			<td>$tpoint_name</td>
			<td align='left'>$seller_name</td>
			<td align='left'>$client_name</td>
			<td align='center' style='min-width:80px;'>$summ$cash_abr</td>
			<td align='left'>$user_name</td>
		</tr>";
		
	}
	$form=str_replace("{list}",$list,$form);
	return $form;
}

function unlinkTaxBack($tax_id){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="������� ���������� �����!";
	$tax_id=$slave->qq($tax_id);
	if ($tax_id>0){
		$db->query("update J_TAX_INVOICE set `client_id`='0' where `id`='$tax_id';");
		$answer=1;$err="";
	}
	return array($answer,$err);
}
function loadTaxBackSellerClient($tax_id){$db=DbSingleton::getDb(); $answer=0;$err="�������";
	$r=$db->query("select * from J_TAX_INVOICE where status=1 and id='$tax_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){
		$client_id=$db->result($r,0,"client_id");$client_name=$this->getClientName($client_id);
		$seller_id=$db->result($r,0,"seller_id");$seller_name=$this->getClientName($seller_id);
		$answer=1;$err="";
	}
	return array($answer,$err,$client_id,$client_name,$seller_id,$seller_name,);
}	
	
}
?>