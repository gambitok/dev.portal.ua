<?php
$access=new access; $mf="non_priced";
list($accss,$acc_lvl)=$access->check_user_access($mf);$alg_u=0;

if ($accss=="1"){
	require_once (RD."/lib/non_priced_class.php"); $non_priced=new non_priced;
	$form_htm=RD."/tpl/non_priced.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$form=str_replace("{non_price_range}",$non_priced->showNonPricedGoods(),$form);
	$content=str_replace("{work_window}", $form, $content);
}

if ($accss=="0"){
	$content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
}
