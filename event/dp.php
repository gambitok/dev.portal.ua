<?php
$access=new access; $mf="catalogue";
list($accss,$acc_lvl)=$access->check_user_access($mf);$alg_u=0;

if ($accss=="1"){
    $dp=new dp;
	$form_htm=RD."/tpl/dp.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$content=str_replace("{work_window}", $form, $content);
	$link=gnLink; if (substr($link,-1)=="/"){$link=substr($link,0,strlen($link)-1);} $links=explode("/", $link);$w=$links[1]; 
	
	if ($w==""){
		session_start();$ses_user=$_SESSION["media_user_id"];
		$uncontrolUserDiscount=0; if ($ses_user==1 || $ses_user==2){$uncontrolUserDiscount=1;}
		$content=str_replace("{uncontrolUserDiscount}", $uncontrolUserDiscount, $content);
		$range_list=$dp->show_dp_list();
		$content=str_replace("{dp_range}", $range_list, $content);
		$content=str_replace("{kilk_orders}", $dp->countOrdersSite()[0], $content);
		$content=str_replace("{kilk_orders_back}", $dp->countOrdersSite()[1], $content);
		$content=str_replace("{kilk_users}", $dp->countUsersSite()[0], $content);
		$content=str_replace("{kilk_users_back}", $dp->countUsersSite()[1], $content);
		$content=str_replace("{status_main_list}", $dp->getDpListFilter("status"), $content);
		$content=str_replace("{tpoint_main_list}", $dp->getDpListFilter("tpoint"), $content);
		$content=str_replace("{author_main_list}", $dp->getDpListFilter("user"), $content);
	}
	
	if ($w=="printSlIv"){ 
		$invoice_id=$links[2];
		$form=$dp->printSaleInvoice($invoice_id);
	}
	
	if ($w=="printDpSlIv"){ 
		$invoice_id=$links[2];
		$form=$dp->printDpSaleInvoice($invoice_id);
	}
	
	if ($w=="printDpJournal"){ 
		$invoice_id=$links[2];
		$form=$dp->printDpJournal($invoice_id);
	}
	
	if ($w=="printJmS1"){ 
		$dp_id=$links[2];$select_id=$links[3];
		$form=$dp->printdpStorageSelect($dp_id,$select_id);
	}
	
	if ($w=="printJmS1L"){ 
		$dp_id=$links[2];$select_id=$links[3];
		$form=$dp->printdpStorageSelectLocal($dp_id,$select_id);
	}
	
	if ($w=="printJmSTP"){ 
		$dp_id=$links[2];$select_id=$links[3];
	    //	$form=$dp->printdpStorageSelectTruckList($dp_id,$select_id);
		$form=$dp->printdpTruckList($dp_id);
	}
	
	if ($alg_u==0){ //не надано права на операціїї з розділом
		$content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
	}
}

if ($accss=="0"){
	$content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
}
