<?php

error_reporting(1);
@ini_set('display_errors', true);
define('RD', __DIR__);
date_default_timezone_set("Europe/Kiev");

$file_list = [];
$target_dir = RD . "/../uploads/images/saved/";
$target_dir2 = "/../uploads/images/saved/";
$dir = $target_dir;

if (is_dir($dir) && $dh = opendir($dir)) {

    //read file
    while ( ($file = readdir($dh)) !== false) {
        if ($file !== '' && $file !== '.' && $file !== '..') {

            //file path
            $file_path = $target_dir . $file;

            //check its not directory
            if (!is_dir($file_path)){
                $size = filesize($file_path);

                $file_list[] = array('name'=>$file, 'size'=>$size, 'path'=>$target_dir2 . $file);

            }
        }
    }
    closedir($dh);
}

echo json_encode($file_list);
exit;