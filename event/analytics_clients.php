<?php
$access=new access; $mf="analytics_clients";
list($accss,$acc_lvl)=$access->check_user_access($mf);$alg_u=0;

if ($accss=="1"){
    require_once (RD."/lib/report_clients_class.php"); $report_clients=new report_clients;
    $form_htm=RD."/tpl/analytics_clients.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
    $content=str_replace("{work_window}", $form, $content);
    $content=str_replace("{date}", date("Y-m-d"), $content);
    $content=str_replace("{cash_select}", $report_clients->getCashList(), $content);
    $content=str_replace("{tpoint_select}", $report_clients->getTpointList(), $content);
    $content=str_replace("{price_select}", getPriceList(), $content);
    $content=str_replace("{clients_list}", $report_clients->getClientsList(), $content);
    $content=str_replace("{states_list}", $report_clients->getLocationList("STATE"), $content);
	$content=str_replace("{regions_list}", $report_clients->getLocationList("REGION"), $content);
}

if ($accss=="0"){
    $content=str_replace("{work_window}", $access->show_access_deny($mf), $content);
}

function getPriceList() {
    $n=50;$list="";
    for ($i=1;$i<=$n;$i++){
        $ii=$i-1;
        $list.="<option value='$i'>Price $ii</option>";
    }
    return $list;
}
