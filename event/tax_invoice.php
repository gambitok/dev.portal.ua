<?php
$access=new access; $mf="tax_invoice";
list($accss,$acc_lvl)=$access->check_user_access($mf);$alg_u=0;

if ($accss=="1"){
	$tax_invoice=new tax_invoice;
	$form_htm=RD."/tpl/tax_invoice.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$content=str_replace("{work_window}", $form, $content);
	$link=gnLink; if (substr($link,-1)=="/"){$link=substr($link,0,strlen($link)-1);} $links=explode("/", $link);$w=$links[1];

	if ($w==""){
		$range_list=$tax_invoice->show_tax_invoice_list();
		$content=str_replace("{tax_invoice_range}", $range_list, $content);
	}

	if ($w=="printSlIvBuh"){ 
		$tax_id=$links[2];
		$form=$tax_invoice->printTaxInvoice($tax_id);
	}

	if ($w=="exportTIvXML"){ 
		$tax_id=$links[2];
		$form=$tax_invoice->exportTaxInvoiceXML($tax_id);
	}

	if ($w=="exportTBIvXML"){ 
		$tax_id=$links[2];
		$form=$tax_invoice->exportTaxBackInvoiceXML($tax_id);
	}

	if ($alg_u==0){ //не надано права на операціїї з розділом
		$content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
	}
}

if ($accss=="0"){
	$content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
}
