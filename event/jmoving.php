<?php
$access=new access; $mf="catalogue";
list($accss,$acc_lvl)=$access->check_user_access($mf);$alg_u=0;

if ($accss=="1"){
    $jmoving=new jmoving;
	$form_htm=RD."/tpl/jmoving.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$content=str_replace("{work_window}", $form, $content);
	$link=gnLink; if (substr($link,-1)=="/"){$link=substr($link,0,strlen($link)-1);} $links=explode("/", $link);$w=$links[1];

	if ($w==""){
		$range_list=$jmoving->show_jmoving_list();
		$status_main_list=$jmoving->showSelectStatusList();
		$content=str_replace("{jmoving_range}", $range_list, $content);
		$content=str_replace("{status_main_list}", $status_main_list, $content);
	}

	if ($w=="printJmS1"){ 
		$jmoving_id=$links[2];$select_id=$links[3];
		$form=$jmoving->printJmovingStorageSelect($jmoving_id,$select_id);
	}

	if ($w=="printJmS1L"){ 
		$jmoving_id=$links[2];$select_id=$links[3];
		$form=$jmoving->printJmovingStorageSelectLocal($jmoving_id,$select_id);
	}
	
	if ($w=="printJmSTP"){ 
		$jmoving_id=$links[2];$select_id=$links[3];
	    // $form=$jmoving->printJmovingStorageSelectTruckList($jmoving_id,$select_id);
		$form=$jmoving->printJmovingTruckList($jmoving_id);
	}

	if ($w=="printPdf"){
        require_once RD.'/vendor/autoload.php';
        //$mpdf = new \Mpdf\Mpdf();
        $mpdf = new \Mpdf\Mpdf(['tempDir' => RD.'/']);
        $mpdf->WriteHTML('<h1>Hello world!</h1>');
        $mpdf->Output();
    }

	if ($alg_u==0){ //не надано права на операціїї з розділом
		$content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
	}
}

if ($accss=="0"){
	$content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
}
