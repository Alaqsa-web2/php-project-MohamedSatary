<?php
require_once "dbconnect.php";
session_start();

// upload image
$msg = "";
// check if the user has clicked the button "UPLOAD" 
if(isset($_POST['sendPost'])){
    $name       = $_FILES['uploadImage']['name'];  
    $temp_name  = $_FILES['uploadImage']['tmp_name'];  
    if(isset($name) and !empty($name)){
        $location = 'uploads/';      
        if(move_uploaded_file($temp_name, $location.$name)){
            $target_file = $location . $name;
            echo 'File uploaded successfully';
        }
    } else {
        echo 'You should select a file to upload !!';
    }
}


if (isset($_POST['sendPost'])) {
    $content  = $_POST['content'];
    $date = date("Y-m-d H:i:s");
    $user_id = $_SESSION["id"];
    $result = mysqli_query(
        $link,
        "insert into posts (user_id, content, image_url, created_at)
                values ('$user_id', '$content', '$target_file', '$date') "
    );
    header("location: index.php");
}
