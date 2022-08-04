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

$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$sale_invoice=new sale_invoice;$back_client=new back_clients;

$r=$db->query("SELECT * FROM `cron_client_invoice` WHERE `status`=0 AND `doc_type`=1;");$n=$db->num_rows($r);
for ($i=1;$i<=$n;$i++) {
    $id = $db->result($r, $i - 1, "id");
    $invoice_id = $db->result($r, $i - 1, "doc_id");
    $user_id = $db->result($r, $i - 1, "user_id");
    list($answer,$err) = $sale_invoice->sendSaleInvoceMail($invoice_id,$user_id);
    $db->query("UPDATE `cron_client_invoice` SET `status`=1 WHERE `id`='$id';");
}

$r=$db->query("SELECT * FROM `cron_client_invoice` WHERE `status`=0 AND `doc_type`=2;");$n=$db->num_rows($r);
for ($i=1;$i<=$n;$i++) {
    $id = $db->result($r, $i - 1, "id");
    $invoice_id = $db->result($r, $i - 1, "doc_id");
    $user_id = $db->result($r, $i - 1, "user_id");
    list($answer,$err) = $back_client->sendBackClientsMail($invoice_id,$user_id);
    $db->query("UPDATE `cron_client_invoice` SET `status`=1 WHERE `id`='$id';");
}
