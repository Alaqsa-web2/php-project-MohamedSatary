<?php
// Initialize the session
session_start();

// Check if the user is already logged in, if yes then redirect him to welcome page
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
  header("location: index.php");
  exit;
}

// Include config file
require_once "dbconnect.php";

// Define variables and initialize with empty values
$email = $password = $username = "";
$email_err = $password_err = $name_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

  // Validate Name
  if (empty(trim($_POST["username"]))) {
    $name_err = "Please enter Name";
  } elseif (!preg_match("/^[a-zA-Z-' ]*$/", $_POST["name"])) {
    $nameErr = "Only letters and white space allowed";
  } else {
    $username = trim($_POST["username"]);
  }

  // upload Image Profile
  if (isset($_POST["submit"])) {
    $name       = $_FILES['profileImage']['name'];
    $temp_name  = $_FILES['profileImage']['tmp_name'];
    if (isset($name) and !empty($name)) {
      $location = 'uploads/profileImages/';
      if (move_uploaded_file($temp_name, $location . $name)) {
        $target_file = $location . $name;
      } else {
        $target_file = $location . 'avatar.png';
      }
    }
  }

  // Validate email
  if (empty(trim($_POST["email"]))) {
    $email_err = "Please enter a email.";
  } elseif (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
    $email_err = "email can only contain letters, numbers, and underscores.";
  } else {
    // Prepare a select statement
    $sql = "SELECT id FROM users WHERE email = ?";

    if ($stmt = mysqli_prepare($link, $sql)) {
      // Bind variables to the prepared statement as parameters
      mysqli_stmt_bind_param($stmt, "s", $param_email);

      // Set parameters
      $param_email = trim($_POST["email"]);

      // Attempt to execute the prepared statement
      if (mysqli_stmt_execute($stmt)) {
        /* store result */
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) == 1) {
          $email_err = "This email is already taken.";
        } else {
          $email = trim($_POST["email"]);
        }
      } else {
        echo "Oops! Something went wrong. Please try again later.";
      }

      // Close statement
      mysqli_stmt_close($stmt);
    }
  }

  // Validate password
  if (empty(trim($_POST["password"]))) {
    $password_err = "Please enter a password.";
  } elseif (strlen(trim($_POST["password"])) < 6) {
    $password_err = "Password must have atleast 6 characters.";
  } else {
    $password = trim($_POST["password"]);
  }

  // Check input errors before inserting in database
  if (empty($email_err) && empty($password_err) && empty($name_err)) {

    // Prepare an insert statement
    // $sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
    $sql = "INSERT INTO users (name, email, password, profile_image) VALUES (?, ?, ?, ?)";
    if ($stmt = mysqli_prepare($link, $sql)) {
      // Set parameters
      $para_name = $username;
      $param_email = $email;
      $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash

      // Bind variables to the prepared statement as parameters
      mysqli_stmt_bind_param($stmt, "ssss", $para_name, $param_email, $param_password, $target_file);

      // Attempt to execute the prepared statement
      if (mysqli_stmt_execute($stmt)) {
        // Redirect to login page
        header("location: login.php");
      } else {
        echo "Oops! Something went wrong. Please try again later.";
      }

      // Close statement
      mysqli_stmt_close($stmt);
    }
  }

  // Close connection
  mysqli_close($link);
}
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
  <title>Sign up</title>
</head>

<body>
  <main class="form-signin">
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST"  enctype="multipart/form-data">
      <a href="index.html">
        <h1 class="brand">Social Media</h1>
      </a>
      <h1 class="h3 mb-3 fw-normal">Register Now</h1>

      <div class="form-floating">
        <!-- <input type="email" class="form-control" id="floatingInput" placeholder="name@example.com"> -->
        <input type="text" name="username" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
        <label for="name">Name</label>
      </div>
      <div class="form-floating">
        <input type="email" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>" placeholder="name@example.com">
        <label for="email">Email address</label>
      </div>
      <div class="form-floating">
        <input type="password" name="password" class="form-control" id="floatingPassword" placeholder="Password">
        <label for="floatingPassword">Password</label>
      </div>
      <div class="form-floating">
        <input type="file" name="profileImage" class="form-control" id="profileImage" placeholder="Upload Your Image Profile">
        <label for="profileImage">Image Profile</label>
      </div>

      <div class="checkbox mb-3">
        <a class="linksignup" href="login.html">I Have Account Go to Login</a>
      </div>
      <button class="w-100 btn btn-lg btnLogin" name="submit" type="submit">Sign up</button>
    </form>
  </main>

  <!--Bootstrap 5 js-->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

</body>

</html>