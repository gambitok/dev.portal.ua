<?php

$access=new access; $mf="users";
list($accss,$acc_lvl)=$access->check_user_access($mf);$alg_u=0;
if ($accss=="1"){
	include_once 'lib/import_artprice_class.php';$import_artprice=new import_artprice;
	$form_htm=RD."/tpl/import_artprice.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$content=str_replace("{work_window}", $form, $content);
	$link=gnLink; if (substr($link,-1)=="/"){$link=substr($link,0,strlen($link)-1);} $links=explode("/", $link);$w=$links[1]; 
	if ($w==""){
		$content=str_replace("{import_artprice_form}", $import_artprice->show_import_artprice_form(), $content);
	}
	if ($alg_u==0){ //�� ������ ����� �� ��������� � �������
		$content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
	}
	
}
if ($accss=="0"){
	$content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
}
?>