<?php

class panel_manager {
	
	function showPanelManager() { $seo_reports=new seo_reports; $user_id=$_SESSION["media_user_id"]; $user_name=$this->getMediaUserName($user_id);
		$form_htm=RD."/tpl/panel_manager.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
		$date_start=$date_end=date("Y-m-d"); $cash_id=$client_status=1;
		$list=$seo_reports->showSeoReports($date_start,$date_end,$user_id,$cash_id,$client_status);
		$form=str_replace("{user_id}",$user_id,$form);
		$form=str_replace("{user_name}",$user_name,$form);
		$form=str_replace("{panel_manager_range}",$list,$form);
		$form=str_replace("{date}", date("Y-m-d"), $form);
		$form=str_replace("{cash_select}", $seo_reports->getCashList(), $form);
		$form=str_replace("{managers_list}", $seo_reports->getManagersList(), $form);
		$form=str_replace("{summ_user}", $seo_reports->getSummUser($user_id,$date_start,$date_end,$cash_id), $form);
		return $form;
	}
	
	function getMediaUserName($user_id){$db=DbSingleton::getDb();$name="";
		$r=$db->query("select name from media_users where id='$user_id' limit 1;");$n=$db->num_rows($r);
		if ($n==1){$name=$db->result($r,0,"name");}
		return $name;
	}
	
}