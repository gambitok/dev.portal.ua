<?php
class paybox{

function getMediaUserName($user_id){$db=DbSingleton::getDb();$name="";
	$r=$db->query("select name from media_users where id='$user_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){$name=$db->result($r,0,"name");}
	return $name;
}
function show_paybox_list(){$db=DbSingleton::getDb();$slave=new slave;$gmanual=new gmanual;$dp=new dp; $where="";
	$r=$db->query("select pb.*, cl.name as client_name, cst.mcaption as doc_type_name from PAY_BOX pb 
	left outer join A_CLIENTS cl on cl.id=pb.firm_id
	left outer join manual cst on cst.id=pb.doc_type_id and cst.`key`='client_sale_type'
	where pb.status=1 order by pb.firm_id asc, pb.id asc;");$n=$db->num_rows($r);$list="";
	for ($i=1;$i<=$n;$i++){
		$id=$db->result($r,$i-1,"id");
		$paybox_name=$paybox_name=$db->result($r,$i-1,"name");
		$in_use=$db->result($r,$i-1,"in_use"); $in_use_name="eye-slash"; if ($in_use==1){$in_use_name="eye";}
		$client_name=$db->result($r,$i-1,"client_name");
		$doc_type_name=$db->result($r,$i-1,"doc_type_name");
		$saldo=$this->getPayboxSaldo($id,0);
		$list.="<tr style='cursor:pointer'  onClick='showPayboxCard(\"$id\",\"$paybox_name\")'>
				<td>$i</td>
				<td align='center'>$doc_type_name</td>
				<td>$paybox_name</td>
				<td align='center'>$client_name</td>
				<td align='center'>$saldo</td>
				<td align='center'><i class='fa fa-$in_use_name'></i></td>
			</tr>";
	}
	return $list;
}
	
function newPayboxCard(){$db=DbSingleton::getDb();$slave=new slave;$manual=new manual; session_start();$user_id=$_SESSION["media_user_id"];$user_name=$_SESSION["user_name"]; $paybox_id=0;
	$r=$db->query("select max(id) as mid from PAY_BOX;");$paybox_id=0+$db->result($r,0,"mid")+1;
	//$db->query("insert into PAY_BOX (`id`) values ('$paybox_id');");
	return $paybox_id;
}

function showPayboxCard($paybox_id){$db=DbSingleton::getDb();$slave=new slave;session_start();$user_id=$_SESSION["media_user_id"];$user_name=$_SESSION["user_name"];
	$form_htm=RD."/tpl/paybox_card.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	
	$r=$db->query("select pb.*, cl.name as firm_name from PAY_BOX pb 
	left outer join A_CLIENTS cl on cl.id=pb.firm_id
	where pb.id='$paybox_id' and pb.status=1 limit 0,1;");$n=$db->num_rows($r);
//	if ($n==0){$form_htm=RD."/tpl/access_deny.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);} }
	if ($n==0){
		$r2=$db->query("select max(id) as mid from PAY_BOX;"); $paybox_id=0+$db->result($r2,0,"mid")+1;
		$form=str_replace("{paybox_id}",$paybox_id,$form);
		$form=str_replace("{paybox_name}","",$form);
		$form=str_replace("{paybox_full_name}","",$form);
		$form=str_replace("{firm_id}","",$form);
		$form=str_replace("{firm_name}","",$form);
		$form=str_replace("{doc_type_list}",$this->getDocTypeSelectList(1),$form);
		$form=str_replace("{inuse_checked}","",$form);
		$form=str_replace("{my_user_id}",$user_id,$form);
		$form=str_replace("{my_user_name}",$user_name,$form);
		$form=str_replace("{oper_visible}"," disabled style=\"display:none;\"",$form);
	}
	
	if ($n==1){
		$paybox_id=$db->result($r,0,"id");
		$name=$db->result($r,0,"name");
		$full_name=$db->result($r,0,"full_name");
		$firm_id=$db->result($r,0,"firm_id");
		$firm_name=$db->result($r,0,"firm_name");
		$doc_type_id=$db->result($r,0,"doc_type_id");
		$doc_type_name=$db->result($r,0,"doc_type_name");
		$in_use=$db->result($r,0,"in_use"); $inuse_checked=""; if ($in_use==1){$inuse_checked=" checked"; }
		
		$form=str_replace("{paybox_id}",$paybox_id,$form);
		$form=str_replace("{paybox_name}",$name,$form);
		$form=str_replace("{paybox_full_name}",$full_name,$form);
		$form=str_replace("{firm_id}",$firm_id,$form);
		$form=str_replace("{firm_name}",$firm_name,$form);
		$form=str_replace("{doc_type_list}",$this->getDocTypeSelectList($doc_type_id),$form);
		$form=str_replace("{inuse_checked}",$inuse_checked,$form);
		
		$form=str_replace("{my_user_id}",$user_id,$form);
		$form=str_replace("{my_user_name}",$user_name,$form);
		
		$saldo=$this->getPayboxSaldo($paybox_id,0);
		if ($saldo==0)
		$form=str_replace("{oper_visible}","",$form); else
		$form=str_replace("{oper_visible}"," disabled",$form);
	}
	return $form;
}

function savePayboxGeneralInfo($paybox_id,$name,$full_name,$firm_id,$doc_type_id,$in_use){$db=DbSingleton::getDb();$slave=new slave;session_start();$user_id=$_SESSION["media_user_id"];$user_name=$_SESSION["user_name"];$answer=0;$err="������� ���������� �����!";

	$paybox_id=$slave->qq($paybox_id);$name=$slave->qq($name);$full_name=$slave->qq($full_name);$firm_id=$slave->qq($firm_id);$doc_type_id=$slave->qq($doc_type_id);$in_use=$slave->qq($in_use);
	if ($paybox_id>0){
		
		$r=$db->query("select * from PAY_BOX where id='$paybox_id';"); $n=$db->num_rows($r);
		if ($n>0) {					
			$db->query("update PAY_BOX set `name`='$name', `full_name`='$full_name', `firm_id`='$firm_id', `doc_type_id`='$doc_type_id', `in_use`='$in_use' where `id`='$paybox_id';");
			$answer=1;$err="";
		} else {			
			$db->query("insert into PAY_BOX (`name`,`full_name`,`firm_id`,`doc_type_id`,`in_use`) values ('$name','$full_name','$firm_id','$doc_type_id','$in_use');");
			$answer=1;$err="";
		}
	}
	return array($answer,$err);
}
function getDocTypeSelectList($sel_id){$db=DbSingleton::getDb();$list="<option value=0>������ � ������</option>";
	$r=$db->query("select id,mcaption from `manual` where ison='1' and `key`='client_sale_type' order by mid,id asc;");$n=$db->num_rows($r);
	for ($i=1;$i<=$n;$i++){
		$id=$db->result($r,$i-1,"id");
		$name=$db->result($r,$i-1,"mcaption");
		$sel="";if ($sel_id==$id){$sel="selected='selected'";}
		$list.="<option value='$id' $sel>$name</option>";
	}
	return $list;	
}

function showWorkersSelectList($sel_id){$db=DbSingleton::getDb();$list="";;
	$r=$db->query("select * from media_users order by name,id asc;");$n=$db->num_rows($r);
	for ($i=1;$i<=$n;$i++){
		$id=$db->result($r,$i-1,"id");
		$name=$db->result($r,$i-1,"name");
		$sel="";if ($id==$sel_id){$sel=" selected";}
		$list.="<option value='$id' $sel>$name</option>";
	}
	return $list;
}
	
	
function showPayboxClientList($sel_id){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$slave=new slave;
	$form_htm=RD."/tpl/clients_parrent_tree.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$r=$db->query("select c.*,ot.name as org_type_name, t2cn.COUNTRY_NAME, t2st.STATE_NAME, t2rg.REGION_NAME, t2ct.CITY_NAME  from A_CLIENTS c 
		left outer join A_ORG_TYPE ot on ot.id=c.org_type 
		left outer join T2_COUNTRIES t2cn on t2cn.COUNTRY_ID=c.country 
		left outer join T2_STATE t2st on t2st.STATE_ID=c.state
		left outer join T2_REGION t2rg on t2rg.REGION_ID=c.region
		left outer join T2_CITY t2ct on t2ct.CITY_ID=c.city
		left outer join A_CLIENTS_CATEGORY cc on cc.client_id=c.id
		left outer join A_CATEGORY ac on ac.id=cc.category_id
		
		where c.status=1 and ac.id=3 $where;");$n=$db->num_rows($r);$list="";
	for ($i=1;$i<=$n;$i++){
		$id=$db->result($r,$i-1,"id");
		$name=$db->result($r,$i-1,"name");
		$org_type_name=$db->result($r,$i-1,"org_type_name");
		$email=$db->result($r,$i-1,"email");
		$phone=$db->result($r,$i-1,"phone");
		$country=$slave->showTableFieldDBT("T2_COUNTRIES","NAME","ID",$db->result($r,$i-1,"country"));
		$state=$slave->showTableFieldDBT("T2_STATE","NAME","ID",$db->result($r,$i-1,"state"));
		$region=$slave->showTableFieldDBT("T2_REGION","NAME","ID",$db->result($r,$i-1,"region"));
		$city=$slave->showTableFieldDBT("T2_CITY","NAME","ID",$db->result($r,$i-1,"city"));
		$address=$db->result($r,$i-1,"address");
		$cur="";$fn="<i class='fa fa-thumb-tack' onClick='setPayboxClient(\"$id\", \"$name\")'></i>";
		if ($id==$prnt_id){$cur="style='background-color:#FFFF00'";}if ($id==$sel_id){$fn="";$cur="style='background-color:#ccc; disabled:disabled;'";}
		$list.="<tr $cur>
				<td>$fn</td>
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
	
function loadPayboxWorkersSaldo($paybox_id){$db=DbSingleton::getDb();$slave=new slave;$gmanual=new gmanual;
	$form_htm=RD."/tpl/paybox_workers_saldo_list.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$r=$db->query("select sum(saldo) as summ, cash_id, user_id, last_update from B_PAYBOX_BALANS where paybox_id='$paybox_id' group by user_id,cash_id order by user_id asc;");$n=$db->num_rows($r);$list="";
	for ($i=1;$i<=$n;$i++){
		$id=$db->result($r,$i-1,"id");
		$worker_id=$db->result($r,$i-1,"user_id");$worker_name=$this->getMediaUserName($worker_id);
		$summ=$db->result($r,$i-1,"summ");
		$cash_id=$db->result($r,$i-1,"cash_id");$cash_abr=$this->getCashAbr($cash_id);
		$last_update=$db->result($r,$i-1,"last_update");
		$list.="
		<tr>
			<td>$i</td>
			<td>$worker_name</td>
			<td>$summ</td>
			<td>$cash_abr</td>
			<td>
				<button class='btn btn-sm btn-default' onClick='showPayboxWorkerSaldoJournal(\"$paybox_id\",\"$worker_id\",\"$cash_id\");'><i class='fa fa-search'></i></button>
			</td>
		</tr>";
	}
	if ($n==0){$list="<tr><td align='center' colspan=5><h3 class='text-center'>������ ������</h3></td></tr>";}
	$form=str_replace("{list_saldo}",$list,$form);
	$form=str_replace("{paybox_id}",$paybox_id,$form);
	return $form;
}
function showPayboxWorkerSaldoJournal($paybox_id,$user_id,$cash_id){$db=DbSingleton::getDb();$slave=new slave;$gmanual=new gmanual;
	$form_htm=RD."/tpl/paybox_workers_saldo_journal.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$r=$db->query("select bj.* from B_PAYBOX_JOURNAL bj where paybox_id='$paybox_id' and cash_id='$cash_id' and user_id='$user_id' order by id desc limit 0,20;");$n=$db->num_rows($r);$list="";$worker_name=$this->getMediaUserName($user_id);
	for ($i=1;$i<=$n;$i++){
		$id=$db->result($r,$i-1,"id");
		
		$saldo_before=$db->result($r,$i-1,"saldo_before");
		$amount=$db->result($r,$i-1,"amount");
		$saldo_after=$db->result($r,$i-1,"saldo_after");
		$cash_id=$db->result($r,$i-1,"cash_id");$cash_abr=$this->getCashAbr($cash_id);
		$data=$db->result($r,$i-1,"data");
		$list.="
		<tr>
			<td>$i</td>
			<td>$data</td>
			<td>$cash_abr</td>
			<td>$saldo_before</td>
			<td>$amount</td>
			<td>$saldo_after</td>
		</tr>";
	}
	if ($n==0){$list="<tr><td align='center' colspan=6><h3 class='text-center'>������ ������</h3></td></tr>";}
	$form=str_replace("{list_saldo}",$list,$form);
	$form=str_replace("{paybox_id}",$paybox_id,$form);
	return $form;
}

function loadPayboxWorkers($paybox_id){$db=DbSingleton::getDb();$slave=new slave;$gmanual=new gmanual;
	$form_htm=RD."/tpl/paybox_workers_list.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$r=$db->query("select * from PAY_BOX_WORKERS where paybox_id='$paybox_id' and status='1' order by id asc;");$n=$db->num_rows($r);$list="";
	for ($i=1;$i<=$n;$i++){
		$id=$db->result($r,$i-1,"id");
		$worker_id=$db->result($r,$i-1,"worker_id");
		$worker_name=$this->getMediaUserName($worker_id);
		$list.="
		<tr>
			<td>
				<button class='btn btn-sm btn-default' onClick='showPayboxWorkerForm(\"$paybox_id\",\"$id\");'><i class='fa fa-edit'></i></button>
				<button class='btn btn-sm btn-default' onClick='dropPayboxWorker(\"$paybox_id\",\"$id\");'><i class='fa fa-times'></i></button>
			</td>
			<td>$i</td>
			<td>$worker_name</td>
		</tr>";
	}
	if ($n==0){$list="<tr><td align='center' colspan=5><h3 class='text-center'>������ ������</h3></td></tr>";}
	$form=str_replace("{list_workers}",$list,$form);
	$form=str_replace("{paybox_id}",$paybox_id,$form);
	return $form;
}

function showPayboxWorkerForm($paybox_id,$s_id){$db=DbSingleton::getDb();$slave=new slave;$gmanual=new gmanual;
	$form_htm=RD."/tpl/paybox_workers_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$r=$db->query("select * from PAY_BOX_WORKERS where id='$s_id' and paybox_id='$paybox_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){
		$str_id=$db->result($r,0,"id");
		$worker_id=$db->result($r,0,"worker_id");
	}
	$form=str_replace("{paybox_id}",$paybox_id,$form);
	$form=str_replace("{s_id}",$s_id,$form);
	$form=str_replace("{workers_list}",$this->showWorkersSelectList($worker_id),$form);
	return $form;
}

function savePayboxWorkerForm($paybox_id,$s_id,$worker_id){ $db=DbSingleton::getDb();$slave=new slave;session_start();$media_user_id=$_SESSION["media_user_id"];$media_user_name=$_SESSION["user_name"];$answer=0;$err="������� ���������� �����!";
	$paybox_id=$slave->qq($paybox_id);$s_id=$slave->qq($s_id);$worker_id=$slave->qq($worker_id);
	if ($paybox_id>0){
		if ($s_id==0 ){
			$r=$db->query("select max(id) as mid from PAY_BOX_WORKERS;");$s_id=0+$db->result($r,0,"mid")+1;
			$db->query("insert into PAY_BOX_WORKERS (id,paybox_id,status) values ('$s_id','$paybox_id','1');");
		}
		if  ($s_id>0){
			$db->query("update PAY_BOX_WORKERS set worker_id='$worker_id' where id='$s_id' and paybox_id='$paybox_id';");
			$answer=1;$err="";
		}
	}else{$answer=0;}
	return array($answer,$err);
}
	
function dropPaybox($paybox_id){$db=DbSingleton::getDb();$answer=0;$err="������� ���������� �����!";$slave=new slave;
	
	if ($paybox_id>0){
		$db->query("update PAY_BOX set status='0' where id='$paybox_id';");
		$answer=1;$err="";
	}
	return array($answer,$err);
}
	
function dropPayboxWorker($paybox_id,$s_id){$db=DbSingleton::getDb();$slave=new slave;session_start();$media_user_id=$_SESSION["media_user_id"];$media_user_name=$_SESSION["user_name"];$answer=0;$err="������� ���������� �����!";
	$paybox_id=$slave->qq($paybox_id);$s_id=$slave->qq($s_id);
	if ($paybox_id>0 && $s_id>0){
		$db->query("update PAY_BOX_WORKERS set status='0' where id='$s_id' and paybox_id='$paybox_id';");
		$answer=1;$err="";
	}
	return array($answer,$err);
}
	
function getPayboxSaldo($paybox_id,$user_id){$db=DbSingleton::getDb();$slave=new slave; $saldo="";
	$r=$db->query("select sum(saldo) as summ, cash_id from B_PAYBOX_BALANS where paybox_id='$paybox_id' $where_user group by cash_id order by cash_id asc;");$n=$db->num_rows($r); if ($n==0){ $saldo="0";}
	for ($i=1;$i<=$n;$i++){
		$summ=$db->result($r,$i-1,"summ");
		$cash_id=$db->result($r,$i-1,"cash_id");$cash_abr=$this->getCashAbr($cash_id);
		$saldo.="$summ$cash_abr; ";
	} 
	return $saldo;
}
function getCashAbr($cash_id){$db=DbSingleton::getDb();$name="";
	$r=$db->query("select abr from CASH where id ='$cash_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){$name=$db->result($r,0,"abr");}
	return $name;
}
	
}
?>