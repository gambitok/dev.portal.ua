<?php
error_reporting(0);@ini_set('display_errors', false);
header('Content-Type: text/html; charset=windows-1251');
if ($_SERVER['REMOTE_ADDR']=="78.152.169.139" || $_SERVER['REMOTE_ADDR']=="93.77.29.10"){error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_WARNING);@ini_set('display_errors', true);}
define('RD', dirname (__FILE__));
$content=null;

require_once (RD."/lib/DbSingleton.php");
require_once (RD."/lib/slave_class.php");
require_once (RD."/lib/config_class.php");
require_once (RD."/lib/module_class.php");
require_once (RD."/lib/access_class.php");
if ($content==null){require_once (RD."/engine.php");}
?>