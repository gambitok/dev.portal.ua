<?php
error_reporting(1);
@ini_set('display_errors', true);
define('RD', dirname (__FILE__));
date_default_timezone_set("Europe/Kiev");
require_once (RD."/lib/mysql_class.php");
require_once (RD."/lib/DbSingleton.php");
$db = DbSingleton::getTokoDb();

if (!empty($_FILES)){
	$news_id = $_REQUEST["photo_news_id"];
	$lang_id = $_REQUEST["photo_lang_id"];
	$file_id = $_REQUEST["photo_file_id"];

	$targetDir = RD."/images/news/$lang_id/$news_id/";

	if (!file_exists(RD."/images/news/$lang_id/$news_id")) {
		mkdir(RD."/images/news/$lang_id/$news_id", 0777, true);
	}

	if ($file_id == "") {
		$r = $db->query("SELECT MAX(`id`) as mid FROM `news_galery`;");
		$max_id = 0 + $db->result($r, 0, "mid") + 1;
		$db->query("INSERT INTO `news_galery` (`id`,`cat`) VALUES ('$max_id','$news_id');");
		$file_id = $max_id;
	}

	$tmpFile = $_FILES['file']['tmp_name'];
	$filename = $targetDir.'/'.$file_id.".jpg";
	move_uploaded_file($tmpFile, $filename);
	$f = $_FILES['file']['name'];

	$db->query("UPDATE `news_galery` SET `caption`='uploaded' WHERE `id`='$file_id';");
}
