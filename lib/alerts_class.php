<?php
class alerts {
	function get_max_alerts_id(){$db=new db;  $r=$db->query("select max(id) as mid from media_alerts;");return $db->result($r,0,"mid")+1; }

	function list_alerts_navigation(){$db=new db;$slave=new slave;$mdl=new module;$url=$mdl->get_file_url("alerts");session_start(); $media_org_id=$_SESSION["media_org_id"];$media_user_id=$_SESSION["media_user_id"];
		$form_htm=RD."/tpl/alerts_navigation_list.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}	$kp=20;$list="";
		$r=$db->query("SELECT count(`id`) as kol FROM media_alerts where status='1' and user_id='$media_user_id' and media_org_id='$media_org_id';"); $kol=$db->result($r,0,"kol");
		$r=$db->query("SELECT * FROM media_alerts where status='1' and user_id='$media_user_id' and media_org_id='$media_org_id' order by id desc limit 0,$kp;"); $n=$db->num_rows($r);
		for ($i=1;$i<=$n;$i++){$k+=1;
			$id=$db->result($r,$i-1,"id");
			$caption=$db->result($r,$i-1,"caption");
			$url=$db->result($r,$i-1,"url");if ($url==""){$url="#";}
			$label=$db->result($r,$i-1,"label");$label=$this->get_alert_label($label);
			$data_add=$db->result($r,$i-1,"data_add");
			$user_id=$db->result($r,$i-1,"user_id");
			
			$list.=$form;
			$list=str_replace("{user_id}",$user_id,$list);
			$list=str_replace("{user_name}",$this->getUserName($user_id),$list);
			$list=str_replace("{url}",$url,$list);
			$list=str_replace("{caption}",$caption,$list);
			$list=str_replace("{label}",$label,$list);
			$list=str_replace("{data_add}",$data_add,$list);
		}
		return array($list,$kol);
	}
	function get_alert_label($label_id){$db=new db;$label="default";
		$r=$db->query("SELECT label FROM alerts_label where id='$label_id' limit 0,1;"); $n=$db->num_rows($r);
		if ($n==1){$label=$db->result($r,0,"label");}
		return $label;
	}
	function getUserName($user_id){$db=new db;$name="";
		$r=$db->query("SELECT name FROM media_users where id='$user_id' limit 0,1;"); $n=$db->num_rows($r);
		if ($n==1){$name=$db->result($r,0,"name");}
		return $name;
	}
}
?>