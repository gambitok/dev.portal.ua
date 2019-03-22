<?php

class users {
	
function newUsersCard(){$db=DbSingleton::getDb();$slave=new slave;$manual=new manual; session_start();$user_id=$_SESSION["media_user_id"];$user_name=$_SESSION["user_name"]; $users_id=0;
	$r=$db->query("select max(id) as mid from media_users;");$users_id=0+$db->result($r,0,"mid")+1;
	$db->query("insert into media_users (`id`,`ison`) values ('$users_id','1');");
	return $users_id;
}

function show_users_list(){$db=DbSingleton::getDb();$slave=new slave;$where="";$list="";
	$r=$db->query("select mu.*, tp.name as tpoint_name, mr.caption as role_name, uss.mcaption as status_name
	from media_users mu 
		left outer join T_POINT tp on tp.id=mu.tpoint_id 
		left outer join media_role mr on mr.id=mu.role_id
		left outer join manual uss on uss.id=mu.status and uss.`key`='user_status'
	where mu.ison=1 $where;");$n=$db->num_rows($r);
	for ($i=1;$i<=$n;$i++){
		$id=$db->result($r,$i-1,"id");
		$name=$db->result($r,$i-1,"name");
		$tpoint_name=$db->result($r,$i-1,"tpoint_name");
		$role_name=$db->result($r,$i-1,"role_name");
		$post=$db->result($r,$i-1,"post");
		$phone=$db->result($r,$i-1,"phone");
		$email=$db->result($r,$i-1,"email");
		$status_name=$db->result($r,$i-1,"status_name");

		$list.="<tr style='cursor:pointer' onClick='showUsersCard(\"$id\")'>
				<td>$id</td>
				<td>$tpoint_name</td>
				<td>$role_name</td>
				<td>$phone</td>
				<td>$name</td>
				<td>$post</td>
				<td>$email</td>
				<td>$status_name</td>
			</tr>";
	}
	return $list;
}
	
function showTrustedIPList(){$db=DbSingleton::getDb();$slave=new slave;$where="";$list="";
	$r=$db->query("select * from trusted_ip where status=1;");$n=$db->num_rows($r);
	for ($i=1;$i<=$n;$i++){
		$id=$db->result($r,$i-1,"id");
		$ip=$db->result($r,$i-1,"ip");
		$descr=$db->result($r,$i-1,"descr");
		$list.="<tr style='cursor:pointer' onClick='showTrustedIPCard(\"$id\")'>
			<td>$id</td>
			<td>$ip</td>
			<td>$descr</td>
		</tr>";
	}
	return $list;
}
	
function newTrustedIPCard() {$db=DbSingleton::getDb();
	$r=$db->query("select max(id) as mid from trusted_ip;");$trusted_id=0+$db->result($r,0,"mid")+1;
	$db->query("insert into trusted_ip (`id`,`status`) values ('$trusted_id',1);");
	return $trusted_id;
}
	
function showTrustedIPCard($trusted_id){$db=DbSingleton::getDb();$slave=new slave;session_start();$user_id=$_SESSION["media_user_id"];$user_name=$_SESSION["user_name"];
	$form_htm=RD."/tpl/trusted_ip_card.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$r=$db->query("select * from trusted_ip where id='$trusted_id' and status=1;");$n=$db->num_rows($r);
	if ($n==0){$form_htm=RD."/tpl/access_deny.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);} }
	if ($n==1){
		$trusted_id=$db->result($r,0,"id");
		$trusted_ip=$db->result($r,0,"ip");
		$trusted_descr=$db->result($r,0,"descr");
		$form=str_replace("{trusted_id}",$trusted_id,$form);
		$form=str_replace("{trusted_ip}",$trusted_ip,$form);
		$form=str_replace("{trusted_descr}",$trusted_descr,$form);
	}
	return $form;
}
	
function saveTrustedIPGeneralInfo($trusted_id,$trusted_ip,$descr){$db=DbSingleton::getDb();
  $slave=new slave;session_start();$user_id=$_SESSION["media_user_id"];$user_name=$_SESSION["user_name"];$answer=0;$err="Помилка збереження даних!";
	if ($trusted_id>0){
		$r=$db->query("select * from trusted_ip where ip='$trusted_ip' and status=1 limit 1;");$n=$db->num_rows($r);
		if ($n==0){
			$db->query("update trusted_ip set `ip`='$trusted_ip', descr='$descr' where `id`='$trusted_id';");
			$answer=1;$err="";
		} else {
			$answer=0;$err="Вказаний IP вже доданий";
		}
	}
	return array($answer,$err);
}
	
function dropTrustedIP($trusted_id) {$db=DbSingleton::getDb(); $answer=0;$err="Помилка збереження даних!";
	if ($trusted_id>0) {
		$db->query("update trusted_ip set `status`=0 where `id`='$trusted_id';");	
		$answer=1;$err="";
	}
	return array($answer,$err);	
}

function showUsersCard($users_id){$db=DbSingleton::getDb();$slave=new slave;session_start();$user_id=$_SESSION["media_user_id"];$user_name=$_SESSION["user_name"];
	$form_htm=RD."/tpl/users_card.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	
	$r=$db->query("select * from media_users where id='$users_id' and ison='1' limit 0,1;");$n=$db->num_rows($r);
	if ($n==0){$form_htm=RD."/tpl/access_deny.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);} }
	if ($n==1){
		$users_id=$db->result($r,0,"id");
		$name=$db->result($r,0,"name");
		$post=$db->result($r,0,"post");
		$tpoint_id=$db->result($r,0,"tpoint_id");
		$role_id=$db->result($r,0,"role_id");
		$phone=$db->result($r,0,"phone");
		$phone2=$db->result($r,0,"phone2"); 
		$email=$db->result($r,0,"email");
		$status=$db->result($r,0,"status");
		$pass=$db->result($r,0,"pass");
		
		$form=str_replace("{users_id}",$users_id,$form);
		$form=str_replace("{users_name}",$name,$form);
		$form=str_replace("{post}",$post,$form);
		$form=str_replace("{tpoints_list}",$this->showTpointSelectList($tpoint_id),$form);
		$form=str_replace("{role_list}",$this->showRoleSelectList($role_id),$form);
		$form=str_replace("{phone}",$phone,$form);
		$form=str_replace("{phone2}",$phone2,$form);
		$form=str_replace("{email}",$email,$form);
		$form=str_replace("{users_pass}",$pass,$form);
		$form=str_replace("{status_list}",$this->showUserStatusSelectList($status),$form);
		$form=str_replace("{my_user_id}",$user_id,$form);
		$form=str_replace("{my_user_name}",$user_name,$form);
	}
	return $form;
}

function saveUsersGeneralInfo($users_id,$name,$post,$tpoint_id,$role_id,$phone2,$login,$pass,$status,$email){$db=DbSingleton::getDb();$slave=new slave;session_start();$user_id=$_SESSION["media_user_id"];$user_name=$_SESSION["user_name"];$answer=0;$err="Помилка збереження даних!";
	$users_id=$slave->qq($users_id);$name=$slave->qq($name);$post=$slave->qq($post);$tpoint_id=$slave->qq($tpoint_id);$role_id=$slave->qq($role_id);
	$phone2=$slave->qq($phone2);$login=$slave->qq($login);$pass=$slave->qq($pass);$status=$slave->qq($status);$email=$slave->qq($email);
	if ($users_id>0){
		$r=$db->query("select * from media_users where ison='1' and phone='$login' and id!='$users_id' limit 0,1;");$n=$db->num_rows($r);
		if ($n==0){
			$db->query("update media_users set `name`='$name',status='$status', `post`='$post', `tpoint_id`='$tpoint_id', `role_id`='$role_id', `email`='$email', `phone2`='$phone2', `phone`='$login', `pass`='$pass' where `id`='$users_id';");
			$answer=1;$err="";
		}if ($n==1){
			$answer=0;$err="Користувач із вказаним логіном вже існує у системі";
		}
	}
	return array($answer,$err);
}
	
function showTpointSelectList($sel_id){$db=DbSingleton::getDb();$list="";;
	$r=$db->query("select * from T_POINT where status=1 order by name,id asc;");$n=$db->num_rows($r);
	for ($i=1;$i<=$n;$i++){
		$id=$db->result($r,$i-1,"id");
		$name=$db->result($r,$i-1,"name");
		$sel="";if ($id==$sel_id){$sel=" selected";}
		$list.="<option value='$id' $sel>$name</option>";
	}
	return $list;
}
	
function showRoleSelectList($sel_id){$db=DbSingleton::getDb();$list="";;
	$r=$db->query("select * from media_role where status=1 order by caption,id asc;");$n=$db->num_rows($r);
	for ($i=1;$i<=$n;$i++){
		$id=$db->result($r,$i-1,"id");
		$name=$db->result($r,$i-1,"caption");
		$sel="";if ($id==$sel_id){$sel=" selected";}
		$list.="<option value='$id' $sel>$name</option>";
	}
	return $list;
}

function loadUsersAccess($users_id){$db=DbSingleton::getDb();$list="";
	$form_htm=RD."/tpl/users_access_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$r=$db->query("select mf.*,rs.lvl, al.caption as level_name
	from module_files mf 
		left outer join media_users_role_structure rs on (rs.file_id=mf.id and rs.user_id='$users_id')
		left outer join access_level al on al.id=rs.lvl
	where mf.system=1 order by mf.id asc;");$n=$db->num_rows($r);
									
	for ($i=1;$i<=$n;$i++){
		$mf_id=$db->result($r,$i-1,"mf.id");
		$mf_caption=$db->result($r,$i-1,"mf.caption");
		$lvl=$db->result($r,$i-1,"rs.lvl");
		$level_name=$db->result($r,$i-1,"level_name");
		$access="Відсутній";if ($lvl>0){$access="Доступ";}
		$list.="<tr>
			<td>
				<button class='btn btn-sm btn-default' onClick='showUsersAccessItemForm(\"$users_id\",\"$mf_id\");'><i class='fa fa-edit'></i></button>
			</td>
			<td>$i</td>
			<td>$mf_caption</td>
			<td>$access</td>
			<td>$level_name</td>
		</tr>";
	}
	if ($n==0){$list="<tr><td align='center' colspan=5><h3 class='text-center'>Записи відсутні</h3></td></tr>";}
	$form=str_replace("{list_range}",$list,$form);
	$form=str_replace("{users_id}",$users_id,$form);
	return $form;
}
	
function loadUsersAccessCredit($users_id){$db=DbSingleton::getDb();
	$form_htm=RD."/tpl/users_access_credit.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$r=$db->query("select * from media_users where id='$users_id' limit 1;");
	$access_credit=$db->result($r,0,"access_credit");								  
	$form=str_replace("{access_credit}",$access_credit,$form);
	$form=str_replace("{users_id}",$users_id,$form);
	return $form;
}
	
function loadUsersAccessTime($users_id){$db=DbSingleton::getDb();
	$form_htm=RD."/tpl/users_access_time.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$r=$db->query("select * from media_users_time where id='$users_id' limit 1;");
	$access=$db->result($r,0,"access");	
	$access_time=$db->result($r,0,"access_time");	
	$access_time_from=$db->result($r,0,"time_from");	
	$access_time_to=$db->result($r,0,"time_to");										
	$access_checked="";if ($access>0){$access_checked=" checked";}
	$access_time_checked="";if ($access_time>0){$access_time_checked=" checked";}
										
	$form=str_replace("{access_checked}",$access_checked,$form);
	$form=str_replace("{access_time_checked}",$access_time_checked,$form);
	$form=str_replace("{access_time_from}",$access_time_from,$form);
	$form=str_replace("{access_time_to}",$access_time_to,$form);
	$form=str_replace("{users_id}",$users_id,$form);
	return $form;
}
	
function saveUsersAccessTime($users_id,$access,$access_time,$time_from,$time_to){$db=DbSingleton::getDb();$answer=0;$err="Помилка збереження даних!";
	if ($users_id>0) {
		$r=$db->query("select * from media_users_time where id='$users_id' limit 1;"); $n=$db->num_rows($r);
		if ($n>0)
			$db->query("update media_users_time set access='$access', access_time='$access_time', time_from='$time_from', time_to='$time_to' where id='$users_id';");
		else 
			$db->query("insert into media_users_time (id,access,access_time,time_from,time_to) values ('$users_id','$access','$access_time','$time_from','$time_to');");
		$answer=1;$err="";
	}
	return array($answer,$err);
}
	
function saveUsersAccessCredit($users_id,$credit){$db=DbSingleton::getDb();$answer=0;$err="Помилка збереження даних!";
	if ($users_id>0) {
		$r=$db->query("update media_users set access_credit='$credit' where id='$users_id';");
		$answer=1;$err="";
	}
	return array($answer,$err);
}
	
function getUsersAccessCredit($users_id) {$db=DbSingleton::getDb();
	$r=$db->query("select access_credit from media_users where id='$users_id' limit 1;");
	$access_credit=$db->result($r,0,"access_credit");
	return $access_credit;
}
	
function clearUsersAcсess($users_id){$db=DbSingleton::getDb();
	$slave=new slave;session_start();$media_user_id=$_SESSION["media_user_id"];$media_user_name=$_SESSION["user_name"];$answer=0;$err="Помилка збереження даних!";
	$users_id=$slave->qq($users_id);
	if ($users_id>0){
		$db->query("delete from media_users_role_structure where user_id='$users_id';");
		$answer=1;$err="";
	}
	return array($answer,$err);
}

function showUsersAccessItemForm($users_id,$mf_id){$db=DbSingleton::getDb();$slave=new slave;$gmanual=new gmanual;
	$form_htm=RD."/tpl/users_access_item_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$r=$db->query("select mf.*,rs.lvl
	from module_files mf 
		left outer join media_users_role_structure rs on (rs.file_id=mf.id and rs.user_id='$users_id')
	where mf.system=1 and mf.id='$mf_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){
		$mf_caption=$db->result($r,0,"mf.caption");
		$lvl=$db->result($r,0,"lvl");
		$in_use=$db->result($r,0,"in_use");
	}
	$access_checked="";if ($lvl>0){$access_checked=" checked";}
	$form=str_replace("{users_id}",$users_id,$form);
	$form=str_replace("{mf_id}",$mf_id,$form);
	$form=str_replace("{mf_caption}",$mf_caption,$form);
	$form=str_replace("{access_checked}",$access_checked,$form);
	$form=str_replace("{lvl_list}",$this->showAccessLevelSelectList($lvl),$form);
	return $form;
}

function showAccessLevelSelectList($sel_id){$db=DbSingleton::getDb();$list="";;
	$r=$db->query("select * from access_level where id<=6 order by id asc;");$n=$db->num_rows($r);
	for ($i=1;$i<=$n;$i++){
		$id=$db->result($r,$i-1,"id");
		$name=$db->result($r,$i-1,"caption");
		$sel="";if ($id==$sel_id){$sel=" selected";}
		$list.="<option value='$id' $sel>$name</option>";
	}
	return $list;
}

function saveUsersAccessItemForm($users_id,$mf_id,$lvl_id,$file_access){ $db=DbSingleton::getDb();$slave=new slave;session_start();$media_user_id=$_SESSION["media_user_id"];$media_user_name=$_SESSION["user_name"];$answer=0;$err="Помилка збереження даних!";
	$users_id=$slave->qq($users_id);$mf_id=$slave->qq($mf_id);$lvl_id=$slave->qq($lvl_id);$file_access=$slave->qq($file_access);
	if ($users_id>0 && $mf_id>0){
		if ($file_access==0){
			$db->query("delete from media_users_role_structure where user_id='$users_id' and file_id='$mf_id' limit 1;");
			$answer=1;$err="";
		}
		if  ($file_access==1){
			$db->query("delete from media_users_role_structure where user_id='$users_id' and file_id='$mf_id' limit 1;");
			$db->query("insert into media_users_role_structure (`user_id`,`file_id`,`lvl`) values ('$users_id','$mf_id','$lvl_id');"); 
			$answer=1;$err="";
		}
	}
	else{$answer=0;}
	return array($answer,$err);
}

function getMediaUserName($user_id){$db=DbSingleton::getDb();$name="";
	$r=$db->query("select name from media_users where id='$user_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){$name=$db->result($r,0,"name");}
	return $name;
}

function showUserStatusSelectList($sel_id){$db=DbSingleton::getDb();$list="";;
	$r=$db->query("select * from manual where `key`='user_status' and  ison=1 order by mcaption,id asc;");$n=$db->num_rows($r);
	for ($i=1;$i<=$n;$i++){
		$id=$db->result($r,$i-1,"id");
		$name=$db->result($r,$i-1,"mcaption");
		$sel="";if ($id==$sel_id){$sel=" selected";}
		$list.="<option value='$id' $sel>$name</option>";
	}
	return $list;
}

function resetDbZero(){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$slave=new slave;session_start();$media_user_id=$_SESSION["media_user_id"];$media_user_name=$_SESSION["user_name"];$answer=0;$err="Помилка збереження даних!";
	if ($media_user_id!=1 && $media_user_id!=2 && $media_user_id!=7){
		$answer=0;$err="А пупчик не розвяжеться? доступ обмежено";
	}
	if ($media_user_id==1 || $media_user_id==2 || $media_user_id==7){
		$db->query("update J_INCOME set oper_status='30' where oper_status='31';");
		$db->query("TRUNCATE TABLE `J_DP`;");
		$db->query("TRUNCATE TABLE `J_DP_STR`;");
		$db->query("TRUNCATE TABLE `J_DP_SUPPL_ORDER`;");
		$db->query("TRUNCATE TABLE `J_DP_DOCUMENT_STR`;");
		$db->query("TRUNCATE TABLE `J_DP_COMMENTS`;");
		$db->query("TRUNCATE TABLE `J_DP_NOTE`;");		
		$db->query("TRUNCATE TABLE `J_MOVING`;");
		$db->query("TRUNCATE TABLE `J_MOVING_SELECT`;");
		$db->query("TRUNCATE TABLE `J_MOVING_CDN`;");
		$db->query("TRUNCATE TABLE `J_MOVING_COMMENTS`;");
		$db->query("TRUNCATE TABLE `J_MOVING_LOCAL_SELECT_STR_BUG`;");
		$db->query("TRUNCATE TABLE `J_MOVING_LOCAL_SELECT_STR_TEMP`;");
		$db->query("TRUNCATE TABLE `J_MOVING_LOCAL_SELECT_TEMP`;");
		$db->query("TRUNCATE TABLE `J_MOVING_SELECT_JOURNAL`;");
		$db->query("TRUNCATE TABLE `J_MOVING_SELECT_STR`;");
		$db->query("TRUNCATE TABLE `J_MOVING_SELECT_STR_BUG`;");
		$db->query("TRUNCATE TABLE `J_MOVING_SELECT_STR_TEMP`;");
		$db->query("TRUNCATE TABLE `J_MOVING_SELECT_TEMP`;");
		$db->query("TRUNCATE TABLE `J_MOVING_STR`;");
		$db->query("TRUNCATE TABLE `J_MOVING_STR_BUG`;");
		$db->query("TRUNCATE TABLE `J_MOVING_STR_UNKNOWN`;");
		$db->query("TRUNCATE TABLE `J_PAY`;");
		$db->query("TRUNCATE TABLE `J_PAY_STR`;");
		$db->query("TRUNCATE TABLE `J_SALE_INVOICE`;");
		$db->query("TRUNCATE TABLE `J_SALE_INVOICE_STORSEL`;");
		$db->query("TRUNCATE TABLE `J_SALE_INVOICE_STR`;");
		$db->query("TRUNCATE TABLE `J_SELECT`;");
		$db->query("TRUNCATE TABLE `J_SELECT_COMMENTS`;");
		$db->query("TRUNCATE TABLE `J_SELECT_JOURNAL`;");
		$db->query("TRUNCATE TABLE `J_SELECT_STR`;");
		$db->query("TRUNCATE TABLE `J_SELECT_STR_BUG`;");
		$db->query("TRUNCATE TABLE `J_SELECT_STR_TEMP`;");
		$db->query("TRUNCATE TABLE `T2_ARTICLES_PARTITIONS`;");
		$db->query("TRUNCATE TABLE `J_SALE_INVOICE_PARTITION_STR`;");
		//$db->query("TRUNCATE TABLE `T2_ARTICLES_PRICE_STOCK`;");
		$db->query("TRUNCATE TABLE `B_CLIENT_AVANS`;");
		$db->query("TRUNCATE TABLE `B_CLIENT_BALANS`;");
		$db->query("TRUNCATE TABLE `B_CLIENT_BALANS_JOURNAL`;");
		$db->query("TRUNCATE TABLE `B_CLIENT_BALANS_PERIOD`;");
		$db->query("TRUNCATE TABLE `B_PAYBOX_BALANS`;");
		$db->query("TRUNCATE TABLE `B_PAYBOX_BALANS_PERIOD`;");
		$db->query("TRUNCATE TABLE `B_PAYBOX_JOURNAL`;");
		$dbt->query("TRUNCATE TABLE `T2_ARTICLES_PRICE_STOCK`;");
		$dbt->query("TRUNCATE TABLE `T2_ARTICLES_STRORAGE`;");
		$dbt->query("TRUNCATE TABLE `T2_ARTICLES_STRORAGE_CELLS`;");
		$db->query("TRUNCATE TABLE `J_BACK_CLIENTS`;");
		$db->query("TRUNCATE TABLE `J_BACK_CLIENTS_CDN`;");
		$db->query("TRUNCATE TABLE `J_BACK_CLIENTS_COMMENTS`;");
		$db->query("TRUNCATE TABLE `J_BACK_CLIENTS_STR`;");
		$db->query("TRUNCATE TABLE `J_INCOME`;");
		$db->query("TRUNCATE TABLE `J_INCOME_CDN`;");
		$db->query("TRUNCATE TABLE `J_INCOME_COMMENTS`;");
		$db->query("TRUNCATE TABLE `J_INCOME_STR`;");
		$db->query("TRUNCATE TABLE `J_INCOME_STR_UNKNOWN`;");
		$db->query("TRUNCATE TABLE `J_INCOME_SPEND_STR`;");
		$db->query("TRUNCATE TABLE `J_INCOME_SPEND_FILES`;");
		$db->query("TRUNCATE TABLE `J_INCOME_CSV`;");
		$db->query("TRUNCATE TABLE `J_MONEY_MOVE`;");
		$db->query("TRUNCATE TABLE `J_MONEY_SPEND`;");
		$db->query("TRUNCATE TABLE `orders_new`;");
		$db->query("TRUNCATE TABLE `orders_str_new`;");
		$db->query("TRUNCATE TABLE `cron_task_prices`;");
		//$db->query("TRUNCATE TABLE `A_CLIENTS_USERS_RETAIL`;");
		$dbt->query("TRUNCATE TABLE `basket`;");
		$answer=1;$err="";
	}
	return array($answer,$err);
}
	
function getSuperUser($user_id) {$db=DbSingleton::getDb();
	$r=$db->query("select * from media_users where id='$user_id' and access=1;");
	$n=$db->num_rows($r);
	$n>0 ? $status=true : $status=false;
	return $status;
}

function getManagerUser($user_id) {$db=DbSingleton::getDb();
	$r=$db->query("select * from media_users where id='$user_id' and access>0;");
	$n=$db->num_rows($r);
	$n>0 ? $status=true : $status=false;
	return $status;
}
	
}
?>