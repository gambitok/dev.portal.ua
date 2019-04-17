<?php

require_once (dirname (__FILE__)."/lib/mysql_class.php");
require_once (dirname (__FILE__)."/lib/DbSingleton.php");

$db=DbSingleton::getTokoDb();

$ds = DIRECTORY_SEPARATOR;

$group_type_id=$_POST["group_type_id"];
$group_str_id=$_POST["group_str_id"];

if ($group_type_id=="group") {
    $storeFolder="uploads/images/group_tree_str";
    $table="T2_GROUP_TREE_HEAD_STR";
    $param="GROUP_ID";
}

if ($group_type_id=="head") {
    $storeFolder="uploads/images/group_tree_head";
    $table="T2_GROUP_TREE_HEAD";
    $param="HEAD_ID";
}

if (!empty($_FILES)) {

    $nameFile=$_FILES['file']['name'];
    $nameFile=str_replace(" ","_",$nameFile);
    $nameFile=str_replace(str_split('\\/:*?"<>|+-()[], '), '', $nameFile);

    $tempFile = $_FILES['file']['tmp_name'];

    $targetPath = dirname( __FILE__ ) . $ds. $storeFolder . $ds;

    $targetFile =  $targetPath. $nameFile;

    move_uploaded_file($tempFile,$targetFile);

    $db->query("update $table set IMAGES='$nameFile' where $param='$group_str_id';");

}

