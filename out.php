<?php

$theme_htm = RD . "/theme/theme.htm";
if (file_exists($theme_htm)) {
    $content = file_get_contents($theme_htm);
}
$content = str_replace("{MediaUserInfo}", $media_users->showMediaUserInfo($media_user_id), $content);

if ((int)$media_user_id === 31) {
    $form = "";
    $form_htm = RD . "/tpl/seo_client/menu.htm";
    if (file_exists($form_htm)) {
        $form = file_get_contents($form_htm);
    }

    $content = str_replace(array("{ModuleMenu}", "{nav_top_menu}", "{user_visible}"), array($form, "", "style='display:none;'"), $content);
}

$mdl = new module;
$module = $_REQUEST["module"];
$file = $_REQUEST["file"];
$alerts = new alerts;
[$alerts_list, $alerts_kol] = $alerts->list_alerts_navigation();

$content = str_replace(array("{list_alerts_navigation}", "{alerts_navigation_kol}"), array($alerts_list, $alerts_kol), $content);

require_once (RD."/lib/catalogue_class.php");
require_once (RD."/lib/clients_class.php");
require_once (RD."/lib/income_class.php");
require_once (RD."/lib/jmoving_class.php");
require_once (RD."/lib/dp_class.php");
require_once (RD."/lib/sale_invoice_class.php");
require_once (RD."/lib/storsel_class.php");
require_once (RD."/lib/storage_class.php");
require_once (RD."/lib/money_format.php");
require_once (RD."/lib/users_class.php");

$config = new config;
[$title, $title_short, $keywords, $descr, $site_address] = $config->get_meta_head();
define('gnLink', $config->get_link());
[$file_id, $file, $module_id, $page_id, $module_caption] = $config->findFileByLink(gnLink);
$content = str_replace("{ModuleMenu}", $mdl->show_menu($module_id, $page_id), $content);

if (file_exists("event/" . $file . ".php")) {
    include "event/" . $file . ".php";
} else {
    include "event/main_page.php";
}

$rl_id = (int)$_SESSION["media_role_id"];
$menu_hidden = "";
if ($rl_id === 5 || $rl_id === 6 || $rl_id === 1) {
	$mh_htm = RD . "/tpl/menu_hidden.htm";
	if (file_exists($mh_htm)) {
	    $menu_hidden = file_get_contents($mh_htm);
	}
}

$dp = new dp;
$access = new access;
$user = new users;

if ($access->check_user_access("report_overdraft")[0] === "0") {
    $content = str_replace("{style_overdraft}", "style='display:none;'", $content);
}

if ($access->check_user_access("clients")[0] === "0") {
    $content = str_replace("{style_retail}", "style='display:none;'", $content);
}

if ($access->check_user_access("catalogue")[0] === "0") {
    $content = str_replace("{style_dp}", "style='display:none;'", $content);
}

if ($access->check_user_access("suppliers_cooperation")[0] === "0") {
    $content = str_replace("{style_cooperation}", "style='display:none;'", $content);
}

$content = str_replace("{nav_top_menu}", $user->loadTopNavigation(), $content);

$content = str_replace("{windowState}", $_SESSION["windowState"], $content);
$content = str_replace("{title}", $module_caption.$title, $content);
$content = str_replace("{title_short}", $title_short, $content);
$content = str_replace("{keywords}", $keywords, $content);
$content = str_replace("{site_address}", $site_address, $content);

$content = str_replace("{media_user_id}", $media_user_id, $content);
$content = str_replace("{UserName}", $_SESSION["user_name"], $content);
$content = str_replace("{UserPost}", $_SESSION["user_post"], $content);
$content = str_replace("{HospitalCaption}",$_SESSION["hospital_caption"],$content);

$content = str_replace("{menu_hidden}", $menu_hidden, $content);
$content = str_replace("{work_window}", "", $content);
$content = str_replace("{sub_menu}", "", $content);
$content = str_replace("{user_info}", "", $content);
$content = str_replace("{top_menu}", "", $content);

$navBar = (int)$_COOKIE["myPartsPortalUserNavBar"];
$navBarCap = "";
if ($navBar === 1) {
    $navBarCap = "mini-navbar";
}
$content = str_replace("{mini-navbar}", $navBarCap, $content);


