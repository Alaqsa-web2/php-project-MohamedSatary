<?php
require_once "dbconnect.php";
session_start();


if (isset($_POST['sendComment'])) {
    $content  = $_POST['comment'];
    $date = date("Y-m-d H:i:s");
    $user_id = $_SESSION["id"];
    $post_id = $_POST["post_id"];
    $result = mysqli_query(
        $link,
        "insert into comment (user_id, post_id, comment, created_at)
                values ('$user_id', '$post_id', '$content', '$date') "
    );
    header("location: index.php");
}
