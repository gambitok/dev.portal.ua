<?php
class suppl_orders{

protected $prefix_new = '��';

function getMediaUserName($user_id){$db=DbSingleton::getDb();$name="";
	$r=$db->query("select name from media_users where id='$user_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){$name=$db->result($r,0,"name");}
	return $name;
}
function show_suppl_orders_list(){$db=DbSingleton::getDb();$slave=new slave;$gmanual=new gmanual;$dp=new dp; $where="";$limit ="limit 0,300"; if ($where!=""){$limit="";}
	$r=$db->query("select j.*, CASH.name as cash_name, (jd.prefix +' '+ jd.doc_nom) as dp_name,tp.name as tpoint_name, c.name as suppl_name, cs.name as suppl_storage_name, mu.name as user_name
	from J_DP_SUPPL_ORDER j
	left outer join J_DP jd on jd.id=j.dp_id
	left outer join CASH on CASH.id=j.cash_id
	left outer join T_POINT tp on tp.id=j.tpoint_id
	left outer join A_CLIENTS c on c.id=j.suppl_id
	left outer join A_CLIENTS_STORAGE cs on cs.id=j.suppl_storage_id
	left outer join media_users mu on mu.id=j.media_user_id
	where j.status=1 $where order by j.id desc $limit;");$n=$db->num_rows($r);$list="";
	for ($i=1;$i<=$n;$i++){
		$id=$db->result($r,$i-1,"id");
		$dp_id=$db->result($r,$i-1,"dp_id");
		$dp_name=$db->result($r,$i-1,"dp_name");
		
		$dp_str_id=$db->result($r,$i-1,"dp_str_id");
		$datatime=$db->result($r,$i-1,"datatime");
		$art_id=$db->result($r,$i-1,"art_id");
		$article_nr_displ=$db->result($r,$i-1,"article_nr_displ");
		$suppl_art_id=$db->result($r,$i-1,"SUPPL_ART_ID");
		$brand_name=$db->result($r,$i-1,"brand_name");
		$amount=$db->result($r,$i-1,"amount");
		$price=$db->result($r,$i-1,"price");
		$summ=round($amount*$price,2);
		$cash_id=$db->result($r,$i-1,"cash_id");
		$cash_name=$db->result($r,$i-1,"cash_name");
		$suppl_id=$db->result($r,$i-1,"suppl_id");
		$suppl_name=$db->result($r,$i-1,"suppl_name");
		$suppl_storage_id=$db->result($r,$i-1,"suppl_storage_id");
		$suppl_storage_name=$db->result($r,$i-1,"suppl_storage_name");
		$tpoint_id=$db->result($r,$i-1,"tpoint_id");
		$tpoint_name=$db->result($r,$i-1,"tpoint_name");
		$delivery_data=$db->result($r,$i-1,"delivery_data");
		$delivery_time=$db->result($r,$i-1,"delivery_time");
		$delivery_type_id=$db->result($r,$i-1,"delivery_type_id");
		$suppl_order_status_id=$db->result($r,$i-1,"suppl_order_status_id");
		$suppl_order_doc=$db->result($r,$i-1,"suppl_order_doc");
		$user_name=$db->result($r,$i-1,"user_name");
		
		
		$function="showSupplOrder(\"$id\")";
		$list.="<tr style='cursor:pointer' onClick='$function'>
				<td align='center'>$datatime</td>
				<td align='center' data-dpid='$dp_id'>$dp_name</td>
				<td>$article_nr_displ</td>
				<td>$brand_name</td>
				<td>$amount</td>
				<td>$price</td>
				<td>$cash_name</td>
				<td>$suppl_name</td>
				<td>$suppl_storage_name</td>
				<td>$amount</td>
				<td>$tpoint_name</td>
				<td>$delivery_time</td>
				<td>$delivery_type_id</td>
				<td>$suppl_order_status_id</td>
				<td>$suppl_order_doc</td>
				<td>$user_name</td>
			</tr>";
	}
	return $list;
}

function getKoursData($data){$db=DbSingleton::getDb();$slave=new slave;$usd_to_uah=0;$eur_to_uah=0;$where="data='$data'";if ($data==""){$data=date("Y-m-d");$where="";}
	$r=$db->query("select kours_value from J_KOURS where cash_id='2' and in_use='1' order by id desc limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){$usd_to_uah=$slave->to_money(round($db->result($r,0,"kours_value"),2));}
	$r=$db->query("select kours_value from J_KOURS where cash_id='3' and in_use='1' order by id desc limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){$eur_to_uah=$slave->to_money(round($db->result($r,0,"kours_value"),2));}
	return array($usd_to_uah,$eur_to_uah);
}


function showSupplOrder($so_id){$db=DbSingleton::getDb();$cat=new catalogue;$slave=new slave;$manual=new manual;session_start();$user_id=$_SESSION["media_user_id"];$user_name=$_SESSION["user_name"]; $storsel=new storsel; $gmanual=new gmanual; 
	$form_htm=RD."/tpl/suppl_orders_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$disabled="disabled"; 
								
	$r=$db->query("select j.*, CASH.name as cash_name, CONCAT(jd.prefix ,'-', jd.doc_nom,' �� ', jd.time_stamp) as dp_name, tp.name as tpoint_name, c.name as suppl_name, cs.name as suppl_storage_name, mu.name as user_name, si.info as suppl_info, cl.name as client_name
	from J_DP_SUPPL_ORDER j
	left outer join J_DP jd on jd.id=j.dp_id
	left outer join CASH on CASH.id=j.cash_id
	left outer join T_POINT tp on tp.id=j.tpoint_id
	left outer join A_CLIENTS cl on cl.id=jd.client_conto_id
	left outer join A_CLIENTS c on c.id=j.suppl_id
	left outer join A_CLIENTS_STORAGE cs on cs.id=j.suppl_storage_id
	left outer join A_CLIENTS_SUPPL_INFO si on si.client_id=c.id
	left outer join media_users mu on mu.id=j.media_user_id
	where j.status=1 and j.id='$so_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){
		$dp_id=$db->result($r,0,"dp_id");
		$dp_name=$db->result($r,0,"dp_name");
		$datatime=$db->result($r,0,"datatime");
		$art_id=$db->result($r,0,"art_id");
		$article_nr_displ=$db->result($r,0,"article_nr_displ");
		$suppl_art_id=$db->result($r,0,"SUPPL_ART_ID");
		$brand_name=$db->result($r,0,"brand_name");
		$amount=$db->result($r,0,"amount");
		$amount_order=$db->result($r,0,"amount_order"); if ($amount_order==0 && $suppl_order_status_id==103){$amount_order=$amount;}
		$price=$db->result($r,0,"price");
		$summ=round($amount*$price,2);
		$cash_id=$db->result($r,0,"cash_id");
		$cash_name=$db->result($r,0,"cash_name");
		
		$client_name=$db->result($r,0,"client_name");
		$suppl_id=$db->result($r,0,"suppl_id");
		$suppl_name=$db->result($r,0,"suppl_name");
		$suppl_info=$db->result($r,0,"suppl_info");
		$suppl_storage_id=$db->result($r,0,"suppl_storage_id");
		$suppl_storage_name=$db->result($r,0,"suppl_storage_name");
		$tpoint_id=$db->result($r,0,"tpoint_id");
		$tpoint_name=$db->result($r,0,"tpoint_name");
		$delivery_data=$db->result($r,0,"delivery_data"); if ($delivery_data=="0000-00-00"){$delivery_data="";}
		$delivery_time=substr($db->result($r,0,"delivery_time"),0,5);
		$delivery_data_finish=$db->result($r,0,"delivery_data_finish");if ($delivery_data_finish=="0000-00-00"){$delivery_data_finish="";}
		$delivery_time_finish=substr($db->result($r,0,"delivery_time_finish"),0,5);
		$delivery_type_id=$db->result($r,0,"delivery_type_id");
		$suppl_order_status_id=$db->result($r,0,"suppl_order_status_id");
		$suppl_order_doc=$db->result($r,0,"suppl_order_doc");
		$user_name=$db->result($r,0,"user_name");
		
		
		$form=str_replace("{so_id}",$so_id,$form);
		$form=str_replace("{suppl_name}",$suppl_name,$form);
		$form=str_replace("{suppl_storage_name}",$suppl_storage_name,$form);
		$form=str_replace("{article_nr_displ}",$article_nr_displ,$form);
		$form=str_replace("{brand_name}",$brand_name,$form);
		$form=str_replace("{article_name}",$cat->getArticleName($art_id),$form);
		$form=str_replace("{suppl_info}",$suppl_info,$form);
		$form=str_replace("{suppl_art_id}",$suppl_art_id,$form);
		$form=str_replace("{amount}",$amount,$form);
		$form=str_replace("{amount_order}",$amount_order,$form);
		$form=str_replace("{delivery_data}",$delivery_data,$form);
		$form=str_replace("{delivery_time}",$delivery_time,$form);
		$form=str_replace("{delivery_data_finish}",$delivery_data_finish,$form);
		$form=str_replace("{delivery_time_finish}",$delivery_time_finish,$form);
		$form=str_replace("{suppl_order_doc}",$suppl_order_doc,$form);
		$form=str_replace("{dp_name}",$dp_name,$form);
		$form=str_replace("{tpoint_name}",$tpoint_name,$form);
		$form=str_replace("{dp_client}",$client_name,$form);
		
		$form=str_replace("{delivery_type_list}",$gmanual->showGmanualSelectList('delivery_type',$delivery_type_id),$form);
		$form=str_replace("{suppl_order_status_list}",$gmanual->showGmanualSelectList('suppl_order_status_id',$suppl_order_status_id),$form);
		$form=str_replace("{cash_list}",$this->showCashListSelect($cash_id),$form);
		
		
	}
	if ($n==0){$form_htm=RD."/tpl/access_deny.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);} }
	
	
	
	return $form;
}
function getClientName($id){$db=DbSingleton::getDb();$slave=new slave;$name=""; 
	$r=$db->query("select name from A_CLIENTS where id='$id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){	$name=$db->result($r,0,"name");	}
	return $name;	
}

function showCashListSelect($sel_id,$ns){$db=DbSingleton::getDb();$list="";if ($ns==""){$ns=1;}
	$r=$db->query("select * from CASH order by name asc;");$n=$db->num_rows($r);$list="";
	for ($i=1;$i<=$n;$i++){
		$id=$db->result($r,$i-1,"id");
		$name=$db->result($r,$i-1,"abr");
		if ($ns==2){ $name=$db->result($r,$i-1,"name");}
		$sel="";if ($sel_id==$id){$sel="selected='selected'";}
		$list.="<option value='$id' $sel>$name</option>";
	}
	return $list;	
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

function saveSupplOrder($so_id,$amount_order,$delivery_data_finish,$delivery_time_finish,$delivery_type_id,$suppl_order_status_id,$suppl_order_doc){$db=DbSingleton::getDb();$slave=new slave;session_start();$user_id=$_SESSION["media_user_id"];$user_name=$_SESSION["user_name"];$answer=0;$err="������� ���������� �����!";
	$so_id=$slave->qq($so_id);
	if ($so_id>0){
		$amount_order=$slave->qq($amount_order);$delivery_data_finish=$slave->qq($delivery_data_finish);$delivery_time_finish=$slave->qq($delivery_time_finish);$delivery_type_id=$slave->qq($delivery_type_id);$suppl_order_status_id=$slave->qq($suppl_order_status_id);$suppl_order_doc=$slave->qq($suppl_order_doc);
		
		$db->query("update J_DP_SUPPL_ORDER set amount_order='$amount_order', delivery_data_finish='$delivery_data_finish', delivery_time_finish='$delivery_time_finish', delivery_type_id='$delivery_type_id', suppl_order_status_id='$suppl_order_status_id', suppl_order_doc='$suppl_order_doc'  where id='$so_id';");
		$answer=1;$err="";
	}
	return array($answer,$err);
}

}
?>