<?php
$access=new access; $mf="storage";
list($accss,$acc_lvl)=$access->check_user_access($mf);$alg_u=0;
if ($accss=="1"){
	
	$form_htm=RD."/tpl/storage.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$content=str_replace("{work_window}", $form, $content);
	include_once 'lib/storage_class.php';$storage=new storage;
	if ($w==""){
		 $range_list=$storage->show_storage_list();
		 $content=str_replace("{storage_range}", $range_list, $content);
	}
	if ($alg_u==0){ //�� ������ ����� �� ��������� � �������
		$content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
	}
	
}
if ($accss=="0"){
	$content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
}
?>