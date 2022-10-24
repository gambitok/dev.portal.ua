<?php

if ($_FILES['file']['name']) {
    if (!$_FILES['file']['error']) {
        $name = md5(rand(100, 200));
        $ext = explode('.', $_FILES['file']['name']);
        $filename = $name . '.' . $ext[1];
        $destination = 'uploads/images/saved_info/' . $filename; //change this directory
        $location = $_FILES["file"]["tmp_name"];
        move_uploaded_file($location, $destination);
        echo 'https://portal.myparts.pro/uploads/images/saved_info/' . $filename;//change this URL
    } else {
        echo  $message = 'Ooops!  Your upload triggered the following error:  ' . $_FILES['file']['error'];
    }
}