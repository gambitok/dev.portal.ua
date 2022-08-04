<?php

$access = new access;
$mf = "group_tree";
list($accss, $acc_lvl) = $access->check_user_access($mf);
$alg_u = 0;

if ($accss == "1") {
	require_once (RD . "/lib/group_tree_class.php");
	$group_tree = new group_tree;
	$content = str_replace("{work_window}", $group_tree->showGroupTree(), $content);
}

if ($accss == "0") {
	$content = str_replace("{work_window}", $access->show_access_deny($mf), $content);
}
