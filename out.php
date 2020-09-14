<?php
$theme_htm=RD."/theme/theme.htm";if (file_exists("$theme_htm")){ $content = file_get_contents($theme_htm);}
$content=str_replace("{MediaUserInfo}", $media_users->showMediaUserInfo($media_user_id), $content);

$mdl=new module; $module=$_REQUEST["module"];$file=$_REQUEST["file"];
$alerts=new alerts;list($alerts_list,$alerts_kol)=$alerts->list_alerts_navigation();
$content=str_replace("{list_alerts_navigation}", $alerts_list, $content);
$content=str_replace("{alerts_navigation_kol}", $alerts_kol, $content);

require_once (RD."/lib/catalogue_class.php");
require_once (RD."/lib/clients_class.php");
require_once (RD."/lib/income_class.php");
require_once (RD."/lib/jmoving_class.php");
require_once (RD."/lib/dp_class.php");
require_once (RD."/lib/sale_invoice_class.php");
require_once (RD."/lib/storsel_class.php");
require_once (RD."/lib/money_format.php");
require_once (RD."/lib/users_class.php");

$config=new config;list($title,$title_short,$keywords,$descr,$site_address)=$config->get_meta_head();
define('gnLink',$config->get_link());
list($file_id,$file,$module_id,$page_id,$module_caption)=$config->findFileByLink(gnLink);
$content=str_replace("{ModuleMenu}", $mdl->show_menu($module_id,$page_id), $content);
if (file_exists("event/".$file.".php")){ include "event/".$file.".php"; } else { include "event/main_page.php"; }

$rl_id=$_SESSION["media_role_id"];$menu_hidden="";
if ($rl_id==5 || $rl_id==6 || $rl_id==1){
	$mh_htm=RD."/tpl/menu_hidden.htm";if (file_exists("$mh_htm")){ $menu_hidden = file_get_contents($mh_htm);}
}

$dp=new dp; $access=new access; $user=new users;

if ($access->check_user_access("report_overdraft")[0]=="0")     { $content=str_replace("{style_overdraft}", "style='display:none;'", $content); }
if ($access->check_user_access("clients")[0]=="0")              { $content=str_replace("{style_retail}", "style='display:none;'", $content); }
if ($access->check_user_access("catalogue")[0]=="0")            { $content=str_replace("{style_dp}", "style='display:none;'", $content); }
if ($access->check_user_access("suppliers_cooperation")[0]=="0"){ $content=str_replace("{style_cooperation}", "style='display:none;'", $content); }

//$content=str_replace("{kilk_orders}", $dp->countOrdersSite()[0], $content);
//$content=str_replace("{kilk_orders_back}", $dp->countOrdersSite()[1], $content);
//
//$content=str_replace("{kilk_users}", $dp->countUsersSite()[0], $content);
//$content=str_replace("{kilk_users_back}", $dp->countUsersSite()[1], $content);
//
//$content=str_replace("{kilk_suppl}", $dp->countSupplCoopSite()[0], $content);
//$content=str_replace("{kilk_suppl_back}", $dp->countSupplCoopSite()[1], $content);
//
//$content=str_replace("{kilk_overdraft}", $dp->countReportOverdrafts()[0], $content);
//$content=str_replace("{kilk_overdraft_back}", $dp->countReportOverdrafts()[1], $content);
//
//$content=str_replace("{kilk_requests}", $dp->countT2Requests()[0], $content);
//$content=str_replace("{kilk_requests_back}", $dp->countT2Requests()[1], $content);

$content=str_replace("{nav_top_menu}", $user->loadTopNavigation(), $content);

$content=str_replace("{windowState}", $_SESSION["windowState"], $content);
$content=str_replace("{title}", $module_caption.$title, $content);
$content=str_replace("{title_short}", $title_short, $content);
$content=str_replace("{keywords}", $keywords, $content);
$content=str_replace("{site_address}", $site_address, $content);

$content=str_replace("{media_user_id}", $media_user_id, $content);
$content=str_replace("{UserName}", $_SESSION["user_name"], $content);
$content=str_replace("{UserPost}", $_SESSION["user_post"], $content);
$content=str_replace("{HospitalCaption}",$_SESSION["hospital_caption"],$content);

$content=str_replace("{menu_hidden}", $menu_hidden, $content);
$content=str_replace("{work_window}", "", $content);
$content=str_replace("{sub_menu}", "", $content);
$content=str_replace("{user_info}", "", $content);
$content=str_replace("{top_menu}", "", $content);

$navBar=$_COOKIE["myPartsPortalUserNavBar"];
$navBarCap="";
if ($navBar==1){$navBarCap="mini-navbar";}
$content=str_replace("{mini-navbar}", $navBarCap, $content);

?>