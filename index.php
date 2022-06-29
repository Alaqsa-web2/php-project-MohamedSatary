<?php
// Include config file
require_once "dbconnect.php";

// Initialize the session
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--Bootstrap 5 css-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <!--font-awesome-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!--style-->
    <link rel="stylesheet" href="css/style.css">
    <title>Social Media</title>
</head>

<body>

    <!--start nav bar-->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand brand">Social Media</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarText" style="flex-grow: 0;">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                    <?php
                    if (isset($_SESSION['loggedin']) && !empty($_SESSION['loggedin'])) {
                        // echo htmlspecialchars($_SESSION["username"]);
                        echo '<li class="nav-item"> Hi ' . $_SESSION["name"] . '</li>';
                        echo '<li class="nav-item">' . '<a class="nav-link asignUp" href="logout.php">Logout</a>' . '</li>';
                    } else {
                        echo '<li class="nav-item">
                    <a class="nav-link " aria-current="page" href="login.php">LOGIN</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link asignUp" href="signup.php">SIGNUP</a>
                  </li>
                </ul>';
                    }
                    ?>

            </div>
        </div>
    </nav>
    <!--end nav bar-->

    <!--start content-->
    <div class="container">
        <div class="postForm">
            <div class="flexPostForm">
                <div class="imgPostForm">
                    <img src="<?php echo $_SESSION["image_profile"]; ?>" alt="">
                </div>
                <input data-bs-toggle="modal" data-bs-target="#exampleModal" class="form-control" type="text" placeholder="Type Your Post Here" aria-label=".form-control-lg example">
                <!-- Modal -->
                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="post.php" method="POST" enctype="multipart/form-data">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Create Post</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <textarea name="content" class="form-control txtPostModal" id="exampleFormControlTextarea1" rows="3">Type your post here...</textarea>
                                    <input name="uploadImage" class="form-control" type="file" id="formFile">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" name="sendPost" class="btn btn-primary">Post</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php
        $query = mysqli_query($link, "SELECT posts.* , users.name, users.profile_image from posts LEFT JOIN users on users.id = posts.user_id order by posts.id DESC") or die(mysqli_error());
        foreach ($query as $key => $post) {
            $post_id = $post["id"];
            $queryComment = mysqli_query($link, "SELECT comment.* , users.name, users.profile_image from comment LEFT JOIN users on users.id = comment.user_id  where comment.post_id = $post_id order  by comment.id DESC") or die(mysqli_error());
            echo '<div class="postCard">
                <div class="postForm">
                    <div class="flexPostForm">
                        <div class="imgPostForm">
                            <img src="' . $post["profile_image"] . '" alt="">
                        </div>
                        <div class="namePostCard">
                            <p class="name">' . $post["name"] . '</p>
                            <div class="divTime">
                                <i class="fa-solid fa-clock"></i>
                                <p>' . date("F jS, Y", strtotime($post["created_at"])) . '
                                </p>
                        </div>
                    </div>
                </div>
                <div>
                    <p style="padding: 15px 60px 0;"> ' . $post["content"] . ' </p>
                    <div class="imgPost"> 
                        <img src="' . $post["image_url"] . '" class="img-fluid rounded" alt="...">
                    </div>
                    <div class="divNumComments">
                        <i class="fa-solid fa-comment pNumComments"></i>
                        <p class="pNumComments">22 Comments</p>
                        </div>
                        <div>
                            <form action="comment.php"  method="POST">
                                <div class="flexPostForm">
                                    <div class="imgPostForm">
                                        <img src=" ' . $_SESSION["image_profile"] . ' " alt="">
                                    </div>
                                    <input type="text" name="post_id" value="'. $post["id"] .'" hidden>
                                    <input class="form-control" type="text" name="comment" placeholder="Type your comment" aria-label=".form-control-lg example">
                                    <button type="submit" name="sendComment" hidden></button>
                                </div>
                            </form>
                        </div>
                        <hr class="hr">'
                        ;
                        foreach ($queryComment as $key => $comment) {
                            echo ' <div class="comment">
                            <div class="flexPostForm">
                                <div class="imgPostForm">
                                    <img src="'.$comment["profile_image"].'" alt="">
                                </div>
                                <div class="boxComment">
                                    <p class="nameBoxComment">'.$comment["name"].'</p>
                                    <p class="contentBoxComment">'.$comment["comment"].'</p>
                                    <div class="divTime">
                                        <i class="fa-solid fa-clock"></i>
                                        <p>' . date("F jS, Y", strtotime($comment["created_at"])) . '</p>
                                    </div>
                                </div>
                            </div>
                        </div>';
                        }
                        echo "</div></div>";
        }
        
        ?>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>