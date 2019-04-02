<?php
session_start();
require_once (RD."/lib/media_users_class.php");$media_users=new media_users;
$media_user_id=$media_users->get_media_user();

if ($media_user_id==""){
	session_unset();session_destroy();$config=new config;
	$link=$_REQUEST["link"];if (substr($link,-1)=="/"){$link=substr($link,0,strlen($link)-1);} $links=explode("/", $link);
	list($title,$title_short,$keywords,$descr,$address)=$config->get_meta_head();
	if ($links[0]!=""){
		header("Location: $address/"); /* Redirect browser */exit();
	}
	$content=file_get_contents(RD."/tpl/login_form.htm");
	$content=str_replace("{title}", $title, $content);
	$content=str_replace("{title_short}", $title_short, $content);
	$content=str_replace("{keywords}", $keywords, $content);
	$content=str_replace("{site_address}", $address, $content);
}
