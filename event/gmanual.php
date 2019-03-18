<?php
$gmanual=new gmanual;$access=new access;session_start();$slave=new slave;
list($accss,$acc_lvl)=$access->check_user_access("Gmanual");$alg_u=0;
if ($accss=="1"){$link=gnLink;
	if (substr($link,-1)=="/"){$link=substr($link,0,strlen($link)-1);} $links=explode("/", $link);$gkey=$links[2];$w=$links[1];	$conf=$_POST["conf"];
	
	
	if ($w=="new" and $acc_lvl>=2){$alg_u=1;
		if ($conf==""){ $content=str_replace("{work_window}", $gmanual->new_gmanual_form(), $content); }
		if ($conf=="true"){ $content=str_replace("{work_window}", $gmanual->add_gmanual_form(), $content); }
	}
	if ($w=="edit" and $acc_lvl>=2){$alg_u=1;$gmanual_id=$links[2];
		if ($conf==""){ $content=str_replace("{work_window}", $gmanual->edit_gmanual_form($gmanual_id), $content); }
		if ($conf=="true"){ $content=str_replace("{work_window}", $gmanual->save_gmanual_form(), $content); }
	}
	
	
	if ($w=="" and $gkey=="" and $acc_lvl>=2){$alg_u=1; 
		$content=str_replace("{work_window}", $gmanual->show_key_list(), $content); 
	}
	if ($w=="view" and $gkey!="" and $acc_lvl>=2){$alg_u=1; 
		$content=str_replace("{work_window}", $gmanual->show_gmanual_list($gkey), $content); 
	}
	
	
	if ($alg_u==0){ //не надано права на операціїї з розділом
		$content=str_replace("{work_window}", $access->show_access_deny("Gmanual"), $content);
	}
	
}
if ($accss=="0"){
	$content=str_replace("{work_window}", $access->show_access_deny("Gmanual"), $content);
}
$content=str_replace("{head}", $gmanual->getFileCaption($gkey), $content);
?>
