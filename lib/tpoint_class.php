<?php
class tpoint{
function newTpointCard(){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$slave=new slave;$manual=new manual; session_start();$user_id=$_SESSION["media_user_id"];$user_name=$_SESSION["user_name"]; $tpoint_id=0;
	$r=$db->query("select max(id) as mid from T_POINT;");$tpoint_id=0+$db->result($r,0,"mid")+1;
	$db->query("insert into T_POINT (`id`,`user_id`) values ('$tpoint_id','$user_id');");
	$dbt->query("insert into T_POINT (`id`,`user_id`) values ('$tpoint_id','$user_id');");
	return $tpoint_id;
}
function show_tpoint_list(){$db=DbSingleton::getTokoDb();$slave=new slave;$where="";
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
			$clients_list=$this->loadTpointClientsShortList($id);
			$storage_list=$this->loadTpointStorageShortList($id);
			$list.="<tr style='cursor:pointer' onClick='showTpointCard(\"$id\")'>
					<td>$id</td>
					<td>$name</td>
					<td>$state</td>
					<td>$region</td>
					<td>$city</td>
					<td>$address</td>
					<td>$worker_name</td>
					<td>$clients_list</td>
					<td>$storage_list</td>
                    </tr>";
		}
		return $list;
}

function showTpointCard($tpoint_id){$db=DbSingleton::getTokoDb();$slave=new slave;session_start();$user_id=$_SESSION["media_user_id"];$user_name=$_SESSION["user_name"];
	$form_htm=RD."/tpl/tpoint_card.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	
	$r=$db->query("select t.* from T_POINT t where t.id='$tpoint_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==0){$form_htm=RD."/tpl/access_deny.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);} }
	if ($n==1){
		$tpoint_id=$db->result($r,0,"id");
		$name=$db->result($r,0,"name");
		$full_name=$db->result($r,0,"full_name");
		$chief=$db->result($r,0,"chief");
		$country=$db->result($r,0,"country");
		$state=$db->result($r,0,"state"); 
		$region=$db->result($r,0,"region");
		$city=$db->result($r,0,"city");
		$address=$db->result($r,0,"address");
		$status=$db->result($r,0,"status");
		
		
		$form=str_replace("{tpoint_id}",$tpoint_id,$form);
		$form=str_replace("{tpoint_name}",$name,$form);
		$form=str_replace("{tpoint_full_name}",$full_name,$form);
		$form=str_replace("{workers_list}",$this->showWorkersSelectList($chief),$form);
		$form=str_replace("{address}",$address,$form);
		$form=str_replace("{country_list}",$slave->showSelectList("T2_COUNTRIES","COUNTRY_ID","COUNTRY_NAME",$country),$form);
		$form=str_replace("{state_list}",$slave->showSelectSubList("T2_STATE","COUNTRY_ID","$country","STATE_ID","STATE_NAME",$state),$form);
		$form=str_replace("{region_list}",$slave->showSelectSubList("T2_REGION","STATE_ID","$state","REGION_ID","REGION_NAME",$region),$form);
		$form=str_replace("{city_list}",$slave->showSelectSubList("T2_CITY","REGION_ID","$region","CITY_ID","CITY_NAME",$city),$form);
		
		$form=str_replace("{my_user_id}",$user_id,$form);
		$form=str_replace("{my_user_name}",$user_name,$form);
		
		
	}
	return $form;
}
	
function deleteTpoint($tpoint_id) {$db=DbSingleton::getTokoDb();
	$answer=0;$err="������� ���������� �����!";
	
	if($tpoint_id>0) {
		$db->query("update T_POINT set status=0 where id='$tpoint_id';");
		$answer=1;$err="";
	}						
	
	return array($answer,$err);
}

function saveTpointGeneralInfo($tpoint_id,$name,$full_name,$address,$chief,$country_id,$state_id,$region_id,$city_id){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$slave=new slave;session_start();$user_id=$_SESSION["media_user_id"];$user_name=$_SESSION["user_name"];$answer=0;$err="������� ���������� �����!";

	$tpoint_id=$slave->qq($tpoint_id);$name=$slave->qq($name);$full_name=$slave->qq($full_name);$address=$slave->qq($address);$chief=$slave->qq($chief);
	$country_id=$slave->qq($country_id);$state_id=$slave->qq($state_id);$city_id=$slave->qq($city_id);$region_id=$slave->qq($region_id);
	if ($tpoint_id>0){
		$db->query("update T_POINT set `name`='$name',status='1', `full_name`='$full_name', `address`='$address', `chief`='$chief', `country`='$country_id', `state`='$state_id', `city`='$city_id', `region`='$region_id' where `id`='$tpoint_id';");
		$dbt->query("update T_POINT set `name`='$name', status='1', `full_name`='$full_name', `address`='$address', `chief`='$chief', `country`='$country_id', `state`='$state_id', `city`='$city_id', `region`='$region_id' where `id`='$tpoint_id';");
		$answer=1;$err="";
	}
	return array($answer,$err);
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
function showPriceRatingSelectList($sel_id){$db=DbSingleton::getDb();$list="";$n=12;
	for ($i=1;$i<=$n;$i++){
		$sel="";if ($i==$sel_id){$sel=" selected";}
		$list.="<option value='$i' $sel>����� ".($i-1)."</option>";
	}
	return $list;
}
function showPriceRatingName($sel_id){$db=DbSingleton::getDb();$name="����� ";
	if ($sel_id>0){$name.=($sel_id-1);}
	return $name;
}


function loadTpointStorageShortList($tpoint_id){$db=DbSingleton::getTokoDb();$slave=new slave;$gmanual=new gmanual;
	$r=$db->query("select ps.*,s.name as storage_name from T_POINT_STORAGE ps left outer join STORAGE s on s.id=ps.storage_id where ps.tpoint_id='$tpoint_id' and ps.status='1' order by ps.id asc;");$n=$db->num_rows($r);$list="";
	for ($i=1;$i<=$n;$i++){
		$id=$db->result($r,$i-1,"id");
		$storage_id=$db->result($r,$i-1,"storage_id");
		$storage_name=$db->result($r,$i-1,"storage_name");
		$local=$gmanual->get_gmanual_caption($db->result($r,$i-1,"local"));
		$delivery_days=$db->result($r,$i-1,"delivery_days");
		$list.="$i) $local - $storage_name<br>";
	}
	if ($n==0){$list="������ ������";}
	return $list;
}
	//edit storage
function loadTpointStorage($tpoint_id){$db=DbSingleton::getTokoDb();$slave=new slave;$gmanual=new gmanual;
	$form_htm=RD."/tpl/tpoint_storage_list.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$r=$db->query("select ps.*,s.name as storage_name,s.full_name as storage_full_name from T_POINT_STORAGE ps left outer join STORAGE s on s.id=ps.storage_id where ps.tpoint_id='$tpoint_id' and ps.status='1' order by ps.id asc;");$n=$db->num_rows($r);$list="";
	for ($i=1;$i<=$n;$i++){
		$id=$db->result($r,$i-1,"id");
		$storage_id=$db->result($r,$i-1,"storage_id");
		$storage_name=$db->result($r,$i-1,"storage_name");
		$storage_full_name=$db->result($r,$i-1,"storage_full_name");
		$local=$gmanual->get_gmanual_caption($db->result($r,$i-1,"local"));
		$default=$db->result($r,$i-1,"default");$def_cap="-";if ($default==1){$def_cap="<i class='fa fa-check'></i>";}
		$list.="
		<tr>
			<td>
				<button class='btn btn-sm btn-default' onClick='showTpointStorageForm(\"$tpoint_id\",\"$id\");'><i class='fa fa-edit'></i></button>
				<button class='btn btn-sm btn-default' onClick='dropTpointStorage(\"$tpoint_id\",\"$id\");'><i class='fa fa-times'></i></button>
			</td>
			<td>$i</td>
			<td>$storage_name</td>
			<td>$storage_full_name</td>
			<td>$local</td>
			<td>$def_cap</td>
		</tr>";
	}
	if ($n==0){$list="<tr><td align='center' colspan=5><h3 class='text-center'>������ ������</h3></td></tr>";}
	$form=str_replace("{list_storage}",$list,$form);
	$form=str_replace("{tpoint_id}",$tpoint_id,$form);
	return $form;
}

function showTpointStorageForm($tpoint_id,$s_id){$db=DbSingleton::getTokoDb();$slave=new slave;$gmanual=new gmanual;
	$form_htm=RD."/tpl/tpoint_storage_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$r=$db->query("select * from T_POINT_STORAGE where id='$s_id' and tpoint_id='$tpoint_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){
		$str_id=$db->result($r,0,"id");
		$storage_id=$db->result($r,0,"storage_id");
		$local=$db->result($r,0,"local");
		$default=$db->result($r,0,"default");
		$def_ch="";if ($default==1){$def_ch=" checked='checked'";}
	}
	$form=str_replace("{tpoint_id}",$tpoint_id,$form);
	$form=str_replace("{s_id}",$s_id,$form);
	$form=str_replace("{storage_list}",$this->showTpointStorageSelectList($storage_id),$form);
	$form=str_replace("{local_list}",$gmanual->showGmanualSelectList("storage_local",$local),$form);
	$form=str_replace("{default_ch}",$def_ch,$form);
	return $form;
}
	
function showTpointSupplStorageForm($tpoint_id,$s_id){$db=DbSingleton::getDb();$slave=new slave;
	$form_htm=RD."/tpl/tpoint_suppl_storage_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$r=$db->query("select * from T_POINT_SUPPL_STORAGE where id='$s_id' and tpoint_id='$tpoint_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){
		$suppl_id=$db->result($r,0,"suppl_id");
		$storage_id=$db->result($r,0,"storage_id");
	}
	$form=str_replace("{suppl_list}",$this->showTpointSupplSelectList($suppl_id),$form);
	$form=str_replace("{storage_list}",$this->showTpointSupplStorageSelectList($suppl_id,$storage_id),$form);												 
	$form=str_replace("{tpoint_id}",$tpoint_id,$form);
	$form=str_replace("{s_id}",$s_id,$form);
	return $form;
}
	
function dropTpointSupplStorageForm($tpoint_id,$s_id){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$slave=new slave;session_start();$media_user_id=$_SESSION["media_user_id"];$media_user_name=$_SESSION["user_name"];$answer=0;$err="������� ���������� �����!";
	$tpoint_id=$slave->qq($tpoint_id);$s_id=$slave->qq($s_id);
	if ($tpoint_id>0 && $s_id>0){
		$db->query("delete from T_POINT_SUPPL_STORAGE where id='$s_id' and tpoint_id='$tpoint_id';");
		$dbt->query("delete from T_POINT_SUPPL_STORAGE where id='$s_id' and tpoint_id='$tpoint_id';");
		$answer=1;$err="";
	}
	return array($answer,$err);
}
	
function loadSupplStorageList($suppl_id,$sel_id){$db=DbSingleton::getDb();$list="<option value='0'> -- ������ � ������ --</option>";
	$r=$db->query("select * from A_CLIENTS_STORAGE where client_id='$suppl_id' and status='1' order by name asc;"); $n=$db->num_rows($r);
	for ($i=1;$i<=$n;$i++){
		$id=$db->result($r,$i-1,"id");
		$name=$db->result($r,$i-1,"name");
		$sel="";if ($id==$sel_id){$sel=" selected";}
		$list.="<option value='$id' $sel>$name</option>";
	}
	return $list;
}

function saveTpointSupplStorageForm($tpoint_id,$s_id,$storage_id,$suppl_id){ 
	$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$slave=new slave;session_start();$media_user_id=$_SESSION["media_user_id"];$media_user_name=$_SESSION["user_name"];$answer=0;$err="������� ���������� �����!";
	$tpoint_id=$slave->qq($tpoint_id);$s_id=$slave->qq($s_id);$storage_id=$slave->qq($storage_id);$suppl_id=$slave->qq($suppl_id);
	if ($tpoint_id>0){
		if ($s_id==0 ){
			$r=$db->query("select max(id) as mid from T_POINT_SUPPL_STORAGE;");$s_id=0+$db->result($r,0,"mid")+1;
			$db->query("insert into T_POINT_SUPPL_STORAGE (id,tpoint_id,storage_id,suppl_id) values ('$s_id','$tpoint_id','$storage_id','$suppl_id');");
			$dbt->query("insert into T_POINT_SUPPL_STORAGE (id,tpoint_id,storage_id,suppl_id) values ('$s_id','$tpoint_id','$storage_id','$suppl_id');");
		}
		if  ($s_id>0){
			$db->query("update T_POINT_SUPPL_STORAGE set storage_id='$storage_id', suppl_id='$suppl_id' where id='$s_id' and tpoint_id='$tpoint_id';");
			$dbt->query("update T_POINT_SUPPL_STORAGE set storage_id='$storage_id', suppl_id='$suppl_id' where id='$s_id' and tpoint_id='$tpoint_id';");
			$answer=1;$err="";
		}
	}else{$answer=0;}
	return array($answer,$err);
}
	
function saveTpointStorageForm($tpoint_id,$s_id,$storage_id,$local,$default){ $db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$slave=new slave;session_start();$media_user_id=$_SESSION["media_user_id"];$media_user_name=$_SESSION["user_name"];$answer=0;$err="������� ���������� �����!";
	$tpoint_id=$slave->qq($tpoint_id);$s_id=$slave->qq($s_id);$storage_id=$slave->qq($storage_id);$local=$slave->qq($local);$default=$slave->qq($default);
	if ($tpoint_id>0){
		
		if ($s_id==0 ){
			$r=$db->query("select max(id) as mid from T_POINT_STORAGE;");$s_id=0+$db->result($r,0,"mid")+1;
			$db->query("insert into T_POINT_STORAGE (id,tpoint_id,`default`,status) values ('$s_id','$tpoint_id','$default','1');");
			$dbt->query("insert into T_POINT_STORAGE (id,tpoint_id,`default`,status) values ('$s_id','$tpoint_id','$default','1');");
		}
		if  ($s_id>0){
			$db->query("update T_POINT_STORAGE set storage_id='$storage_id', local='$local', `default`='$default' where id='$s_id' and tpoint_id='$tpoint_id';");
			$dbt->query("update T_POINT_STORAGE set storage_id='$storage_id', local='$local', `default`='$default'where id='$s_id' and tpoint_id='$tpoint_id';");
			$answer=1;$err="";
		}
	}else{$answer=0;}
	return array($answer,$err);
}
	
function dropTpointStorage($tpoint_id,$s_id){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$slave=new slave;session_start();$media_user_id=$_SESSION["media_user_id"];$media_user_name=$_SESSION["user_name"];$answer=0;$err="������� ���������� �����!";
	$tpoint_id=$slave->qq($tpoint_id);$s_id=$slave->qq($s_id);
	if ($tpoint_id>0 && $s_id>0){
		$db->query("update T_POINT_STORAGE set status='0' where id='$s_id' and tpoint_id='$tpoint_id';");
		$dbt->query("update T_POINT_STORAGE set status='0' where id='$s_id' and tpoint_id='$tpoint_id';");
		$answer=1;$err="";
	}
	return array($answer,$err);
}

function loadTpointClientsShortList($tpoint_id){$db=DbSingleton::getDb();$slave=new slave;$gmanual=new gmanual;
	$r=$db->query("select pc.* from T_POINT_CLIENTS pc where pc.tpoint_id='$tpoint_id' and pc.status='1' order by pc.id asc;");$n=$db->num_rows($r);$list="";
	for ($i=1;$i<=$n;$i++){
		$id=$db->result($r,$i-1,"id");
		$client_id=$db->result($r,$i-1,"client_id");
		$client_name=$this->getAClientName($client_id);
		$vat_use=$db->result($r,$i-1,"vat_use"); $vat="��� ���";if ($vat_use==1){$vat="� ���";}
		$list.="$i) $client_name: $vat<br>";
	}
	if ($n==0){$list="������ ������";}
	return $list;
}

function loadTpointClients($tpoint_id){$db=DbSingleton::getTokoDb();$slave=new slave;$gmanual=new gmanual;
	$form_htm=RD."/tpl/tpoint_clients_list.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$r=$db->query("select pc.* from T_POINT_CLIENTS pc where pc.tpoint_id='$tpoint_id' and pc.status='1' order by pc.id asc;");$n=$db->num_rows($r);$list="";
	for ($i=1;$i<=$n;$i++){
		$id=$db->result($r,$i-1,"id");
		$client_id=$db->result($r,$i-1,"client_id");
		$client_name=$this->getAClientName($client_id);
		$sale_type=$gmanual->get_gmanual_caption($db->result($r,$i-1,"sale_type"));
		$tax_credit=$db->result($r,$i-1,"tax_credit");
		$tax_inform=$db->result($r,$i-1,"tax_inform");
		$in_use=$db->result($r,$i-1,"in_use"); $in_use_cap="-";if ($in_use==1){$in_use_cap="<i class='fa fa-eye'></i>";}
		$list.="
		<tr>
			<td>
				<button class='btn btn-sm btn-default' onClick='showTpointClientsForm(\"$tpoint_id\",\"$id\");'><i class='fa fa-edit'></i></button>
				<button class='btn btn-sm btn-default' onClick='dropTpointClient(\"$tpoint_id\",\"$id\");'><i class='fa fa-times'></i></button>
			</td>
			<td>$i</td>
			<td>$client_name</td>
			<td>$sale_type</td>
			<td>$tax_credit</td>
			<td>$in_use_cap</td>
		</tr>";
	}
	if ($n==0){$list="<tr><td align='center' colspan=5><h3 class='text-center'>������ ������</h3></td></tr>";}
	$form=str_replace("{list_clients}",$list,$form);
	$form=str_replace("{tpoint_id}",$tpoint_id,$form);
	return $form;
}
function showTpointClientsForm($tpoint_id,$s_id){$db=DbSingleton::getTokoDb();$slave=new slave;$gmanual=new gmanual;
	$form_htm=RD."/tpl/tpoint_clients_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$r=$db->query("select pc.* from T_POINT_CLIENTS pc where pc.id='$s_id' and pc.tpoint_id='$tpoint_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){
		$str_id=$db->result($r,0,"id");
		$client_id=$db->result($r,0,"client_id");
		$client_name=$this->getAClientName($client_id);
		$sale_type=$db->result($r,0,"sale_type");
		$tax_credit=$db->result($r,0,"tax_credit");
		$tax_inform=$db->result($r,0,"tax_inform");
		$in_use=$db->result($r,0,"in_use");
	}
	$inuse_checked="";if ($in_use==1){$inuse_checked=" checked";}
	$form=str_replace("{tpoint_id}",$tpoint_id,$form);
	$form=str_replace("{s_id}",$s_id,$form);
	$form=str_replace("{client_id}",$client_id,$form);
	$form=str_replace("{client_name}",$client_name,$form);
	$form=str_replace("{sale_type_list}",$gmanual->showGmanualSelectList("client_sale_type",$sale_type),$form);
	$form=str_replace("{tax_credit}",$tax_credit,$form);
	$form=str_replace("{tax_inform}",$tax_inform,$form);
	$form=str_replace("{inuse_checked}",$inuse_checked,$form);
	return $form;
}

function saveTpointClientsForm($tpoint_id,$s_id,$client_id,$sale_type,$tax_credit,$tax_inform,$in_use){ $db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$slave=new slave;session_start();$media_user_id=$_SESSION["media_user_id"];$media_user_name=$_SESSION["user_name"];$answer=0;$err="������� ���������� �����!";
	$tpoint_id=$slave->qq($tpoint_id);$s_id=$slave->qq($s_id);$client_id=$slave->qq($client_id);$sale_type=$slave->qq($sale_type);$tax_credit=$slave->qq($tax_credit);$tax_inform=$slave->qq($tax_inform);$in_use=$slave->qq($in_use);
	if ($tpoint_id>0){
		if ($s_id==0 ){
			$r=$db->query("select max(id) as mid from T_POINT_CLIENTS;");$s_id=0+$db->result($r,0,"mid")+1;
			$db->query("insert into T_POINT_CLIENTS (id,tpoint_id,status) values ('$s_id','$tpoint_id','1');");
			$dbt->query("insert into T_POINT_CLIENTS (id,tpoint_id,status) values ('$s_id','$tpoint_id','1');");
		}
		if  ($s_id>0){
			if ($in_use==1){ 
				$db->query("update T_POINT_CLIENTS set `in_use`='0' where `in_use`='1' and tpoint_id='$tpoint_id' and sale_type='$sale_type';"); 
				$dbt->query("update T_POINT_CLIENTS set `in_use`='0' where `in_use`='1' and tpoint_id='$tpoint_id' and sale_type='$sale_type';"); 
			}
			$db->query("update T_POINT_CLIENTS set `client_id`='$client_id', sale_type='$sale_type', tax_credit='$tax_credit', tax_inform='$tax_inform', in_use='$in_use' where id='$s_id' and tpoint_id='$tpoint_id';");
			$dbt->query("update T_POINT_CLIENTS set `client_id`='$client_id', sale_type='$sale_type', tax_credit='$tax_credit', tax_inform='$tax_inform', in_use='$in_use' where id='$s_id' and tpoint_id='$tpoint_id';");
			$answer=1;$err="";
		}
	}else{$answer=0;}
	return array($answer,$err);
}
function dropTpointClients($tpoint_id,$s_id){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$slave=new slave;session_start();$media_user_id=$_SESSION["media_user_id"];$media_user_name=$_SESSION["user_name"];$answer=0;$err="������� ���������� �����!";
	$tpoint_id=$slave->qq($tpoint_id);$s_id=$slave->qq($s_id);
	if ($tpoint_id>0 && $s_id>0){
		$db->query("update T_POINT_CLIENTS set status='0' where id='$s_id' and tpoint_id='$tpoint_id';");
		$dbt->query("update T_POINT_CLIENTS set status='0' where id='$s_id' and tpoint_id='$tpoint_id';");
		$answer=1;$err="";
	}
	return array($answer,$err);
}

function loadTpointWorkers($tpoint_id){$db=DbSingleton::getTokoDb();$slave=new slave;$gmanual=new gmanual;
	$form_htm=RD."/tpl/tpoint_workers_list.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$r=$db->query("select pw.* from T_POINT_WORKERS pw where pw.tpoint_id='$tpoint_id' and pw.status='1' order by pw.id asc;");$n=$db->num_rows($r);$list="";
	for ($i=1;$i<=$n;$i++){
		$id=$db->result($r,$i-1,"id");
		$worker_id=$db->result($r,$i-1,"media_user_id");
		$worker_name=$this->getMediaUserName($worker_id);
		$list.="
		<tr>
			<td>
				<button class='btn btn-sm btn-default' onClick='showTpointWorkersForm(\"$tpoint_id\",\"$id\");'><i class='fa fa-edit'></i></button>
				<button class='btn btn-sm btn-default' onClick='dropTpointWorkers(\"$tpoint_id\",\"$id\");'><i class='fa fa-times'></i></button>
			</td>
			<td>$i</td>
			<td>$worker_name</td>
		</tr>";
	}
	if ($n==0){$list="<tr><td align='center' colspan=5><h3 class='text-center'>������ ������</h3></td></tr>";}
	$form=str_replace("{list_workers}",$list,$form);
	$form=str_replace("{tpoint_id}",$tpoint_id,$form);
	return $form;
}

function showTpointWorkersForm($tpoint_id,$s_id){$db=DbSingleton::getTokoDb();$slave=new slave;$gmanual=new gmanual;
	$form_htm=RD."/tpl/tpoint_workers_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$r=$db->query("select * from T_POINT_WORKERS where id='$s_id' and tpoint_id='$tpoint_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){
		$str_id=$db->result($r,0,"id");
		$media_user_id=$db->result($r,0,"media_user_id");
	}
	$form=str_replace("{tpoint_id}",$tpoint_id,$form);
	$form=str_replace("{s_id}",$s_id,$form);
	$form=str_replace("{workers_list}",$this->showWorkersSelectList($media_user_id),$form);
	return $form;
}

function saveTpointWorkersForm($tpoint_id,$s_id,$worker_id){ $db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$slave=new slave;session_start();$media_user_id=$_SESSION["media_user_id"];$media_user_name=$_SESSION["user_name"];$answer=0;$err="������� ���������� �����!";
	$tpoint_id=$slave->qq($tpoint_id);$s_id=$slave->qq($s_id);$worker_id=$slave->qq($worker_id);
	if ($tpoint_id>0){
		if ($s_id==0 ){
			$r=$db->query("select max(id) as mid from T_POINT_WORKERS;");$s_id=0+$db->result($r,0,"mid")+1;
			$db->query("insert into T_POINT_WORKERS (id,tpoint_id,status) values ('$s_id','$tpoint_id','1');");
			$dbt->query("insert into T_POINT_WORKERS (id,tpoint_id,status) values ('$s_id','$tpoint_id','1');");
		}
		if  ($s_id>0){
			$db->query("update T_POINT_WORKERS set media_user_id='$worker_id' where id='$s_id' and tpoint_id='$tpoint_id';");
			$dbt->query("update T_POINT_WORKERS set media_user_id='$worker_id' where id='$s_id' and tpoint_id='$tpoint_id';");
			$answer=1;$err="";
		}
	}else{$answer=0;}
	return array($answer,$err);
}
function dropTpointWorkers($tpoint_id,$s_id){$db=DbSingleton::getTokoDb();$dbt=DbSingleton::getTokoDb();$slave=new slave;session_start();$media_user_id=$_SESSION["media_user_id"];$media_user_name=$_SESSION["user_name"];$answer=0;$err="������� ���������� �����!";
	$tpoint_id=$slave->qq($tpoint_id);$s_id=$slave->qq($s_id);
	if ($tpoint_id>0 && $s_id>0){
		$db->query("update T_POINT_WORKERS set status='0' where id='$s_id' and tpoint_id='$tpoint_id';");
		$dbt->query("update T_POINT_WORKERS set status='0' where id='$s_id' and tpoint_id='$tpoint_id';");
		$answer=1;$err="";
	}
	return array($answer,$err);
}

function showTpointClientList($sel_id){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$slave=new slave;
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
		$cur="";$fn="<i class='fa fa-thumb-tack' onClick='setTpointClient(\"$id\", \"$name\")'></i>";
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
function showTpointSupplSelectList($sel_id){$db=DbSingleton::getDb();$list="";
	$r=$db->query("select c.* from A_CLIENTS c left outer join A_CLIENTS_CATEGORY cc on cc.client_id=c.id where c.status='1' and cc.category_id='2';");$n=$db->num_rows($r);$list="";
	for ($i=1;$i<=$n;$i++){
		$id=$db->result($r,$i-1,"id");
		$name=$db->result($r,$i-1,"name");
		$sel="";if ($id==$sel_id){$sel=" selected";}
		$list.="<option value='$id' $sel>$name</option>";
	}
	return $list;
}
function showTpointSupplStorageSelectList($suppl_id,$sel_id){$db=DbSingleton::getDb();$list="";
	$r=$db->query("select * from A_CLIENTS_STORAGE where status='1' and client_id='$suppl_id' order by name,id asc;");$n=$db->num_rows($r);
	for ($i=1;$i<=$n;$i++){
		$id=$db->result($r,$i-1,"id");
		$name=$db->result($r,$i-1,"name");
		$sel="";if ($id==$sel_id){$sel=" selected";}
		$list.="<option value='$id' $sel>$name</option>";
	}
	return $list;
}

function showTpointStorageSelectList($sel_id){$db=DbSingleton::getTokoDb();$list="";
	$r=$db->query("select * from STORAGE where status='1' order by name,id asc;");$n=$db->num_rows($r);
	for ($i=1;$i<=$n;$i++){
		$id=$db->result($r,$i-1,"id");
		$name=$db->result($r,$i-1,"name");
		$sel="";if ($id==$sel_id){$sel=" selected";}
		$list.="<option value='$id' $sel>$name</option>";
	}
	return $list;
}

function getTpointNameById($sel_id, $field="name"){$name="";
	$r=$db->query("select `$field` from A_CLIENTS where id='$sel_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){$name=$db->result($r,0,"$field");}
	return $name;
}
function loadStateSelectList($country_id,$sel_id){$slave=new slave;
	$list=$slave->showSelectSubList("T2_STATE","COUNTRY_ID","$country_id","STATE_ID","STATE_NAME",$sel_id);
	return $list;	
}
function loadRegionSelectList($state_id,$sel_id){$slave=new slave;
	return $slave->showSelectSubList("T2_REGION","STATE_ID","$state_id","REGION_ID","REGION_NAME",$sel_id);
}
function loadCitySelectList($region_id,$sel_id){$slave=new slave;//$list="";
	return "<option value='NEW'>�������� ��������� �����</option>".$slave->showSelectSubList("T2_CITY","REGION_ID","$region_id","CITY_ID","CITY_NAME",$sel_id);
}

function getMediaUserName($user_id){$db=DbSingleton::getDb();$name="";
	$r=$db->query("select name from media_users where id='$user_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){$name=$db->result($r,0,"name");}
	return $name;
}
function getAClientName($client_id){$db=DbSingleton::getDb();$name="";
	$r=$db->query("select name from A_CLIENTS where id='$client_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){$name=$db->result($r,0,"name");}
	return $name;
}

function loadTpointDeliveryTime($tpoint_id){$db=DbSingleton::getTokoDb();$slave=new slave;$gmanual=new gmanual;
	$form_htm=RD."/tpl/tpoint_delivery_time_list.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$r=$db->query("select pd.*,s.name as storage_name from T_POINT_DELIVERY_TIME pd left outer join STORAGE s on s.id=pd.storage_id where pd.tpoint_id='$tpoint_id' and pd.status='1' order by pd.storage_id asc,  pd.week_day asc, pd.time_from asc;");$n=$db->num_rows($r);$list="";
	for ($i=1;$i<=$n;$i++){
		$id=$db->result($r,$i-1,"id");
		$storage_id=$db->result($r,$i-1,"storage_id");
		$storage_name=$db->result($r,$i-1,"storage_name");
		$week_day=$slave->get_weekday_name($db->result($r,$i-1,"week_day"));
		$time_from=substr($db->result($r,$i-1,"time_from"),0,-3);
		$time_to=substr($db->result($r,$i-1,"time_to"),0,-3);
		$delivery_days=$db->result($r,$i-1,"delivery_days");$dd_word="����� ".$delivery_days." ��. ";if ($delivery_days==0){$dd_word="�������";}if ($delivery_days==1){$dd_word="������";}
		$giveout_time=$db->result($r,$i-1,"giveout_time");
		$time_from_del=substr($db->result($r,$i-1,"time_from_del"),0,-3);
		$time_to_del=substr($db->result($r,$i-1,"time_to_del"),0,-3);
		$week_day_short=$slave->get_weekday_abr($db->result($r,$i-1,"week_day"));
		
    	$date_del=date('d.m', strtotime(' + '.$delivery_days.' days'));
		
		$giveout_client_info="$date_del ($week_day_short) � $time_from_del �� $time_to_del";
		//$giveout_client_info="$dd_word � $time_from_del �� $time_to_del";
		
		$list.="
		<tr>
			<td>
				<button class='btn btn-sm btn-default' onClick='showTpointDeliveryForm(\"$tpoint_id\",\"$id\");'><i class='fa fa-edit'></i></button>
				<button class='btn btn-sm btn-default' onClick='dropTpointDelivery(\"$tpoint_id\",\"$id\");'><i class='fa fa-times'></i></button>
			</td>
			<td>$i</td>
			<td>$storage_name</td>
			<td>$week_day</td>
			<td>$time_from</td>
			<td>$time_to</td>
			<td>$delivery_days</td>
			
			<td>$time_from_del</td>
			<td>$time_to_del</td>
			<td>$giveout_client_info</td>
		</tr>";
	}
//											<td>$giveout_time</td>
	if ($n==0){$list="<tr><td align='center' colspan='9'><h3 class='text-center'>������ ������</h3></td></tr>";}
	$form=str_replace("{list_delivery}",$list,$form);
	$form=str_replace("{tpoint_id}",$tpoint_id,$form);
	return $form;
}
function showTpointDeliveryForm($tpoint_id,$s_id){$db=DbSingleton::getTokoDb();$slave=new slave;$gmanual=new gmanual;
	$form_htm=RD."/tpl/tpoint_delivery_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$r=$db->query("select * from T_POINT_DELIVERY_TIME where id='$s_id' and tpoint_id='$tpoint_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){
		$str_id=$db->result($r,0,"id");
		$storage_id=$db->result($r,0,"storage_id");
		$storage_name=$db->result($r,0,"storage_name");
		$week_day=$db->result($r,0,"week_day");
		$time_from=substr($db->result($r,0,"time_from"),0,-3);
		$time_to=substr($db->result($r,0,"time_to"),0,-3);
		$giveout_time=$db->result($r,0,"giveout_time");
		$delivery_days=$db->result($r,0,"delivery_days");
		$time_from_del=substr($db->result($r,0,"time_from_del"),0,-3);
		$time_to_del=substr($db->result($r,0,"time_to_del"),0,-3);
	}
	$form=str_replace("{tpoint_id}",$tpoint_id,$form);
	$form=str_replace("{s_id}",$s_id,$form);
	$form=str_replace("{storage_list}",$this->showTpointStorageSelectList($storage_id),$form);
	$form=str_replace("{week_day_list}",$slave->showWeekdaySelectList($week_day),$form);
	$form=str_replace("{time_from}",$time_from,$form);
	$form=str_replace("{time_to}",$time_to,$form);
	$form=str_replace("{delivery_days}",$delivery_days,$form);
	$form=str_replace("{giveout_time}",$giveout_time,$form);
	$form=str_replace("{time_from_del}",$time_from_del,$form);
	$form=str_replace("{time_to_del}",$time_to_del,$form);
	$form=str_replace("{workers_list}",$this->showWorkersSelectList($media_user_id),$form);
	return $form;
}
function saveTpointDeliveryForm($tpoint_id,$s_id,$storage_id,$week_day,$time_from,$time_to,$delivery_days,$giveout_time,$time_from_del,$time_to_del){ $db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$slave=new slave;session_start();$media_user_id=$_SESSION["media_user_id"];$media_user_name=$_SESSION["user_name"];$answer=0;$err="������� ���������� �����!";
	$tpoint_id=$slave->qq($tpoint_id);$s_id=$slave->qq($s_id);$storage_id=$slave->qq($storage_id);$week_day=$slave->qq($week_day);$time_from=$slave->qq($time_from);$time_to=$slave->qq($time_to);$delivery_days=$slave->qq($delivery_days);$giveout_time=$slave->qq($giveout_time);
	if ($tpoint_id>0){
		if ($s_id==0 ){
			$r=$db->query("select max(id) as mid from T_POINT_DELIVERY_TIME;");$s_id=0+$db->result($r,0,"mid")+1;
			$db->query("insert into T_POINT_DELIVERY_TIME (id,tpoint_id,status) values ('$s_id','$tpoint_id','1');");
			$dbt->query("insert into T_POINT_DELIVERY_TIME (id,tpoint_id,status) values ('$s_id','$tpoint_id','1');");
		}
		if  ($s_id>0){
			$db->query("update T_POINT_DELIVERY_TIME set storage_id='$storage_id', week_day='$week_day', time_from='$time_from', time_to='$time_to', delivery_days='$delivery_days', giveout_time='$giveout_time', time_from_del='$time_from_del', time_to_del='$time_to_del' where id='$s_id' and tpoint_id='$tpoint_id';");
			$dbt->query("update T_POINT_DELIVERY_TIME set storage_id='$storage_id', week_day='$week_day', time_from='$time_from', time_to='$time_to', delivery_days='$delivery_days', giveout_time='$giveout_time', time_from_del='$time_from_del', time_to_del='$time_to_del' where id='$s_id' and tpoint_id='$tpoint_id';");
			$answer=1;$err="";
		}
	}else{$answer=0;}
	return array($answer,$err);
}
function dropTpointDelivery($tpoint_id,$s_id){$db=DbSingleton::getTokoDb();$dbt=DbSingleton::getTokoDb();$slave=new slave;session_start();$media_user_id=$_SESSION["media_user_id"];$media_user_name=$_SESSION["user_name"];$answer=0;$err="������� ���������� �����!";
	$tpoint_id=$slave->qq($tpoint_id);$s_id=$slave->qq($s_id);
	if ($tpoint_id>0 && $s_id>0){
		$db->query("update T_POINT_DELIVERY_TIME set status='0' where id='$s_id' and tpoint_id='$tpoint_id';");
		$dbt->query("update T_POINT_DELIVERY_TIME set status='0' where id='$s_id' and tpoint_id='$tpoint_id';");
		$answer=1;$err="";
	}
	return array($answer,$err);
}


function loadTpointSupplDeliveryTime($tpoint_id){$db=DbSingleton::getDb();$slave=new slave;$gmanual=new gmanual;
	$form_htm=RD."/tpl/tpoint_suppl_delivery_time_list.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$r=$db->query("select pd.*, cs.name as storage_name, c.name as client_name from T_POINT_SUPPL_DELIVERY_TIME pd 
				   left outer join A_CLIENTS_STORAGE cs on cs.id=pd.suppl_storage_id
				   left outer join A_CLIENTS c on c.id=cs.client_id
				   where pd.tpoint_id='$tpoint_id' and pd.status='1' order by pd.suppl_storage_id asc, pd.week_day asc, pd.time_from asc;");$n=$db->num_rows($r);$list="";
	for ($i=1;$i<=$n;$i++){
		$id=$db->result($r,$i-1,"id");
		$client_name=$db->result($r,$i-1,"client_name");
		$storage_id=$db->result($r,$i-1,"suppl_storage_id");
		$storage_name=$db->result($r,$i-1,"storage_name");
		$week_day=$slave->get_weekday_name($db->result($r,$i-1,"week_day"));
		$time_from=substr($db->result($r,$i-1,"time_from"),0,-3);
		$time_to=substr($db->result($r,$i-1,"time_to"),0,-3);
		$delivery_days=$db->result($r,$i-1,"delivery_days");$dd_word="����� ".$delivery_days." ��. ";if ($delivery_days==0){$dd_word="�������";}if ($delivery_days==1){$dd_word="������";}
		$giveout_time=$db->result($r,$i-1,"giveout_time");
		$time_from_del=substr($db->result($r,$i-1,"time_from_del"),0,-3);
		$time_to_del=substr($db->result($r,$i-1,"time_to_del"),0,-3);
		
		$week_day_short=$slave->get_weekday_abr($db->result($r,$i-1,"week_day"));
    	$date_del=date('d.m', strtotime(' + '.$delivery_days.' days'));
		
		$giveout_client_info="$date_del ($week_day_short)<br>� $time_from_del �� $time_to_del";
		//$giveout_client_info="$dd_word $giveout_time";
		$list.="
		<tr>
			<td>
				<button class='btn btn-sm btn-default' onClick='showTpointSupplDeliveryForm(\"$tpoint_id\",\"$id\");'><i class='fa fa-edit'></i></button>
				<button class='btn btn-sm btn-default' onClick='dropTpointSupplDelivery(\"$tpoint_id\",\"$id\");'><i class='fa fa-times'></i></button>
			</td>
			<td>$i</td>
			<td>$client_name</td>
			<td>$storage_name</td>
			<td>$week_day</td>
			<td>$time_from</td>
			<td>$time_to</td>
			<td>$delivery_days</td>
			<td>$time_from_del</td>
			<td>$time_to_del</td>
			<td>$giveout_client_info</td>
		</tr>";
	}
	if ($n==0){$list="<tr><td align='center' colspan='9'><h3 class='text-center'>������ ������</h3></td></tr>";}
	$form=str_replace("{list_delivery}",$list,$form);
	$form=str_replace("{tpoint_id}",$tpoint_id,$form);
	return $form;
}

function loadTpointSupplStorage($tpoint_id){$db=DbSingleton::getDb(); $dbt=DbSingleton::getTokoDb();$slave=new slave;$gmanual=new gmanual;
	$form_htm=RD."/tpl/tpoint_suppl_storage_list.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$r=$db->query("select pss.*, st.name as storage_name, cc.name as suppl_name from T_POINT_SUPPL_STORAGE pss left join A_CLIENTS_STORAGE st on st.id=pss.storage_id	left join A_CLIENTS cc on cc.id=pss.suppl_id where pss.tpoint_id='$tpoint_id' order by pss.id asc;");$n=$db->num_rows($r);$list="";
	for ($i=1;$i<=$n;$i++){
		$id=$db->result($r,$i-1,"id");
		$suppl_name=$db->result($r,$i-1,"suppl_name");
		$storage_name=$db->result($r,$i-1,"storage_name");
		$list.="<tr>
			<td>
				<button class='btn btn-sm btn-default' onClick='showTpointSupplStorageForm(\"$tpoint_id\",\"$id\");'><i class='fa fa-edit'></i></button>
				<button class='btn btn-sm btn-default' onClick='dropTpointSupplStorageForm(\"$tpoint_id\",\"$id\");'><i class='fa fa-times'></i></button>
			</td>
			<td>$i</td>
			<td>$suppl_name</td>
			<td>$storage_name</td>
		</tr>";
	}
	if ($n==0){$list="<tr><td align='center' colspan=5><h3 class='text-center'>������ ������</h3></td></tr>";}
	$form=str_replace("{list_storage}",$list,$form);
	$form=str_replace("{tpoint_id}",$tpoint_id,$form);
	return $form;
}

function showTpointSupplDeliveryForm($tpoint_id,$s_id){$db=DbSingleton::getTokoDb();$slave=new slave;$gmanual=new gmanual;
	$form_htm=RD."/tpl/tpoint_suppl_delivery_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$r=$db->query("select * from T_POINT_SUPPL_DELIVERY_TIME where id='$s_id' and tpoint_id='$tpoint_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){
		$str_id=$db->result($r,0,"id");
		$suppl_id=$db->result($r,0,"suppl_id");
		$suppl_storage_id=$db->result($r,0,"suppl_storage_id");
		$storage_name=$db->result($r,0,"storage_name");
		$week_day=$db->result($r,0,"week_day");
		$time_from=substr($db->result($r,0,"time_from"),0,-3);
		$time_to=substr($db->result($r,0,"time_to"),0,-3);
		$delivery_days=$db->result($r,0,"delivery_days");
		$giveout_time=$db->result($r,0,"giveout_time");
		$time_from_del=substr($db->result($r,0,"time_from_del"),0,-3);
		$time_to_del=substr($db->result($r,0,"time_to_del"),0,-3);
	}
	$form=str_replace("{tpoint_id}",$tpoint_id,$form);
	$form=str_replace("{s_id}",$s_id,$form);
	$form=str_replace("{suppl_list}",$this->showTpointSupplSelectList($suppl_id),$form);
	$form=str_replace("{suppl_storage_list}",$this->showTpointSupplStorageSelectList($suppl_id,$suppl_storage_id),$form);
	$form=str_replace("{week_day_list}",$slave->showWeekdaySelectList($week_day),$form);
	$form=str_replace("{time_from}",$time_from,$form);
	$form=str_replace("{time_to}",$time_to,$form);
	$form=str_replace("{delivery_days}",$delivery_days,$form);
	$form=str_replace("{giveout_time}",$giveout_time,$form);
	$form=str_replace("{time_from_del}",$time_from_del,$form);
	$form=str_replace("{time_to_del}",$time_to_del,$form);
	$form=str_replace("{workers_list}",$this->showWorkersSelectList($media_user_id),$form);
	return $form;
}
function saveTpointSupplDeliveryForm($tpoint_id,$s_id,$suppl_id,$suppl_storage_id,$week_day,$time_from,$time_to,$delivery_days,$giveout_time,$time_from_del,$time_to_del){ $db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$slave=new slave;session_start();$media_user_id=$_SESSION["media_user_id"];$media_user_name=$_SESSION["user_name"];$answer=0;$err="������� ���������� �����!";
	$tpoint_id=$slave->qq($tpoint_id);$s_id=$slave->qq($s_id);$suppl_id=$slave->qq($suppl_id);$suppl_storage_id=$slave->qq($suppl_storage_id);$week_day=$slave->qq($week_day);$time_from=$slave->qq($time_from);$time_to=$slave->qq($time_to);$delivery_days=$slave->qq($delivery_days);$giveout_time=$slave->qq($giveout_time);
	if ($tpoint_id>0){
		if ($s_id==0 ){
			$r=$db->query("select max(id) as mid from T_POINT_SUPPL_DELIVERY_TIME;");$s_id=0+$db->result($r,0,"mid")+1;
			$db->query("insert into T_POINT_SUPPL_DELIVERY_TIME (id,tpoint_id,status) values ('$s_id','$tpoint_id','1');");
			$dbt->query("insert into T_POINT_SUPPL_DELIVERY_TIME (id,tpoint_id,status) values ('$s_id','$tpoint_id','1');");
		}
		if  ($s_id>0){
			$db->query("update T_POINT_SUPPL_DELIVERY_TIME set suppl_id='$suppl_id', suppl_storage_id='$suppl_storage_id', week_day='$week_day', time_from='$time_from', time_to='$time_to', delivery_days='$delivery_days', giveout_time='$giveout_time', time_from_del='$time_from_del', time_to_del='$time_to_del' where id='$s_id' and tpoint_id='$tpoint_id';");
			$dbt->query("update T_POINT_SUPPL_DELIVERY_TIME set suppl_id='$suppl_id', suppl_storage_id='$suppl_storage_id', week_day='$week_day', time_from='$time_from', time_to='$time_to', delivery_days='$delivery_days', giveout_time='$giveout_time', time_from_del='$time_from_del', time_to_del='$time_to_del' where id='$s_id' and tpoint_id='$tpoint_id';");
			$answer=1;$err="";
		}
	}else{$answer=0;}
	return array($answer,$err);
}

function dropTpointSupplDelivery($tpoint_id,$s_id){$db=DbSingleton::getTokoDb();$dbt=DbSingleton::getTokoDb();$slave=new slave;session_start();$media_user_id=$_SESSION["media_user_id"];$media_user_name=$_SESSION["user_name"];$answer=0;$err="������� ���������� �����!";
	$tpoint_id=$slave->qq($tpoint_id);$s_id=$slave->qq($s_id);
	if ($tpoint_id>0 && $s_id>0){
		$db->query("update T_POINT_SUPPL_DELIVERY_TIME set status='0' where id='$s_id' and tpoint_id='$tpoint_id';");
		$dbt->query("update T_POINT_SUPPL_DELIVERY_TIME set status='0' where id='$s_id' and tpoint_id='$tpoint_id';");
		$answer=1;$err="";
	}
	return array($answer,$err);
}
function loadTpointSupplFm($tpoint_id){$db=DbSingleton::getDb();$slave=new slave;$gmanual=new gmanual;
	$form_htm=RD."/tpl/tpoint_suppl_fm_list.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$r=$db->query("select pd.*, cs.name as storage_name, c.name as client_name from T_POINT_SUPPL_FM pd 
				   left outer join A_CLIENTS_STORAGE cs on cs.id=pd.suppl_storage_id
				   left outer join A_CLIENTS c on c.id=pd.suppl_id
				   where pd.tpoint_id='$tpoint_id' and pd.status='1' order by pd.suppl_id asc,pd.suppl_storage_id asc,pd.price_rating_id asc, pd.price_from asc;");$n=$db->num_rows($r);$list="";
	for ($i=1;$i<=$n;$i++){
		$id=$db->result($r,$i-1,"id");
		$client_name=$db->result($r,$i-1,"client_name");
		$suppl_storage_id=$db->result($r,0,"suppl_storage_id");
		$storage_name=$db->result($r,$i-1,"storage_name");
		$price_rating_name=$this->showPriceRatingName($db->result($r,$i-1,"price_rating_id"));
		$price_from=$db->result($r,$i-1,"price_from");
		$price_to=$db->result($r,$i-1,"price_to");
		$margin=$db->result($r,$i-1,"margin");
		$delivery=$db->result($r,$i-1,"delivery");
		$margin2=$db->result($r,$i-1,"margin2");
		$list.="
		<tr>
			<td>
				<button class='btn btn-sm btn-default' onClick='showTpointSupplFmForm(\"$tpoint_id\",\"$id\");'><i class='fa fa-edit'></i></button>
				<button class='btn btn-sm btn-default' onClick='dropTpointSupplFm(\"$tpoint_id\",\"$id\");'><i class='fa fa-times'></i></button>
			</td>
			<td>$i</td>
			<td>$client_name</td>
			<td>$storage_name</td>
			<td>$price_rating_name</td>
			<td>$price_from</td>
			<td>$price_to</td>
			<td>$margin</td>
			<td>$delivery</td>
			<td>$margin2</td>
		</tr>";
	}
	if ($n==0){$list="<tr><td align='center' colspan='9'><h3 class='text-center'>������ ������</h3></td></tr>";}
	$form=str_replace("{list_suppl_fm}",$list,$form);
	$form=str_replace("{tpoint_id}",$tpoint_id,$form);
	return $form;
}

function showTpointSupplFmForm($tpoint_id,$s_id){$db=DbSingleton::getTokoDb();$slave=new slave;$gmanual=new gmanual;
	$form_htm=RD."/tpl/tpoint_suppl_fm_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$r=$db->query("select * from T_POINT_SUPPL_FM where id='$s_id' and tpoint_id='$tpoint_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){
		$str_id=$db->result($r,0,"id");
		$suppl_id=$db->result($r,0,"suppl_id");
		$suppl_storage_id=$db->result($r,0,"suppl_storage_id");
		$price_rating_id=$db->result($r,0,"price_rating_id");
		$price_from=$db->result($r,0,"price_from");
		$price_to=$db->result($r,0,"price_to");
		$margin=$db->result($r,0,"margin");
		$delivery=$db->result($r,0,"delivery");
		$margin2=$db->result($r,0,"margin2");
	}
	$form=str_replace("{tpoint_id}",$tpoint_id,$form);
	$form=str_replace("{s_id}",$s_id,$form);
	$form=str_replace("{suppl_list}",$this->showTpointSupplSelectList($suppl_id),$form);
	$form=str_replace("{suppl_storage_list}",$this->showTpointSupplStorageSelectList($suppl_id,$suppl_storage_id),$form);
	$form=str_replace("{price_rating_list}",$this->showPriceRatingSelectList($price_rating_id),$form);
	$form=str_replace("{price_from}",$price_from,$form);
	$form=str_replace("{price_to}",$price_to,$form);
	$form=str_replace("{margin}",$margin,$form);
	$form=str_replace("{delivery}",$delivery,$form);
	$form=str_replace("{margin2}",$margin2,$form);
	$form=str_replace("{workers_list}",$this->showWorkersSelectList($media_user_id),$form);
	return $form;
}

function saveTpointSupplFmForm($tpoint_id,$s_id,$suppl_id,$suppl_storage_id,$price_rating_id,$price_from,$price_to,$margin,$delivery,$margin2){ $db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$slave=new slave;session_start();$media_user_id=$_SESSION["media_user_id"];$media_user_name=$_SESSION["user_name"];$answer=0;$err="������� ���������� �����!";
	$tpoint_id=$slave->qq($tpoint_id);$s_id=$slave->qq($s_id);$suppl_id=$slave->qq($suppl_id);$suppl_storage_id=$slave->qq($suppl_storage_id);$price_rating_id=$slave->qq($price_rating_id);$price_from=$slave->qq($price_from);$price_to=$slave->qq($price_to);$margin=$slave->qq($margin);$delivery=$slave->qq($delivery);$margin2=$slave->qq($margin2);
	if ($tpoint_id>0){
		if ($s_id==0 ){
			$r=$db->query("select max(id) as mid from T_POINT_SUPPL_FM;");$s_id=0+$db->result($r,0,"mid")+1;
			$db->query("insert into T_POINT_SUPPL_FM (id,tpoint_id,status) values ('$s_id','$tpoint_id','1');");
			$dbt->query("insert into T_POINT_SUPPL_FM (id,tpoint_id,status) values ('$s_id','$tpoint_id','1');");
		}
		if  ($s_id>0){
			$db->query("update T_POINT_SUPPL_FM set suppl_id='$suppl_id', `suppl_storage_id`='$suppl_storage_id', price_rating_id='$price_rating_id', price_from='$price_from', price_to='$price_to', margin='$margin', delivery='$delivery', margin2='$margin2' where id='$s_id' and tpoint_id='$tpoint_id';");
			$dbt->query("update T_POINT_SUPPL_FM set suppl_id='$suppl_id', `suppl_storage_id`='$suppl_storage_id', price_rating_id='$price_rating_id', price_from='$price_from', price_to='$price_to', margin='$margin', delivery='$delivery', margin2='$margin2' where id='$s_id' and tpoint_id='$tpoint_id';");
			$answer=1;$err="";
		}
	}else{$answer=0;}
	return array($answer,$err);
}

function dropTpointSupplFm($tpoint_id,$s_id){$db=DbSingleton::getTokoDb();$dbt=DbSingleton::getTokoDb();$slave=new slave;session_start();$media_user_id=$_SESSION["media_user_id"];$media_user_name=$_SESSION["user_name"];$answer=0;$err="������� ���������� �����!";
	$tpoint_id=$slave->qq($tpoint_id);$s_id=$slave->qq($s_id);
	if ($tpoint_id>0 && $s_id>0){
		$db->query("update T_POINT_SUPPL_FM set status='0' where id='$s_id' and tpoint_id='$tpoint_id';");
		$dbt->query("update T_POINT_SUPPL_FM set status='0' where id='$s_id' and tpoint_id='$tpoint_id';");
		$answer=1;$err="";
	}
	return array($answer,$err);
}

function getCashName($cash_id){$db=DbSingleton::getDb();$name="";
	$r=$db->query("select name from CASH where id ='$cash_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){$name=$db->result($r,0,"name");}
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
function loadTpointPayBox($tpoint_id){$db=DbSingleton::getDb();$slave=new slave;$gmanual=new gmanual;
	$form_htm=RD."/tpl/tpoint_pay_box_list.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$r=$db->query("select pb.* from T_POINT_PAY_BOX pb where pb.tpoint_id='$tpoint_id' and pb.status='1' order by pb.id asc;");$n=$db->num_rows($r);$list="";
	for ($i=1;$i<=$n;$i++){
		$id=$db->result($r,$i-1,"id");
		$client_id=$db->result($r,$i-1,"client_id");
		$client_name=$this->getAClientName($client_id);
		$name=$db->result($r,$i-1,"name");
		$in_use=$db->result($r,$i-1,"in_use"); $in_use_cap="-";if ($in_use==1){$in_use_cap="<i class='fa fa-eye'></i>";}
		$list.="
		<tr>
			<td>
				<button class='btn btn-sm btn-default' onClick='showTpointPayBoxForm(\"$tpoint_id\",\"$id\");'><i class='fa fa-edit'></i></button>
				<button class='btn btn-sm btn-default' onClick='dropTpointPayBox(\"$tpoint_id\",\"$id\");'><i class='fa fa-times'></i></button>
			</td>
			<td>$i</td>
			<td>$client_name</td>
			<td>$name</td>
			<td>$in_use_cap</td>
		</tr>";
	}
	if ($n==0){$list="<tr><td align='center' colspan=6><h3 class='text-center'>������ ������</h3></td></tr>";}
	$form=str_replace("{list_pay_box}",$list,$form);
	$form=str_replace("{tpoint_id}",$tpoint_id,$form);
	return $form;
}

function showTpointPayBoxForm($tpoint_id,$s_id){$db=DbSingleton::getDb();$slave=new slave;$gmanual=new gmanual;
	$form_htm=RD."/tpl/tpoint_pay_box_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$r=$db->query("select pb.* from T_POINT_PAY_BOX pb where pb.id='$s_id' and pb.tpoint_id='$tpoint_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){
		$str_id=$db->result($r,0,"id");
		$client_id=$db->result($r,0,"client_id");
		$client_name=$this->getAClientName($client_id);
		$name=$db->result($r,0,"name");
		$in_use=$db->result($r,0,"in_use");
	}
	$inuse_checked="";if ($in_use==1){$inuse_checked=" checked";}
	$form=str_replace("{tpoint_id}",$tpoint_id,$form);
	$form=str_replace("{s_id}",$s_id,$form);
	$form=str_replace("{client_id}",$client_id,$form);
	$form=str_replace("{client_name}",$client_name,$form);
	$form=str_replace("{name}",$name,$form);
	$form=str_replace("{inuse_checked}",$inuse_checked,$form);
	return $form;
}

function saveTpointPayBoxForm($tpoint_id,$s_id,$client_id,$name,$in_use){ $db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$slave=new slave;session_start();$media_user_id=$_SESSION["media_user_id"];$media_user_name=$_SESSION["user_name"];$answer=0;$err="������� ���������� �����!";
	$tpoint_id=$slave->qq($tpoint_id);$s_id=$slave->qq($s_id);$client_id=$slave->qq($client_id);$name=$slave->qq($name);$in_use=$slave->qq($in_use);
	if ($tpoint_id>0){
		if ($s_id==0 ){
			$r=$db->query("select max(id) as mid from T_POINT_PAY_BOX;");$s_id=0+$db->result($r,0,"mid")+1;
			$db->query("insert into T_POINT_PAY_BOX (id,tpoint_id,status) values ('$s_id','$tpoint_id','1');");
		}
		if  ($s_id>0){
			$db->query("update T_POINT_PAY_BOX set `client_id`='$client_id', name='$name', in_use='$in_use' where id='$s_id' and tpoint_id='$tpoint_id';");
			$answer=1;$err="";
		}
	}else{$answer=0;}
	return array($answer,$err);
}
function dropTpointPayBox($tpoint_id,$s_id){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$slave=new slave;session_start();$media_user_id=$_SESSION["media_user_id"];$media_user_name=$_SESSION["user_name"];$answer=0;$err="������� ���������� �����!";
	$tpoint_id=$slave->qq($tpoint_id);$s_id=$slave->qq($s_id);
	if ($tpoint_id>0 && $s_id>0){
		$db->query("update T_POINT_PAY_BOX set status='0' where id='$s_id' and tpoint_id='$tpoint_id';");
		$answer=1;$err="";
	}
	return array($answer,$err);
}


}
?>