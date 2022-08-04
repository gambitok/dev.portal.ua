<?php

error_reporting(E_ERROR);
@ini_set('display_errors', true);
@ini_set('html_errors', false);
define('RD', dirname (__FILE__));
date_default_timezone_set("Europe/Kiev");
require_once (RD."/../lib/mysql_class.php");
require_once (RD."/../lib/DbSingleton.php");
require_once (RD."/../lib/slave_class.php");
require_once (RD."/../lib/sale_invoice_class.php");
require_once (RD."/../lib/catalogue_class.php");
require_once (RD."/../lib/class.phpmailer.php");
require_once (RD."/../lib/back_clients_class.php");

$dbt = DbSingleton::getTokoDb();

$date_sel = date('d-m-Y', strtotime('-30 days', strtotime(date('d-m-Y')))) . PHP_EOL;

$dbt->query("DELETE FROM `T2_SUPPL_IMPORT_ARCHIVE` WHERE `data_update`<'$date_sel';");