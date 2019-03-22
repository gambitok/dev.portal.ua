<?php
class buh_back{

protected $prefix_new = 'В';

function getMediaUserName($user_id){$db=DbSingleton::getDb();$name="";
	$r=$db->query("select name from media_users where id='$user_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){$name=$db->result($r,0,"name");}
	return $name;
}
function get_doc_prefix($client_id,$prefix_id){ $db=DbSingleton::getDb();$prefix="Дф-";
	$r=$db->query("select prefix from A_CLIENTS_DOCUMENT_PREFIX where client_id='$client_id' and id='$prefix_id' and status='1' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){$prefix=$db->result($r,0,"prefix");}
	return $prefix; 
}
function get_back_clients_prefix($back_id){ $db=DbSingleton::getDb();$prefix="ПР";
	$r=$db->query("select type_id from J_BACK_CLIENTS where id='$back_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){$type_id=$db->result($r,0,"type_id"); if ($type_id==0){$prefix="В-ПР";}}
	return $prefix; 
}
function get_doc_client_prefix($client_id){ $db=DbSingleton::getDb();$prefix_id=0;$doc_type_id=40;
	$r=$db->query("select id from A_CLIENTS_DOCUMENT_PREFIX where client_id='$client_id' and doc_type_id='$doc_type_id' and status='1' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){$prefix_id=$db->result($r,0,"id");}
	return $prefix_id; 
}
function get_df_doc_nom_new(){ $db=DbSingleton::getDb();$doc_nom=0;
	$r=$db->query("select max(doc_nom) as doc_nom from J_BACK_CLIENTS where oper_status='30' and status='1' limit 0,1;");$doc_nom=0+$db->result($r,0,"doc_nom")+1;
	return $doc_nom;
}
function getBackClientsName($back_id){$db=DbSingleton::getDb();
	$r=$db->query("select * from J_BACK_CLIENTS where id='$back_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){
		$prefix=$db->result($r,0,"prefix");
		$doc_nom=$db->result($r,0,"doc_nom");
	}
	return $prefix."-".$doc_nom;
}

function getBackClientsClientName($back_id){$db=DbSingleton::getDb();$client_name="";
	$r=$db->query("select client_id from J_BACK_CLIENTS where id='$back_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){
		$client_id=$db->result($r,0,"client_id");$client_name=$this->getClientName($client_id);
	}
	return $client_name;
}
function getTpointAddress($tpoint_id){$db=DbSingleton::getDb();$address="";
	$r=$db->query("select full_name, address from T_POINT where id='$tpoint_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){ $address=$db->result($r,0,"full_name")." ".$db->result($r,0,"address"); }
	return $address;
}
function getTpointLocalStorage($tpoint_id){$db=DbSingleton::getDb();$storage_id=0;
	$r=$db->query("select `storage_id` from T_POINT_STORAGE where tpoint_id='$tpoint_id' and `default`='1' and status='1' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){$storage_id=$db->result($r,0,"storage_id");}
	if ($n==0){
		$r=$db->query("select `storage_id` from T_POINT_STORAGE where tpoint_id='$tpoint_id' and local='41' and status='1' limit 0,1;");$n=$db->num_rows($r);
		if ($n==1){$storage_id=$db->result($r,0,"storage_id");}
	}
	return $storage_id;
}
function newBackClientsCard(){$db=DbSingleton::getDb();$slave=new slave;$manual=new manual; session_start();$user_id=$_SESSION["media_user_id"];$user_name=$_SESSION["user_name"]; $ses_tpoint_id=$_SESSION["media_tpoint_id"]; $back_id=0;$storage_id=$this->getTpointLocalStorage($ses_tpoint_id);
	$r=$db->query("select max(id) as mid from J_BACK_CLIENTS;");$back_id=0+$db->result($r,0,"mid")+1;
	$doc_nom=$this->get_df_doc_nom_new();
	$db->query("insert into J_BACK_CLIENTS (`id`,`prefix`,`doc_nom`,`user_id`,`data`,`tpoint_id`,`storage_id`) values ('$back_id','$this->prefix_new','$doc_nom','$user_id',CURDATE(),'$ses_tpoint_id','$storage_id');");
	return $back_id;
}
	
function show_back_clients_list(){$db=DbSingleton::getDb();$slave=new slave;$gmanual=new gmanual;$income=new income;  session_start(); $ses_tpoint_id=$_SESSION["media_tpoint_id"]; 	     //$where=" and j.tpoint_id='$ses_tpoint_id' and j.status_back!=0"; 
	$media_user_id=$_SESSION["media_user_id"]; if ($media_user_id==1 || $media_user_id==2 || $media_user_id==7){$where=" and j.status_back!=0";} 
	
	$data_cur=date("Y-m-d"); $data_old = date('Y-m-d', strtotime('-7 day', strtotime($data_cur)));							  
	$where_date=" and j.data>='$data_old 00:00:00' and j.data<='$data_cur 23:59:59'";						
								  
	$r=$db->query("select j.*, t.name as tpoint_name, CASH.name as cash_name, c.name as client_name, si.prefix as sale_prefix, si.doc_nom as sale_doc_nom from J_BACK_CLIENTS j
	left outer join T_POINT t on t.id=j.tpoint_id
	left outer join A_CLIENTS c on c.id=j.client_id
	left outer join CASH on CASH.id=j.cash_id
	left outer join J_SALE_INVOICE si on si.id=j.sale_invoice_id
	where j.status=1 $where_date and si.doc_type_id=61 order by j.id desc limit 0,500;");$n=$db->num_rows($r);$list="";
								  
	for ($i=1;$i<=$n;$i++){
		$id=$db->result($r,$i-1,"id");
		$prefix=$db->result($r,$i-1,"prefix");
		$doc_nom=$db->result($r,$i-1,"doc_nom");
		$tpoint_id=$db->result($r,$i-1,"tpoint_id");
		$tpoint_name=$db->result($r,$i-1,"tpoint_name");
		$client_id=$db->result($r,$i-1,"client_id");
		$client_name=$db->result($r,$i-1,"client_name");
		$cash_id=$db->result($r,$i-1,"cash_id");
		$cash_name=$db->result($r,$i-1,"cash_name");
		$summ=$db->result($r,$i-1,"summ");
		$sale_nom=$db->result($r,$i-1,"sale_prefix")."-".$db->result($r,$i-1,"sale_doc_nom");
		$data=$db->result($r,$i-1,"data");
		$user_name=$this->getMediaUserName($db->result($r,$i-1,"user_id"));
		$status_back=$db->result($r,$i-1,"status_back");
		$status_back_name=$gmanual->get_gmanual_caption($status_back);
		$summ_pdv=round($summ/6,2);
		$function="showBackClientsCard(\"$id\")";
		$list.="<tr style='cursor:pointer' onClick='$function'>
				<td>$prefix - $doc_nom</td>
				<td align='center'>$data</td>
				<td>$client_name</td>
				<td>$cash_name</td>
				<td>$summ</td>
				<td>$summ_pdv</td>
				<td>$sale_nom</td>
				<td>$user_name</td>
				<td>$status_back_name</td>
				</tr>";
	}
	return $list;
}
	
function show_back_clients_list_filter($data_start,$data_end){$db=DbSingleton::getDb();$slave=new slave;$gmanual=new gmanual;$income=new income;  session_start(); $ses_tpoint_id=$_SESSION["media_tpoint_id"]; //$where=" and j.tpoint_id='$ses_tpoint_id' and j.status_back!=0"; 
	$media_user_id=$_SESSION["media_user_id"]; if ($media_user_id==1 || $media_user_id==2 || $media_user_id==2){$where=" and j.status_back!=0";}
										 
	$data_cur=date("Y-m-d"); 	
	if ($data_start!='' && $data_end!='') $where_date="and j.data>='$data_start 00:00:00' and j.data<='$data_end 23:59:59'"; else 
		$where_date=" and j.data>='$data_cur 00:00:00' and j.data<='$data_cur 23:59:59'";													
										 
	$r=$db->query("select j.*, t.name as tpoint_name, CASH.name as cash_name, c.name as client_name, si.prefix as sale_prefix, si.doc_nom as sale_doc_nom from J_BACK_CLIENTS j
	left outer join T_POINT t on t.id=j.tpoint_id
	left outer join A_CLIENTS c on c.id=j.client_id
	left outer join CASH on CASH.id=j.cash_id
	left outer join J_SALE_INVOICE si on si.id=j.sale_invoice_id
	where j.status=1 $where_date and si.doc_type_id=61 order by j.id desc limit 0,500;");$n=$db->num_rows($r);$list="";
										 
	for ($i=1;$i<=$n;$i++){
		$id=$db->result($r,$i-1,"id");
		$prefix=$db->result($r,$i-1,"prefix");
		$doc_nom=$db->result($r,$i-1,"doc_nom");
		$tpoint_id=$db->result($r,$i-1,"tpoint_id");
		$tpoint_name=$db->result($r,$i-1,"tpoint_name");
		$client_id=$db->result($r,$i-1,"client_id");
		$client_name=$db->result($r,$i-1,"client_name");
		$cash_id=$db->result($r,$i-1,"cash_id");
		$cash_name=$db->result($r,$i-1,"cash_name");
		$summ=$db->result($r,$i-1,"summ");
		$sale_nom=$db->result($r,$i-1,"sale_prefix")."-".$db->result($r,$i-1,"sale_doc_nom");
		$data=$db->result($r,$i-1,"data");
		$user_name=$this->getMediaUserName($db->result($r,$i-1,"user_id"));
		$status_back=$db->result($r,$i-1,"status_back");
		$status_back_name=$gmanual->get_gmanual_caption($status_back);
		$summ_pdv=round($summ/6,2);
		$function="showBackClientsCard(\"$id\")";
		$list.="<tr style='cursor:pointer' onClick='$function'>
				<td>$prefix - $doc_nom</td>
				<td align='center'>$data</td>
				<td>$client_name</td>
				<td>$cash_name</td>
				<td>$summ</td>
				<td>$summ_pdv</td>
				<td>$sale_nom</td>
				<td>$user_name</td>
				<td>$status_back_name</td>
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
function getSaleInvoiceName($id){$db=DbSingleton::getDb(); $name="";
	$r=$db->query("select prefix,doc_nom from J_SALE_INVOICE where id='$id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){$name=$db->result($r,0,"prefix")."-".$db->result($r,0,"doc_nom");}
	return $name;
}
function getSaleInvoicePrefix($id){$db=DbSingleton::getDb(); $prefix="";
	$r=$db->query("select prefix from J_SALE_INVOICE where id='$id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){$prefix=$db->result($r,0,"prefix");}
	return $prefix;
}
function getSaleInvoiceSumm2($id){$db=DbSingleton::getDb(); $summ=0;$summ_debit=0;
	$r=$db->query("select summ,summ_debit from J_SALE_INVOICE where id='$id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){$summ=$db->result($r,0,"summ");$summ_debit=$db->result($r,0,"summ_debit");}
	return array($summ,$summ_debit);
}
function showBackClientsCard($back_id){$db=DbSingleton::getDb();$slave=new slave;$manual=new manual; $gmanual=new gmanual; session_start();$user_id=$_SESSION["media_user_id"];$user_name=$_SESSION["user_name"];
	$form_htm=RD."/tpl/back_clients_card.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$r=$db->query("select * from J_BACK_CLIENTS j where j.id='$back_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==0){$form_htm=RD."/tpl/access_deny.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);} }
	if ($n==1){ $this->updateBackClientsSumm($back_id);
		$id=$back_id;
		$prefix=$db->result($r,0,"prefix");
		$doc_nom=$db->result($r,0,"doc_nom");
		$user_use=$db->result($r,0,"user_use");
		if ($user_id!=$user_use && $user_use>0){
			$form_htm=RD."/tpl/back_clients_use_deny.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
			$form=str_replace("{user_name}",$this->getMediaUserName($user_use),$form);
			$admin_unlock="";
			if ($user_id==1 || $user_id==2){$admin_unlock="<button class='btn btn-sm btn-warning' onClick='unlockBackClientsCard(\"$back_id\");'><i class='fa fa-unlock'></i> Розблокувати</button>";}
			$form=str_replace("{admin_unlock}",$admin_unlock,$form);
		}
		if ($user_id==$user_use || $user_use==0){
			$data=$db->result($r,0,"data");if ($data=="0000-00-00"){$data=date("Y-m-d");}
			$cash_id=$db->result($r,0,"cash_id");$cash_name=$this->getCashAbr($cash_id); 
			$sale_invoice_id=$db->result($r,0,"sale_invoice_id"); $sale_invoice_name=$this->getSaleInvoiceName($sale_invoice_id);
			$tpoint_id=$db->result($r,0,"tpoint_id"); $tpoint_name=$this->getTpointName($tpoint_id);
			$client_id=$db->result($r,0,"client_id");$client_name=$this->getClientName($client_id);
			$storage_id=$db->result($r,0,"storage_id");$storage_list=$this->showStorageSelectListByTpoint($tpoint_id,$storage_id);
			
			$usd_to_uah=$db->result($r,0,"usd_to_uah");
			$eur_to_uah=$db->result($r,0,"eur_to_uah");
			
			list($usd_to_uah_new,$eur_to_uah_new)=$this->getKoursData('');
			
			if($usd_to_uah!=$usd_to_uah_new){$usd_to_uah=$usd_to_uah_new;}
			if($eur_to_uah!=$eur_to_uah_new){$eur_to_uah=$eur_to_uah_new;}
			
			$summ=$db->result($r,0,"summ");
			$status_back=$db->result($r,0,"status_back");

			if ($status_back==103){
				$form=str_replace("{hide_new_row_button}"," disabled style=\"visibility:hidden;\"",$form);
				$form=str_replace("{oper_disabled}"," disabled",$form);
				$form=str_replace("{oper_disabled2}"," disabled",$form);
				$form=str_replace("{oper_disabled3}"," ",$form);
			}
			
			$form=str_replace("{back_id}",$back_id,$form);
			$form=str_replace("{data}",$data,$form);
			$form=str_replace("{cash_id}",$cash_id,$form);
			$form=str_replace("{cash_name}",$cash_name,$form);
			$form=str_replace("{back_summ}",$summ,$form);
			$form=str_replace("{sale_invoice_id}",$sale_invoice_id,$form);
			$form=str_replace("{sale_invoice_name}",$sale_invoice_name,$form);
			$form=str_replace("{storage_id}",$storage_id,$form);
			$form=str_replace("{storage_list}",$storage_list,$form);
			$form=str_replace("{tpoint_id}",$tpoint_id,$form);
			$form=str_replace("{tpoint_name}",$tpoint_name,$form);
			$form=str_replace("{client_id}",$client_id,$form);
			$form=str_replace("{client_name}",$client_name,$form);
			$form=str_replace("{usd_to_uah}",$usd_to_uah,$form);
			$form=str_replace("{eur_to_uah}",$eur_to_uah,$form);
			$form=str_replace("{comment}",$comment,$form);
			$form=str_replace("{status_back_id}",$status_back,$form);
			
			$BackClientsChildsList="";
			list($BackClientsChildsList,$kol_str_row)=$this->showBackClientsStrList($back_id,$status_back,$sale_invoice_id);
			$form=str_replace("{BackClientsChildsList}",$BackClientsChildsList,$form);
			$form=str_replace("{kol_str_row}",$kol_str_row,$form);

			if ($status_back==102 && $kol_str_row>0){
				$form=str_replace("{oper_disabled}"," disabled",$form);
				$form=str_replace("{oper_disabled2}"," ",$form);
				$form=str_replace("{oper_disabled3}"," disabled",$form);
			}
			if ($status_back==102 && $kol_str_row==0){
				$form=str_replace("{oper_disabled}","",$form);
				$form=str_replace("{oper_disabled2}"," disabled",$form);
				$form=str_replace("{oper_disabled3}"," disabled",$form);
			}
			$form=str_replace("{oper_disabled}","",$form);
			$form=str_replace("{oper_disabled2}","disabled",$form);
			$form=str_replace("{hide_new_row_button}","",$form);
			
			$form=str_replace("{my_user_id}",$user_id,$form);
			$form=str_replace("{my_user_name}",$user_name,$form);
			
			list($kol_comments,$label_comments)=$this->labelCommentsCount($back_id);
			$form=str_replace("{labelCommentsCount}",$label_comments,$form);
			$form=str_replace("{labelArticlesUnKnownCount}",$label_art_unknown,$form);
			
			$this->setBackClientsCardUserAccess($back_id,$user_id);
		}
	}
	return array($form,$prefix."-".$doc_nom);
}
function unlockBackClientsCard($back_id){session_start();$user_id=$_SESSION["media_user_id"];$user_name=$_SESSION["user_name"];$answer=0;
	if ($user_id==1 || $user_id==2){$db=DbSingleton::getDb();
		$db->query("update J_BACK_CLIENTS set user_use='0' where id='$back_id';");
		$answer=1;
	}
	return $answer;
}

function closeBackClientsCard($back_id){session_start();$user_id=$_SESSION["media_user_id"];$user_name=$_SESSION["user_name"];$answer=0;
	$this->unsetBackClientsCardUserAccess($back_id,$user_id);
	$answer=1;
	return $answer;
}
function setBackClientsCardUserAccess($back_id,$user_id){$db=DbSingleton::getDb();
	if($back_id>0 && $user_id>0){
		$db->query("update J_BACK_CLIENTS set user_use='$user_id' where id='$back_id';");
	}
	return;
}
function unsetBackClientsCardUserAccess($back_id,$user_id){$db=DbSingleton::getDb();
	if($back_id>0 && $user_id>0){
		$db->query("update J_BACK_CLIENTS set user_use='0' where id='$back_id';");
	}
	return;
}

function clearBackClientsStr($back_id){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$slave=new slave;$answer=0;$err="";
	$back_id=$slave->qq($back_id);
	$r=$db->query("select oper_status,status_back from J_BACK_CLIENTS where id='$back_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){
		$oper_status=$db->result($r,0,"oper_status");
		$status_back=$db->result($r,0,"status_back");
		if ($oper_status==30 && $status_back==102) {
			
			$db->query("delete from J_BACK_CLIENTS_STR where back_id='$back_id' limit 1;");
			$db->query("update J_BACK_CLIENTS set summ=0 where id='$back_id' limit 1;");
			$answer=1;$err="";
		} else {$answer=0;$err="Документ заблоковано. Зміни вносити заборонено.";}
	}
	return array($answer,$err);
}

function setBackClientsClient($back_id,$client_id){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="Помилка збереження даних!";
	$back_id=$slave->qq($back_id);$client_id=$slave->qq($client_id);
	if ($back_id>0 && $client_id>0){
		$db->query("update J_BACK_CLIENTS set `client_id`='$client_id' where `id`='$back_id';");
		$answer=1;$err="";
	}
	return array($answer,$err);
}
	
function getUserTpointId($user_id){$db=DbSingleton::getDb();$tpoint_id=0;
	$r=$db->query("select * from media_users where id='$user_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){$tpoint_id=$db->result($r,0,"tpoint_id");}
	return $tpoint_id;
}

function showBackClientsClientList($sel_id){$db=DbSingleton::getDb();$slave=new slave;session_start();
	$form_htm=RD."/tpl/clients_parrent_tree.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$user_tpoint_id=$this->getUserTpointId($_SESSION["media_user_id"]);$user_tpoint_name=$this->getTpointName($user_tpoint_id);
	$r=$db->query("select c.*,ot.name as org_type_name, t2cn.COUNTRY_NAME, t2st.STATE_NAME, t2rg.REGION_NAME, t2ct.CITY_NAME,acc.tpoint_id, tp.name as tpoint_name   from A_CLIENTS c 
		left outer join A_ORG_TYPE ot on ot.id=c.org_type 
		left outer join T2_COUNTRIES t2cn on t2cn.COUNTRY_ID=c.country 
		left outer join T2_STATE t2st on t2st.STATE_ID=c.state
		left outer join T2_REGION t2rg on t2rg.REGION_ID=c.region
		left outer join T2_CITY t2ct on t2ct.CITY_ID=c.city
		left outer join A_CLIENTS_CATEGORY cc on cc.client_id=c.id
		left outer join A_CATEGORY ac on ac.id=cc.category_id
		left outer join A_CLIENTS_CONDITIONS acc on acc.client_id=c.id 
		left outer join T_POINT tp on tp.id=acc.tpoint_id 
		
		where c.status=1 and ac.id>0 $where group by c.id;");$n=$db->num_rows($r);$list="";
	for ($i=1;$i<=$n;$i++){
		$id=$db->result($r,$i-1,"id");
		$name=$db->result($r,$i-1,"name");
		$org_type_name=$db->result($r,$i-1,"org_type_name");
		$email=$db->result($r,$i-1,"email");
		$phone=$db->result($r,$i-1,"phone");
		$country=$db->result($r,$i-1,"COUNTRY_NAME");
		$state=$db->result($r,$i-1,"STATE_NAME");
		$region=$db->result($r,$i-1,"REGION_NAME");
		$city=$db->result($r,$i-1,"CITY_NAME");
		$address=$db->result($r,$i-1,"address");
		$tpoint_id=$db->result($r,$i-1,"tpoint_id");
		$tpoint_name=$db->result($r,$i-1,"tpoint_name");
		$cur="";$fn=" onClick='setBackClientsClient(\"$id\",\"$user_tpoint_id\",\"$user_tpoint_name\")'";
		if ($id==$prnt_id){$cur="background-color:#FFFF00;";}if ($id==$sel_id){$cur="background-color:#0CF;";}
		$list.="<tr style='$cur cursor:pointer;' $fn>
				<td></td>
				<td>$id</td>
				<td>$org_type_name</td>
				<td>$name</td>
				<td>$country</td>
				<td>$state</td>
				<td>$region</td>
				<td>$city</td>
				<td>$email</td>
				<td>$phone</td>
				</tr>";
	}
	$form=str_replace("{list}",$list,$form);
	return $form;
}


function unlinkBackClientsClient($back_id){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="Помилка збереження даних!";
	$back_id=$slave->qq($back_id);
	if ($back_id>0){
		$db->query("update J_BACK_CLIENTS set `client_id`='0' where `id`='$back_id';");
		$answer=1;$err="";
	}
	return array($answer,$err);
}

function getSaleInvoiceCashData($id){$db=DbSingleton::getDb();$slave=new slave;$cash_id=1;$usd_to_uah=$eur_to_uah=1;
	$r=$db->query("select * from J_SALE_INVOICE where id='$id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){
		$cash_id=$db->result($r,0,"cash_id");
		$cash_abr=$db->result($r,0,"cash_abr");
		$usd_to_uah=$db->result($r,0,"usd_to_uah");
		$eur_to_uah=$db->result($r,0,"eur_to_uah");
		if ($usd_to_uah==0 || $eur_to_uah==0){
			list($usd_to_uah_new,$eur_to_uah_new)=$this->getKoursData('');
		}
	}
	return array($cash_id,$usd_to_uah,$eur_to_uah);
}

function getBackClientSaleInvoiceStr($sis_id,$art_id){$back_amount=$back_price=$back_summ=0;$db=DbSingleton::getDb();
	$r=$db->query("select sum(amount) as back_amount, sum(price) as back_price, SUM(summ) as back_summ from J_BACK_CLIENTS_STR where art_id='$art_id' and sale_invoice_str_id='$sis_id' and status=1;");$n=$db->num_rows($r);
	for ($i=1;$i<=$n;$i++){
		$back_amount+=$db->result($r,$i-1,"back_amount");
		$back_price+=$db->result($r,$i-1,"back_price");
		$back_summ+=$db->result($r,$i-1,"back_summ");
	}
	return array($back_amount,$back_price,$back_summ);
}
	
function showSaleInvoiceArticleSearchForm($back_id,$si_id,$si_str_id,$art_id){$db=DbSingleton::getDb();$slave=new slave;$gmanual=new gmanual;
	$form_htm=RD."/tpl/back_clients_articles_list.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
																			  
	$r=$db->query("select sis.* from J_SALE_INVOICE_STR sis where sis.status=1 and sis.invoice_id='$si_id' order by sis.id asc;");$n=$db->num_rows($r);
	if ($n>0){
		list($cash_id,$usd_to_uah,$eur_to_uah)=$this->getSaleInvoiceCashData($si_id);
		for ($i=1;$i<=$n;$i++){
			$sis_id=$db->result($r,$i-1,"id");
			$art_id=$db->result($r,$i-1,"art_id"); 
			$article_nr_displ=$db->result($r,$i-1,"article_nr_displ");
			$brand_id=$db->result($r,$i-1,"brand_id");$brand_name=$this->getBrandName($brand_id);
			$amount=$db->result($r,$i-1,"amount");
			$price=$db->result($r,$i-1,"price_end");
			$summ=$db->result($r,$i-1,"summ");
			list($back_amount,$back_price,$back_summ)=$this->getBackClientSaleInvoiceStr($sis_id,$art_id);
			/*$back_amount=$db->result($r,$i-1,"back_amount");
			$back_price=$db->result($r,$i-1,"back_price");
			$back_summ=$db->result($r,$i-1,"back_summ");
			*/
			$max_back=$amount-$back_amount;
			/*
			if ($cash_id==1){
				$price=round($price*$usd_to_uah,2); $summ=round($summ*$usd_to_uah,2); 
			}
			if ($cash_id==3){
				$price=round($price*$usd_to_uah/$euro_to_uah,2); $summ=round($summ*$usd_to_uah/$euro_to_uah,2); 
			}*/

			$cur="";$fn=" onClick='showBackClientsArticleAmountWindow(\"$art_id\", \"$article_nr_displ\", \"$brand_name\", \"$amount\", \"$price\", \"$summ\",\"$sis_id\",\"$max_back\")'";
			if ($si_str_id==$sis_id){$cur="background-color:#FFFF00;";}
			if ($max_back<=0){$fn=""; $cur="background-color:#ebebeb;"; }
			$list.="<tr style='$cur cursor:pointer;' $fn>
				<td>$i</td>
				<td>$article_nr_displ</td>
				<td align='center'>$brand_name</td>
				<td>$amount</td>
				<td align='right'>$price</td>
				<td align='right'>$summ</td>
				<td>$back_amount</td>
				<td>$back_summ</td>
			</tr>";
		}
	}
	$form=str_replace("{list}",$list,$form);
	return $form;
}
	
function showBackClientsArticleAmountWindow($art_id,$back_id){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$amount=0;
	$form_htm=RD."/tpl/back_clients_articles_amount_window.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$form=str_replace("{art_id}",$art_id,$form);
	$amount=$this->getBackClientsArticleAmountBack($art_id,$back_id);
	$form=str_replace("{amount}",$amount,$form);
	return $form;
}

function getBackClientsArticleAmountBack($art_id,$back_id){$db=DbSingleton::getDb();$amount=0;
	$r=$db->query("select sum(amount) as back_amount from J_BACK_CLIENTS_STR where status=1 and back_id='$back_id' and art_id='$art_id';");$n=$db->num_rows($r);
	if ($n==1){$amount=$db->result($r,0,"back_amount")+0;	}
	return $amount;
}	
function showBackClientsSaleInvoiceList($client_id,$si_id){$db=DbSingleton::getDb();$slave=new slave;$gmanual=new gmanual;
	$form_htm=RD."/tpl/back_clients_sale_invoice_tree.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$r=$db->query("select sv.*, dp.prefix as dp_prefix, dp.doc_nom as dp_nom, t.name as tpoint_name, sl.name as seller_name, cl.name as client_name, dt.mvalue as doc_type_name,ch.abr2 as cash_abr2 from J_SALE_INVOICE sv
					left outer join J_DP dp on dp.id=sv.dp_id
					left outer join CASH ch on ch.id=sv.cash_id
					left outer join T_POINT t on t.id=sv.tpoint_id
					left outer join A_CLIENTS sl on sl.id=sv.seller_id
					left outer join A_CLIENTS cl on cl.id=sv.client_conto_id
					left outer join manual dt on dt.key='client_sale_type' and dt.id=sv.doc_type_id
					where sv.status=1 and sv.client_conto_id='$client_id' and sv.status_invoice='86' order by sv.status_invoice desc, sv.data_pay desc;");$n=$db->num_rows($r);
	for ($i=1;$i<=$n;$i++){
		$id=$db->result($r,$i-1,"id");
		$dp_id=$db->result($r,$i-1,"dp_id"); $dp_nom=$db->result($r,$i-1,"dp_prefix").$db->result($r,$i-1,"dp_nom");
		$prefix=$db->result($r,$i-1,"prefix");
		$doc_nom=$db->result($r,$i-1,"doc_nom");
		$data_create=$db->result($r,$i-1,"data_create");
		$tpoint_id=$db->result($r,$i-1,"tpoint_id");
		$tpoint_name=$db->result($r,$i-1,"tpoint_name");
		$seller_id=$db->result($r,$i-1,"seller_id");
		$seller_name=$db->result($r,$i-1,"seller_name");
		$client_id=$db->result($r,$i-1,"client_id");
		$client_name=$db->result($r,$i-1,"client_name");
		$doc_type_id=$db->result($r,$i-1,"doc_type_id");
		$doc_type_name=$db->result($r,$i-1,"doc_type_name");
		$summ=$db->result($r,$i-1,"summ");
		$cash_id=$db->result($r,$i-1,"cash_id");
		$cash_abr=$db->result($r,$i-1,"cash_abr2");
		$data_pay=$db->result($r,$i-1,"data_pay");
		$user_name=$this->getMediaUserName($db->result($r,$i-1,"user_id"));
		$status_select=$db->result($r,$i-1,"status_select");
		$status_select_cap=$gmanual->get_gmanual_caption($status_select);
		
		$cur="";$fn=" onClick='setBackClientsSaleInvoice(\"$id\", \"$prefix-$doc_nom\",\"$cash_id\",\"$cash_abr\",\"$seller_id\")'";
		if ($id==$si_id){$cur="background-color:#FFFF00;";}if ($id==$sel_id){$cur="background-color:#0CF;";}
		$list.="<tr style='$cur cursor:pointer;' $fn>
			<td>$i</td>
			<td>$prefix-$doc_nom</td>
			<td align='center'>$data_create</td>
			<td>$dp_nom</td>
			<td>$tpoint_name</td>
			<td align='left'>$seller_name</td>
			<td align='left'>$client_name</td>
			<td>$doc_type_name</td>
			<td align='center' style='min-width:80px;'>$summ$cash_abr</td>
			<td align='right'>$volume</td>
			<td align='right'>$data_pay</td>
			<td align='center'>$status</td>
		</tr>";
	}
	$form=str_replace("{list}",$list,$form);
	return $form;
}
	
function setBackClientsSaleInvoice($back_id,$si_id,$cash_id,$seller_id){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="Помилка збереження даних!";
	$back_id=$slave->qq($back_id);$si_id=$slave->qq($si_id);
	if ($back_id>0 && $si_id>0){
		$db->query("update J_BACK_CLIENTS set `sale_invoice_id`='$si_id',`cash_id`='$cash_id',`seller_id`='$seller_id' where `id`='$back_id';");
		$answer=1;$err="";
	}
	return array($answer,$err);
}	
	
function showBackClientsTpointList($sel_id){$db=DbSingleton::getDb();$slave=new slave;
	$form_htm=RD."/tpl/tpoint_tree.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	
	$r=$db->query("select t.*, t2cn.COUNTRY_NAME, t2st.STATE_NAME, t2rg.REGION_NAME, t2ct.CITY_NAME
		from T_POINT t 
		left outer join T2_COUNTRIES t2cn on t2cn.COUNTRY_ID=t.country 
		left outer join T2_STATE t2st on t2st.STATE_ID=t.state
		left outer join T2_REGION t2rg on t2rg.REGION_ID=t.region
		left outer join T2_CITY t2ct on t2ct.CITY_ID=t.city
		where t.status=1 $where;");$n=$db->num_rows($r);$list="";
	for ($i=1;$i<=$n;$i++){
		$id=$db->result($r,$i-1,"id");
		$name=$db->result($r,$i-1,"name");
		$full_name=$db->result($r,$i-1,"full_name");
		$country=$db->result($r,$i-1,"COUNTRY_NAME");
		$state=$db->result($r,$i-1,"STATE_NAME");
		$region=$db->result($r,$i-1,"REGION_NAME");
		$city=$db->result($r,$i-1,"CITY_NAME");
		$address=$db->result($r,$i-1,"address");
		$chief=$db->result($r,$i-1,"chief");
		$worker_name=$this->getMediaUserName($chief);
		$cur="";$fn=" onClick='setBackClientsTpoint(\"$id\", \"$name\")'";
		if ($id==$prnt_id){$cur="background-color:#FFFF00;";}if ($id==$sel_id){$cur="background-color:#0CF;";}
		$list.="<tr style='$cur cursor:pointer;' $fn>
			<td>$id</td>
			<td>$name</td>
			<td>$state</td>
			<td>$region</td>
			<td>$city</td>
			<td>$address</td>
			<td>$worker_name</td>
		</tr>";
	}
	
	$form=str_replace("{list}",$list,$form);
	return $form;
}

function unlinkBackClientsTpoint($back_id){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="Помилка збереження даних!";
	$back_id=$slave->qq($back_id);
	if ($back_id>0){
		$db->query("update J_BACK_CLIENTS set `tpoint_id`='0' where `id`='$back_id';");
		$answer=1;$err="";
	}
	return array($answer,$err);
}
	
function showBackClientsStrList($back_id,$status_back,$si_id){$db=DbSingleton::getDb();$slave=new slave;$cat=new catalogue;$manual=new manual;$gmanual=new gmanual; $list="";if ($status_back==""){$status_back=102;}  $tpoint_id=$this->getBackClientsTpoint($back_id);
	$r=$db->query("select j.* from J_BACK_CLIENTS_STR j 
				   where j.back_id='$back_id' order by j.id asc;");$n=$db->num_rows($r);$kl_rw=$n;$summ_back=0;
	  //print "back_id=$back_id,status_back=$status_back, si_id=$si_id";
	for ($i=1;$i<=$kl_rw;$i++){
		$id=$db->result($r,$i-1,"id");
		$si_str_id=$db->result($r,$i-1,"sale_invoice_str_id");
		$art_id=$db->result($r,$i-1,"art_id");
		$article_nr_displ=$db->result($r,$i-1,"article_nr_displ");
		$brand_id=$db->result($r,$i-1,"brand_id");$brand_name=$cat->getBrandName($brand_id);
		$amount=$db->result($r,$i-1,"amount");$amount_back_clients=$amount;
		$price=$slave->to_money($db->result($r,$i-1,"price"));
		$summ=$slave->to_money($db->result($r,$i-1,"summ"));
		$summ_back+=$summ;
	
		if ($status_back==102){
			$disabled="";if ($status_back!=102 && $status_back>0){$disabled=" disabled";}

			$list.="<tr id='strRow_$i'>
				<td>$i<input type='hidden' id='idStr_$i' value='$id'></td>
				<td style='min-width:140px;'><input type='hidden' id='artIdStr_$i' value='$art_id'>
					<div class='input-group'>
						<input class='form-control input-xs' type='text' readonly id='article_nr_displStr_$i' value='$article_nr_displ' placeholder='Індекс товару'>
						<span class='input-group-btn'> 
							<button type='button' class='btn btn-xs btn-info $disabled' $disabled onClick=\"showSaleInvoiceArticleSearchForm('$i','$si_str_id','$art_id','$back_id','$si_id');\"><i class=\"fa fa-bars\"></i></button>
						</span>
					</div>
					<span class='hidden'>$article_nr_displ</span>
				</td>
				<td style='min-width:100px;'><input type='hidden' id='brandIdStr_$i' value='$brand_id'>
					<input class='form-control input-xs' type='text' readonly id='brandNameStr_$i' value='$brand_name' placeholder='Бренд'>
					<span class='hidden'>$brand_name</span>
				</td>
				<td>
					<input type='text' id='amountStr_$i' readonly value='$amount' class='form-control input-xs numberOnly'>
					<span class='hidden'>$amount</span>
				</td>
				<td>
					<input type='text' id='priceStr_$i' readonly value='$price' class='form-control input-xs numberOnlyLong'>
					<span class='hidden'>$price</span>
				</td>
				<td>
					<input type='text' id='summStr_$i' readonly value='$summ' class='form-control input-xs numberOnlyLong'>
				</td>
				<td><button class='btn btn-xs btn-default $disabled' $disabled onClick='dropBackClientsStr(\"$i\",\"$back_id\",\"$id\");'><i class='fa fa-times'></i></button></td>
			</tr>";
		}
		if ($status_back==103){
			if ($article_nr_displ!=""){
				$list.="<tr>
				<td>$i<input type='hidden' id='idStr_$i' value='$id'></td>
				<td style='min-width:140px;'>$article_nr_displ</td>
				<td style='min-width:120px;'>$brand_name</td>
				<td>$amount</td>
				<td>$price</td>
				<td>$summ</td>
				<td></td>
			</tr>";
			}
		}
	}
	if ($status_back==102){
		$list="
			<tr id='bcStrNewRow' class='hidden'>
				<td>nom_i<input type='hidden' id='idStr_0' value=''></td>
				<td style='min-width:140px;'><input type='hidden' id='artIdStr_0' value=''>
					<div class='input-group'>
						<input class='form-control input-xs' type='text' readonly id='article_nr_displStr_0' value='' placeholder='Індекс товару'>
						<span class='input-group-btn'> 
							<button type='button' class='btn btn-xs btn-info' onClick=\"showSaleInvoiceArticleSearchForm('i_0','0','0','$back_id','$si_id');\"><i class=\"fa fa-bars\"></i></button>
						</span>
					</div>
					<span class='hidden'></span>
				</td>
				<td style='min-width:100px;'><input type='hidden' id='brandIdStr_0' value=''>
					<input class='form-control input-xs' type='text' readonly id='brandNameStr_0' value='' placeholder='Бренд'>
					<span class='hidden'></span>
				</td>
				<td>
					<div class='input-group'>
						<input type='text' id='amountStr_0' readonly value='' class='form-control input-xs numberOnly' >
					</div>
					<span class='hidden'></span>
				</td>
				<td>
					<input type='text' id='priceStr_0' readonly value='' class='form-control input-xs numberOnlyLong' >
					<span class='hidden'></span>
				</td>
				
				<td>
					<input type='text' id='summStr_0' readonly value='' class='form-control input-xs numberOnlyLong'>
				</td>
				<td></td>
			</tr>".$list;
	}
															  
	if ($status_back==102){
		//$db->query("update J_BACK_CLIENTS set `summ`='$summ_back' where id='$back_id' and status_back='102';");
		
	}
	return array($list,$kl_rw);
}

function saveBackClientsCard($back_id,$data_pay,$cash_id,$back_clients_summ,$doc_type_id,$tpoint_id,$client_id,$client_conto_id,$delivery_type_id,$carrier_id,$delivery_address){$db=DbSingleton::getDb();$slave=new slave;session_start();$user_id=$_SESSION["media_user_id"];$user_name=$_SESSION["user_name"];$answer=0;$err="Помилка збереження даних!";

	$back_id=$slave->qq($back_id);$data_pay=$slave->qq($data_pay);$cash_id=$slave->qq($cash_id);$back_clients_summ=$slave->qq($back_clients_summ);$doc_type_id=$slave->qq($doc_type_id);$tpoint_id=$slave->qq($tpoint_id);$client_id=$slave->qq($client_id);$client_conto_id=$slave->qq($client_conto_id);$delivery_type_id=$slave->qq($delivery_type_id);$carrier_id=$slave->qq($carrier_id);$delivery_address=$slave->qq($delivery_address);
	if ($back_id==0 || $back_id==""){
	}
	if ($back_id>0){
		//$this->check_doc_prefix_nom($income_id,$client_id);
		$db->query("update J_BACK_CLIENTS set `doc_type_id`='$doc_type_id', `tpoint_id`='$tpoint_id', `client_id`='$client_id', `client_conto_id`='$client_conto_id', `data_pay`='$data_pay', `cash_id`='$cash_id', `summ`='$back_clients_summ', `delivery_type_id`='$delivery_type_id', `carrier_id`='$carrier_id', `delivery_address`='$delivery_address' where `id`='$back_id';");		
		
		$answer=1;$err="";
	}
	return array($answer,$err);
}

function saveBackClientsCardData($back_id,$cash_id,$frm,$tto,$idStr,$artIdStr,$article_nr_displStr,$brandIdStr,$amountStr,$priceStr,$priceEndStr,$discountStr,$summStr){$db=DbSingleton::getDb();$slave=new slave;session_start();$user_id=$_SESSION["media_user_id"];$user_name=$_SESSION["user_name"];$answer=0;$err="Помилка збереження даних!";
	$back_id=$slave->qq($back_id);$frm=$slave->qq($frm);$tto=$slave->qq($tto);$cash_id=$slave->qq($cash_id);
	if ($back_id>0){
		
		/*$idStr=$slave->qq($idStr);$artIdStr=$slave->qq($artIdStr);$article_nr_displStr=$slave->qq($article_nr_displStr);$brandIdStr=$slave->qq($brandIdStr);$amountStr=$slave->qq($amountStr);$priceStr=$slave->qq($priceStr);$discountStr=$slave->qq($discountStr);
		for($i=$frm;$i<=$tto;$i++){
			$idS=$idStr[$i]; $artIdS=$artIdStr[$i]; $article_nr_displS=$article_nr_displStr[$i]; $brandIdS=$brandIdStr[$i]; $amountS=$amountStr[$i]; $priceS=$priceStr[$i]; $priceEndS=$priceEndStr[$i]; $discountS=$discountStr[$i];$summS=$summStr[$i]; 
			if ($idS=="" || $idS==0){
				$r=$db->query("select max(id) as mid from J_BACK_CLIENTS_STR;");$idS=0+$db->result($r,0,"mid")+1;
				$db->query("insert into J_BACK_CLIENTS_STR (`id`,`back_id`) values ('$idS','$back_id');");
			}
			if ($idS>0){
				if ($artIdS!="" && $artIdS>0 && $article_nr_displS!=""){
					$db->query("update J_BACK_CLIENTS_STR set `art_id`='$artIdS', `article_nr_displ`='$article_nr_displS', `brand_id`='$brandIdS', `amount`='$amountS', `price`='$priceS', `price_end`='$priceEndS', `discount`='$discountS',`summ`='$summS' where id='$idS' and back_id='$back_id';");
				}else{
					$db->query("delete from J_BACK_CLIENTS_STR where id='$idS' and back_id='$back_id';");
				}
			}
		}
		*/
		$answer=1;$err="";
	}
	return array($answer,$err);
}

function setArticleToBackClients($back_id,$si_id,$sis_id,$art_id,$article_nr_displ,$amount_back){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$slave=new slave;session_start();$user_id=$_SESSION["media_user_id"];$user_name=$_SESSION["user_name"];$answer=0;$err="Помилка збереження даних!";
	$back_id=$slave->qq($back_id);$si_id=$slave->qq($si_id);$sis_id=$slave->qq($sis_id);
	if ($back_id>0 && $si_id>0 && $sis_id>0){
		$art_id=$slave->qq($art_id);$article_nr_displ=$slave->qq($article_nr_displ);$amount_back=$slave->qq($amount_back);
		$bcs_amountEx=0;$sis_amount=0;
		$r=$db->query("select id,amount from J_BACK_CLIENTS_STR where back_id='$back_id' and sale_invoice_str_id='$sis_id' and art_id='$art_id' and status='1' limit 0,1;");$n=$db->num_rows($r);
		if ($n==1){
			$bcs_id=$db->result($r,0,"id");
			$bcs_amountEx=$db->result($r,0,"amount");
		}
		$r1=$db->query("select amount,price_end from J_SALE_INVOICE_STR where invoice_id='$si_id' and id='$sis_id' and art_id='$art_id' and status='1' limit 0,1;");$n1=$db->num_rows($r1);
		if ($n1==1){
			$sis_amount=$db->result($r1,0,"amount");
			$sis_price=$db->result($r1,0,"price_end");
		}
		$max_back=0;
		if ($n>0 && $n1>0 && $bcs_amountEx>0 && $sis_amount>0){ $max_back=$sis_amount-$bcs_amountEx; }
		if ($n==0 && $n1>0 && $sis_amount>0){ $max_back=$sis_amount; }
		
		
		if ($max_back<$amount_back){$answer=0;$err="Кількість для повернення ВЖЕ більша за можливу! (максимально: $max_back)";}
		if ($max_back>0 && $max_back>=$amount_back){
			
			list($cash_id,$usd_to_uah,$eur_to_uah)=$this->getSaleInvoiceCashData($si_id);			
			/*
			if ($cash_id==1){ $sis_price=round($sis_price*$usd_to_uah,2);}
			if ($cash_id==3){ $sis_price=round($sis_price*$usd_to_uah/$euro_to_uah,2);}			*/
			
			if ($n==0){ $brand_id=$this->getBrandIdByArtId($art_id);
				$r=$db->query("select max(id) as mid from J_BACK_CLIENTS_STR;");$bcs_id=0+$db->result($r,0,"mid")+1;
				$db->query("insert into J_BACK_CLIENTS_STR (`id`,`back_id`,`sale_invoice_str_id`,`art_id`,`article_nr_displ`,`brand_id`,`price`) values ('$bcs_id','$back_id','$sis_id','$art_id','$article_nr_displ','$brand_id','$sis_price');");
			}
			if ($bcs_id>0){
				$amount_back_update=$bcs_amountEx+$amount_back;
				$sis_summ=round($amount_back_update*$sis_price,2);
				$db->query("update J_BACK_CLIENTS_STR set amount='$amount_back_update', `summ`='$sis_summ' where id='$bcs_id' and back_id='$back_id' and sale_invoice_str_id='$sis_id' limit 1;");
				
				$back_clients_summ=0;
				$rs=$db->query("select sum(`summ`) as back_summ from J_BACK_CLIENTS_STR where back_id='$back_id' and status='1';");$back_clients_summ=$db->result($rs,0,"back_summ")+0;
				$db->query("update J_BACK_CLIENTS set `summ`='$back_clients_summ' where id='$back_id';");
				$answer=1;$err="";
			}
		}
	}
	return array($answer,$err,$back_clients_summ);
}

function getClientCashConditions($client_id){$db=DbSingleton::getDb();$cash_id=0;$credit_cash_id=0;
	$r=$db->query("select cash_id,credit_cash_id from A_CLIENTS_CONDITIONS where client_id ='$client_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){
		$cash_id=$db->result($r,0,"cash_id");
		$credit_cash_id=$db->result($r,0,"credit_cash_id");
	}
	return array($cash_id,$credit_cash_id);
}
function getClientOrgType($client_id){$db=DbSingleton::getDb();$org_type=0;
	$r=$db->query("select org_type from A_CLIENTS where id ='$client_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){$org_type=$db->result($r,0,"org_type");}
	return $org_type;
}
function getCashName($cash_id){$db=DbSingleton::getDb();$name="";
	$r=$db->query("select name from CASH where id ='$cash_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){$name=$db->result($r,0,"name");}
	return $name;
}

function getBackClientsClientContoCash($client_id){$db=DbSingleton::getDb();$cash_id=1;$answer=0;$err="Помилка";
	$r=$db->query("select cash_id from A_CLIENTS_CONDITIONS where client_id ='$client_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){$cash_id=$db->result($r,0,"cash_id");$answer=1;$err="";}
	return array($answer,$err,$cash_id);
}
function getBackClientsClientDocType($client_id){$db=DbSingleton::getDb();$doc_type_id=64;$answer=0;$err="Помилка";
	$r=$db->query("select doc_type_id from A_CLIENTS_CONDITIONS where client_id ='$client_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){$doc_type_id=$db->result($r,0,"doc_type_id");$answer=1;$err="";}
	return array($answer,$err,$doc_type_id);
}
function getClientPaymentDelay($client_id){$db=DbSingleton::getDb();$data_pay=date("Y-m-d");$answer=0;$err="Помилка";
	$r=$db->query("select * from A_CLIENTS_CONDITIONS where client_id='$client_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){	
		$payment_delay=$db->result($r,0,"payment_delay");
		$data_pay=date("Y-m-d",strtotime("+$payment_delay day", strtotime($data_pay)));
		$answer=1;$err="";
	}
	return array($answer,$err,$data_pay);
}


function changeBackClientsCash($back_id,$cash_id){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$slave=new slave;session_start();$user_id=$_SESSION["media_user_id"];$user_name=$_SESSION["user_name"];$answer=0;$err="Помилка збереження даних!";
	$back_id=$slave->qq($back_id);$cash_id=$slave->qq($cash_id);
	if ($back_id>0){
		$r=$db->query("select oper_status,client_conto_id from J_BACK_CLIENTS where id='$back_id' limit 0,1;");$n=$db->num_rows($r);
		if ($n==1){
			$oper_status=$db->result($r,0,"oper_status");
			if ($oper_status==30) {
				$client_conto_id=$db->result($r,0,"client_conto_id");
				$org_type=$this->getClientOrgType($client_conto_id);
				list($client_cash_id,$credit_cash_id)=$this->getClientCashConditions($client_conto_id);
				if ($client_cash_id==$cash_id || $org_type==0 || $org_type==1){
					$db->query("update J_BACK_CLIENTS set cash_id='$cash_id' where id='$back_id';");
					$this->updateBackClientsPriceCash($back_id);
					$answer=1;$err="";
				}else{$answer=0;$err="Валюта розрахунку клієнта ".$this->getCashName($client_cash_id).". Змініть кінцевого платника на того кому дозволено розрахунок у валюті ".$this->getCashName($cash_id);}
			} else {$answer=0;$err="Документ заблоковано. Зміни вносити заборонено.";}
		}
		
	}
	return array($answer,$err);
}

function updateBackClientsPriceCash($back_id){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$slave=new slave;session_start();$user_id=$_SESSION["media_user_id"];$user_name=$_SESSION["user_name"];$answer=0;
	$r=$db->query("select * from J_BACK_CLIENTS where id='$back_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){
		$oper_status=$db->result($r,0,"oper_status");
		if ($oper_status==30) {
			$client_conto_id=$db->result($r,0,"client_conto_id");
			$cash_id=$db->result($r,0,"cash_id");
			
			list($usd_to_uah,$eur_to_uah)=$this->getKoursData('');
			
			$r=$db->query("select * from J_BACK_CLIENTS_STR where back_id='$back_id' order by id asc;");$n=$db->num_rows($r);$summ_back=0;
			for ($i=1;$i<=$n;$i++){
				$art_id=$db->result($r,$i-1,"art_id");
				$amount=$db->result($r,$i-1,"amount");
				$price=$db->result($r,$i-1,"price");
				$price_end=$db->result($r,$i-1,"price_end");
				$discount=$db->result($r,$i-1,"discount");
				$summ=$db->result($r,$i-1,"summ");
				if ($cash_id==1){$price=round($price*$usd_to_uah,2); $price_end=round($price_end*$usd_to_uah,2); }
				if ($cash_id==3){$price=round($price*$usd_to_uah/$eur_to_uah,2); $price_end=round($price_end*$usd_to_uah/$eur_to_uah,2); }
				$summ=$amount*$price_end;
				$summ_back+=$summ;
			}
			$db->query("update J_BACK_CLIENTS set `summ`='$summ_back' where id='$back_id' limit 1;");
			$answer=1;
		} else {$answer=0;}
	}
	return $answer;
}


function getArticlePrice($art_id,$back_id){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$slave=new slave;$price=0;
	if ($back_id>0 && $art_id!=""){
		list($price_lvl,$margin_price_lvl,$price_suppl_lvl,$margin_price_suppl_lvl,$client_vat)=$this->getBackClientsClientPriceLevels($back_id);
		$query="select t2apr.price_".$price_lvl.", t2si.price_usd as suppl_price_usd
				from T2_ARTICLES t2a 
				left outer join T2_ARTICLES_PRICE_RATING t2apr on (t2apr.art_id=t2a.ART_ID)
				left outer join T2_SUPPL_IMPORT t2si on (t2si.art_id=t2a.ART_ID)
				where t2a.ART_ID='$art_id' and t2apr.in_use='1' limit 0,1;";
		$r=$dbt->query($query);$n=$dbt->num_rows($r);
		if ($n==1){
			$price=$dbt->result($r,0,"price_".$price_lvl);
			if ($margin_price_lvl>0){
				$price=$price+round($price*$margin_price_lvl/100,2);
			}
			$suppl_price_usd=$dbt->result($r,0,"suppl_price_usd");
		}
	}
	return $price;
}

function getArticleSupplPrice($art_id,$back_id,$suppl_id,$suppl_storage_id){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$slave=new slave;$price=0;

	if ($back_id>0 && $art_id!=""){
		list($price_lvl,$margin_price_lvl,$price_suppl_lvl,$margin_price_suppl_lvl,$client_vat)=$this->getBackClientsClientPriceLevels($back_id);
		$query="select t2si.price_usd from T2_ARTICLES t2a 
		left outer join T2_SUPPL_ARTICLES_IMPORT t2sai on (t2sai.art_id=t2a.ART_ID)
		left outer join T2_SUPPL_IMPORT t2si on (t2si.art_id=t2sai.art_id and t2si.suppl_id=t2sai.suppl_id and t2si.status=1)
		where t2a.ART_ID = '$art_id' and t2sai.suppl_id='$suppl_id' limit 0,1;";
		$r=$dbt->query($query);$n=$dbt->num_rows($r);
		if ($n==1){
			//////////////
			$suppl_price_usd=$dbt->result($r,0,"price_usd");

			list($price_in_vat,$show_in_vat,$price_add_vat)=$this->getSupplVatConditions($suppl_id);
			$price_suppl=$suppl_price_usd;
			$tpoint_id=$this->getBackClientsTpoint($back_id);
			//Step 1;
			list($suppl_margin_fm,$suppl_delivery_fm,$suppl_margin2_fm)=$this->getTpointSupplFm($tpoint_id,$suppl_id,$suppl_storage_id,$price_suppl,$price_suppl_lvl);
			if ($suppl_margin_fm>0){

				$price=($price_suppl+$price_suppl*$suppl_margin_fm/100)-$price_suppl;
				if ($price>$suppl_delivery_fm){
					$price=($price_suppl+$price_suppl*$suppl_margin_fm/100);
				}
				if ($price<=$suppl_delivery_fm){
					$price=$price_suppl+$price_suppl*$suppl_margin2_fm/100+$suppl_delivery_fm;
				}
				//Step 2; Client Margin
				if ($margin_price_suppl_lvl>0 && $margin_price_suppl_lvl!=""){
					$price=$price+$price*$margin_price_suppl_lvl/100;
				}

				//Step 3; VAT
				//$price_in_vat,$show_in_vat,$price_add_vat
				if ($client_vat==1){
					if ($price_in_vat==0 && $show_in_vat==1 && $price_add_vat==1){
						$price=$price+$price*20/100;
					}
					if ($price_in_vat==0 && $show_in_vat==0){
						$price=0;
					}
				}
			}
			$price=round($price,2);
			
			//////////////
		}
	}
	return $price;
}

function dropBackClientsStr($back_id,$back_str_id){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$slave=new slave;$answer=0;$err="Помилка індексу";
	$back_id=$slave->qq($back_id);$back_str_id=$slave->qq($back_str_id);
	$r=$db->query("select oper_status,status,status_back from J_BACK_CLIENTS where id='$back_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){
		$status=$db->result($r,0,"status");
		$oper_status=$db->result($r,0,"oper_status");
		$status_back=$db->result($r,0,"status_back");
		if ($oper_status==30 && ($status==1 ||$status==1) && $status_back==102){
			$r1=$db->query("select * from J_BACK_CLIENTS_STR where id='$back_str_id' limit 0,1;");$n1=$db->num_rows($r1);
			if ($n1==1){
				$db->query("delete from J_BACK_CLIENTS_STR where id='$back_str_id' and back_id='$back_id' limit 1;");
				$back_clients_summ=$this->updateBackClientsSumm($back_id);
				$answer=1;$err="";
			}
		}else {$answer=0;$err="Видалення заблоковано. Повернення вже прийнято.";}
	}
	return array($answer,$err,$back_clients_summ);
}

function updateBackClientsSumm($back_id){$db=DbSingleton::getDb();$slave=new slave;$sum=0;
	$cash_id=$this->getBackClientsCashId($back_id);list($usd_to_uah,$eur_to_uah)=$this->getKoursData('');
	$r=$db->query("select * from J_BACK_CLIENTS_STR where back_id='$back_id';");$n=$db->num_rows($r);
	for ($i=1;$i<=$n;$i++){
		$str_id=$db->result($r,$i-1,"id");
		$price=$db->result($r,$i-1,"price"); 
		$amount=$db->result($r,$i-1,"amount");
		
		$summ=round($amount*$price,2);
		$summ_db=$slave->to_money($db->result($r,$i-1,"summ"));
		if ($summ_db!=$summ){$summ_db=$summ;
			$db->query("update J_BACK_CLIENTS_STR set `summ`='$summ_db' where id='$str_id' limit 1;"); 
		}
		$sum=$sum+$summ_db;
	}
	if ($n>0){ 
		$db->query("update J_BACK_CLIENTS set `summ`='$sum' where id='$back_id' and oper_status='30' and status='1';"); 
	}
	return $sum;
}

function makeBackClientsCardFinish($back_id){$db=DbSingleton::getDb();$slave=new slave;$cat=new catalogue;$answer=0;$err="";
	$back_id=$slave->qq($back_id);
	/*$r=$db->query("select oper_status,storage_id,storage_cells_id from J_INCOME where id='$back_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){
		$oper_status=$db->result($r,0,"oper_status");
		$storage_id=$db->result($r,0,"storage_id");
		$storage_cells_id=$db->result($r,0,"storage_cells_id");
		if ($storage_id==0 || $storage_cells_id==0){$answer=0;$err="Не вказано \"Склад зберігання\" або \"Комірка зберігання\". Накладну не проведено!";}
		if ($storage_id>0 && $storage_cells_id>0){
			if ($oper_status==30) {
				$db->query("update J_INCOME set oper_status='31' where id='$back_id';");
				/* 				make calculation back_clients  */
				
	/*			$r1=$db->query("select * from J_INCOME_STR where back_id='$back_id' order by id asc;");$n1=$db->num_rows($r1);
				for ($i=1;$i<=$n1;$i++){
					$art_id=$db->result($r1,$i-1,"art_id");
					$amount=$db->result($r1,$i-1,"amount");
					$price_man_usd=$db->result($r1,$i-1,"price_man_usd");
					
					list($oper_price,$general_stock)=$cat->getArticleOperPriceGeneralStock($art_id);
					$new_oper_price=round((($oper_price*$general_stock)+($amount*$price_man_usd))/($amount+$general_stock),2);
					$new_general_stock=$amount+$general_stock;
					
					$cat->setArticleOperPriceGeneralStock($art_id,$new_oper_price,$new_general_stock);
//					$db->query("insert into T2_ARTICLES_STRORAGE_CELLS (`ART_ID`,`AMOUNT`,`INCOME_ID`,`STORAGE_ID`,`STORAGE_CELLS_ID`) values ('$art_id','$amount','$back_id','$storage_id','$storage_cells_id');");
					$db->query("insert into T2_ARTICLES_STRORAGE (`ART_ID`,`AMOUNT`,`INCOME_ID`,`STORAGE_ID`) values ('$art_id','$amount','$back_id','$storage_id');");
				}
				
				/* 				end calculation back_clients  */
	/*			$answer=1;$err="";
			} else {$answer=0;$err="Накладну заблоковано. Зміни вносити заборонено.";}
		}
	}*/
	return array($answer,$err);
}



function getBackClientsCashId($back_id){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$slave=new slave;$cash_id=2;
	$r=$db->query("select cash_id  from J_BACK_CLIENTS where id='$back_id' limit 0,1;"); $n=$db->num_rows($r);
	if ($n==1){	$cash_id=$db->result($r,0,"cash_id");}
	return $cash_id;
}

function getBackClientsClient($back_id){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$slave=new slave;$client_conto_id=0;$client_id=0;
	$r=$db->query("select client_id,client_conto_id  from J_BACK_CLIENTS where id='$back_id' limit 0,1;"); $n=$db->num_rows($r);
	if ($n==1){
		$client_id=$db->result($r,0,"client_id");
		$client_conto_id=$db->result($r,0,"client_conto_id");
		if ($client_conto_id==0 && $client_id>0){$client_conto_id=$client_id;}
	}
	return $client_conto_id;
}


function getArticleInBackClients($art_id,$back_id){$db=DbSingleton::getDb();$amount=0;
	$r=$db->query("select sum(amount) as amount from J_BACK_CLIENTS_STR where art_id='$art_id' and back_id='$back_id';");$amount=0+$db->result($r,0,"amount");
	return $amount;
}

function getArticleRemoteStorageAmount($art_id,$cur_storage_id){$db=DbSingleton::getTokoDb();$amount=0;
	$r=$db->query("select sum(AMOUNT) as amount, sum(RESERV_AMOUNT) as reserv from T2_ARTICLES_STRORAGE where art_id='$art_id' and STORAGE_ID!='$cur_storage_id';");
	$amount=0+$db->result($r,0,"amount")-$db->result($r,0,"reserv");
	return $amount;
}

function setArticleToSelectAmountBackClients($art_id,$back_id){
	$form_htm=RD."/tpl/back_clients_select_amount_article_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$form=str_replace("{back_clients_rest_storage_list}",$this->showArticleRestStorageSelectList($art_id,$back_id),$form);
	$form=str_replace("{art_id}",$art_id,$form);
	return $form;
}

function showBackClientsArticleAmountChange($art_id,$back_clients_str_id,$amount){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();
	$form_htm=RD."/tpl/back_clients_select_amount_article_change_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$r=$db->query("select * from J_BACK_CLIENTS_STR where id='$back_clients_str_id' and status_back='93' limit 0,1");$n=$db->num_rows($r);
	if ($n==1){
		$back_id=$db->result($r,0,"back_id");
		$article_nr_displ=$db->result($r,0,"article_nr_displ");
		$brand_id=$db->result($r,0,"brand_id");$brand_name=$this->getBrandName($brand_id);
		$amount=$db->result($r,0,"amount");
		$storage_id=$db->result($r,0,"storage_id_from");
		list($info,$max_moving)=$this->showArticleRestStorageSelectText($art_id,$storage_id,$amount);
	}
	$form=str_replace("{storage_name}",$this->getStorageName($storage_id),$form);
	$form=str_replace("{amountRestText}",$info,$form);
	$form=str_replace("{max_moving}",$max_moving,$form);
	$form=str_replace("{cur_amount}",$amount,$form);
	$form=str_replace("{back_clients_str_id}",$back_clients_str_id,$form);
	
	return array($form,$article_nr_displ,$brand_name);
}

function getBackClientsTpoint($back_id){$db=DbSingleton::getDb();$tpoint_id=0;
	$r=$db->query("select tpoint_id from J_BACK_CLIENTS where id='$back_id' limit 0,1");$n=$db->num_rows($r);
	if ($n==1){ $tpoint_id=$db->result($r,0,"tpoint_id"); }
	return $tpoint_id;
}

function getArticleStorageAmountBackClients($art_id,$back_id,$storage_id){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$amount=0;
	$r=$db->query("select amount from J_BACK_CLIENTS_STR where back_id='$back_id' and status_back='93' and storage_id_from='$storage_id' and art_id='$art_id' limit 0,1");$n=$db->num_rows($r);
	if ($n==1){ $amount=$db->result($r,0,"amount"); }
	return $amount;
}
function getArticleSupplStorageAmountBackClients($art_id,$back_id,$suppl_id,$suppl_storage_id){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$amount=0;
	$r=$db->query("select amount from J_BACK_CLIENTS_STR where back_id='$back_id' and status_back='93' and suppl_id='$suppl_id' and suppl_storage_id='$suppl_storage_id' and art_id='$art_id' limit 0,1");$n=$db->num_rows($r);
	if ($n==1){ $amount=$db->result($r,0,"amount"); }
	return $amount;
}

function showArticleRestStorageSelectList($art_id,$back_id){$db=DbSingleton::getTokoDb();$list="";
	$where=""; $tpoint_id=$this->getBackClientsTpoint($back_id);
	$query="select s.id,s.name,t2as.AMOUNT, t2as.RESERV_AMOUNT from STORAGE s left outer join T2_ARTICLES_STRORAGE t2as on t2as.STORAGE_ID=s.id where s.status='1' and t2as.ART_ID='$art_id' $where order by s.name asc,s.id asc;";
	$r=$db->query($query);$n=$db->num_rows($r);
	for ($i=1;$i<=$n;$i++){
		$id=$db->result($r,$i-1,"id");
		$name=$db->result($r,$i-1,"name");
		$amount=$db->result($r,$i-1,"AMOUNT");
		$reserv_amount=$db->result($r,$i-1,"RESERV_AMOUNT");
		$cur_amount=$this->getArticleStorageAmountBackClients($art_id,$back_id,$id);
		$reserv_amount_rest=$reserv_amount-$cur_amount;
		$delivery_info=$this->getTpointDeliveryInfo($tpoint_id,$id);
		
		$max_moving=$amount;
		if ($amount!=0 || $cur_amount!=0 || $reserv_amount_rest!=0){
			$list.="<tr onClick=\"showBackClientsAmountInputWindow('$art_id','$id');\" style='cursor:pointer'>
				<td>$i <input type='hidden' id='storage_amount_id' value='$id'></td>
				<td>$name</td>
				<td>$amount</td>
				<td>$cur_amount</td>
				<td>$reserv_amount_rest</td>
				<td>$delivery_info</td>
			</tr>";
		}
	}
	return $list;
}
function showBackClientsAmountInputWindow($art_id,$back_id,$storage_id){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$amount=0;
	$form_htm=RD."/tpl/back_clients_amount_window.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$form=str_replace("{art_id}",$art_id,$form);
	$amount=$this->getArticleStorageAmountBackClients($art_id,$back_id,$storage_id);
	$form=str_replace("{amount}",$amount,$form);
	return $form;
}
function showBackClientsSupplAmountInputWindow($art_id,$article_nr_displ,$brand_id,$back_id,$suppl_id,$suppl_storage_id,$price){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$amount=0;
	$form_htm=RD."/tpl/back_clients_amount_suppl_window.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$form=str_replace("{art_id}",$art_id,$form);
	$amount=$this->getArticleSupplStorageAmountBackClients($art_id,$back_id,$suppl_id,$suppl_storage_id);
	require_once RD."/lib/catalogue_class.php";$cat=new catalogue;
	$form=str_replace("{amount}",$amount,$form);
	$form=str_replace("{price}",$price,$form);
	
	$summ=0;$summ=$amount*$price;
	
	$form=str_replace("{summ}",$summ,$form);
	$form=str_replace("{article_nr_displ}",$article_nr_displ,$form);
	$form=str_replace("{brand_id}",$brand_id,$form);
	$form=str_replace("{suppl_id}",$suppl_id,$form);
	$suppl_storage_name=$cat->getSupplStorageName($suppl_storage_id);
	$form=str_replace("{suppl_storage_id}",$suppl_storage_id,$form);
	$form=str_replace("{suppl_storage_code}",$suppl_storage_name." ($suppl_id.$suppl_storage_id)",$form);
	$form=str_replace("{suppl_delivery_info}",$this->getTpointSupplDeliveryInfo($this->getBackClientsTpoint($back_id),$suppl_id,$suppl_storage_id),$form);
	
	return $form;
}

function showArticleRestStorageCellsList($art_id,$storage_id){$db=DbSingleton::getTokoDb();$list="<option value='0'>-- Оберіть зі списку --</option>";
	$query="SELECT sc.id, sc.cell_value, t2asc.AMOUNT, t2asc.RESERV_AMOUNT,t2as.AMOUNT as AMOUNT_STORAGE, t2as.RESERV_AMOUNT as RESERV_AMOUNT_STORAGE
			FROM STORAGE_CELLS sc
			LEFT OUTER JOIN T2_ARTICLES_STRORAGE_CELLS t2asc ON ( t2asc.STORAGE_CELLS_ID = sc.id )
			LEFT OUTER JOIN T2_ARTICLES_STRORAGE t2as ON ( t2as.STORAGE_ID = sc.storage_id )
			WHERE sc.status = '1' AND t2asc.ART_ID = '$art_id' AND t2as.ART_ID = '$art_id' AND sc.storage_id='$storage_id' ORDER BY sc.cell_value ASC , sc.id ASC;";
			
			
	$r=$db->query($query);$n=$db->num_rows($r);
	//print $query;
	for ($i=1;$i<=$n;$i++){
		$id=$db->result($r,$i-1,"id");
		$name=$db->result($r,$i-1,"cell_value");
		$amount=$db->result($r,$i-1,"AMOUNT");
		$reserv_amount=$db->result($r,$i-1,"RESERV_AMOUNT");
		$amount_storage=$db->result($r,$i-1,"AMOUNT_STORAGE");
		$reserv_amount_storage=$db->result($r,$i-1,"RESERV_AMOUNT_STORAGE");
		if ($amount>$amount_storage){$amount=$amount_storage; $reserv_amount=$reserv_amount_storage;}
		
		$max_moving=$amount;
		if ($reserv_amount!=0 || $amount!=0){
			$list.="<option value='$id' data-max-mov='$max_moving' data-cellId-mov='0'>$name | Залишок: $amount; Резерв: $reserv_amount; </option>";
		}
	}
	return $list;
}

function showStorageCellsList($storage_id,$exclude_id){$db=DbSingleton::getTokoDb();$list="<option value='0'>-- Оберіть зі списку --</option>";
	$query=" SELECT id, cell_value FROM STORAGE_CELLS WHERE status = '1' AND storage_id='$storage_id' AND id<>'$exclude_id'  ORDER BY cell_value ASC , id ASC;";
	$r=$db->query($query);$n=$db->num_rows($r);
	for ($i=1;$i<=$n;$i++){
		$id=$db->result($r,$i-1,"id");
		$name=$db->result($r,$i-1,"cell_value");
		$list.="<option value='$id'>$name</option>";
	}
	return $list;
}


function getArticleName($art_id){$db=DbSingleton::getTokoDb();$slave=new slave; $name="";
	$r=$db->query("select * from T2_NAMES where ART_ID='$art_id' and `LANG_ID`='16' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){
		$name=$db->result($r,0,"NAME");
	}
	return $name;
}

function getArticleWightVolume($art_id){$db=DbSingleton::getTokoDb();$slave=new slave; $weight=0;$volume=0;$weight2=0;
	$r=$db->query("select VOLUME,WEIGHT_BRUTTO,WEIGHT_NETTO from T2_PACKAGING where ART_ID='$art_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){
		$weight=$db->result($r,0,"WEIGHT_BRUTTO");
		$weight2=$db->result($r,0,"WEIGHT_NETTO");
		$volume=$db->result($r,0,"VOLUME");
	}
	return array($weight,$volume,$weight2);
}

function getArticleReservType($tpoint_id,$storage_id){$db=DbSingleton::getTokoDb();$slave=new slave; $reserv_type_id=68;
	$r=$db->query("select * from T_POINT_STORAGE where `tpoint_id`='$tpoint_id' and status='1' and `storage_id`='$storage_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){
		$local=$db->result($r,0,"local");
		if ($local==41){$reserv_type_id=67;}
	}if ($n==0){$reserv_type_id=68;}
	return $reserv_type_id;
}

function getArticleRestTpoint($art_id,$tpoint_id){$db=DbSingleton::getTokoDb();$slave=new slave; $stock=0;$reserv=0;
	$r=$db->query("select SUM(t2as.`AMOUNT`) as stock, SUM(t2as.`RESERV_AMOUNT`) as reserv,t2as.STORAGE_ID from T2_ARTICLES_STRORAGE t2as left outer join T_POINT_STORAGE tps on tps.storage_id=t2as.STORAGE_ID where t2as.ART_ID='$art_id' and tps.`tpoint_id`='$tpoint_id' and tps.status='1';");$n=$db->num_rows($r);
	for ($i=1;$i<=$n;$i++){
		$stock+=$db->result($r,$i-1,"stock");
		$reserv+=$db->result($r,$i-1,"reserv");
		$storage_id=$db->result($r,0,"STORAGE_ID");
	}
	return array($stock,$reserv,$storage_id);
}

function getArticleRestNotTpoint($art_id,$tpoint_id){$db=DbSingleton::getTokoDb();$slave=new slave; $stock=0;$reserv=0;
	$r=$db->query("select SUM(t2as.`AMOUNT`) as stock, SUM(t2as.`RESERV_AMOUNT`) as reserv,t2as.STORAGE_ID from T2_ARTICLES_STRORAGE t2as left outer join T_POINT_STORAGE tps on tps.storage_id=t2as.STORAGE_ID where t2as.ART_ID='$art_id' and tps.`tpoint_id`!='$tpoint_id' and tps.status='1';");$n=$db->num_rows($r);
	for ($i=1;$i<=$n;$i++){
		$stock+=$db->result($r,$i-1,"stock");
		$reserv+=$db->result($r,$i-1,"reserv");
		$storage_id=$db->result($r,0,"STORAGE_ID");
	}
	return array($stock,$reserv,$storage_id);
}

function getArticleRestStorage($art_id,$storage_id){$db=DbSingleton::getTokoDb();$slave=new slave; $stock=0;$reserv=0;if ($storage_id==""){$storage_id=0;}if ($cell_id==""){$cell_id=0;}
	$r=$db->query("select SUM(`AMOUNT`) as stock, SUM(`RESERV_AMOUNT`) as reserv from T2_ARTICLES_STRORAGE where ART_ID='$art_id' and `STORAGE_ID`='$storage_id';");$n=$db->num_rows($r);
	for ($i=1;$i<=$n;$i++){
		$stock+=$db->result($r,$i-1,"stock");
		$reserv+=$db->result($r,$i-1,"reserv");
	}
	return array($stock,$reserv);
}
function getArticleRestStorageCell($art_id,$storage_id,$cell_id){$db=DbSingleton::getTokoDb();$slave=new slave; $stock=0;$reserv=0;if ($storage_id==""){$storage_id=0;}if ($cell_id==""){$cell_id=0;}
	$r=$db->query("select `AMOUNT` as stock, `RESERV_AMOUNT` as reserv from T2_ARTICLES_STRORAGE_CELLS where ART_ID='$art_id' and `STORAGE_ID`='$storage_id' and `STORAGE_CELLS_ID`='$cell_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){
		$stock=$db->result($r,0,"stock");
		$reserv=$db->result($r,0,"reserv");
	}
	return array($stock,$reserv);
}


function loadback_clientsStorage($back_id){$db=DbSingleton::getDb();$slave=new slave;$gmanual=new gmanual;$income=new income;$list="";
	$form_htm=RD."/tpl/back_clients_storage_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	
	$r=$db->query("select storage_id,storage_cells_id from J_INCOME where `id`='$back_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){
		$storage_id=$db->result($r,0,"storage_id");
		$storage_cells_id=$db->result($r,0,"storage_cells_id");
	}
	$form=str_replace("{back_id}",$back_id,$form);
	$form=str_replace("{storage_list}",$this->showStorageSelectList($storage_id),$form);
	$form=str_replace("{storage_cells_list}",$this->showStorageCellsSelectList($storage_id,$storage_cells_id),$form);
	return $form;
}


function getStorageName($sel_id){$db=DbSingleton::getTokoDb();$name="";
	$r=$db->query("select name from `STORAGE` where status='1' and id='$sel_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){$name=$db->result($r,0,"name");}
	return $name;	
}
	

function setBackClientsTpointStorage($back_id,$tpoint_id,$storage_id){$db=DbSingleton::getDb();$answer="Помилка!";
	$r=$db->query("update J_BACK_CLIENTS set tpoint_id='$tpoint_id', storage_id='$storage_id' where id='$back_id' limit 1;");
	$answer="";
	return $answer;	
}
	
function showStorageSelectListByTpoint($tpoint_id,$sel_id){$db=DbSingleton::getTokoDb();$list="<option value=0>Оберіть зі списку</option>";
	$query="select s.* from `STORAGE` s left outer join T_POINT_STORAGE t on t.storage_id=s.id where s.status='1' and t.status=1 and t.tpoint_id='$tpoint_id' and t.local=41 order by s.name,s.id asc;";
	$r=$db->query($query);$n=$db->num_rows($r);
	for ($i=1;$i<=$n;$i++){
		$id=$db->result($r,$i-1,"id");
		$name=$db->result($r,$i-1,"name");
		$sel="";if ($sel_id==$id){$sel="selected='selected'";}
		$list.="<option value='$id' $sel>$name</option>";
	}
	return $list;	
}
	
function showStorageSelectList($sel_id,$cells_only=0){$db=DbSingleton::getTokoDb();$list="<option value=0>Оберіть зі списку</option>";
	$query="select * from `STORAGE` where status='1' order by name,id asc;";
	if ($cells_only==1){
		$query="select s.* from `STORAGE` s inner join STORAGE_STR ss on ss.storage_id=s.id where s.status='1' group by ss.storage_id order by s.name,s.id asc;";
	}
	$r=$db->query($query);$n=$db->num_rows($r);
	for ($i=1;$i<=$n;$i++){
		$id=$db->result($r,$i-1,"id");
		$name=$db->result($r,$i-1,"name");
		$sel="";if ($sel_id==$id){$sel="selected='selected'";}
		$list.="<option value='$id' $sel>$name</option>";
	}
	return $list;	
}
function getStorageCellName($sel_id){$db=DbSingleton::getTokoDb();$name="";
	$r=$db->query("select cell_value from `STORAGE_CELLS` where status='1' and id='$sel_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){$name=$db->result($r,0,"cell_value");}
	return $name;	
}
function showStorageCellsSelectList($storage_id,$sel_id){$db=DbSingleton::getTokoDb();$list="<option value=0>Оберіть зі списку</option>";$cells_show=1;
	$r=$db->query("select * from `STORAGE_CELLS` where status='1' and storage_id='$storage_id' order by cell_value,id asc;");$n=$db->num_rows($r);
	if ($n==0){$cells_show=0;}
	for ($i=1;$i<=$n;$i++){
		$id=$db->result($r,$i-1,"id");
		$cell_value=$db->result($r,$i-1,"cell_value");
		$sel="";if ($sel_id==$id){$sel="selected='selected'";}
		$list.="<option value='$id' $sel>$cell_value</option>";
	}
	return array($list,$cells_show);	
}

function getCashAbr($sel_id){$db=DbSingleton::getDb();$name="грн";
	$r=$db->query("select abr from CASH where id='$sel_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){$name=$db->result($r,0,"abr");}
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

function getDocTypeSelectList($sel_id){$db=DbSingleton::getDb();$list="<option value=0>Оберіть зі списку</option>";
	$r=$db->query("select id,mcaption from `manual` where ison='1' and `key`='client_sale_type' order by mid,id asc;");$n=$db->num_rows($r);
	for ($i=1;$i<=$n;$i++){
		$id=$db->result($r,$i-1,"id");
		$name=$db->result($r,$i-1,"mcaption");
		$sel="";if ($sel_id==$id){$sel="selected='selected'";}
		$list.="<option value='$id' $sel>$name</option>";
	}
	return $list;	
}
function getCarrierSelectList($sel_id){$db=DbSingleton::getDb();$list="<option value=0>Оберіть зі списку</option>";$cells_show=1;
	$r=$db->query("select id,name from `M_CARRIER` where status='1' order by id asc;");$n=$db->num_rows($r);
	for ($i=1;$i<=$n;$i++){
		$id=$db->result($r,$i-1,"id");
		$name=$db->result($r,$i-1,"name");
		$sel="";if ($sel_id==$id){$sel="selected='selected'";}
		$list.="<option value='$id' $sel>$name</option>";
	}
	return $list;	
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

function saveBackClientsStorage($back_id,$storage_id,$storage_cells_id){$db=DbSingleton::getDb();$slave=new slave;session_start();$media_user_id=$_SESSION["media_user_id"];$media_user_name=$_SESSION["user_name"];$answer=0;$err="Помилка збереження даних!";
	$back_id=$slave->qq($back_id);$storage_id=$slave->qq($storage_id);$storage_cells_id=$slave->qq($storage_cells_id);
	if ($back_id>0 && $storage_id>0 && $storage_cells_id>0){
		$db->query("update J_INCOME set storage_id='$storage_id', `storage_cells_id`='$storage_cells_id' where id='$back_id';");
		$answer=1;$err="";
	}
	return array($answer,$err);
}

function getRateTypeDeclarationdocumentPos($costums_id,$country_id){$db=DbSingleton::getDb();$slave=new slave;$manual=new manual;$rate=0;$type_declaration="";$type_declaration_id=0;
	$r=$db->query("select DUTY from T2_COUNTRIES where country_id='$country_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){
		$duty=$db->result($r,0,"DUTY");
	}
	$r=$db->query("select PREFERENTIAL_RATE,FULL_RATE,TYPE_DECLARATION from T2_COSTUMS where costums_id='$costums_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){
		$preferential_rate=$db->result($r,0,"PREFERENTIAL_RATE");
		$full_rate=$db->result($r,0,"FULL_RATE");
		$rate=$preferential_rate; if ($duty==2){$rate=$full_rate;}
		$type_declaration_id=$db->result($r,0,"TYPE_DECLARATION");
		$type_declaration=$manual->getManualMCaption("costums_type_declaration",$type_declaration_id);
		
	}
	return array($rate,$type_declaration,$type_declaration_id);
}

function loadBackClientsCommets($back_id){$db=DbSingleton::getDb();$slave=new slave;
	$form_htm=RD."/tpl/back_clients_comment_block.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$r=$db->query("select cc.*,u.name from J_BACK_CLIENTS_COMMENTS cc 
		left outer join media_users u on u.id=cc.USER_ID 
		where cc.back_id='$back_id' order by id desc;");$n=$db->num_rows($r);$list="";
	for ($i=1;$i<=$n;$i++){
		$id=$db->result($r,$i-1,"id");
		$user_id=$db->result($r,$i-1,"user_id");
		$user_name=$db->result($r,$i-1,"name");
		$data=$db->result($r,$i-1,"data");
		$comment=$db->result($r,$i-1,"comment");
		
		$block=$form;
		$block=str_replace("{back_id}",$back_id,$block);
		$block=str_replace("{id}",$id,$block);
		$block=str_replace("{user_id}",$user_id,$block);
		$block=str_replace("{user_name}",$user_name,$block);
		$block=str_replace("{data}",$data,$block);
		$block=str_replace("{comment}",$comment,$block);
		$list.=$block;
	}
	if ($n==0){$list="<h3 class='text-center'>Коментарі відсутні</h3>";}
	return $list;
}
function saveBackClientsComment($back_id,$comment){$db=DbSingleton::getDb();$slave=new slave;session_start();$user_id=$_SESSION["media_user_id"];$user_name=$_SESSION["user_name"];$answer=0;$err="Помилка збереження даних!";
	$back_id=$slave->qq($back_id);$comment=$slave->qq($comment);
	if ($back_id>0 && $comment!=""){
		$r=$db->query("insert into J_BACK_CLIENTS_COMMENTS (`back_id`,`user_id`,`comment`) values ('$back_id','$user_id','$comment');");
		$answer=1;$err="";
	}
	return array($answer,$err);
}
function dropBackClientsComment($back_id,$comment_id){$db=DbSingleton::getDb();$slave=new slave;session_start();$user_id=$_SESSION["media_user_id"];$user_name=$_SESSION["user_name"];$answer=0;$err="Помилка видалення запису!";
	$back_id=$slave->qq($back_id);$comment_id=$slave->qq($comment_id);
	if ($back_id>0 && $comment_id>0){
		$r=$db->query("select * from J_BACK_CLIENTS_COMMENTS where back_id='$back_id' and id='$comment_id' limit 0,1;");$n=$db->num_rows($r);
		if ($n==1){
			$db->query("delete from J_BACK_CLIENTS_COMMENTS where back_id='$back_id' and id='$comment_id';");
			$answer=1;$err="";
		}
	}
	return array($answer,$err);
}

function loadBackClientsCDN($back_id){$db=DbSingleton::getDb();$slave=new slave;
	$form_htm=RD."/tpl/back_clients_cdn_block.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$r=$db->query("select cc.*,u.name as user_name from J_BACK_CLIENTS_CDN cc 
		left outer join media_users u on u.id=cc.USER_ID 
		where cc.back_id='$back_id' and cc.status='1' order by cc.file_name asc;");$n=$db->num_rows($r);$list="";
		for ($i=1;$i<=$n;$i++){
			$file_id=$db->result($r,$i-1,"id");
			$user_id=$db->result($r,$i-1,"user_id");
			$file_key=$db->result($r,$i-1,"file_key");
			$file_name=$db->result($r,$i-1,"file_name");
			$name=$db->result($r,$i-1,"name");
			$data=$db->result($r,$i-1,"data");
			$comment=$db->result($r,$i-1,"comment");
			$user_name=$db->result($r,$i-1,"user_name");
			
			
			$link="http://cdn.myparts.pro/back_clients_files/$back_id/$file_name";
			
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
			$block=str_replace("{back_id}",$back_id,$block);
			$block=str_replace("{link}",$link,$block);
			$block=str_replace("{file_view}",$file_view,$block);
			
			$list.=$block;
			
		}
		if ($n==0){$list="<h3 class='text-center'>Файли відсутні</h3>";}
		return $list;
}

function BackClientsCDNDropFile($back_id,$file_id){$db=DbSingleton::getDb();$slave=new slave;session_start();$user_id=$_SESSION["media_user_id"];$user_name=$_SESSION["user_name"];$answer=0;$err="Помилка видалення файлу!";
	
	$back_id=$slave->qq($back_id);$file_id=$slave->qq($file_id);
	if ($back_id>0 && $file_id>0){
		$r=$db->query("select FILE_NAME from J_BACK_CLIENTS_CDN where back_id='$back_id' and id='$file_id' limit 0,1;");$n=$db->num_rows($r);
		if ($n==1){
			$file_name=$db->result($r,0,"file_name");
			unlink(RD.'/cdn/back_clients_files/$back_id/$file_name');
			$r=$db->query("delete from J_BACK_CLIENTS_CDN where back_id='$back_id' and id='$file_id';");
			$answer=1;$err="";
		}
	}
	return array($answer,$err);
}

function getBrandIdByArtId($art_id){$db=DbSingleton::getTokoDb();$brand_id=0; 
	$r=$db->query("select brand_id from T2_ARTICLES where ART_ID='$art_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){	$brand_id=$db->result($r,0,"BRAND_ID");	}
	return $brand_id;	
}
function getArtId($code,$brand_id){$db=DbSingleton::getTokoDb();$slave=new slave;$cat=new catalogue;$id=0; $code=$slave->qq($code); $code=$cat->clearArticle($code);
	$r=$db->query("select ART_ID from T2_ARTICLES where ARTICLE_NR_SEARCH='$code' and BRAND_ID='$brand_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){	$id=$db->result($r,0,"ART_ID");	}
	return $id;	
}
function getBrandId($code){$db=DbSingleton::getTokoDb();$slave=new slave;$id=0; $code=$slave->qq($code);
	$r=$db->query("select BRAND_ID from T2_BRANDS where BRAND_NAME='$code' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){	$id=$db->result($r,0,"BRAND_ID");	}
	return $id;	
}
function getBrandName($id){$db=DbSingleton::getTokoDb();$slave=new slave;$name=""; 
	$r=$db->query("select BRAND_NAME from T2_BRANDS where BRAND_ID='$id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){	$name=$db->result($r,0,"BRAND_NAME");	}
	return $name;	
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

function showWorkPairForm($back_id){$db=DbSingleton::getDb();$list="";
	$r=$db->query("select PAIR_INDEX from T2_WORK_PAIR where ART_ID='$back_id';");$n=$db->num_rows($r);
	for ($i=1;$i<=$n+3;$i++){
		$pair_index="";
		if ($i<=$n){$pair_index=$db->result($r,$i-1,"PAIR_INDEX");}
		$list.="<tr><td><input type='text' id='work_pair_$i' value='$pair_index' class='form-control'></td></tr>";
	}$list.="<input type='hidden' id='work_pair_n' value='".($n+3)."'>";
	return $list;
}
function labelArtEmptyCount($back_id,$kol){$db=DbSingleton::getDb();$slave=new slave;$label="";
	if ($kol==0 || $kol==""){ 
		list($weight,$volume,$kol)=$this->updateBackClientsWeightVolume($back_id);
	}
	if ($kol>0){$label="<span class='label label-tab label-info'>$kol</span>";}
	return array($kol,$label);
}

function labelCommentsCount($back_id){$db=DbSingleton::getDb();$slave=new slave;$kol=0;$label="";
	$r=$db->query("select count(id) as kol from J_BACK_CLIENTS_COMMENTS where back_id='$back_id';");$kol=0+$db->result($r,0,"kol");
	if ($kol>0){$label="<span class='label label-tab label-info'>$kol</span>";}
	return array($kol,$label);
}


function getStorageToTpointLocal($tpoint_id,$storage_id){$db=DbSingleton::getDb();$slave=new slave;$local=42;
	$r=$db->query("select `local` from T_POINT_STORAGE where tpoint_id='$tpoint_id' and storage_id='$storage_id' and status='1' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){$local=$db->result($r,0,"local");}
	return $local;
}
function getTpointStorageLocal($tpoint_id){$db=DbSingleton::getDb();$storage_id=0;
	$r=$db->query("select storage_id from T_POINT_STORAGE where  tpoint_id='$tpoint_id' and `local`='41' and status='1' limit 0,1");$n=$db->num_rows($r);
	if ($n==1){$storage_id=$db->result($r,0,"storage_id");}
	return $storage_id;
}

//======================================================================================

function updateStockFromStorage($art_id,$storage_id_from,$cell_id_from,$cell_use,$amount){$dbt=DbSingleton::getTokoDb(); $er=1;
	$r=$dbt->query("select `RESERV_AMOUNT` from T2_ARTICLES_STRORAGE where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id_from' limit 0,1;");$n=$dbt->num_rows($r);
	if ($n==1){
		$t2s_reserv_amount=$dbt->result($r,0,"RESERV_AMOUNT");
		if ($amount<=$t2s_reserv_amount){
			$t2s_reserv_amount=$t2s_reserv_amount-$amount;
			$dbt->query("update T2_ARTICLES_STRORAGE set `RESERV_AMOUNT`='$t2s_reserv_amount' where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id_from' limit 1;");
			if ($cell_use==1){
				$r1=$dbt->query("select `RESERV_AMOUNT` from T2_ARTICLES_STRORAGE_CELLS where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id_from' and `STORAGE_CELLS_ID`='$cell_id_from' limit 0,1;");$n1=$dbt->num_rows($r1);
				if ($n1==1){
					$t2sc_reserv_amount=$dbt->result($r1,0,"RESERV_AMOUNT");
					if ($amount>0){ 
						$t2sc_reserv_amount=$t2sc_reserv_amount-$amount;
						$dbt->query("update T2_ARTICLES_STRORAGE_CELLS set `RESERV_AMOUNT`='$t2sc_reserv_amount' where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id_from' and `STORAGE_CELLS_ID`='$cell_id_from' limit 1;");
					}
				}
			}
		}
		$er=0;
	}
	return $er;
}

function updateStockToStorage($art_id,$storage_id_to,$cell_id_to,$cell_use,$amount){$dbt=DbSingleton::getTokoDb(); $er=1;
	$r=$dbt->query("select `AMOUNT` from T2_ARTICLES_STRORAGE where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id_to' limit 0,1;");$n=$dbt->num_rows($r);
	if ($n==0){
		$dbt->query("insert into T2_ARTICLES_STRORAGE (`ART_ID`,`AMOUNT`,`RESERV_AMOUNT`,`STORAGE_ID`) values ('$art_id','$amount','0','$storage_id_to');");
		if ($cell_use==1){
			$dbt->query("insert into T2_ARTICLES_STRORAGE_CELLS (`ART_ID`,`AMOUNT`,`RESERV_AMOUNT`,`STORAGE_ID`,`STORAGE_CELLS_ID`) values ('$art_id','$amount','0','$storage_id_to','$cell_id_to');");
		}
		$er=0;
	}
	if ($n==1){
		$t2s_amount=$dbt->result($r,0,"AMOUNT");
		if ($amount>0){ $t2s_amount=$t2s_amount+$amount;
			$dbt->query("update T2_ARTICLES_STRORAGE set `AMOUNT`='$t2s_amount' where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id_to' limit 1;");
			if ($cell_use==1){
				$r1=$dbt->query("select `AMOUNT` from T2_ARTICLES_STRORAGE_CELLS where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id_to' and `STORAGE_CELLS_ID`='$cell_id_to' limit 0,1;");$n1=$dbt->num_rows($r1);
				if ($n1==0){
					$dbt->query("insert into T2_ARTICLES_STRORAGE_CELLS (`ART_ID`,`AMOUNT`,`RESERV_AMOUNT`,`STORAGE_ID`,`STORAGE_CELLS_ID`) values ('$art_id','$amount','0','$storage_id_to','$cell_id_to');");
				}
				if ($n1==1){
					$t2sc_amount=$dbt->result($r1,0,"AMOUNT");
					if ($amount>0){ 
						$t2sc_amount=$t2sc_amount+$amount;
						$dbt->query("update T2_ARTICLES_STRORAGE_CELLS set `AMOUNT`='$t2sc_amount' where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id_to' and `STORAGE_CELLS_ID`='$cell_id_to' limit 1;");
					}
				}
			}
			$er=0;
		}
	}
	return $er;
}

function updateStockFromStorageLocal($art_id,$storage_id_from,$cell_id_from,$cell_id_to,$amount){$dbt=DbSingleton::getTokoDb(); $er=1;
	$r=$dbt->query("select `AMOUNT`,`RESERV_AMOUNT` from T2_ARTICLES_STRORAGE where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id_from' limit 0,1;");$n=$dbt->num_rows($r);
	if ($n==1){
		$t2s_amount=$dbt->result($r,0,"AMOUNT");
		$t2s_reserv_amount=$dbt->result($r,0,"RESERV_AMOUNT");
		if ($amount<=$t2s_reserv_amount){
			$t2s_reserv_amount=$t2s_reserv_amount-$amount;
			$t2s_amount=$t2s_amount+$amount;
			$dbt->query("update T2_ARTICLES_STRORAGE set `RESERV_AMOUNT`='$t2s_reserv_amount',`AMOUNT`='$t2s_amount' where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id_from' limit 1;");

			$r1=$dbt->query("select `RESERV_AMOUNT` from T2_ARTICLES_STRORAGE_CELLS where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id_from' and `STORAGE_CELLS_ID`='$cell_id_from' limit 0,1;");$n1=$dbt->num_rows($r1);
			if ($n1==1){
				$t2sc_reserv_amount=$dbt->result($r1,0,"RESERV_AMOUNT");
				if ($amount>0){ 
					$t2sc_reserv_amount=$t2sc_reserv_amount-$amount;
					$dbt->query("update T2_ARTICLES_STRORAGE_CELLS set `RESERV_AMOUNT`='$t2sc_reserv_amount' where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id_from' and `STORAGE_CELLS_ID`='$cell_id_from' limit 1;");
				}
			}
			$r2=$dbt->query("select `AMOUNT` from T2_ARTICLES_STRORAGE_CELLS where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id_from' and `STORAGE_CELLS_ID`='$cell_id_to' limit 0,1;");$n2=$dbt->num_rows($r2);
				if ($n2==0){
					$dbt->query("insert into T2_ARTICLES_STRORAGE_CELLS (`ART_ID`,`AMOUNT`,`RESERV_AMOUNT`,`STORAGE_ID`,`STORAGE_CELLS_ID`) values ('$art_id','$amount','0','$storage_id_from','$cell_id_to');");
				}
				if ($n2==1){
					$t2sc_amount2=$dbt->result($r2,0,"AMOUNT");
					if ($amount>0){ 
						$t2sc_amount2=$t2sc_amount2+$amount;
						$dbt->query("update T2_ARTICLES_STRORAGE_CELLS set `AMOUNT`='$t2sc_amount2' where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id_from' and `STORAGE_CELLS_ID`='$cell_id_to' limit 1;");
					}
				}
		}
		$er=0;
	}
	return $er;
}
function getStorageDefaultCell($storage_id){$db=DbSingleton::getDb();	$cell_use=0;$cell_id=0;
	$r=$db->query("select id from STORAGE_CELLS where storage_id='$storage_id' and status='1' and `default`='1' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){ $cell_use=1;
		$cell_id=$db->result($r,0,"id");
	}
	if ($n==0){
		$r=$db->query("select id from STORAGE_CELLS where storage_id='$storage_id' and status='1' order by id asc limit 0,1;");$n=$db->num_rows($r);
		if ($n==1){ $cell_use=1;
			$cell_id=$db->result($r,0,"id");
		}	
	}
	return array($cell_use,$cell_id);
}

function getSaleInvoiceDocType($id){$db=DbSingleton::getDb(); $doc_type_id=0;
	$r=$db->query("select doc_type_id from J_SALE_INVOICE where id='$id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){$doc_type_id=$db->result($r,0,"doc_type_id");}
	return $doc_type_id;
}
	
function acceptBackClients($back_id){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$slave=new slave; $cat=new catalogue; session_start(); $user_id=$_SESSION["media_user_id"]; $user_name=$_SESSION["user_name"]; $answer=0;$err="Помилка обробки даних!";
	$back_id=$slave->qq($back_id);
	if ($back_id>0){
		$r=$db->query("select * from J_BACK_CLIENTS where id='$back_id' and status='1' limit 0,1;");$n=$db->num_rows($r);
		if ($n==1){
			$prefix=$db->result($r,0,"prefix");
			$status_back=$db->result($r,0,"status_back");
			$summ_back=$db->result($r,0,"summ");
			$sale_invoice_id=$db->result($r,0,"sale_invoice_id");
			$storage_id_back=$db->result($r,0,"storage_id");
			$client_id=$db->result($r,0,"client_id");
			
			if ($summ_back>0 && $status_back==102){
				$sale_invoice_prefix=$this->getSaleInvoicePrefix($sale_invoice_id);
				$db->query("update J_BACK_CLIENTS set status_back='103',prefix='$prefix".$sale_invoice_prefix."' where id='$back_id' and status='1' and status_back=102 limit 1;");
				
				//возвращаем финансы
				
				list($summ_invoice,$summ_debit)=$this->getSaleInvoiceSumm2($sale_invoice_id);
				$summ_avans=$summ_debit-$summ_back;
				
				$jpay=new jpay;
				list($balans_before,$balans_before_cash_id)=$jpay->getClientGeneralSaldo($client_id);
				if ($summ_avans>0){
					
					//делаем меньше задолженность по накладной 
					//делаем меньше задолженость по балансу
					//не трогаем авансы
					
					
					//500-234,6=265
					$db->query("update J_SALE_INVOICE set summ_debit=summ_debit-$summ_back where id='$sale_invoice_id' limit 1;");
					
					$balans_after=$balans_before+$summ_back;
					//   -753.60	75.36	
										
					$db->query("insert into B_CLIENT_BALANS_JOURNAL (`client_id`, `cash_id`, `balans_before`, `deb_kre`, `summ`, `balans_after`, `doc_type_id`, `doc_id`, `pay_cash_id`, `pay_summ`) 
					values ('$client_id', '$balans_before_cash_id', '$balans_before', '2', '".abs($summ_back)."', '$balans_after', '5', '$back_id', '$balans_before_cash_id', '$summ_back');");
					
					$db->query("update B_CLIENT_BALANS set saldo=saldo+$summ_back, last_update=NOW() where client_id='$client_id';");
				}
				if ($summ_avans<=0){
					
					//делаем задолженность по накладной = 0
					//делаем меньше задолженость по балансу
					//создаем аванс
					
					//100-234,6=-134.60
					
					$db->query("update J_SALE_INVOICE set summ_debit=0 where id='$sale_invoice_id' limit 1;");
					
					$balans_after=$balans_before+$summ_back;
					$db->query("insert into B_CLIENT_BALANS_JOURNAL (`client_id`, `cash_id`, `balans_before`, `deb_kre`, `summ`, `balans_after`, `doc_type_id`, `doc_id`, `pay_cash_id`, `pay_summ`) values ('$client_id', '$balans_before_cash_id', '$balans_before', '2', '".abs($summ_back)."', '$balans_after', '5', '$back_id', '$balans_before_cash_id', '$summ_back');");
					
					
					$db->query("update B_CLIENT_BALANS set saldo=`saldo`+$summ_back, last_update=NOW() where client_id='$client_id';");
					$jpay->updateClientAvans($client_id,$balans_before_cash_id,abs($summ_avans));
				}
				
				// возвращаем товар на склад физически
				
				list($cell_use,$cell_id_back)=$this->getStorageDefaultCell($storage_id_back);
				$r1=$db->query("select * from J_BACK_CLIENTS_STR where back_id='$back_id' order by id asc;");$n1=$db->num_rows($r1);
				for ($i1=1;$i1<=$n1;$i1++){
					$art_id_back=$db->result($r1,$i1-1,"art_id");
					$amount_back=$db->result($r1,$i1-1,"amount");
					$back_si_str_id=$db->result($r1,$i1-1,"sale_invoice_str_id");
					
					//RETURN TO STORAGE
					$rs=$dbt->query("select AMOUNT from T2_ARTICLES_STRORAGE where ART_ID ='$art_id_back' and STORAGE_ID='$storage_id_back' limit 0,1;");$ns=$dbt->num_rows($rs); 
					if ($ns==1){
						$amount_storage=$dbt->result($rs,0,"AMOUNT");
						$dbt->query("update T2_ARTICLES_STRORAGE set AMOUNT=AMOUNT+$amount_back where  ART_ID ='$art_id_back' and STORAGE_ID='$storage_id_back' limit 1");
						if ($cell_use==1){
							$dbt->query("update T2_ARTICLES_STRORAGE_CELLS set AMOUNT=AMOUNT+$amount_back where  ART_ID ='$art_id_back' and STORAGE_ID='$storage_id_back' and STORAGE_CELLS_ID='$cell_id_back' limit 1");
						}
					}
					if ($ns==0){
						$dbt->query("insert into T2_ARTICLES_STRORAGE (ART_ID,AMOUNT,RESERV_AMOUNT,STORAGE_ID) values ('$art_id_back','$amount_back','0','$storage_id_back');");
						if ($cell_use==1){
							$dbt->query("insert into T2_ARTICLES_STRORAGE_CELLS (ART_ID,AMOUNT,RESERV_AMOUNT,STORAGE_ID,STORAGE_CELLS_ID) values ('$art_id_back','$amount_back','0','$storage_id_back','$cell_id_back');");
						}
						
					}
					$dbt->query("update T2_ARTICLES_PRICE_STOCK set GENERAL_STOCK=GENERAL_STOCK+$amount_back where ART_ID='$art_id_back' limit 1;");
					
					$slave->addJuornalArtDocs(4,$back_id,$art_id_back,$amount_back);
					
					//Возвращаем товар в партии
					
					$rp=$db->query("select * from J_SALE_INVOICE_PARTITION_STR where invoice_id='$sale_invoice_id' and invoice_str_id='$back_si_str_id' and art_id='$art_id_back' order by id desc;");$np=$db->num_rows($rp);
					if ($np>0){
						$amount_back_partition=$amount_back; //print "np=$np\n";
						for ($ip=1;$ip<=$np;$ip++){
							if ($amount_back_partition>0){
								//print "i=$ip;\n";
								$partition_str_id=$db->result($rp,$ip-1,"id");
								$pratition_article_nr_displ=$db->result($rp,$ip-1,"article_nr_displ");
								$pratition_brand_id=$db->result($rp,$ip-1,"brand_id");
								$partition_id=$db->result($rp,$ip-1,"partition_id");
								//print "partition_id=$partition_id; partition_str_id=$partition_str_id\n";
								$partition_amount=$db->result($rp,$ip-1,"partition_amount");
								$oper_price_partition=$db->result($rp,$ip-1,"oper_price_partition");
								$price_partition=$db->result($rp,$ip-1,"price_partition");
								$price_buh_uah=$db->result($rp,$ip-1,"price_buh_uah");
								$price_man_uah=$db->result($rp,$ip-1,"price_man_uah");
								$price_invoice=$db->result($rp,$ip-1,"price_invoice");
								$op=0;
								//print "partition_amount=$partition_amount; amount_back_partition=$amount_back_partition\n";
								$ri=$db->query("select parrent_doc_id from T2_ARTICLES_PARTITIONS where id='$partition_id' limit 0,1;");$ni=$db->num_rows($ri);
								if ($ni==1){
									$income_id=$db->result($ri,0,"parrent_doc_id");
								}
								//10>6
								if ($amount_back_partition>$partition_amount){
									$db->query("update T2_ARTICLES_PARTITIONS set rest=rest+$partition_amount where id='$partition_id' limit 1;");//print "back>=\n $amount_back_partition>=$partition_amount\n";
									$amount_back_partition-=$partition_amount; $op=1; //10-6=4
									$db->query("update J_SALE_INVOICE_PARTITION_STR set partition_amount=partition_amount-$partition_amount where id='$partition_str_id';");
									list($oper_price,$general_stock)=$cat->getArticleOperPriceGeneralStock($art_id_back);
									$price_man_usd=$this->getArticlePriceManUsd($art_id_back,$income_id);
									$new_oper_price=round((($oper_price*$general_stock)+($partition_amount*$price_man_usd))/($partition_amount+$general_stock),2);
									$new_general_stock=$partition_amount+$general_stock;
									$cat->setArticleOperPriceGeneralStock($art_id,$new_oper_price,$new_general_stock);
								}
								// 10<=6
								if ($amount_back_partition<=$partition_amount && $op==0){
									$db->query("update T2_ARTICLES_PARTITIONS set rest=rest+$amount_back_partition where id='$partition_id' limit 1;");
									//print "back<\n $amount_back_partition<$partition_amount\n";
									$db->query("update J_SALE_INVOICE_PARTITION_STR set partition_amount=partition_amount-$amount_back_partition where id='$partition_str_id';");
									list($oper_price,$general_stock)=$cat->getArticleOperPriceGeneralStock($art_id_back);
									$price_man_usd=$this->getArticlePriceManUsd($art_id_back,$income_id);
									$new_oper_price=round((($oper_price*$general_stock)+($amount_back_partition*$price_man_usd))/($amount_back_partition+$general_stock),2);
									$new_general_stock=$amount_back_partition+$general_stock;
									$cat->setArticleOperPriceGeneralStock($art_id,$new_oper_price,$new_general_stock);
									//print "$amount_back_partition-=$partition_amount;\n";
									$amount_back_partition-=$partition_amount; $op=1;
								}
								//print "amount_back_partition=$amount_back_partition\n";
							}
						}
					}
				}
				$answer=1;$err="";
			}
			
		}
	}
	return array($answer,$err);
}
function getArticlePriceManUsd($art_id,$income_id){$db=DbSingleton::getDb();$price_man_usd=0;
	$r=$db->query("select price_man_usd from J_INCOME_STR where art_id='$art_id' and income_id='$income_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){ $price_man_usd=$db->result($r,0,"price_man_usd"); }
	return $price_man_usd;
}	
	
//===============			Print BACKCLIENTS 		==================================

function printBackClientsN1($back_id){$db=DbSingleton::getDb();$cat=new catalogue;$slave=new slave;$manual=new manual;session_start();$user_id=$_SESSION["media_user_id"];$user_name=$_SESSION["user_name"]; $gmanual=new gmanual; $money=new toMoney; $cat=new catalogue;
									  
	$r=$db->query("select j.*, sl.full_name as seller_name, sld.edrpou, sld.account, sld.vat, sld.bank, sld.mfo, ot.name as org_type_abr, ch.abr2 as cash_abr from J_BACK_CLIENTS j
					left outer join J_SALE_INVOICE si on si.id=j.sale_invoice_id
					left outer join A_CLIENTS sl on sl.id=si.seller_id
					left outer join A_CLIENT_DETAILS sld on sld.client_id=si.seller_id
					left outer join A_ORG_TYPE ot on ot.id=sl.org_type
					left outer join CASH ch on ch.id=j.cash_id
					
	where j.id='$back_id' and j.status_back=103 and j.status=1 limit 0,1;");$n=$db->num_rows($r);
	if ($n==0){$form_htm=RD."/tpl/access_deny.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);} }
	if ($n==1){ 
		
		
		$sale_invoice_id=$db->result($r,0,"sale_invoice_id"); 
		$sale_invoice_name=$this->getSaleInvoiceName($sale_invoice_id);
		$doc_type_id=$this->getSaleInvoiceDocType($sale_invoice_id);
		
		$form="";
		if ($doc_type_id==61) $form_htm=RD."/tpl/back_clients_print_n1.htm"; //БНП
		if ($doc_type_id==63) $form_htm=RD."/tpl/back_clients_print_n2.htm"; //ТЧ
		if ($doc_type_id==64) $form_htm=RD."/tpl/back_clients_print_n3.htm"; //БК
		if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
		
		$prefix=$db->result($r,0,"prefix");
		$doc_nom=$db->result($r,0,"doc_nom");
		$data=$db->result($r,0,"data");if ($data=="0000-00-00"){$data=date("d.m.Y");}
		$cash_id=$db->result($r,0,"cash_id");$cash_name=$this->getCashAbr($cash_id);
		$data=date("d.m.Y");
		$tpoint_id=$db->result($r,0,"tpoint_id"); $tpoint_name=$this->getTpointName($tpoint_id);
		$client_id=$db->result($r,0,"client_id");$client_name=$this->getClientName($client_id);
		$storage_id=$db->result($r,0,"storage_id");$storage_name=$this->getStorageName($storage_id);
		$summ=$db->result($r,0,"summ");
		$status_back=$db->result($r,0,"status_back");
		$status_back_cap=$gmanual->get_gmanual_caption($status_back);
		$seller_name=$db->result($r,0,"seller_name");
		$edrpou=$db->result($r,0,"edrpou");
		$org_type_abr=$db->result($r,0,"org_type_abr");
		$cash_abr=$db->result($r,0,"cash_abr");
		$account=$db->result($r,0,"account");
		$bank=$db->result($r,0,"bank");
		$mfo=$db->result($r,0,"mfo");
		$vat=$db->result($r,0,"vat");
		
		$form=str_replace("{curtime}",date("d.m.Y H:i:s"),$form);
		$form=str_replace("{seller_name}",$seller_name,$form);
		$form=str_replace("{edrpou}",$edrpou,$form);
		$form=str_replace("{org_type_abr}",$org_type_abr,$form);
			
		$form=str_replace("{back_id}",$back_id,$form);
		$form=str_replace("{prefix}",$prefix,$form);
		$form=str_replace("{doc_nom}",$doc_nom,$form);
	
		$form=str_replace("{data}",$data,$form);
		$form=str_replace("{cash_name}",$cash_name,$form);
		$form=str_replace("{back_summ}",$summ,$form);
		$form=str_replace("{back_summ_word}",$money->num2str($summ),$form);
		$form=str_replace("{sale_invoice_name}",$sale_invoice_name,$form);
		$form=str_replace("{storage_name}",$storage_name,$form);
		$form=str_replace("{tpoint_name}",$tpoint_name,$form);
		$form=str_replace("{client_id}",$client_id,$form);
		$form=str_replace("{client_name}",$client_name,$form);
		$form=str_replace("{status_back_cap}",$status_back_cap,$form);
		$form=str_replace("{cash_abr}",$cash_abr,$form);
		$form=str_replace("{rr}",$account,$form);
		$form=str_replace("{bank}",$bank,$form);
		$form=str_replace("{mfo}",$mfo,$form);
		$form=str_replace("{ipn_nom}",$vat,$form);
		$vat_summ=$summ/6;
		$form=str_replace("{vat_summ}",round($vat_summ,2),$form);
		
		$r=$db->query("select * from J_BACK_CLIENTS_STR where back_id='$back_id' order by id asc;");$n=$db->num_rows($r);
		for ($i=1;$i<=$n;$i++){
			$art_id=$db->result($r,$i-1,"art_id");
			$article_nr_displ=$db->result($r,$i-1,"article_nr_displ");
			$article_name=$cat->getArticleNameLang($art_id);
			$brand_id=$db->result($r,$i-1,"brand_id");$brand_name=$cat->getBrandName($brand_id);
			$amount=intval($db->result($r,$i-1,"amount"));
			$unit=$this->getUnitArticle($art_id);
			$price=$slave->to_money($db->result($r,$i-1,"price"));
			$summ=$slave->to_money($db->result($r,$i-1,"summ"));
			$list.="<tr>
				<td align='center'>$i</td>
				<td align='left'>$article_nr_displ ($brand_name)</td>
				<td align='left'>$article_name</td>
				<td align='center'>$unit</td>
				<td align='center'>$amount</td>
				<td align='right'>$price</td>
				<td align='right'>$summ</td>
			</tr>";
		}
		$form=str_replace("{list}",$list,$form);
		//"Формування друкованої форми"
		$mp=new media_print;
		//$mp->print_document($form,array(210,280));
		$mp->print_document($form,"A4-L");
		
	}
	return $form;
}
	
	function getUnitArticle($art_id) {$db=DbSingleton::getTokoDb(); $abr="";
	$r=$db->query("select t2u.abr from T2_PACKAGING t2p 
	left outer join units t2u on t2u.id=t2p.UNITS_ID
	where t2p.ART_ID='$art_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){
		$abr=$db->result($r,0,"abr");
	}
	return $abr;
}

//======================================================================================
}
?>