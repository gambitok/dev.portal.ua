<?php
$access=new access; $mf="storsel";
list($accss,$acc_lvl)=$access->check_user_access($mf);$alg_u=0;

if ($accss=="1"){
	$storsel=new storsel;
	$form_htm=RD."/tpl/storsel.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$content=str_replace("{work_window}", $form, $content);
	$link=gnLink; if (substr($link,-1)=="/"){$link=substr($link,0,strlen($link)-1);} $links=explode("/", $link);$w=$links[1]; 

	if ($w==""){
		list($range_list,$range_size)=$storsel->show_storsel_list();
		$content=str_replace("{storsel_range}", $range_list, $content);
	}

	if ($w=="printStS1"){ $select_id=$links[2];
		$form=$storsel->printStorselView($select_id);
	}

	if ($w=="printStS2"){ $select_id=$links[2];
		$form=$storsel->printStorselView2($select_id);
	}

	if ($w=="printJmSTP"){
		$storsel_id=$links[2];$select_id=$links[3];
		$form=$storsel->printStorselTruckList($storsel_id);
	}

	if ($alg_u==0){ //не надано права на операціїї з розділом
		$content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
	}
}

if ($accss=="0"){
	$content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
}
