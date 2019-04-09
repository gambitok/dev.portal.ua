<?php
error_reporting(E_ERROR);
@ini_set('display_errors', true);
@ini_set('html_errors', false);
define('RD', dirname (__FILE__));
require_once (RD."/../lib/DbSingleton.php");$db=DbSingleton::getDb();
require_once (RD."/../lib/sms_class.php");$sms=new sms;
/*
$r=$db->query("select * from A_CLIENTS_USERS where status=1;");$n=$db->num_rows($r); 
for ($i=1;$i<=$n;$i++){
	$id=$db->result($r,$i-1,"id");
	$phone=$db->result($r,$i-1,"phone");
	$pass=$db->result($r,$i-1,"pass");
	$phone=$sms->correct_nomber($phone);
	if ($pass==""){$pass=$phone;}
	
	$db->query("update A_CLIENTS_USERS set phone='$phone', pass='$pass' where id='$id';");
	
	$mess=nl2br("Privet ot obnovljonnogo www.toko.ua". PHP_EOL .
"Vash login: ".$phone. PHP_EOL .
"Vash parol: ".$pass. PHP_EOL .
"Spasibo chto Vy s nami!");
	
	print $sms->send_sms("TOKO.UA",$phone,$mess);
	
}

/*
$mess=nl2br("Privet ot obnovljonnogo www.toko.ua". PHP_EOL .
"Vash login: ".$phone. PHP_EOL .
"Vash parol: ".$pass. PHP_EOL .
"Spasibo chto Vy s nami!");
	
print $sms->send_sms("TOKO.UA",$phone,$mess)."<br>";
*/

?>