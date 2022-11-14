<?php

$folder_name = 'uploads/images/saved/';

if(!empty($_FILES)) {
    $temp_file = $_FILES['file']['tmp_name'];
    $location = $folder_name . $_FILES['file']['name'];
    move_uploaded_file($temp_file, $location);
}

if(isset($_POST["name"])) {
    $filename = $folder_name . $_POST["name"];
    unlink($filename);
}

$result = array();

$files = scandir('uploads/images/saved');

$output = '<div class="row">';

$name = $_POST["name"];

if(false !== $files) {
    foreach($files as $file) {
        if('.' !=  $file && '..' != $file) {
            $output .= '
               <div class="col-md-2">
                <img src="' . $folder_name . $file . '" class="img-thumbnail img-center text-center" width="175" height="175" style="height: 175px; margin-top: 30px;" />
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeReviewCard(this);" id="' . $file . '">Remove</button>
                    <button type="button" class="btn btn-sm btn-warning" onclick="choseReviewCardImage(this);" id="' . $file . '">Choose</button>
                    <button type="button" class="btn btn-sm btn-info" data-src="' . $folder_name . $file . '" onclick="copyReviewImagePath(this);" id="' . $file . '">Copy</button>
                </div>
               </div>
            ';
        }
    }
}

$output .= '</div>';
echo $output;
