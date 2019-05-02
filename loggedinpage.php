<?php

  session_start();

  $logOut = "";

  $diaryContent = "";

  if(array_key_exists("id", $_COOKIE)) {

    $_SESSION['id'] = $_COOKIE['id'];

  }

  if (array_key_exists("id", $_SESSION)) {

    //enable logout link @navbar
    $logOut = "index.php?logout=1";

    include("connection.php");

    $query = "SELECT `diary` FROM `users` WHERE id =".mysqli_real_escape_string($link,$_SESSION['id'])." LIMIT 1";

    $row = mysqli_fetch_array(mysqli_query($link,$query));

    $diaryContent = $row['diary'];

  } else {

//If SESSION does not exist - redirect to index.php for login form

    header("Location: index.php");

  }



?>
<?php include("header.php");?>

  <nav class="navbar navbar-expand-md navbar-light bg-light fixed-top">
  <a class="navbar-brand" href="#">Secret Dairy</a>

  <div class="navbar-collapse justify-content-end" id="navbarsExampleDefault">

    <div class="form-inline my-2 my-lg-0" action="index.php">
      <a class="btn btn-outline-secondary my-2 my-sm-0" href=<?php echo $logOut; ?>> LogOut </a>
    </div>
  </div>
</nav>

<main role="main" id="loggedInPageContainer" class="container-fluid">

  <textarea id="diary" class="form-control" autofocus><?php echo $diaryContent; ?></textarea>

</main><!-- /.container -->
<?php include("footer.php");?>
