<?php
$start = microtime(true);
error_reporting(E_ERROR);
@ini_set('display_errors', true);
@ini_set('html_errors', false);
ini_set('memory_limit', '2048M');
define('RD', dirname (__FILE__));
date_default_timezone_set("Europe/Kiev");
require_once (RD . "/../lib/suppl_class.php");
require_once (RD . "/../lib/DbSingleton.php");
$db = DbSingleton::getDb();
$dbt = DbSingleton::getTokoDb();
$suppl = new suppl();

$date_cur = date("Y-m-d H:i:s");
session_start();
$user_id = $_SESSION["media_user_id"];
$nssss = 0; $ncount = 0; $numbers_count = 0; $numbers = 0;

function clearArticle($art) {
    $art = str_replace(" ", "", $art);
    $art = str_replace("_", "", $art);
    $art = str_replace("-", "", $art);
    $art = str_replace(".", "", $art);
    $art = str_replace("+", "", $art);
    $art = str_replace("'", "", $art);
    $art = str_replace("/", "", $art);
    $art = str_replace('"', "", $art);
    $art = preg_replace ("/[^a-zA-ZР-пр-џ0-9\s]/", "", $art);
    $art = strtolower($art);
    return $art;
}

$rc = $db->query("SELECT `suppl_id` FROM `cron_suppl_price_import` WHERE `status`=1;");
$nc = $db->num_rows($rc);

if ($nc > 0) {

    for ($k = 1; $k <= $nc; $k++) {
        $suppl_id = $db->result($rc, $k - 1, "suppl_id");

        $rs = $db->query("SELECT * FROM `SUPPL_BRANDS_PREFIX` WHERE `suppl_id`='$suppl_id';");
        $ns = $db->num_rows($rs);
        for ($j = 1; $j <= $ns; $j++) {
            $brand_id = $db->result($rs, $j - 1, "brand_id");
            $suppl_brand = $db->result($rs, $j - 1, "suppl_brand");
            $prefix = $db->result($rs, $j - 1, "prefix");
            $return_delay = $db->result($rs, $j - 1, "return_delay");
            $warranty_info = $db->result($rs, $j - 1, "warranty_info");

            $dbt->query("DELETE FROM `T2_ARTICLES_SUPPL_COPY`;");
            $dbt->query("ALTER TABLE `T2_ARTICLES_SUPPL_COPY` AUTO_INCREMENT = 1;");
            $dbt->query("INSERT INTO `T2_ARTICLES_SUPPL_COPY` (`ART_ID`, `ARTICLE_NR_DISPL`, `ARTICLE_NR_SEARCH`, `BRAND_ID`) 
            SELECT `ART_ID`, `ARTICLE_NR_DISPL`, `ARTICLE_NR_SEARCH`, `BRAND_ID` FROM `T2_ARTICLES` WHERE `BRAND_ID`=$brand_id;");

            $r = $dbt->query("SELECT * FROM `T2_SUPPL_IMPORT` WHERE `status`=1 AND `art_id`=0 AND `suppl_id`=$suppl_id AND `brand`='$suppl_brand';");
            $n = $dbt->num_rows($r);
            for ($i = 1; $i <= $n; $i++) {
                $id = $dbt->result($r, $i - 1, "ID");
                $suppl_index = $dbt->result($r, $i - 1, "suppl_index");
                $brand = $dbt->result($r, $i - 1, "brand");
                $brand = str_replace("'", '"', $brand);

                $suppl_index_search = str_replace("$prefix", "", $suppl_index);
                $suppl_index_search = clearArticle($suppl_index_search);
                $suppl_index_search = strtoupper($suppl_index_search);

                $r2 = $dbt->query("SELECT * FROM `T2_ARTICLES_SUPPL_COPY` WHERE `ARTICLE_NR_SEARCH`='$suppl_index_search';");
                $n2 = $dbt->num_rows($r2);
                $art_id = 0;
                if ($n2 > 0) {
                    $art_id = $dbt->result($r2, 0, "ART_ID");
                }
                $art_id_count = $n2;

                if ($art_id > 0 && $art_id_count == 1) {
                    $dbt->query("UPDATE `T2_SUPPL_IMPORT` SET `art_id`='$art_id', `return_delay`='$return_delay', `warranty_info`='$warranty_info' WHERE `ID`=$id AND `art_id`=0 AND `status`=1;");
                    $r2 = $dbt->query("SELECT * FROM `T2_SUPPL_ARTICLES_IMPORT` WHERE `suppl_id`=$suppl_id AND `suppl_index`='$suppl_index' AND `suppl_brand`='$brand' AND `art_id`='$art_id';");
                    $n2 = $dbt->num_rows($r2);
                    if ($n2 == 0) {
                        $dbt->query("INSERT INTO `T2_SUPPL_ARTICLES_IMPORT` (`suppl_id`, `suppl_index`, `suppl_brand`, `art_id`, `return_delay`, `warranty_info`, `user_create`, `date_create`)
                        VALUES ($suppl_id, '$suppl_index', '$brand', $art_id, $return_delay, '$warranty_info', '$user_id', '$date_cur');");
                        $ncount++;
                    }
                }
            }

            $dbt->query("DELETE FROM `T2_ARTICLES_SUPPL_COPY`;");
        }

        $db->query("UPDATE `cron_suppl_price_import` SET `status`=0 WHERE `suppl_id`='$suppl_id';");

        $nssss = $nssss + $ns;

        $numbers = $suppl->cronSetNumbersList($suppl_id);

        $numbers_count += $numbers;
    }

    $time = microtime(true) - $start;
    $time = gmdate("H:i:s", $time);
    print "Count suppls: $nc \n
    Count brands: $nssss \n
    Count articles: $ncount \n
    Run time: $time \n
    Numbers was updated: $numbers_count \n";
}

