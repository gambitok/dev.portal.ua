<?php
$access=new access; $mf="income";
list($accss,$acc_lvl)=$access->check_user_access($mf);$alg_u=0;

if ($accss=="1"){
    require_once (RD."/lib/income_class.php");
	$income=new income;
	$form_htm=RD."/tpl/income.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$content=str_replace("{work_window}", $form, $content);
	$link=gnLink; if (substr($link,-1)=="/"){$link=substr($link,0,strlen($link)-1);} $links=explode("/", $link);$w=$links[1];

	if ($w==""){
	    // $income->recalculateIncome();
        // $income->recalculateIncomeOperPrice();
		 $range_list=$income->show_income_list();
		 $content=str_replace("{income_range}", $range_list, $content);
		 $content=str_replace("{date_today}", date("Y-m-d"), $content);
		 // $content=str_replace("{doc_prefix}", $income->get_doc_prefix(), $content);
		 $content=str_replace("{doc_prefix}", "", $content);
	}

	if ($w=="printIn"){ $income_id=$links[2];
		$form=$income->printIncome($income_id);
	}

	if ($w=="printInL"){ $income_id=$links[2];
		$form=$income->printIncome($income_id);
	}

	if ($alg_u==0){ //не надано права на операціїї з розділом
		$content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
	}
}

if ($accss=="0"){
	$content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
}
