<?php
$start = microtime(true);
error_reporting(E_ERROR);
@ini_set('display_errors', true);
@ini_set('html_errors', false);
ini_set('memory_limit', '2048M');
define('RD', dirname (__FILE__));
date_default_timezone_set("Europe/Kiev");
require_once (RD."/../lib/DbSingleton.php");
require_once (RD."/../lib/slave_class.php"); $slave=new slave;
require_once (RD."/../lib/storage_class.php"); $storage=new storage;
$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();

$j=0;
$r=$dbt->query("SELECT `ART_ID`, `STORAGE_ID`, `RESERV_AMOUNT` FROM `T2_ARTICLES_STRORAGE`;");
$n=$dbt->num_rows($r); $list="";
for ($i=1;$i<=$n;$i++) {
    $ART_ID = $dbt->result($r, $i - 1, "ART_ID");
    $ARTICLE_NR_DISPL = $storage->getArtDispl($ART_ID);
    $STORAGE_ID = $dbt->result($r, $i - 1, "STORAGE_ID");
    $STORAGE_NAME = $storage->getStorageName($STORAGE_ID);
    $RESERV_AMOUNT = $dbt->result($r, $i - 1, "RESERV_AMOUNT");
    list($COUNT_DOCS, $DOC_NAME) = $storage->getCountDocs($ART_ID, $STORAGE_ID); $DOC_NAME=$slave->translit($DOC_NAME);

    if ($RESERV_AMOUNT != $COUNT_DOCS) {
        $list.="
            $ART_ID  $ARTICLE_NR_DISPL  Storage#$STORAGE_ID  $RESERV_AMOUNT  $COUNT_DOCS  $DOC_NAME
        \n";
        $j++;
        $dbt->query("INSERT INTO `storage_test` (`art_id`, `article_nr_displ`, `storage_id`, `reserv`, `count_docs`, `doc_name`) 
        VALUES ($ART_ID, '$ARTICLE_NR_DISPL', $STORAGE_ID, $RESERV_AMOUNT, $COUNT_DOCS, '$DOC_NAME');");
    }
}

//$time = microtime(true) - $start;
//$time = gmdate("H:i:s", $time);
//print "Checked articles: $n \n
//Errors found: $j \n
//Run time: $time \n";
//
//print($list);