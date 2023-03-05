<?php

//if ($_FILES['file']['name']) {
//    if (!$_FILES['file']['error']) {
//        $filename = 'uploads/images/saved_info/' . $_FILES['file']['name'];
//        unlink($filename);
//        echo $filename . " deleted!";
//    } else {
//        echo  $message = 'Ooops!  Your upload triggered the following error:  ' . $_FILES['file']['error'];
//    }
//}
$filename = $_FILES['file']['name'];
unlink($filename);