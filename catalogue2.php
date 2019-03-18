<?php
error_reporting(0);@ini_set('display_errors', false);
if ($_SERVER['REMOTE_ADDR']=="78.152.169.139" || $_SERVER['REMOTE_ADDR']=="93.77.29.10"){error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_WARNING);@ini_set('display_errors', true);}
define('RD', dirname (__FILE__));
date_default_timezone_set("Europe/Kiev");
$content=null;
require_once (RD."/lib/mysql_class.php");
require_once (RD."/lib/slave_class.php");
require_once (RD."/lib/manual_class.php");
require_once (RD."/lib/gmanual_class.php");
require_once (RD."/lib/config_class.php");
require_once (RD."/event/get_access.php");
require_once (RD."/lib/module_class.php");
require_once (RD."/lib/access_class.php");


require_once (RD."/lib/catalogue_class.php"); 
require_once (RD."/lib/clients_class.php");
require_once (RD."/lib/income_class.php");
$cat=new catalogue;
$link=gnLink;if (substr($link,-1)=="/"){$link=substr($link,0,strlen($link)-1);} $links=explode("/", $link);$w=$links[0];

print $cat->show_catalogue_range2($links[1]);

?>