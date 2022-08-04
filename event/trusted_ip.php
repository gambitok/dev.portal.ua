<?php
$access = new access;
$mf = "users";
list($accss, $acc_lvl) = $access->check_user_access($mf);
$alg_u = 0;

if ($accss == "1") {
    require_once (RD."/lib/users_class.php");
    $users = new users;
    $form = ""; $form_htm = RD . "/tpl/trusted_ip.htm";
	if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
	$content = str_replace("{work_window}", $form, $content);
    $content = str_replace("{ip_range}", $users->showTrustedIPList(), $content);

	if ($alg_u == 0) { //не надано права на операціїї з розділом
		$content = str_replace("{work_window}", $access->show_access_deny($mf), $content);
	}
}

if ($accss == "0") {
	$content = str_replace("{work_window}", $access->show_access_deny($mf), $content);
}
