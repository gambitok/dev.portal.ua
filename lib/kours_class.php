<?php
class kours{
function newKoursCard(){$db=DbSingleton::getDb();$slave=new slave;$manual=new manual; session_start();$user_id=$_SESSION["media_user_id"];$user_name=$_SESSION["user_name"]; $kours_id=0;
	$r=$db->query("select max(id) as mid from J_KOURS;");$kours_id=0+$db->result($r,0,"mid")+1;
	$db->query("insert into J_KOURS (`id`,`user_id`,`in_use`) values ('$kours_id','$user_id','2');");
	return $kours_id;
}
function show_kours_list(){$db=DbSingleton::getDb();$slave=new slave;$where="";
	$r=$db->query("select k.*, c.name, c.symbol from J_KOURS k
		left outer join CASH c on c.id=k.cash_id where k.in_use in (0,1)
		order by k.in_use desc,k.id asc;");$n=$db->num_rows($r);$list="";
		for ($i=1;$i<=$n;$i++){
			$id=$db->result($r,$i-1,"id");
			$name=$db->result($r,$i-1,"name");
			$symbol=$db->result($r,$i-1,"symbol");
			$kours_value=$db->result($r,$i-1,"kours_value");
			$data_from=$db->result($r,$i-1,"data_from");
			$data_to=$db->result($r,$i-1,"data_to");if ($data_to=="0000-00-00 00:00:00"){$data_to="поточний курс";}
			$in_use=$db->result($r,$i-1,"in_use");$inuse="";if ($in_use==1){$inuse="діючий";}
			$list.="<tr>
				<td>$inuse</td>
				<td>$name ($symbol)</td>
				<td>$kours_value</td>
				<td>$data_from</td>
				<td>$data_to</td>
			</tr>";
		}
		return $list;
}

function showKoursCard($kours_id){$db=DbSingleton::getDb();$slave=new slave;session_start();$user_id=$_SESSION["media_user_id"];$user_name=$_SESSION["user_name"];
	$form_htm=RD."/tpl/kours_card.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	
	$r=$db->query("select * from J_KOURS where id='$kours_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==0){$form_htm=RD."/tpl/access_deny.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);} }
	if ($n==1){
		$kours_id=$db->result($r,0,"id");

		$form=str_replace("{kours_id}",$kours_id,$form);
		$form=str_replace("{cash_list}",$this->showCashSelectList(1),$form);
	}
	return $form;
}
function saveKoursForm($kours_id,$kours_value,$cash_id){$db=DbSingleton::getDb();$slave=new slave;session_start();$user_id=$_SESSION["media_user_id"];$user_name=$_SESSION["user_name"];$answer=0;$err="Помилка збереження даних!";
	$kours_id=$slave->qq($kours_id);$kours_value=$slave->qq($kours_value);$cash_id=$slave->qq($cash_id);
	if ($kours_id>0){
		$data_from=date("Y-m-d H:i:s");$data_to=$data_from;
		
		if (strpos($kours_value, ',') !== false)
			$kours_value = floatval(str_replace(',', '.', str_replace('.', '', $kours_value)));
		
		$db->query("update J_KOURS set `in_use`='0', `data_to`='$data_to' where `cash_id`='$cash_id' and `in_use`='1';");
		$db->query("update J_KOURS set `kours_value`='$kours_value', `cash_id`='$cash_id', data_from='$data_from', `in_use`='1' where `id`='$kours_id';");
		$answer=1;$err="";
	}
	return array($answer,$err);
}
function showCashSelectList($sel_id){$db=DbSingleton::getDb();$list="";;
	$r=$db->query("select * from CASH order by name,id asc;");$n=$db->num_rows($r);
	for ($i=1;$i<=$n;$i++){
		$id=$db->result($r,$i-1,"id");
		$name=$db->result($r,$i-1,"name")." (".$db->result($r,$i-1,"symbol").")";
		$sel="";if ($id==$sel_id){$sel=" selected";}
		$list.="<option value='$id' $sel>$name</option>";
	}
	return $list;
}
}
?>