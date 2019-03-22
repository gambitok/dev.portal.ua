<?php
//error_reporting(0);@ini_set('display_errors', false);
if ($_SERVER['REMOTE_ADDR']=="78.152.169.139" || $_SERVER['REMOTE_ADDR']=="93.77.29.10"){error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_WARNING);@ini_set('display_errors', true);}
header('Content-Type: text/html; charset=windows-1251');
define('RD', dirname (__FILE__));
date_default_timezone_set("Europe/Kiev");
$content=null;

require_once (RD."/lib/DbSingleton.php");
require_once (RD."/lib/slave_class.php");
require_once (RD."/lib/manual_class.php");
require_once (RD."/lib/gmanual_class.php");
require_once (RD."/lib/config_class.php");
require_once (RD."/event/get_access.php");
require_once (RD."/lib/module_class.php");
require_once (RD."/lib/access_class.php");
require_once (RD."/lib/alerts_class.php");
require_once (RD."/lib/print_class.php");

if ($content==null){require_once (RD."/out.php");}
echo $content;
//}else {echo "system update...";}
?>