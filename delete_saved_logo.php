<?php
define('RD', __DIR__);
$filename = RD . '/uploads/images/saved/' . $_POST['file_name'];

if (file_exists($filename)) {
    unlink($filename);
    echo "The file $filename exists";
} else {
    echo "The file $filename does not exist";
}
