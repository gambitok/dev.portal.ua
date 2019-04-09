<?php
error_reporting(E_ERROR);
@ini_set('display_errors', true);
@ini_set('html_errors', false);
define('RD', dirname (__FILE__));
require_once(RD . "/lib/DbSingleton.php");$db=DbSingleton::getDb();
require_once (RD."/lib/slave_class.php");

$curdate=date("Y-m-d");$usd="0.0";$euro="0.0";$rub="0.0";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.privatbank.ua/p24api/pubinfo?exchange&coursid=3');
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
$result = curl_exec($ch);
$xml=simplexml_load_string ($result);
$ex=$xml->row;
foreach($ex as $cur){
	foreach($cur as $c){
		$cure=$c["ccy"];
		$val=$c["buy"];
		if ($cure=="USD"){$usd=$val;}
		if ($cure=="EUR"){$euro=$val;}
		if ($cure=="RUR"){$rub=$val;}
	}
}
$db->query("insert into kours values ('','$curdate','$usd','$euro','$rub');");

?>