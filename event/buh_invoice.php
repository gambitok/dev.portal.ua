<?php
$access=new access; $mf="buh_invoice";
list($accss,$acc_lvl)=$access->check_user_access($mf);$alg_u=0;

if ($accss=="1"){
	$buh_invoice=new buh_invoice;
	$form_htm=RD."/tpl/buh_invoice.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$form=str_replace("{sale_invoice_range}",$buh_invoice->show_sale_invoice_list(),$form);
	$content=str_replace("{work_window}", $form, $content);
	$content=str_replace("{date_today}", date("Y-m-d"), $content);
}

if ($accss=="0"){
	$content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
}
