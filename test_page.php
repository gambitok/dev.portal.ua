<?php
define('RD', dirname (__FILE__));
require_once (RD."/lib/catalogue_class.php"); $cat=new catalogue;
require_once (RD."/lib/dp_class.php"); $dp=new dp;
require_once (RD."/lib/back_clients_class.php"); $back=new back_clients;
require_once (RD."/lib/DbSingleton.php");
require_once (RD."/lib/slave_class.php");
require_once (RD."/lib/mysql_class.php");

//$cat->testFunction();
//
//$seller_id=$back->getSellerId(22391);
//$new_prefix = $dp->getSellerPrefixDocNom($seller_id,64);

$form="";$form_htm=RD."/tpl/test_page.htm"; if (file_exists("$form_htm")){$form = file_get_contents($form_htm);}
echo $form;

//	$cat->fixArtDocs();

