<?php
$access=new access; $mf="catalogue";
list($accss,$acc_lvl)=$access->check_user_access($mf);$alg_u=0;

if ($accss=="1"){
    require_once (RD."/lib/catalogue_class.php");
	$cat=new catalogue;
	$link=gnLink;if (substr($link,-1)=="/"){$link=substr($link,0,strlen($link)-1);} $links=explode("/", $link);$w=$links[0];
	
	if ($w=="Catalogue"){
		 $content=str_replace("{work_window}", $cat->show_catalogue_range($links[1]), $content);
	}

	if ($alg_u==0){ //не надано права на операціїї з розділом
		$content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
	}
}

if ($accss=="0"){
	$content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
}
