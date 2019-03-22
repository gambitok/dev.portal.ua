<?php
class money_spend{

function getMediaUserName($user_id){$db=DbSingleton::getDb();$name="";
	$r=$db->query("select name from media_users where id='$user_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){$name=$db->result($r,0,"name");}
	return $name;
}
function show_money_spend_list(){$db=DbSingleton::getDb();$slave=new slave;$gmanual=new gmanual;$dp=new dp; $where="";$limit ="limit 0,300"; if ($where!=""){$limit="";}
	$r=$db->query("select j.*, CASH.name as cash_name, pf.name as name_from, pf.full_name as full_name_from, muf.name, m.mcaption as spend_type_caption
	from J_MONEY_SPEND j
	left outer join CASH on CASH.id=j.cash_id
	left outer join PAY_BOX pf on pf.id=j.paybox_id_from
	left outer join media_users muf on muf.id=j.user_id_from
	left outer join manual m on (m.`key`='spend_type_id' and m.id=j.spend_type_id)
	where j.status=1 $where order by j.id desc $limit;");$n=$db->num_rows($r);$list="";
	for ($i=1;$i<=$n;$i++){
		$id=$db->result($r,$i-1,"id");
		$data=$db->result($r,$i-1,"data");
		$cash_name=$db->result($r,$i-1,"cash_name");
		$cash_id=$db->result($r,$i-1,"cash_id");
		$summ=$db->result($r,$i-1,"summ");
		$comment=$db->result($r,$i-1,"comment");
		$paybox_id_from=$db->result($r,$i-1,"paybox_id_from");
		$paybox_name_from=$db->result($r,$i-1,"name_from");if ($paybox_name_from==""){$paybox_name_from=$db->result($r,$i-1,"full_name_from");}
		$user_name_from=$db->result($r,$i-1,"muf.name");
		$spend_type_caption=$db->result($r,$i-1,"spend_type_caption");
		$spend_type_id=$db->result($r,$i-1,"spend_type_id");
		
		$function="viewMoneySpend(\"$id\")";
		$list.="<tr style='cursor:pointer' onClick='$function'>
				<td align='center'>ВГ-$id</td>
				<td align='center'>$data</td>
				<td align='center'>$cash_name</td>
				<td align='center'>$summ</td>
				<td align='center'>$spend_type_caption</td>
				<td>$paybox_name_from/$user_name_from</td>
				<td>$comment</td>
			</tr>";
	}
	return $list;
}

function loadMoneySpendCashBoxList($client_id,$doc_type_id,$seller_id){$db=DbSingleton::getDb();$list="";
	$list=$this->showPayBoxSelectList(0,$doc_type_id,$seller_id);
	return $list;
}
function showMoneySpendForm(){$db=DbSingleton::getDb();$cat=new catalogue;$slave=new slave;$manual=new manual;$gmanual=new gmanual; session_start(); $user_id=$_SESSION["media_user_id"]; $user_name=$_SESSION["user_name"];  
	$form_htm=RD."/tpl/money_spend_form.htm";	if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$cash_kours="1";$cash_id=1;$cash_name=$this->getCashName($cash_id);
	
	$form=str_replace("{user_id}",$user_id,$form);
	$form=str_replace("{cash_name}",$cash_name,$form);
	$form=str_replace("{doc_cash_id}",$cash_id,$form);
	$form=str_replace("{paybox_from_list}",$this->showPayBoxUserSelectList($user_id),$form);
	$form=str_replace("{spend_type_list}",$gmanual->showGmanualSelectList("spend_type_id",0),$form);
	$form=str_replace("{disabled}","",$form);
	return $form;
}
function viewMoneySpend($spend_id){$db=DbSingleton::getDb();$cat=new catalogue;$slave=new slave;$manual=new manual;session_start();$user_id=$_SESSION["media_user_id"];$user_name=$_SESSION["user_name"]; $storsel=new storsel; $gmanual=new gmanual; 
	$form_htm=RD."/tpl/money_spend_view.htm";	if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$r=$db->query("select j.*, CASH.name as cash_name, pf.name as name_from, pf.full_name as full_name_from, muf.name, m.mcaption as spend_type_caption
	from J_MONEY_SPEND j
	left outer join CASH on CASH.id=j.cash_id
	left outer join PAY_BOX pf on pf.id=j.paybox_id_from
	left outer join media_users muf on muf.id=j.user_id_from
	left outer join manual m on (m.`key`='spend_type_id' and m.id=j.spend_type_id)
	where j.status=1 and j.id='$spend_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){
		
		$id=$db->result($r,0,"id");
		$data=$db->result($r,0,"data");
		$cash_name=$db->result($r,0,"cash_name");
		$cash_id=$db->result($r,0,"cash_id");
		$summ=$db->result($r,0,"summ");
		$comment=$db->result($r,0,"comment");
		$paybox_id_from=$db->result($r,0,"paybox_id_from");
		$paybox_name_from=$db->result($r,0,"name_from");if ($paybox_name_from==""){$paybox_name_from=$db->result($r,0,"full_name_from");}
		$user_name_from=$db->result($r,0,"muf.name");
		$spend_type_caption=$db->result($r,0,"spend_type_caption");
		$spend_type_id=$db->result($r,0,"spend_type_id");
		
	}
	
	if ($spend_id==0){$form_htm=RD."/tpl/access_deny.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);} }
	
	$form=str_replace("{spend_id}",$spend_id,$form);
	$form=str_replace("{user_id}",$user_id,$form);
	$form=str_replace("{cash_name}",$cash_name,$form);
	$form=str_replace("{balans_id_from}",$user_name_from,$form);
	$form=str_replace("{paybox_from_name}",$paybox_name_from,$form);
	$form=str_replace("{spend_type_name}",$spend_type_caption,$form);
	$form=str_replace("{doc_data}",$slave->data_word($data_time),$form);
	$form=str_replace("{summ}",$summ,$form);
	$form=str_replace("{comment}",$comment,$form);
	return $form;
}
function showPayBoxSelectList($paybox_id,$doc_type_id,$seller_id){$db=DbSingleton::getDb(); session_start();$user_id=$_SESSION["media_user_id"];$list="";
	$where_seller="";if ($seller_id>0){$where_seller=" and pb.firm_id='$seller_id'";}
	$r=$db->query("select pb.* from PAY_BOX pb left outer join PAY_BOX_WORKERS pbw on pbw.paybox_id=pb.id where pbw.worker_id='$user_id' and pb.doc_type_id='$doc_type_id' $where_seller and pbw.status=1 and pb.status=1 and pb.in_use=1 order by pb.name asc;");
	$n=$db->num_rows($r);
	for ($i=1;$i<=$n;$i++){
		$id=$db->result($r,$i-1,"id");
		$name=$db->result($r,$i-1,"name");
		$list.="<option value='$id'";if ($i==1){$list.=" selected";}$list.=">$name</option>";
	}
	return $list;
}
function showPayBoxUserSelectList($user_id,$paybox_id){$db=DbSingleton::getDb(); $list="";
	if ($user_id=="" || $user_id==0){session_start();$user_id=$_SESSION["media_user_id"];}
	$r=$db->query("select DISTINCT pb.* from PAY_BOX pb 
	left outer join PAY_BOX_WORKERS pbw on pbw.paybox_id=pb.id 
	left outer join B_PAYBOX_BALANS pbb on pbb.paybox_id=pb.id 
	where pbw.worker_id='$user_id' and pbb.user_id='$user_id' and pbb.saldo>0 and pbw.status=1 and pb.status=1 and pb.in_use=1 order by pb.name asc;");
	$n=$db->num_rows($r);
	for ($i=1;$i<=$n;$i++){
		$id=$db->result($r,$i-1,"id");
		$name=$db->result($r,$i-1,"name");
		$list.="<option value='$id'";if ($id==$paybox_id){$list.=" selected";}$list.=">$name</option>";
	}
	return $list;
}
	
function getPayboxUserCashSaldoList($paybox_id,$user_id){$db=DbSingleton::getDb(); $list="<option value=\"0\">--Оберіть зі списку--</option>";
	if ($user_id>0 || $paybox_id>0 ){
		$r=$db->query("select pb.* from B_PAYBOX_BALANS pb where pb.user_id='$user_id' and pb.paybox_id='$paybox_id' order by pb.cash_id asc;");$n=$db->num_rows($r);
		for ($i=1;$i<=$n;$i++){
			$id=$db->result($r,$i-1,"id");
			$saldo=$db->result($r,$i-1,"saldo");
			$cash_id=$db->result($r,$i-1,"cash_id");$cash_name=$this->getCashAbr($cash_id);
			$list.="<option value='$id' max-saldo='$saldo'>$saldo $cash_name</option>";
		}
	}
	return $list;
}
function getPayboxResiverList($paybox_id,$balans_id_from, $user_id){$db=DbSingleton::getDb(); $list="<option value=\"0\">--Оберіть зі списку--</option>";
	$paybox_type_id=$this->getPayBoxType($paybox_id);
	$r=$db->query("select pb.* from PAY_BOX pb left outer join PAY_BOX_WORKERS pbw on pbw.paybox_id=pb.id where pbw.worker_id!='$user_id' and pb.doc_type_id='$paybox_type_id' and pbw.status=1 and pb.status=1 and pb.in_use=1 group by pb.id order by pb.name asc;");
	$n=$db->num_rows($r);
	for ($i=1;$i<=$n;$i++){
		$id=$db->result($r,$i-1,"id");
		$name=$db->result($r,$i-1,"name");
		$list.="<option value='$id'>$name</option>";
	}
	return $list;
}
function getPayboxManagerList($paybox_id,$balans_id_from){$db=DbSingleton::getDb(); $list="<option value=\"0\">--Оберіть зі списку--</option>";
	$paybox_type_id=$this->getPayBoxType($paybox_id);
	$r=$db->query("select pbw.* from PAY_BOX pb left outer join PAY_BOX_WORKERS pbw on pbw.paybox_id=pb.id where  pb.doc_type_id='$paybox_type_id' and pbw.status=1 and pb.status=1 and pb.in_use=1 group by pb.id order by pb.name asc;");
	$n=$db->num_rows($r);
	for ($i=1;$i<=$n;$i++){
		$worker_id=$db->result($r,$i-1,"worker_id");
		$worker_name=$this->getMediaUserName($worker_id);
		$list.="<option value='$worker_id'>$worker_name</option>";
	}
	return $list;
}


function getPayBoxType($paybox_id){$db=DbSingleton::getDb(); $type_id=0;
	$r=$db->query("select doc_type_id from PAY_BOX where id='$paybox_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){$type_id=$db->result($r,0,"doc_type_id");}
	return $type_id;
}	
function getPayBoxUserBalans($paybox_id,$user_id,$cash_id){$db=DbSingleton::getDb(); $saldo=0;
	$r=$db->query("select saldo from B_PAYBOX_BALANS where user_id='$user_id' and paybox_id='$paybox_id' and cash_id='$cash_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){
		$saldo=$db->result($r,0,"saldo");
	}
	return $saldo;
}

function getPayBoxBalans($paybox_id){$db=DbSingleton::getDb(); session_start();$user_id=$_SESSION["media_user_id"];$list="---";
	$r=$db->query("select pb.* from B_PAYBOX_BALANS pb where pb.user_id='$user_id' and pb.paybox_id='$paybox_id' order by pb.id asc;");$n=$db->num_rows($r);
	if ($n>0){$list="";
		for ($i=1;$i<=$n;$i++){
			$saldo=$db->result($r,$i-1,"saldo");
			$cash_id=$db->result($r,$i-1,"cash_id");$cash_abr=$this->getCashAbr($cash_id);
			$last_update=$db->result($r,$i-1,"last_update");
			$list.="<strong>$saldo $cash_abr</strong>-$last_update<br>";
		}
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

function getPayBoxUserBalansById($id){$db=DbSingleton::getDb(); $saldo=$cash_id=$user_id_from=0;
	$r=$db->query("select * from B_PAYBOX_BALANS where id='$id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){
		$saldo=$db->result($r,0,"saldo");
		$cash_id=$db->result($r,0,"cash_id");
		$user_id_from=$db->result($r,0,"user_id");
	}
	return array($saldo,$cash_id,$user_id_from);
}
	
function saveMoneySpend($paybox_id_from,$balans_id_from,$spend_type_id,$summ,$comment){$db=DbSingleton::getDb();$slave=new slave;session_start();$user_id=$_SESSION["media_user_id"];$user_name=$_SESSION["user_name"];$answer=0;$err="Помилка збереження даних!";
	$paybox_id_from=$slave->qq($paybox_id_from);$spend_type_id=$slave->qq($spend_type_id);$comment=$slave->qq($comment);
	$balans_id_from=$slave->qq($balans_id_from);$summ=$slave->qq($summ);
	if ($paybox_id_from>0 && $spend_type_id>0 && $balans_id_from>0 && $summ>=0){
		list($current_balans_summ,$cash_id,$user_id_from)=$this->getPayBoxUserBalansById($balans_id_from);
		if ($summ>$current_balans_summ){ $answer=0;$err="Сума видачы вже більша за наявну у касі!"; }
		if ($summ<=$current_balans_summ){
			$r=$db->query("select max(id) as mid from J_MONEY_SPEND;"); $spend_id=$db->result($r,0,"mid")+1;
			$db->query("insert into J_MONEY_SPEND (`id`,`paybox_id_from`,`user_id_from`,`spend_type_id`,`cash_id`,`summ`,`comment`) value ('$spend_id','$paybox_id_from','$user_id_from','$spend_type_id','$cash_id','$summ','$comment');");
			$this->updatePayboxBalans($paybox_id_from,2,$cash_id,$summ,$user_id_from,$spend_id);
			$answer=1;$err="";
		}
	}
	return array($answer,$err,$spend_id);
}


function updatePayboxBalans($paybox_id,$deb_kre,$cash_id,$summ,$user_id,$money_spend_id){$db=DbSingleton::getDb();
	$r=$db->query("select count(id) as kol from B_PAYBOX_BALANS where paybox_id='$paybox_id' and cash_id='$cash_id' and user_id='$user_id';");$ex=$db->result($r,0,"kol"); if ($deb_kre==2){ $summ=$summ*-1; }
	if ($ex==0){
		$db->query("insert into B_PAYBOX_BALANS (`paybox_id`,`saldo`,`cash_id`,`user_id`) values ('$paybox_id','$summ','$cash_id','$user_id');");
	}
	if ($ex>0){
		$db->query("update B_PAYBOX_BALANS set saldo=saldo+$summ where `paybox_id`='$paybox_id' and `cash_id`='$cash_id' and `user_id`='$user_id' limit 1;");
	}
	// insert paybox journal record
	$r=$db->query("select * from B_PAYBOX_JOURNAL where paybox_id='$paybox_id' and cash_id='$cash_id' and user_id='$user_id' order by id desc limit 0,1;");$n=$db->num_rows($r); $saldo_before=0; 
	if ($n==1){
		$saldo_before=$db->result($r,0,"saldo_after");
	}
	$sald_after=round($saldo_before+$summ,2);
	$db->query("insert into B_PAYBOX_JOURNAL (`paybox_id`,`user_id`,`saldo_before`,`amount`,`saldo_after`,`cash_id`,`jpay_id`) values ('$paybox_id','$user_id','$saldo_before','$summ','$sald_after','$cash_id','$money_spend_id');");
	return;
}

function loadMoneySpendCDN($spend_id){$db=DbSingleton::getDb();$slave=new slave;
	$form_htm=RD."/tpl/money_spend_cdn_block.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$r=$db->query("select cc.*,u.name as user_name from MONEY_SPEND_CDN cc 
		left outer join media_users u on u.id=cc.USER_ID 
		where cc.spend_id='$spend_id' and cc.status='1' order by cc.file_name asc;");$n=$db->num_rows($r);$list="";
		for ($i=1;$i<=$n;$i++){
			$file_id=$db->result($r,$i-1,"id");
			$user_id=$db->result($r,$i-1,"user_id");
			$file_key=$db->result($r,$i-1,"file_key");
			$file_name=$db->result($r,$i-1,"file_name");
			$name=$db->result($r,$i-1,"name");
			$data=$db->result($r,$i-1,"data");
			$comment=$db->result($r,$i-1,"comment");
			$user_name=$db->result($r,$i-1,"user_name");
			
			
			$link="http://portal.myparts.pro/cdn/money_spend_files/$spend_id/$file_name";
			
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
			$block=str_replace("{spend_id}",$spend_id,$block);
			$block=str_replace("{link}",$link,$block);
			$block=str_replace("{file_view}",$file_view,$block);
			
			$list.=$block;
			
		}
		if ($n==0){$list="<h3 class='text-center'>Файли відсутні</h3>";}
		return $list;
}

function moneySpendCDNDropFile($spend_id,$file_id){$db=DbSingleton::getDb();$slave=new slave;session_start();$user_id=$_SESSION["media_user_id"];$user_name=$_SESSION["user_name"];$answer=0;$err="Помилка видалення файлу!";
	
	$spend_id=$slave->qq($spend_id);$file_id=$slave->qq($file_id);
	if ($spend_id>0 && $file_id>0){
		$r=$db->query("select FILE_NAME from MONEY_SPEND_CDN where spend_id='$spend_id' and id='$file_id' limit 0,1;");$n=$db->num_rows($r);
		if ($n==1){
			$file_name=$db->result($r,0,"file_name");
			unlink(RD.'/cdn/money_spend_files/$spend_id/$file_name');
			$r=$db->query("delete from MONEY_SPEND_CDN where spend_id='$spend_id' and id='$file_id';");
			$answer=1;$err="";
		}
	}
	return array($answer,$err);
}
	
	
}
?>