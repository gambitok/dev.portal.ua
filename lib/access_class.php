<?php
class access {
	
	function check_user_access($file){$db=DbSingleton::getDb(); 
		$slave=new slave; session_start(); $media_user_id=$_SESSION["media_user_id"]; $media_role_id=$_SESSION["media_role_id"]; 

			if ($media_user_id!=1){	
				$access="0"; $acc_lvl="0"; $time=date("H:i:s");		   
				$r=$db->query("select rs.lvl from media_users_role_structure rs inner join module_files mf on mf.id=rs.file_id where rs.user_id='$media_user_id' and mf.file='$file' limit 0,1;"); $n=$db->num_rows($r);
				if ($n==1){
					$access="1"; $acc_lvl=$db->result($r,0,"lvl");
				}
			}	
	
		if ($media_user_id==1){$access="1";$acc_lvl=9;}
		return array($access,$acc_lvl);
	}
	
	function show_access_deny($file){$db=DbSingleton::getDb(); $slave=new slave;$slave=new slave;$mdl=new module;$url=$mdl->get_file_url($file);
		$form_htm=RD."/tpl/access_deny.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
		$message="������ ����������. <br><br><small>�� ���������� ����������� ��������� �� �����������</small>";
		$form=str_replace("{ModuleCaption}","������ ��������",$form);
		$form=str_replace("{OperationCaption}","",$form);
		$form=str_replace("{message}",$message,$form);
		$form=str_replace("{back_caption}","�����",$form);
		$form=str_replace("{back_url}","/",$form);
		return $form;
	}
	
	function checkTrustedIp($media_user_id) {$db=DbSingleton::getDb();
	  	$ip_address=$_SERVER['REMOTE_ADDR'];
		$r=$db->query("select * from trusted_ip where ip='$ip_address' and status=1 limit 1;"); $n=$db->num_rows($r);
		$n>0 ? $result=true : $result=false;
		$media_user_id!=1 ? : $result=true;
		return $result;
	}
	
	function checkAccessTime($media_user_id) {$db=DbSingleton::getDb(); 
		$result=false; $cur_time=date("h:i:s");					
		$r=$db->query("select * from media_users_time where id='$media_user_id' limit 1;"); $n=$db->num_rows($r);
		if ($n>0) {
			$access=$db->result($r,0,"access");
			$access_time=$db->result($r,0,"access_time");
			$time_from=$db->result($r,0,"time_from");
			$time_to=$db->result($r,0,"time_to");

			if($access==1) {
				$result=$this->checkTrustedIp($media_user_id);
			} 

			if($access_time==1) {
				if ($cur_time>=$time_from && $cur_time<=$time_to) { 
					$result=true;
				} 
				
				if ($cur_time<=$time_from && $cur_time>=$time_to) {
					$result=false;
				}
			}
			
			if($access==0) {
				$result=true;
			} 
		}
		$media_user_id!=1 ? : $result=true;
											  
		return $result;
	}
	
	function getMediaUserRole() {$db=DbSingleton::getDb(); 
		$media_user_id=$_SESSION["media_user_id"];
		$r=$db->query("select role_id from media_users where id='$media_user_id' limit 1;");
		$role_id=$db->result($r,0,"role_id");
		return $role_id;
	}
	
}
?>