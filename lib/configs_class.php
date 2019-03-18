<?php
class configs {
	function show_configs(){
		$db=DbSingleton::getDb(); $slave=new slave;$mdl=new module;$url=$mdl->get_file_url(); $dep_up=$slave->get_dep_up();

		$form_htm=RD."/tpl/configs_show.htm";
		if (!file_exists("$form_htm")){ $form="�� �������� ���� �������"; }
		if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
		
		$r=$db->query("select * from config order by id desc limit 0,1;");
		$n=$db->num_rows($r);
		if ($n==1){
			$title=$db->result($r,0,"title");
			$key_words=$db->result($r,0,"key_words");
			$description=$db->result($r,0,"description");
		}
		$form=str_replace("{url}","?$url&wn=edit",$form);
		$form=str_replace("{title}",$title,$form);
		$form=str_replace("{key_words}",$key_words,$form);
		$form=str_replace("{description}",$description,$form);
		return $form;
	}
	
	function edit_configs_form(){
		$db=DbSingleton::getDb(); $slave=new slave;$mdl=new module;
		$form_htm=RD."/tpl/configs_form.htm";
		if (!file_exists("$form_htm")){ $form="�� �������� ���� �������"; }
		if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
		
		$r=$db->query("select * from config order by id desc limit 0,1;");
		$n=$db->num_rows($r);
		if ($n>0){
			$title=$db->result($r,0,"title");
			$key_words=$db->result($r,0,"key_words");
			$description=$db->result($r,0,"description");
		}
		
		$form=str_replace("{module}", $slave->get_module(), $form);
		$form=str_replace("{module_page}", $slave->get_module_page(), $form);
		$form=str_replace("{file}", $slave->get_file(), $form);
		$form=str_replace("{w}", $slave->get_w(), $form);
		$form=str_replace("{dep_up}", $slave->get_dep_up(), $form);
		$form=str_replace("{dep_cur}", $slave->get_dep_cur(), $form);
		$form=str_replace("{wn}", $slave->get_wn(), $form);
		
		$form=str_replace("{title}",$title,$form);
		$form=str_replace("{key_words}",$key_words,$form);
		$form=str_replace("{description}",$description,$form);
		
		return $form;
	}
	
	function save_configs_form(){
		$db=DbSingleton::getDb(); $slave=new slave;
		$title=$slave->qq($_POST["title"]);
		$key_words=$slave->qq($_POST["key_words"]);
		$description=$slave->qq($_POST["description"]);
		
		$db->query("update config set title='$title', key_words='$key_words', description='$description';");
		
		$form_htm=RD."/tpl/save_message.htm";
		if (!file_exists("$form_htm")){ $form="�� �������� ���� �������"; }
		if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
		
		$message="������������ ����� &quot;$title&quot; ������ ���������";
		
		$form=str_replace("{message}",$message,$form);
		$form=str_replace("{navigation}","",$form);
		$form=str_replace("{dep_menu}","",$form);
		return $form;
	}
}
?>
